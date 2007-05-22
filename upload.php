<?php
session_start();
echo "<html><head>\n";
if ($_POST) {
	include("inc/stdLib.php");
	include("documents.php");
	$dbfile=new document();
	$dbfile->setDocData("descript",$_POST["descript"]);
	$rc=$dbfile->uploadDocument($_FILES,$_POST["pfad"]);
	if ($rc) {
	?>
	<script>
		top.main_window.newFile('left');
		top.main_window.dateibaum('left','<?= $_POST["pfad"] ?>')
	</script>
<?php  }; 
} ?>
<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<head><body class="klein" style="padding:0em; margin:0em;">
<table width="100%" class="klein lg">
	<tr style="border-bottom:1px solid black;"><td>Eine Datei hochladen</td><td align="right"><a href="javascript:top.main_window.newFile('left')">(X)</a></td></tr>
</table>
<form name="iform" action="upload.php?fid=<?= $_GET["fid"] ?>&pid=<?= $_GET["pid"] ?>" method="post" enctype="multipart/form-data">
<input id="upldpath" name="pfad" type="hidden">
<br>
&nbsp;<textarea name="descript" id="caption" cols="37" rows="3"></textarea><br>
&nbsp;Bemerkung<br>
&nbsp;<input id="Datei" type="file" name="Datei" size="19"><br>
&nbsp;Dateiname<br>
<br>
&nbsp;<input type="submit" value="speichern">
</form>
</html>
