<?php

require_once __DIR__.'/../inc/stdLib.php'; // for debug
require_once __DIR__.'/../inc/crmLib.php';
require_once __DIR__.'/../inc/ajax2function.php';

function resultInfo( $success, $text = '', $debug = false ){
    $info = '{ "success":'.(($success)? 'true' : 'false');
    if( !empty( $text ) ) if( !$success || $debug ) $info .= ', "debug":"'.$text.'"';
    echo $info.' }';
}

/*********************************************
* Check lxcars tables exists and then get
* last version
*********************************************/
function getLxcarsVer(){
     echo $GLOBALS['dbh']->getOne( "SELECT EXISTS(SELECT * FROM information_schema.tables WHERE table_name = 'lxc_ver') AS lxcars, (SELECT json_agg( lxc_ver ) AS lxc_ver FROM (SELECT COALESCE(version, '') AS version, COALESCE(subversion, '') AS subversion FROM public.lxc_ver WHERE EXISTS(SELECT * FROM information_schema.tables WHERE table_name = 'lxc_ver') ORDER BY datum DESC LIMIT 1) AS lxc_ver) AS lxc_ver", true );
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
    $query .= "( SELECT json_agg( cars ) AS cv FROM (SELECT c_ln AS ln, '--------' AS manuf, '-----' AS ctype, '---' AS cart FROM lxc_cars WHERE c_ow = ".$data['id']." ORDER BY c_id) AS cars) AS cars";

    echo $GLOBALS['dbh']->getOne( $query, true );

    // Write history
    $lastdata[0] = $data['id']; //for compatibility
    $lastdata[1] = $data['name'];
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
                "SELECT id, description FROM tax_zones WHERE obsolete = false ORDER BY description ASC".
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
                "account_number, taxnumber, taxzone_id, payment_id, bank_code, bank, ustid, iban, bic, direct_debit, ".
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

function insertDB( $data ){
    writeLog( 'insertDB' );
    writeLog( $data );
    resultInfo( true );
}

function updateDB( $data ){
    writeLog( 'updateDB' );
    writeLog( $data );
    foreach( $data AS $key => $value ){
        writeLog( $key );
        writeLog( $value );
    }
    
    //$query = 
    
    
    resultInfo( true );

}
