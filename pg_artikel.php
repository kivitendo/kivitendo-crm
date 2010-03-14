<?php
	require_once("inc/stdLib.php");
	require_once("inc/wvLib.php");
?>
	<script language="JavaScript">
	<!--
		function putData() {
			nr=document.pgartikel.artikel.selectedIndex;
			tmp=document.pgartikel.artikel.options[nr].value;
			val=tmp.split(",");
			preis=val[1];
			txt=document.pgartikel.artikel.options[nr].text.split(" ->");
			anz=1
			sum=preis;
			val=anz+";"+val[0]+";"+preis;
			NeuerEintrag = new Option(anz+" x "+txt[0]+" ("+preis+")",val,false,true);
			top.document.mat.elements[2].options[top.document.mat.elements[2].length] = NeuerEintrag;
		}
	//-->
	</script>
<form name="pgartikel">
<select name="artikel" size="7" Style="width:448px" onDblClick="putData()">
<?php
	$daten=getGrpArtikel($_GET["pg"]);
	if ($daten) foreach ($daten as $zeile) {
		if ($zeile["lastcost"]<>0) {
			$preis=sprintf("%0.2f",$zeile["lastcost"]);
			$text=substr($zeile["description"],0,54);
		} else  {
			$preis=sprintf("%0.2f",$zeile["sellprice"]);
			$text=substr($zeile["description"],0,50)." VKP";
                }
		echo "\t<option value='".$zeile["id"].",".$preis."'>".$text." ->".$preis."</option>\n";
	}
?>
</select>
</form>
