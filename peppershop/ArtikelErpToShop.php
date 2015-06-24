<?php


$api = php_sapi_name();
if ( $api != "cli" ) {
    echo "<html>\n<head>\n<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>\n</head>\n<body>\n";
    @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);
    $shopnr = $_GET["Shop"];
    $start = ($_GET["start"]>0)?$_GET['start']:0;
    $counter = ($_GET["counter"]>0)?$_GET['counter']:0;
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
include_once("$pfad/Picture.php");


//Bilder
$pict = new picture($ERPftphost,$ERPftpuser,$ERPftppwd,$ERPimgdir,$SHOPftphost,$SHOPftpuser,$SHOPftppwd,$SHOPimgdir,$err);
//$pict->original = false;

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
     $shop = new pepper($shopdb,$err,$SHOPdbname,$divStd,$divVerm,$minder,$nachn,$versandS,$versandV,$paypal,$treuhand,$mwstLX,$mwstPGLX,$pricegroup,$mwstS,$variantnr,$variant,$pict,$nopic,$nopicerr,$nofiles,$codeLX,$codeS);
} else {
    $err->out('Keine Verbindung zum Shop',true);
    exit();
}
$max = 100;
$artikel = $erp->getParts($pricegroup,$shopnr,$start,$max);
$lang = $shop->getLang("de");
$cnt = 0;
$errors = 0;

if ( $api != 'cli' ) ob_start();

$err->out("Artikelexport für Shop $shopnr",true);

if ($artikel) foreach ($artikel as $row) {
    $lastpartnr = $row['partnumber'];
    $rc = $shop->saveArtikel($row,"de",$lastpartnr);
    if ($rc) { 
       $cnt++;
       if ( $cnt % 10 == 0 ) $err->out(".");  
    } else {
       $errors++;
    }
}
$err->out('',true);
if ($counter > 0 ) { $anzahl = $counter*$max+$cnt; }
else { $anzahl = $cnt; };
$err->out("$anzahl Artikel übertragen, $errors Artikel nicht",true);

if ( count($artikel) >= $max ) {
$counter++;
echo "CNT:".count($artikel)."!MAX:$max!start:$start!counter:$counter!";
$url = $_SERVER['PHP_SELF']."?Shop=$shopnr&start=".($start+$max)."&counter=$counter&lastpart=$lastpartnr";
?>
<script language="JavaScript">setTimeout("location.href='<?PHP echo $url ?>'",1000);</script>
<?php }
if ( $api != "cli" ) {
    echo "</body>\n</html>\n";
}
?>
