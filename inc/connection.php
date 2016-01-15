<?php
/*************************************************************************************************
*** connection.php:
*** 1. liest die ERP-Config aus readERPConfig() und speichert die Variablen und deren Werte in einem
***    assziativen Array erpConfig in der Session (nur wenn der Array erpConfig nicht vorhanden ist)
*** 2. erzeugt zwei DB-Handle createDbHandle() userdbh und authdbh
*** 3.
*** 4.
**************************************************************************************************/

if( !varExist( $_SESSION['globalConfig'] ) ) $_SESSION['globalConfig'] = getGlobalConfig(); //printArray(getGlobalConfig());
$_SESSION['erppath'] =& $_SESSION['globalConfig']['erppath'];//ToDO: delete
$_SESSION['crmpath'] =& $_SESSION['globalConfig']['crmpath'];//ToDO: delete
$_SESSION['baseurl'] =& $_SESSION['globalConfig']['baseUrl'];//ToDO: delete


//if( !varExist( $_SESSION['erpConfig'] ) ){
if( TRUE ){
    $erpConfigFile = file_exists( $_SESSION['erppath'].'/config/kivitendo.conf' ) ? $_SESSION['erppath'].'/config/kivitendo.conf' : $_SESSION['erppath'].'/config/kivitendo.conf.default';
    if( $erpConfigFile ) $_SESSION['erpConfig'] = configFile2array( $erpConfigFile );
}

$_SESSION['sessid'] =& $_COOKIE[$_SESSION['erpConfig']['authentication']['cookie_name']];
//$_SESSION['cookie'] =& $_SESSION['erpConfig']['authentication']['cookie_name'];
//$_SESSION['sesstime'] =& $_SESSION['erpConfig']['authentication']['session_timeout'];
//printArray( $_SESSION);

$conf_auth_db = $_SESSION['erpConfig']['authentication/database'];
$dbh_auth = new myPDO ($conf_auth_db['host'], $conf_auth_db['port'], $conf_auth_db['db'], $conf_auth_db['user'], $conf_auth_db['password'], $_SESSION["sessid"] );


if( !varExist( $_SESSION['userConfig'] ) )   $_SESSION['userConfig']   = getUserConfig(); //printArray( getUserConfig());
$_SESSION['token'] =& $_SESSION['userConfig']['token'];//ToDO: delete
$_SESSION['login'] =& $_SESSION['userConfig']['login'];//ToDO: delete
$_SESSION['fax'] =& $_SESSION['userConfig']['fax'];//ToDO: delete
$_SESSION['email'] =& $_SESSION['userConfig']['email'];//ToDO: delete
$_SESSION['signature'] =& $_SESSION['userConfig']['signature'];//ToDO: delete
$_SESSION['name'] =& $_SESSION['userConfig']['name'];//ToDO: delete
$_SESSION['vclimit'] =& $_SESSION['userConfig']['vclimit'];//ToDO: delete
$_SESSION['countrycode'] =& $_SESSION['userConfig']['countrycode'];//ToDO: delete
$_SESSION['stylesheet'] =& $_SESSION['userConfig']['stylesheet'];//ToDO: delete
$_SESSION['tel'] =& $_SESSION['userConfig']['tel'];//ToDO: delete
$_SESSION['client_id'] =& $_SESSION['userConfig']['client_id'];//ToDO: delete
$_SESSION['all_erp_users'] =& $_SESSION['userConfig']['all_erp_users'];//ToDO: delete
$_SESSION['all_erp_groups'] =& $_SESSION['userConfig']['all_erp_groups'];//ToDO: delete
$_SESSION['all_erp_assingments'] =& $_SESSION['userConfig']['all_erp_assignments'];//ToDO: delete


if( !varExist( $_SESSION['dbData'] ) )       $_SESSION['dbData']       = getDbData();   //printArray( getDbData());
$_SESSION['manid'] =& $_SESSION['dbData']['manid'];//ToDO: delete
$_SESSION['mandant'] =& $_SESSION['dbData']['mandant'];//ToDO: delete
$_SESSION['dbhost'] =& $_SESSION['dbData']['dbhost'];// Das muss weg !

$dbData = $_SESSION['dbData'];

//printArray($dbh_auth->getAll( 'select * from auth.user_config' ));

//if( varExist( $_SESSION['sessid'] ) ) $dbh = new myPDO( $_SESSION["dbhost"], $_SESSION["dbport"], $_SESSION["dbname"], $_SESSION["dbuser"], $_SESSION["dbpasswdcrypt"], $_SESSION["sessid"] );
if( varExist( $_SESSION['sessid'] ) ) $dbh = new myPDO( $dbData["dbhost"], $dbData["dbport"], $dbData["dbname"], $dbData["dbuser"], $dbData["dbpasswd"], $_SESSION["sessid"] );

//$_SESSION["menu"]     = makeMenu($_SESSION["sessid"],$_SESSION["token"]);

//printArray( $_SESSION );

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

