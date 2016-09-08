<?php

require_once __DIR__.'/../inc/ajax2function.php';

function openInvoice( $data ){
	$sql = "SELECT * FROM ar WHERE customer_id = " .$data. " AND amount > paid";
	$rs = $GLOBALS['dbh']->getOne( $sql );
	if($rs) {
		echo 1;
	}
	else echo 0;
}

?>