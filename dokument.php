<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/crmLib.php");
    $t = new Template($base);
    $menu =  $_SESSION['menu'];
    if ($_GET['P']==1) {
        $head = mkHeader();
        $t->set_var(array(
            'JAVASCRIPTS'   => '',
            'STYLESHEETS'   => $menu['stylesheets'],
            'PRE_CONTENT'   => '',
            'START_CONTENT' => '',
            'END_CONTENT'   => '',
            'CRMPATH'       => $head['CRMPATH'],
            'JQUERYUI'      => $head['JQUERYUI'],
            'THEME'         => $head['THEME'],
            'CRMCSS'        => $head['CRMCSS'],
            'JQUERY'        => $head['JQUERY'],
        ));
    } else {
        doHeader($t);
    }
    $t->set_file(array("doc" => "dokument.tpl"));
    $t->set_var(array(
            PICUP   => ($_GET['P']==1)?'true':'false',
            mandant => $_SESSION['mansel'],
            tiny    => ($_SESSION['tinymce'])?'true':'false'
    ));
    $t->Lpparse("out",array("doc"),$_SESSION["lang"],"firma");
?>
