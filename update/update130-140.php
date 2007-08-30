<?
if (!is_writable("tmp/upd130140.log")) {
        $log=fopen("tmp/upd130140.log","a");
} else {
        $log=fopen("/tmp/upd130140.log","a");
        echo "Logfile in /tmp<br> Schreibrechte im CRM-Verzeichnis pr&uuml;fen<br>";
}
fputs($log,date("d.m.Y H:i:s")."\n");
fputs($log,"130->140\n");
echo "Update auf Version $VERSION<br>";
echo "Vorraussetzungen pr&uuml;fen:<br>";
        $path=ini_get("include_path");
        fputs($log,"Suchpfad: $path\n");
        $pfade=split(":",$path);
        $chk=array ("xajax/xajax.inc");
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
?>
Die Verzeichnisstruktur der Dokumente (Kunden/Lieferanten/Kontakte) hat sich ge&auml;ndert.<br>
Das Script versucht die Verzeichnisse entsprechent umzubenennen und zu verschieben.<br>
Bitte erstellen Sie zun&auml;chst ein Backup des Dokumentenverzeichnis (crm/dokumente/).
<form name="update" method="post" action="">
	<input type="submit" name="ok" value="ok">
	<input type="submit" name="nok" value="nee - lieber nicht">
</form>
<?
if ($_POST["ok"]) {
	echo "Datenbankinstanz ".$_SESSION["dbname"]." erweitern : ";
	$updatefile="update/update130-140.sql";
	if (ob_get_level() == 0) ob_start();
	ob_flush();
	flush();
	$f=fopen($updatefile,"r");
	if (!$f) { 
		fputs($log,"$updatefile nicht gefunden\n");
		echo "<br>Kann Datei $updatefile nicht &ouml;ffnen.<br>Abbruch!";
		fclose($log);
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
			if (eregi("INSERT.*INTO.*crm",$query)) {
                                $query_=substr($query,0,strpos($query,"(0,"));
                                $query_.="(".$_SESSION["loginCRM"].",";
                                $query_.=substr($query,strpos($query,"(0,")+3);
				$query=$query_;
                        }
			$rc=$db->query(substr($query,0,-1));
			if ($rc) { $ok++; echo "."; fputs($log,"."); }
			else { $fehl++; echo "!"; fputs($log,"!");};
			ob_flush();
			flush();
			$query="";
		};
		$zeile=trim(fgets($f,1000));
	};
	fputs($log,"\n");
	if ($fehl>0) { 
		echo "Es sind $fehl Fehler aufgetreten<br>";  
		fputs($log,"Es sind $fehl Fehler aufgetreten\n");
	}
	else { 
		fputs($log,"Datenbankupdate erfolgreich\n");
		echo "Datenbankupdate erfolgreich durchgef&uuml;hrt.<br>"; 
	}
	require ("newdocdir.php");
	fclose($f);

} else if ($_POST["nok"]) {
	echo "Abbruch";
	exit;
}
?>
