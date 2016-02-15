<?php
require_once __DIR__.'/../inc/stdLib.php';


//printArray( $GLOBALS['dbh']->query( 'DELETE FROM postitall' ) );
//printArray( $GLOBALS['dbh']->query( " INSERT INTO events ( duration, title, description, \"allDay\", uid, visibility, category, prio, job, color, done, location, cust_vend_pers, repeat, repeat_factor, repeat_quantity, repeat_end ) VALUES ( '[2016-01-20 10:30:00, 2016-01-20 11:00:00)','sssss','', false, , -1, 10, 1, '0', '', 'false', '', '', 'day', '0', '0', NULL ) " ));
//printArray( $GLOBALS['dbh']->getAll( "SELECT *  FROM postitall ORDER BY iduser, split_part(idnote, '_', 2)::int " ) );
//printArray( $GLOBALS['dbh']->getAll( "SELECT * FROM version " ) );
//printArray( $GLOBALS['dbh']->getAll( "select count(*) as total from postitall where iduser='1'" ) );


$GLOBALS['dbh']->begin();
$sql = "CREATE TABLE test(
    id      serial NOT NULL PRIMARY KEY,
    label   text,
    color      char(7)
);";

//printArray( $GLOBALS['dbh']->exec( $sql ) );

$sql = "
INSERT INTO test ( label, color ) VALUES ( 'test_label', 'rot' );

";






printArray( $GLOBALS['dbh']->exec( $sql ) );

$sql = "
INSERT INTO test ( label, color ) VALUES ( 'test_label1', 'blue' );

";
printArray( $GLOBALS['dbh']->exec( $sql ) );
$GLOBALS['dbh']->commit();

$rs = $GLOBALS['dbh']->getAll( 'select * from test' );
//foreach( $rs as $key => $value ){
    //printArray( $key );
    //printArray( $value['tag'] );
  //  $test[$key] = $value['tag'];
    //printArray( $test );
    //$value['tag' );
//}

printArray( $rs );


//printArray( $dbh_auth->getAll( 'select * from auth.user_config' ) );
//printArray();


?>