<?php
require_once __DIR__.'/../inc/ajax2function.php';



function showVersion(){
     $rs = $GLOBALS['dbh']->getOne( "select * from crm order by  version DESC, datum DESC" );
	 echo json_encode( $rs );
}

function saveDBs(){
    //$error = 0;
    $date = date( 'Y-m-d\TH-i-s' );
    $dbAuthName = $_SESSION['erpConfig']['authentication/database']['db']; 
    $dbName     = $_SESSION['dbData']['dbname'];
    $dbAuthFileName = $dbAuthName.'-'.$date.'.sql';
    $dbFileName = $dbName.'-'.$date.'.sql';
    exec( 'pg_dump '.$dbAuthName.' > '.__DIR__.'/../db_dumps/'.$dbAuthFileName.' && pg_dump '.$dbName.' > '.__DIR__.'/../db_dumps/'.$dbFileName, $output, $error );
    //echo json_encode( $dbAuthName.' saved in: '.$dbAuthFileName);
    echo json_encode( 'Auth-DB in '.$dbAuthFileName.' und <br> DB in '.$dbFileName.' gesichert'  );
   // printArray( $_SESSION['erpConfig'] );
   //echo 'test';//$test;
} 
?>