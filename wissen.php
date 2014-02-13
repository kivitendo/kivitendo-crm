<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include_once("inc/UserLib.php");

    $tpl = new Template($base);
    $tpl->set_file(array("wi" => "wissen.tpl"));
    $menu =  $_SESSION['menu'];

    if ( isset($_GET['kdhelp']) && $_GET["kdhelp"] > '0') {
        $popup = 'hidden';
        $head = mkHeader();
        $tpl->set_var(array(
            'JAVASCRIPTS'   => '',
            'STYLESHEETS'   => $menu['stylesheets'],
            'PRE_CONTENT'   => '',
            'START_CONTENT' => '',
            'END_CONTENT'   => '',
            'JQUERYUI'      => $head['JQUERYUI'],
            'THEME'         => $head['THEME'],
            'CRMCSS'        => $head['CRMCSS'],
            'JQUERY'        => $head['JQUERY'],
            'baseurl'       => $_SESSION['baseurl'],
        ));
        $init = "\tvar initkat = ".$_GET['kdhelp'].";\n";
    } else {
        $popup = 'visible';
        doHeader($tpl);
        $init = "\tvar initkat = -1;\n";
        $tpl->set_var(array(
            'baseurl'       => $_SESSION['baseurl'],
        ));
    }

    if ($_SESSION['tinymce']) {
        $tiny  =  "<script language='javascript' type='text/javascript' src='".$_SESSION['baseurl']."crm/inc/tiny_mce/tiny_mce.js'></script>\n";
        $init  .= "\tvar tiny = true;\n";
    } else {
        $init  .= "\tvar tiny = false;\n";
    };

    $tpl->set_var(array(
        'init'     => $init,
        'popup'    => $popup,
        'PICUP'    => "false",
        'tiny'     => $tiny,
        ));

    $tpl->Lpparse("out",array("wi"),$_SESSION["lang"],"work");
?>
