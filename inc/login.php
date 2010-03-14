<?php
while( list($key,$val) = each($_SESSION) ) {
	unset($_SESSION[$key]);
};
clearstatcache();
if ($_POST["erpname"]) {
	if (is_file("../".$_POST["erpname"]."/config/authentication.pl")) {
		if (is_writable("inc/conf.php")) {
			$name=false;
			$configfile=file("inc/conf.php");
			$f=fopen("inc/conf.php","w");
			foreach($configfile as $row) {
				$tmp=trim($row);
				if (ereg("ERPNAME",$tmp)) {
					fputs($f,'$ERPNAME="'.$_POST["erpname"]."\";\n");
					$name=true;
				} else {
					if (ereg("\?>",$tmp) && !$name) fputs($f,'$ERPNAME="'.$_POST["erpname"].'";'."\n");
					fputs($f,$tmp."\n");
				}
			}
			fclose($f);
		} else {
			echo "inc/conf.php ist nicht beschreibbar";
		}
	}
	$ERPNAME=$_POST["erpname"];
}

if (substr(getcwd(),-3)=="inc") {
    $conffile="../../$ERPNAME/config/authentication.pl";
} else {
    $conffile="../$ERPNAME/config/authentication.pl";
}
/*if (!$login) {
	header("location: ups.html");
} else */
if (is_file($conffile)) {
	$tmp=anmelden();
	if ($tmp) {
		if (chkVer()) {
			$db=$_SESSION["db"];
			$_SESSION["loginok"]="ok";
		} else {
			echo "db-Version nicht ok";
			exit;
		}
	} else {
		echo $_SESSION["db"]." nicht erreichbar.";
		exit;
	}
} else {
	echo "Configfile nicht gefunden<br>$PHPSELF<br>";
	echo "Lx-Office ERP V 2.6.0 oder gr&ouml;&szlig;er erwartet!!!<br><br>";
	echo "<form name='erppfad' method='post' action='".$PHPSELF."'>";
	echo "Bitte den Verzeichnisnamen (nicht den Pfad) der ERP eingeben:<br>";
	echo "<input type='text' name='erpname'>";
	echo "<input type='submit' name='saveerp' value='sichern'>";
	echo "</form>";
	exit;
}
?>
