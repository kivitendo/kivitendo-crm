<html>
<head><title>Zusatzdaten</title>
        {STYLESHEETS}
        {CRMCSS}
	<script langage="JavaScript">
		// sind in den Funktionen geschweifte Klammern drin, dann als extra File laden
		// da die sonst von der TemplateEngie gelöscht werden
                function checkfelder() {
                        document.test.save.value=1;
                        document.test.submit();
                }
                function doSubmit() {
                        document.test.suche.value=1;
                        document.test.submit();
                }
	</script>
</head>
<body onLoad="window.resizeTo(1024, 768)">
<center>
<h2>Weitere Kontaktdaten</h2>
<form name="test" action="extrafelder.php" method="post">
<input type="hidden" name="owner" value="{owner}">
<input type="hidden" name="suche" value="">
<input type="hidden" name="save" value="">
<table>
<tr><td>Status</td><td> <input type="checkbox" name="status1"  value="1" {status1_1}>Potentieller TN 
			<input type="checkbox" name="status2"  value="1" {status2_1}>Teilnehmer
			<input type="checkbox" name="status3"  value="1" {status3_1}>Entscheider
			<input type="checkbox" name="status4"  value="1" {status4_1}>Besteller
</td></tr>
<tr><td>Geburtstag</td><td><input type="text" name="gebdat" size="10" value="{gebdat}"></td></tr>
<tr><td>Kurshistorie</td><td><input type="text" name="semhist" size="90" value="{semhist}"></td></tr>
<tr><td>Weitere Kurse</td><td><input type="text" name="weitersem" size="50" value="{weitersem}"></td></tr>
<tr><td>Herkunft</td><td><select name="herkunft">
<option value=""  {herkunft0}>== bitte ausw&auml;hlen ==
<option value="1" {herkunft1}>Webseite
<option value="2" {herkunft2}>Katalog-Download
<option value="3" {herkunft3}>IT-Fortbildung.com
<option value="4" {herkunft4}>Empfehlung Kollegen
<option value="5" {herkunft5}>sonstige
</select>
sonstige Herkunft<input type="text" name="sonst_herkunft" size="30" value="{sonst_herkunft}"></td></tr>
<tr><td>Newsletter</td><td><input type="radio" name="newsletter"  value="1" {newsletter_1}>Ja <input type="radio" name="newsletter"  value="2" {newsletter_2}>Nein</td></tr>
<tr><td>Brief-Mailing</td><td><input type="radio" name="brief"  value="1" {brief_1}>Ja <input type="radio" name="brief"  value="2" {brief_2}>Nein</td></tr>
<tr><td align="left" valign="top">Kontakt-Historie</td>
       <td valign="top">
<textarea name="kontakthistorie" cols="80" rows="25">{kontakthistorie}</textarea>
</td></tr>
</table>
<input type="button" name="saveit" value="sichern" onClick="checkfelder()"  {visiblesichern}>
<input type="button" name="search" value="suchen" onClick="doSubmit();" {visiblesuchen}>
</form>
</center>
</body>
</html>
