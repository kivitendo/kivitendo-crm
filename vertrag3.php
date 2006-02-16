<?
// $Id: vertrag3.php,v 1.4 2005/11/02 11:33:06 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/wvLib.php");

	unset($maschinen);
	$t = new Template($base);	
	$template="vertrag3.tpl";
	$vid=($_POST["vid"])?$_POST["vid"]:$_GET["vid"];
	if ($_POST["stat"]) {
		$jahr=($_POST["jahr"])?$_POST["jahr"]:date("Y");
		$data=getVertragStat($vid,$jahr);
		$vertrag=getVertrag($vid);
		$SM=substr($vertrag["anfangdatum"],5,2);
		$SJ=substr($vertrag["anfangdatum"],0,4);
		if ($SJ<$jahr) { $einnahme=$vertrag["betrag"]*12; } 
		else { $EM=date("m"); $einnahme=$vertrag["betrag"]*($EM-$SM+1); };
		$template="vertragS.tpl";	
		$t->set_file(array("vert" => $template));
		$t->set_block("vert","Liste","Block1");
		$m=$data[0]["mid"]; $mid="($m) ".$data[0]["artnr"]; 
		$a=$data[0]["aid"]; $sum=0; $zsum=0; $gt=0; $sernr=$data[0]["serialnumber"];
		$mid="[<a href=\"maschine1.php?sernr=".$sernr."\">$m</a>] ";
		$masch=$data[0]["partnumber"]." #$sernr";
		if($data) foreach($data as $zeile) {
			if ($zeile["mid"]<>$m) { 
				$t->set_var(array(
					MID		=> $mid,
					masch		=> $masch,
					RID		=> "[<a href=\"repauftrag.php?hole={$a}\">{$a}</a>]",
					BETRAG	=> sprintf("%0.2f",$zsum),
					SUMME	=> ""
				));
				$sum+=$zsum;
				$t->parse("Block1","Liste",true);
				$zsum=$zeile["summe"];
				$a=$zeile["aid"];
				$m=$zeile["mid"];
				$mid="[<a href=\"maschine1.php?sernr=".$sernr."\">$m</a>] ".$data[0]["partnumber"]." #$sernr";
				$sernr=$zeile["serialnumber"];
				$t->set_var(array(
					MID		=> "", 	RID		=> "", 	BETRAG	=> "", masch => "",
					SUMME	=> sprintf("%0.2f",$sum)
				));
				$t->parse("Block1","Liste",true);
				$gt+=$sum;
				$sum=0;
			} else 	if ($zeile["aid"]<>$a) {
				$t->set_var(array(
					MID		=> $mid,
					masch		=> $masch,
					RID		=> "[<a href=\"repauftrag.php?hole={$a}\">{$a}</a>]",
					BETRAG	=> sprintf("%0.2f",$zsum),
					SUMME	=> ""
				));
				$sum+=$zsum;
				$t->parse("Block1","Liste",true);
				$zsum=$zeile["summe"];
				$a=$zeile["aid"];
				$mid="";
			} else { 
				$zsum+=$zeile["summe"];
			}
		}	
		$t->set_var(array(
					MID		=> $mid,
					masch		=> $masch,
					RID		=> "[<a href=\"repauftrag.php?hole={$a}\">{$a}</a>]",
					BETRAG	=> sprintf("%0.2f",$zsum),
					SUMME	=> ""
				));
				$sum+=$zsum;
				$t->parse("Block1","Liste",true);
		$t->set_var(array(
			MID		=> "",	RID		=> "",	BETRAG	=> "", masch => "",
			SUMME	=> sprintf("%0.2f",$sum)
		));
		$t->parse("Block1","Liste",true);
		$gt+=$sum;
		$diff=$einnahme-$gt;
		$diff=($diff>0)?"&Uuml;berschuss ".sprintf("%0.2f",$diff):"Mehrkosten ".sprintf("%0.2f",$diff*-1);
		$t->set_var(array(
			VID => $vid,
			jahr => $jahr,
			VertragNr => $vertrag["contractnumber"],
			FID => $vertrag["customer_id"],
			Firma => $vertrag["name"],
			betrag => sprintf("%0.2f",$vertrag["betrag"]),		
			anfangdatum => db2date($vertrag["anfangdatum"]),
			endedatum => db2date($vertrag["endedatum"]),			
			kosten => sprintf("%0.2f",$gt),
			einnahme => sprintf("%0.2f",$einnahme),
			diff => $diff
		));		
		$t->pparse("out",array("vert"));
		exit;
	}

	if ($_POST["ok"]) {
		if ($_POST["vid"]) {
			$vid=updateVertrag($_POST);
		} else  {
			if (!empty($_POST["maschinen"][0][0])) $vid=saveNewVertrag($_POST);
		}
	}	
	$vorlagen=getWVorlagen();
	if ($vid) {

		$template="vertrag3e.tpl";
		$vertrag=getVertrag($vid);
		$maschinen=getVertragMaschinen($vertrag["contractnumber"]);
	}

	$t->set_file(array("vert" => $template));

	$t->set_var(array(
		VID => $vid,
		vorlage_old => $vertrag["customer_id"]."/".$vertrag["template"],
		vorlage => $vertrag["template"],
		Notiz => $vertrag["bemerkung"],
		FID => $vertrag["customer_id"],
		Firma => $vertrag["name"],
		betrag => sprintf("%0.2f",$vertrag["betrag"]),		
		anfangdatum => db2date($vertrag["anfangdatum"]),
		endedatum => db2date($vertrag["endedatum"]),						
		VertragNr => $vertrag["contractnumber"]
	));

	$t->set_block("vert","Vorlage","Block1");
	if($vorlagen) foreach($vorlagen as $zeile) {
		if ($zeile==$vertrag["template"]) { $sel=" selected"; } else { $sel=""; };
		$t->set_var(array(
			Vsel 	=> $sel,
			Vertrag	=>	$zeile
		));
		$t->parse("Block1","Vorlage",true);
	}
	$t->set_block("vert","Maschinen","Block2");	
	$i=0;

	if($maschinen) foreach($maschinen as $zeile) {
		$t->set_var(array(
			I => ++$i,
			MID	=>	$zeile["mid"],
			Maschine	=>	$zeile["partnumber"]." | ".$zeile["serialnumber"],
			SerNr => $zeile["serialnumber"],
			Standort => $zeile["standort"]
		));
		$t->parse("Block2","Maschinen",true);
	}
	$t->pparse("out",array("vert"));

?>
