<!-- $Id: firma3a.tpl,v 1.5 2005/11/02 11:35:45 hli Exp $ -->
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
		<td width="25%" class="menueD reiter ce">
			<a href="{Link1}" >Kundendaten</a>
		</td>
		<td width="25%" class="menueD reiter ce">
			<a href="{Link2}" >Kontakte</a>
		</td>
		<td width="25%" class="menueA reiter ce">
			<a href="{Link3}" class="reiterA">Ums&auml;tze</a>
		</td>
		<td width="25%" class="menueD reiter ce">
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
Ums&auml;tze/Angebote von Monat {Monat}
<table><tr><td width="180px">
	<table>
	<tr><th class="smal" width="10%">Datum</th><th class="smal">Nummer</th><th class="smal">Netto</th><th class="smal">Brutto</th><th class="smal" width="10%"></th><th class="smal">Art</th><th class="smal">OP</th></tr>
<!-- BEGIN Liste -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showP('{Typ}{RNid}','{RNr}');">
		<td class="smal">{Datum}</td><td class="smal">&nbsp;{RNr}&nbsp;</td><td class="smal re">{RSumme}&nbsp;&nbsp;</td><td class="smal re">{RBrutto}&nbsp;</td><td class="smal">{Curr}</td><td class="smal">&nbsp;{Typ}</td><td class="smal">&nbsp;{offen}</td>
	</tr>
<!-- END Liste -->
	<tr><td class="smal" colspan="6"><b>R</b>echnung, <b>A</b>ngebot, <b>L</b>ieferung/Auftrag</td></tr>
	</table>
	
<!-- Hier endet die Karte ------------------------------------------->
</td><td width="*">&nbsp;</td></tr></table>
</body>
</html>
