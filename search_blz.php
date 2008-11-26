<?
	require_once("inc/stdLib.php");
	$ort=$_GET["ort"];
	//Umlaute wandeln hängt von der Serverumgebung ab!!
	//$loc_de = setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
	$blz=$_GET["blz"];
	$wo=$_GET["wo"];
	$sql="SELECT * from blz_data where ";
	if ($blz) {
	  	$sql.="blz like '%$blz%' ";
	}
	if ($ort) {
	  	$ort=strtoupper($ort);
	  	if ($blz) $sql.="and ";
	  	$sql.="UPPER(ort) like '%$ort%' ";
	} 
	if ($bank) {
		$bank=strtoupper($_GET["bank"]);
		if ($blz or $ort) $sql.="and ";
		$sql.="UPPER(kurzbez) like '%$bank%' ";
	}
	$sql.="order by plz,kurzbez";
	$rs=$db->getAll($sql);
?>
<html>
	<script language="JavaScript">
	<!--
	var wo = '<?= $wo ?>';
	function auswahl() {
		nr=document.firmen.Alle.selectedIndex;
		val=document.firmen.Alle.options[nr].value;
		tmp=val.split("--");		
		if (wo=="F") {
			opener.document.getElementById("blz").value=tmp[0];
			opener.document.getElementById("bank").value=tmp[1];
		}
	}
	//-->
	</script>
<body onLoad="self.focus()">
<center>Gefundene - Eintr&auml;ge:<br><br>
<form name="firmen">
<select name="Alle" >
<?
	if ($rs) foreach ($rs as $zeile) {
		$ort=$zeile["ort"]; 
		$kurz=$zeile["kurzbez"]; 
		$bank=$zeile["bezeichnung"]; 
		$blz=$zeile["blz"];
		echo "\t<option value='".$blz."--".$kurz."'>".$blz." ".$ort." ".$bank."</option>\n";
	}
?>
</select><br>
<br>
<input type="button" name="ok" value="&uuml;bernehmen" onClick="auswahl()"><br>
<input type="button" name="ok" value="Fenster schlie&szlig;en" onClick="self.close();">
</form>
</body>
</html>


