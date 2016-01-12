<?php
if ( !isset($_SESSION["db"])     ||
     !isset($_SESSION["cookie"]) ||
     !isset($_COOKIE[$_SESSION["cookie"]]) ||
     ( $_SESSION["sessid"] <> $_COOKIE[$_SESSION["cookie"]]) ) {
        while( list($key,$val) = each($_SESSION) ) {
            unset($_SESSION[$key]);
        }

        if ( !anmelden() ) echo "+++++++++++++++++++++++++++ Fehler: anmelden fehlgeschlagen! +++++++++++++++++++++++++++";
        $_SESSION["loginok"] = "ok";
} else {
    //global $dbh;
    if( varExist( $_SESSION['dbhost'] ) ) $dbh = new myPDO( $_SESSION["dbhost"], $_SESSION["dbport"], $_SESSION["dbname"], $_SESSION["dbuser"], $_SESSION["dbpasswdcrypt"], $_SESSION["sessid"] );
    $_SESSION["db"] = new myDB($_SESSION["dbhost"],$_SESSION["dbuser"],$_SESSION["dbpasswd"],$_SESSION["dbname"],$_SESSION["dbport"]);
    $_SESSION["loginok"] = "ok";
}

?>
