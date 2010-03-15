<?php
session_start();
echo "<html><head>\n";
include("inc/stdLib.php");
if ($_POST) {
	require_once("documents.php");
	$dbfile=new document();
    $id=$dbfile->searchDocument($_FILES["Datei"]["name"],$_POST["pfad"]);
    if ($id) {
        $rc = $dbfile->getDokument($id);
    }
    if ($dbfile->lock>0) {
        echo translate('.:file locked:.','firma')."!";    
        $rc = False;
    } else {
	    $dbfile->setDocData("descript",$_POST["descript"]);
	    $rc=$dbfile->uploadDocument($_FILES,$_POST["pfad"]);
    }
	if ($rc) {
	?>
	<script>
		top.main_window.newFile('left');
		top.main_window.dateibaum('left','<?php echo  $_POST["pfad"] ?>')
	</script>
<?php  }; 
} ?>
<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<head><body class="klein" style="padding:0em; margin:0em;">
<table width="100%" class="klein lg">
	<tr style="border-bottom:1px solid black;"><td><?php echo  translate('.:uploadDocument:.','firma') ?></td><td align="right"><a href="javascript:top.main_window.newFile('left')">(X)</a></td></tr>
</table>
<form name="iform" action="upload.php?fid=<?php echo  $_GET["fid"] ?>&pid=<?php echo  $_GET["pid"] ?>" method="post" enctype="multipart/form-data">
<input id="upldpath" name="pfad" type="hidden">
<br>
&nbsp;<textarea name="descript" id="caption" cols="37" rows="3"></textarea><br>
&nbsp;<?php echo  translate('.:Remarks:.','firma') ?><br>
&nbsp;<input id="Datei" type="file" name="Datei" size="19"><br>
&nbsp;<?php echo  translate('.:Filename:.','firma') ?><br>
<br>
&nbsp;<input type="submit" value="<?php echo  translate('.:save:.','firma') ?>">
</form>
</html>
