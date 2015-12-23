<?php
require_once("../inc/ajax2function.php");

function getPostits(){
    $sql = "SELECT json_agg( json_postits ) FROM (SELECT * FROM postit WHERE employee = ".$_SESSION['id']." ) AS json_postits";
    $rs = $_SESSION['db']->getone( $sql );
    echo $rs['json_agg'];
    return 1;
}

function putPostit( $data ){
    $rc = $_SESSION['db']->insert( 'postit', array_keys( $data ), array_values( $data ) );
    return 1;
}

function upadtePostit( $data ){
    $rc = $_SESSION['db']->update( 'postit',array_keys( $data ), array_values( $data ), 'employee='.$_SESSION['id'] );
    return 1;
}





?>