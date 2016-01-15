<?php
require_once("../inc/ajax2function.php");

function getPostits(){
    $sql = "SELECT json_agg( json_postits ) FROM (SELECT * FROM postit WHERE employee = ".$_SESSION['id']." ) AS json_postits";
    $rs = $GLOBALS['dbh']->getone( $sql );
    echo $rs['json_agg'];
    return 1;
}

function putPostit( $data ){
    $rc = $GLOBALS['dbh']->insert( 'postit', array_keys( $data ), array_values( $data ) );
    return 1;
}

function upadtePostit( $data ){
    $rc = $GLOBALS['dbh']->update( 'postit',array_keys( $data ), array_values( $data ), 'employee='.$_SESSION['id'] );
    return 1;
}





?>