<?php

require_once __DIR__.'/../inc/ajax2function.php';

function getCategories(){
     $sql = " SELECT json_agg (my_json) from (select row_to_json(x0) as my_json from (SELECT json_agg( i0 ) as maingroup FROM ( SELECT * FROM knowledge_category WHERE maingroup = 0 ORDER BY labeltext ) i0) x0 union all select row_to_json(x1) from (SELECT json_agg( i1 ) as undergroup FROM ( SELECT * FROM knowledge_category WHERE maingroup > 0 ORDER BY labeltext  ) i1) x1) xxx";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] );
}

function getArticle( $cat_id ){
     $rs = $GLOBALS['dbh']->query( 'UPDATE knowledge_content SET modifydate = now() WHERE category = '.$cat_id.' AND version = (SELECT max(version) FROM knowledge_content WHERE category = '.$cat_id.')'  );
     $sql = "SELECT json_agg (xxx) from (SELECT * FROM knowledge_content WHERE category = $cat_id ORDER BY version DESC ) xxx";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] );
}

function getLastArticle(){
     $sql = "SELECT json_agg (xxx) from (SELECT * FROM knowledge_content WHERE  modifydate = (SELECT max(modifydate) FROM knowledge_content ) ) xxx";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] );
}

function updateContent( $data ){
   //Letzten Eintrag
   //$sql = "SELECT * FROM knowledge_content WHERE category = ".$data['cat_id']." ORDER BY id DESC LIMIT 1";
   //$rs = $GLOBALS['dbh']->getOne( $sql );
   //UPDATE SQL
   writeLog($data);
   $rs = $GLOBALS['dbh']->update( 'knowledge_content', array( 'modifydate', 'employee', 'content' ), array( 'now()', $_SESSION['id'] , $data['content'] ), "category = ".$data['cat_id'] );
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
   //Hauptkategorie
   if( $data['mainCheck'] == "true") {
        $rs = $GLOBALS['dbh']->insert( 'knowledge_category', array('labeltext', 'maingroup', 'help' ), array($data['catName'], 0, "FALSE") );
        $sql = "SELECT MAX(id) FROM knowledge_category";
        $rc = $GLOBALS['dbh']->getOne( $sql );
        $rs = $GLOBALS['dbh']->insert( 'knowledge_content', array( 'modifydate', 'employee', 'content', 'version', 'category' ), array( 'now()', $_SESSION['id'], 'neue Kategorie', 1, $rc['max'] ) );
   }
   //Unterkategorie
   else {
        $rs = $GLOBALS['dbh']->insert( 'knowledge_category', array('labeltext', 'maingroup', 'help' ), array($data['catName'], $data['cat_id'], "FALSE") );
        $sql = "SELECT MAX(id) FROM knowledge_category";
        $rc = $GLOBALS['dbh']->getOne( $sql );
        $rs = $GLOBALS['dbh']->insert( 'knowledge_content', array( 'modifydate', 'employee', 'content', 'version', 'category' ), array( 'now()', $_SESSION['id'], 'neue Kategorie', 1, $rc['max'] ) );
   }
   echo json_encode( "ok" );
}

function editCategory( $data ){
    $rs = $GLOBALS['dbh']->update( 'knowledge_category', array( 'labeltext'), array( $data['catName'] ), "id = ".$data['cat_id'] );
    echo json_encode( "ok" );
}

function delCategory( $data ){

}

function searchArt( $data ){
    $sql = "SELECT distinct KCA.* as cid from knowledge_content KCO left join knowledge_category KCA on KCO.category=KCA.id where content ilike '%".$data."%'";
    $founds = $GLOBALS['dbh']->getAll($sql);
    //writeLog("test");
    echo json_encode($founds);
}

?>
