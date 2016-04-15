<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/crmLib.php");
    $t = new Template($base);
    $menu =  $_SESSION['menu'];
    if ( isset($_GET['P']) && $_GET['P']==1 ) {
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
            'ELFINDER'        => $head['ELFINDER'],
        ));
    } else {
        doHeader($t);
    }
    $t->set_file(array("doc" => "dokument.tpl"));
    $t->set_var(array(
            'PICUP'   => ( isset($_GET['P']) )?'true':'false',
            'mandant' => $_SESSION['dbname'],
            'tiny'    => ($_SESSION['tinymce'])?'true':'false'
    ));
    $t->Lpparse("out",array("doc"),$_SESSION['countrycode'],"firma");
?>
