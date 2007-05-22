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
<? if ($pers==1) { ?>
			opener.document.formular.cp_cv_id.value=val;
			opener.document.formular.name.value=txt;
<? } else if ($op) { ?>
			opener.document.formular.fid.value=val.substr(1,val.length);
			opener.document.formular.name.value=txt;
<? } else if ($_GET["konzernname"]) {?>
			opener.document.neueintrag.konzern.value=val.substr(1,val.length);
			opener.document.neueintrag.konzernname.value=txt;
<? } else {?>
			opener.document.formular.cp_cv_id.value=val.substr(1,val.length);
			opener.document.formular.name.value=txt;
<? } ?>
<? if ($nq==1) { ?>
			if (val.substr(0,1)=="V") {
				opener.document.formular.Quelle.value="V";
			} else {
				opener.document.formular.Quelle.value="C";
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
	$name=($_GET["name"])?strtoupper($_GET["name"]):strtoupper($_GET["konzernname"]);
	if ($name=="EINZELPERSON") $name="";
	if ($_GET["tab"]) {
		$tmp="daten".$_GET["tab"];
		$datenC=getAllFirmen(array(1,$name),true,$_GET["tab"]);
	} else {
		$datenC=getAllFirmen(array(1,$name),true,"C");
		$datenL=getAllFirmen(array(1,$name),true,"V");
	}
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
