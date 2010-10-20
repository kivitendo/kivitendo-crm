<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/FirmenLib.php");
	include("inc/persLib.php");

	$pers=$_GET["pers"];
	$op=$_GET["op"];
	$nq=$_GET["nq"];
?>
<html>
	<script language="JavaScript">
	<!--
		function auswahl() {
			nr=document.firmen.Alle.selectedIndex;
			val=document.firmen.Alle.options[nr].value;
			tmp=document.firmen.Alle.options[nr].text;
			txt=tmp.substr(0,(tmp.length - 2));
			fid=val.substr(1,val.length);
<?php if ($pers==1) { ?>
			opener.document.formular.cp_cv_id.value=val;
			opener.document.formular.name.value=txt;
<?php } else if ($op) { ?>
			opener.document.formular.fid.value=fid;
			opener.document.formular.name.value=txt;
			opener.document.formular.tab.value=val.substr(0,1);
<?php } else if ($_GET["konzernname"]) {?>
			opener.document.neueintrag.konzern.value=fid;
			opener.document.neueintrag.konzernname.value=txt;
<?php } else {?>
			opener.document.formular.cp_cv_id.value=fid;
			opener.document.formular.name.value=txt;
<?php } 
 if ($nq==1) { ?>
			opener.document.formular.Quelle.value=val.substr(0,1);
<?php } ?>
		}
	//-->
	</script>
<body onLoad="self.focus()">
<center>Gefundene - Eintr&auml;ge:<br><br>
<form name="firmen">
<select name="Alle" >
	<option value=''>nichts markiert</option>
<?php
	$name=($_GET["name"])?strtoupper($_GET["name"]):strtoupper($_GET["konzernname"]);
	if ($name=="EINZELPERSON") $name="";
	if ($_GET["tab"]) {
		$tmp="daten".$_GET["tab"];
		$datenC=getAllFirmen(array(1,$name),true,$_GET["tab"]);
	} else {
		$datenC=getAllFirmen(array(1,$name),true,"C");
		$datenL=getAllFirmen(array(1,$name),true,"V");
	}
	if ($pers) {
		$datenP=getAllPerson(array(1,$name));
		if ($datenP) foreach ($datenP as $zeile) {
			echo "\t<option value='P".$zeile["cp_id"]."'>".$zeile["cp_name"].", ".$zeile["cp_givenname"]." P</option>\n";
		}
	}
	if ($datenC) {
        if ($_GET["tab"]=="V")
            $tab="L";
        else
            $tab="K";
        foreach ($datenC as $zeile) {
	    	echo "\t<option value='C".$zeile["id"]."'>".$zeile["customernumber"]." ".$zeile["name"]." $tab</option>\n";
        }
	}
	if ($datenL) foreach ($datenL as $zeile) {
		echo "\t<option value='V".$zeile["id"]."'>".$zeile["vendornumber"]." ".$zeile["name"]." L</option>\n";
	}

?>
</select><br>
<br>
<input type="button" name="ok" value="&uuml;bernehmen" onClick="auswahl()"><br>
<input type="button" name="ok" value="Fenster schlie&szlig;en" onClick="self.close();">
</form>
</body>
</html>
