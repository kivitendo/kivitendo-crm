<?php
// $Id$

require_once("documents.php");
include ("mailLib.php");

/****************************************************
* mkSuchwort
* in: suchwort = String
* out: sw = array(Art,suchwort)
* Joker umwandeln, Anfrage ist Telefon oder Name
*****************************************************/
function mkSuchwort($suchwort) {
	$suchwort=ereg_replace("\*","%",$suchwort);
	$suchwort=ereg_replace("\?","_",$suchwort);
	if (ereg("^[0-9 -/]+[0-9 -/%]*$",$suchwort)) {   // Telefonnummer?
		$sw[0]=0;
	} else { 								// nein Name
		if (empty($suchwort)) $suchwort=" ";
		$sw[0]=1;
		setlocale(LC_ALL,"C");  // keine Großbuchastaben für Umlaute
		$suchwort=strtoupper($suchwort);
	};
	$sw[1]=$suchwort;
	return $sw;
}



/****************************************************
* getAllTelCall
* in: id = int, firma = boolean
* out: rs = array(Felder der db)
* hole alle Anrufe einer Person oder einer Firma
*****************************************************/
function getAllTelCall($id,$firma,$start=0,$lim=19) {
global $db;
	if (!$start || $start<0) $start=0;
	if ($firma) {	// dann hole alle Kontakte der Firma
		$sql="select id,caller_id,kontakt,cause,calldate,cp_name,inout from ";
		$sql.="telcall left join contacts on caller_id=cp_id where bezug=0 ";
		$sql.="and (caller_id in (select cp_id from contacts where cp_cv_id=$id) or caller_id=$id)";
 	} else {  // hole nur die einer Person
		$where="and caller_id=$id and caller_id=cp_id";
		$sql="select id,caller_id,kontakt,cause,calldate,cp_name,inout from ";
		$sql.="telcall left join contacts on caller_id=cp_id where bezug=0 and caller_id=$id";
	}
	$rs=$db->getAll($sql." order by calldate desc offset $start ".(($lim>0)?"limit $lim":""));
	if(!$rs) {
		$rs=false;
	} else {
		if ($start>0 && count($rs)==0) { //Mit dem Offset keine Eitr�ge gefunden, also die vorherigen Eintr�ge lesen
			$start=($start>$lim)?($start-$lim):0;
			$rs=$db->getAll($sql." order by calldate desc offset $start ".(($lim>0)?"limit $lim":""));
		}
		//Neuesten Eintrag ermitteln
		$sql="select telcall.* from telcall left join contacts on caller_id=cp_id where  ";
		$sql.="(caller_id in (select cp_id from contacts where cp_cv_id=$id) or caller_id=$id) ";
		$sql.="order by calldate desc limit 1";
		$rs2=$db->getAll($sql);
		if ($rs2[0]["bezug"]==0) { $new=$rs2[0]["id"]; }
		else { $new=$rs2[0]["bezug"]; };
		$i=0;
		foreach ($rs as $row) {
			if ($row["id"]==$new) {
				$rs[$i]["new"]=1;
				break;
			}
			$i++;
		}
	}
	return $rs;
}

/****************************************************
* getAllTelCallMax
* in: id = int, firma = int
* out: count(rs) = int
* Anzahl aller Einträge einer Fa
*****************************************************/
function getAllTelCallMax($id,$firma) {
global $db;
	if ($firma) {	// dann hole alle Kontakte der Firma
		$sql="select id,caller_id,kontakt,cause,calldate,cp_name from ";
		$sql.="telcall left join contacts on caller_id=cp_id where bezug=0 ";
		$sql.="and (caller_id in (select cp_id from contacts where cp_cv_id=$id) or caller_id=$id)";
 	} else {  // hole nur die einer Person
		$where="and caller_id=$id and caller_id=cp_id";
		$sql="select id,caller_id,kontakt,cause,calldate,cp_name from ";
		$sql.="telcall left join contacts on caller_id=cp_id where bezug=0 and caller_id=$id";
	}
	$rs=$db->getAll($sql);
	return count($rs);
}
/****************************************************
* getAllTelCallUser
* in: id = int, firma = boolean
* out: rs = array(Felder der db)
* hole alle Anrufe einer Person oder einer Firma
*****************************************************/
function getAllTelCallUser($id,$start=0,$art) {
global $db;
	if (!$start) $start=0;
	$sql="select telcall.id,caller_id,kontakt,cause,calldate,cp_email,C.email as cemail,";
	$sql.="V.email as vemail,V.id as vid, C.id as cid,cp_id as pid from telcall ";
	$sql.="left join contacts on cp_id=caller_id ";
	$sql.="left join customer C on C.id=caller_id ";
	$sql.="left join vendor V on V.id=caller_id ";	
	$sql.="where telcall.employee=$id and kontakt = '$art'";
	$rs=$db->getAll($sql." order by calldate desc offset $start limit 19");
	if(!$rs) {
		$rs=false;
	} else {
		$sql="select telcall.* from telcall left join contacts on caller_id=cp_id where  ";
		$sql.="(caller_id in (select cp_id from contacts where cp_cv_id=$id) or caller_id=$id) ";
		$sql.="order by calldate desc limit 1";
		$rs2=$db->getAll($sql);
		if ($rs2[0]["bezug"]==0) { $new=$rs2[0]["id"]; }
		else { $new=$rs2[0]["bezug"]; };
		$i=0;
		foreach ($rs as $row) {
			if ($row["id"]==$new) {
				$rs[$i]["new"]=1;
				break;
			}
			$i++;
		}
	}
	return $rs;
}

/****************************************************
* delTelCall
* in: id = int
* out: 
* einen TelCall Eintrag löschen
*****************************************************/
function delTelCall($id) {
global $db;
	$rs=$db->getAll("select * from telcall where id=$id");
	if ($rs[0]["bezug"]==0) {
		$sql="delete from telcall where bezug=$id";
		$rs=$db->query($sql);
	}
	$sql="delete from telcall where id=$id";
	$rc=$db->query($sql);
}

/****************************************************
* saveAllTelCall
* in: id = int
* out: rs = array(Felder der db)
* sichert einen geänderten TelCall-Eintrag
*****************************************************/
function saveTelCall($id,$empl,$grund) {
global $db;
	$sql="select id,cause,caller_id,calldate,c_long,employee,kontakt,bezug,dokument from telcall where id = %d";
	$rs=$db->getAll(sprintf($sql,$id));
	$tmp=$rs[0];
	$sql="insert into telcallhistory (orgid,cause,caller_id,calldate,c_long,employee,kontakt,bezug,dokument,chgid,grund,datum)";
	$sql.=" values (%d,'%s',%d,'%s','%s',%d,'%s',%d,%d,%d,'%s','%s')";
	$rs=$db->query(sprintf($sql,$tmp["id"],$tmp["cause"],$tmp["caller_id"],$tmp["calldate"],$tmp["c_long"],
				$tmp["employee"],$tmp["kontakt"],$tmp["bezug"],$tmp["dokument"],$empl,$grund,date("Y-m-d H:i:s")));
	return $rs;
}

/****************************************************
* mkPager
* in: items = array, 
* in: pager = int, start = int, next = int, prev = int
* out: 
* TelCall-Einträge Seitenweise bereitstellen
*****************************************************/
function mkPager(&$items,&$pager,&$start,&$next,&$prev) {
	if ($items) {
		$pager=$start;
		if (count($items)==19) {
			$next=$start+19;
			$prev=($start>19)?($start-19):0;
		} else {
			$next=$start;
			$prev=($start>19)?($start-19):0;
		}
	} else if ($start>0) {
		$pager=($start>19)?($start-19):0;
		$item[]=array(id => "",calldate => "", caller_id => $employee, cause => "Keine weiteren Eintr&auml;ge" );
		$next=$start;
		$prev=($pager>19)?($pager-19):0;
	} else {
		$pager=0;
		$next=0;
		$prev=0;
	}
}

/****************************************************
* mvTelCall
* in: TID = int, Anzeige = int, CID = int
* out: rs = boolean
* einen TelCall-Eintrag verschieben
*****************************************************/
function mvTelcall($TID,$Anzeige,$CID) {
global $db;
	$call=getCall($Anzeige,$_SESSION["loginCRM"],"U");
	$caller="";
	if ($call["CID"]!=$CID) {
		//saveTextCall($Anzeige);
		if ($call["bezug"]==0) {
			$sql="update telcall set caller_id=$CID where id=$Anzeige";
		} else {
			$sql="update telcall set bezug=0, caller_id=$CID where id=$Anzeige";
		}
		$rc=$db->query($sql);
	} 
	if ($TID<>$Anzeige) {
		if ($call["bezug"]==0) {
			$sql="update telcall set bezug=$TID where id=$Anzeige or Bezug=$Anzeige";
			$sqlH="update telcallhistory set orgid=$TID where orgid=$Anzeige or Bezug=$Anzeige";
		} else {
			$sql="update telcall set bezug=$TID where id=$Anzeige";
			$sqlH="update telcallhistory set orgid=$TID where orgid=$Anzeige";
		}
		$rc=$db->query($sqlH);
	} else {
		return false;
	}
	$rs=$db->query($sql);
	return $rs;
}

/****************************************************
* getAllUsrCall
* in: id = int
* out: rs = array(Felder der db)
* hole alle Anrufe einer Person
* wo erfolgt er aufruf? kann ersetzt werden, s.o.
*****************************************************/
function getAllUsrCall($id) {
global $db;
	$sql="select * from telcall where caller_id=$id order by calldate desc";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$rs=false;
	}
	return $rs;
}

/****************************************************
* getAllCauseCall
* in: id = int
* out: rs = array(Felder der db)
* hole alle Anrufe einer Person zu einem Betreff
*****************************************************/
function getAllCauseCall($id) {
global $db;
	$sql="select * from telcall where id=$id";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$rs=false;
	} else {
		if ($rs[0]["bezug"]===0) {  // oberste Ebene
			$sql="select * from telcall where bezug=".$rs[0]["id"]."order by calldate desc";
		} else {
			$sql="select * from telcall where bezug=".$rs[0]["id"]." or id=$id order by calldate desc";
		}
		$rs=$db->getAll($sql);
		if(!$rs) {
			$rs=false;
		}
	}
	return $rs;
}

/****************************************************
* insFormDoc  !!wird nur in prtWVertragOOo.php benutzt. ändern!!
* in: data = array(Formularfelder)
* out: id = des Calls
* ein neues FormDokument speichern
*****************************************************/
function insFormDoc($data,$file) {
global $db;
	$sql="select * from docvorlage where docid=".$data["docid"];
	$rs=$db->getAll($sql);
	$datum=date("Y-m-d H:i:00");
	$id=mknewTelCall();
	$dateiID=0;
	$did="null";
	$datei["Datei"]["tmp_name"]="./tmp/".$file;
	$datei["Datei"]["size"]=filesize("./tmp/".$file);
	$datei["Datei"]["name"]=$file;
	$dateiID=saveDokument($datei,$rs[0]["vorlage"],$datum,$data["CID"],$data["CRMUSER"],""); //##### letzten Parameter noch ändern
	$did=documenttotc($id,$dateiID);
	$c_cause=addslashes($rs[0]["beschreibung"]);
	$c_cause=nl2br($rs[0]["beschreibung"]);
	$sql="update telcall set cause='".$rs[0]["vorlage"]."',c_long='$c_cause',caller_id='".$data["CID"];
	$sql.="',calldate='$datum',kontakt='D',dokument=$did,bezug='0',employee=".$data["CRMUSER"]." where id=$id";
	$rs=$db->query($sql);
	if(!$rs) {
		$id=false;
	}
	return $id;
}


