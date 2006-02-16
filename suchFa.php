<?
// $Id: suchFa.php,v 1.4 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/FirmaLib.php");
	include("inc/LieferLib.php");
	include("inc/persLib.php");

	$pers=$_GET["pers"];
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
<?
	$name=strtoupper($_GET["name"]);
	$datenC=getAllCustomer(array(1,$name));
	$datenL=getAllVendor(array(1,$name));
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
