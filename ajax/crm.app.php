<?php

require_once __DIR__.'/../inc/stdLib.php'; // for debug
require_once __DIR__.'/../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

/*************************************************
* Erzeugt ein JSON das für die JS-Function
* showMessageDialog verwendet werden kann
*************************************************/
function resultInfo( $success, $debug_text = '' ){
    $info = '{ "success":'.(($success)? 'true' : 'false');
    if( !empty( $debug_text ) ) $info .= ', "debug":"'.$debug_text.'"';
    echo $info.' }';
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
        echo $GLOBALS['dbh']->getAll("(SELECT 'Kunde' AS category, 'C' AS src, '' AS value, id, name AS label FROM customer WHERE name ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'Lieferant' AS category, 'V' AS src, '' AS value, id, name AS label FROM vendor WHERE name ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'Kontaktperson' AS category, 'P' AS src, '' AS value, cp_id AS id, concat(cp_givenname, ' ', cp_name) AS name FROM contacts WHERE cp_name ILIKE '%".$term."%' OR cp_givenname ILIKE '%".$term."%' LIMIT 5) UNION ALL (SELECT 'Fahrzeug' AS category, 'A' AS src, c_ln AS value, c_id AS id, name AS label FROM lxc_cars JOIN customer ON c_ow = id WHERE c_ln ILIKE '%".$term."%' AND obsolete = false LIMIT 5)", true);
    }
}

function computeArticleNumber( $data ){
    if( $data[part_type] == "P" )
        $rs = $GLOBALS['dbh']->getOne( "SELECT id AS defaults_id, articlenumber::INT + 1 AS newnumber, 0 AS service FROM defaults");
    else
        $rs = $GLOBALS['dbh']->getOne( "SELECT id AS defaults_id, servicenumber::INT + 1 AS newnumber, customer_hourly_rate, 1 AS service FROM defaults");
    while( $GLOBALS['dbh']->getOne( "SELECT partnumber FROM parts WHERE partnumber = '".$rs['newnumber']."'" )['partnumber'] ) $rs['newnumber']++;

    return $rs;
}

function newArticleNumber( $data ){
    echo json_encode( computeArticleNumber( $data ) );
}

function dataForNewArticle( $data ){
    $an = computeArticleNumber( $data );

    $query .= "SELECT (SELECT json_agg( units ) AS units FROM (".
                "SELECT name FROM units".
                    ") AS units) AS units, ";

    $query .= "(SELECT json_agg( buchungsgruppen ) AS buchungsgruppen FROM (".
                "SELECT id, description FROM  buchungsgruppen WHERE obsolete = false ORDER BY sortkey ASC".
                    ") AS buchungsgruppen) AS buchungsgruppen";

    $units = $GLOBALS['dbh']->getOne($query, true);

    echo  '{ "defaults": '.json_encode( $an ).', "common": '.$units.' }';
}

function insertNewArticle( $data ){
//    genericSingleInsert( $data );
}