/****************************************************
* insCall
* in: data = array(Formularfelder) datei = übergebene Datei
* out: id = des Calls
* einen neuen Anruf speichern
*****************************************************/
function insCall($data,$datei) {
global $db;
	$data['Datum']=date2db($data['Datum']);
	$id=mknewTelCall();
	$dateiID=0;
	$did=($data["DateiID"]>0)?$data["DateiID"]:"null";
	if ($data["fid"]!=$data["CID"]) {
		$pfad="/".$data["Q"][0].$data["nummer"]."/".$data["CID"];
	} else {
		$pfad="/".$data["Q"][0].$data["nummer"];
	}
	$datum=$data['Datum']." ".$data['Zeit'].":00";  // Postgres timestamp
	$anz=($datei["Datei"]["name"][0]<>"")?count($datei["Datei"]["name"]):0;
	for ($o=0; $o<$anz; $o++) {
		if ($datei["Datei"]["name"][$o]<>"") {
			$dat["Datei"]["name"]=$datei["Datei"]["name"][$o];
			$dat["Datei"]["tmp_name"]=$datei["Datei"]["tmp_name"][$o];
			$dat["Datei"]["type"]=$datei["Datei"]["type"][$o];
			$dat["Datei"]["size"]=$datei["Datei"]["size"][$o];
			$text=($data["DCaption"])?$data["DCaption"]:$data["cause"];
			$dbfile=new document();
			$dbfile->setDocData("descript",$text);
			$rc=$dbfile->uploadDocument($dat,$pfad);
			$dateiID=$dbfile->id;
			//$dateiID=saveDokument($dat,$text,$datum,$data["CID"],$data["CRMUSER"],$pfad);
			$did=documenttotc($id,$dateiID);
			$did=1;
		};
	}
	$c_cause=addslashes($data["c_cause"]);
	$c_cause=nl2br($c_cause);
	$sql="update telcall set cause='".$data["cause"]."',c_long=E'$c_cause',caller_id='".$data["CID"];
	$sql.="',calldate='$datum',kontakt='".$data["Kontakt"]."',dokument=$did,bezug='".$data["Bezug"];
	$sql.="',employee='".$data["CRMUSER"]."',inout='".$data["inout"]."' where id=$id";
	$rs=$db->query($sql);
	if(!$rs) {
		$id=false;
	}
	return $id;
}
/****************************************************
* updCall
* in: data = array(Formularfelder) datei = übergebene Datei
* out: id = des Calls
* einen geänderten Anruf speichern
*****************************************************/
function updCall($data,$datei=false) {
global $db;
	$data['Datum']=date2db($data['Datum']);
	$did=($data["datei"])?$data["datei"]:"Null";
	$datum=$data['Datum']." ".$data['Zeit'].":00";  // Postgres timestamp
	if ($datei["Datei"]["name"][0]<>"") {
		$dat["Datei"]["name"]=$datei["Datei"]["name"][0];
		$dat["Datei"]["tmp_name"]=$datei["Datei"]["tmp_name"][0];
		$dat["Datei"]["type"]=$datei["Datei"]["type"][0];
		$dat["Datei"]["size"]=$datei["Datei"]["size"][0];
		$text=($data["DCaption"])?$data["DCaption"]:$data["cause"];
		$dbfile=new document();
		$dbfile->setDocData("descript",$text);
		$rc=$dbfile->uploadDocument($dat,$pfad);
		$dateiID=$dbfile->id;
		//$dateiID=saveDokument($dat,$text,$datum,$data["CID"],$data["CRMUSER"],"/".$data["nummer"]);
		$did=documenttotc($id,$dateiID);
		$did=1;
	}
	$c_cause=addslashes($data["c_cause"]);
	$c_cause=nl2br($c_cause);
	$sql="update telcall set cause='".$data["cause"]."',c_long='$c_cause',caller_id='".$data["CID"]."',";
	$sql.="calldate='$datum',kontakt='".$data["Kontakt"]."',dokument=$did,bezug='".$data["bezug"]."',";
	$sql.="employee='".$data["CRMUSER"]."',inout='".$data["inout"]."' where id=".$data["id"];
	$rs=$db->query($sql);
	if(!$rs) {
		$id=false;
	}
	return $id;
}

/****************************************************
* mknewTelCall
* in:
* out: id = int
* TelCallsatz erzeugen ( insert )
*****************************************************/
function mknewTelCall() {
global $db;
	$newID=uniqid (rand());
	$datum=date("Y-m-d H:m:i");
	$sql="insert into telcall (cause,caller_id,calldate) values ('$newID',0,'$datum')";
	$rc=$db->query($sql);
	if ($rc) {
		$sql="select id from telcall where cause = '$newID'";
		$rs=$db->getAll($sql);
		if ($rs) {
			$id=$rs[0]["id"];
		} else {
			$id=false;
		}
	} else {
		$id=false;
	}
return $id;
}

/****************************************************
* mknewWVL
* in:
* out: id = int
* WVLnsatz erzeugen ( insert )
*****************************************************/
function mknewWVL() {
global $db;
	$newID=uniqid (rand());
	$datum=date("Y-m-d H:m:i");
	$sql="insert into wiedervorlage (cause,initdate,initemployee) values ('$newID','$datum',".$_SESSION["loginCRM"].")";
	$rc=$db->query($sql);
	if ($rc) {
		$sql="select id from wiedervorlage where cause = '$newID'";
		$rs=$db->getAll($sql);
		if ($rs) {
			$id=$rs[0]["id"];
		} else {
			$id=false;
		}
	} else {
		$id=false;
	}
return $id;
}

/****************************************************
* mknewDocument
* in:
* out: id = int
* Dokumentsatz erzeugen ( insert )
*****************************************************/
function mknewDocument() {
global $db;
	$newID=uniqid (rand());
	$datum=date("Y-m-d H:m:i");
	$sql="insert into documents (descript) values ('$newID')";
	$rc=$db->query($sql);
	if ($rc) {
		$sql="select id from documents where descript = '$newID'";
		$rs=$db->getAll($sql);
		if ($rs) {
			$id=$rs[0]["id"];
		} else {
			$id=false;
		}
	} else {
		$id=false;
	}
return $id;
}


/****************************************************
* saveDokument
* in: Datei = array(Formularfile), beschreibung =  string
* in: datum = string,  CID = int, crmuser = string
* out: rs = id des Dokumentes
* ein Dokument sichern
*****************************************************/
function saveDokument($Datei,$Beschreibung,$datum,$CID,$CRMUSER,$pfad="") {
global $db;
	$Name=$Datei["Datei"]["name"];
	$Size=$Datei["Datei"]["size"];
	if ($CID>0) {									// gehört einem Kontakt
		$dir=$_SESSION["mansel"]."/".$CID;
	} else {  									// gehört einem User
		$dir=$_SESSION["mansel"]."/".$CRMUSER;
	};
	$dest="./dokumente/".$_SESSION["mansel"].$pfad."/".$Name;
	$ok=chkdir($pfad);
	if (copy($Datei["Datei"]["tmp_name"],$dest)) {
		//unlink($Datei["Datei"]['tmp_name']);	Macht doch PHP selber
		$id=mknewDocument();
		$sql="update documents set filename='$Name',descript='$Beschreibung',datum='$datum',";
		$sql.="size=$Size,employee=$CRMUSER,kunde=$CID,pfad=$pfad where id=$id";
		$rs=$db->query($sql);
		if(!$rs) {
			$rs=false;
		} else { $rs=$id; }
	} else {
		$RC="Datei '$dest' wurde nicht hochgeladen!";
		$rs=false;
	};
	return $rs;
}

/****************************************************
* saveDocAttribut
* in: name = string, pfad =  string
* in: komment = string,  id = int, kunde = id
* out: 
* Attribute zu einem Dokument sichern
*****************************************************/
function saveDocAttribut($name,$pfad,$komment,$id,$kunde) {
global $db;
	if ($id>0) $rs=getDokument($id);
	if ($rs) {
		$sql="update documents set filename='$name',descript='$komment' where id = $id";
	} else {
		$sql="insert into documents (filename,descript,datum,employee,kunde,pfad) values (";
		$sql.="'$name','$komment',".date("Y-m-d").",".$_SESSION["loginCRM"].",$kunde,'$pfad')";
	}
	$rc=$db->query($sql);
}

/****************************************************
* delDokument
* in: id = int
* out:
* ein Dokument löschen
*****************************************************/
function delDokument($id,$file="") {
global $db;
	if ($id>0) {
		$data=getDokument($id); // gibt es das dokument
		if ($data) {
			$file="dokumente/".$_SESSION["mansel"].$data["pfad"]."/".$data["filename"];
			$sql="delete from documents where id=$id";   // aus db l�schen
			$rs=$db->query($sql);
		}
	}
	if ($file) unlink("$file");
}

/****************************************************
* getDokument
* in: id = int
* out: rs = array(Felder der db)
* ein Dokument aus db holen
*****************************************************/
function getDokument($id) {
global $db;
	$sql="select * from documents where id=$id";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$rs=false;
	}
	return $rs[0];
}

/****************************************************
* getAllDokument
* in: id = int
* out: rs = array(Felder der db)
* alle Dokumente zu einem telcall aus db holen
*****************************************************/
function getAllDokument($id){
global $db;
	$sql="select B.* from documenttotc A,documents B where A.telcall=$id and A.documents=B.id";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$rs=false;
	}
	return $rs;
}

/****************************************************
* getCall
* in: id = int
* out: rs = array(Felder der db)
* einen Datensatz aus telcall holen
*****************************************************/
function getCall($id) {
global $db;
	$sql="select * from telcall where id=$id";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$daten=false;
	} else {
		$daten["Datum"]=db2date(substr($rs[0]["calldate"],0,10));
		$daten["Zeit"]=substr($rs[0]["calldate"],11,5);
		$daten["Betreff"]=$rs[0]["cause"];
		$daten["Kontakt"]=$rs[0]["kontakt"];
		$c_cause=ereg_replace("<br />","",$rs[0]["c_long"]);
		$c_cause=stripslashes($c_cause);
		$daten["LangTxt"]=$c_cause;
		$daten["CID"]=$rs[0]["caller_id"];
		$daten["inout"]=$rs[0]["inout"];
		$daten["Bezug"]=$rs[0]["bezug"];
		$daten["employee"]=$rs[0]["employee"];
		if ($rs[0]["dokument"]==1) {
			$daten["Files"]=getAllDokument($id);
		} else if ($rs[0]["dokument"]>1) {
			$dat=getDokument($rs[0]["dokument"]);
			if ($dat) {
				$daten["Kunde"]=($dat["kunde"]>0)?$dat["kunde"]:$dat["employee"];
				$daten["Datei"]=$dat["filename"];
				$daten["Dpfad"]=$dat["pfad"];
				$daten["DCaption"]=$dat["descript"];
			} else {
				$daten["Dpfad"]="";
				$daten["Datei"]="";
				$daten["DCaption"]="";
				$daten["Kunde"]="";
			}
		} else {
			$daten["Dpfad"]="";
			$daten["Datei"]="";
			$daten["DCaption"]="";
			$daten["Kunde"]="";
		}
		$daten["ID"]=$id;
		$daten["history"]=getCntCallHist($id);
	}
	return $daten;
}

/****************************************************
* getCntCallHist
* in: id = int, bezug = boolean
* out: int
* Änderungen an TelCall inst History schreiben 
*****************************************************/
function getCntCallHist($id,$bezug=false) {
global $db;
	if ($bezug) {
		$sql="select count(*) as cnt from telcallhistory where bezug=$id and grund='D'";
	} else  {
		$sql="select count(*) as cnt from telcallhistory where orgid=$id";
	}
	$rs=$db->getAll($sql);
	return $rs[0]["cnt"];
}

/****************************************************
* getCallHistory
* in:  id = int, bezug = boolean
* out: array
* History zu einem TelCall holen
*****************************************************/
function getCallHistory($id,$bezug=false) {
global $db;
	if ($bezug) {
		$sql="select * from telcallhistory where bezug=$id order by datum desc";
	} else  {
		$sql="select * from telcallhistory where orgid=$id order by datum desc";
	}
	$rs=$db->getAll($sql);
	return $rs;
}

/****************************************************
* getWvl
* in: crmuser = int
* out: rs = array(Felder der db)
* alle wiedervorlagen eines Users auslesen
*****************************************************/
function getWvl($crmuser) {
global $db;
	$sql="select * from wiedervorlage where (employee=$crmuser or employee is null) and status > '0' order by  finishdate asc ,initdate asc";
	$rs1=$db->getAll($sql);
	if(!$rs1) {
		$rs1=false;
	} else {
		if (count($rs1)==0) $rs1=array(array("id"=>0,"initdate"=>date("Y-m-d H:i:00"),"cause"=>"Keine Eintr&auml;ge"));
	}
	$sql="SELECT follow_ups.id,follow_up_date,created_for_user,subject,body,trans_id,note_id,trans_module from ";
	$sql.="follow_ups left join notes on note_id=notes.id where done='f' and created_for_user=$crmuser";
	$rs2=$db->getAll($sql);
	if ($rs2) {
		foreach ($rs2 as $row) {
			$rs1[]=array("id"=>$row["id"],
				"cause"=>$row["subject"],
				"desctipt"=>$row["body"],
				"kontakt"=>$row["trans_module"],
				"status"=>"F",
				"initemployee"=>$row["created_by"],
				"employee"=>$row["created_for_user"],
				"initdate"=>$row["follow_up_date"],
				"note_id"=>$row["note_id"]);
		}
	}
	return $rs1;
}

