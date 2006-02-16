<?php
// $Id: user3.php,v 1.3 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	if ($_POST["holen"]) {
		$msg=getCustMsg($_POST["cp_cv_id"],true);
	} else if ($_POST["delete"]) {
		$rc=delCustMsg($_POST["messages"]);
		$msg=getCustMsg($_POST["cp_cv_id"],true);
	} else if ($_POST["edit"]) {
		$data=getCustMsg($_POST["cp_cv_id"],$_POST["messages"]);
		$msg=getCustMsg($_POST["cp_cv_id"],true);
	} else if ($_POST["reset"]) {
		$msg=getCustMsg($_POST["cp_cv_id"],true);
	} else if ($_POST["sichern"]) {
		$rc=saveCustMsg($_POST);
		$msg=getCustMsg($_POST["cp_cv_id"],true);
	}
	$fid=$_POST["cp_cv_id"];
	$name=$_POST["name"];
	//echo "Data "; print_r($data);
	//echo "<br>Msg "; print_r($msg);
	$t = new Template($base);
	$t->set_file(array("msg" => "user4.tpl"));
	$t->set_var(array(
			MEN2 => ($_SESSION["Status"]<>1)?"":"<a href='user2.php' >Gruppen</a>",
			MEN4 => ($_SESSION["Status"]<>1)?"":"<a href='user4.php' >Update</a>",
			MEN5 => ($_SESSION["Status"]<>1)?"":"<a href='aufkleber_def.php?fid=".$_SESSION["loginCRM"]."' >Etiketten</a>",
			FID => $fid,
			Firma => $name,
			ID => $data["id"],
			MSG => $data["msg"],
			R1 => ($data["prio"]==1)?"checked":"",
			R2 => ($data["prio"]==2)?"checked":"",
			R3 => ($data["prio"]==3 || !$data["prio"])?"checked":"",
			));
	$t->set_block("msg","Selectbox","Block");
	if ($msg) {
		foreach($msg as $zeile) {
			if ($gruppe==$zeile["id"]) { $sel=" selected"; } else { $sel=""; };
			$t->set_var(array(
				SEL 	=> $sel,
				MID	=>	$zeile["id"],
				MSGTXT	=>	$zeile["prio"]." ".$zeile["msg"]
			));
			$t->parse("Block","Selectbox",true);
		}
	}
	$t->pparse("out",array("msg"));
?>
