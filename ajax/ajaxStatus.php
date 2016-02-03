<?php
require_once __DIR__.'/../inc/ajax2function.php';

function showVersion(){
     $rs = $GLOBALS['dbh']->getOne( "select * from crm order by  version DESC, datum DESC" );
     echo json_encode( $rs );
}

function saveDBs(){
    $date = date( 'Y-m-d\TH-i-s' );
    $dbAuthName = $_SESSION['erpConfig']['authentication/database']['db'];
    $dbName     = $_SESSION['dbData']['dbname'];
    $fileName[$dbAuthName] = $dbAuthName.'-'.$date.'.sql';
    $fileName[$dbName]     = $dbName.'-'.$date.'.sql';
    exec( "ls -t ".__DIR__."/../db_dumps/*  | sed '1,12d' | xargs rm -r" );
    exec( 'pg_dump '.$dbAuthName.' > '.__DIR__.'/../db_dumps/'.$fileName[$dbAuthName].' && pg_dump '.$dbName.' > '.__DIR__.'/../db_dumps/'.$fileName[$dbName], $output, $error );
    echo !$error ? json_encode( $fileName ) : '';
}

function showDbFiles(){
    $fileArray = scandir( __DIR__.'/../db_dumps' );
    $remove = array( '.', '..', '.gitignore' );
    foreach( $remove as $remValue ) unset( $fileArray[array_search( $remValue, $fileArray )] );
    echo json_encode( $fileArray );
}
?>