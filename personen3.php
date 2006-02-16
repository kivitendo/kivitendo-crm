<?
// $Id: personen3.php,v 1.3 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/laender.php");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	include("inc/persLib.php");
	$t = new Template($base);
	
	if ($_POST["show"]) {
		if ($_POST["Quelle"]=="L") {
			header("location:liefer2.php?id=".$_POST["PID"]);
		} else {
			header("location:firma2.php?id=".$_POST["PID"]);
		}
	} else if ($_POST["save"]||$_POST["neu"]) {
		if ($_POST["neu"]) { 
			$_POST["PID"]=0;
			$rc=savePersonStamm($_POST,$_FILES);
		} else {
			$rc=savePersonStamm($_POST,$_FILES);
		}
		if (ereg("^[0-9]+$",$rc)) {
			$msg="Daten gesichert.";
			$daten=getKontaktStamm(($_POST["PID"])?$_POST["PID"]:$rc);
			$daten["Quelle"]=$_POST["Quelle"];
			$btn1="<input type='submit' name='save' value='sichern update' tabindex='25'>";
			$btn2="<input type='submit' name='neu' value='sichern als neu'>";
			$btn3="<input type='submit' name='show' value='zur Anzeige'>";
			vartplP ($t,$daten,$msg,$btn1,$btn2,$btn3,"cp_givenname","white",0,3);
		} else {
			if ($_POST["PID"]) {
				$_POST["cp_id"]=$_POST["PID"];
				$btn1="<input type='submit' name='save' value='sichern update' tabindex='25'>";
				$btn3="<input type='submit' name='show' value='zur Anzeige'>";
			} else {
				$btn1="";
				$btn3="";
			}
			$msg="Fehler beim Sichern ($rc)";
			$btn2="<input type='submit' name='neu' value='sichern als neu'>";			
			vartplP ($t,$_POST,$msg,$btn1,$btn2,$btn3,$rc,"red",1,3);
		}
	} else if ($_POST["edit"] || $_GET["edit"]) {
		if ($_POST["id"]) {
			$id=$_POST["id"];
			$Quelle=$_POST["Quelle"];
		} else {
			$id=$_GET["id"];
			$Quelle=$_GET["Quelle"];
		}
		if (!$id) header("location:personen1.php");
		$daten=getKontaktStamm($id);
		$daten["Quelle"]=$Quelle;
		$msg="Edit: <b>$id</b>";
		$btn1="<input type='submit' name='save' value='sichern update' tabindex='25'>";
		$btn2="<input type='submit' name='neu' value='sichern als neu'>";
		$btn3="<input type='submit' name='show' value='zur Anzeige'>";
		vartplP ($t,$daten,$msg,$btn1,$btn2,$btn3,"cp_givenname","white",0,3);
	} else {
		$msg="Neue Person";
		leertplP($t,$_GET["fid"],$msg,3,true);
	}
	$t->pparse("out",array("pers1"));

?>