/********************************************
* Find parts like service, instructions and goods for orders
* Sortet by quantity and categorie (instruction, good and service)
********************************************/
function findPart( $term ){
    //Index installieren create index idx_orderitems on orderitems ( parts_id );
    if( isset( $_GET['term'] ) && !empty( $_GET['term'] ) ) {
        $term = $_GET['term'];
        $sql = "(SELECT 'D' AS part_type,  'Anweisungen' AS category, description, partnumber, id, description AS value, part_type, unit,  partnumber || ' ' || description AS label, instruction, sellprice,";
        $sql .= " (SELECT qty FROM instructions WHERE instructions.parts_id = parts.id AND instructions.qty IS NOT null GROUP BY qty ORDER BY count( instructions.qty ) DESC, qty DESC LIMIT 1) AS qty,";
        $sql .= " (SELECT tax.rate FROM parts i INNER JOIN taxzone_charts ON parts.buchungsgruppen_id = taxzone_charts.buchungsgruppen_id INNER JOIN taxkeys ON taxzone_charts.income_accno_id = taxkeys.chart_id INNER JOIN tax ON taxkeys.tax_id = tax.id WHERE i.id = parts.id AND parts.obsolete = false AND taxzone_charts.taxzone_id = 4 GROUP BY parts.id, tax.rate, taxkeys.startdate ORDER BY taxkeys.startdate DESC LIMIT 1) AS rate";
        $sql .= " FROM parts WHERE ( description ILIKE '%$term%' OR partnumber ILIKE '$term%' ) AND obsolete = FALSE AND part_type ='service' AND instruction = true ORDER BY ( SELECT ( SELECT count( qty ) FROM orderitems WHERE parts_id = parts.id ) ) DESC NULLS LAST LIMIT 5) UNION ALL";
        $sql .= " (SELECT 'W' AS part_type,  'Waren' AS category, description, partnumber, id, description AS value, part_type, unit,  partnumber || ' ' || description AS label, instruction, sellprice,";
        $sql .= " (SELECT qty FROM orderitems WHERE orderitems.parts_id = parts.id AND orderitems.qty IS NOT null GROUP BY qty ORDER BY count( orderitems.qty ) DESC, qty DESC LIMIT 1) AS qty,";
        $sql .= " (SELECT tax.rate FROM parts i INNER JOIN taxzone_charts ON parts.buchungsgruppen_id = taxzone_charts.buchungsgruppen_id INNER JOIN taxkeys ON taxzone_charts.income_accno_id = taxkeys.chart_id INNER JOIN tax ON taxkeys.tax_id = tax.id WHERE i.id = parts.id AND parts.obsolete = false AND taxzone_charts.taxzone_id = 4 GROUP BY parts.id, tax.rate, taxkeys.startdate ORDER BY taxkeys.startdate DESC LIMIT 1) AS rate";
        $sql .= " FROM parts WHERE ( description ILIKE '%$term%' OR partnumber ILIKE '$term%' ) AND obsolete = FALSE AND part_type = 'part'  AND instruction = false ORDER BY ( SELECT ( SELECT count( qty ) FROM orderitems WHERE parts_id = parts.id ) ) DESC NULLS LAST LIMIT 5) UNION ALL";
        $sql .= " (SELECT 'D' AS part_type,  'Dienstleistung' AS category, description, partnumber, id, description AS value, part_type, unit,  partnumber || ' ' || description AS label, instruction, sellprice,";
        $sql .= " (SELECT qty FROM orderitems WHERE orderitems.parts_id = parts.id AND orderitems.qty IS NOT null GROUP BY qty ORDER BY count( orderitems.qty ) DESC, qty DESC LIMIT 1) AS qty,";
        $sql .= " (SELECT tax.rate FROM parts i INNER JOIN taxzone_charts ON parts.buchungsgruppen_id = taxzone_charts.buchungsgruppen_id INNER JOIN taxkeys ON taxzone_charts.income_accno_id = taxkeys.chart_id INNER JOIN tax ON taxkeys.tax_id = tax.id WHERE i.id = parts.id AND parts.obsolete = false AND taxzone_charts.taxzone_id = 4 GROUP BY parts.id, tax.rate, taxkeys.startdate ORDER BY taxkeys.startdate DESC LIMIT 1) AS rate";
        $sql .= " FROM parts WHERE ( description ILIKE '%$term%' OR partnumber ILIKE '$term%' ) AND obsolete = FALSE AND part_type ='service' AND instruction = false ORDER BY ( SELECT ( SELECT count( qty ) FROM orderitems WHERE parts_id = parts.id ) ) DESC NULLS LAST LIMIT 5)";
        echo $GLOBALS['dbh']->getAll( $sql, true );
    }
}

