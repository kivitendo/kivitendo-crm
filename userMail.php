<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	$start=$_GET["start"];
	$id=$_GET["id"];
	$items=getAllTelCallUser($id,$start,"M");
	mkPager($items,$pager,$start,$next,$prev);
?>
<html>
	<head><title>User Stamm</title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
		<script language="JavaScript">
	<!--
		function showItem(Q,id) {
			F1=open("getCall.php?hole="+id+Q,"Caller","width=610, height=600, left=100, top=50, scrollbars=yes");
		}
	//-->
	</script>
	</head>
<body>
 	<table style="width:99%">
 		<tr><td colspan="3">&nbsp;versendete Benutzermails:</td></tr>
<?
    if ($items) foreach ($items as $col) {
        $jj++;
		if ($col["cp_email"]) {
			$email=$col["cp_email"];
			$Q="'&Q=XC&pid=".$col["pid"]."',";
		} else if ($col["cemail"]) {
			$email=$col["cemail"];
			$Q="'&Q=C&fid=".$col["cid"]."',";
		} else if ($col["vemail"]) {
			$email=$col["vemail"];
			$Q="'&Q=V&fid=".$col["vid"]."',";
		} else {
			$p=strpos($col["cause"],"|");
			$Q="'&Q=XX',";
			if ($p) {
				$email=substr($col["cause"],$p+1);
				$col["cause"]=substr($col["cause"],0,$p);
			} else {
				$email="---------";
			}
		}

?>
	<tr height="14px" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='<?= $bgcol[($jj%2+1)] ?>';" bgcolor="<?= $bgcol[($jj%2+1)] ?>" onClick="showItem(<?= $Q.$col["id"] ?>);">
		<td class="smal" width="100px"><?= db2date(substr($col["calldate"],0,10)) ?> <?= substr($col["calldate"],11,5) ?></td>
		<td class="smal le"><?= $email ?></td><td class="smal le"><?= $col["cause"] ?></td></tr>
<?		
	}
?>
		<tr><td>&nbsp;</td><td colspan="3"><a href="userMail.php?id=<?= $id ?>&start=<?= $prev ?>">&lt;&lt;</a> &nbsp; &nbsp; <a href="userMail.php?id=<?= $id ?>&start=<?= $next ?>">&gt;&gt;</a></td></tr>
	</table>
</body>
</html>
