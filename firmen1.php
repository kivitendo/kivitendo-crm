<?php
// $Id$
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/FirmenLib.php");
    include("inc/UserLib.php");
    $Q=($_GET["Q"])?$_GET["Q"]:$_POST["Q"];
    $t = new Template($base);
    if ($_POST["reset"]) {
        leertpl($t,1,$Q,"",true);
    } else if ($_POST["felder"]) {
        $rc=doReport($_POST,$Q);
        $t->set_file(array("fa1" => "firmen1.tpl"));
        if ($rc) { 
            $tmp="<div style='width:300px'>[<a href='tmp/report_".$_SESSION["loginCRM"].".csv'>download Report</a>]</div>";
        } else {
            $tmp="Sorry, not found";
        }
        $t->set_var(array( 
                report => $tmp
        ));
        leertpl($t,1,$Q,"",true);
    } else if ($_POST["suche"]!="" || $_GET["first"]) {
        if ($_GET["first"]) {
            $daten=getAllFirmen(array(1,$_GET["first"]),false,$Q);
        } else {
            $daten=suchFirma($_POST,$Q);
        };
        if (count($daten)==1 && $daten<>false) {
            header ("location:firma1.php?Q=$Q&id=".$daten[0]["id"]);
        } else if (count($daten)>1) {
            $t->set_file(array("fa1" => "firmen1L.tpl"));
            $menu =  $_SESSION['menu']; 
            $t->set_var(array(
                JAVASCRIPTS   => $menu['javascripts'],
                STYLESHEETS   => $menu['stylesheets'],
                PRE_CONTENT   => $menu['pre_content'],
                START_CONTENT => $menu['start_content'],
                END_CONTENT   => $menu['end_content'],
                JQUERY        => $_SESSION['basepath'].'crm/',
                ERPCSS          => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
            ));
            $t->set_block("fa1","Liste","Block");
            $t->set_var(array(
                FAART => ($Q=="C")?"Customer":"Vendor", 
            ));
            $i=0;
            $rc = clearCSVData();
            $header = array("ANREDE","NAME1","NAME2","LAND","PLZ","ORT","STRASSE","TEL","FAX","EMAIL","KONTAKT","ID",
                        "KDNR","USTID","STEUERNR","KTONR","BANK","BLZ","LANG","KDTYP");
            if ($_POST["umsatz"]) $header[]="UMSATZ";
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
            insertCSVData($header,-255);
            if ($daten) foreach ($daten as $zeile) {
                $data = array($zeile["greeting"],$zeile["name"],$zeile["department_1"],
                        $zeile["country"],$zeile["zipcode"],$zeile["city"],$zeile["street"],
                        $zeile["phone"],$zeile["fax"],$zeile["email"],$zeile["contact"],$zeile["id"],
                        ($Q=="C")?$zeile["customernumber"]:$zeile["vendornumber"],
                        $zeile["ustid"],$zeile["taxnumber"],
                        $zeile["account_number"],$zeile["bank"],$zeile["bank_code"],
                        $zeile["language_id"],$zeile["business_id"]);    
                if ($_POST["umsatz"]) $data[]=$zeile["umsatz"];
                if ($cvar>0) {
                    $rs = getFirmaCVars($zeile["id"]);
                    if ($rs) {
                        foreach($cvheader as $cvh) {
                            $data[] = $rs[$cvh];
                        }
                    } else {
                        for ($j=0; $j<$cvar; $j++) $data[] = false;
                    }
                }
                insertCSVData($data,$zeile["id"]);
                if ($i<$listLimit) {
                    $t->set_var(array(
                        Q => $Q,
                        ID => $zeile["id"],
                        LineCol => ($i%2)+1,
                        KdNr => ($Q=="C")?$zeile["customernumber"]:$zeile["vendornumber"],
                        Name => $zeile["name"],
                        Plz => $zeile["zipcode"],
                        Ort => $zeile["city"],
                        Strasse => $zeile["street"],
                        Telefon => $zeile["phone"],
                        eMail => $zeile["email"]
                    ));
                    $t->parse("Block","Liste",true);
                    $i++;
                    if ($i>=$listLimit) {
                        $t->set_var(array(
                            report => "$listLimit von ".count($daten)." Treffern",
                        ));
                    }
                   
                }
            }
        } else {
            $msg="Sorry, not found.";
            vartpl ($t,$_POST,$Q,$msg,"","",1,true);
        }
    } else {
        leertpl($t,1,$Q,"",true);
    }

    $t->Lpparse("out",array("fa1"),$_SESSION["lang"],"firma");
?>
