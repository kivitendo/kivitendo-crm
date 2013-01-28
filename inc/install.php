<?php
if ($_GET['check']==1) {
	$p='../';
	$check=true;
    $inclpa=ini_get('include_path');
    ini_set('include_path',$inclpa.":../:./crmajax:./inc:../inc");
} else {	
   	$p='';
   	$check=false;
}
$p=($_GET['check']==1)?'../':'';
include_once("inc/stdLib.php");
include($p.'inc/conf.php');
include($p.'inc/version.php');
if (ob_get_level() == 0) ob_start();
echo "<br>Installation der Version $VERSION";
echo " der Datenbankinstanz: ".$_SESSION["dbname"]."<br>";
ob_flush();
flush();
if ($log=@fopen($p.'log/install.log',"a")) {
	$logfile=$p."log/install.log";
	echo 'Logfile in '.$logfile.'<br>';
} else {
	$logfile="";
	echo "Keine Schreibrechte f&uuml;r Logfile in log.<br>";
	echo "Installation abgebrochen";
	exit (1);
}
echo "Schreibrechte im CRM-Verzeichnis pr&uuml;fen<br>";
if (!file_exists($p."dokumente/".$_SESSION["dbname"]))  {
	$rc=mkdir ($p."dokumente/".$_SESSION["dbname"], $_GLOBALS['dir_mode']);
	if ($rc) { 
        if ( $GLOBALS["dir_group"] ) chgrp($p."dokumente/".$_SESSION["dbname"], $_GLOBALS['dir_group']);
        echo "Verzeichnis: dokumente/".$_SESSION["dbname"]." erfolgreich erstellt.<br>"; 
    } else { echo "Konte Verzeichnis: dokumente/".$_SESSION["dbname"]." nicht erstellen.<br>"; }
} else {
	echo "Verzeichnis 'dokumente/".$_SESSION["dbname"]."' existiert.<br>";
}
$ok="<b>ok</b>";
$fehler="<font color='red'>Fehler!</font>";
if (is_writable($p."dokumente")) { 
	echo "dokumente/ : $ok<br>"; 
	fputs($log,"dokumente/ : ok\n");
} else { 
	echo "dokumente/ : $fehler kein Schreibrecht<br>"; 
	fputs($log,"dokumente/ : fehler\n");
}
if (is_writable($p."dokumente/".$_SESSION["dbname"])) { 
	echo "dokumente/".$_SESSION["dbname"]." : $ok<br>"; 
	fputs($log,"dokumente/".$_SESSION["dbname"]." : ok\n");
} else { 
	echo "dokumente/".$_SESSION["dbname"]." : $fehler kein Schreibrecht<br>"; 
	fputs($log,"dokumente/".$_SESSION["dbname"]." : fehler\n");
}
if (is_writable($p."vorlage")) { 
	echo "vorlage/ : $ok<br>"; 
	fputs($log,"vorlage/ : ok\n");
} else { 
	echo "vorlage/ : $fehler kein Schreibrecht<br>"; 
	fputs($log,"vorlage/ : fehler\n");
}
if (is_writable($p."inc/conf.php")) { 
	echo "inc/conf.php : $ok<br>"; 
	fputs($log,"inc/conf.php : ok\n");
} else { 
	echo "inc/conf.php : $fehler kein Schreibrecht<br>"; 
	fputs($log,"inc/conf.php : fehler\n");
}

fputs($log,date("d.m.Y H:i:s")."\n");
fputs($log,$VERSION."\n");

