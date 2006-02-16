<!-- $Id: getCall.tpl,v 1.3 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title>LX - CRM</title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
		function artikel() {
			f1=open("artikel.php","artikel","width=580,height=480,left=250,top=100,scroppbars=yes");
		}
		function showCall(id) {
			if (id) {
				uri="getCall.php?hole=" + id + "&INIT={INIT}&Q={Q}";
				location.href=uri;
			}
		}
	//-->
	</script>
<body >

<table class="karte"><tr><td class="karte" style="height:580px;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="call" action="getCall.php" enctype='multipart/form-data' method="post">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="2000000">
<table class="stamm" width="100%">
			<td rowspan="3">
				<select name="CRMUSER">
<!-- BEGIN Selectbox -->
					<option value="{UID}"{Sel}>{Login}</option>
<!-- END Selectbox -->
				</select>
				<select name="CID">
<!-- BEGIN Selectbox2 -->
					<option value="{CID}"{Sel}>{CName}</option>
<!-- END Selectbox2 -->
				</select>
				<select name="TID">
<!-- BEGIN Selectbox3 -->
					<option value="{TID}"{Sel}>{TID}</option>
<!-- END Selectbox3 -->
				</select>
				<input type="submit" name="verschiebe" value="verschieben">
			</td></tr>
	<tr><td class="smal bold">{Firma}</td></tr>
	<tr><td class="smal bold">{Plz} {Ort}</td></tr>
</table>
<br>
<input type="hidden" name="Bezug" value="{Bezug}">
<input type="hidden" name="Anzeige" value="{Anzeige}">
<input type="hidden" name="fid" value="{FID}">
<input type="hidden" name="pid" value="{PID}">
<input type="hidden" name="id" value="{ID}">
<input type="hidden" name="Q" value="{Q}">
<input type="hidden" name="INIT" value="{INIT}">
<input type="hidden" name="datei" value="{ODatei}">
<input type="text" name="cause" value="{NBetreff}" size="43" maxlength="125"> &nbsp; <input type="text" name="Datum" value="{NDatum}" size="11" maxlength="10"> 
<input type="text" name="Zeit" value="{NZeit}" size="6" maxlength="5"> &nbsp; <input type="submit" name="reset" value="reset">
<!--input type="reset" value="reset" onClick="javascript:location.href='getCall.php?fid={FID}&id={ID}'"--><br>
<span class="smal">Betreff</span><br>
<textarea name="c_cause" cols="76" rows="12" wrap="physical">{LangTxt}</textarea><br>
<span class="smal">Bemerkung &nbsp; &nbsp; &nbsp;<!--a href="#" onClick="artikel()">Artikelliste</a--></span><br>
<table>
<tr><td><input type="file" name="Datei[]" value="{Datei}" size="35" maxlength="125"><br>
	     <span class="smal">Datei/Dokument<b> {ODatei}</b></span></td>
	<td rowspan="2">
<!-- BEGIN Files -->
	{Anhang} 
<!-- END Files -->
	</td></tr>
<tr><td><input type="text" name="DCaption" value="{DCaption}" size="46" maxlength="125"><br>
<span class="smal">Datei Beschreibung</span></td></tr>
</table>
<span class="smal">
<input type="radio" name="Kontakt" value="T" {R1}>Telefon	&nbsp;
<input type="radio" name="Kontakt" value="M" {R2}>eMail &nbsp;
<input type="radio" name="Kontakt" value="S" {R3}>Fax/Brief &nbsp;
<input type="radio" name="Kontakt" value="P" {R4}>Pers&ouml;nlich
<input type="radio" name="Kontakt" value="D" {R5}>Datei &nbsp;
<input type="radio" name="Kontakt" value="X" {R6}>Termin &nbsp; <input type="submit" name="sichern" value="neuer Eintrag"><br>
Kontaktart</span><br>
</form>
<table class="liste" width="100%">
<!-- BEGIN Liste -->
	<tr  class="smal" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showCall({IID});">
		<td width="118px">{Datum}</td><td>{Betreff}</td><td style="background-color:{Type};">{Kontakt}</td></tr>
<!-- END Liste -->
</table>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
<script language="JavaScript">self.focus()</script>
</body>
</html>
