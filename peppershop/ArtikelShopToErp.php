<?php

//$debug = true;

$api = php_sapi_name();
if ( $api != "cli" ) {
    echo "<html>\n<head>\n<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>\n</head>\n<body>\n";
    @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);
    $shopnr = $_GET["Shop"];
    $nofiles = ( $_GET["nofiles"] == '1' )?true:false;
} else {
    $p = array('shopnr','nofiles');
    if ( $argc > 1 ) {
        for( $i=1; $i<count($argv); $i++)  {
                $tmp = explode("=",trim($argv[$i]));
                if ( count($tmp) < 2 ) {
                    echo "Falscher Aufruf: php ArtikelErpToShop.php shopnr=1 [nofiles=1]\n";
                    exit (-1);
                };
                if ( ! in_array(strtolower($tmp[0]),$p) ) {
                    echo "Falscher Aufruf: php ArtikelErpToShop.php shopnr=1 [nofiles=1]\n";
                    exit (-1);
                };
                ${$tmp[0]} = trim($tmp[1]);
        }
    } else {
        $shopnr=false;
        $nofiles=false;
    }
}
$pfad = getcwd();
include_once("$pfad/conf$shopnr.php");
include_once("$pfad/error.php");
//Fehlerinstanz
$err = new error($api);

include_once("$pfad/dblib.php");
include_once("$pfad/pepper.php");
include_once("$pfad/erplib.php");


echo "Preise im Shop: ".(($mwstS==1)?'Brutto':'Netto')."<br>";
echo "Preise in der ERP: ".(($mwstLX==1)?'Brutto':'Netto')."<br>";
if ($pricegroup>0) echo "Preise der Preisgruppe: ".(($mwstPGLX==1)?'Brutto':'Netto')."<br>";
echo "Preisgruppe: ".(($pricegroup>0)?$pricegroup:'keine')."<br>";
//ERP-Instanz
$erpdb = new mydb($ERPhost,$ERPdbname,$ERPuser,$ERPpass,$ERPport,'pgsql',$err,$debug);
if ($erpdb->db->connected_database_name == $ERPdbname) {
    $erp = new erp($erpdb,$err,$divStd,$divVerm,$auftrnr,$kdnum,$preA,$preK,$invbrne,$mwstS,$OEinsPart,$lager,$pricegroup,$staffel,$parent,$ERPusrID);
} else {
    $err->out('Keine Verbindung zur ERP',true);
    exit();
}
//Shop-Instanz
$shopdb = new mydb($SHOPhost,$SHOPdbname,$SHOPuser,$SHOPpass,$SHOPport,'mysql',$err,$debug);
if ($shopdb->db->connected_database_name == $SHOPdbname) {
     $shop = new pepper($shopdb,$err,$SHOPdbname,$divStd,$divVerm,$minder,$nachn,$versandS,$versandV,$paypal,$treuhand,$mwstLX,$mwstPGLX,$pricegroup,$mwstS,$variantnr,$variant,false,false,false,false,$codeLX,$codeS);
} else {
    $err->out('Keine Verbindung zum Shop',true);
    exit();
}

$artikel = $shop->getAllArtikel();
$cnt = 0;
$errors = 0;
//Artikel die mehreren Warengruppen zugeordnet sind, werden nur einmal importiert.
//Es wird dann auch nur die erste Warengruppe angelegt.
if ( $api != 'cli' ) ob_start();
$err->out("Artikelimport von Shop $shopnr",true);
echo "<table>";
if ($artikel) foreach ($artikel as $row) {
     $rc = $erp->chkPartnumber($row,true);
     if ($rc) { 
    	$cnt++;
     } else { 
        $err->out('Fehler: '.$row['partnumber'],true);
	$errors++;
     }
}
echo "</table>";
$err->out('',true);
$err->out("$cnt Artikel geprüft bzw. übertragen, $errors Artikel nicht",true);
if ( $api != "cli" ) {
    echo "</body>\n</html>\n";
}
?>
