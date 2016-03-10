<?php

require_once __DIR__.'/../inc/ajax2function.php';

function getCategories(){
     $sql = " SELECT json_agg (my_json) from (select row_to_json(x0) as my_json from (SELECT json_agg( i0 ) as maingroup FROM ( SELECT * FROM knowledge_category WHERE maingroup = 0 ORDER BY labeltext ) i0) x0 union all select row_to_json(x1) from (SELECT json_agg( i1 ) as undergroup FROM ( SELECT * FROM knowledge_category WHERE maingroup > 0 ORDER BY labeltext  ) i1) x1) xxx";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] );
}

function getArticle( $cat_id ){
     $sql = " SELECT json_agg (xxx) from (SELECT * FROM knowledge_content WHERE category = $cat_id ORDER BY version DESC ) xxx";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] ); 
}

function getLastArticle( $cat_id ){
     $sql = " SELECT json_agg (xxx) from (SELECT * FROM knowledge_content WHERE category = $cat_id AND version = ( (SELECT max(version) FROM knowledge_content WHERE category = ".$cat_id." )-1 ) ) xxx";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] );
}

function updateContent( $data ){
   //Letzten Eintrag
   $sql = "SELECT * FROM knowledge_content WHERE category = ".$data['cat_id']." ORDER BY id DESC LIMIT 1";
   $rs = $GLOBALS['dbh']->getOne( $sql );
   //UPDATE SQL
   $sql = "UPDATE knowledge_content SET initdate = now(), employee = ".$_SESSION['id'].", content = '".$data['content']."' WHERE id = ".$rs['id'];
   $rs = $GLOBALS['dbh']->query( $sql );
   echo json_encode("ok");
}

function nextVersion( $data ){
    $vers = "SELECT max(version) FROM knowledge_content WHERE category = ".$data['cat_id'];
    $rs = $GLOBALS['dbh']->getOne($vers);
    $version = $rs['max']+1;
    $sql = "INSERT INTO knowledge_content (initdate, employee, content, version, category) VALUES (now() , ".$_SESSION['id']." , '".$data['content']."' , ".$version." , ".$data['cat_id']." )";
    $rs = $GLOBALS['dbh']->query( $sql );
    echo json_encode("ok");
}

function newCategory( $data ){
   echo json_encode( "ok" );
}

?>
