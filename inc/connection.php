<?php
/*************************************************************************************************
*** Konfigurationsdatei -> Session, Db-handle erstellen, User-Date -> Session, Menu -> Session ***
**************************************************************************************************/

require_once __DIR__.'/phpDataObjects.php';
require_once __DIR__.'/stdLib.php';
require_once __DIR__.'/conf.php';
require_once __DIR__.'/version.php';

if( !varExist( $_SESSION ) ) session_start();

if( !varExist( $_SESSION, 'globalConfig' ) ) $_SESSION['globalConfig'] = getGlobalConfig(); //printArray(getGlobalConfig());

//Prüfen ob es sich um eine neu Session handelt oder die Elemente von $_SESSION gelöscht wurden
$newSession = ( $_SESSION['sessid'] != $_COOKIE[$_SESSION['erpConfig']['authentication']['cookie_name']] ) || $_SESSION['clear'] || !$_SESSION['menu']['javascripts'];//wo wird menu.javascripts zerstört??

$_SESSION['erppath'] =& $_SESSION['globalConfig']['erppath'];//ToDO: delete??
$_SESSION['crmpath'] =& $_SESSION['globalConfig']['crmpath'];//ToDO: delete??
$_SESSION['baseurl'] =& $_SESSION['globalConfig']['baseurl'];//ToDO: delete??

//Kivitendo Configfile in Array erpConfig speichern
if( $newSession ){
    $erpConfigFile = file_exists( $_SESSION['erppath'].'/config/kivitendo.conf' ) ? $_SESSION['erppath'].'/config/kivitendo.conf' : $_SESSION['erppath'].'/config/kivitendo.conf.default';
    if( $erpConfigFile ) $_SESSION['erpConfig'] = configFile2array( $erpConfigFile );
    else die( 'kivitendo configfile not found!' );
}

//Neue Session-Id speichern
if( $newSession ) $_SESSION['sessid'] = $_COOKIE[$_SESSION['erpConfig']['authentication']['cookie_name']];

$_SESSION['cookie'] =& $_SESSION['erpConfig']['authentication']['cookie_name'];
$_SESSION['sesstime'] =& $_SESSION['erpConfig']['authentication']['session_timeout'];

//DB-handle auth erzeugen
$conf_auth_db =& $_SESSION['erpConfig']['authentication/database'];
if( $conf_auth_db['host'] ) $dbh_auth = new myPDO ($conf_auth_db['host'], $conf_auth_db['port'], $conf_auth_db['db'], $conf_auth_db['user'], $conf_auth_db['password'], $_SESSION["sessid"] );
else {
    /*printArray( '$conf_auth_db[host] is empty!!!!' ); */
    delcurrentSess();
}

//(ERP)-Userdaten in Session speichern
if( $newSession ) $_SESSION['userConfig'] = getUserConfig(); //printArray( getUserConfig());

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

//Daten fürs DB-handle in Session
if( $newSession ) $_SESSION['dbData'] = getDbData();   //printArray( getDbData());

$_SESSION['manid'] =& $_SESSION['dbData']['manid'];//ToDO: delete
$_SESSION['mandant'] =& $_SESSION['dbData']['mandant'];//ToDO: delete
//$_SESSION['dbhost'] =& $_SESSION['dbData']['dbhost'];// Das muss weg!

//Db-handle erzeugen
$dbData =& $_SESSION['dbData'];
if( $dbData["dbhost"] )
$dbh = new myPDO( $dbData["dbhost"], $dbData["dbport"], $dbData["dbname"], $dbData["dbuser"], $dbData["dbpasswd"], $_SESSION["sessid"] );
else echo 'No $_SESSION[dbData][dbhost]';
//$db = &$dbh; //Open Konto kompatibel bleiben  wo wird $db zerstört?????

