<html>
	<head><title></title>
        {STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css">
        <link rel="stylesheet" type="text/css" href="{JQUERY}/jquery-ui/themes/base/jquery-ui.css">
        {THEME}    
        <script type="text/javascript" src="{JQUERY}jquery-ui/jquery.js"></script>
        {JAVASCRIPTS}
<body>
{PRE_CONTENT}
{START_CONTENT}
<table class="reiter">
	<tr>
		<td width="25%" class="menueA reiter ce">
			<a href="wvl.php" >refresh</a>
		</td>
		<td width="25%" class="menueD reiter ce">
			<a href="" ></a>
		</td>
		<td width="25%" class="menueD reiter ce">
			<a href="" ></a>
		</td>
		<td width="25%" class="menueD reiter ce">
			<a href="" ></a>
		</td>
	</tr>
</table>

<table width="95%" class="karte"><tr><td class="karte" width="50%">
<!-- Beginn Code ------------------------------------------->
<form name="wvl" action="wvl.php" method="post">
<input type="hidden" name="CID" value="{CID}">
<table cellpadding="2">
	<tr class="mini"><td><select name="CRMUSER" tabindex="1">
<!-- BEGIN Selectbox -->
							<option value="{Login}"{Sel}>{Login}</option>
<!-- END Selectbox -->
						</select><br>Zugewiesen an</td><td></td></tr>
	<tr class="mini"><td colspan="2"><input type="text" name="Cause" size="40" value="{Cause}" tabindex="2"><br>Betreff</td></tr>
	<tr class="mini"><td colspan="2"><textarea name="LangTxt" cols="41" rows="5" tabindex="3">{LangTxt}</textarea><br>Beschreibung</td></tr>
	<tr class="mini"><td colspan="2"><input type="file" name="Dokumnet" size="30" value="{Dokument}" tabindex="4"><br>Dokument</td></tr>
	<tr class="mini"><td colspan="2"><input type="text" name="DCaption" size="40" value="{DCaption}" tabindex="5"><br>Betreff</td></tr>
	<tr class="mini"><td><input type="radio" name="Status" value="1" {Status}>1&nbsp;
							<input type="radio" name="Status" value="2" {Status}>2&nbsp;
							<input type="radio" name="Status" value="3" {Status}>3<br>Priorit&auml;t
					</td><td><input type="text" name="Finish" size="11" value="{Finish}" tabindex="7"><br>Zu Erledigen bis</td></tr>
	<tr class="mini"><td><input type="submit" name="save" value="sichern"></td><td><input type="reset" value="reset"></td></tr>
</form>
</table></td><td width="50%"><table class="stamm" width="370" height="400px">
	<tr><td>&nbsp;</td></tr>
</table>
<!-- End Code ------------------------------------------->
</td></tr></table>
{END_CONTENT}
</body>
</html>
