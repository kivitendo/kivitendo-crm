<?php
	require_once("../inc/stdLib.php");
	include("crmLib.php");
	include("UserLib.php");
	include("FirmenLib.php");

	function getMailTpl($id,$KontaktTO='') {
		$data=getOneMailVorlage($id);
		$Subject=$data["cause"];
		$BodyText=$data["c_long"];
		if ($KontaktTO<>'') {
			$user=getUserStamm($_SESSION["loginCRM"]);
			if (substr($KontaktTO,0,1)=="P") {
				include("inc/persLib.php");
				$empf=getKontaktStamm(substr($KontaktTO,1));
                $tmp = getFirmaCVars($empf["cp_cv_id"]);
                if ($tmp) foreach($tmp as $key=>$val) { $empf[$key]=$val; };
			} else if ($KontaktTO) {
				include("inc/FirmenLib.php");
				$empf=getFirmenStamm(substr($KontaktTO,1),true,substr($KontaktTO,0,1));
			};
			foreach ($user as $key=>$val) {
				$empf['employee'.strtolower($key)]=$val;
			}
			preg_match_all("/%([A-Z0-9_]+)%/iU",$BodyText,$ph, PREG_PATTERN_ORDER);
			$ph=array_slice($ph,1);
			if ($ph[0]) {
				$anrede=false;
				foreach ($ph[0] as $x) {
					//$y=$empf[strtolower($x)];
					$y=$empf[$x];
					if ($x=="cp_greeting") $anrede=$y;
					$BodyText=preg_replace("/%".$x."%/i",$y,$BodyText);
				}
				if ($anrede=="Herr") { $BodyText=preg_replace("/%cp_anrede%/","r",$BodyText); }
				else if ($anrede) { $BodyText=preg_replace("/%cp_anrede%/","",$BodyText); }
			}
		}
		$MailSign=ereg_replace("\r","",$user["mailsign"]);
		$objResponse = new xajaxResponse();
		$objResponse->assign("rcmsg", "innerHTML", "");
		$objResponse->assign("Subject", "value", $Subject);
		$objResponse->assign("BodyText", "value", $BodyText." \n".$MailSign);
		return $objResponse;
	}
	function saveMailTpl($sub,$txt,$mid=0) {
		$rc=saveMailVorlage(array("Subject"=>$sub,"BodyText"=>$txt,"MID"=>$mid));
		$objResponse = new XajaxResponse();
		if ($rc){
			if ($mid>0) {
                $sScript = "var sel = document.getElementById('vorlagen').selectedIndex;";
                $sScript .= "document.getElementById('vorlagen').options[sel].text = '$sub';";
                $objResponse->script($sScript);
			} else {
                $sScript  = "var objOption = new Option('".$sub."', '".$rc."');";
                $sScript .= "document.getElementById('vorlagen').options.add(objOption);";
                $objResponse->script($sScript);
			}
			$objResponse->assign("rcmsg", "innerHTML", "Vorlage gesichert");
			return $objResponse;
		} else {
			$objResponse->assign("rcmsg", "innerHTML", "Fehler beim sichern");
			return $objResponse;
		}
		return true;
	}
	function delMailTpl($id) {
		$rc=deleteMailVorlage($id);
		$objResponse = new XajaxResponse();
        //delOptions
        $sScript = "var sel = document.getElementById('vorlagen').selectedIndex;";
        $sScript .= "document.getElementById('vorlagen').options[sel]=null;";
        $objResponse->script($sScript);
		$objResponse->assign("Subject", "value", "");
		$objResponse->assign("BodyText", "value", "");
		if ($rc) {
			$objResponse->assign("rcmsg", "innerHTML", "Vorlage gelöscht");
		} else {
			$objResponse->assign("rcmsg", "innerHTML", "Vorlage konnte nicht gelöscht werden");
		}
		return $objResponse;
	}

	require("mailcommon".XajaxVer.".php");
	$xajax->processRequest();

?>
