<?php
if ( isset($_GET['check']) && $_GET['check']==1 ) {
    $check = true;
    $inclpa = ini_get('include_path');
    ini_set('include_path',$inclpa.":../:./inc:../inc");
} else {
       $check = false;
}
include_once("inc/stdLib.php");
include($_SESSION['crmpath'].'/inc/conf.php');
include($_SESSION['crmpath'].'/inc/version.php');
if ( ob_get_level() == 0 ) ob_start();
echo "<br>Installation der Version $VERSION";
echo " der Datenbankinstanz: ".$_SESSION["dbname"]."<br>";
ob_flush();
flush();
if ( !file_exists($_SESSION['crmpath'].'/log') ) mkdir($_SESSION['crmpath'].'/log',0755);
if ( $log = @fopen($_SESSION['crmpath'].'/log/install.log',"a") ) {
    $logfile = $_SESSION['crmpath']."/log/install.log";
    echo 'Logfile in '.$logfile.'<br>';
} else {
    echo "Keine Schreibrechte f&uuml;r Logfile in log.<br>";
    echo "Installation abgebrochen";
    exit (1);
}

fputs($log,date("d.m.Y H:i:s")."\n");
fputs($log,'Installation: '.$VERSION."\n");

echo "Verzeichnisse und Schreibrechte im CRM-Verzeichnis pr&uuml;fen<br>";
$ok        = "<b>ok</b>";
$fehler    = "<font color='red'>Fehler!</font>";
$mkdir     = array('dokumente','dokumente/'.$_SESSION["dbname"],'tmp');
$writeable = array('dokumente','dokumente/'.$_SESSION["dbname"],'vorlage','inc/conf.php','tmp');
if ( !isset($_SESSION['dir_mode']) ) $_SESSION['dir_mode'] = 0755;
foreach ( $mkdir as $chk ) {
    if ( !file_exists($_SESSION['crmpath'].'/'.$chk) )  {
        $rc = mkdir($_SESSION['crmpath'].'/'.$chk, $_SESSION['dir_mode']);
        if ( $rc ) {
            if ( $_SESSION["dir_group"] ) chgrp($_SESSION['crmpath'].'/'.$chk, $_SESSION['dir_group']);
            echo 'Verzeichnis: '.$chk.' erfolgreich erstellt.<br>';
            fputs($log,"Verzeichnis $chk : erstellt\n");
        } else {
            echo 'Konte Verzeichnis: '.$chk.' nicht erstellen.<br>';
            fputs($log,"Verzeichnis $chk : nicht erstellt!!\n");
        }
    } else {
        echo 'Verzeichnis: '.$chk.' existiert.<br>';
        fputs($log,"Verzeichnis $chk : vorhanden\n");
    }
};

foreach ( $writeable as $chk ) {
    if ( is_writable($_SESSION['crmpath'].'/'.$chk) ) {
        echo "$chk : $ok<br>";
        fputs($log,"$chk : ok\n");
    } else {
        echo "$chk : $fehler kein Schreibrecht<br>";
        fputs($log,"$chk : fehler\n");
    }
}

echo "<br>Voraussetzungen pr&uuml;fen:<br>";
    $path = ini_get("include_path");
    fputs($log,"Suchpfad: $path\n");
    $pfade = explode(":",$path);
    if( function_exists('curl_init')){
        echo "Curl: $ok<br>";
        fputs($log,"Curl : ok\n");
    } else {
      echo "Curl: $fehler<br>";
      fputs($log,"Curl : Fehler\n");
    }
    $chkfile=array("fpdf","fpdi","Mail","Mail/mime","jpgraph","Contact_Vcard_Build","Contact_Vcard_Parse");
    $chkstat=array(1,1,0,0,0,0,0,0,0);
    $OK=true;
    $pos=0;
    $dbok=true;
    foreach($chkfile as $key=>$file) {
        $ook = false;
        if ( is_array($file) ) {
            foreach ( $file as $altfile ) {
                echo "$altfile: ";
                fputs($log,"$altfile: ");
                $aok = false;
                foreach( $pfade as $path ) {
                    $path = ((substr($path,0,1)=="/")?"":$p).$path;
                    if ( is_readable($path."/".$altfile.".php" ) || is_readable($path."/".$altfile.".js")) {
                        $aok = true;
                        $ook = true;
                        break;
                    }
                }
                if ( $aok ) {
                    echo "ok<br>";
                    fputs($log,"ok\n");
                } else {
                    echo "fehlt<br>";
                    fputs($log,"fehlt\n");
                }
            }
            echo "$key: ";
            fputs($log,"$key ");
            if ( $ook ) {
                echo "$ok<br>";
                fputs($log,"ok\n");
            }
        } else {
            echo "$file: ";
            fputs($log,"$file: ");
            foreach( $pfade as $path ) {
                if ( is_readable($path."/".$file.".php") ) {
                    $ook=true;
                    break;
                }
            }
            if ( $ook ) {
                echo "$ok<br>";
                fputs($log,"ok\n");
            };
        }
        if ( !$ook ) {
            $OK = false;
            if ( $chkstat[$pos]==0 ) {
                echo "<font color='red'>dieses Paket fehlt oder kann nicht geladen werden.</font><br>";
                fputs($log,"Fehler\n");
            } else {
                $dbok = false;
                echo "<font color='red'><b>unbedingt erforderlich!!</b></font><br>";
                fputs($log,"Fehler: erforderlich\n");
            }
        }
        $pos++;
    }
        if ( !$OK ) {
            echo "Einige Voraussetzungen sind nicht erf&uuml;llt.<br>&Uuml;berpr&uuml;fen Sie die die Variable 'include_path' in der 'php.ini'.<br>";
            echo "Andernfalls installieren Sie die noch fehlenden Pakete.<br>";
            echo "Aktueller include_path: ".ini_get('include_path').'<br>';
        }
        fputs($log,"\n");

