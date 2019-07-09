<?php
require_once __DIR__.'/../inc/ajax2function.php';

function saveDefaults( $data ){
  $last_old_id = $GLOBALS['dbh']->getOne( 'SELECT max( id ) AS last_old_id FROM crmdefaults' )['last_old_id'];
  if( $GLOBALS['dbh']->insertMultiple( 'crmdefaults', $data ) ){ 
    $GLOBALS['dbh']->query( 'DELETE FROM crmdefaults WHERE id <= '.$last_old_id );
    echo 0;
  }
  else echo json_encode( 'SQL-Error in '.__FUNCTION__.'()!' );
}

function getDefaults(){
  $sql = "SELECT key, COALESCE( val , '' ) AS val FROM crmdefaults"; //NULL to ''
  echo $GLOBALS['dbh']->getALL( $sql, TRUE ); //as json
}
?>