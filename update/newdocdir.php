<?

function suchPerson($nummer) {
global $db;
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
global $db;
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
	if ($ok) $ok=rename($root."/".$old,$root."/".$new);
	return $ok;
}

//Verzeichnisse umbenennen
$root="dokumente/".$_SESSION["mansel"];
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
				echo (newname($root,$filename,$tab))?'ok':'fehler';
				echo "<br>\n";
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
                echo (newname($root,$filename,$tab))?'ok':'fehler';
                echo "<br>\n";
	} else {
        	echo 'Verzeichnis '.$filename." nicht verschoben<br>\n";
        }

}

//Pfadnamen berichtigen
echo "Pfadnamen erg&auml;nzen ";
$log=fopen("/tmp/test.x","w");
fputs($log,"Pfadnamen in db ändern\n");
$sql="select oid,kunde from documents";
$data=$db->getAll($sql);
if ($data) foreach ($data as $file) {
	$pfad=suchtabelle($file["kunde"]);
	$sql="update documents set pfad='/$pfad' where oid=".$file["oid"];
	$ok=$db->query($sql);
	if (!$ok) fputs($log,$file["oid"]." -> $pfad Fehler\n");
}
echo "done<br>";
?>
