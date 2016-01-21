<?php
require_once "inc/stdLib.php";
//printArray( $_SESSION['name'] );

//$sql = "SELECT * FROM employee";
//echo $sql;

//printArray( $GLOBALS['dbh']->getAll( $sql ));
//printArray( $dbh->getAll( $sql ));
//$old = $GLOBALS['dbh']->getAll( $sql );
 printArray($GLOBALS['dbh']->query( " INSERT INTO events ( duration, title, description, \"allDay\", uid, visibility, category, prio, job, color, done, location, cust_vend_pers, repeat, repeat_factor, repeat_quantity, repeat_end ) VALUES ( '[2016-01-20 10:30:00, 2016-01-20 11:00:00)','sssss','', false, , -1, 10, 1, '0', '', 'false', '', '', 'day', '0', '0', NULL ) " ));
 printArray($GLOBALS['dbh']->getAll( 'SELECT * FROM postitall' ));
 //printArray($dbh_auth->getAll( 'select * from auth.user_config' ));
//printArray( );

//if($diff) echo "VERSCHIEDEN";
//INSERT INTO events ( duration, title, description, \"allDay\", uid, visibility, category, prio, job, color, done, location, cust_vend_pers, repeat, repeat_factor, repeat_quantity, repeat_end ) VALUES ( '[2016-01-20 10:30:00, 2016-01-20 11:00:00)','sssss','', false, , -1, 10, 1, '0', '', 'false', '', '', 'day', '0', '0', NULL )


//else

?>