<?
    require_once ("xajax/xajax.inc.php");

    $xajax = new xajax("crmajax/mailserver.php");
    define("xajaxver", $xajax->getVersion());
    $xajax->registerFunction("getMailTpl");
    $xajax->registerFunction("delMailTpl");
    $xajax->registerFunction("saveMailTpl");

?>
