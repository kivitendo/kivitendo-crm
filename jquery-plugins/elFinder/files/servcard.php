<?php
require_once("inc/stdLib.php");
    $codecs = array("ISO_8859-1","ISO_8859-15","ASCII","UTF-8","UTF-7","Windows-1252");
    $srvcode = strtoupper($_SESSION["charset"]);
    $p = sprintf("sel%d",array_search($srvcode,$codecs));
    ${$p} = "selected";
?>

    <script>
    $(document).ready(
        $('#send').click(function(event) {
            event.preventDefault();
            single = $("input[name='single']:checked").val();
            ext = $('#extension').val();
            tc = $('#targetcode option:selected').val();
            zip = $("input[name='zip']:checked").val();
            $.get('jqhelp/serien.php?task=vcard&single='+single+'&extension='+ext+'&targetcode='+tc+'&zip='+zip,function(rc){ $('#ergebnis').empty().append(rc); } )
        })
    );
    </script>

<form name="vcard" method="post" action="servcard.php">
   Serverkodierung: <?php echo $srvcode ?>
   Zielkodierung:  <select name="targetcode" id="targetcode">
        <option value="ISO_8859-1" <?php echo $sel0 ?>>ISO_8859-1</option>
        <option value="ISO_8859-15" <?php echo $sel1 ?>>ISO_8859-15</option>
        <option value="ASCII" <?php echo $sel2 ?>>ASCII</option>
        <option value="UTF-8" <?php echo $sel3 ?>>UTF-8</option>
        <option value="UTF-7" <?php echo $sel4 ?>>UTF-7</option>
        <option value="Windows-1252" <?php echo $sel5 ?>>Windows-1252</option>
    </select><br />
   Extension: <input type="input" name="extension" id="extension" size="5" value="vcf"><br />
   Zip-Komprimierung <input type="radio" name="zip" value="0" checked>Nein <input type="radio" name="zip" value="1">Ja<br />
   Singel-File <input type="radio" name="single" value="1" checked><br /> Je Adresse ein File  <input type="radio" name="single" value="0"><br />
   <button id="send" name="send">erstellen</button><br />

<div id="ergebnis"></div>

