<?php 
session_start();
require_once("inc/version.php");
require_once("inc/stdLib.php");
$git = @exec('git log -1',$out,$rc);
if ( $rc > 0 ) {
    $commit = '';
} else {
    foreach( $out as $row ) {
        if ( substr($row,0,1) == 'D' ) $date = substr($row,6);
    }
    $commit  = '<tr><td>Git: </td><td>'.substr($out[0],7).'</td></tr>';
    $commit .= '<tr><td>Datum: </td><td>'.$date.'</td></tr>';
}
$rc = false;
if ( isset($_GET['test']) and $_GET['test'] == 'ja' ) {
    $rc = $GLOBALS['dbh']->getAll("select * from crm order by version","Status");
}
$menu =  $_SESSION['menu'];
$head = mkHeader();
?>
<html>
<head><title></title>
<?php 
	echo $menu['stylesheets'];
	echo $menu['javascripts'];
	echo $head['CRMCSS']; 
	echo $head['JQUERY']; 
	echo $head['JQUERYUI']; 
	echo $head['THEME']; 

?>
    <script language="JavaScript" type="text/javascript">
        function chksrv() {
            $.get("jqhelp/logserver.php",function(data) {
                $("#SRV").append(data);
            });
        }
    </script>
<body>
<?php
 echo $menu['pre_content'];
 echo $menu['start_content'];
?>
<p class="listtop">Status</p>
<center>
<?php
$db=false;
$prog=false;
$d = dir("log/");
print_r($d);
/*while (false !== ($entry = $d->read())) {
	if (preg_match('/upd.*log/',$entry)) echo "<a href='log/$entry'>$entry</a><br>\n";
	if (preg_match('/instprog.log/',$entry)) $prog=true;
	if (preg_match('/install.log/',$entry)) $db=true;
}
$d->close();*/
if ($prog) { echo "<a href='log/instprog.log'>Programminstallation</a><br>"; } else { echo "Kein Logfile f&uuml;r Programminstallation<br>"; }
if ($db) { echo "<a href='log/install.log'>Datenbankinstallation</a><br>"; } else { echo "Kein Logfile f&uuml;r Datenbankinstallation<br>"; }
?>
<table>
	<tr><td>ProgrammVersion</td><td>[<?php echo  $VERSION." ".$SUBVER ?>]</td></tr>
<?php echo $commit; ?>
	<tr><td>Datenbank:</td><td> [<?php echo  $_SESSION["dbname"] ?>]</td></tr>
	<tr><td>db-Server:</td><td>[<?php echo  $_SESSION["dbhost"] ?>]</td></tr>
	<tr><td>Benutzer:</td><td>[<?php echo  $_SESSION["login"] ?>:<?php echo  $_SESSION["loginCRM"] ?>]</td></tr>
	<tr><td>Session-ID:</td><td>[<?php echo  session_id() ?>]</td></tr>
	<tr><td>PHP-Umgebung:</td><td>[<a href="info.php">anzeigen</a>]</td></tr>
	<tr><td>Session<a href="showsess.php?ok=show">:</a></td><td>[<a href="showsess.php">l&ouml;schen</a>]</td></tr>
	<tr><td>db-Zugriff:</td><td>[<a href="status.php?test=ja">testen</a>]</td></tr>
	<tr><td>Updatecheck<a href="update/newdocdir.php?chk=1">:</a></td><td>[<a href='inc/update_neu.php'>durchf&uuml;hren</a>]</td></tr>
	<tr><td>Installationscheck:</td><td>[<a href='inc/install.php?check=1'>durchf&uuml;hren</a>]</td></tr>
	<tr><td>Benutzerfreundliche Links zu Verzeichnissen:</td><td>[<a href='links.php?all=1'>erzeugen</a>]</td></tr>
<?php if ($_SESSION['logfile']) { ?>
 	<tr><td><input type="button" value="Server" onClick="chksrv()">:</td><td>[<div id='SRV'></div>]</td></tr>
<?php } ?>
</table>
    
<?php
	if ($rc) {
		echo 'Datenbankzugriff erfolgreich!<br>';
//print_r($rc);
		foreach ($rc as $row) {
			echo 'Installierte Version: '.$row["version"].' vom: '.$row["datum"].' durch: '.$row["uid"].'<br>';
		}
	}
?>
</center>
<?php echo $menu['end_content']; ?>
</body>
</html>
