<?php
    require_once ("xajax_core/xajax.inc.php");
    if (empty($punkt)) $punkt = "";
    $xajax = new xajax("$punkt./crmajax/firmaserver.php");

    define("xajaxver", $xajax->getVersion());
    //$xajax->configure('javascript URI','./crmajax/');
    //$xajax->configure('debug', true);

    $xajax->register(XAJAX_FUNCTION,"getCustomTermin");
    $xajax->register(XAJAX_FUNCTION,"showShipadress");
    $xajax->register(XAJAX_FUNCTION,"showContactadress");
    $xajax->register(XAJAX_FUNCTION,"showCalls");
    $xajax->register(XAJAX_FUNCTION,"showDir");
    $xajax->register(XAJAX_FUNCTION,"newDir");
    $xajax->register(XAJAX_FUNCTION,"lockFile");
    $xajax->register(XAJAX_FUNCTION,"moveFile");
    $xajax->register(XAJAX_FUNCTION,"delFile");
    $xajax->register(XAJAX_FUNCTION,"showFile");
    $xajax->register(XAJAX_FUNCTION,"getDocVorlage_");
    $xajax->register(XAJAX_FUNCTION,"saveAttribut");
    $xajax->register(XAJAX_FUNCTION,"editTevent");
    $xajax->register(XAJAX_FUNCTION,"listTevents");
?>
