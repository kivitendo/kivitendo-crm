<?php
	require_once("inc/stdLib.php");
	$ort=$_GET["ort"];
	$bank=$_GET["bank"];
	//Umlaute wandeln hängt von der Serverumgebung ab!!
	//$loc_de = setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
	$blz=$_GET["blz"];
	$plz=$_GET["plz"];
	$wo=$_GET["wo"];
	$mitort=$_GET["mitort"];
	$sql="SELECT * from blz_data where ";
	if ($blz) {
	  	$sql.="blz like '$blz%' ";
	}
	if ($bank) {
		$bank=strtoupper($_GET["bank"]);
		if ($blz) $sql.="and ";
		$sql.="UPPER(kurzbez) like '%$bank%' ";
	}
	if ($ort and $mitort) {
	  	$ort=strtoupper($ort);
	  	if ($blz or $bank) $sql.="and ";
	  	$sql.="UPPER(ort) like '%$ort%' ";
	} 
	if ($plz and $mitort) {
	  	if ($bank or $blz or $ort) $sql.="and ";
	  	$sql.="plz like '$plz%' ";
	} 
	$sql.="order by plz,kurzbez";
	$rs=$_SESSION['db']->getAll($sql);
        $menu =  $_SESSION['menu'];
?>
<html>
<head><title></title>
    <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['baseurl'].'css/'.$_SESSION["stylesheet"]; ?>/main.css"></link>
    <!-- ERP Stylesheet -->
    <?php echo $menu['stylesheets']; ?>
	<script language="JavaScript">
	<!--
	var wo = '<?php echo  $wo ?>';
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
</head>
<body onLoad="self.focus()">
<center>Gefundene - Eintr&auml;ge:<br><br>
<form name="firmen">
<select name="Alle" >
<?php
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
