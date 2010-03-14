<?php
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/persLib.php");
	$link1="dokument1.php";
	$link2="dokument2.php";
	$link3="";
	$link4="";
	$t = new Template($base);
	$t->set_file(array("doc" => "dokument1.tpl"));
	$t->set_var(array(
			Link1 => $link1,
			Link2 => $link2,
			Link3 => $link3,
			Link4 => $link4
			));
	$t->set_block("doc","Liste","Block");
	$user=getVorlagen();
	$i=0;
	if (!$user) $user[0]=array(docid=>0,vorlage=>"Keine Vorlagen eingestellt",applikation=>"O");
	if ($user) foreach($user as $zeile) {
		switch ($zeile["applikation"]) {
			case "T": 
				$format = "Tex";
				break;
			case "O": 
				$format = "OOo";
				break;
			case "R": 
				$format = "RTF";
				break;
			case "B": 
				$format = "BIN";
				break;
			default: 
				$format = "n/a";
				break;
		}
		$t->set_var(array(
			LineCol	=> $bgcol[($i%2+1)],
			did =>	$zeile["docid"],
			Bezeichnung =>	$zeile["vorlage"],
			Appl	=>	$format,
		));
		$i++;
		$t->parse("Block","Liste",true);
	}
	$t->pparse("out",array("doc"));

?>
