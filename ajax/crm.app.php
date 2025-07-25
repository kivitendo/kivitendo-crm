<?php

require_once __DIR__.'/../inc/stdLib.php'; // for debug
require_once __DIR__.'/../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

$GLOBALS['dbh']->setShowError( true );

/*************************************************get
* Erzeugt ein JSON das für die JS-Function
* showMessageDialog verwendet werden kann
*************************************************/
function resultInfo( $success, $debug_text = '' ){
    $info = '{ "success":'.(($success)? 'true' : 'false');
    if( !empty( $debug_text ) ) $info .= ', "debug":"'.$debug_text.'"';
    echo $info.' }';
    //writeLogR( $info );
}

/*********************************************
* Check lxcars tables exists and then get
* last version
*********************************************/
function isLxcars(){
     echo $GLOBALS['dbh']->getOne( "SELECT EXISTS(SELECT * FROM information_schema.tables WHERE table_name = 'lxc_ver') AS lxcars", true );
}

/*********************************************
* Last edited objects like customer and so on
*********************************************/
function getHistory(){
    $rs = $GLOBALS['dbh']->getOne( "SELECT val FROM crmemployee WHERE uid = '" . $_SESSION["loginCRM"]."' AND manid = ".$_SESSION['manid']." AND key = 'search_history'" );
    echo $rs['val'] ? $rs['val'] : '0';
}

/*********************************************
* Fast search (Schnellsuche)
*********************************************/
function fastSearch(){
    if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ) {
        $term = $_GET['term'];
        //echo $GLOBALS['dbh']->getAll("(SELECT 'Kunde' AS category, 'C' AS src, '' AS value, id, name AS label FROM customer WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'Lieferant' AS category, 'V' AS src, '' AS value, id, name AS label FROM vendor WHERE name ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'Kontaktperson' AS category, 'P' AS src, '' AS value, cp_id AS id, concat(cp_givenname, ' ', cp_name) AS name FROM contacts WHERE cp_name ILIKE '%".$term."%' OR cp_givenname ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'Fahrzeug' AS category, 'A' AS src, c_ln AS value, c_id AS id, ' [ ' || COALESCE(c_ln, '') || ' ] ' || COALESCE(name, '') AS label FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ILIKE '%".$term."%' AND obsolete = false LIMIT 5)", true);
        /*
        $query = "(SELECT 'Kunde' AS category, 'C' AS src, '' AS value, id, name AS label FROM customer WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' LIMIT ".
                "(SELECT (((( 20 - vn - an - pn ) + 5 ) / 3 ) / 3 ) + 5 FROM ( SELECT (SELECT count(*)::INT FROM (SELECT * FROM customer WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' LIMIT 5) AS c) AS cn, (SELECT count(*)::INT FROM (SELECT * FROM vendor WHERE name ILIKE '%".$term."%' LIMIT 5) AS v) AS vn, (SELECT count(*)::INT FROM (SELECT * FROM lxc_cars WHERE c_ln ILIKE '%".$term."%' LIMIT 5) AS a) AS an, (SELECT count(*)::INT FROM (SELECT * FROM contacts WHERE cp_name ILIKE '%D%' OR cp_givenname ILIKE '%D%' LIMIT 5) AS p) AS pn) AS test)".
                ")".
                "UNION ALL ".
                "(SELECT 'Lieferant' AS category, 'V' AS src, '' AS value, id, name AS label FROM vendor WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' LIMIT ".
                "(SELECT (((( 20 - cn - an - pn ) + 5 ) / 3 ) / 3 ) + 5 FROM ( SELECT (SELECT count(*)::INT FROM (SELECT * FROM customer WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' LIMIT 5) AS c) AS cn, (SELECT count(*)::INT FROM (SELECT * FROM vendor WHERE name ILIKE '%".$term."%' LIMIT 5) AS v) AS vn, (SELECT count(*)::INT FROM (SELECT * FROM lxc_cars WHERE c_ln ILIKE '%".$term."%' LIMIT 5) AS a) AS an, (SELECT count(*)::INT FROM (SELECT * FROM contacts WHERE cp_name ILIKE '%D%' OR cp_givenname ILIKE '%D%' LIMIT 5) AS p) AS pn) AS test)".
                ") ".
                "UNION ALL ".
                "(SELECT 'Kontaktperson' AS category, 'P' AS src, '' AS value, cp_id AS id, concat(cp_givenname, ' ', cp_name) AS name FROM contacts WHERE cp_name ILIKE '%".$term."%' OR cp_givenname ILIKE '%".$term."%' LIMIT ".
                "(SELECT (((( 20 - cn - vn - an ) + 5 ) / 3 ) / 3 ) + 5 FROM ( SELECT (SELECT count(*)::INT FROM (SELECT * FROM customer WHERE name ILIKE '%".$term."%' LIMIT 5) AS c) AS cn, (SELECT count(*)::INT FROM (SELECT * FROM vendor WHERE name ILIKE '%".$term."%' LIMIT 5) AS v) AS vn, (SELECT count(*)::INT FROM (SELECT * FROM lxc_cars WHERE c_ln ILIKE '%".$term."%' LIMIT 5) AS a) AS an, (SELECT count(*)::INT FROM (SELECT * FROM contacts WHERE cp_name ILIKE '%D%' OR cp_givenname ILIKE '%D%' LIMIT 5) AS p) AS pn) AS test)".
                ") ".
                "UNION ALL ".
                "(SELECT 'Fahrzeug' AS category, 'A' AS src, c_ln AS value, c_id AS id, ' [ ' || COALESCE(c_ln, '') || ' ] ' || COALESCE(name, '') AS label FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ILIKE '%".$term."%' AND obsolete = false LIMIT ".
                "(SELECT (((( 20 - cn - vn - pn ) + 5 ) / 3 ) / 3 ) + 5 FROM ( SELECT (SELECT count(*)::INT FROM (SELECT * FROM customer WHERE name ILIKE '%".$term."%' LIMIT 5) AS c) AS cn, (SELECT count(*)::INT FROM (SELECT * FROM vendor WHERE name ILIKE '%".$term."%' LIMIT 5) AS v) AS vn, (SELECT count(*)::INT FROM (SELECT * FROM lxc_cars WHERE c_ln ILIKE '%".$term."%' LIMIT 5) AS a) AS an, (SELECT count(*)::INT FROM (SELECT * FROM contacts WHERE cp_name ILIKE '%D%' OR cp_givenname ILIKE '%D%' LIMIT 5) AS p) AS pn) AS test)".
                ")";
        */
        // Sämtliche Ergebnisse holen, vermischen, limitieren und danach nach Kategorien sortieren
        $query= "SELECT * FROM ( SELECT * FROM ( ".
                "(SELECT 'Kunde' AS category, 'C' AS src, '' AS value, id, name AS label FROM customer WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' )".
                " UNION ALL ".
                "(SELECT 'Lieferant' AS category, 'V' AS src, '' AS value, id, name AS label FROM vendor WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' )".
                " UNION ALL ".
                "(SELECT 'Kontaktperson' AS category, 'P' AS src, '' AS value, cp_id AS id, concat(cp_givenname, ' ', cp_name) AS name FROM contacts WHERE cp_name ILIKE '%".$term."%' OR cp_givenname ILIKE '%".$term."%' )".
                " UNION ALL ".
                "(SELECT 'Fahrzeug' AS category, 'A' AS src, c_ln AS value, c_id AS id, ' [ ' || COALESCE( c_ln, '' ) || ' ] ' || COALESCE( name, '' ) AS label FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ILIKE '%".$term."%' OR c_fin ILIKE '%".$term."%' OR ( C_2 = SUBSTR( '".$term."', 1, 4 )  AND c_3 ILIKE SUBSTR( '".$term."', 5, 3 ) || '%' ) AND obsolete = false )".
                ") AS allResults ORDER BY random() LIMIT 20 ) AS mixed ORDER BY category";
        //writeLogR( $query );
        echo $GLOBALS['dbh']->getAll( $query , true);
    }
}

function searchPersonsAndCars(){
    if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ) {
        $term = $_GET['term'];
        $query= "SELECT * FROM ( SELECT * FROM ( ".
                "(SELECT 'Kunde' AS category, 'C' AS src, name AS value, id, 0 AS c_id, name AS label FROM customer WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' )".
                " UNION ALL ".
                "(SELECT 'Lieferant' AS category, 'V' AS src, name AS value, id, 0 AS c_id, name AS label FROM vendor WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' )".
                " UNION ALL ".
                "(SELECT 'Kontaktperson' AS category, 'P' AS src, concat(cp_givenname, ' ', cp_name) AS value, cp_id AS id, 0 AS c_id, concat(cp_givenname, ' ', cp_name) AS label FROM contacts WHERE cp_name ILIKE '%".$term."%' OR cp_givenname ILIKE '%".$term."%' )".
                " UNION ALL ".
                "(SELECT 'Fahrzeug' AS category, 'A' AS src, name AS value, id, c_id, ' [ ' || COALESCE( c_ln, '' ) || ' ] ' || COALESCE( name, '' ) AS label FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ILIKE '%".$term."%' OR c_fin ILIKE '%".$term."%' OR ( C_2 = SUBSTR( '".$term."', 1, 4 )  AND c_3 = SUBSTR( '".$term."', 5, 3 ) ) AND obsolete = false )".
                ") AS allResults ORDER BY random() LIMIT 20 ) AS mixed ORDER BY category";
        echo $GLOBALS['dbh']->getAll( $query , true);
    }
}

function searchCustomer(){
    if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ) {
        $term = $_GET['term'];
        echo $GLOBALS['dbh']->getAll( "SELECT name AS value, id, customernumber FROM customer WHERE name ILIKE '%".$term."%' ORDER BY name ASC LIMIT 15", true );
    }
}

function searchVendor(){
    if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ) {
        $term = $_GET['term'];
        echo $GLOBALS['dbh']->getAll( "SELECT name AS value, id, vendornumber FROM vendor WHERE name ILIKE '%".$term."%' ORDER BY name ASC LIMIT 15", true );
    }
}

function searchCustomerVendor(){
    if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ) {
        $term = $_GET['term'];

        $query = "(SELECT 'Kunde' AS category, 'C' AS src, name AS value, id, name AS label FROM customer WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' )".
                " UNION ALL ".
                "(SELECT 'Lieferant' AS category, 'V' AS src, name AS value, id, name AS label FROM vendor WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' )";
        echo $GLOBALS['dbh']->getAll( $query , true);
    }
}

function searchCVP(){
    if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ) {
        $term = $_GET['term'];

        $query= "SELECT * FROM ( SELECT * FROM ( ".
                "(SELECT 'Kunde' AS category, 'C' AS src, name AS value, id, name AS label FROM customer WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' )".
                " UNION ALL ".
                "(SELECT 'Lieferant' AS category, 'V' AS src, name AS value, id, name AS label FROM vendor WHERE name ILIKE '%".$term."%' OR sw ILIKE '%".$term."%' OR contact ILIKE '%".$term."%' )".
                " UNION ALL ".
                "(SELECT 'Kontaktperson' AS category, 'P' AS src, concat(cp_givenname, ' ', cp_name) AS value, cp_id AS id, concat(cp_givenname, ' ', cp_name) AS name FROM contacts WHERE cp_name ILIKE '%".$term."%' OR cp_givenname ILIKE '%".$term."%' )".
                ") AS allResults ORDER BY random() LIMIT 20 ) AS mixed ORDER BY category";
        echo $GLOBALS['dbh']->getAll( $query , true);
    }
}
function searchCarLicense(){
    if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ) {
        $term = $_GET['term'];
        $query = "SELECT c_ln AS value, c_id AS id FROM lxc_cars AS id WHERE c_ln ILIKE '%".$term."%'";
        if( isset( $_GET['customer'] ) && !empty( $_GET['customer'] ) ) $query .= " AND c_ow = ".$_GET['customer'];
        $query .= " ORDER BY c_ln ASC LIMIT 15";
        echo $GLOBALS['dbh']->getAll( $query, true );
    }
}

function searchCarKbaValue( $value ){
    if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ) {
        $term = $_GET['term'];
        echo $GLOBALS['dbh']->getAll( "SELECT DISTINCT ON ( ".$value." ) ".$value." AS value FROM lxckba WHERE ".$value." ILIKE '%".$term."%' LIMIT 5", true);
    }
}

function searchCarManuf(){
    searchCarKbaValue( 'hersteller' );
}

function searchCarType(){
    searchCarKbaValue( 'name' );
}

function searchCarBrand(){
    searchCarKbaValue( 'marke' );
}

function searchOrder( $data ){
    $where = '';
    if( $data['customer_name'] != '' ){
        $where .= "customer.name ILIKE '%".$data['customer_name']."%' AND ";
    }

    if( $data['car_license'] != '' ){
        $where .= "cars.c_ln ILIKE '%".$data['car_license']."%' AND ";
    }

    if( $data['car_manuf'] != '' ){
        $where .= "cars.hersteller ILIKE '%".$data['car_manuf']."%' AND ";
    }

    if( $data['car_type'] != '' ){
        $where .= "cars.name ILIKE '%".$data['car_type']."%' AND ";
    }

    if( $data['car_brand'] != '' ){
        $where .= "cars.marke ILIKE '%".$data['car_brand']."%' AND ";
    }

    if( $data['date_from'] != '' ){
        $where .= "oe.transdate >= '".$data['date_from']."' AND ";
    }

    if( $data['date_to'] != '' ){
        $where .= "oe.transdate <= '".$data['date_to']."' AND ";
    }

    if( $data['status'] == 'nicht abgerechnet' ){
        $where .= " oe.status != 'abgerechnet'  AND ";
    }
    elseif( $data['status'] != '' && $data['status'] != 'alle' ){
        $where .= " oe.status = '".$data['status']."'  AND ";
    }

    $sql = "SELECT *, TO_CHAR(delivery_ts, 'DD.MM.YYYY HH24:MI') AS delivery_ts_formatted FROM (
        SELECT DISTINCT ON (internal_order, init_ts) 
            'true'::BOOL AS instruction, 
            oe.id, 
            cars.c_ln, 
            TO_CHAR(oe.transdate, 'DD.MM.YYYY') AS transdate, 
            oe.ordnumber, 
            instructions.description, 
            oe.car_status, 
            oe.status, 
            oe.finish_time, 
            customer.name AS owner, 
            oe.c_id AS c_id, 
            oe.customer_id, 
            cars.c_2 AS c_2, 
            cars.c_3 AS c_3, 
            cars.hersteller AS car_manuf, 
            cars.name AS car_type, 
            oe.internalorder AS internal_order, 
            oe.itime AS init_ts,
            COALESCE(
                NULLIF(
                    CASE 
                        WHEN delivery_time ILIKE '%Uhr' THEN 
                            TO_TIMESTAMP(REPLACE(delivery_time, ' Uhr', ''), 'DD.MM.YYYY HH24:MI')
                        WHEN delivery_time ~ '^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$' THEN 
                            TO_TIMESTAMP(delivery_time, 'DD.MM.YYYY HH24:MI')
                        ELSE NULL
                    END, NULL
                ), oe.itime
            ) AS delivery_ts
        FROM oe
        JOIN instructions ON instructions.trans_id = oe.id
        JOIN parts ON parts.id = instructions.parts_id
        JOIN customer ON customer.id = oe.customer_id
        JOIN (SELECT * FROM lxc_cars LEFT JOIN lxckba ON lxckba.id = lxc_cars.kba_id) AS cars ON cars.c_id = oe.c_id
        WHERE " . $where . " oe.quotation = FALSE
          AND (COALESCE(
                NULLIF(
                    CASE 
                        WHEN delivery_time ILIKE '%Uhr' THEN 
                            TO_TIMESTAMP(REPLACE(delivery_time, ' Uhr', ''), 'DD.MM.YYYY HH24:MI')
                        WHEN delivery_time ~ '^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$' THEN 
                            TO_TIMESTAMP(delivery_time, 'DD.MM.YYYY HH24:MI')
                        ELSE NULL
                    END, NULL
                ), oe.itime) <= NOW() + INTERVAL '7 days')

        UNION 

        SELECT DISTINCT ON (internal_order, init_ts) 
            'false'::BOOL AS instruction, 
            oe.id, 
            cars.c_ln, 
            TO_CHAR(oe.transdate, 'DD.MM.YYYY') AS transdate, 
            oe.ordnumber, 
            orderitems.description, 
            oe.car_status, 
            oe.status, 
            oe.finish_time, 
            customer.name AS owner, 
            oe.c_id AS c_id, 
            oe.customer_id, 
            cars.c_2 AS c_2, 
            cars.c_3 AS c_3, 
            cars.hersteller AS car_manuf, 
            cars.name AS car_type, 
            oe.internalorder AS internal_order, 
            oe.itime AS init_ts,
            COALESCE(
                NULLIF(
                    CASE 
                        WHEN delivery_time ILIKE '%Uhr' THEN 
                            TO_TIMESTAMP(REPLACE(delivery_time, ' Uhr', ''), 'DD.MM.YYYY HH24:MI')
                        WHEN delivery_time ~ '^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$' THEN 
                            TO_TIMESTAMP(delivery_time, 'DD.MM.YYYY HH24:MI')
                        ELSE NULL
                    END, NULL
                ), oe.itime
            ) AS delivery_ts
        FROM oe
        JOIN orderitems ON orderitems.trans_id = oe.id AND orderitems.position = 1
        JOIN parts ON parts.id = orderitems.parts_id
        JOIN customer ON customer.id = oe.customer_id
        JOIN (SELECT * FROM lxc_cars LEFT JOIN lxckba ON lxckba.id = lxc_cars.kba_id) AS cars ON cars.c_id = oe.c_id
        WHERE " . $where . " oe.quotation = FALSE
          AND (COALESCE(
                NULLIF(
                    CASE 
                        WHEN delivery_time ILIKE '%Uhr' THEN 
                            TO_TIMESTAMP(REPLACE(delivery_time, ' Uhr', ''), 'DD.MM.YYYY HH24:MI')
                        WHEN delivery_time ~ '^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$' THEN 
                            TO_TIMESTAMP(delivery_time, 'DD.MM.YYYY HH24:MI')
                        ELSE NULL
                    END, NULL
                ), oe.itime) <= NOW() + INTERVAL '7 days')
    ) AS myTable 
    ORDER BY internal_order ASC, delivery_ts DESC 
    LIMIT 100";


