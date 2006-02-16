<?
// $Id: kontakt.php,v 1.3 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
 	include("inc/template.inc");
	include("inc/persLib.php");
	include("inc/UserLib.php");
	$co=getKontaktStamm($_GET["id"]);
	if ($co["cp_cv_id"]) {
		$Table=chkTable($co["cp_cv_id"]);
		header ("Location:".$Table."?id=".$_GET["id"]);
	} else {
		header ("Location:firma2.php?id=".$_GET["id"]);
	}

?>
