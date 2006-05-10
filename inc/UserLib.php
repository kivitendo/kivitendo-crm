<?
// $Id$


/****************************************************
* saveUserStamm
* in: val = array
* out: rc = boolean
* AnwenderDaten sichern
* !! in eine andere Lib verschieben
*****************************************************/
function saveUserStamm($val) {
global $db;
	if (!$val["Interv"]) $val["Interv"]=60;
	if ($val["Vertreter"]==$val["UID"]) {$vertreter="null";} else {$vertreter=$val["Vertreter"];};
	$sql="update employee set name='".$val["Name"]."',etikett=".$val["etikett"].", addr1='".$val["Strasse"]."', addr2='".$val["Plz"]."', addr3='";
	$sql.=$val["Ort"]."', workphone='".$val["Tel2"]."', homephone='".$val["Tel1"]."', notes='".$val["Bemerkung"]."',";
	$sql.="msrv='".$val["Msrv"]."', postf='".$val["Postf"]."', kennw='".$val["Kennw"]."', countrycode='".$val["countrycode"]."', postf2='";
	$sql.=$val["Postf2"]."', interv='".$val["Interv"]."', pre='".$val["Pre"]."', abteilung='".$val["Abteilung"]."',";
	$sql.="position='".$val["Position"]."', vertreter=$vertreter,mailsign='".$val["MailSign"]."',email='".$val["eMail"];
	$sql.="',termbegin=".$val["termbegin"].",termend=".$val["termend"].",kdview=".$val["kdview"]." where id=".$val["UID"];
	$rc=$db->query($sql);
	if ($val["Tel1"]) mkTelNummer($val["UID"],"E",array($val["Tel1"]));
}

/****************************************************
* getAllUser
* in: sw = array(Art,suchwort)
* out: rs = array(Felder der db)
* hole alle Anwender
*****************************************************/
function getAllUser($sw) {
global $db,$Pre;
		if (!$sw[0]) { $where="workphone like '$Pre".$sw[1]."%' or homephone like '$Pre".$sw[1]."%' "; }
		else { $where="upper(name) like '$Pre".$sw[1]."%' "; }
		$sql="select * from employee where $where";
		$rs=$db->getAll($sql);
		if(!$rs) {
			$rs=false;
		};
		return $rs;
}


/****************************************************
* getUserStamm
* in: id = int
* out: daten = array
* AnwenderDaten holen
* !! in eine andere Lib verschieben
*****************************************************/
function getUserStamm($id) {
global $db;
	$sql="select * from employee where id=$id";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		$sql="select  * from gruppenname N left join grpusr G on G.grpid=N.grpid  where usrid=$id";
		$rs2=$db->getAll($sql);
		$daten["Id"]=$rs[0]["id"];
		$daten["Login"]=$rs[0]["login"];
		$daten["Name"]=$rs[0]["name"];
		$daten["Strasse"]=$rs[0]["addr1"];
		$daten["Plz"]=$rs[0]["addr2"];
		$daten["Ort"]=$rs[0]["addr3"];
		$daten["Tel2"]=$rs[0]["workphone"];
		$daten["Tel1"]=$rs[0]["homephone"];
		$daten["Bemerkung"]=$rs[0]["notes"];
		$daten["MailSign"]=$rs[0]["mailsign"];
		$daten["eMail"]=$rs[0]["email"];
		$daten["Regel"]=$rs[0]["role"];
		$daten["Msrv"]=$rs[0]["msrv"];
		$daten["Postf"]=$rs[0]["postf"];
		$daten["Kennw"]=$rs[0]["kennw"];
		$daten["Postf2"]=$rs[0]["postf2"];
		$daten["Interv"]=$rs[0]["interv"];
		$daten["Pre"]=$rs[0]["pre"];
		$daten["Abteilung"]=$rs[0]["abteilung"];
		$daten["Position"]=$rs[0]["position"];
		$daten["Vertreter"]=$rs[0]["vertreter"];
		$daten["gruppen"]=$rs2;
		$daten["etikett"]=$rs[0]["etikett"];
		$daten["countrycode"]=$rs[0]["countrycode"];
		$daten["termbegin"]=$rs[0]["termbegin"];
		$daten["termend"]=$rs[0]["termend"];
		$daten["kdview"]=$rs[0]["kdview"];
		return $daten;
	}
}

function getGruppen() {
global $db;
	$sql="select * from gruppenname order by grpname";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		return $rs;
	}
}

function delGruppe($id) {
global $db;
	$sql="select count(*) as cnt from customer where owener = $id";
	$rs=$db->getAll($sql);
	$cnt=$rs[0]["cnt"];
	$sql="select count(*) as cnt from vendor   where owener = $id";
	$rs=$db->getAll($sql);
	$cnt+=$rs[0]["cnt"];
	$sql="select count(*) as cnt from contacts where cp_owener = $id";
	$rs=$db->getAll($sql);
	$cnt+=$rs[0]["cnt"];
	if ($cnt===0) {
		$sql="delete from grpusr where grpid=$id";
		$rc=$db->query($sql);
		echo $sql; print_r($rc);
		if(!$rc) return "Mitglieder konnten nicht gel&ouml;scht werden";
		$sql="delete from gruppenname where grpid=$id";
		$rc=$db->query($sql);
		if(!$rc) return "Gruppe konnte nicht gel&ouml;scht werden";
		return "Gruppe gel&ouml;scht";
	} else {
		return "Gruppe wird noch benutzt.";
	}
}

function saveGruppe($data) {
global $db;
	if (strlen($data["name"])<2) return "Name zu kurz";
	$newID=uniqid (rand());
	$sql="insert into gruppenname (grpname,rechte) values ('$newID','".$data["rechte"]."')";
	$rc=$db->query($sql);
	if ($rc) {
		$sql="select * from gruppenname where grpname = '$newID'";
		$rs=$db->getAll($sql);
		if(!$rs) {
			return "Fehler beim Anlegen";
		} else {
			$sql="update gruppenname set grpname='".$data["name"]."' where grpid=".$rs[0]["grpid"];
			$rc=$db->query($sql);
			if(!$rc) {
				return "Fehler beim Anlegen";
			}
			return "Gruppe angelegt";
		}
	} else { return "Fehler beim Anlegen"; }
}

function getMitglieder($gruppe) {
global $db;
	$sql="select * from employee left join grpusr on usrid=id where grpid=$gruppe";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		return $rs;
	}
}

function saveMitglieder($mitgl,$gruppe) {
global $db;
	$sql="delete from grpusr where grpid=$gruppe";
	$rc=$db->query($sql);
	if ($mitgl) {
		foreach($mitgl as $row) {
			$sql="insert into grpusr (grpid,usrid) values ($gruppe,$row)";
			$rc=$db->query($sql);
		}
	}
}

function getOneGrp($id) {
global $db;
	$sql="select grpname from gruppenname where grpid=$id";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		return $rs[0]["grpname"];
	}
}
?>
