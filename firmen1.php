<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/FirmenLib.php");
	include("inc/UserLib.php");
	require("firmacommon".XajaxVer.".php");
	$Q=($_GET["Q"])?$_GET["Q"]:$_POST["Q"];
	$bgcol[1]="#ddddff";
	$bgcol[2]="#ddffdd";
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
			$t->set_block("fa1","Liste","Block");
			$t->set_var(array(
				AJAXJS  => $xajax->printJavascript(XajaxPath),
				FAART => ($Q=="C")?"Customer":"Vendor", 
			));
			$i=0;
			clearCSVData();
            $header = array("ANREDE","NAME1","NAME2","LAND","PLZ","ORT","STRASSE","TEL","FAX","EMAIL","KONTAKT","ID",
						"KDNR","USTID","STEUERNR","KTONR","BANK","BLZ","LANG","KDTYP");
            if ($_POST["umsatz"]) $header[]="UMSATZ";
			insertCSVData($header,-1);
			if ($daten) foreach ($daten as $zeile) {
                $data = array($zeile["greeting"],$zeile["name"],$zeile["department_1"],
						$zeile["country"],$zeile["zipcode"],$zeile["city"],$zeile["street"],
						$zeile["phone"],$zeile["fax"],$zeile["email"],$zeile["contact"],$zeile["id"],
						($Q=="C")?$zeile["customernumber"]:$zeile["vendornumber"],
						$zeile["ustid"],$zeile["taxnumber"],
						$zeile["account_number"],$zeile["bank"],$zeile["bank_code"],
						$zeile["language_id"],$zeile["business_id"]);	
                if ($_POST["umsatz"]) $data[]=$zeile["umsatz"];
				insertCSVData($data,$zeile["id"]);
                if ($i<$listLimit) {
                    $t->set_var(array(
                        Q => $Q,
                        ID => $zeile["id"],
                        LineCol => $bgcol[($i%2+1)],
                        KdNr => ($Q=="C")?$zeile["customernumber"]:$zeile["vendornumber"],
                        Name => $zeile["name"],
                        Plz => $zeile["zipcode"],
                        Ort => $zeile["city"],
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
					$t->set_var(array(
                        ERPCSS      => $_SESSION["stylesheet"],
					    ));
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
