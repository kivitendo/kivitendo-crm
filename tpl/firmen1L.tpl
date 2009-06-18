<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showK (id) {
		if (id) {
			Frame=eval("parent.main_window");
			uri="firma1.php?Q={Q}&id=" + id;
			Frame.location.href=uri;
		}
	}
	function chngSerial(site) {
		etikett.document.location.href = site + ".php";
	}
	//-->
	</script>
<body>

<p class="listtop">Ergebnis Firmensuche {FAART}</p>
<table><tr><td valign="top">
<!-- Beginn Code ------------------------------------------->

<table>
<!-- BEGIN Liste -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showK({ID});">
		<td class="mini">{KdNr}</td><td class="mini">{Name}</td><td class="mini">{Plz} {Ort}</td><td class="mini">{Telefon}</td><td class="mini">{eMail}</td></tr>
<!-- END Liste -->
</table>
{report}
</td><td class="mini">
<form>
	<input type="button" name="etikett" value="Etiketten" onClick="chngSerial('etiketten');">&nbsp;
	<a href="sermail.php"><input type="button" name="email" value="Serienmail"></a>&nbsp;
	<input type="button" name="brief" value="Serienbrief" onClick="chngSerial('serdoc');">
</form>
	<br>
	<iframe src="etiketten.php" name="etikett" width="300" height="380" scrolling="yes"> marginheight="0" marginwidth="0" align="left">
		<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
	</iframe>
</td></tr>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
