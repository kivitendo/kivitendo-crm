<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/FirmenLib.php");
	include("inc/UserLib.php");
	$t = new Template($base);
	$t->set_file(array("fa1" => "liefern3.tpl"));
	if ($_POST["saveneu"]) {
		$rc=saveNeuFirmaStamm($_POST,$_FILES,"V");
		if ($rc[0]>0) { header("location:liefern3.php?id=".$rc[0]."&edit=1");}
		else { $msg="Fehler beim Sichern (".$rc[1].")"; };
		$btn1="";$btn2="";$_POST["id"]="";
		vartpl ($t,$_POST,"V",$msg,$btn1,$btn2,3);
	} else if ($_POST["save"]) {
		if ($_POST["id"]) {
			$rc=saveFirmaStamm($_POST,$_FILES,"V");
		} else {
			$rc[0]=-1; $rc[1]="Kein Bestandskunde";
		}
		if ($rc[0]>0) {
			$msg="Daten gesichert.";
			$_POST=getFirmenStamm($rc[0],false,"V");
			if ($_FILES["Datei"]["name"]) {
				$x=preg_match("°(jpg|jpeg|gif|png)\$°i",$_FILES["Datei"]["name"],$regs);
				$_POST["grafik"]=$regs[1];
			}
		} else {
			$msg="Fehler beim Sichern ( ".$rc[1]." )";
		};
		$btn1="<input type='submit' name='save' value='sichern'>";
		$btn2="<input type='submit' name='show' value='zur Anzeige'>";
		vartpl ($t,$_POST,"V",$msg,$btn1,$btn2,3);
	} else if ($_POST["show"]) {
		header("location:liefer1.php?id=".$_POST["id"]);
	} else if ($_POST["edit"] || $_GET["edit"]) {
		if ($_POST["id"]) {
			$id=$_POST["id"];
		} else {
			$id=$_GET["id"];
		}
		$daten=getFirmenStamm($id,false,"V");
		$msg="Edit: <b>$id</b>";
		$btn1="<input type='submit' name='save' value='sichern'>";
		$btn2="<input type='submit' name='show' value='zur Anzeige'>";
		vartpl ($t,$daten,"V",$msg,$btn1,$btn2,3);
	} else {
		$t->set_file(array("fa1" => "liefern3.tpl"));
		leertpl ($t,3,"V","Neuer Lieferant");
	}
	$t->pparse("out",array("fa1"));
?>
