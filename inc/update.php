<?php
	echo "Update auf Version $VERSION<br>";
	$updatefile="update/update".$rc[0]["version"]."-$VERSION";
	$updatefile=str_replace(".","",$updatefile);
	if (is_file($updatefile.".php")) {
		require($updatefile.".php");
	} else if (is_file($updatefile.".sql")) {
		if (ob_get_level() == 0) ob_start();
		echo "<br>Update der Datenbankinstanz: ".$_SESSION["dbname"]."<br>";
		ob_flush();
		flush();
		$f=fopen("update/".$updatefile.".sql","r");
		if (!$f) { 
			echo "Kann Datei ".$updatefile.".sql nicht &ouml;ffnen.";
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
				$rc=$_SESSION['db']->query(substr($query,0,-1));
				if ($rc) { $ok++; echo ".";}
				else { $fehl++; echo "!"; };
				ob_flush();
				flush();
				$query="";
			};
			$zeile=trim(fgets($f,1000));
		};
	} else {
		echo "Updatefile: $updatefile.[php|sql] nicht vorhanden.";
	}
?>
