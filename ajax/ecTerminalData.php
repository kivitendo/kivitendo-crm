<?php
require_once __DIR__.'/../inc/ajax2function.php';

function getTerminalData(){
  $sql = "SELECT val FROM crmdefaults WHERE employee = -1 AND key = 'ec_terminal_ip-adress' OR key = 'ec_terminal_port' OR key = 'ec_terminal_passwd'";
  echo $GLOBALS['dbh']->getALL( $sql, TRUE ); //as json
}
?>