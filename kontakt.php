<?php
    require_once("inc/stdLib.php");
     include("inc/template.inc");
    include("inc/persLib.php");
    $co=getKontaktStamm($_GET["id"]);
    if ($co["cp_cv_id"]) {
        $Table=chkTable($co["cp_cv_id"]);
        header ("Location:".$_SESSION['baseurl']."crm/firma2.php?Q=$Table&id=".$_GET["id"]);
    } else {
        header ("Location:".$_SESSION['baseurl']."crm/firma2.php?id=".$_GET["id"]);
    }

?>
