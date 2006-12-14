<?
if (!is_writable("tmp/upd120130.log")) {
	$log=fopen("tmp/upd120130.log","a");
} else {
	$log=fopen("/tmp/upd120130.log","a");
	echo "Logfile in /tmp<br> Schreibrechte im CRM-Verzeichnis pr&uuml;fen<br>";
}
fputs($log,date("d.m.Y H:i:s")."\n");
fputs($log,"120->130\n");
echo "Update auf Version $VERSION<br>";
echo "Vorraussetzungen pr&uuml;fen:<br>";
	$path=ini_get("include_path");
	fputs($log,"Suchpfad: $path\n");
	$pfade=split(":",$path);
	$chk=array("DB","fpdf","fpdi","Mail","Mail/mime","Image/Canvas","Image/Graph","jpgraph");
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
echo "Konfigurationsdatei erweitern ";
	if (is_writable("inc/conf.php")) {
		require("inc/conf.php");
		copy("inc/conf.php","tmp/conf.php120");
		$configfile=file("inc/conf.php");
		$f=fopen("inc/conf.php","w");
		foreach($configfile as $row) {
			$tmp=trim($row);
			if (ereg("\?>",$tmp)) {
				fputs($f,'$showErr=false;'."\n");
				fputs($f,'$logmail=true;'."\n");
				fputs($f,'$tinymce=false;'."\n");
				fputs($f,'$jcalendar=true;'."\n");
				fputs($f,'$listLimit=200;'."\n");
				fputs($f,'$jpg=false;'."\n");
				fputs($f,$tmp."\n");
			} else {
				fputs($f,$tmp."\n");
			}
		}
		fclose($f);
		echo "<b>ok</b><br>";
		fputs($log,"conf.php geändert\n");
	} else {
		echo "<br>inc/conf.php ist nicht beschreibbar. Abbruch!<br>";
		fputs($log,"conf.php nicht beschreibbar\n");
		fclose($log);
		exit();
	};

echo "Datenbankinstanz ".$_SESSION["dbname"]." erweitern : ";
	$sql="select last_value from id";
	$rs=$db->getAll($sql);
	if ($rs) $lastID=$rs[0]["last_value"]+1;
	fputs($log,"Sequence ID: $lastID\n";
	$sql="CREATE SEQUENCE 'crmid' start $lastID increment 1 maxvalue 9223372036854775807 minvalue 1 cache 1";
	$rc=$db->query($sql);
	if ($rc) { echo "Sequence crmid erzeugt<br>"; fputs($log,"Sequence crmid erzeugt\n";}
	else { echo "Fehler. Kann Sequence crmid nicht erzeugen."; fputs($log,"Sequence crmid mit $lastID nicht erzeugt\n"; exit(); };
	$updatefile="update/update".$rc[0]["version"]."-$VERSION";
	$updatefile=ereg_replace("\.","",$updatefile).".sql";
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
	fclose($f);

echo "Menue erweitern<br>";
	if (is_writable("../$ERPNAME/menu.ini")) {
		$menufile=file("../$ERPNAME/menu.ini");
		copy("../$ERPNAME/menu.ini","tmp/menu.ini120");
		$f=fopen("../$ERPNAME/menu.ini","w");
		foreach($menufile as $row) {
			$tmp=trim($row);
			if (ereg("crm/personen3.php",$tmp)) {
				fputs($f,$tmp."\n");
				fputs($f,"\n");
				fputs($f,"[CRM--Auftragschance]\n");
				fputs($f,"module=crm/opportunity.php\n");
				fputs($f,"\n");
				fputs($f,"[CRM--Wissens-DB]\n");
				fputs($f,"module=crm/wissen.php\n");
				fputs($f,"\n");
				fputs($f,"[CRM--Notizen]\n");
				fputs($f,"module=crm/postit.php\n");
				fputs($f,"\n");
				fputs($f,"[CRM--Status]\n");
				fputs($f,"module=crm/status.php\n");  
			} else if (ereg("crm/menu1.php",$tmp)) {
				fputs($f,"module=crm/hilfe.php\n");
			} else {
				fputs($f,$tmp."\n");
			}
		}
		fputs($log,"Menue erweitert\n");
		fclose($f);
		echo "<b>ok</b><br>";
	} else {
		fputs($log,"../$ERPNAME/menu.ini ist nicht beschreibbar\n");
		echo "<br>../$ERPNAME/menu.ini ist nicht beschreibbar. Abbruch!<br>";
		echo "Bitte manuell eintragen:<br>";
		echo "[CRM--Auftragschance]<br>";
		echo "module=crm/opportunity.php<br>";
		echo "<br>";
		echo "[CRM--Wissens-DB]<br>";
		echo "module=crm/wissen.php<br>";
		echo "<br>";
		echo "[CRM--Notizen]<br>";
		echo "module=crm/postit.php<br>";
		echo "<br>";
		echo "[CRM--Status]<br>";
		echo "module=crm/status.php<br>";
		echo "<br>";
		echo "&Auml;ndern:<br>";
		echo "module=crm/menu1.php in module=crm/hilfe.php<br>";
		echo "Die Men&uuml;struktur hat sich bei Neuinstallationen ge&auml;ndert. Wenn Sie die neue Struktur auch verwenden wollen, m&uuml;ssen Sie die 'menu.ini' manuell anpassen. Entfernen Sie daraus allen CRM-Eintr&auml;ge und f&uuml;gen Sie die 'menu130.ini'  ein.";
		
	};
fclose($log);
?>
