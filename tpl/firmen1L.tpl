<html>
        <head><title></title>
{STYLESHEETS}
    <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
    <link rel="stylesheet" type="text/css" href="{JQUERY}/jquery-ui/themes/base/jquery-ui.css">
    {THEME}        
    <script type="text/javascript" src="{JQUERY}jquery-ui/jquery.js"></script>
    <script type="text/javascript" src="{JQUERY}jquery-ui/ui/jquery-ui.js"></script>
{JAVASCRIPTS}

	<script language="JavaScript">
	<!--
	function showK (id) {
		if (id) {
			uri="firma1.php?Q={Q}&id=" + id;
			location.href=uri;
		}
	}
	function chngSerial(site) {
		etikett.document.location.href = site + ".php?src=F";
	}
	//-->
	</script>
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:search result:. {FAART}</p>
<table><tr><td valign="top">
<!-- Beginn Code ------------------------------------------->

<table>
<!-- BEGIN Liste -->
	<tr class=' bgcol{LineCol}' onClick="showK({ID});" >
		<td>{KdNr}</td><td>{Name}</td><td>{Plz}</td><td>{Ort}</td><td>{Strasse}</td><td>{Telefon}</td><td>{eMail}</td></tr>
<!-- END Liste -->
</table>
{report}
</td><td class="mini">
<form>
	<input type="button" name="etikett" value=".:label:." onClick="chngSerial('etiketten');">&nbsp;
	<a href="sermail.php"><input type="button" name="email" value=".:sermail:."></a>&nbsp;
	<input type="button" name="brief" value=".:serdoc:." onClick="chngSerial('serdoc');">
	<input type="button" name="vcard" value=".:servcard:." onClick="chngSerial('servcard');">
</form>
	<br>
	<iframe src="etiketten.php" name="etikett" width="350" height="380" scrolling="yes"> marginheight="0" marginwidth="0" align="left">
		<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
	</iframe>
</td></tr>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
{END_CONTENT}
</body>
</html>
