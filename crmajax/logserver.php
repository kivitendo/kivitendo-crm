<?php

	function chkSrv() {
        $x = @exec('tail -5 /var/log/apache2/error.log',$status1); 
        $x = @exec('tail -5 /var/log/apache2/access.log',$status2); 
        if (empty($status1)) {
            $status = "Error:<br />Logfile ist leer oder nicht lesbar.";  
        } else {
            $status = "Error:<br />".implode("<br />",$status1);  
        }
        $status .= "<br />------------------------------------<br />";
        if (empty($status2)) {
            $status .= "Access:<br />Logfile ist leer oder nicht lesbar.";  
        } else {
            $status .= "Access:<br />".implode("<br />",$status2);  
        }
        $objResponse = new xajaxResponse();
        if (trim($status)=="") $status="Keine Meldung";
        $objResponse->assign("SRV", "innerHTML", $status);
        return $objResponse;
	}

    require_once("../inc/conf.php");
	require("logcommon".XajaxVer.".php");
	$xajax->processRequest();

?>
