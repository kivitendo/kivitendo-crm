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
   $rs = $GLOBALS['dbh']->update( 'knowledge_content', array( 'modifydate', 'employee', 'content' ), array( 'now()', $_SESSION['id'] , $data['content'] ), "id = ".$rs['id'] );
   echo json_encode("ok");
}

function nextVersion( $data ){
    $vers = "SELECT max(version) FROM knowledge_content WHERE category = ".$data['cat_id'];
    $rs = $GLOBALS['dbh']->getOne($vers);
    $version = $rs['max']+1;
    $rs = $GLOBALS['dbh']->insert( 'knowledge_content', array( 'modifydate', 'employee', 'content', 'version', 'category' ), array( 'now()', $_SESSION['id'], $data['content'], $version, $data['cat_id'] ) );
    echo json_encode("ok");
}

function newCategory( $data ){
   echo json_encode( "ok" );
}

function searchArt( $data ){
    $sql = "SELECT distinct KCA.* as cid from knowledge_content KCO left join knowledge_category KCA on KCO.category=KCA.id where content ilike '%".$data."%'";
    $founds = $GLOBALS['dbh']->getAll($sql);
    //writeLog(json_encode($rs));
    echo json_encode($founds);
}

?>
