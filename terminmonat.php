<?
// $ID: $
	require_once("inc/stdLib.php");
	include_once("inc/crmLib.php");

?>
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
<?
	list($day,$month,$year)=split("\.",$_GET["datum"]);
	if (strlen($month)==1) $month="0$month";
	if ($_GET["fld"]) {
?>
	function tag(tg) {
		opener.document.termedit.<?= $_GET["fld"] ?>.value=tg;
	}
	function kw(w) {
	}
<? } else { ?>
	function tag(tg) {
		self.location.href="termlist.php?ansicht=T&datum="+tg;
	}
	function kw(w) {
		self.location.href="termlist.php?ansicht=W&kw="+w+"&year="+<?= $year ?>;
	}
<?
	}
?>
	function monmin() {
			self.location.href="terminmonat.php?ansicht=M&datum=01.<?= ($month>1)?($month-1):12 ?>.<?= ($month>1)?$year:($year-1) ?>&fld=<?= $_GET["fld"] ?>";
	}
	function monplu() {
			self.location.href="terminmonat.php?ansicht=M&datum=01.<?= ($month<12)?($month+1):1 ?>.<?= ($month<12)?$year:($year+1) ?>&fld=<?= $_GET["fld"] ?>";
	}
	//-->
	</script>
<body onLoad="self.focus()">
<center>
<input type="button" value="<--" onClick="monmin()"> [<a href="prtmkal.php?month=<?= $month ?>&year=<?= $year ?>"><?= $month."/".$year ?></a>] <input type="button" value="-->" onClick="monplu()">
<br><br>
<table style="width:335px">
	<tr><th width="30px"class="gr">Kw</th><th width="49px" class="gr">Mo</th><th width="49px" class="gr">Di</th><th width="49px" class="gr">Mi</th><th width="49px" class="gr">Do</th><th width="49px" class="gr">Fr</th><th width="30px" class="gr">Sa</th><th width="30px" class="gr">So</th></tr>
<?
	$firstday=mktime(0,0,0,$month,1,$year);
	$anztage=date("t", mktime(0,0,0,($month+1),0,$year));
	$ft=feiertage($year);
	$ftk=array_keys($ft);
		$data=getTermin(0,$month,$year,"M");
		$tmp=array();
		if ($data) foreach ($data as $term) {
			$tmp[$term["tag"]]=1;
		}
		$days=array_keys($tmp);
		//first week, still in last month?
		if (date("w", mktime(0,0,0,$month,1,$year)) == 0) { $da = -6; }
		elseif (date("w", mktime(0,0,0,$month,1,$year)) <> 1) { $da = - date("w", mktime(0,0,0,$month,1,$year)) +1;}
		else {$da = 1;}
		// set week number for the first time
 		$W=strftime("%V",mktime(0,0,0,$month,$da+2,$year));
		echo "\t<tr height='50px'>\n\t\t<td class='norm gr re' onClick='kw({$W})'>$W</td>\n";
		// show days of the previous month
		if ( date("w", mktime(0,0,0,$month,1,$year)) == 0) { $start = 7; }
		else {$start = date("w", mktime(0,0,0,$month,1,$year)); }
  		for ($a = ($start-2); $a>=0; $a--) {
    		$d = date("t", mktime(0,0,0,$month,0,$year)) - $a;
			echo "\t\t<td class='smal lg re'>$d</td>\n";
  		}
		// show days of the actual month
		for ($d=1; $d <= $anztage; $d++) {
			// today = different colour
			$akt=mktime(0,0,0,$month,$d,$year);
  			if ($month==date("m") AND $year==date("Y") AND $d==date("d")) {
				 $da = "<div style='color:red; font-weight:bold;'>".$d."</div>"; }
			else if(in_array($akt,$ftk)) {
				$da = "<div style='color:blue; font-weight:bold;'>".$d."</div>";
			} else {
				$da = $d;
			}
			if (in_array($d,$days)) { $bg=" background='image/data.gif'";} else { $bg="";};
			if (date("w",mktime(0,0,0,$month,$d,$year))==0 || date("w",mktime(0,0,0,$month,$d,$year))==6 || in_array($akt,$ftk)) { $col="ft";} else { $col="we";};
			// day link
			echo"\t\t<td class='norm dot $col re'$bg onClick='tag(\"".sprintf("%02d",$d).".".sprintf("%02d",$month).".".$year."\")'>$da</td>\n";
			if (date("w", mktime(0,0,0,$month,$d,$year)) == 0)  {// && date("t", mktime(0,0,0,($month+1),0,$year)) > $d )  {
				echo "\t</tr>\n";
				$da = $d + 1;
				$W=strftime("%V",mktime(0,0,0,$month,$da+2,$year));
				echo "\t<tr height='50px'>\n\t\t<td class='norm gr re' onClick='kw({$W})'>$W</td>\n";
			}
		}
		// show days of the next month
		if (date("w", mktime(0,0,0,$month+1,1,$year)) <> 1) {
			$d=1;
			while (date("w", mktime(0,0,0,($month+1),$d,$year)) <> 1) {
				echo"\t\t<td class='smal lg re'>$d</td>\n";
				$d++;
			}
		}
?>
	</tr>
</table>

</center>
</body>
</html>
