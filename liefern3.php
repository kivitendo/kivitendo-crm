<?
// $Id: liefern3.php,v 1.3 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/LieferLib.php");
	include("inc/UserLib.php");
	$t = new Template($base);
	if ($_POST["saveneu"]) {
		$rc=saveNeuLieferStamm($_POST,$_FILES);
		if ($rc>0) { header("location:liefern3.php?id=$rc&edit=1");}
		else { $msg="Fehler beim Sichern (".$rc.")"; };
		$btn1="";$btn2="";$_POST["id"]="";
		vartplL ($t,$_POST,$msg,$btn1,$btn2,3);
	} else if ($_POST["save"]) {
		if ($_POST["id"]) {
			$rc=saveLieferStamm($_POST,$_FILES);
		} else {
			$rc="Kein Bestandslieferant";
		}
		if (ereg("^[0-9]+$",$rc)) {
			$msg="Daten gesichert.";
			$_POST=getLieferStamm($rc,false);
			$x=preg_match("°(jpg|jpeg|gif|png)\$°i",$_FILES["Datei"]["name"],$regs);
			$_POST["grafik"]=$regs[1];
		} else {
			$msg="Fehler beim Sichern ( $rc )";
		};
		$btn1="<input type='submit' name='save' value='sichern'>";
		$btn2="<input type='submit' name='show' value='zur Anzeige'>";
		vartplL ($t,$_POST,$msg,$btn1,$btn2,3);
	} else if ($_POST["show"]) {
		header("location:liefer1.php?id=".$_POST["id"]);
	} else if ($_POST["edit"] || $_GET["edit"]) {
		if ($_POST["id"]) {
			$id=$_POST["id"];
		} else {
			$id=$_GET["id"];
		}
		$daten=getLieferStamm($id,false);
		$msg="Edit: <b>$id</b>";
		$btn1="<input type='submit' name='save' value='sichern'>";
		$btn2="<input type='submit' name='show' value='zur Anzeige'>";
		vartplL ($t,$daten,$msg,$btn1,$btn2,3);
	} else {
		$t->set_file(array("li1" => "liefern3.tpl"));
		leertplL ($t,3,"Neuer Lieferant");
	}
	$t->pparse("out",array("li1"));
?>
