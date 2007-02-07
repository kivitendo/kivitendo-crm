<?
	require_once("../inc/stdLib.php");
	include("FirmenLib.php");
	include("persLib.php");
	include("crmLib.php");
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
		$htmllink="<a href='".$data["shiptohomepage"]."' target='_blank'>".$data["shiptohomepage"]."</a>";
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
                $objResponse->addAssign("shiptohomepage",	"innerHTML", $htmllink);
                return $objResponse;	
	}
	function showContactadress($id){
		global $cp_sonder;
		$data=getKontaktStamm($id,".");
		if (preg_match("/UTF-8/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="UTF-8"; }
		else if (preg_match("/ISO-8859-15/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-15"; }
		else if (preg_match("/ISO-8859-1/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-1"; }
		else { $charset="ISO-8859-1"; };
		if ($data["cp_grafik"]) {
			$img="<img src='dokumente/".$_SESSION["mansel"]."/".$data["cp_id"]."/kopf.".$data["cp_grafik"]."' ".$data["icon"]." border='0'>";
			$data["cp_grafik"]="<a href='dokumente/".$_SESSION["mansel"]."/".$data["cp_id"]."/kopf.".$data["cp_grafik"]."' target='_blank'>$img</a>";
		};
		$data["cp_email"]="<a href='mail.php?TO=".$data["cp_email"]."&KontaktTO=P".$data["cp_id"]."'>".$data["cp_email"]."</a>";
		$data["cp_homepage"]="<a href='".$data["cp_homepage"]."' target='_blank'>".$data["cp_homepage"]."</a>";
		if (strpos($data["cp_birthday"],"-")) {
			$data["cp_birthday"]=db2date($data["cp_birthday"]);
		}
		$sonder="";
		if ($cp_sonder)	{ 
			while (list($key,$val) = each($cp_sonder)) {
				$sonder.=($data["cp_sonder"] & $key)?"$val ":"";
			}
			$data["cp_sonder"]=$sonder;
		}
		if ($data["cp_phone2"]) $data["cp_phone2"]="(".$data["cp_phone2"].")";
		if ($data["cp_privatphone"]) $data["cp_privatphone"]="Privat: ".$data["cp_privatphone"];
		if ($data["cp_mobile2"]) $data["cp_mobile2"]="(".$data["cp_mobile2"].")";
		if ($data["cp_privatemail"]) $data["cp_privatemail"]="Privat: <a href='mail.php?TO=".$data["cp_privatemail"]."&KontaktTO=P".$data["cp_id"]."'>".$data["cp_privatemail"]."</a>";;
		$nocodec = array("cp_email","cp_homepage","cp_zipcode","cp_birthday","cp_grafik","cp_privatemail");
		$objResponse = new xajaxResponse();
		foreach ($data as $key=>$val) {
			if (in_array($key,$nocodec)) {
                		$objResponse->addAssign($key,            "innerHTML", $val);
			} else {
                		$objResponse->addAssign($key,            "innerHTML", htmlentities($val));
			}
		}
		$objResponse->addAssign("cp_id", 	"value", $data["cp_id"]);
                return $objResponse;	
	}
	function showCalls($id,$start) {
		$i=0;
		$nun=date("Y-m-d h:i");
		$itemN[]=array(id => 0,calldate => $nun, caller_id => $employee, cause => "Neuer Eintrag" );
		$zeile ="<tr class='calls%d' onClick='showItem(%d);'>";
		$zeile.="<td class='calls' nowrap width='15%%'>%s %s</td>";
		$zeile.="<td class='calls re' width='5%%'>%s&nbsp;</td>";
		$zeile.="<td class='calls le' width='55%%'>%s</td>";
		$zeile.="<td class='calls le' width='15%%'>%s</td></tr>\n";
		$items=getAllTelCall($id,false,$start);
		if ($items) {
			$item=array_merge($itemN,$items);
		} else {
			$item=$itemN;
		}
		$tmp="";
		if ($item) foreach($item as $col){
			if ($col["new"]) { $cause="<b>".htmlentities($col["cause"])."</b>"; }
			else { $cause=htmlentities($col["cause"]); }
			$tmp.=sprintf($zeile,$i,$col["id"],db2date(substr($col["calldate"],0,10)),substr($col["calldate"],11,5),
						$col["id"],$cause,htmlentities($col["cp_name"]));
			$i=($i==1)?0:1;
		}
		$objResponse = new xajaxResponse();
		$objResponse->addAssign("calls", 	"innerHTML", $tmp);
		if ($start==0) {
			$max=getAllTelCallMax($id,$firma);
			$objResponse->addScript("max = $max;");
		}
                return $objResponse;	
	}
	require("firmacommon.php");
	$xajax->processRequests();


?>
