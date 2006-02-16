<!-- $Id: firma2L.tpl,v 1.4 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showK (id) {
		Frame=eval("parent.main_window");
		uri="firma2.php?id=" + id;
		Frame.location.href=uri;
	}
	//-->
	</script>
<body>
<p class="listtop">Detailansicht</p>
<table class="reiter">
	<tr>
		<td class="reiter desel">
			<a href="firma1.php?id={FID}" >Kundendaten</a>
		</td>
		<td class="reiter sel">
			<a href="firma2.php?fid={FID}" class="reiterA">Kontakte</a>
		</td>
		<td class="reiter desel">
			<a href="firma3.php?fid={FID}" >Ums&auml;tze</a>
		</td>
		<td class="reiter desel">
			<a href="firma4.php?fid={FID}">Dokumente</a>
		</td>
	</tr>
</table>

<table class="karte"><tr><td class="karte">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="firma2" action="{action}" method="post">
<table class="stamm" style="width:230px">
	<tr title="Firmenanschrift"><td class="smal bold">{Fname1}</td><td class="smal bold" title="Kundenummer">{KDNR}</td></tr>
	<tr title="Firmenanschrift"><td class="smal bold">{Fdepartment_1}</td><td></td></tr>
	<tr title="Firmenanschrift"><td class="smal bold">{Plz} {Ort}</td><td></td></tr>
</table>
[<a href="personen3.php?fid={FID}&Quelle=F" class="bold">Kontakt eingeben</a>] - [<a href="personen1.php?fid={FID}&Quelle=F" class="bold">Kontakt aus Liste</a>]
<br><br>
<table class="liste">
<!-- BEGIN Liste -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showK({KID});" colspan="0">
		<td class="smal"> {Nname}, {Vname}</td><td class="smal">{Anrede} {Titel}</td><td class="smal">{Tel}</td><td class="smal">{eMail}</td></tr>
<!-- END Liste -->
</table>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
