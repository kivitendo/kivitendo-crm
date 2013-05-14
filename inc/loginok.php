<?php
if ($_SESSION["cookie"]) $sessid=$_COOKIE[$_SESSION["cookie"]];
if (!$_SESSION["db"] ||
    $sessid=="" ||
    ($_SESSION["sessid"]<>$_COOKIE[$_SESSION["cookie"]])) {
		while( list($key,$val) = each($_SESSION) ) {
			unset($_SESSION[$key]);
		}
		$tmp=anmelden();
		if (!$tmp) echo "+++++++++++++++++++++++++++ Fehler: anmelden fehlgeschlagen! +++++++++++++++++++++++++++";
		$db=$_SESSION["db"];
		$_SESSION["loginok"]="ok";
} else {
	$db=new myDB($_SESSION["dbhost"],$_SESSION["dbuser"],$_SESSION["dbpasswd"],$_SESSION["dbname"],$_SESSION["dbport"]);
	$_SESSION["db"]=$db;
	$_SESSION["loginok"]="ok";
}

?>
