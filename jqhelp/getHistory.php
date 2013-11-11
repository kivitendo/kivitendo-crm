<?php
require_once("../inc/stdLib.php");
$history_data = accessHistory();
$liste = '';
if ( $history_data ) foreach ( $history_data as $key => $value ) {
    $liste .= '<li id="'.$value[2].$value[0].'"><a href="javascript:void(0);">'.$value[1].'</a></li>';
}
echo $liste;
?>