<?
if ($_GET['check']==1) {
	$p='../';
	$check=true;
} else {	
   	$p='';
   	$check=false;
}
$p=($_GET['check']==1)?'../':'';
include($p.'inc/conf.php');
if (ob_get_level() == 0) ob_start();
echo "<br>Installation der Version $VERSION";
echo " der Datenbankinstanz: ".$_SESSION["dbname"]."<br>";
ob_flush();
flush();
if ($log=@fopen($p."tmp/install.log","a")) {
	$logfile="tmp/install.log";
	echo "Logfile in tmp/install.log<br>";
} else {
	if ($log=@fopen("/tmp/install.log","a")) {
		$logfile="/tmp/install.log";
		echo "Logfile in /tmp/install.log<br>";
	} else {
		$logfile="";
		echo "Keine Schreibrechte f&uuml;r Logfile in tmp und /tmp.<br>";
		echo "Installation abgebrochen";
		exit (1);
	}
}
echo "Schreibrechte im CRM-Verzeichnis pr&uuml;fen<br>";
if (!file_exists($p."dokumente/".$_SESSION["dbname"]))  {
	$rc=mkdir ($p."dokumente/".$_SESSION["dbname"], 0700);
	if ($rc) { echo "Verzeichnis: dokumente/".$_SESSION["dbname"]." erfolgreich erstellt.<br>"; }
	else { echo "Konte Verzeichnis: dokumente/".$_SESSION["dbname"]." nicht erstellen.<br>"; }
} else {
	echo "Verzeichnis 'dokumente/".$_SESSION["dbname"]."' existiert.<br>";
}
$ok="<b>ok</b>";
$fehler="<font color='red'>fehler</font>";
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


echo "Vorraussetzungen pr&uuml;fen:<br>";
        $path=ini_get("include_path");
        fputs($log,"Suchpfad: $path\n");
        $pfade=split(":",$path);
        $chkfile=array("DB","DB/pgsql","fpdf","fpdi","Mail","Mail/mime","Image/Canvas","Image/Graph",
			"jpgraph","Contact_Vcard_Build","Contact_Vcard_Parse","xajax/xajax.inc");
        $chkstat=array(1,1,0,0,0,0,0,0,0,0,0,1);
        $OK=true;
	$pos=0;
	$dbok=true;
        foreach($chkfile as $file) {
                echo "$file: ";
                $ook=false;
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
                } else {
			//if ($file=="DB" or $file=="DB/pgsql") $dbok=false;
                        $OK=false;
			if ($chkstat[$pos]==1) {
				$dbok=false;
				echo "<font color='red'><b>unbedingt Erforderlich!!</b></font><br>";
                        	fputs($log,"Fehler: Erforderlich\n");
			} else {
				echo "<font color='red'>dieses Paket fehlt oder kann nicht geladen werden.</font><br>";
                        	fputs($log,"Fehler\n");
			}
                }
		$pos++;
        }
        if (!$OK) {
                echo "Einige Vorraussetzungen sind nicht erf&uuml;llt.<br>&Uuml;berpr&uuml;fen Sie die die Variable 'include_path' in der 'php.ini'.<br>";
                echo "Andernfalls installieren Sie die noch fehlenden Pakete<br>";
		echo "Aktueller include_path: ".ini_get('include_path').'<br>';
        }
        fputs($log,"\n");

//ERP da?
	$OK=is_file($p."../$ERPNAME/config/authentication.pl");
	fputs($log,"$ERPNAME : ");
	fputs($log,(($OK)?"gefunden":"fehler")."\n");
	if ($OK) {
		echo "ERP authtenticaltion.pl gefunden<br>";
	} else {
		echo "ERP (authentication.pl) nicht gefunden. Abbruch.<br>";
		exit(1);
	}

if ($dbok) {
	if ($_GET['check']==2 || $_GET['check']=='') {
		//$sql="select * from defaults";
		$sql="SELECT * from schema_info  where tag like 'release_%' order by tag desc limit 1";
		$rs=$db->getAll($sql);
		if (substr($rs[0]["tag"],0,11)>="release_2_6") {  //Muß noch an die akt. Version angepasst werden !!!!
			fputs($log,$rs[0]["version"]." als Basis\n");
			echo "$ok. ERP-DB gefunden<br>";
		} else {
			fputs($log,"Keine gueltige ERP-DB gefunden\n");
			echo "$fehler Keine g&uuml;ltige ERP-DB gefunden (".$rs[0]["version"].")<br>";
			exit;
		}
	}
} else {
	echo "Datenbankvorraussetzung nicht erfüllt, Abbruch<br>";
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