//Mit Brainfuck-Message:
/*
$sql = "WITH random_message AS (
    SELECT message
    FROM brainfuck
    WHERE CURRENT_TIME >= start_time AND CURRENT_TIME < end_time
    ORDER BY RANDOM()
    LIMIT 1
),
main_data AS (
    SELECT json_agg(row_to_json(t)) AS data
    FROM (
        SELECT *, 
               TO_CHAR(delivery_ts, 'DD.MM.YYYY HH24:MI') AS delivery_ts_formatted
        FROM (
            SELECT DISTINCT ON (internal_order, init_ts) 
                'true'::BOOL AS instruction, 
                oe.id, 
                cars.c_ln, 
                TO_CHAR(oe.transdate, 'DD.MM.YYYY') AS transdate, 
                oe.ordnumber, 
                instructions.description, 
                oe.car_status, 
                oe.status, 
                oe.finish_time, 
                customer.name AS owner, 
                oe.c_id AS c_id, 
                oe.customer_id, 
                cars.c_2 AS c_2, 
                cars.c_3 AS c_3, 
                cars.hersteller AS car_manuf, 
                cars.name AS car_type, 
                oe.internalorder AS internal_order, 
                oe.itime AS init_ts,
                COALESCE(
                    NULLIF(
                        CASE 
                            WHEN delivery_time ILIKE '%Uhr' THEN 
                                TO_TIMESTAMP(REPLACE(delivery_time, ' Uhr', ''), 'DD.MM.YYYY HH24:MI')
                            WHEN delivery_time ~ '^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$' THEN 
                                TO_TIMESTAMP(delivery_time, 'DD.MM.YYYY HH24:MI')
                            ELSE NULL
                        END, NULL
                    ), oe.itime
                ) AS delivery_ts
            FROM oe
            JOIN instructions ON instructions.trans_id = oe.id
            JOIN parts ON parts.id = instructions.parts_id
            JOIN customer ON customer.id = oe.customer_id
            JOIN (SELECT * FROM lxc_cars LEFT JOIN lxckba ON lxckba.id = lxc_cars.kba_id) AS cars ON cars.c_id = oe.c_id
            WHERE $where oe.quotation = FALSE
              AND (COALESCE(
                    NULLIF(
                        CASE 
                            WHEN delivery_time ILIKE '%Uhr' THEN 
                                TO_TIMESTAMP(REPLACE(delivery_time, ' Uhr', ''), 'DD.MM.YYYY HH24:MI')
                            WHEN delivery_time ~ '^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$' THEN 
                                TO_TIMESTAMP(delivery_time, 'DD.MM.YYYY HH24:MI')
                            ELSE NULL
                        END, NULL
                    ), oe.itime) <= NOW() + INTERVAL '7 days')
            UNION 
            SELECT DISTINCT ON (internal_order, init_ts) 
                'false'::BOOL AS instruction, 
                oe.id, 
                cars.c_ln, 
                TO_CHAR(oe.transdate, 'DD.MM.YYYY') AS transdate, 
                oe.ordnumber, 
                orderitems.description, 
                oe.car_status, 
                oe.status, 
                oe.finish_time, 
                customer.name AS owner, 
                oe.c_id AS c_id, 
                oe.customer_id, 
                cars.c_2 AS c_2, 
                cars.c_3 AS c_3, 
                cars.hersteller AS car_manuf, 
                cars.name AS car_type, 
                oe.internalorder AS internal_order, 
                oe.itime AS init_ts,
                COALESCE(
                    NULLIF(
                        CASE 
                            WHEN delivery_time ILIKE '%Uhr' THEN 
                                TO_TIMESTAMP(REPLACE(delivery_time, ' Uhr', ''), 'DD.MM.YYYY HH24:MI')
                            WHEN delivery_time ~ '^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$' THEN 
                                TO_TIMESTAMP(delivery_time, 'DD.MM.YYYY HH24:MI')
                            ELSE NULL
                        END, NULL
                    ), oe.itime
                ) AS delivery_ts
            FROM oe
            JOIN orderitems ON orderitems.trans_id = oe.id AND orderitems.position = 1
            JOIN parts ON parts.id = orderitems.parts_id
            JOIN customer ON customer.id = oe.customer_id
            JOIN (SELECT * FROM lxc_cars LEFT JOIN lxckba ON lxckba.id = lxc_cars.kba_id) AS cars ON cars.c_id = oe.c_id
            WHERE $where oe.quotation = FALSE
              AND (COALESCE(
                    NULLIF(
                        CASE 
                            WHEN delivery_time ILIKE '%Uhr' THEN 
                                TO_TIMESTAMP(REPLACE(delivery_time, ' Uhr', ''), 'DD.MM.YYYY HH24:MI')
                            WHEN delivery_time ~ '^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$' THEN 
                                TO_TIMESTAMP(delivery_time, 'DD.MM.YYYY HH24:MI')
                            ELSE NULL
                        END, NULL
                    ), oe.itime) <= NOW() + INTERVAL '7 days')
        ) AS myTable 
        ORDER BY internal_order ASC, delivery_ts DESC 
        LIMIT 100
    ) t
)
SELECT json_build_object(
    'message', (SELECT message FROM random_message),
    'data', (SELECT data FROM main_data)
) AS result";

*/


    //writeLog( $sql );
    // ohne Brainfuck
    $rs = $GLOBALS['dbh']->getALL( $sql, true );
    //Mit brainfuck
    //$rs = $GLOBALS['dbh']->getOne( $sql );


    echo '{ "rs": '.( ( empty( $rs ) )? '{}' : $rs ).' }';
}

function checkArticleNumber( $data ){
    echo $GLOBALS['dbh']->getOne("SELECT EXISTS( SELECT partnumber FROM parts WHERE partnumber LIKE '".$data['partnumber']."' LIMIT 1) AS exists", true);
}

function computeArticleNumber( $data ){
    if( $data['part_type'] == "P" || $data['part_type'] == "part" )
        $rs = $GLOBALS['dbh']->getOne( "SELECT id AS defaults_id, articlenumber::INT + 1 AS newnumber, 0 AS service FROM defaults");
    else
        $rs = $GLOBALS['dbh']->getOne( "SELECT id AS defaults_id, servicenumber::INT + 1 AS newnumber, customer_hourly_rate, 1 AS service FROM defaults");
    while( $GLOBALS['dbh']->getOne( "SELECT partnumber FROM parts WHERE partnumber = '".$rs['newnumber']."'" )['partnumber'] ) $rs['newnumber']++;

    return $rs;
}

function newArticleNumber( $data ){
    $an = computeArticleNumber( $data );

    $rs;
    if( "I" == $data['part_type'] ){
        $rs = $GLOBALS['dbh']->getOne( "SELECT unit FROM instructions WHERE description ILIKE '".$data['description']."%' GROUP BY unit ORDER BY count(unit) DESC LIMIT 1");
    }
    else{
        $rs = $GLOBALS['dbh']->getOne( "SELECT orderitems.unit FROM orderitems INNER JOIN parts ON orderitems.parts_id = parts.id WHERE orderitems.description ILIKE '".
                                        $data['description']."%' AND parts.part_type = '".
                                        ( ( "P" == $data['part_type'] )? 'part' : 'service' )."' AND parts.instruction = false AND parts.obsolete = false GROUP BY orderitems.unit ORDER BY count(orderitems.unit) DESC LIMIT 1" );
    }

    echo  '{ "newnumber": '.$an['newnumber'].', "unit": "'.$rs['unit'].'" }';
}

function computeArticleQty( $data ){
    $rs;
    if( "I" == $data['part_type'] ){
        $rs = $GLOBALS['dbh']->getOne( "SELECT qty FROM instructions WHERE description ILIKE '".$data['description']."%' AND unit = '".$data['unit']."' GROUP BY qty ORDER BY count(qty) DESC" );
    }
    else{
        $rs = $GLOBALS['dbh']->getOne( "SELECT orderitems.qty, count(orderitems.qty) AS c FROM orderitems INNER JOIN parts ON orderitems.parts_id = parts.id WHERE orderitems.description ILIKE '".
                                        $data['description']."%' AND orderitems.unit = '".$data['unit']."' AND parts.part_type = '".
                                        $data['part_type']."' AND parts.instruction = false AND parts.obsolete = false GROUP BY orderitems.qty ORDER BY count(orderitems.qty) DESC" );
    }

    echo  '{ "qty": "'.$rs['qty'].'" }';
}

function dataForNewArticle( $data ){
    $an = computeArticleNumber( $data );

    $query .= "SELECT (SELECT json_agg( units ) AS units FROM (".
                "SELECT name FROM units".
                    ") AS units) AS units, ";

    if( !empty( $data['parts_id'] ) ){
        $query .= "(SELECT row_to_json( part ) AS part FROM (".
                    "SELECT buchungsgruppen_id, partnumber, unit FROM parts WHERE id = ".$data['parts_id'].
                        ") AS part) AS part, ";
    }

    $query .= "(SELECT json_agg( buchungsgruppen ) AS buchungsgruppen FROM (".
                "SELECT id, description FROM  buchungsgruppen WHERE obsolete = false ORDER BY sortkey ASC".
                    ") AS buchungsgruppen) AS buchungsgruppen";

    $units = $GLOBALS['dbh']->getOne($query, true);

    echo  '{ "defaults": '.json_encode( $an ).', "common": '.$units.' }';
}

function insertNewArticle( $data ){
    $id = $GLOBALS['dbh']->insert( "parts", array_keys( $data ), array_values( $data ), TRUE, "id" );
    echo  '{ "id": '.$id.' }';
}

function getArticleRate( $data ){
    echo $GLOBALS['dbh']->getOne("SELECT tax.rate FROM parts INNER JOIN taxzone_charts ON parts.buchungsgruppen_id = taxzone_charts.buchungsgruppen_id INNER JOIN taxkeys ON taxzone_charts.income_accno_id = taxkeys.chart_id INNER JOIN tax ON taxkeys.tax_id = tax.id WHERE parts.id = ".$data['part_id']." AND parts.obsolete = false AND taxzone_charts.taxzone_id = 4 GROUP BY parts.id, tax.rate, taxkeys.startdate ORDER BY taxkeys.startdate DESC LIMIT 1", true);
}

/********************************************
* Find parts like service, instructions and goods for orders
* Sortet by quantity and categorie (instruction, good and service)
********************************************/
function findPart( $term ){
    //Index installieren create index idx_orderitems on orderitems ( parts_id );
    if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) && isset( $_GET['filter'] ) && !empty( $_GET['filter'] )) {
        $term = $_GET['term'];
        $filter = '';
        if( 'order' == $_GET['filter'] ) $filter = 'orderitems';
        elseif ( 'invoice' == $_GET['filter'] ) $filter = 'invoice';
        elseif ( 'offer' == $_GET['filter'] ) $filter = 'orderitems';
        $sql = "";
        if( 'order' == $_GET['filter'] ){
            // $sql .= "(SELECT 'W' AS part_type,  'Waren' AS category, description, partnumber, id, description AS value, part_type, unit,  partnumber || ' ' || description AS label, instruction, sellprice,";
            // $sql .= " (SELECT qty FROM invoice WHERE invoice.parts_id = parts.id AND invoice.qty IS NOT null GROUP BY qty ORDER BY count( invoice.qty ) DESC, qty DESC LIMIT 1) AS qty,";
            //$sql .= "(SELECT 'D' AS part_type,  'Anweisungen' AS category, description, partnumber, id, description AS value, part_type, unit,  partnumber || ' ' || description AS label, instruction, '100' AS sellprice,";
            //$sql .= " '0' AS qty,";
            //$sql .= " (SELECT tax.rate FROM parts i INNER JOIN taxzone_charts ON parts.buchungsgruppen_id = taxzone_charts.buchungsgruppen_id INNER JOIN taxkeys ON taxzone_charts.income_accno_id = taxkeys.chart_id INNER JOIN tax ON taxkeys.tax_id = tax.id WHERE i.id = parts.id AND parts.obsolete = false AND taxzone_charts.taxzone_id = 4 GROUP BY parts.id, tax.rate, taxkeys.startdate ORDER BY taxkeys.startdate DESC LIMIT 1) AS rate";
            //$sql .= " FROM parts WHERE ( description ILIKE '%$term%' OR partnumber ILIKE '$term%' ) AND obsolete = FALSE AND part_type ='service' AND instruction = true ORDER BY ( SELECT ( SELECT count( qty ) FROM orderitems WHERE parts_id = parts.id ) ) DESC NULLS LAST LIMIT 5) UNION ALL";
            $sql .= "(SELECT 'D' AS part_type,  'Anweisungen' AS category, description, partnumber, id, description AS value, part_type, unit,";
            $sql .= " (SELECT row_to_json( unit_type ) AS unit_type FROM (";
            $sql .= "   SELECT base_unit, factor FROM units WHERE units.name = parts.unit";
            $sql .= " ) AS unit_type) AS unit_type,";
            $sql .= " partnumber || ' ' || description AS label, instruction, '100' AS sellprice, '0' AS qty, null AS buchungsziel";
            $sql .= " FROM parts WHERE ( description ILIKE '%$term%' OR partnumber ILIKE '$term%' ) AND obsolete = FALSE AND part_type ='service' AND instruction = true ORDER BY ( SELECT ( SELECT count( qty ) FROM orderitems WHERE parts_id = parts.id ) ) DESC NULLS LAST LIMIT 5)";
            $sql .= " UNION ALL";
        }
        //$sql .= " (SELECT 'W' AS part_type,  'Waren' AS category, description, partnumber, id, description AS value, part_type, unit,  partnumber || ' ' || description AS label, instruction, sellprice,";
        //$sql .= " (SELECT qty FROM $filter WHERE $filter.parts_id = parts.id AND $filter.qty IS NOT null GROUP BY qty ORDER BY count( $filter.qty ) DESC, qty DESC LIMIT 1) AS qty,";
        //$sql .= " (SELECT tax.rate FROM parts i INNER JOIN taxzone_charts ON parts.buchungsgruppen_id = taxzone_charts.buchungsgruppen_id INNER JOIN taxkeys ON taxzone_charts.income_accno_id = taxkeys.chart_id INNER JOIN tax ON taxkeys.tax_id = tax.id WHERE i.id = parts.id AND parts.obsolete = false AND taxzone_charts.taxzone_id = 4 GROUP BY parts.id, tax.rate, taxkeys.startdate ORDER BY taxkeys.startdate DESC LIMIT 1) AS rate";
        //$sql .= " FROM parts WHERE ( description ILIKE '%$term%' OR partnumber ILIKE '$term%' ) AND obsolete = FALSE AND part_type = 'part'  AND instruction = false ORDER BY ( SELECT ( SELECT count( qty ) FROM $filter WHERE parts_id = parts.id ) ) DESC NULLS LAST LIMIT 5) UNION ALL";
        //$sql .= " (SELECT 'D' AS part_type,  'Dienstleistung' AS category, description, partnumber, id, description AS value, part_type, unit,  partnumber || ' ' || description AS label, instruction, sellprice,";
        //$sql .= " (SELECT qty FROM $filter WHERE $filter.parts_id = parts.id AND $filter.qty IS NOT null GROUP BY qty ORDER BY count( $filter.qty ) DESC, qty DESC LIMIT 1) AS qty,";
        //$sql .= " (SELECT tax.rate FROM parts i INNER JOIN taxzone_charts ON parts.buchungsgruppen_id = taxzone_charts.buchungsgruppen_id INNER JOIN taxkeys ON taxzone_charts.income_accno_id = taxkeys.chart_id INNER JOIN tax ON taxkeys.tax_id = tax.id WHERE i.id = parts.id AND parts.obsolete = false AND taxzone_charts.taxzone_id = 4 GROUP BY parts.id, tax.rate, taxkeys.startdate ORDER BY taxkeys.startdate DESC LIMIT 1) AS rate";
        //$sql .= " FROM parts WHERE ( description ILIKE '%$term%' OR partnumber ILIKE '$term%' ) AND obsolete = FALSE AND part_type ='service' AND instruction = false ORDER BY ( SELECT ( SELECT count( qty ) FROM $filter WHERE parts_id = parts.id ) ) DESC NULLS LAST LIMIT 5)";
        $sql .= " (SELECT 'W' AS part_type,  'Waren' AS category, description, partnumber, id, description AS value, part_type, unit,";
        $sql .= " (SELECT row_to_json( unit_type ) AS unit_type FROM (";
        $sql .= "   SELECT base_unit, factor FROM units WHERE units.name = parts.unit";
        $sql .= " ) AS unit_type) AS unit_type,";
        $sql .= " partnumber || ' ' || description AS label, instruction, sellprice,";
        $sql .= " (SELECT qty FROM orderitems WHERE orderitems.parts_id = parts.id AND orderitems.qty IS NOT null GROUP BY qty ORDER BY count( orderitems.qty ) DESC, qty DESC LIMIT 1) AS qty,";
        $sql .= " (SELECT row_to_json( buchungsziel ) AS buchungsziel FROM (";
        $sql .= "    SELECT c2.id AS income_chart_id, tk.tax_id, tx.chart_id AS tax_chart_id, tx.rate FROM parts p LEFT JOIN buchungsgruppen bg ON p.buchungsgruppen_id = bg.id LEFT JOIN taxzone_charts tc on bg.id = tc.buchungsgruppen_id LEFT JOIN chart c1 ON bg.inventory_accno_id = c1.id LEFT JOIN chart c2 ON tc.income_accno_id = c2.id LEFT JOIN chart c3 ON tc.expense_accno_id = c3.id LEFT JOIN taxkeys tk ON tk.chart_id = c2.id LEFT JOIN tax tx ON tx.id = tk.tax_id WHERE tc.taxzone_id = '4' AND p.id IN (parts.id) ORDER BY tk.startdate DESC LIMIT 1";
        $sql .= " ) AS buchungsziel) AS buchungsziel";
        $sql .= " FROM parts WHERE ( description ILIKE '%$term%' OR partnumber ILIKE '$term%' ) AND obsolete = FALSE AND part_type = 'part'  AND instruction = false ORDER BY ( SELECT ( SELECT count( qty ) FROM orderitems WHERE parts_id = parts.id ) ) DESC NULLS LAST LIMIT 5)";
        $sql .= " UNION ALL";
        $sql .= " (SELECT 'D' AS part_type,  'Dienstleistung' AS category, description, partnumber, id, description AS value, part_type, unit,";
        $sql .= " (SELECT row_to_json( unit_type ) AS unit_type FROM (";
        $sql .= "   SELECT base_unit, factor FROM units WHERE units.name = parts.unit";
        $sql .= " ) AS unit_type) AS unit_type,";
        $sql .= " partnumber || ' ' || description AS label, instruction, sellprice,";
        $sql .= " (SELECT qty FROM orderitems WHERE orderitems.parts_id = parts.id AND orderitems.qty IS NOT null GROUP BY qty ORDER BY count( orderitems.qty ) DESC, qty DESC LIMIT 1) AS qty,";
        $sql .= " (SELECT row_to_json( buchungsziel ) AS buchungsziel FROM (";
        $sql .= "    SELECT c2.id AS income_chart_id, tk.tax_id, tx.chart_id AS tax_chart_id, tx.rate FROM parts p LEFT JOIN buchungsgruppen bg ON p.buchungsgruppen_id = bg.id LEFT JOIN taxzone_charts tc on bg.id = tc.buchungsgruppen_id LEFT JOIN chart c1 ON bg.inventory_accno_id = c1.id LEFT JOIN chart c2 ON tc.income_accno_id = c2.id LEFT JOIN chart c3 ON tc.expense_accno_id = c3.id LEFT JOIN taxkeys tk ON tk.chart_id = c2.id LEFT JOIN tax tx ON tx.id = tk.tax_id WHERE tc.taxzone_id = '4' AND p.id IN (parts.id) ORDER BY tk.startdate DESC LIMIT 1";
        $sql .= " ) AS buchungsziel) AS buchungsziel";
        $sql .= " FROM parts WHERE ( description ILIKE '%$term%' OR partnumber ILIKE '$term%' ) AND obsolete = FALSE AND part_type ='service' AND instruction = false ORDER BY ( SELECT ( SELECT count( qty ) FROM orderitems WHERE parts_id = parts.id ) ) DESC NULLS LAST LIMIT 5)";

        //writeLog( $sql );
        echo $GLOBALS['dbh']->getAll( $sql, true );
    }
}

