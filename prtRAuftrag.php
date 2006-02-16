<?
// $Id: prtRAuftrag.php,v 1.3 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/FirmaLib.php");	
	include("inc/wvLib.php");
	$rep=getRAuftrag($_GET["aid"]);
	$masch=getAllMaschine($rep["mid"]);
	$firma=getFirmaStamm($masch["customer_id"]);
	$hist=getHistory($rep["mid"]);	
	$material=getAllMat($_GET["aid"],$rep["mid"]);
	if ($material) foreach ($material as $zeile) {
		$mat.=$zeile["menge"]." x ".substr($zeile["description"],0,70)."\n";
	};
	define("FPDF_FONTPATH","../font/");
	require("fpdi.php");
	$pdf = new FPDI('P','mm','A4');
	$seiten=$pdf->setSourceFile("vorlage/repauftrag.pdf");
	$hdl=$pdf->ImportPage(1);
	$pdf->addPage();
	$pdf->useTemplate($hdl);
	$pdf->SetFont('Helvetica','B',14);
	$pdf->Text(24.0,58.0,$firma["name"]);
	$pdf->Text(24.0,66.0,$firma["street"]);
	$pdf->Text(24.0,74.0,$firma["zipcode"]." ".$firma["city"]);
	$pdf->Text(24.0,82.0,$firma["phone"]);	
	$pdf->SetFont('Helvetica','',12);
	$pdf->Text(138.0,64.7,$_GET["aid"]);
	$pdf->Text(138.0,71.8,$firma["id"]);
	$pdf->Text(138.0,77.7,date("d.m.Y"));		
	$pdf->Text(50.0,100.0,$masch["description"]);
	$pdf->Text(50.0,107.0,$masch["serialnumber"]);
	$pdf->Text(50.0,114.0,$masch["standort"]);			
	$pdf->Text(20.0,128.0,$rep["cause"]);
	$pdf->SetY(135);
	$pdf->SetX(20);
	$pdf->MultiCell(0,6,$rep["schaden"],0);
	$pdf->addPage();	
	$history=$hist[0]["datum"];$history.="     ".$hist[0]["art"]."    ";
	if ($hist[0]["art"]=="RepAuftr") { 
		preg_match("/^[0-9]+\|(.+)/",$hist[0]["beschreibung"],$treffer);
		$history.=$treffer[1]."\n";
	} else { 
		$history.=$hist[0]["beschreibung"]."\n";	
	}
	$history.=$hist[1]["datum"];$history.="     ".$hist[1]["art"]."    ";
	if ($hist[1]["art"]=="RepAuftr") { 
		preg_match("/^[0-9]+\|(.+)/",$hist[1]["beschreibung"],$treffer);
		$history.=$treffer[1]."\n";
	} else { 
		$history.=$hist[1]["beschreibung"]."\n";	
	}
	$history.=$hist[2]["datum"];$history.="     ".$hist[2]["art"]."    ";
	if ($hist[2]["art"]=="RepAuftr") { 
		preg_match("/^[0-9]+\|(.+)/",$hist[2]["beschreibung"],$treffer);
		$history.=$treffer[1]."\n";
	} else { 
		$history.=$hist[2]["beschreibung"]."\n";	
	}

	$pdf->MultiCell(0,6,$history."\n".$rep["reparatur"]."\n\n".$mat,0);

	$pdf->OutPut();
?>
