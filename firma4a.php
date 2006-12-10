<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/persLib.php");
	include("inc/FirmenLib.php");
	$pid=($_GET["pid"])?$_GET["pid"]:$_POST["pid"];
	$fid=($_GET["fid"])?$_GET["fid"]:$_POST["fid"];
	$did=($_GET["did"])?$_GET["did"]:$_POST["did"];
	$datum=date("d.m.Y");
	$zeit=date("H:i");
	$knopf="<input type='submit' name='erstellen' value='Erstellen'>";
	if ($_POST["erstellen"]) {
		$docdata=getDOCvorlage($_POST["docid"]);
		if ($docdata["document"]["applikation"]=="O") {
			include('inc/phpOpenOffice.php');
			$doc = new phpOpenOffice();
			$fname=substr($docdata["document"]["file"],0,-4);
		} else if ($docdata["document"]["applikation"]=="R") {
			include('inc/phpRtf.php');
			$doc = new phpRTF();
			$fname=substr($docdata["document"]["file"],0,-4);
		} else if ($docdata["document"]["applikation"]=="B") {
			require('inc/phpBIN.php');
			$doc = new phpBIN();
			$fname=$docdata["document"]["file"];
		}
		$doc->loadDocument("vorlage/".$docdata["document"]["file"]);
		$vars= array();
		if ($docdata["felder"]) foreach($docdata["felder"] as $zeile) {
			$value=$zeile["platzhalter"];
			$name=strtoupper($value);
			$vars[$name] = $_POST[$value];
		}
		$doc->parse($vars);
		$data=$_POST;
		$data["CID"]=($pid)?$pid:$fid;
		$data["CRMUSER"]=$_SESSION["loginCRM"];
		//$doc->download(date("YmdHi").substr($docdata["document"]["file"],0,-4));
		$pre=date("YmdHi");
		$doc->prepsave($pre.$fname);
		insFormDoc($data,$pre.$docdata["document"]["file"]);
		$doc->clean();
		$knopf="Dokument erstellt: <a href='./dokumente/".$_SESSION["mansel"]."/".$data["CID"]."/".$pre.$docdata["document"]["file"]."'>&lt;shift&gt;+&lt;klick&gt;</a>";
	}

	if (empty($fid)) {
		$co=getKontaktStamm($pid);
		$anrede=$co["cp_greeting"]." ".$co["cp_title"];
		$name2=$co["cp_givenname"];
		$name1=$co["cp_name"];
		$name=$name2." ".$name1;
		$plz=$co["cp_zipcode"];
		$ort=$co["cp_city"];
		$strasse=$co["cp_street"];
		$art="Einzelperson";
		$link1="#";
		$link2="firma2.php?id=$pid";
		$link3="#";
		$link4="firma4.php?pid=$pid";
	} else {
		$fa=getFirmenStamm($fid);
		$anrede="Firma";
		$name=$fa["name"];
		$name1=$name;
		$name2=$fa["department_1"];
		$kontakt=$fa["contact"];
		$plz=$fa["zipcode"];
		$ort=$fa["city"];
		$strasse=$fa["street"];
		if (!empty($pid)){
			$co=getKontaktStamm($pid);
			$anredepers=$co["cp_greeting"];
			$anredepers.=($co["cp_title"])?" ".$co["cp_title"]:"";
			$namepers=$co["cp_givenname"]." ".$co["cp_name"];
			$plzpers=$co["cp_zipcode"]; $ortpers=$co["cp_city"]; $strassepers=$co["cp_street"];
			$art="Firma/Kontakt";
		} else {
			$art="Firmendokumente";
		}
		$link1="firma1.php?id=$fid";
		$link2="firma2.php?fid=$fid";
		$link3="firma3.php?fid=$fid";
		$link4="firma4.php?fid=$fid";
	}
	$document=getDocVorlage($did);
	$t = new Template($base);
	$t->set_file(array("doc" => "firma4a.tpl"));
	$t->set_var(array(
			FID => $fid,
			KDNR	=> $fa["customernumber"],
			PID => $pid,
			Link1 => $link1,
			Link2 => $link2,
			Link3 => $link3,
			Link4 => $link4,
			Name => $name,
			Department_1 => $department_1,
			Plz => $plz,
			Ort => $ort,
			Art => $art,
			Beschreibung => $document["document"]["beschreibung"],
			DOCID => $did,
			Knopf => $knopf
			));
	$t->set_block("doc","Liste","Block");
	$t->set_block("doc","RegEx","Block2");
	$i=0;
	if ($document["felder"]) {
	 foreach($document["felder"] as $zeile) {
		$value=strtolower($zeile["platzhalter"]);
		if ($zeile["laenge"]>60) {
			$rows=floor($zeile["laenge"]/60)+1;
			$input="<textarea cols=60 rows=$rows name='".$zeile["platzhalter"]."'>".${$value}."</textarea>";
		} else {
			$input="<input type='text' name='".$zeile["platzhalter"]."' size='".$zeile["laenge"]."' value='".${$value}."'>";
		}
		$t->set_var(array(
			EINGABE => $input,
			Feldname => $zeile["feldname"]
		));
		$t->parse("Block","Liste",true);
		$t->set_var(array(
			fld => $zeile["platzhalter"],
			regul => $zeile["zeichen"]
		));
		$t->parse("Block2","RegEx",true);
		$i++;
	 }
	}
	$t->Lpparse("out",array("doc"),$_SESSION["lang"],"firma");
?>
