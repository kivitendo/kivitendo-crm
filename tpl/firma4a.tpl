<!-- $Id: firma4a.tpl,v 1.4 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	//-->
	</script>
<body>

<table class="reiter">
	<tr>
		<td class="reiter desel">
			<a href="{Link1}" >Kundendaten</a>
		</td>
		<td class="reiter desel">
			<a href="{Link2}" >Kontakte</a>
		</td>
		<td class="reiter desel">
			<a href="{Link3}" >Ums&auml;tze</a>
		</td>
		<td class="reiter sel">
			<a href="{Link4}" class="reiterA">Dokumente</a>
		</td>
	</tr>
</table>
<p class="listtop">Dokumenterstellung</p>
<table width="99%" class="karte"><tr><td class="karte">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="firma4" action="firma4a.php" method="post">
<input type="hidden" name="docid" value="{DOCID}">
<input type="hidden" name="fid" value="{FID}">
<input type="hidden" name="pid" value="{PID}">
<table class="stamm" style="width:290px">
	<tr title="Firmenanschrift"><td class="smal bold">{Name}</td><td class="smal bold">{KDID}</td></tr>
	<tr title="Firmenanschrift"><td class="smal bold">{Plz} {Ort}</td><td></td></tr>
	<tr title="Dokumentenart"><td class="smal bold">{Art}</td><td></td></tr>
</table>
{Beschreibung}<br>
<table>
<!-- BEGIN Liste -->
	<tr><td>{Feldname} </td><td title="Eingabe zu {Feldname}">&nbsp;{EINGABE}</td></tr>
<!-- END Liste -->
</table><br>
{Knopf}
</form>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
