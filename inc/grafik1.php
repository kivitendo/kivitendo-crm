<?php
unset($GLOBALS['php_errormsg']);
include ("jpgraph.php");
include ("jpgraph_line.php");
include ("jpgraph_bar.php");
include ("jpgraph_log.php");
define("FPDF_FONTPATH","/usr/share/fpdf/font/");
define("FONTART","2");
define("FONTSTYLE","1");

/****************************************************
* getLastYearPlot
* in: re = array
* out: IMG = string
* Rechnungsdaten grafis aufbereiten
*****************************************************/
function  getLastYearPlot($re,$an,$art=false) {

	$employee=$_SESSION["employee"];
	$keys=array_keys($re);
	$sum=array(); $avg=array(); $monate=array(); $gut=array();
	if ($re) foreach($re as $month) {
		if ($month["summe"]<0) {
			$gut[]=$month["summe"]*-1;
			$sum[]=0;
			$avg[]=0;
		} else {
			$sum[]=$month["summe"];
			$gut[]=0;
			if ($month["count"]>0) {
				$avg[]=$month["summe"]/$month["count"];
			} else {
				$avg[]=0;
			}
		};
	}
	if ($an) foreach($an as $month) {
		$sumA[]=$month["summe"];
	}
	if ($keys) foreach ($keys as $m) {
		$monate[]=substr($m,4,2);
	}
	$monate=array_splice($monate,0,-1);
	$sum=array_splice ($sum,0,-1);
	$gut=array_splice ($gut,0,-1);
	if (!empty($sumA)) {
		$sumA=array_splice ($sumA,0,-1);
	} else {
		$sumA[]=0;
	}
	$avg=array_splice ($avg,0,-1);
	$graph = new Graph(500,280);
	if ($art) {$art="textlin";}
	else {$art="textlog";};
	$graph->SetScale($art);
	$graph->img->SetMargin(70,20,25,70);

	$sumplot = new BarPlot($sum);		// Graph Umsatz
	$sumplot->SetColor("darkgreen");
	$sumplot->SetWidth(1.0);
	$sumplot->SetAlign("center");
	//$sumplot->SetCenter(true);
	//$sumplot->mark->SetType(MARK_CIRCLE);
	$sumplot->SetLegend("Gesamtumsatz");
	$graph->Add($sumplot);

	$avgplot = new BarPlot($avg);		// Graph durchn. Umsatz
	$avgplot->SetColor("darkred");
	$avgplot->SetFillColor("red");
	$avgplot->SetWidth(0.5);
	$avgplot->SetAlign("center");
	//$avgplot->SetStyle("dashed");
	//$avgplot->SetCenter(true);
	$avgplot->SetLegend("duchschnitt");
	$graph->Add($avgplot);

	if (empty($gut)) $gut[]=0;
	$gutplot = new BarPlot($gut);		// Graph Umsatz
	$gutplot->SetColor("darkgreen");
	$gutplot->SetFillColor("green");
	$gutplot->SetWidth(0.2);
	$gutplot->SetAlign("center");
	//$sumplot->SetCenter(true);
	//$sumplot->mark->SetType(MARK_CIRCLE);
	$gutplot->SetLegend("Gutschrift");
	$graph->Add($gutplot);


	$angbplot = new LinePlot($sumA);		// Graph Angebote
	$angbplot->SetColor("darkblue");
	//$angbplot->SetStyle("dashed");
	$angbplot->SetCenter(true);
	$angbplot->mark->SetType(MARK_FILLEDCIRCLE);
	$angbplot->mark->SetWidth(4);
	$angbplot->mark->SetFillColor("yellow");
	//$angbplot->mark->SetType(MARK_CIRCLE);

	$angbplot->SetLegend("Angbote");
	$graph->Add($angbplot);

	$graph->yaxis->title->SetFont(FONTART,FONTSTYLE);
	$graph->xaxis->title->SetFont(FONTART,FONTSTYLE);
	$graph->yaxis->SetFont(FONTART,FONTSTYLE);
	$graph->xaxis->SetFont(FONTART,FONTSTYLE);
	$graph->xaxis->title->Set("Monate");
	$graph->yaxis->title->Set("Euro");
	$graph->yaxis->SetTitleMargin(50);
	$graph->xaxis->SetTickLabels($monate);
	$graph->legend->Pos(0.03,0.90,"left","center");

	$IMG='tmp/'.$employee.'.png';
	@exec('rm '.$IMG);

	$graph->Stroke($IMG);
	return $IMG;
}
?>
