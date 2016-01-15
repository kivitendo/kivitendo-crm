<?php
require_once "inc/stdLib.php";
//printArray( $_SESSION['name'] );

$sql = "SELECT * FROM employee";
//echo $sql;

//printArray( $_SESSION['db']->getAll( $sql ));
//printArray( $dbh->getAll( $sql ));
//$old = $_SESSION['db']->getAll( $sql );

 printArray($GLOBALS['dbh_auth']->getAll( 'select * from auth.user_config' ));
 //printArray($dbh_auth->getAll( 'select * from auth.user_config' ));
//printArray( );

//if($diff) echo "VERSCHIEDEN";


//else

?>