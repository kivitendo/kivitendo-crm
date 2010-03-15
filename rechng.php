<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/crmLib.php");
	include("inc/FirmenLib.php");
	$nr=$_GET["nr"];
	$id=$_GET["id"];
	$tmp=getRechParts(substr($id,1),substr($id,0,1));
	$adr=getRechAdr(substr($id,1),substr($id,0,1),$tmp[0]["shipto_id"]);
	if ($adr["shiptoname"]) { $NAME=$adr["shiptoname"]; } else { $NAME=$adr["name"]; }
	if ($adr["shiptostreet"]) { $STRASSE=$adr["shiptostreet"]; } else { $STRASSE=$adr["street"]; };
	if ($adr["shiptocity"]) { $ORT=$adr["shiptozipcode"]." ".$adr["shiptocity"]; } else { $ORT=$adr["zipcode"]." ".$adr["city"]; };		
	$reP=$tmp[0];
	if (substr($id,0,1)=="R") {
		$header="Rechnung Nr: ".$nr;
		$header2=($tmp[1]["quonumber"])?"Angebots Nr: ".$tmp[1]["quonumber"]:"";
		$header3=($tmp[1]["ordnumber"])?"Auftrags Nr: ".$tmp[1]["ordnumber"]:"";
		$mul=1;
	} else if (substr($id,0,1)=="V") {
		$header="Rechnung Nr: ".$nr;
		$header2=($tmp[1]["quonumber"])?"Angebots Nr: ".$tmp[1]["quonumber"]:"";
		$header3=($tmp[1]["ordnumber"])?"Auftrags Nr: ".$tmp[1]["ordnumber"]:"";
		$mul=-1;
	} else {
		if ($tmp[1]["quotation"]=="t") {
			$header="Angebots Nr: ".$nr;
		} else {
			$header="Auftrags Nr: ".$nr;
			$header2=($tmp[1]["quonumber"])?"Angebots Nr: ".$tmp[1]["quonumber"]:"";
		}
		$mul=1;
	}
?>
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main1.css"></link>
<body>
<table width="100%" class="karte"><tr><td class="karte">
<!-- Hier beginnt die Karte  ------------------------------------------->
<table width="100%">
<tr class='smal'><td>Rechnung Anschrift</td><td>Lieferanschrift</td><td></td></tr>
<tr class='smal'><td><?php echo  $adr["name"]."<br>".$adr["street"]."<br>".$adr["zipcode"]." ".$adr["city"] ?></td>
	<td><?php echo  $NAME."<br>".$STRASSE."<br>".$ORT ?></td
	<td style="vertical-align:top; text-align:right;" nowrap><?php echo  $header." vom ".db2date($tmp[1]["transdate"]) ?><br><?php echo  $header2 ?><br><?php echo  $header3 ?></td></tr>
</table>
<table>
<tr class='smal'><td>Menge</td><td>Einh.</td><td>Artikel</td><td>VKpreis</td><td>Einzelpreis</td><td>Summe</td></tr>
<?php
$i=0;

if (empty($reP)) {
	echo "<br><br>Nur Buchungssatz";
} else {
	foreach ($reP as $col) {
		echo "\t<tr  class='smal' onMouseover='this.bgColor=\"#FF0000\";' onMouseout='this.bgColor=\"".$bgcol[($i%2+1)]."\";' bgcolor='".$bgcol[($i%2+1)]."'>";
		echo "\t\t<td width='30px' align='right'>".($col["qty"]*$mul)."</td><td width='30px'>".$col["unit"]."</td>";
		echo "<td width='280px'>".$col["artikel"]."</td><td width='70px' align='right'>".sprintf("%0.2f",$col["sellprice"])."</td><td width='70px' align='right'>".sprintf("%0.2f",$col["endprice"])."</td><td width='70px' align='right'>".sprintf("%0.2f",$col["endprice"]*$col["qty"]*$mul)."</td>";
		echo "\t</tr>\n";
		if ($col["notes"]) {
			echo "\t<tr  class='smal' onMouseover='this.bgColor=\"#FF0000\";' onMouseout='this.bgColor=\"".$bgcol[($i%2+1)]."\";' bgcolor='".$bgcol[($i%2+1)]."'>";
			echo "\t<td colspan='2'></td><td>".$col["notes"]."</td><td colspan='3'></td></tr>";
		}
		if ($col["serialnumber"]) {
			echo "\t<tr  class='smal' onMouseover='this.bgColor=\"#FF0000\";' onMouseout='this.bgColor=\"".$bgcol[($i%2+1)]."\";' bgcolor='".$bgcol[($i%2+1)]."'>";
			echo "\t<td colspan='2'></td><td colspan='5'>".$col["serialnumber"]."</td></tr>";
		}
		$i++;
	}
?>
	<tr  class='smal' onMouseover='this.bgColor="#FF0000";' onMouseout='this.bgColor="<?php echo  $bgcol[($i%2+1)] ?>";' bgcolor='<?php echo  $bgcol[($i%2+1)] ?>'>
		<td colspan='5' align='right'>Rechnungssumme Netto</td><td align='right'><?php echo  sprintf("%0.2f",$tmp[1]["netto"]) ?></td>
	</tr>
	<tr  class='smal' onMouseover='this.bgColor="#FF0000";' onMouseout='this.bgColor="<?php echo  $bgcol[($i%2+1)] ?>";' bgcolor='<?php echo  $bgcol[($i%2+1)] ?>'>
		<td colspan='5' align='right'>enthaltene MwSt</td><td align='right'><?php echo  sprintf("%0.2f",($tmp[1]["brutto"]-$tmp[1]["netto"])) ?></td>
	</tr>
	<tr  class='smal' onMouseover='this.bgColor="#FF0000";' onMouseout='this.bgColor="<?php echo  $bgcol[($i%2+1)] ?>";' bgcolor='<?php echo  $bgcol[($i%2+1)] ?>'>
		<td colspan='5' align='right'>Rechnungssumme Brutto</td><td align='right'><?php echo  sprintf("%0.2f",$tmp[1]["brutto"]) ?></td>
	</tr>
	
<?php } ?>

	<tr  class='smal' onMouseover='this.bgColor="#FF0000";' onMouseout='this.bgColor="<?php echo  $bgcol[($i%2+1)] ?>";' bgcolor='<?php echo  $bgcol[($i%2+1)] ?>'>
		<td colspan='2' align='left'>Re-Notiz:</td><td colspan='4'> <?php echo  $tmp[1]["notes"] ?></td>
	</tr>
	<tr  class='smal' onMouseover='this.bgColor="#FF0000";' onMouseout='this.bgColor="<?php echo  $bgcol[($i%2+1)] ?>";' bgcolor='<?php echo  $bgcol[($i%2+1)] ?>'>
		<td colspan='2' align='left'>Intern:</td><td colspan='4'> <?php echo  $tmp[1]["intnotes"] ?></td>
	</tr>
</table>
<center><a href="javascript:self.close()">schlie&szlig;en</a>
<script language='JavaScript'>self.focus();</script>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
