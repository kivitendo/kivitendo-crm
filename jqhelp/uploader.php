<?php
require_once("../inc/stdLib.php");

error_reporting(E_ALL | E_STRICT);
require('../jquery-ui/plugin/FileUpload/server/php/UploadHandler.php');
$options['upload_dir'] = getcwd().'/../dokumente/'.$_SESSION['dbname'].'/tmp/';
chkdir('tmp');
$options['upload_url'] = $_SESSION['baseurl'].'crm/dokumente/'.$_SESSION['dbname'].'/tmp/';
$upload_handler = new UploadHandler($options);

?>
