<?php
/*************************************************************************************************
*** connection.php:
*** 1. liest die ERP-Config aus readERPConfig() und speichert die Variablen und deren Werte in einem
***    assziativen Array erpConfig in der Session (nur wenn der Array erpConfig nicht vorhanden ist) 
*** 2. erzeugt zwei DB-Handle createDbHandle() userdbh und authdbh
*** 3. 
*** 4.
**************************************************************************************************/

if( !varExist( $_SESSION['erpConfig'] ) ){
//if( TRUE ){
    $erpConfigFile = file_exists( $_SESSION['erppath'].'/config/kivitendo.conf' ) ? $_SESSION['erppath'].'/config/kivitendo.conf' : $_SESSION['erppath'].'/config/kivitendo.conf.default';
    if( $erpConfigFile ) $_SESSION['erpConfig'] = configFile2array( $erpConfigFile );
}

$_SESSION['sessid'] = $_COOKIE[$_SESSION['erpConfig']['authentication']['cookie_name']];
//printArray( $_SESSION['erpConfig']);
global $dbh;
if( varExist( $_SESSION['sessid'] ) ) $dbh = new myPDO( $_SESSION["dbhost"], $_SESSION["dbport"], $_SESSION["dbname"], $_SESSION["dbuser"], $_SESSION["dbpasswdcrypt"], $_SESSION["sessid"] ); 


function configFile2array( $file ) {
    $str = file_get_contents( $file );
    if( empty( $str ) ) return false;
    $lines = explode( "\n", $str );
    $ret = array();
    $inside_section = false;

    foreach( $lines as $line ){
        $line = trim( $line );
        if( !$line || $line[0] == "#" ) continue;
        if( $line[0] == "[" && $endIdx = strpos( $line, "]" ) ){
            $inside_section = substr( $line, 1, $endIdx-1 );
            continue;
        }
        if( !strpos( $line, '=' ) ) continue;
        $tmp = explode( "=", $line, 2 );
        if( $inside_section ){
            $key = rtrim( $tmp[0] );
            $value = ltrim( $tmp[1] );
            if( preg_match( "/^\".*\"$/", $value ) || preg_match( "/^'.*'$/", $value ) ){
                $value = mb_substr( $value, 1, mb_strlen( $value ) - 2 );
            }
            $t = preg_match( "^\[(.*?)\]^", $key, $matches );
            if( !empty( $matches ) && isset( $matches[0] ) ){
                $arr_name = preg_replace( '#\[(.*?)\]#is', '', $key );
                if( !isset( $ret[$inside_section][$arr_name] ) || !is_array( $ret[$inside_section][$arr_name] ) ){
                    $ret[$inside_section][$arr_name] = array();
                }
                if( isset( $matches[1] ) && !empty( $matches[1] ) ) $ret[$inside_section][$arr_name][$matches[1]] = $value;
                else $ret[$inside_section][$arr_name][] = $value;
            }
            else $ret[$inside_section][trim( $tmp[0] )] = $value;
        } 
        else $ret[trim( $tmp[0] )] = ltrim( $tmp[1] );
    }
    foreach( $ret as $key0 =>$values ) //encrypt passwd
        foreach( $values as $key1 => $value )
            if( strpos( $key1, 'password' ) !== FALSE ) $ret[$key0][$key1] = base64_encode( @openssl_encrypt( $value,'AES128', $_COOKIE[$ret['authentication']['cookie_name']] ) ); 
 
    return $ret;
}

//echo "connections";