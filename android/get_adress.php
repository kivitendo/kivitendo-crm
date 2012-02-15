<?php

$f = fopen("/tmp/android.txt","a");
fputs($f,"get_adress:\n".print_r($_POST,true));
require("androidLib.php");
$dbA = authDB();
$auth = userData($dbA,$_POST["sessid"],$_POST["ip"],$_POST["login"],$_POST["password"],$f);
fputs($f,"Auth:".print_r($auth,true));

if ($auth["db"]) {
    $postarray = array("name","city","street","phone");
    $custsql = array();
    $vendsql = array();
    $contsql = array();
    $rs = false;
    $db = $auth["db"];
    foreach($postarray as $key) {
        if (!empty($_POST[$key])) {
            $custsql[] = $key." ilike '%".$_POST[$key]."%'";
            $vendsql[] = $key." ilike '%".$_POST[$key]."%'";
            if ($key=="name") {
                $contsql[] = "cp_given".$key." ilike '%".$_POST[$key]."%'";
            } else {
                $contsql[] = "cp_".$key." ilike '%".$_POST[$key]."%'";
            }
        }
    }
    $offset = ($_POST["offset"])?$_POST["offset"]:0;
    $max = ($_POST["max"])?$_POST["max"]:25;
    $vonbis = " offset $offset limit $max";
    $sql      = "SELECT id,name,country,zipcode,street,city,email,homepage as www,phone,'' as mobile,fax,notes,contact,";
    $cust_sql = $sql."'C' as tab FROM customer WHERE ".implode(" and ",$custsql);
    $vend_sql = $sql."'V' as tab FROM vendor WHERE ".implode(" and ",$vendsql);
    $pers_sql = "SELECT cp_id as id,cp_name as name,cp_country as country,cp_zipcode as zipcode,cp_street as street,";
    $pers_sql.= "cp_city as city,cp_email as email,cp_homepage as www,cp_phone1 as phone,cp_mobile1 as mobile,cp_fax as fax, cp_notes as notes,'' as contact,";
    $pers_sql = "'P' as tab FROM contacts WHERE ".implode(" and ",$contsql);
    $mysql = "";
    if ($_POST["cbc"]=="t") {
        $mysql = $cust_sql;
        fputs($f,$mysql."\n");
    };
    if ($_POST["cbv"]=="t") {
        if ($mysql != '') $mysql .= " union ";
        $mysql .= $vend_sql;
        fputs($f,$mysql."\n");
    }
    if ($_POST["cbp"]=="t") {
        if ($mysql != '') $mysql .= " union ";
        $mysql .= $pers_sql;
    }
    fputs($f,$mysql.$vonbis."\n");
    $rs = $db->getAll($mysql.$vonbis);

    header("Content-type: text/json; charset=utf-8;");   
    if (!$rs) {
        $rs[0] = array("id"=>"","name"=>"Nichts gefunden","country"=>"","zipcode"=>"","street"=>"","city"=>"",
                "email"=>"","www"=>"","phone"=>"","mobile"=>"","fax"=>"","notes"=>"","contact"=>"","tab"=>""); 
    } else {
        $max = count($rs);
        if ($max > 25) {
            $rs = array_slice($rs,$offset,25);
        }
        $rs[] = array("max"=>$max,"offset"=>$offset);
    };
    fputs($f,print_r($rs,true));
    print(json_encode($rs));
} else {
    echo "NO";
}
fclose($f);
?>
