<!-- $Id$ -->
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
	function chngSerial(site) {
		etikett.document.location.href = site + ".php";
	}
	//-->
	</script>
<body>

<p class="listtop">Ergebnis Lieferantensuche</p>
<table><tr><td>
<!-- Beginn Code ------------------------------------------->

<table>
<!-- BEGIN Liste -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showK({FID});">
		<td class="smal">{Name}</td><td class="smal">{Plz} {Ort}</td><td class="smal">{Telefon}</td><td class="smal">{eMail}</td></tr>
<!-- END Liste -->
</table>
{report}
</td><td class="smal">
<form>
	<input type="button" name="etikett" value="Etiketten" onClick="chngSerial('etiketten');">&nbsp;
	<a href="sermail.php"><input type="button" name="email" value="Serienmail"></a>&nbsp;
	<input type="button" name="brief" value="Serienbrief" onClick="chngSerial('serdoc');">
</form>
	<br>
	<iframe src="etiketten.php" name="etikett" width="240" height="380" marginheight="0" marginwidth="0" align="left">
		<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
	</iframe>
</td></tr>
<!-- End Code ------------------------------------------->
</td></tr></table>
</body>
</html>
