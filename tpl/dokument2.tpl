<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showD (id) {
		if (id>0) {
			Frame=eval("parent.main_window");
			uri="document2.php?id=" + id;
			Frame.location.href=uri;
		}
	}
	//-->
	</script>
<body>
<p class="listtop">Dokumentvorlagen</p>
<table class="reiter">
	<tr>
		<td class="reiter desel">
			<a href="{Link1}">Dokumente</a>
		</td>
		<td class="reiter sel">
			<a href="{Link2}" class="reiterA">neue Vorlage</a>
		</td>
		<td class="reiter desel">
			<a href="{Link3}" >Felder</a>
		</td>
		<td class="reiter desel">
			<a href="{Link4}"></a>
		</td>
	</tr>
</table>

<!--table width="95%" class="karte"><tr><td class="karte"-->
<!-- Hier beginnt die Karte  ------------------------------------------->
<br>
<form name="firma4" enctype='multipart/form-data' action="dokument2.php" method="post">
<input type="hidden" name="did" value="{did}">
<input type="hidden" name="file_" value="{file}">
<input type="text" name="vorlage" value="{vorlage}" size="40" maxlength="80"><br>Bezeichnung<br>
<textarea name="beschreibung" cols="52" rows="3">{beschreibung}</textarea><br>Beschreibung<br>
<b>{file}</b><br>
<input type="file" name="file" size="30"><br>Vorlage<br><br>
Dokumententyp
<input type="radio" name="applikation" value="O" {sel1}>OOo
<input type="radio" name="applikation" value="R" {sel2}>RTF
<input type="radio" name="applikation" value="B" {sel3}>Bin&auml;rfile 
<input type="radio" name="applikation" value="T" {sel4}>Tex<br><br>
<input type="submit" name="ok" value="sichern"> <input type="submit" name="del" value="l&ouml;schen">
</td></tr></table>
<!-- Hier endet die Karte ------------------------------------------->
<!--/td></tr></table-->
</body>
</html>
