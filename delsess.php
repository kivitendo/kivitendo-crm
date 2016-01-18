<?php
session_start();
//echo '<script type="text/javascript">window.location.href="'.$_SESSION['baseurl'].'controller.pl?action=LoginScreen/logout";</script>';
while( list($key,$val) = each($_SESSION) ) unset($_SESSION[$key]);
//while( list($key,$val) = each($_COOKIE) ) unset($_COOKIE[$key]);
$_SESSION['clear'] = TRUE;
echo '<script type="text/javascript">window.location.href="status.php";</script>';
?>
