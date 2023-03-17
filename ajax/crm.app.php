<?php

require_once __DIR__.'/../inc/stdLib.php'; // for debug
require_once __DIR__.'/../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

function resultInfo($success, $text = '', $debug = false) {
	$info = '{ "success":'.(($success)? 'true' : 'false');
	if(!empty($text)) if(!$success || $debug) $info .= ', "debug":"'.$text.'"';
	echo $info.' }';
}

function fastSearch(){
	if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ) {
		$term = $_GET['term'];
		echo $GLOBALS['dbh']->getAll("(SELECT 'Kunde' AS category, 'C' AS src, '' AS value, id, name AS label FROM customer WHERE name ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'Lieferant' AS category, 'V' AS src, '' AS value, id, name AS label FROM vendor WHERE name ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'Kontaktperson' AS category, 'P' AS src, '' AS value, cp_id AS id, concat(cp_givenname, ' ', cp_name) AS name FROM contacts WHERE cp_name ILIKE '%".$term."%' OR cp_givenname ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'Fahrzeug' AS category, 'L' AS src, c_ln AS value, c_id AS id, name AS label FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ILIKE '%".$term."%' AND obsolete = false LIMIT 5)", true);
	}
}

function getCustomer( $data ){
	$id = json_decode( $data );
	echo $GLOBALS['dbh']->getAll('SELECT * FROM customer WHERE id='.$id->id.';');
}
