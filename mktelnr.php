<?php
	require_once ("inc/stdLib.php");
	$sql1="select id,phone from customer where phone <> ''";
	//$sql2="select trans_id as id,shiptophone as phone from shipto where phone <> ''";
	$sql3="select id,phone from vendor where phone <> ''";
	$sql4="select id,homephone as phone from employee where homephone <> ''";
	$sql5="select cp_id as id,cp_phone1 as phone from contacts where cp_phone1 <> ''";
	$sql6="select cp_id as id,cp_phone2 as phone from contacts where cp_phone2 <> ''";
	$sql7="select cp_id as id,cp_mobile1 as phone from contacts where cp_mobile1 <> ''";
	$sql8="select cp_id as id,cp_mobile2 as phone from contacts where cp_mobile2 <> ''";
	$sql9="select cp_id as id,cp_fax as phone from contacts where cp_fax <> ''";
	$sql10="select cp_id as id,cp_homephone as phone from contacts where cp_homephone <> ''";

	$rs=$_SESSION['db']->getAll($sql1,DB_FETCHMODE_ASSOC);
	foreach($rs as $eintrag) {
		mkTelNummer($eintrag["id"],"C",array($eintrag["phone"]));
	}
	$rs=$_SESSION['db']->getAll($sql3,DB_FETCHMODE_ASSOC);
	foreach($rs as $eintrag) {
		mkTelNummer($eintrag["id"],"V",array($eintrag["phone"]));
	}
	$rs=$_SESSION['db']->getAll($sql4,DB_FETCHMODE_ASSOC);
	foreach($rs as $eintrag) {
		mkTelNummer($eintrag["id"],"E",array($eintrag["phone"]));
	}
	$rs1=$_SESSION['db']->getAll($sql5,DB_FETCHMODE_ASSOC);
	$rs2=$_SESSION['db']->getAll($sql6,DB_FETCHMODE_ASSOC);
	$rs3=$_SESSION['db']->getAll($sql7,DB_FETCHMODE_ASSOC);
	$rs4=$_SESSION['db']->getAll($sql8,DB_FETCHMODE_ASSOC);
	$rs5=$_SESSION['db']->getAll($sql9,DB_FETCHMODE_ASSOC);
	$rs6=$_SESSION['db']->getAll($sql10,DB_FETCHMODE_ASSOC);
	$rs=array_merge($rs1,$rs2);
	foreach($rs as $eintrag) {
		mkTelNummer($eintrag["id"],"P",array($eintrag["phone"]),false);
	}

?>