function getCVPA( $data ){
    $query = "SELECT ";
    if( $data['src'] == 'P' ){
        $cp_cv = $GLOBALS['dbh']->getOne( "SELECT id, 'C' AS src FROM customer WHERE id = (SELECT cp_cv_id FROM contacts WHERE cp_id = ".$data['id'].") UNION ALL SELECT id, 'V' AS src FROM vendor WHERE id = (SELECT cp_cv_id FROM contacts WHERE cp_id = ".$data['id'].")" );
        //writeLog( $cp_cv );
        $data['id'] = $cp_cv['id'];
        $data['src'] = $cp_cv['src'];
    }
    // Autos
    if( $data['src'] == 'A' ){
        $c_ow = $GLOBALS['dbh']->getOne( "SELECT c_ow FROM lxc_cars WHERE c_id = ".$data['id'] )['c_ow'];
        $query .= "(SELECT row_to_json( car ) AS car FROM (".
                    "SELECT *, to_char( c_hu, 'DD.MM.YYYY') AS c_hu, to_char( c_d, 'DD.MM.YYYY') AS c_d FROM lxc_cars LEFT JOIN lxckba ON( lxc_cars.kba_id = lxckba.id ) WHERE c_id = ".$data['id'].
                    ") AS car) AS car, ";
        //So nun müssen wir noch die Fahrzeugspezifische Aufträge holen
        $query .= "(
                SELECT json_agg(car_ord) AS car_ord 
                FROM (
                    SELECT DISTINCT ON (oe.itime)
                        to_char(oe.transdate, 'DD.MM.YYYY') AS date, 
                        COALESCE(instructions.description, orderitems.description) AS description, 
                        COALESCE(ROUND(amount, 2)) || ' ' || COALESCE(C.name) AS amount, 
                        oe.ordnumber AS number, 
                        oe.id 
                    FROM oe 
                    LEFT JOIN orderitems ON oe.id = orderitems.trans_id 
                    LEFT JOIN instructions ON oe.id = instructions.trans_id 
                    LEFT JOIN currencies C ON currency_id = C.id 
                    WHERE quotation = FALSE 
                        AND c_id = ".$data['id']." 
                    ORDER BY oe.itime DESC, orderitems.itime 
                    LIMIT 10
                ) AS car_ord
            ) AS car_ord, ";
        $data['src'] = 'C'; // Nur Kunden haben Autos
        $data['id'] = $c_ow;
    }
    if( $data['src'] == 'C' || $data['src'] == 'V' ){
        // Stammdaten
        $db_table = array('C' => 'customer', 'V' => 'vendor');
        $query .= "(SELECT row_to_json( cv ) AS cv FROM (".
                    "SELECT '".$data['src']."' AS src, id, ".$db_table[$data['src']]."number AS cvnumber, greeting, name, street, zipcode, contact, phone AS phone1, fax AS phone2, phone3, note_phone AS note_phone1, note_fax AS note_phone2, note_phone3, email, city, country FROM ".$db_table[$data['src']]." WHERE id=".$data['id'].
                    ") AS cv) AS cv, ";
        // Angebote
        $id = array('C' => 'customer_id', 'V' => 'vendor_id');
        $query .= "(SELECT json_agg( off ) AS off FROM (".
                    "SELECT DISTINCT ON (oe.itime) to_char(oe.transdate, 'DD.MM.YYYY') as date, description, COALESCE(ROUND(amount,2))||' '||COALESCE(C.name) as amount, ".
                    "oe.quonumber as number, oe.id FROM oe LEFT JOIN orderitems ON oe.id=trans_id LEFT JOIN currencies C on currency_id=C.id WHERE quotation = TRUE AND ".$id[$data['src']]." = ".$data['id']." ORDER BY oe.itime DESC, orderitems.id".
                    ") AS off) AS off, ";
        // Aufträge
        $query .= "(SELECT json_agg( ord ) AS ord FROM (".
                    "SELECT DISTINCT ON (oe.itime) to_char(oe.transdate, 'DD.MM.YYYY') as date, COALESCE( instructions.description, orderitems.description ) AS description, COALESCE(ROUND(amount,2))||' '||COALESCE(C.name) as amount, ".
                    "oe.ordnumber as number, oe.id FROM oe LEFT JOIN orderitems ON oe.id = orderitems.trans_id LEFT JOIN instructions ON oe.id = instructions.trans_id LEFT JOIN currencies C on currency_id=C.id WHERE quotation = FALSE AND ".$id[$data['src']]." = ".$data['id']." AND EXISTS(SELECT * FROM information_schema.tables WHERE table_name = 'lxc_ver') ORDER BY oe.itime DESC, orderitems.itime, instructions.itime".
                    ") AS ord) AS ord, ";
        // Lieferscheine
        $query .= "(SELECT json_agg( del ) AS del FROM (".
                    "SELECT DISTINCT ON (delivery_orders.id) delivery_orders.id, to_char(delivery_orders.transdate, 'DD.MM.YYYY') as date, description, to_char(delivery_orders.reqdate, 'DD.MM.YYYY') as deldate, donumber ".
                    "FROM delivery_orders LEFT JOIN delivery_order_items ON delivery_orders.id = delivery_order_id WHERE ".$id[$data['src']]." = ".$data['id']." AND closed = FALSE ORDER BY delivery_orders.id DESC".
                    ") AS del) AS del, ";
        // Rechnungen
        $db_table = array('C' => 'ar', 'V' => 'ap');
        $query .= "(SELECT json_agg( inv ) AS inv FROM (".
                    "SELECT DISTINCT ON (".$db_table[$data['src']].".id) to_char(".$db_table[$data['src']].".transdate, 'DD.MM.YYYY') as date, COALESCE( description, '---------' ) AS description, COALESCE( ROUND( amount,2 ) )||' '||COALESCE( C.name ) as amount, ".
                    "invnumber as number, ".$db_table[$data['src']].".id FROM ".$db_table[$data['src']]." LEFT JOIN invoice  ON ".$db_table[$data['src']].".id=trans_id LEFT JOIN currencies C on currency_id=C.id  WHERE ".$id[$data['src']]." = ".$data['id']." ORDER BY ".$db_table[$data['src']].".id DESC, invoice.id".
                    ") AS inv) AS inv, ";

        $query .= "(SELECT json_agg( custom_vars ) AS custom_vars FROM (".
                    "SELECT COALESCE( custom_variables.bool_value::text, custom_variables.number_value::text, custom_variables.timestamp_value::text, custom_variables.text_value ) AS value, custom_variable_configs.description FROM custom_variables JOIN custom_variable_configs ON custom_variables.config_id = custom_variable_configs.id WHERE trans_id = ".$data['id'].
                    ") AS custom_vars) AS custom_vars, ";

        $query .= "(SELECT json_agg( contacts ) AS contacts FROM (".
                    "SELECT * FROM contacts WHERE cp_cv_id = ".$data['id'].
                    ") AS contacts) AS contacts, ";
    }

    // Fahrzeuge
    $query .= "( SELECT json_agg( cars ) AS cars FROM (".
                "SELECT c_id, c_ln, COALESCE( hersteller, '---------' ) AS hersteller, COALESCE( name, '---------' ) AS name, COALESCE( fhzart, '---------' ) AS mytype FROM lxc_cars LEFT JOIN lxckba ON( lxc_cars.kba_id = lxckba.id ) WHERE c_ow = ".$data['id']." ORDER BY c_id DESC".
                ") AS cars) AS cars";

    $rs = $GLOBALS['dbh']->getOne( $query, true );
    echo $rs;

    // Write history
    $lastdata[0] = $data['id']; //for compatibility
    $lastdata[1] = json_decode( $rs )->cv->name;
    $lastdata[2] = $data['src'];
    $rs = $GLOBALS['dbh']->getOne( "select val from crmemployee where uid = '" . $_SESSION["loginCRM"]."' AND manid = ".$_SESSION['manid']." AND key = 'search_history'" ); //get current history

    $array_of_data = $rs['val'] ? json_decode( $rs['val'], true ) : array(); //current history in array or new empty array

    foreach( $array_of_data as $array_data ) {
        if( $lastdata[0] == $array_data[0] ) unset( $array_of_data[array_search( $lastdata, $array_of_data )] ); //remove duplicates
    }

    array_unshift( $array_of_data, $lastdata ); //add last access to array

    if( count( $array_of_data ) > 12 ) array_pop( $array_of_data ); //remove entry numer 12
    $GLOBALS['dbh']->update( 'crmemployee', array( 'val' ), array( json_encode( $array_of_data ) ), "uid = ".$_SESSION['loginCRM']." AND manid = ".$_SESSION['manid']." AND key = 'search_history'" );
}

/***********************************************
* Append query with sql select statments
* to build dialog drop down elements
**********************************************/
function appendQueryForCustomerDlg( &$query ){
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

    // Angestellte/ Verkäufer
    $query .= "(SELECT json_agg( employees ) AS employees FROM (".
                "SELECT id, name FROM employee WHERE deleted = false ORDER BY name ASC".
                ") AS employees) AS employees, ";

    // Zahlungsbedingung(en)
    $query .= "(SELECT json_agg( payment_terms ) AS payment_terms FROM (".
                "SELECT id, description FROM payment_terms WHERE obsolete = false ORDER BY description ASC".
                ") AS payment_terms) AS payment_terms, ";

    // Steuerzonen
    $query .= "(SELECT json_agg( tax_zones ) AS tax_zones FROM (".
                "SELECT id, description FROM tax_zones WHERE obsolete = false ORDER BY description DESC".
                ") AS tax_zones) AS tax_zones, ";

    // Branchen
    $query .= "(SELECT json_agg( branches ) AS branches FROM (".
                "SELECT branche AS name FROM public.customer WHERE branche IS NOT NULL GROUP BY branche ORDER BY branche ASC".
                ") AS branches) AS branches, ";

    // Sprachen
    $query .= "(SELECT json_agg( languages ) AS languages FROM (".
                "SELECT id, description FROM language WHERE obsolete = false ORDER BY description ASC".
                ") AS languages) AS languages, ";

    // Leads
    $query .= "(SELECT json_agg( lead ) AS lead FROM (".
                "SELECT id, lead FROM leads ORDER BY leads ASC".
                ") AS leads) AS leads, ";

    // Variablen
    $query .= "(SELECT json_agg( vars_conf ) AS vars_conf FROM (".
                "SELECT id, name, description AS label, type, description AS tooltip, '42' AS size, options AS data FROM custom_variable_configs WHERE module = 'CT' ORDER BY sortkey ASC".
                ") AS vars_conf) AS vars_conf";
}

function appendQueryForCustomVars( $data, &$query ){
    // Braucht 3 JOINs für Vendor, Customer und Parts um den Namen und die partnumber, customernumber und vendornumber zu holen
    $query .= "(SELECT json_agg( custom_vars ) AS custom_vars FROM (".
                "SELECT custom_variables.*, custom_variable_configs.name, custom_variable_configs.type FROM custom_variables JOIN custom_variable_configs ON custom_variables.config_id = custom_variable_configs.id WHERE trans_id = ".$data['id'].
                ") AS custom_vars) AS custom_vars, ";
}

/*
    1.Fall: HSN und TSN ist vorhanden und c2 ist in der DB leer oder null, dann wird die KBA geupdated
    2.Fall: Ist HSN und TSN und c2 vorhanden, dann wird geprüft zwischen den c2 vom FS-Scan: wenn verschieden dann git es ein Insert
    3.Fall: Ist TSN '000' dann wird in die KBA ein Insert ausgeführt (die Fahrzeuge können sich trotz gleicher HSN unterscheiden)
*/
function appendQueryWithKba( $data, &$query ){
    if( array_key_exists( 'hsn', $data ) ){
        $query .= "(SELECT row_to_json( kba ) AS kba FROM (".
                    "( SELECT true AS exists, * FROM lxckba WHERE lxckba.hsn = '".$data['hsn']."' AND  SUBSTRING( '".$data['tsn']."', 0, 4 ) = lxckba.tsn AND ( d2 IS NULL OR d2 = '".$data['d2']."' ) ) UNION ALL ".
                    "( SELECT false AS exists, * FROM lxckba WHERE lxckba.hsn = '".$data['hsn']."' AND  SUBSTRING( '".$data['tsn']."', 0, 4 ) = lxckba.tsn ) LIMIT 1".
                    ") AS kba) AS kba, ";
    }
}

/*********************************************
* Get data for customer or vendor
*********************************************/
function getCustomerForEdit( $data ){
    $db_table = array('C' => 'customer', 'V' => 'vendor');
    $query = "SELECT ";

    // costumer or vendor -> cv
    $query .= "(SELECT row_to_json( cv ) AS cv FROM (".
                "SELECT '".$data['src']."' AS src, id, greeting, name, street, zipcode, contact, phone, fax, email, city, country, bland, contact AS person, notes, business_id, sw, ".
                "account_number, currency_id, taxnumber, taxzone_id, payment_id, bank_code, bank, ustid, iban, bic, direct_debit, ".
                "branche, homepage, department_1, department_2, lead, leadsrc, konzern, headcount, language_id, employee, phone3, note_phone, note_fax, note_phone3 ".
                "FROM ".$db_table[$data['src']]." WHERE id=".$data['id'].
                ") AS cv) AS cv, ";

    // Lieferadressen
    $query .= "(SELECT json_agg( deladdr ) AS deladdr FROM (".
                "SELECT * FROM shipto WHERE trans_id = ".$data['id']." ORDER BY shiptoname ASC".
                ") AS deladdr) AS deladdr, ";

    appendQueryWithKba( $data, $query );
    appendQueryForCustomVars( $data, $query );
    appendQueryForCustomerDlg( $query );

    echo $GLOBALS['dbh']->getOne($query, true);
}

function getContactPerson( $data ){
    echo $GLOBALS['dbh']->getOne( "SELECT contacts.*, COALESCE ( customer.name, vendor.name) AS contacts_company_name FROM contacts LEFT JOIN customer ON contacts.cp_cv_id = customer.id LEFT JOIN vendor ON contacts.cp_cv_id = vendor.id WHERE cp_id = ".$data['id'], true );
}

function getCarKbaData( $data ){
   echo $GLOBALS['dbh']->getOne("SELECT * FROM lxckba WHERE lxckba.hsn = '".$data['hsn']."' AND  SUBSTRING( '".$data['tsn']."', 0, 4 ) = lxckba.tsn AND ( d2 IS NULL OR d2 = '".$data['d2']."' )", true);
}

function findCarKbaData( $data ){
   echo $GLOBALS['dbh']->getAll("SELECT id, tsn || ' / ' || COALESCE( d2, '---' ) AS label, tsn AS value, hersteller AS category, * FROM lxckba WHERE lxckba.hsn = '".$data['hsn']."' AND lxckba.tsn ILIKE SUBSTRING( '".$data['tsn']."', 0, 4 ) LIMIT 10", true);
}

function findCarKbaDataWithName( $data ){
    echo $GLOBALS['dbh']->getAll("SELECT id, tsn || ' / ' || COALESCE( name, '---' ) || ' / ' || COALESCE( datum, '---' ) AS label, name AS value, hersteller AS category, * FROM lxckba WHERE lxckba.hsn = '".$data['hsn']."' AND lxckba.name ILIKE '".$data['name']."%' LIMIT 10", true);
 }

 function getCarKbaDataById( $data ){
    echo $GLOBALS['dbh']->getOne("SELECT * FROM lxckba WHERE lxckba.id = ".$data['id'], true);
 }

 /***********************************************
* Get Data form DB to build drop down elemennts
* in new CV dialog
**********************************************/
function getCVInitData( ){
    $query = "SELECT ";

    appendQueryForCustomerDlg( $query );

    echo $GLOBALS['dbh']->getOne($query, true);
}

function getCVDialogData( $data ){
    $query = "SELECT ";

    appendQueryWithKba( $data, $query );
    appendQueryForCustomerDlg( $query );

    echo $GLOBALS['dbh']->getOne($query, true);
}

function getPhoneCallList(){ //Holt die Anruferliste von jetzt bis 1,5-Wochen in der Vergangenheit
    $sql = "SELECT  EXTRACT( EPOCH FROM TIMESTAMPTZ( crmti_init_time ) ) * 1000 AS call_date, crmti_status, crmti_src, crmti_dst, crmti_caller_id, crmti_caller_typ, crmti_direction, crmti_number, unique_call_id FROM crmti WHERE crmti_init_time > NOW() - INTERVAL '1.5 WEEK' ORDER BY crmti_init_time DESC";
    echo $GLOBALS['dbh']->getAll( $sql, TRUE ); //Ist der zwei Parameter true, dann wird das Ergebnis als JSON zurückgegeben
}

function playPhoneCall( $data ){
    $files = scandir(  '/var/spool/asterisk/monitor' );
    foreach( $files as $file ){
        if( strpos( $file, $data['id'] ) !== false && filesize( '/var/spool/asterisk/monitor/'.$file ) > 44 ){ //Dateien die kleiner als 44 Byte sind, sind i.d.R leere Dateien
            echo '{ "filename": "'.$file.'" }';
            return;
        }
    }
    resultInfo( false, 'File not found' );
}

function firstnameToGender( $data ){
    $rs = $GLOBALS['dbh']->getOne("SELECT gender FROM firstnametogender WHERE firstname ILIKE '".$data['name']."%'", true);
    if( empty( $rs ) ) $rs = '{ "gender": "E" }';
    echo $rs;
}

function zipcodeToLocation( ){
    if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ) {
        $term = $_GET['term'];
        echo $GLOBALS['dbh']->getAll("SELECT plz || ' ' || ort || ', ' || bundesland AS label, plz AS value, ort, bundesland FROM zipcode_to_location WHERE plz ILIKE '".$term."%' LIMIT 15", true);
    }
}

/*********************************************
* Based on old version of getScans function
*********************************************/
function insertScans( $data ){
    /***********************************************************************************************************************************************************
    ********** follow lines generate the $colArray *************************************************************************************************************
    ************************************************************************************************************************************************************
    $rsFsData = file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/ScanDetails/'.$apiKeyArray['lxcarsapi'].'/b7ef0bdf-0063-41f4-be05-8d9d5f0809ca/false' );
    $rsFsDataArray = json_decode( $rsFsData, TRUE ); //JSON to Array
    foreach( $rsFsDataArray AS $key => $value ) if( strpos( $key, 'img')) unset( $rsFsDataArray[$key]); // remove *_img
    $rsFsDataArrayKeys = array_keys( $rsFsDataArray );
    $col = '';
    foreach( $rsFsDataArrayKeys AS $key => $value ) $col .= "'$value', ";
    writeLog( $col );
    */
    $colArray = array( 'scan_detail_id', 'scan_id', 'ez', 'ez_string', 'hsn', 'tsn', 'vsn', 'field_2_2', 'vin', 'd3', 'registrationNumber', 'name1', 'name2', 'firstname', 'address1', 'address2', 'j', 'field_4', 'field_3', 'd1', 'd2_1', 'd2_2', 'd2_3', 'd2_4', 'field_2', 'field_5_1', 'field_5_2', 'v9', 'field_14', 'p3', 'field_10', 'field_14_1', 'p1', 'l', 'field_9', 'p2_p4', 't', 'field_18', 'field_19', 'field_20', 'g', 'field_12', 'field_13', 'q', 'v7', 'f1', 'f2', 'field_7_1', 'field_7_2', 'field_7_3', 'field_8_1', 'field_8_2', 'field_8_3', 'u1', 'u2', 'u3', 'o1', 'o2', 's1', 's2', 'field_15_1', 'field_15_2', 'field_15_3', 'r', 'field_11', 'k', 'field_6', 'field_17', 'field_16', 'field_21', 'field_22', 'hu', 'creation_date', 'creation_city', 'document_id', 'Maker', 'Model', 'PowerKw', 'PowerHpKw', 'Ccm', 'Fuel', 'FuelCode', 'Filename', 'itime' );

    //get last timestamp from database
    $lastTimestamp = $GLOBALS['dbh']->getOne( 'SELECT itime FROM lxc_fs_scans ORDER BY itime DESC LIMIT 1' )['itime'];
    $lastTsDB = (int) strtotime( $lastTimestamp ); //seconds from unix epoch

    $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );
    $rs = file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/GetScans/'.$apiKeyArray['lxcarsapi'].'/?take=16' );
    $rsArray = json_decode( $rs, TRUE ); //JSON to Array

    foreach( $rsArray AS $key => $value ){ // Timestamp from scanner is always a iso 8601 timestamp!!!
        $scanTs = (int) strtotime( $value['timestamp'] );
        if( $lastTsDB < $scanTs ){
            $rsFsData = file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/ScanDetails/'.$apiKeyArray['lxcarsapi'].'/'.$value['id'].'/false' );
            $rsFsDataArray = json_decode( $rsFsData, TRUE ); //JSON to Array
            foreach( $rsFsDataArray AS $key1 => $value1 ) if( strpos( $key1, 'img' ) ) unset( $rsFsDataArray[$key1] ); // remove *_img data
            $fsValues = array_values( $rsFsDataArray );
            $fsValues[] =  $value['timestamp'];
            $GLOBALS['dbh']->insert( 'lxc_fs_scans', $colArray, $fsValues );
        }
    }
}

/*********************************************
* New version of getScans function
* appends data for dialog elements
*********************************************/
function getScans( $data ){
    insertScans( $data );

    $query = "SELECT ";

    // scans
    $query .= "(SELECT json_agg( db_scans  ) AS db_scans FROM (".
                "SELECT *, to_char( itime::TIMESTAMP AT TIME ZONE 'GMT',  'DD.MM.YYYY HH24:MI' ) AS myts FROM lxc_fs_scans ORDER BY itime DESC LIMIT ".$data['fsmax'].
                ") AS db_scans) AS db_scans, ";

    appendQueryForCustomerDlg( $query );

    echo $GLOBALS['dbh']->getOne($query, true);
}

function getFsData( $data ){
//  $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );
//  $rs = file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/ScanDetails/'.$apiKeyArray['lxcarsapi'].'/'.$data['id'].'/false' );
    $rs = $GLOBALS['dbh']->getOne( "SELECT * FROM lxc_fs_scans WHERE scan_id = '".$data['id']."'", true );
    echo $rs;
}

function searchCustomerForScan( $data ){
    $rs = $GLOBALS['dbh']->getAll( "SELECT id, name, street, zipcode, city FROM customer WHERE name ILIKE '%".$data['name']."%' OR name ILIKE '%".$data['orig_name']."%' LIMIT 18", true );
    echo ( empty( $rs ) )? 0 : $rs;
}

function checkCarLicense( $data ){
    echo $GLOBALS['dbh']->getOne( "SELECT test.ln_exists, customer.name FROM (SELECT COALESCE ((SELECT 'true' FROM lxc_cars WHERE c_ln = '".$data['c_ln']."'), 'false') AS ln_exists) AS test LEFT JOIN lxc_cars ON lxc_cars.c_ln = '".$data['c_ln']."' LEFT JOIN customer ON lxc_cars.c_ow = customer.id", true);
}

function checkCarFin( $data ){
    echo $GLOBALS['dbh']->getOne( "SELECT test.fin_exists, customer.name FROM (SELECT COALESCE ((SELECT 'true' FROM lxc_cars WHERE c_fin = '".$data['fin']."'), 'false') AS fin_exists) AS test LEFT JOIN lxc_cars ON lxc_cars.c_fin = '".$data['fin']."' LEFT JOIN customer ON lxc_cars.c_ow = customer.id", true);
}

function checkCarLicenseAndFin( $data ){
    echo '{ "ln_check": ';
    checkCarLicense( $data );
    echo ', "fin_check": ';
    checkCarFin( $data );
    echo ' }';
}

