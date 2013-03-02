<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/persLib.php");
    include("inc/laender.php");
    include("inc/UserLib.php");
    include("inc/FirmenLib.php");
    $t = new Template($base);
    $menu =  $_SESSION['menu'];
    $t->set_var(array(
        JAVASCRIPTS   => $menu['javascripts'],
        STYLESHEETS   => $menu['stylesheets'],
        PRE_CONTENT   => $menu['pre_content'],
        START_CONTENT => $menu['start_content'],
        END_CONTENT   => $menu['end_content'],
        'JQUERY'        => $_SESSION['basepath'].'crm/',
        'THEME'         => $_SESSION['theme'],
    ));
    $Quelle=($_POST["Quelle"])?$_POST["Quelle"]:$_GET["Quelle"];
    if (!$Quelle) $Quelle="C";
    if ($_GET["first"]) {
        $_POST["cp_name"]=$_GET["first"];
        $_POST["fuzzy"]="%";
    }
    if ($_POST["suche"]=="suchen" || $_POST["first"]=="1") {
        $daten=suchPerson($_POST);
        if (!chkAnzahl($daten,$tmp)) {
            $msg="Trefferanzahl zu gro&szlig;. Bitte einschr&auml;nken.";
            $btn1="";
            vartplP($t,$_POST,$msg,$btn1,$btn1,$btn1,"Anrede","white",$_POST["FID1"],1);
        } if (count($daten)==1 && $daten<>false && !$_POST["FID1"]) { //@holgi Eine Prüfung FID1 wird dreimal gemacht, vielleicht
                                                                  // kann man das vereinheitlichen?
            header ("location:kontakt.php?id=".$daten[0]["cp_id"]);
        } else if (count($daten)>=1) {
            $t->set_file(array("pers1" => "personen1L.tpl"));
            $t->set_block("pers1","Liste","Block");
            $i=0;
            if ($_POST["FID1"]) { 
                $snd="<input type='submit' name='insk' value='.:allocate:.'><br>[<a href='firma2.php?Q=$Quelle&fid=".$_POST["FID1"]."'>.:back:.</a>]";  
            } else { 
                $snd=""; $dest=""; 
            };
            clearCSVData();
            $header = array("ANREDE","TITEL","NAME1","NAME2","LAND","PLZ","ORT","STRASSE","TEL","FAX","EMAIL","FIRMA","FaID","GESCHLECHT","ID");
            $sql = "select name from custom_variable_configs where module = 'CT'";
            $rs = $db->getAll($sql);
            if ($rs) {
                $cvar = 0;
                foreach ($rs as $row) {
                    $cvheader[] = "vc_cvar_".$row["name"];
                    $header[] = "VC_CVAR_".strtoupper($row["name"]);
                    $cvar++;
                };
            }  else {
                $cvar = false;
            }
            insertCSVData($header,-1);
             /*
             * An dieser Stelle suchen wir die entsprechenden Werte für die verschiedenen Anreden der vorhandenen Sprachen.
             * Die Routine befindet sich im Backend persLib.php
             * Die Daten hierfür kommen aus der Tabelle generic_translations ursprünglich ein xplace Commit s.a. xplace Rev. 7667
             */
            $anredenFrau = getCpAnredenGeneric('female');
            $anredenHerr = getCpAnredenGeneric('male');

            if ($daten) foreach ($daten as $zeile) { //Diese Algorithmus macht die Suche bei einer großen Trefferzahl langsam ...
                                                     // TODO executeMultiple ... ;-) jb 16.6.2009
                if ($zeile["cp_gender"] =="f"){
                    if ($zeile["language_id"]) {
                        $zeile["cp_greeting"]= $anredenFrau[$zeile["language_id"]];
                    } else {
                        $zeile["cp_greeting"]="Frau";
                    }
                } else if ($zeile["cp_gender"] =="m"){
                    if ($zeile["language_id"]) {
                        $zeile["cp_greeting"]= $anredenHerr[$zeile["language_id"]];
                    } else {
                        $zeile["cp_greeting"]="Herr";
                    }
                } else {
                        $zeile["cp_greeting"]="KEIN GESCHLECHT";
                }
                /*if ($zeile["cp_country"] == 'Deutschland'){ //Schnellanpassung für xplace. Kann wieder raus, da dies in der Druckvorlage gesetzt wird
                                                              //@Jan: Des war ja nun wohl erst recht Nonsens
                $zeile["cp_country"]='';
                }*/

                /* 
                 * Der Blog ist sowieso gut und sollte mal hier angemerkt werden 'google: "mokka mit schlag"
                 * http://cafe.elharo.com/optimization/how-to-write-network-backup-software-a-lesson-in-practical-optimization/
                */
                $save = array($zeile["cp_greeting"],$zeile["cp_title"],$zeile["cp_name"],$zeile["cp_givenname"],
                        $zeile["cp_country"],$zeile["cp_zipcode"],$zeile["cp_city"],$zeile["cp_street"],
                        $zeile["cp_phone1"],$zeile["cp_fax"],$zeile["cp_email"],$zeile["name"],$zeile["cp_cv_id"],
                        $zeile["cp_gender"],$zeile["cp_id"]); 
                if ($cvar>0) {
                    $rs = getFirmaCVars($zeile["cp_cv_id"]);
                    if ($rs) {
                        foreach($cvheader as $cvh) {
                            $save[] = $rs[$cvh];
                        }
                    } else {
                        for ($i=0; $i<$cvar; $i++) $save[] = false;
                    }
                }
                insertCSVData($save,$zeile["cp_id"]);
                if ($_POST["FID1"]) {
                    $insk="<input type='checkbox' name='kontid[]' value='".$zeile["cp_id"]."'>"; 
                    $js="";
                } else { 
                    $js='showK('.$zeile["cp_id"].',"'.$zeile["tbl"].'");'; //showK({PID},'{TBL}')
                    $insk=""; 
                };
                if ($i<$_SESSION['listLimit']) {
                    $t->set_var(array(
                        js => $js,
                        LineCol => ($i%2+1),
                        Name => $zeile["cp_name"].", ".$zeile["cp_givenname"],
                        Plz => $zeile["cp_zipcode"],
                        Ort => $zeile["cp_city"],
                        Telefon => $zeile["cp_phone1"],
                        eMail => $zeile["cp_email"],
                        Firma => $zeile["name"],
                        insk => $insk,
                        DEST => $dest,
                        QUELLE => $Quelle,
                        Q => $Quelle,
                    ));
                    $t->parse("Block","Liste",true);
                    $i++;
                    if ($i>=$_SESSION['listLimit']) {
                        $t->set_var(array(
                            report => $_SESSION['listLimit']." von ".count($daten)." Treffern",
                        ));
                    }
                    $t->set_var(array(
                        ERPCSS      => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
                    ));

                }
            }
            $t->set_var(array(
                snd => $snd,
                FID => $_POST["FID1"]
            ));
        } else {
            $msg="Sorry, not found.";
            $btn1="";
            vartplP($t,$_POST,$msg,$btn1,$btn1,$btn1,"Anrede","white",$_POST["FID1"],1);
        }
    } else {
        leertplP($t,$_GET["fid"],"",1,false,$Quelle);
    }
    $t->Lpparse("out",array("pers1"),$_SESSION["lang"],"firma");
?>
