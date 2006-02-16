<!-- $Id: personen1L.tpl,v 1.4 2005/11/02 11:35:45 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showK (id) {
		{no}
		Frame=eval("parent.main_window");
		uri="kontakt.php?id=" + id;
		Frame.location.href=uri;
	}
	//-->
	</script>
<body>
<p class="listtop">Ergebnis Personensuche</p>
<table width="100%" border="0"><tr><td valign="top">
<!-- Beginn Code ------------------------------------------->
<form name="personen" action="{DEST}2.php" method="post">
<input type="hidden" name="fid" value="{FID}">
<table><tr><td class="smal">
<!-- BEGIN Liste -->
	<tr class="smal" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showK({PID});">
		<td>{Name}</td><td>&nbsp;{Plz} {Ort}</td><td>&nbsp;{Telefon}</td><td>&nbsp;{eMail}</td><td>&nbsp;{Firma}</td><td>&nbsp;{insk}</td></tr>
<!-- END Liste -->	
	<tr><td class="smal re" colspan="6">{snd}</td></tr>
</table>
</td><td class="smal">
	<iframe src="etiketten.php" name="etikett" width="240" height="380" marginheight="0" marginwidth="0" align="left">
		<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
	</iframe>
</td></tr>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
