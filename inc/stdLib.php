<?php
ini_set('session.bug_compat_warn', 0);// Warnung für Sessionbug in neueren Php-Versionen abschalten.
ini_set('session.bug_compat_42', 0);  // Das ist natürlich lediglich eine Provirorische Lösung.
//Warning: Unknown: Your script possibly relies on a session side-effect which existed until PHP 4.2.3. ....
//session_set_cookie_params(480*60); //480*60
session_start();
//printArray( $_SESSION );
//writeLog( $_SESSION );

$_SESSION['erppath'] = substr($_SERVER['CONTEXT_DOCUMENT_ROOT'], 0, -1);//Pfade haben am Ende kein Slash
$_SESSION['crmpath'] = $_SESSION['erppath'] .'/crm';
$inclpa = ini_get('include_path');
ini_set('include_path',$inclpa.":../:./inc:../inc");

if ( isset($_SESSION['php_error']) && $_SESSION['php_error'] ) {
    error_reporting (E_ALL & ~E_DEPRECATED);
    ini_set ('display_errors',1);
}

include_once "mdb.php";
require_once "conf.php";

function printArray( $array ){
    echo '<pre>';
    print_r( $array );
    echo '</pre>';
}
//in terminal: tail -f tmp/log.txt
function writeLog( $log ){
    file_put_contents( $_SESSION['crmpath'].'/tmp/log.txt', date("Y-m-d H:i:s -> " ).print_r( $log, TRUE )."\n", FILE_APPEND );
}

if ( !isset($_SESSION['dbhost']) ) {
    if ( !isset($erpConfigFile) ) $erpConfigFile = $erp;
    $_SESSION['ERPNAME'] = $ERPNAME;
    $_SESSION['ERP_BASE_URL'] = $ERP_BASE_URL;
    $_SESSION['erpConfigFile'] = $erpConfigFile;
    require_once "version.php";
    $_SESSION['VERSION'] = $VERSION;
    require_once "login.php";
    //exit();
} else {
    if ( !isset($_SESSION["cookie"]) ||
         ( $_SESSION["sessid"] != $_COOKIE[$_SESSION["cookie"]] ) ) {
             while( list($key,$val) = each($_SESSION) ) {
                 unset($_SESSION[$key]);
             };
             $_SESSION['ERPNAME'] = $ERPNAME;
             $_SESSION['ERP_BASE_URL'] = $ERP_BASE_URL;
             $_SESSION['erpConfigFile'] = $erpConfigFile;
             require_once "version.php";
             $_SESSION['VERSION'] = $VERSION;
             if ( !anmelden() ) header("location: ups.html");
    };
};

require_once "login".$_SESSION["loginok"].".php";
//$db = $_SESSION['db']; // Das muß noch raus!!!


/****************************************************
* db2date
* in: Datum = String
* out: Datum = String
* wandelt ein db-Datum in ein "normales" Datum um
*****************************************************/
function db2date($datum) {
   if ( strpos($datum,"-") ) {
       $D = explode("-",$datum);
       $datum = sprintf ("%02d.%02d.%04d",$D[2],$D[1],$D[0]);
   }
   return $datum;
}

/****************************************************
* date2db
* in: Datum = String
* out: Datum = String
* wandelt ein "normales" Datum in ein db-Datum um
*****************************************************/
function date2db($Datum) {
   if ( empty($Datum) ) return date("Y-m-d");
   $repl = array ("/","-",","," ");
   $date = str_replace($repl,".",$Datum);
   $t = explode(".",$date);
   if ( checkdate($t[1],$t[0],$t[2]) ) {
      if ( $t[2] <= date('y') + 20 ) {
          $Datum = sprintf('20%02d-%02d-%02d',$t[2],$t[1],$t[0]);
      } else if ( $t[2] > 100 ) {
          $Datum = sprintf('%04d-%02d-%02d',$t[2],$t[1],$t[0]);
      } else {
          $Datum = sprintf('19%02d-%02d-%02d',$t[2],$t[1],$t[0]);
      }
   }
   return $Datum;
}

function translate($word,$file) {
    include("locale/$file.".$_SESSION['countrycode']);
    if ( $texts[$word] ) {
            return $texts[$word];
    } else {
        return $word;
    }
}
function chksesstime($dbhost,$dbport,$dbuser,$dbpasswd,$dbname,$session,$sesstime) {
    $db   = new myDB($dbhost,$dbuser,$dbpasswd,$dbname,$dbport);
    $sql  = "SELECT * FROM auth.session WHERE = '$session'";
    $rs   = $db->getOne($sql);

    return true;
}

function authuser($dbhost,$dbport,$dbuser,$dbpasswd,$dbname,$cookie) {
    $db   = new myDB($dbhost,$dbuser,$dbpasswd,$dbname,$dbport);
    //Hat sich ein User angemeldet
    $sql  = "select sc.session_id,u.id,u.login from auth.session_content sc left join auth.\"user\" u on ";
    $sql .= "(E'--- ' || u.login || chr(10) )=sc.sess_value left join auth.session s on s.id=sc.session_id ";
    $sql .= "where session_id = '$cookie' and sc.sess_key='login'";
    $rs   = $db->getAll($sql);
    if ( count($rs) != 1 ) { // Garnicht mit ERP angemeldet oder zu viele Sessions, sollte die ERP drauf achten
        unset($_SESSION);
        $Url = preg_replace( "^crm/.*^", "", $_SERVER['REQUEST_URI'] );
        header( "location:".$Url."controller.pl?action=LoginScreen/user_login" );
    }
    $auth = array();
    $uid = $rs[0]["id"];
    $auth["login"]      = $rs[0]["login"];
    $sql = "select * from auth.user_config where user_id=".$uid;
    $rs = $db->getAll($sql);
    $keys = array("countrycode","stylesheet","vclimit","signature","email","tel","fax","name");
    foreach ( $rs as $row ) {
        if ( in_array($row["cfg_key"],$keys) ) {
            $auth[$row["cfg_key"]] = $row["cfg_value"];
        }
    }
    $auth["stylesheet"] = substr($auth["stylesheet"],0,-4);
    //Welcer Mandant ist verbunden
    $sql  = "SELECT sess_value FROM auth.session_content WHERE session_id = '$cookie' and sess_key='client_id'";
    $rs   = $db->getOne($sql);
    $mandant = substr($rs['sess_value'],4);
    $sql  = 'SELECT id as manid,name as mandant,dbhost,dbport,dbname,dbuser,dbpasswd FROM auth.clients WHERE id = '.$mandant;
    $rs   = $db->getOne($sql);
    $auth = array_merge($auth,$rs);
    //$sql  = "SELECT id AS value, name AS text FROM auth.group";
    //Nur die Gruppen die den angemeldete Benutzer zugeordnet sind hinzufügen
    $sql="SELECT grp.id AS value, name AS text FROM auth.group AS grp
                   INNER JOIN auth.user_group
                     ON grp.id = group_id
                   INNER JOIN auth.user AS usr
                     ON user_id = usr.id
                   WHERE usr.id = $uid";
    $rs_grp = $db->getAll( $sql );
    $auth['grp'] = $rs_grp;
    //Eine der Gruppen des Users darf sales_all_edit
    $sql  = "SELECT granted from auth.group_rights G where G.right = 'sales_all_edit' ";
    $sql .= "and G.group_id in (select group_id from auth.user_group where user_id = ".$uid.")";
    $rs3 = $db->getAll($sql);
    $auth["sales_edit_all"] = 'f';
    if ( $rs3 ) {
        foreach ( $rs3 as $row ) {
             if ( $row["granted"] == 't' ) {
                   $auth["sales_edit_all"] = 't';
                   break;
              }
         }
    }
    // Ist der User ein CRM-Supervisor?
    $sql = "SELECT count(*) as cnt from auth.user_group left join auth.group on id=group_id where name = 'Admin' and user_id = ".$uid;
    $rs  = $db->getOne($sql);
    $auth['Admin'] = $rs['cnt'];
    //Session update
    $sql = "update auth.session set mtime = '".date("Y-M-d H:i:s.100001")."' where id = '".$cookie."'";
    $db->query($sql,"authuser_3");
    //Token lesen
    $sql = "SELECT * FROM auth.session WHERE id = '".$cookie."'";
    $rsa =  $db->getOne($sql);
    $auth['token'] = $rsa['api_token'];
    return $auth;
}

