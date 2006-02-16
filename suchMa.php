<?
// $Id: suchMa.php,v 1.3 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/wvLib.php");

	$masch=$_GET["masch"];
?>
<html>
	<script language="JavaScript">
	<!--
		function auswahl() {
			nr=document.firmen.Alle.selectedIndex;
			val=document.firmen.Alle.options[nr].value;
			txt=document.firmen.Alle.options[nr].text;
 			opener.document.formular.elements[14].value = txt;
 			opener.document.formular.elements[13].value = val; 			
		}
	//-->
	</script>
<body onLoad="self.focus()">
<center>Gefundene - Maschinen:<br><br>
<form name="firmen">
<select name="Alle" >
<?
	$daten=getAllMaschinen($masch);
	if ($daten) foreach ($daten as $zeile) {
		echo "\t<option value='".$zeile["id"]."'>".substr($zeile["description"],0,20)." | ".$zeile["serialnumber"]."</option>\n";
	}

?>
</select><br>
<br>
<input type="button" name="ok" value="&uuml;bernehmen" onClick="auswahl()"><br>
<input type="button" name="ok" value="Fenster schlie&szlig;en" onClick="self.close();">
</form>
</body>
</html>
