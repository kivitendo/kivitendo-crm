<?php
// $Id: liefern1.php,v 1.4 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/LieferLib.php");
	include("inc/UserLib.php");
	$bgcol[1]="#ddddff";
	$bgcol[2]="#ddffdd";
	$t = new Template($base);
	if ($_POST["reset"]) {
		leertplL($t,1,"");
	} else	if ($_POST["suche"]=="suchen" || $_GET["first"]) {
		if ($_GET["first"]) {
			$daten=getAllVendor(array(1,$_GET["first"]),false);
		} else {
			$daten=suchLieferer($_POST);
		};
		if (count($daten)==1 && $daten<>false) {
			header ("location:liefer1.php?id=".$daten[0]["id"]);
		} else if (count($daten)>1) {
			$t->set_file(array("li1" => "liefern1L.tpl"));
			$t->set_block("li1","Liste","Block");
			$i=0;
			$fn=fopen("tmp/suche_".$_SESSION["loginCRM"].".csv","w");
			fputs($fn,"'ANREDE','NAME','DEPARTMENT_1','LAND','PLZ','ORT','STRASSE','TEL','MAIL'\n");
			if ($daten) foreach ($daten as $zeile) {
				fputs($fn,sprintf("'Firma','%s','%s','%s','%s','%s','%s','%s','%s'\n",
							$zeile["name"],$zeile["department_1"],
							$zeile["country"],$zeile["zipcode"],$zeile["city"],
							$zeile["street"],
							$zeile["phone"],$zeile["email"] ));
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
			fclose($fn);
		} else {
			$msg="Leider nichts gefunden.";
			leertplL ($t,1,$msg);
			vartplL ($t,$_POST,$msg,"","",1);
		}
	} else {
		leertplL ($t,1);
	}
	$t->pparse("out",array("li1"));
?>
