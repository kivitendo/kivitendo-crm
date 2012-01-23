<?php
// $Id:  $
	require_once("inc/stdLib.php");
	include("template.inc");
	include("crmLib.php");
	include("UserLib.php");
	$t = new Template($base);
	$jscal ="<style type='text/css'>@import url(../js/jscalendar/calendar-win2k-1.css);</style>\n";
	$jscal.="<script type='text/javascript' src='../js/jscalendar/calendar.js'></script>\n";
	$jscal.="<script type='text/javascript' src='../js/jscalendar/lang/calendar-de.js'></script>\n";
	$jscal.="<script type='text/javascript' src='../js/jscalendar/calendar-setup.js'></script>\n";
	$jscal1="<script type='text/javascript'><!--\nCalendar.setup( {\n";
	$jscal1.="inputField : 'zieldatum',ifFormat :'%d.%m.%Y',align : 'BL', button : 'trigger1'} );\n";
	$jscal1.="//-->\n</script>";
	$stamm="none";
	$show = "visible";
	if ($_GET["Q"] and $_GET["fid"]) {
		$fid=$_GET["fid"];
		if ($_GET["new"]) {
			include_once("inc/FirmenLib.php");
			$daten["firma"]=getName($fid,$_GET["Q"]);
			$daten["fid"]=$fid;
			$daten["tab"]=$_GET["Q"];
		} else {
			$_POST["tab"]=$_GET["Q"];
			$_POST["fid"]=$fid;
			$_POST["action"] = "suchen";
		}
		$search="visible";
		$save="visible";
		$none="block";
		$stamm="block";
		$block="none";			
	} else if ($_GET["history"]) {
        	$history = true;
        	$show = "hidden";
        	$_POST["oppid"]=$_GET["history"];
    	}
	$oppstat=getOpportunityStatus();
	$salesman=getAllUser(array(0=>true,1=>"%"));
	if ($_POST["action"]=='suchen' || $history) {
		$data=suchOpportunity($_POST);
		$none="block";
		$block="none";
		if (count($data)>1){
			$t->set_file(array("op" => "opportunityL.tpl"));
			$t->set_block("op","Liste","Block");
            		$last = 0;
			foreach ($data as $row) {
				if ($last <> $row["oppid"] || $history) {
					$t->set_var(array(
					LineCol	=> $bgcol[($i%2+1)],
					id => $row["id"], 
					firma => ($last==$row["oppid"])?"":$row["firma"], 
					oppid => $row["oppid"],
					show => $show,
					title => $row["title"],
					chance => $row["chance"]*10, 
					betrag => sprintf("%0.2f",$row["betrag"]), 
					status => $row["statusname"],
					datum => db2date($row["zieldatum"]),
                                        user => $row["user"],
                                        chgdate => db2date($row["itime"])
					));
					$t->parse("Block","Liste",true);
					$i++;
				} 
				$last = $row["oppid"];
			}
			$stamm="block";
			$t->set_var(array(
				ERPCSS      => $_SESSION["stylesheet"],
			));
			$t->Lpparse("out",array("op"),$_SESSION["lang"],"work");
			exit;
		} else if (count($data)==0 || !$data){
			if ($_POST["fid"]) {
				include_once("inc/FirmenLib.php");
				$data["firma"]=getName($_POST["fid"],$_POST["tab"]);
			};
			$msg=".:notfound:.!";
			$daten["fid"]=$_POST["fid"];
			$daten["firma"]=$data["firma"];
			$daten["tab"]=$_POST["tab"];
			$search="visible";
			$save="visible";
			$none="block";
			$block="none";
			$stamm="none";
		} else {
			$daten=getOneOpportunity($data[0]["id"]);
			$save="visible";
			$search="hidden";
			$none="none";
			$block="block";
			$stamm="block";
		}
	} else if ($_POST["action"] == "save") {
		$rc=saveOpportunity($_POST);
		if (!$rc) { 
			$msg=".:error:. .:save:.";
			$daten=$_POST;
                        if ($_POST["id"]) {
                            $data = getOneOpportunity($_POST["id"]);	
			    $daten["orders"]=$data["orders"];
                        };
			$daten["zieldatum"]=date2db($daten["zieldatum"]);
			$save="visible";
			$search="hidden";
			$none="block";
			$block="none";
			$stamm="block";
		} else {
			$daten=getOneOpportunity($rc);
			$msg=".:datasave:.";
			$save="visible";
			$search="hidden";
			$none="none";
			$block="block";
			$stamm="block";
		}
	} else if ($_GET["id"]) {
                //Genau diesen einen Eintrag holen
		$daten=getOneOpportunity($_GET["id"]);
		$save="visible";
		$search="hidden";
		$none="none";
		$block="block";
		$stamm="block";
	} else {
		$save="visible";
		$search="visible";
		$none="block";
		$block="none";
	}
	$t->set_file(array("op" => "opportunityS.tpl"));
	$t->set_block("op","status","BlockS");
	if ($oppstat) foreach ($oppstat as $row) {
		$t->set_var(array(
			ssel => ($row["id"]==$daten["status"])?"selected":"",
			sval => $row["id"],
			sname => $row["statusname"]
		));
		$t->parse("BlockS","status",true);
	}
	$t->set_block("op","salesman","BlockV");
	if (!$daten["salesman"]) $daten["salesman"]=$_SESSION["loginCRM"];
	if ($salesman) foreach ($salesman as $row) {
		$t->set_var(array(
			esel => ($row["id"]==$daten["salesman"])?"selected":"",
			evals => $row["id"],
			ename => ($row["name"])?$row["name"]:$row["login"]
		));
		$t->parse("BlockV","salesman",true);
	}
	$t->set_block("op","auftrag","BlockA");
	if ($daten["orders"]) foreach ($daten["orders"] as $row) {
		$t->set_var(array(
			asel => ($row["id"]==$daten["auftrag"])?"selected":"",
			aval => $row["id"],
			aname => $row["ordnumber"]." : ".db2date($row["transdate"])
		));
		$t->parse("BlockA","auftrag",true);
	}
	if ($daten["fid"]) $backlink = "firma1.php?Q=".$daten["tab"]."&id=".$daten["fid"];
	$t->set_var(array(
		ERPCSS      => $_SESSION["stylesheet"],
		id => $daten["id"],
		oppid => $daten["oppid"],
		auftrag => ($daten["auftrag"]>0)?$daten["auftrag"]:"0",
		auftragshow => ($daten["auftrag"]>0)?"visible":"hidden",
		tab => $daten["tab"],
		fid => $daten["fid"],
		title => $daten["title"],
		firma => ($daten["firma"])?$daten["firma"]:$_POST["firma"],
		zieldatum => ($daten["zieldatum"])?db2date($daten["zieldatum"]):"",
		betrag => ($daten["betrag"])?sprintf("%0.2f",$daten["betrag"]):"",
		next => ($daten["next"])?$daten["next"]:$_POST["next"],
		notxt => ($daten["notiz"])?nl2br($daten["notiz"]):"---",
		notiz => $daten["notiz"],
                user => $daten["user"],
                chgdate => db2date($daten["itime"]),
		ssel.$daten["status"] => "selected",
		csel.$daten["chance"] => "selected",
		save => $save,
		search => $search,
		stamm => $stamm,
		block => $block,
		none => $none,
		button => $button,
		backlink => $backlink,
		blshow => ($backlink)?"visible":"hidden",
		msg => $msg,
		jcal0 => ($jcalendar)?$jscal:"",
		jcal2 => ($jcalendar)?$jscal1:"",
		//jcal1 => ($jcalendar)?"<input type='image' src='image/date.png' title='.:targetdate:. .:search:.' name='zieldatum' align='middle' id='trigger1' value='?'>":""
		jcal1 => ($jcalendar)?"<a href='#' id='trigger1' name='zieldatum' title='.:targetdate:. .:search:.' onClick='false'><img src='image/date.png' border='0' align='middle'></a>":""
	));
        $history = $daten=getOpportunityHistory($daten['oppid']);
        $i = 0;
        $t->set_block("op","Liste","Block");
        if ($history) foreach ($history as $row) {
		$t->set_var(array(
			nr	=> $i,
			LineCol => $bgcol[($i%2+1)],
			histtitle => $row["title"],
			histchance => $row["chance"]*10,
			histbetrag => sprintf("%0.2f",$row["betrag"]),
			histstatus => $row["statusname"],
			histdatum => db2date($row["zieldatum"]),
			histauftrag => $row["ordnumber"],
			histnext => $row["next"],
			histnotiz => strtr($row["notiz"],array("\n"=>"<br>")),
			user => $row["user"],
			chgdate => db2date($row["itime"])
		));
		$t->parse("Block","Liste",true);
		$i++;
	}

	$t->Lpparse("out",array("op"),$_SESSION["lang"],"work");
?>