function getCar( $data ){ //Stell die Daten für die Fahrzeugübersicht zusammen,
    $query = "
        SELECT 
            (
                SELECT row_to_json(car) AS car 
                FROM (
                    SELECT 
                        *, 
                        to_char(c_hu, 'DD.MM.YYYY') AS c_hu, 
                        to_char(c_d, 'DD.MM.YYYY') AS c_d 
                    FROM lxc_cars 
                    LEFT JOIN lxckba ON (lxc_cars.kba_id = lxckba.id) 
                    WHERE c_id = ".$data['id']."
                ) AS car
            ) AS car, 
            (
                SELECT json_agg(car_ord) AS car_ord 
                FROM (
                    SELECT DISTINCT ON (oe.itime)
                        to_char(oe.transdate, 'DD.MM.YYYY') AS date, 
                        COALESCE(instructions.description, orderitems.description) AS description, 
                        COALESCE(ROUND(amount, 2)) || ' ' || COALESCE(C.name) AS amount, 
                        oe.ordnumber AS number, 
                        oe.id 
                    FROM oe 
                    LEFT JOIN orderitems ON oe.id = orderitems.trans_id 
                    LEFT JOIN instructions ON oe.id = instructions.trans_id 
                    LEFT JOIN currencies C ON currency_id = C.id 
                    WHERE quotation = FALSE 
                        AND c_id = ".$data['id']." 
                    ORDER BY oe.itime DESC, orderitems.itime 
                    LIMIT 10
                ) AS car_ord
            ) AS car_ord,
            (
                SELECT row_to_json(cv) AS cv 
                FROM (
                    SELECT 
                        'C' AS src, 
                        id, 
                        customernumber AS cvnumber, 
                        name, 
                        street, 
                        zipcode, 
                        contact, 
                        phone AS phone1, 
                        fax AS phone2, 
                        phone3, 
                        note_phone AS note_phone1, 
                        note_fax AS note_phone2, 
                        note_phone3, 
                        email, 
                        city, 
                        country
                    FROM 
                        lxc_cars 
                    JOIN 
                        customer ON lxc_cars.c_ow = customer.id 
                    WHERE 
                        lxc_cars.c_id = ".$data['id']."
                ) AS cv
            ) AS cv
        ";
//    writeLog( $query );
    echo $GLOBALS['dbh']->getOne( $query, true );
}

function getDataForNewLxcarsOrder( $data ){
    $query = "SELECT customer.id AS customer_id, greeting AS customer_greeting, customer.name AS customer_name, customer.notes AS int_cu_notes, c_id, ".
                "lxc_cars.c_ln, lxc_cars.c_text AS int_car_notes, employee.id AS employee_id, employee.name AS employee_name ".
                "FROM lxc_cars INNER JOIN customer ON customer.id = lxc_cars.c_ow INNER JOIN employee ON employee.id = ".$_SESSION['loginCRM']." WHERE lxc_cars.c_id = ".$data['id'];

    echo '{ "common": '.$GLOBALS['dbh']->getOne( $query, true ).', "workers": '.json_encode(ERPUsersfromGroup("Werkstatt")).' }';
}

function getDataForNewOffer( $data ){
    $table = array( 'C' => 'customer', 'V' => 'vendor' );

    $query = "SELECT ";

    $query .= "(SELECT row_to_json( common ) AS common FROM (".
                "SELECT customer.id AS customer_id, greeting AS customer_greeting, customer.name AS customer_name, customer.notes AS int_cu_notes, ".
                "employee.id AS employee_id, employee.name AS employee_name ".
                "FROM ".$table[$data['src']]." INNER JOIN employee ON employee.id = ".$_SESSION['loginCRM']." WHERE ".$table[$data['src']].".id = ".$data['id'].
                ") AS common) AS common, ";

    $query .= "(SELECT json_agg( printers ) AS printers FROM (".
                "SELECT * FROM printers".
                ") AS printers) AS printers";

    //writeLog( $query );

    //echo '{ "common": '.$GLOBALS['dbh']->getOne( $query, true ).' }';
    echo $GLOBALS['dbh']->getOne( $query, true );
}

function getOffer( $data ){
    getOrder( $data, true );
}

function getOrder( $data, $offer = false){
    $orderID = $data['id'];
    $taxzone_id = 4;

    //$sql = "SELECT  item_id as id, parts_id, position, instruction, qty, mysubquery.description, unit, sellprice, marge_total, discount, u_id, partnumber, part_type, longdescription, status, rate, k.taxkey_id, tax.chart_id, chart.accno ".
    //        "FROM ( SELECT parts.instruction, parts.buchungsgruppen_id, instructions.id AS item_id, instructions.parts_id, instructions.qty, instructions.description, instructions.position, instructions.unit, instructions.sellprice, instructions.marge_total, instructions.discount, instructions.u_id, instructions.status, parts.partnumber, parts.part_type, instructions.longdescription ".
    //        "FROM instructions INNER JOIN  parts  ON ( parts.id = instructions.parts_id ) WHERE instructions.trans_id = '".$orderID."' UNION ".
    //        "SELECT  parts.instruction, parts.buchungsgruppen_id, orderitems.id AS item_id, orderitems.parts_id, orderitems.qty, orderitems.description, orderitems.position, orderitems.unit, orderitems.sellprice, orderitems.marge_total, orderitems.discount, orderitems.u_id, orderitems.status, parts.partnumber, parts.part_type, orderitems.longdescription ".
    //        "FROM orderitems INNER JOIN parts ON ( parts.id = orderitems.parts_id ) WHERE orderitems.trans_id = '".$orderID."' ORDER BY position ) AS mysubquery ".
    //        "JOIN taxzone_charts c ON ( mysubquery.buchungsgruppen_id = c.buchungsgruppen_id ) ".
    //        "JOIN taxkeys k ON ( c.income_accno_id = k.chart_id AND k.startdate = ( SELECT max(startdate) FROM taxkeys tk1 WHERE c.income_accno_id = tk1.chart_id AND tk1.startdate::TIMESTAMP <= NOW()  ) ) ".
    //        "JOIN tax ON (k.tax_id = tax.id ) LEFT JOIN chart ON ( tax.chart_id = chart.id ) ".
    //        "WHERE taxzone_id = 4 GROUP BY item_id, parts_id, position, instruction, qty, mysubquery.description, unit, sellprice, marge_total, discount, u_id, partnumber, part_type, longdescription, status, rate, k.taxkey_id, tax.chart_id, chart.accno ORDER BY position ASC";

    $sql = "SELECT  instructions.id, instructions.parts_id, instructions.qty, instructions.description, instructions.position, parts.instruction, instructions.unit,".
            " (SELECT row_to_json( unit_type ) AS unit_type FROM (".
            "   SELECT base_unit, factor FROM units WHERE units.name = instructions.unit".
            " ) AS unit_type) AS unit_type,".
            " instructions.sellprice, instructions.marge_total, instructions.discount, parts.partnumber, parts.part_type, instructions.longdescription, instructions.status, instructions.u_id,".
            " null AS buchungsziel".
            " FROM instructions LEFT JOIN parts ON instructions.parts_id = parts.id WHERE trans_id = '".$orderID."'".
            " UNION ALL".
            " SELECT  orderitems.id, orderitems.parts_id, orderitems.qty, orderitems.description, orderitems.position, parts.instruction, orderitems.unit,".
            " (SELECT row_to_json( unit_type ) AS unit_type FROM (".
            "   SELECT base_unit, factor FROM units WHERE units.name = orderitems.unit".
            " ) AS unit_type) AS unit_type,".
            " orderitems.sellprice, orderitems.marge_total, orderitems.discount, parts.partnumber, parts.part_type, orderitems.longdescription, orderitems.status, orderitems.u_id,".
            " (SELECT row_to_json( buchungsziel ) AS buchungsziel FROM (".
            "     SELECT c2.id AS income_chart_id, tk.tax_id, tx.chart_id AS tax_chart_id, tx.rate FROM parts p LEFT JOIN buchungsgruppen bg ON p.buchungsgruppen_id = bg.id LEFT JOIN taxzone_charts tc on bg.id = tc.buchungsgruppen_id LEFT JOIN chart c1 ON bg.inventory_accno_id = c1.id LEFT JOIN chart c2 ON tc.income_accno_id = c2.id LEFT JOIN chart c3 ON tc.expense_accno_id = c3.id LEFT JOIN taxkeys tk ON tk.chart_id = c2.id LEFT JOIN tax tx ON tx.id = tk.tax_id WHERE tc.taxzone_id = '4' AND p.id IN (parts.id) ORDER BY tk.startdate DESC LIMIT 1".
            " ) AS buchungsziel) AS buchungsziel".
            " FROM orderitems LEFT JOIN parts ON orderitems.parts_id = parts.id WHERE trans_id = '".$orderID."' ORDER BY position ASC";

    $query = "SELECT ";

    if( $offer ){
        $query .= "(SELECT row_to_json( common ) AS common FROM (".
                    "SELECT oe.*, customer.name AS customer_name, customer.greeting AS customer_greeting, customer.notes AS int_cu_notes, employee.id AS employee_id, employee.name AS employee_name FROM oe INNER JOIN customer ON customer.id = oe.customer_id INNER JOIN employee ON oe.employee_id = employee.id WHERE oe.id = ".$orderID.
                    ") AS common) AS common, ";

        $query .= "(SELECT json_agg( printers ) AS printers FROM (".
                    "SELECT * FROM printers".
                    ") AS printers) AS printers, ";
    }
    else{
        $query .= "(SELECT row_to_json( common ) AS common FROM (".
                    "SELECT * FROM ".
                    "(SELECT oe.*, customer.name AS customer_name, customer.greeting AS customer_greeting, customer.street AS customer_street, customer.zipcode AS customer_zipcode, customer.city AS customer_city, customer.notes AS int_cu_notes, lxc_cars.c_ln AS c_ln, lxc_cars.c_fin, to_char( lxc_cars.c_d, 'DD.MM.YYYY') AS c_d_de, c_mkb, c_2, c_3, lxc_cars.c_text AS int_car_notes, employee.id AS employee_id, employee.name AS employee_name, lxckba.hersteller, lxckba.name AS kba_typ, lxckba.leistung, lxckba.hubraum FROM oe INNER JOIN customer ON customer.id = oe.customer_id INNER JOIN lxc_cars ON lxc_cars.c_id = oe.c_id LEFT JOIN lxckba ON lxc_cars.kba_id = lxckba.id INNER JOIN employee ON oe.employee_id = employee.id WHERE oe.id = ".$orderID.") AS lxcars ".
                    "UNION ".
                    "(SELECT oe.*, customer.name AS customer_name, customer.greeting AS customer_greeting, customer.street AS customer_street, customer.zipcode AS customer_zipcode, customer.city AS customer_city, customer.notes AS int_cu_notes, lxc_cars.c_ln AS c_ln, lxc_cars.c_fin, to_char( lxc_cars.c_d, 'DD.MM.YYYY') AS c_d_de, c_mkb, c_2, c_3, lxc_cars.c_text AS int_car_notes, employee.id AS employee_id, employee.name AS employee_name, lxckba.hersteller, lxckba.name AS kba_typ, lxckba.leistung, lxckba.hubraum FROM oe INNER JOIN customer ON customer.id = oe.customer_id LEFT JOIN lxc_cars ON lxc_cars.c_id = oe.c_id LEFT JOIN lxckba ON lxc_cars.kba_id = lxckba.id INNER JOIN employee ON oe.employee_id = employee.id WHERE oe.id = ".$orderID.") ".
                    ") AS common) AS common, ";
    }

    $query .= "(SELECT json_agg( orderitems ) AS orderitems FROM (".$sql.") AS orderitems) AS orderitems";

    if( $offer ){
        echo '{ "offer": '.$GLOBALS['dbh']->getOne( $query, true ).' }';
    }
    else{
        $workers = json_encode(ERPUsersfromGroup("Werkstatt"));
        echo '{ "order": '.$GLOBALS['dbh']->getOne( $query, true ).', "workers": '.$workers.' }';
    }
}

function getInvoice( $data, $flag = null ){  //ToDo c_id verwenden statt shippingpoint
    $invoiceID = $data['id'];
    $taxzone_id = 4; //ToDo: Steuerzone aus der Rechnung holen (übergeben)

    //$sql = "SELECT  item_id as id, parts_id, position, qty, mysubquery.description, unit, sellprice, marge_total, discount, partnumber, part_type, longdescription, rate, k.taxkey_id, tax.chart_id, chart.accno FROM ".
    //    "( SELECT  parts.buchungsgruppen_id, invoice.id AS item_id, invoice.parts_id, invoice.qty, invoice.description, invoice.position, invoice.unit, invoice.sellprice, invoice.marge_total, invoice.discount, parts.partnumber, parts.part_type, invoice.longdescription ".
    //    "FROM invoice INNER JOIN parts ON ( parts.id = invoice.parts_id ) WHERE invoice.trans_id = ".$invoiceID." ORDER BY position ) AS mysubquery ".
    //    "JOIN taxzone_charts c ON ( mysubquery.buchungsgruppen_id = c.buchungsgruppen_id ) ".
    //    "JOIN taxkeys k ON ( c.income_accno_id = k.chart_id AND k.startdate = ( SELECT max(startdate) FROM taxkeys tk1 WHERE c.income_accno_id = tk1.chart_id AND tk1.startdate::TIMESTAMP <= NOW() ) ) ".
    //    "JOIN tax ON ( k.tax_id = tax.id ) ".
    //    "LEFT JOIN chart ON ( tax.chart_id = chart.id ) ".
    //    "WHERE taxzone_id = 4 GROUP BY item_id, parts_id, position, qty, mysubquery.description, unit, sellprice, marge_total, discount, partnumber, part_type, longdescription, rate, k.taxkey_id, tax.chart_id, chart.accno ORDER BY position ASC";

    $sql = "SELECT  invoice.id, invoice.parts_id, invoice.qty, invoice.description, invoice.position, invoice.unit, invoice.fxsellprice AS sellprice, invoice.marge_total, invoice.discount, parts.partnumber, parts.part_type, invoice.longdescription,".
        " (SELECT row_to_json( buchungsziel ) AS buchungsziel FROM (".
        "    SELECT c2.id AS income_chart_id, tk.tax_id, tx.chart_id AS tax_chart_id, tx.rate FROM parts p LEFT JOIN buchungsgruppen bg ON p.buchungsgruppen_id = bg.id LEFT JOIN taxzone_charts tc on bg.id = tc.buchungsgruppen_id LEFT JOIN chart c1 ON bg.inventory_accno_id = c1.id LEFT JOIN chart c2 ON tc.income_accno_id = c2.id LEFT JOIN chart c3 ON tc.expense_accno_id = c3.id LEFT JOIN taxkeys tk ON tk.chart_id = c2.id LEFT JOIN tax tx ON tx.id = tk.tax_id WHERE tc.taxzone_id = '4' AND p.id IN (parts.id) ORDER BY tk.startdate DESC LIMIT 1".
        " ) AS buchungsziel) AS buchungsziel".
        " FROM invoice LEFT JOIN parts ON invoice.parts_id = parts.id WHERE trans_id = ".$invoiceID." ORDER BY position ASC";

    $query = "SELECT ";
    $query .= "(SELECT row_to_json( common ) AS common FROM (".
                "SELECT ar.*, customer.name AS customer_name, customer.greeting AS customer_greeting, customer.notes AS int_cu_notes, lxc_cars.c_id AS c_id, lxc_cars.c_ln AS c_ln, lxc_cars.c_text AS int_car_notes, employee.id AS employee_id, employee.name AS employee_name FROM ar INNER JOIN customer ON customer.id = ar.customer_id LEFT JOIN lxc_cars ON lxc_cars.c_ln = ar.shippingpoint INNER JOIN employee ON ar.employee_id = employee.id WHERE ar.id = ".$invoiceID.
                ") AS common) AS common, ";

    $query .= "(SELECT json_agg( printers ) AS printers FROM (".
                "SELECT * FROM printers".
                ") AS printers) AS printers, ";

    $query .= "(SELECT json_agg( payment ) AS payment FROM (".
                "SELECT chart_id, amount, source, memo, transdate FROM acc_trans WHERE trans_id = ".$invoiceID." AND chart_link LIKE '%AR_paid%' ORDER BY acc_trans_id DESC".
                ") AS payment) AS payment, ";

    $query .= "(SELECT json_agg( payment_acc ) AS payment_acc FROM (".
                "SELECT id, accno, description FROM chart WHERE link LIKE '%AP_paid%' ORDER BY accno". //<= Kann auch auf ein echtes Bankkonto buchen
                //Zahlungseingangskonto darf nicht mit einem Bankkonto verknüpft sein =>
                //"SELECT chart.id, chart.accno, chart.description, bank_accounts.chart_id FROM chart LEFT JOIN bank_accounts ON chart.id = bank_accounts.chart_id WHERE link LIKE '%AP_paid%' AND chart_id IS NULL".
                ") AS payment_acc) AS payment_acc, ";

    $query .= "(SELECT json_agg( invoice ) AS invoice FROM (".$sql.") AS invoice) AS invoice";
    //writeLogR( $query );
    //writeLogR( $sql );

    echo '{ "bill": '.$GLOBALS['dbh']->getOne( $query, true ).(( $flag != null )? ', "flag": "'.$flag.'" }' : ' }');
}

function insertInvoiceFromOrder( $data ){
    $exists = $GLOBALS['dbh']->getOne( "SELECT id FROM ar WHERE ordnumber = '".$data['ordnumber']."' LIMIT 1" );

    if( is_array( $exists ) && sizeof( $exists ) > 0 ){
        getInvoice( $exists, "exists" );
        return;
    }

    $GLOBALS['dbh']->beginTransaction();
    $id = $GLOBALS['dbh']->getOne( "WITH tmp AS ( UPDATE defaults SET invnumber = invnumber::INT + 1 RETURNING invnumber) ".
                                "INSERT INTO ar ( invnumber, customer_id, employee_id, taxzone_id, currency_id, shippingpoint, notes, ordnumber, intnotes, shipvia, amount, netamount, invoice, type ) ".
                                "SELECT (SELECT invnumber FROM tmp), oe.customer_id, ".$_SESSION['id'].", oe.taxzone_id, oe.currency_id, oe.shippingpoint, oe.notes, oe.ordnumber, oe.intnotes, oe.shipvia, oe.amount, oe.netamount, true AS invoice, 'invoice' AS type ".
                                "FROM oe WHERE id = ".$data['oe_id']." RETURNING id;" )['id'];

    $query = "INSERT INTO invoice (trans_id, position, parts_id, description, longdescription, qty, unit, sellprice, discount, marge_total, fxsellprice) ".
            "(SELECT ".$id.", position, parts_id, description, longdescription, qty, unit, sellprice, discount, marge_total, sellprice AS fxsellprice FROM orderitems WHERE trans_id = ".$data['oe_id'].")";

    $GLOBALS['dbh']->myquery( $query );
    $GLOBALS['dbh']->commit();

    $exists = array( "id" => $id );
    getInvoice( $exists );
}

function insertOfferFromOrder( $data ){
    $query = "WITH tmp AS ( UPDATE defaults SET sqnumber = sqnumber::INT + 1 RETURNING sqnumber ) ".
                "INSERT INTO oe ( quonumber, quotation, ".
                "ordnumber, transdate, vendor_id, customer_id, amount, netamount, reqdate, taxincluded, shippingpoint, notes, employee_id, ".
                "closed, cusordnumber, intnotes, department_id, shipvia, cp_id, language_id, payment_id, delivery_customer_id, ".
                "delivery_vendor_id, taxzone_id, proforma, shipto_id, order_probability, expected_billing_date, globalproject_id, delivered, ".
                "salesman_id, marge_total, marge_percent, transaction_description, delivery_term_id, currency_id, exchangerate, ".
                "tax_point, km_stnd, c_id, status, car_status, finish_time, printed, car_manuf, car_type, internalorder, ".
                "billing_address_id, order_status_id ".
                ") SELECT ( SELECT sqnumber FROM tmp ), true, ".
                "ordnumber, transdate, vendor_id, customer_id, amount, netamount, reqdate, taxincluded, shippingpoint, notes, ".$_SESSION['id'].", ".
                "closed, cusordnumber, intnotes, department_id, shipvia, cp_id, language_id, payment_id, delivery_customer_id, ".
                "delivery_vendor_id, taxzone_id, proforma, shipto_id, order_probability, expected_billing_date, globalproject_id, delivered, ".
                "salesman_id, marge_total, marge_percent, transaction_description, delivery_term_id, currency_id, exchangerate, ".
                "tax_point, km_stnd, c_id, status, car_status, finish_time, printed, car_manuf, car_type, internalorder, ".
                "billing_address_id, order_status_id ".
                "FROM oe WHERE id = ".$data['oe_id']." RETURNING id;";

    //writeLog( '---' );
    //writeLog( $query );

    $GLOBALS['dbh']->beginTransaction();
    $id = $GLOBALS['dbh']->getOne( $query )['id'];

    $query = "INSERT INTO orderitems (trans_id, position, parts_id, description, longdescription, qty, unit, sellprice, discount, marge_total) ".
            "(SELECT ".$id.", position, parts_id, description, longdescription, qty, unit, sellprice, discount, marge_total FROM orderitems WHERE trans_id = ".$data['oe_id'].")";

    $GLOBALS['dbh']->myquery( $query );
    $GLOBALS['dbh']->commit();

    getOrder( array( "id" => $id ), true );
}

