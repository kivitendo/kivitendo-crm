<?php
//generiert aus einer url einen Funftionsaufruf,
//Bsp.: jqhelp/filename.php?action=functionname&data=DatenOderSerialisierteDaten
require_once("../inc/stdLib.php");
header('Content-Type: application/json');
( isset( $_GET['action'] ) and function_exists( $_GET['action'] ) ) or die( 'Param action or function: "'.array_shift( $_GET ).'" not defined' );
$_GET['action']( isset( $_GET['data'] ) ? $_GET['data'] : '' ); //Funktion aufrufen
?>