/****************************************************
* getOneWvl
* in: id = int
* out: rs = array(Felder der db)
* einen Datensatz aus wiedervorlage holen
*****************************************************/
function getOneWvl($id) {
global $db;
	//$sql="select W.*,C.cp_name,C.cp_givenname from wiedervorlage W left join contacts C on W.kontaktid=C.cp_id where id=$id";
	$sql="select * from wiedervorlage where id=$id";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$data=false;
	} else {
		switch ($rs[0]["kontakttab"]) {
			case "C" : $sql="select name,'' as sep,'' as name2 from customer where id = ".$rs[0]["kontaktid"]; 
						$rsN=$db->getAll($sql); 
						break;
			case "L" : $sql="select name,'' as sep,'' as name2  from vendor where id = ".$rs[0]["kontaktid"];
						$rsN=$db->getAll($sql); 
						break;
			case "P" : $sql="select cp_name as name ,', ' as sep ,cp_givenname as name2 from contacts where cp_id = ".$rs[0]["kontaktid"];
						$rsN=$db->getAll($sql); 
						break;
			default	:	$rsN=false;
		}
		if ($rs[0]["document"]) { // gibt es ein Dokument
			$datei=getDokument($rs[0]["document"]);
			if ($datei) {
				$pre=($datei["kunde"]>0)?$datei["kunde"]:$datei["employee"];
				$name=$datei["filename"];
				$path=$_SESSION["mansel"]."/".$pre."/";
			} else {
				$name="";
				$path="";
			}
		} else {
			$name="";
			$path="";
		}
		$data["id"]=$rs[0]["id"];
		$data["Initdate"]=$rs[0]["initdate"];
		$data["Change"]=$rs[0]["changedate"];
		$data["Finish"]=($rs[0]["finishdate"]<>"")?db2date(substr($rs[0]["finishdate"],0,12)):"";
		$data["Cause"]=$rs[0]["cause"];
		$data["LangTxt"]=stripslashes(ereg_replace("<br />","",$rs[0]["descript"]));
		$data["Datei"]=$rs[0]["document"];
		$data["DName"]=$name;
		$data["DPath"]=$path;
		$data["DCaption"]=$datei["descript"];
		$data["status"]=$rs[0]["status"];
		$data["CRMUSER"]=$rs[0]["employee"];
		$data["InitCrm"]=$rs[0]["initemployee"];
		$data["kontakt"]=$rs[0]["kontakt"];
		$data["tellid"]=$rs[0]["tellid"];
		$data["kontaktid"]=$rs[0]["kontaktid"];
		$data["kontakttab"]=$rs[0]["kontakttab"];
		$data["kontaktname"]=$rsN[0]["name"].$rsN[0]["sep"].$rsN[0]["name2"];
	}
	return $data;
}

/****************************************************
* getOneERP
* in: id = int
* out: rs = array(Felder der db)
* einen Datensatz aus follow_ups/notes holen
*****************************************************/
function getOneERP($id) {
global $db;
	$sql="SELECT follow_ups.id,follow_up_date,created_for_user,subject,body,trans_id,note_id,trans_module,follow_ups.created_by,";
	$sql.="follow_ups.itime,follow_ups.mtime ";
	$sql.="from follow_ups left join notes on note_id=notes.id where done='f' and follow_ups.id=$id";
	$rs=$db->getAll($sql);
	$data["id"]=$rs[0]["id"];
	$data["Initdate"]=substr($rs[0]["itime"],0,19);
	$data["Change"]=substr($rs[0]["mtime"],0,19);
	$data["Finish"]=($rs[0]["follow_up_date"]<>"")?db2date($rs[0]["follow_up_date"]):"";
	$data["Cause"]=$rs[0]["subject"];
	$data["LangTxt"]=stripslashes(ereg_replace("<br />","",$rs[0]["body"]));
	$data["Datei"]="";
	$data["DName"]="";
	$data["DPath"]="";
	$data["DCaption"]="";
	$data["status"]="1";
	$data["CRMUSER"]=$rs[0]["created_for_user"];
	$data["InitCrm"]=$rs[0]["created_by"];
	$data["kontakt"]="F";
	$data["noteid"]=$rs[0]["note_id"];
	$data["kontaktid"]="";
	$data["kontakttab"]="";
	$data["kontaktname"]=$rsN[0]["name"].$rsN[0]["sep"].$rsN[0]["name2"];
	return $data;
}

/****************************************************
* mkPfad
* in: wer = String
* out: pfad = String
* einen Dokumentenpfad erstellen
*****************************************************/
function mkPfad($wer,$alt) {
global $db;
	$pfad="";
	if (substr($wer,0,1)=="P") {
		$tmp=substr($wer,1);
		$rs=$db->getAll("select customernumber from customer C, contacts P where P.cp_cv_id=C.id and cp_id=$tmp");
		if ($rs[0]["customernumber"]) {
			$pfad="C".$rs[0]["customernumber"]."/$tmp";
		} else {
			$rs=$db->getAll("select vendornumber from vendor V, contacts P where P.cp_cv_id=V.id and cp_id=$tmp");
			if ($rs[0]["vendornumber"]) {
				$pfad="V".$rs[0]["vendornumber"]."/$tmp";
			} else {
				$pfad=$tmp;
			}
		}
	} else if ($wer<>""){
		$tmp=substr($wer,1);
		$ttmp=substr($wer,0,1);
		if ($ttmp=="C") {
			$rs=$db->getAll("select customernumber as number from customer where id=$tmp");
		} else {
			$rs=$db->getAll("select vendornumber as number from vendor where id=$tmp");
		}
		$pfad=$ttmp.$rs[0]["number"];
	} else  {
		$pfad=$alt;
	}
	return $pfad;
}

/****************************************************
* insWvl
* in: data = array(Formularfelder), datei = übergebene Datei
* out: rs = boolean
* einen Datensatz in wiedervorlage einfügen
*****************************************************/
function insWvl($data,$datei="") {
	$data["WVLID"]=mknewWVL();
	$rs=updWvl($data,$datei);
	return $rs;
}

/****************************************************
* updWvl
* in: data = array(Formularfelder), datei = übergebene Datei
* out: rs = boolean
* einen Datensatz in wiedervorlage aktualisieren
*****************************************************/
function updWvl($data,$datei="") {
global $db;
	$nun=date("Y-m-d H:i:00");
	$anz=0;
	$pfad="";
	if ($datei) $anz=($datei["Datei"]["name"][0]<>"")?count($datei["Datei"]["name"]):0;
	if ($anz>0) {  // ein neues Dokument
		if ($data["DateiID"]) delDokument($data["DateiID"]); // ein altes löschen
		for ($o=0; $o<$anz; $o++) {
			$dat["Datei"]["name"]=$datei["Datei"]["name"][$o];
			$dat["Datei"]["tmp_name"]=$datei["Datei"]["tmp_name"][$o];
			$dat["Datei"]["type"]=$datei["Datei"]["type"][$o];
			$dat["Datei"]["size"]=$datei["Datei"]["size"][$o];
			if (!$data["DCaption"]) $data["DCaption"]=$data["Cause"];
	                $dbfile=new document();
	                $dbfile->setDocData("descript",$data["DCaption"]);
			$pfad=mkPfad($data["cp_cv_id"],$data["CRMUSER"]);
        	        $rc=$dbfile->uploadDocument($dat,$pfad);
                	$dateiID=$dbfile->id;       
			//$dateiID=saveDokument($dat,$data["DCaption"],$nun,0,$data["CRMUSER"],"");
		}
		if ($anz>1) $dateiID=1;
	} else {
		$dateiID=$data["DateiID"];
	}
	if (empty($dateiID)) $dateiID=0;
	$finish=($data["Finish"]<>"")?", finishdate='".date2db($data["Finish"])." 0:0:00'":"";
	$descript=addslashes($data["LangTxt"]);
	$descript=nl2br($descript);
	if ($data["kontakt"]=="F") {
		$rc=$db->query("BEGIN");
		$sql="update notes set subject='".$data["Cause"]."',body='$descript', created_by=".$_SESSION["loginCRM"];
		$sql.=" where id=".$data["noteid"];
		$rc=$db->query($sql);
		if (!$rc) { $db->query("ROLLBACK"); return false; };
		$sql="update follow_ups set created_for_user=".$data["CRMUSER"].",done='".(($data["status"]!=1)?"t":"f")."', ";
		$sql.="follow_up_date ='".date2db($data["Finish"])."' where id = ".$data["WVLID"];
		$rc=$db->query($sql);
		if (!$rc) { $db->query("ROLLBACK"); return false; };
		$rs=$db->query("COMMIT");
	} else {
		$sql="update wiedervorlage set employee=".$data["CRMUSER"].", cause='".$data["Cause"]."', descript='$descript', ";
		$sql.="document=$dateiID, status=".$data["status"].",kontakt='".$data["kontakt"]."',changedate='$nun'".$finish;
		$sql.=" where id=".$data["WVLID"];
		$rs=$db->query($sql);
		if(!$rs) {
			$rs=false;
		} else {
			$rs=$data["WVLID"];
		};
		if ($data["cp_cv_id"]<>$data["cp_cv_id_old"] or $data["status"]<1) {  // es wurde eine neue Zuweisung an einen Kunden gemacht
			$id=kontaktWvl($data["WVLID"],$data["cp_cv_id"],$pfad);
			if ($id) {$rs=$data["WVLID"];} else {$rs=false;}
		}
	}
	return $rs;
}

/****************************************************
* documenttotc
* in: newID,did = integer
* out: rs = boolean
* eine DockId zum Telcall oder Person zuordnen
*****************************************************/
function documenttotc($newID,$did) {
global $db;
	$sql="insert into documenttotc (telcall,documents) values ($newID,$did)";
	$rs=$db->query($sql);
	return $rs;
}

/****************************************************
* documenttotc
* in: newID,did = integer
* out: rs = boolean
* eine DockId von Person auf Telcall ändern
*****************************************************/
function documenttotc_($newID,$tid) {
global $db;
	$sql="update documenttotc set telcall=$tid where telcall=$newID";
	$rs=$db->query($sql);
	return $rs;
}

/****************************************************
* insWvlM
* in: data = array(Formularfelder)
* out: rs = boolean
* einen Mail-Datensatz in WVL nach telcall verschieben
*****************************************************/
function insWvlM($data) {
global $db;
	if(empty($data["cp_cv_id"]) && $data["status"]<1) {
		$kontaktID=$data["CRMUSER"];
		//$data["cp_cv_id"]=$data["CRMUSER"];
	} else {  
		$kontaktID=substr($data["cp_cv_id"],1);
		$kontaktTAB=substr($data["cp_cv_id"],0,1);
	}
	if(!empty($kontaktID)) {
		$data["status"]=0;
		$nun=date("Y-m-d H:i:00");
		$data["kontakt"]="M";
		$did=false;
		$data["c_cause"]=$data["LangTxt"];
		$data["cause"]=$data["Cause"];
		$data["Bezug"]=0;
		$data["Kontakt"]="M";
		$data["Datum"]=date("d.m.Y");
		$data["Zeit"]=date("H:i");
		$CID=$data["CID"];
		$data["CID"]=$kontaktID;
		$tid=insCall($data,false);
		if (!$tid) return false;
		if (!empty($data["dateien"])) {
			$data["DateiID"]=true;
			foreach($data["dateien"] as $mail){
				//trenne Anhang und speichere in tmp
				$file=explode(",",$mail);
				$Datei["Datei"]["name"]=$file[0];
   				$Datei["Datei"]["tmp_name"]="./tmp/".$file[0];
				$Datei["Datei"]["size"]=$file[1];
				$dbfile=new document();
		        $dbfile->setDocData("descript",$data["DCaption"]);
				$pfad=mkPfad($data["cp_cv_id"],$data["CRMUSER"]);
        		$rc=$dbfile->uploadDocument($Datei,$pfad);
                $did=$dbfile->id;     
                documenttotc($tid,$did);
			}
			moveMail($data["Mail"],$CID);
			$sql="update telcall set dokument=1 where id = $tid";
			$rc=$db->query($sql);
			return $rc;
		} else {
			$data["DateiID"]=false;
			moveMail($data["Mail"],$data["CID"]);
		}
		// bis hier ok
		$rs=true;
	} else { 
		//$rs=false; 
		$rs=1;
	};
	return $rs;
}

