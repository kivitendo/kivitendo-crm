<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	//-->
	</script>
<body>
<p class="listtop">Detailansicht</p>
<div style="position:absolute; top:33px; left:8px;  width:770px;">
	<ul id="tabmenue">
	<li><a href="{Link1}">Lieferantendaten</a><li>
	<li><a href="{Link2}">Kontakte</a></li>
	<li><a href="{Link3}">Ums&auml;tze</a></li>
	<li><a href="{Link4}" id="aktuell">Dokumente</a></li>
	</ul>
</div>

<span style="position:absolute; left:10px; top:67px; width:98%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="liefer4" action="liefer4a.php" method="post">
<input type="hidden" name="docid" value="{DOCID}">
<input type="hidden" name="fid" value="{FID}">
<input type="hidden" name="pid" value="{PID}">
<div style="position:absolute; left:1px; width:450px; height:50px; text-align:left; border: 1px solid black;" class="fett">
		{Name} &nbsp; &nbsp; {KDNR}<br />
		{Plz} {Ort}<br />
		{Art}
</div>
<div style="position:absolute; left:1px; top:55px; width:100%; text-align:left; border: 0px solid black;" class="normal">
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
