<?php
// $Id$
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
		$term=getTerminData($_GET["holen"],$_GET["CUID"]);
		$data["tid"]=$_GET["holen"]; $data["grund"]=$term["cause"];$data["lang"]=$term["c_cause"];
		$data["wdhlg"]=$term["repeat"];$data["ft"]=$term["ft"];
		$data["vondat"]=db2date($term["starttag"]);$data["bisdat"]=db2date($term["stoptag"]);
        $data["privat"]=($term["privat"]=='t')?1:0;
		$DATUM=$data["vondat"];
		$data["von"]=$term["startzeit"];$data["bis"]=$term["stopzeit"];
		$user=getTerminUser($_GET["holen"]);
		if ($user) foreach ($user as $row) {
			$data["user"][]=$row["uid"];
		}
	}
    if ($_POST["search"]<>"") {
        $rs = searchTermin($_POST["search"],($_POST["uid"]>0)?$_POST["uid"]:0);
        if (count($rs)>0) {
            $rc=true;
            $ts="S";
            foreach ($rs as $t) {
                $ts.=$t["id"].",";
            }
            $ANSICHT=$ts;
        } else {
            $rc=false;
            $data["grund"]=".:not found:.";
        }
	} else if ($_POST["sichern"]) {
		if (!$_POST["ok"]) {
			$rs=checkTermin($_POST["vondat"],$_POST["bisdat"],$_POST["von"],$_POST["bis"],($_POST["tid"]>0)?$_POST["tid"]:0);
			$DATUM=$_POST["vondat"];
			if (count($rs)>0) {
				$rc=true;
				$ts="K"; 
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
			$DATUM=$_POST["vondat"];
		} else {
			$ok="<input type='checkbox' name='ok' value='1'>Konflikte, trozdem eintragen<br>";
			$data["vondat"]=$_POST["vondat"];$data["von"]=$_POST["von"];
			$data["bisdat"]=$_POST["bisdat"];$data["bis"]=$_POST["bis"];
			$data["grund"]=$_POST["grund"];$data["lang"]=$_POST["lang"];
			$data["ft"]=$_POST["ft"];$data["wdhlg"]=$_POST["wdhlg"];
			$data["user"]=$_POST["user"]; $data["tid"]=$_POST["tid"];
            $data["privat"]=$_POST["privat"];
			$DATUM=$_POST["vondat"];
		};
	}
	$mit=getAllUser(array(0=>true,1=>"%"));
	$grp=getGruppen();
	$selusr=getUsrNamen($data["user"]);
	// $data=$_POST;
	if (!$Tag) $Tag=date("d");
	if (!$Monat)$Monat=date("m");
	if (!$Jahr) $Jahr=date("Y");
	if (!$data["vondat"]) $data["vondat"]="$Tag.$Monat.$Jahr";
	$t = new Template($base);
	$t->set_file(array("term" => "termin.tpl"));
	$t->set_block("term","User","Block");
	if ($mit) foreach($mit as $zeile) {
		if ($zeile["id"]==$_SESSION["loginCRM"]) $name=$zeile["name"];
		$t->set_var(array(
			USRID	=>	"E".$zeile["id"],
			USRNAME	=>	($zeile["name"])?$zeile["name"]:$zeile["login"]
		));
		$t->parse("Block","User",true);
	}
	if($grp) {
		foreach($grp as $zeile) {
			if ($zeile["id"]==$_SESSION["loginCRM"]) $name=$zeile["name"];
			$t->set_var(array(
				USRID	=>	"G".$zeile["grpid"],
				USRNAME	=>	"G:".$zeile["grpname"]
			));
			$t->parse("Block","User",true);
		}
	}
	if ($selusr) {
		$t->set_block("term","Selusr","BlockU");
		foreach($selusr as $row) {
			$t->set_var(array(
				UID => $row["id"],
				UNAME => ($row["name"])?$row["name"]:$row["login"]
			));
			$t->parse("BlockU","Selusr",true);
		}
	}
	$t->set_block("term","CalUser","BlockV");
    $tmpid=($_GET["CUID"])?$_GET["CUID"]:$_SESSION["loginCRM"];
	$t->set_var(array(
			CUID => -1,
			CUNAME => "Alle"
	));
	$t->parse("BlockV","CalUser",true);
	if ($mit) foreach($mit as $row) {
		$t->set_var(array(
			CUID => $row["id"],
            CUIDSEL => ($row["id"]==$tmpid)?"selected":"",
			CUNAME => ($row["name"])?$row["name"]:$row["login"]
		));
		$t->parse("BlockV","CalUser",true);
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
	for ($i=2004; $i<2020; $i++) {
		$t->set_var(array(
			JV => $i,
			JK => $i,
			JS => ($i==$Jahr)?" selected":""
		));
		$t->parse("BlockJ","Jahre",true);
	}
	$t->set_block("term","Time1","Block1");
	$t->set_block("term","Time2","Block2");
	for ($i=$_SESSION["termbegin"]; $i<=$_SESSION["termend"]; $i++){
		$j=sprintf("%02d",$i);
		$t->set_var(array(
			tval1	=>	$j.":00", 
			tkey1	=>	$j.":00",
			tsel1	=>  ($data["von"]=="$j:00")?" selected":"",
		));
		$t->parse("Block1","Time1",true);
		$t->parse("Block2","Time2",true);
        if ($_SESSION["termseq"]>0) {
            for ($s = $_SESSION["termseq"] ; $s < 60; $s+=$_SESSION["termseq"]) {
                $sq = sprintf("%02d",$s);
                $t->set_var(array(
                    tval1	=>	$j.":".$sq, 
                    tkey1	=>	$j.":".$sq,
                    tsel1	=>  ($data["von"]=="$j:$sq")?" selected":""
                ));
                $t->parse("Block1","Time1",true);
                $t->parse("Block2","Time2",true);
            }
	    }
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
        CHKPRIVAT => ($data["privat"]==1)?"checked":"",
		VONDAT => $data["vondat"],
		BISDAT => $data["bisdat"],
		VON => $data["von"],
		BIS => $data["bis"],
		GRUND => $data["grund"],
		LANG => $data["lang"],
		FT => ($data["ft"])?" checked":"",
		ANSICHT => $ANSICHT,
		DATUM => $DATUM
	));
	$t->Lpparse("out",array("term"),$_SESSION["lang"],"work");
?>
