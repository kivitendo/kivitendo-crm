<?
// $Id: showTermin.php,v 1.3 2005/11/02 10:37:52 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/crmLib.php");
	if ($_GET["del"]) {
		deleteTermin($_GET["del"]);
		echo "<script language='JavaScript'>self.close();</script>";
	}
	$data=getTerminData($_GET["termid"]);
	list($tt,$mm,$yy)=split("\.",$data["starttag"]);
	$ft=feiertage($yy);
	$x=mktime(0,0,0,$mm,$tt,$yy);
	$wdhlg=array("0"=>"einmalig","1"=>"t&auml;glich","2"=>"2-t&auml;gig","7"=>"w&ouml;chentlich",
		"14"=>"2-w&ouml;chenltich","30"=>"monatlich","365"=>"j&auml;hlich");
?>
<html>
<head><title>Lx-Termin</title>
<script language="JavaScript">
	function editterm() {
		opener.top.main_window.location.href="termin.php?holen=<?= $_GET["termid"] ?>";
		self.close();
	}
	function delterm() {
		Check = confirm("Wirklich löschen?");
		if(Check == false) return false;
		document.location.href="showTermin.php?del=<?= $_GET["termid"] ?>";
	}
</script>
</head>
<body>
<?
	echo "Termin: <b>".$data["cause"]."</b><br>";
	echo db2date($data["starttag"])." ".$data["startzeit"]." - ";
	echo ($data["stoptag"]<>$data["starttag"])?db2date($data["stoptag"])." ".$data["stopzeit"]:$data["stopzeit"];
	echo "<br>";
	echo "Wiederholung: ".$wdhlg[$data["repeat"]].", ";
	echo ($data["ft"]==1)?"nur Arbeitstage":"auch Feiertage";
	echo "<br>";
	if ($ft[$x]) echo $ft[$x];
	echo "<hr><br>";
	echo $data["c_cause"]."<br>";
?>
<hr><br>
<input type="button" onClick="self.close()" value="schlie&szlig;en"> &nbsp; &nbsp;
<input type="button" onClick="delterm()" value="l&ouml;schen"> &nbsp; &nbsp;
<input type="button" onClick="editterm()" value="&auml;ndern">
<script language='JavaScript'>self.focus();</script>
</body>
</html>
