<?php
include( "../inc/stdLib.php" );
include( "../inc/UserLib.php" );
//include( "inc/crmLib.php" );
//print_r( $_POST );
//echo "saveUserData";
    if ( $_POST["proto"] == 1 ) {
        $_POST["proto"] = 't';
    }
    else {
        $_POST["proto"] = 'f';
    }
    $rc = saveUserStamm( $_POST );




?>