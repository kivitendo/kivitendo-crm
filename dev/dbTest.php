<?php
require_once __DIR__.'/../inc/stdLib.php';


//printArray( $GLOBALS['dbh']->query( 'DELETE FROM postitall' ) );
//printArray( $GLOBALS['dbh']->query( " INSERT INTO events ( duration, title, description, \"allDay\", uid, visibility, category, prio, job, color, done, location, cust_vend_pers, repeat, repeat_factor, repeat_quantity, repeat_end ) VALUES ( '[2016-01-20 10:30:00, 2016-01-20 11:00:00)','sssss','', false, , -1, 10, 1, '0', '', 'false', '', '', 'day', '0', '0', NULL ) " ));
//printArray( $GLOBALS['dbh']->getAll( "SELECT *  FROM postitall ORDER BY iduser, split_part(idnote, '_', 2)::int " ) );
//printArray( $GLOBALS['dbh']->getAll( "SELECT * FROM version " ) );
//printArray( $GLOBALS['dbh']->getAll( "select count(*) as total from postitall where iduser='1'" ) );



printArray( $GLOBALS['dbh']->getAll( 'select * from employee ' ) );
//foreach( $rs as $key => $value ){
    //printArray( $key );
    //printArray( $value['tag'] );
  //  $test[$key] = $value['tag'];
    //printArray( $test );
    //$value['tag' );
//}



//printArray( $dbh_auth->getAll( 'select * from auth.user_config' ) );
//printArray();


?>