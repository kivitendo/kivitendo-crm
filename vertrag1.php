<?php
    require_once("inc/stdLib.php");
    include("inc/wvLib.php");
    $menu =  $_SESSION['menu'];
    $head = mkHeader();

    if ($_POST["ok"]) {
        $vid=suchVertrag($_POST["vid"]);
        if (!$vid) {
            $msg="Kein Vertrag gefunden";
        } else if (count($vid)==1) {
            header("location:vertrag3.php?vid=".$vid[0]["cid"]);
        }
    }    
?>
<html>
    <head><title></title>
<?php echo $menu['stylesheets']; ?>
<?php echo $head['CRMCSS']; ?>
<?php echo $head['JQUERY']; ?>
<?php echo $head['JQUERYUI']; ?>
<?php echo $head['THEME']; ?>
<?php echo $head['JQTABLE']; ?>
<?php echo $menu['javascripts']; ?>
    <script>
    $(function() {
        $("#treffer")
            .tablesorter({widthFixed: true, widgets: ['zebra']})
    });    
    </script>

<body >
<?php echo $menu['pre_content'];?>
<?php echo $menu['start_content'];?>
<p class="listtop">Wartungsvertr&auml;ge suchen</p>
<form name="formular" enctype='multipart/form-data' action="vertrag1.php" method="post">
<input type="text" name="vid" size="20" value="" tabindex="1"> &nbsp; 
<input type="submit" name="ok" value="suchen"><br>Vertragsnummer
</form>
<?php  echo $msg; ?><br>
<table id='treffer' class='tablesorter'>
<thead><tr ><th>Vertragsnummer</th><th>Kunde</th></tr></thead>
<tbody>
<?php
        if (count($vid)>1) {
            foreach($vid as $nr) {
                echo "<tr><td>[<a href=vertrag3.php?vid=".$nr["cid"].">".$nr["contractnumber"]."</a>]</td><td>".$nr["name"]."</td></tr>\n";
            }
        }
?>
</tbody>
</table>
<?php echo $menu['end_content'];?>
</body>
</html>
