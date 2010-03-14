<?php
	require_once("inc/stdLib.php");
	if ($_SESSION["loginCRM"])  {
		$v=($_SESSION["dbname"])?getVersiondb():"";
	}
	
?>
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<body>
<p class="listtop">Hilfe/Dokumentation</p>

<!--table class="karte"><tr><td class="karte"-->
<!---------------------------------------------------------------------->
<center><br>
<img src="image/lx-office-crm.png"><br>
<a href="http://lx-office.org" target="_top">http://lx-office.org</a> - <a href="mailto:info@lx-office.org" target="_top">info@lx-office.org</a><br>
&quot;Lx-Office CRM&quot; ist ein Teilprodukt aus dem Lx-Office Paket.<br>

die Software unterliegt der <a href="hilfe/artistic.html" target="_blank">Artistic License</a><br><br>
Verwendete Datenbank: [<?= $_SESSION["dbname"] ?>] Version [<?= $v ?>]  auf Server [<?= $_SESSION["dbhost"] ?>]<br>
Benutzer [<?= $_SESSION["employee"] ?>:<?= $_SESSION["loginCRM"] ?>] [<?= session_id() ?>]
<br><br><br>
&gt;<a href="hilfe/index.html">Online-Hilfe</a>&lt;<br>
</center>
<!---------------------------------------------------------------------->
<!--/td></tr></table-->
</body>
</html>