/****************************************************
* kontaktWvl
* in: id,fid = int
* out: rs = id
* eine wiedervorlage mit telcall verbinden
*****************************************************/
function kontaktWvl($id,$fid,$pfad) {
global $db;
	$sql="select * from wiedervorlage where id=$id";
	$rs=$db->getAll($sql);
	if(!$rs) return false;
	$nun=date("Y-m-d H:i:00");
	$tab=substr($fid,0,1);
	$fid=substr($fid,1);
	if (!$db->begin()) return false;;
	if ($rs[0]["kontaktid"]>0 and $fid<>$rs[0]["kontaktid"]){
		// bisherigen Kontakteintrag ungültig markieren
		$sql="update telcall set cause=cause||' storniert' where id=".$rs[0]["tellid"];
		//$rc=$db->query($sql);
		if (!$db->query($sql)) return false;
	} 
	if (!$rs[0]["kontaktid"]>0 or empty($rs[0]["kontaktid"])) {
		$tid=mknewTelCall();
		$sql="update telcall set cause='".$rs[0]["cause"]."',caller_id=$fid,calldate='$nun',";
		$sql.="c_long='".$rs[0]["descript"]."',employee=".$rs[0]["employee"].",kontakt='".$rs[0]["kontakt"];
		$sql.="',bezug=0,dokument=".$rs[0]["document"]." where id=$tid";
		$rc=$db->query($sql);
		if(!$rc) {
			$db->rollback();
			return false;
		} else {
			$ok=$tid;
			$sql="update wiedervorlage set kontaktid=$fid,kontakttab='$tab',tellid=$tid where id=$id";
			if (!$db->query($sql)) {
				$db->rollback();
				return false;
			}
		}
	}
	if ($rs[0]["status"]<1) {
		if ($rs[0]["document"] && $rs[0]["kontakt"]<>"M") {
			$sql="select * from documents where id=".$rs[0]["document"];
			$rsD=$db->getAll($sql);
			$von="dokumente/".$_SESSION["mansel"]."/".$rsD[0]["employee"]."/".$rsD[0]["filename"];
			if (!$pfad) {
				//$pfad=$_SESSION["mansel"]."/".$pfad;
			//} else {
				$pfad=mkPfad($tab.$fid,$fid);
			}
			$ok=chkdir($pfad);
			$nach="dokumente/".$_SESSION["mansel"]."/".$pfad."/".$rsD[0]["filename"];
			if (file_exists($von)) {
				$rc=rename($von,$nach);
				if ($rc) {
					$sql="update documents set kunde=".$fid.", pfad='".$pfad."' where id=".$rsD[0]["id"];
					if (!$db->query($sql)) {
						$db->rollback();
						return false;
					}
				}
			} else if(file_exists($nach)) {
				$sql="update documents set kunde=".$fid.", pfad='".$pfad."' where id=".$rsD[0]["id"];
				if (!$db->query($sql)) {
					$db->rollback();
					return false;
				}
			} else {
				$db->rollback();
				return false;
			}
		}
	}
	return $db->commit();
}

/****************************************************
* decode_string
* in: string = string
* out: string = string
* dekodiert einen MailString
*****************************************************/
function decode_string ($string) {
   if (eregi("=?([A-Z,0-9,-]+)?([A-Z,0-9,-]+)?([A-Z,0-9,-,=,_]+)?=", $string)) {
      $coded_strings = explode('=?', $string);
      $counter = 1;
      $string = $coded_strings[0]; // add non encoded text that is before the encoding 
      while ($counter < sizeof($coded_strings)) {
         $elements = explode('?', $coded_strings[$counter]); // part 0 = charset 
         if (eregi("Q", $elements[1])) {
            $elements[2] = str_replace('_', ' ', $elements[2]);
            $elements[2] = eregi_replace("=([A-F,0-9]{2})", "%\\1", $elements[2]);
            $string .= urldecode($elements[2]);
         } else { // we should check for B the only valid encoding other then Q 
            $elements[2] = str_replace('=', '', $elements[2]);
            if ($elements[2]) { $string .= base64_decode($elements[2]); }
         }
         if (isset($elements[3]) && $elements[3] != '') {
            $elements[3] = ereg_replace("^=", '', $elements[3]);
            $string .= $elements[3];
         }
		 $string .= " ";
         $counter++;
      }
   }
   return $string;
}

/****************************************************
* holeMailHeader
* in: usr = int
* out: rs = array
* alle Mailheader holen
*****************************************************/
function holeMailHeader($usr) {
	$srv=getUsrMailData($usr);
	$m=array();
	if ($srv["msrv"] && $srv["postf"]) {  // Mailserver/Postfach eingetragen
        $mbox = mail_login($srv["msrv"],"143",$srv["postf"],$srv["kennw"]);
		if ($mbox) {
            $status = mail_stat($mbox);
			$anzahl= $status["Nmsgs"] - $status["Deleted"];
			if ($anzahl>0) {
                $overview = mail_list($mbox);
				$m=false;
				if (is_array ($overview )) {
                    foreach ($overview as $mail) {
                        if (!$mail["deleted"]) {
                            $gelesen=($mail["seen"])?"-":"+";
                            $m[]=array("Nr"     =>  $mail["msgno"],
                                    "Datum"     =>  $mail["date"]." ".$mail["time"],
                                    "Betreff"   =>  $mail["subject"],
                                    "Abs"       =>  $mail["from"],
                                    "Gelesen"   =>  $gelesen);
                        }
    				}
					if (empty($m)) $m[]=array("Nr"=>0,"Datum"=>"","Betreff"=>"Keine Mails","Abs"=>"","Gelesen"=>"");
				}
				imap_close ($mbox);
			} else {
				$m[]=array("Nr"=>0,"Datum"=>"","Betreff"=>"Keine Mails","Abs"=>"","Gelesen"=>"");
			}
		} else {  // Mailserver nicht erreicht
			$m[]=array("Nr"=>0,"Datum"=>"","Betreff"=>"can't connect to Mailserver ","Abs"=>"","Gelesen"=>"");
		}
		return $m;
	} else {
		return false;
	};
}

function getSenderMail($email) {
global $db;
	preg_match("/<(.*@.+\.[^>]+)/",$email,$clean);
	$sql="select id,name from customer where email like '%$clean%'";
	$clean=$clean[1];
	$rs=$db->getOne($sql);
	$t="C";
	if (!$rs) {
		$sql="select id,name from vendor where email like '%$clean%'";
		$rs=$db->getOne($sql);
		$t="V";
	} 
	if (!$rs) {
		$sql="select cp_id as id ,cp_name as name from contacts where cp_email like '%$clean%'";
		$rs=$db->getOne($sql);
		$t="P";
	} 
	if ($rs) {
		return array('kontaktname'=>$rs['name'],'kontaktid'=>$rs['id'],'kontakttab'=>$t);
	} else {
		return array('name'=>'','id'=>'');
	}
}

/****************************************************
* getOneMail
* in: usr = int, nr = int
* out: data = array
* eine Mail holen
*****************************************************/
function getOneMail($usr,$nr) {
	$files=array();
    mb_internal_encoding(ini_get("default_charset"));
	$srv=getUsrMailData($usr);
        $mbox = mail_login($srv["msrv"],"143",$srv["postf"],$srv["kennw"]);
    $head = mail_parse_headers(mail_retr($mbox,$nr));
	if (!$head) return;
    $senderadr = $head["From"]."\n".$head["Date"]."\n";
	$sender = getSenderMail($head["From"]);
	$mybody = $senderadr;
	$htmlbody = "Empty Message Body";
	$structure = imap_fetchstructure($mbox,$nr);
    $parts = create_part_array($structure);

	$body = mail_get_body($mbox,$nr,$parts[0]);
    $subject = $head["Subject"];

	if ( !eregi("PLAIN",$structure->subtype) )  {
       for ($p=1; $p < count($parts); $p++) {
            $attach = mail_get_file($mbox,$nr,$parts[$p]);
            if ($attach) $files[] = $attach;
        }
	}
	$data["id"]=$nr;
	$data['kontaktname']=$sender['kontaktname'];
	$data['kontakttab']=$sender['kontakttab'];
	$data['kontaktid']=$sender['kontaktid'];
	$data["sendername"]=$sender["name"];
	$data["senderid"]=$sender["id"];
	$data["Initdate"]=$head["date"];
	$data["Cause"]=$subject;
	$data["LangTxt"]=$mybody.$body; 
	$data["Datei"]=$anhang;
	$data["status"]="2";
	$data["InitCrm"]=$_SESSION["loginCRM"];	//$head[""];
	$data["CRMUSER"]=$_SESSION["employee"];	//$head[""];
	$data["DCaption"]=($files)?$data["Cause"]:"";
	$data["Anhang"]=$files;
	return $data;
}

/****************************************************
* getUsrMailData
* in: id = int
* out: data = array
* die Maildaten des Users holen
*****************************************************/
function getUsrMailData($id) {
global $db;
	$sql="select * from employee where id='$id'";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$data=false;
	} else {
		$data["msrv"]=$rs[0]["msrv"];
		$data["postf"]=$rs[0]["postf"];
		$data["kennw"]=$rs[0]["kennw"];
		$data["postf2"]=$rs[0]["postf2"];
	}
	return $data;
}

/****************************************************
* eine neue Mailbox erstellen
* in: name = string, id = int
* out:
* eine Mailbox anlegen
* !! geht nicht mit jeder IMAP - Installation
* !! noch weiter Testen
*****************************************************/
function createMailBox($name,$id) {
	$srv=getUsrMailData($id);
	$mbox = imap_open ("{".$srv["msrv"].":143/imap/notls}", $srv["postf"],$srv["kennw"]);
	$name1 = $name;
	$name2 = imap_utf7_encode ($name);
	$newname = $name1;
	echo "Newname will be '$name1'<br>\n";

	# we will now create a new mailbox "phptestbox" in your inbox folder,
	# check its status after creation and finaly remove it to restore
	# your inbox to its initial state

	if (@imap_createmailbox ($mbox,imap_utf7_encode ("{".$srv["msrv"]."}INBOX.$newname"))) {
	    $status = @imap_status($mbox,"{".$srv["msrv"]."}INBOX.$newname",SA_ALL);
	    if($status) {
		print("your new mailbox '$name1' has the following status:<br>\n");
		print("Messages:   ". $status->messages   )."<br>\n";
		print("Recent:     ". $status->recent     )."<br>\n";
		print("Unseen:     ". $status->unseen     )."<br>\n";
		print("UIDnext:    ". $status->uidnext    )."<br>\n";
		print("UIDvalidity:". $status->uidvalidity)."<br>\n";

		if (imap_renamemailbox ($mbox,"{".$srv["msrv"]."}INBOX.$newname", "{your.imap.host}INBOX.$name2")) {
		    echo "renamed new mailbox from '$name1' to '$name2'<br>\n";
		    $newname=$name2;
		} else {
		    print "imap_renamemailbox on new mailbox failed: ".imap_last_error ()."<br>\n";
		}
	    } else {
		    print "imap_status on new mailbox failed: ".imap_last_error()."<br>\n";
	    }
	    if (@imap_deletemailbox($mbox,"{".$srv["msrv"]."}INBOX.$newname")) {
		print "new mailbox removed to restore initial state<br>\n";
	    } else {
		print  "imap_deletemailbox on new mailbox failed: ".implode ("<br>\n", imap_errors())."<br>\n";
	    }
	} else {
	    print "could not create new mailbox: ".implode ("<br>\n",imap_errors())."<br>\n";
	}
	imap_close($mbox);
}

/****************************************************
* moveMail
* in: mail,id = int
* out:
* eine Mail  in eine andere Mailbox verschieben
* !! wg. Probleme mit einigen IMAP-Installationen
* !! nur ein markieren mit Delete
*****************************************************/
function moveMail($mail,$id) {
	$srv=getUsrMailData($id);
	$mbox = @imap_open ("{".$srv["msrv"].":143/imap/notls}", $srv["postf"],$srv["kennw"]);
	@imap_delete($mbox,$mail);
	// imap_mail_move ($mbox,$mail,$srv["postf2"]);
}

/****************************************************
* delMail
* in: mail,id = int
* out:
* eine Mail  als geslöscht marmieren
* !! wg. Probleme mit einigen IMAP-Installationen
* !! nur ein markieren mit Delete
*****************************************************/
function delMail($mail,$id) {
	$srv=getUsrMailData($id);
	$mbox = imap_open ("{".$srv["msrv"].":143/imap/notls}", $srv["postf"],$srv["kennw"]);
	imap_delete($mbox,$mail);
}

/****************************************************
* getIntervall
* in: id = int
* out: rs = int
* Userspezifischen Updateintervall holen
*****************************************************/
function getIntervall($id) {
global $db;
	$sql="select * from employee where id=$id";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return 60;
	}
	if ($rs[0]["interv"]) { return $rs[0]["interv"]; }
	else { return 60; }
}

/****************************************************
* getAllMails
* in: sw = string
* out: rs = array(Felder der db)
* hole alle eMails
*****************************************************/
function getAllMails($suche) {
global $db,$Pre;        
    $sql1="select name,'E' as src,id,email from employee where upper(email) like '$Pre".strtoupper($suche)."%' and email <> '' order by email";
    $rs1=$db->getAll($sql1);
    //$sql2="select name,'C' as src,id,email from customer where upper(email) like '$Pre".strtoupper($suche)."%' and email <> '' order by email";
    $sql2="select '' as name,'C' as src,id,email from customer where upper(email) like '$Pre".strtoupper($suche)."%' and email <> '' order by email";
    $rs2=$db->getAll($sql2);
    $sql3="select cp_name as name,'K' as src,cp_id as id,cp_email as email from contacts where upper(cp_email) like '$Pre".strtoupper($suche)."%' and cp_email <> '' order by cp_email";
    $rs3=$db->getAll($sql3);
    //$sql4="select shiptoname as name,'S' as src,trans_id as id,shiptoemail as email from shipto where upper(shiptoemail) like '$Pre".strtoupper($suche)."%' and shiptoemail <> ''  order by shiptoemail";
    $sql4="select '' as name,'S' as src,trans_id as id,shiptoemail as email from shipto where upper(shiptoemail) like '$Pre".strtoupper($suche)."%' and shiptoemail <> ''  order by shiptoemail";
    $rs4=$db->getAll($sql4);
    //$sql5="select name,'V' as src,id,email from vendor where upper(email) like '$Pre".strtoupper($suche)."%' and email <> '' order by email";
    $sql5="select '' as name,'V' as src,id,email from vendor where upper(email) like '$Pre".strtoupper($suche)."%' and email <> '' order by email";
    $rs5=$db->getAll($sql5);
    $rs=array_merge($rs2,$rs3,$rs5,$rs4,$rs1);
    usort($rs,"eMailSort");
    return $rs;
}

