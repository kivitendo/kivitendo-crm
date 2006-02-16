<?
// $Id: loginok.php,v 1.1 2006/01/05 09:57:09 hli Exp $
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
		$_SESSION["ok"]="ok";
	} else {
		while( list($key,$val) = each($_SESSION) ) {
			unset($_SESSION[$key]);
		}
		header("location: ups.html");
    	}
} else {
	$db=new myDB($_SESSION["dns"]); 
	$_SESSION["db"]=$db;
	$_SESSION["ok"]="ok";
}

?>
