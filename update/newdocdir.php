<?php
if ($_GET["chk"]==1) include("../inc/stdLib.php");
function suchPerson($nummer) {
	$db=$_SESSION["db"];
	$sql="select * from contacts where cp_id=$nummer";
	$rs=$db->getAll($sql);
	if (!empty($rs) && $rs[0]["cp_id"]==$nummer) { // Person gefunden
		if (!$rs[0]["cp_cv_id"]) return false; // Einzelperson
		$sql="select * from customer where id=".$rs[0]["cp_cv_id"];
		$rs2=$db->getAll($sql);
		if (count($rs2)<1) { // Kein Customer, suche bei Vendor
			$sql="select * from vendor where id=".$rs[0]["cp_cv_id"];
			$rs2=$db->getAll($sql);
			if (!empty($rs2) && $rs2[0]["id"]==$rs[0]["cp_cv_id"]) {
				$Q="V".$rs2[0]["vendornumber"];
			} else {
				return false;
			}
		} else {
			$Q="C".$rs2[0]["customernumber"];
		}
		return $Q."/".$nummer;
	} else {
		return false;
	}
}

function suchtabelle($nummer)   {
	$db=$_SESSION["db"];
	$sql="select * from customer where id=$nummer";
	$rs=$db->getAll($sql);
	if (empty($rs) || !$rs) { // Kein Kunde
		$sql="select * from vendor where id=$nummer";
		$rs=$db->getAll($sql);
		if (!empty($rs) && $rs[0]["id"]==$nummer) { // Lieferant gefunden
			return "V".$rs[0]["vendornumber"];
		} else { 
			return false;	// Nichts gefunden
		}
	} else { // Kunde gefunden
		return "C".$rs[0]["customernumber"];
	}
}

function newname($root,$old,$new) {
	$ok=chkdir($new);
	if ($ok)    {
         $ok=rename($root."/".$old,$root."/".$new);
         if ($ok) {
            $sql="update documents set path='$new' where kunde = $old";
            $ok = $db->query($sql);
            if (!$ok) {
                rename($root."/".$new,$root."/".$old);
            }
        }
    }
	return $ok;
}
//Verzeichnisse umbenennen
$root="dokumente/".$_SESSION["dbname"];
if (!$updatefile) $updatefile="chk";
$docfile=$updatefile."_doc";
if ($doclog=@fopen("tmp/".$docfile.".log","a") ) {
	if ($log) fputs($log,"DocLog in tmp/$docfile.log\n");
} else if ($doclog=@fopen("/tmp/".$docfile.".log","a") ) {
	if ($log) fputs($log,"DocLog in /tmp/$docfile.log\n");
	echo "Logfile (Doc) in /tmp<br> Schreibrechte im CRM-Verzeichnis pr&uuml;fen<br>";
} else {
	echo "Kann kein Logfile (Doc) erstellen<br>";
	fputs($log,"Kein DocLog.\n");
	$doclog=false;
}
if ($_GET["chk"]==1) chdir (".."); 
chdir ("$root");
$tmp=glob("*");
chdir("../..");
$personen=array();
if ($tmp)  foreach ($tmp as $filename) {
	if ($filename == "..") continue;
	if ($filename == ".") continue;
	if (is_dir($root."/".$filename)) {
		preg_match("/^[0-9]+$/",$filename+"",$treffer);
		if ($treffer[0]==$filename) {
			$tab=suchtabelle($filename);
			if ($tab) {
				echo 'Verzeichnis '.$filename.' nach '.$tab.' verschieben: ';
				$ok=newname($root,$filename,$tab)?'ok':'fehler';
				if ($doclog) fputs($doclog,$filename.' -> '.$tab.' '.$ok."\n");
				echo "$ok<br>\n";
			} else {
				$personen[]=$filename;
			}
		}
	}
}
if ($personen) foreach ($personen as $filename) {
	$tab=suchPerson($filename);
	if ($tab) {
		echo 'Verzeichnis '.$filename.' nach '.$tab.' verschieben: ';
		$ok=newname($root,$filename,$tab)?'ok':'fehler';
		if ($doclog) fputs($doclog,$filename.' -> '.$tab.' '.$ok."\n");
		echo "$ok<br>\n";
	} else {
        $sql="update documents set path='$filename' where kunde = $filename";
        $ok = $db->query($sql);
       	echo 'Verzeichnis '.$filename." nicht verschoben<br>\n";
		if ($doclog) fputs($doclog,$filename.' nicht verschoben '."\n");
        }

}

echo "done<br>";
?>
