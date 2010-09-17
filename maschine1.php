<?php
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/wvLib.php");	
	$t = new Template($base);
	$disp="style='display:none'";
	if ($_POST["search"] or $_GET["sernr"]) {
		if ($_POST["serialnumber"]) {
			$tmp=explode("|",$_POST["serialnumber"]);
			$data=getSernumber($tmp[0],$tmp[1]);
		} else if ($_GET["sernr"]) {
			$data=getSernumber($_GET["sernr"]);
		} else {
			$data=getArtnumber($_POST["partnumber"]."%");
		}
		if (count($data)>1) {
			$t->set_file(array("vert" => "maschinenL.tpl"));		
			$t->set_block("vert","Sernumber","Block1");
			foreach($data as $zeile) {	
				$t->set_var(array(
					action => "maschine1.php",
					fldname => "serialnumber",
					number => $zeile["serialnumber"]."|".$zeile["parts_id"],
					description	=>	$zeile["serialnumber"]." - ".$zeile["description"]
				));
				$t->parse("Block1","Sernumber",true);
			}
	        $t->set_var(array(
                ERPCSS      => $_SESSION["stylesheet"],
            ));
			$t->pparse("out",array("vert"));			
			exit;
		} else if (!$data) {
			$data["serialnumber"]="";
			$data["description"]="Nicht gefunden";
		} else {
			$data=$data[0];
			$hist=getHistory(($data["mid"])?$data["mid"]:$data["id"]);
			if ($data["contractnumber"]) { $disp=""; };
		};
	} else if ($_POST["ort"] && $_POST["mid"]) {
		$rc=saveNewStandort($_POST["standort"],$_POST["mid"]);
		$data=getSernumber($_POST["serialnumber"]);
		$data=$data[0];
		$hist=getHistory($data["mid"]);
		$disp="";
	} else if ($_POST["cnt"] && $_POST["mid"]) {
		$rc=updateCounter($_POST["counter"],$_POST["mid"]);
		$data=getSernumber($_POST["serialnumber"]);
		$data=$data[0];
		$hist=getHistory($data["mid"]);
		$disp="";
	} else if ($_POST["idat"] && $_POST["mid"]) {
		$rc=updateIdat($_POST["inspdatum"],$_POST["mid"]);
		$data=getSernumber($_POST["serialnumber"]);
		$data=$data[0];
		$hist=getHistory($data["mid"]);
		$disp="";
	}
	$cnt=($data["mid"])?getCounter($data["mid"]):"";
	$t->set_file(array("masch" => "maschinen1.tpl"));
	$t->set_var(array(
        ERPCSS      => $_SESSION["stylesheet"],
		action => "maschine1.php",
		msg => $msg,
		disp => $disp,
		parts_id	=> $data["id"],
		partnumber 	=> $data["partnumber"],
		description	=>	$data["description"],
		notes 	=> $data["notes"],
		standort => $data["standort"],
		serialnumber => $data["serialnumber"],
		contractnumber => $data["contractnumber"],
		inspdatum => db2date($data["inspdatum"]),
		counter 	=> $cnt,
		cid => $data["cid"],
		mid => $data["mid"],						
		customer => $data["name"],
		custid => $data["customer_id"]
	));
	$t->set_block("masch","History","Block1");	
	if($hist) foreach($hist as $zeile) {
		if ($zeile["art"]=="RepAuftr") {
				preg_match("/^([0-9]+)\|(.+)/",$zeile["beschreibung"],$treffer);
				$art="<a href='repauftrag.php?hole=".$treffer[1]."'>RepAuftr</a>";
				$beschr=$treffer[2];
		} else if ($zeile["art"]=="contsub" or $zeile["art"]=="contadd") {
			$beschr=$zeile["beschreibung"];
			$vid=suchVertrag($beschr);
			$art="<a href='vertrag3.php?vid=".$vid[0]["cid"]."'>".$zeile["art"]."</a>";
		} else {
				if ($zeile["art"]=="neu") $maschzusatz=$zeile["beschreibung"];
				$art=$zeile["art"];
				$beschr=substr($zeile["beschreibung"],0,40);
		};
		$t->set_var(array(
			date   =>	db2date($zeile["datum"]),
			art   =>	$art,
			beschreibung =>	$beschr
		));
		$t->parse("Block1","History",true);
	}	
	$t->set_var(array(
		maschzusatz => $maschzusatz
	));
	$t->pparse("out",array("masch"));

?>
