<!-- $Id: liefer2L.tpl,v 1.3 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showK (id) {
		Frame=eval("parent.main_window");
		uri="liefer2.php?id=" + id;
		Frame.location.href=uri;
	}
	//-->
	</script>
<body>
<p class="listtop">Detailansicht</p>
<table class="reiter">
	<tr>
		<td class="reiter desel">
			<a href="{Link1}" >Lieferantendaten</a>
		</td>
		<td class="reiter sel">
			<a href="{Link2}" class="reiterA">Kontakte</a>
		</td>
		<td class="reiter desel">
			<a href="{Link3}" >Ums&auml;tze</a>
		</td>
		<td class="reiter desel">
			<a href="{Link4}">Dokumente</a>
		</td>
	</tr>
</table>

<table class="karte"><tr><td class="karte">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="liefer2" action="{action}" method="post">
<table class="stamm">
	<tr title="Lieferantenanschrift"><td class="smal bold">{Name}</td>
			<td class="smal re bold" title="Lieferantennummer">{LInr}</td></tr>
	<tr title="Lieferantenanschrift"><td class="smal bold">{Plz} {Ort}</td><td></td></tr>
</table>
<br>
<table class="liste">
<!-- BEGIN Liste -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showK({KID});">
		<td class="smal">{Nname}, {Vname}</td><td class="smal">{Anrede} {Titel}</td><td class="smal">{Tel}</td><td class="smal">{eMail}</td></tr>
<!-- END Liste -->
</table>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
