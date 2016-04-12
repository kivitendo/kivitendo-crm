<?php
session_start();
//echo '<script type="text/javascript">window.location.href="'.$_SESSION['baseurl'].'controller.pl?action=LoginScreen/logout";</script>';
while( list($key,$val) = each($_SESSION) ) unset($_SESSION[$key]);
//while( list($key,$val) = each($_COOKIE) ) unset($_COOKIE[$key]);
$_SESSION['clear'] = TRUE;
$relocate = 'status.php';
if(isset($_GET['url'])) {
    $temp = urldecode(http_build_query($_GET));
    $pos = strpos($temp, 'crm');
    $relocate = substr($temp, $pos+4);
}
echo '<script type="text/javascript">window.location.href="'.$relocate.'";</script>';
?>