function saveOrder( $data ){
    if( array_key_exists( 'buchungsziel', $data ) ){
        $GLOBALS['dbh']->beginTransaction();
        if( array_key_exists( 'id', $data['buchungsziel'] ) ){
            $GLOBALS['dbh']->query( "DELETE FROM acc_trans WHERE trans_id = ".$data['buchungsziel']['id'] );
        }
        if( array_key_exists( 'charts', $data['buchungsziel'] ) ){
            foreach( $data['buchungsziel']['charts'] AS $key => $value ){
                for($i = 0; $i < count( $value ); $i++ ){
                    //writeLog( $value );
                    $query = "INSERT INTO acc_trans (trans_id, chart_id, amount, transdate, source, memo, tax_id, taxkey, chart_link) VALUES (".$data['buchungsziel']['id'].", ".$key.", '".$value[$i]['amount']."', CURRENT_DATE, '".$value[$i]['source']."', '".$value[$i]['memo']."', ".$value[$i]['tax_id'].", (SELECT taxkey_id FROM taxkeys WHERE chart_id = ".$key." AND startdate <= CURRENT_DATE ORDER BY startdate DESC LIMIT 1), (SELECT link FROM chart WHERE id = ".$key.") )";
                    $GLOBALS['dbh']->query( $query );
                }
            }
        }
        $GLOBALS['dbh']->commit();
        unset( $data['buchungsziel'] );
    }
    genericUpdateEx( $data );
}

//Wird aufgerufen in der Funktion insertNewCuWithCar und  updateCuWithNewCar
function prepareKba( &$data ){
    $kba_id = FALSE;
    if( array_key_exists( 'lxckba', $data ) && array_key_exists( 'lxc_cars', $data )){
        if( array_key_exists( 'kba_id', $data['lxc_cars'] ) && !strpos( $data['lxckba']['tsn'], '000' ) ){
            $kba_id = $data['lxc_cars']['kba_id'];
            $where = "id = ".$kba_id;
            $GLOBALS['dbh']->update( 'lxckba', array_keys( $data['lxckba'] ), array_values( $data['lxckba'] ), $where );
        }
        else{
            $kba_id = $GLOBALS['dbh']->insert( 'lxckba', array_keys( $data['lxckba'] ), array_values( $data['lxckba'] ), TRUE );
            $data['lxc_cars'] += [ "kba_id" => $kba_id ];
        }
    }
    unset( $data['lxckba'] );
}

function getGenericTranslations( $data ){
    $query = "SELECT ".
                "(SELECT greeting FROM customer WHERE id = ".$data['id'].") AS greeting, ".
                "(SELECT translation FROM generic_translations WHERE translation_type = 'salutation_female') AS salutation_female, ".
                "(SELECT translation FROM generic_translations WHERE translation_type = 'salutation_punctuation_mark') AS salutation_punctuation_mark, ".
                "(SELECT translation FROM generic_translations WHERE translation_type = 'salutation_general') AS salutation_general, ".
                "(SELECT translation FROM generic_translations WHERE translation_type = 'preset_text_sales_quotation') AS preset_text_sales_quotation, ".
                "(SELECT translation FROM generic_translations WHERE translation_type = 'preset_text_periodic_invoices_email_body') AS preset_text_periodic_invoices_email_body, ".
                "(SELECT translation FROM generic_translations WHERE translation_type = 'preset_text_invoice') AS preset_text_invoice, ".
                "(SELECT translation FROM generic_translations WHERE translation_type = 'preset_text_sales_order') AS preset_text_sales_order, ".
                "(SELECT translation FROM generic_translations WHERE translation_type = 'salutation_male') AS salutation_male, ".
                "(SELECT translation FROM generic_translations WHERE translation_type = 'preset_text_periodic_invoices_email_subject') AS preset_text_periodic_invoices_email_subject";

    echo $GLOBALS['dbh']->getOne($query, true);
}

/********************************************
* Insert a new Customer optional  with new Car
********************************************/
function insertNewCuWithCar( $data ){
    $id = FALSE;
    $GLOBALS['dbh']->beginTransaction();
    prepareKba( $data );
    foreach( $data AS $key => $value ){
        if( strcmp( $key, 'customer' ) === 0 ){
            $value['customernumber'] = calculateCVnumber( ( ( array_key_exists( 'business_id', $value ) )? $value['business_id'] : null ), 'customer' ); //Kundennummer berechnen
            $id = $GLOBALS['dbh']->insert( $key, array_keys( $value ), array_values( $value ), TRUE, "id" );
            if( FALSE === $id ){
                $GLOBALS['dbh']->rollBack();
                resultInfo( false , "Error: Update ".$key );
                return;
            }
         }
        elseif( strcmp( $key, 'lxc_cars' ) === 0 ){
            $value['c_ow'] = $id;
            if( $GLOBALS['dbh']->insert( $key, array_keys( $value ), array_values( $value ) )  === FALSE ){
                $GLOBALS['dbh']->rollBack();
                resultInfo( false , "Error: Update ".$key );
                return;
            }
        }
        elseif( strcmp( $key, 'custom_variables' ) === 0 ){
            for( $i = 0; $i < count( $value  ); $i++ ){
                $value[$i]['trans_id'] = $id;
            }
            updateCustomVars( $key, $value );
        }
        else{
            if( $GLOBALS['dbh']->insert( $key, array_keys( $value ), array_values( $value ) ) === FALSE ){
                $GLOBALS['dbh']->rollBack();
                resultInfo( false , "Error: Update ".$key );
                return;
            }
         }
    }
    $GLOBALS['dbh']->commit();

    echo '{ "src": "C", "id": "'.$id.'" }';
}

function insertNewOrder( $data ){
    $rs = $GLOBALS['dbh']->getOne( "WITH tmp AS ( UPDATE defaults SET sonumber = sonumber::INT + 1 RETURNING sonumber) INSERT INTO oe ( ordnumber, customer_id, employee_id, taxzone_id, currency_id, c_id) SELECT ( SELECT sonumber FROM tmp), ".$data['customer_id'].", ".$_SESSION['id'].",  customer.taxzone_id, customer.currency_id, ".$data['c_id']." FROM customer WHERE customer.id = ".$data['customer_id']." RETURNING id, ordnumber");
    echo '{ "id": "'.$rs['id'].'", "ordnumber": "'.$rs['ordnumber'].'"  }';
}

function insertNewOffer( $data ){
    $sql = "WITH tmp AS ( UPDATE defaults SET sqnumber = sqnumber::INT + 1 RETURNING sqnumber ) ".
            "INSERT INTO oe ( quonumber, ordnumber, customer_id, employee_id, taxzone_id, currency_id, quotation";
    if( array_key_exists( 'c_id', $data ) ) $sql .= ", c_id";
    $sql .= ") SELECT ( SELECT sqnumber FROM tmp ), '', ".$data['customer_id'].", ".$_SESSION['id'].",  customer.taxzone_id, customer.currency_id, true";
    if( array_key_exists( 'c_id', $data ) ) $sql .= ", ".$data['c_id'];
    $sql .= " FROM customer WHERE customer.id = ".$data['customer_id']." RETURNING id, quonumber, itime";

    //writeLog( $sql );

    $rs = $GLOBALS['dbh']->getOne( $sql );
    echo '{ "id": "'.$rs['id'].'", "quonumber": "'.$rs['quonumber'].'", "itime": "'.$rs['itime'].'" }';
}

function calculateCVnumber( $business, $cv ){ //Berechnet die nächste Kundennummer für einen Kunden oder Lieferanten
    if( $cv == 'vendor' ){ //Lieferanten werden in Zukunft nur über defaults hochgezählt, die Kundengruppe wird nicht mehr berücksichtigt
        $rs = $GLOBALS['dbh']->getOne( "SELECT vendornumber::int AS newnumber FROM defaults" );
        while( $GLOBALS['dbh']->getOne( "SELECT vendornumber FROM vendor WHERE vendornumber = '".++$rs['newnumber']."'" )['vendornumber'] );
        $GLOBALS['dbh']->myquery( "UPDATE defaults SET vendornumber = ".$rs['newnumber'] );
    }
    if( $cv == 'customer' ){
        if( $business ){ //Kundengruppe  vorhanden
            $rs = $GLOBALS['dbh']->getOne( "SELECT customernumberinit::int AS newnumber FROM business WHERE id = ".$business );
            //wir suchen die nächste freie Nummer
            while( $GLOBALS['dbh']->getOne( "SELECT customernumber FROM customer WHERE customernumber = '".++$rs['newnumber']."'" )['customernumber'] );
            $GLOBALS['dbh']->myquery( "UPDATE business SET customernumberinit = ".$rs['newnumber']." WHERE id = ".$business );
        }
        else{ // keine Kundengruppe vorhanden, business ist leer
            $rs = $GLOBALS['dbh']->getOne( "SELECT customernumber::int AS newnumber FROM defaults" );
            while( $GLOBALS['dbh']->getOne( "SELECT customernumber FROM customer WHERE customernumber = '".++$rs['newnumber']."'" )['customernumber'] );
            $GLOBALS['dbh']->myquery( "UPDATE defaults SET customernumber = ".$rs['newnumber'] );
        }
    }
    return $rs['newnumber'];
}

function newCV( $data ){
    $cv_id = null;
    $cv_src = null;

    foreach( $data AS $key => $value ){
        if( strcmp( $key, 'customer' ) === 0 || strcmp( $key, 'vendor' ) === 0 ){
            $cv_src = ( strcmp( $key, 'customer' ) === 0 )? 'C' : 'V';
            $cv_nr = calculateCVnumber( ( ( array_key_exists( 'business_id', $value ) )? $value['business_id'] : null ), $key );
            $value[$key.'number'] = $cv_nr;
            $cv_id = $GLOBALS['dbh']->insert( $key, array_keys( $value ), array_values( $value ), TRUE, "id" );
        }
        elseif( strcmp( $key, 'custom_variables' ) === 0 ){
            for( $i = 0; $i < count( $value  ); $i++ ){
                $value[$i]['trans_id'] = $cv_id;
            }
            updateCustomVars( $key, $value );
        }
        else{
            $value['cp_cv_id'] = $cv_id;
            $GLOBALS['dbh']->insert( $key, array_keys( $value ), array_values( $value ));
        }
    }

    makeCVDir( $cv_src, $cv_id );
    echo '{ "src": "'.$cv_src.'", "id": "'.$cv_id.'" }';
}

function updateCustomVars( $db_table, $custom_vars ){
    foreach( $custom_vars AS $custom_var ){
        $where = '';
        if( array_key_exists( 'WHERE', $custom_var ) ){
                foreach( $custom_var['WHERE'] AS $whereId => $whereVal ){
                    if( strcmp( $whereId, 'id' ) === 0 ) $id = $whereVal;
                    //Achtung nur eine Bedingung möglich, mit dem Schema 'beliebige ID ist gleich Wert'
                    $where = $whereId.' = '.$whereVal;
                }
                unset( $custom_var['WHERE'] );
        }
        if( empty( $where ) ){
            if( array_key_exists( 'timestamp_value', $custom_var ) && empty( $custom_var['timestamp_value'] ) ) unset( $custom_var['timestamp_value'] );
            $GLOBALS['dbh']->insert( $db_table, array_keys( $custom_var ), array_values( $custom_var ) );
        }
        else{
            if( array_key_exists( 'timestamp_value', $custom_var ) && empty( $custom_var['timestamp_value'] ) ) $custom_var['timestamp_value'] = null;
            $GLOBALS['dbh']->update( $db_table, array_keys( $custom_var ), array_values( $custom_var ), $where );
        }
    }
}

function makeCVDir( $cv_src, $cv_id, $unlink = false ){
    $trans = array( ' ' => '_', '\\'=> '_', '/' => '_' , ':' => '_',  '*' => '_',  '?' => '_',  '"' => '_', '<' => '_',  '>' => '_',  '|' => '_', ',' => '');
    $mandant = strtr( $_SESSION['mandant'], $trans );
    $base_dir = $_SESSION['crmpath']."/dokumente/".$mandant.'/';
    $dir .= $base_dir.( ( strcmp( $cv_src, 'C' ) === 0 )? 'C' : 'V' );
    $dir .= $cv_id;
    $trash_dir = $dir.'/.trash/';
    $permissions = ( $_SESSION['dir_mode'] )? octdec( $_SESSION['dir_mode'] ) : 0777;
    //Ordner für Kunden/Lieferanten erstellen
    if( !$unlink && !file_exists( $dir ) ) mkdir( $dir, $permissions, true );
    if ( $_SESSION['dir_group'] ) chgrp( $dir, $_SESSION['dir_group'] );
    //Ordner für Papierkorb erstellen (Ordner für Thumbnails werden von elfinder automatisch erstellt)
    if( !$unlink && !file_exists( $trash_dir ) ) mkdir( $trash_dir, $permissions, true );
    if ( $_SESSION['dir_group'] ) chgrp( $trash_dir, $_SESSION['dir_group'] );
    //Symlink erstellen
    $table = array( 'C' => 'customer', 'V' => 'vendor' );
    $rs = $GLOBALS['dbh']->getOne( "SELECT name, $table[$cv_src]number FROM $table[$cv_src] WHERE id = ".$cv_id );
    $link_dir = $base_dir."link_dir_".( ( strcmp( $cv_src, 'C' ) === 0 )? 'cust' : 'vend' ).'/';
    $link = $link_dir.strtr( $rs['name'].'_'.$rs[$table[$cv_src].'number'], $trans );
    if( !file_exists( $link_dir ) ) mkdir( $link_dir, $permissions, true );
    if( !$unlink ) symlink( $dir, $link );
    else unlink( $link );
}

/********************************************
* Ubdate Customer optional with new Car
* KBA-Daten werden in der Funktion prepareKba
* in die DB eingefügt oder aktualisiert
********************************************/
function updateCuWithNewCar( $data ){
    $id = FALSE;
    $GLOBALS['dbh']->beginTransaction();
    prepareKba( $data );
    foreach( $data AS $key => $value ){
        $where = '';
        if( array_key_exists( 'WHERE', $value ) ){
                foreach( $value['WHERE'] AS $whereId => $whereVal ){
                    if( strcmp( $whereId, 'id' ) === 0 ) $id = $whereVal;
                    //Achtung nur eine Bedingung möglich, mit dem Schema 'beliebige ID ist gleich Wert'
                    $where = $whereId.' = '.$whereVal;
                }
                unset( $value['WHERE'] );
        }
        makeCVDir( 'C', $id, true );
        if( strcmp( $key, 'lxc_cars' ) === 0 ){
            $value['c_ow'] = $id;
            $GLOBALS['dbh']->insert( $key, array_keys( $value ), array_values( $value ) );
        }
        elseif( strcmp( $key, 'shipto' ) === 0 ){
            if( empty( $where ) ){
                $GLOBALS['dbh']->insert( $key, array_keys( $value ), array_values( $value ) );
            }
            else{
                $GLOBALS['dbh']->update( $key, array_keys( $value ), array_values( $value ), $where );
            }
        }
        elseif( strcmp( $key, 'custom_variables' ) === 0 ){
            updateCustomVars( $key, $value );
        }
        else{
            if( empty( $where ) ){
                resultInfo( false, 'Risky SQL-Statment with empty WHERE clausel'  );
                return;
            }
            $GLOBALS['dbh']->update( $key, array_keys( $value ), array_values( $value ), $where );
        }
    }
    $GLOBALS['dbh']->commit();

    makeCVDir( 'C', $id );
    echo '{ "src": "C", "id": "'.$id.'" }';
}

function insertOrderPosHuAu( $data ){
    $today   = date( 'Y-m-d' );
    $newdate = date( 'Y-m-01', strtotime( $today.' + 2 year ' ) );
    $GLOBALS['dbh']->update( 'lxc_cars', array( 'c_hu' ), array( $newdate ), 'c_id = '.$data['record']['huau'] );

    genericSingleInsert( $data );
}

/*
Das JSON-Objekt muss folgendes Schema haben:
data[record][tabellename][columnname]: wert
data[sequence_name]: id bzw. sequencename
*/
function genericSingleInsert( $data, $getLastId = false, $no_output = false ){
    //writeLog( $data );
    $tableName = array_key_first( $data['record'] );
    $id = $GLOBALS['dbh']->insert( $tableName, array_keys( $data['record'][$tableName] ), array_values( $data['record'][$tableName] ),
                                array_key_exists( 'sequence_name', $data ) || $getLastId, ( array_key_exists( 'sequence_name', $data ) )? $data['sequence_name'] : FALSE );
    if( !$no_output ) echo '{ "id": "'.$id.'" }';
}

function genericSingleInsertGetId( $data ){
    genericSingleInsert( $data, true );
}

function getblandid($data){
    $sql = "SELECT id FROM bundesland WHERE bundesland ='".$data["bundesland"]."'";
    $rs = $GLOBALS['dbh']->getOne($sql, true);
    echo $rs;
}

function genericUpdate( $data ){
    foreach( $data AS $key => $value ){
        $where = '';
        if( array_key_exists( 'WHERE', $value ) ){
                foreach( $value['WHERE'] AS $whereId => $whereVal ){
                    //Achtung nur eine Bedingung möglich, mit dem Schema 'beliebige ID ist gleich Wert' (siehe genericUpdateEx)
                    $where = $whereId.' = '.$whereVal;
                }
                unset( $value['WHERE'] );
        }
        if( empty( $where ) ){
            resultInfo( false, 'Risky SQL-Statment with empty WHERE clausel'  );
            return;
        }

        foreach( $value AS $i => $val){
            if( empty( $val ) ) $value[$i] = null;
        }

        /*writeLog( $key ); writeLog( array_keys( $value ) );*/  /* writeLog( $where );*/
        //writeLogR( array_keys( $value ) );
        //writeLogR( array_values( $value ) );
        $GLOBALS['dbh']->update( $key, array_keys( $value ), array_values( $value ), $where );
    }

    resultInfo( true );
}

 /*

    */


function genericUpdateEx( $data ){
    //$start = hrtime( true );
    //writeLog( $data );
    $update = function( $tableName, $dataObject ){
        $where = '';
        if( array_key_exists( 'WHERE', $dataObject ) ){
            $where = $dataObject['WHERE'];
            unset( $dataObject['WHERE'] );
        }
        if( empty( $where ) ){
            return false;
        }
        $dbFields = array_keys( $dataObject );
        $dbValues = array_values( $dataObject );
        $GLOBALS['dbh']->update( $tableName, $dbFields, $dbValues, $where );
        return true;
    };

    $GLOBALS['dbh']->beginTransaction();
    foreach( $data AS $tableName => $dataObject ){
        if( array_key_exists(0, $dataObject) ){
            foreach( $dataObject AS $dataRow ){
                if( !$update( $tableName, $dataRow ) ){
                    $GLOBALS['dbh']->rollBack();
                    resultInfo( false, 'Risky SQL-Statment with empty WHERE clausel'  );
                    return;
                }
            }
        }
        else{
            if( !$update( $tableName, $dataObject ) ){
                $GLOBALS['dbh']->rollBack();
                resultInfo( false, 'Risky SQL-Statment with empty WHERE clausel'  );
                return;
            }
        }
    }
    $GLOBALS['dbh']->commit();
    resultInfo( true );
    //$end = hrtime( true );
    //$eta = $end - $start;
    //writeLogR( "genericUpdateEx Zeit: ".$eta / 1e+6 ."in ms");
}

/*
Das JSON-Objekt muss folgendes Schema haben:
[table_name]['WHERE']: 'id = wert'
*/
function genericDelete( $data ){
    foreach( $data AS $tableName => $where){
        if( !isset( $where['WHERE'] ) ){
            resultInfo( false, 'Risky SQL-Statment with empty WHERE clausel'  );
            return;
        }
        $GLOBALS['dbh']->myquery( "DELETE FROM $tableName WHERE ".$where['WHERE'] );
    }
    resultInfo(true);
}

