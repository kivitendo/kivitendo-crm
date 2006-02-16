<?
// $Id: login.php,v 1.1 2006/01/05 09:57:09 hli Exp $
while( list($key,$val) = each($_SESSION) ) {
	unset($_SESSION[$key]);
};
clearstatcache();
require ("VERSION.php");
if ($_POST["erpname"]) {
	if (is_file("../".$_POST["erpname"]."/users/".$_GET["login"].".conf")) {
		if (is_writable("inc/conf.php")) {
			$name=false;
			$configfile=file("inc/conf.php");
			$f=fopen("inc/conf.php","w");
			foreach($configfile as $row) {
				$tmp=trim($row);
				if (ereg("ERPNAME",$tmp)) {
					fputs($f,'$ERPNAME="'.$_POST["erpname"].'";\n');
					$name=true;
				} else {
					if (ereg("\?>",$tmp) && !$name) fputs($f,'$ERPNAME="'.$_POST["erpname"].'";'."\n");
					fputs($f,$tmp."\n");
					$ERPNAME=$_POST["erpname"];
				}
			}
			fclose($f);
		} else {
			echo "inc/conf.php ist nicht beschreibbar";
		}
	}
}

$usrfile="../$ERPNAME/users/".$_GET["login"].".conf";
if (!$_GET["login"]) {
	header("location: ups.html");
} else if (is_file($usrfile)) {
	$tmp=anmelden(($_GET["login"])?$_GET["login"]:$_POST["login"]);
	if ($tmp) {
		if (chkVer()) {
			$db=$_SESSION["db"];
			$_SESSION["ok"]="ok";
		} else {
			echo "db-Version nicht ok";
			exit;
		}
	} else {
		echo $_SESSION["db"]." nicht erreichbar.";
		exit;
	}
} else {
	echo "$usrfile nicht gefunden<br>$PHPSELF<br>";
	echo "<form name='erppfad' method='post' action='".$PHPSELF."'>";
	echo "Bitte den Verzeichnisnamen (nicht den Pfad) der ERP eingeben:<br>";
	echo "<input type='hidden' name='login' value='".$_GET["login"]."'>";	
	echo "<input type='text' name='erpname'>";
	echo "<input type='submit' name='saveerp' value='sichern'>";
	echo "</form>";
	exit;
}
?>
