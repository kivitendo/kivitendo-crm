<?php
define('debug',true);

$basepath = substr(__DIR__,0,-11);  // solte /var/www/openkonto liefern
$inclpath = ini_get('include_path');
ini_set('include_path',$inclpath.":".$basepath.'crm/inc'.":".$basepath.'crm/android');

require 'mdb.php';

function db2date($datum) {
   if ( strpos($datum,"-") ) {
       $D = explode("-",$datum);
       $datum = sprintf ("%02d.%02d.%04d",$D[2],$D[1],$D[0]);
   }
   return $datum;
}

function authDB() {
    ini_set("gc_maxlifetime","3600");
    if ( file_exists($GLOBALS['basepath']."/config/kivitendo.conf") ) {  
	    $lxo = fopen($GLOBALS['basepath']."/config/kivitendo.conf","r");  
    } else if ( file_exists($GLOBALS['basepath']."/config/kivitendo.conf.default") ) {
	    $lxo = fopen($GLOBALS['basepath']."/config/kivitendo.conf.default","r");
    } else {
        return false;
    }
    $dbsec = false;
    $tmp   = fgets($lxo,512);
    $conn = array();
    while ( !feof($lxo) ) {
       if ( preg_match("/^[\s]*#/",$tmp )) { //Kommentar, überlesen
            $tmp = fgets($lxo,512);
            continue;
       }
       if ( preg_match("!\[authentication/ldap\]!",$tmp) ) $dbsec = false;
       if ( $dbsec ) {
            preg_match("/db[ ]*= (.+)/",$tmp,$hits);
            if ( $hits[1] ) $conn['dbname']   = $hits[1];
            preg_match("/password[ ]*= (.+)/",$tmp,$hits);
            if ( $hits[1] ) $conn['dbpasswd'] = $hits[1];
            preg_match("/user[ ]*= (.+)/",$tmp,$hits);
            if ( $hits[1] ) $conn['dbuser']   = $hits[1];
            preg_match("/host[ ]*= (.+)/",$tmp,$hits);
            if ( $hits[1] ) $conn['dbhost']   = ($hits[1])?$hits[1]:"localhost";
            preg_match("/port[ ]*= ([0-9]+)/",$tmp,$hits);
            if ( $hits[1] ) $conn['dbport']   = ($hits[1])?$hits[1]:"5432";
            if ( preg_match("/\[[a-z]+/",$tmp) ) $dbsec = False;
            $tmp = fgets($lxo,512);
            continue;
       }
       preg_match("/cookie_name[ ]*=[ ]*(.+)/",$tmp,$hits);
       if ( $hits[1] ) $cookiename = $hits[1];
       preg_match("/dbcharset[ ]*=[ ]*(.+)/",$tmp,$hits);
       if ( $hits[1] ) $dbcharset  = $hits[1];
       if (preg_match("!\[authentication/database\]!",$tmp)) $dbsec = true;
       $tmp = fgets($lxo,512);
    };
    if ( $GLOBALS['log'] ) $GLOBALS['log']->write('authDB-conn: '.print_r($conn,true));
    fclose($lxo);
    $db = new myDB($conn);
    if ( $GLOBALS['log'] ) { $db->log = true; } else { $db->log = false; };
    return $db;
}

