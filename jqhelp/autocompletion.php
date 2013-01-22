<?php 
//Sourcefile for Autocompletion 
if (empty($_GET['term'])) exit;
require_once("../inc/stdLib.php"); 

if ($_GET['case']=='name') { 
    require_once("inc/crmLib.php");     
    require_once("inc/FirmenLib.php"); 
    require_once("inc/persLib.php"); 
    $suchwort = mkSuchwort($_GET['term']); 
    $rsC = getAllFirmen($suchwort,true,"C"); 
    $rsV = getAllFirmen($suchwort,true,"V"); 
    $rsK = getAllPerson($suchwort); 
    $rs = array(); 
    if ($rsC) foreach ( $rsC as $key => $value ) { 
        if (count($rs) > 11) break;
        array_push($rs,array('label'=>$value['name'],'category'=>'')); 
    } 
    if ($rsV) foreach ( $rsV as $key => $value ) {
        if (count($rs) > 11) break; 
        array_push($rs,array('label'=>$value['name'],'category'=>'Lieferanten'));//ToDo translate 
    } 
    if ($rsK) foreach ( $rsK as $key => $value ) {
        if (count($rs) > 11) break;  
        array_push($rs,array('label'=>$value['cp_givenname']." ".$value['cp_name'],'category'=>'Personen'));//ToDo translate 
    } 
    echo json_encode($rs); 
} 
?> 
