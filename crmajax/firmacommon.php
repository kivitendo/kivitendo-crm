<?
    require_once ("xajax/xajax.inc.php");


    $xajax = new xajax("crmajax/firmaserver.php");

    define("xajaxver", $xajax->getVersion());

    $xajax->registerFunction("Buland");
    $xajax->registerFunction("getShipto");
    $xajax->registerFunction("showShipadress");
    $xajax->registerFunction("showContactadress");
    $xajax->registerFunction("showCalls");
    $xajax->registerFunction("showDir");
    $xajax->registerFunction("newDir");
    $xajax->registerFunction("moveFile");
    $xajax->registerFunction("delFile");
    $xajax->registerFunction("showFile");
    $xajax->registerFunction("getDocVorlage_");
    $xajax->registerFunction("saveAttribut");

?>
