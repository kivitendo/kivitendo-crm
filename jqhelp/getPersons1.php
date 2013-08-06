<?php
    require_once("../inc/stdLib.php");
    include("../inc/template.inc");
    include("../inc/persLib.php");
    include("../inc/laender.php");
    include_once("../inc/UserLib.php");
    include("../inc/FirmenLib.php");
    $t = new Template($base);
    doHeader($t);   
    $Quelle=(isset($_POST["Quelle"]))?$_POST["Quelle"]:'';
    if (!$Quelle) $Quelle="C";
    if (isset($_POST["first"])) {
        $_POST["cp_name"] = $_POST["first"];
        $_POST["fuzzy"]="%";
    }
    if (isset($_POST["suche"]) || isset($_POST["first"])) {
        $daten=suchPerson($_POST);
        //print_r( $daten );
        if ( count($daten) > $_SESSION['listLimit'] ) {
            echo '<script>$( "#dialog_viele" ).dialog( "open" );</script>';
        } if (count($daten)==1 && $daten<>false && !$_POST["FID1"]) { 
            echo '<script> showK__("'.($daten[0]["cp_id"]).'");</script>';
        } else if (count($daten) > 1) {
            $t->set_file(array("pers1" => "persons1Result.tpl"));
            $t->set_block("pers1","Liste","Block");
            $i=0;
            if ( isset( $_POST["FID1"] ) ) { 
                $snd="<input type='submit' name='insk' value='.:allocate:.'><br>[<a href='firma2.php?Q=$Quelle&fid=".$_POST["FID1"]."'>.:back:.</a>]";  
            } else { 
                $snd=""; //$dest=""; 
            };
            clearCSVData();
            $header = array("ANREDE","TITEL","NAME1","NAME2","LAND","PLZ","ORT","STRASSE","TEL","FAX","EMAIL","FIRMA","FaID","GESCHLECHT","ID");
            $sql = "select name from custom_variable_configs where module = 'CT'";
            $rs = $_SESSION['db']->getAll($sql);
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
            $anredenFrau = getCpAnredenGeneric('female');
            $anredenHerr = getCpAnredenGeneric('male');

            if ($daten) foreach ($daten as $zeile) { 
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
                $save = array($zeile["cp_greeting"],$zeile["cp_title"],$zeile["cp_name"],$zeile["cp_givenname"],
                        $zeile["cp_country"],$zeile["cp_zipcode"],$zeile["cp_city"],$zeile["cp_street"],
                        $zeile["cp_phone1"],$zeile["cp_fax"],$zeile["cp_email"],$zeile["name"],$zeile["cp_cv_id"],
                        $zeile["cp_gender"],$zeile["cp_id"]); 
                if ($cvar>0) {
                    $rs = getFirmaCVars($zeile["cp_cv_id"]);
                    if ($rs) {
                        foreach($cvheader as $cvh) {
                            $save[] = ( isset($rs[$cvh]) ) ? $rs[$cvh] : false;
                        }
                    } else {
                        for ($i=0; $i<$cvar; $i++) $save[] = false;
                    }
                }
                insertCSVData($save,$zeile["cp_id"]);
               
                if ( isset( $_POST["FID1"] ) && $_POST["FID1"] ) {
                    $insk="<input type='checkbox' name='kontid[]' value='".$zeile["cp_id"]."'>"; 
                    $js="";
                } else { 
                    $js='showK('.$zeile["cp_id"].',"'.$zeile["tbl"].'");'; //showK({PID},'{TBL}')
                    $insk=""; 
                };
                if ($i<$_SESSION['listLimit']) {
                    $t->set_var(array(
                        'js'        => $js,
                        'LineCol'   => ($i%2+1),
                        'Name'      => $zeile["cp_name"].", ".$zeile["cp_givenname"],
                        'Plz'       => $zeile["cp_zipcode"],
                        'Ort'       => $zeile["cp_city"],
                        'Telefon'   => $zeile["cp_phone1"],
                        'eMail'     => $zeile["cp_email"],
                        'Firma'     => $zeile["name"],
                        'insk'      => $insk,
                        //'DEST'      => $dest,
                        'QUELLE'    => $Quelle, //zwei mal??
                        'Q'         => $Quelle,
                    ));
                    $t->parse("Block","Liste",true);
                    $i++;
                    if ($i>=$_SESSION['listLimit']) {
                        $t->set_var(array(
                            report => $_SESSION['listLimit']." von ".count($daten)." Treffern",
                        ));
                    }
                    $t->set_var(array(
                        'ERPCSS' => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
                    ));

                }
            }
            $t->set_var(array(
                'snd' => $snd,
                'FID' => isset($_POST["FID1"]) ? $_POST["FID1"] : '',
            ));
        } else {
            ;//nichts gefunden
        }
    } else {
        leertplP($t,"","",1,false,$Quelle,true);
    }
    $t->Lpparse("out",array("pers1"),$_SESSION["lang"],"firma");
?>
