<?php
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/FirmenLib.php");	
	include("inc/wvLib.php");	
	$mid=($_GET["mid"])?$_GET["mid"]:$_POST["mid"];
	if ($_POST["ok"]) {
		if ($_POST["cause"]) {
			$rc=saveRAuftrag($_POST);
			if ($rc) { $rep=$rc; $msg="Auftrag gesichert"; }
			else { $rep=$_POST; $msg="Fehler beim Sichern"; };
			$mid=$rep["mid"];
		} else {
			$mid=$_POST["mid"];
		}
	} else 	if ($_GET["hole"]) {
		$rep=getRAuftrag($_GET["hole"]);
		if (!$rep) $msg="Nicht gefunden";
		$mid=$rep["mid"];
	} else {
		$rep=$_POST;
	}
	$masch=getAllMaschine($mid);
	$kdnr=($rep["kdnr"])?$rep["kdnr"]:$masch["customer_id"];
	$firma=getFirmenStamm($kdnr);
	$hist=getHistory($mid);
	
	$t = new Template($base);
	$t->set_file(array("masch" => "repauftrag.tpl"));
	
	if (!$rep["datum"]) $rep["datum"]=date("d.m.Y");

	$t->set_block("masch","History","Block1");	
	if($hist) {
		//$hist=array_reverse($hist);
		$i=0;
		while ($zeile = array_shift($hist) and $i<4 ) {
			if ($zeile["art"]=="RepAuftr") {
				preg_match("/^([0-9]+)\|(.+)/",$zeile["beschreibung"],$treffer);
				$art="<a href='repauftrag.php?hole=".$treffer[1]."'>RepAuftr</a>";
				$beschr=$treffer[2];
			} else if ($zeile["art"]=="contsub") {
				$beschr=$zeile["beschreibung"];
				$vid=suchVertrag($beschr);
				$art="<a href='vertrag3.php?vid=".$vid[0]["cid"]."'>contsub</a>";
			} else {
				$art=$zeile["art"];
				$beschr=$zeile["beschreibung"];
			};
			$t->set_var(array(
				date   =>	db2date($zeile["datum"]),
				art   =>	$art,
				beschreibung =>	$beschr
			));
			$t->parse("Block1","History",true);
			$i++;
		}
	}		
	if (!$rep["aid"]) {
		$disp2="style='display:none'";
		$disp3=$disp2;
		$sel1="checked";
		$sel2=""; $sel3="";
	} else if ($rep["status"]==1) {
		$disp3="style='display:none'";
		$sel1="checked";
		$sel2=""; $sel3="";
	} else if ($rep["status"]==2) {
		$disp1="style='display:none'";
		$sel2="checked";
		$sel1=""; $sel3="";
	} else if ($rep["status"]==3) {
		$disp1="style='display:none'";
		$sel3="checked";
		$sel1=""; $sel2="";
	} 
	$t->set_var(array(
		action => "repauftrag.php",
		msg => $msg,
		AID => $rep["aid"],
		mid => $mid,
		name => $firma["name"],
		kdnr => $firma["id"],
		customernumber => $firma["customernumber"],
		strasse => $firma["street"],
		plz => $firma["zipcode"],
		ort => $firma["city"],
		telefon => $firma["phone"],
		standort => $masch["standort"],
		description => $masch["description"],
		serialnumber => $masch["serialnumber"],
		contractnumber => $masch["contractnumber"],
		cid => $masch["cid"],		
		schaden => $rep["schaden"],
		behebung => $rep["reparatur"],
		bearbdate => db2date(substr($rep["bearbdate"],0,10)),
		cause => $rep["cause"],
		counter => $rep["counter"],		
		datum => $rep["datum"],
		anlagedatum => db2date(substr($rep["anlagedatum"],0,10))." ".substr($rep["anlagedatum"],11,5),		
		sel1 => $sel1,
		sel2 => $sel2,
		sel3 => $sel3,
		disp1 => $disp1,
		disp2 => $disp2,
		disp3 => $disp3,
	));
	$t->pparse("out",array("masch"));

?>