function printOrder( $data ){
    //WICHTIG: sudo apt-get install qrencode
    require 'fpdf.php';
    require_once __DIR__.'/../lxcars/inc/lxcLib.php';
    include_once __DIR__.'/../lxcars/inc/config.php';

    if( isset( $data['data'] ) ){
        $data = $data['data'];
    }

    $sql  = "SELECT oe.ordnumber, oe.transdate, oe.finish_time, oe.km_stnd, oe.employee_id, printed, ";
    $sql .= "customer.name AS customer_name, customer.greeting AS customer_greeting, customer.street, customer.zipcode, customer.city, customer.phone, customer.fax, customer.notes, ";
    $sql .= "lxc_cars.c_ln, lxc_cars.c_2, lxc_cars.c_3, lxc_cars.c_mkb, lxc_cars.c_t, lxc_cars.c_fin, lxc_cars.c_st_l, lxc_cars.c_wt_l, ";
    $sql .= "lxc_cars.c_text, lxc_cars.c_color, lxc_cars.c_zrk, lxc_cars.c_zrd, lxc_cars.c_em, lxc_cars.c_bf, lxc_cars.c_wd, lxc_cars.c_d, lxc_cars.c_hu, kba_id, employee.name AS employee_name, lxc_flex.flxgr ";
    $sql .= "FROM oe join customer on oe.customer_id = customer.id join lxc_cars on oe.c_id = lxc_cars.c_id join employee on oe.employee_id = employee.id ";
    $sql .= "left join lxc_flex on ( lxc_cars.c_2 = lxc_flex.hsn AND lxc_flex.tsn = substring( lxc_cars.c_3 from 1 for 3 ) ) WHERE oe.id = ".$data['orderId'];

    $query = "SELECT * FROM ($sql) AS o LEFT JOIN lxckba ON lxckba.id = o.kba_id";

    $orderData = $GLOBALS['dbh']->getOne( $query );

    $taxzone_id = 4;
    $query  = "SELECT  item_id as id, parts_id, position, instruction, qty, description, unit, sellprice, marge_total, discount, u_id, partnumber, part_type, longdescription, status, rate ";
    $query .= "FROM ( SELECT parts.instruction, parts.buchungsgruppen_id, instructions.id AS item_id, instructions.parts_id, instructions.qty, instructions.description, instructions.position, instructions.unit, instructions.sellprice, instructions.marge_total, instructions.discount, instructions.u_id, instructions.status, parts.partnumber, parts.part_type, instructions.longdescription FROM instructions INNER JOIN  parts  ON ( parts.id = instructions.parts_id ) WHERE instructions.trans_id = '".$data['orderId']."' ";
    $query .= "UNION SELECT  parts.instruction, parts.buchungsgruppen_id, orderitems.id AS item_id, orderitems.parts_id, orderitems.qty, orderitems.description, orderitems.position, orderitems.unit, orderitems.sellprice, orderitems.marge_total, orderitems.discount, orderitems.u_id, orderitems.status, parts.partnumber, parts.part_type, orderitems.longdescription FROM orderitems INNER JOIN parts ON ( parts.id = orderitems.parts_id ) WHERE orderitems.trans_id = '".$data['orderId']."' ORDER BY position ) AS mysubquery ";
    $query .= "JOIN taxzone_charts c ON ( mysubquery.buchungsgruppen_id = c.buchungsgruppen_id )  JOIN taxkeys k ON ( c.income_accno_id = k.chart_id AND k.startdate = ( SELECT max(startdate) FROM taxkeys tk1 WHERE c.income_accno_id = tk1.chart_id AND tk1.startdate::TIMESTAMP <= NOW()  ) ) JOIN tax ON (k.tax_id = tax.id ) WHERE taxzone_id = ".$taxzone_id." GROUP BY item_id, parts_id, position, instruction, qty, description, unit, sellprice, marge_total, discount, u_id, partnumber, part_type, longdescription, status, rate ORDER BY position ASC";

    $positions = $GLOBALS['dbh']->getAll( $query );

        // === QR-Code für Adresse erzeugen ===
    $fullAddress = $orderData['street'] . ', ' . $orderData['zipcode'] . ' ' . $orderData['city'];
    $googleMapsUrl = 'https://www.google.de/maps/place/' . urlencode($fullAddress);
    $qrCodeFile = __DIR__ . '/../tmp/qrcode_' . $data['orderId'] . '.png';
    shell_exec('qrencode -o ' . escapeshellarg($qrCodeFile) . ' -s 5 ' . escapeshellarg($googleMapsUrl));

    //define( 'FPDF_FONTPATH', '../font/');
    define( 'x', 0 );
    define( 'y', 1 );
    $pdf = new FPDF( 'P', 'mm', 'A4' );
    $pdf->AddPage();

    $fontsize = 11;
    $textPosX_right = 120;
    $textPosY = 25;
    $textPosX_left = 12;

    if( $orderData['printed'] ){
        $pdf->SetFont( 'Helvetica', 'B', 10 );
        $pdf->SetTextColor( 255, 0, 0 );
        $pdf->Text( '10','7','Kopie' );
        $pdf->SetTextColor( 0, 0, 0 );
    }
    //writeLog( $orderData );
    $pdf->SetFont( 'Helvetica', 'B', 14 ); //('font_family','font_weight','font_size')
    $pdf->Text( '10','12','Autoprofis Rep.-Auftrag '.' '.$orderData['hersteller'].' '.$orderData['marke'].' '.$orderData['name'] );
    $pdf->Text( '10','18', $orderData['c_ln'] );
    $pdf->SetFont( 'Helvetica', '', 14 );

    //QR-Code einfügen
    //$pdf->SetXY( 10, 20 );
    if(file_exists($qrCodeFile)){
        $pdf->Image($qrCodeFile, 90, 23, 30, 30);
    }

    //fix values
    $pdf->SetFont( 'Helvetica', 'B', $fontsize ) ;
    $pdf->Text( $textPosX_left, $textPosY,'Kunde:' );
    $pdf->Text( $textPosX_left, $textPosY + 5, mb_convert_encoding( 'Straße:', 'ISO-8859-1', 'UTF-8' ) ); // mb_convert_encoding($utf8_string, 'ISO-8859-1', 'UTF-8');
    $pdf->Text( $textPosX_left, $textPosY + 10, 'Ort:' );
    $pdf->Text( $textPosX_left, $textPosY + 15, 'Tele.:' );
    $pdf->Text( $textPosX_left, $textPosY + 20, 'Tele2:' );
    $pdf->Text( $textPosX_left, $textPosY + 25, 'Bearb.:' );

    $pdf->SetFont( 'Helvetica', '', $fontsize );
    $pdf->Text( $textPosX_left + 20, $textPosY, mb_convert_encoding( substr( $orderData['customer_name'], 0, 34 ), 'ISO-8859-1', 'UTF-8' ) );
    $pdf->Text( $textPosX_left + 20, $textPosY + 5, mb_convert_encoding( $orderData['street'], 'ISO-8859-1', 'UTF-8' ) );
    $pdf->Text( $textPosX_left + 20, $textPosY + 10, $orderData['zipcode'].' '.mb_convert_encoding( $orderData['city'], 'ISO-8859-1', 'UTF-8' ) );
    $pdf->Text( $textPosX_left + 20, $textPosY + 15, $orderData['phone'] );
    $pdf->Text( $textPosX_left + 20, $textPosY + 20, $orderData['fax'] );
    $pdf->Text( $textPosX_left + 20, $textPosY + 25, $orderData['employee_name'] );

    $pdf->SetFont( 'Helvetica', 'B', $fontsize );
    $pdf->Text( $textPosX_right, $textPosY, 'KBA:' );
    $pdf->Text( $textPosX_right, $textPosY + 5, 'Baujahr:' );
    $pdf->Text( $textPosX_right, $textPosY + 10,'HU/AU:' );
    $pdf->Text( $textPosX_right, $textPosY + 15, 'FIN:' );
    $pdf->Text( $textPosX_right, $textPosY + 20, 'MK:' );
    $pdf->Text( $textPosX_right, $textPosY + 25, 'KM:' );

    $pdf->SetFont( 'Helvetica', '', $fontsize );
    $pdf->Text( $textPosX_right + 20, $textPosY, $orderData['c_2'].' '.$orderData['c_3'] );
    $pdf->Text( $textPosX_right + 20, $textPosY + 5, db2date( $orderData['c_d'] ) );
    $pdf->Text( $textPosX_right + 20, $textPosY + 10, db2date( $orderData['c_hu'] ) );
    $pdf->Text( $textPosX_right + 20, $textPosY + 15, $orderData['c_fin'] );
    $pdf->Text( $textPosX_right + 20, $textPosY + 20, $orderData['c_mkb'] );
    $pdf->Text( $textPosX_right + 20, $textPosY + 25, $orderData['km_stnd'] );


    $pdf->Text( $textPosX_right, $textPosY + 45, 'Flexgr.:' );
    $pdf->Text( $textPosX_right, $textPosY + 50, 'Color.:' );

    $pdf->Text( $textPosX_right, $textPosY + 35, mb_convert_encoding( 'Lo Sommerräder.:' , 'ISO-8859-1', 'UTF-8' ) );
    $pdf->Text( $textPosX_right, $textPosY + 40, mb_convert_encoding( 'Lo Winterräder.:' , 'ISO-8859-1', 'UTF-8' ) );

    $pdf->Text( $textPosX_left, $textPosY + 35, mb_convert_encoding( 'nächst. ZR-Wechsel KM:', 'ISO-8859-1', 'UTF-8' ) );
    $pdf->Text( $textPosX_left, $textPosY + 40, mb_convert_encoding( 'nächst. ZR-Wechsel:', 'ISO-8859-1', 'UTF-8' ) );
    $pdf->Text( $textPosX_left, $textPosY + 45, mb_convert_encoding( 'nächst. Bremsfl.:', 'ISO-8859-1', 'UTF-8' ) );
    $pdf->Text( $textPosX_left, $textPosY + 50, mb_convert_encoding( 'nächst. WD:', 'ISO-8859-1', 'UTF-8' ) );

    $pdf->SetLineWidth( 0.2 );

    $pdf->SetFont( 'Helvetica', '', $fontsize );

    $pdf->Text( $textPosX_right + 45, $textPosY + 45, mb_convert_encoding( $orderData['flxgr'], 'ISO-8859-1', 'UTF-8' ) );
    $pdf->Text( $textPosX_right + 45, $textPosY + 50, mb_convert_encoding( $orderData['c_color'], 'ISO-8859-1', 'UTF-8' ) );

    //left side under line one
    $lsulo = 50;
    $pdf->Text( $textPosX_left + $lsulo, $textPosY + 35, $orderData['c_zrk'] );
    $pdf->Text( $textPosX_left + $lsulo, $textPosY + 40, mb_convert_encoding( $orderData['c_zrd'], 'ISO-8859-1', 'UTF-8' ) );
    $pdf->Text( $textPosX_left + $lsulo, $textPosY + 45, mb_convert_encoding( $orderData['c_bf'], 'ISO-8859-1', 'UTF-8' ) );
    $pdf->Text( $textPosX_left + $lsulo, $textPosY + 50, mb_convert_encoding( $orderData['c_wd'], 'ISO-8859-1', 'UTF-8' ) );


    $pdf->Text( $textPosX_right + 45, $textPosY + 35, mb_convert_encoding( $orderData['c_st_l'], 'ISO-8859-1', 'UTF-8' ) );
    $pdf->Text( $textPosX_right + 45, $textPosY + 40, mb_convert_encoding( $orderData['c_wt_l'], 'ISO-8859-1', 'UTF-8' ) );

    //Finish Time
    if( strpos( $orderData['finish_time'], 'wartet' ) ) $pdf->SetTextColor( 255, 0, 0 );
    $pdf->SetFont( 'Helvetica', 'B', '12' );
    $finishTimeHeight = 85;
    $pdf->Text( $textPosX_left, $finishTimeHeight, 'Fertigstellung:' );
    $pdf->SetFont( 'Helvetica', 'B', '12' );
    $pdf->Text( $textPosX_right, $finishTimeHeight, mb_convert_encoding( $orderData['finish_time'], 'ISO-8859-1', 'UTF-8' ) );
    $pdf->SetTextColor( 0, 0, 0 );


    $pdf->SetFont( 'Helvetica', '', '10' );
    $pos_todo[x] = 20; $pos_todo[y] = 110;

    //Instructions and positions
    $pdf->SetFont( 'Helvetica', '', '8' );
    $height = '85';

    $pdf->SetLineWidth(0.4);

    //draw first Line x1, y1, x2, y2
    $lineHeight = 54;
    $lineWidth  = 180;
    $pdf->Line( $textPosX_left, $lineHeight, $textPosX_left + $lineWidth , $lineHeight );
    //draw second Line x1, y1, x2, y2
    $lineHeight = 78;
    $pdf->Line( $textPosX_left, $lineHeight, $textPosX_left + $lineWidth , $lineHeight );

    foreach( $positions as $index => $element ){
        //writeLog( $element );
        if( $element['instruction'] ){
            $height = $height + 8;
            //$pdf->SetTextColor( 255, 0, 0 );
            $pdf->SetLineWidth( 0.1 );
            $pdf->Line( 10, $height + 1.6 , 190, $height + 1.6 );
            $pdf->SetFont('Helvetica','B','12');
            //$pdf->SetTextColor( 100, 100, 100 );
            $pdf->Text( '12',$height, mb_convert_encoding( $element['description'], 'ISO-8859-1', 'UTF-8' ) );
            //Longdescriptions
            if( trim( $element['longdescription'] ) != '' ){
                $height = $height + 2;
                $pdf->SetFont( 'Helvetica', '', '10' );
                //$pdf->SetTextColor( 0, 0, 0 );
                //writeLog( strlen( $element['longdescription'] ) );//  intdiv
                $words = explode( ' ', $element['longdescription'] );
                $i = 0;
                $lineArray = array();
                foreach( $words as $value ){
                    if( strlen( $lineArray[$i].$value.' ' ) > 113 ) $i++;
                    $lineArray[$i] .= $value.' ';
                }
                //writeLog( $lineArray );
                foreach( $lineArray as $line ){
                    $height = $height + 4;
                    $pdf->Text( '16', $height, mb_convert_encoding( $line, 'ISO-8859-1', 'UTF-8' ) );
                }
            }
        }
    }

    while( $height < 250 ){
        $height = $height + 10;
        $pdf->Line( 10, $height + 1.6, 190, $height + 1.6 );
    }


    //Footer
    //$pdf->SetTextColor( 0, 0, 0 );
    $pdf->SetFont( 'Helvetica', '', '10' );
    $pdf->Text( '22', '270', 'Datum:' );
    $pdf->Text( '45', '270', date( 'd.m.Y' ) );
    $pdf->Text( '105','270','Kundenunterschrift: __________________' );
   // $pdf->SetTextColor( 255, 0, 0 );
    $pdf->Text( '22', '280', mb_convert_encoding( 'Endkontrolle UND Probefahrt durchgeführt von: __________________', 'ISO-8859-1', 'UTF-8' ) );
    //$pdf->SetTextColor( 0, 0, 0 );
    $pdf->SetFont( 'Helvetica', '', '08' );
    $pdf->Text( '75', '290', 'Powered by lxcars.de - Freie Kfz-Werkstatt Software' );


    //Backside
    //EPSON Printer print must rotate secound page
    $rotate = 0; //Degree --Only for Epson
    $pdf->AddPage( 'P', 'A4', $rotate );
    $pdf->SetFont( 'Helvetica', 'B', '16' );
    $pdf->Text( 10, 20, mb_convert_encoding( 'Verbaute Ersazteile', 'ISO-8859-1', 'UTF-8' ) );
    $height = 30;
    $pdf->SetFont( 'Helvetica', '', '10' );
    $pdf->line( 10, $height - 4.4,  190, $height - 4.4 );
    $totalLines = 22;
    foreach( $positions as $index => $element ){
        //writeLog( $element);
        if( ( $element['part_type']  == 'part' ) && !$element['instruction'] ){
            $totalLines--;
            //writeLog( 'part' );
            $pdf->line( 10, $height + 1.6, 190, $height + 1.6 );
            $pdf->Text( '12', $height, mb_convert_encoding( $element['qty']." ".$element['unit'], 'ISO-8859-1', 'UTF-8' ) );
            $pdf->Text( '26', $height, mb_convert_encoding( $element['description'], 'ISO-8859-1', 'UTF-8' ) );
            $height = $height + 6;
        }
    }
    while( $totalLines-- ){ //Erzeugt Endlosschleife wenn if fehlt
        if( $totalLines <= 0 ) break; //hier müsste dann ein zweites Auftragsblatt erzeugt werden
        //writeLog( $totalLines );
        $pdf->line( 10, $height + 1.6, 190, $height + 1.6 );
        $height = $height + 6;
    }


    $pdf->SetFont( 'Helvetica', 'B', '16' );
    $pdf->Text( 10, $height + 10, mb_convert_encoding( 'Ausgeführte Arbeiten', 'ISO-8859-1', 'UTF-8' ) );
    $height = $height + 20;
    $pdf->SetFont( 'Helvetica', '', '10' );
    $totalLines = 16;
    $pdf->line( 10, $height - 4.4,  190, $height - 4.4 );
    foreach( $positions as $index => $element ){
        if( ( $element['part_type']  == 'service' ) && !$element['instruction'] ){
            $totalLines--;
            $pdf->line( 10, $height + 1.6,  190, $height + 1.6 );
            $pdf->Text( '12', $height, mb_convert_encoding( $element['qty']." ".$element['unit'], 'ISO-8859-1', 'UTF-8' ) );
            $pdf->Text( '26', $height, mb_convert_encoding( $element['description'], 'ISO-8859-1', 'UTF-8' ) );
            $height = $height + 6;
        }
    }
    while( $totalLines-- ){ //Erzeugt Endlosschleife wenn if fehlt
        if( $totalLines <= 0 ) break; //hier müsste dann ein zweites Auftragsblatt erzeugt werden
        //writeLog( $totalLines );
        $pdf->line( 10, $height + 1.6, 190, $height + 1.6 );
        $height = $height + 6;
    }
    $printFileName = 'Auftrag_'.$orderData['ordnumber'].'_'.$orderData['c_ln'].'.pdf';
    $pdf->Text( '10','290', mb_convert_encoding( 'Ich habe sämtliche Ersazteile und ausgeführte Arbeiten i.d. obigen Liste notiert. Unterschrift: __________________', 'ISO-8859-1', 'UTF-8' ) );
    $pdf->OutPut( __DIR__.'/../printedFiles/'.$printFileName, 'F' );

    // QR-Datei wieder löschen
    if(file_exists($qrCodeFile)){
        unlink($qrCodeFile);
    }


    if( $data['print'] == 'printOrder1' ){
        system('lpr -P Canon_LBP663C '.__DIR__.'/../printedFiles/'.$printFileName );
        if( !$orderData['printed'] ) $GLOBALS['dbh']->update( 'oe', array( 'printed' ), array( 'TRUE' ), 'id = '.$data['orderId'] );
    }
    if( $data['print'] == 'printOrder2' ){

        system('lpr -P HP_LASER '.__DIR__.'/../printedFiles/'.$printFileName );
        if( !$orderData['printed'] ) $GLOBALS['dbh']->update( 'oe', array( 'printed' ), array( 'TRUE' ), 'id = '.$data['orderId'] );
    }

    echo json_encode( $printFileName );
}

function getCompanyAdress(){
    echo $GLOBALS['dbh']->getOne( "SELECT company, address_street1, address_zipcode, address_city  FROM defaults", true );
}


function getBilledHours( $u_id, $from, $to ){
    //SELECT SUM( qty ) FROM orderitems WHERE u_id = 'Stefan Baggerprofi' AND itime < NOW() AND itime > NOW() - INTERVAL '2 YEAR' ;
}

function getWorkedHours( $u_id, $from, $to ){
    //SELECT SUM( qty ) FROM instructions WHERE u_id = 'Stefan Baggerprofi' AND itime < NOW() AND itime > NOW() - INTERVAL '2 YEAR' ;
}

//function getCalendarEvents( $data ){
//    $employee = $data['employee'];
//    $start = $data['start'];
//    $end   = $data['end'];
//
//    $query = "( SELECT '0' AS id, 'Alle' AS label, '' AS color, (SELECT json_agg( events ) AS events FROM ( ".
//                "SELECT id, id AS \"groupId\", title, description, dtstart, dtend, duration, freq, interval, count, location, uid, prio, category, visibility, \"allDay\", color, cvp_id, order_id, car_id, cvp_name, cvp_type, ".
//                    "(SELECT row_to_json( rrule ) AS rrule FROM ( ".
//                        "SELECT dtstart, interval, freq, count FROM calendar_events WHERE id = cal.id ".
//                    ") AS rrule ) AS rrule ".
//                "FROM calendar_events cal WHERE dtstart > '2023-11-17' AND CASE WHEN visibility = 0 THEN uid = 861 ELSE TRUE END ".
//            ") AS events) AS events ) ".
//            "UNION ALL ".
//            "( SELECT id, label, color, (SELECT json_agg( events ) AS events FROM ( ".
//                "SELECT id, id AS \"groupId\", title, description, dtstart, dtend, duration, freq, interval, count, location, uid, prio, category, visibility, \"allDay\", color, cvp_id, order_id, car_id, cvp_name, cvp_type, ".
//                    "(SELECT row_to_json( rrule ) AS rrule FROM ( ".
//                        "SELECT dtstart, interval, freq, count FROM calendar_events WHERE id = cal.id ".
//                    ") AS rrule ) AS rrule ".
//                "FROM calendar_events cal WHERE dtstart > '2023-11-17' AND category = event_category.id AND CASE WHEN visibility = 0 THEN uid = 861 ELSE TRUE END ".
//            ") AS events) AS events FROM event_category ORDER BY cat_order )";
//
//    writeLogR( $query );
//
//    $GLOBALS['dbh']->setShowError( true );
//    echo $GLOBALS['dbh']->getAll( $query, true );
//}

