<?php
	require_once("inc/stdLib.php");
	include("inc/FirmenLib.php");	
	include("inc/wvLib.php");
	include("inc/pdfpos.php");
	$rep=suchVertrag($_GET["aid"]);
	$rep=$rep[0];
	$masch=getVertragMaschinen($rep["cid"]);
	$firma=getFirmenStamm($rep["customer_id"]);
	define("FPDF_FONTPATH","../font/");
	require("fpdf.php");
	require("fpdi.php");
	$pdf = new FPDI('P','mm','A4');
	$seiten=$pdf->setSourceFile("vorlage/wv".$rep["template"]);
	$ende=($rep["endedatum"]==$rep["anfangdatum"])?"offen":db2date($rep["endedatum"]);
	$hdl=$pdf->ImportPage(1);
	$pdf->addPage();
	$pdf->useTemplate($hdl);
	$pdf->SetFont($wvfont,'',$wvsize);	
	$pdf->Text($wvname[x],$wvname[y],utf8_decode($firma["name"]));
	$pdf->Text($wvstr[x],$wvstr[y],utf8_decode($firma["street"]));
	$pdf->Text($wvort[x],$wvort[y],$firma["zipcode"]." ".utf8_decode($firma["city"]));
	$pdf->Text($wvkdnr[x],$wvkdnr[y],$firma["customernumber"]);	
	$pdf->Text($wvwvnr[x],$wvwvnr[y],$rep['contractnumber']);
	$pdf->Text($wvstart[x],$wvstart[y],db2date($rep["anfangdatum"]));
	$pdf->Text($wvende[x],$wvende[y],$ende);	
	$pdf->Text($wvbetrag[x],$wvbetrag[y],sprintf("%0.2f",$rep["betrag"]));
	$pdf->SetFont('Helvetica','',10);	
	$bem=($rep["bemerkung"])?utf8_decode($rep["bemerkung"]):"Es werden keine Sondervereinbarungen getroffen";
	$pdf->SetY($wvbem[y]);
	$pdf->SetX($wvbem[x]);
	$pdf->MultiCell(0,6,$bem,0);
	for ($j=2; $j<=$seiten; $j++) {
		$hdl=@$pdf->ImportPage($j);
		$pdf->addPage();
	        $pdf->useTemplate($hdl);
	}
	$pdf->SetFont($wvfont,'',$wvsize);	
	$i=300; $p=1;
	foreach ($masch as $row) {
		if ($i>270) {
			$pdf->addPage();	
			$pdf->Text($wvkopf[x],$wvkopf[y],"Anhang A (Seite $p) zum Wartungsvertrag  ".$rep['contractnumber']."  vom  ".db2date($rep["anfangdatum"]));
			$i=40; $p++;
		}
		$pdf->Text($wvmasch,$i,utf8_decode($row["description"]));
		$pdf->Text($wvsernr,$i," #".$row["serialnumber"]);
		$pdf->Text($wvsort,$i+8,utf8_decode($row["standort"]));
		$i+=20;
	}
	$pdf->Output('Wartungsvertrag_'.$rep['contractnumber'].'.pdf',"I");
?>
