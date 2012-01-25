<?php
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/persLib.php");
	include("inc/crmLib.php");
	include("inc/FirmenLib.php");
	include("inc/wvLib.php");
	require("firmacommon".XajaxVer.".php");
	$fid=($_GET["fid"])?$_GET["fid"]:$_POST["fid"];
	$pid=($_GET["pid"])?$_GET["pid"]:$_POST["pid"];
	$Q=($_GET["Q"])?$_GET["Q"]:$_POST["Q"];
	if (!empty($fid)) {
		$fa=getFirmenStamm($fid,true,$Q);
		if (!empty($pid)){
			$id=$pid;
			$co=getKontaktStamm($pid);
			$name=$co["cp_givenname"]." ".$co["cp_name"];
			$plz=$co["cp_zipcode"];
			$ort=$co["cp_city"];
			$firma=$fa["name"];
		} else {
			$id=$fid;
			$name=$fa["name"];
			$plz=$fa["zipcode"];
			$ort=$fa["city"];
			$firma="Firmendokumente";
		}
		$vertrag=getCustContract($fid);
		$link1="firma1.php?Q=$Q&id=$fid";
		$link2="firma2.php?Q=$Q&fid=$fid";
		$link3="firma3.php?Q=$Q&fid=$fid";
		$link4="firma4.php?Q=$Q&fid=$fid&pid=$pid";
	} else {    
        $fid = 0;
		$co=getKontaktStamm($pid);
		$name=$co["cp_givenname"]." ".$co["cp_name"];
		$plz=$co["cp_zipcode"];
		$ort=$co["cp_city"];
		$firma="Einzelperson";
		$link1="#";
		$link2="firma2.php?Q=$Q&id=$pid";
		$link3="#";
		$link4="firma4.php?Q=$Q&pid=$pid&fid=0";
	}
	$t = new Template($base);
	$t->set_file(array("doc" => "firma4.tpl"));
	$t->set_var(array(
			AJAXJS  => $xajax->printJavascript(XajaxPath),
			FAART   => ($Q=="C")?".:Customer:.":".:Vendor:.",       //"Kunde":"Lieferant",
            		ERPCSS  => $_SESSION["stylesheet"],
			Q => $Q,
			FID => $fid,
			customernumber	=> ($Q=="C")?$fa["customernumber"]:$fa["vendornumber"],
			kdnr => $fa["nummer"],
			PID => $pid,
			Link1 => $link1,
			Link2 => $link2,
			Link3 => $link3,
			Link4 => $link4,
			Name => $name,
			Plz => $plz,
			Ort => $ort,
			Firma => $firma
			));
	$t->set_block("doc","Liste","Block");
	$user=getVorlagen();
	$i=0;
	if (!$user) $user[0]=array(docid=>0,vorlage=>"Keine Vorlagen eingestellt",applikation=>"O");
	if ($user) foreach($user as $zeile) {
		switch ($zeile['applikation']) {
                        case "O":
                                $format = "OOo";
                                break;
                        case "R":
                                $format = "RTF";
                                break;
                        case "B":
                                $format = "BIN";
                                break;
                }
		$t->set_var(array(
			LineCol	=> $bgcol[($i%2+1)],
			ID =>	$zeile["docid"],
			Bezeichnung =>	$zeile["vorlage"],
			Appl	=>	$format,
		));
		$i++;
		$t->parse("Block","Liste",true);
	}
	$t->set_block("doc","Vertrag","Block3");
	if ($vertrag) foreach ($vertrag as $row) {
		$t->set_var(array(
				vertrag => $row["contractnumber"],
				cid => $row["cid"]
		));
		$t->parse("Block3","Vertrag",true);
	}	
	$t->Lpparse("out",array("doc"),$_SESSION["lang"],"firma");

?>
