<?php
// $Id: termin.php,v 1.4 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	$ok="<input type='hidden' name='ok' value=''>";
	unset($data);
	$ANSICHT="T";
	$data["ft"]=1;
	$data["user"][]="E".$_SESSION["loginCRM"];
	if ($_GET["holen"]) {
		$term=getTerminData($_GET["holen"]);
		$data["tid"]=$_GET["holen"]; $data["grund"]=$term["cause"];$data["lang"]=$term["c_cause"];
		$data["wdhlg"]=$term["repeat"];$data["ft"]=$term["ft"];
		$data["vondat"]=db2date($term["starttag"]);$data["bisdat"]=db2date($term["stoptag"]);
		$data["von"]=$term["startzeit"];$data["bis"]=$term["stopzeit"];
		$user=getTerminUser($_GET["holen"]);
		if ($user) foreach ($user as $row) {
			$data["user"][]=$row["uid"];
		}
	}
	if ($_POST["sichern"]) {
		if (!$_POST["ok"]) {
			$rs=checkTermin($_POST["vondat"],$_POST["bisdat"],$_POST["von"],$_POST["bis"]);
			if (count($rs)>0) {
				$rc=true;
				$ts="K"; 
				print_r($rs);
				foreach ($rs as $t) {
					$ts.=$t["id"].",";
				}
				$ANSICHT=$ts;
			} else {
				$rc=false;
			}
		} else { $rc=false; };
		if (!$rc) {
			$rc=saveTermin($_POST);
		} else {
			$ok="<input type='checkbox' name='ok' value='1'>Konflikte, trozdem eintragen<br>";
			$data["vondat"]=$_POST["vondat"];$data["von"]=$_POST["von"];
			$data["bisdat"]=$_POST["bisdat"];$data["bis"]=$_POST["bis"];
			$data["grund"]=$_POST["grund"];$data["lang"]=$_POST["lang"];
			$data["ft"]=$_POST["ft"];$data["wdhlg"]=$_POST["wdhlg"];
			$data["user"]=$_POST["user"]; $data["tid"]=$_POST["tid"];
		};
	}
	$mit=getAllUser(array(0=>true,1=>"%"));
	$grp=getGruppen();
	$selusr=getUsrNamen($data["user"]);
	// $data=$_POST;
	if (!$Tag) $Tag=date("d");
	if (!$Monat)$Monat=date("m");
	if (!$Jahr) $Jahr=date("Y");
	$t = new Template($base);
	$t->set_file(array("term" => "termin.tpl"));
	$t->set_block("term","User","Block");
	if ($mit) foreach($mit as $zeile) {
		if ($zeile["id"]==$loginCRM) $name=$zeile["name"];
		$t->set_var(array(
			USRID	=>	"E".$zeile["id"],
			USRNAME	=>	$zeile["name"]
		));
		$t->parse("Block","User",true);
	}
	if($grp) {
		foreach($grp as $zeile) {
			if ($zeile["id"]==$loginCRM) $name=$zeile["name"];
			$t->set_var(array(
				USRID	=>	"G".$zeile["grpid"],
				USRNAME	=>	$zeile["grpname"]
			));
			$t->parse("Block","User",true);
		}
	}
	if ($selusr) {
		$t->set_block("term","Selusr","BlockU");
		foreach($selusr as $row) {
			$t->set_var(array(
				UID => $row["id"],
				UNAME => $row["name"]
			));
			$t->parse("BlockU","Selusr",true);
		}
	}
	$t->set_block("term","Tage","BlockT");
	for ($i=1; $i<32; $i++) {
		$t->set_var(array(
			TV => sprintf("%02d",$i),
			TK => $i,
			TS => ($i==$Tag)?" selected":""
		));
		$t->parse("BlockT","Tage",true);
	}
	$monthday=array(1=>"Januar",2=>"Februar",3=>"M&auml;rz",4=>"April",5=>"Mai",6=>"Juni",
			7=>"Juli",8=>"August",9=>"September",10=>"Oktober",11=>"November",12=>"Dezember");
	$t->set_block("term","Monat","BlockM");
	for ($i=1; $i<13; $i++) {
		$t->set_var(array(
			MV => sprintf("%02d",$i),
			MK => $monthday[$i],
			MS => ($i==$Monat)?" selected":""
		));
		$t->parse("BlockM","Monat",true);
	}
	$t->set_block("term","Jahre","BlockJ");
	for ($i=2004; $i<2010; $i++) {
		$t->set_var(array(
			JV => $i,
			JK => $i,
			JS => ($i==$Jahr)?" selected":""
		));
		$t->parse("BlockJ","Jahre",true);
	}
	$t->set_block("term","Time1","Block1");
	for ($i=$_SESSION["termbegin"]; $i<=$_SESSION["termend"]; $i++){
		$j=sprintf("%02d",$i);
		$t->set_var(array(
			tval1	=>	$j.":00", 
			tkey1	=>	$j.":00",
			tsel1	=>  ($data["von"]=="$j:00")?" selected":"",
			tval2	=>	$j.":30", 
			tkey2	=>	$j.":30",
			tsel2	=>  ($data["von"]=="$j:30")?" selected":"", 
		));
		$t->parse("Block1","Time1",true);
	}
	$t->set_block("term","Time2","Block2");
	for ($i=$_SESSION["termbegin"]; $i<=$_SESSION["termend"]; $i++){
		$j=sprintf("%02d",$i);
		$t->set_var(array(
			tval1	=>	$j.":00", 
			tkey1	=>	$j.":00",
			tsel1	=>  ($data["bis"]=="$j:00")?" selected":"",
			tval2	=>	$j.":30", 
			tkey2	=>	$j.":30",
			tsel2	=>  ($data["bis"]=="$j:30")?" selected":"",
		));
		$t->parse("Block2","Time2",true);
	}
	$rpt=array("0"=>"einmalig","1"=>"t&auml;glich","2"=>"2-t&auml;gig","7"=>"w&ouml;chentlich","14"=>"2-w&ouml;chentlich","30"=>"monatlich","365"=>"j&auml;hrlich");
	$t->set_block("term","repeat","Block3");
	while(list($key,$val) = each($rpt)){
		$t->set_var(array(
			RPTV => $val,
			RPTK => $key,
			RPTS => ($data["wdhlg"]==$key)?" selected":""
		));
		$t->parse("Block3","repeat",true);
	}
	$t->set_var(array(
		uid => $_SESSION["loginCRM"],
		TID => $data["tid"],
		TT => $Tag,
		MM => $Monat,
		YY => $Jahr,
		OK => $ok,
		VONDAT => $data["vondat"],
		BISDAT => $data["bisdat"],
		VON => $data["von"],
		BIS => $data["bis"],
		GRUND => $data["grund"],
		LANG => $data["lang"],
		FT => ($data["ft"])?" checked":"",
		ANSICHT => $ANSICHT
	));
	$t->pparse("out",array("term"));
?>
