<?php
    require_once ("xajax_core/xajax.inc.php");

    $xajax = new xajax("crmajax/logserver.php");

    //$xajax->configure('debug', true);
    //$xajax->configure('javascript URI','./crmajax/');
    define("xver", $xajax->getVersion());

    $xajax->register(XAJAX_FUNCTION,"chkSrv");

?>
