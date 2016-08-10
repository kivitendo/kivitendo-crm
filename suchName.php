<?php
    require_once("inc/stdLib.php");
    include("inc/FirmenLib.php");
    include("inc/persLib.php");

?>
<html>
    <script language="JavaScript">
    <!--
    function auswahl() {
        nr=document.firmen.Alle.selectedIndex;
        val=document.firmen.Alle.options[nr].value;
        txt=document.firmen.Alle.options[nr].text;
        NeuerEintrag = new Option(txt,val,false,true);
        opener.document.getElementById("istusr").options[opener.document.getElementById("istusr").length] = NeuerEintrag;
    }
    //-->
    </script>
<body onLoad="self.focus()">
<center>Gefundene - Eintr&auml;ge:<br><br>
<form name="firmen">
<select name="Alle" >
<?php
    $name=strtoupper($_GET["name"]);
    $pers=getAllPerson(array(1,$name));
    $cust=getAllFirmen(array(1,$name),true,"C");
    $vend=getAllFirmen(array(1,$name),true,"V");
    $daten = array();
    if ($pers) $daten=$pers;
    if ($cust) $daten=array_merge($daten,$cust);
    if ($vend) $daten=array_merge($daten,$vend);
    if ($daten) foreach ($daten as $zeile) {
        echo "\t<option value='".$zeile["tab"].$zeile["id"]."'>".$zeile["name"]."</option>\n";
    }
?>
</select><br>
<br>
<input type="button" name="ok" value="&uuml;bernehmen" onClick="auswahl()"><br>
<input type="button" name="ok" value="Fenster schlie&szlig;en" onClick="self.close();">
</form>
</body>
</html>
