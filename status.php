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
    $commit  = '<tr><td>Git: </td><td>'.substr($out[0],7,-34).'</td></tr>';
    $commit .= '<tr><td>Datum: </td><td>'.$date.'</td></tr>';
}
$rc = false;
/*if( varExist( $_GET['test'] == 'ja' ) ){
    $rs = $GLOBALS['dbh']->getOne("select * from crm order by  version DESC, datum DESC");
    printArray( $rs );
}*/
$menu =  $_SESSION['menu'];
$head = mkHeader();
?>
<html>
<head><title></title>
<?php
    echo varExist( $menu['stylesheets'] );
    echo varExist( $menu['javascripts'] );
    echo varExist( $head['CRMCSS'] );
    echo varExist( $head['JQUERY'] );
    echo varExist( $head['JQUERYUI'] );
    echo varExist( $head['THEME'] ) ;
    echo varExist( $head['JQTABLE'] ) ;

?>
    <script language="JavaScript" type="text/javascript">
        function chksrv() {
            $.get("jqhelp/logserver.php",function(data) {
                $("#SRV").append(data);
            });
        }
        $(document).ready(function() {
          $('#dbwrapper').dialog({
                    autoOpen: false,
                    title: 'Db-Test'
                });
          $("#showDB").click(function() {
                 $.ajax({
                    dataType: "json",
                    url: "ajax/testDB.php?action=showDB",
                    method: "GET",
                    success : function (data){
                        $("#dbwrapper").empty();
                        $("#dbwrapper").append('Installierte Version: ' +data.version +'</br></br> vom: ' +data.datum+ '</br></br> durch: ' +data.uid);
                        $("#dbwrapper").dialog('open');
                    },
                     error: function(){
                        alert("Fehler beim Test der Datenbankverbindung aufgetreten!");
                    }
                 });
          });
          $("#info").tablesorter();
        });
    </script>
</head>
<body>
<?php
 echo $menu['pre_content'];
 echo $menu['start_content'];
?>
<div class="ui-widget-content" style="height:600px">
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">Status</p>
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
<table id="info" class="tablesorter" style="width:auto; font-size:1pt">
    <thead></thead>
    <tbody>
    <tr><td>ProgrammVersion</td><td><?php echo  $VERSION." ".$SUBVER ?>]</td></tr>
<?php echo $commit; ?>
    <tr><td>Auth-Datenbank:</td><td> <?php echo  varExist( $_SESSION['erpConfig']['authentication/database']['db'] )?></td></tr>
    <tr><td>Datenbank:</td><td> <?php echo  varExist( $_SESSION['dbData']['dbname'] )?></td></tr>
    <tr><td>db-Server:</td><td><?php echo  varExist( $_SESSION['dbData']['dbhost'] )?></td></tr>
    <tr><td>Mandant:</td><td><?php echo  varExist( $_SESSION['dbData']['mandant'] )?>:<?php echo  $_SESSION['dbData']['manid']?></td></tr>
    <tr><td>Benutzer:</td><td><?php echo  varExist( $_SESSION['userConfig']['name'] )?>:<?php echo  $_SESSION['userConfig']['id'] ?></td></tr>
    <tr><td>Session-ID:</td><td><?php echo  session_id() ?></td></tr>
    <tr><td>PHP-Umgebung:</td><td><a href="info.php">anzeigen</a></td></tr>
    <tr><td>Session:</td><td><a href="delsess.php">löschen</a></td></tr>
    <tr><td>Session:</td><td><a href="showsess.php">anzeigen</a></td></tr>
    <tr><td>db-Zugriff:</td><td><div id="showDB">testen</div></td></tr>
    <tr><td>Updatecheck<a href="update/newdocdir.php?chk=1">:</a></td><td><a href='inc/update_neu.php'>durchf&uuml;hren</a></td></tr>
    <tr><td>Installationscheck:</td><td><a href='inc/install.php?check=1'>durchf&uuml;hren</a></td></tr>
    <tr><td>Benutzerfreundliche Links zu Verzeichnissen:</td><td><a href='links.php?all=1'>erzeugen</a></td></tr>
<?php if ($_SESSION['logfile']) { ?>
     <tr><td><input type="button" value="Server" onClick="chksrv()">:</td><td><div id='SRV'></div></td></tr>
<?php } ?>
</tbody>
</table>
<div id="dbwrapper">
</div>

<?php
   /* if ($rs) {
        echo 'Datenbankzugriff erfolgreich!<br>';

        //foreach ($rc as $row) {
            echo 'Installierte Version: '.$rs["version"].' vom: '.$rs["datum"].' durch: '.$rs["uid"].'<br>';
        //}
    } */
?>
</center>
<?php echo $menu['end_content']; ?>
</body>
</html>
