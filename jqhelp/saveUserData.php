<?php
include( "../inc/stdLib.php" );
include( "../inc/UserLib.php" );
if ( $_POST["proto"] == 1 ) {
    $_POST["proto"] = 't';
}
else {
    $_POST["proto"] = 'f';
}
$rc = saveUserStamm( $_POST );
?>