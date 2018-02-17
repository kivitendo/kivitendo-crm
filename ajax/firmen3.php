<?php
require_once __DIR__.'/../inc/ajax2function.php';

function getData( $data ){
    $table = $data['type'] == 'C' ? 'customer' : 'vendor';
    $term = trim( substr( $data['term'], strrpos( $data['term'], ' ') ) );
    $sql = "SELECT name AS value, '".$data['type']."' AS type, id  FROM $table WHERE name ILIKE '".$term."%' LIMIT 10";
    if( strlen( $term ) > 2 ) echo $GLOBALS[ 'dbh' ]->getAll( $sql, true );
}

?>