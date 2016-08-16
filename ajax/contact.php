<?php

require_once __DIR__.'/../inc/ajax2function.php';

function newContact( $data ){
    //writeLog('-----sent data:-----');        
    writeLog($data);
    $data = ( array ) json_decode( $data );
    writeLog('-----Array for DB:-----');        
    writeLog($data);
//    $rs = $GLOBALS['dbh']->insert( 'telcall', array( 'caller_id',  'cause', 'calldate', 'c_long', 'employee', 'kontakt', '"inout"', 'bezug'), array( $data['caller_id'], $data['cause'], $data['calldate'], $data['c_long'], $data['employee'], $data['type_of_contact'], $data['direction_of_contact'], 0) );
//    $rs = $GLOBALS['dbh']->insert( 'telcall', array( 'caller_id',  'calldate'), array( $data['callerid'],  'now()'  ) );

    // in neue Tabelle tnew01
    $rs = $GLOBALS['dbh']->insert( 'tnew01', array( 'calldate', 'cause', 'bezug' ), array( 'now()', $data['cause'], 0 ) );
    writeLog('Anzahl Datensätze: ' .$rs );
//    echo json_encode($rs);
    echo 1;
    //echo true;
    //echo $rs;
    //echo json_encode('1');
    
}

?>