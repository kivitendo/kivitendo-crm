<?php
// $Id$
    require_once("inc/stdLib.php");
    include("inc/crmLib.php");

    $part=$_GET["part"];
    $daten = getPart($part);

?>
<html>
    <head><title>Artikelsuche</title>
    <script language="JavaScript">
    <!--
        function auswahl() {
            nr = document.parts.Alle.selectedIndex;
            if ( nr == 0 ) return;
            val = document.parts.Alle.options[nr].value;
            txt = document.parts.parttext.value;
            if ( txt == '' ) txt=document.parts.Alle.options[nr].text;
            qty = document.parts.qty.value;
            line = qty + " * " + txt;
            value = qty+'|'+val+'|'+txt;
            NeuerEintrag = new Option(line, value, false, true);
            opener.document.getElementById('parts').options[opener.document.getElementById('parts').length] = NeuerEintrag;
        };
        function chgtxt() {
            nr = document.parts.Alle.selectedIndex;
            if ( nr == 0 ) return;
            txt = document.parts.Alle.options[nr].text;
            document.parts.parttext.value = txt;
        };
    //-->
    </script>
    </head>
<body onLoad="self.focus()">
<center>Gefundene - Eintr&auml;ge:<br>
<form name="parts">
<table><tr><td><?php echo translate('.:qty:.','work') ?></td><td><?php echo translate('.:part:.','work') ?></td></tr>
<tr><td><input type="text" size="10" name="qty" value="1" style="text-align:right;"></td><td>
<select name="Alle" style='width:35em;' >
    <option value=''>nichts markiert</option>
<?php
        foreach ($daten as $zeile) {
            echo "\t<option value='".$zeile["id"]."'>".$zeile["description"]."</option>\n";
        }

?>
</select></td></tr>
<tr><td><input type="button" name="txtchg" id="txtchg" onClick="chgtxt()" value="<?php echo translate('.:txtchg:.','work') ?>"></td>
    <td><input type="text" name="parttext" id="parttext" size="45">
        <input type="button" name="clr" id="clr" value="<?php echo translate('.:clr:.','work') ?>" onClick="document.parts.parttext.value=''"></td></tr>
</table>
<br>
<input type="button" name="ok" value="&uuml;bernehmen" onClick="auswahl()"><br><br>
<input type="button" name="ok" value="Fenster schlie&szlig;en" onClick="self.close();">
</form>
</body>
</html>
