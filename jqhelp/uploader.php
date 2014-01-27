<?php
require_once("../inc/stdLib.php"); // nur wegen chkdir

//error_reporting(E_ALL | E_STRICT);
require('../jquery-ui/plugin/FileUpload/server/php/UploadHandler.php');
//$options['upload_dir'] = getcwd().'/../dokumente/'.$_SESSION['dbname'].'/tmp/';
$options['upload_dir'] = $_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$_SESSION['login'].'/tmp/';
chkdir($_SESSION['login'].'/tmp/');
$options['upload_url'] = $_SESSION['baseurl'].'crm/dokumente/'.$_SESSION['dbname'].'/'.$_SESSION['login'].'/tmp/';
$upload_handler = new UploadHandler($options);

?>
