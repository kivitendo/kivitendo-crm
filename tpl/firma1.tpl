<!-- $Id: firma1.tpl,v 1.5 2005/12/03 09:46:56 hli Exp $ -->
<html>
	<head><title>Firma Stamm</title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
		function showItem(id) {
			F1=open("getCall.php?Q=C&fid={FID}&id="+id,"Caller","width=610, height=600, left=100, top=50, scrollbars=yes");
		}
		function anschr(A) {
			if (A==1) {
				F1=open("showAdr.php?fid={FID}","Adresse","width=350, height=400, left=100, top=50, scrollbars=yes");
			} else {
				F1=open("showAdr.php?sid={FID}","Adresse","width=350, height=400, left=100, top=50, scrollbars=yes");
			}
		}
		function notes() {
                                F1=open("showNote.php?fid={FID}","Notes","width=400, height=400, left=100, top=50, scrollbars=yes");
                }
		function vcard(){
			document.location.href="vcardexp.php?fid={FID}";
		}						
	//-->
	</script>
<body>
<p class="listtop">Detailansicht</p>
<table class="reiter">
	<tr>
		<td class="reiter sel">
			<a href="firma1.php?id={FID}" class="reiterA">Kundendaten</a>
		</td>
		<td class="reiter desel">
			<a href="firma2.php?fid={FID}" >Kontakte</a>
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
<!-- Beginn Code ----------------------------------------------->
<table ><tr><td width="380px">
	<table class="stamm" height="410px">
		<tr class="topbot"><td class="stamm" title="Kundentyp & Rabatt & Preisgruppe">{kdtyp} &nbsp; {rabatt} &nbsp; {preisgrp}</td>
				   <td class="stamm re">[<a href="javascript:vcard()">VCard</a>] [<a href="javascript:notes()">Notiz</a>]</td>
		<tr><td class="stamm">{Fname1}</td>		<td rowspan="3" class="stamm re" title="Firmenlogo">{IMG}</td></tr>
		<tr><td class="stamm">{Fdepartment_1}</td></tr>
		<tr><td class="stamm">{Strasse}</td></tr>
		<tr><td class="stamm">{Land}-{Plz} {Ort}</td>	<td class="stamm re" title="Briefanschrift & Etikett"><a href="#" onCLick="anschr(1);"><img src="image/brief.gif" border="0"></a></td></tr>
		<tr><td class="stamm">Tel: {Telefon}</td>	<td class="stamm re" title="Erstellungsdatum">{INID}</td></tr>
		<tr><td class="stamm">Fax: {Fax}</td>		<td class="stamm re" title="Kundennummer">{KDNR}</td></tr>
		<tr><td class="stamm">Steuer-Nr.: {USTID}</td>	<td class="stamm re"></td></tr>
		<tr><td class="stamm" title="Kreditlimit, offene Posten">Kredit: {kreditlim} OP: {op}</td>	
				<td class="stamm re" title="Zahlungsziel in Tagen">Ziel: {terms}</td></tr>
		<tr><td class="stamm" colspan="2"><a href="mail.php?TO={eMail}&KontaktTO=C{FID}">{eMail}</a></td></tr>
		<tr><td class="stamm" colspan="2"><a href="{Internet}" target="_blank">{Internet}</a></td></tr>

		<tr class="topbot"><td class="stamm" height="*" style="vertical-align:middle;" colspan="2" title="Wichtige MItteilung">{Cmsg}</td></tr>

		<tr><td class="stamm"><b>Lieferadresse</b></td>
		    <td class="stamm re" rowspan="2">
			<form action="../oe.pl" method="post">
	  		<input type="hidden" name="path" value="bin/mozilla">
			<input type="hidden" name="login" value="{login}">
			<input type="hidden" name="action" value="add">
			<input type="hidden" name="type" value="sales_order">
			<input type="hidden" name="password" value="{password}">
	  		<input type="hidden" name="customer_id" value="{FID}"><input type="submit" value="Auftrag" title="neuen Auftrag eingeben"></form>
		    </td></tr>
		<tr><td class="smal">{Sname1}</td></tr>
		<tr><td class="smal">{Sdepartment_1}</td>	<td rowspan="2" class="smal re" title="Briefanschrift & Etikett"><a href="#" onCLick="anschr(2);"><img src="image/brief.gif" border="0"></a></td></tr>
		<tr><td class="smal">{SStrasse}</td></tr>
		<tr><td class="smal" colspan="2">{SLand}-{SPlz} {SOrt}</td></tr>
		<tr><td class="smal" colspan="2">Tel: {STelefon}</td></tr>
		<tr><td class="smal" colspan="2">Fax: {SFax}</td></tr>
		<tr><td class="smal" colspan="2"><a href="mail.php?TO={SeMail}&KontaktTO=C{FID}">{SeMail}</a></td></tr>
	</table>
</td><td width="420px">
	<table class="stamm"height="410px">
<!-- BEGIN Liste -->
		<tr height="14px" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showItem({IID});">
			<td class="smal" width="100px">{Datum} {Zeit}</td><td class="smal" width="60px">{Nr}</td><td class="smal le">{Betreff}</td><td class="smal le">{Name}</td></tr>
<!-- END Liste -->
		<tr height="*"><td colspan="4" style="vertical-align:bottom;">	<a href="firma1.php?id={FID}&start={PREV}">&lt;</a> <a href="firma1.php?id={FID}&start={PAGER}" class="bold">neu laden</a> <a href="firma1.php?id={FID}&start={NEXT}">&gt;</a></td></tr>
	</table>
</td></tr></table>
<a href="firmen3.php?id={FID}&edit=1" class="bold">Bearbeiten</a>
<!-- End Code ----------------------------------------------->
</td></tr></table>
</body>
</html>
