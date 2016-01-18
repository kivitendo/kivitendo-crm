<?php
/*************************************************************************************************
*** connection.php:
*** 1. liest die ERP-Config aus readERPConfig() und speichert die Variablen und deren Werte in einem
***    assziativen Array erpConfig in der Session (nur wenn der Array erpConfig nicht vorhanden ist)
*** 2. erzeugt zwei DB-Handle dbh und authdbh
*** 3.
*** 4.
**************************************************************************************************/
require_once "phpDataObjects.php";
require_once "stdLib.php";
if( !varExist( $_SESSION ) ) session_start();
//printArray( $_SESSION );
if( !varExist( $_SESSION['globalConfig'] ) ) $_SESSION['globalConfig'] = getGlobalConfig(); //printArray(getGlobalConfig());
$_SESSION['erppath'] =& $_SESSION['globalConfig']['erppath'];//ToDO: delete
$_SESSION['crmpath'] =& $_SESSION['globalConfig']['crmpath'];//ToDO: delete
$_SESSION['baseurl'] =& $_SESSION['globalConfig']['baseurl'];//ToDO: delete
//printArray( "Baseurl: ".$_SESSION['baseurl']);

//printArray( $_SESSION['erpConfig'] );
//if( !varExist( $_SESSION['erpConfig'] ) ){ //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Funktioniert nicht
if( TRUE ){
    $erpConfigFile = file_exists( $_SESSION['erppath'].'/config/kivitendo.conf' ) ? $_SESSION['erppath'].'/config/kivitendo.conf' : $_SESSION['erppath'].'/config/kivitendo.conf.default';
    if( $erpConfigFile ) $_SESSION['erpConfig'] = configFile2array( $erpConfigFile );
}
//printArray( "name: ".$_SESSION['erpConfig']['authentication']['cookie_name'] );
$_SESSION['sessid'] = $_COOKIE[$_SESSION['erpConfig']['authentication']['cookie_name']];
//printArray( $_SESSION['sessid'] );
//$_SESSION['cookie'] =& $_SESSION['erpConfig']['authentication']['cookie_name'];
$_SESSION['sesstime'] =& $_SESSION['erpConfig']['authentication']['session_timeout'];


$conf_auth_db = $_SESSION['erpConfig']['authentication/database'];
$dbh_auth = new myPDO ($conf_auth_db['host'], $conf_auth_db['port'], $conf_auth_db['db'], $conf_auth_db['user'], $conf_auth_db['password'], $_SESSION["sessid"] );


if( !varExist( $_SESSION['userConfig'] ) );// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Funktioniert nicht
$_SESSION['userConfig'] = getUserConfig(); //printArray( getUserConfig());



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


if( !varExist( $_SESSION['dbData'] ) ); // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! Funktioniert nicht
$_SESSION['dbData'] = getDbData();   //printArray( getDbData());
$_SESSION['manid'] =& $_SESSION['dbData']['manid'];//ToDO: delete
$_SESSION['mandant'] =& $_SESSION['dbData']['mandant'];//ToDO: delete
$_SESSION['dbhost'] =& $_SESSION['dbData']['dbhost'];// Das muss weg !

$dbData = $_SESSION['dbData'];
//printArray( $dbData );
//printArray($_SESSION["sessid"]);
$dbh = new myPDO( $dbData["dbhost"], $dbData["dbport"], $dbData["dbname"], $dbData["dbuser"], $dbData["dbpasswd"], $_SESSION["sessid"] );

if( !varExist( $_SESSION['menu'] ) ) $_SESSION["menu"]  = makeMenu();

