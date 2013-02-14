<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/crmLib.php");
    $t = new Template($base);
    $menu =  $_SESSION['menu'];
    if ($_GET['P']==1) {
        $t->set_var(array(
            JAVASCRIPTS   => '',
            STYLESHEETS   => $menu['stylesheets'],
            PRE_CONTENT   => '',
            START_CONTENT => '',
            END_CONTENT   => '',
            ERPCSS        => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
            JQUERY        => $_SESSION['basepath'].'crm/',
        ));
    } else {
        $t->set_var(array(
            JAVASCRIPTS   => $menu['javascripts'],
            STYLESHEETS   => $menu['stylesheets'],
            PRE_CONTENT   => $menu['pre_content'],
            START_CONTENT => $menu['start_content'],
            END_CONTENT   => $menu['end_content'],
            ERPCSS   => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
            JQUERY        => $_SESSION['basepath'].'crm/',
        ));
    }
    $t->set_file(array("doc" => "dokument.tpl"));
    $t->set_var(array(
            PICUP   => ($_GET['P']==1)?'true':'false',
            mandant => $_SESSION['mansel'],
            tiny    => ($tinymce)?'true':'false'
    ));
    $t->Lpparse("out",array("doc"),$_SESSION["lang"],"firma");
?>
