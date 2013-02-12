<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/UserLib.php");
    include("inc/crmLib.php");
    $tpl = new Template($base);
    $tpl->set_file(array("wi" => "wissen.tpl"));
    $menu =  $_SESSION['menu'];
    if ($_GET["kdhelp"] <> '1') {
        $popup = 'visible';
        $tpl->set_var(array(
            JAVASCRIPTS   => $menu['javascripts'],
            STYLESHEETS   => $menu['stylesheets'],
            PRE_CONTENT   => $menu['pre_content'],
            START_CONTENT => $menu['start_content'],
            END_CONTENT   => $menu['end_content'],
            ERPCSS        => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
            'JQUERY'        => $_SESSION['basepath'].'crm/'
        ));
    } else {
        $popup = 'hidden';
        $tpl->set_var(array(
            JAVASCRIPTS   => '',
            STYLESHEETS   => $menu['stylesheets'],
            PRE_CONTENT   => '',
            START_CONTENT => '',
            END_CONTENT   => '',
            ERPCSS        => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
            'JQUERY'        => $_SESSION['basepath'].'crm/'
        ));
    }

    if ($tinymce) {
        $tiny  = "<script language='javascript' type='text/javascript' src='inc/tiny_mce/tiny_mce.js'></script>\n";
    }
    $catname=getOneWCategorie($tmp[0]);
    $tpl->set_var(array(
        popup    => $popup,
        PICUP    => "false",
        tiny     => $tiny,
        authority => translate('.:authority:.','firma')
        ));
    if ( ( $content["owner"] == $_SESSION["loginCRM"] ) || ( $content['owner'] == '' ) ) {
            $first[]=array("grpid"=>"","rechte"=>"w","grpname"=>translate('.:public:.','firma'));
            $first[]=array("grpid"=>$_SESSION['loginCRM'],"rechte"=>"w","grpname"=>translate('.:personal:.','firma'));
            $grp=getGruppen();
            if ($grp) {
                $user=array_merge($first,$grp); 
            } else { $user=$first; };
            doBlock($tpl,"wi","OwenerListe","OL",$user,"grpid","grpname",$content["owener"]);
    } else {
            $user[0] = array("grpid"=>$daten["cp_owener"],"grpname"=>($daten["cp_owener"])?getOneGrp($daten["cp_owener"]):translate('.:public:.','firma'));
            doBlock($tpl,"wi1","OwenerListe","OL",$user,"grpid","grpname",$daten["cp_owener"]);
    }
    $tpl->Lpparse("out",array("wi"),$_SESSION["lang"],"work");
?>
