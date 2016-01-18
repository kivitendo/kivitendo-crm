<?php
session_destroy();
//session_unset();
include_once "inc/stdLib.php";
session_start();

echo '<script type="text/javascript">window.location.href="status.php";</script>';
?>
