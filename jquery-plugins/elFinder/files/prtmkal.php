<?php
	require_once("inc/stdLib.php");
	include("inc/crmLib.php");
	include_once("inc/UserLib.php");
	include('inc/phpOpenOffice.php');
	$month=$_GET["month"];
	$year=$_GET["year"];
	$usr=getUserStamm($_SESSION["loginCRM"]);
	$vars=array("KW1"=>"","KW2"=>"","KW3"=>"","KW4"=>"","KW5"=>"","KW6"=>"","NAME"=>$usr["name"],"MONAT"=>$month,"JAHR"=>$year);
	$kaltg=array(" ","MO","DI","MI","DO","FR","SA","SO");
	for ($i=1; $i<7; $i++) 
		for ($j=1; $j<8; $j++) {
			$vars[$kaltg[$j]."$i"]="";
			$vars[$kaltg[$j]."#$i"]="";
		}
	if (!$kw) {
		$kw=date("W",mktime(0,0,0,$month,1,$year));
	}
	$ft=feiertage($year);
	$ftk=array_keys($ft);
	$data=getTermin(0,$month,$year,"M");
		$tmp=array();
		if ($data) foreach ($data as $term) {
			$tmp[$term["tag"]]++;
		}
	$days=array_keys($tmp);
	//first week, still in last month?
	if (date("w", mktime(0,0,0,$month,1,$year)) == 0) { $da = -6; }
	elseif (date("w", mktime(0,0,0,$month,1,$year)) <> 1) { $da = - date("w", mktime(0,0,0,$month,1,$year)) +1;}
	else {$da = 1;}
	// set week number for the first time
 	$woche=strftime("%V",mktime(0,0,0,$month,$da+2,$year));
	$vars["KW1"]=$woche;
	// show days of the previous month
	if ( date("w", mktime(0,0,0,$month,1,$year)) == 0) { $start = 7; }
	else {$start = date("w", mktime(0,0,0,$month,1,$year)); }
	$x=1;
  	for ($a = ($start-2); $a>=0; $a--) {
    		$d = date("t", mktime(0,0,0,$month,0,$year)) - $a;
		//$vars[$kaltg[$x]."1"]=$d;
		$vars[$kaltg[$x]."1"]="";
		$x++;
  	}
	$wo=1;
	$firstday=mktime(0,0,0,$month,1,$year);
	$anztage=date("t", mktime(0,0,0,($month+1),0,$year));
	// show days of the actual month
	$wtag=date("w", mktime(0,0,0,$month,1,$year));   
	for ($d=1; $d <= $anztage; $d++) {
		$bg="";
		$akt=mktime(0,0,0,$month,$d,$year);
		if(in_array($akt,$ftk)) {
			$bg .= substr($ft[$akt],0,1);
		}
		if (in_array($d,$days)) { $bg.=$tmp[$d];};
		$vars[$kaltg[$wtag]."$wo"]=$d;
		$vars[$kaltg[$wtag]."#$wo"]=$bg;
		$wtag++;
		if ($wtag>7) {
				$wtag=1;
				$da = $d + 1;
				$woche=strftime("%V",mktime(0,0,0,$month,$da+2,$year));
				$wo++;
				$vars["KW$wo"]=$woche;
		}
	}
	// show days of the next month
	if (date("w", mktime(0,0,0,$month+1,1,$year)) <> 1) {
		$tg=1;
		$start=date("w", mktime(0,0,0,($month+1),$tg,$year));
		while ($start > 1 and $start <8) {
			//$vars[$kaltg[$start].$wo]=$tg;
			$vars[$kaltg[$start].$wo]="";
			$tg++;
			$start++;
		}
	}

	$x=mondaykw($kw,$year);
	$tag=date("d",$x);
	$startday=date("d",$x);
	$data=getTermin($startday,date("m",$x),$year,"W");
	$vars["KW"]=$kw;
	for($i=0; $i<7; $i++) {
		$vars[$drkwt[$i]]=date("d.m.",mktime(0,0,0,$month,$tag+$i,$year));
	}
	$doc = new phpOpenOffice();
	if (file_exists("vorlage/kalmonat_".$usr["login"].".sxw")) {
		$doc->loadDocument("vorlage/kalmonat_".$usr["login"].".sxw");
	} else {
		$doc->loadDocument("vorlage/kalmonat.sxw");
	}
	
	$doc->parse($vars);
	$doc->download("");
	$doc->clean();
?>
