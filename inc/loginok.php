<?
// $Id$
if (!$_SESSION["dns"] || 
    !$_SESSION["db"] ||
    ($_GET["login"] && $_SESSION["employee"]<>$_GET["login"]) ) {
	if ($_SESSION["employee"] && !$_GET["login"]) $_GET["login"]=$_SESSION["employee"];
	if ($_GET["login"]) {
		while( list($key,$val) = each($_SESSION) ) {
			unset($_SESSION[$key]);
		}
		if (!is_file("../$ERPNAME/users/".$_GET["login"].".conf")) header("location: login.php");
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
	$db=new myDB($_SESSION["dbhost"],$_SESSION["dbuser"],$_SESSION["dbpasswd"],$_SESSION["dbname"],$showErr);
	$_SESSION["db"]=$db;
	$_SESSION["loginok"]="ok";
}

?>