echo "<br>Voraussetzungen pr&uuml;fen:<br>";
        $path=ini_get("include_path");
        fputs($log,"Suchpfad: $path\n");
        $pfade=explode(":",$path);
        if( function_exists('curl_init')){
            echo "Curl: $ok<br>";
            fputs($log,"Curl : ok\n");
        } else {
		      echo "Curl: $fehler<br>";
		      fputs($log,"Curl : Fehler\n");
		  }	
        $chkfile=array("DB"=>array("DB","MDB2"),"Driver"=>array("DB/pgsql","MDB2/Driver/pgsql"),
                        "fpdf","fpdi","Mail","Mail/mime",
                        "Image/Canvas","Image/Graph","jpgraph",
		                "Contact_Vcard_Build","Contact_Vcard_Parse",
                        "Xajax"=>array("xajax/xajax.inc","xajax_core/xajax.inc"),
                        "jQuery-UI"=>array("jquery-ui/jquery","jquery-ui/ui/jquery-ui"));
        $chkstat=array(1,1,0,0,0,0,0,0,0,0,0,1,1);
        $OK=true;
	$pos=0;
	$dbok=true;
        foreach($chkfile as $key=>$file) {
                $ook=false;
                if (is_array($file)) {
                    foreach ($file as $altfile) {
                        echo "$altfile: ";
                        fputs($log,"$altfile: ");
                        $aok=false;
                        foreach($pfade as $path) {
                            $path = ((substr($path,0,1)=="/")?"":$p).$path;
                            if (is_readable($path."/".$altfile.".php") || is_readable($path."/".$altfile.".js")) {
                                $aok=true;
                                $ook=true;
                                break;
                            }
                        }
                        if ($aok) {
                            echo "ok<br>";
                            fputs($log,"ok\n");
                        } else {
                            echo "fehlt<br>";
                            fputs($log,"fehlt\n");
                        }
                    }
                    echo "$key: ";
                    fputs($log,"$key ");
                    if ($ook) {
                        echo "$ok<br>";
                        fputs($log,"ok\n");
                    }
                } else {
                    echo "$file: ";
                    fputs($log,"$file: ");
                    foreach($pfade as $path) {
                        if (is_readable($path."/".$file.".php")) {
                                $ook=true;
                                break;
                        }
                    }
                    if ($ook) {
                        echo "$ok<br>";
                        fputs($log,"ok\n");
                    };
                }
                if (!$ook) {
                    $OK=false;
                    if ($chkstat[$pos]==0) {
                        echo "<font color='red'>dieses Paket fehlt oder kann nicht geladen werden.</font><br>";
                                    fputs($log,"Fehler\n");
                    } else {
                        $dbok=false;
                        echo "<font color='red'><b>unbedingt erforderlich!!</b></font><br>";
                                    fputs($log,"Fehler: erforderlich\n");
                    }
                }
		$pos++;
        }
        if (!$OK) {
                echo "Einige Voraussetzungen sind nicht erf&uuml;llt.<br>&Uuml;berpr&uuml;fen Sie die die Variable 'include_path' in der 'php.ini'.<br>";
                echo "Andernfalls installieren Sie die noch fehlenden Pakete.<br>";
		echo "Aktueller include_path: ".ini_get('include_path').'<br>';
        }
        fputs($log,"\n");

//ERP da?
	$OK=is_file($p."../$ERPNAME/config/$erpConfigFile.conf");
	fputs($log,"$ERPNAME : ");
	fputs($log,(($OK)?"gefunden":"fehler")."\n");
	if ($OK) {
		echo "ERP $erpConfigFile.conf gefunden<br>";
	} else {
		echo "ERP ($erpConfigFile.conf) nicht gefunden. Abbruch.<br>";
		exit(1);
	}

if ($dbok) {
	if ($_GET['check']==2 || $_GET['check']=='') {
		//$sql="select * from defaults";
		$sql="SELECT * from schema_info  where tag like 'release_%' order by tag desc limit 1";
		$rs=$db->getAll($sql);
		if (substr($rs[0]["tag"],0,11)>="release_2_6") {  
			fputs($log,$rs[0]["version"]." als Basis\n");
			echo "$ok. ERP-DB gefunden<br>";
		} else {
			fputs($log,"Keine gueltige ERP-DB gefunden\n");
			echo "$fehler Keine g&uuml;ltige ERP-DB gefunden (".$rs[0]["version"].")<br>";
			exit;
		}
	}
} else {
	echo "Datenbankvoraussetzung nicht erfüllt, Abbruch<br>";
	fputs($log,"Abbruch\n");
	exit(1);
}
if ($check) exit(0);
echo "Datenbank einrichten<br>";
$f=fopen("update/installcrmi.sql","r");
if (!$f) { 
	echo "Kann Datei installcrmi.sql nicht &ouml;ffnen.";
	fputs($log,"Kann Datei installcrmi.sql nicht oeffnen.\n");
	exit();
}
$zeile=trim(fgets($f,1000));
$query="";
$OK=0;
$fehl=0;
$pos=0;

while (!feof($f)) {
	if (empty($zeile)) { $zeile=trim(fgets($f,1000)); continue; };
	if (preg_match("/^--/",$zeile)) { $zeile=trim(fgets($f,1000)); continue; };
	if (!preg_match("/;$/",$zeile)) { 
		$query.=$zeile;
	} else {
		$query.=$zeile;
		$rc=$db->query(substr($query,0,-1));
		if ($rc) { $OK++; echo ".";}
		else { 
			$fehl++; 
			echo "!"; 
			fputs($log,$query."\n");
		};
		$pos++;
		if ($pos % 10 == 0) echo " ";
		ob_flush();
		flush();
		$query="";
	};
	$zeile=trim(fgets($f,1000));
};

if ($fehl>0) { 
	echo "<br>Es sind $fehl Fehler aufgetreten. Das mu&szlig; nicht zu Problemen f&uuml;hren. <br>";  
	echo "Kontrollieren Sie dazu bitte das <a href='$logfile'>Logfile</a><br>";
	fputs($log,"Es sind $fehl Fehler aufgetreten\n");
} else { 
	echo "<br>Alle Datenbankupdates erfolgreich durchgef&uuml;hrt.<br>"; 
	fputs($log,"Alle Datenbankupdates erfolgreich\n");
}

fclose($f);
fclose($log);
ob_end_flush();
?>
