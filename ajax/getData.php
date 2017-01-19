<?php

require_once __DIR__.'/../inc/ajax2function.php';

/*
function newEntry( $data ){
    writeLog( $data );
    $data = json_decode( $data );
    $data = ( array ) $data;
    writeLog($data);
    $rs = $GLOBALS[ 'dbh' ]->insert( 'example', array( 'date_time', 'c_name', 'c_age', 'c_comments' ), array( $data['datetime'], $data['name'],$data['age'], $data['comments']) );
    echo 1;
}
*/

function getHistory(){

    $sql  = "select val from crmemployee where uid = '" . $_SESSION["loginCRM"];
    $sql .= "' AND manid = ".$_SESSION['manid']." AND key = 'search_history'";
    $rs =   $GLOBALS['dbh']->getOne( $sql );
    //writeLog( $sql );

    echo $rs['val'];

}


?>