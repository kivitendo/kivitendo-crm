<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showK (id,tbl) {
		{no}
		Frame=eval("parent.main_window");
		uri="firma2.php?Q="+tbl+"&id=" + id;
		Frame.location.href=uri;
	}
	function showK__ (id) {
		{no}
		Frame=eval("parent.main_window");
		uri="kontakt.php?id=" + id;
		Frame.location.href=uri;
	}
	function chngSerial(site) {
		etikett.document.location.href = site + ".php";
	}
	//-->
	</script>
<body>
<p class="listtop">Ergebnis Personensuche</p>
<table width="100%" border="0"><tr><td valign="top">
<!-- Beginn Code ------------------------------------------->
<form name="personen" action="firma2.php" method="post">
<input type="hidden" name="fid" value="{FID}">
<input type="hidden" name="Q" value="{Q}">
<!-- input type="hidden" name="ANZAHL_ANSPRECHPARTNER" value="{ANZAHL_ANSPRECHPARTNER}" -->
<table border="0"><tr><td class="mini">
<!-- BEGIN Liste -->
	<tr class="mini" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showK({PID},'{TBL}');">
		<td>{Name}</td><td>&nbsp;{Plz} {Ort}</td><td>&nbsp;{Telefon}</td><td>&nbsp;{eMail}</td><td>&nbsp;{Firma}</td><td>&nbsp;{insk}</td></tr>
	  <input type="hidden" name="PID_{laufende_nummer}" value="{PID}"> <!-- hier muss noch ein schleifenzähler rein, muss nicht mehr ist als hidden variable dabei -> nächste Idee, brauch ich auch gar nicht als hidden wert (s.a. Algorithmus in firma2.php), aber kann ich in dieser template-engine (phplip) irgendwie besser kommentare setzen?-->
<!-- END Liste -->
	
	<tr><td class="klein" colspan="6"> <select class="klein" name="cp_sonder"> <!-- BEGIN sonder --> <option value="{sonder_id}">{sonder_name}</option><!-- END sonder --> <input type="submit" name="ansprechpartnern_attribute_zuordnen" class="klein" value="Ansprechpartnern Attribute zuordnen" ><span class="mini">Hinweis: Alle vorher definierten Attribute werden überschrieben</span>
 </td></tr>
	<tr><td class="mini re" colspan="6">{snd}</td></tr>
</table>
</td><td class="mini">
<form>
	<input type="button" name="etikett" value="Etiketten" onClick="chngSerial('etiketten');">&nbsp;
	<a href="sermail.php"><input type="button" name="email" value="Serienmail"></a>&nbsp;
	<input type="button" name="brief" value="Serienbrief" onClick="chngSerial('serdoc');">
</form>
	<iframe src="etiketten.php" name="etikett" width="300" height="380" marginheight="0" marginwidth="0" align="left">
		<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
	</iframe>
</td></tr>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
