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
	$id = json_decode( $data );// meines Wissens wird hier kein json_decode benötigt
	echo $GLOBALS['dbh']->getAll('SELECT * FROM customer WHERE id='.$id->id.';'); // getOne() ist hier besser geeignet, ich würde es so schreiben
	echo  $GLOBALS['dbh']->getOne( 'SELECT * FROM customer WHERE id = '.$data['id']; // !!! Semikolon an Ende brachst du nicht schreiben
}

// Ich würde die vier Funktionen in eine packen
der Ajaxaufruf sähe dan wie folgt aus
data: [ action: 'getCVPA', data: [ item: ui.item.src, id:  ui.item.id ]], // das 'C' muss dann durch item... ersetzt werden

function getCVPA( $data ){ // get Customer, Vendor, Person, or Car (auto)
    $srcArray = array( 'C' => 'customer', 'V' => 'vendor', 'P' => 'contacts', 'A' => 'lxc_cars' );
    echo  $GLOBALS['dbh']->getOne( 'SELECT * FROM '.$srcArray[$data['item']].' WHERE id = '.$data['id'];
    // ev heißt id in lxc_cars 'c_id' dass sollte dann geändert werden
}

Ich heábe den Code nicht getestet - keine Brille..
Aber so in etwa sieht der Standardcode in LxCars und im CRM aus
    
