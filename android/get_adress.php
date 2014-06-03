<?php

require("androidLib.php");

if ( debug ) {
    include ('logging.php');
    $log = new logging();
    $log->write("get_adress:\n".print_r($_POST,true));
} else {
    $log = false;
}

$dbA  = authDB();
$auth = userData($dbA,$_POST["sessid"],$_POST["ip"],$_POST['mandant'],$_POST["login"],$_POST["password"],$f);

if ( $log ) $log->write("Auth:".print_r($auth,true));
function mkwort($txt) {
    $txt = strtr($txt,'?*','_%');
    if ( substr($txt,0,1) == '!' ) return substr($txt,1);
    return '%'.$txt;
}
if ($auth["db"]) {
    $postarray = array("name","city","street","phone");
    $custsql = array();
    $vendsql = array();
    $contsql = array();
    $rs = false;
    $db = $auth["db"];
    foreach( $postarray as $key ) {
        if ( !empty($_POST[$key] )) {
            $custsql[] = $key." ilike '".mkwort($_POST[$key])."%'";
            $vendsql[] = $key." ilike '".mkwort($_POST[$key])."%'";
            if ( $key=="name" ) {
                $contsql[] = "cp_given".$key." ilike '".mkwort($_POST[$key])."%'";
            } else {
                $contsql[] = "cp_".$key." ilike '".mkwort($_POST[$key])."%'";
            }
        }
    }
    $offset = ($_POST["offset"])?$_POST["offset"]:0;
    $max    = ($_POST["max"])?$_POST["max"]:25;
    $vonbis = " offset $offset limit $max";
    $sql      = "SELECT id,name,country,zipcode,street,city,email,homepage as www,phone,'' as mobile,fax,notes,contact,";
    $cust_sql = $sql."'C' as tab FROM customer WHERE ".implode(" and ",$custsql);
    $vend_sql = $sql."'V' as tab FROM vendor WHERE ".implode(" and ",$vendsql);
    $pers_sql = "SELECT cp_id as id,cp_name as name,cp_country as country,cp_zipcode as zipcode,cp_street as street,";
    $pers_sql.= "cp_city as city,cp_email as email,cp_homepage as www,cp_phone1 as phone,cp_mobile1 as mobile,";
    $pers_sql.= "cp_fax as fax, cp_notes as notes,'' as contact,";
    $pers_sql = "'P' as tab FROM contacts WHERE ".implode(" and ",$contsql);
    $mysql    = "";
    if ($_POST["cbc"]=="t") {
        $mysql = $cust_sql;
    };
    if ($_POST["cbv"]=="t") {
        if ($mysql != '') $mysql .= " union ";
        $mysql .= $vend_sql;
    }
    if ($_POST["cbp"]=="t") {
        if ($mysql != '') $mysql .= " union ";
        $mysql .= $pers_sql;
    }

    if ( $log ) $log->write($mysql.$vonbis);

    $rs = $db->getAll($mysql.$vonbis);

    header("Content-type: text/json; charset=utf-8;");   
    if ( !$rs ) {
        $rs[0] = array("id"=>"","name"=>"Nichts gefunden","country"=>"","zipcode"=>"","street"=>"","city"=>"",
                       "email"=>"","www"=>"","phone"=>"","mobile"=>"","fax"=>"","notes"=>"","contact"=>"","tab"=>""); 
    } else {
        $max = count($rs);
        if ($max > 25) {
            $rs = array_slice($rs,$offset,25);
        }
        $rs[] = array("max"=>$max,"offset"=>$offset);
    };
    if ( $log ) $log->write(print_r($rs,true));
    print(json_encode($rs));
} else {
    echo "NO";
}
if ( $log ) $log->close();
?>
