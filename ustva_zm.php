<?php
    require_once("inc/stdLib.php");
    $menu =  $_SESSION['menu'];
?>
<html>
    <head><title></title>
    <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['basepath'].'css/'.$_SESSION["stylesheet"]; ?>/main.css"></link>
    <!-- ERP Stylesheet -->
    <?php echo $menu['stylesheets']; ?>
    <!-- ERP JavaScripts -->
    <?php echo $menu['javascripts']; ?>
    <!-- Ende ERP -->
<body>
<?php
 echo $menu['pre_content'];
 echo $menu['start_content'];
if ($_POST["ok"]=="erzeugen") {
    if ($_POST["istsoll"]==1) {
		$bezug='datepaid';
    } else {
		$bezug='transdate';
    }
	$sqlar="SELECT ar.id,invnumber,amount,netamount,(amount-netamount) as mwst,ar.taxzone_id,name,customer.id as cid,country,ustid from ar left join customer on customer.id=customer_id where ";
    if ($_POST["tz"]==1) {
			$sqlar.="ar.taxzone_id=1 and ";
    } else if ($_POST["tz"]==2) {
			$sqlar.="ar.taxzone_id=2 and ";
    } else if ($_POST["tz"]==3) {
			$sqlar.="ar.taxzone_id=3 and ";
    } else if ($_POST["tz"]=="0") {
			$sqlar.="ar.taxzone_id=0 and ";
    }
	function schaltjahr($jahr) {
		// Funktion noch verbessern?
		if ($jahr % 4 <> 0) return false;
		if ($jahr % 400 == 0 ) return true;
		if ($jahr % 100 <> 0 ) return false;
		return true;
	}
	$Day=array(0,"31","28","31","30","31","30","31","31","30","31","30","31");
	if ($_POST["quartal"]<>"") {  
		$start=array(0,"01","04","07","10");
		$stopM=array(0,"03","06","09","12");
		$stopD=array(0,"31","30","30","31");
		$von=$start[$_POST["quartal"]]."-01";
		$bis=$stopM[$_POST["quartal"]]."-".$stopD[$_POST["quartal"]];
		$sqlar.=$bezug." >= '".$_POST["jahr"]."-$von' and ".$bezug." <= '".$_POST["jahr"]."-$bis'";
	} else if ($_POST["monatbis"]<>"") {
		if ($_POST["monatbis"]=="2") {
			if (schaltjahr($_POST["jahr"])) {
				$day=29;
			} else {
				$day=28;
			}
		} else { 
			$day=$Day[$_POST["monatbis"]];
		};
		$sqlar =$bezug." >= '".$_POST["jahr"]."-".$_POST["monatvon"]."-01' and ";
		$sqlar.=$bezug." <= '".$_POST["jahr"]."-".$_POST["monatbis"]."-$day";
	} else if ($_POST["monatvon"]<>"") {
		if ($_POST["monatvon"]=="2") {
			if (schaltjahr($_POST["jahr"])) {
				$day=29;
			} else {
				$day=28;
			}
		} else { 
			$day=$Day[$_POST["monatvon"]];
		};
		$sqlar.=$bezug." between '".$_POST["jahr"]."-".$_POST["monatvon"]."-01' and '".$_POST["jahr"]."-".$_POST["monatvon"]."-".$day."'";
	} else {
		$sqlar.=$bezug." between '".$_POST["jahr"]."-01-01' and '".$_POST["jahr"]."-12-31'";
	}
	//echo $sqlar;
	$rs=$_SESSION["db"]->getAll($sqlar);
	echo "<table>\n";
	echo "<tr><td>TransID</td><td>Re-Nr.</td><td>Brutto</td><td>Netto</td><td>MwSt</td><td>Tax<br>Zone</td><td>Kunde</td><td>Land</td><td>UST-ID</td></tr>";
	$netto0=0;
	$brutto0=0;
	$netto1=0;
	$brutto1=0;
	$netto2=0;
	$brutto2=0;
	//$linkR="../is.pl?login=".$_SESSION["employee"]."&password=".$_SESSION["password"]."&action=edit&id=";
	$linkR="../is.pl?action=edit&id=";
	//$linkC="../ct.pl?login=".$_SESSION["employee"]."&password=".$_SESSION["password"]."&action=edit&db=customer&id=";
	$linkC="../ct.pl?action=edit&db=customer&id=";
	if ($rs) foreach ($rs as $row) {
		if ($row["amount"]==$row["netamount"]) {
			$col1="<font color='red'>";
			$col2="</font>";
		} else {
			$col1="";
			$col2="";
		};
		if ($row["taxzone_id"]==1) {
			$tz1="<font color='blue'>";
			$tz2="</font>";
		} else if ($row["taxzone_id"]==2) {
			$tz1="<font color='green'>";
			$tz2="</font>";
		} else if ($row["taxzone_id"]==3) {
			$tz1="<font color='cyan'>";
			$tz2="</font>";
		} else {
			$tz1="";
			$tz2="";
		}
?>
	<tr><td><a href="<?= $linkR.$row["id"] ?>" target="_blank"><?= $row["id"] ?></a> </td><td> <?= $row["invnumber"] ?> </td>
		<td align="right"><?= sprintf("%01.2f",$row["amount"]) ?></td>
		<td align="right"><?= sprintf("%01.2f",$row["netamount"]) ?></td>
		<td align="right"><?= $col1.sprintf("%01.2f",$row["mwst"]).$col2 ?></td>
		<td align="center"><?= $tz1.$row["taxzone_id"].$tz2 ?></td>
		<td><a href="<?= $linkC.$row["cid"] ?>" target="_blank"><?= $row["name"] ?></a></td>
		<td align="center"><?= $row["country"] ?></td>
		<td><?= $row["ustid"] ?></td></tr>
<?
		$tmp="netto".$row["taxzone_id"];
		${$tmp}+=$row["netamount"];
		$tmp="brutto".$row["taxzone_id"];
		${$tmp}+=$row["amount"];
		$netto+=$row["netamount"];
		$brutto+=$row["amount"];
	};
	echo "<tr><td colspan='2'></td><td align='right'>".sprintf("%01.2f",$brutto)."</td><td align='right'>".sprintf("%01.2f",$netto)."</td><td align='right'>".sprintf("%01.2f",($brutto-$netto))."</td></tr>";
	echo "<tr><td colspan='4'>0 Steuer Inland</td><td align='right'>".sprintf("%01.2f",($brutto0-$netto0))."</td></tr>";
	echo "<tr><td colspan='4'>1 Steuer EU mit USTID *)</td><td align='right'><font color='blue'>".sprintf("%01.2f",($brutto1-$netto1))."</font></td></tr>";
	echo "<tr><td colspan='4'>2 Steuer EU ohne USTID</td><td align='right'><font color='green'>".sprintf("%01.2f",($brutto2-$netto2))."</font></td></tr>";
	echo "<tr><td colspan='4'>3 Steuer Ausland *)</td><td align='right'><font color='cyan'>".sprintf("%01.2f",($brutto3-$netto3))."</font></td></tr>";
	echo "</table>\n";
    echo "*) sollte 0.00 sein<br />\n";
	echo ($_POST["istsoll"]==1)?"Istbesteuerung":"Sollbesteuerung";
} else { ?>
<form name="ustva" action="ustva_zm.php" method="post">
<table>
<tr><td>Jahr</td><td><select name="jahr"><option value="2010">2010<option value="2011">2011<option value="2012">2012<option value="2013">2013<option value="2014">2014</select></td></tr>
<tr><td>Quartal</td><td><select name="quartal">
<option value=""><option value="1">1<option value="2">2<option value="3">3<option value="4">4
</select></td></tr>
<tr><td>Monat</td><td>von:<select name="monatvon">
<option value=""><option value="1">1<option value="2">2<option value="3">3<option value="4">4
<option value="5">5<option value="6">6<option value="7">7<option value="8">8<option value="9">9
<option value="10">10<option value="11">11<option value="12">12
</select>
 bis:<select name="monatbis">
<option value=""><option value="1">1<option value="2">2<option value="3">3<option value="4">4
<option value="5">5<option value="6">6<option value="7">7<option value="8">8<option value="9">9
<option value="10">10<option value="11">11<option value="12">12
</select></td></tr>
<tr><td>Besteuerung</td><td><input type="radio" name="istsoll" value="1" checked>ist <input type="radio" name="istsoll" value="0">soll</td></tr>
<tr><td>Steuerzone</td><td><input type="radio" name="tz" value="0" checked>Inland <input type="radio" name="tz" value="1">EU mit ID <input type="radio" name="tz" value="2">EU ohne ID <input type="radio" name="tz" value="3">Ausland <input type="radio" name="tz" value="-1" checked>Alle</td></tr>
</table>
<input type="submit" name="ok" value="erzeugen">
</form>
<?php }
echo $menu['end_content']; ?>
</body>
</html>
