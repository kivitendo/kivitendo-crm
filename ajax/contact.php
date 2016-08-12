<?php

require_once __DIR__.'/../inc/ajax2function.php';

function newContact( $data ){
    writeLog($data);
    $data = json_decode( $data );
    $data = ( array ) $data;
    //$data['date'] = strftime("%Y/%m/%d  %H:%M:%S", $data['date'] );   
    writeLog($data);
    $rs = $GLOBALS['dbh']->insert( 'telcall', array( 'caller_id',  'cause', 'calldate', 'c_long', 'employee', 'kontakt', 'inout', 'bezug'), array( 891, $data['subject'], $data['date'], $data['comments'], 890, $data['type_of_contact'], $data['direction_of_contact'], 0) );
    echo 1;
}


?>