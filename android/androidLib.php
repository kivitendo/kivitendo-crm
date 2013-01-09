<?php

require_once "../inc/conf.php";
$inclpa=ini_get('include_path');
ini_set('include_path',$inclpa.":../:./crmajax:./inc:../inc");
require_once $dbmodul."db.php";

function db2date($datum) {
   if ( strpos($datum,"-") ) {
       $D = explode("-",$datum);
       $datum = sprintf ("%02d.%02d.%04d",$D[2],$D[1],$D[0]);
   }
   return $datum;
}

function authDB() {
global $ERPNAME;
    ini_set("gc_maxlifetime","3600");
    if (file_exists("../../".$ERPNAME."/config/lx_office.conf")) {
        $lxo = fopen("../../".$ERPNAME."/config/lx_office.conf","r");
    } else if (file_exists("../../".$ERPNAME."/config/lx_office.conf.default")) {
        $lxo = fopen("../../".$ERPNAME."/config/lx_office.conf.default","r");
    } else {
        return false;
    }
    $tmp = file_get_contents("../../$ERPNAME/config/lx_office.conf");
    $dbsec = false;
    $tmp = fgets($lxo,512);
    while (!feof($lxo)) {
       if (preg_match("/^[\s]*#/",$tmp)) { //Kommentar, überlesen
            $tmp = fgets($lxo,512);
            continue;
       }
       if (preg_match("!\[authentication/ldap\]!",$tmp)) $dbsec = false;
       if ($dbsec) {
            preg_match("/db[ ]*= (.+)/",$tmp,$hits);
            if ($hits[1]) $dbname=$hits[1];
            preg_match("/password[ ]*= (.+)/",$tmp,$hits);
            if ($hits[1]) $dbpasswd=$hits[1];
            preg_match("/user[ ]*= (.+)/",$tmp,$hits);
            if ($hits[1]) $dbuser=$hits[1];
            preg_match("/host[ ]*= (.+)/",$tmp,$hits);
            if ($hits[1]) $dbhost=($hits[1])?$hits[1]:"localhost";
            preg_match("/port[ ]*= ([0-9]+)/",$tmp,$hits);
            if ($hits[1]) $dbport=($hits[1])?$hits[1]:"5432";
            if (preg_match("/\[[a-z]+/",$tmp)) $dbsec = False;
            $tmp = fgets($lxo,512);
            continue;
       }
       preg_match("/cookie_name[ ]*=[ ]*(.+)/",$tmp,$hits);
       if ($hits[1]) $cookiename=$hits[1];
       preg_match("/dbcharset[ ]*=[ ]*(.+)/",$tmp,$hits);
       if ($hits[1]) $dbcharset=$hits[1];
       if (preg_match("!\[authentication/database\]!",$tmp)) $dbsec = true;
       $tmp = fgets($lxo,512);
    }
    $db=new myDB($dbhost,$dbuser,$dbpasswd,$dbname,$dbport,false);
    return $db;
}

function authuser($db,$login,$pwd,$ip) {
    $pwd = '{CRYPT}'.crypt($pwd,substr($login,0,2));
    $sql="select * from auth.user where login = '$login' and password = '$pwd'";
    $rs=$db->getAll($sql,"authuser_1");
    if (!$rs) {
        delSession($db,false,$ip);
        return false;
    }
    $sql="select * from auth.user_config where user_id = ".$rs[0]['id'];
    $rs=$db->getOne($sql,"authuser_1a");
    if (!$rs) return false;
    $sess = newSession($db,$ip,$login);
    return $sess;
} 
function userData($db,$id,$ip,$login,$pwd,$f=false) {
    $rs = chkSession($db,$id,$ip);
    if ($f) fputs($f,"chlS:$id,$ip,$login,$pwd,".$rs."\n");
    if (!$rs) {
        delSession($db,$id,$ip);
        $id =  authuser($db,$login,$pwd,$ip,false);
        if ($f) fputs($f,"chlS2:".$id."\n");
        if (!$id) return false;
    };
    $sql = "select U.id as user_id,SC.sess_value from auth.session S left join auth.session_content SC on S.id = SC.session_id ";
    $sql.= "left join auth.user U on U.login=SC.sess_value where S.ip_address = '$ip' and S.id = '$id' and sess_key='login'";
    $rs = $db->getOne($sql);
    if ($f) fputs($f,"chlS3:".$sql."\n");
    if ($f) fputs($f,"chlS3:".print_r($rs,true)."\n");
    $auth=array();
    if ($rs) { 
        $auth["login"]=$rs["sess_value"];
        $auth["id"]=$rs["user_id"];
        $sql="select * from auth.user_config where user_id=".$rs["user_id"];
        $rs=$db->getAll($sql,"authuser_2");
        if ($rs) { 
            $keys=array("dbname","dbpasswd","dbhost","dbport","dbuser");
            foreach ($rs as $row) {
                if (in_array($row["cfg_key"],$keys)) {
                    $auth[$row["cfg_key"]]=$row["cfg_value"];
                }
            }
            $auth["db"]=new myDB($auth["dbhost"],$auth["dbuser"],$auth["dbpasswd"],$auth["dbname"],$auth["dbport"],$showErr);
            $auth["id"] = $id;
            return $auth;
        } else {
            return false;
        }
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

function newSession($db,$ip,$login) {
    $newID=uniqid (rand());
    $id = md5($newID);
    $sql = "delete from auth.session_content where session_id in (select id from auth.session where ip_address = '$ip')";
    $rc  = $db->query($sql);
    $sql = "delete from auth.session where ip_address = '$ip'";
    $rc  = $db->query($sql);
    $sql = "insert into auth.session (id,mtime,ip_address) values ('$id',now(),'$ip')";
    $rc  = $db->query($sql);
    $sql = "insert into auth.session_content (session_id,sess_key,sess_value) values (";
    $sql.= "'$id','login','$login')";
    $rc  = $db->query($sql);
    return $id;
}
function chkSession($db,$id,$ip){
    $sql  = "SELECT * FROM auth.session S WHERE S.id = '$id' AND S.ip_address = '$ip' and mtime >= now() - Interval '2 Hours'";
    $rs = $db->getOne($sql);
    if ($rs) { 
        return true;
    } else {
        return false;
    }
}
?>