/****************************************************
* eMailSort
* in: a,b = array
* out: array
* Sortierfunktion für eMail-Adressen
*****************************************************/
function eMailSort($a,$b) {
    if ($a["name"] == $b["name"]) return 0;
    return ($a["name"] < $b["name"]) ? -1 : 1;
}

/****************************************************
* chkMailAdr
* in: mailadr = string
* out: string
* Mailaddr. auf Gültigkeit prüfen
*****************************************************/
function chkMailAdr ($mailadr) {
	if (substr(",",$mailadr)) {
		$tmp=explode(",",$mailadr);
	}else {
		$tmp=array($mailadr);
	}
	foreach($tmp as $mailadr) {
	 	$syntax=preg_match("/^(.*<)?([_A-Z0-9-]+[\._A-Z0-9-]*@[\.A-Z0-9-]+\.[A-Z]{2,4})>?$/i",trim($mailadr),$x);
		if ($syntax) {
			list($user, $host) = explode("@", array_pop($x));
			$dns=(checkdnsrr($host, "MX") or checkdnsrr($host, "A"));
			if (!$dns) return  "DNS-Fehler";
		} else {
			return "Syntax-Fehler";
		}
	}
	return "ok";
}

/****************************************************
* getReJahr
* in: fid = int
* out: rechng = array
* Rechnungsdaten je Monat
*****************************************************/
function getReJahr($fid,$jahr,$liefer=false,$user=false) {
global $db;
	$lastYearV=date("Y-m-d",mktime(0, 0, 0, date("m")+1, 1, $jahr-1));
	$lastYearB=date("Y-m-d",mktime(0, 0, 0, date("m"), 31, $jahr));
    if ($user) {
        $sea = " and salesman_id = ".$fid." ";
    } else if ($_SESSION["sales_edit_all"] == "f") {
        $sea = sprintf(" and (employee_id = %d or salesman_id = %d) ", $_SESSION["loginCRM"], $_SESSION["loginCRM"]);
    }
    $sql  = "select sum(netamount),count(*),substr(cast(transdate as text),1,4)||substr(cast(transdate as text),6,2) as month,'%s' from %s ";
    $sql .= "where %s=%d and transdate >= '%s' and transdate <= '%s' %s group by month ";

	if ($liefer) {
        $bezug = ($user)?"employee_id":"vendor_id";
		$rs2=$db->getAll(sprintf($sql,'A','oe',$bezug,$fid,$lastYearV,$lastYearB,$sea));
		$sql=sprintf($sql,'R','ap',$bezug,$fid,$lastYearV,$lastYearB,$sea);
	} else {
        $bezug = ($user)?"employee_id":"customer_id";
		$rs2=$db->getAll(sprintf($sql,'A','oe',$bezug,$fid,$lastYearV,$lastYearB,$sea));
		$sql=sprintf($sql,'R','ar',$bezug,$fid,$lastYearV,$lastYearB,$sea);
	};
	$rs1=$db->getAll($sql);
	$rs=array_merge($rs1,$rs2);
	$rechng=array();
    $curr = getCurr();
	for ($i=11; $i>=0; $i--) {
		$dat=date("Ym",mktime(0, 0, 0, date("m")-$i, 1 , $jahr));
		$rechng[$dat]=array("summe"=>0,"count"=>0,"curr"=>$curr);
	}
	$rechng["Jahr  "]=array("summe"=>0,"count"=>0,"curr"=>$curr);
	// unterschiedliche Währungen sind noch nicht berücksichtigt. Summe stimmt aber.
	if ($rs) foreach ($rs as $re){
        $m = $re["month"];
		$rechng[$m]["summe"] = $re["sum"];
		$rechng[$m]["count"] = $re["count"];
		$rechng["Jahr  "]["summe"] += $re["sum"];
		$rechng["Jahr  "]["count"]++;
	}
	return $rechng;
}

/****************************************************
* getAngebJahr
* in: fid = int
* out: rechng = array
* Angebotsdaten je Monat
*****************************************************/
function getAngebJahr($fid,$jahr,$liefer=false,$user=false) {
global $db;
	$lastYearV=date("Y-m-d",mktime(0, 0, 0, date("m"), 1, $jahr-1));
	$lastYearB=date("Y-m-d",mktime(0, 0, 0, date("m")+1, -1, $jahr));
    if ($user) {
        $sea = " and salesman_id = ".$fid." ";
    } else if ($_SESSION["sales_edit_all"] == "f") {
        $sea = sprintf(" and (employee_id = %d or salesman_id = %d) ", $_SESSION["loginCRM"], $_SESSION["loginCRM"]);
    }
    $sql  = "select sum(netamount),count(*),substr(cast(transdate as text),1,4)||substr(cast(transdate as text),6,2) as month from oe ";
    $sql .= "where %s=%d and quotation = 't' and transdate >= '%s' and transdate <= '%s' %s group by month ";
	if ($liefer) {
        $bezug = ($user)?"employee_id":"vendor_id";
	} else {
        $bezug = ($user)?"employee_id":"customer_id";
	}
	$rs=$db->getAll(sprintf($sql,$bezug,$fid,$lastYearV,$lastYearB,$sea));
	$rechng=array();
    $curr = getCurr();
	for ($i=11; $i>=0; $i--) {
		$dat=date("Ym",mktime(0, 0, 0, date("m")-$i, 1, date("Y")));
		$rechng[$dat]=array("summe"=>0,"count"=>0,"curr"=>$curr);
	}
	$rechng["Jahr  "]=array("summe"=>0,"count"=>0,"curr"=>$curr);
	if ($rs) foreach ($rs as $re){
        $m = $re["month"];
		$rechng[$m]["summe"] = $re["sum"];
		$rechng[$m]["count"] = $re["count"];
		$rechng["Jahr  "]["summe"] += $re["sum"];
		$rechng["Jahr  "]["count"]++;
	}
	return $rechng;
}

/****************************************************
* getCurr
* out: curr = String
*****************************************************/
function getCurr() {
global $db;
    $sql="SELECT curr FROM defaults";
	$rsc=$db->getAll($sql);
    if ($rsc[0]['curr']) {
        $curr = explode(":",$rsc[0]['curr']);
        $curr = $curr[0];
    } else {
        $curr = "Eur";
    }
    return $curr;
}

/****************************************************
* getReMonat
* in: fid = int
* jahr = char(4)
* monat = char(2)
* liefern = boolean
* out: rs = array
* Rechnungsdaten für den Monat
*****************************************************/
function getReMonat($fid,$jahr,$monat,$liefer=false){
global $db;
    if ($_SESSION["sales_edit_all"] == "f") $sea = sprintf(" and (employee_id = %d or salesman_id = %d) ", $_SESSION["loginCRM"], $_SESSION["loginCRM"]);
        if ($monat=="00") {
            $next=($jahr+1).'-01-01';
            $monat='01';
        } else {
            $next = ($monat<12)?"$jahr-".($monat+1)."-01":($jahr+1)."-01-01";
        }
        if ($liefer) {
                $sql1="select * from ap where vendor_id=$fid and transdate >= '$jahr-$monat-01' and transdate < '$next' $sea order by transdate desc";
                $sql2="select * from oe where vendor_id=$fid and transdate >= '$jahr-$monat-01' and transdate < '$next' $sea and closed = 'f' order by transdate desc";
        } else {
                $sql1="select * from ar where customer_id=$fid and transdate >= '$jahr-$monat-01' and transdate < '$next' $sea order by transdate desc";
                $sql2="select * from oe where customer_id=$fid and transdate >= '$jahr-$monat-01' and transdate < '$next' $sea order by transdate desc";
        };
	$rs2=$db->getAll($sql2);
	$rs1=$db->getAll($sql1);
	$rs=array_merge($rs1,$rs2);
	usort($rs,"cmp");
	return $rs;
}

/****************************************************
* cmp
* in: $a,$b = datum
* out: 0,1,-1
* Funktion für Usort
*****************************************************/
function cmp ($a, $b) {
    return strcmp($b["transdate"],$a["transdate"]);
    //if ($a["transdate"] == $b["transdate"]) return 0;
    //return ($a["transdate"] < $b["transdate"]) ? -1 : 1;
}

/****************************************************
* getRechParts
* in: $id = int
*     $tab = char(1)
* out: $rs = array
* Reschnungspositionen holen
*****************************************************/
function getRechParts($id,$tab) {
global $db;
	if ($tab=="R" || $tab=="V") {
		$sql="select *,I.sellprice as endprice,I.fxsellprice as orgprice,I.discount,I.description as artikel ";
		$sql.="from invoice I left join parts P on P.id=I.parts_id where trans_id=$id";
		if ($tab=="V") {
			$sql1="select amount as brutto, netamount as netto,transdate, intnotes, notes,quonumber,ordnumber from ap where id=$id";
		} else {
			$sql1="select amount as brutto, netamount as netto,transdate, intnotes, notes,quonumber,ordnumber from ar where id=$id";
		}
	} else {
		$sql="select *,O.sellprice as endprice,O.sellprice as orgprice,O.discount,O.description as artikel ";
		$sql.="from orderitems O left join parts P on P.id=O.parts_id where trans_id=$id";
		$sql1="select amount as brutto, netamount as netto,transdate, intnotes, notes, quotation,quonumber from oe where id=$id";
	}
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		$rs2=$db->getAll($sql1);
		$data[0]=$rs;
		if($rs2) {
			$data[1]=$rs2[0];
		}
		return $data;
	}
}

/****************************************************
* getRechAdr
* in: $id = int
*     $tab = char(1)
* out: $rs = array
* Reschnungadress holen
*****************************************************/
function getRechAdr($id,$tab) {
global $db;
	if ($tab=="R" || $tab=="V") {
		if ($tab=="R") { $tab="ar"; $firma="customer"; } else { $tab="ap"; $firma="vendor"; };
		if ($_SESSION["ERPver"]>="2.2.0.10") {
			$rs=$db->getAll("select shipto_id from $tab where id=$id");
			if ($rs[0]["shipto_id"]>0) {
				$sql="select F.*,S.* from $tab A left join shipto S on S.shipto_id=A.shipto_id, $firma F where ";
				$sql.="A.id=$id and F.id=A.".$firma."_id";
			} else {
				$rs=$db->getAll("select * from shipto where trans_id=$id and module='".strtoupper($tab)."'");
				if ($rs[0]["shipto_id"]>0) {
					$sql="select F.*,S.* from $tab A left join shipto S on S.trans_id=A.id, $firma F where ";
					$sql.="A.id=$id and F.id=A.".$firma."_id and S.module='".strtoupper($tab)."'";
				} else {
					$sql="select F.* from $tab A left join $firma F on F.id=A.".$firma."_id where A.id=$id";
				}
			}
			$rs=$db->getAll($sql);
			if($rs) { return $rs[0]; } else { return false;	};
		} else {
			$sql="select * from $firma F left join shipto S on F.id=S.trans_id left join $tab A on A.".$firma."_id=F.id where A.id=$id";
			$rs=$db->getAll($sql);
			if ($rs[0]["id"]>0) {
				return $rs[0];
			} else {
				$rs=$db->getAll("select * from $firma F left join $tab A on A.".$firma."_id=F.id where A.id=$id");
				if($rs) { return $rs[0]; } else { return false;	};
			}
		}
	} else {
		if ($_SESSION["ERPver"]>="2.2.0.10") {	
			$firma="customer";
			$rs=$db->getAll("select shipto_id from oe where id=$id");
			if ($rs[0]["shipto_id"]>0) {
				$sql="select F.*,S.* from oe O left join shipto S on S.shipto_id=O.shipto_id, $firma F where ";
				$sql.="O.id=$id and C.id=O.".$firma."_id";
			} else {
				$rs=$db->getAll("select * from shipto where trans_id=$id and module='OE'");
				if ($rs[0]["shipto_id"]>0) {
					$sql="select F.*,S.* from oe O left join shipto S on S.trans_id=O.id, $firma F where ";
					$sql.="O.id=$id and F.id=O.".$firma."_id and S.module='OE'";
				} else {
					$sql="select F.* from oe O left join $firma F on F.id=O.".$firma."_id where O.id=$id";
				}
			}
			$rs=$db->getAll($sql);
			if($rs) { return $rs[0]; } else { return false;	};
		} else {
			$rs=$db->getAll("select * from $firma F left join $tab O on O.".$firma."_id=F.id where O.id=$id");
			if($rs) { return $rs[0]; } else { return false;	};
		}
	}
}