function authuser($db,$mandant,$login,$pwd,$ip) {
    $pwd = '{SHA256S}'.hash('sha256',$login.$pwd);
    if ( $GLOBALS['log'] ) $GLOBALS['log']->write('authuser: '.$mandant.','.$login.','.$pwd.','.$ip);
    //$pwd = '{CRYPT}'.crypt($pwd,substr($login,0,2));
    $sql = "SELECT * FROM auth.user WHERE login = '$login' AND password = '$pwd'";
    $rs  = $db->getOne($sql,"authuser_1");
    if ( !$rs ) {
        if ( $GLOBALS['log'] ) $GLOBALS['log']->write('authuser: not logged in');
        delSession($db,false,$ip);
        return false;
    };
    $data['id'] = $rs['id'];
    $sql = "SELECT * FROM auth.clients C LEFT JOIN auth.clients_users U ON C.id=U.client_id WHERE C.name='$mandant' AND U.user_id = ".$rs['id'];
    $rsC = $db->getOne($sql,"authuser_1a");
    if ( !$rsC ) {
        if ( $GLOBALS['log'] ) $GLOBALS['log']->write('authuser: not in mandant');
        return false;
    }
    $sess = newSession($db,$ip,$login,$rsC['id']);
    $data['sess'] = $sess;
    return $data;
} 
function chkUser($db,$sess,$user,$mandant) {
    // Session Livetime noch prüfen
    $sql  = "SELECT id FROM auth.user WHERE login = '$login'";
    $usr  = $db->getOne($sql);
    $sql  = "SELECT * FROM auth.session_content WHERE session_id = '$sess' ";
    $rs   = $db->getAll($sql,'chksess');
    if ( !$rs ) return false;
    $ok   = true;
    foreach ( $rs as $row ) {
        if ( $row['sess_key'] == 'client_id' ) if ( $row['sess_value'] != $usr['id'] ) $ok = false;
        if ( $row['sess_key'] == 'login' )     if ( $row['sess_value'] != $mandant ) $ok = false;
    };
    return $ok;
}

function userData($db,$id,$ip,$mandant,$login,$pwd) {
    $rs = chkSession($db,$id,$ip);
    if ( $GLOBAS['log'] ) $GLOBALS['log']->write("chlS:$id,$ip,$mandant,$login,$pwd,".$rs);
    if ( !$rs ) {
        delSession($db,$id,$ip);
        $sess = authuser($db,$mandant,$login,$pwd,$ip);
        if ( $GLOBAS['log'] ) $GLOBALS['log']->write("chlS2:$sess");
        if ( !$sess ) return false;
    } else {
       $sess['sess'] = $id;
    };
    $sql = "SELECT * FROM  auth.clients WHERE name = '$mandant'";
    $rs  = $db->getOne($sql);
    if ( $GLOBAS['log'] ) $GLOBALS['log']->write("chlS3:$sql");
    if ( $GLOBAS['log'] ) $GLOBALS['log']->write("chlS3:".print_r($rs,true));
    if ( $rs ) { 
            $sess["db"] = new myDB($rs); 
            if ( $GLOBALS['log'] ) { $sess['db']->log = true; } else { $sess['db']->log = false; };
            return $sess;
    } else {
        return false;
    }
}

function delSession($db,$id,$ip) {
    $sql  = "delete from auth.session_content where session_id in ";
    $sql .= "(select id from auth.session where ip_address = '$ip') ";
    if ($id) $sql .= "or session_id = '$id'";
    $rc  = $db->query($sql);
    $sql = "delete from auth.session where ip_address = '$ip' ";
    if ($id) $sql .= "or id = '$id'";
    $rc  = $db->query($sql);
}

function newSession($db,$ip,$login,$client) {
    $rc    = delSession($db,false,$ip);
    $newID = uniqid (rand());
    $id    = md5($newID);
    $sql   = "INSERT INTO auth.session (id,mtime,ip_address) VALUES ('$id',now(),'$ip')";
    $rc    = $db->query($sql);
    $sql   = "INSERT INTO auth.session_content (session_id,sess_key,sess_value) VALUES (";
    $sqll  = "'$id','login','$login')";
    $sqlc  = "'$id','client_id','$client')";
    $rc    = $db->query($sql.$sqll);
    $rc    = $db->query($sql.$sqlc);
    return $id;
}

function chkSession($db,$id,$ip){
    $sql  = "SELECT * FROM auth.session S WHERE S.id = '$id' AND S.ip_address = '$ip' and mtime >= now() - Interval '2 Hours'";
    $rs   = $db->getOne($sql);
    if ( $rs ) { 
        return true;
    } else {
        return false;
    }
}
?>
