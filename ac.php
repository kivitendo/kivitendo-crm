<?php 
/******************************************************************************************** 
***           AutoComplete für Suche,                                                     *** 
********************************************************************************************/ 
require_once( "inc/stdLib.php" ); 
require_once( "inc/persLib.php" ); 
 
if( isset( $_GET['search'] ) ){ 
        $mode = 0; 
} 
 
switch( $mode ){ 
    case 0: 
    require_once( "inc/crmLib.php" ); 
    require_once( "inc/FirmenLib.php" ); 
    require_once( "inc/persLib.php" ); 
    $suchwort = mkSuchwort( $_GET['q'] ); 
    $rsC = getAllFirmen( $suchwort, true, "C" ); 
    $rsV = getAllFirmen( $suchwort, true, "V" ); 
    $rsK = getAllPerson( $suchwort ); 
    $rs = ""; 
    foreach( $rsC as $key => $value ){ 
        $rs .= $value['name']."\n"; 
    } 
    foreach( $rsV as $key => $value ){ 
        $rs .= $value['name']."\n"; 
    } 
    foreach( $rsK as $key => $value ){ 
        $rs .= $value['cp_givenname']." ".$value['cp_name']."\n"; 
    } 
    echo $rs ; 
    break; 
} 
 
?> 
