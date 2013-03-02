<?php
	require_once("inc/stdLib.php");
	include("inc/FirmenLib.php");	
	include("inc/wvLib.php");
        include("inc/pdfpos.php");
	$rep=getRAuftrag($_GET["aid"]);
	$masch=getAllMaschine($rep["mid"]);
	$firma=getFirmenStamm($masch["customer_id"]);
	$hist=getHistory($rep["mid"]);	
	$material=getAllMat($_GET["aid"],$rep["mid"]);
	if ($material) foreach ($material as $zeile) {
		$mat.=$zeile["menge"]." x ".substr($zeile["description"],0,70)."\n";
	};
	require("fpdf.php");
	require("fpdi.php");
	$pdf = new FPDI('P','mm','A4');
	$seiten=$pdf->setSourceFile("vorlage/repauftrag.pdf");
	$hdl=$pdf->ImportPage(1);
	$pdf->addPage();
	$pdf->useTemplate($hdl);
        $pdf->SetFont($repfont,'B',$repsizeL);
        $pdf->Text($repname[x],$repname[y],utf8_decode($firma["name"]));
        $pdf->Text($repstr[x],$repstr[y],utf8_decode($firma["street"]));
        $pdf->Text($report[x],$report[y],$firma["zipcode"]." ".utf8_decode($firma["city"]));
	$pdf->Text($repphone[x],$repphone[y],$firma["phone"]);	
	$pdf->Text($repaid[x],$repaid[y],$_GET["aid"]);

        $pdf->SetFont($repfont,'',$repsizeN);
        $pdf->Text($repwvnr[x],$repwvnr[y],$masch['contractnumber']);
        $pdf->Text($repkdnr[x],$repkdnr[y],$firma["customernumber"]);
	$pdf->Text($repdate[x],$repdate[y],date("d.m.Y"));		
	$pdf->Text($repmasch[x],$repmasch[y],utf8_decode($masch["description"]));
	$pdf->Text($repser[x],$repser[y],$masch["serialnumber"]);
	$pdf->Text($repsort[x],$repsort[y],utf8_decode($masch["standort"]));			
	$pdf->Text($repcnt[x],$repcnt[y],$masch["counter"]);			
	$pdf->Text($repinsp[x],$repinsp[y],db2date($masch["inspdatum"]));			
	$pdf->Text($repkurz[x],$repkurz[y],utf8_decode($rep["cause"]));
	$pdf->SetY($replang[x]);
	$pdf->SetX($replang[y]);
	$pdf->MultiCell(0,6,utf8_decode($rep["schaden"]),0);
	$pdf->addPage();	
	$history="Die letzten Ereignisse:\n";
	$history.=db2date($hist[0]["datum"]);$history.="     ".$hist[0]["art"]."    ";
	if ($hist[0]["art"]=="RepAuftr") { 
		preg_match("/^[0-9]+\|(.+)/",utf8_decode($hist[0]["beschreibung"]),$treffer);
		$history.=$treffer[1]."\n";
	} else { 
		$history.=$hist[0]["beschreibung"]."\n";	
	}
	$history.=db2date($hist[1]["datum"]);$history.="     ".$hist[1]["art"]."    ";
	if ($hist[1]["art"]=="RepAuftr") { 
		preg_match("/^[0-9]+\|(.+)/",utf8_decode($hist[1]["beschreibung"]),$treffer);
		$history.=$treffer[1]."\n";
	} else { 
		$history.=utf8_decode($hist[1]["beschreibung"])."\n";	
	}
	$history.=db2date($hist[2]["datum"]);$history.="     ".$hist[2]["art"]."    ";
	if ($hist[2]["art"]=="RepAuftr") { 
		preg_match("/^[0-9]+\|(.+)/",utf8_decode($hist[2]["beschreibung"]),$treffer);
		$history.=$treffer[1]."\n";
	} else { 
		$history.=utf8_decode($hist[2]["beschreibung"])."\n";	
	}

	$pdf->MultiCell($repanl[x],$repanl[y],$history."\nLetzte Reparatur:\n".$rep["reparatur"]."\n\nVerbrauchtes Material:\n".$mat,0);

	$pdf->OutPut('Reparaturauftrag_'.$_GET["aid"].'.pdf',"I");
?>
