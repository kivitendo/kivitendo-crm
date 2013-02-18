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
	function showK (id,tbl) {
		{no}
		uri="firma2.php?Q="+tbl+"&id=" + id;
		location.href=uri;
	}
	function showK__ (id) {
		{no}
		uri="kontakt.php?id=" + id;
		location.href=uri;
	}
	function chngSerial(site) {
		etikett.document.location.href = site + ".php?src=P";
	}
	//-->
	</script>
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:search result:. .:Contacts:.</p>
<table><tr><td valign="top">
<!-- Beginn Code ------------------------------------------->

<table>
<!-- BEGIN Liste -->
	<tr class="bgcol{LineCol}" onClick='{js}'>
		<td>{Name}</td><td>&nbsp;{Plz}</td><td>{Ort}</td><td>&nbsp;{Telefon}</td><td>&nbsp;{eMail}</td><td>&nbsp;{Firma}</td><td>&nbsp;{insk}</td></tr>
<!-- END Liste -->
	<tr><td class="re" colspan="6">{snd}</td></tr>
</table>
</td><td class="mini">
    <form>
	    <input type="button" name="etikett" value="Etiketten" onClick="chngSerial('etiketten');">&nbsp;
	    <a href="sermail.php"><input type="button" name="email" value="Serienmail"></a>&nbsp;
	    <input type="button" name="brief" value="Serienbrief" onClick="chngSerial('serdoc');">
	    <input type="button" name="vcard" value=".:servcard:." onClick="chngSerial('servcard');">
    </form><br />
    <iframe src="etiketten.php" name="etikett" width="380" height="380" marginheight="0" marginwidth="0" align="left">
 	    <p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
    </iframe>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
{END_CONTENT}
</body>
</html>
