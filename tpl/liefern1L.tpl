<!-- $Id: liefern1L.tpl,v 1.3 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showK (id) {
		if (id) {
			Frame=eval("parent.main_window");
			uri="liefer1.php?id=" + id;
			Frame.location.href=uri;
		}
	}
	//-->
	</script>
<body>

<p class="listtop">Ergebnis Lieferantensuche</p>
<table><tr><td>
<!-- Beginn Code ------------------------------------------->

<table>
<!-- BEGIN Liste -->
<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showK({FID});"><td>{FID}</td><td>{Name}</td><td>{Plz} {Ort}</td><td>{Telefon}</td><td>{eMail}</td></tr>
<!-- END Liste -->
</table>
</td><td>
	<iframe src="etiketten.php" name="etikett" width="240" height="380" marginheight="0" marginwidth="0" align="left">
		<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
	</iframe>
</td></tr>
<!-- End Code ------------------------------------------->
</td></tr></table>
</body>
</html>
