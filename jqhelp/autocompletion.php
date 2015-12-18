<?php
//Sourcefile for Autocompletion
if (empty($_GET['term'])) exit;
require_once("../inc/stdLib.php");

if ($_GET['case']=='name') {
    require_once("../inc/crmLib.php");
    require_once("../inc/FirmenLib.php");
    require_once("../inc/persLib.php");
    require_once("../inc/UserLib.php");
    $suchwort = mkSuchwort($_GET['term']);
    $rsC = getAllFirmen($suchwort,true,"C");
    $rsV = getAllFirmen($suchwort,true,"V");
    $rsK = getAllPerson($suchwort);
    $rsE = getAllUser($suchwort);
    $rs = array();
    if ($rsC) foreach ( $rsC as $key => $value ) {
        if (count($rs) > 11) break;
        array_push($rs,array('label'=>$value['name'],'category'=>'','src'=>'C','id'=>$value['id']));
    }
    if ($rsV) foreach ( $rsV as $key => $value ) {
        if (count($rs) > 11) break;
        array_push($rs,array('label'=>$value['name'],'category'=>translate('.:vendors:.','firma'),'src'=>'V','id'=>$value['id']));
        if(isset($_GET['src']) && $_GET['src']=='cv') {
            echo json_encode($rs);
            return;
        }
    }
    if ($rsK) foreach ( $rsK as $key => $value ) {
        if (count($rs) > 11) break;
        array_push($rs,array('label'=>$value['cp_givenname']." ".$value['cp_name'],'category'=>translate('.:personen:.','firma'),'src'=>'K','id'=>$value['id']));
    }
    if ($rsE) foreach ( $rsE as $key => $value ) {
        if (count($rs) > 11) break;
        array_push($rs,array('label'=>$value['name'],'category'=>translate('.:users:.','firma'),'src'=>'E','id'=>$value['id']));
    }
    echo json_encode($rs);
}
?>
