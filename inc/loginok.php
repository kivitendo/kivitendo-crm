<?
// $Id$
if ($_GET["login"]) {
	$login=$_GET["login"];
}  
if (!$_SESSION["db"] ||
    ($login && $_SESSION["employee"]<>$login) ) {
	if ($_SESSION["employee"] && !$login) $login=$_SESSION["employee"];
	if ($login) {
		while( list($key,$val) = each($_SESSION) ) {
			unset($_SESSION[$key]);
		}
		if (!is_file("../$ERPNAME/users/".$login.".conf")) header("location: login.php");
		$tmp=anmelden($_GET["login"]);
		$db=$_SESSION["db"];
		$_SESSION["db"]=$db;
		$_SESSION["loginok"]="ok";
	} else {
		while( list($key,$val) = each($_SESSION) ) {
			unset($_SESSION[$key]);
		}
		header("location: ups.html");
    	}
} else {
	$db=new myDB($_SESSION["dbhost"],$_SESSION["dbuser"],$_SESSION["dbpasswd"],$_SESSION["dbname"],$_SESSION["dbport"],$showErr);
	$_SESSION["db"]=$db;
	$_SESSION["loginok"]="ok";
}

?>
