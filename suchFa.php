<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/FirmenLib.php");
	include("inc/persLib.php");

	$pers=$_GET["pers"];
	$op=$_GET["op"];
?>
<html>
	<script language="JavaScript">
	<!--
		function auswahl() {
			nr=document.firmen.Alle.selectedIndex;
			val=document.firmen.Alle.options[nr].value;
			txt=document.firmen.Alle.options[nr].text;
			opener.document.formular.name.value=txt;
<? if ($pers==1) { ?>
			opener.document.formular.cp_cv_id.value=val;
<? } else if ($op) { ?>
			opener.document.formular.fid.value=val.substr(1,val.length);
<? } else {?>
			opener.document.formular.cp_cv_id.value=val.substr(1,val.length);
<? } ?>
<? if ($nq==1) { ?>
			if (val.substr(0,1)=="L") {
				opener.document.formular.Quelle.value="L";
			} else {
				opener.document.formular.Quelle.value="F";
			}
<? } ?>
		}
	//-->
	</script>
<body onLoad="self.focus()">
<center>Gefundene - Eintr&auml;ge:<br><br>
<form name="firmen">
<select name="Alle" >
	<option value=''>Einzelperson</option>
<?
	$name=strtoupper($_GET["name"]);
	if ($name=="EINZELPERSON") $name="";
	$datenC=getAllFirmen(array(1,$name),true,"C");
	$datenL=getAllFirmen(array(1,$name),true,"V");
	if ($pers) {
		$datenP=getAllPerson(array(1,$name));
		if ($datenP) foreach ($datenP as $zeile) {
			echo "\t<option value='P".$zeile["cp_id"]."'>".$zeile["cp_name"].", ".$zeile["cp_givenname"]."</option>\n";
		}
	}
	if ($datenC) foreach ($datenC as $zeile) {
		echo "\t<option value='C".$zeile["id"]."'>".$zeile["name"]."</option>\n";
	}
	if ($datenL) foreach ($datenL as $zeile) {
		echo "\t<option value='L".$zeile["id"]."'>".$zeile["name"]."</option>\n";
	}

?>
</select><br>
<br>
<input type="button" name="ok" value="&uuml;bernehmen" onClick="auswahl()"><br>
<input type="button" name="ok" value="Fenster schlie&szlig;en" onClick="self.close();">
</form>
</body>
</html>
