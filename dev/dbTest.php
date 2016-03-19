<?php
require_once __DIR__.'/../inc/stdLib.php';

//printArray( $GLOBALS['dbh']->getOne( "SELECT EXISTS  ( SELECT 1 FROM   information_schema.tables WHERE  table_schema = 'public' AND  table_name = 'crm') AS crm_exist " ) );



printArray( $GLOBALS['dbh']->getAll( "SELECT json_agg (xxx) from (SELECT * FROM knowledge_content WHERE category = 50 ORDER BY version DESC ) xxx"));// SELECT json_agg (xxx) from (SELECT * FROM knowledge_content WHERE category = 50 ORDER BY version DESC ) xxx

?>