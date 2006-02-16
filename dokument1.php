<?
// $Id: dokument1.php,v 1.3 2005/11/02 10:37:51 hli Exp $
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
		$t->set_var(array(
			LineCol	=> $bgcol[($i%2+1)],
			did =>	$zeile["docid"],
			Bezeichnung =>	$zeile["vorlage"],
			Appl	=>	($zeile["applikation"]=="O")?"OOo":"RTF",
		));
		$i++;
		$t->parse("Block","Liste",true);
	}
	$t->pparse("out",array("doc"));

?>
