<?php
    require_once("inc/stdLib.php");
    include("inc/wvLib.php");
    $menu =  $_SESSION['menu'];
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
    <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['basepath'].'css/'.$_SESSION["stylesheet"]; ?>/main.css"></link>
    <!-- ERP Stylesheet -->
    <?php echo $menu['stylesheets']; ?>
    <!-- ERP JavaScripts -->
    <?php echo $menu['javascripts']; ?>
    <!-- Ende ERP -->
<body >
<?=$menu['pre_content'];?>
<?=$menu['start_content'];?>
<p class="listtop">Wartungsvertr&auml;ge suchen</p>
<?php
        if (count($vid)>1) {
            echo "<table>\n";
            foreach($vid as $nr) {
                echo "<tr><td>[<a href=vertrag3.php?vid=".$nr["cid"].">".$nr["cid"]."</a>]</td><td>".$nr["contractnumber"]."</td><td>".$nr["name"]."</td></tr>\n";
            }
            echo "</table>\n";
        }
        echo $msg;
?>
<form name="formular" enctype='multipart/form-data' action="vertrag1.php" method="post">
<input type="text" name="vid" size="20" value="" tabindex="1"> &nbsp; 
<input type="submit" name="ok" value="suchen"><br>Vertragsnummer
</form>
<?=$menu['end_content'];?>
</body>
</html>
