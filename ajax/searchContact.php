<?php

require_once __DIR__.'/../inc/ajax2function.php';

function getSearch(){
        $sw=$_GET["sw"];
        $Q=$_GET["Q"];
        $fid=$_GET["fid"];
        $sql="select calldate, cause, id, caller_id, contact_reference from contact_events where ( cause ilike '%$sw%' or cause_long ilike '%$sw%') ";
        $sql.="and (caller_id in (select cp_id from contacts where cp_cv_id=$fid) or caller_id=$fid)";
        $rs=$GLOBALS['dbh']->getAll($sql." order by contact_reference,calldate desc");
        writeLog($rs);
        echo json_encode($rs);
}

?>