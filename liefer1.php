<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/FirmenLib.php");
	include("inc/crmLib.php");
	$kdhelp=getWCategorie(true);
	$li=getFirmenStamm($_GET["id"],true,"V");
	$start=($_GET["start"])?($_GET["start"]):0;
	$items=getAllTelCall($_GET["id"],true,$start);
	$t = new Template($base);
	$t->set_file(array("Li1" => "liefer1.tpl"));
	if ($li["grafik"]) {
		$Image="<img src='dokumente/".$_SESSION["mansel"]."/".$_GET["id"]."/logo.".$li["grafik"]."' ".$li["size"].">";
	} else {
		$Image="";
	}
	if ($li["homepage"]<>"") {
		$internet=(preg_match("°://°",$li["homepage"]))?$li["homepage"]:"http://".$li["homepage"];
	};
	mkPager($items,$pager,$start,$next,$prev); 
	if ($li["discount"]) {
		$rab=($li["discount"]*100)."%";
	} else if($li["typrabatt"]) {
		$rab=($li["typrabatt"]*100)."%";
	} else {
		$rab="";
	}
	$sonder="";
	if ($cp_sonder) while (list($key,$val) = each($cp_sonder)) {
		$sonder.=($li["sonder"] & $key)?"$val ":"";
	}
	$views=array(""=> "lie",1=>"lie",2=>"not",3=>"inf");
	$taxzone=array("Inland","EU mit UStId","EU ohne UStId","Ausland");
	$t->set_var(array(
			LInr		=> $li["vendornumber"],
			KDnr		=> $li["v_customer_id"],
			INID		=> db2date(substr($li["itime"],0,10)),
			lityp   	=> $li["lityp"],
			Lname		=> $li["name"],
			Ldepartment_1 	=> $li["department_1"],
			Ldepartment_2	=> ($li["department_2"])?$li["department_2"]."<br />":"",
			Strasse		=> $li["street"],
			Land		=> $li["country"],
			Bundesland	=> $li["bundesland"],
			Plz		=> $li["zipcode"],
			Ort		=> $li["city"],
			Telefon		=> $li["phone"],
			Fax		=> $li["fax"],
			eMail		=> $li["email"],
			branche 	=> $li["branche"],
			sw	 	=> $li["sw"],
			op	 	=> sprintf("%0.2f",$li["op"]),
			Internet	=> $internet,
			FID		=> $_GET["id"],
			sonder		=> $sonder,
			notes		=> $li["notes"],
			ustid 		=> $li["ustid"],
			Steuerzone 	=> ($li["taxzone_id"])?$taxzone[$li["taxzone_id"]]:$taxzone[0],
			taxnumber 	=> $li["taxnumber"],
			bank		=> $li["bank"],
			bank_code	=> $li["bank_code"],
			account_number	=> $li["account_number"],
			terms		=> $li["terms"],
			kreditlim	=> sprintf("%0.2f",$li["creditlimit"]),	
			rabatt		=> $rab,
			Sname		=> $li["shiptoname"],
			Sdepartment_1 	=> $li["shiptodepartment_1"],
			Sdepartment_2 	=> $li["shiptodepartment_2"],
			SStrasse 	=> $li["shiptostreet"],
			SLand		=> $li["shiptocountry"],
			SBundesland	=> $li["shiptobundesland"],
			SPlz		=> $li["shiptozipcode"],
			SOrt		=> $li["shiptocity"],
			STelefon	=> $li["shiptophone"],
			SFax		=> $li["shiptofax"],
			SeMail		=> $li["shiptoemail"],
			IMG		=> $Image,
			PAGER		=> $pager,
			NEXT		=> $next,
			PREV		=> $prev,
			kdview => $views[$_SESSION["kdview"]],
			verstecke => ($li["obsolete"]=="t")?"visible":"hidden",
			chelp => ($kdhelp)?"visible":"hidden"
			));
		$t->set_block("Li1","Liste","Block");
		$i=0;
		$nun=date("Y-m-d h:i");
		$itemN[]=array(id => 0,calldate => $nun, caller_id => $employee, cause => "Neuer Eintrag" );
		if ($items) {
			$item=array_merge($itemN,$items);
		} else {
			$item=$itemN;
		}
		if ($item) foreach($item as $col){
			if ($col["new"]) { $cause="<b>".$col["cause"]."</b>"; }
			else { $cause=$col["cause"]; }
			$t->set_var(array(
				IID => $col["id"],
				LineCol	=> $bgcol[($i%2+1)],
				Datum	=> db2date(substr($col["calldate"],0,10)),
				Zeit	=> substr($col["calldate"],11,5),
				Name	=> $col["cp_name"],
				Betreff	=> $cause,
				Nr	=> $col["id"]
				));
			$t->parse("Block","Liste",true);
			$i++;
		}
	if ($kdhelp) { 
		$t->set_block("Li1","kdhelp","Block1");
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
	$t->pparse("out",array("Li1"));
?>
