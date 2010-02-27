<?
	require_once("inc/stdLib.php");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	include('inc/phpOpenOffice.php');
	$kalterm=array("datum"=>"","txt"=>"");
	$kaldrk=array("MO"=>$kalterm,"DI"=>$kalterm,"MI"=>$kalterm,"DO"=>$kalterm,"FR"=>$kalterm,"SA"=>$kalterm,"SO"=>$kalterm);
	$drkwt=array("MO","DI","MI","DO","FR","SA","SO");
	$vars=array("JAHR"=>"","MO"=>"","DI"=>"","MI"=>"","DO"=>"","FR"=>"","SA"=>"","SO"=>"",
		    "TERMINMO"=>"","TERMINDI"=>"","TERMINMI"=>"","TERMINDO"=>"","TERMINFR"=>"","TERMINSA"=>"","TERMINSO"=>"");
	$kw=$_GET["kw"];
	$year=$_GET["year"];
	if (!$kw) {
		list($day,$month,$year)=split("\.",$datum);
		$kw=date("W",mktime(0,0,0,$month,$day,$year));
	}
	$ft=feiertage($year);
	$ftk=array_keys($ft);
	$x=mondaykw($kw,$year);
	$tag=date("d",$x);
	$month=date("m",$x);
	$startday=date("d",$x);
	$data=getTermin($startday,date("m",$x),$year,"W");
	$vars["JAHR"]=$year;
	$vars["KW"]=$kw;
	$usr=getUserStamm($_SESSION["loginCRM"]);
	$vars["NAME"]=$usr["name"];
	for($i=0; $i<7; $i++) {
		$vars[$drkwt[$i]]=date("d.m.",mktime(0,0,0,$month,$tag+$i,$year));
	}
	if ($data) foreach($data as $row) {
		if ($row["termin"]<>$lastt || $lastd<>$row["tag"]) {
			$w=date("w",mktime(0,0,0,$row["monat"],$row["tag"],$row["jahr"]))-1;
			$vars["TERMIN".$drkwt[$w]].=(($vars["TERMIN".$drkwt[$w]])?"\n":"").$row["startzeit"]." ".$row["cause"];
			$lastt=$row["termin"];
			$lastd=$row["tag"];
		} else { $lastt=0;}
	}
	$doc = new phpOpenOffice();
	if (file_exists("vorlage/kalwoche_".$usr["login"].".sxw")) {
		$doc->loadDocument("vorlage/kalwoche_".$usr["login"].".sxw");
	} else {
		$doc->loadDocument("vorlage/kalwoche.sxw");
	}
	
	$doc->parse($vars);
	$doc->download("");
	$doc->clean();
?>