function getCVPA( $data ){
    $query = "SELECT ";
    if($data['src'] == 'C' || $data['src'] == 'V' ){
        // Stammdaten
        $db_table = array('C' => 'customer', 'V' => 'vendor');
        $query .= "(SELECT row_to_json( cv ) AS cv FROM (".
                    "SELECT '".$data['src']."' AS src, id, name, street, zipcode, contact, phone AS phone1, fax AS phone2, email, city, country FROM ".$db_table[$data['src']]." WHERE id=".$data['id'].
                    ") AS cv) AS cv, ";
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
    //$query .= "( SELECT json_agg( cars ) AS cv FROM (SELECT c_id AS id, c_ln AS ln, '--------' AS manuf, '-----' AS ctype, '---' AS cart FROM lxc_cars WHERE c_ow = ".$data['id']." ORDER BY c_id) AS cars) AS cars";
    $query .= "( SELECT json_agg( cars ) AS cars FROM (".
                "SELECT c_id, c_ln, hersteller, name, 'automobil' AS mytype FROM lxc_cars JOIN kbacars ON( lxc_cars.c_2 = kbacars.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbacars.tsn   ) WHERE c_ow = ".$data['id']." UNION All ".
                "SELECT c_id, c_ln, hersteller, name, 'trailer' AS mytype FROM lxc_cars JOIN kbatrailer ON( lxc_cars.c_2 = kbatrailer.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatrailer.tsn   ) WHERE c_ow = ".$data['id']." UNION ALL ".
                "SELECT c_id, c_ln, hersteller, name, 'bikes' AS mytype FROM lxc_cars JOIN kbabikes ON( lxc_cars.c_2 = kbabikes.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbabikes.tsn   ) WHERE c_ow = ".$data['id']." UNION ALL ".
                "SELECT c_id, c_ln, hersteller, name, 'trucks' AS mytype FROM lxc_cars JOIN kbatrucks ON( lxc_cars.c_2 = kbatrucks.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatrucks.tsn   ) WHERE c_ow = ".$data['id']." UNION ALL ".
                "SELECT c_id, c_ln, hersteller, name, 'tractor' AS mytype FROM lxc_cars JOIN kbatractors ON( lxc_cars.c_2 = kbatractors.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatractors.tsn   ) WHERE c_ow = ".$data['id'].
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
                "SELECT id, name, description AS label, type, description AS tooltip, '42' AS size, options AS data FROM custom_variable_configs WHERE module = 'CT' ORDER BY description ASC".
                ") AS vars_conf) AS vars_conf";
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
                "branche, homepage, department_1, department_2, lead, leadsrc, konzern, headcount, language_id, employee ".
                "FROM ".$db_table[$data['src']]." WHERE id=".$data['id'].
                ") AS cv) AS cv, ";

    // Lieferadressen
    $query .= "(SELECT json_agg( deladdr ) AS deladdr FROM (".
                "SELECT trans_id, shipto_id, shiptoname, shiptodepartment_1, shiptodepartment_2, shiptostreet, shiptozipcode, shiptocity, shiptocountry, shiptocontact, shiptophone, shiptofax, shiptoemail, shiptoemployee, shiptobland FROM shipto WHERE trans_id = ".$data['id']." ORDER BY shiptoname ASC".
                ") AS deladdr) AS deladdr, ";

    appendQueryForCustomerDlg( $query );

    echo $GLOBALS['dbh']->getOne($query, true);
}

/***********************************************
* Get Data form DB to build drop down elemennts
* in new CV dialog
**********************************************/
function getCVDialogData(){
    $query = "SELECT ";
    appendQueryForCustomerDlg( $query );

    echo $GLOBALS['dbh']->getOne($query, true);
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
    $apiKeyArray = getDefaultsByArray( array( 'lxcarsapi') );
    $rs = file_get_contents( 'https://fahrzeugschein-scanner.de/api/Scans/ScanDetails/'.$apiKeyArray['lxcarsapi'].'/'.$data['id'].'/false' );
    echo $rs;
}

function searchCustomerForScan( $data ){
    $rs = $GLOBALS['dbh']->getAll( "SELECT id, name, street, zipcode, city FROM customer WHERE name ILIKE '%".$data['name']."%' LIMIT 12", true );
    echo ( empty( $rs ) )? 0 : $rs;
}

function getCar( $data ){
    echo $GLOBALS['dbh']->getOne( "SELECT * FROM lxc_cars WHERE c_id = ".$data['id'], true );
}

function getDataForNewLxcarsOrder( $data ){
    writeLog($_SESSION['loginCRM']);

    $query = "SELECT customer.id AS customer_id, customer.name AS customer_name, customer.notes AS int_cu_notes, c_id, ".
                "lxc_cars.c_ln, lxc_cars.c_text AS int_car_notes, employee.id AS employee_id, employee.name AS employee_name ".
                "FROM lxc_cars INNER JOIN customer ON customer.id = lxc_cars.c_ow INNER JOIN employee ON employee.id = ".$_SESSION['loginCRM']." WHERE lxc_cars.c_id = ".$data['id'];

    echo '{ "common": '.$GLOBALS['dbh']->getOne( $query, true ).', "workers": '.json_encode(ERPUsersfromGroup("Werkstatt")).' }';
}

function getOrder( $data ){
    $orderID = $data['id'];
    $taxzone_id = 4;
    $sql  = "SELECT  item_id as id, parts_id, position, instruction, qty, description, unit, sellprice, marge_total, discount, u_id, partnumber, part_type, longdescription, status, rate ";
    $sql .= "FROM ( SELECT parts.instruction, parts.buchungsgruppen_id, instructions.id AS item_id, instructions.parts_id, instructions.qty, instructions.description, instructions.position, instructions.unit, instructions.sellprice, instructions.marge_total, instructions.discount, instructions.u_id, instructions.status, parts.partnumber, parts.part_type, instructions.longdescription FROM instructions INNER JOIN  parts  ON ( parts.id = instructions.parts_id ) WHERE instructions.trans_id = '".$orderID."' ";
    $sql .= "UNION SELECT  parts.instruction, parts.buchungsgruppen_id, orderitems.id AS item_id, orderitems.parts_id, orderitems.qty, orderitems.description, orderitems.position, orderitems.unit, orderitems.sellprice, orderitems.marge_total, orderitems.discount, orderitems.u_id, orderitems.status, parts.partnumber, parts.part_type, orderitems.longdescription FROM orderitems INNER JOIN parts ON ( parts.id = orderitems.parts_id ) WHERE orderitems.trans_id = '".$orderID."' ORDER BY position ) AS mysubquery ";
    $sql .= "JOIN taxzone_charts c ON ( mysubquery.buchungsgruppen_id = c.buchungsgruppen_id )  JOIN taxkeys k ON ( c.income_accno_id = k.chart_id AND k.startdate = ( SELECT max(startdate) FROM taxkeys tk1 WHERE c.income_accno_id = tk1.chart_id AND tk1.startdate::TIMESTAMP <= NOW()  ) ) JOIN tax ON (k.tax_id = tax.id ) WHERE taxzone_id = ".$taxzone_id." GROUP BY item_id, parts_id, position, instruction, qty, description, unit, sellprice, marge_total, discount, u_id, partnumber, part_type, longdescription, status, rate ORDER BY position ASC";

    $query = "SELECT ";
    $query .= "(SELECT row_to_json( common ) AS common FROM (".
                "SELECT oe.*, customer.name AS customer_name, customer.notes AS int_cu_notes, lxc_cars.c_ln AS c_ln, lxc_cars.c_text AS int_car_notes, employee.id AS employee_id, employee.name AS employee_name FROM oe INNER JOIN customer ON customer.id = oe.customer_id INNER JOIN lxc_cars ON lxc_cars.c_id = oe.c_id INNER JOIN employee ON oe.employee_id = employee.id WHERE oe.id = ".$orderID.
                ") AS common) AS common, ";

    $query .= "(SELECT json_agg( orderitems ) AS orderitems FROM (".$sql.") AS orderitems) AS orderitems";

    $workers = json_encode(ERPUsersfromGroup("Werkstatt"));

    echo '{ "order": '.$GLOBALS['dbh']->getOne( $query, true ).', "workers": '.$workers.' }';
}

/********************************************
* Insert a new Customer optional  with new Car
********************************************/
function insertNewCuWithCar( $data ){
    $id = FALSE;
    $GLOBALS['dbh']->beginTransaction();
    foreach( $data AS $key => $value ){
        if( strcmp( $key, 'customer' ) === 0 ){
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
    $id = $GLOBALS['dbh']->getOne( "WITH tmp AS ( UPDATE defaults SET sonumber = sonumber::INT + 1 RETURNING sonumber) INSERT INTO oe ( ordnumber, customer_id, employee_id, taxzone_id, currency_id, c_id) SELECT ( SELECT sonumber FROM tmp), ".$data['customer_id'].", ".$_SESSION['id'].",  customer.taxzone_id, customer.currency_id, ".$data['c_id']." FROM customer WHERE customer.id = ".$data['customer_id']." RETURNING id ")['id'];
    echo '{ "id": "'.$id.'"  }';
}

/********************************************
* Ubdate Customer optional with new Car
********************************************/
function updateCuWithNewCar( $data ){
    $id = FALSE;
    foreach( $data AS $key => $value ){
        $where = '';
        if( array_key_exists( 'WHERE', $value ) ){
                foreach( $value['WHERE'] AS $whereId => $whereVal ){
                    if( strcmp( $whereId, 'id' ) === 0 ) $id = $whereVal;
                    //Kommas fehlen:
                    $where = $whereId.' = '.$whereVal;
                }
                unset( $value['WHERE'] );
        }
        if( strcmp( $key, 'lxc_cars' ) === 0 ){
            $value['c_ow'] = $id;
            $GLOBALS['dbh']->insert( $key, array_keys( $value ), array_values( $value ) );
        }
        else{
            if( empty( $where ) ){
                resultInfo( false, 'Risky SQL-Statment with empty WHERE clausel'  );
                return;
            }
            $GLOBALS['dbh']->update( $key, array_keys( $value ), array_values( $value ), $where );
        }
    }
    echo '{ "src": "C", "id": "'.$id.'" }';
}

function genericSingleInsert( $data ){
    $tableName = array_key_first( $data['record'] );
    $id = $GLOBALS['dbh']->insert( $tableName, array_keys( $data['record'][$tableName] ), array_values( $data['record'][$tableName] ),
                                array_key_exists( 'sequence_name', $data ), ( array_key_exists( 'sequence_name', $data ) )? $data['sequence_name'] : FALSE );
    echo '{ "id": "'.$id.'" }';
}

function genericUpdate( $data ){

    foreach( $data AS $key => $value ){
        $where = '';
        if( array_key_exists( 'WHERE', $value ) ){
                foreach( $value['WHERE'] AS $whereId => $whereVal ){
                    //Kommas fehlen:
                    $where = $whereId.' = '.$whereVal;
                }
                unset( $value['WHERE'] );
        }
        if( empty( $where ) ){
            resultInfo( false, 'Risky SQL-Statment with empty WHERE clausel'  );
            return;
        }
        //writeLog( $key ); writeLog( array_keys( $value ) ); writeLog( array_values( $value ) ); writeLog( $where );
        $GLOBALS['dbh']->update( $key, array_keys( $value ), array_values( $value ), $where );
    }

    resultInfo( true );
}

function genericUpdateEx( $data ){

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
}

function genericDelete( $data ){
    foreach( $data AS $tableName => $where){
        if( !isset( $where['WHERE'] ) ){
            resultInfo( false, 'Risky SQL-Statment with empty WHERE clausel'  );
            return;
        }
        $GLOBALS['dbh']->query( "DELETE FROM $tableName WHERE ".$where['WHERE'] );
    }
    resultInfo(true);
}

function printOrder( $data ){

    require 'fpdf.php';
    require_once __DIR__.'/../lxcars/inc/lxcLib.php';
    include_once __DIR__.'/../lxcars/inc/config.php';

    if( isset( $data['data'] ) ){
        $data = $data['data'];
    }

    $sql  = "SELECT oe.ordnumber, oe.transdate, oe.finish_time, oe.km_stnd, oe.employee_id, printed, ";
    $sql .= "customer.name, customer.street, customer.zipcode, customer.city, customer.phone, customer.fax, customer.notes, ";
    $sql .= "lxc_cars.c_ln, lxc_cars.c_2, lxc_cars.c_3, lxc_cars.c_mkb, lxc_cars.c_t, lxc_cars.c_fin, lxc_cars.c_st_l, lxc_cars.c_wt_l, ";
    $sql .= "lxc_cars.c_text, lxc_cars.c_color, lxc_cars.c_zrk, lxc_cars.c_zrd, lxc_cars.c_em, lxc_cars.c_bf, lxc_cars.c_wd, lxc_cars.c_d, lxc_cars.c_hu, employee.name AS employee_name, lxc_flex.flxgr ";
    $sql .= "FROM oe join customer on oe.customer_id = customer.id join lxc_cars on oe.c_id = lxc_cars.c_id join employee on oe.employee_id = employee.id ";
    $sql .= "left join lxc_flex on ( lxc_cars.c_2 = lxc_flex.hsn AND lxc_flex.tsn = substring( lxc_cars.c_3 from 1 for 3 ) ) WHERE oe.id = ".$data['orderId'];

    $query = "SELECT * FROM ($sql) AS o, ";

    $sql = "SELECT c_id, c_ln, hersteller, name AS bezeichnung, 'automobil' AS mytype FROM lxc_cars JOIN kbacars ON( lxc_cars.c_2 = kbacars.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbacars.tsn   ) WHERE c_ow = ".$data['customerId']." UNION All ".
           "SELECT c_id, c_ln, hersteller, name AS bezeichnung, 'trailer' AS mytype FROM lxc_cars JOIN kbatrailer ON( lxc_cars.c_2 = kbatrailer.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatrailer.tsn   ) WHERE c_ow = ".$data['customerId']." UNION ALL ".
           "SELECT c_id, c_ln, hersteller, name AS bezeichnung, 'bikes' AS mytype FROM lxc_cars JOIN kbabikes ON( lxc_cars.c_2 = kbabikes.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbabikes.tsn   ) WHERE c_ow = ".$data['customerId']." UNION ALL ".
           "SELECT c_id, c_ln, hersteller, name AS bezeichnung, 'trucks' AS mytype FROM lxc_cars JOIN kbatrucks ON( lxc_cars.c_2 = kbatrucks.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatrucks.tsn   ) WHERE c_ow = ".$data['customerId']." UNION ALL ".
           "SELECT c_id, c_ln, hersteller, name AS bezeichnung, 'tractor' AS mytype FROM lxc_cars JOIN kbatractors ON( lxc_cars.c_2 = kbatractors.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatractors.tsn   ) WHERE c_ow = ".$data['customerId'];

    $query .= "($sql) AS kba";

    $orderData = $GLOBALS['dbh']->getOne( $query );

    $taxzone_id = 4;
    $query  = "SELECT  item_id as id, parts_id, position, instruction, qty, description, unit, sellprice, marge_total, discount, u_id, partnumber, part_type, longdescription, status, rate ";
    $query .= "FROM ( SELECT parts.instruction, parts.buchungsgruppen_id, instructions.id AS item_id, instructions.parts_id, instructions.qty, instructions.description, instructions.position, instructions.unit, instructions.sellprice, instructions.marge_total, instructions.discount, instructions.u_id, instructions.status, parts.partnumber, parts.part_type, instructions.longdescription FROM instructions INNER JOIN  parts  ON ( parts.id = instructions.parts_id ) WHERE instructions.trans_id = '".$data['orderId']."' ";
    $query .= "UNION SELECT  parts.instruction, parts.buchungsgruppen_id, orderitems.id AS item_id, orderitems.parts_id, orderitems.qty, orderitems.description, orderitems.position, orderitems.unit, orderitems.sellprice, orderitems.marge_total, orderitems.discount, orderitems.u_id, orderitems.status, parts.partnumber, parts.part_type, orderitems.longdescription FROM orderitems INNER JOIN parts ON ( parts.id = orderitems.parts_id ) WHERE orderitems.trans_id = '".$data['orderId']."' ORDER BY position ) AS mysubquery ";
    $query .= "JOIN taxzone_charts c ON ( mysubquery.buchungsgruppen_id = c.buchungsgruppen_id )  JOIN taxkeys k ON ( c.income_accno_id = k.chart_id AND k.startdate = ( SELECT max(startdate) FROM taxkeys tk1 WHERE c.income_accno_id = tk1.chart_id AND tk1.startdate::TIMESTAMP <= NOW()  ) ) JOIN tax ON (k.tax_id = tax.id ) WHERE taxzone_id = ".$taxzone_id." GROUP BY item_id, parts_id, position, instruction, qty, description, unit, sellprice, marge_total, discount, u_id, partnumber, part_type, longdescription, status, rate ORDER BY position ASC";

    $positions = $GLOBALS['dbh']->getAll( $query );

    define( 'FPDF_FONTPATH', '../font/');
    define( 'x', 0 );
    define( 'y', 1 );

    $pdf = new FPDF( 'P','mm','A4' );
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

    $pdf->SetFont( 'Helvetica', 'B', 14 ); //('font_family','font_weight','font_size')
    $pdf->Text( '10','12','Autoprofis Rep.-Auftrag '.' '.$orderData['hersteller'].' '.$orderData['mytype'].' '.$orderData['bezeichnung'] );
    $pdf->Text( '10','18', $orderData['c_ln'] );
    $pdf->SetFont( 'Helvetica', '', 14 );

    //fix values
    $pdf->SetFont( 'Helvetica', 'B', $fontsize ) ;
    $pdf->Text( $textPosX_left, $textPosY,'Kunde:' );
    $pdf->Text( $textPosX_left, $textPosY + 5, utf8_decode( 'Straße:' ) );
    $pdf->Text( $textPosX_left, $textPosY + 10, 'Ort:' );
    $pdf->Text( $textPosX_left, $textPosY + 15, 'Tele.:' );
    $pdf->Text( $textPosX_left, $textPosY + 20, 'Tele2:' );
    $pdf->Text( $textPosX_left, $textPosY + 25, 'Bearb.:' );

    $pdf->SetFont( 'Helvetica', '', $fontsize );
    $pdf->Text( $textPosX_left + 20, $textPosY, utf8_decode( substr( $orderData['name'], 0, 34 ) ) );
    $pdf->Text( $textPosX_left + 20, $textPosY + 5, utf8_decode( $orderData['street'] ) );
    $pdf->Text( $textPosX_left + 20, $textPosY + 10, $orderData['zipcode'].' '.utf8_decode( $orderData['city'] ) );
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

    $pdf->Text( $textPosX_right, $textPosY + 35, utf8_decode( 'Lo Sommerräder.:' ) );
    $pdf->Text( $textPosX_right, $textPosY + 40, utf8_decode( 'Lo Winterräder.:' ) );

    $pdf->Text( $textPosX_left, $textPosY + 35, utf8_decode( 'nächst. ZR-Wechsel KM:' ) );
    $pdf->Text( $textPosX_left, $textPosY + 40, utf8_decode( 'nächst. ZR-Wechsel:' ) );
    $pdf->Text( $textPosX_left, $textPosY + 45, utf8_decode( 'nächst. Bremsfl.:' ) );
    $pdf->Text( $textPosX_left, $textPosY + 50, utf8_decode( 'nächst. WD:' ) );

    $pdf->SetLineWidth( 0.2 );

    $pdf->SetFont( 'Helvetica', '', $fontsize );

    $pdf->Text( $textPosX_right + 45, $textPosY + 45, utf8_decode( $orderData['flxgr'] ) );
    $pdf->Text( $textPosX_right + 45, $textPosY + 50, utf8_decode( $orderData['c_color'] ) );

    //left side under line one
    $lsulo = 50;
    $pdf->Text( $textPosX_left + $lsulo, $textPosY + 35, $orderData['c_zrk'] );
    $pdf->Text( $textPosX_left + $lsulo, $textPosY + 40, utf8_decode( $orderData['c_zrd'] ) );
    $pdf->Text( $textPosX_left + $lsulo, $textPosY + 45, utf8_decode( $orderData['c_bf'] ) );
    $pdf->Text( $textPosX_left + $lsulo, $textPosY + 50, utf8_decode( $orderData['c_wd'] ) );


    $pdf->Text( $textPosX_right + 45, $textPosY + 35, utf8_decode( $orderData['c_st_l'] ) );
    $pdf->Text( $textPosX_right + 45, $textPosY + 40, utf8_decode( $orderData['c_wt_l'] ) );

    //Finish Time
    if( strpos( $orderData['finish_time'], 'wartet' ) ) $pdf->SetTextColor( 255, 0, 0 );
    $pdf->SetFont( 'Helvetica', 'B', '12' );
    $finishTimeHeight = 85;
    $pdf->Text( $textPosX_left, $finishTimeHeight, 'Fertigstellung:' );
    $pdf->SetFont( 'Helvetica', 'B', '12' );
    $pdf->Text( $textPosX_right, $finishTimeHeight, utf8_decode( $orderData['finish_time'] ) );
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
        writeLog( $element );
        if( $element['instruction'] ){
            $height = $height + 8;
            //$pdf->SetTextColor( 255, 0, 0 );
            $pdf->SetLineWidth( 0.1 );
            $pdf->Line( 10, $height + 1.6 , 190, $height + 1.6 );
            $pdf->SetFont('Helvetica','B','12');
            //$pdf->SetTextColor( 100, 100, 100 );
            $pdf->Text( '12',$height, utf8_decode( $element['description'] ) );
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
                    $pdf->Text( '16', $height, utf8_decode( $line ) );
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
    $pdf->Text( '22', '280', utf8_decode( 'Endkontrolle UND Probefahrt durchgeführt von: __________________' ) );
    //$pdf->SetTextColor( 0, 0, 0 );
    $pdf->SetFont( 'Helvetica', '', '08' );
    $pdf->Text( '75', '290', 'Powered by lxcars.de - Freie Kfz-Werkstatt Software' );


    //Backside
    //EPSON Printer print must rotate secound page
    $rotate = 0; //Degree --Only for Epson
    $pdf->AddPage( 'P', 'A4', $rotate );
    $pdf->SetFont( 'Helvetica', 'B', '16' );
    $pdf->Text( 10, 20, utf8_decode( 'Verbaute Ersazteile' ) );
    $height = 30;
    $pdf->SetFont( 'Helvetica', '', '10' );
    $pdf->line( 10, $height - 4.4,  190, $height - 4.4 );
    $totalLines = 22;
    foreach( $positions as $index => $element ){
        //writeLog( $element);
        if( ( $element['part_type']  == 'part' ) && !$element['instruction'] ){
            $totalLines--;
            //writeLog( 'part' );
            $pdf->line( 10, $height + 1.6,  190, $height + 1.6 );
            $pdf->Text( '12', $height, utf8_decode( $element['qty']." ".$element['unit'] ) );
            $pdf->Text( '26', $height, utf8_decode( $element['description'] ) );
            $height = $height + 6;
        }
    }
    while( $totalLines-- ){
        $pdf->line( 10, $height + 1.6, 190, $height + 1.6 );
        $height = $height + 6;
    }


    $pdf->SetFont( 'Helvetica', 'B', '16' );
    $pdf->Text( 10, $height + 10, utf8_decode( 'Ausgeführte Arbeiten' ) );
    $height = $height + 20;
    $pdf->SetFont( 'Helvetica', '', '10' );
    $totalLines = 16;
    $pdf->line( 10, $height - 4.4,  190, $height - 4.4 );
    foreach( $positions as $index => $element ){
        if( ( $element['part_type']  == 'service' ) && !$element['instruction'] ){
            $totalLines--;
            $pdf->line( 10, $height + 1.6,  190, $height + 1.6 );
            $pdf->Text( '12', $height, utf8_decode( $element['qty']." ".$element['unit'] ) );
            $pdf->Text( '26', $height, utf8_decode( $element['description'] ) );
            $height = $height + 6;
        }
    }
    while( $totalLines-- ){
        $pdf->line( 10, $height + 1.6,  190, $height + 1.6 );
        $height = $height + 6;
    }

    $pdf->Text( '10','290', utf8_decode( 'Ich habe sämtliche Ersazteile und ausgeführte Arbeiten i.d. obigen Liste notiert. Unterschrift: __________________' ) );
    $pdf->OutPut( __DIR__.'/../out.pdf', 'F' );


    if( $data['print'] == 'printOrder1' ){
      //system('lpr -P test '.__DIR__.'/../out.pdf' );
      //system('lpr -P Buro1 '.__DIR__.'/../out.pdf' );
      system('lpr -P Canon_LBP663C '.__DIR__.'/../out.pdf' );
      if( !$orderData['printed'] )
        $GLOBALS['dbh']->update( 'oe', array( 'printed' ), array( 'TRUE' ), 'id = '.$data['orderId'] );
    }
    if( $data['print'] == 'printOrder2' ){
      //system('lpr -P test '.__DIR__.'/../out.pdf' );
      system('lpr -P HP_LASER '.__DIR__.'/../out.pdf' );
      if( !$orderData['printed'] )
        $GLOBALS['dbh']->update( 'oe', array( 'printed' ), array( 'TRUE' ), 'id = '.$data['orderId'] );
    }

    echo 1;
}
