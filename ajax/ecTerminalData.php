<?php
require_once __DIR__.'/../inc/ajax2function.php';

function getTerminalCustomerData( $customer_id ){
  $sql = "SELECT val FROM crmdefaults WHERE employee = -1 AND key = 'ec_terminal_ip-adress' OR key = 'ec_terminal_port' OR key = 'ec_terminal_passwd' UNION SELECT greeting || ' ' || name AS customername FROM customer WHERE id=$customer_id ORDER BY 1";
  echo $GLOBALS['dbh']->getALL( $sql, TRUE ); //as json
}
?>