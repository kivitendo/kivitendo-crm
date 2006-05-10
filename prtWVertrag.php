<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/FirmenLib.php");	
	include("inc/wvLib.php");
	$rep=suchVertrag($_GET["aid"]);
	$rep=$rep[0];
	$masch=getVertragMaschinen($rep["contractnumber"]);
	$firma=getFirmenStamm($rep["customer_id"]);
	
	define("FPDF_FONTPATH","../font/");
	require("fpdi.php");
	$pdf = new FPDI('P','mm','A4');
	$seiten=$pdf->setSourceFile("vorlage/wv".$rep["template"]);
	$hdl=$pdf->ImportPage(1);
	$pdf->addPage();
	$pdf->useTemplate($hdl);
	$pdf->SetFont('Helvetica','',12);
	$pdf->Text(24.0,53.0,$firma["name"]);
	$pdf->Text(24.0,61.0,$firma["street"]);
	$pdf->Text(24.0,69.0,$firma["zipcode"]." ".$firma["city"]);
	$pdf->Text(160.0,55.0,$firma["id"]);	
	$pdf->Text(50.0,144.5,$rep["cid"]);
	$pdf->Text(110.0,145.0,db2date($rep["anfangdatum"]));
	$ende=($rep["endedatum"]==$rep["anfangdatum"])?"":db2date($rep["endedatum"]);
	$pdf->Text(156.0,145.0,$ende);	
	$pdf->SetFont('Helvetica','',10);	
	$bem=($rep["bemerkung"])?$rep["bemerkung"]:"Es werden keine Sondervereinbarungen getroffen";
	//$pdf->Text(20.0,170.0,$bem);
	$pdf->SetY(165);
	$pdf->SetX(20);
	$pdf->MultiCell(0,6,$bem,0);
	$pdf->Text(172.0,240.0,sprintf("%0.2f",$rep["betrag"]));
	$pdf->SetFont('Helvetica','',12);	
	$i=300; $p=1;
	foreach ($masch as $row) {
		if ($i>270) {
			$pdf->addPage();	
			$pdf->Text(24.0,25.0,"Anhang A (Seite $p) zum Wartungsvertrag  ".$rep["cid"]."  vom  ".db2date($rep["anfangdatum"]));
			$i=40; $p++;
		}
		$pdf->Text(24.0,$i,$row["description"]);
		$pdf->Text(135.0,$i," #".$row["serialnumber"]);
		$pdf->Text(24.0,$i+8,$row["standort"]);
		$i+=20;
	}
	$pdf->OutPut();
?>