/****************************************************
* anmelden
* in: name,pwd = String
* out: rs = integer
* prueft ob name und kennwort in db sind und liefer die UserID
*****************************************************/
function anmelden() {
    ini_set("gc_maxlifetime","3600");
    global $ERPNAME; // ! das funzt nicht mit $_SESSION[ERPNAME] weil die Session in loginok.php zerstört wird...
    global $erpConfigFile;
    //Konfigurationsfile der ERP einlesen
    $deep = is_dir("../".$ERPNAME) ? "../" : "../../";                // anmelden() aus einem Unterverzeichnis
    if ( file_exists($deep.$ERPNAME."/config/".$erpConfigFile.".conf") ) {
        $lxo = fopen($deep.$ERPNAME."/config/".$erpConfigFile.".conf","r");
    } else if ( file_exists($deep.$ERPNAME."/config/".$erpConfigFile.".conf.default") ) {
        $lxo = fopen($deep.$ERPNAME."/config/".$erpConfigFile.".conf.default","r");
    } else {
        return false;
    }
    $dbsec = false;
    $tmp = fgets($lxo,512);
    //Parameter für die Auth-DB in der ERP-Konfiguration finden
    while ( !feof($lxo) ) {
        if ( preg_match("/^[\s]*#/",$tmp) || $tmp == "\n" ) { //Kommentar, ueberlesen
            $tmp = fgets($lxo,512);
            continue;
        }
        if ( $dbsec && preg_match("!\[.+]!",$tmp) ) $dbsec = false;
        if ( $dbsec ) {
            if ( preg_match("/db[ ]*=[ ]*(.+)/",$tmp,$hits) )       $dbname = $hits[1];
            if ( preg_match("/password[ ]*=[ ]*(.+)/",$tmp,$hits) ) $dbpasswd = $hits[1];
            if ( preg_match("/user[ ]*=[ ]*(.+)/",$tmp,$hits) )     $dbuser = $hits[1];
            if ( preg_match("/host[ ]*=[ ]*(.+)/",$tmp,$hits) )     $dbhost = ($hits[1])?$hits[1]:"localhost";
            if ( preg_match("/port[ ]*=[ ]*([0-9]+)/",$tmp,$hits) ) $dbport = ($hits[1])?$hits[1]:"5432";
            if ( preg_match("/\[[a-z]+/",$tmp) ) $dbsec = false;
            $tmp = fgets($lxo,512);
            continue;
        }
        if ( preg_match("/cookie_name[ ]*=[ ]*(.+)/",$tmp,$hits) ) $cookiename = $hits[1];
        //if ( preg_match("/dbcharset[ ]*=[ ]*(.+)/",$tmp,$hits) )   $dbcharset = $hits[1];
        if ( preg_match("/session_timeout[ ]*=[ ]*(.+)/",$tmp,$hits) )   $sesstime = $hits[1];
        if ( preg_match("!\[authentication/database\]!",$tmp) )    $dbsec = true;
        $tmp = fgets($lxo,512);
    }
    if ( !$cookiename ) $cookiename = $_SESSION['erpConfigFile'].'_session_id';
    if ( !$sesstime ) $sesstime = 480;
    fclose($lxo);
    $cookie = $_COOKIE[$cookiename];
    if ( !$cookie ) header("location: ups.html");
    // Benutzer anmelden
    error_log("!$ERPNAME!$dbhost,$dbport,$dbuser,$dbpasswd,$dbname,$cookie!",0);
    $auth = authuser($dbhost,$dbport,$dbuser,$dbpasswd,$dbname,$cookie);
    if ( !$auth ) {  return false; };                    // Anmeldung des Users fehlgeschlagen
    chkdir($auth["dbname"]);                         // gibt es unter dokumente ein Verzeichnis mit dem Instanznamen
    chkdir($auth["dbname"].'/tmp/');
    foreach ($auth as $key=>$val) $_SESSION[$key] = $val;                // Mandanten + Userdaten in Session speichern
    $_SESSION["sessid"] = $cookie;
    $_SESSION["cookie"] = $cookiename;
    $_SESSION["sesstime"] = $sesstime;
    // Benutzer/Gruppen/Gruppenzuordnung aus der ERP als Arrays in Session schreiben
    $db_new   = new myDB($dbhost,$dbuser,$dbpasswd,$dbname,$dbport);
    $sql_all_users = "SELECT usr.id AS user_id, usr.login, usrc.cfg_value AS name FROM auth.user AS usr INNER JOIN auth.user_config AS usrc ON usr.id = usrc.user_id WHERE usrc.cfg_key = 'name' ORDER by usr.id";
    $sql_all_groups = "SELECT grp.id AS grp_id, grp.name AS grp_name FROM auth.group AS grp ORDER by grp.id";
    $sql_all_assignments = "SELECT usrg.user_id AS user_id, usrg.group_id AS group_id FROM auth.user_group AS usrg ORDER by usrg.user_id";
    $all_users = $db_new->getAll( $sql_all_users);
    $all_groups = $db_new->getAll( $sql_all_groups);
    $all_assignments = $db_new->getAll( $sql_all_assignments);
    // $_SESSSION['ok'] da anmelden 2x durchlaufen wird ?? Bessere Lösung ?
    if(!$_SESSION['ok']) {
        $_SESSION['all_erp_users'] = $all_users;
        $_SESSION['all_erp_groups'] = $all_groups;
        $_SESSION['all_erp_assignments'] = $all_assignments;
    }
    $_SESSION['ok'] = "1";
    // Mit der Mandanten-DB verbinden
    $_SESSION["db"]     = new myDB($_SESSION["dbhost"],$_SESSION["dbuser"],$_SESSION["dbpasswd"],$_SESSION["dbname"],$_SESSION["dbport"]);
    if( !$_SESSION["db"] ) {
        return false;
    } else {
        $_SESSION['Admin'] = $auth['Admin'];
        $charset = ini_get("default_charset");
        //if ( $charset == "" ) $charset = $dbcharset;
        if ( $charset == "" ) $charset = 'UTF8';
        $_SESSION["charset"] = $charset;
        include_once("inc/UserLib.php");
        $user_data = getUserStamm(0,$_SESSION["login"]);
        $BaseUrl  = (empty( $_SERVER['HTTPS'] )) ? 'http://' : 'https://';
        $BaseUrl .= $_SERVER['HTTP_HOST'];
        $BaseUrl .= preg_replace( "^crm/.*^", "", $_SERVER['REQUEST_URI'] );
        if ($user_data) foreach ($user_data as $key => $val) $_SESSION[$key] = $val;
        if ( isset($_SESSION['sql_error']) && $_SESSION['sql_error'] ) $_SESSION['db']->setShowError(true);
        else $_SESSION['db']->setShowError(false);
        $_SESSION['dir_mode']  = ( $user_data['dir_mode'] != '' )?octdec($user_data['dir_mode']):493; // 0755
        $_SESSION["loginCRM"] = $user_data["id"];
        $_SESSION['theme']    = ($user_data['theme']=='' || $user_data['theme']=='base')?'':$user_data['theme'];
        $sql = "SELECT  * from schema_info where tag like 'relea%' order by itime desc limit 1";
        $rs = $_SESSION["db"]->getOne($sql);
        $tmp = substr($rs['tag'],8);
        $_SESSION["ERPver"]   = strtr($tmp,'_','.');
        $_SESSION["menu"]     = makeMenu($_SESSION["sessid"],$_SESSION["token"]);
        $_SESSION["basepath"] = $BaseUrl;
        $_SESSION['token']    = False;
        return true;
    }
}