/****************************************************
* getUsrNamen
* in: user = string
* out: array
* 
*****************************************************/
function getUsrNamen($user) {
global $db;
	if ($user) foreach ($user as $row) {
		     if (substr($row,0,1)=="G") {$grp.=substr($row,1).",";}
		else if (substr($row,0,1)=="E") {$empl.=substr($row,1).",";}
		else if (substr($row,0,1)=="V") {$ven.=substr($row,1).",";}
		else if (substr($row,0,1)=="C") {$cust.=substr($row,1).",";}
		else if (substr($row,0,1)=="P") {$cont.=substr($row,1).",";};
	}
	if ($grp)  $sql[]="select 'G'||grpid as id,grpname as name from gruppenname where  grpid in (".substr($grp,0,-1).")";
	if ($empl) $sql[]="select 'E'||id as id,name,login from employee where  id in (".substr($empl,0,-1).")";
	if ($ven)  $sql[]="select 'V'||id as id,name from vendor where  id in (".substr($ven,0,-1).")";
	if ($cust) $sql[]="select 'C'||id as id,name from customer where  id in (".substr($cust,0,-1).")";
	if ($cont) $sql[]="select 'P'||cp_id as id,cp_name as name from contacts where cp_id in (".substr($cont,0,-1).")";
	$data=false;
	if ($sql) foreach ($sql as $row) {
		$rs=$db->getAll($row);
		if($rs) {
			if (empty($data)) {$data=$rs;}
			else {$data=array_merge($data,$rs);};
		}
	}
	return $data;
}

/****************************************************
* newTermin
* in: 
* out: int
* neuen Termineintrag generieren
*****************************************************/
function newTermin() {
global $db;
	$newID=uniqid (rand());
	$sql="insert into termine (c_cause) values ('$newID')";
	$rc=$db->query($sql);
	$sql="select * from termine where c_cause='$newID'";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		return $rs[0]["id"];
	}
}

/****************************************************
* saveTermin
* in: data = array
* out: ??? Überprüfen
* einen Termin sichern
*****************************************************/
function saveTermin($data) {
global $db;
	if (!$data["tid"]) {
		$termid=newTermin();
	} else {
		$termid=$data["tid"];
		$sql="delete from terminmember where termin=$termid";
		$rc=$db->query($sql);
		$sql="delete from termdate where termid=$termid";
		$rc=$db->query($sql);
	}
	if (!$termid) {
		return false;
	} else {
		if (!$data["bisdat"]) $data["bisdat"]=$data["vondat"];
		$von=mktime(0,0,0,substr($data["vondat"],3,2),substr($data["vondat"],0,2),substr($data["vondat"],6,4));
		$bis=mktime(0,0,0,substr($data["bisdat"],3,2),substr($data["bisdat"],0,2),substr($data["bisdat"],6,4));
		//Bisdatum nicht kleiner Vondatum
		if ($bis<$von) $bis=$von;
 		//Bisdatum nicht grösser Vondatum, dann biszeit>=vonzeit 
		if ((($bis==$von) || ($data["wdhlg"]<>"0")) && $data["bis"]<$data["von"] )   $data["bis"]=$data["von"];
		$sql="update termine set cause='".$data["grund"]."',kategorie=".$data["kategorie"].",c_cause='".$data["lang"];
		$sql.="',starttag='".date("Y-m-d",$von)."',stoptag='".date("Y-m-d",$bis)."',startzeit='".$data["von"]."',stopzeit='".$data["bis"]."',";
		$sql.="repeat=".$data["wdhlg"].",ft='".$data["ft"]."',uid=".$data["uid"].",privat='".(($data["privat"]==1)?'t':'f')."' ";
 		// echtes Datum eintragen, schadet mal nicht und wird künfig verwendet.
 		$sql.=",start='".date("Y-m-d H:i:00",$von." ".$data["von"])."', stop='".date("Y-m-d H:i:00",$bis." ".$data["bis"])."' ";
        $sql.=",location='".$data["location"]."' ";
		$sql.=" where id=".$termid;
		$rc=$db->query($sql);
		if ($rc) {
			$year=date("Y",$von);
			$ft=feiertage($year);
			$ftk=array_keys($ft);
			$idx=0;
			while ($bis>=$von) {
  			    if (date("Y",$von)<>$year) {
  			        $year=date("Y",$von);
  			        $ft=feiertage($year);
  			        $ftk=array_keys($ft);
  			    }
                $sql="insert into termdate (termid,tag,monat,jahr,kw,idx) values (";
                $sql.="$termid,'".date("d",$von)."','".date("m",$von)."',".date("Y",$von).",".strftime("%V",$von).",".$idx.")";
                if (($data["ft"] && date("w",$von)<>6 && date("w",$von)<>0 && !in_array($von,$ftk)) || !$data["ft"] || $von==$bis)
                    $rc=$db->query($sql);
                switch ($data["wdhlg"]) {
                    case '0' :
                    case '1' : $von+=60*60*24;
                             break;
                    case '2' : $von+=60*60*24*2;
                             break;
                    case '7' : $von+=60*60*24*7;
                             break;
                    case '14' : $von+=60*60*24*14;
                              break;
                    case '30' : $von=mktime(0,0,0,date("m",$von)+1,date("d",$von),date("Y",$von));
                               break;
                    case '365' : $von=mktime(0,0,0,date("m",$von),date("d",$von),date("Y",$von)+1);
                               break;
                    default :  $bis=mktime(0,0,0,12,31,2100);
                }
                $idx++;
            }
            if ($data["user"]) foreach($data["user"] as $teiln) {
                $nr=substr($teiln,1);
                $tab=substr($teiln,0,1);
                $sql="insert into terminmember (termin,member,tabelle) values (";
                $sql.=$termid.",$nr,'$tab')";
                $rc=$db->query($sql);
                //if ($tabelle<>"G") {
                if ($tab<>"G" && $tab<>"E") {
                    $tid=mknewTelCall();
                    $nun=date2db($data["vondat"])." ".$data["von"].":00";
                    $sql="update telcall set cause='".$data["grund"];
                    $sql.="',caller_id=$nr,calldate='$nun',termin_id=$termid,c_long='".$data["lang"];
                    $sql.="',employee='".$_SESSION["loginCRM"]."',kontakt='X',bezug=0 where id=$tid";
                    $rc=$db->query($sql);
                    if(!$rs) {
                        $rs=-1;
                    }
                }
            }
		}
	}
}

/****************************************************
* checkTermin
* in: start=string,stop=string,von=string,bis=string,TID = int
* out: array
* 
*****************************************************/
function checkTermin($start,$stop,$von,$bis,$TID=0) {
global $db;
	$grp=getGrp($_SESSION["loginCRM"],true);
	$start=date2db($start);
	$stop=date2db($stop);
	if ($stop<$start) $stop=$start;
	$start.=$von;
	$stop.=$bis;
	$sql="select distinct id from termine D left join terminmember M on M.termin=D.id  where ";
    //$sql.="(start between '$start' and '$stop' ) or ";
    //$sql.="(stop between '$start' and '$stop') ";
	$sql.="((starttag||startzeit between '$start' and '$stop' ) or ";
	$sql.="(stoptag||stopzeit between '$start' and '$stop'))";
	if ($TID>0) $sql.=" and id<>$TID";
	if ($grp) $sql.=" and (M.member in $grp)";
	//folgendes tut irgendwie nicht
	//$rs=$db->getAll($sql);
	//dann erst einmal so
	$rs=$db->query($sql);
	while ($row = $rs->fetchRow(DB_FETCHMODE_ASSOC)) {
		$ids[]=array("id"=>$row["id"]);
	}
	return $ids;
}

function searchTermin($suche,$von,$bis,$TID=0) {
global $db;
    $grp=getGrp($_SESSION["loginCRM"],true);
    $sql="select distinct id from termine D left join terminmember M on M.termin=D.id  where ";
    $sql.="cause ilike '%$suche%'";
    if ($von) $sql .= " and start >= '".date2db($von)."%'";
    if ($bis) $sql .= " and stop <= '".date2db($bis)."%'";
	if ($TID>0) $sql.=" and member=$TID";
	//if ($grp) $sql.=" and (M.member in $grp)";
	$rs=$db->query($sql);
	while ($row = $rs->fetchRow(DB_FETCHMODE_ASSOC)) {
		$ids[]=array("id"=>$row["id"]);
	}
	return $ids;
}

/****************************************************
* getTerminList
* in: id = int
* out: array
* 
*****************************************************/
function getTerminList($id) {
global $db;
	//$sql="select id,cause,starttag,stoptag,startzeit,stopzeit,kategorie,location,c_cause from termine where id in ($id)";
	$sql="select T.*,K.catname from termine T left join termincat K on K.catid=T.kategorie where T.id in ($id)";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		return $rs;
	}
}

/****************************************************
* getTermin
* in: day,month,year = int, art = char
* out: array
* 
*****************************************************/
function getTermin($day,$month,$year,$art,$cuid=false) {
global $db;
    if ($cuid<=0) {
        $rechte="";
    } else {
        $sql="select distinct(grpid) from grpusr where usrid=$cuid";
        $rs=$db->getAll($sql);
        if ($rs) {
            foreach ($rs as $r) $tmp[]=$r["grpid"];
            $grp = "or (M.member in (".implode(",",$tmp).") and M.tabelle='G'))";
        } else {
            $grp=")";
        }
        if ($cuid) {
            $rechte="and ((M.member = $cuid and M.tabelle ='E') $grp";
        } else {
            $rechte="and ((M.member = ".$_SESSION["loginCRM"]." and M.tabelle ='E') $grp";
        }
    }
	//$grp=getGrp($_SESSION["loginCRM"],true);
	//if ($grp) $rechte.=" M.member in $grp";
	if ($art=="M") {
		$min=mktime(0,0,0,$month,1,$year);
		$max=mktime(0,0,0,$month,date("t",$min),$year);
		$sql="select * from termdate D left join terminmember M on M.termin=D.termid ";
		//$sql.="where jahr=$year and monat='$month' and ($rechte)  order by tag";
		$sql.="where jahr=$year and monat='$month' $rechte  order by tag";
		//$sql.="where jahr=$year and monat='$month' and M.member = $uid  order by tag";
		$rs=$db->getAll($sql);
		if(!$rs) {
			return false;
		} else {
			return $rs;
		}
	} else if ($art=="T") {
		$sql="select * from termine T left join termdate D on T.id=D.termid left join terminmember M on M.termin=D.termid ";
		//$sql.="where jahr=$year and monat='$month' and tag='$day' and ($rechte)  order by starttag, startzeit";
		//$sql.="where jahr=$year and monat='$month' and tag='$day' and M.member = $uid  order by starttag, startzeit";
		$sql.="where jahr=$year and monat='$month' and tag='$day'  $rechte  order by starttag, startzeit";
		$rs=$db->getAll($sql);
		if(!$rs) {
			return false;
		} else {
			return $rs;
		}
	} else if ($art=="W") {
		$stopmonth=date("m",mktime(0,0,0,$month,$day+6,$year));
		$stopday=date("d",mktime(0,0,0,$month,$day+6,$year));
		//$sql="select * from termine T left join termdate D on T.id=D.termid where jahr=$year and ";
		$sql="select * from termine T left join termdate D on T.id=D.termid left join terminmember M on M.termin=D.termid  where jahr=$year and ";
		if ($stopmonth==$month) {
			$sql.="monat='$month' and (tag>='$day' and tag<='$stopday') ";
		} else {
			$sql.="((monat='$month' and tag>='$day') or (monat='$stopmonth' and tag<='$stopday')) ";
		}
		//$sql.="and ($rechte) order by startzeit";
		//$sql.="and M.member = $uid order by startzeit";
		$sql.=" $rechte order by startzeit";
		$rs=$db->getAll($sql);
		if(!$rs) {
			return false;
		} else {
			return $rs;
		}
	}
}

/****************************************************
* getTerminData
* in: tid = int
* out: array
* 
*****************************************************/
function getTerminData($tid) {
global $db;
	$sql="select * from termine T left join termdate D on T.id=D.termid where T.id=$tid";
	$rs=$db->getAll($sql);
		if(!$rs) {
			return false;
		} else {
			return $rs[0];
		}
}

/****************************************************
* getTerminUser
* in: tid = int
* out: array
* 
*****************************************************/
function getTerminUser($tid) {
global $db;
	$sql="select tabelle||member as uid from terminmember where termin=$tid";
	$rs=$db->getAll($sql);
		if(!$rs) {
			return false;
		} else {
			return $rs;
		}
}

