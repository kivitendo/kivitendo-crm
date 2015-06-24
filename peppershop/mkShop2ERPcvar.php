<?php
//$shopnr = $_GET['Shop'];
$shopnr = '';
include_once("conf$shopnr.php");
include_once("error.php");
//Fehlerinstanz
$api = php_sapi_name();
$err = new error($api);

include_once("dblib.php");
include_once("pepper.php");
include_once("erplib.php");

$erpdb = new mydb($ERPhost,$ERPdbname,$ERPuser,$ERPpass,$ERPport,'pgsql',$err,$debug);
$shopdb = new mydb($SHOPhost,$SHOPdbname,$SHOPuser,$SHOPpass,$SHOPport,'mysql',$err,$debug);

$sql = "SELECT * FROM custom_variable_configs WHERE name = 'pepperkunde'";
$rs = $erpdb->getOne($sql);
if ( isset($rs['id'] ) ) {
    $cvarid = $rs['id'];
} else {
    exit();
}


$sql = "SELECT k_ID,Kunden_NR FROM kunde WHERE Kunden_NR != '0'";
$kunden = $shopdb->getAll($sql);

$sqlkdnr = "SELECT id FROM customer WHERE customernumber = '%s' ";
$sqldel  = "DELETE FROM custom_variables WHERE config_id=$cvarid AND trans_id=";
$sqlins  = "INSERT INTO custom_variables (config_id,trans_id,text_value) VALUES ($cvarid,%d,'%s')";

$i=0;

if ( $kunden ) foreach ( $kunden as $nr ) {
	$rs = $erpdb->getOne(sprintf($sqlkdnr,$nr['kunden_nr']));
	if ( isset($rs['id']) ) {
            echo $nr['k_id']." -> ".$nr['kunden_nr']."\n";
	    $rc = $erpdb->query($sqldel.$rs['id']);
	    $rc = $erpdb->query(sprintf($sqlins,$rs['id'],$nr['kunden_nr']));
        };
}

?>
