<?
// $Id$
require_once("inc/stdLib.php");


function getAllPostIt($id) {
global $db;
	$sql="select * from postit where employee=$id order by date";
	$rs=$db->getAll($sql);
	return $rs;
}
function getOnePostIt($id) {
global $db;
	$sql="select * from postit where id=$id";
	$rs=$db->getAll($sql);
	if ($rs) {
		$data=$rs[0];
		$data["notes"]=stripslashes($data["notes"]);
		return $data;
	} else {
		return false;
	}
}
function savePostIt($data) {
global $db;
	if (!$data["id"]) {
		$newID=uniqid (rand());
		$sql="insert into postit (employee,date,cause) values (".$_SESSION["loginCRM"].",now(),'$newID')";
	        $rc=$db->query($sql);
		if ($rc) {
                	$sql="select id from postit where cause = '$newID'";
	                $rs=$db->getAll($sql);
		} else {
			return false;
		}
                if ($rs) {
                        $data["id"]=$rs[0]["id"];
                } else {
                        return false;
                }
	}
	$sql="update postit set cause='%s',notes='%s' where id = %d";
	$rc=$db->query(sprintf($sql,substr($data["cause"],0,100),addslashes($data["notes"]),$data["id"]));
	return $rc;
}
function DelPostIt($id) {
global $db;
	$sql="delete from postit where id=$id";
	$rc=$db->query($sql);
	return $rc;
}
if ($_POST["save"]) {
	if ($_POST["cause"]) $rc=savePostIt($_POST);
	if (!$rc) $data=$_POST;
} else if ($_GET["hole"]) {
	$data=getOnePostIt($_GET["hole"]);
} else if ($_POST["delete"]) {
	if ($_POST["id"]) $rc=delPostIt($_POST["id"]);
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head><title>Firma Stamm</title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function PopUp() {
		f1=open("postit.php?popup=1","PostIt","width=600,height=600");
	}
	//-->
	</script>
	</head>
<body>
<table>
<?
$liste=getAllPostIt($_SESSION["loginCRM"]);
if ($liste) foreach($liste as $row) {
	echo "<tr class='smal'><td>";
	echo db2date(substr($row["date"],0,10))." ".substr($row["date"],11,5);
	echo "</td><td>&nbsp;[<a href='postit.php?hole=".$row["id"]."'>".$row["cause"]."</a>]</td></tr>\n";
};
?>
</table>
<form name="postit" method="post" action="postit.php">
<input type="hidden" name="id" value="<?= $data["id"] ?>">
<input type="text" name="cause" size="75" maxlength="100" value="<?= $data["cause"] ?>"><br />
<textarea rows="7" cols="85" name="notes"><?= $data["notes"] ?></textarea><br />
<input type="submit" name="save" value="sichern">&nbsp;
<input type="submit" name="clear" value="clear">&nbsp;
<input type="submit" name="delete" value="l&ouml;schen">&nbsp;
<? if ($_GET["popup"]==1) { ?>
<input type="button" name="ppp" value="Close" onCLick="self.close();">
<? }  else { ?>
<input type="button" name="ppp" value="Pop Up" onCLick="PopUp();">
<? } ?>
</from>
</body>
</html>
