<?
// $Id: dokument2.php,v 1.3 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	$did=($_GET["did"])?$_GET["did"]:$_POST["did"];
	if ($_POST["ok"]) {
		$did=saveDocVorlage($_POST,$_FILES);
	} else if ($_POST["del"]) {
		$did=delDocVorlage($_POST);
	}
	$link1="dokument1.php";
	$link2="dokument2.php?did=".$_GET["did"];
	if ($did) {
		$link3="dokument3.php?docid=".$did;
	} else {
		$link3="";
	}
	$link4="";
	if ($did) {
		$docdata=getDOCvorlage($did);
	}
	$t = new Template($base);
	$t->set_file(array("doc" => "dokument2.tpl"));
	$t->set_var(array(
			Link1 => $link1,
			Link2 => $link2,
			Link3 => $link3,
			Link4 => $link4,
			vorlage	=> $docdata["document"]["vorlage"],
			beschreibung =>	$docdata["document"]["beschreibung"],
			file =>	$docdata["document"]["file"],
			sel1 =>	($docdata["document"]["applikation"]=="O")?"checked":"",
			sel2 =>	($docdata["document"]["applikation"]=="R")?"checked":"",
			did => $did
		));
	$t->pparse("out",array("doc"));

?>
