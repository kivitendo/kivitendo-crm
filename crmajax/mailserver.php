<?
	require_once("../inc/stdLib.php");
	include("crmLib.php");
	include("UserLib.php");
	function getMailTpl($id,$KontaktTO='') {
		$data=getOneMailVorlage($id);
		$Subject=$data["cause"];
		$BodyText=$data["c_long"];
		if ($KontaktTO<>'') {
			if (substr($KontaktTO,0,1)=="K") {
				include("inc/persLib.php");
				$empf=getKontaktStamm(substr($KontaktTO,1));
			} else if ($KontaktTO) {
				include("inc/FirmenLib.php");
				$empf=getFirmenStamm(substr($KontaktTO,1),true,substr($KontaktTO,0,1));
			};
			preg_match_all("/%([A-Z0-9_]+)%/iU",$BodyText,$ph, PREG_PATTERN_ORDER);
			$ph=array_slice($ph,1);
			if ($ph[0]) {
				$anrede=false;
				foreach ($ph[0] as $x) {
					$y=$empf[strtolower($x)];
					if ($x=="cp_greeting") $anrede=$y;
					$BodyText=preg_replace("/%".$x."%/i",$y,$BodyText);
				}
				if ($anrede=="Herr") { $BodyText=preg_replace("/%cp_anrede%/","r",$BodyText); }
				else if ($anrede) { $BodyText=preg_replace("/%cp_anrede%/","",$BodyText); }
			}
		}
		$user=getUserStamm($_SESSION["loginCRM"]);
		$MailSign=ereg_replace("\r","",$user["MailSign"]);
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("Subject", "value", utf8_encode($Subject));
		$objResponse->addAssign("BodyText", "value", utf8_encode($BodyText." \n".$MailSign));
		return $objResponse;
	}
	function saveMailTpl($sub,$txt,$mid=0) {
		$rc=saveMailVorlage(array("Subject"=>utf8_decode($sub),"BodyText"=>utf8_decode($txt),"MID"=>$mid));
		if ($rc){
			$objResponse = new myXajaxResponse();
			if ($mid>0) {
				$objResponse->modOption("vorlagen", $sub);
				return $objResponse;
			} else {
				$objResponse->addCreateOption("vorlagen",$sub,$rc);
				return $objResponse;
			}
		}
		return true;
	}
	function delMailTpl($id) {
		$rc=deleteMailVorlage($id);
		$objResponse = new myXajaxResponse();
		$objResponse->delOption("vorlagen");
		$objResponse->addAssign("Subject", "value", "");
		$objResponse->addAssign("BodyText", "value", "");
		return $objResponse;
	}

	require("mailcommon.php");
	$xajax->processRequests();


?>
