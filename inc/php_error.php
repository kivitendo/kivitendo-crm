<?php
// Wir werden unsere Fehler selbst behandeln
error_reporting(0);

// Benutzerdefinierte Fehlerfunktion
function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars, $debug = true) 
{
    // Zeitstempel für den Fehlereintrag
    $dt = date("Y-m-d H:i:s (T)");

    // Definiert ein assoziatives Array für den Fehler
    // Tatsächlich sollten wir nur
    // E_WARNING, E_NOTICE, E_USER_ERROR,
    // E_USER_WARNING und E_USER_NOTICE beachten.
    $errortype = array (
                E_ERROR              => 'Fehler',
                E_WARNING            => 'Warnung',
                E_PARSE              => 'Parser-Fehler',
                E_NOTICE             => 'Hinweis',
                E_CORE_ERROR         => 'Kern-Fehler',
                E_CORE_WARNING       => 'Kern-Warnung',
                E_COMPILE_ERROR      => 'Kompilierungsfehler',
                E_COMPILE_WARNING    => 'Kompilierungswarnung',
                E_USER_ERROR         => 'Benutzerfehler',
                E_USER_WARNING       => 'Benutzerwarnung',
                E_USER_NOTICE        => 'Benutzerhinweis',
                E_STRICT             => 'Laufzeitwarnung',
                E_RECOVERABLE_ERROR  => 'Abfangbarer fataler Fehler'
                );
    // Satz von Fehlern für welche ein Eintrag gespeichert wird.
    //$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
    $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_RECOVERABLE_ERROR);
    
    $err = "<errorentry>\n";
    $err .= "\t<datetime>" . $dt . "</datetime>\n";
    $err .= "\t<errornum>" . $errno . "</errornum>\n";
    $err .= "\t<errortype>" . $errortype[$errno] . "</errortype>\n";
    $err .= "\t<errormsg>" . $errmsg . "</errormsg>\n";
    $err .= "\t<scriptname>" . $filename . "</scriptname>\n";
    $err .= "\t<scriptlinenum>" . $linenum . "</scriptlinenum>\n";

    if (in_array($errno, $user_errors)) {
        $err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variablen") . "</vartrace>\n";
        $err .= "</errorentry>\n\n";
        error_log($err, 3, "/tmp/php_error.log");
    } else {
        if ( $debug ) {
            $err = "errornum: $errno \t errormsg: $errmsg \t scriptname: $filename \t linenum: $linenum\n";
            error_log($err, 3, "/tmp/php_error.log");
        };
    }

    // Speicher den Fehler im Log und schicke mir eine E-Mail falls es ein kritischer Benutzerfehler ist.
    if ($errno == E_USER_ERROR) {
        mail("root", "Critical User Error", $err);
    }
}

$old_error_handler = set_error_handler("userErrorHandler");

?>
