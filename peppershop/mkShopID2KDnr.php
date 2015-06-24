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

$sql = "SELECT k_ID,Kunden_NR FROM kunde WHERE Kunden_NR != '0'";
$kunden = $shopdb->getAll($sql);

$sqlkdnr = "SELECT customernumber FROM customer WHERE id = ";
$sqlupd  = "UPDATE kunde SET Kunden_NR = '%s' WHERE k_ID = %d";


if ( $kunden ) foreach ( $kunden as $nr ) {
	$rs = $erpdb->getOne($sqlkdnr.$nr['kunden_nr']);
	if ( isset($rs['customernumber']) ) {
            echo $nr['k_id']." -> ".$rs['customernumber']."\n";
	    $rc = $shopdb->query(sprintf($sqlupd,$rs['customernumber'],$nr['k_id']));
        };
}

?>
