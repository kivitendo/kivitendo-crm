<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/crmLib.php");
    include_once("inc/UserLib.php");
    $templ="wvln.tpl";
    $t = new Template($base);
    doHeader($t);
    $t->set_file(array("wvl" => $templ));
    $t->set_var(array(
            'timeout'     => $_SESSION['interv']*1000,
            ));
    $sel=$_SESSION["loginCRM"];
    $usr = getAllUser(array(0=>true,1=>"%"));
    $gruppen = getGruppen(true);
    $nouser[0] = array("login" => "-----", "id"=>0 );
    $user = array_merge($nouser,$usr);
    $user = array_merge($user,$gruppen);
    $t->set_block("wvl","Selectbox","Block1");
    if ($user) foreach($user as $zeile) {
        $t->set_var(array(
            'Sel'     => ($sel==$zeile["id"])?" selected":"",
            'UID'     => $zeile["id"],
            'Login'   => ( isset($zeile['name']) and $zeile['name'] != '' )?$zeile['name']:$zeile["login"],
        ));
        $t->parse("Block1","Selectbox",true);
    }
    $t->pparse("out",array("wvl"));
?>
