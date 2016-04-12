<?php
    require_once("inc/stdLib.php");
    include("crmLib.php");
    include("FirmenLib.php");
    require_once("documents.php");
    include("template.inc");
    include("persLib.php");

    $t = new Template($base);
    doHeader($t);
    $pid=($_GET["pid"])?$_GET["pid"]:$_POST["pid"];
    $fid=($_GET["fid"])?$_GET["fid"]:$_POST["fid"];
    $did=($_GET["did"])?$_GET["did"]:$_POST["did"];
    $tab=($_GET["tab"])?$_GET["tab"]:$_POST["tab"];
    if ( $_POST['erstellen'] ) {
        $docdata=getDOCvorlage($_POST["did"]);
        if ($docdata["document"]["applikation"]=="O") {
            include('inc/phpOpenOffice.php');
            $doc = new phpOpenOffice();
        } else if ($docdata["document"]["applikation"]=="R") {
            include('inc/phpRtf.php');
            $doc = new phpRTF();
        } else if ($docdata["document"]["applikation"]=="T") {
            include('inc/phpTex.php');
            $doc = new phpTex();
        } else if ($docdata["document"]["applikation"]=="B") {
            require('inc/phpBIN.php');
            $doc = new phpBIN();
        }
        $fname=$docdata["document"]["file"];
        $doc->loadDocument("./vorlage/".$docdata["document"]["file"]);
        $vars= array();
        if ($docdata["felder"]) foreach($docdata["felder"] as $zeile) {
            $value=$zeile["platzhalter"];
            $name=strtoupper($value);
            if (ini_get("default_charset")=='utf-8') {
                $vars[$name] = utf8_decode($_POST[$value]);
            } else {
                $vars[$name] = $_POST[$value];
            }
        }
        $doc->parse($vars);
        $name=date("YmdHi").$fname;
        $doc->prepsave("$name");
        $pfad = $_POST['pfad'];
        copy('tmp/'.$name,"dokumente/".$_SESSION["dbname"]."$pfad/$name");
        $dbfile=new document();
        $dbfile->setDocData("descript","Dokumentvorlage: ".$docdata["document"]["vorlage"]."\n".$docdata["document"]["beschreibung"]);
        $dbfile->setDocData("name",$name);
        $dbfile->setDocData("pfad",$pfad);
        $rc=$dbfile->saveDocument();
        //$doc->clean();
        $cdata["id"]=mknewTelCall();
        $cdata["Datum"]=date("d.m.Y");
        $cdata["Date"]=date("d.m.Y");
        $cdata["Zeit"]=date("H:i");
        $cdata["Time"]=date("H:i");
        $cdata["datei"]=1;
        $cdata["cause"]=$docdata["document"]["vorlage"];
        $cdata["c_cause"]=$docdata["document"]["beschreibung"];
        $cdata["CID"]=($pid)?$pid:$fid;
        $cdata["Kontakt"]="D";
        $cdata["bezug"]=0;
        $cdata["CRMUSER"]=$_SESSION["loginCRM"];
        updCall($cdata);
        documenttotc($cdata["id"],$dbfile->id);
        echo "Dokument erstellt"; 
        exit();
    };
    if ($fid) {
        $fa=getFirmenStamm($fid,true,$tab);
        $pfad="/$tab";
        $pfad.=($tab=="C")?$fa["customernumber"]:$fa["vendornumber"];
    };
    if ($pid) $pfad.="/$pid";

    if (empty($fid)) {
        $data=getKontaktStamm($pid);
        $data["ANREDE"]=$data["cp_greeting"]." ".$data["cp_title"];
        $data["NAME2"]=$data["cp_givenname"];
        $data["NAME1"]=$data["cp_name"];
        $data["NAME"]=$name2." ".$name1;
        $data["PLZ"]=$data["cp_zipcode"];
        $data["ORT"]=$data["cp_city"];
        $data["STRASSE"]=$co["cp_street"];
        $art="Einzelperson";
    } else {
        $data=getFirmenStamm($fid,true,$tab);
        $anrede="Firma";
        $data["ANREDE"]=$data["greeting"];
        $data["NAME"]=$data["name"];
        $data["NAME1"]=$data["name"];
        $data["NAME2"]=$data["department_1"];
        $data["KONTAKT"]=$data["contact"];
        $data["PLZ"]=$data["zipcode"];
        $data["ORT"]=$data["city"];
        $data["STRASSE"]=$data["street"];
        foreach ($data as $key=>$val) {
            $key = strtoupper($key);
            if (substr($key,0,8) == "VC_CVAR_") $data[$key] = $val;
        }
        if (!empty($pid)){
            $co=getKontaktStamm($pid);
            $data["ANREDEPERS"]=$co["cp_greeting"];
            $data["ANREDEPERS"].=($co["cp_title"])?" ".$co["cp_title"]:"";
            $data["NAMEPERS"]=$co["cp_givenname"]." ".$co["cp_name"];
            $data["PLZPERS"]=$co["cp_zipcode"]; $ortpers=$co["cp_city"]; $strassepers=$co["cp_street"];
            $data=array_merge($data,$co);
            $art="Firma/Kontakt";
        } else {
            $art="Firmendokumente";
        }
    }
    $document = getDocVorlage($did);
    $t->set_file(array("doc" => "firma4a.tpl"));
    $t->set_var(array(
            FAART => ($tab=="C")?"Kunde":"Lieferant",
            TAB => $tab,
            PFAD => $pfad,
            FID => $fid,
            PID => $pid,
            Art => $art,
            Beschreibung => $document["document"]["beschreibung"],
            DOCID => $did,
            ));
    $t->set_block("doc","Liste","Block");
    $t->set_block("doc","RegEx","Block2");
    $i=0;
    $data["DATUM"]=date("d.m.Y");
    $data["DATE"]=date("d.m.Y");
    $data["ZEIT"]=date("H:i");
    $data["Time"]=date("H:i");
    if ($document["felder"]) {
        foreach($document["felder"] as $zeile) {
            $value=$zeile["platzhalter"]; //Alte Platzhalter
            if ( !array_key_exists($value,$data) ) $value=strtolower($zeile["platzhalter"]); //DB-Namen
            if ($zeile["laenge"]>60) {
                $rows=floor($zeile["laenge"]/60)+1;
                $input="<textarea class='klein' cols=60 rows=$rows name='".$zeile["platzhalter"]."'>".$data[$value]."</textarea>";
                $typ="textarea";
            } else {
                $input="<input type='text' name='".$zeile["platzhalter"]."' size='".$zeile["laenge"]."' value='".$data[$value]."'>";
                $typ="input";
            }
            $t->set_var(array(
                EINGABE => $input,
                Feldname => $zeile["feldname"]
            ));
            $t->parse("Block","Liste",true);
            $t->set_var(array(
                fld => $zeile["platzhalter"],
                typ => $typ,
                fldname => $zeile["feldname"],
                regul => $zeile["zeichen"]
            ));
            $t->parse("Block2","RegEx",true);
            $i++;
        }
    }
    $t->Lpparse("out",array("doc"),$_SESSION['countrycode'],"firma");
?>
