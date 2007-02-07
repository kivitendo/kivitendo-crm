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
	    function delAllOptions($sSelectId)      {
		$sScript = "var i = document.getElementById('".$sSelectId."').length;";
		$sScript.= "while ( i > 0) {";
		$sScript.= "document.getElementById('".$sSelectId."').options[i-1]=null;";
		$sScript.= "i--;}";
		$this->addScript($sScript);
	   }
	}

	$xajax = new xajax("crmajax/firmaserver.php");
	$xajax->registerFunction("Buland");
	$xajax->registerFunction("getShipto");
	$xajax->registerFunction("showShipadress");
	$xajax->registerFunction("showContactadress");
        $xajax->registerFunction("showCalls");

?>
