<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/FirmenLib.php");
	include("inc/persLib.php");
	require("firmacommon.php");
	$fid=($_GET["fid"])?$_GET["fid"]:$_POST["fid"];
	$Q=($_GET["Q"])?$_GET["Q"]:$_POST["Q"];	
	$kdhelp=getWCategorie(true);
	if ($_POST["insk"]) {
		insFaKont($_POST);
	}
	if ($_GET["ldap"]) {
		include("inc/ldapLib.php");
		$rc=Ldap_add_Customer($_GET["fid"]);
	}

	// Einen Kontakt anzeigen lassen
	if ($_GET["id"]) {
		$co=getKontaktStamm($_GET["id"]);
		if (empty($co["cp_cv_id"])) {
			// Ist keiner Firma zugeordnet
			$id=$_GET["id"];
			$fa["name"]="Einzelperson";
			$fa["department_1"]="";
			$fa["department_2"]="";
			$fa["zipcode"]="";
			$fa["city"]="";
			$fa["id"]=0;
			$link1="#";
			$link2="#";
			$link3="#";
			$link4="firma4.php?pid=$id";
			$ep="&ep=1";
			$init="";
		} else {
			$id=$_GET["id"];
			$fid=$co["cp_cv_id"];
			$fa["id"]=0;
			$ep="";
		}
	} 
	if ($fid>0){ 
		// Aufruf mit einer Firmen-ID
		$co=getAllKontakt($fid);		
		$liste="";
		if (count($co)>1) {
			// Mehr als einen Kontakt gefunden
			foreach ($co as $row) {
				$liste.="<option value='".$row["cp_id"];
				$liste.=($row["cp_id"]==$id)?"' selected>":"'>";
				$liste.=$row["cp_name"].", ".$row["cp_givenname"]."\n";
			}
			$co=$co[0];
			$init=$co["cp_id"];
			$id=$co["cp_id"];
		} else if (count($co)==0 || $co==false) {
			// Keinen Kontakt gefunden
			$co["cp_greeting"]="Leider keine Kontakte gefunden";
			$init="";
		} else {
			// Genau ein Kontakt
			$co=$co[0]; 
			$id=$co["cp_id"];
		}
		$fa=getFirmenStamm($fid,true,$Q);
		$KDNR=($Q=="C")?$fa["customernumber"]:$fa["vendornumber"];
		$link1="firma1.php?Q=$Q&id=$fid";
		$link2="firma2.php?Q=$Q&fid=$fid";
		$link3="firma3.php?Q=$Q&fid=$fid";
		$link4="firma4.php?Q=$Q&fid=$fid&pid=".$co["cp_id"];
	} else if ($ep=="") {
		$co["cp_greeting"]="Fehlerhafter Aufruf";
		$init="";
		$link1="#";
		$link2="#";
		$link3="#";
		$link4="#";
	}
	if (trim($co["cp_grafik"])<>"") {
		$Image="<img src='dokumente/".$_SESSION["mansel"]."/$Q$KDNR/".$_GET["id"]."/kopf.".$co["cp_grafik"]."' ".$co["size"].">";
	} else {
		$Image="";
	}
	if ($co["cp_homepage"]<>"") {
		$internet=(preg_match("^://^",$co["cp_homepage"]))?$co["cp_homepage"]:"http://".$co["cp_homepage"];
	};
	$sonder="";
	if ($cp_sonder) while (list($key,$val) = each($cp_sonder)) {
		$sonder.=($co["cp_sonder"] & $key)?"($val) ":"";
	}
	$t = new Template($base);
	$t->set_file(array("co1" => "firma2.tpl"));
	$t->set_var(array(
			INIT	=> ($init=="")?"showOne($id)":"showContact()",
			AJAXJS  => $xajax->printJavascript('/xajax/'),
			FAART => ($Q=="C")?"Customer":"Vendor",   //"Kunde":"Lieferant",
			interv	=> $_SESSION["interv"]*1000,
			Q => $Q,
			Link1 => $link1,
			Link2 => $link2,
			Link3 => $link3,
			Link4 => $link4,
			Fname1 => $fa["name"],
			Fdepartment_1 => $fa["department_1"],
			Fdepartment_2 => $fa["department_2"],
			Plz => $fa["zipcode"],
			Ort => $fa["city"],
			Street => $fa["street"],
			PID => $co["cp_id"],
			FID => $co["cp_cv_id"],
			customernumber	=> $KDNR,
			moreC => ($liste<>"")?"visible":"hidden",
			kontakte => $liste,
			ep => $ep,
			Edit => "edit",
			none => ($ep=="" && $init=="")?"hidden":"visible",
			chelp 		=> ($kdhelp)?"visible":"hidden"
	));
	if ($kdhelp) { 
		$t->set_block("co1","kdhelp","Block1");
		$tmp[]=array("id"=>-1,"name"=>"Online Kundenhilfe");
		$kdhelp=array_merge($tmp,$kdhelp); 
		foreach($kdhelp as $col) {
			$t->set_var(array(
				cid => $col["id"],
				cname => $col["name"]
			));	
			$t->parse("Block1","kdhelp",true);
		};
	}
	$t->Lpparse("out",array("co1"),$_SESSION["lang"],"firma");
?>
