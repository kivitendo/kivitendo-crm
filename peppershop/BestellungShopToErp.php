<?php

//$debug = False;

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

$err->out("Shop $shopnr, Bestellimport",true);

//ERP-Instanz
$erpdb = new mydb($ERPhost,$ERPdbname,$ERPuser,$ERPpass,$ERPport,'pgsql',$err,$debug);
if ($erpdb->db->connected_database_name == $ERPdbname) {
    $erp = new erp($erpdb,$err,$divStd,$divVerm,$auftrnr,$kdnum,$preA,$preK,$invbrne,$mwstS,$OEinsPart,$lager,$pricegroup,$staffel,$parent,$ERPusrID);
     if ( $erp->cvarid == 0 ) {
        $err->out('Benutzerdefinierte Variable pepperkunde nicht gesetzt.');
        $err->out('Keine Beziehung der Kunden Shop/ERP möglich');
        exit(); 
     }
} else {
    $err->out('Keine Verbindung zur ERP',true);
    exit();
}

//Shop-Instanz
$shopdb = new mydb($SHOPhost,$SHOPdbname,$SHOPuser,$SHOPpass,$SHOPport,'mysql',$err,$debug);
if ($shopdb->db->connected_database_name == $SHOPdbname) {
     $shop = new pepper($shopdb,$err,$SHOPdbname,
                          $divStd,$divVerm,$minder,$nachn,$versandS,$versandV,$paypal,$treuhand,
                          $mwstLX,$mwstPGLX,$pricegroup,$mwstS,$variantnr,$variant,
                          false,false,false,false,$codeLX,$codeS);
//echo "<pre>"; print_r($shopdb->db); print_r($shopnr); echo "</pre>";
} else {
    $err->out('Keine Verbindung zum Shop',true);
    exit();
}

$bestellungen = $shop->getBestellung($ERPusrID);
//print_r($bestellungen); exit(1);
$cnt = 0;
$errors = 0;

$err->out("Bestellimport vom Shop $shopnr",true);
if ($bestellungen) foreach ($bestellungen as $row) {
    $rc = $erp->mkAuftrag($row,$shopnr,$longtxt);
    if ( $rc < 0 ) {
           $errors++;
           $err->out("Fehler mkAuftrag ".$row['cusordnumber'],true);
    } else if ( $rc != '' ) {
           if ( $rc != $row['customer']['kunden_nr'] )
               $shop->setKundenNr($row['customer']['shopid'],$rc); 
           $shop->setAbgeholt($row['cusordnumber']); 
           $cnt++;
           $err->out("ok",true);
    } else {
        $err->out("Fehler Kunde zuordnen ".$row['customer']['shopid'].":".$row['cusordnumber'],true);
        $errors++;
    } 
}
$err->out('Von '.count($bestellungen)." Bestellungen $cnt übertragen, $errors nicht",true);
if ( $api != "cli" ) {
    echo "<br /><a href='../../oe.pl?vc=customer&type=sales_order&nextsub=orders&action=Weiter&open=1&notdelivered=1&delivered=1&l_ordnumber=Y&l_transdate=Y&l_reqdate=1&l_name=Y&l_employee=Y&l_amount=Y' target='_top'>Auftragsliste</a>";
    echo "</body>\n</html>\n";
}

?>