function getGlobalConfig(){
    $baseUrl = isset( $_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
    $baseUrl.= '://'.$_SERVER['SERVER_NAME'].preg_replace( "^crm/.*^", "", $_SERVER['REQUEST_URI'] );
    $rs['baseUrl'] = $baseUrl;
    if ( isset($_SERVER['CONTEXT_DOCUMENT_ROOT']) ) {
        $basepath = $_SERVER['CONTEXT_DOCUMENT_ROOT'];
    } else if ( isset($_SERVER['SCRIPT_FILENAME']) ) {
        $tmp = explode('crm',$_SERVER['SCRIPT_FILENAME']);
        $basepath = substr($tmp[0],0,-1);
    } else if ( isset($_SERVER['DOCUMENT_ROOT']) ) {
    $basepath = $_SERVER['DOCUMENT_ROOT'];
    } else if ( substr($ERPNAME,0,1) == '/' ) {
        $basepath = $ERPNAME;
    } else {
    echo "Basispfad konnte nicht ermittelt werden.<br>";
    echo 'Bitte in "$ERPNAME" in inc/conf.php den absoluten Pfad eintragen.';

    }
    $basepath = substr($basepath, -1) == '/' ? substr($basepath,0,-1) : $basepath;
    //echo $basepath; //Pfade dürfen kein Slash am Ende haben
    $rs['erppath'] = $basepath;
    $rs['crmpath'] = $rs['erppath'] .'/crm';
    $inclpa = ini_get('include_path');
    ini_set('include_path',$inclpa.":../:./inc:../inc");//ToDo kann doch raus?? Ist es nicht besser $_SESSION['crmpath'] zu benützen??
    return $rs;
}

function getUserConfig(){
    $sql  = "select u.id, u.login from auth.session_content sc left join auth.\"user\" u on ";
    $sql .= "(E'--- ' || u.login || chr(10) )=sc.sess_value left join auth.session s on s.id=sc.session_id ";
    $sql .= "where session_id = '".$_SESSION['sessid']."' and sc.sess_key='login'";
    $rs   = $GLOBALS['dbh_auth']->getAll( $sql );
    if ( count($rs) != 1 ) { // Garnicht an ERP angemeldet oder zu viele Sessions
        header( "location:".$_SESSION['baseurl']."controller.pl?action=LoginScreen/user_login" );
        unset($_SESSION);
    }
    $userConfig = $rs[0];
    $sql = "select * from auth.user_config where user_id=".$rs[0][id];
    $rs = $GLOBALS['dbh_auth']->getAll( $sql );
    foreach ( $rs as $row ) $userConfig[$row["cfg_key"]] = $row["cfg_value"];
    $userConfig["stylesheet"] = substr( $userConfig["stylesheet"], 0, -4 );
    //Token lesen
    $sql = "SELECT * FROM auth.session WHERE id = '".$_SESSION['sessid']."'";
    $rs =  $GLOBALS['dbh_auth']->getOne($sql);
    $userConfig['token'] = $rs;
    //Welcer Mandant ist verbunden
    $sql  = "SELECT sess_value FROM auth.session_content WHERE session_id = '".$_SESSION['sessid']."' and sess_key='client_id'";
    $rs   = $GLOBALS['dbh_auth']->getOne( $sql );
    $userConfig['client_id'] = substr( $rs['sess_value'], 4 ); 
    //ERP users
    $sql = "SELECT usr.id AS id, usr.login, usrc.cfg_value AS name FROM auth.user AS usr ";
    $sql .= "INNER JOIN auth.user_config AS usrc ON usr.id = usrc.user_id INNER JOIN auth.clients_users AS cliusr ON usr.id = cliusr.user_id ";
    $sql .= "WHERE usrc.cfg_key = 'name' AND cliusr.client_id = '".$userConfig['client_id']."' ORDER by usr.id";
    $rs = $GLOBALS['dbh_auth']->getAll( $sql );
    $userConfig['all_erp_users'] = $rs;
    //ERP groups
    $sql = "SELECT grp.id AS id, grp.name AS name FROM auth.group AS grp ";
    $sql .= "INNER JOIN auth.clients_groups AS cligrp ON grp.id = cligrp.group_id WHERE cligrp.client_id = '".$userConfig['client_id']."' ORDER by grp.id";
    $rs = $GLOBALS['dbh_auth']->getAll( $sql );
    $userConfig['all_erp_groups'] = $rs;
    //ERP assignments
    $sql= "SELECT usrg.user_id AS user_id, usrg.group_id AS group_id FROM auth.user_group AS usrg ORDER by usrg.user_id";
    $rs = $GLOBALS['dbh_auth']->getAll( $sql );
    $userConfig['all_erp_assignments'] = $rs;

    //printArray( $rs );
    //$user
    return $userConfig;
}

function getDbData(){
    $sql  = 'SELECT id as manid,name as mandant,dbhost,dbport,dbname,dbuser,dbpasswd FROM auth.clients WHERE id = '.$_SESSION['userConfig']['client_id'];
    $rs = $GLOBALS['dbh_auth']->getOne( $sql );
    $rs['dbpasswd'] = base64_encode( @openssl_encrypt( $rs['dbpasswd'],'AES128', $_SESSION['sessid'] ) );
    return $rs;
}
//echo "connections";