<?php
// $Id$
	usleep(25000); // Timingprobleme??
	require_once("inc/stdLib.php");

	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	$ansicht=$_GET["ansicht"];
	$datum=$_GET["datum"];
	if ($datum=="") $datum=date("d.m.Y");
	if (!$ansicht) $ansicht="T";
	$t = new Template($base);
	if ($ansicht=="T") {
		if (!$datum) {$day=date("d"); $month=date("m"); $year=date("Y");}
		else {list($day,$month,$year)=split("\.",$datum);}
		$data=getTermin($day,$month,$year,"T");
		$termdata=array();
		$tlist=array();

		if ($data) foreach($data as $row) {
			if (!in_array($row["id"],$tlist)) {
				if ($row["stoptag"]>"$year-$month-$day" && $row["repeat"]=="0") $row["stopzeit"]="24:00";
				if ($row["starttag"]<"$year-$month-$day" && $row["repeat"]=="0") $row["startzeit"]="00:00";
				$tmp=split(":",$row["startzeit"]);
				$v=mktime($tmp[0],$tmp[1],0,$month,$day,$year);
				$tmp=split(":",$row["stopzeit"]);
				$b=mktime($tmp[0],$tmp[1],0,$month,$day,$year);
				$grund="[<font color='#4444ff'>".$row["cause"]."</font>] ";
				$tid=$row["termid"];
				for($v; $v<=$b; $v+=1800) {
					if (date("G",$v)>=$_SESSION["termbegin"] && date("G",$v)<=$_SESSION["termend"]) {
						$termdata[date("G:i",$v)].="<span  onClick=\"zeige(".$tid.")\">".$grund."</span>";
						$termid[date("G:i",$v)]=$tid;
						$grund="|| ";
						$tid=0;
					} else {
						$grund="[<font color='#4444ff'>".$row["cause"]."</font>] ";
						$tid=$row["termid"];
					}
				}
				$tlist[]=$row["id"];
			}
		}
		//print_r($termdata);
		$t->set_file(array("term" => "termintag.tpl"));
		$t1=date("d.m.Y",mktime(0,0,0,$month,$day-1,$year));
		$t2=date("d.m.Y",mktime(0,0,0,$month,$day+1,$year));
		$t->set_block("term","Stunden","Block");
		//print_r($termdata);
		for ($i=$_SESSION["termbegin"]; $i<=$_SESSION["termend"]; $i++) {
			$t->set_var(array(
				col => "gr",
				zeit => sprintf("%02d:00",$i),
				text => $termdata["$i:00"],
				tid => $termid["$i:00"],
			));
			$t->parse("Block","Stunden",true);
			$t->set_var(array(
				col => "we",
				zeit => sprintf("%02d:30",$i),
				text => $termdata["$i:30"],
				tid => $termid["$i:30"],
			));
			$t->parse("Block","Stunden",true);
		}
		$t->set_var(array(
			tag => $day.".".$month.".".$year,
			dat1 => $t1,
			dat2 => $t2,
			day => $day,
			month => $month,
			year => $year
		));
	} else if (substr($ansicht,0,1)=="K") {	
		$data=getTerminList(substr($ansicht,1,-1));
		$t->set_file(array("term" => "terminlist.tpl"));
		$t->set_block("term","Liste","Block");
		foreach ($data as $row) {
			$t->set_var(array(
				tid => $row["id"],
				start => db2date($row["starttag"])." ".$row["startzeit"],
				stop => db2date($row["stoptag"])." ".$row["stopzeit"],
				cause => $row["cause"]
			));
			$t->parse("Block","Liste",true);
		}
	} else if ($ansicht=="W") {
		$kw=$_GET["kw"];
		if ($_GET["year"]>0) {
			$year=$_GET["year"];
		} else {
			$year=substr($datum,6,4);
		}
		if (empty($kw) || $kw==0 || $kw=="") {
			list($day,$month,$year)=split("\.",$datum);
			$kw=date("W",mktime(0,0,0,$month,$day,$year));
		}
		$firstmonday = firstkw($year);
		$ft=feiertage($year);
		$ftk=array_keys($ft);
		$x=mondaykw($kw,$year);
		$kw=date("W",$x); 
		$kw1=date("W",$x-604800);
		$kw2=date("W",$x+604800);
		$year1=date("Y",$x-604800);
		$year2=date("Y",$x+604800);
		$tag=date("d.m.Y",$x);
		$startday=date("d",$x);
		$data=getTermin($startday,date("m",$x),$year,"W");
		$termdate=array();
		for ($i=0; $i<7; $i++) {
			if ($ft[$x+$i*86400]) {$termdate[$i][]=array("id"=>0,"txt"=>$ft[$x+$i*86400],"ft"=>1);};
		}
		$kalterm=array("datum"=>"","txt"=>"");
		$kaldrk=array("Mo"=>$kalterm,"Di"=>$kalterm,"Mi"=>$kalterm,"Do"=>$kalterm,"Fr"=>$kalterm,"Sa"=>$kalterm,"So"=>$kalterm);
		$drkwt=array("Mo","Di","Mi","Do","Fr","Sa","So");
		$lastt=0; $lastd=0;// Mehrfachtermine wegen Gruppe/n und UID
		if ($data) foreach($data as $row) {
			if ($row["termin"]<>$lastt || $lastd<>$row["tag"]) {
				$w=date("w",mktime(0,0,0,$row["monat"],$row["tag"],$row["jahr"]))-1;
				$termdate[$w][]=array("txt"=>$row["startzeit"]." ".$row["cause"],"id"=>$row["termin"],"ft"=>($i==5||$i==6)?1:0);
				$kaldrk[$drkwt[$w]]["txt"].=$row["startzeit"]." ".$row["cause"]."\n";
				$kaldrk[$drkwt[$w]]["datum"]=$row["tag"].".".$row["monat"];
				$lastt=$row["termin"];
				$lastd=$row["tag"];
			} else { $lastt=0;}
		}
		//$wt=array(0=>"Montag",1=>"Dienstag",2=>"Mitwoch",3=>"Donnerstag",4=>"Freitag",5=>"Samstag",6=>"Sonntag");
		$wt=array(0=>"Mo",1=>"Di",2=>"Mi",3=>"Do",4=>"Fr",5=>"Sa",6=>"So");
		$t->set_file(array("term" => "terminwoche.tpl"));
		$t->set_block("term","Woche","Block");
		$ftf=array(5,6);
		for ($i=0; $i<7; $i++) {
			if ($termdate[$i]) {
				$datum=$wt[$i].", ".date("d.m.",$x);
				$farbe="gr";
				if ($termdate[$i]) foreach($termdate[$i] as $row) {
					$farbe=($row["ft"]==1)?"ft":$farbe;
					$t->set_var(array(
						col => $farbe,
						datum => date("d.m.Y",$x),
						S1 => $datum,
						S2 => $row["txt"],
						tid => $row["id"]
					));
					$t->parse("Block","Woche",true);
					$farbe="we";
					$datum="";
				}
				$t->set_var(array(
					col => "we",
					datum => date("d.m.Y",$x),
					S1 => "&nbsp;",
					S2 => "",
					tid => 0
				));
				$t->parse("Block","Woche",true);
			} else {
				$t->set_var(array(
					col => ($i==5||$i==6)?"ft":"gr",
					datum => date("d.m.Y",$x),
					S1 => $wt[$i].", ".date("d.m.",$x),
					S2 => ". . . . . . . . . . . . .",
					tid => 0
				));
				$t->parse("Block","Woche",true);
				$t->set_var(array(
					col => "we",
					datum => date("d.m.Y",$x),
					S1 => "&nbsp;",
					S2 => "",
					tid => 0
				));
				$t->parse("Block","Woche",true);
			}
			$x+=60*60*24;
		}
		$t->set_var(array(
					tag => $tag,
					kw => $kw,
					kw1 => $kw1,
					kw2 => $kw2,
					year1 => $year1,
					year2 => $year2,
					year => $year,
				));
	} else if ($ansicht=="M") {
		require ("terminmonat.php");
		//header("location:terminmonat.php");
	} else {
		$t->set_file(array("term" => "termintag.tpl"));
	}

	$t->pparse("out",array("term"));
?>
