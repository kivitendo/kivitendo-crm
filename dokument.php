<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/crmLib.php");
    $t = new Template($base);
    $menu =  $_SESSION['menu'];
    $t->set_var(array(
        JAVASCRIPTS   => $menu['javascripts'],
        STYLESHEETS   => $menu['stylesheets'],
        PRE_CONTENT   => $menu['pre_content'],
        START_CONTENT => $menu['start_content'],
        END_CONTENT   => $menu['end_content'],
        ERPCSS   => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
        JQUERY        => $_SESSION['basepath'].'crm/',
    ));
    $t->set_file(array("doc" => "dokument.tpl"));
    $t->set_var(array(
            ERPCSS  => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
            PICUP   => $pickup,
    ));
    $t->Lpparse("out",array("doc"),$_SESSION["lang"],"firma");
?>
