<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
		function showItem(id) {
			F1=open("getCall.php?Q=V&&fid={FID}&id="+id,"Caller","width=610, height=600, left=100, top=50, scrollbars=yes");
		}
		function anschr(A) {
			if (A==1) {
 				F1=open("showAdr.php?lid={FID}","Adresse","width=350, height=400, left=100, top=50, scrollbars=yes");
			} else {
				F1=open("showAdr.php?sid={FID}","Adresse","width=350, height=400, left=100, top=50, scrollbars=yes");
			}
		}
		function notes() {
                                F1=open("showNote.php?lid={FID}","Notes","width=400, height=400, left=100, top=50, scrollbars=yes");
                }
		function ks() {
			sw=document.ksearch.suchwort.value;
			if (sw != "") 
				F1=open("suchKontakt.php?suchwort="+sw+"&Q=V&id={FID}","Suche","width=400, height=400, left=100, top=50, scrollbars=yes");
			return false;
		}
	//-->
	</script>
<body>
<p class="listtop">Detailansicht</p>
<table class="reiter">
	<tr>
		<td class="reiter sel">
			<a href="liefer1.php?id={FID}" class="reiterA">Lieferantendaten</a>
		</td>
		<td class="reiter desel">
			<a href="liefer2.php?fid={FID}" >Kontakte</a>
		</td>
		<td class="reiter desel">
			<a href="liefer3.php?fid={FID}" >Ums&auml;tze</a>
		</td>
		<td class="reiter desel">
			<a href="liefer4.php?fid={FID}">Dokumente</a>
		</td>
	</tr>
</table>

<table class="karte"><tr><td class="karte">
<!-- Beginn Code ------------------------------------------->
<table><tr><td width="380px">
	<table class="stamm" height="410px">
		<tr class="topbot"><td class="stamm" title="Lieferantentyp & Unser Rabatt">{lityp} {rabatt}</td>
					<td class="stamm re">[<a href="javascript:notes()">Notiz</a>]</td></tr>
		<tr><td class="stamm">{Lname}</td>
					<td class="stamm re" rowspan="3" title="Firmenlogo">{IMG}</td></tr>
		<tr><td class="stamm">{Ldepartment_1}</td></tr>
		<tr><td class="stamm">{Strasse}</td></tr>
		<tr><td class="stamm">{Land}-{Plz} {Ort}</td>
					<td class="stamm re" rowspan="2" title="Briefanschrift & Etikett"><a href="#" onCLick="anschr(1);"><img src="image/brief.gif" border="0"></a></td></tr>
		<tr><td class="stamm">&nbsp;</td></tr>
		<tr><td class="stamm">Tel: {Telefon}</td>
					<td class="stamm re" title="Erstellungsdatum">{INID}</td></tr>
		<tr><td class="stamm">Fax: {Fax}</td>
					<td class="stamm re" title="Lieferantennummer">{LInr}</td>
		<tr><td class="stamm">Steuer-Nr.: {USTID}</td>
					<td class="stamm re" title="Unsere Kundennummer">{KDnr}</td></tr>
		<tr><td class="stamm" colspan="2"><a href="mail.php?TO={eMail}&KontaktTO=V{FID}">{eMail}</a></td></tr>
		<tr><td class="stamm" colspan="2"><a href="{Internet}" target="_blank">{Internet}</a></td></tr>

		<tr class="topbot"><td class="stamm" height="*" style="vertical-align:middle;" colspan="2">{Cmsg}</td></tr>

		<tr><td class="smal"><b>Lieferadresse</b></td>	<td></td></tr>
		<tr><td class="smal">{Sname}</td>
					<td  class="smal re"rowspan="2"><a href="#" onCLick="anschr(2);" title="Briefanschrift & Etikett"><img src="image/brief.gif" border="0"></a></td></tr>
		<tr><td class="smal">{Sdepartment_1}</td></tr>
		<tr><td class="smal">{SStrasse}</td>		<td></td></tr>
		<tr><td class="smal">{SLand}-{SPlz} {SOrt}</td>	<td></td></tr>
		<tr><td class="smal">Tel: {STelefon}</td>	<td></td></tr>
		<tr><td class="smal">Fax: {SFax}</td>		<td></td></tr>
		<tr><td class="smal" colspan="2"><a href="mail.php?TO={SeMail}&KontaktTO=V{FID}">{SeMail}</a></td></tr>
	</table>
</td><td width="420px">
	<table class="stamm" height="410px">
<!-- BEGIN Liste -->
		<tr height="14px" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showItem({IID});">
			<td class="smal" width="100px">{Datum} {Zeit}</td><td class="smal" width="40px">{Nr}</td><td class="smal">{Betreff}</td><td class="smal">{Name}</td></tr>
<!-- END Liste -->
		<tr height="*">
			<td colspan="2" style="vertical-align:bottom;">	<a href="firma1.php?id={FID}&start={PREV}">&lt;</a> <a href="liefer1.php?id={FID}&start={PAGER}" class="bold">neu laden</a> <a href="liefer1.php?id={FID}&start={NEXT}">&gt;</a></td>
			<td style="vertical-align:bottom;"><form name="ksearch" onSubmit="return ks();"><input type="text" name="suchwort" size="20"></td>
			<td style="vertical-align:bottom;"><input type="submit" name="ok" value="suchen"></form></td>
		</tr>
	</table>
</td></tr></table>
[<a href="liefern3.php?id={FID}&edit=1" class="bold">Edit</a>]
<!-- End Code ------------------------------------------->
</td></tr></table>
</body>
</html>