/****************************************************
* chkdir
* in: dir = String
* out: boolean
* prueft, ob Verzeichnis besteht und legt es bei Bedarf an
*****************************************************/
function chkdir($dir,$p="") {
    if ( isset($_SESSION['crmpath']) && file_exists($_SESSION['crmpath']."/dokumente/".$_SESSION["dbname"]."/".$dir) ) {
        return $_SESSION['crmpath']."/dokumente/".$_SESSION["dbname"]."/".$dir;
    } else {
        if ( ! isset($_SESSION["dbname"]) ) return false;
        $dirs = explode("/",$dir);
        $tmp  = $_SESSION["dbname"]."/";
        foreach ( $dirs as $dir ) {
            if ( !file_exists($_SESSION['crmpath']."/dokumente/$tmp".$dir) ) {
                if ( isset($_SESSION['dir_mode']) && $_SESSION['dir_mode'] != ''  ) {
                    $ok = @mkdir($_SESSION['crmpath']."/dokumente/$tmp".$dir, $_SESSION['dir_mode']);
                } else {
                    $ok = @mkdir($_SESSION['crmpath']."/dokumente/$tmp".$dir);
                }
                if ( isset($_SESSION['dir_group']) && $_SESSION['dir_group'] && $ok ) @chgrp($_SESSION['crmpath']."/dokumente/$tmp".$dir,$_SESSION['dir_group']);
                if ( !$ok ) {
                    return false;
                }
            };
            $tmp .= $dir."/";
        };
        return $_SESSION['crmpath']."/dokumente/".$_SESSION["dbname"]."/".$dir;
    }
}

/****************************************************
* liesdir
* in: dir = String
* out: files = Array
* liest die Dateien eines Verzeichnisses
*****************************************************/
function liesdir($dir) {
    $dir = $_SESSION['crmpath']."/dokumente/$dir/";
    if ( !file_exists($dir) ) return false;
    $cdir = dir($dir);
    while ( $entry = $cdir->read() ) {
        if ( !is_dir($entry) ) {
            $files[] = array("size"=>filesize($dir.$entry),"date"=>date("d.m.Y H:i:s",filemtime($dir.$entry)),"name"=>$entry);
        }
    }
    return $files;
}

/****************************************************
* chkFld
* in: val = mixed, empty = boolean, rule = int
* out: ok = boolean
* Daten nach Regeln pruefen
*****************************************************/
function chkFld(&$val,$empty,$rule,$len) {
    if ( $empty===0 ) { $leer="|^$"; }
    else { $leer = ''; };
    switch ($rule) {
        case 1 : $ok = preg_match('/[\w\s\d]'.$leer.'/i',$val,$hit); //String
                 if ( strlen($val)>$len && $len>0 ) $val = substr($val,0,$len);
                 $val = strtr($val,array("'"=>"''"));
                 break;
        case 2 : if ( $empty===0 && empty($val) ) { $ok = true; $val = ""; }
                 else { $ok=preg_match('/^[0-9A-Z]{2,4}[-]{0,1}[0-9A-Z]{0,5}$/',$val,$hit); }; // Plz: PL=dd-ddd, NL=ddwwww, A=dddd
                 if ( strlen($val)>$len && $len>0 ) $val = substr($val,0,$len);
                 break;
        case 3 : if ( $empty===0 && empty($val) ) { $ok=true; $val=""; }
                 else { $ok=preg_match('/^070[01][ A-Z]{6,9}$/', $val,$hit) || preg_match('/^\+?[0-9\(\)\/ \-]+$/', $val,$hit); }; //Telefon
                 if ( strlen($val)>$len && $len>0 ) $val = substr($val,0,$len);
                 break;
        case 4 : $ok = preg_match('!^(http(s)?://)?([a-zA-Z0-9\-]*\.)?[a-zA-Z0-9\-]{2,}(\.[a-zA-Z0-9\-]{2,})?(\.[a-zA-Z0-9\-]{2,})(/.*)?$'.$leer.'!',$val,$hit); // www
                 if ( strlen($val)>$len && $len>0 ) $val = substr($val,0,$len);
                 break;
        case 5 : $ok = preg_match('!^([a-z_0-9]+)([a-z_0-9\.\-]+)([a-z_0-9]*)\@([a-z0-9][a-z0-9._-]*\\.)*[a-z0-9][a-z0-9._-]*\\.[a-z]{2,5}$'.$leer.'!i',$val,$hit); //eMail
                 if ( strlen($val) > $len && $len > 0 ) $val = substr($val,0,$len);
                 break;
        case 6 : if ( $empty===0 && empty($val) ) { $ok = true; $val = "null"; }
                 else { $ok = preg_match('/^[0-9]+$/',$val,$hit); } // Ganzzahlen
                 break;
        case 7 : if ( $empty===0 && empty($val) ) { $ok = true; $val = null;} // Datum
                 else if ( $val == '00.00.0000' ) { $ok = true; $val = null;}
                 else {
                     $t = explode(".",$val);
                     if ( $ok = checkdate($t[1],$t[0],$t[2]) ) {
                        if ( $t[2] <= date('y') ) {
                            $val = sprintf('20%02d-%02d-%02d',$t[2],$t[1],$t[0]);
                        } else if ( $t[2] > 100 ) {
                            $val = sprintf('%04d-%02d-%02d',$t[2],$t[1],$t[0]);
                        } else {
                            $val = sprintf('19%02d-%02d-%02d',$t[2],$t[1],$t[0]);
                        }
                    }
                 };
                 break;
        case 8 : $val = mb_strtoupper($val,'utf-8'); $ok = preg_match("/[\w\s-]+$leer/",$val,$hit); // String, keine Zahlen oder Zeichen kleiner chr(32)
                 if ( strlen($val) > $len && $len > 0 ) $val = substr($val,0,$len);
                 break;
        default : $ok=true;
    }
    return $ok;
}

