<?php
require_once __DIR__.'/../inc/stdLib.php';

//printArray( $GLOBALS['dbh']->getOne( "SELECT EXISTS  ( SELECT 1 FROM   information_schema.tables WHERE  table_schema = 'public' AND  table_name = 'crm') AS crm_exist " ) );

printArray( $GLOBALS['dbh']->query("DROP TABLE IF EXISTS test "));
printArray( $GLOBALS['dbh']->query("CREATE TABLE test( id serial, content text)"));

$myContent = "Hello ' World";
$myContent2 = "Hello ' updated World";

//printArray( $GLOBALS['dbh']->query( "INSERT INTO test (content) VALUES ('".$myContent."')" ) );//FALSCH
printArray( $GLOBALS['dbh']->insert( 'test', array('content'), array( $myContent ) ) );//RICHTIG

printArray( $GLOBALS['dbh']->query( "INSERT INTO test (content) VALUES ('tex')"));//FALSCH

printArray( $GLOBALS['dbh']->update( 'test', array('content'), array( $myContent2 ), 'id = 2    ' ) );//RICHTIG

printArray( $GLOBALS['dbh']->getAll( "SELECT * FROM test"));

?>