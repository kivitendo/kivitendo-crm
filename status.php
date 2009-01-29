<?php 
	session_start();
	$ver=file("VERSION");
	$ver=$ver[0];
	if ($_GET["test"]=="ja") {
		require("inc/stdLib.php");
		$rc=$db->getAll("select * from crm order by version","Status");
		
	}
?>
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<body>
<p class="listtop">Status</p>

<!--table class="karte"><tr><td class="karte"-->
<!---------------------------------------------------------------------->
<center>
<?php
$db=false;
$prog=false;
$d = dir("tmp/");
while (false !== ($entry = $d->read())) {
	if (ereg("upd.*log",$entry)) echo "<a href='tmp/$entry'>$entry</a><br>\n";
	if (ereg("instprog.log",$entry)) $prog=true;
	if (ereg("install.log",$entry)) $db=true;
}
$d->close();
if ($prog) { echo "<a href='tmp/instprog.log'>Programminstallation</a><br>"; } else { echo "Kein Logfile f&uuml;r Programminstallation<br>"; }
if ($db) { echo "<a href='tmp/install.log'>Datenbankinstallation</a><br>"; } else { echo "Kein Logfile f&uuml;r Datenbankinstallation<br>"; }
?>
<table>
	<tr><td>ProgrammVersion</td><td>[<?= $ver ?>]</td></tr>
	<tr><td>Datenbank:</td><td> [<?= $_SESSION["dbname"] ?>]</td></tr>
	<tr><td>db-Server:</td><td>[<?= $_SESSION["dbhost"] ?>]</td></tr>
	<tr><td>Benutzer:</td><td>[<?= $_SESSION["employee"] ?>:<?= $_SESSION["loginCRM"] ?>]</td></tr>
	<tr><td>Session-ID:</td><td>[<?= session_id() ?>]</td></tr>
	<tr><td>PHP-Umgebung:</td><td>[<a href="info.php">anzeigen</a>]</td></tr>
	<tr><td>Session<a href="showsess.php?ok=show">:</a></td><td>[<a href="showsess.php">l&ouml;schen</a>]</td></tr>
	<tr><td>db-Zugriff:</td><td>[<a href="status.php?test=ja">testen</a>]</td></tr>
	<tr><td>Installationscheck:</td><td>[<a href='inc/install.php?check=1'>durchf&uuml;hren</a>]</td></tr>
</table>
<?php
	if ($rc) {
		echo 'Datenbankzugriff erfolgreich!<br>';
		foreach ($rc as $row) {
			echo 'Installierte Version: '.$row["version"].' vom: '.$row["datum"].' durch: '.$row["uid"].'<br>';
		}
	}

?>
</center>
<!---------------------------------------------------------------------->
<!--/td></tr></table-->
</body>
</html>
