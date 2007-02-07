<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/FirmenLib.php");
	include("inc/persLib.php");
	require("firmacommon.php");
	if ($_POST["insk"]) {
		insFaKont($_POST);
		$fid=$_POST["fid"];
	}
	if ($_GET["ldap"]) {
		include("inc/ldapLib.php");
		$rc=Ldap_add_Customer($_GET["fid"]);
	}
	$fid=$_GET["fid"];
	if ($_GET["id"]) {
		$co=getKontaktStamm($_GET["id"]);
		if (empty($co["cp_cv_id"])) {
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
		$fa=getAllKontakt($fid);		
		$liste="";
		if (count($fa)>1) {
			foreach ($fa as $row) {
				$liste.="<option value='".$row["cp_id"];
				$liste.=($row["cp_id"]==$id)?"' selected>":"'>";
				$liste.=$row["cp_name"].", ".$row["cp_givenname"]."\n";
			}
			$co=$fa[0];
			$init=$co["cp_id"];
		} else if (count($fa)==0 || $fa==false) {
			$co["cp_greeting"]="Leider keine Kontakte gefunden";
			$co=$fa[0];
			$init=$co["cp_id"];
			$init="";
		}
		$fa=getFirmenStamm($fid);
		$link1="firma1.php?id=".$co["cp_cv_id"];
		$link2="firma2.php?fid=".$co["cp_cv_id"];
		$link3="firma3.php?fid=".$co["cp_cv_id"];
		$link4="firma4.php?pid=".$co["cp_id"]."&fid=".$co["cp_cv_id"];
	} else if ($ep=="") {
		$co["cp_greeting"]="Leider keine Kontakte gefunden";
		$init="";
		
	}
	if (trim($co["cp_grafik"])<>"") {
		$Image="<img src='dokumente/".$_SESSION["mansel"]."/".$_GET["id"]."/kopf.".$co["cp_grafik"]."' ".$co["size"].">";
	} else {
		$Image="";
	}
	if ($co["cp_homepage"]<>"") {
		$internet=(preg_match("°://°",$co["cp_homepage"]))?$co["cp_homepage"]:"http://".$co["cp_homepage"];
	};
	$sonder="";
	if ($cp_sonder) while (list($key,$val) = each($cp_sonder)) {
		$sonder.=($co["cp_sonder"] & $key)?"($val) ":"";
	}
	$t = new Template($base);
	$t->set_file(array("co1" => "firma2.tpl"));
	$t->set_var(array(
			INIT	=> ($init=="")?"":"showContact()",
			AJAXJS  => $xajax->printJavascript('./xajax/'),
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
			customernumber	=> $fa["customernumber"],
			moreC => ($liste<>"")?"visible":"hidden",
			kontakte => $liste,
			ep => $ep,
			Edit => "Bearbeiten",
			none => ($ep=="" && $init=="")?"hidden":"visible",
	));
	$t->Lpparse("out",array("co1"),$_SESSION["lang"],"firma");
?>
