<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/grafik$jpg.php");
	include("inc/LieferLib.php");
	$t = new Template($base);
	$fa=getLieferStamm($_GET["fid"]);
	if ($_GET["linlog"]) { $linlog="&linlog=0"; $ll=true; }
	else {$linlog="&linlog=1"; $ll=false; }
	$link1="liefer1.php?id=".$_GET["fid"];
	$link2="liefer2.php?fid=".$_GET["fid"];
	$link3="liefer3.php?fid=".$_GET["fid"].$linlog;
	$link4="liefer4.php?fid=".$_GET["fid"];
	$name=$fa["name"];
	$plz=$fa["zipcode"];
	$ort=$fa["city"];
	$jahr=$_GET["jahr"];
	if (empty($jahr)) $jahr=date("Y"); 
	if ($jahr==date("Y"))  {
		$JahrV="";
	} else {
		$link3.="&jahr=$jahr";
		$JahrV=$jahr+1;
	}
	$JahrZ=$jahr-1;	
	if ($_GET["monat"]) {
		$m=substr($_GET["monat"],3,4)."-".substr($_GET["monat"],0,2);
		$reM=getReMonat($_GET["fid"],$m,true);
		$t->set_file(array("fa1" => "liefer3a.tpl"));
		$IMG="";
	} else {
		$re=getReJahr($_GET["fid"],$jahr,true);
		$an=getAngebJahr($_GET["fid"],$jahr,true);
		$t->set_file(array("fa1" => "liefer3.tpl"));
		$IMG=getLastYearPlot($re,$an,$ll);
		$monat="";
	}
	$t->set_var(array(
			FID => $_GET["fid"],
			PID => $_GET["pid"],
			Link1 => $link1,
			Link2 => $link2,
			Link3 => $link3,
			Link4 => $link4,
			LInr => $fa["vendornumber"],
			Name => $name,
			Plz => $plz,
			Ort => $ort,
			IMG	=> $IMG,
			JAHR => $jahr,
			JAHRV => $JahrV,
			JAHRZ => $JahrZ,
			JAHRVTXT => ($JahrV>0)?"Sp&auml;ter":"",
			Monat => $monat
			));
	if ($re) {
		$t->set_block("fa1","Liste","Block");
		$i=0;
		$monate=array_keys($re);
		for ($i=0; $i<13; $i++) {
			$colr=array_shift($re);
			$cola=array_shift($an);
			$t->set_var(array(
				LineCol => $bgcol[($i%2+1)],
				Month => substr($monate[$i],4,2)."/".substr($monate[$i],0,4),
				Rcount => $colr["count"],
				RSumme => sprintf("%01.2f",$colr["summe"]),
				ASumme => sprintf("%01.2f",$cola["summe"]),
				Curr => $colr["curr"]
			));
			$t->parse("Block","Liste",true);
			//$i++;
		}
	}
	if ($reM) {
		$t->set_block("fa1","Liste","Block");
		$i=0;
		if ($reM) foreach($reM as $col){
			if (array_key_exists("invnumber",$col)){
				$typ="R";
				$renr=$col["invnumber"];
				$offen=($col["amount"]==$col["paid"])?"-":"+";
			} else {
				if ($col["quotation"]=="f") {
					$typ="L";
					$renr=$col["ordnumber"];
					$offen="+";
				} else {
					$typ="A";
					$renr=$col["quonumber"];
					$offen="";
				}
			}
			$t->set_var(array(
				LineCol => $bgcol[($i%2+1)],
				Datum => db2date($col["transdate"]),
				RNr	=> (array_key_exists("invnumber",$col))?$col["invnumber"]:$col["quonumber"],
				RNid => $col["id"],
				RSumme => sprintf("%01.2f",$col["netamount"]),
				RBrutto => sprintf("%01.2f",$col["amount"]),
				Curr => $col["curr"],
				Typ => $typ,
				offen => $offen,
				Type => ($typ=="R")?"V":$typ
				));
			$t->parse("Block","Liste",true);
			$i++;
		}
	}
	if ($monat and !$reM) {
		$t->set_block("fa1","Liste","Block");
			$i=0;
			$t->set_var(array(
				LineCol => "",
				Datum => "",
				RNr	=> "Keine ",
				RSumme => "Ums&auml;tze",
				Curr => ""
				));
			$t->parse("Block","Liste",true);
	}
	$t->pparse("out",array("fa1"));
?>
