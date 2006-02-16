<?php
// $Id: liefer1.php,v 1.4 2005/11/02 10:37:52 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/LieferLib.php");
	include("inc/crmLib.php");
	$li=getLieferStamm($_GET["id"]);
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
	$t->set_var(array(
			LInr	=> $li["vendornumber"],
			KDnr	=> $li["v_customer_id"],
			INID	=> db2date(substr($li["itime"],0,10)),
			lityp   => $fa["lityp"],
			Lname	=> $li["name"],
			Ldepartment_1 => $li["department_1"],
			Ldepartment_2 => $li["department_2"],
			Strasse	=> $li["street"],
			Land	=> $li["country"],
			Plz	=> $li["zipcode"],
			Ort	=> $li["city"],
			Telefon	=> $li["phone"],
			Fax	=> $li["fax"],
			eMail	=> $li["email"],
			Internet => $internet,
			FID	=> $_GET["id"],
			USTID	=> $li["taxnumber"],
			rabatt	=> ($fa["discount"])?($li["discount"]*100)."%":"",
			Sname	=> $li["shiptoname"],
			Sdepartment_1 => $li["shiptodepartment_1"],
			Sdepartment_2 => $li["shiptodepartment_2"],
			SStrasse => $li["shiptostreet"],
			SLand	=> $li["shiptocountry"],
			SPlz	=> $li["shiptozipcode"],
			SOrt	=> $li["shiptocity"],
			STelefon => $li["shiptophone"],
			SFax	=> $li["shiptofax"],
			SeMail	=> $li["shiptoemail"],
			IMG	=> $Image,
			PAGER	=> $pager,
			NEXT	=> $next,
			PREV	=> $prev	
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
			$t->set_var(array(
				IID => $col["id"],
				LineCol	=> $bgcol[($i%2+1)],
				Datum	=> db2date(substr($col["calldate"],0,10)),
				Zeit	=> substr($col["calldate"],11,5),
				Name	=> $col["cp_name"],
				Betreff	=> $col["cause"],
				Nr	=> $col["id"]
				));
			$t->parse("Block","Liste",true);
			$i++;
		}
	$t->pparse("out",array("Li1"));
?>
