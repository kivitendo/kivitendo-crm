<!-- $Id: liefer3a.tpl,v 1.3 2005/11/02 10:38:59 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showP (id,nr) {
		if (id!='') {
			Frame=eval("parent.main_window");
			f1=open("rechng.php?id="+id+"&nr="+nr,"rechng","width=600,height=420,left=10,top=10,scrollbars=yes");
		}
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
<table class="stamm" style="width:280px">
	<tr title="Lieferantenanschrift"><td class="smal bold">{Name}</td>
			<td class="smal re bold" title="Lieferantennummer">{LInr}</td></tr>
	<tr title="Lieferantenanschrift"><td class="smal bold">{Plz} {Ort}</td><td></td></tr>
</table>
<br>
Ums&auml;tze/Angebote von Monat {Monat}
<table class="liste"><tr><td width="160px"><table>
	<tr><th class="smal" width="10%">Datum</th><th class="smal">Nummer</th><th class="smal">Netto</th><th class="smal">Brutto</th><th class="smal" width="10%"></th></tr>
<!-- BEGIN Liste -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showP('V{RNid}','{RNr}');">
		<td class="smal" width="10%">{Datum}</td><td class="smal">{RNr}</td><td class="smal re">{RSumme}</td><td class="smal re">{RBrutto}</td><td class="smal" width="10%">{Curr}</td>
	</tr>
<!-- END Liste -->
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
