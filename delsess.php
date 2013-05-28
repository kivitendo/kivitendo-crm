<?php
session_start();
while( list($key,$val) = each($_SESSION) ) {
        unset($_SESSION[$key]);
}
echo '<script type="text/javascript">window.location.href="status.php";</script>';
?>