if( !varExist( $_SESSION['crmUserData'] ) ) $_SESSION["crmUserData"] = getCrmUserData();//ToDo: deprecated!

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
    $rs['baseurl'] = $baseUrl;

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
    //echo "getUserConfig";
    $sql  = "select u.id, u.login from auth.session_content sc left join auth.\"user\" u on ";
    $sql .= "(E'--- ' || u.login || chr(10) )=sc.sess_value left join auth.session s on s.id=sc.session_id ";
    $sql .= "where session_id = '".$_SESSION['sessid']."' and sc.sess_key='login'";
    $rs   = $GLOBALS['dbh_auth']->getAll( $sql );
    //echo count($rs);
    if ( count($rs) != 1 ) { // Garnicht an ERP angemeldet oder zu viele Sessions
        header( "location:".$_SESSION['baseurl']."controller.pl?action=LoginScreen/user_login" );
        unset($_SESSION);
    }
    $userConfig = $rs[0];
    $sql = "select * from auth.user_config where user_id=".$rs[0][id];
    $rs = $GLOBALS['dbh_auth']->getAll( $sql );
    foreach ( $rs as $row ) $userConfig[$row["cfg_key"]] = $row["cfg_value"];
    $userConfig["stylesheet"] = substr( $userConfig["stylesheet"], 0, -4 );
    //Müssen die folgenden beiden Zeilen nicht in außerhalb dieser Funktion stehen??  bei jedem Durchlauf oder wenn die Session abgelaufen war??
    $sql = "update auth.session set mtime = '".date("Y-M-d H:i:s.100001")."' where id = '".$_SESSION['sessid']."'";
    $rs =  $GLOBALS['dbh_auth']->query($sql);
    //Token lesen
    $sql = "SELECT * FROM auth.session WHERE id = '".$_SESSION['sessid']."'";
    $rs =  $GLOBALS['dbh_auth']->getOne($sql);
    $userConfig['token'] = $rs;
    //Welcer Mandant ist verbunden
    $sql  = "SELECT sess_value FROM auth.session_content WHERE session_id = '".$_SESSION['sessid']."' and sess_key='client_id'";
    $rs   = $GLOBALS['dbh_auth']->getOne( $sql );
    $userConfig['client_id'] = substr( $rs['sess_value'], 4 );
    //ERP users !!!!!!kann alles in Funktionen gepackt werden. muus nicht mit der Session rumgeschleppt werden
    //$sql = "SELECT usr.id AS id, usr.login, usrc.cfg_value AS name FROM auth.user AS usr ";
    //$sql .= "INNER JOIN auth.user_config AS usrc ON usr.id = usrc.user_id INNER JOIN auth.clients_users AS cliusr ON usr.id = cliusr.user_id ";
    //$sql .= "WHERE usrc.cfg_key = 'name' AND cliusr.client_id = '".$userConfig['client_id']."' ORDER by usr.id";
    //$rs = $GLOBALS['dbh_auth']->getAll( $sql );
    //$userConfig['all_erp_users'] = $rs;
    //ERP groups
    //$sql = "SELECT grp.id AS id, grp.name AS name FROM auth.group AS grp ";
    //$sql .= "INNER JOIN auth.clients_groups AS cligrp ON grp.id = cligrp.group_id WHERE cligrp.client_id = '".$userConfig['client_id']."' ORDER by grp.id";
    //$rs = $GLOBALS['dbh_auth']->getAll( $sql );
    //$userConfig['all_erp_groups'] = $rs;
    //ERP assignments
    //$sql= "SELECT usrg.user_id AS user_id, usrg.group_id AS group_id FROM auth.user_group AS usrg ORDER by usrg.user_id";
    //$rs = $GLOBALS['dbh_auth']->getAll( $sql );
    //$userConfig['all_erp_assignments'] = $rs;

    //printArray( $userConfig );
    //$user
    return $userConfig;
}

function getDbData(){
    //echo "getDbData";
    $sql  = 'SELECT id as manid,name as mandant,dbhost,dbport,dbname,dbuser,dbpasswd FROM auth.clients WHERE id = '.$_SESSION['userConfig']['client_id'];
    //echo "SQL: ".$sql;
    $rs = $GLOBALS['dbh_auth']->getOne( $sql );
    $rs['dbpasswd'] = base64_encode( @openssl_encrypt( $rs['dbpasswd'],'AES128', $_SESSION['sessid'] ) );
    return $rs;
}

