<?php
//$_POST=$_GET;
$f = fopen("/tmp/android.txt","a");
fputs($f,"POST\n");
fputs($f,print_r($_POST,true));
fputs($f,"GET\n");
fputs($f,print_r($_GET,true));
require("androidLib.php");
$db = authDB();
fputs($f,"android\n".print_r($db,true));
if ($db) {
	$session = authuser($db,$_POST["login"],$_POST["password"],$_POST["ip"],$f);
	$db->log = true;
	fputs($f,"androit2\n".print_r($db,true));
}
fclose($f);
if ($session) {
    echo "200:".$session;
} else {
    echo false;
}
?>