function getVersiondb() {
    $rs = $_SESSION['db']->getOne("select * from crm order by datum desc limit 1");
    if ( !$rs["version"] ) return "V n.n.n";
    return $rs["version"];
}

function berechtigung($tab="") {
    $grp = getGrp($_SESSION["loginCRM"]);
    $rechte = "( ".$tab."owener=".$_SESSION["loginCRM"]." or ".$tab."owener is null";
    if ( $grp ) $rechte .= " or ".$tab."owener in $grp";
    return $rechte.")";
}

/****************************************************
* getBundesland
* out: array
* Bundeslaender holen
*****************************************************/
function getBundesland($land) {
    $land = mb_strtolower($land,'utf-8');
    if (in_array($land,array('deutschland','germany','allemagne','d'))) {
        $sql = "select * from bundesland where country = 'D' order by country,bundesland";
    } else if (in_array($land,array('österreich','austria','autriche','a'))) {
        $sql = "select * from bundesland where country = 'A' order by country,bundesland";
    } else if (in_array($land,array('schweiz','swiss','suisse','ch'))) {
        $sql = "select * from bundesland where country = 'CH' order by country,bundesland";
    } else {
        $sql = "select * from bundesland order by country,bundesland";
    }
    $rs = $_SESSION['db']->getAll($sql);
    return $rs;
}

/****************************************************
* mkTelNummer
* in: id = int, tab = char, tels = array
* out: rs = int
* Telefonnummern genormt speichern
*****************************************************/
function mkTelNummer($id,$tab,$tels,$delete=true) {
    if ( $delete ) {
        $sql = "delete from telnr where id=$id and tabelle='$tab'";
        $rs = $_SESSION['db']->query($sql);
    }
    foreach( $tels as $tel ) {
        $tel = strtr($tel,array(" "=>"","-"=>"","/"=>"","\\"=>"","("=>"",")"=>""));
        if ( substr($tel,0,1) == "+" ) $tel = substr($tel,3);
        if ( substr($tel,0,1) == "0" ) $tel = substr($tel,1);
        if ( trim($tel) <> "" ) {
            $sql = "insert into telnr (id,tabelle,nummer) values (%d,'%s','%s')";
            $sql = sprintf($sql,$id,$tab,$tel);
            $rs = $_SESSION['db']->query($sql);
        }
    }
}

function getAnruf($nr) {
    $nun = date("H:i");
    $name = "_0;$nun $nr unbekannt";
    $sql = "select * from telnr where nummer = '$nr'";
    $rs  = $_SESSION['db']->getAll($sql);
    if( !$rs ) {
        return false;
    } else {
        $i = 1;
        $more = "";
        while ( count($rs) == 0 && $i < 5 ) {
            $sql = "select * from telnr where nummer like '".substr($nr,0,-$i)."%'";
            $rs  = $_SESSION['db']->getAll($sql);
            $i++;
            $more = "?";
        };
        if ( $i < 5 ) {
            if ( $rs[0]["tabelle"] == "P" ) {
                $sql = "select cp_name as name2,cp_givenname as name1 from contacts where cp_id=".$rs[0]["id"];
            } else if ( $rs[0]["tabelle"] == "S" ) {
                $sql = "select shipto_name as name1,'' as name2 from shipto where transid=".$rs[0]["id"];
            } else if ( $rs[0]["tabelle"] == "C" ) {
                $sql = "select name as name1,'' as name2 from customer where id=".$rs[0]["id"];
            } else if ( $rs[0]["tabelle"] == "V" ) {
                $sql = "select name as name1,'' as name2 from vendor where id=".$rs[0]["id"];
            } else if ( $rs[0]["tabelle"] == "E" ) {
                $sql = "select name as name1,'' as name2 from employee where id=".$rs[0]["id"];
            } else {
                $name = "_0;".$nun." ".$nr." unbekannt"; return $name;
            }
            $rs1 = $_SESSION['db']->getAll($sql);
            $name = $rs[0]["tabelle"].$rs[0]["id"].$nun." ".$rs1[0]["name1"]." ".$rs1[0]["name2"].$more;
        } else {
            $name = "_00000".$nun." ".$nr." unbekannt";
        }
    }
    return $name;
}

function getVertretung($user) {
    $sql = "select workphone from employee where vertreter=(select id from employee where workphone='$user')";
    $rs  = $_SESSION['db']->getAll($sql);
    if ( count($rs) > 0 ) { return $rs; }
    else { return false; };
}

function getVorlagen() {
    $sql = "select * from docvorlage";
    $rs  = $_SESSION['db']->getAll($sql);
    if ( count($rs) > 0 ) { return $rs; }
    else { return false; };
}

function getDocVorlage($did) {
    if ( !$did ) return false;
    $sql = "select * from docvorlage where docid=$did";
    $rs1 = $_SESSION['db']->getOne($sql);
    if ( !$rs1 ) return false;
    $sql = "select * from docfelder where docid=$did order by position";
    $rs2 = $_SESSION['db']->getAll($sql);
    $rs["document"] = $rs1;
    $rs["felder"]   = $rs2;
    if ( count($rs) > 0 ) { return $rs; }
    else { return false; };
}

