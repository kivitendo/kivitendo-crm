<?php
// $Id$
require_once("inc/stdLib.php");
$popup=($_GET["popup"])?$_GET["popup"]:0;

function getAllPostIt($id) {
	$sql = "select * from postit where employee=$id order by date";
	$rs  = $_SESSION['db']->getAll($sql);
	return $rs;
}
function getOnePostIt($id) {
	$sql = "select * from postit where id=$id";
	$rs  = $_SESSION['db']->getAll($sql);
	if ($rs) {
		$data=$rs[0];
		$data["notes"]=stripslashes($data["notes"]);
		return $data;
	} else {
		return false;
	}
}
function savePostIt($data) {
	if (!$data["id"]) {
		$newID = uniqid (rand());
		$rc    = $_SESSION['db']->insert(postit,array('employee','date','cause'),array($_SESSION["loginCRM"],'now()',$newID));
		if ($rc) {
                	$sql = "select id from postit where cause = '$newID'";
	                $rs  = $_SESSION['db']->getOne($sql);
		} else {
			return false;
		}
                if ($rs) {
                        $data["id"] = $rs["id"];
                        $rc = $_SESSION['db']->commit();
                } else {
                        return false;
                }
	};
        $sql = "UPDATE postit SET notes = '".$data["notes"]."',cause='".substr($data["cause"],0,100)."' WHERE id = ".$data['id'];
        $rc = $_SESSION['db']->query($sql);
	return $rc;
}
function DelPostIt($id) {
	$sql = "delete from postit where id=$id";
	$rc  = $_SESSION['db']->query($sql);
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
$menu = $_SESSION['menu'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<title>
<?php echo  translate(".:LxO:.","work"); 
		echo  translate(".:postit:.","work"); ?>
</title>
<?php 
    $menu = $_SESSION['menu'];
    $head = mkHeader();	
    echo $menu['stylesheets'];
    echo $menu['javascripts'];
    echo $head['FULLCALCSS'];
	 echo $head['JQUERY'];   
    echo $head['JQUERYUI'];
    echo $head['THEME'];
?>
	<script language="JavaScript">
	<!--
	function PopUp() {
		f1=open("postit.php?popup=1","PostIt","width=600,height=400");
	}
	//-->
	</script>
	</head>
<body onLoad="if (1==<?php echo  $popup ?>) window.resizeTo(600,400);">
<?php echo $menu['pre_content'];
      echo $menu['start_content'];?>
<br />
<p class="listtop"><?php echo  translate(".:notes:.","work"); ?></p>
<table >
<?php
$liste=getAllPostIt($_SESSION["loginCRM"]);
if ($liste) foreach($liste as $row) {
	echo "<tr class='klein'><td>";
	echo db2date(substr($row["date"],0,10))." ".substr($row["date"],11,5);
	echo "</td><td>&nbsp;[<a href='postit.php?hole=".$row["id"]."'>".$row["cause"]."</a>]</td></tr>\n";
};
?>
</table>
<form name="postit" method="post" action="postit.php">
<input type="hidden" name="id" value="<?php echo  $data["id"] ?>">
<input type="text" name="cause" size="90" maxlength="100" value="<?php echo  $data["cause"] ?>"><br />
<textarea class="normal" rows="7" cols="80" name="notes"><?php echo  $data["notes"] ?></textarea><br />
<input type="submit" class="sichern" name="save" value="<?php echo  translate(".:save:.","work"); ?>">&nbsp;
<input type="submit" class="clear" name="clear" value="<?php echo  translate(".:clear:.","work"); ?>">&nbsp;
<input type="submit" class="sichernneu" name="delete" value="<?php echo  translate(".:delete:.","work"); ?>">&nbsp;
<?php if ($_GET["popup"]==1) { ?>
<input type="button" name="ppp" value="<?php echo  translate(".:close:.","work"); ?>" onCLick="self.close();">
<?php }  else { ?>
<input type="button" name="ppp" value="<?php echo  translate(".:popup:.","work"); ?>" onCLick="PopUp();">
<?php } ?>
</form>
<?php echo $menu['end_content']; ?>
</body>
</html>
