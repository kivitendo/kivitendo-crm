<?php

require_once __DIR__.'/../inc/ajax2function.php';

function getCategories(){
     $sql = " select json_agg (my_json) from (select row_to_json(x0) as my_json from (SELECT json_agg( i0 ) as maingroup FROM ( SELECT * FROM knowledge_category WHERE maingroup = 0 ) i0) x0 union all select row_to_json(x1) from (SELECT json_agg( i1 ) as undergroup FROM ( SELECT * FROM knowledge_category WHERE maingroup > 0 ) i1) x1) xxx";
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] );
}

function getArticle($cat_id){
     $sql = " select json_agg (xxx) from (SELECT * FROM knowledge_content WHERE category = $cat_id ORDER BY version DESC ) xxx";         
     $rs = $GLOBALS['dbh']->getOne( $sql );
     echo json_encode( $rs['json_agg'] ); 
}

function updateContent(){
}

?>
