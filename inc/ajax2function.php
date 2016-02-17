<?php
//generiert aus einer url einen Funftionsaufruf,
//Bsp.: ajax/ajaxFilename.php?action=functionname&data=DatenOderSerialisierteDaten
require_once("../inc/stdLib.php");
header('Content-Type: application/json');
( isset( $_GET['action'] ) and function_exists( $_GET['action'] ) ) or die( 'Param action or function: "'.array_shift( $_GET ).'" not defined' );
if( isset( $_GET['data'] ) ) $_GET['action']( $_GET['data'] ); //Funktion mit Parameter aufrufen
else $_GET['action'](); //..ohne Parameter
?>
