<?php
    require_once("inc/stdLib.php");
    $file = "dokumente/".$_SESSION["mansel"].$_GET['file'];
?>
<html>
<head><title>Download</title>
<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<meta http-equiv="refresh" content="1; URL='<?php echo  $file ?>'">
</head>
<body onLoad="self.focus();">
<div align="center"><a href="<?php echo  "dokumente/".$_SESSION["mansel"].$_GET['file'] ?>">Der Download startet sofort.<br \> 
Falls Ihr Browser die automatische Weiterleitung nicht unterst&uuml;tzt klicken Sie hier!</a><br><br>
<a href="JavaScript:self.close();">close</a>
</div>
</body>
</html>