//Menu und Javascript-Sachen in Session speichern
if( $newSession ) {
    // global - ERP users, groups in kivi.myconfig laden
    $users_groups = [
            "erp_all_users" => getAllERPusers(),
            "erp_all_groups" => getAllERPgroups()
        ];
    $myglobal = $users_groups;
    $myglobal['baseurl'] = substr($_SESSION['baseurl'], 0, -1);//warum -1 Url darf doch Slash am Ende Kevin oder was steht in Session.baseurl
    $myglobalJson = json_encode($myglobal, JSON_UNESCAPED_UNICODE);
    $id = $_SESSION['userConfig']['id'];
    $sql  = "select * from auth.user_config where user_id = '".$id."' and cfg_key = 'global_conf'";
    $rs   = $GLOBALS['dbh_auth']->getAll( $sql );
    if(empty($rs)) {
        $sql = "insert into auth.user_config (user_id,cfg_key,cfg_value) values ('".$id."','global_conf','".$myglobalJson."')";
        $rs = $GLOBALS['dbh_auth']->query ( $sql );
    }
    else {
        $sql="update auth.user_config set cfg_value = '".$myglobalJson."' where user_id = '".$id."' and cfg_key = 'global_conf'";
        $rs=$GLOBALS['dbh_auth']->query ( $sql );
    }
    $_SESSION["menu"]  = makeMenu();//warum geht das menu verloren?? warum MUSS "OR !varExist( $_SESSION, 'menu'" )

    checkInstallation();
}

//Vorerst Userdaten der CRM in crmUserData speichern !!besser in userConfig speichern
if( $newSession ) $_SESSION["crmUserData"] = getCrmUserData();//ToDo: deprecated!
$_SESSION['loginCRM'] =& $_SESSION['crmUserData']['loginCRM'];//ToDO: delete

//ERP Users in die auth.user_config eintragen als JSON
// Extra Funktion anlegen ?
if( $newSession && needUpdate() );// header( 'Location:'.$_SESSION['baseurl'].'crm/status.php?action=needUpdate' );

//Die Session-Variable ist nun gefüllt
$_SESSION['clear'] = FALSE;

//printArray( $_SESSION );

function configFile2array( $file ){
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
        $tmp = explode('/crm',$_SERVER['SCRIPT_FILENAME']);
        $basepath = $tmp[0];
    } else if ( isset($_SERVER['DOCUMENT_ROOT']) ) {
    $basepath = $_SERVER['DOCUMENT_ROOT'];
    } else if ( substr($ERPNAME,0,1) == '/' ) {
        $basepath = $ERPNAME;
    } else {
    echo "Basispfad konnte nicht ermittelt werden.<br>";
    echo 'Bitte in "$ERPNAME" in inc/conf.php den absoluten Pfad eintragen.';

    }
    $basepath = substr($basepath, -1) == '/' ? substr($basepath,0,-1) : $basepath; //
    //echo $basepath; //Pfade dürfen kein Slash am Ende haben
    $rs['erppath'] = $basepath;
    $rs['crmpath'] = $rs['erppath'] .'/crm';
    $inclpa = ini_get('include_path');
    ini_set('include_path',$inclpa.":../:./inc:../inc");//ToDo kann doch raus?? Ist es nicht besser __DIR__ zu benützen??
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
    if( !function_exists( 'curl_init' ) ){
        die( 'Curl (php5-curl) ist nicht installiert!' );
    }
    $url = $_SESSION['baseurl'].'controller.pl?action=Layout/empty&format=json';
    $ch = curl_init();
    //printArray( $url);;
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
    $_arr = get_object_vars( $objResult );
    $rs['javascripts']   = '';
    $rs['stylesheets']   = '';
    $rs['pre_content']   = '';
    $rs['start_content'] = '';
    $rs['end_content']   = '';
    if ($objResult) {
        //echo "<pre>";
        //print_r($objResult->{'javascripts'});
        //echo "</pre";
        $rs['javascripts'] .= '<script type="text/javascript" src="'.$_SESSION['baseurl'].'crm/nodejs/node_modules/jquery/dist/jquery.min.js"></script>'."\n".'   ';
        $rs['javascripts'] .= '<script type="text/javascript" src="'.$_SESSION['baseurl'].'crm/jquery/jquery-ui.min.js"></script>'."\n".'   ';

        foreach($objResult->{'javascripts'} as $js)
            if( strpos( $js, "jquery")  === false ) $rs['javascripts'] .= '<script type="text/javascript" src="'.$_SESSION['baseurl'].$js.'"></script>'."\n".'   ';

        foreach($objResult->{'stylesheets'} as $style)
            if ($style) $rs['stylesheets'] .= '<link rel="stylesheet" href="'.$_SESSION['baseurl'].$style.'" type="text/css">'."\n".'   ';

        foreach($objResult->{'stylesheets_inline'} as $style)
            if ($style) $rs['stylesheets'] .= '<link rel="stylesheet" href="'.$_SESSION['baseurl'].$style.'" type="text/css">'."\n".'   ';

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
        // Kann der Teil raus? da global jetzt in kivi.myconfig steht ?
        /*$users_groups = [
            "erp_all_users" => getAllERPusers(),
            "erp_all_groups" => getAllERPgroups()
        ];
        $myglobal = $users_groups;
        $myglobal['baseurl'] = $_SESSION['baseurl'];//foreach( $elem, ...
        $myglobalJson = json_encode($myglobal, JSON_UNESCAPED_UNICODE);
        $rs['end_content'] .= 'kivi.global = '.$myglobalJson.";";
        */
        //Inline-JS der ERP in den Footer (nach end_content)
        foreach($objResult->{'javascripts_inline'} as $js) {
            $js = preg_replace($suche, $ersetze,$js);
            $rs['end_content'] .= $js; //'<script type="text/javascript" src="'.$BaseUrl.$js.'"></script>'."\n".'   ';
        }
        $rs['end_content'] .= '</script>'."\n";
        $rs['end_content_ui']   = '</div>'; //End UI-Look
    }

    /*****************************************
   *
   * Bugfix Menue verschwindet aus Session
   *
   *
   *******************************************/
    if ( $rs['javascripts'] == '' ) delcurrentSess();
    return $rs;
}

