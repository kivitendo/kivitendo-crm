<?php
$f = fopen("/tmp/android.txt","a");
fputs($f,"!in GetAll!");
fputs($f,print_r($_POST,true));
$inclpa=ini_get('include_path');
ini_set('include_path',$inclpa.":../:../inc");

include("FirmenLib.php");
include("persLib.php");
require("androidLib.php");
$dbA = authDB();
$auth = userData($dbA,$_POST["sessid"],$_POST["ip"]);
fputs($f,"!Auth!");
fputs($f,print_r($auth,true));

if ($auth["db"]) {
    $custsql = array();
    $vendsql = array();
    $contsql = array();
    $rs = false;
    $db = $auth["db"];
    $tab = $_POST["tab"];
    $id = $_POST["id"];
    fputs($f,$id.":".$tab."\n");
    if ($tab == "P") {
        $rs=getKontaktStamm($id,"..");
    } else {
        $rs=getFirmenStamm($id,true,$tab);        
    }
    fputs($f,print_r($rs,true));
    header("Content-type: text/json; charset=utf-8;");   
    if (!$rs) {
        echo "";
    } else {
      fputs($f,print_r($rs,true));
      print(json_encode($rs));
    };
} else {
    echo "";
}
fclose($f);
?>
