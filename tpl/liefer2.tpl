<!-- $Id: liefer2.tpl,v 1.3 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
		function showItem(id) {
			F1=open("getCall.php?Q=VC&pid={PID}&id="+id,"Caller","width=610, height=600, left=100, top=50, scrollbars=yes");
		}
		function anschr() {
			F1=open("showAdr.php?PID={PID}","Adresse","width=340, height=400, left=100, top=50, scrollbars=yes");
		}
		function notes() {
			F1=open("showNote.php?pid={PID}","Notes","width=400, height=400, left=100, top=50, scrollbars=yes");
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
<!-- Beginn Code ------------------------------------------->
<table><tr><td width="380px">
	<table class="stamm" height="410px">
		<tr title="Lieferantenanschrift"><td class="smal bold">{Lname}</td>
					<td class="smal bold" title="Lieferantennummer">{LInr}</td></tr>
		<tr title="Lieferantenanschrift"><td class="smal bold">{Ldepartment_1}</td>	<td></td></tr>
		<tr title="Lieferantenanschrift"><td class="smal bold">{Street}</td>		<td></td></tr>
		<tr title="Lieferantenanschrift"><td class="smal bold">{Plz} {Ort}</td>		<td></td></tr>
		<tr><td class="stamm" colspan="2"><hr></td></tr>
		<tr><td class="stamm">{Anrede} {Titel}</td>
				<td class="stamm re" title="interne ID">{PID}</td></tr>
		<tr><td class="stamm">{Vname} {Nname}</td>
				<td class="stamm re" rowspan="2" title="Briefanschrift & Etikett"><a href="#" onCLick="anschr();"><img src="image/brief.gif" border="0"></a></td></tr>
		<tr><td class="stamm">{StreetC}</td></tr>
		<tr><td class="stamm"></td>							<td></td></tr>
		<tr><td class="stamm">{LandC}{PlzC} {OrtC}</td>
				<td class="stamm re" title="Geburtsdatum">{GebDat}</td></tr>
		<tr><td class="stamm">Tel: {Telefon}</td>					<td></td></tr>
		<tr><td class="stamm">Mob: {Mobile}</td>					<td></td></tr>
		<tr><td class="stamm" colspan="2"><a href="mailto:{eMail}">{eMail}</a></td></tr>
		<tr class="topbot"><td class="stamm re" title="Abteilung">{Abteilung}</td>
				<td class="stamm re" title="Position">{Position}</td></tr>
		<tr><td class="stamm" colspan="2">[<a href="javascript:notes()">Notiz</a>]</td></tr>
	</table>
</td><td width="420px">
	<table class="stamm" height="410px">
<!-- BEGIN Liste -->
		<tr  class="smal" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showItem({IID});">
			<td width="100px">{Datum} {Zeit}</td><td>{Nr}</td><td>{Betreff}</td><td>{Name}</td></tr>
<!-- END Liste -->
		<tr><td colspan="4" style="vertical-align:bottom;"><a href="liefer2.php?id={PID}" class="bold">refresh</a></td></tr>
	</table>
</td></tr></table>
[<a href="personen3.php?id={PID}&edit=1&Quelle=L" class="bold">{Edit}</a>] [<a href="personen3.php?fid={FID}&Quelle=L" class="bold">Kontakt eingeben</a>] - [<a href="personen1.php?fid={FID}&Quelle=L" class="bold">Kontakt aus Liste</a>]
<!-- End Code ------------------------------------------->
</td></tr></table>
</body>
</html>
