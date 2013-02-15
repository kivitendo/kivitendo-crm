<?php
	require_once("inc/stdLib.php");
    $menu = $_SESSION['menu'];
?>
<html>
	<head><title></title>
    <?php echo $menu['stylesheets']; ?>
	<link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['basepath'].'css/'.$_SESSION["stylesheet"]; ?>/main.css">
    <script type="text/javascript" src="<?php echo $_SESSION['basepath']; ?>crm/jquery-ui/jquery.js"></script>
    <?php echo $menu['javascript']; ?>

<body>
<?php
echo $menu['pre_content'];
echo $menu['start_content'];

if ($_POST["ok"]=="erzeugen") {

    /**
     * getBugru: Buchungsgruppen holen
     * 
     * @return Array
     */
    function getTax() {
            $sql ="SELECT C.id,(T.rate * 100) as rate,TK.startdate,C.accno from chart C ";
            $sql.="left join taxkeys TK  on TK.chart_id=C.id left join tax T on T.id=TK.tax_id ";
            $sql.="where  TK.startdate <= now() and C.taxkey_id=0 and category = 'I' and datevautomatik = 'f' ";
            $sql.="order by C.id, TK.startdate ";
            $rs=$_SESSION["db"]->getAll($sql,DB_FETCHMODE_ASSOC);
            if ($rs) foreach ($rs as $row) {
                $tax[$row["id"]]=sprintf("%0.2f",$row["rate"]);
        }
        return $tax;
    }

    function getSteuern($sqlar) {
        $sql = "select trans_id,chart_id,amount from acc_trans acc left join chart c on acc.chart_id=c.id ";
        $sql.= "where trans_id in (select id from ar) and c.taxkey_id=0 and c.category='I' ";
        $sql.= "and trans_id in (select id from ar where ".$sqlar.") order by trans_id";
        $rs = $_SESSION["db"]->getAll($sql,DB_FETCHMODE_ASSOC);
        return $rs;
    }

    /**
     * getRechnungen: Rechnungen auslesen
     * 
     * @return Array()
     */
    function getRechnungen($von,$bis,$tz,$istsoll) {
        $tax = getTax();
        $sql =  "SELECT ar.id,invnumber,amount,netamount,(amount-netamount) as mwst,ar.taxzone_id,name,customer.id as cid,country,ustid,ar.transdate ";
        $sql .= "from ar left join customer on customer.id=customer_id where " ;
        if ($tz==1) {
            $sqlar ="ar.taxzone_id=1 and ";
        } else if ($tz==2) {
            $sqlar ="ar.taxzone_id=2 and ";
        } else if ($tz==3) {
            $sqlar ="ar.taxzone_id=3 and ";
        } else if ($tz=="0") {
            $sqlar ="ar.taxzone_id=0 and ";
        }      
        if ($istsoll==1) {
            $bezug='datepaid';
        } else {
            $bezug='transdate';
        }
        $sqlar.=$bezug." between '".$von."' and '".$bis."'";
        //echo $sqlar;
        $steuern = getSteuern($sqlar);
        //$rs = $_SESSION["db"]->getAll($sql.$sqlar." order by invnumber",DB_FETCHMODE_ASSOC);
        $rs = $_SESSION["db"]->getAll($sql.$sqlar." order by transdate",DB_FETCHMODE_ASSOC);
        if ( !$rs ) return false;
        foreach ($rs as $row) {
            $rechng[$row["id"]] = $row;
        }
        $steuersatz = array();
        foreach ($steuern as $row) {
            if ($rechng[$row["trans_id"]]["amount"]<>$rechng[$row["trans_id"]]["netamount"]) {
                $rechng[$row["trans_id"]][$tax[$row["chart_id"]]]=$row["amount"];
                if (!in_array($tax[$row["chart_id"]],$steuersatz)) $steuersatz[]=$tax[$row["chart_id"]];
            }
        }
        return array($rechng,$steuersatz);
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
		$von = $_POST["jahr"]."-".$start[$_POST["quartal"]]."-01";
		$bis = $_POST["jahr"]."-".$stopM[$_POST["quartal"]]."-".$stopD[$_POST["quartal"]];
	} else if ($_POST["monatbis"]<>"" or $_POST["monatvon"]<>"") {
        if ($_POST["monatvon"]<>"") {
            $von = $_POST["jahr"]."-".$_POST["monatvon"]."-01";
        } else {
            $von = $_POST["jahr"]."-".$_POST["monatbis"]."-01";
        }
        if ($_POST["monatbis"]=="" or $_POST["monatbis"]<$_POST["monatvon"]) $_POST["monatbis"] = $_POST["monatvon"];
        if ($_POST["monatbis"]=="2") {
			if (schaltjahr($_POST["jahr"])) {
				$day=29;
			} else {
				$day=28;
			}
		} else {
			$day=$Day[$_POST["monatbis"]];
		} 
        $bis = $_POST["jahr"]."-".$_POST["monatbis"]."-".$day;
    } else {
        $von = $_POST["jahr"]."-01-01";
        $bis = $_POST["jahr"]."-12-31";
    }
    $rechnungen = getRechnungen($von,$bis,$_POST["tz"],$_POST["istsoll"]);
    if ( $rechnungen ) {
        //echo "<pre>";print_r($rechnungen); echo "</pre>";	
        $zeile = "<tr><td><a href='../is.pl?action=edit&id=%s' target='_blank'>%s</a></td>";
        $zeile.= "<td>%s</td>";
        $zeile.= "<td><a href='../ct.pl?action=edit&db=customer&id=%s' target='_blank'>%s</a></td>";
        $zeile.="<td>%s</td><td align='right'>%s</td><td align='right'>%s</td><td align='right'>%s</td>";
        $tz = "\t";
        $zeilec = "%s$tz%s".$_POST["jahr"]."$tz%s$tz%s$tz%0.2f$tz%0.2f$tz%0.2f$tz%0.2f%s\n";
        $zeilec = "%s$tz%s".$_POST["jahr"]."$tz%s$tz%s$tz%0.2f$tz%0.2f$tz%0.2f%s\n";
        echo "Jahr : ".$_POST["jahr"]."<br />";
        echo "<table cellpadding=3px'>";
        echo "<tr><th>Re-Nr</th><th>Datum</th><th>Kunde</th><th>UStID</th><th> Brutto </th><th> Netto </th><th> MwSt </th>";
        $colsp=7;
        if ($rechnungen[1]) {
            //taxzone = 0
            foreach ($rechnungen[1] as $row) { echo "<th>".$row." % </th>"; $colsp++;};
            //taxzone = 2
            foreach ($rechnungen[1] as $row) { echo "<th>".$row." % </th>"; };
        }
        echo "</tr>\n";
        $f = fopen('tmp/deb.csv','w');
        foreach ($rechnungen[0] as $row) {
            $transdate = split("-",$row["transdate"]);
            $transdate = $transdate[2].".".$transdate[1].".";
            $mwst = $row["amount"]- $row["netamount"];
            if ($mwst<>0) { 
                $mwst = sprintf("%0.2f",$mwst) ;
            } else { 
                $mwst = "";
                if ($row["taxzone_id"]==1) {
                    $nettobrutto1 += $row["netamount"];
                } else if ($row["taxzone_id"]==2) {
                    $nettobrutto2 += $row["netamount"];
                } else if ($row["taxzone_id"]==3) {
                    $nettobrutto3 += $row["netamount"];
                } else {
                    $nettobrutto += $row["netamount"];
                }
            }
            if (substr($row["invnumber"],0,6) == "Storno") $row["invnumber"] = substr($row["invnumber"],9)."Sto";
            echo sprintf($zeile,$row["id"],$row["invnumber"],$transdate,$row["cid"],$row["name"],$row["ustid"],sprintf("%0.2f",$row["amount"]),sprintf("%0.2f",$row["netamount"]),$mwst);
            if ($row["taxzone_id"]==2) { echo "<td></td><td></td>"; };
            $m1 = 0;
            $m2 = '';
            foreach ($rechnungen[1] as $rate) {
                    $tax[$row["taxzone_id"]][$rate]['rate'] += $row[$rate];
                    $m1 += $row[$rate];
                    if ($row[$rate]) { 
                        $tax[$row["taxzone_id"]][$rate]['amount'] += $row[$rate]/$rate*100; 
                        echo "<td align='right'>".sprintf("%0.2f",$row[$rate])."</td>"; 
 	   	    $m2 = sprintf("$tz%0.2f",$row[$rate]);
                    } else {
                        if ($row["taxzone_id"]==1 or $row["taxzone_id"]==3)  $tax[$row["taxzone_id"]][$rate]['amount'] += $row["netamount"];
                        echo "<td></td>";
                    }
            };
            if ($row["taxzone_id"]!=2) { echo "<td></td><td></td>"; }
            $brutto += $row["amount"];
            $netto += $row["netamount"];
            echo "<td>$m1 ".round($mwst-$m1,3)."</td></tr>\n";
            $mwstsum += $m1;
            $line = sprintf($zeilec,$row["invnumber"],$transdate,$row["name"],$row["ustid"],sprintf("%0.2f",$row["amount"]),sprintf("%0.2f",$row["netamount"]),$mwst,$m2); 
            fputs($f,$line);
        }
        echo "<tr><td colspan='$colsp'><hr></td></tr>";
        echo "<tr><td></td><td></td><td></td><td></td><td align='right'>".sprintf("%0.2f",$brutto)."</td><td align='right'>".sprintf("%0.2f",$netto),"</td>";
        echo "<td align='right'>".sprintf("%0.2f",($brutto-$netto))."</td>";
        if ($rechnungen[1]) {
            while (list($key,$val) = each($tax[0])) {
                echo "<td align='right'>".$val['rate']."</td>";
            }
            if ($tax[2]) while (list($key,$val) = each($tax[2])) {
                echo "<td align='right'>".$val['rate']."</td>";
            }
        }
        echo "<td>$mwstsum</td></tr>";
        echo "</table>";
        echo "<table>";
        if ($tax[0]) foreach ($tax[0] as $key=>$mwst) {
            echo "<tr><td colspan=2>Inland $key%: </td><td align='right'>".sprintf("%0.2f",$mwst['amount'])."</td><td align='right'>".sprintf("%0.2f",$mwst['rate'])."</td></tr>";
        };
        if ($tax[2]) foreach ($tax[2] as $key=>$mwst) {
            echo "<tr><td colspan=2>EU ohne UStID $key%: </td><td align='right'>".sprintf("%0.2f",$mwst['amount'])."</td><td align='right'>".sprintf("%0.2f",$mwst['rate'])."</td></tr>";
        };
        echo "<tr><td colspan=2>EU mit UStID : </td><td align='right'>".sprintf("%0.2f",$nettobrutto1)."</td><td></td></tr>";
        echo "<tr><td colspan=2>Ausland : </td><td align='right'>".sprintf("%0.2f",$nettobrutto3)."</td><td></td></tr>";
        echo "<tr><td colspan=2>Brutto==Netto: (?)</td><td>".$nettobrutto."</td><td></td></tr>";
        echo "</table>";
        fclose($f);
        echo '<a href="tmp/deb.csv">csv</a>';
        //echo "<pre>";print_r($tax);echo "</pre>";
   } else {
        echo "Keine Treffer";
   }
} else {
?>
<form name="ustva" action="eur.php" method="post">
<table>
<tr><td>Jahr</td><td><select name="jahr"><option value="2007">2007<option value="2008">2008<option value="2009">2009<option value="2010">2010<option value="2011">2011</select></td></tr>
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
<?php };
echo $menu['end_content'];
?>
</body>
</html>
