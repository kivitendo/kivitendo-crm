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
    //Löschen oder deaktivieren der Kategorie ?
    //Unterkategorie löschen ?
    //Inhalte löschen - alle Versionen oder Deaktivierung ?
}

function searchArt( $data ){
    $sql = "SELECT KCA.id, KCA.labeltext, KCO.version, KCO.content from knowledge_content KCO left join knowledge_category KCA on KCO.category=KCA.id where content ilike '%".$data."%' ORDER BY id,version DESC";
    $rs = $GLOBALS['dbh']->getAll( $sql );
    if( $rs ) $rs = array_values(unique_multidim_array($rs,'id'));
    //writeLog($rs);
    if( $rs ) foreach( $rs as $key => $value ){
        $pos = stripos( $value['content'], $data );
        $str_before = substr( $value['content'], 0, $pos );
        $str_after = substr( $value['content'], ( $pos+strlen($data) ) );
        $pos_before = strpos_all( $str_before, "<br" );
        $pos_after = strpos( $str_after, "<br" );
        $tmp = $pos_before[count($pos_before) - 1];
        $tmp1 = strip_tags( substr( $str_before, $tmp ) );
        $tmp2 = strip_tags( substr( $str_after, 0, $pos_after ) );
        $rs[$key]['content'] = $tmp1."<b>".$data."</b>".$tmp2;
    }
    echo json_encode( $rs );
}

function getLastVersionNumber( $category ){
    $rs = $GLOBALS['dbh']->getOne( "SELECT max(version) FROM knowledge_content WHERE category = ".$category );
    return $rs['max'];
}
?>
