<?
// $Id$
	require("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/FirmenLib.php");
	include("inc/crmLib.php");
	$kdhelp=getWCategorie(true);
	$id=$_GET["id"];
	$fa=getFirmenStamm($id);
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
	$sonder="";
	if ($cp_sonder) while (list($key,$val) = each($cp_sonder)) {
		$sonder.=($fa["sonder"] & $key)?"$val ":"";
	}
	$karte=str_replace(array("%TOSTREET%","%TOZIPCODE%","%TOCITY%"),array(strtr($fa["street"]," ",$planspace),$fa["zipcode"],$fa["city"]),$stadtplan);
	if (preg_match("/%FROM/",$karte)) {
		include "inc/UserLib.php";
		$user=getUserStamm($_SESSION["loginCRM"]);
		if ($user["Strasse"]<>"" and $user["Ort"]<>"" and $user["Plz"]) {
			$karte=str_replace(array("%FROMSTREET%","%FROMZIPCODE%","%FROMCITY%"),array(strtr($user["Strasse"]," ",$planspace),$user["Plz"],$user["Ort"]),$karte);
		} else {
			$karte="";
		};
	}
	$views=array(""=> "lie",1=>"lie",2=>"not",3=>"inf");
	$taxzone=array("Inland","EU mit UStId","EU ohne UStId","Ausland");
	$t->set_var(array(
			FID	=> $id,
			INID	=> db2date(substr($fa["itime"],0,10)),
			Fname1	=> $fa["name"],
			customernumber	=> $fa["customernumber"],
			kdtyp   => $fa["kdtyp"],
			lead	=> $fa["leadname"],
			Fdepartment_1	=> $fa["department_1"],
			Fdepartment_2	=> ($fa["department_2"])?$fa["department_2"]."<br />":"",
			Strasse => $fa["street"],
			Land	=> $fa["country"],
			Bundesland	=> $fa["bundesland"],
			Plz	=> $fa["zipcode"],
			Ort	=> $fa["city"],
			Telefon	=> $fa["phone"],
			Fax	=> $fa["fax"],
			Fcontact=> $fa["contact"],
			eMail	=> $fa["email"],
			branche => $fa["branche"],
			sw => $fa["sw"],
			sonder	=> $sonder,
			notiz =>  $fa["notes"],
			bank => $fa["bank"],
			blz => $fa["bank_code"],
			konto => $fa["account_number"],
			Internet	=> $internet,
			USTID	=> $fa["ustid"],
			Steuerzone => $taxzone[$fa["taxzone_id"]],
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
			SBundesland	=> $fa["shiptobundesland"],
			SPlz	=> $fa["shiptozipcode"],
			SOrt	=> $fa["shiptocity"],
			STelefon => $fa["shiptophone"],
			SFax	=> $fa["shiptofax"],
			SeMail	=> $fa["shiptoemail"],
			Scontact=> $fa["shiptocontact"],
			Scnt 	=> $fa["shiptocnt"],
			Cmsg	=> $cmsg,
			IMG	=> $Image,
			PAGER	=> $pager,
			NEXT	=> $next,
			PREV	=> $prev,
			KARTE	=> $karte,
			zeigeplan => ($karte)?"visible":"hidden",
			login	=> $_SESSION["employee"],
			password	=> $_SESSION["password"],
			leadsrc => $fa["leadsrc"],
			erstellt => db2date($fa["itime"]),
			modify => db2date($fa["mtime"]),
			kdview => $views[$_SESSION["kdview"]],
			zeige => ($fa["obsolete"]=="f")?"visible":"hidden",
			verstecke => ($fa["obsolete"]=="t")?"visible":"hidden",
			chelp => ($kdhelp)?"visible":"hidden"
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
		if ($col["new"]) { $cause="<b>".$col["cause"]."</b>"; }
		else { $cause=$col["cause"]; }
		$t->set_var(array(
			Nr	=> $col["id"],
			LineCol	=> $bgcol[($i%2+1)],
			Datum	=> db2date(substr($col["calldate"],0,10)),
			Zeit	=> substr($col["calldate"],11,5),
			Name	=> $col["cp_name"],
			Betreff	=> $cause,
		));
		$t->parse("Block","Liste",true);
		$i++;
	}
	if ($kdhelp) { 
		$t->set_block("fa1","kdhelp","Block1");
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
	$t->Lpparse("out",array("fa1"),$_SESSION["lang"],"firma");
	
?>

