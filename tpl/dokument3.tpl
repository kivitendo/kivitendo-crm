<!-- $Id: dokument3.tpl,v 1.3 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
		function showH() {
			f1=open("dochelp.html","hilfe","width=400,height=350,left=100,top=100");
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
		<td class="reiter desel">
			<a href="{Link2}">neue Vorlage</a>
		</td>
		<td class="reiter sel">
			<a href="{Link3}" class="reiterA">Felder</a>
		</td>
		<td class="reiter desel">
			<a href="{Link4}"></a>
		</td>
	</tr>
</table>

<!--table width="95%" class="karte"><tr><td class="karte"-->
<!-- Hier beginnt die Karte  ------------------------------------------->
{vorlage}<br>

<table style="width:550px">
<!-- BEGIN Liste -->
	<tr>
		<form name="update" action="dokument3.php" method="post">
		<input type="hidden" name="fid" value="{fid}">
		<input type="hidden" name="docid" value="{docid}">
		<td><input type="text" name="feldname" value="{feldname_}" size="15" maxlength="20"></td>
		<td><input type="text" name="platzhalter" value="{platzhalter_}" size="12" maxlength="20"></td>
		<td><input type="text" name="laenge" value="{laenge_}" size="2"maxlength="5"> </td>
		<td><input type="text" name="zeichen" value="{zeichen_}" size="12" maxlength="20"></td>
		<td><input type="text" name="position" value="{position_}" size="2" maxlength="5"></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="5"><input type="text" name="beschreibung" value="{beschreibung_}" size="65"  maxlength="200"></td>
		<td><input type="submit" name="ok" value="ok"><input type="submit" name="del" value="del"></td>
		</form>
	</tr>
<!-- END Liste -->
	<tr>
		<td colspan="6"><hr></td></tr>
	</tr>
	<tr>
		<form name="neu" action="dokument3.php" method="post">
		<input type="hidden" name="docid" value="{docid}">
		<td class="smal"><input type="text" name="feldname" size="15" maxlength="20"><br>Feldname</td>
		<td class="smal"><input type="text" name="platzhalter" size="12" maxlength="20"><br>Platzhalter</td>
		<td class="smal"><input type="text" name="laenge" size="2" maxlength="5"><br>L&auml;nge</td>
		<td class="smal"><input type="text" name="zeichen" size="12" maxlength="20"><br>Zeichen</td>
		<td class="smal"><input type="text" name="position" size="2" maxlength="5"><br>Pos.</td>
		<td class="smal"></td>
	</tr>
	<tr class="smal">
		<td class="smal" colspan="5"><input type="text" name="beschreibung" size="60" maxlength="200"><br>Beschreibung</td>
		<td><input type="submit" name="neu" value="neu"></td>
		</form>
	</tr>
</table>
<!-- Hier endet die Karte ------------------------------------------->
<!--/td></tr></table-->
</body>
</html>
