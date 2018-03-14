<?php

require_once __DIR__.'/../inc/ajax2function.php';

function getCategories(){
     $sql = " SELECT json_agg (my_json) from (select row_to_json(x0) as my_json from (SELECT json_agg( i0 ) as maingroup FROM ( SELECT * FROM knowledge_category WHERE maingroup = 0 ORDER BY labeltext ) i0) x0 union all select row_to_json(x1) from (SELECT json_agg( i1 ) as undergroup FROM ( SELECT * FROM knowledge_category WHERE maingroup > 0 ORDER BY labeltext  ) i1) x1) xxx";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] );
}

function getArticle( $data ){
     $rs = $GLOBALS['dbh']->query( 'UPDATE knowledge_content SET modifydate = now() WHERE category = '.$data['data'].' AND version = (SELECT max(version) FROM knowledge_content WHERE category = '.$data['data'].')'  );
     $sql = "SELECT json_agg (json) from (SELECT * FROM knowledge_content WHERE category =" .$data['data']. "AND version = ( SELECT max(version) FROM knowledge_content WHERE category =".$data['data']." ) ) json";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     if( $rs['json_agg'] == NULL ) {
        $sql = "SELECT json_agg (json) from ( SELECT * FROM knowledge_content WHERE category = ".$data['data']." ORDER BY id DESC ) json";
        $rs = $GLOBALS['dbh']->getOne( $sql );
        $GLOBALS['dbh']->update( 'knowledge_content', array( 'version' ), array( 1 ), "id = ".json_decode($rs['json_agg'])[0]->id );
     }
     echo json_encode( $rs['json_agg'] );
}

function getLastArticle(){ //Holt den zuletzt gelesenen Artikel
     $sql = "SELECT json_agg (json) from (SELECT * FROM knowledge_content WHERE  modifydate = (SELECT max(modifydate) FROM knowledge_content ) ) json";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] );
}

function updateContent( $data ){
    $version = getLastVersionNumber( $data['cat_id'] );
    if( $version == $data['version']) echo $GLOBALS['dbh']->update( 'knowledge_content', array( 'modifydate', 'employee', 'content' ), array( 'now()', $_SESSION['id'] , $data['content'] ), "category = ".$data['cat_id']." AND version = ".$version );
    else  echo $GLOBALS['dbh']->insert( 'knowledge_content', array( 'modifydate', 'employee', 'content', 'version', 'category' ), array( 'now()', $_SESSION['id'], $data['content'], $version + 1, $data['cat_id'] ) );
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
    $version = getLastVersionNumber( $data['cat_id'] ) + 1;
    $rs = $GLOBALS['dbh']->insert( 'knowledge_content', array( 'modifydate', 'employee', 'content', 'version', 'category' ), array( 'now()', $_SESSION['id'], $data['content'], $version, $data['cat_id'] ) );
    echo 1;
}

function newCategory( $data ){
    $rc = $GLOBALS['dbh']->insert( 'knowledge_category', array( 'labeltext', 'maingroup', 'help' ), array( $data['catName'], $data['mainCheck'] == 'true' ? 0 : $data['cat_id'], 'FALSE' ) );
    $rs = $GLOBALS['dbh']->insert( 'knowledge_content', array( 'modifydate', 'employee', 'content', 'version', 'category' ), array( 'now()', $_SESSION['id'], $data['catName'], 1, $rc ) );
    echo $rc;
}

function editCategory( $data ){
    $rs = $GLOBALS['dbh']->update( 'knowledge_category', array( 'labeltext'), array( $data['catName'] ), "id = ".$data['cat_id'] );
    echo 1;
}

function delCategory( $data ){
    $sql = "DELETE FROM knowledge_category WHERE id = '".$data['data']."'";
    $rs = $GLOBALS['dbh']->query( $sql );
    $sql = "DELETE FROM knowledge_content WHERE category = '".$data['data']."'";
    $rs = $GLOBALS['dbh']->query( $sql );
    echo 1;
}

function searchArt( $data ){
    $sql = "SELECT DISTINCT ON ( KCA.id )  KCA.id, ( SELECT labeltext FROM knowledge_category WHERE id = KCA.maingroup ) || ' / ' || KCA.labeltext AS labeltext, KCO.version, KCO.content FROM knowledge_content KCO LEFT JOIN knowledge_category KCA ON KCO.category = KCA.id WHERE content ILIKE '%".$data['data']."%' OR labeltext ILIKE '".$data['data']."%' ORDER BY id, version DESC";
    $rs = $GLOBALS['dbh']->getAll( $sql );
    if( $rs ) foreach( $rs as $key => $value ){
        $search_len = strlen( $data['data'] );
        $value['content'] = strip_tags( $value['content'] );
        $pos = stripos( $value['content'], $data['data'] );
        $first = $pos - 60 <= 0 ? 0 : $pos - 60; //$first must not be negative
        $value['content'] = substr( $value['content'],  $first, 120 );
        $rs[$key]['content'] = preg_replace( "/".$data['data']."/i", '<b>$0</b>', $value['content'] ); //bold

    }
    //writeLog( json_last_error() ); //5 = JSON_ERROR_UTF8
    echo json_encode( $rs, 512 ); //JSON_PARTIAL_OUTPUT_ON_ERROR => 512
}

function getLastVersionNumber( $category ){
    $rs = $GLOBALS['dbh']->getOne( "SELECT max(version) FROM knowledge_content WHERE category = ".$category );
    return $rs['max'];
}
?>
