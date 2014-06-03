<?php
require("androidLib.php");

if ( debug ) {
    include ('logging.php');
    $log = new logging();
    $log->write("!get_addressdata!");
    $log->write(print_r($_POST,true));
} else {
    $log = false;
};

include("FirmenLib.php");
include("persLib.php");

$dbA = authDB();
$auth = userData($dbA,$_POST["sessid"],$_POST["ip"],$_POST['mandant'],$_POST["login"],$_POST["password"],$f);

if ( $log ) $log->write("auth:".print_r($auth,true));

if ($auth['db']) {
    $db      = $auth['db'];
    $custsql = array();
    $vendsql = array();
    $contsql = array();
    $rs      = false;
    $tab     = $_POST["tab"];
    $id      = $_POST["ID"];

    if ($tab == "P") {
        $rs = getKontaktStamm($id,"..");
    } else {
        $rs = getFirmenStamm($id,true,$tab);        
    }
    if ( $log ) $log->write(print_r($rs,true));

    header("Content-type: text/json; charset=utf-8;");   
    if ( !$rs ) {
        echo "";
    } else {
      while( list($key,$val) = each($rs) ) {
         if ($val == Null) $rs[$key] = '';
      }
      print(json_encode($rs));
    };
} else {
    echo "";
}
if ( $log ) $log->close();
?>
