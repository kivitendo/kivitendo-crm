<!-- $Id: liefer3.tpl,v 1.3 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showM (month) {
		Frame=eval("parent.main_window");
		uri="liefer3.php?monat=" + month + "&fid=" + {FID};
		Frame.location.href=uri;
	}
	//-->
	</script>
<body>
<p class="listtop">Detailansicht</p>
<table class="reiter">
	<tr>
		<td class="reiter desel">
			<a href="{Link1}" >Kundendaten</a>
		</td>
		<td class="reiter desel">
			<a href="{Link2}" >Kontakte</a>
		</td>
		<td class="reiter sel">
			<a href="{Link3}" class="reiterA">Ums&auml;tze</a>
		</td>
		<td class="reiter desel">
			<a href="{Link4}">Dokumente</a>
		</td>
	</tr>
</table>

<table class="karte"><tr><td class="karte">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="liefer3" action="{action}" method="post">
<table class="stamm" style="width:260px">
	<tr title="Lieferantenanschrift"><td class="smal bold">{Name}</td>
			<td class="smal re bold" title="Lieferantennummer">{LInr}</td></tr>
	<tr title="Lieferantenanschrift"><td class="smal bold">{Plz} {Ort}</td><td></td></tr>
</table>
<br>
Ums&auml;tze der letzten 12 Monate
<table><tr><td><table width="160px">
	<tr><th class="smal" width="10%">Monat</th><th></th><th class="smal">Umsatz</th><td width="10%"></td></tr>
<!-- BEGIN Liste -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showM('{Month}');">
		<td class="smal" width="10%">{Month}</td><td class="smal">{Rcount}</td><td class="smal re">{RSumme}</td><td class="smal" width="10%">{Curr}</td>
	</tr>
<!-- END Liste -->
</table></td><td class="smal" width="50px">&nbsp;</td><td>
	<img src="{IMG}" width="500" height="280" title="Umsatz der letzten 12 Monate">
</td></tr></table>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
