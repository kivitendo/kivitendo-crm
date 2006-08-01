<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
		function showItem(id) {
			F1=open("getCall.php?Q=CC&pid={PID}&Bezug="+id,"Caller","width=670, height=600, left=100, top=50, scrollbars=yes");
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
<div style="position:absolute; top:33px; left:8px;  width:770px;">
	<ul id="tabmenue">
	<li><a href="{Link1}">Kundendaten</a><li>
	<li><a href="{Link2}" id="aktuell">Kontakte</a></li>
	<li><a href="{Link3}">Ums&auml;tze</a></li>
	<li><a href="{Link4}">Dokumente</a></li>
	<span title="Wichtige MItteilung">{Cmsg}</span>
	</ul>
</div>

<span style="position:absolute; left:10px; top:67px; width:99%;">
<!-- Beginn Code ------------------------------------------->
<div style="float:left; width:53%; height:430px; text-align:center; border: 1px solid black;">
	<div style="float:left; width:100%; height:50px; text-align:left; border-bottom: 1px solid black;" class="fett">
		{Fname1} &nbsp; &nbsp; {customernumber}<br />
		{Fdepartment_1}<br /> 
		{Plz} {Ort}<br />
	</div>
	<div style="float:left; width:70%; height:210px; text-align:left; border-bottom: 1px solid black;" class="gross">
		{Anrede} {Titel}<br />
		{Vname} {Nname}<br />
		{StreetC}<br />
		<span class="mini">&nbsp;<br /></span>
		{LandC}{PlzC} {OrtC}<br />
		<span class="mini">&nbsp;<br /></span>
		<img src="image/telefon.gif" style="visibility:{none};"> {Telefon}<br />
		&nbsp;<img src="image/mobile.gif" style="visibility:{none};"> {Mobile}<br />
		<img src="image/fax.gif" style="visibility:{none};"> {Fax}<br />
		<a href="mail.php?TO={eMail}&KontaktTO=K{PID}">{eMail}</a><br />
		<a href="{www}" target="_blank">{www}</a><br /><br />
	</div>
	<div style="float:left; width:30%; height:210px; text-align:right; border-bottom: 1px solid black;" class="gross">
		{PID} &nbsp; <a href="#" onCLick="anschr();"><img src="image/brief.png" border="0" style="visibility:{none};"></a><br />
		{IMG}</br >
		{GDate}</br />
		{Position}<br />
		{Abteilung}<br />
	</div>
	<div style="float:none; width:100%; height:385px; text-align:left; border-bottom: 1px solid black;" class="normal">
		{Sonder}<br>
		<span class="klein">{Notiz}</span>
	</div><br>
	<div style="position:absolute; bottom:8px; width:49%; " class="normal">
	<!--div style="float:none; width:100%; height:15px; text-align:center; border: 0px solid black;" class="normal"-->
		<span style="visibility:{none};">[<a href="javascript:vcard()">VCard</a>]  
		[<a href="personen3.php?id={PID}&edit=1&Quelle=F">{Edit}</a>]</span> 
		[<a href="firma2.php?fid={PID}&ldap=1&Quelle=F">LDAP</a>]
		<b>Kontakt:</b> [<a href="personen3.php?fid={FID}&Quelle=F">eingeben</a>] [<a href="personen1.php?fid={FID}&Quelle=F">aus Liste</a>]
	</div>
</div>
<span style="float:left; width:46%; height:430px; text-align:left; border: 1px solid black; border-left:0px;">
<table width="99%">
<!-- BEGIN Liste -->
	<tr height="14px" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showItem({IID});">
		<td class="smal" width="105">{Datum} {Zeit}</td>
		<td class="smal" style="width:38;text-align:right;">{Nr}&nbsp;</td>
		<td class="smal le">{Betreff}</td>
		<td class="smal le">{Name}</td>
	</tr>
<!-- END Liste -->
</table>
	<!--span style="float:left;  text-align:left; border:0px solid black"-->	
	<span style="position:absolute; bottom:10px; visibility:{none};">
		<form name="ksearch"> &nbsp; 
		<a href="firma2.php?id={PID}&start={PREV}"><img src="image/leftarrow.png" align="middle" border="0" title="zur&uuml;ck"></a> 
		<a href="firma2.php?id={PID}&start={PAGER}" class="bold"><img src="image/reload.png" align="middle" border="0" title="reload"></a> 
		<a href="firma2.php?id={PID}&start={NEXT}"><img src="image/rightarrow.png" align="middle" border="0" title="mehr"></a>&nbsp;
		<input type="text" name="suchwort" size="20">
		<input type="submit" src="image/suchen_kl.png" name="ok" value="suchen" align="middle" border="0"> 
		</form>
	</span>
</span>

<!-- End Code ------------------------------------------->
</span>
</body>
</html>
