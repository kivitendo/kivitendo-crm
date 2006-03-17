<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/persLib.php");
	include("inc/LieferLib.php");
	if ($_POST["insk"]) {
		insFaKont($_POST);
		$fid=$_POST["fid"];
	}
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
			$link4="liefer4.php?pid=".$_GET["id"];
		} else {
			$fa=getLieferStamm($co["cp_cv_id"]);
			$link1="liefer1.php?id=".$co["cp_cv_id"];
			$link2="liefer2.php?fid=".$co["cp_cv_id"];
			$link3="liefer3.php?fid=".$co["cp_cv_id"];
			$link4="liefer4.php?pid=".$_GET["id"]."&fid=".$co["cp_cv_id"];
		}
		if ($co["cp_homepage"]<>"") {
			$internet=(preg_match("°://°",$co["cp_homepage"]))?$co["cp_homepage"]:"http://".$co["cp_homepage"];
		};
		$t = new Template($base);
		$t->set_file(array("co1" => "liefer2.tpl"));
		$t->set_var(array(
			Link1 => $link1,
			Link2 => $link2,
			Link3 => $link3,
			Link4 => $link4,
			none => "visible",
			Edit => "Edit",
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
			Fax => $co["cp_fax"],
			eMail => $co["cp_email"],
			www	=> $internet,
			Abteilung	=> $co["cp_abteilg"],
			Position	=> $co["cp_position"],
			GebDat	=> db2date($co["cp_gebdatum"]),
			Notiz	=> $co["cp_notes"],
			FaID => $co["cp_cv_id"],
			LInr => $fa["vendornumber"],
			Lname => $fa["name"],
			Ldepartment_1 => $fa["department_1"],
			Ldepartment_2 => $fa["department_2"],
			Plz => $fa["zipcode"],
			Ort => $fa["city"],
			Street => $fa["street"],
			PID => $_GET["id"],
			FID => $co["cp_cv_id"]
			));
			$t->set_block("co1","Liste","Block");
			$i=0;
			$nun=date("Y-m-d h:i");
			$itemN[]=array(id => 0,calldate => $nun, caller_id => $employee, cause => "Neuer Eintrag" );
			$items=getAllTelCall($_GET["id"],false);
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
		$t->pparse("out",array("co1"));
	} else {
		if ($_GET["fid"]) $fid=$_GET["fid"];
		if ($_POST["fid"]) $fid=$_POST["fid"];
		$co=getAllKontakt($fid);
		if (count($co)==1 && $co[0]["cp_id"]>1) { header("location: liefer2.php?id=".$co[0]["cp_id"]);};
		$t = new Template($base);
		$link1="liefer1.php?id=".$fid;
		$link2="liefer2.php?fid=".$fid;
		$link3="liefer3.php?fid=".$fid;
		$link4="liefer4.php?fid=".$fid;
		$fa=getLieferStamm($fid);
		if (count($co)>1) {
			$t->set_file(array("co1" => "liefer2L.tpl"));
			$t->set_var(array(
				Link1 => $link1,
				Link2 => $link2,
				Link3 => $link3,
				Link4 => $link4,
				Lname => $fa["name"],
				LInr => $fa["vendornumber"],
				Ldepartment_1 => $fa["department_1"],
				Ldepartment_2 => $fa["department_2"],
				Plz => $fa["zipcode"],
				Ort => $fa["city"],
				Street => $fa["street"],
				FID => $fid
			));
			$t->set_block("co1","Liste","Block");
			$i=0;
			if ($co) foreach($co as $col){
				$t->set_var(array(
					KID => $col["cp_id"],
					LineCol => $bgcol[($i%2+1)],
					Vname => $col["cp_givenname"],
					Nname => $col["cp_name"],
					Anrede => $col["cp_greeting"],
					Titel => $col["cp_title"],
					Tel => $col["cp_phone1"],
					eMail => $col["cp_email"]
					));
				$t->parse("Block","Liste",true);
				$i++;
			}
		} else {
			$t->set_file(array("co1" => "liefer2.tpl"));
			$t->set_var(array(
				Link1 => $link1,
				Link2 => $link2,
				Link3 => $link3,
				Link4 => $link4,
				none => "hidden",
				LInr => $fa["vendornumber"],
				Lname => $fa["name"],
				Ldepartment_1 => $fa["department_1"],
				Ldepartment_2 => $fa["department_2"],
				Plz => $fa["zipcode"],
				Ort => $fa["city"],
				Street => $fa["street"],
				FID => $fid,
				Edit => "",
				Anrede => "Leider keine Kontakte gefunden",
				Abteilung => "",
				Position => "",
				Titel => "",
				Vname => "",
				Nname => "",
				LandC => "",
				PlzC => "",
				OrtC => "",
				StreetC => "",
				GebDat => "",
				PID => "",
				Notiz => "",
				Telefon => "",
				Mobile => "",
				eMail => "",
				Fax => "",
				www => "",
				FID => $fid
			));
		}
		$t->pparse("out",array("co1"));
	}
?>
