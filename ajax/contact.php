<?php

require_once __DIR__.'/../inc/ajax2function.php';

function newContact( $data ){
    writeLog($data);
    $data = ( array ) json_decode( $data );
    writeLog($data);
    $rs = $GLOBALS['dbh']->insert( 'contact_events', array( 'cause', 'calldate','caller_id', 'contact_reference', 'employee', 'cause_long', 'type_of_contact', 'inout' ),
                                                    array( $data['cause'], $data['calldate'], $data['caller_id'], 0, $data['employee'], $data['cause_long'], $data['type_of_contact'], $data['inout'] ) );
    writeLog( $rs );
    echo 1;
}


/*function newEntry( $data ){
    writeLog( $data );
    $data = json_decode( $data );
    $data = ( array ) $data;
    writeLog($data);
    $rs = $GLOBALS[ 'dbh' ]->insert( 'example', array( 'date_time', 'c_name', 'c_age', 'c_comments' ), array( $data['datetime'], $data['name'], $data['age'], $data['comments']) );
    //writelog( $rs );
    echo 1;
}
*/



function getData(){
    //alle Datensätze bereitstellen
    $rs = $GLOBALS[ 'dbh' ]->getAll( 'SELECT * FROM contact_events', true );
//    $rs = $GLOBALS[ 'dbh' ]->getAll( 'SELECT * contact_events', true );
    writeLog( $rs );
    echo $rs;

}


?>