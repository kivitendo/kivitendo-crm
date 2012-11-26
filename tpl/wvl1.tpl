<html>
	<head><title></title>
        {STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="css/{ERPCSS}/main.css"></link>
        {JAVASCRIPTS}
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
	{jcal0}
<body onLoad="doInit();" >
{PRE_CONTENT}
{START_CONTENT}
<!-- Beginn Code ------------------------------------------->
<p class="listtop">Wiedervorlage</p>
<table>
<form name="formular" action="wvl1.php" enctype='multipart/form-data' method="post">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="2000000">
<input type="hidden" name="CID" value="{CID}">
<input type="hidden" name="WVLID" value="{WVLID}">
<input type="hidden" name="noteid" value="{noteid}">
<input type="hidden" name="DateiID" value="{Datei}">
<input type="hidden" name="cp_cv_id" value="{cpcvid}">
<input type="hidden" name="cp_cv_id_old" value="{cpcvid}">
<tr>
	<td class="klein">
	<select name="CRMUSER" tabindex="1">
<!-- BEGIN Selectbox -->
		<option value="{UID}"{Sel}>{Login}</option>
<!-- END Selectbox -->
	</select>
	<span style="visibility:{nohide};">
	<input type="text" name="name" size="14" maxlength="75"  value="{Fname}"tabindex="2"> <input type="button" name="dst" value=" ? " onClick="suchDst();" tabindex="99"> </span>
	<br><span class="klein">CRM-User &nbsp; &nbsp; &nbsp;</span><span class="klein" style="visibility:{nohide};">Zugewiesen an &nbsp;[<a href="{stammlink}" name="addresse">Adresse</a>]</span>
	</td><td class="klein"><input type="text" name="Finish" id="Finish" size="11" maxlength="10" value="{Finish}" tabindex="3">{jcal1}
							<br><span class="klein">Zu Erledigen bis</span></td>
	<td rowspan="6">
		<iframe src="wvll.php" name="wvll" width="500" height="480" marginheight="0" marginwidth="0" align="left">
		<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
		</iframe>
	</td>
</tr><tr>
	<td class="klein">
	<input type="text" name="Cause" size="28" maxlength="60" value="{Cause}" tabindex="4"><br><span class="klein">Betreff</span>
	</td><td class="klein">
	<input type="radio" name="status" value="1" tabindex="5" {Status1}>1&nbsp;
	<span style="visibility:{hide};">
	<input type="radio" name="status" value="2" tabindex="6" {Status2}>2&nbsp;
	<input type="radio" name="status" value="3" tabindex="7" {Status3}>3&nbsp;
	</span>
	<input type="radio" name="status" value="0" tabindex="8" >Erledigt
	<br><span class="klein">Priorit&auml;t</span></td>
</tr><tr>
	<td class="klein" colspan="2">
	<textarea name="LangTxt" cols="65" rows="11" tabindex="9">{LangTxt}</textarea><br><span class="klein">Beschreibung</span>
	</td>
</tr><tr>
	<td class="klein" style="visibility:{hide};">
	<input type="file" name="Datei[]" maxlength="2000000" size="14" maxlength="75" tabindex="10"><br><span class="klein">Dokument</span> <a href="{DLink}" target="_blank"><b><font color="black">{DName}</font></b></a></td>
	<td class="klein" align="right"><input type="submit" value="reset" tabindex="18"></td>
</tr><tr>
	<td class="klein" class="klein" style="visibility:{hide};">
	<input type="text" name="DCaption" size="28" maxlength="75" value="{DCaption}" tabindex="11"><br><span class="klein">Dokumentbeschreibung</span>
	<td class="klein" align="right"><input type="submit" name="save" value="sichern" tabindex="17"></td>
</tr><tr>
	<td class="klein" colspan="2">
	<span style="visibility:{hide};">
	<input type="radio" name="kontakt" value="T" {R1} tabindex="12">Telefon	&nbsp;
	<input type="radio" name="kontakt" value="M" {R2} tabindex="13">eMail &nbsp;
	<input type="radio" name="kontakt" value="S" {R3} tabindex="14">Fax/Brief &nbsp;
	<input type="radio" name="kontakt" value="P" {R4} tabindex="15">Pers&ouml;nlich&nbsp;
	<input type="radio" name="kontakt" value="D" {R5} tabindex="16">Datei&nbsp;
	</span>
	<input type="radio" name="kontakt" value="F" {R6} tabindex="16">ERP&nbsp;
	<br>
	<span class="klein">Kontaktart</span> <b>{Msg}</b>
</tr>
</form>
</td></tr></table>
<!-- End Code ------------------------------------------->
<script type='text/javascript'><!--
Calendar.setup( {
inputField : 'Finish',ifFormat :'%d.%m.%Y',align : 'BL', button : 'trigger1'} );
//-->
</script>
{END_CONTENT}
</body>
</html>

