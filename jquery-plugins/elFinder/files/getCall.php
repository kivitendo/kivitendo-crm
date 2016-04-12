<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/crmLib.php");
    include("inc/FirmenLib.php");
    include("inc/persLib.php");
    include_once("inc/UserLib.php");
    $fid=($_POST["fid"])?$_POST["fid"]:$_GET["fid"];
    $pid=($_POST["pid"])?$_POST["pid"]:$_GET["pid"];
    $INIT=($_POST["INIT"])?$_POST["INIT"]:$_GET["INIT"];
    if (empty($INIT)) $INIT=0;
    $Bezug=($_POST["Bezug"])?$_POST["Bezug"]:$_GET["Bezug"];
    if (empty($Bezug)) $Bezug=0;
    $Q=($_GET["Q"])?$_GET["Q"]:$_POST["Q"];
    $select=$_SESSION["loginCRM"];
    $selectC=(strlen($Q)==1)?$fid:$pid;
    if ($INIT>0) {    $daten=getCall($INIT);    }
    $daten["Datum"]=date("d.m.Y");
    $daten["Zeit"]=date("H:i");
    $daten["Kontakt"]="T";
    $daten["c_long"]="";
    $daten["Files"]=false;
    $daten["Anzeige"]=0;
    $daten["Datei"]="";
    $daten["DCaption"]="";
    $daten["Q"]=$Q;
    $daten["id"]=0;
    $daten["CID"]=($pid>0)?$pid:$fid;
    $daten["Kunde"]=0;
    $daten["Anzeige"]=0;
    $daten["wvldate"]="";
    $daten["wvlid"]=false;
    if ($_POST["verschiebe"]) {
        $rc=mvTelcall($_POST["TID"],$_POST["id"],$_POST["CID"]);
        $daten["Betreff"]=$_POST["Betreff"];
        if ($_POST["Bezug"]==$_POST["id"]) {
            $daten["id"]=$_POST["TID"];
            $Bezug=$_POST["TID"];
        } else {
            $daten["id"]=$_POST["Bezug"];
        }                                            // verschiebe
    } else     if ($_POST["delete"]) {
        $rc=saveTelCall($_POST["id"],$_SESSION["loginCRM"],"D");
        $rc=delTelCall($_POST["id"]);
        if ($_POST["bezug"]==0) $Bezug=0;
    } else     if ($_GET["hole"]) {
        $daten=getCall($_GET["hole"]);
        $Bezug=($daten["Bezug"]==0)?$daten["ID"]:$daten["Bezug"];
        $select=$daten["employee"];
        $co=getKontaktStamm($daten["CID"]);
        if ($co["cp_id"]) {
            $pid=$co["cp_id"];
            $fid=$co["cp_cv_id"];
        } else {
            $fid=$daten["CID"];        // Einzelperson o. Firma allgem.
        }
        $selectC=$daten["CID"];                        // if ($_GET["hole"])
    } else if ($_POST["update"]) {
        $rc=saveTelCall($_POST["id"],$_SESSION["loginCRM"],"U");
        $rc=updCall($_POST,$_FILES);
        if ($rc) {
            $daten["Betreff"]=$_POST["cause"];
        } else {
            $daten=$_POST;
        }                                            // if ($rc)
    } else if ($_POST["sichern"]) {
        unset($_POST["id"]);
        $rc=insCall($_POST,$_FILES);
        if ($rc) {
            $daten["Betreff"]=$_POST["cause"];
            if ($Bezug==0) $Bezug=$rc;
        } else {
            $daten=$_POST;
        }                                            // if ($rc)
    }                                //  end sichern
    switch ($Q) {
        case "C" :  $fa=getFirmenStamm($fid,true,"C");
                    $daten["Firma"]=$fa["name"];
                    $daten["Plz"]=$fa["zipcode"];
                    $daten["Ort"]=$fa["city"];
                    $daten["nummer"]=$fa["nummer"];
                    break;
        case "V" :  $fa=getFirmenStamm($fid,true,"V");
                    $daten["Firma"]=$fa["name"];
                    $daten["Plz"]=$fa["zipcode"];
                    $daten["Ort"]=$fa["city"];
                    $daten["nummer"]=$fa["nummer"];
                    break;
        case "XC" :
        case "CC" :
        case "VC" : $co=getKontaktStamm($pid);
                    $daten["Firma"]=$co["cp_givenname"]." ".$co["cp_name"];
                    $daten["Plz"]=$co["cp_zipcode"];
                    $daten["Ort"]=$co["cp_city"];
                    $daten["nummer"]=$co["nummer"];
                    break;
        default   : $daten["Firma"]="xxxxxxxxxxxxxx";
                    $daten["nummer"]="";
    }

    //------------------------------------------- Beginn Ausgabe
    $t = new Template($base);
    $t->set_file(array("cont" => "getCall.tpl"));
    doHeader($t);
    //------------------------------------------- CRMUSER
    $t->set_block("cont","Selectbox","Block2");
    $user=getAllUser("%");
    if ($user) foreach($user as $zeile) {
        $t->set_var(array(
            Sel => ($select==$zeile["id"])?" selected":"",
            UID    =>    $zeile["id"],
            Login    =>    $zeile["login"],
        ));
        $t->parse("Block2","Selectbox",true);
    }
    //------------------------------------------- Firma/Kontakte
    $t->set_block("cont","Selectbox2","Block3");
    if ($fid) {
        $contact=getAllKontakt($fid);
        $first[]=array("cp_id"=>$fid,"cp_name"=>"Firma","cp_givenname"=>"allgemein");
        if ($contact) {
            $contact=array_merge($first,$contact);
        } else {
                    $contact=$first;
            }
    } else {
        if ($co["cp_cv_id"]) {
            $first[]=array("cp_id"=>$co["cp_cv_id"],"cp_name"=>"Firma","cp_givenname"=>"allgemein");
            $contact=getAllKontakt($co["cp_cv_id"]);
            if ($contact) {
                $contact=array_merge($first,$contact);
            } else {
                $first[]=array("cp_id"=>$pid,"cp_name"=>$co["cp_name"],"cp_givenname"=>$co["cp_givenname"]);
                        $contact=$first;
                }
        } else {
            $contact[]=array("cp_id"=>$pid,"cp_name"=>$co["cp_name"],"cp_givenname"=>$co["cp_givenname"]);
        }
    }
    foreach($contact as $zeile) {
        $t->set_var(array(
            Sel => ($selectC==$zeile["cp_id"])?" selected":"",
            CID    =>    $zeile["cp_id"],
            CName    =>    $zeile["cp_name"].", ".$zeile["cp_givenname"],
        ));
        $t->parse("Block3","Selectbox2",true);
    }
    //------------------------------------------- Kontaktverl�ufe
    $t->set_block("cont","Selectbox3","Block4");
    if ($Q<>"XX")    {
        $thread=getAllTelCall(($pid)?$pid:$fid,($Q=="C" || $Q=="V"),0,-1); // Liste Verschieben
        if ($thread) {
            $thread=array_merge(array(array("id"=>"0")) ,$thread);
        } else {
            $thread=array(array("id"=>"0"),array("id"=>"$id"));
        }
    } else {
        $thread=array(array("id"=>"0"),array("id"=>"$id"));
    }
    if ($thread) foreach($thread as $zeile) {
        $t->set_var(array(
            Sel => ($daten["id"]==$zeile["id"])?" selected":"",
            TID    =>    $zeile["id"],
        ));
        $t->parse("Block4","Selectbox3",true);
    }
    //------------------------------------------- Kontakte
    $i=0;
    $t->set_block("cont","Liste","Block");
    $zeile="";
    if ($Bezug<>0) {
        $calls=getAllCauseCall($Bezug);
        if ($calls) foreach($calls as $zeile) {
            $t->set_var(array(
                LineCol => ($zeile["bezug"]==0)?4:($i%2+1),
                Type    => $zeile["kontakt"],
                Datum    =>    db2date($zeile["calldate"]).substr($zeile["calldate"],10,6),
                Betreff    =>    $zeile["cause"],
                Kontakt    =>    $zeile["kontakt"],
                IID => $zeile["id"]
            ));
            $t->parse("Block","Liste",true);
            $i++;
        }
    } ;
    //------------------------------------------- Eingabemaske
    if (empty($daten["CID"])) {
        $cid=(empty($zeile["caller_id"])?"0":$zeile["caller_id"]);
    } else {
        $cid=$daten["CID"];
    }
    $cause=(empty($daten["Betreff"]))?$zeile["cause"]:$daten["Betreff"];
    $deletes=getCntCallHist($Bezug,true);
    $t->set_var(array(
        nummer => $daten["nummer"],
        EDIT => ($_SESSION['CallEdit']=='t' and $_GET["hole"])?"visible":"hidden",
        DELETE => ($_SESSION['CallDel']=='t' and $_GET["hole"])?"visible":"hidden",
        HISTORY => ($daten["history"]>0)?"visible":"hidden",
        HDEL => ($deletes>0)?"visible":"hidden",
        Person => $Person,
        NBetreff => addslashes($cause),
        Q => $Q,
        Firma => $daten["Firma"],
        Plz => $daten["Plz"],
        Ort => $daten["Ort"],
        NDatum => $daten["Datum"],
        wvl => ($daten["wvldate"])?"checked":"",
        wvldate => $daten["wvldate"],
        WVLID => $daten["wvlid"],
        NZeit => $daten["Zeit"],
        c_long => $daten["c_long"],
        CID => $cid,
        FID => $fid,
        PID => $pid,
        INOUT.$daten["inout"] => "checked",
        bezug => $daten["Bezug"],
        Bezug => ($Bezug)?$Bezug:0,
        R1 => ($daten["Kontakt"]=="T")?" checked":"",
        R2 => ($daten["Kontakt"]=="M" or $daten["Kontakt"]=="m")?" checked":"",
        R3 => ($daten["Kontakt"]=="S")?" checked":"",
        R4 => ($daten["Kontakt"]=="P")?" checked":"",
        R5 => ($daten["Kontakt"]=="D")?" checked":"",
        R6 => ($daten["Kontakt"]=="X")?" checked":"",

        Start => $telcall*-1,
        Datei => $daten["Datei"],
        ODatei => (empty($daten["Datei"]))?"":("<a href='dokumente/".$_SESSION["dbname"]."/".$daten["Dpfad"]."/".$daten["Datei"]."' target='_blank'>".$daten["Datei"]."</a>"),
        DateiID => $daten["DateiID"],
        Dcaption => $daten["DCaption"],
        ID => $daten["ID"],
    ));
    //------------------------------------------- Dateianhänge
     if ($daten["Files"]){
        $t->set_block("cont","Files","Block1");
        if ($daten["Files"]) foreach($daten["Files"] as $zeile) {
            $filelink="<a href='dokumente/".$_SESSION["dbname"]."/".$zeile["pfad"]."/".$zeile["filename"]."' target='_blank'>".$zeile["filename"]."</a>";
            $t->set_var(array(
                Anhang    => $filelink,
                DCaption => $zeile["descript"]
            ));
            $t->parse("Block1","Files",true);
            $i++;
        }
    };
    $t->pparse("out",array("cont"));

?>
