<?php
/*
Lieferantenrechnung - Pfad zum Dokument erstellen und zurück geben
*/
require_once '../inc/ajax2function.php';

// Rekursives Durchsuchen der Unterordner nach Dateien
function rglob($pattern, $flags = 0) {

     $files = glob($pattern, $flags);
     foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
          $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
     }
    return $files;
}

function showInvo($data) {
     $vars = (array)json_decode($data);
     $vendor_id = $vars['vendor_id'];
     $invoice_no = $vars['invoice_no'];
     $path = '../dokumente/'.$_SESSION['dbData']['dbname'].'/';
     //Vendor Nummer abfragen
     $sql = "SELECT vendornumber FROM vendor WHERE id = '".$vendor_id."'";
     $rs = $GLOBALS['dbh']->getOne($sql);

     $path = $path."V".$rs['vendornumber']."/*".$invoice_no."*.pdf";
     $temp = '';

     $file_arr = rglob($path);
     $temp = $file_arr[0];
     // Rückgabestring für die HREF der Fancybox
     if($temp != ''){
        echo json_encode(array("link" => "crm".substr($temp, 2)));
     }else{
        return 0;
     }
}


?>