function getCrmUserData(){
    require_once __DIR__.'/UserLib.php';
    $user_data = getUserStamm(0,$_SESSION["login"]);
    if ($user_data) foreach ($user_data as $key => $val) $_SESSION[$key] = $val;
    $user_data['dir_mode']  = ( $user_data['dir_mode'] != '' )?octdec($user_data['dir_mode']):493; // 0755
    $user_data["loginCRM"] = $user_data["id"];
    $user_data['theme']    = ($user_data['theme']=='' || $user_data['theme']=='base')?'':$user_data['theme'];
    return $user_data;
}

function delcurrentSess(){
    $url = $_SERVER['REQUEST_URI'];
    $baseurl = substr($_SESSION['baseurl'], 0, -1);
    echo '<script type="text/javascript">window.location.href="'.$baseurl.'crm/delsess.php?url='.$url.'";</script>';
    return;
}

function checkInstallation(){  //*** Prüft ob die Tabelle crm installiert ist und installiert ggf alle nötigen Tabellen
    $rs = $GLOBALS['dbh']->getOne( "SELECT EXISTS  ( SELECT 1 FROM   information_schema.tables WHERE  table_schema = 'public' AND  table_name = 'crm') AS crm_exist " );
    if( !$rs['crm_exist'] ){  //Installation beginnt
        $instFileArray = file( __DIR__.'/../db_install/installation_01.sql',  FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES  );//Array mit Lines aus Installations-SQL-File laden
        $sql = '';
        $comment_patterns = array(
        '/\/\*.*(\n)*.*(\*\/)?/', //C comments
        '/\s*--.*\n/',            //inline comments start with --
        '/\s*#.*\n/',             //inline comments start with #
        );
         $GLOBALS['dbh']->begin();
        foreach( $instFileArray as $key => $line ){
            $line = preg_replace( $comment_patterns, "", $line ); //remove comments
            $sql .= $line;
            if( strpos( $line, ';' ) !== FALSE ){
                //$sql = preg_replace( $comment_patterns, "", $sql );
                //printArray( $sql );
                if( $GLOBALS['dbh']->exec( $sql ) === FALSE ){
                    writeLog( $sql );
                    echo "SQL-Error in installation_01.sql";
                    return;
                }
                $sql = '';
            }
        }
        $GLOBALS['dbh']->exec( "INSERT INTO crm (uid,datum,version) VALUES (0,now(), '".VERSION."')" );
        $GLOBALS['dbh']->commit();
    }
}
