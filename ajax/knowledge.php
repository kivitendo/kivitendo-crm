<?php

require_once __DIR__.'/../inc/ajax2function.php';

function getCategories(){
     $sql = " SELECT json_agg (my_json) from (select row_to_json(x0) as my_json from (SELECT json_agg( i0 ) as maingroup FROM ( SELECT * FROM knowledge_category WHERE maingroup = 0 ORDER BY labeltext ) i0) x0 union all select row_to_json(x1) from (SELECT json_agg( i1 ) as undergroup FROM ( SELECT * FROM knowledge_category WHERE maingroup > 0 ORDER BY labeltext  ) i1) x1) xxx";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] );
}

function getArticle($cat_id){
     $sql = " SELECT json_agg (xxx) from (SELECT * FROM knowledge_content WHERE category = $cat_id ORDER BY version DESC ) xxx";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] ); 
}

function updateContent( $data ){
   $parts = explode("___", $data);
   //Letzten Eintrag
   $sql = "SELECT * FROM knowledge_content WHERE category = ".$parts[0]." ORDER BY id DESC LIMIT 1";
   $rs = $GLOBALS['dbh']->getOne( $sql );
   //UPDATE SQL
   $sql = "UPDATE knowledge_content SET initdate = now(), employee = ".$_SESSION['id'].", content = '".$parts[1]."' WHERE id = ".$rs['id'];
   $rs = $GLOBALS['dbh']->query( $sql );
   echo json_encode("ok");
}

function nextVersion( $data ){
    $parts = explode("___", $data);
    $vers = "SELECT max(version) FROM knowledge_content WHERE category = ".$parts[0];
    $rs = $GLOBALS['dbh']->getOne($vers);
    $versi = $rs['max']+1;
    $sql = "INSERT INTO (initdate, employee, content, version, category) VALUES ()";
    echo json_encode("ok");
}

function newCategory( $data ){
   echo json_encode("ok");
}

?>
