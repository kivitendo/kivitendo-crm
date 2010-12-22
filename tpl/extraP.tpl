<html>
<head><title>Zusatzdaten</title>
    <link type="text/css" REL="stylesheet" HREF="../css/{ERPCSS}"></link>
    <link type="text/css" REL="stylesheet" HREF="css/{ERPCSS}"></link>
	<script langage="JavaScript">
		// sind in den Funktionen geschweifte Klammern drin, dann als extra File laden
		// da die sonst von der TemplateEngie gelöscht wird
		function checkfelder() {
			if (!document.test.plz.value.match(/^[0-9]+$/)) { alert ("Fehlerhafte PLZ"); return false; };
			document.test.save.value=1;
			document.test.submit();
		}
		function doSubmit() {
			document.test.suche.value=1;
			document.test.submit();
		}
	</script>
</head>
<body onLoad="window.resizeTo(580, 420)">
<center>
<h2>Ein Test</h2>
<form name="test" action="extrafelder.php" method="post" >
<input type="hidden" name="owner" value="{owner}">
<input type="hidden" name="suche" value="">
<input type="hidden" name="save" value="">
<table>
<tr><td>Name</td><td><input type="text" name="name" size="30" value="{name}"></td></tr>
<tr><td>Strasse</td><td><input type="text" name="strasse" size="30" value="{strasse}"></td></tr>
<tr><td>Plz/Ort</td><td><input type="text" name="plz" size="5" value="{plz}"><input type="text" name="ort" size="25" value="{ort}"></td></tr>
<tr><td>Test</td><td><select name="test">
<option value="" {test0}>
<option value="1" {test1}>Wert1
<option value="2" {test2}>Wert2
<option value="3" {test3}>Wert3
<option value="4" {test4}>Wert4
<option value="5" {test5}>Wert5
<option value="6" {test6}>Wert6
</select></td></tr>
<tr><td>JaNein</td><td><input type="radio" name="radiofld"  value="1" {radiofld_1}>Ja <input type="radio" name="radiofld"  value="2" {radiofld_2}>Nein</td></tr>
<tr><td>Checkit</td><td><input type="checkbox" name="chkfld1"  value="1" {chkfld1_1}>A 
			<input type="checkbox" name="chkfld2"  value="2" {chkfld2_2}>B
			<input type="checkbox" name="chkfld3"  value="2" {chkfld3_2}>C</td></tr>

</table>
<input type="button" name="saveit" value="sichern" onClick="checkfelder()">
<input type="button" name="search" value="suchen" onClick="doSubmit();">
</form>
</center>
</body>
</html>
