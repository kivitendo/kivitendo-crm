<?php
require_once "inc/stdLib.php";
//printArray( $_SESSION['name'] );

//$sql = "SELECT * FROM employee";
//echo $sql;

//printArray( $GLOBALS['dbh']->getAll( $sql ));
//printArray( $dbh->getAll( $sql ));
//$old = $GLOBALS['dbh']->getAll( $sql );

 printArray($GLOBALS['dbh']->getAll( 'SELECT * FROM postitall' ));
 //printArray($dbh_auth->getAll( 'select * from auth.user_config' ));
//printArray( );

//if($diff) echo "VERSCHIEDEN";


//else

?>