function makeMenu(){
    //echo "MakeMenueX";
    if( !function_exists( 'curl_init' ) ){
        die( 'Curl (php5-curl) ist nicht installiert!' );
    }
    if ( !isset($_SESSION['ERP_BASE_URL']) || $_SESSION['ERP_BASE_URL'] == '' ){
        $BaseUrl  = (empty( $_SERVER['HTTPS'] )) ? 'http://' : 'https://';
        $BaseUrl .= $_SERVER['HTTP_HOST'];
        $BaseUrl .= preg_replace( "^crm/.*^", "", $_SERVER['REQUEST_URI'] );
    } else {
        $BaseUrl = $_SESSION['ERP_BASE_URL'];
    }
    $url = $_SESSION['baseurl'].'controller.pl?action=Layout/empty&format=json';
    $ch = curl_init();
    //printArray( $url);
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_TIMEOUT, 1 );
    curl_setopt( $ch, CURLOPT_ENCODING, 'gzip,deflate' );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array (
                "Connection: keep-alive",
                "Cookie: ".$_SESSION["cookie"]."=".$_SESSION['sessid']."; ".$_SESSION["cookie"]."_api_token=".$_SESSION["token"]['api_token']
                ));

    $result = curl_exec( $ch );


    if( $result === false || curl_errno( $ch )){
        die( 'Curl-Error: ' .curl_error($ch).' </br> $ERP_BASE_URL in "inc/conf.php" richtig gesetzt??' );
    }
    curl_close( $ch );
    $objResult = json_decode( $result );
    //if (!is_object($objResult)) anmelden();
    $_arr = get_object_vars($objResult);
    $rs['javascripts']   = '';
    $rs['stylesheets']   = '';
    $rs['pre_content']   = '';
    $rs['start_content'] = '';
    $rs['end_content']   = '';
    if ($objResult) {
        //echo "<pre>";
        //print_r($objResult->{'javascripts'});
        //echo "</pre";
        foreach($objResult->{'javascripts'} as $js) {//<script type="text/javascript" src="http://localhost/kivitendo-dev/crm/jquery/jquery-ui.min.js"></script>
            //jQuery und UI der ERP benützen
            //$rs['javascripts'] .= '<script type="text/javascript" src="'.$_SESSION['baseurl'].$js.'"></script>'."\n".'   ';
            //Da die ERP eine veraltete JUI benützt, aktuelle JUI aus CRM laden
            //ToDo: JUI aus ERP laden wenn diese >= Version 11.4 wird
            //Achtung!: JUI wird von der ERP nur geliefert wenn fast alle Module aktiviert sind (Menü)
            if( strpos( $js, "jquery-ui")  === false ) $rs['javascripts'] .= '<script type="text/javascript" src="'.$_SESSION['baseurl'].$js.'"></script>'."\n".'   ';
            $rs['javascripts'] .= '<script type="text/javascript" src="'.$_SESSION['baseurl'].'crm/jquery/jquery-ui.min.js"></script>'."\n".'   ';;
        }
        foreach($objResult->{'stylesheets'} as $style) {
            if ($style) $rs['stylesheets'] .= '<link rel="stylesheet" href="'.$_SESSION['baseurl'].$style.'" type="text/css">'."\n".'   ';
        }
        foreach($objResult->{'stylesheets_inline'} as $style) {
            if ($style) $rs['stylesheets'] .= '<link rel="stylesheet" href="'.$_SESSION['baseurl'].$style.'" type="text/css">'."\n".'   ';
        }
        $suche = '^([/a-zA-Z_0-9]+)\.(pl|php|phtml)^';
        $ersetze = $_SESSION['baseurl'].'${1}.${2}';
        $tmp = preg_replace($suche, $ersetze, $objResult->{'pre_content'} );
        $tmp = str_replace( 'itemIcon="', 'itemIcon="'.$_SESSION['baseurl'], $tmp );
        $rs['pre_content']   = str_replace( 'src="', 'src="'.$_SESSION['baseurl'], $tmp );
        $rs['start_content'] = $objResult->{'start_content'};
        $rs['start_content_ui'] = '<div class="ui-widget-content">';//Begin UI-Look
        $rs['end_content']   = $objResult->{'end_content'};
        $rs['end_content']  .= '<script type="text/javascript">';
        $rs['end_content']  .= " \n";
        $users_groups = [
            "erp_all_users" => $_SESSION['all_erp_users'],
            "erp_all_groups" => $_SESSION['all_erp_groups']
        ];
        $myglobal = $users_groups;
        $myglobal['baseurl'] = $_SESSION['baseurl'];//foreach( $elem, ...
        $myglobalJson = json_encode($myglobal, JSON_UNESCAPED_UNICODE);
        $rs['end_content'] .= 'kivi.global = '.$myglobalJson.";";

        //Inline-JS der ERP in den Footer (nach end_content)
        foreach($objResult->{'javascripts_inline'} as $js) {
            $js = preg_replace($suche, $ersetze,$js);
            $rs['end_content'] .= $js; //'<script type="text/javascript" src="'.$BaseUrl.$js.'"></script>'."\n".'   ';
        }
        $rs['end_content'] .= '</script>'."\n";
        $rs['end_content_ui']   = '</div>'; //End UI-Look
    }
    return $rs;
}

function getCrmUserData(){
    require_once "UserLib.php";
    $user_data = getUserStamm(0,$_SESSION["login"]);
    if ($user_data) foreach ($user_data as $key => $val) $_SESSION[$key] = $val;
    $user_data['dir_mode']  = ( $user_data['dir_mode'] != '' )?octdec($user_data['dir_mode']):493; // 0755
    $user_data["loginCRM"] = $user_data["id"];
    $user_data['theme']    = ($user_data['theme']=='' || $user_data['theme']=='base')?'':$user_data['theme'];
    return $user_data;
}

//echo "connections";