function getCalenderCategories(){
    $query = "( SELECT '0' AS id, 'Alle' AS label, '' AS color )".
                " UNION ALL ".
                "( SELECT id, label, color FROM event_category ORDER BY cat_order )";
    echo $GLOBALS['dbh']->getAll( $query, true );
}

function getCalendarEvents( $data ){
    $employee = $data['employee'];
    $start = $data['start'];
    $end   = $data['end'];
    $category = ( 0 != $data['category'] ) ? "category = ".$data['category']." AND " : '';

    $query = "SELECT id, id AS \"groupId\", title, description, dtstart, dtend, duration, freq, interval, count, location, uid, prio, category, visibility, \"allDay\", color, cvp_id, order_id, car_id, cvp_name, cvp_type, ".
                "(SELECT row_to_json( rrule ) AS rrule FROM ( ".
                    "SELECT dtstart, interval, freq, count FROM calendar_events WHERE id = cal.id ".
                ") AS rrule ) AS rrule ".
                "FROM calendar_events cal WHERE ( dtstart >= '$start' AND dtend <= '$end' OR repeat_end >= '$start' ) AND $category CASE WHEN visibility = 0 THEN uid = $employee ELSE TRUE END";

    //writeLogR( $query );

    $GLOBALS['dbh']->setShowError( true );
    echo $GLOBALS['dbh']->getAll( $query, true );
}

function getCarsForCalendar( $data ){ //darf nicht getCars() heißen weil getCars schon kivitendo-crm/lxcars/inc/lxcLib.php on line 129 verwendet wird
    // Sortiert nach Fahrzeugtyp, dann nach Hersteller, dann nach Zulassung:
    //$query .= "SELECT c_id, substring( c_ln || ' | ' || COALESCE( name, hersteller, '' ), 0, 23 ) AS label FROM lxc_cars LEFT JOIN lxckba ON( lxc_cars.kba_id = lxckba.id ) WHERE c_ow = ".$data['id']." ORDER BY name, hersteller, c_ln, c_id DESC";
    // Sortiert nach Zulassung:
    $query .= "SELECT c_id, substring( c_ln || ' | ' || COALESCE( name, hersteller, '' ), 0, 23 ) AS label FROM lxc_cars LEFT JOIN lxckba ON( lxc_cars.kba_id = lxckba.id ) WHERE c_ow = ".$data['id']." ORDER BY c_id DESC";
    echo $GLOBALS['dbh']->getAll( $query, true );
}

function updateCalendarEventFromOrder( $data ){
    //writeLogR( $data );
    $sql = "DELETE FROM calendar_events WHERE order_id = ".$data[0]['record']['calendar_events']['order_id'];
    $GLOBALS['dbh']->query( $sql );
    foreach ($data as $entry) genericSingleInsert( $entry, false, true );
    resultInfo( true );
}

function updateEventCategoriesOrder( $data ){
    //writeLogR( $data );
    $columns = array( 'id' => 'int', 'cat_order' => 'int' );
    echo $GLOBALS['dbh']->updateAll( 'event_category', $columns, $data );
}

function insertNewCalendar( $data ){
    $GLOBALS['dbh']->beginTransaction();
    $sql = "INSERT INTO event_category ( label, color, cat_order ) SELECT '".$data['label']."', '".$data['color']."', MAX( cat_order ) + 1 FROM event_category";
    $GLOBALS['dbh']->query( $sql );
    $query = "SELECT currval AS id FROM currval( 'event_category_id_seq'::regclass )";
    //writeLogR( $sql );
    echo $GLOBALS['dbh']->getOne( $query, true );
    $GLOBALS['dbh']->commit();
}

function deleteCalendar( $data ){
    $GLOBALS['dbh']->beginTransaction();
    $sql = "DELETE FROM event_category WHERE id = ".$data['id'];
    $GLOBALS['dbh']->query( $sql );
    $sql = "DELETE FROM calendar_events WHERE category = ".$data['id'];
    $GLOBALS['dbh']->query( $sql );
    $GLOBALS['dbh']->commit();
    resultInfo( true );
}

/************************************************************************************************************
ToDo:
1. Calendar mit Google Calendar synchronisieren...
https://developers.google.com/google-apps/calendar/recurringevents
**************************************************************************************************************/
function calendar(){ //Ich würde für jeden Task eine eigene Funktion schreiben, dann ist es übersichtlicher
    $task  = varExist( $_GET, 'task' );
    //ToDo Funktion AjaxSql schreiben. Diese werten $_POST oder $_GET aus, erster Parameter ist Tabelle, zweiter P ist task (insert, select, update) folgende sind die serialisierten Daten
    if( !varExist( $task ) ) $task = 'getEvents';
    $startGet       = $_GET['start'];
    $endGet         = $_GET['end'];
    $repeat_end_GET= varExist( $_GET, 'repeat_end' ) == 'Invalid date' ? 'NULL' : $_GET['repeat_end'];
    $where          = $_GET['where'];
    $myuid          = $_GET['myuid'];

    foreach( $_POST as $key => $value ){
        $$key = htmlspecialchars($value);
    }
    $repeat_end_sql = varExist( $repeat_end ) == 'Invalid date' ? 'NULL' : "'$repeat_end'::TIMESTAMP";
    switch( $task ){
        case "getUserdata":
            //$sql = "SELECT BLA";
            //$rs = $GLOBALS['dbh']->getOne( $sql );
            //echo $rs['json_agg'];
            //Kann gleich aus der SESSION genommen werden
        break;
        case "newEvent":
            $sql = "INSERT INTO events ( duration, title, description, \"allDay\", uid, visibility, category, prio, job, color, done, location, cust_vend_pers, repeat, repeat_factor, repeat_quantity, repeat_end ) VALUES ( '[$start, $end)','$title','$description', $allDay, $uid, $visibility, $category, $prio, '$job', '$color', '$done', '$location', '$cust_vend_pers', '$repeat', '$repeat_factor', '$repeat_quantity', $repeat_end_sql )";
            $rc = $GLOBALS['dbh']->myquery( $sql );
        break;
        case "updateEvent":
            $sql = "UPDATE events SET title = '$title', duration = '[$start, $end)', description = '$description', \"allDay\" = $allDay, uid = '$uid', visibility = '$visibility', category = '$category', prio = '$prio', job = '$job', color = '$color', done = '$done', location = '$location', cust_vend_pers = '$cust_vend_pers', repeat = '$repeat', repeat_factor = '$repeat_factor', repeat_quantity = '$repeat_quantity', repeat_end = $repeat_end_sql  WHERE id = $id";
            $rc = $GLOBALS['dbh']->myquery( $sql );
        break;
        case "updateTimestamp":
            $sql = "UPDATE events SET  duration = '[$start, $end)', \"allDay\" = $allDay WHERE id = $id";
            $rc = $GLOBALS['dbh']->myquery( $sql );
        break;
        case "deleteEvent":
            $sql = "DELETE FROM events WHERE id = $id";
            $rc = $GLOBALS['dbh']->myquery( $sql );
        break;
        case "getEvents":
            $sql = "SELECT json_agg(json_event) FROM ( select *, lower( tsrange ) AS start, upper( tsrange ) AS end  FROM ( select id, title, repeat, repeat_factor, repeat_quantity, repeat_end, description, location, uid, visibility,  prio, category, \"allDay\", color, job, done, job_planned_end, cust_vend_pers, row_number - 1 AS repeat_num, tsrange(lower(duration) + (row_number - 1)::INT * (repeat_factor||repeat)::interval, upper(duration) + (row_number - 1)::INT * (repeat_factor||repeat)::interval) from (select t.*, row_number() over (partition by id) from events t cross join lateral (select generate_series(0, t.repeat_quantity ) i) x) foo) alle_termine where '[$startGet, $endGet)'::tsrange && tsrange AND $where CASE WHEN visibility = 0 THEN uid = $myuid ELSE TRUE END ) json_event";
            //echo $sql;
            // " // ToDo: Visibility für Gruppen implementieren.
            $rs = $GLOBALS['dbh']->getOne( $sql );
            echo $rs['json_agg'];
        break;
        case "getUsers":
            $sql = "SELECT json_agg( json_employee ) FROM (SELECT id AS value, name AS text FROM employee WHERE deleted = FALSE ) json_employee"; //login
            $rs = $GLOBALS['dbh']->getOne( $sql );
            echo $rs['json_agg'];
        break;
        case "getCategory":
            $sql = "SELECT json_agg( json_category ) FROM (SELECT id AS value, label AS text FROM event_category ORDER BY cat_order ) json_category";
            $rs = $GLOBALS['dbh']->getOne( $sql );
            echo $rs['json_agg'];
        break;
     }
     /*
     ToDo: getUsers, getCategory und getEvents in einem JSON zusammenfassen:
     Bsp.:
        DROP TABLE IF EXISTS users;
            CREATE TABLE users (
            id     serial,
            login     text,
            name     text
        );
        INSERT INTO users ( login, name ) VALUES ( 'emmy', 'Emmy Noether' );
        INSERT INTO users ( login, name ) VALUES ( 'ada', 'Augusta Ada Byron King' );
        INSERT INTO users ( login, name ) VALUES ( 'eu', 'Euklid von Alexandria' );
        DROP TABLE IF EXISTS groups;
        CREATE TABLE groups (
           id     serial,
           name     text
        );
        INSERT INTO groups ( name ) VALUES ( 'Mathematiker' );
        INSERT INTO groups ( name ) VALUES ( 'Informatiker' );

        select json_agg (my_json) from (select row_to_json(x0) as my_json from (SELECT json_agg( i0 ) as groups FROM ( SELECT * FROM groups ) i0) x0 union all select row_to_json(x1) from (SELECT json_agg( i1 ) as users FROM ( SELECT * FROM users ) i1) x1) bla;
    */
}

function deleteOrderOffer( $data ){ //Auftrag bzw. Angebot löschen, inkl. aller Positionen, wenn SUBVER == lxcars dann auch alle Instructions
    $GLOBALS['dbh']->beginTransaction();
    $GLOBALS['dbh']->query( 'DELETE FROM oe WHERE id = '.$data['orderID'] );
    $GLOBALS['dbh']->query( 'DELETE FROM orderitems WHERE trans_id = '.$data['orderID'] );
    if( SUBVER == 'lxcars' ) $GLOBALS['dbh']->query( 'DELETE FROM instructions WHERE trans_id = '.$data['orderID'] );
    echo $GLOBALS['dbh']->commit();
}

function testFunction(){
    $trans = array( ' ' => '_', '\\'=> '_', '/' => '_' , ':' => '_',  '*' => '_',  '?' => '_',  '"' => '_', '<' => '_',  '>' => '_',  '|' => '_', ',' => '');
    $mandant = strtr( $_SESSION['mandant'], $trans );
    $dbname = $_SESSION['dbData']['dbname'];
    $dir_man = $_SESSION['crmpath']."/dokumente/".$mandant.'/';
    $dir_db = $_SESSION['crmpath']."/dokumente/".$dbname.'/';
    $sql = "SELECT name FROM customer WHERE name ILIKE '%ronny%'";
    $rs = $GLOBALS['dbh']->getAll( $sql, TRUE );
    writeLogR( $dir_man );
    writeLogR( $dir_db );
    echo $rs;
}

function getAagToken( $debug = FALSE ){
    $aagLogin = $GLOBALS['dbh']->getKeyValueData( 'crmdefaults', array( 'aag-online_user', 'aag-online_passwd' ), 'employee = -1', FALSE );
    if( $debug ) writeLogR( $aagLogin );
    // Die URL für den Login-Request.
    $loginUrl = 'https://tm-next.dvse.de/data/TM.Next.Authority/external/login/GetAuthToken';

    // Die Login-Daten.
    $loginData = [
        'authId' => 'ti6x', //Authentifizierungs-ID (wird von der DVSE bereitgestellt) ti6x == AAG-Online
        'username' => $aagLogin['aag-online_user'],//'108799', // Benutzername
        'password' => $aagLogin['aag-online_passwd']//'cuye79', // Passwort
    ];

    // Initialisiert eine cURL-Session
    $curl = curl_init( $loginUrl );

    // Setzt die Optionen für die cURL-Session
    curl_setopt( $curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $curl, CURLOPT_POST, true );
    curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $loginData ) );

    // Führt die cURL-Session aus und speichert die Antwort
    $response = curl_exec( $curl );

    // Überprüft auf cURL-Fehler
    if( curl_errno( $curl ) ){
        if( $debug ) writeLogR( 'Curl error: ' . curl_error( $curl ) );
    }
    else {
        // Verarbeitet die Antwort
        $decodedResponse = json_decode( $response, true );
        if( isset( $decodedResponse['token'] ) ){
            if( $debug ) writeLogR( 'Token: ' . $decodedResponse['token'] );
            if( $debug ) writeLogR( 'Schema: ' . $decodedResponse['schema'] );
        }
        else{
            if( $debug ) writeLogR( 'Token konnte nicht abgerufen werden. Antwort: ' . $response );
        }
    }
    return $decodedResponse['token'];
    // Schließt die cURL-Session
    curl_close( $curl );
}



