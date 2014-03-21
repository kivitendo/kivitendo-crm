<?php
	require_once("inc/stdLib.php");
	$ort=$_GET["ort"];
	//Umlaute wandeln hängt von der Serverumgebung ab!!
	//$loc_de = setlocale (LC_ALL, 'de_DE@euro', 'de_DE', 'de', 'ge');
	//HACK!!
	$ort=strtr($ort,array(chr(228)=>"AE",chr(246)=>"OE",chr(252)=>"UE",chr(223)=>"SS"));
	$ort=strtoupper($ort);
	$plz=$_GET["plz"];
	$wo=$_GET["wo"];
	if ($plz and $ort) {
	  $sql="SELECT loc_id,text_val,text_type from geodb_textdata where loc_id in ";
	  $sql.="(SELECT distinct(loc_id) from geodb_textdata where ";
	  $sql.="text_val like '%".$plz."%' and text_type = 500300000 and ";
	  $sql.="loc_id in ( SELECT loc_id from geodb_textdata where ";
	  $sql.="(text_val like '%".$ort."%' and text_type = 500100002))) ";
	  $sql.="and (text_type = 500100000 or text_type = 500300000  or text_type = 500400000) order by loc_id";
	} else if ($plz) {
	  $sql="SELECT loc_id,text_val,text_type from geodb_textdata where text_type in ";
	  $sql.="(500100000,500300000,500400000) and loc_id in (";
	  $sql.="SELECT loc_id FROM geodb_textdata where text_val like '%".$plz."%' and text_type = 500300000) ";
	  $sql.="order by loc_id";
	} else if ($ort) {
	  $sql="SELECT loc_id,text_val,text_type from geodb_textdata where text_type in ";
	  $sql.="(500100000,500300000,500400000) and loc_id in (";
	  $sql.="SELECT loc_id FROM geodb_textdata where text_val like '%".$ort."%' and text_type = 500100002) ";
	  $sql.="order by loc_id";
	}
	$rs=$_SESSION['db']->getAll($sql);
	$ort="";
	if ($rs) foreach ($rs as $zeile) {
		if ($zeile["text_type"]==500100000) {
			$ort=$zeile["text_val"];
			$data[$zeile["loc_id"]]["ort"]=$zeile["text_val"];
		} else if ($zeile["text_type"]==500400000) {
			$data[$zeile["loc_id"]]["tel"]=$zeile["text_val"];
		} else {
			$data[$zeile["loc_id"]]["plz"][]=$zeile["text_val"];
		}
	}
        $menu =  $_SESSION['menu'];
?>
<html>
<head><title></title>
    <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['baseurl'].'css/'.$_SESSION["stylesheet"]; ?>/main.css"></link>
    <!-- ERP Stylesheet -->
    <?php echo $menu['stylesheets']; ?>
	<script language="JavaScript">
	<!--
	var wo = '<?php echo  $wo ?>';
	function auswahl() {
		nr=document.firmen.Alle.selectedIndex;
		val=document.firmen.Alle.options[nr].value;
		tmp=val.split("--");		
		if (wo=="R" || wo=="P") {
			opener.document.getElementById("zipcode").value=tmp[0];
			opener.document.getElementById("city").value=tmp[1];
			opener.document.getElementById("phone").value=tmp[2];
		} else {
			opener.document.getElementById("shiptozipcode").value=tmp[0];
			opener.document.getElementById("shiptocity").value=tmp[1];
			opener.document.getElementById("shiptophone").value=tmp[2];
		}
	}
	//-->
	</script>
</head>
<body onLoad="self.focus()">
<center>Gefundene - Eintr&auml;ge:<br><br>
<form name="firmen">
<select name="Alle" >
<?php
	if ($rs) foreach ($data as $key=>$zeile) {
		$tmp=array_keys($zeile);
		$ort=$zeile["ort"]; 
		$plz=$zeile["plz"];
		$tel=$zeile["tel"];
		foreach ($plz as $nix=>$val) {
			echo "\t<option value='".$val."--".$ort."--".$tel."'>".$val." ".$ort." (".$tel.")\n"; //$zeile["500300000"]." ".$zeile["500100002"]."</option>\n";
		}
	}
?>
</select><br>
<br>
<input type="button" name="ok" value="&uuml;bernehmen" onClick="auswahl()"><br>
<input type="button" name="ok" value="Fenster schlie&szlig;en" onClick="self.close();">
</form>
</body>
</html>
