<?php
	require_once("inc/stdLib.php");
    include("inc/grafik$jpg.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	if ($_POST["ok"] and $_POST["termseq"]<61) {
		$rc=saveUserStamm($_POST);
		$id=$_POST["UID"];
		$_SESSION["termbegin"]=$_POST["termbegin"];
		$_SESSION["termend"]=$_POST["termend"];
		$_SESSION["pre"]=$_POST["pre"];
		$_SESSION["kdview"]=$_POST["kdview"];
	} else if ($_POST["mkmbx"]) {
		$rc=createMailBox($_POST["Postf2"],$_POST["Login"]);
	} 
	$t = new Template($base);
	if ($_GET["id"]) {
		$fa=getUserStamm($_GET["id"]);
		$t->set_file(array("usr1" => "user1b.tpl"));
	} else {
		$fa=getUserStamm($_SESSION["loginCRM"]);
		$t->set_file(array("usr1" => "user1.tpl"));
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
    if ($jahr=="") $jahr = date("Y");
    $re = getReJahr($fa["id"],$jahr,false,true);
    $an = getAngebJahr($fa["id"],$jahr,false,true);
    $IMG=getLastYearPlot($re,$an,false);
	$t->set_var(array(
            IMG => $IMG,
			login => $fa["login"],
			name => $fa["name"],
			addr1 => $fa["addr1"],
			addr2 => $fa["addr2"],
			addr3 => $fa["addr3"],
			uid => $fa["id"],
			homephone => $fa["homephone"],
			workphone => $fa["workphone"],
			role => $fa["role"],
			notes => $fa["notes"],
			mailsign => $fa["mailsign"],
			email => $fa["email"],
			msrv =>	$fa["msrv"],
			kennw => $fa["kennw"],
			postf => $fa["postf"],
			postf2 => $fa["postf2"],
			interv => $fa["interv"],
			pre => $fa["pre"],
			kdview.$fa["kdview"] => "selected",
			abteilung => $fa["abteilung"],
			position => $fa["position"],
			termbegin => $tbeg,
			termend	=> $tend,
			termseq	=> ($fa["termseq"])?$fa["termseq"]:30,
			GRUPPE => $gruppen,
            DATUM => date('d.m.Y'),
            icalext => $fa["icalext"],
            icaldest => $fa["icaldest"],
            icalart.$fa["icalart"] => "selected",
			));
	if ($_GET["id"]) {	
		$t->set_var(array(Vertreter => $fa["vertreter"]." ".$fa["vname"]));
	} else {
		$t->set_block("usr1","Selectbox","Block");
		$select=(!empty($fa["vertreter"]))?$fa["vertreter"]:$fa["id"];
		$user=getAllUser(array(0=>true,1=>""));
		if ($user) foreach($user as $zeile) {
			$t->set_var(array(
				Sel => ($select==$zeile["id"])?" selected":"",
				vertreter	=>	$zeile["id"],
				vname	=>	$zeile["name"]
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
	}
	$t->pparse("out",array("usr1"));
?>
