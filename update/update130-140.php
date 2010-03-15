<?php

$log=false;

if ($_POST) {
	session_start();
	chdir("..");
	include("inc/conf.php");
	include_once("inc/db.php");
	include("inc/loginok.php");
}
	$updatefile=basename(__FILE__,".php");
       	$log=@fopen('tmp/'.$updatefile.'.log','a');
	if (!$log) {
        	$log=@fopen('/tmp/'.$updatefile.'.log','a');
		if ($log) {
		    echo 'Logfile in /tmp<br> Schreibrechte im CRM-Verzeichnis pr&uuml;fen<br>';
		} else {
			echo 'Kann kein Logfile'.$updatefile.'.log erstellen<br>';
		}
        }

log2file($log,date("d.m.Y H:i:s"));
log2file($log,$updatefile);

if ($_POST["ok"]) {
	log2file($log,"Update ok");
	echo "Datenbankinstanz ".$_SESSION["dbname"]." erweitern : ";
	if (ob_get_level() == 0) ob_start();
	ob_flush();
	flush();
	$f=fopen("update/".$updatefile.".sql","r");
	if (!$f) { 
		log2file($log,"$updatefile.sql nicht gefunden");
		echo "<br>Kann Datei $updatefile.sql nicht &ouml;ffnen.<br>Abbruch!";
		if ($log) fclose($log);
		exit();
	}
	echo $updatefile.".sql verwendet<br>";
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
			if (eregi("INSERT.*INTO.*crm",$query)) {
                                $query_=substr($query,0,strpos($query,"(0,"));
                                $query_.="(".$_SESSION["loginCRM"].",";
                                $query_.=substr($query,strpos($query,"(0,")+3);
				$query=$query_;
                        }
			$rc=$db->query(substr($query,0,-1));
			if ($rc) { $ok++; echo "."; log2file($log,substr($query,0,6)." ok"); }
			else { $fehl++; echo "!"; log2file($log,substr($query,0,6)." Fehler!");};
			if ($pos % 10 == 0) {
				echo " ";
			}
			ob_flush();
			flush();
			$query="";
		};
		$zeile=trim(fgets($f,1000));
	};
	log2file($log,"");
	if ($fehl>0) { 
		echo "Es sind $fehl Fehler aufgetreten<br>";  
		log2file($log,"Es sind $fehl Fehler aufgetreten");
	}
	else { 
		log2file($log,"Datenbankupdate erfolgreich");
		echo "Datenbankupdate erfolgreich durchgef&uuml;hrt.<br>"; 
	}
	require ("update/newdocdir.php");
	fclose($f);

} else if ($_POST["nok"]) {
	echo "Abbruch";
	exit;
} else {
	echo "Vorraussetzungen pr&uuml;fen:<br>";
        $path=ini_get("include_path");
        log2file($log,"Suchpfad: $path");
        $pfade=split(":",$path);
        $chkfile=array("DB","DB/pgsql","fpdf","fpdi","Mail","Mail/mime","Image/Canvas","Image/Graph",
                        "jpgraph","Contact_Vcard_Build","Contact_Vcard_Parse","xajax/xajax.inc");
        $chkstat=array(1,1,0,0,0,0,0,0,0,0,0,1);
        $ok=true;
        foreach($chkfile as $file) {
                echo "$file: ";
                $ook=false;
                foreach($pfade as $path) {
                        if (is_readable($path."/".$file.".php")) {
                                $ook=true;
                                break;
                        }
                }
                if ($ook) {
                        echo "ok<br>";
			log2file($log,"$file: ok");
                } else {
                        $ok=false;
			if ($chkstat[$pos]==1) {
                                $dbok=false;
                                echo "<font color='red'><b>unbedingt Erforderlich!!</b></font><br>";
				log2file($log,"$file: Fehler: Erforderlich");
                        } else {
                                echo "<font color='red'>dieses Paket fehlt oder kann nicht geladen werden.</font><br>";
				log2file($log,"$file: Fehler");
                        }
         	}
	}
	@fclose($log);
?>
Die Verzeichnisstruktur der Dokumente (Kunden/Lieferanten/Kontakte) hat sich ge&auml;ndert.<br>
Das Script versucht die Verzeichnisse entsprechent umzubenennen und zu verschieben.<br>
Bitte erstellen Sie zun&auml;chst ein Backup des Dokumentenverzeichnis (crm/dokumente/<?php echo  $_SESSION["dbname"] ?>).
<form name="update" method="post" action="update/update131-140.php">
	<input type="hidden" name="oldver" value="<?php echo  $rc[0]["version"] ?>">
	<input type="submit" name="ok" value="ok">
	<input type="submit" name="nok" value="nee - lieber nicht">
</form>
<?php 
} 

function log2file($log,$txt) {
	if ($log) {
		fputs($log,$txt."\n");
	}
}

?>
