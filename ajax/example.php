<?php

require_once __DIR__.'/../inc/ajax2function.php';

function newEntry( $data ){
    //writeLog( $data );
    $data = json_decode( $data );
    $data = ( array ) $data;
    //writeLog($data);
    $rs = $GLOBALS[ 'dbh' ]->insert( 'example', array( 'date_time', 'c_name', 'c_age', 'c_comments' ), array( $data['datetime'], $data['name'],$data['age'], $data['comments']) );
    //writelog( $rs );
    echo 1;
}

/*function getLastData() {
    //letzten Datensatz bereitstellen
    $sql = "SELECT json_agg ( json ) from ( SELECT * FROM example WHERE  id = (SELECT max( id ) FROM example ) ) json";
    $rs = $GLOBALS[ 'dbh' ]->getOne( $sql );
    writelog($rs[ 'json_agg' ]);
    $rs1 = trim( $rs[ 'json_agg' ], "[]" );
    //writelog( $rs1 );
    echo json_encode( $rs1 );
}*/

function getData(){
    //TODO: alle Datensätze bereitstellen
    //$sql = "SELECT json_agg ( json ) from ( SELECT * FROM example WHERE  id = ( SELECT max( id ) FROM example ) ) json";
    //$sql = "SELECT json_agg ( json ) from ( SELECT * FROM example ) json";
    //$sql = "select (json) from ( select * from example ) json;";
    //$rs = $GLOBALS[ 'dbh' ]->getAll( $sql );
    $rs = $GLOBALS[ 'dbh' ]->getAll( 'SELECT * FROM example', true );
    //$rs = $GLOBALS[ 'dbh' ]->getAll( 'example', 1 );
    //writeLog( $rs );
    //$rs = trim( $rs );
    writeLog( $rs );
    //writeLog( json_encode( $rs ) );
    //echo json_encode( $rs );
    //echo json_encode( json_decode( json_encode( $rs )));
    echo $rs;

}


?>