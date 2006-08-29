<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/FirmenLib.php");	
	include("inc/wvLib.php");
	$rep=getRAuftrag($_GET["aid"]);
	$masch=getAllMaschine($rep["mid"]);
	$firma=getFirmenStamm($masch["customer_id"]);
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
	$pdf->Text(26.0,77.0,$firma["name"]);
	$pdf->Text(26.0,85.0,$firma["street"]);
	$pdf->Text(26.0,99.0,$firma["zipcode"]." ".$firma["city"]);
	$pdf->Text(26.0,108.0,$firma["phone"]);	
	$pdf->Text(138.0,59.8,$_GET["aid"]);
	$pdf->SetFont('Helvetica','',12);
	$pdf->Text(138.0,77.7,$masch["contractnumber"]);
	$pdf->Text(138.0,89.8,$firma["customernumber"]);
	$pdf->Text(138.0,101.7,date("d.m.Y"));		
	$pdf->Text(50.0,130.0,$masch["description"]);
	$pdf->Text(50.0,136.0,$masch["serialnumber"]);
	$pdf->Text(50.0,142.0,$masch["standort"]);			
	$pdf->Text(50.0,148.0,$masch["counter"]);			
	$pdf->Text(138.0,148.0,db2date($masch["inspdatum"]));			
	$pdf->Text(50.0,154.0,$rep["cause"]);
	$pdf->SetY(160);
	$pdf->SetX(19);
	$pdf->MultiCell(0,6,$rep["schaden"],0);
	$pdf->addPage();	
	$history="Die letzten Ereignisse:\n";
	$history.=db2date($hist[0]["datum"]);$history.="     ".$hist[0]["art"]."    ";
	if ($hist[0]["art"]=="RepAuftr") { 
		preg_match("/^[0-9]+\|(.+)/",$hist[0]["beschreibung"],$treffer);
		$history.=$treffer[1]."\n";
	} else { 
		$history.=$hist[0]["beschreibung"]."\n";	
	}
	$history.=db2date($hist[1]["datum"]);$history.="     ".$hist[1]["art"]."    ";
	if ($hist[1]["art"]=="RepAuftr") { 
		preg_match("/^[0-9]+\|(.+)/",$hist[1]["beschreibung"],$treffer);
		$history.=$treffer[1]."\n";
	} else { 
		$history.=$hist[1]["beschreibung"]."\n";	
	}
	$history.=db2date($hist[2]["datum"]);$history.="     ".$hist[2]["art"]."    ";
	if ($hist[2]["art"]=="RepAuftr") { 
		preg_match("/^[0-9]+\|(.+)/",$hist[2]["beschreibung"],$treffer);
		$history.=$treffer[1]."\n";
	} else { 
		$history.=$hist[2]["beschreibung"]."\n";	
	}

	$pdf->MultiCell(0,6,$history."\nLetzte Reparatur:\n".$rep["reparatur"]."\n\nVerbrauchtes Material:\n".$mat,0);

	$pdf->OutPut();
?>
