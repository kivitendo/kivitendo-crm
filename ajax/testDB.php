<?php

require_once("../inc/ajax2function.php");

function showDB(){
     $rs = $GLOBALS['dbh']->getOne("select * from crm order by  version DESC, datum DESC");
	 echo json_encode($rs);
}

?>