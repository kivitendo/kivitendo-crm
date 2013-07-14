<?php
    include_once("../inc/stdLib.php");
    include("../inc/template.inc");
    include("../inc/FirmenLib.php");
    include_once("../inc/UserLib.php");
    //print_r($_POST);
    if ( isset($_GET['Q']) and $_GET['Q'] != '') { $Q = $_GET['Q']; }
    else if ( isset($_POST['Q']) and $_POST['Q'] != '') { $Q = $_POST['Q']; }
    else { $Q = 'C'; };
    $t = new Template($base);
    //doHeader($t);
    if ( isset($_POST["felder"]) && $_POST["felder"] != '' ) {
        $rc = doReport($_POST,$Q);
        $t->set_file(array("fa1" => "companies1.tpl"));
        if ($rc) { 
            $tmp="<div style='width:300px'>[<a href='tmp/report_".$_SESSION["loginCRM"].".csv'>download Report</a>]</div>";
        } else {
            $tmp="Sorry, not found";
        }
        $t->set_var(array( 
                'report' => $tmp
        ));
        leertpl($t,1,$Q,"",true,true);
    } else if ( (isset($_POST["suche"]) and $_POST["suche"] !="") || isset($_POST["first"]) ) {
        if ( isset($_POST["first"]) ) {
            $daten = getAllFirmen(array(1,$_POST["first"]),false,$Q);
        } else {
            $daten = suchFirma($_POST,$Q);
        };
        if (count($daten) == 1 && $daten <> false) {
            echo '<script> showD("'.$Q.'","'.($daten[0]["id"]).'");</script>';
        } else if ( count($daten)>1 ) {
            $t->set_file(array("fa1" => "companies1Result.tpl"));
            $t->set_block("fa1","Liste","Block");
            $t->set_var(array(
                'FAART' => ($Q=="C")?"Customer":"Vendor", 
            ));
            $i=0;
            $rc = clearCSVData();
            $header = array('ANREDE', 'NAME1', 'NAME2', 'LAND', 'PLZ', 'ORT', 'STRASSE', 'TEL', 'FAX', 'EMAIL', 'KONTAKT', 'ID',
                            'KDNR', 'USTID', 'STEUERNR', 'KTONR', 'BANK', 'BLZ', 'LANG', 'KDTYP');
            if ( isset($_POST['umsatz']) and $_POST['umsatz'] != '' ) $header[] = "UMSATZ";
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
            insertCSVData($header,-255);
            if ($daten) foreach ($daten as $zeile) {
                $data = array($zeile["greeting"],$zeile["name"],$zeile["department_1"],
                        $zeile["country"],$zeile["zipcode"],$zeile["city"],$zeile["street"],
                        $zeile["phone"],$zeile["fax"],$zeile["email"],$zeile["contact"],$zeile["id"],
                        ($Q=="C")?$zeile["customernumber"]:$zeile["vendornumber"],
                        $zeile["ustid"],$zeile["taxnumber"],
                        $zeile["account_number"],$zeile["bank"],$zeile["bank_code"],
                        $zeile["language_id"],$zeile["business_id"]);    
                if ( isset($_POST["umsatz"]) and $_POST['umsatz'] != '' ) $data[]=$zeile["umsatz"];
                if ($cvar>0) {
                    $rs = getFirmaCVars($zeile["id"]);
                    if ($rs) {
                        foreach($cvheader as $cvh) {
                            if ( isset($rs[$cvh]) and $rs[$cvh] != '' ) {
                                $data[] = $rs[$cvh];
                            } else {
                                $data[] = '';
                            }
                        }
                    } else {
                        for ($j=0; $j<$cvar; $j++) $data[] = false;
                    }
                }
                insertCSVData($data,$zeile["id"]);
                if ( $i <= $_SESSION['listLimit'] ) {
                    $t->set_var(array(
                        'Q' => $Q,
                        'ID' => $zeile["id"],
                        'LineCol' => ($i%2)+1,
                        'KdNr' => ($Q=="C")?$zeile["customernumber"]:$zeile["vendornumber"],
                        'Name' => $zeile["name"],
                        'Plz' => $zeile["zipcode"],
                        'Ort' => $zeile["city"],
                        'Strasse' => $zeile["street"],
                        'Telefon' => $zeile["phone"],
                        'eMail' => $zeile["email"],
                        'obsolete' => ($zeile['obsolete']=='t')?'.:yes:.':''
                    ));
                    $t->parse("Block","Liste",true);
                    $i++;
                    if ( $i >= $_SESSION['listLimit'] ) {
                        $t->set_var(array(
                            'report' => $_SESSION['listlimit'].' von '.count($daten).' Treffer',
                        ));
                    }
                    $t->set_var(array(
                        'CRMTL' => ($_SESSION['CRMTL'] == 1)?'visible':'hidden'
                    ));
                }
            }
        } else {
            ;//Nichts gefunden
        }
        if ( $i > $_SESSION['listLimit'] ) 
             echo '<script>$( "#dialog_viele" ).dialog( "open" );</script>';
    } else {
        leertpl($t,1,$Q,"",true,true);
    }

    $t->Lpparse("out",array("fa1"),$_SESSION["lang"],"firma");
?>
