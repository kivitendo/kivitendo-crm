<!-- $Id: firma2.tpl,v 1.5 2005/12/03 09:46:56 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
		function showItem(id) {
			F1=open("getCall.php?Q=CC&pid={PID}&id="+id,"Caller","width=670, height=600, left=100, top=50, scrollbars=yes");
		}
		function anschr() {
			F1=open("showAdr.php?pid={PID}{ep}","Adresse","width=350, height=400, left=100, top=50, scrollbars=yes");
		}
		function notes() {
                               F1=open("showNote.php?pid={PID}","Notes","width=400, height=400, left=100, top=50, scrollbars=yes");
                }
		function vcard(){
			document.location.href="vcardexp.php?pid={PID}";
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
<form name="firma2" action="{action}" method="post">
<table><tr><td width="380px">
	<table class="stamm" height="410px">
		<tr title="Firmenanschrift"><td class="smal bold">{Fname1}</td><td class="smal re bold" title="Kundennummer">{KDNR}</td></tr>
		<tr title="Firmenanschrift"><td class="smal bold">{Fdepartment_1}</td><td></td></tr>
		<tr title="Firmenanschrift"><td class="smal bold">{Plz} {Ort}</td><td></td></tr>
		<tr><td class="stamm" colspan="2"><hr></td></tr>
		<tr><td class="stamm">{Anrede} {Titel}</td>	<td rowspan="3" class="stamm re" title="Foto">{IMG}</td></tr>
		<tr><td class="stamm">{Vname} {Nname}</td></tr>
		<tr><td class="stamm">{StreetC}</td></tr>
		<tr><td class="stamm"></td>			<td rowspan="2" class="stamm re" title="Briefadresse & Etikett"><a href="#" onCLick="anschr();"><img src="image/brief.gif" border="0"></a></td></tr>
		<tr><td class="stamm">{LandC}{PlzC} {OrtC}</td></tr>
		<tr><td class="stamm">Tel: {Telefon}</td>	<td class="stamm re" title="Geburtsdatum">{GDate}</td></tr>
		<tr><td class="stamm" colspan="2">Mob: {Mobile}</td></tr>
		<tr><td class="stamm" colspan="2"><a href="mail.php?TO={eMail}&KontaktTO=K{PID}">{eMail}</a></td></tr>
		<tr><td class="stamm" colspan="2"><a href="{www}" target="_blank">{www}</a></td></tr>
		<tr class="topbot"><td class="stamm" title="Abteilung">{Abteilung}&nbsp;</td>	<td class="stamm" title="Position/Funktion">{Position}</td></tr>		
		<tr><td class="stamm">[<a href="javascript:notes()">Notiz</a>] [<a href="javascript:vcard()">VCard</a>]</td><td class="stamm re" title="interne ID">{PID}</td></tr>
	</table>
</td><td width="420px">
	<table class="stamm" height="410px">
<!-- BEGIN Liste -->
		<tr height="14px" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showItem({IID});">
			<td class="smal" width="100px">{Datum} {Zeit}</td><td class="smal" width="50px">{Nr}</td><td class="smal">{Betreff}</td><td class="smal">{Name}</td></tr>
<!-- END Liste -->
		<tr height="*"><td style="vertical-align:bottom;"> <a href="firma2.php?id={PID}" class="bold">neu laden</a></td></tr>
	</table>
</td></tr></table>
[<a href="personen3.php?id={PID}&edit=1&Quelle=F" class="bold">{Edit}</a>] [<a href="personen3.php?fid={FID}&Quelle=F" class="bold">Kontakt eingeben</a>] [<a href="personen1.php?fid={FID}&Quelle=F" class="bold">Kontakt aus Liste</a>]
<!-- End Code ------------------------------------------->
</td></tr></table>
</body>
</html>
