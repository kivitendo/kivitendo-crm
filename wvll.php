<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");

	function mkdate($dat) {
		$t=split(" ",substr($dat,0,-3));
		$n=split("-",$t[0]);
		$d=date("d M Y",mktime(0, 0, 0, $n[1], $n[2], $n[0]));
		return $d." ".$t[1];
	}
	$t = new Template($base);
	$t->set_file(array("wvl" => "wvll.tpl"));
	$t->set_block("wvl","Liste","Block");
	$i=0;
	$interv=getIntervall($_SESSION["loginCRM"]);
	$mail=holeMailHeader($_SESSION["loginCRM"]);
	if ($mail)
	foreach($mail as $col){
		$t->set_var(array(
			LineCol => $bgcol[($i%2+1)],
			Type	=> $typcol["M"],
			Status => $col["Gelesen"],
			Cause => $col["Betreff"],
			Initdate => $col["Datum"],
			ID => $col["Nr"],
			IniUser => $col["Abs"],
			Art => "M"
		));
		$t->parse("Block","Liste",true);
		$i++;
	}

	$wvl=getWvl($_SESSION["loginCRM"]);
	$termine=getTermin(date("d"),date("m"),date("Y"),"T");
	if ($termine && $wvl) {
		 $wvl=array_merge($termine,$wvl);
	} else if ($termine) {
		$wvl=$termine;
	}
	$nun=date("Y-m-d 00:00:00");
	$nun1=date("Y-m-d H:i");
	if ($wvl) foreach($wvl as $col){
		if ($col["finishdate"] || $col["stoptag"]) {
			if ( ($col["finishdate"]<>"" && $col["finishdate"]<$nun) || 
			     ($col["stoptag"]<>"" && $col["stoptag"]." ".$col["stopzeit"]<$nun1) ) {
				$bgc=$bgcol[3];
			} else {
				$bgc=$bgcol[($i%2+1)];
			}
			$datum=mkdate(($col["finishdate"])?$col["finishdate"]:$col["stoptag"]." ".$col["stopzeit"].":00");
		} else {
			if ($col["trans_module"]) {
				$datum=mkdate($col["initdate"]." 00:00:00");
			} else {
				$datum=mkdate(($col["initdate"])?$col["initdate"]:$col["starttag"]." ".$col["startzeit"].":00");
			}
			$bgc=$bgcol[($i%2+1)];
		}
		if ($col["status"]=="F") { $Art="F"; }
		else if ($col["starttag"]) { $Art="T"; }
		else { $Art="D"; };
		$t->set_var(array(
			LineCol => $bgc,
			Type	=> $typcol[($col["kontakt"])?$col["kontakt"]:"X"],
			Status => ($col["status"])?$col["status"]:"-",
			Cause => $col["cause"],
			Initdate => $datum,
			ID => $col["id"],
			IniUser => ($col["initemployee"])?$col["initemployee"]:$col["employee"],
			Art => $Art
		));
		$t->parse("Block","Liste",true);
		$i++;
	} 
	$t->set_var(array(
		Interv => (!$interv || $interv<10)?999999:$interv
	));
	$t->pparse("out",array("wvl"));
?>
