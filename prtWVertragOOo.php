<?
// $Id: prtWVertragOOo.php,v 1.3 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/FirmaLib.php");	
	include("inc/wvLib.php");
	$rep=suchVertrag($_GET["aid"]);
	$rep=$rep[0];
	$masch=getVertragMaschinen($rep["contractnumber"]);
	$firma=getFirmaStamm($masch["customer_id"]);
	print_r($firma);
	include('inc/phpOpenOffice.php');
	$doc = new phpOpenOffice();
	$doc->loadDocument("vorlage/wv".$rep["template"]);
	$vars= array();
	$vars["NAME"]=$firma["name"];
	$vars["STRASSE"]=$firma["street"];
	$vars["PLZ"]=$firma["zipcode"];
	$vars["ORT"]=$firma["city"];
	$vars["BEMERKUNGEN"]=$rep["bemerkung"];
	$vars["NUMMER"]=$rep["aid"];
	$vars["DATUM"]=$rep["datum"];
	$vars["KNDR"]=$rep["customer_id"];
	$vars["EURO"]=$rep["betrag"];
	foreach ($masch as $row) {
		$vars["MASCHINEN"].=$row["description"]." #".$row["serialnumber"]."\n".$row["standort"]."\n";
	}
	$doc->parse($vars);
	$pre=date("YmdHi");	
	$doc->prepsave($pre.substr($rep["template"],0,-4));

	$data["CID"]=$masch["customer_id"];
	$data["CRMUSER"]=$_SESSION["loginCRM"];
	insFormDoc($data,$pre.$rep["template"]);
	//$doc->clean();
	//$knopf="Dokument erstellt: <a href='./".$_SESSION["mansel"]."/".$data["CID"]."/".$pre.$rep["template"]."'>&lt;shift&gt;+&lt;klick&gt;</a>";
	$knopf="Dokument erstellt: <a href='./tmp/".$pre.$rep["template"]."'>&lt;shift&gt;+&lt;klick&gt;</a>";
	echo $knopf;
?>
