<?php
require_once __DIR__.'/../inc/stdLib.php';

printArray( $GLOBALS['dbh']->getOne( "SELECT EXISTS  ( SELECT 1 FROM   information_schema.tables WHERE  table_schema = 'public' AND  table_name = 'crm') AS crm_exist " ) );

printArray( $GLOBALS['dbh']->getOne( " WITH tmp AS ( UPDATE defaults SET sonumber = sonumber::INT + 1 RETURNING sonumber) INSERT INTO oe ( ordnumber, customer_id, employee_id, taxzone_id, currency_id, c_id )  SELECT ( SELECT sonumber FROM tmp), 1126, 861,  customer.taxzone_id, customer.currency_id, 14 FROM customer WHERE customer.id = '1126' RETURNING id "));


//printArray( $GLOBALS['dbh']->getAll( "SELECT json_agg (xxx) from (SELECT * FROM knowledge_content WHERE category = 50 ORDER BY version DESC ) xxx"));// SELECT json_agg (xxx) from (SELECT * FROM knowledge_content WHERE category = 50 ORDER BY version DESC ) xxx

//printArray( $GLOBALS['dbh']->getAll( "SELECT * FROM oe WHERE c_id = 14 "));

//Zum Testen von updateAll
$data = array( array( 'label10', '#FFF', '120', '100' ), array( 'label2', '#000', '11', '200' ), array( 'label2', '#111', '10', '300' ) );
$columns = array( 'label' => 'text', 'color' => 'text', 'id' => 'int', 'cat_order' => 'int' );
echo $GLOBALS['dbh']->updateAll( 'event_category', $columns, $data );

$data = array( array( '12', '101' ), array( '11', '202' ), array( '10', '303' ) );
$columns = array( 'id' => 'int', 'cat_order' => 'int' );
echo $GLOBALS['dbh']->updateAll( 'event_category', $columns, $data );

?>