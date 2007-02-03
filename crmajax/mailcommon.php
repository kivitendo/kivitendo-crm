<?
	require_once ("xajax/xajax.inc.php");

	class myXajaxResponse extends xajaxResponse {  
	    function addCreateOption($sSelectId, $sOptionText, $sOptionValue)      {  
		$sScript  = "var objOption = new Option('".$sOptionText."', '".$sOptionValue."');";
	        $sScript .= "document.getElementById('".$sSelectId."').options.add(objOption);";
        	$this->addScript($sScript);
	    }
	    function addCreateOptions($sSelectId, $aOptions)    {
	        foreach($aOptions as $sOptionText => $sOptionValue) {
	            $this->addCreateOption($sSelectId, $sOptionText, $sOptionValue);
        	}
	    }
	    function modOption($sSelectId, $sOptionText)      {
		$sScript = "var sel = document.getElementById('".$sSelectId."').selectedIndex;";
		$sScript .= "document.getElementById('".$sSelectId."').options[sel].text = '$sOptionText';";
		$this->addScript($sScript);
	    }
	    function delOption($sSelectId)      {
		$sScript = "var sel = document.getElementById('".$sSelectId."').selectedIndex;";
		$sScript .= "document.getElementById('".$sSelectId."').options[sel]=null;";
		$this->addScript($sScript);
	   }
	}

	$xajax = new xajax("crmajax/mailserver.php");
	$xajax->registerFunction("getMailTpl");
	$xajax->registerFunction("delMailTpl");
	$xajax->registerFunction("saveMailTpl");

?>
