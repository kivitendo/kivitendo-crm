<?
    require_once ("xajax/xajax.inc.php");

    $xajax = new xajax("crmajax/logserver.php");
    define("xajaxver", $xajax->getVersion());
    $xajax->registerFunction("chkSrv");

?>
