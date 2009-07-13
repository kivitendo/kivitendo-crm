<?
    require_once ("xajax_core/xajax.inc.php");

    $xajax = new xajax("crmajax/firmaserver.php");

    define("xajaxver", $xajax->getVersion());
    //$xajax->configure('javascript URI','./crmajax/');
    //$xajax->configure('debug', true);

    $xajax->register(XAJAX_FUNCTION,"Buland");
    $xajax->register(XAJAX_FUNCTION,"getShipto");
    $xajax->register(XAJAX_FUNCTION,"showShipadress");
    $xajax->register(XAJAX_FUNCTION,"showContactadress");
    $xajax->register(XAJAX_FUNCTION,"showCalls");
    $xajax->register(XAJAX_FUNCTION,"showDir");
    $xajax->register(XAJAX_FUNCTION,"newDir");
    $xajax->register(XAJAX_FUNCTION,"moveFile");
    $xajax->register(XAJAX_FUNCTION,"delFile");
    $xajax->register(XAJAX_FUNCTION,"showFile");
    $xajax->register(XAJAX_FUNCTION,"getDocVorlage_");
    $xajax->register(XAJAX_FUNCTION,"saveAttribut");

?>
