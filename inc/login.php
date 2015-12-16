<?php
clearstatcache();
require_once "conf.php";
require_once "version.php";

if(!isset($_SESSION)) session_start();

while( list($key,$val) = each($_SESSION) ) {       
	     unset($_SESSION[$key]);
};

$_SESSION['ERP_BASE_URL']  = $ERP_BASE_URL;
$_SESSION['erpConfigFile'] = $erpConfigFile;
$_SESSION['ERPNAME']       = $ERPNAME;

// Beim Setzen von crmpath muss zwingend darauf geachtet werden, dass man sich nicht in einem Unterverzeichnis befindet.
// Bem.: Da am Ende des von getcwd() zurück gegeben Strings kein Slash steht funktioniert dirname() hier.
// aus /root/kivitendo/inc wird /root/kivitendo 
$basepath = substr(__DIR__,0,-3).'../'.$_SESSION['ERPNAME'];
$_SESSION['crmpath'] = $basepath;
$conffile = $_SESSION['crmpath'].'/config/'.$erpConfigFile.'.conf';
if ( is_file($conffile) ) {
    $tmp = anmelden();
    if ( $tmp ) {
        if ( chkcrm() ) $rs = $GLOBALS['db']->getOne('SELECT * FROM crm ORDER BY version DESC LIMIT 1');
        $dbver  = $rs['version'];
        // Existiert crm nicht so kann auch ein Fehler-Objekt zurückgegeben werden
        if ( is_object($rs) || !$rs || $dbver=="" || $dbver==false ) {
            echo "CRM-Tabellen sind nicht (vollst&auml;ndig) installiert"; 
            flush(); 
            require("install.php");
            require("inc/update_neu.php");
            echo "<b>Richten Sie nun zun&auml;chst den [<a href='mandant.php'>Mandenten</a>] in der CRM ein,<br>";
            echo "danach den Benutzer.</b><br>";
            exit(1);
        } else if (  $dbver <> $VERSION ) {
            echo "Istversion: $dbver Sollversion: $VERSION<br>";
            require("inc/update_neu.php");
            echo "<b>Richten Sie nun zun&auml;chst den [<a href='mandant.php'>Mandenten</a>] in der CRM ein,<br>";
            echo "danach den Benutzer.</b><br>";
        } else {
            $_SESSION["loginok"] = "ok";
            require ("update_neu.php");
        } /*else {
            echo "db-Version nicht ok";
            exit;*/
    } else {
        echo "Session abgelaufen oder ein anderes Problem beim Anmelden."; 
        $Url  = (empty( $_SERVER['HTTPS'] )) ? 'http://' : 'https://';
        $Url .= $_SERVER['HTTP_HOST'];
        $Url .= preg_replace( "^crm/.*^", "", $_SERVER['REQUEST_URI'] );
        session_unset();
        //header('Location: '.$Url.'login.pl?x=1');
        exit;
    };
} else {
    echo $conffile." Configfile nicht gefunden<br>$PHPSELF<br>";
    exit;
}
?>
