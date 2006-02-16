<!-- $Id: firma3.tpl,v 1.5 2005/11/02 11:35:45 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showM (month) {
		Frame=eval("parent.main_window");
		uri="firma3.php?monat=" + month + "&fid=" + {FID};
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
<form name="firma3" action="{action}" method="post">
<table class="stamm" style="width:230px">
	<tr title="Firmenanschrift"><td class="smal bold">{Name}</td><td class="smal bold" title="Kundennummer">{KDNR}</td></tr>
	<tr title="Firmenanschrift"><td class="smal bold">{Plz} {Ort}</td><td></td></tr>
</table>
<br>
Nettoums&auml;tze der letzten 12 Monate
<table><tr><td width="160px">
	<table>
	<tr><th class="smal" width="10%">Monat</th><th class="smal"></th><th class="smal">Umsatz</th><th class="smal">Angebot</td><td width="10%"></td></tr>
<!-- BEGIN Liste -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showM('{Month}');">
		<td class="smal">{Month}</td><td class="smal">{Rcount}</td><td class="smal re">{RSumme}</td><td class="smal re">{ASumme}</td><td class="smal">{Curr}</td>
	</tr>
<!-- END Liste -->
	</table>
	</td><td width="50px">&nbsp;</td><td>
	<img src="{IMG}" width="500" height="280" title="Umsatzdaten der letzten 12 Monate">
	</td></tr></table>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
