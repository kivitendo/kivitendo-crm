<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<link type="text/css" REL="stylesheet" HREF="css/tabcontent.css"></link>
	<script language="JavaScript">
	<!--
	function chkfld() {
<!-- BEGIN RegEx -->
		if (! document.firma4.{fld}.value.match(/^{regul}*$/)) { alert("{fld}"); return false; };
<!-- END RegEx -->
		return true;
	}
	//-->
	</script>
<body>

<p class="listtop">Dokumenterstellung</p>
<span style="position:absolute; left:10px; top:10px; width:98%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="firma4" action="firma4a.php" method="post" onsubmit="return chkfld();">
<input type="hidden" name="docid" value="{DOCID}">
<input type="hidden" name="fid" value="{FID}">
<input type="hidden" name="pid" value="{PID}">
<input type="hidden" name="tab" value="{TAB}">
<div style="position:absolute; left:1px; top:30px; width:100%; text-align:left; border: 0px solid black;" class="normal">
{Beschreibung}<br>
<table>
<!-- BEGIN Liste -->
	<tr><td>{Feldname} </td><td title="Eingabe zu {Feldname}">&nbsp;{EINGABE}</td></tr>
<!-- END Liste -->
</table><br>
{Knopf}
</div>
</form>
<!-- Hier endet die Karte ------------------------------------------->
</span>
</body>
</html>