/****************************************************
* deleteTermin
* in: id = int
* out: 
* 
*****************************************************/
function deleteTermin($id) {
global $db;
	$sql1="delete from termine where id=$id";
	$rc=$db->query($sql1);
	$sql2="delete from terminmember where termin=$id";
	$rc=$db->query($sql2);
	$sql3="delete from termdate where termid=$id";
	$rc=$db->query($sql3);
}

/****************************************************
* getNextTermin
* in: tid = int
* out: array
* 
*****************************************************/
function getNextTermin($tid) {
global $db;
	$nun=date("Y-m-dH:i");
	$grp=getGrp($tid,true);
	if ($grp) $rechte.=" M.member in $grp";
	//$sql="select * from termine T left join termdate D on D.termid=T.id left join terminmember M on M.termin=T.id ";
	//$sql.="where D.jahr||'-'||D.monat||'-'||D.tag||T.startzeit>='$nun' and $rechte order by jahr,monat,tag,startzeit limit 1";
	$sql="select * from termine T left join termdate D on D.termid=T.id left join terminmember M on M.termin=T.id ";
	$sql.="where D.jahr||'-'||D.monat||'-'||D.tag||T.startzeit>'$nun' and $rechte order by jahr,monat,tag,startzeit limit 1";
	$rs=$db->getAll($sql);
	//echo $sql;
	if ($rs[0]["termid"]) {
		$data["id"]=$rs[0]["termid"];
		$ziel=mktime(substr($rs[0]["startzeit"],0,2),substr($rs[0]["startzeit"],3,2),0,$rs[0]["monat"],$rs[0]["tag"],$rs[0]["jahr"]);
		$nun=time();
		$data["zeit"]=$ziel-$nun;
	} else {
		$data["id"]=-1;
		$data["zeit"]=-1;
	}
	return $data;
}

/****************************************************
* advent
* in: year = int
* out: int
* 
*****************************************************/
function advent($year= -1) {
	if ($year == -1) $year= date('Y');
	$s= mktime(0, 0, 0, 11, 26, $year);
	while (0 != date('w', $s)) $s+= 86400;
	return $s;
}

/****************************************************
* eastern
* in: year = int
* out: int
* 
*****************************************************/
function eastern($year= -1) {
      if ($year == -1) $year= date('Y');
      // the Golden number
      $golden= ($year % 19) + 1;
      // the "Domincal number"
      $dom= ($year + (int)($year / 4) - (int)($year / 100) + (int)($year / 400)) % 7;
      if ($dom < 0) $dom+= 7;
      // the solar and lunar corrections
      $solar= ($year - 1600) / 100 - ($year - 1600) / 400;
      $lunar= ((($year - 1400) / 100) * 8) / 25;
      // uncorrected date of the Paschal full moon
      $pfm= (3 - (11 * $golden) + $solar - $lunar) % 30;
      if ($pfm < 0) $pfm += 30;
      // corrected date of the Paschal full moon
      // days after 21st March
      if (($pfm == 29) || ($pfm == 28 && $golden > 11)) {
        $pfm--;
      }
      $tmp= (4 - $pfm - $dom) % 7;
      if ($tmp < 0) $tmp += 7;
      // Easter as the number of days after 21st March */
      $easter= $pfm + $tmp + 1;
      if ($easter < 11) {
        $m= 3;
        $d= $easter + 21;
      } else {
        $m= 4;
        $d= $easter - 10;
      }
      return mktime(0, 0, 0, $m, $d, $year, -1);
}

/****************************************************
* ostern
* in: intYear = int
* out:  int
* 
*****************************************************/
function ostern($intYear) {
	$a = 0; $b = 0; $c = 0; $d = 0; $e = 0;
	$intDay = 0; $intMonth = 0;
	$a = $intYear % 19;
	$b = $intYear % 4;
	$c = $intYear % 7;
	$d = (19 * $a + 24) % 30;
	$e = (2 * $b + 4 * $c + 6 * $d + 5) % 7;
	$intDay = 22 + $d + $e;
	$intMonth = 3;
	if($intDay > 31) {
		$intDay = $d + $e - 9;
		$intMonth = 4;
	} else if($intDay == 26 && $intMonth == 4)
		$intDay = 19;
	else if((($intDay == 25 && $intMonth == 4) && ($d == 28 && $e == 6)) && $a > 10)
   	$intDay = 18;
	return mktime(0,0,0,$intMonth,$intDay,$intYear);
}

/****************************************************
* feiertage
* in:  jahr = int
* out: array
* 
*****************************************************/
function feiertage($jahr) {
    $holiday= array();
    $CAL_SEC_DAY=86400;
    $easter=eastern($jahr);
    $advent=advent($jahr);
    // Feste Feiertage
    $holiday[mktime(0, 0, 0, 1,   1, $jahr)]= 'G,Neujahr';
    $holiday[mktime(0, 0, 0, 1,   6, $jahr)]= 'R,Heilige 3 K&ouml;nige BW,BY,ST';
    $holiday[mktime(0, 0, 0, 5,   1, $jahr)]= 'G,Tag der Arbeit';
    $holiday[mktime(0, 0, 0, 8,  15, $jahr)]= 'R,Maria Himmelfahrt BY,SL';
    $holiday[mktime(0, 0, 0, 10,  3, $jahr)]= 'G,Tag der deutschen Einheit';
    $holiday[mktime(0, 0, 0, 10, 31, $jahr)]= 'R,Reformationstag BB,MV,SN,ST,TH';
    $holiday[mktime(0, 0, 0, 11,  1, $jahr)]= 'R,Allerheiligen BW,BY,NW,RP,SL';
    $holiday[mktime(0, 0, 0, 12, 24, $jahr)]= 'F,Heiligabend';
    $holiday[mktime(0, 0, 0, 12, 25, $jahr)]= 'G,1. Weihnachtsfeiertag';
    $holiday[mktime(0, 0, 0, 12, 26, $jahr)]= 'G,2. Weihnachtsfeiertag';
    $holiday[mktime(0, 0, 0, 12, 31, $jahr)]= 'F,Sylvester';

    // Bewegliche Feiertage, von Ostern abhängig
    $holiday[$easter - $CAL_SEC_DAY * 48]= 'R,Rosenmontag';
    $holiday[$easter - $CAL_SEC_DAY * 46]= 'R,Aschermittwoch';
    $holiday[$easter - $CAL_SEC_DAY *  2]= 'G,Karfreitag';
    $holiday[$easter]=                     'F,Ostersonntag';
    $holiday[$easter + $CAL_SEC_DAY *  1]= 'G,Ostermontag';
    $holiday[$easter + $CAL_SEC_DAY * 39]= 'G,Himmelfahrt';
    $holiday[$easter + $CAL_SEC_DAY * 49]= 'F,Pfingstsonntag';
    $holiday[$easter + $CAL_SEC_DAY * 50]= 'G,Pfingstmontag';
    $holiday[$easter + $CAL_SEC_DAY * 60]= 'R,Fronleichnam BW,BY,HE,NW,RP,SL';

    // Bewegliche Feiertage, vom ersten Advent abhängig
    $holiday[$advent]=                      'F,1. Advent';
    $holiday[$advent + $CAL_SEC_DAY *  7]=  'F,2. Advent';
    $holiday[$advent + $CAL_SEC_DAY * 14]=  'F,3. Advent';
    $holiday[$advent + $CAL_SEC_DAY * 21]=  'F,4. Advent';
    $holiday[$advent - $CAL_SEC_DAY * 35]=  'F,Volkstrauertag';
    $holiday[$advent - $CAL_SEC_DAY * 32]=  'R,Bu&szlig;- und Bettag SN';
    $holiday[$advent - $CAL_SEC_DAY * 28]=  'F,Totensonntag';
    return $holiday;
}

/****************************************************
* getCustMsg
* in: id = int, all = boolean
* out: string
* 
*****************************************************/
function getCustMsg($id,$all=false) {
global $db;
	if (!$all) { $where="fid=$id and akt='t'"; }
	else {
		if ($id) {$where="fid=$id"; }
		else { return false; }
	}
	$sql="select * from custmsg where $where ";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		if ($all==1) {
			return $rs;
		} else if ($all>1) {
			return $rs[0];
		} else {
			if ($rs[0]) {
				switch ($rs[0]["prio"]) {
					case 1 : $atre="<font color='red'><blink>"; $atra="</blink></font>";break;
					case 2 : $atre="<blink>"; $atra="</blink>"; break;
					case 3 : $atre=""; $atra=""; break;
					default : $atre=""; $atra="";
				}
				$msg=$atre.$rs[0]["msg"].$atra;
			}
		}
		return $msg;
	}
}

/****************************************************
* saveCustMsg
* in:  data = array
* out: 
* 
*****************************************************/
function saveCustMsg($data) {
global $db;
	if (!$data["cp_cv_id"]) return false;
	$sql="delete from custmsg where fid=".$data["cp_cv_id"];
	$rc=$db->query($sql);
	if ($rc) for($i=1; $i<=3; $i++) {
		if ($data["message$i"]) { 
			$sql="insert into custmsg (msg,prio,fid,uid,akt) values (";
			$sql.="'".$data["message$i"]."',$i,".$data["cp_cv_id"].",".$_SESSION["loginCRM"].",".(($data["prio"]==$i)?"'t'":"'f'").")";
			$rc=$db->query($sql);
		}
	}
}

/****************************************************
* getOneLable
* in: format = int
* out: array
* 
*****************************************************/
function getOneLable($format) {
	global $db;
	$lab=false;
	$sql="select * from labels where id=".$format;
	$rs=$db->getAll($sql);
	if ($rs) {
		$sql="select * from labeltxt where lid=".$rs[0]["id"];
		$rs2=$db->getAll($sql);
		$lab=$rs[0];
		$lab["Text"]=$rs2;
	}
	return $lab;
}

/****************************************************
* getLableNames
* in: 
* out: array
* 
*****************************************************/
function getLableNames() {
	global $db;
	$sql="select id,name from labels";
	$rs=$db->getAll($sql);
	return $rs;
}

/****************************************************
* mknewLable
* in: id = int
* out: int
* 
*****************************************************/
function mknewLable($id=0) {
	global $db;
	$newID=uniqid (rand());
	$sql="insert into labels (name) values ('$newID')";
	$rc=$db->query($sql);
	if ($rc) {
		$sql="select id from labels where name = '$newID'";
		$rs=$db->getAll($sql);
		if ($rs) {
			$id=$rs[0]["id"];
		} else {
			$id=false;
		}
	} else {
		$id=false;
	}
	return $id;
}

/****************************************************
* insLable
* in: data = array
* out: int
* 
*****************************************************/
function insLable($data) {
	$data["id"]=mknewLable();
	$data["name"]=$data["custname"];
	$data["cust"]="C";
	return updLable($data);
}

/****************************************************
* updLable
* in: data = array
* out: int
* 
*****************************************************/
function updLable($data) {
	global $db;
	$data["fontsize"]="10";
	$felder=array("name","cust","papersize","metric","marginleft","margintop","nx","ny","spacex","spacey","width","height","fontsize");
	$tmp="update labels set ";
	foreach ($felder as $feld) {
		$tmp.=$feld."='".$data[$feld]."',";
	}
	$sql=substr($tmp,0,-1)." where id=".$data["id"];
	if ($data["cust"]=="C") {
		$rc=$db->query($sql);
		$i=0;
		$db->query("delete from labeltxt where lid=".$data["id"]);
		if($data["Text"]) foreach($data["Text"] as $row) {
			$sql=sprintf("insert into labeltxt (lid,font,zeile) values (%d,%d,'%s')",$data["id"],$data["Schrift"][$i],$row);
			$db->query($sql);
			$i++;
		}
	} else {
		return false;
	}
	return $data["id"];
}

/****************************************************
* getWPath
* in: id = int
* out: string
* 
*****************************************************/
function getWPath($id) {
global $db;
	$sql="select * from wissencategorie where id = $id";
	$rs=$db->getAll($sql);
	if ($rs) {
		$pfad=$rs[0]["id"];
		if ($rs[0]["hauptgruppe"]==0) return $pfad;
	}
	while ($rs and $rs[0]["hauptgruppe"]>0) {
		$sql="select * from wissencategorie where id = ".$rs[0]["hauptgruppe"];
		$rs=$db->getAll($sql);
		if ($rs) $pfad.=",".$rs[0]["id"];
	}
	return $pfad;
}

