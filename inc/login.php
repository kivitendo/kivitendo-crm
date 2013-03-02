<?php
while( list($key,$val) = each($_SESSION) ) {
    unset($_SESSION[$key]);
};
clearstatcache();
if ($_POST["erpname"]) {
    if ( is_file("../".$_POST["erpname"]."/config/".$_SESSION['erpConfigFile'].".conf") ) {
        if ( is_writable("inc/conf.php") ) {
            $name = false;
            $configfile = file("inc/conf.php");
            $f = fopen("inc/conf.php","w");
            foreach( $configfile as $row ) {
                $tmp = trim($row);
                if ( preg_match('/ERPNAME/',$tmp) ) {
                    fputs($f,'$ERPNAME="'.$_POST["erpname"]."\";\n");
                    $name=true;
                } else {
                    if ( preg_match('/\?>/',$tmp) && !$name ) fputs($f,'$ERPNAME="'.$_POST["erpname"].'";'."\n");
                    fputs($f,$tmp."\n");
                }
            }
            fclose($f);
        } else {
            echo "inc/conf.php ist nicht beschreibbar";
        }
    }
    $_SESSION['ERPNAME'] = $_POST["erpname"];
}

$conffile = '';
if ( substr(getcwd(),-3) == "inc" || substr(getcwd(),-6) == "jqhelp"  ) {
    $conffile = "../";
}
$conffile .= "../".$_SESSION['ERPNAME']."/config/".$_SESSION['erpConfigFile'].".conf";

if ( is_file($conffile) ) {
    $tmp = anmelden();
    if ( $tmp ) {
        if ( chkVer() ) {
            $db = $_SESSION["db"];
            $_SESSION["loginok"] = "ok";
            $LOGIN = True;
            require ("update_neu.php");
        } else {
            echo "db-Version nicht ok";
            exit;
        }
    } else {
        echo $_SESSION["db"]."Session abgelaufen.";
        $Url  = (empty( $_SERVER['HTTPS'] )) ? 'http://' : 'https://';
        $Url .= $_SERVER['HTTP_HOST'];
        $Url .= preg_replace( "^crm/.*^", "", $_SERVER['REQUEST_URI'] );
        unset($_SESSION);
        header('Location: '.$Url.'login.pl?x=1');
        exit;
    }
} else {
    echo "Configfile nicht gefunden<br>$PHPSELF<br>";
    echo "ERP V 3.0.0 oder gr&ouml;&szlig;er erwartet!!!<br><br>";
    echo "<form name='erppfad' method='post' action='".$PHPSELF."'>";
    echo "Bitte den Verzeichnisnamen (nicht den Pfad) der ERP eingeben:<br>";
    echo "<input type='text' name='erpname'>";
    echo "<input type='submit' name='saveerp' value='sichern'>";
    echo "</form>";
    exit;
}
?>
