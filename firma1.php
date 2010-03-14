<?php
	require("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/FirmenLib.php");
	include("inc/crmLib.php");
    require("firmacommon".XajaxVer.".php");
	$Q=($_GET["Q"])?$_GET["Q"]:$_POST["Q"];
	$kdhelp=getWCategorie(true);
	$id=$_GET["id"];
	$fa=getFirmenStamm($id,true,$Q);
	$start=($_GET["start"])?($_GET["start"]):0;
	$cmsg=getCustMsg($id);
	$tmp=getVariablen($id);
	$variablem="";
	$Vars="";
	if (count($tmp)>0) {
		$variablen=count($tmp)." Variablen";
		foreach ($tmp as $row) {
			$Vars.="<tr><td>".$row["description"]." </td><td>: ".$row["text_value"]."</td></tr>\n";
		}
	}
	$t = new Template($base);
	$t->set_file(array("fa1" => "firma1.tpl"));
	if ($fa["grafik"]) {
		if (file_exists("dokumente/".$_SESSION["mansel"]."/$Q".$fa["nummer"]."/logo.".$fa["grafik"])) {
		$Image="<a href='dokumente/".$_SESSION["mansel"]."/$Q".$fa["nummer"]."/logo.".$fa["grafik"]."' target='_blank'>";
		$Image.="<img src='dokumente/".$_SESSION["mansel"]."/$Q".$fa["nummer"]."/logo.".$fa["grafik"]."' ".$fa["icon"]." border='0'></a>";
		} else {
			$Image="Bild nicht<br>im Verzeichnis";
		}
	} else {
		$Image="";
	};
	if ($fa["homepage"]<>"") {
		$internet=(preg_match("^://^",$fa["homepage"]))?$fa["homepage"]:"http://".$fa["homepage"];
	};
	if ($fa["discount"]) {
		$rab=($fa["discount"]*100)."%";
	} else if($fa["typrabatt"]) {
		$rab=($fa["typrabatt"]*100)."%";
	} else {
		$rab="";
	}
	$sonder="";
    $cp_sonder=getSonder(False);
	if ($cp_sonder) foreach ($cp_sonder as $row) {
		$sonder.=($fa["sonder"] & $row["svalue"])?$row["skey"]." ":"";
	}
	$karte=str_replace(array("%TOSTREET%","%TOZIPCODE%","%TOCITY%"),array(strtr($fa["street"]," ",$planspace),$fa["zipcode"],$fa["city"]),$stadtplan);
	if (preg_match("/%FROM/",$karte)) {
		include "inc/UserLib.php";
		$user=getUserStamm($_SESSION["loginCRM"]);
		if ($user["addr1"]<>"" and $user["addr3"]<>"" and $user["addr2"]) {
			$karte=str_replace(array("%FROMSTREET%","%FROMZIPCODE%","%FROMCITY%"),array(strtr($user["addr1"]," ",$planspace),$user["addr2"],$user["addr3"]),$karte);
		} else {
			$karte="";
		};
	}
	$views=array(""=> "lie",1=>"lie",2=>"not",3=>"inf");
	$taxzone=array("Inland","EU mit UStId","EU ohne UStId","Ausland");
	$t->set_var(array(
			AJAXJS  	=> $xajax->printJavascript(XajaxPath),
			FAART 		=> ($Q=="C")?".:Customer:.":".:Vendor:.",
			CuVe 		=> ($Q=="C")?"customer":"vendor",
			Q 		=> $Q,
			FID		=> $id,
			INID		=> db2date(substr($fa["itime"],0,10)),
			interv		=> $_SESSION["interv"]*1000,
			Fname1		=> $fa["name"],
			kdnr		=> $fa["nummer"],
			kdtyp   	=> $fa["kdtyp"],
			lead		=> $fa["leadname"],
			Fdepartment_1	=> $fa["department_1"],
			Fdepartment_2	=> ($fa["department_2"])?$fa["department_2"]."<br />":"",
			Strasse 	=> $fa["street"],
			Land		=> $fa["country"],
			Bundesland	=> $fa["bundesland"],
			Plz		=> $fa["zipcode"],
			Ort		=> $fa["city"],
			GEODB		=> ($GEODB)?'1==1':'1>2',
			Telefon		=> $fa["phone"],
			Fax		=> $fa["fax"],
			Fcontact	=> $fa["contact"],
			eMail		=> $fa["email"],
			verkaeufer	=> $fa["verkaeufer"],
			branche 	=> $fa["branche"],
			sw 		=> $fa["sw"],
			sonder		=> $sonder,
			notiz 		=> nl2br($fa["notes"]),
			bank 		=> $fa["bank"],
			directdebit	=> ($fa["direct_debit"]=="t")?".:yes:.":".:no:.",
			blz 		=> $fa["bank_code"],
			konto 		=> $fa["account_number"],
            iban        => $fa["iban"],
            bic         => $fa["bic"],
			konzernname	=> $fa["konzernname"],
			konzernmember	=> ($fa["konzernmember"]>0)?"( ".$fa["konzernmember"]." )":"( * )",
			konzern		=> $fa["konzern"],
			Internet	=> $internet,
			USTID		=> $fa["ustid"],
			Steuerzone 	=> ($fa["taxzone_id"])?$taxzone[$fa["taxzone_id"]]:$taxzone[0],
			Taxnumber	=> $fa["taxnumber"],
			rabatt		=> $rab,
            headcount   => $fa["headcount"],
			//terms		=> $fa["terms"],
			terms		=> ($fa["terms_netto"])?$fa["terms_netto"]:"0",
			kreditlim	=> sprintf("%0.2f",$fa["creditlimit"]),
			op		=> ($fa["op"]>0)?sprintf("<span class='op'>%0.2f</span>",$fa["op"]):"0.00",
			oa		=> ($fa["oa"]>0)?sprintf("<span class='oa'>%0.2f</span>",$fa["oa"]):"0.00",
			preisgrp	=> $fa["pricegroup"],
			Sshipto_id	=> ($fa["shipto_id"]>0)?$fa["shipto_id"]:"",
			Sname1		=> $fa["shiptoname"],
			Sdepartment_1	=> $fa["shiptodepartment_1"],
			Sdepartment_2	=> $fa["shiptodepartment_2"],
			SStrasse	=> $fa["shiptostreet"],
			SLand		=> $fa["shiptocountry"],
			SBundesland	=> $fa["shiptobundesland"],
			SPlz		=> $fa["shiptozipcode"],
			SOrt		=> $fa["shiptocity"],
			STelefon 	=> $fa["shiptophone"],
			SFax		=> $fa["shiptofax"],
			SeMail		=> $fa["shiptoemail"],
			Scontact	=> $fa["shiptocontact"],
			Scnt 		=> $fa["shiptocnt"],
			Sids		=> $fa["shiptoids"],
			Cmsg		=> $cmsg,
			IMG		=> $Image,
			PAGER		=> $pager,
			NEXT		=> $next,
			PREV		=> $prev,
			KARTE		=> $karte,
			sales		=> ($Q=="C")?"sales":"purchase",
			request		=> ($Q=="C")?"sales":"request",
			apr		=> ($Q=="C")?"ar":"ap",
			zeigeplan 	=> ($karte)?"visible":"hidden",
			zeigeextra 	=> ($zeigeextra)?"visible":"hidden",
			tools    	=> ($tools)?"visible":"hidden",
			login		=> $_SESSION["employee"],
			password 	=> $_SESSION["password"],
			leadsrc 	=> $fa["leadsrc"],
			variablen 	=> $variablen,
			Vars	 	=> $Vars,
			erstellt	=> db2date($fa["itime"]),
			modify 		=> db2date($fa["mtime"]),
			kdview 		=> $views[$_SESSION["kdview"]],
			zeige 		=> ($fa["obsolete"]=="f")?"visible":"hidden",
			verstecke 	=> ($fa["obsolete"]=="t")?"visible":"hidden",
			chelp 		=> ($kdhelp)?"visible":"hidden",
			none		=> "visible"
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
	if ($kdhelp) { 
		$t->set_block("fa1","kdhelp","Block1");
		$kdtmp[]=array("id"=>-1,"name"=>"Online Kundenhilfe");
		$kdhelp=array_merge($kdtmp,$kdhelp); 
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

