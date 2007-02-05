<?
	require_once("../inc/stdLib.php");
	include("FirmenLib.php");
	function getShipto($id) {
		$data=getShipStamm($id);
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("shiptoname", 		"value", $data["shiptoname"]);
		$objResponse->addAssign("shiptodepartment_1",	"value", $data["shiptodepartment_1"]);
		$objResponse->addAssign("shiptodepartment_2",	"value", $data["shiptodepartment_2"]);
		$objResponse->addAssign("shiptostreet", 	"value", $data["shiptostreet"]);
		$objResponse->addAssign("shiptocity", 		"value", $data["shiptocity"]);
		$objResponse->addAssign("shiptocontact", 	"value", $data["shiptocontact"]);
		$objResponse->addAssign("shiptocountry", 	"value", $data["shiptocountry"]);
		$objResponse->addAssign("shiptophone", 		"value", $data["shiptophone"]);
		$objResponse->addAssign("shiptofax", 		"value", $data["shiptofax"]);
		$objResponse->addAssign("shiptoemail", 		"value", $data["shiptoemail"]);
		$objResponse->addAssign("shiptozipcode", 	"value", $data["shiptozipcode"]);
		$objResponse->addAssign("shipto_id", 		"value", $data["shipto_id"]);
		$objResponse->addAssign("module", 		"value", $data["module"]);
		$objResponse->addAssign("shiptobland", 		"value", $data["shiptobland"]);
		return $objResponse;
	}
	function Buland($land,$bl) {
		$data=getBundesland(strtoupper($land));
		$objResponse = new myXajaxResponse();
		$objResponse->delAllOptions($bl);
		if (preg_match("/UTF-8/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="UTF-8"; }
		else if (preg_match("/ISO-8859-15/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-15"; }
		else if (preg_match("/ISO-8859-1/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-1"; }
		else { $charset="ISO-8859-1"; };
		foreach ($data as $row) { 
			$objResponse->addCreateOption($bl,html_entity_decode($row["bundesland"],ENT_NOQUOTES,$charset),$row["id"]);		
		}
		return $objResponse;
	}
	function showShipadress($id,$tab){
		$data=getShipStamm($id);
		if (preg_match("/UTF-8/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="UTF-8"; }
		else if (preg_match("/ISO-8859-15/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-15"; }
		else if (preg_match("/ISO-8859-1/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-1"; }
		else { $charset="ISO-8859-1"; };
		$maillink="<a href='mail.php?TO=".$data["shiptoemail"]."&KontaktTO=$tab".$data["trans_id"]."'>".$data["shiptoemail"]."</a>";
		$objResponse = new xajaxResponse();
                $objResponse->addAssign("SID",         		"innerHTML", $id);
                $objResponse->addAssign("shiptoname",           "innerHTML", htmlentities($data["shiptoname"]));
                $objResponse->addAssign("shiptodepartment_1",   "innerHTML", htmlentities($data["shiptodepartment_1"]));
                $objResponse->addAssign("shiptodepartment_2",   "innerHTML", htmlentities($data["shiptodepartment_2"]));
                $objResponse->addAssign("shiptostreet",         "innerHTML", htmlentities($data["shiptostreet"])); //,ENT_NOQUOTES,$charset));
                $objResponse->addAssign("shiptocountry",        "innerHTML", $data["shiptocountry"]);
                $objResponse->addAssign("shiptobland",          "innerHTML", html_entity_decode($data["shiptobundesland"],ENT_NOQUOTES,$charset));
                $objResponse->addAssign("shiptozipcode",        "innerHTML", $data["shiptozipcode"]);
                $objResponse->addAssign("shiptocity",           "innerHTML", htmlentities($data["shiptocity"]));
                $objResponse->addAssign("shiptocontact",        "innerHTML", $data["shiptocontact"]);
                $objResponse->addAssign("shiptophone",          "innerHTML", $data["shiptophone"]);
                $objResponse->addAssign("shiptofax",            "innerHTML", $data["shiptofax"]);
                $objResponse->addAssign("shiptocontact",        "innerHTML", $data["shiptocontact"]);
                $objResponse->addAssign("shiptoemail",		"innerHTML", $maillink);
                return $objResponse;	
	}
	require("firmacommon.php");
	$xajax->processRequests();


?>
