<?php
session_start();
include("inc/stdLib.php");
$menu =  $_SESSION['menu'];
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
        $rc=$dbfile->uploadDocument($_FILES,$_POST["upldpath"]);
    }
    if ($rc) {
?>
	<script language="JavaScript">
		top.dateibaum('left','<?php echo  $_POST["upldpath"] ?>');
        top.document.getElementById("uploadfr").style.visibility = "hidden";
	</script>
<?php  }; 
} ?>
<html><head>
<title></title>
	<script language="JavaScript">
        function getpath() {
            var p = top.document.getElementById('path').innerHTML;
            document.getElementById('upldpath').value = p;
            return true;
        }
	</script>
    <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['baseurl'].'css/'.$_SESSION["stylesheet"]; ?>/main.css">
</head>
<body class="docfrm" style="padding:0em; margin:0em; width:100%; height:100%;" >
<form name="iform" action="upload.php" method="post" enctype="multipart/form-data" onSubmit="return getpath();">
<input id="upldpath" name="upldpath" type="hidden">
<br>
&nbsp;<textarea name="descript" class="normal" id="caption" cols="35" rows="3"></textarea><br>
&nbsp;<?php echo  translate('.:Remarks:.','firma') ?><br>
&nbsp;<input id="Datei" type="file" name="Datei" size="19"><br>
&nbsp;<?php echo  translate('.:Filename:.','firma') ?><br>
<br>
&nbsp;<input type="submit" value="<?php echo  translate('.:save:.','firma') ?>">
</form>
</html>
