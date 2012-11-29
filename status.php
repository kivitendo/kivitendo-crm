<?php 
session_start();
require_once("inc/conf.php");
require_once("inc/version.php");
require_once("inc/stdLib.php");
if ($logfile) 
    require("crmajax/logcommon".XajaxVer.".php");
if ($_GET["test"]=="ja") {
    $rc=$db->getAll("select * from crm order by version","Status");
}
$menu =  $_SESSION['menu'];
?>
<html>
<head><title></title>
    <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['basepath'].'css/'.$_SESSION["stylesheet"]; ?>/main.css"></link>
    <!-- ERP Stylesheet -->
    <?php echo $menu['stylesheets']; ?>
    <!-- ERP JavaScripts -->
    <?php echo $menu['javascripts']; ?>
    <!-- Ende ERP -->
<?php
    if ($logfile) {
    echo $xajax->printJavascript(XajaxPath) 
?>
    <script language="JavaScript" type="text/javascript">
    <!--
        function chksrv() {
            xajax_chkSrv();
        }
    -->
    </script>
<?php    } ?>
<body>
<?php
 echo $menu['pre_content'];
 echo $menu['start_content'];
?>
<p class="listtop">Status</p>

<!--table class="karte"><tr><td class="karte"-->
<!---------------------------------------------------------------------->
<center>
<?php
$db=false;
$prog=false;
$d = dir("log/");
while (false !== ($entry = $d->read())) {
	if (preg_match('/upd.*log/',$entry)) echo "<a href='log/$entry'>$entry</a><br>\n";
	if (preg_match('/instprog.log/',$entry)) $prog=true;
	if (preg_match('/install.log/',$entry)) $db=true;
}
$d->close();
if ($prog) { echo "<a href='log/instprog.log'>Programminstallation</a><br>"; } else { echo "Kein Logfile f&uuml;r Programminstallation<br>"; }
if ($db) { echo "<a href='log/install.log'>Datenbankinstallation</a><br>"; } else { echo "Kein Logfile f&uuml;r Datenbankinstallation<br>"; }
?>
<table>
	<tr><td>ProgrammVersion</td><td>[<?php echo  $VERSION." ".$SUBVER ?>]</td></tr>
	<tr><td>Datenbank:</td><td> [<?php echo  $_SESSION["dbname"] ?>]</td></tr>
	<tr><td>db-Server:</td><td>[<?php echo  $_SESSION["dbhost"] ?>]</td></tr>
	<tr><td>Benutzer:</td><td>[<?php echo  $_SESSION["employee"] ?>:<?php echo  $_SESSION["loginCRM"] ?>]</td></tr>
	<tr><td>Session-ID:</td><td>[<?php echo  session_id() ?>]</td></tr>
	<tr><td>PHP-Umgebung:</td><td>[<a href="info.php">anzeigen</a>]</td></tr>
	<tr><td>Session<a href="showsess.php?ok=show">:</a></td><td>[<a href="showsess.php">l&ouml;schen</a>]</td></tr>
	<tr><td>db-Zugriff:</td><td>[<a href="status.php?test=ja">testen</a>]</td></tr>
	<tr><td>Updatecheck<a href="update/newdocdir.php?chk=1">:</a></td><td>[<a href='inc/update_neu.php'>durchf&uuml;hren</a>]</td></tr>
	<tr><td>Installationscheck:</td><td>[<a href='inc/install.php?check=1'>durchf&uuml;hren</a>]</td></tr>
	<tr><td>Benutzerfreundliche Links zu Verzeichnissen:</td><td>[<a href='links.php?all=1'>erzeugen</a>]</td></tr>
<?php if ($logfile) { ?>
 	<tr><td><input type="button" value="Server" onClick="chksrv()">:</td><td>[<div id='SRV'></div>]</td></tr>
<?php } ?>
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
<?php echo $menu['end_content']; ?>
</body>
</html>