/****************************************************
* getWCategorie
* in: kdhelp = boolean
* out: array
* 
*****************************************************/
function getWCategorie($kdhelp=false) {
global $db;
	if ($kdhelp) { 
		$sql="select * from wissencategorie where kdhelp is true order by name";
	} else {
		$sql="select * from wissencategorie order by hauptgruppe,name";
	}
	$rs=$db->getAll($sql);
	$data=array();
	if ($rs) { 
		if ($kdhelp) if (count($rs)>0) { return $rs;} else { return false; };
		foreach ($rs as $row) {
			$data[$row["hauptgruppe"]][]=array("name"=>$row["name"],"id"=>$row["id"],"kdhelp"=>$row["kdhelp"]);
		}
		return $data;
	} else {
		return false;
	}
}

/****************************************************
* insWCategorie
* in: data = array
* out: int
* 
*****************************************************/
function insWCategorie($data) {
global $db;
	$tmp = explode(",",$data["m"]);
	if (!$data["cid"]) {
		$newID=uniqid (rand());
		$sql="insert into wissencategorie (name,kdhelp) values ('$newID','".(($data["kdhelp"]==1)?'true':'false')."')";
		$rc=$db->query($sql);
		$sql="select * from wissencategorie where name='$newID'";
		$rs=$db->getAll($sql);
		if(!$rs) {
			return false;
		} else {
			$id=$rs[0]["id"];
		}
	} else {
		$id=$data["cid"];
	}
	if ($tmp[0]=='') {
        	$tmp[0]=0;
        } else if (count($tmp)==1) {
        	$tmp[0]=0;
	}
    $name=html_entity_decode($data["catname"]);
	$sql="update wissencategorie set name='".$name."',hauptgruppe='".$data["hg"];
	$sql.="',kdhelp=".(($data["kdhelp"]==1)?'true':'false')." where id = ".$id;
	$rc=$db->query($sql);
	return ($rc)?$rs[0]["id"]:false;
}

/****************************************************
* getOneWCategorie
* in: id = int
* out: array
* 
*****************************************************/
function getOneWCategorie($id) {
global $db;
	$sql="select * from  wissencategorie where id = $id";
	$rs=$db->getAll($sql);
	return $rs[0];
}

/****************************************************
* getWContent
* in: id = int
* out: array
* 
*****************************************************/
function getWContent($id) {
global $db;
	$sql="select O.*,A.name,E.login from wissencontent O left join wissencategorie A on A.id=O.categorie ";
	$sql.="left join employee E on O.employee=E.id where categorie = $id order by initdate desc limit 1";
	$rs=$db->getAll($sql);
	if ($rs) {
		return $rs[0];
	} else {
		return false;
	}
}

/****************************************************
* insWContent
* in: data = array
* out: int
* 
*****************************************************/
function insWContent($data) {
global $db;
	$tmp = explode(",",$data["m"]);
	$sql="insert into wissencontent (initdate,content,employee,version,categorie) values ";
	$sql.="(now(),'".trim($data["content"])."',".$_SESSION["loginCRM"].",".($data["version"]+1).",".$tmp[0].")";
	$rc=$db->query($sql);
	return $rc;
}


/****************************************************
* getWHistory
* in: id = int
* out: array
* 
*****************************************************/
function getWHistory($id) {
global $db;
	$sql="select W.*,E.login from  wissencontent W left join employee E on W.employee=E.id where categorie = $id order by initdate";
	$rs=$db->getAll($sql);
	return $rs;
}
/**
 * TODO: short description.
 * 
 * @param mixed $wort 
 * @param mixed $kat  
 * 
 * @return TODO
 */

/****************************************************
* suchWDB
* in: wort = string, kat = int
* out: array
* 
*****************************************************/
function suchWDB($wort,$kat) {
global $db;
    $sql = "SELECT WK.* from wissencontent WC left join wissencategorie WK on WC.categorie=WK.id where content ilike '%".trim($wort)."%'";
	$rs=$db->getAll($sql);
	return $rs;
}
/****************************************************
* diff
* in: text1,text2 = string
* out: array
*
* Geschrieben von TBT-Moderator php-resource.de am 28-11-2002
*****************************************************/
function diff($text1,$text2) {
	$text1=preg_replace("/(<[a-z]+[a-z]*[^>]*?>)/e","ereg_replace(' ','°°','\\1')",$text1);
	$text2=preg_replace("/(<[a-z]+[a-z]*[^>]*?>)/e","ereg_replace(' ','°°','\\1')",$text2);
	$array1 = explode(" ", str_replace(array("   ","    ","  ", "\r", "\n"), array(" "," "," ", "", ""), $text1));
	$array2 = explode(" ", str_replace(array("   ","    ","  ", "\r", "\n"), array(" "," "," ", "", ""), $text2));
	$max1 = count($array1);
	$max2 = count($array2);
	$start1 = $start2 = 0;
	$jump1 = $jump2 = 0;
	while($start1 < $max1 && $start2 < $max2){
		$pos11 = $pos12 = $start1;
		$pos21 = $pos22 = $start2;
		$diff2 = 0; 
		// schaukel 1. Array hoch
		while($pos11 < $max1 && $array1[$pos11] != $array2[$pos21]){
			++$pos11;
		}
		// Ende des 1 Arrays erreicht ?
		if($pos11 == $max1){
			$start2++;
			continue;
		} 
		// Gegenschaukel wenn übersprunge Wörter
		if(($diff1 = $pos11 - $pos21) > 1){
			while($pos22 < $max2 && $array1[$pos12] != $array2[$pos22]){
				++$pos22;
			}
			$diff2 = $pos22 - $pos12 + $jump2;
		} 
		// Ende des 2 Arrays erreicht ?
		if($pos22 == $max2){
			$start1++;
			continue;
		}
		$diff1 += $jump1; 
		// Auswertung der Schaukel
		if($diff1 >= $diff2 && $diff2){
			unset($array1[$pos12], $array2[$pos22]);
			$start1 = $pos12 + 1;
			$start2 = $pos22 + 1;
			$jump2 = $diff2;
		}else{
			unset($array1[$pos11], $array2[$pos21]);
			$start1 = $pos11 + 1;
			$start2 = $pos21 + 1;
			$jump1 = $diff1;
		}
	}
	$safe1 = explode(" ", str_replace(array("   ","    ","  ", "\r", "\n"), array(" "," "," ", "", ""), $text1));
	reset($array1);
	while(list($key1,) = each($array1)){
	    if (preg_match("/<\/?([ou]l|li|img|input)/i",$safe1[$key1])) {
		    $safe1[$key1] = "[_" . $safe1[$key1] . "_]";
	    } else {
		    $safe1[$key1] = "<span class='diff1'>" . $safe1[$key1] . "</span>";
	    }
	}
	$safe2 = explode(" ", str_replace(array("   ","    ","  ", "\r", "\n"), array(" "," "," ", "", ""), $text2));
	reset($array2);
	while(list($key2,) = each($array2)){
	    $safe2[$key2] = "<span class='diff2'>" . $safe2[$key2] . "</span>";
	}
	$text1=implode(" ", $safe1);
	$text2=implode(" ", $safe2);
	$text1=preg_replace("/(<[a-z]+[a-z]*[^>]*?>)/e","ereg_replace('°°',' ','\\1')",$text1);
	$text2=preg_replace("/(<[a-z]+[a-z]*[^>]*?>)/e","ereg_replace('°°',' ','\\1')",$text2);
	return array($text1,$text2);
}

/****************************************************
* getOpportunityStatus
* in: 
* out: array
* 
*****************************************************/
function getOpportunityStatus() {
global $db;
	$sql="select * from opport_status order by sort";
	$rs=$db->getAll($sql);
	return $rs;
}

/****************************************************
* getOneOpportunity
* in: id = int
* out: array
* 
*****************************************************/
function getOneOpportunity($id) {
global $db;
	//$sql="select O.*,C.name as firma from  opportunity O left join customer C on O.fid=C.id where O.id = $id";
	$sql="select * from  opportunity where id = $id";
	$rs=$db->getAll($sql);
	if ($rs) {
		if ($rs[0]["tab"]=="V") {
			$sql="select name from vendor where id = ".$rs[0]["fid"];
		} else {
			$sql="select name from customer where id = ".$rs[0]["fid"];
		}
		$rs2=$db->getAll($sql);
		$rs[0]["firma"]=$rs2[0]["name"];
	}
	return $rs[0];
}

/****************************************************
* getOpportunity
* in: fid = int
* out: array
* 
*****************************************************/
function getOpportunity($fid) {
global $db;
	$sql="select O.*,C.name as firmaC,V.name as firmaV from  opportunity O left join customer C on O.fid=C.id ";
	$sql.="left join vendor V on O.fid=V.id where fid = $fid";
	$rs=$db->getAll($sql);
	return $rs;
}

/****************************************************
* suchOpportunity
* in: data = array
* out: boolean
* 
*****************************************************/
function suchOpportunity($data) {
global $db;
	if ($data) while (list($key,$val)=each($data)) {
		if (in_array($key,array("title","notiz","zieldatum","next")) and $val) { $val=str_replace("*","%",$val); $where.="and $key like '$val%' "; }
		else if (in_array($key,array("status","chance","salesman")) and $val) { $where.="and $key = $val "; };
	}
	/*if ($data["fid"]  and $data["name"]) { 
		$where.="and (fid in (select id from customer where lower(name) like '%".strtolower($data["name"])."%') or fid = ".$data["fid"].")";
	} else */ //Nonsens!
	if ($data["fid"]) { 
		$where.="and fid = ".$data["fid"]." and tab='".$data["Quelle"]."'"; 
	} else if ($data["name"]) {
		$where.="and (fid in (select id from customer where lower(name) like '%".strtolower($data["name"])."%')";
		$where.="or fid in (select id from vendor where lower(name) like '%".strtolower($data["name"])."%') )";
	}
	$sql="select O.*,C.name as firmaC,V.name as firmaV from  opportunity O left join customer C on O.fid=C.id ";
	$sql.="left join vendor V on O.fid=V.id where ".substr($where,3)." order by chance desc,betrag desc";
	$rs=$db->getAll($sql);
	return $rs;
}

/****************************************************
* saveOpportunity
* in: data = array
* out: int
* 
*****************************************************/
function saveOpportunity($data) {
global $db;
	if ($data["fid"] and $data["title"] and $data["betrag"] and $data["status"] and $data["chance"] and $data["zieldatum"]) {
		$data["betrag"]=str_replace(",",".",$data["betrag"]);
		if (!$data["id"]) {
			$newID=uniqid (rand());
			$sql="insert into opportunity (title) values ('$newID')";
			$rc=$db->query($sql);
			$sql="select * from opportunity where title='$newID'";
			$rs=$db->getAll($sql);
			if(!$rs) {
				return false;
			} else {
				$data["id"]=$rs[0]["id"];
			}
		}
		$datum=date2db($data["zieldatum"]);
		$tmp="update opportunity set fid=%d,tab='%s',title='%s',zieldatum='%s', betrag=%s, chance=%d, status=%d, salesman=%d, ";
		$tmp.="next='%s', notiz='%s', mtime='%s',memployee=%d where id=%d";
		$sql=sprintf($tmp,$data["fid"],$data["Quelle"],$data["title"],$datum,$data["betrag"],$data["chance"],$data["status"],$data["salesman"],
				$data["next"],$data["notiz"],date("Y-m-d H:i:s"),$_SESSION["loginCRM"],$data["id"]);
		$rc=$db->query($sql);
		if ($rc) {
			return $data["id"];
		} else {
			return false;
		}
	} else { 
		return false;
	}
}

/****************************************************
* saveMailVorlage
* in: data = array
* out: int
* 
*****************************************************/
function saveMailVorlage($data) {
global $db;
	if ($data["MID"]) {
		$sql="update mailvorlage set cause='%s', c_long='%s' where id = %d";
		$rc=$db->query(sprintf($sql,$data["Subject"],$data["BodyText"],$data["MID"]));
	} else {
		$sql="insert into mailvorlage (cause,c_long,employee) values ('%s','%s',%d)";
		$rc=$db->query(sprintf($sql,$data["Subject"],$data["BodyText"],$_SESSION["loginCRM"]));
		$sql="select id from mailvorlage where cause='".$data["Subject"];
		$sql.="' and c_long='".$_SESSION["loginCRM"]."' and employee=".$_SESSION["loginCRM"];
		$rs=$db->getAll($sql);
		if ($rs[0]["id"]>0) return $rs[0]["id"];
	}
	return $rc;
}

/****************************************************
* getMailVorlage
* in: 
* out: array
* 
*****************************************************/
function getMailVorlage() {
global $db;
	$sql="select * from mailvorlage order by cause";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		return $rs;
	}
}

/****************************************************
* getOneMailVorlage
* in: MID = int
* out: array
* 
*****************************************************/
function getOneMailVorlage($MID) {
global $db;
	$sql="select * from mailvorlage where id = $MID";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		return $rs[0];
	}
}

/****************************************************
* deleteMailVorlage
* in: id = int
* out: int
* 
*****************************************************/
function deleteMailVorlage($id) {
global $db;
	$sql="delete from mailvorlage where id = $id";
	$rc=$db->query($sql);
	return $rc;
}
?>