function getDOCvar($did) {
    $sql = "select * from docvorlage where docid=$did";
    $rs1 = $_SESSION['db']->getOne($sql);
    return $rs1;
}

function updDocFld($data) {
    $sql  = "update docfelder set feldname='".$data["feldname"]."', platzhalter='".strtoupper($data["platzhalter"]);
    $sql .= "', beschreibung='".$data["beschreibung"]."',laenge=".$data["laenge"].",zeichen='".$data["zeichen"];
    $sql .= "',position=".$data["position"].",docid=".$data["docid"]." where fid=".$data["fid"];
    $rs  = $_SESSION['db']->query($sql);
    if( !$rs ) {
        return false;
    }
    return $data["fid"];
}

function insDocFld($data) {
    $fid = mknewDocFeld();
    if ( !$fid ) return false;
    $data["fid"] = $fid;
    $fid = updDocFld($data);
    return $fid;
}

function delDocFld($data) {
    $sql = "delete from docfelder where fid=".$data["fid"];
    $rs = $_SESSION['db']->query($sql);
}

/****************************************************
* mknewDocFeld
* in:
* out: id = int
* Dokumentsatz erzeugen ( insert )
*****************************************************/
function mknewDocFeld() {
    $newID = uniqid (rand());
    $sql = "insert into docfelder (beschreibung) values ('$newID')";
    $rc = $_SESSION['db']->query($sql);
    if ( $rc ) {
        $sql = "select fid from docfelder where beschreibung = '$newID'";
        $rs = $_SESSION['db']->getOne($sql);
        if ( $rs ) {
            $id = $rs["fid"];
        } else {
            $id = false;
        }
    } else {
        $id = false;
    }
    return $id;
}

/****************************************************
* mknewDocVorlage
* in:
* out: id = int
* Dokumentsatz erzeugen ( insert )
*****************************************************/
function mknewDocVorlage() {
    $newID = uniqid (rand());
    $sql = "insert into docvorlage (vorlage) values ('$newID')";
    $rc = $_SESSION['db']->query($sql);
    if ( $rc ) {
        $sql = "select docid from docvorlage where vorlage = '$newID'";
        $rs  = $_SESSION['db']->getOne($sql);
        if ( $rs ) {
            $id = $rs["docid"];
        } else {
            $id = false;
        }
    } else {
        $id = false;
    }
    return $id;
}

function delDocVorlage($data) {
    $sql = "delete from docfelder where docid=".$data["did"];
    $rs = $_SESSION['db']->query($sql);
    if ( $rs ) {
        $sql = "delete from docvorlage where docid=".$data["did"];
        $rs  = $_SESSION['db']->query($sql);
    }
}

function saveDocVorlage($data,$files) {
    if ( !$data["did"] ) {
        $data["did"] = mknewDocVorlage();
        if ( !$data["did"] ) { return false; };
    }
    if ( $files["file"]["name"] ) {
        exec("cp ".$files["file"]["tmp_name"]." ./vorlage/".$files["file"]["name"]);
        $file = $files["file"]["name"];
    } else {
        $file = $data["file_"];
    }
    if ( !$data["vorlage"] ) $data["vorlage"] = "Kein Titel ".datum("d.m.Y");
    $sql  = "update docvorlage set vorlage='".$data["vorlage"]."', beschreibung='".$data["beschreibung"];
    $sql .= "', file='".$file."', applikation='".$data["applikation"]."' where docid=".$data["did"];
    $rs = $_SESSION['db']->query($sql);
    if( !$rs ) {
        return false;
    } else {
        return $data["did"];
    }
}

function shopartikel() {
    $sql  = "SELECT t.rate,PG.partsgroup,P.partnumber,P.description,P.notes,P.sellprice,P.priceupdate FROM ";
    $sql .= "chart c left join partstax pt on pt.chart_id = c.id,";
    $sql .= "tax t, parts P left join partsgroup PG on PG.id=P.partsgroup_id ";
    $sql .= "where c.category='I' AND t.taxnumber=c.accno  and pt.parts_id = P.id and P.shop=1";
    $rs = $_SESSION['db']->getAll($sql);
    if( !$rs ) {
        return false;
    } else {
        return $rs;
    }
}

function getAllArtikel($art="A") {
    if ($art == "A") { $where = ""; }
    else if ($art == "W") { $where = "where inventory_accno_id is not null and expense_accno_id is not null"; }
    else if ($art == "D") { $where = "where inventory_accno_id is null and expense_accno_id is not null"; }
    else if ($art == "E") { $where = "where inventory_accno_id is null and expense_accno_id is null"; };
    $sql = "SELECT * from parts $where order by description";
    $rs = $_SESSION['db']->getAll($sql);
    if(!$rs) {
        return false;
    } else {
        return $rs;
    }
}

function getGrp($usrid,$inkluid=false){
    $sql = "select distinct(grpid) from grpusr where usrid=$usrid";
    $rs  = $_SESSION['db']->getAll($sql);
    if( !$rs ) {
        if ( $inkluid ) { return "($usrid)"; }
        else { $data = false; };
    } else {
        if ( $rs ) {
           $data = "(";
            foreach( $rs as $row ) {
                $data .= $row["grpid"].",";
            };
            if ( $inkluid ) { $data .= "$usrid)"; }
            else { $data = substr($data,0,-1).")"; };
        } else {
            if ( $inkluid ) { $data .= "($usrid)"; }
            else { $data = false; };
        }
        return $data;
    }
};

function firstkw($jahr) {
    $erster = mktime(0,0,0,1,1,$jahr);
    $wtag = date('w',$erster);
    if ( $wtag <= 4 ) {
        // Donnerstag oder kleiner: auf den Montag zurueckrechnen.
        $montag = mktime(0,0,0,1,1-($wtag-1),$jahr);
    } else {
        // auf den Montag nach vorne rechnen.
        $montag = mktime(0,0,0,1,1+(7-$wtag+1),$jahr);
    }
    return $montag;
}

function mondaykw($kw,$jahr) {
    $firstmonday = firstkw($jahr);
    $mon_monat   = date('m',$firstmonday);
    $mon_jahr    = date('Y',$firstmonday);
    $mon_tage    = date('d',$firstmonday);
    $tage = ($kw-1)*7;
    $mondaykw    = mktime(0,0,0,$mon_monat,$mon_tage+$tage,$mon_jahr);
    return $mondaykw;

}
function clearCSVData() {
    return $_SESSION['db']->query("delete from tempcsvdata where uid = '".$_SESSION["loginCRM"]."'");
}


