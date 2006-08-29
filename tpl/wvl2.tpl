<!-- $Id: -->
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
<body onLoad="doInit();">
<!--table width="103%" class="karte"><tr><td class="karte"-->
<!-- Beginn Code ------------------------------------------->
<p class="listtop">Wiedervorlage</p>
<table width="100%" border="0">
<form name="formular" action="wvl1.php" enctype='multipart/form-data' method="post">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="2000000">
<input type="hidden" name="CID" value="{CID}">
<input type="hidden" name="WVLID" value="{WVLID}">
<input type="hidden" name="Mail" value="{Mail}">
<input type="hidden" name="cp_cv_id" value="{cpcvid}">
<input type="hidden" name="DCaption" value="{DCaption}">
<tr>
	<td class="smal" width="350px">
	<select name="CRMUSER" tabindex="1">
<!-- BEGIN Selectbox -->
		<option value="{UID}"{Sel}>{Login}</option>
<!-- END Selectbox -->
	</select>
	<input type="text" name="name" size="18" maxlength="75"  value="{Fname}"   tabindex="1"> <input type="button" name="dst" value=" ? " onClick="suchDst();">
	<br>CRM-User &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Zugewiesen an
	</td><td class="smal" width="120px">
	<input type="text" name="Finish" size="10" maxlength="10" value="{Finish}" tabindex="3"><br>Zu Erledigen bis
	</td>
	<td rowspan="5">
		<iframe src="wvll.php" name="wvll" width="359" height="410" marginheight="0" marginwidth="0" align="left">
		<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
		</iframe>
	</td>
</tr><tr>
	<td class="smal">
	<input type="text" name="Cause" size="31" maxlength="60" value="{Cause}" tabindex="4"><br>Betreff
	</td><td class="smal"">
	<!--input type="radio" name="Status" value="1" tabindex="5" {Status1}>1&nbsp;
	<input type="radio" name="Status" value="2" tabindex="6" {Status2}>2&nbsp;
	<input type="radio" name="Status" value="3" tabindex="7" {Status3}>3&nbsp;
	<input type="radio" name="Status" value="0" tabindex="8" >Erledigt&nbsp;
	<br>Priorit&auml;t-->
	</td>
</tr><tr>
	<td class="smal" colspan="2">
	<textarea name="LangTxt" cols="71" rows="14" tabindex="9">{LangTxt}</textarea><br>Beschreibung
	</td>
</tr>
<!-- BEGIN Filebox -->
<tr>
	{file}
</tr>
<!-- END Filebox -->
<tr>
	<td colspan="2"><input type="submit" name="delete" value="l&ouml;schen" tabindex="17"> &nbsp; <input type="submit" value="reset" tabindex="18"> &nbsp; <input type="submit" name="save" value="sichern" tabindex="17"></td>
</tr>
</form>
</td></tr></table>
<!-- End Code ------------------------------------------->
<!--/td></tr></table-->
</body>
</html>

