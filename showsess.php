<?
session_start();
if ($_GET["ok"]) {
	echo "<pre>";
	print_r($_SESSION);
	echo "</pre>";
	echo "<form name='x' action='showsess.php' method='post'>";
	echo "<input type='submit' name='del' value='Session l&ouml;schen'>";
	echo "</form>";
} else {
	while( list($key,$val) = each($_SESSION) ) {
		unset($_SESSION[$key]);
	}
	echo "ok. Session-Variablen gel&ouml;scht.<br>";
	echo "Rufen Sie nun einen anderen CRM-Men&uuml;punkt auf, um eine neue Session zu erzeugen";
}

?>

