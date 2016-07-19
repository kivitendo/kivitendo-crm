<?php

require_once __DIR__.'/../inc/ajax2function.php';

function getCategories(){
     $sql = " SELECT json_agg (my_json) from (select row_to_json(x0) as my_json from (SELECT json_agg( i0 ) as maingroup FROM ( SELECT * FROM knowledge_category WHERE maingroup = 0 ORDER BY labeltext ) i0) x0 union all select row_to_json(x1) from (SELECT json_agg( i1 ) as undergroup FROM ( SELECT * FROM knowledge_category WHERE maingroup > 0 ORDER BY labeltext  ) i1) x1) xxx";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     //Falls es keine Hauptkategorie gibt, so wird ein Rechtsklick ermöglicht,  SEHR VERBESSERUNGSWÜRDIG, wird besser mit jQuery gemacht
     echo !json_decode( $rs['json_agg']  )['0']->maingroup ? '"[{\"maingroup\":[{\"id\":1,\"labeltext\":\"right-klick and new category!\",\"maingroup\":0,\"help\":false}]}, {\"undergroup\":null}]"' : json_encode( $rs['json_agg'] ); ;
}

function getArticle( $cat_id ){
     $rs = $GLOBALS['dbh']->query( 'UPDATE knowledge_content SET modifydate = now() WHERE category = '.$cat_id.' AND version = (SELECT max(version) FROM knowledge_content WHERE category = '.$cat_id.')'  );
     $sql = "SELECT json_agg (json) from (SELECT * FROM knowledge_content WHERE category = $cat_id AND version = ( SELECT max(version) FROM knowledge_content WHERE category = $cat_id ) ) json";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     if( $rs['json_agg'] == NULL ) {
        $sql = "SELECT json_agg (json) from ( SELECT * FROM knowledge_content WHERE category = $cat_id ORDER BY id DESC ) json";
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
    $version = getLastVersionNumber( $data['cat_id'] ) + 1;
    $rs = $GLOBALS['dbh']->insert( 'knowledge_content', array( 'modifydate', 'employee', 'content', 'version', 'category' ), array( 'now()', $_SESSION['id'], $data['content'], $version, $data['cat_id'] ) );
    echo 1;
}

function newCategory( $data ){
    $rc = $GLOBALS['dbh']->insert( 'knowledge_category', array( 'labeltext', 'maingroup', 'help' ), array( $data['catName'], $data['mainCheck'] == 'true' ? 0 : $data['cat_id'], 'FALSE' ) );
    $rs = $GLOBALS['dbh']->insert( 'knowledge_content', array( 'modifydate', 'employee', 'content', 'version', 'category' ), array( 'now()', $_SESSION['id'], $data['catName'], 1, $rc ) );
    echo 1;
}

function editCategory( $data ){
    $rs = $GLOBALS['dbh']->update( 'knowledge_category', array( 'labeltext'), array( $data['catName'] ), "id = ".$data['cat_id'] );
    echo 1;
}

function delCategory( $data ){
    $sql = "DELETE FROM knowledge_category WHERE id = '".$data."'";
    $rs = $GLOBALS['dbh']->query( $sql );
    $sql = "DELETE FROM knowledge_content WHERE category = '".$data."'";
    $rs = $GLOBALS['dbh']->query( $sql );
    echo 1;
}

function searchArt( $data ){
    $sql = "SELECT KCA.id, KCA.labeltext, KCO.version, KCO.content from knowledge_content KCO left join knowledge_category KCA on KCO.category=KCA.id where content ilike '%".$data."%' ORDER BY id,version DESC";
    $rs = $GLOBALS['dbh']->getAll( $sql );
    if( $rs ) $rs = array_values(unique_multidim_array($rs,'id'));
    //writeLog($rs);
    if( $rs ) foreach( $rs as $key => $value ){
        $pos = stripos( $value['content'], $data );
        $test = strip_tags( $value['content'] );
        $str_before = strip_tags( substr( $value['content'], 0, $pos ) );
        $str_after = strip_tags( substr( $value['content'], ( $pos+strlen($data) ) ) );
        $leng1 = strlen($str_before);
        $leng2 = strlen($str_after);
        $pos1 = ( $leng1 <= 60) ? $leng1 : 60;
        $pos2 = ($leng2 <= 60) ? $leng2 : 60;
        $tmp1 = substr( $str_before, ($leng1-$pos1) );
        $tmp2 = substr( $str_after, 0, $pos2 );
        $rs[$key]['content'] = $tmp1."<b>".$data."</b>".$tmp2;
    }
    echo json_encode( $rs );
}

function getLastVersionNumber( $category ){
    $rs = $GLOBALS['dbh']->getOne( "SELECT max(version) FROM knowledge_content WHERE category = ".$category );
    return $rs['max'];
}
?>
