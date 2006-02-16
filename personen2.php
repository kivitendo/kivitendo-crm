<?
// $Id: personen2.php,v 1.3 2005/11/02 10:37:52 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	include("inc/persLib.php");
	include("inc/laender.php");

	$t = new Template($base);
	if ($_POST["reset"]) {
		leertpl($t,$fid);
	} else if ($_POST["show"]) {
		if ($_POST["Quelle"]=="L") {
			header("location:liefer2.php?id=".$_POST["PID"]);
		} else {
			header("location:firma2.php?id=".$_POST["PID"]);
		}
	} else if ($_POST["save"]||$_POST["neu"]) {
		if ($_POST["neu"]) {
			$rc=saveNeuPersonStamm($_POST);
			if ($rc) $_POST["PID"]=$rc;
			$Quelle=$daten["Quelle"];
		} else {
			$rc=savePersonStamm($_POST);
		}
		if (ereg("^[0-9]+$",$rc)) {
			$msg="Daten gesichert.";
			$daten=getKontaktStamm($_POST["PID"]);
			$btn1="<input type='submit' name='save' value='sichern update'>";
			$btn2="<input type='submit' name='neu' value='sichern als neu'>";
			$btn3="<input type='submit' name='show' value='zur Anzeige'>";
			vartplP ($t,$daten,$msg,$btn1,$btn2,$btn3,"Anrede","white",0,2);
			//leertpl($t);
		} else {
			$msg="Fehler beim Sichern ($rc)";
			$btn1="<input type='submit' name='save' value='sichern update'>";
			$btn2="<input type='submit' name='neu' value='sichern als neu'>";
			$btn3="<input type='submit' name='show' value='zur Anzeige'>";
			vartplP (&$t,$_POST,$msg,$btn1,$btn2,$btn3,$rc,"red",0,2);
		}
	} else if ($_POST["edit"] || $_GET["edit"]) {
		if ($_POST["id"]) {
			$id=$_POST["id"];
			$Quelle=$_POST["Quelle"];
		} else {
			$id=$_GET["id"];
			$Quelle=$_GET["Quelle"];
		}
		if (!$id) header("location:personen3.php");
		$daten=getKontaktStamm($id);
		$daten["Quelle"]=$Quelle;
		$msg="Edit: <b>$id</b>";
		$btn1="<input type='submit' name='save' value='sichern update'>";
		$btn2="<input type='submit' name='neu' value='sichern als neu'>";
		$btn3="<input type='submit' name='show' value='zur Anzeige'>";
		vartplP ($t,$daten,$msg,$btn1,$btn2,$btn3,"Anrede","white",0,2);
	} else if ($_POST["suche"]=="suchen") {
		$daten=suchPerson($_POST);
		if (count($daten)==1 && $daten<>false) {
			if ($fid) {addKontakt($daten[0]["cp_id"],$fid);}
			header ("location:firma2.php?id=".$daten[0]["cp_id"]);
		} else if (!chkAnzahl($daten,$tmp)) {
			$msg="Trefferanzahl zu gro&szlig;. Bitte einschr&auml;nken.";
			$btn1="";
			vartplP($t,$_POST,$msg,$btn1,$btn1,$btn1,"Anrede","white",$_POST["FID1"],2);
		} else if (count($daten)>1) {
			$t->set_file(array("pers1" => "personen2L.tpl"));
			$t->set_block("pers1","Liste","Block");
			$i=0;
			$bgcol[1]="#ddddff";
			$bgcol[2]="#ddffdd";
			if ($_POST["FID1"]) { $snd="<input type='submit' name='insk' value='zuordnen'><br><br><a href='firma2.php?fid=".$_POST["FID1"]."'>zur&uuml;ck</a>"; } //<input type='checkbox' value='".$zeile["cp_id"]."'>alle 
			else { $snd=""; };
			if ($daten) foreach ($daten as $zeile) {
				if ($_POST["FID1"]) {$insk="<input type='checkbox' name='kontid[]' value='".$zeile["cp_id"]."'>"; } else { $insk=""; };
				$t->set_var(array(
				PID => $zeile["cp_id"],
				LineCol => $bgcol[($i%2+1)],
				Name => $zeile["cp_name"],
				Plz => $zeile["cp_zipcode"],
				Ort => $zeile["cp_city"],
				Telefon => $zeile["cp_phone1"],
				eMail => $zeile["cp_email"],
				Firma => $zeile["name"],
				TBL => $zeile["tbl"],
				insk => $insk
				));
				$t->parse("Block","Liste",true);
				$i++;
			}
			$t->set_var(array(
				snd => $snd,
				FID => $_POST["FID1"],
				no => ($_POST["FID1"])?"return;":"",
			));
		} else {
			$msg="Leider nichts gefunden.";
			$btn1="";
			vartplP($t,$_POST,$msg,$btn1,$btn1,$btn1,"Anrede","white",$_POST["FID1"],2);
		}
	} else {
		$msg="Neue Suche";
		leertplP($t,$_GET["fid"],$msg,2);
	}

	$t->pparse("out",array("pers1"));

?>