/**
 * Schreibt eine :-separierte Zeichenkette in die Tabelle tempcsvdata unter csvdaten
 * Die Eintraege sind ueber die Benutzer-Session wieder auffindbar und werden immer
 * wieder geloescht. TODO: Soll das immer so sein?, dann vielleicht das Loeschen in diese
 * Funktion integrieren?
 * Der erste Eintrag soll immer der INDEX sein, d.h. die entsprechenden Spaltennamen
 * TODO Ist es moeglich den ersten Eintrag zu pruefen, um somit weniger Fehler beim
 * Aufrufen zu erzeugen? jb 16.6.2009
 *
 * @param string $data
 *
 * @return $rc TODO auf boolean setzen und korrekte Fehlerbehandlung umsetzen
 */
function insertCSVData($data,$id){
    $tmpstr = implode(":",$data);                                               // ANREDE:NAME:STRASSE (...)
    $sql = "insert into tempcsvdata (uid,csvdaten,id) values ('"
            . $_SESSION["loginCRM"] . "','" . $_SESSION['db']->saveData($tmpstr) . "','"
            . $_SESSION['db']->saveData($id) . "')";                                        // saveData escapt die Zeichenkette
    $rc = $_SESSION['db']->query($sql);
    return $rc;     //Fehlerbehandlung? Wie sieht es aus mit exceptions? Muessen wir php4-kompatibel sein?
                    //Sollte eigentlich schon immer in den Funktionen direkt passieren
                    // http://pear.php.net/manual/de/standars.errors.php
}

/**
 * Liest die entsprechenden Daten der :-separierte Zeichenkette aus der Tabelle tempcsvdata.
 * Ruft selber nur getAll auf und schiebt die Fehlermeldung dann nach oben. Frage:
 * Kann man das eleganter gestalten?
 *
 * @return false, mixed
 */
function getCSVData(){
    $sql = "select * from tempcsvdata where uid = '" . $_SESSION["loginCRM"] .  "' ORDER BY id";
    return $_SESSION['db']->getAll($sql);   //liefert false bei misserfolg
}

/**
 * Liefert die aktuell in der CRM-Session gespeicherte IDs.
 * Klammert die CSV-Kopfzeile aus, da nur ids groesser 0 aus der DB gelesen werden
 * False bei Misserfolg. Leider ansonsten Kopie von getCSVData. Ideen?
 *
 * @return false, mixed
 */
function getCSVDataID(){
    $sql = "select id from tempcsvdata where uid = '" . $_SESSION["loginCRM"] .  "' and id > 0";
    return $_SESSION['db']->getAll($sql);   //liefert false bei misserfolg
}
function startTime() {
    $zeitmessung = microtime();
    $zeittemp    = explode(" ",$zeitmessung);
    $zeitmessung = $zeittemp[0]+$zeittemp[1];
    return $zeitmessung;
}
function stopTime($start) {
    $stop = startTime();
    $zeit = $stop - $start;
    return substr($zeit,0,8);
}

/**
 * TODO: Listen in Templates fuellen.
 *
 * @param object $t Template
 * @param string $tpl Templatename
 * @param string $pre Platzhalter / Blockname
 * @param array  $data Daten
 * @param string $id  Spaltenname fuer ID
 * @param string $text  Spaltenname fuer Text
 *
 * @return void
 */
