<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/FirmaLib.php");
	include("inc/LieferLib.php");
	include("inc/persLib.php");

?>
<html>
	<script language="JavaScript">
	<!--
		function auswahl() {
			nr=document.firmen.Alle.selectedIndex;
			val=document.firmen.Alle.options[nr].value;
			txt=document.firmen.Alle.options[nr].text;
			NeuerEintrag = new Option(txt,val,false,true);
			opener.document.getElementById("istusr").options[opener.document.getElementById("istusr").length] = NeuerEintrag;
		}
	//-->
	</script>
<body onLoad="self.focus()">
<center>Gefundene - Eintr&auml;ge:<br><br>
<form name="firmen">
<select name="Alle" >
<?
	$name=strtoupper($_GET["name"]);
	$daten=array_merge(getAllPerson(array(1,$name)),getAllCustomer(array(1,$name)),getAllVendor(array(1,$name)));
	if ($daten) foreach ($daten as $zeile) {
		echo "\t<option value='".$zeile["tab"].$zeile["id"]."'>".$zeile["name"]."</option>\n";
	}
?>
</select><br>
<br>
<input type="button" name="ok" value="&uuml;bernehmen" onClick="auswahl()"><br>
<input type="button" name="ok" value="Fenster schlie&szlig;en" onClick="self.close();">
</form>
</body>
</html>
