<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/UserLib.php");

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
        ));
        $init = "\tvar initkat = ".$_GET['kdhelp'].";\n";
    } else {
        $popup = 'visible';
        doHeader($tpl);
        $init = "\tvar initkat = -1;\n";
    }

    if ($_SESSION['tinymce']) {
        $tiny  =  "<script language='javascript' type='text/javascript' src='inc/tiny_mce/tiny_mce.js'></script>\n";
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

    if ( ( $content["owner"] == $_SESSION["loginCRM"] ) || ( $content['owner'] == '' ) ) {
            $first[]=array("grpid"=>"","rechte"=>"w","grpname"=>translate('.:public:.','firma'));
            $first[]=array("grpid"=>$_SESSION['loginCRM'],"rechte"=>"w","grpname"=>translate('.:personal:.','firma'));
            $grp=getGruppen();
            if ($grp) {  $user=array_merge($first,$grp); 
            } else    {  $user=$first; };
            doBlock($tpl,"wi","OwenerListe","OL",$user,"grpid","grpname",$content["owener"]);
    } else {
            $user[0] = array("grpid"=>$daten["cp_owener"],"grpname"=>($daten["cp_owener"])?getOneGrp($daten["cp_owener"]):translate('.:public:.','firma'));
            doBlock($tpl,"wi1","OwenerListe","OL",$user,"grpid","grpname",$daten["cp_owener"]);
    }

    $tpl->Lpparse("out",array("wi"),$_SESSION["lang"],"work");
?>
