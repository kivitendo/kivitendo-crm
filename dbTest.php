<?php
require_once "inc/stdLib.php";
printArray( $_SESSION['name'] );

$sql = "SELECT * FROM employee";
echo $sql;

printArray( $_SESSION['db']->getAll( $sql ));
printArray( $_SESSION['dbPDO']->getAll( $sql ));
//$old = $_SESSION['db']->getAll( $sql );
//$new = $_SESSION['dbPDO']->getAll( $sql );
printArray( $diff = array_diff_assoc( $_SESSION['db']->getOne( $sql ),$_SESSION['dbPDO']->getAll( $sql )));

if($diff) echo "VERSCHIEDEN";


//else 

?>