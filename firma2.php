<?
// $Id: firma2.php,v 1.4 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/FirmaLib.php");
	include("inc/persLib.php");
	$fid=$_GET["fid"];
	$id=$_GET["id"];
	if ($_POST["insk"]) {
		insFaKont($_POST);
		$fid=$_POST["fid"];
	}
	if ($id) {
		$co=getKontaktStamm($id);
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
		} else {
			$fa["customernumber"]=$co["customernumber"];
			$fa["name"]=$co["Firma"];
			$fa["department_1"]=$co["Department_1"];
			$fa["department_2"]="";
			$fa["zipcode"]="";
			$fa["city"]="";
			$fa["id"]=0;
			$link1="firma1.php?id=".$co["cp_cv_id"];
			$link2="firma2.php?fid=".$co["cp_cv_id"];
			$link3="firma3.php?fid=".$co["cp_cv_id"];
			$link4="firma4.php?pid=$id&fid=".$co["cp_cv_id"];
			$ep="";
		}
		if (trim($co["cp_grafik"])<>"") {
			$Image="<img src='dokumente/".$_SESSION["mansel"]."/".$_GET["id"]."/kopf.".$co["cp_grafik"]."' ".$co["size"].">";
		} else {
			$Image="";
		}
		if ($co["cp_homepage"]<>"") {
			$internet=(preg_match("°://°",$co["cp_homepage"]))?$co["cp_homepage"]:"http://".$co["cp_homepage"];
		};
		$t = new Template($base);
		$t->set_file(array("co1" => "firma2.tpl"));
		$t->set_var(array(
			Link1 => $link1,
			Link2 => $link2,
			Link3 => $link3,
			Link4 => $link4,
			ep => $ep,
			Edit => "Bearbeiten",
			Anrede => $co["cp_greeting"],
			Titel => $co["cp_title"],
			Vname => $co["cp_givenname"],
			Nname => $co["cp_name"],
			LandC	=> $co["cp_country"],
			PlzC => $co["cp_zipcode"],
			OrtC => $co["cp_city"],
			StreetC => $co["cp_street"],
			Telefon => $co["cp_phone1"],
			Mobile => $co["cp_phone2"],
			eMail => $co["cp_email"],
			www	=> $internet,
			Abteilung	=> $co["cp_abteilung"],
			Position	=> $co["cp_position"],
			GDate => db2date($co["cp_gebdatum"]),
			Notiz => $co["cp_notes"],
			Fname1 => $fa["name"],
			Fdepartment_1 => $fa["department_1"],
			Fdepartment_2 => $fa["department_2"],
			Plz => $fa["zipcode"],
			Ort => $fa["city"],
			Street => $fa["street"],
			PID => $id,
			FID => $co["cp_cv_id"],
			KDNR	=> $fa["customernumber"],
			IMG		=> $Image
			));
			$t->set_block("co1","Liste","Block");
			$i=0;
			$nun=date("Y-m-d h:i");
			$itemN[]=array(id => 0,calldate => $nun, caller_id => $employee, cause => "Neuer Eintrag" );
			$items=getAllTelCall($id,false);
			if ($items) {
				$item=array_merge($itemN,$items);
			} else {
				$item=$itemN;
			}
			if ($item) foreach($item as $col){
				$t->set_var(array(
					IID => $col["id"],
					LineCol	=> $bgcol[($i%2+1)],
					Datum	=> db2date(substr($col["calldate"],0,10)),
					Zeit	=> substr($col["calldate"],11,5),
					Name	=> $col["cp_name"],
					Betreff	=> $col["cause"],
					Nr		=> $col["id"]
					));
				$t->parse("Block","Liste",true);
				$i++;
			}
		//$t->pparse("out",array("co1"));
		$t->Lpparse("out",array("co1"),$_SESSION["lang"],"firma");
	} else {
		if ($_GET["fid"]) $fid=$_GET["fid"];
		if ($_POST["fid"]) $fid=$_POST["fid"];
		$co=getAllKontakt($fid);
		if (count($co)==1 && $co[0]["cp_id"]>1) { header("location: firma2.php?id=".$co[0]["cp_id"]);}
		$link1="firma1.php?id=".$fid;
		$link2="firma2.php?fid=".$fid;
		$link3="firma3.php?fid=".$fid;
		$link4="firma4.php?fid=".$fid;
		$fa=getFirmaStamm($fid);
		$t = new Template($base);
		if (count($co)>1) {
			$t->set_file(array("co1" => "firma2L.tpl"));
			$t->set_var(array(
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
				FID => $fid
				));
			$t->set_block("co1","Liste","Block");
			$i=0;
			foreach($co as $col){
				$t->set_var(array(
					KID => $col["cp_id"],
					LineCol => $bgcol[($i%2+1)],
					Vname => $col["cp_givenname"],
					Nname => $col["cp_name"],
					Anrede => $col["cp_greeting"],
					Titel => $col["cp_titel"],
					Tel => $col["cp_phone1"],
					eMail => $col["cp_email"]
					));
				$t->parse("Block","Liste",true);
				$i++;
			}
		}  else {
			$t->set_file(array("co1" => "firma2.tpl"));
			$t->set_var(array(
				Link1 => $link1,
				Link2 => $link2,
				Link3 => $link3,
				Link4 => $link4,
				KDNR	=> $fa["customernumber"],
				Fname1 => $fa["name"],
				Fdepartment_1 => $fa["department_1"],
				Fdepartment_2 => $fa["department_2"],
				Plz => $fa["zipcode"],
				Ort => $fa["city"],
				Street => $fa["street"],
				FID => $fid,
				Anrede => "Leider keine Kontakte gefunden",
				Edit => "",
				Abteilung => "",
				Position => "",
				Titel => "",
				Vname => "",
				Nname => "",
				LandC => "",
				PlzC => "",
				OrtC => "",
				StreetC => "",
				GDate => "",
				PID => "",
				Notiz => "",
				Telefon => "",
				Mobile => "",
				eMail => "",
				www => "",
				FID => $fid
			));
		}
		//$t->pparse("out",array("co1"));
		$t->Lpparse("out",array("co1"),$_SESSION["lang"],"firma");
	}
?>
