<?php
require_once("../inc/stdLib.php");

error_reporting(E_ALL | E_STRICT);
require('../jquery-ui/plugin/FileUpload/server/php/UploadHandler.php');
$options['upload_dir'] = getcwd().'/../dokumente/'.$_SESSION['mansel'].'/tmp/';
chkdir('tmp');
$options['upload_url'] = $_SESSION['baseurl'].'crm/dokumente/'.$_SESSION['mansel'].'/tmp/';
$upload_handler = new UploadHandler($options);

?>