//Erzeugt eine URL zum starten von AAG-Online, hierzu werden customer_id und car_id benötigt
function getAagUrl( $data ){
    //writeLog( $data );
    /***** Begin Data **************************************************************************
    $data = [
        "workTaskId" => "string", // Optional, um den Vorgang direkt anzusprechen (nicht empfohlen)
        "referenceId" => "string", // Eindeutiger Identifier des Beleges aus dem DMS System
        "voucherId" => "A_123", // DMS Belegenummer
        "voucherType" => [ // DMS Belegart z.B. Auftrag, Rechnung, Angebot, etc.
            "referenceId" => "string", // Eindeutiger Identifier der Belegart aus dem DMS System
            "description" => "string" // Belegartbezeichnung
        ],
        "invoiced" => true, // Gibt beim Import an, ob der Beleg bereits fakturiert wurde und somit nicht mehr bearbeitet werden darf. Ist dies der Fall, wird der Beleg als "neu" importiert und sowohl die "referenceId" als auch die "voucherId" werden nicht übernommen.
        "customer" => [
            "referenceId" => "string", // Eindeutiger Identifier des Kunden aus dem DMS System
            "customerId" => "string", // Kundennummer (des Werkstattkunden)
            "title" => 0, // Enumeration Undefiniert = 0, Herr = 1, Frau = 2, Firma = 3
            "academicTitle" => "string", // Akademischer Titel z.B. Dr. oder Prof.
            "firstName" => "string", // Vorname des Kunden
            "lastName" => "string", // Nachname des Kunden
            "companyName" => "string", // Firmenname
            "generalAddress" => [ // Beleg-Adresse
                "referenceId" => "string", // Eindeutiger Identifier der Adresse aus dem DMS System
                "description" => "string", // Adressbezeichnung
                "street" => "string", // Straße
                "addressAddition" => "string", // Adresszusatz
                "city" => "string", // Stadt
                "zip" => "string", // Postleitzahl
                "state" => "string", // Landkreis
                "country" => "string" // Land
            ],
            "phone" => "string", // Telefon
            "mobile" => "string", // Telefon
            "fax" => "string", // Fax
            "email" => "string", // Email
            "birthDate" => "2018-04-18T11:34:10.382Z", // Geburtstag (UTC Zeit)
            "taxId" => "string", // Steuernummer
            "matchcode" => "string", // Kundenkürzel
            "memos" => [[ // Liste von Freitextfeldern
                "description" => "string", // Bezeichnung des Freitextfeldes
                "value" => "string", // Wert des Freitextfeldes
                "type" => "string", // Typen. Enumeration: Text = 0, Xml = 1
                "isVisible" => true // Soll der Value im Teilekatalog angezeigt werden.
            ]],
            "creationDate" => "2018-04-18T11:34:10.382Z", // Datensatz erstellt am (UTC Zeit)
            "modifiedDate" => "2018-04-18T11:34:10.382Z" // Datensatz zuletzt geändert am (UTC Zeit)
        ],
        "vehicle" => [
            "referenceId" => "string", // Eindeutiger Identifier des Fahrzeugs aus dem DMS System
            "manufacturer" => "string", // Fahrzeughersteller (z.B. OPEL)
            "model" => "string", // Fahrzeugmodell (z.B. 1.4 i (C08, C48, D08, D48))
            "type" => "string", // Fahrzeugtyp (z.B. KADETT E CC (T85))
            "vehicleType" => [
                "id" => 0, // (TecDoc) Typnummer
                "type" => 0, // Enumeration PKW = 1, NKW = 2, Motorrad = 3
                "clientId" => 0, // Mandantennummer
                "description" => "string" // Fahrzeugbezeichnung (z.B. "Audi A4 1.9 TDI")
            ],
            "registrationInformation" => [ // Registratur-Information
                "plateId" => "string", // Kennzeichen
                "countryCode" => "string", // Länderkennzeichen (ISO 3166-1 Alpha-2)
                "registrationNo" => "string", // z.B. KBA oder Schweizer Typenscheinnummer
                "registrationDate" => "2018-04-18T11:34:10.382Z", // Erstzulassung (UTC Zeit)
                "registrationTypeId" => 0 // Art der Registrierungsnummer. Enumeration KBA = 0
            ],
            "vin" => "string", // Fahrgestellnummer (FIN)
            "mileage" => 0, // Tachometerstand
            "mileageType" => 0, // Enumeration Kilometer = 1, Meilen = 2
            "engineCode" => "string", // Motorcode
            "memos" => [[ // Liste von Freitextfeldern
                "description" => "string", // Bezeichnung des Freitextfeldes
                "value" => "string", // Wert des Freitextfeldes
                "type" => "string", // Typen. Enumeration: Text = 0, Xml = 1
                "isVisible" => true // Soll der Value im Teilekatalog angezeigt werden.
            ]],
            "nextGeneralInspection" => "2018-04-18T11:34:10.382Z", // Hauptuntersuchung (UTC Zeit)
            "nextServiceDate" => "2018-04-18T11:34:10.382Z", // Nächster Service (UTC Zeit)
            "lastWorkshopAppointment" => "2018-04-18T11:34:10.382Z", // Letzter Werkstattbesuch (UTC Zeit)
            "creationDate" => "2018-04-18T11:34:10.382Z", // Datensatz erstellt am (UTC Zeit)
            "modifiedDate" => "2018-04-18T11:34:10.382Z" // Datensatz zuletzt geändert am (UTC Zeit)
        ],
        "clientAdvisor" => [ // Kundenberater
            "referenceId" => "string", // Eindeutiger Identifier des Kundenberaters aus dem DMS System
            "name" => "string", // Vor- und Zuname
            "employeeNo" => "string" // Mitarbeiter Id
        ],
        "mechanic" => [ // Mechaniker
            "referenceId" => "string", // Eindeutiger Identifier des Mechanikers aus dem DMS System
            "name" => "string", // Vor- und Zuname
            "employeeNo" => "string" // Mitarbeiter Id
        ],
        "parts" => [[
            "id" => "string", // Interne Katalog Id
            "referenceId" => "string", // Eindeutiger Identifier des Ersatzteils aus dem DMS System
            "replacedReferenceId" => "string", // Referenz Id des im Beleg ersetzten Artikels
            "replacedPartInfo" => "string", // Artikelbeschreibung des ersetzten Artikels
            "wholesalerArticleId" => "string", // Händlerartikelnummer
            "dataSupplierArticleId" => "string", // Herstellerartikelnummer
            "dataSupplier" => [
                "id" => 0, // Hersteller Id
                "clientId" => 0, // Mandantennummer
                "description" => "string" // Fahrzeugart Bezeichnung
            ],
            "oeArticleId" => [
                "id" => "string", // Artikelnummer OE-Artikelnummer
                "manufacturer" => "string", // OE-Hersteller
                "manufacturerId" => "string" // OE-HerstellerId
            ],
            "additionalArticleIds" => [[ // Liste zusätzlicher Artikelnummern
                "id" => "string", // Artikelnummer
                "referenceId" => "string", // Wenn der ControllIndicator "AdditionalTradeReference = 1" gesetzt ist, wird hier die zugehörige "TradeReferenceId" eingetragen
                "type" => 0, // Typ der Artikelnummer: z.B. 1 = OE, 2 = EAN, 3 = DMS, WholesalerArticleNo = 4
                "description" => "string" // Artikelnummernbezeichnung
            ]],
            "vehicleType" => [
                "id" => 0, // TecDoc Typnummer
                "type" => 0, // Enumeration PKW = 1, NKW = 2, Motorrad = 3
                "clientId" => 0, // Mandantennummer
                "description" => "string" // Fahrzeugart Bezeichnung
            ],
            "description" => "string", // Artikelbeschreibung
            "additionalDescription" => "string", // Artikelzusatzbeschreibung
            "productGroups" => [[ // Liste der Produktgruppen
                "id" => 0, // Produktgruppen Id
                "clientId" => 0, // Mandantennummer
                "description" => "string" // Produktgruppen Bezeichnung
            ]],
            "prices" => [[ // Liste der Preise wird beim Import nur für benutzerdefinierte Teile verwendet und beim Export für alle Teile
                "value" => 0, // Wert (z.B. "10.00")
                "currencyCode" => "string", // Währungskennzeichen ISO 4217
                "currencySymbol" => "string", // Währungssymbol
                "rebateValue" => 0, // Rabattierter Preis "8.00"
                "rebate" => 0, // Rabatt in Prozent "20"
                "type" => 0, // Preisart. Enumeration: EK = 4, VK = 5 vom Großhändler. Für benutzerdefinierte Teile VK = 5 Weitere Preistypen im Abschnitt Datentypen
                "description" => "string", // Beschreibungstext des Preises
                "unit" => "string", // Preiseinheit
                "vat" => 0, // Mehrwertsteuer
                "isTaxIncluded" => true // Mehrwertsteuer im Preis enthalten
            ]],
            "quantity" => 0, // Menge
            "unit" => "string", // Mengeneinheit
            "isInOrder" => true, // Wird im Warenkorb des Teilekataloges, im Sinne von "Bei der nächsten Bestellung berücksichtigen", markiert.
            "isChangeable" => true, // Darf durch das Katalogsystem und / oder den Benutzer manipuliert werden
            "isChanged" => true, // Wurde durch das Katalogsystem und / oder den Benutzer verändert
            "isInCostEstimation" => true, // Der Artikel ist im KVA als druckbar markiert.
            "isReplacementPart" => true, // Der Artikel ist ein Austauschteil und somit altteilesteuerpflichtig.
            "orderInformation" => [
                "state" => 0, // Bestellstatus. Enumeration: 0 = nicht bestellt, 1 = bestellt
                "orderId" => "string", // Bestellnummer
                "timestamp" => "2018-04-18T11:34:10.382Z", // Bestellzeitpunkt (UTC Zeit)
                "memos" => [[ // Liste von Freitextfeldern
                    "description" => "string", // Bezeichnung des Freitextfeldes
                    "value" => "string", // Wert des Freitextfeldes
                    "type" => "string", // Typen. Enumeration: Text = 0, Xml = 1
                    "isVisible" => true // Soll der Value im Teilekatalog angezeigt werden.
                ]]
            ],
            "memos" => [[ // Liste von Freitextfeldern
                "description" => "string", // Bezeichnung des Freitextfeldes
                "value" => "string", // Wert des Freitextfeldes
                "type" => "string", // Typen. Enumeration: Text = 0, Xml = 1
                "isVisible" => true // Soll der Value im Teilekatalog angezeigt werden.
            ]]
        ]],
        "repairTimes" => [[
            "id" => "string", // Interne Katalog Id
            "referenceId" => "string", // Eindeutiger Identifier aus dem DMS System
            "vehicleType" => [ // Fahrzeug
                "id" => 0, // TecDoc Typnummer
                "type" => 0, // Enumeration PKW = 1, NKW = 2, Motorrad = 3
                "clientId" => 0, // Mandantennummer
                "description" => "string" // Fahrzeugart Bezeichnung
            ],
            "provider" => 0, // Enumeration für den Arbeitswerteprovider, AwDoc = 1, Haynes = 2, Autodata = 3, TecRMI = 4, Eurotax = 5, DAT = 6, benutzerdefiniert = 7
            "repairTimeId" => "string", // Arbeitswert Nummer
            "referencedRepairTimeId" => "string", // Referenzierte Arbeitswert Nummer: z.B. bei Umfasst-Arbeiten der Arbeitswert, der die Arbeit enthält
            "description" => "string", // Arbeitswert Beschreibung
            "typeOfWork" => 0, // Art der Arbeit, Enumeration NotCategorized = 0, Werkstattarbeiten = 1, Karosseriearbeiten = 2, Zubehörarbeiten = 3, Lackierarbeiten = 4, Elektrikarbeiten = 5, Elektronikarbeiten = 6, Sattlerarbeiten = 7, SmartRepair = 8
            "typeOfWorkShortCode" => "string", // Kurzbezeichnung Art der Arbeit: WS – Werkstattarbeiten, KA - Karosseriearbeiten, ZB – Zubehörarbeiten, LA – Lackierarbeiten, EL – Elektrikarbeiten, EK – Elektronikarbeiten, SA – Sattlerarbeiten, SR - SmartRepair
            "type" => 0, // Enumeration Hauptarbeit = 1, Umfasst Arbeit = 2, Folgearbeit = 3, Vorarbeit = 4
            "calculation" => [
                "hourlyRate" => 0, // Stundensatz
                "priceValue" => 0, // Kalkulierter Preis (z.B. hourlyRate * calculatedTimeValue oder Festpreis)
                "priceType" => "string", // Preisart
                "priceDescription" => "string", // Beschreibungstext des Preises
                "currencyCode" => "string", // Währungskennzeichen ISO 4217
                "currencySymbol" => "string", // Währungssymbol
                "rebateValue" => 0, // Rabattierter Wert
                "rebate" => 0, // Rabatt in Prozent
                "vat" => 0, // Mehrwertsteuer
                "isTaxIncluded" => true, // Mehrwertsteuer im Preis enthalten
                "isFixedPrice" => true, // Ist ein Festpreis
                "timeValue" => 0, // Arbeitswert (nicht kalkuliert)
                "calculatedTimeValue" => 0, // Kalkulierter Arbeitswert
                "division" => 0 // Bestimmt "Taktung" des Arbeitswerts
            ],
            "isChangeable" => true, // Darf durch das Katalogsystem und / oder den Benutzer manipuliert werden
            "isChanged" => true, // Wurde durch das Katalogsystem und / oder den Benutzer verändert
            "isExpanded" => true, // Die Umfasstarbeiten dieser Hauptarbeit sind im Beleg sichtbar
            "isInCostEstimation" => true, // Der Arbeitswert ist im KVA als druckbar markiert
            "isMaintenanceWork" => true, // Das ist ein Wartungsplan-Arbeitswert
            "memos" => [[ // Liste von Freitextfeldern
                "description" => "string", // Bezeichnung des Freitextfeldes
                "value" => "string", // Wert des Freitextfeldes
                "type" => "string", // Typen. Enumeration: Text = 0, Xml = 1
                "isVisible" => true // Soll der Value im Teilekatalog angezeigt werden.
            ]]
        ]],
        "memos" => [[ // Liste von Freitextfeldern
            "description" => "string", // Bezeichnung des Freitextfeldes
            "value" => "string", // Wert des Freitextfeldes
            "type" => "string", // Typen. Enumeration: Text = 0, Xml = 1
            "isVisible" => true // Soll der Value im Teilekatalog angezeigt werden.
        ]],
        "creationDate" => "2018-04-18T11:34:10.382Z", // Datensatz erstellt am (UTC Zeit)
        "modifiedDate" => "2018-04-18T11:34:10.382Z", // Datensatz zuletzt geändert am (UTC Zeit)
        "controlIndicators" => [[
            "type" => 0, // Enumeration "ImportControlIndicators"
            "parameters" => [[ // Zusätzliche Parameter
                "id" => "string", // Schlüssel
                "value" => "string" // Schlüsselwert
            ]]
        ]]
    ];
    ***** End Example Data **************************************************************************/

    $sql = "
        SELECT
            oe.id AS oe_id,                                      -- ID der Bestellung (oe) mit Alias oe_id
            oe.ordnumber,                                        -- Bestellnummer
            oe.km_stnd,                                          -- Kilometerstand
            customer.id AS customer_id,                          -- Kunden-ID mit Alias customer_id
            customer.customernumber,                             -- Kundennummer
            CASE
                WHEN greeting LIKE '%Herr/Frau%' THEN 0          -- Wenn Anrede 'Herr/Frau' enthält, dann 0 (divers)
                WHEN greeting LIKE '%Herr%'      THEN 1          -- Wenn Anrede 'Herr' enthält, dann 1
                WHEN greeting LIKE '%Frau%'      THEN 2          -- Wenn Anrede 'Frau' enthält, dann 2
                ELSE 3                                           -- Andernfalls 3
            END AS greeting,                                     -- Anrede-Wert mit Alias greeting
            customer.name,                                       -- Kundenname
            CASE
                WHEN greeting IN ('Frau', 'Herr', 'Herr/Frau' ) THEN  -- Wenn Anrede 'Frau' oder 'Herr' oder 'Herr/Frau' ist
                    split_part(name, ' ', array_length(string_to_array(name, ' '), 1))  -- Nachname (letzter Teil des Namens)
                ELSE ''                                          -- Andernfalls leerer String
            END AS last_name,                                    -- Nachname mit Alias last_name
            CASE
                WHEN greeting IN ('Frau', 'Herr') THEN           -- Wenn Anrede 'Frau' oder 'Herr' ist
                    array_to_string(array_remove(string_to_array(name, ' '), split_part(name, ' ', array_length(string_to_array(name, ' '), 1))), ' ')  -- Vorname (alle Teile außer dem letzten)
                ELSE ''                                          -- Andernfalls leerer String
            END AS first_name,                                   -- Vorname mit Alias first_name
            CASE
                WHEN greeting NOT IN ('Frau', 'Herr') THEN       -- Wenn Anrede weder 'Frau' noch 'Herr' ist
                    name                                         -- Wert von name als companyname ausgeben
                ELSE ''                                          -- Andernfalls leerer String
            END AS company_name,
            customer.street,                                     -- Straße
            customer.zipcode,                                    -- Postleitzahl
            customer.city,                                       -- Stadt
            customer.country,                                    -- Land
            customer.phone,                                      -- Telefonnummer 1
            customer.fax,                                        -- Telefonnummer 2
            customer.phone3,                                     -- Telefonnummer 3
            customer.email,                                      -- Email
            customer.notes,                                      -- Bemerkungen
            to_char( customer.itime, 'YYYY-MM-DD\"T\"HH24:MI:SS.MS\"Z\"' ) AS customer_itime,  -- Initzeit Zeitstempel im ISO 8601-Format
            to_char( customer.mtime, 'YYYY-MM-DD\"T\"HH24:MI:SS.MS\"Z\"' ) AS customer_mtime,  -- Modifikationszeit Zeitstempel im ISO 8601-Format
            lxc_cars.c_ln,                                       -- Autokennzeichen
            to_char( lxc_cars.c_d, 'YYYY-MM-DD\"T\"HH24:MI:SS.MS\"Z\"' ) AS registation_date,  -- Erstzulassung
            lxc_cars.c_fin,                                      -- Fahrgestellnummer
            lxc_cars.c_mkb,                                      -- Motorcode
            lxc_cars.c_text,                                     -- Bemerkungen zum Fahrzeug
            lxc_cars.c_id,                                       -- ID des Fahrzeugs
            to_char( lxc_cars.c_it, 'YYYY-MM-DD\"T\"HH24:MI:SS.MS\"Z\"' ) AS car_itime,        -- Initzeit Zeitstempel im ISO 8601-Format
            CONCAT(lxc_cars.c_2, lxc_cars.c_3) AS kba            -- KBA-Nummer, zusammengesetzt aus c_2 und c_3
        FROM
            oe                                                   -- Tabelle oe
        JOIN
            customer ON oe.customer_id = customer.id             -- Join mit Tabelle customer über customer_id
        JOIN
            lxc_cars ON oe.c_id = lxc_cars.c_id                  -- Join mit Tabelle lxc_cars über c_id
        WHERE
            oe.id = ".$data['oe-id'];                            //-- Bedingung: Auftrags-ID entspricht der übergebenen ID


    $oe_customer_car = $GLOBALS['dbh']->getOne( $sql, FALSE );
    //writeLog( $oe_customer_car );

    $data = [
        "referenceId" => $oe_customer_car['ordnumber'], // Eindeutiger Identifier des Beleges aus dem DMS System
        "voucherId" => (string)$oe_customer_car['oe_id'],
        "voucherType" => [
            "referenceId" => "2",
            "description" => $oe_customer_car['name'],
            "countryCode" => "DE"
        ],
        //"invoiced" => false, // Gibt beim Import an, ob der Beleg bereits fakturiert wurde und somit nicht mehr bearbeitet werden darf. Ist dies der Fall, wird der Beleg als "neu" importiert und sowohl die "referenceId" als auch die "voucherId" werden nicht übernommen.
        "customer" => [
            "referenceId" => (string)$oe_customer_car['customer_id'], // Eindeutiger Identifier des Kunden aus dem DMS System
            "customerId" => $oe_customer_car['customernumber'], // Kundennummer (des Werkstattkunden) customernumber
            "title" => $oe_customer_car['greeting'], // Enumeration Undefiniert = 0, Herr = 1, Frau = 2, Firma = 3
            "firstName" => $oe_customer_car['first_name'], // Vorname des Kunden
            "lastName" => $oe_customer_car['last_name'], // Nachname des Kunden
            "companyName" => $oe_customer_car['company_name'], // Firmenname
            "generalAddress" => [ // Beleg-Adresse
                "description" => "Anschrift", // Adressbezeichnung
                "street" => $oe_customer_car['street'], // Straße
                "city" => $oe_customer_car['city'], // Stadt
                "zip" => $oe_customer_car['zipcode'], // Postleitzahl
                //"state" => "string", // Landkreis
                "country" => $oe_customer_car['country'] // Land
            ],
            "phone" => $oe_customer_car['phone'], // Telefon 1
            "mobile" => $oe_customer_car['fax'],  // Telefon 2
            "fax" => $oe_customer_car['phone3'],  // Telefon 3
            "email" => $oe_customer_car['email'], // Email
            "memos" => [[ // Liste von Freitextfeldern
                "description" => "Bemerkungen", // Bezeichnung des Freitextfeldes
                "value" => $oe_customer_car['notes'], // Wert des Freitextfeldes
                "type" => "0", // Typen. Enumeration: Text = 0, Xml = 1
                "isVisible" => true // Soll der Value im Teilekatalog angezeigt werden.
            ]],
            "creationDate" => $oe_customer_car['customer_itime'], // Datensatz erstellt am (UTC Zeit)
            "modifiedDate" => $oe_customer_car['customer_mtime'] // Datensatz zuletzt geändert am (UTC Zeit)
        ],
        "vehicle" => [
            "referenceId" => (string)$oe_customer_car['c_id'],
            //"vehicleType" => [
            //    "id" => 0, // (TecDoc) Typnummer
            //    "type" => 0, // Enumeration PKW = 1, NKW = 2, Motorrad = 3
            //    "clientId" => 0, // Mandantennummer
            //    "description" => "XXX" // Fahrzeugbezeichnung (z.B. "Audi A4 1.9 TDI")
            //],
            "registrationInformation" => [
                "plateId" => $oe_customer_car['c_ln'],
                "registrationNo" => $oe_customer_car['kba'], // KBANR wird nur benutzt wenn keine KTYPNR übergeben wird
                "registrationDate" => $oe_customer_car['registation_date'],
                "registrationTypeId" => "0" // Art der Registrierungsnummer. Enumeration KBA = 0
            ],
            "vin" => $oe_customer_car['c_fin'], // Fahrgestellnummer (FIN)
            "mileage" => (string)$oe_customer_car['km_stnd'], // Tachometerstand
            "mileageType" => "1", // Enumeration Kilometer = 1, Meilen = 2
            "engineCode" => $oe_customer_car['c_mkb'], // Motorcode
            "memos" => [[ // Liste von Freitextfeldern
                "description" => "Bemerkungen zum Fahrzeug", // Bezeichnung des Freitextfeldes
                "value" => $oe_customer_car['c_text'], // Wert des Freitextfeldes
                "type" => "0", // Typen. Enumeration: Text = 0, Xml = 1
                "isVisible" => true // Soll der Value im Teilekatalog angezeigt werden.
            ]],

            //"nextGeneralInspection" => "2018-04-18T11:34:10.382Z", // Hauptuntersuchung (UTC Zeit)
            //"nextServiceDate" => "2018-04-18T11:34:10.382Z", // Nächster Service (UTC Zeit)
            //"lastWorkshopAppointment" => "2018-04-18T11:34:10.382Z", // Letzter Werkstattbesuch (UTC Zeit)
            "creationDate" => $oe_customer_car['car_itime'], // Datensatz erstellt am (UTC Zeit)
            //"modifiedDate" => $oe_customer_car['car_mtime'] // Datensatz zuletzt geändert am (UTC Zeit) ToDo: muss noch in LxCars repariert werden!!
            ]
    ];

    /*
    $data = [
        "referenceId" => "A_123",
        "voucherId" => "23456",
        "voucherType" => [
            "referenceId" => "2",
            "description" => "Angebot"
        ],
        "vehicle" => [
            "referenceId" => "v_1",
            "vehicleType" => [
                "id" => 111074, // KTYPNR wenn vorhanden
                "type" => 1 // Fahrzeugtyp PKW
            ],
            "registrationInformation" => [
                "plateId" => "TE ST 2018",
                "countryCode" => "DE",
                "registrationNo" => "0005CKF00158", // KBANR wird nur benutzt wenn keine KTYPNR übergeben wird
                "registrationDate" => "2018-03-27T22:00:00Z"
            ]
        ]
    ];
    */
   
    $data = [
        "referenceId" => $oe_customer_car['ordnumber'],
        "voucherType" => [
            "referenceId" => "2",
            "description" => "Angebot"
        ],
        "vehicle" => [
            "referenceId" => "v_1",
            "vehicleType" => [
               // "id" => 111074, // KTYPNR wenn vorhanden
                "type" => 1 // Fahrzeugtyp PKW
            ],
            "registrationInformation" => [
                "plateId" => $oe_customer_car['c_ln'],
                "countryCode" => "DE",
                "registrationNo" => $oe_customer_car['kba'], // KBANR wird nur benutzt wenn keine KTYPNR übergeben wird
                //"registrationDate" => $oe_customer_car['registation_date']
                "registrationDate" => "2018-03-27T22:00:00Z"
            ]
        ]
    ];
 


    $tries = 8;
    while( $tries-- ){
        //writeLog( json_encode($data) );
        if( !isset( $_SESSION['aagToken'] ) ) $_SESSION['aagToken'] =  getAagToken( $debug = FALSE );

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://tm-next.dvse.de/data/TM.Next.Dms/api/portal/service/v1/Gsi/ImportVoucher",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode( $data ),
            CURLOPT_HTTPHEADER => [
                "Accept-Language: de",
                "Content-Type: application/json",
                "Authorization: Bearer ".$_SESSION['aagToken']
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if( $err ){
            unset( $_SESSION['aagToken'] );
            //writeLog( "cURL Error #:" . $err );
        }
        else {
            echo $response;
            break;
        }
    }//end while
}

function writeLogFromJs( $data ){
    writeLog( $data['info'] );
    if( $data['reverse']) writeLogR( $data['message'] );
    writeLog( $data['message'] );
    resultInfo( true ); 
}
/***********************************
 *  [name] => Ronny Zimmermann
    [c_ln] => MOL-ID100
    [dim] => 322/85R12 75S
    [location] => A9
 */

function printTyreLabel( $data ){
    // Zielverzeichnis festlegen
    $dir = __DIR__.'/../tmp/labels';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $loc = ['VR', 'VL', 'HR', 'HL'];
    foreach ($loc as $wheel){ 
        $ezpl = 
            '^Q150,3
^W100
^H8
^P1
^S3
^AD
^C1
^R60
~Q+60
^O0
^D3 
^E28
~R200
^XSET,ROTATION,0
^C1
^D0
^D1
^L
Dy2-me-dd
Th:m:s
AD,0,154,3,3,0,0E,'.$data['name'].' 
AD,0,268,3,3,0,0E,'.$data['c-ln'].' 
AD,0,382,3,3,0,0E,'.$data['dim'].' 
AD,0,496,3,3,0,0E,'.$data['warehouse'].' 
AD,322,1324,10,10,0,0E,'.$wheel.'
W370,778,5,2,M,8,13,39,0
https://melissa.spdns.de/kivitendo/c200
E
';

        // PRN-Datei erstellen
        $filename = 'label.prn';
        $filepath = $dir . "/" . $filename;
        file_put_contents( $filepath, $ezpl );
        //sleep(3);
        exec( 'lp -d BP730-Raw -o raw '.escapeshellarg( $filepath ) );
    }
    echo '{"result":1}';
}
