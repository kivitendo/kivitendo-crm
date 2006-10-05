<?
// $Id$
require_once 'Image/Graph.php';
require_once 'Image/Canvas.php'; 

/****************************************************
* getLastYearPlot
* in: re = array
* out: IMG = string
* Rechnungsdaten grafis aufbereiten
*****************************************************/
function  getLastYearPlot($re,$an,$art=false) {
	$Canvas =& Image_Canvas::factory('png', array('width'=>500, 'height'=>280)); 
	$Graph =& Image_Graph::factory('graph', $Canvas); 
	//$Font =& $Graph->addNew('font', 'Verdana');
	$Font =& $Graph->addNew('font', 'FreeSans');
	$Font->setSize(8);
	$Graph->setFont($Font);
	if ($art) { $log="_log"; } else { $log=""; };
	$Graph->add(
	   Image_Graph::vertical(
        	    $Plotarea = Image_Graph::factory('plotarea',array('axis',"axis$log")),
	            $Legend = Image_Graph::factory('legend'),
        	    90
	    )
	);
	$Legend->setPlotarea($Plotarea);
	$Legend->setShowMarker(true);

	$gut =& Image_Graph::factory('dataset'); 
	$sum =& Image_Graph::factory('dataset'); 
	$avg =& Image_Graph::factory('dataset'); 
	$agb =& Image_Graph::factory('dataset'); 
	$gut->addPoint(0,1);
	$sum->addPoint(0,1);
	$avg->addPoint(0,1);
	$agb->addPoint(0,0);
	$employee=$_SESSION["employee"];
	$keys=array_keys($re);
	$maxY=0;
	if ($re) for($xx=0; $xx<12; $xx++) { 
		$k=substr($keys[$xx],4,2);
		if ($re[$keys[$xx]]["summe"]<0) {
			$gut->addPoint($xx+1, $re[$keys[$xx]]["summe"]*-1); 
			$sum->addPoint($xx+1, 1); 
			$avg->addPoint($xx+1, 1);
		} else {
			if ($re[$keys[$xx]]["summe"]>0) { $sum->addPoint($xx+1,$re[$keys[$xx]]["summe"]);
			} else {  $sum->addPoint($xx+1, 1); }
			$gut->addPoint($xx+1, 0); 
			if ($re[$keys[$xx]]["count"]>0) {
				$avg->addPoint($xx+1,($re[$keys[$xx]]["summe"]/$re[$keys[$xx]]["count"]) );
			} else {	
				$avg->addPoint($xx+1, 1);
			}
		};
		if ($maxY<$re[$keys[$xx]]["summe"]) $maxY=$re[$keys[$xx]]["summe"];
	}
	$xx=0;
	if ($an) foreach($an as $month) {
		if ($xx<12) {
			$agb->addPoint($xx+1,$month["summe"]);
			if ($maxY<$month["summe"])$maxY=$month["summe"];
		}
		$xx++;
	}
	$gut->addPoint(13,1);
	$sum->addPoint(13,1);
	$avg->addPoint(13,1);
	$agb->addPoint(13,0);
	$monate[]="";
	if ($keys) foreach ($keys as $m) {
		$monate[]=substr($m,2,2)."/".substr($m,4,2);
	}
	$monate=array_splice($monate,0,-1);
	$monate[]=" ";
	$Datasets = array($sum,$avg,$gut);

	$Plot =& $Plotarea->addNew('bar', array(&$Datasets));
	$Datasets[0]->setName("Gesamtumsatz");
	$Datasets[1]->setName("Durchschnitt");
	$Datasets[2]->setName("Gutschrift");
	
	$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
	$FillArray->addColor('blue@0.7');
	$FillArray->addColor('green@0.7');
	$FillArray->addColor('yellow@0.7');
	$Plot->setFillStyle($FillArray); 

	$Plot3 =& $Plotarea->addNew('line', array(&$agb));
	$Plot3->setLineColor('gray@0.0');
	$Marker =& Image_Graph::factory('Image_Graph_Marker_Circle');
	$Plot3->setMarker($Marker);
	$Marker->setFillColor('red@0.7'); 
	$Plot3->setTitle("Angebote"); 
	$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
	if ($art) $AxisY->setLabelInterval(array(5, 10, 20, 30, 50, 100, 200, 300, 500, 1000, 2000, 3000, 5000, 10000, 20000, 30000, 50000)); 
	$AxisY->forceMaximum($maxY+50); 
	$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X); 
	$AxisX->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Array', array($monate)));
	$AxisX->setLabelInterval(1); 
	$GridY =& $Plotarea->addNew('line_grid', null, IMAGE_GRAPH_AXIS_Y);
	$GridY->setLineColor('gray@0.4'); 
	$Graph->setLineColor('red'); 
	$fill =& Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, '#fffc9e', 'lightblue@0.3'));
	$Graph->setBackgroundColor($fill); 
	$Graph->setBorderColor('black');
	$Graph->showShadow(); 
	$Fillbg =& Image_Graph::factory('Image_Graph_Fill_Image', 'image/umsatz.jpg');
	$Plotarea->setFillStyle($Fillbg); 

	$IMG="./tmp/$employee.png";
	@exec("rm ./tmp/$employee.png");
	$Graph->done( 
	    array('filename' => $IMG) 
	);
	return $IMG;
}
?>
