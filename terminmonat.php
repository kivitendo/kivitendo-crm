<?php
// $ID: $
	require_once("inc/stdLib.php");
	include_once("inc/crmLib.php");
    $CUID=($_GET["cuid"])?$_GET["cuid"]:$_SESSION["loginCRM"];
?>
<html onLoad="self.focus()">
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
<?php
	list($day,$month,$year)=split("\.",$_GET["datum"]);
	if (strlen($month)==1) $month="0$month";
    if ($_GET["fld"]=="suchfld") {
?>
	function tag(tg) {
        d = tg.substr(0,2);
        m = tg.substr(3,2);
        y = "20" + tg.substr(6,2);
        y = tg.substr(6,4);
        for (i=0; i<opener.document.termedit.Tag.length; i++) {
            if (opener.document.termedit.Tag.options[i].value == d) 
        		opener.document.termedit.Tag.selectedIndex=i;
        }
        for (i=0; i<opener.document.termedit.Monat.length; i++) { 
            if (opener.document.termedit.Monat.options[i].value == m) {
        		opener.document.termedit.Monat.selectedIndex=i;
            }
        }
        for (i=0; i<opener.document.termedit.Jahr.length; i++) {
            if (opener.document.termedit.Jahr.options[i].value == y) 
        		opener.document.termedit.Jahr.selectedIndex=i;
        }
	}
	function kw(w) {
	}
<?php  } else if ($_GET["fld"]) { ?>
	function tag(tg) {
		opener.document.termedit.<?= $_GET["fld"] ?>.value=tg;
	}
	function kw(w) {
	}
<?php } else { ?>
	function tag(tg) {
		self.location.href="termlist.php?cuid=<?= $CUID ?>&ansicht=T&datum="+tg;
	}
	function kw(w) {
		self.location.href="termlist.php?cuid=<?= $CUID ?>&ansicht=W&kw="+w+"&year="+<?= $year ?>;
	}
<?php
	}
?>
	function monmin() {
			self.location.href="terminmonat.php?cuid=<?= $CUID ?>&ansicht=M&datum=01.<?= ($month>1)?($month-1):12 ?>.<?= ($month>1)?$year:($year-1) ?>&fld=<?= $_GET["fld"] ?>";
	}
	function monplu() {
			self.location.href="terminmonat.php?cuid=<?= $CUID ?>&ansicht=M&datum=01.<?= ($month<12)?($month+1):1 ?>.<?= ($month<12)?$year:($year+1) ?>&fld=<?= $_GET["fld"] ?>";
	}
	//-->
	</script>
<body onLoad="self.focus()">
<center>
<input type="button" value="<--" onClick="monmin()"> [<a href="prtmkal.php?month=<?= $month ?>&year=<?= $year ?>"><?= $month."/".$year ?></a>] <input type="button" value="-->" onClick="monplu()">
<br><br>
<table style="width:29em" class="klein">
	<tr><th style="width:2.1em" class="gr">Kw</th><th style="width:3.9em" class="gr">Mo</th><th style="width:3.9em" class="gr">Di</th><th style="width:3.9em" class="gr">Mi</th><th style="width:3.9em" class="gr">Do</th><th style="width:3.9em" class="gr">Fr</th><th style="width:3.0em" class="gr">Sa</th><th style="width:3.0em" class="gr">So</th></tr>
<?php
	$firstday=mktime(0,0,0,$month,1,$year);
	$anztage=date("t", mktime(0,0,0,($month+1),0,$year));
	$ft=feiertage($year);
	$ftk=array_keys($ft);
		$data=getTermin(0,$month,$year,"M",$CUID);
		$tmp=array();
		if ($data) foreach ($data as $term) {
			$tmp[$term["tag"]]+=1;
		}
		$days=array_keys($tmp);
		//first week, still in last month?
		if (date("w", mktime(0,0,0,$month,1,$year)) == 0) { $da = -6; }
		elseif (date("w", mktime(0,0,0,$month,1,$year)) <> 1) { $da = - date("w", mktime(0,0,0,$month,1,$year)) +1;}
		else {$da = 1;}
		// set week number for the first time
 		$W=strftime("%V",mktime(0,0,0,$month,$da+2,$year));
		echo "\t<tr height='50px'>\n\t\t<td class='gr ce' onClick='kw({$W})'>$W</td>\n";
		// show days of the previous month
		if ( date("w", mktime(0,0,0,$month,1,$year)) == 0) { $start = 7; }
		else {$start = date("w", mktime(0,0,0,$month,1,$year)); }
  		for ($a = ($start-2); $a>=0; $a--) {
    		$d = date("t", mktime(0,0,0,$month,0,$year)) - $a;
			echo "\t\t<td class='klein lg re'>$d</td>\n";
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
			if (in_array($d,$days)) { $bg=" style='background-image:url(image/data.gif); background-repeat:no-repeat;background-position:center;'";} else { $bg="";};
			if (date("w",mktime(0,0,0,$month,$d,$year))==0 || date("w",mktime(0,0,0,$month,$d,$year))==6 || in_array($akt,$ftk)) { $col="ft";} else { $col="we";};
			// day link
			echo"\t\t<td class='dot $col re'$bg onClick='tag(\"".sprintf("%02d",$d).".".sprintf("%02d",$month).".".$year."\")'>$da</td>\n";
			if (date("w", mktime(0,0,0,$month,$d,$year)) == 0)  {// && date("t", mktime(0,0,0,($month+1),0,$year)) > $d )  {
				echo "\t</tr>\n";
				$da = $d + 1;
				$W=strftime("%V",mktime(0,0,0,$month,$da+2,$year));
				echo "\t<tr style='height:4em'>\n\t\t<td class='gr ce' onClick='kw({$W})'>$W</td>\n";
			}
		}
		// show days of the next month
		if (date("w", mktime(0,0,0,$month+1,1,$year)) <> 1) {
			$d=1;
			while (date("w", mktime(0,0,0,($month+1),$d,$year)) <> 1) {
				echo"\t\t<td class='klein lg re'>$d</td>\n";
				$d++;
			}
		}
?>
	</tr>
</table>
[<a href="javaScript:self.close()">close</a>]
</center>
</body>
</html>