//ERP da?
    $OK = is_file($_SESSION['crmpath']."/../$ERPNAME/config/$erpConfigFile.conf");
    fputs($log,"$ERPNAME : ");
    fputs($log,(($OK)?"gefunden":"fehler")."\n");
    if ( $OK ) {
        echo "ERP $erpConfigFile.conf gefunden<br>";
    } else {
        echo "ERP ($erpConfigFile.conf) nicht gefunden. Abbruch.<br>";
        exit(1);
    }

if ( $dbok ) {
    if ( $_GET['check']==2 || $_GET['check']=='' ) {
        $sql = "SELECT * from schema_info  where tag like 'release_%' order by tag desc limit 1";
        $rs  = $_SESSION['db']->getAll($sql);
        if ( substr($rs[0]["tag"],0,11)>="release_2_6" ) {
            fputs($log,$rs[0]["version"]." als Basis\n");
            echo "$ok. ERP-DB gefunden<br>";
        } else {
            fputs($log,"Keine gueltige ERP-DB gefunden\n");
            echo "$fehler Keine gültige ERP-DB gefunden (".$rs[0]["version"].")</ br>";
            echo "Diese Version arbeitet nur mit ERP-Versionen >= 3.0.1 oder der aktuellen Git-Version zusammen";
            exit;
        }
    }
} else {
    echo "Datenbankvoraussetzung nicht erfüllt, Abbruch<br>";
    fputs($log,"Abbruch\n");
    exit(1);
}

if ( $check ) {
    echo '
      <form action="#">
        <p>
          <input type="button" name="Next" value="Next" onclick="window.location.href = \''.$_SERVER["HTTP_REFERER"].'\'">
        </p>
      </form>';
    exit(0);
}
echo "Datenbank einrichten<br>";
$f = fopen("update/installcrmi.sql","r");
if ( !$f ) {
    echo "Kann Datei installcrmi.sql nicht &ouml;ffnen.";
    fputs($log,"Kann Datei installcrmi.sql nicht oeffnen.\n");
    exit();
}
$zeile = trim(fgets($f,1000));
$query = "";
$OK    = 0;
$fehl  = 0;
$pos   = 0;

while ( !feof($f) ) {
    if ( empty($zeile) ) { $zeile = trim(fgets($f,1000)); continue; };
    if ( preg_match("/^--/",$zeile) ) { $zeile = trim(fgets($f,1000)); continue; };
    if ( !preg_match("/;$/",$zeile) ) {
        $query .= $zeile;
    } else {
        $query .= $zeile;
        $rc = $_SESSION['db']->query(substr($query,0,-1));
        if ( $rc ) { $OK++; echo ".";}
        else {
            $fehl++;
            echo "!";
            fputs($log,$query."\n");
        };
        $pos++;
        if ( $pos % 10 == 0 ) echo " ";
        ob_flush();
        flush();
        $query = "";
    };
    $zeile = trim(fgets($f,1000));
};

if ( $fehl>0 ) {
    echo "<br>Es sind $fehl Fehler aufgetreten. Das mu&szlig; nicht zu Problemen f&uuml;hren. <br>";
    echo "Kontrollieren Sie dazu bitte das <a href='$logfile'>Logfile</a><br>";
    fputs($log,"Es sind $fehl Fehler aufgetreten\n");
} else {
    echo "<br>Alle Datenbankupdates erfolgreich durchgef&uuml;hrt.<br>";
    fputs($log,"Alle Datenbankupdates erfolgreich\n");
}

fclose($f);
fclose($log);
echo '
  <form action="#">
    <p>
      <input type="button" name="Next" value="Next" onclick="window.location.href = \''.$_SERVER["HTTP_REFERER"].'\'">
    </p>
  </form>';
ob_end_flush();
?>
