<?
// $Id: liefer4.php,v 1.3 2005/11/02 10:37:52 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/persLib.php");
	include("inc/crmLib.php");
	include("inc/LieferLib.php");
	$fid=($_GET["fid"])?$_GET["fid"]:$_POST["fid"];
	$pid=($_GET["pid"])?$_GET["pid"]:$_POST["pid"];
	if ($_POST["sichern"]) {
		$id=($pid)?$pid:$fid;
		saveDokument($_FILES,$_POST["caption"],date("Y-m-d"),$id,$_SESSION["loginCRM"]);
	}
	if (!empty($fid)) {
		$fa=getLieferStamm($fid);
		if (!empty($pid)){
			$co=getKontaktStamm($pid);
			$name=$co["cp_givenname"]." ".$co["cp_name"];
			$plz=$co["cp_zipcode"];
			$ort=$co["cp_city"];
			$firma=$fa["name"];
			$id=$pid;
		} else {
			$name=$fa["name"];
			$plz=$fa["zipcode"];
			$ort=$fa["city"];
			$firma="Lieferantendokumente";
			$id=$fid;
		}
		$link1="liefer1.php?id=".$fid;
		$link2="liefer2.php?fid=".$fid;
		$link3="liefer3.php?fid=".$fid;
		$link4="liefer4.php?fid=".$fid;
	} else {
		$id=$pid;
		$co=getKontaktStamm($pid);
		$name=$co["cp_givenname"]." ".$co["cp_name"];
		$plz=$co["cp_zipcode"];
		$ort=$co["cp_city"];
		$firma="Einzelperson";
		$link1="#";
		$link2="liefer2.php?id=$pid";
		$link3="#";
		$link4="liefer4.php?pid=$pid";
	}
	$t = new Template($base);
	$t->set_file(array("doc" => "liefer4.tpl"));
	$t->set_var(array(
			FID => $fid,
			PID => $pid,
			Link1 => $link1,
			Link2 => $link2,
			Link3 => $link3,
			Link4 => $link4,
			LInr => $fa["vendornumber"],
			Name => $name,
			Plz => $plz,
			Ort => $ort,
			Firma => $firma
			));
	$t->set_block("doc","Liste","Block");
	$user=getVorlagen();
	$i=0;
	if (!$user) $user[0]=array(docid=>0,vorlage=>"Keine Vorlagen eingestellt",applikation=>"O");
	if ($user) foreach($user as $zeile) {
		$t->set_var(array(
			LineCol	=> $bgcol[($i%2+1)],
			ID =>	$zeile["docid"],
			Bezeichnung =>	$zeile["vorlage"],
			Appl	=>	($zeile["applikation"]=="O")?"OOo":"RTF",
		));
		$i++;
		$t->parse("Block","Liste",true);
	}
	$files=liesdir($_SESSION["mansel"]."/".$id);
	$t->set_block("doc","Liste2","Block2");
	if ($files) foreach ($files as $row) {
		$t->set_var(array(
				key => $row["date"]." &nbsp; ".$row["name"],
				val => $_SESSION["mansel"]."/".$id."/".$row["name"]
		));
		$t->parse("Block2","Liste2",true);
	}
	$t->pparse("out",array("doc"));

?>
