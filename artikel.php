<?php
	require_once("inc/stdLib.php");
	include("inc/wvLib.php");
	$mid=($_GET["mid"])?$_GET["mid"]:$_POST["mid"];
	$aid=($_GET["aid"])?$_GET["aid"]:$_POST["aid"];	
	if ($_POST["sichern"]) {
		safeMaschMat($mid,$aid,$_POST["material"]);
	}
	$masch=getAllMaschine($mid);
	$material=getAllMat($aid,$mid);
    $menu = $_SESSION['menu'];
    $head = mkHeader();    
?>
<html>
	<head><title>LX - CRM</title>
<?php echo $menu['stylesheets']; ?>
<?php echo $head['CRMCSS']; ?>    
	<script language="JavaScript">
	<!--
		function selall() {
			len=document.mat.elements[2].length;
			document.mat.elements[2].multiple=true;
			for (i=0; i<len; i++) {
				document.mat.elements[2].options[i].selected=true;
			}
		}
		function subart() {
			nr=document.mat.elements[2].selectedIndex;
			document.mat.elements[2].options[nr]=null
		}
		function auswahl() {
			nr=artikelwindow.document.pgartikel.artikel.selectedIndex;
			tmp=artikelwindow.document.pgartikel.artikel.options[nr].value;
			val=tmp.split(",");
			preis=val[1];
			txt=artikelwindow.document.pgartikel.artikel.options[nr].text.split(" ->");
			tmp=document.artikel.anz.value;
			anz=tmp.replace(",",".");
			tmp=document.artikel.preis.value;
			if (tmp) preis=tmp;
			sum=anz*preis;
			val=anz+";"+val[0]+";"+preis;
			NeuerEintrag = new Option(anz+" x "+txt[0]+" ("+preis+")",val,false,true);
			document.mat.elements[2].options[document.mat.elements[2].length] = NeuerEintrag;
		}

		function getData() {
			nr=document.artikel.gruppen.selectedIndex;
			val=document.artikel.gruppen.options[nr].value;
			artikelwindow.location.href="pg_artikel.php?pg="+val;
		}
	//-->
	</script>
	</head>
<body onLoad="self.focus()">
<center><form name="artikel">
<table class="karte" width="100%">
	<tr><td>Warengruppen:</td><td></td></tr>
	<tr><td>
			<select name="gruppen" size="8" Style="width:450px" onDblClick="getData()">
			<option value=''>Artikel ohne Warengruppe</option>
<?php
	$partsgrp=getAllPG();
	if ($partsgrp) foreach ($partsgrp as $zeile) {
 		echo "\t<option value='".$zeile["id"]."'>".$zeile["partsgroup"]."</option>\n";
	}
?>
			</select>
		</td>
		<td><input type="button" name="ok" value="Fenster schlie&szlig;en" onClick="self.close();"></td>
	</tr>
	<tr><td>Artikel:</td><td></td></tr>
	<tr><td>
		<iframe src="pg_artikel.php" name="artikelwindow" width="450" height="141" marginheight="0" marginwidth="0" frameborder="0" align="left">
		<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
		</iframe>
		</td>
		<td><input type="button" name="ok" value="&uuml;bernehmen" onClick="auswahl()"><br><br>
			<input type="text" name="anz" size="4" value="1">Stck.
			<input type="text" name="preis" size="5" value="">&euro;</td>
	</tr>
	<tr><td>verbrauchtes Material und erbrachte Leistungen:</td><td></form><form name="mat" action="artikel.php" method="post" onSubmit="return selall();"></td></tr>
	<tr><td>	
		<input type="hidden" name="mid" value="<?php echo  $mid ?>">
		<input type="hidden" name="aid" value="<?php echo  $aid ?>">		
		<select name="material[]" size="6" Style="width:450px">
<?php

	if ($material) foreach ($material as $zeile) {
		$val=$zeile["menge"];
		$val.=";".$zeile["parts_id"];
		$preis=sprintf("%0.2f",$zeile["betrag"]);
		$val.=";".$preis;
		echo "\t<option value='".$val."'>".$zeile["menge"]." x ".substr($zeile["description"],0,54)." (".$preis.")</option>\n";
	}
?>
			</select>
		</td>
		<td>
			<input type="button" name="ok" value="<- entfernen" onClick="subart()"><br><br>
			<input type="submit" name="sichern" value="sichern" ><br><br>
		</td>
	</tr>
</table>
</form>
</body>
</html>
