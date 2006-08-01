<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/crmLib.php");
	include("inc/FirmenLib.php");
	$nr=$_GET["nr"];
	$id=$_GET["id"];
	$tmp=getRechParts(substr($id,1),substr($id,0,1));
	$adr=getRechAdr(substr($id,1),substr($id,0,1));
	if ($adr["shiptoname"]) { $NAME=$adr["shiptoname"]; } else { $NAME=$adr["name"]; }
	if ($adr["shiptostreet"]) { $STRASSE=$adr["shiptostreet"]; } else { $STRASSE=$adr["street"]; };
	if ($adr["shiptocity"]) { $ORT=$adr["shiptozipcode"]." ".$adr["shiptocity"]; } else { $ORT=$adr["zipcode"]." ".$adr["city"]; };		
	$reP=$tmp[0];
	if (substr($id,0,1)=="R") {
		$header="Rechnung Nr: ".$nr;
		$mul=1;
	} else if (substr($id,0,1)=="V") {
		$header="Rechnung Nr: ".$nr;
		$mul=-1;
	} else {
		if ($tmp[1]["quotation"]=="t") {
			$header="Angebots Nr: ".$nr;
		} else {
			$header="Auftrags Nr: ".$nr;
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
<table>
<tr class='smal'><td colspan="3"/td><?= $NAME."<br>".$STRASSE."<br>".$ORT ?>
	<td colspan="3" style="vertical-align:top"><?= $header." vom ".db2date($tmp[1]["transdate"]) ?></td></tr>

<tr class='smal'><td>Menge</td><td>Einh.</td><td>Artikel</td><td>VKpreis</td><td>Einzelpreis</td><td>Summe</td></tr>
<?
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
	<tr  class='smal' onMouseover='this.bgColor="#FF0000";' onMouseout='this.bgColor="<?= $bgcol[($i%2+1)] ?>";' bgcolor='<?= $bgcol[($i%2+1)] ?>'>
		<td colspan='5' align='right'>Rechnungssumme Netto</td><td align='right'><?= sprintf("%0.2f",$tmp[1]["netto"]) ?></td>
	</tr>
	<tr  class='smal' onMouseover='this.bgColor="#FF0000";' onMouseout='this.bgColor="<?= $bgcol[($i%2+1)] ?>";' bgcolor='<?= $bgcol[($i%2+1)] ?>'>
		<td colspan='5' align='right'>enthaltene MwSt</td><td align='right'><?= sprintf("%0.2f",($tmp[1]["brutto"]-$tmp[1]["netto"])) ?></td>
	</tr>
	<tr  class='smal' onMouseover='this.bgColor="#FF0000";' onMouseout='this.bgColor="<?= $bgcol[($i%2+1)] ?>";' bgcolor='<?= $bgcol[($i%2+1)] ?>'>
		<td colspan='5' align='right'>Rechnungssumme Brutto</td><td align='right'><?= sprintf("%0.2f",$tmp[1]["brutto"]) ?></td>
	</tr>
	
<? } ?>

	<tr  class='smal' onMouseover='this.bgColor="#FF0000";' onMouseout='this.bgColor="<?= $bgcol[($i%2+1)] ?>";' bgcolor='<?= $bgcol[($i%2+1)] ?>'>
		<td colspan='2' align='left'>Re-Notiz:</td><td colspan='4'> <?= $tmp[1]["notes"] ?></td>
	</tr>
	<tr  class='smal' onMouseover='this.bgColor="#FF0000";' onMouseout='this.bgColor="<?= $bgcol[($i%2+1)] ?>";' bgcolor='<?= $bgcol[($i%2+1)] ?>'>
		<td colspan='2' align='left'>Intern:</td><td colspan='4'> <?= $tmp[1]["intnotes"] ?></td>
	</tr>
</table>
<center><a href="javascript:self.close()">schlie&szlig;en</a>
<script language='JavaScript'>self.focus();</script>
<!-- Hier endet die Karte ------------------------------------------->
</td></tr></table>
</body>
</html>
