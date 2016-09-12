<?php

require_once __DIR__.'/../inc/ajax2function.php';


function newContact( $data ){
    //writeLog($data);
    $data = ( array ) json_decode( $data );
//    writeLog($data);
    //wenn id == 0 neuen Datensatz, sonst Update
    $data['id'] ? $rs = $GLOBALS['dbh']->update( 'contact_events', array( 'cause', 'calldate','caller_id', 'contact_reference', 'employee', 'cause_long', 'type_of_contact', 'inout' ),
            array( $data['cause'], $data['calldate'], $data['caller_id'], 0, $_SESSION['loginCRM'], $data['cause_long'], $data['type_of_contact'], $data['inout'] ), "id =" .$data['id'])
        :   $rs = $GLOBALS['dbh']->insert( 'contact_events', array( 'cause', 'calldate','caller_id', 'contact_reference', 'employee', 'cause_long', 'type_of_contact', 'inout' ),
            array( $data['cause'], $data['calldate'], $data['caller_id'], 0, $_SESSION['loginCRM'], $data['cause_long'], $data['type_of_contact'], $data['inout'] ) );
    echo 1;
}

function getData(){
    //alle Datensätze bereitstellen
    $rs = $GLOBALS[ 'dbh' ]->getAll( 'SELECT * FROM contact_events', true );
    echo $rs;

}


?>