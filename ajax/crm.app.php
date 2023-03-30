<?php

require_once __DIR__.'/../inc/stdLib.php'; // for debug
require_once __DIR__.'/../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

function resultInfo($success, $text = '', $debug = false){
    $info = '{ "success":'.(($success)? 'true' : 'false');
    if(!empty($text)) if(!$success || $debug) $info .= ', "debug":"'.$text.'"';
    echo $info.' }';
}

function getHistory(){
    $rs = $GLOBALS['dbh']->getOne( "SELECT val FROM crmemployee WHERE uid = '" . $_SESSION["loginCRM"]."' AND manid = ".$_SESSION['manid']." AND key = 'search_history'" );
    echo $rs['val'] ? $rs['val'] : '0';
}

function fastSearch(){
    if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ) {
        $term = $_GET['term'];
        echo $GLOBALS['dbh']->getAll("(SELECT 'Kunde' AS category, 'C' AS src, '' AS value, id, name AS label FROM customer WHERE name ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'Lieferant' AS category, 'V' AS src, '' AS value, id, name AS label FROM vendor WHERE name ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'Kontaktperson' AS category, 'P' AS src, '' AS value, cp_id AS id, concat(cp_givenname, ' ', cp_name) AS name FROM contacts WHERE cp_name ILIKE '%".$term."%' OR cp_givenname ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'Fahrzeug' AS category, 'A' AS src, c_ln AS value, c_id AS id, name AS label FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ILIKE '%".$term."%' AND obsolete = false LIMIT 5)", true);
    }
}

function getCVPA( $data ){
    $query = "SELECT ";
    if($data['src'] == 'C' || $data['src'] == 'V' ){
        // Stammdaten
        $db_table = array('C' => 'customer', 'V' => 'vendor');
        $query .= "(SELECT row_to_json( cv ) AS cv FROM (SELECT '".$data['src']."' AS src, id, name, street, zipcode, contact, phone AS phone1, fax AS phone2, email, city, country, contact AS person FROM ".$db_table[$data['src']]." WHERE id=".$data['id'].") AS cv) AS cv, ";

        // Angebote
        $id = array('C' => 'customer_id', 'V' => 'vendor_id');
        $query .= "(SELECT json_agg( off ) AS off FROM (".
                    "SELECT DISTINCT ON (oe.id) to_char(oe.transdate, 'DD.MM.YYYY') as date, description, COALESCE(ROUND(amount,2))||' '||COALESCE(C.name) as amount, ".
                    "oe.quonumber as number, oe.id FROM oe LEFT JOIN orderitems ON oe.id=trans_id LEFT JOIN currencies C on currency_id=C.id WHERE quotation = TRUE AND ".$id[$data['src']]." = ".$data['id']." ORDER BY oe.id DESC, orderitems.id".
                    ") AS off) AS off, ";
        // Aufträge
        $query .= "(SELECT json_agg( ord ) AS ord FROM (".
                    "SELECT DISTINCT ON (oe.itime) to_char(oe.transdate, 'DD.MM.YYYY') as date, COALESCE( instructions.description, orderitems.description ) AS description, COALESCE(ROUND(amount,2))||' '||COALESCE(C.name) as amount, ".
                    "oe.ordnumber as number, oe.id FROM oe LEFT JOIN orderitems ON oe.id = orderitems.trans_id LEFT JOIN instructions ON oe.id = instructions.trans_id LEFT JOIN currencies C on currency_id=C.id WHERE quotation = FALSE AND ".$id[$data['src']]." = ".$data['id']." AND EXISTS(SELECT * FROM information_schema.tables WHERE table_name = 'lxc_ver') ORDER BY oe.itime DESC, orderitems.itime".
                    ") AS ord) AS ord, ";
        // Lieferscheine
        $query .= "(SELECT json_agg( del ) AS del FROM (".
                    "SELECT DISTINCT ON (delivery_orders.id) delivery_orders.id, to_char(delivery_orders.transdate, 'DD.MM.YYYY') as date, description, to_char(delivery_orders.reqdate, 'DD.MM.YYYY') as deldate, donumber ".
                    "FROM delivery_orders LEFT JOIN delivery_order_items ON delivery_orders.id = delivery_order_id WHERE ".$id[$data['src']]." = ".$data['id']." AND closed = FALSE ORDER BY delivery_orders.id DESC".
                    ") AS del) AS del, ";
        // Rechnungen
        $db_table = array('C' => 'ar', 'V' => 'ap');
        $query .= "(SELECT json_agg( inv ) AS inv FROM (".
                    "SELECT DISTINCT ON (".$db_table[$data['src']].".id) to_char(".$db_table[$data['src']].".transdate, 'DD.MM.YYYY') as date, description, COALESCE(ROUND(amount,2))||' '||COALESCE(C.name) as amount, ".
                    "invnumber as number, ".$db_table[$data['src']].".id FROM ".$db_table[$data['src']]." LEFT JOIN invoice  ON ".$db_table[$data['src']].".id=trans_id LEFT JOIN currencies C on currency_id=C.id  WHERE ".$id[$data['src']]." = ".$data['id']." ORDER BY ".$db_table[$data['src']].".id DESC, invoice.id".
                    ") AS inv) AS inv, ";
    }

    // Fahrzeuge
    $query .= "(SELECT json_agg( cars ) AS cv FROM (SELECT c_ln AS ln, '--------' AS manuf, '-----' AS ctype, '---' AS cart FROM lxc_cars WHERE c_ow = ".$data['id']." ORDER BY c_id) AS cars) AS cars";

    echo $GLOBALS['dbh']->getOne($query, true);
}

function getCustomerForEdit( $data ){
    $db_table = array('C' => 'customer', 'V' => 'vendor');
    $query = "SELECT ";

    // costumer or vendor -> cv
    $query .= "(SELECT row_to_json( cv ) AS cv FROM (".
                "SELECT '".$data['src']."' AS src, id, greeting, name, street, zipcode, contact, phone AS phone1, fax AS phone2, email, city, country, bland, contact AS person, notes, business_id, sw FROM ".$db_table[$data['src']]." WHERE id=".$data['id'].
                ") AS cv) AS cv, ";

    // greetings
    $query .= "(SELECT json_agg( greetings ) AS greetings FROM (".
                "SELECT description FROM greetings".
                ") AS greetings) AS greetings, ";

    // bundesland
    $query .= "(SELECT json_agg( bundesland ) AS bundesland FROM (".
                "SELECT id, country, bundesland AS name FROM bundesland".
                ") AS bundesland) AS bundesland, ";

    // business
    $query .= "(SELECT json_agg( business ) AS business FROM (".
                "SELECT id, description AS name FROM business ORDER BY business ASC".
                ") AS business) AS business, ";

    $query .= "(SELECT json_agg( deladdr ) AS deladdr FROM (".
                "SELECT trans_id, shipto_id, shiptoname, shiptodepartment_1, shiptodepartment_2, shiptostreet, shiptozipcode, shiptocity, shiptocountry, shiptocontact, shiptophone, shiptofax, shiptoemail, shiptoemployee, shiptobland FROM shipto WHERE trans_id = ".$data['id']." ORDER BY shiptoname ASC".
                ") AS deladdr) AS deladdr";

    echo $GLOBALS['dbh']->getOne($query, true);
}
