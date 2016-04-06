<?php

require_once __DIR__.'/../inc/ajax2function.php';

function getCategories(){
     $sql = " SELECT json_agg (my_json) from (select row_to_json(x0) as my_json from (SELECT json_agg( i0 ) as maingroup FROM ( SELECT * FROM knowledge_category WHERE maingroup = 0 ORDER BY labeltext ) i0) x0 union all select row_to_json(x1) from (SELECT json_agg( i1 ) as undergroup FROM ( SELECT * FROM knowledge_category WHERE maingroup > 0 ORDER BY labeltext  ) i1) x1) xxx";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] );
}

function getArticle( $cat_id ){
     $rs = $GLOBALS['dbh']->query( 'UPDATE knowledge_content SET modifydate = now() WHERE category = '.$cat_id.' AND version = (SELECT max(version) FROM knowledge_content WHERE category = '.$cat_id.')'  );
     $sql = "SELECT json_agg (json) from (SELECT * FROM knowledge_content WHERE category = $cat_id ORDER BY version DESC ) json";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] );
}

function getLastArticle(){ //Holt den zuletzt gelesenen Artikel
     $sql = "SELECT json_agg (json) from (SELECT * FROM knowledge_content WHERE  modifydate = (SELECT max(modifydate) FROM knowledge_content ) ) json";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] );
}

function updateContent( $data ){
    $version = getLastVersionNumber( $data['cat_id'] );
    if( $version == $data['version']) $rs = $GLOBALS['dbh']->update( 'knowledge_content', array( 'modifydate', 'employee', 'content' ), array( 'now()', $_SESSION['id'] , $data['content'] ), "category = ".$data['cat_id']." AND version = ".$version );
    else  $rs = $GLOBALS['dbh']->insert( 'knowledge_content', array( 'modifydate', 'employee', 'content', 'version', 'category' ), array( 'now()', $_SESSION['id'], $data['content'], $version + 1, $data['cat_id'] ) );
    echo json_encode("ok");
}

function getOtherVersion( $data ){ // holt voherige oder nachfolgende Version eines Artikels
    $lastVersion = getLastVersionNumber( $data['cat_id'] );
    $version = $data['version'] >=  $lastVersion ? $lastVersion : $data['version'];
    $version = $version < 1 ? 1 : $version;
    $sql = "SELECT json_agg (json) from (SELECT * FROM knowledge_content WHERE category = ".$data['cat_id']." AND version = ".$version." ) json";
    $rs = $GLOBALS['dbh']->getOne( $sql );
    echo json_encode( $rs['json_agg'] );
}

function createNewVersion( $data ){ // erstellt eine neue Version
    $version = getLastVersionNumber( $data['cat_id'] ) + 1; writeLog( $version );
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
    $sql = "SELECT distinct KCA.id, KCA.labeltext, KCO.version, KCO.content from knowledge_content KCO left join knowledge_category KCA on KCO.category=KCA.id where content ilike '%".$data."%'";
    $rs = $GLOBALS['dbh']->getAll( $sql );
    //writeLog($rs);
    if( $rs ) foreach( $rs as $key => $value ){
        // es soll nur die Zeile mit dem Such-String zurückgegeben werden
        // es sollen in dieser Zeile alle Tags entfernt werden
        // der Suchstring soll fett  oder kursiv markiert werden
        $pos = stripos( $value['content'], $data );
        $test = strip_tags( $value['content'] );
        $pos = stripos( $test, $data );
        $rs[$key]['content'] = $test.' '.$pos;
    }
    echo json_encode( $rs );
}

function getLastVersionNumber( $category ){
    $rs = $GLOBALS['dbh']->getOne( "SELECT max(version) FROM knowledge_content WHERE category = ".$category );
    return $rs['max'];
}
?>
