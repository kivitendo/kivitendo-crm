<?php
// $Id:  $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	$t = new Template($base);
	$jscal ="<style type='text/css'>@import url(../../$ERPNAME/js/jscalendar/calendar-win2k-1.css);</style>\n";
	$jscal.="<script type='text/javascript' src='../../$ERPNAME/js/jscalendar/calendar.js'></script>\n";
        $jscal.="<script type='text/javascript' src='../../$ERPNAME/js/jscalendar/lang/calendar-de.js'></script>\n";
        $jscal.="<script type='text/javascript' src='../../$ERPNAME/js/jscalendar/calendar-setup.js'></script>\n";
        $jscal1="<script type='text/javascript'><!--\nCalendar.setup( {\n";
	$jscal1.="inputField : 'zieldatum',ifFormat :'%d.%m.%Y',align : 'BL', button : 'trigger1'} );\n";
        $jscal1.="//-->\n</script>";
	if ($_GET["fid"])  {
		$fid=$_GET["fid"];
		$_POST["fid"]=$fid;
		$_POST["suchen"]=1;
	}
	$oppstat=getOpportunityStatus();
	if ($_POST["suchen"]) {
		$data=suchOpportunity($_POST);
		$none="block";
		$block="none";
		if (count($data)>1){
			$t->set_file(array("op" => "opportunityL.tpl"));
			$t->set_block("op","Liste","Block");
			foreach ($data as $row) {
				$t->set_var(array(
					LineCol	=> $bgcol[($i%2+1)],
					id => $row["id"], 
					name => $row["firma"], 
					title => $row["title"],
					chance => $row["chance"]*10, 
					betrag => sprintf("%0.2f",$row["betrag"]), 
					status => $row["status"],
					datum => db2date($row["zieldatum"]),
				));
				$t->parse("Block","Liste",true);
				$i++;
			}
			$t->pparse("out",array("op"));
			exit;
		} else if (count($data)==0 || !$data){
			if ($_POST["fid"]) {
				include("inc/FirmenLib.php");
				$data["name"]=getName($_POST["fid"],"C");
			};
			$msg="Nichts gefunden!";
			$daten["fid"]=$_POST["fid"];
			$daten["firma"]=$data["name"];
			$search="visible";
			$save="visible";
		} else {
			$daten=$data[0];
			$save="visible";
			$search="hidden";
			$none="none";
			$block="block";
		}
	} else if ($_POST["save"]) {
		$rc=saveOpportunity($_POST);
		if (!$rc) { 
			$msg="Fehler beim Sichern";
			$daten=$_POST;
			$daten["zieldatum"]=date2db($daten["zieldatum"]);
			$save="visible";
			$search="hidden";
			$none="block";
			$block="none";
		} else {
			$daten=getOneOpportunity($rc);
			$msg="Daten gesichert";
			$save="visible";
			$search="hidden";
			$none="none";
			$block="block";
		}
	} else if ($_GET["id"]) {
		$daten=getOneOpportunity($_GET["id"]);
		$save="visible";
		$search="hidden";
		$none="none";
		$block="block";
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
	$t->set_var(array(
		id => $daten["id"],
		fid => $daten["fid"],
		title => $daten["title"],
		name => ($daten["firma"])?$daten["firma"]:$_POST["firma"],
		zieldatum => ($daten["zieldatum"])?db2date($daten["zieldatum"]):"",
		betrag => ($daten["betrag"])?sprintf("%0.2f",$daten["betrag"]):"",
		notxt => ($daten["notiz"])?$daten["notiz"]:"---",
		notiz => $daten["notiz"],
		ssel.$daten["status"] => "selected",
		csel.$daten["chance"] => "selected",
		save => $save,
		search => $search,
		block => $block,
		none => $none,
		button => $button,
		msg => $msg,
		jcal0 => ($jcalendar)?$jscal:"",
		jcal2 => ($jcalendar)?$jscal1:"",
		//jcal1 => ($jcalendar)?"<input type='image' src='image/date.png' title='Zieldatum suchen' name='zieldatum' align='middle' id='trigger1' value='?'>":""
		jcal1 => ($jcalendar)?"<a href='#' id='trigger1' name='zieldatum' title='Zieldatum suchen' onClick='false'><img src='image/date.png' border='0' align='middle'></a>":""
	));
	$t->pparse("out",array("op"));
?>
