<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	if ($_POST["ok"]) {
		$rc=saveUserStamm($_POST);
		$id=$_POST["UID"];
		$_SESSION["termbegin"]=$_POST["termbegin"];
		$_SESSION["termend"]=$_POST["termend"];
		$_SESSION["pre"]=$_POST["pre"];
		$_SESSION["lang"]=$_POST["lang"];
		$_SESSION["kdview"]=$_POST["kdview"];
	} else if ($_POST["mkmbx"]) {
		$rc=createMailBox($_POST["Postf2"],$_POST["Login"]);
	} 
	$fa=getUserStamm($_SESSION["loginCRM"]);
	$t = new Template($base);
	if ($fa["Login"]==$_SESSION["employee"]) {
		$t->set_file(array("usr1" => "user1.tpl"));
	} else {
		$t->set_file(array("usr1" => "user1b.tpl"));
	}

	if ($fa) foreach ($fa["gruppen"] as $row) {
		$gruppen.=$row["grpname"]."<br>";
	}
	$i=($fa["termbegin"]>=0 && $fa["termbegin"]<=23)?$fa["termbegin"]:8;
	$j=($fa["termend"]>=0 && $fa["termend"]<=23 && $fa["termend"]>$fa["termbegin"])?$fa["termend"]:19;
	for ($z=0; $z<24; $z++) {
		$tbeg.="<option value=$z".(($i==$z)?" selected":"").">$z";
		$tend.="<option value=$z".(($j==$z)?" selected":"").">$z";
	}
	$t->set_var(array(
			Login => $fa["Login"],
			Name => $fa["Name"],
			Strasse => $fa["Strasse"],
			Plz => $fa["Plz"],
			Ort => $fa["Ort"],
			UID => $_SESSION["loginCRM"],
			Tel1 => $fa["Tel1"],
			Tel2 => $fa["Tel2"],
			Regel => $fa["Regel"],
			Bemerkung => $fa["Bemerkung"],
			MailSign => $fa["MailSign"],
			eMail => $fa["eMail"],
			Msrv =>	$fa["Msrv"],
			Kennw => $fa["Kennw"],
			Postf => $fa["Postf"],
			Postf2 => $fa["Postf2"],
			Interv => $fa["Interv"],
			Pre => $fa["Pre"],
			kdview.$fa["kdview"] => "selected",
			Abteilung => $fa["Abteilung"],
			Position => $fa["Position"],
			termbegin => $tbeg,
			termend	=> $tend,
			GRUPPE => $gruppen
			));
	$t->set_block("usr1","Selectbox","Block");
	$select=(!empty($fa["Vertreter"]))?$fa["Vertreter"]:$fa["Id"];
	$user=getAllUser(array(0=>true,1=>""));
	if ($user) foreach($user as $zeile) {
		$t->set_var(array(
			Sel => ($select==$zeile["id"])?" selected":"",
			Vertreter	=>	$zeile["id"],
			VName	=>	$zeile["name"]
		));
		$t->parse("Block","Selectbox",true);
	}
	$t->set_block("usr1","SelectboxB","BlockB");
	$ALabels=getLableNames();
	if ($ALabels) foreach ($ALabels as $data) {
			$t->set_var(array(
				FSel => ($data["id"]==$fa["etikett"])?" selected":"",
				LID	=>	$data["id"],
				FTXT =>	$data["name"]
			));
			$t->parse("BlockB","SelectboxB",true);
	}
	$t->set_block("usr1","SelectboxC","BlockC");
	$language=array("de"=>"Deutsch","en"=>"English","fr"=>"France");
	while(list($key,$val) = each($language)) {
		$t->set_var(array(
			LSel => ($key==$fa["countrycode"])?" selected":"",
			LID  =>	$key,
			LTXT =>	$val
		));
		$t->parse("BlockC","SelectboxC",true);
	}
	$t->set_block("usr1","Liste","BlockD");
	$i=0;
	if ($items) foreach($items as $col){
		$t->set_var(array(
			IID => $col["id"],
			LineCol	=> $bgcol[($i%2+1)],
			Datum	=> db2date(substr($col["calldate"],0,10)),
			Zeit	=> substr($col["calldate"],11,5),
			Name	=> $col["cp_name"],
			Betreff	=> $col["cause"],
			Nr		=> $col["id"]
		));
		$t->parse("BlockD","Liste",true);
		$i++;
	}
	
	$t->pparse("out",array("usr1"));
?>
