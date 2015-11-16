<?php
/*********************************************************************
*** CRMTI - Customer Relationship Management Telephone Integration ***
*** geschrieben von Ronny Kumke ronny@lxcars.de Artistic License 2 ***
*** begonnen im April 2011, liest crmti aus, Version 1.1.0         ***
*********************************************************************/

require_once("../inc/conf.php");
//require_once("../inc/db.php");
require_once("../inc/stdLib.php");


$_GET['action']($_GET['data']); //Funktion aufrufen

//array_shift( $_GET )();

function CreateFunctionsAndTable(){ //Legt beim ersten Aufruf der Datenbank die benötigten Tabellen und Funktionen an.
    global $db;
    $sql = file_get_contents("update/install_crmti.sql");
    $statement = explode(";;", $sql );//zum Erzeugen von Funktionen sind Semikola notwendig, fertiges sql-Statement = ;;
    $sm0 = '/\/\*.{0,}\*\//';// SuchMuster ' /* bla */ '
    $sm1 = '/--.{0,}\n/';    // SuchMuster ' --bla \n '
    foreach( $statement as $key=>$value ){
        $sok0 = preg_replace( $sm0, '',$statement[$key] );
        $sok1 = preg_replace( $sm1, '',$sok0 );
        $rc=$db->query( $sok1 );
    }
    $sql="insert into schema_info (tag, login) values ('crm_telefon_integration', '".$_SESSION['login'].")'";
    $rc=$_SESSION['db']->query($sql);
}

function getCallListComplete(){
    $sql = "SELECT json_agg( json_calls ) FROM ( SELECT EXTRACT(EPOCH FROM TIMESTAMPTZ(crmti_init_time)) AS call_date, crmti_status, crmti_src, crmti_dst, crmti_caller_id, crmti_caller_typ, crmti_direction  FROM crmti ORDER BY crmti_init_time DESC) AS json_calls";
    $rs = $_SESSION['db']->getone( $sql );
    if( !$rs ){
        CreateFunctionsAndTable();
    }
    //print_r( $rs );
    echo $rs['json_agg'];
    return 1;
}

function getLastCall(){
    return 'lastItem';
}
$number = $_GET['number'];
function numberToAdress( $number  ){
    //$number = "03343515230";
    //$number = "03343515279";
    $klicktelKey = "95d5a5f8d8ef062920518592da992cba";
    $url = "http://openapi.klicktel.de/searchapi/invers?key=";
    $url .= $klicktelKey;
    $url .= "&number=";
    $url .= $number;

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    $result = curl_exec( $ch );
    if( $result === false || curl_errno( $ch ) ){
        die( 'Curl-Error: ' .curl_error( $ch ) );
    }
    curl_close( $ch );
    $objResult = json_decode( $result, true );
    $first_entry = $objResult['response']['results']['0']['entries']['0'];

    if( $first_entry['entrytype'] == 'business' ){
        $entry['salutation'] = 'Firma';
    }
    else {
        $entry['salutation'] = $first_entry['salutation'];
        $entry['firstname']  = $first_entry['firstname'];
        $entry['lastname']   = $first_entry['lastname'];
    }

    $entry['fullName']     = $first_entry['displayname'];
    $entry['backlink']     = $first_entry['backlink'];
    $entry['street']       = $first_entry['location']['street'];
    $entry['streetnumber'] = $first_entry['location']['streetnumber'];
    $entry['zipcode']      = $first_entry['location']['zipcode'];
    $entry['city']         = $first_entry['location']['city'];

    if( $objResult['response']['results']['0'] ) echo json_encode( $entry );
}

?>