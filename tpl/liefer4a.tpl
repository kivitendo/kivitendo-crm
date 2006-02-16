<!-- $Id: liefer4a.tpl,v 1.4 2005/12/01 08:14:26 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	//-->
	</script>
<body>
<p class="listtop">Detailansicht</p>
<table class="reiter">
	<tr>
		<td class="reiter desel">
                        <a href="{Link1}" >Lieferantendaten</a>
                </td>
                <td class="reiter desel">
                        <a href="{Link2}">Kontakte</a>
                </td>
                <td class="reiter desel">
                        <a href="{Link3}" >Ums&auml;tze</a>
                </td>
                <td class="reiter sel">
                        <a href="{Link4}" class="reiterA">Dokumente</a>
                </td>
	</tr>
</table>

<table class="karte"><tr><td class="karte">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="liefer4" action="liefer4a.php" method="post">
<input type="hidden" name="docid" value="{DOCID}">
<input type="hidden" name="fid" value="{FID}">
<input type="hidden" name="pid" value="{PID}">
<table class="stamm" style="width:280px">
	<tr title="Lieferantenanschrift"><td class="smal bold">{Name}</td>
			<td class="smal re bold" title="Lieferantennummer">{LInr}</td></tr>
	<tr title="Lieferantenanschrift"><td class="smal bold">{Plz} {Ort}</td><td></td></tr>
	<tr title="Dokumenttyp"><td class="smal bold">{Art}</td><td></td></tr>
</table>
{Beschreibung}<br>
<table>
<!-- BEGIN Liste -->
	<tr><td>{Feldname}</td><td title="Eingabe zu {Feldname}">&nbsp;{EINGABE}</td></tr>
<!-- END Liste -->
</table><br>
{Knopf}
</form>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
