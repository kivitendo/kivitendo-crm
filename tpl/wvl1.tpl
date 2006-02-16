<!-- $ID: $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	
	<script language="JavaScript">
	<!--
		function doInit() {
			{JS}
		}
		function suchDst() {
			val=document.formular.name.value;
			f1=open("suchFa.php?pers=1&name="+val,"suche","width=350,height=200,left=100,top=100");
		}
	//-->
	</script>
<body onLoad="doInit();" >
<!--table width="103%" class="karte" border="0"><tr><td class="karte"-->
<!-- Beginn Code ------------------------------------------->
<p class="listtop">Wiedervorlage</p>
<table>
<form name="formular" action="wvl1.php" enctype='multipart/form-data' method="post">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="2000000">
<input type="hidden" name="CID" value="{CID}">
<input type="hidden" name="WVLID" value="{WVLID}">
<input type="hidden" name="DateiID" value="{Datei}">
<input type="hidden" name="cp_cv_id" value="{cpcvid}">
<tr>
	<td class="smal">
	<select name="CRMUSER" tabindex="1">
<!-- BEGIN Selectbox -->
		<option value="{UID}"{Sel}>{Login}</option>
<!-- END Selectbox -->
	</select>
	<input type="text" name="name" size="14" maxlength="75"  value="{Fname}" tabindex="2"> <input type="button" name="dst" value=" ? " onClick="suchDst();" tabindex="99">
	<br>CRM-User &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Zugewiesen an
	</td><td class="smal""><input type="text" name="Finish" size="11" maxlength="10" value="{Finish}" tabindex="3">
							<br>Zu Erledigen bis</td>
	<td rowspan="6">
		<iframe src="wvll.php" name="wvll" width="359" height="410" marginheight="0" marginwidth="0" align="left">
		<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
		</iframe>
	</td>
</tr><tr>
	<td class="smal">
	<input type="text" name="Cause" size="28" maxlength="60" value="{Cause}" tabindex="4"><br>Betreff
	</td><td class="smal">
	<input type="radio" name="status" value="1" tabindex="5" {Status1}>1&nbsp;
	<input type="radio" name="status" value="2" tabindex="6" {Status2}>2&nbsp;
	<input type="radio" name="status" value="3" tabindex="7" {Status3}>3&nbsp;
	<input type="radio" name="status" value="0" tabindex="8" >Erledigt
	<br>Priorit&auml;t</td>
</tr><tr>
	<td class="smal" colspan="2">
	<textarea name="LangTxt" cols="69" rows="11" tabindex="9">{LangTxt}</textarea><br>Beschreibung
	</td>
</tr><tr>
	<td class="smal">
	<input type="file" name="Datei[]" maxlength="2000000" size="14" maxlength="75" tabindex="10"><br>Dokument <a href="{DLink}" target="_blank"><b><font color="black">{DName}</font></b></a></td>
	<td class="smal" align="right"><input type="submit" value="reset" tabindex="18"></td>
</tr><tr>
	<td class="smal">
	<input type="text" name="DCaption" size="28" maxlength="75" value="{DCaption}" tabindex="11"><br>Dokumentbeschreibung
	<td class="smal" align="right"><input type="submit" name="save" value="sichern" tabindex="17"></td>
</tr><tr>
	<td class="smal" colspan="2">
	<input type="radio" name="kontakt" value="T" {R1} tabindex="12">Telefon	&nbsp;
	<input type="radio" name="kontakt" value="M" {R2} tabindex="13">eMail &nbsp;
	<input type="radio" name="kontakt" value="S" {R3} tabindex="14">Fax/Brief &nbsp;
	<input type="radio" name="kontakt" value="P" {R4} tabindex="15">Pers&ouml;nlich&nbsp;
	<input type="radio" name="kontakt" value="D" {R5} tabindex="16">Datei&nbsp;
	<br>
	<span class="smal">Kontaktart</span> <b>{Msg}</b>
</tr>
</form>
</td></tr></table>
<!-- End Code ------------------------------------------->
<!--/td></tr></table-->
</body>
</html>

