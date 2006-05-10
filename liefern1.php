<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/FirmenLib.php");
	include("inc/UserLib.php");
	$bgcol[1]="#ddddff";
	$bgcol[2]="#ddffdd";
	$t = new Template($base);
	if ($_POST["reset"]) {
		leertpl($t,1,"V","");
	} else if ($_POST["felder"]) {
		$rc=doReport($_POST,"V");
		$t->set_file(array("fa1" => "liefern1L.tpl"));
		if ($rc) { 
			$tmp="<div style='width:300px'>[<a href='tmp/report_".$_SESSION["loginCRM"].".csv'>Report</a>]</div>";
		} else {
			$tmp="Keine Treffer";
		}
		$t->set_var(array( 
				report => $tmp
		));
	} else	if ($_POST["suche"]=="suchen" || $_GET["first"]) {
		if ($_GET["first"]) {
			$daten=getAllFirmen(array(1,$_GET["first"]),false,"V");
		} else {
			$daten=suchFirma($_POST,"V");
		};
		if (count($daten)==1 && $daten<>false) {
			header ("location:liefer1.php?id=".$daten[0]["id"]);
		} else if (count($daten)>1) {
			$t->set_file(array("fa1" => "liefern1L.tpl"));
			$t->set_block("fa1","Liste","Block");
			$i=0;
			clearCSVData();
			insertCSVData(array("ANREDE","NAME1","NAME2","LAND","PLZ","ORT","STRASSE","TEL","FAX","EMAIL","KONTAKT","ID",
						"KDNR","USTID","STEUERNR","KTONR","BANK","BLZ","LANG","KDTYP"));
			if ($daten) foreach ($daten as $zeile) {
				insertCSVData(array("Firma",$zeile["name"],$zeile["department_1"],
							$zeile["country"],$zeile["zipcode"],$zeile["city"],$zeile["street"],
							$zeile["phone"],$zeile["fax"],$zeile["email"],$zeile["contact"],$zeile["id"],
							$zeile["customernumber"],$zeile["ustid"],$zeile["taxnumber"],
							$zeile["account_number"],$zeile["bank_code"],$zeile["bank"],
							$zeile["language"],$zeile["business_id"]));
				$t->set_var(array(
					FID => $zeile["id"],
					LineCol => $bgcol[($i%2+1)],
					Name => $zeile["name"],
					Plz => $zeile["zipcode"],
					Ort => $zeile["city"],
					Telefon => $zeile["phone"],
					eMail => $zeile["email"]
				));
				$t->parse("Block","Liste",true);
				$i++;
			}
		} else {
			$msg="Leider nichts gefunden.";
			leertpl ($t,1,"V",$msg);
			vartpl ($t,$_POST,"V",$msg,"","",1);
		}
	} else {
		leertpl ($t,1,"V");
	}
	$t->pparse("out",array("fa1"));
?>