function  doBlock(&$t,$tpl,$liste,$pre,$data,$id,$text,$selid) {
    $t->set_block($tpl,$liste,'Block'.$pre);
    if ( $data ) foreach ( $data as $row ) {
        if ( is_array($text) ) {
            $textval = '';
            foreach ( $text as $txt ) {
                $textval .= $row[$txt]." ";
            }
        } else {
            $textval =  $row[$text];
        }
        $x[$pre.'id']   = $row[$id];
        $x[$pre.'text'] = $textval;
        if ($selid) $x[$pre.'sel']  =  ($row[$id]==$selid)?"selected":"";
        $t->set_var($x);
        $t->parse('Block'.$pre,$liste,true);
    }
}
function mkHeader() {
    $pager = '<span id="pager" class="pager">
                                <form>
                                    <img src="'.$_SESSION["baseurl"].'crm/jquery-plugins/tablesorter-master/addons/pager/icons/first.png" class="first"/>
                                    <img src="'.$_SESSION["baseurl"].'crm/jquery-plugins/tablesorter-master/addons/pager/icons/prev.png" class="prev"/>
                                    <img src="'.$_SESSION["baseurl"].'crm/jquery-plugins/tablesorter-master/addons/pager/icons/next.png" class="next"/>
                                    <img src="'.$_SESSION["baseurl"].'crm/jquery-plugins/tablesorter-master/addons/pager/icons/last.png" class="last"/>
                                    <select class="pagesize" id="pagesize">
                                        <option value="10">10</option>
                                        <option value="20" selected>20</option>
                                        <option value="30">30</option>
                                        <option value="all">Alle</option>
                                    </select>
                                </form>
                            </span>';

    $SV = '<script type="text/javascript" src="';
    $SN = '"></script>'."\n";
    $LV = '<link rel="stylesheet" type="text/css" href="';
    $LVID = '<link id="'.$_SESSION['theme'].'" rel="stylesheet" type="text/css" href="';
    $LN = '">'."\n";
    $head = array(
        'JQTABLE'       => $LV.$_SESSION['baseurl'].'crm/jquery-plugins/tablesorter-master/css/theme.jui.css'.$LN.
                           $LV.$_SESSION['baseurl'].'crm/jquery-plugins/tablesorter-master/addons/pager/jquery.tablesorter.pager.css'.$LN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/tablesorter-master/js/jquery.tablesorter.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/tablesorter-master/js/jquery.tablesorter.widgets.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/tablesorter-master/addons/pager/jquery.tablesorter.pager.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/js/tablesorter.js'.$SN,
        'JQTABLE-BUILD' => $SV.$_SESSION['baseurl'].'crm/jquery-plugins/tablesorter-master/js/widgets/widget-build-table.js'.$SN, // Achtung class="tablesorter" nicht verwenden
        'JQBOX'         => $SV.$_SESSION['baseurl'].'/crm/jquery-plugins/selectBoxIt/selectBoxIt.js'.$SN,
        'JQTIMECSS'     => $LV.$_SESSION['baseurl'].'crm/jquery-plugins/timepicker-master/jquery.ui.timepicker.css'.$LN,
        'JQTIME'        => $SV.$_SESSION['baseurl'].'crm/jquery-plugins/timepicker-master/jquery.ui.timepicker.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/timepicker-master/i18n/jquery.ui.timepicker-'.$_SESSION['countrycode'].'.js'.$SN,
        'JQFILEUP'      => $LV.$_SESSION['baseurl'].'crm/jquery-plugins/FileUpload/css/jquery.fileupload-ui.css'.$LN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/FileUpload/js/jquery.iframe-transport.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/FileUpload/js/jquery.fileupload.js'.$SN,
        'JQWIDGET'      => $SV.$_SESSION['baseurl'].'crm/jquery-ui/ui/minified/jquery.ui.widget.min.js'.$SN,
        'THEME'         => ($_SESSION['theme']!='')? $LVID .$_SESSION['baseurl'].'crm/jquery-themes/'.$_SESSION['theme'].'/jquery-ui.css'.$LN:'<link id="blue-style" rel="stylesheet" type="text/css" href="'.$_SESSION['baseurl'].'crm/jquery-themes/blue-style/jquery-ui.css'.$LN,
        'CRMCSS'        => $LV.$_SESSION['baseurl'].'crm/css/'.$_SESSION["stylesheet"].'/main.css'.$LN,
        'BOXCSS'        => $LV.$_SESSION['baseurl'].'crm/jquery-plugins/selectBoxIt/selectBoxIt.css'.$LN,
        'JUI-DROPDOWN'  => $LV.$_SESSION['baseurl'].'crm/jquery-plugins/jui_dropdown-master/jquery.jui_dropdown.css'.$LN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/jui_dropdown-master/jquery.jui_dropdown.min.js'.$SN,
        'CRMPATH'       => $_SESSION['baseurl'].'crm/',
        'FULLCALCSS'    => $LV.$_SESSION['baseurl'].'crm/jquery-plugins/fullcalendar2/fullcalendar.css'.$LN.
                           $LV.$_SESSION['baseurl'].'crm/jquery-plugins/fullcalendar2/fullcalendar.print.css" media="print'.$LN,
        'FULLCALJS'     => $SV.$_SESSION['baseurl'].'crm/jquery-plugins/fullcalendar2/lib/moment.min.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/fullcalendar2/fullcalendar.min.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/fullcalendar2/lang/'.$_SESSION['countrycode'].'.js'.$SN,
        'COLORPICKERCSS'=> $LV.$_SESSION['baseurl'].'crm/jquery-plugins/colorPicker/syronex-colorpicker.css'.$LN,
        'COLORPICKERJS' => $SV.$_SESSION['baseurl'].'crm/jquery-plugins/colorPicker/syronex-colorpicker.js'.$SN,
        'JTABLECSS'     => $LV.$_SESSION['baseurl'].'crm/jquery-plugins/jtable/themes/jqueryui/jtable_jqueryui.min.css'.$LN,
        'JTABLEJS'      => $SV.$_SESSION['baseurl'].'crm/jquery-plugins/jtable/jquery.jtable.min.js'.$SN,
        'JQCOOKIE'      => $SV.$_SESSION['baseurl'].'crm/jquery-plugins/jquery-cookie/jquery.cookie.js'.$SN,
        'TINYMCE'       => $SV.$_SESSION['baseurl'].'crm/jquery-plugins/tinymce/tinymce.min.js'.$SN,
        'DATEPICKER'    => $_SESSION['countrycode'] == 'de' ? $SV.$_SESSION['baseurl'].'js/jquery/ui/i18n/jquery.ui.datepicker-de.js'.$SN : '',
        'JQCALCULATOR'  => $LV.$_SESSION['baseurl'].'crm/jquery-plugins/jquery-calculator/jquery.calculator.css'.$LN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/jquery-calculator/jquery.plugin.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/jquery-calculator/jquery.calculator.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/jquery-calculator/jquery.calculator-'.$_SESSION['countrycode'].'.js'.$SN,
        'FANCYBOX'      => $LV.$_SESSION['baseurl'].'crm/jquery-plugins/fancybox/source/jquery.fancybox.css'.$LN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/fancybox/source/jquery.fancybox.pack.js'.$SN,
        'QRCODE'        => $SV.$_SESSION['baseurl'].'crm/jquery-plugins/qrcode/jquery.qrcode-0.12.0.js'.$SN,
        'TOOLS'         => $LV.$_SESSION['baseurl'].'crm/jquery-plugins/jquery-calculator/jquery.calculator.css'.$LN.
                           $LV.$_SESSION['baseurl'].'crm/node_modules/postitall/dist/jquery.postitall.css'.$LN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/jquery-calculator/jquery.plugin.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/jquery-calculator/jquery.calculator.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/jquery-plugins/jquery-calculator/jquery.calculator-'.$_SESSION['countrycode'].'.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/node_modules/postitall/dist/jquery.postitall.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/js/jquery.postitall.ajax.js'.$SN.
                           $SV.$_SESSION['baseurl'].'crm/js/tools.js'.$SN,
        'JQTABLE-PAGER' => $pager
        );

    return $head;

}
function doHeader(&$t) {
    $head = mkHeader();
    $menu =  $_SESSION['menu'];
    $t->set_var(array(
        'JAVASCRIPTS'   => $menu['javascripts'],
        'STYLESHEETS'   => $menu['stylesheets'],
        'PRE_CONTENT'   => $menu['pre_content'],
        'START_CONTENT' => $menu['start_content'],
        'END_CONTENT'   => $menu['end_content'],
        'JQTABLE'       => $head['JQTABLE'],
        'JQWIDGET'      => $head['JQWIDGET'],
        'JQFILEUP'      => $head['JQFILEUP'],
        'THEME'         => $head['THEME'],
        'CRMCSS'        => $head['CRMCSS'],
        'CRMPATH'       => $head['CRMPATH'],
        'BOXCSS'        => $head['BOXCSS'],
        'JQBOX'         => $head['JQBOX'],
        'DATEPICKER'    => $head['DATEPICKER'],
        'JQCALCULATOR'  => $head['JQCALCULATOR'],
        'FANCYBOX'      => $head['FANCYBOX'],
        'QRCODE'        => $head['QRCODE'],
        'TOOLS'         => $head['TOOLS']
    ));
}
/**
 * TODO: short description.
 *
 * @return TODO
 */
function getLanguage() {
    $sql = "select id,description from language";
    return $_SESSION['db']->getAll($sql);
}
function nextNumber($number) {
    $_SESSION['db']->begin();
    $sql = "select $number from defaults";
    $rs  = $_SESSION['db']->getOne($sql);
    preg_match("/([^\d]*)([\d]+)([^\d]*)/",$rs[$number],$hit);
    $nr  = $hit[1].($hit[2]+1).$hit[3];
    $sql = "update defaults set $number = '$nr'";
    $rc  = $_SESSION['db']->query($sql,"nextnumber");
    if ( !$rc ) {
        $_SESSION['db']->rollback();
        return false;
    } else {
        $_SESSION['db']->commit();
        return $nr;
    }
}

