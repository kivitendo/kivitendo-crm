<?
// $Id$
	require("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/FirmaLib.php");
	include("inc/crmLib.php");
	$id=$_GET["id"];
	$fa=getFirmaStamm($id);
	$start=($_GET["start"])?($_GET["start"]):0;
	$items=getAllTelCall($_GET["id"],true,$start);
	$cmsg=getCustMsg($id);
	$t = new Template($base);
	$t->set_file(array("fa1" => "firma1.tpl"));
	if ($fa["grafik"]) {
		$Image="<img src='dokumente/".$_SESSION["mansel"]."/".$_GET["id"]."/logo.".$fa["grafik"]."' ".$fa["size"].">";
	} else {
		$Image="";
	};
	if ($fa["homepage"]<>"") {
		$internet=(preg_match("°://°",$fa["homepage"]))?$fa["homepage"]:"http://".$fa["homepage"];
	};
	mkPager($items,$pager,$start,$next,$prev);
	if ($fa["discount"]) {
		$rab=($fa["discount"]*100)."%";
	} else if($fa["typrabatt"]) {
		$rab=($fa["typrabatt"]*100)."%";
	} else {
		$rab="";
	}
	$t->set_var(array(
			FID	=> $id,
			INID	=> db2date(substr($fa["itime"],0,10)),
			Fname1	=> $fa["name"],
			KDNR	=> $fa["customernumber"],
			kdtyp   => $fa["kdtyp"],
			Fdepartment_1	=> $fa["department_1"],
			Fdepartment_2	=> $fa["department_2"],
			Strasse => $fa["street"],
			Land	=> $fa["country"],
			Plz	=> $fa["zipcode"],
			Ort	=> $fa["city"],
			Telefon	=> $fa["phone"],
			Fax	=> $fa["fax"],
			Fcontact=> $fa["contact"],
			eMail	=> $fa["email"],
			branche => $fa["branche"],
			sw => $fa["sw"],
			notiz =>  $fa["notes"],
			bank => $fa["bank"],
			blz => $fa["bank_code"],
			konto => $fa["account_number"],
			Internet	=> $internet,
			USTID	=> $fa["ustid"],
			Taxnumber	=> $fa["taxnumber"],
			rabatt	=> $rab,
			terms	=> $fa["terms"],
			kreditlim	=> sprintf("%0.2f",$fa["creditlimit"]),
			op	=> sprintf("%0.2f",$fa["op"]),
			oa	=> sprintf("%0.2f",$fa["oa"]),
			preisgrp	=> $fa["pricegroup"],
			Sname1	=> $fa["shiptoname"],
			Sdepartment_1	=> $fa["shiptodepartment_1"],
			Sdepartment_2	=> $fa["shiptodepartment_2"],
			SStrasse	=> $fa["shiptostreet"],
			SLand	=> $fa["shiptocountry"],
			SPlz	=> $fa["shiptozipcode"],
			SOrt	=> $fa["shiptocity"],
			STelefon => $fa["shiptophone"],
			SFax	=> $fa["shiptofax"],
			SeMail	=> $fa["shiptoemail"],
			Cmsg	=> $cmsg,
			IMG	=> $Image,
			PAGER	=> $pager,
			NEXT	=> $next,
			PREV	=> $prev,
			login	=> $_SESSION["employee"],
			password	=> $_SESSION["password"]
			));
		$t->set_block("fa1","Liste","Block");
		$i=0;
		$nun=date("Y-m-d H:i");
		$itemN[]=array(id => 0,calldate => $nun, caller_id => $employee, cause => "Neuer Eintrag" );
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
	//$t->pparse("out",array("fa1"));
	$t->Lpparse("out",array("fa1"),$_SESSION["lang"],"firma");
	
?>

