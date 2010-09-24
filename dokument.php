<?php
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	require("firmacommon".XajaxVer.".php");
    $pickup = "false";
    if ($_GET["P"]=="1") $pickup = "true";
	$files=liesdir($_SESSION["mansel"]);
	$t = new Template($base);
	$t->set_file(array("doc" => "dokument.tpl"));
	$t->set_var(array(
            ERPCSS      => $_SESSION["stylesheet"],
			AJAXJS  => $xajax->printJavascript(XajaxPath),
            PICUP => $pickup,
			));
	$t->Lpparse("out",array("doc"),$_SESSION["lang"],"firma");

?>