/***************************************************************
*** erzeugt valide Verzeichnisnamen für viele Betriebsysteme ***
*** invalide Zeichen sind: Umlaute  \ / : * ? " < > |        ***
***************************************************************/
function mkDirName($name) {
//sollte eigentlich in stdLib, diese müsste dann jedoch utf-8 codiert sein!
    $ers = array(
        ' ' => '_',  'ä' => 'ae', 'â' => 'ae', 'ã' => 'ae', 'à' => 'ae', 'á' => 'ae', 'ç' => 'c',
        'ï' => 'i',  'í' => 'i',  'ì' => 'i',  'î' => 'i',  'ö' => 'oe', 'ó' => 'oe', 'ò' => 'oe',
        'õ' => 'oe', 'ü' => 'ue', 'ú' => 'ue', 'ù' => 'ue', 'û' => 'ue', 'Ä' => 'Ae', 'Â' => 'Ae',
        'Ã' => 'Ae', 'Á' => 'Ae', 'À' => 'Ae', 'Ç' => 'C',  'É' => 'E',  'È' => 'E',  'Ê' => 'E',
        'Ë' => 'E',  'Í' => 'I',  'Ì' => 'I',  'Î' => 'I',  'Ï' => 'I',  'Ö' => 'Oe', 'Ó' => 'Oe',
        'Ò' => 'Oe', 'Õ' => 'Oe', 'Ô' => 'Oe', 'Ü' => 'Ue', 'Ú' => 'Ue', 'Ù' => 'Ue', 'Û' => 'Ue',
        '\\'=> '_',  'ß' => 'ss', '/' => '_' , ':' => '_',  '*' => '_',  '?' => '_',  '"' => '_',
        '<' => '_',  '>' => '_',  '|' => '_' , ',' => '' );
    return strtr($name,$ers);
}

function makeMenu($sess,$token){
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
    $_SESSION['baseurl'] = $BaseUrl;
    $Url = $BaseUrl.'controller.pl?action=Layout/empty&format=json';
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $Url );
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_TIMEOUT, 1 );
    curl_setopt( $ch, CURLOPT_ENCODING, 'gzip,deflate' );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array (
                "Connection: keep-alive",
                "Cookie: ".$_SESSION["cookie"]."=".$sess."; ".$_SESSION["cookie"]."_api_token=".$token
                ));

    $result = curl_exec( $ch );
    if( $result === false || curl_errno( $ch )){
        die( 'Curl-Error: ' .curl_error($ch).' </br> $ERP_BASE_URL in "inc/conf.php" richtig gesetzt??' );
    }
    curl_close( $ch );
    $objResult = json_decode( $result );
    if (!is_object($objResult)) anmelden();
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
            //$rs['javascripts'] .= '<script type="text/javascript" src="'.$BaseUrl.$js.'"></script>'."\n".'   ';
            //Da die ERP eine veraltete JUI benützt, aktuelle JUI aus CRM laden
            //ToDo: JUI aus ERP laden wenn diese >= Version 11.4 wird
            //Achtung!: JUI wird von der ERP nur geliefert wenn fast alle Module aktiviert sind (Menü)
            if( strpos( $js, "jquery-ui")  === false ) $rs['javascripts'] .= '<script type="text/javascript" src="'.$BaseUrl.$js.'"></script>'."\n".'   ';
            $rs['javascripts'] .= '<script type="text/javascript" src="'.$BaseUrl.'crm/jquery/jquery-ui.min.js"></script>'."\n".'   ';;
        }
        foreach($objResult->{'stylesheets'} as $style) {
            if ($style) $rs['stylesheets'] .= '<link rel="stylesheet" href="'.$BaseUrl.$style.'" type="text/css">'."\n".'   ';
        }
        foreach($objResult->{'stylesheets_inline'} as $style) {
            if ($style) $rs['stylesheets'] .= '<link rel="stylesheet" href="'.$BaseUrl.$style.'" type="text/css">'."\n".'   ';
        }
        $suche = '^([/a-zA-Z_0-9]+)\.(pl|php|phtml)^';
        $ersetze = $BaseUrl.'${1}.${2}';
        $tmp = preg_replace($suche, $ersetze, $objResult->{'pre_content'} );
        $tmp = str_replace( 'itemIcon="', 'itemIcon="'.$BaseUrl, $tmp );
        $rs['pre_content']   = str_replace( 'src="', 'src="'.$BaseUrl, $tmp );
        $rs['start_content'] = $objResult->{'start_content'};
        $rs['start_content_ui'] = '<div class="ui-widget-content">';//Begin UI-Look
        $rs['end_content']   = $objResult->{'end_content'};
        $rs['end_content']  .= '<script type="text/javascript">';
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
/*******************************************************************************************************************************************************
*** History wird mit Parameter schreibend benutzt, ohne Paramter gibt Sie die History zurück ***********************************************************
*******************************************************************************************************************************************************/
function accessHistory( $data=false ) {
    if ( $_SESSION['loginok'] == 'ok' ){
        $sql  = "select val from crmemployee where uid = '" . $_SESSION["loginCRM"];
        $sql .= "' AND manid = ".$_SESSION['manid']." AND key = 'search_history'";
        $rs =   $_SESSION['db']->getOne( $sql );
        $array_of_data = json_decode( $rs['val'], true );
        if( !is_array ( $array_of_data[0] ) ) unset( $array_of_data[0] );//ToDo
        if ( !$data && $array_of_data ) {
             return array_reverse( $array_of_data );
        }
        else {
            if ( $array_of_data && in_array( $data, $array_of_data ) ) unset( $array_of_data[array_search( $data, $array_of_data )] );
            $array_of_data[] = $data;
            if ( count( $array_of_data ) > 8 ) array_shift( $array_of_data );
            $sql =  "UPDATE crmemployee SET val = '".json_encode( $array_of_data )."' WHERE uid = ".$_SESSION['loginCRM'];
            $sql .= " AND manid = ".$_SESSION['manid']." AND key = 'search_history'";
            $_SESSION['db']->query( $sql );
        }
    }
}

function getCurrencies() {
    $sql = "SELECT * from currencies";
    $rs = $_SESSION['db']->getAll($sql);
    return $rs;
}

function ts2gerdate( $myts ){//Timestamp to German Date
    $german_weekdays = array( "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag" );
    $german_months = array( "Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember" );
    //Datumsbestandteile ermitteln
    $wd   = $german_weekdays[date("w",$myts)];
    $day  = date( "d", $myts );
    $m    = $german_months[date("m",$myts)-1];
    $year = date("Y",$myts);
    $hour = date("H",$myts);
    $min  = date("i",$myts);
    return $wd.", ".$day.". ".$m." ".$year." ".$hour.":".$min;
}

?>
