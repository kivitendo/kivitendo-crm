<!-- $Id: firmen1L.tpl,v 1.3 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showK (id) {
		if (id) {
			Frame=eval("parent.main_window");
			uri="firma1.php?id=" + id;
			Frame.location.href=uri;
		}
	}
	//-->
	</script>
<body>

<p class="listtop">Ergebnis Firmen-/Kundensuche</p>
<table><tr><td valign="top">
<!-- Beginn Code ------------------------------------------->

<table>
<!-- BEGIN Liste -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showK({ID});">
		<td class="smal">{Name}</td><td class="smal">{Plz} {Ort}</td><td class="smal">{Telefon}</td><td class="smal">{eMail}</td></tr>
<!-- END Liste -->
</table>
{report}
</td><td class="smal">
	<iframe src="etiketten.php" name="etikett" width="240" height="380" scrolling="yes"> marginheight="0" marginwidth="0" align="left">
		<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
	</iframe>
</td></tr>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
