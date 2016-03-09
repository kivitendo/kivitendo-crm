<?php
//generiert aus einer url einen Funftionsaufruf,
//Bsp.: ajax/ajaxFilename.php?action=functionname&data=DatenOderSerialisierteDaten
require_once("../inc/stdLib.php");
header('Content-Type: application/json');
$action = varExist( $_GET, 'action' ) ? $_GET['action'] : varExist( $_POST, 'action' );
$data   = varExist( $_GET, 'data' ) ? $_GET['data'] : varExist( $_POST, 'data' );
( $action and function_exists( $action ) ) or die( 'Param action or function: "'.$action.'" not defined' );

if( $data ) $_GET['action']( $data ); //Funktion mit Parameter aufrufen
else $_GET['action'](); //..ohne Parameter
?>
