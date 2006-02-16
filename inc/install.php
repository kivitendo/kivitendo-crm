<?
if (ob_get_level() == 0) ob_start();
echo "<br>Installation der Datenbankinstanz: ".$_SESSION["dbname"]."<br>";
ob_flush();
flush();
$f=fopen("update/install120.sql","r");
if (!$f) { 
	echo "Kann Datei install120.sql nicht &ouml;ffnen.";
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
		else { $fehl++; echo "!"; };
		ob_flush();
		flush();
		$query="";
	};
	$zeile=trim(fgets($f,1000));
};

if ($fehl>0) { echo "Es sind $fehl Fehler aufgetreten<br>";  }
else { echo "Alle Datenbankupdates erfolgreich durchgef&uuml;hrt.<br>"; }

fclose($f);
if (!file_exists("dokumente/".$_SESSION["dbname"]))  {
	$rc=mkdir ("dokumente/".$_SESSION["dbname"], 0700);
	if ($rc) { echo "Verzeichnis: dokumente/".$_SESSION["dbname"]." erfolgreich erstellt.<br>"; }
	else { echo "Konte Verzeichnis: dokumente/".$_SESSION["dbname"]." nicht erstellen.<br>"; }
} else {
	echo "Verzeichnis 'dokumente/".$_SESSION["dbname"]."' existiert bereits.<br>";
}
ob_end_flush();
?>
