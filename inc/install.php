<?
if (ob_get_level() == 0) ob_start();
echo "<br>Installation der Datenbankinstanz: ".$_SESSION["dbname"]."<br>";
ob_flush();
flush();
if (!is_writable("tmp/install.log")) {
	$log=fopen("tmp/install.log","a");
} else {
	$log=fopen("/tmp/install.log","a");
	echo "Logfile in /tmp<br> Schreibrechte im CRM-Verzeichnis pr&uuml;fen<br>";
}
fputs($log,date("d.m.Y H:i:s")."\n");
fputs($log,$VERSION."\n");
require("inc/conf.php");
fputs($log,"../$ERPNAME/users/".$_GET["login"].".conf : ");
fputs($log,((is_file("../$ERPNAME/users/".$_GET["login"].".conf"))?"gefunden":"fehler")."\n");
$sql="select * from defaults";
$rs=$db->getAll($sql);
if ($rs[0]["version"]) {
	fputs($log,$rs[0]["version"]." als Basis\n");
	echo "ok. ERP gefunden<br>";
} else {
	fputs($log,"Keine gueltige ERP gefunden\n");
	echo "Keine g&uuml;ltige ERP gefunden<br>";
	exit;
}
echo "Installation der Version $VERSION<br>";
echo "Vorraussetzungen pr&uuml;fen:<br>";
        $path=ini_get("include_path");
        fputs($log,"Suchpfad: $path\n");
        $pfade=split(":",$path);
        $chk=array("DB","DB/pgsql","fpdf","fpdi","Mail","Mail/mime","Image/Canvas","Image/Graph","jpgraph");
        $ok=true;
        foreach($chk as $file) {
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
                        echo "ok<br>";
                        fputs($log,"ok\n");
                } else {
                        $ok=false;
                        echo "false<br>";
                        fputs($log,"false\n");
                }
        }
        if (!$ok) {
                echo "Einige Vorraussetzungen sind nicht erf&uuml;llt.<br>&Uuml;berpr&uuml;fen Sie die die Variable 'include_path' in der 'php.ini'.<br>";
                echo "Andernfalls installieren Sie die noch fehlenden Programme<br>";
        }
        fputs($log,"\n");


$f=fopen("update/installcrmi.sql","r");
if (!$f) { 
	echo "Kann Datei installcrmi.sql nicht &ouml;ffnen.";
	fputs($log,"Kann Datei installcrmi.sql nicht oeffnen.\n");
	exit();
}
$zeile=trim(fgets($f,1000));
$query="";
$ok=0;
$fehl=0;

while (!feof($f)) {
	if (empty($zeile)) { $zeile=trim(fgets($f,1000)); continue; };
	if (preg_match("/^--/",$zeile)) { $zeile=trim(fgets($f,1000)); continue; };
	if (!preg_match("/;$/",$zeile)) { 
		$query.=$zeile;
	} else {
		$query.=$zeile;
		$rc=$db->query(substr($query,0,-1));
		if ($rc) { $ok++; echo ".";}
		else { 
			$fehl++; 
			echo "!"; 
			fputs($log,print_r($rc,true)."\n");
		};
		ob_flush();
		flush();
		$query="";
	};
	$zeile=trim(fgets($f,1000));
};

if ($fehl>0) { 
	echo "Es sind $fehl Fehler aufgetreten<br>";  
	fputs($log,"Es sind $fehl Fehler aufgetreten\n");
} else { 
	echo "Alle Datenbankupdates erfolgreich durchgef&uuml;hrt.<br>"; 
	fputs($log,"Alle Datenbankupdates erfolgreich\n");
}

fclose($f);
if (!file_exists("dokumente/".$_SESSION["dbname"]))  {
	$rc=mkdir ("dokumente/".$_SESSION["dbname"], 0700);
	if ($rc) { echo "Verzeichnis: dokumente/".$_SESSION["dbname"]." erfolgreich erstellt.<br>"; }
	else { echo "Konte Verzeichnis: dokumente/".$_SESSION["dbname"]." nicht erstellen.<br>"; }
} else {
	echo "Verzeichnis 'dokumente/".$_SESSION["dbname"]."' existiert.<br>";
}
fclose($log);
ob_end_flush();
?>
