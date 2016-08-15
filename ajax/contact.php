<?php

require_once __DIR__.'/../inc/ajax2function.php';

function newContact( $data ){
    //writeLog('-----sent data:-----');        
    writeLog($data);
    $data = ( array ) json_decode( $data );
    //$data = ( array ) $data;
    writeLog('-----Array for DB:-----');        
    writeLog($data);
    $rs = $GLOBALS['dbh']->insert( 'telcall', array( 'caller_id',  'cause', 'calldate', 'c_long', 'employee', 'kontakt', 'inout', 'bezug'), array( 891, $data['subject'], $data['date'], $data['comments'], 890, $data['type_of_contact'], $data['direction_of_contact'], 0) );
    writeLog( $rs );
    echo 1;
    //echo true;
    //echo $rs;
    //echo json_encode('1');
    
}
/*function newEntry( $data ){
    //writeLog( $data );
    $data = json_decode( $data );
    $data = ( array ) $data;
    //writeLog($data);
    $rs = $GLOBALS[ 'dbh' ]->insert( 'example', array( 'date_time', 'c_name', 'c_age', 'c_comments' ), array( $data['datetime'], $data['name'],$data['age'], $data['comments']) );
    //writelog( $rs );
    echo 1;
}*/

?>