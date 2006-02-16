<? // $Id: $
	require_once ("inc/stdLib.php");
	$sql1="select id,phone from customer where phone <> ''";
	//$sql2="select trans_id as id,shiptophone as phone from shipto where phone <> ''";
	$sql3="select id,phone from vendor where phone <> ''";
	$sql4="select cp_id as id,cp_phone1 as phone from contacts where cp_phone1 <> ''";
	$sql5="select cp_id as id,cp_phone2 as phone from contacts where cp_phone2 <> ''";
	$sql6="select id,homephone as phone from employee where homephone <> ''";

	$rs=$db->getAll($sql1,DB_FETCHMODE_ASSOC);
	foreach($rs as $eintrag) {
		mkTelNummer($eintrag["id"],"C",array($eintrag["phone"]));
	}
	$rs=$db->getAll($sql3,DB_FETCHMODE_ASSOC);
	foreach($rs as $eintrag) {
		mkTelNummer($eintrag["id"],"V",array($eintrag["phone"]));
	}
	$rs=$db->getAll($sql6,DB_FETCHMODE_ASSOC);
	foreach($rs as $eintrag) {
		mkTelNummer($eintrag["id"],"E",array($eintrag["phone"]));
	}
	$rs1=$db->getAll($sql4,DB_FETCHMODE_ASSOC);
	$rs2=$db->getAll($sql5,DB_FETCHMODE_ASSOC);
	$rs=array_merge($rs1,$rs2);
	foreach($rs as $eintrag) {
		mkTelNummer($eintrag["id"],"P",array($eintrag["phone"]),false);
	}

?>
