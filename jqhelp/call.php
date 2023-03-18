<?php
/**************************************************************************
*** CRMTI - Customer Relationship Management Telephone Integration      ***
*** geschrieben von Ronny Zimmermann ronny@lxcars.de Artistic License 2 ***
*** begonnen im April 2011, Version 1.1.0                               ***
**************************************************************************/

require_once("../inc/ajax2function.php");

function CreateFunctionsAndTable(){ //Legt beim ersten Aufruf der Datenbank die benötigten Tabellen und Funktionen an.
    global $db;
    $sql = file_get_contents("../update/install_crmti.sql");
    $statement = explode(";;", $sql );//zum Erzeugen von Funktionen sind Semikola notwendig, fertiges sql-Statement = ;;
    $sm0 = '/\/\*.{0,}\*\//';// SuchMuster ' /* bla */ '
    $sm1 = '/--.{0,}\n/';    // SuchMuster ' --bla \n '
    foreach( $statement as $key=>$value ){
        $sok0 = preg_replace( $sm0, '',$statement[$key] );
        $sok1 = preg_replace( $sm1, '',$sok0 );
        $rc=$GLOBALS['dbh']->query( $sok1 );
    }
    $sql="insert into schema_info (tag, login) values ('crm_telefon_integration', '".$_SESSION['login'].")'";
    $rc=$GLOBALS['dbh']->query($sql);
}

function getCallListComplete(){
    $sql = "SELECT json_agg( json_calls ) FROM ( SELECT EXTRACT(EPOCH FROM TIMESTAMPTZ(crmti_init_time)) AS call_date, crmti_status, crmti_src, crmti_dst, crmti_caller_id, crmti_caller_typ, crmti_direction, crmti_number  FROM crmti ORDER BY crmti_init_time DESC) AS json_calls";
    $rs = $GLOBALS['dbh']->getone( $sql );
    echo $rs['json_agg'];
    return 1;
}

function getLastCall(){
    return 'lastItem';
}

function numberToAdress( $myarray  ){ //Holt mit $nummer Daten aus dem öffentlichem Telefonverzeichnis
    /*
    $klicktelKey = $_SESSION['klicktel_key'];
    writeLog( 'KlicktelKey: '.$_SESSION['klicktel_key'] );
    $url = "http://openapi.klicktel.de/searchapi/invers?key=";
    $url .= $klicktelKey;
    $url .= "&number=";
    $url .= $myarray['data'];

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
        $entry['greeting'] = 'Firma';
        $entry['name']     = $first_entry['displayname'];
    }
    else {
        $space_name = ( $first_entry['firstname'] && $first_entry['lastname'] ) ? ' ' : '';
        $entry['greeting'] = $first_entry['salutation'];
        $entry['name']  = $first_entry['firstname'].$space_name.$first_entry['lastname'];
    }
    $entry['firstname']    = $first_entry['firstname'];
    $entry['lastname']     = $first_entry['lastname'];
    $space_street          = $first_entry['location']['streetnumber'] ? ' ' : '';
    $entry['backlink']     = $first_entry['backlink'];
    $entry['street']       = $first_entry['location']['street'].$space_street.$first_entry['location']['streetnumber'];
    $entry['zipcode']      = $first_entry['location']['zipcode'];
    $entry['city']         = $first_entry['location']['city'];
    $entry['phone']        = $_GET['data'];
    if( $objResult['response']['results']['0'] ) echo json_encode( $entry );
    */

    $number = $myarray['data'];
    $result = array();
    $url = "http://www.dasoertliche.de/Controller?form_name=search_inv&ph=$number";

    $dom = new DOMDocument();
    $dom->loadHTMLFile( $url );

    if ($dom->documentURI == null){
        writeLog( 'Datei: '.__file__.' Zeile: '.__line__.'Timeout bei Abruf der Webseite '.$url );
        return false;
    }
    $finder = new DomXPath( $dom );

    $nodes = $finder->query( '//span[@class="st-treff-name"]' );
    $result['name'] = $nodes->item( 0 )->nodeValue;
    $nodes = $finder->query( '//span[@class="st-rufnr-nm"]' );
    $result['mobile'] = trim( substr( $nodes->item( 1 )->nodeValue, 7 ) );
    $result['mobile'] = str_replace( ' ', $tmpMobile,  $result['mobile'] );

    $adressNodeList = $dom->getElementsByTagName( 'address' );
    $adress = $adressNodeList->item( 0 )->nodeValue;
    $commaPos = strpos( $adress, ',' );
    $result['street'] = trim( substr( $adress, 0, $commaPos ) );
    $result['zipCode'] = trim( substr( $adress, $commaPos + 1, 8 ) );
    $result['city'] = trim( substr( $adress, $commaPos + 9 ) );

    echo json_encode( $result );
}
?>