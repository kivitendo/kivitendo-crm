<?
// $Id: crmLib.php,v 1.7 2006/01/18 16:01:51 hli Exp $

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
* getShipStamm
* in: id = int
* out: rs = array(Felder der db)
* hole die abweichenden Lieferdaten
*****************************************************/
function getShipStamm($id) {
global $db;
	$sql="select * from shipto where trans_id=$id";
	$rs2=$db->getAll($sql);
	if(!$rs2) {
		return false;
	} else {
		return $rs2[0];
	}
}

/****************************************************
* getAllTelCall
* in: id = int, firma = boolean
* out: rs = array(Felder der db)
* hole alle Anrufe einer Person oder einer Firma
*****************************************************/
function getAllTelCall($id,$firma,$start=0) {
global $db;
	if (!$start) $start=0;
	if ($firma) {	// dann hole alle Kontakte der Firma
		$sql="select id,caller_id,kontakt,cause,calldate,cp_name from ";
		$sql.="telcall left join contacts on caller_id=cp_id where bezug=0 ";
		$sql.="and (caller_id in (select cp_id from contacts where cp_cv_id=$id) or caller_id=$id)";
 	} else {  // hole nur die einer Person
		$where="and caller_id=$id and caller_id=cp_id";
		$sql="select id,caller_id,kontakt,cause,calldate,cp_name from ";
		$sql.="telcall left join contacts on caller_id=cp_id where bezug=0 and caller_id=$id";
	}
	$rs=$db->getAll($sql." order by calldate desc offset $start limit 23");
	if(!$rs) {
		$rs=false;
	}
	return $rs;
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
	$rs=$db->getAll($sql." order by calldate desc offset $start limit 23");
	if(!$rs) {
		$rs=false;
	}
	return $rs;
}

function mkPager(&$items,&$pager,&$start,&$next,&$prev) {
	if ($items) {
		$pager=$start;
		if (count($items)==23) {
			$next=$start+23;
			$prev=($start>23)?($start-23):0;
		} else {
			$next=$start;
			$prev=($start>23)?($start-23):0;
		}
	} else if ($start>0) {
		$pager=($start>23)?($start-23):0;
		$item[]=array(id => "",calldate => "", caller_id => $employee, cause => "Keine weiteren Eintr&auml;ge" );
		$next=$start;
		$prev=($pager>23)?($pager-23):0;
	} else {
		$pager=0;
		$next=0;
		$prev=0;
	}
}
function mvTelcall($TID,$Anzeige,$CID) {
global $db;
	$call=getCall($Anzeige);
	$caller="";
	if ($call["CID"]!=$CID) {
		if ($call["bezug"]==0) {
			$sql="update telcall set caller_id=$CID where id=$Anzeige";
		} else {
			$sql="update telcall set bezug=0, caller_id=$CID where id=$Anzeige";
		}
	} else if ($TID<>$Anzeige) {
		if ($call["bezug"]==0) {
			$sql="update telcall set bezug=$TID where id=$Anzeige or Bezug=$Anzeige";
		} else {
			$sql="update telcall set bezug=$TID where id=$Anzeige";
		}
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
* insFormDoc
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
	$dateiID=saveDokument($datei,$rs[0]["vorlage"],$datum,$data["CID"],$data["CRMUSER"]);
	$did=documenttotc($id,$dateiID);
	$c_cause=addslashes($rs[0]["beschreibung"]);
	$c_cause=nl2br($rs[0]["beschreibung"]);
	$sql="update telcall set cause='".$rs[0]["vorlage"]."',c_long='$c_cause',caller_id='".$data["CID"]."',calldate='$datum',kontakt='D',dokument=$did,bezug='0',employee=".$data["CRMUSER"]." where id=$id";
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
	$did="null";
	$datum=$data['Datum']." ".$data['Zeit'].":00";  // Postgres timestamp
	$anz=($datei["Datei"]["name"][0]<>"")?count($datei["Datei"]["name"]):0;
	for ($o=0; $o<$anz; $o++) {
		if ($datei["Datei"]["name"][$o]<>"") {
			$dat["Datei"]["name"]=$datei["Datei"]["name"][$o];
			$dat["Datei"]["tmp_name"]=$datei["Datei"]["tmp_name"][$o];
			$dat["Datei"]["type"]=$datei["Datei"]["type"][$o];
			$dat["Datei"]["size"]=$datei["Datei"]["size"][$o];
			$text=($data["DCaption"])?$data["DCaption"]:$data["cause"];
			$dateiID=saveDokument($dat,$text,$datum,$data["CID"],$data["CRMUSER"]);
			$did=documenttotc($id,$dateiID);
			$did=1;
		};
	}
	$c_cause=addslashes($data["c_cause"]);
	$c_cause=nl2br($c_cause);
	$sql="update telcall set cause='".$data["cause"]."',c_long='$c_cause',caller_id='".$data["CID"]."',calldate='$datum',kontakt='".$data["Kontakt"]."',dokument=$did,bezug='".$data["Bezug"]."',employee='".$data["CRMUSER"]."' where id=$id";
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
function saveDokument($Datei,$Beschreibung,$datum,$CID,$CRMUSER) {
global $db;
	$Name=$Datei["Datei"]["name"];
	$Size=$Datei["Datei"]["size"];
	if ($CID>0) {									// gehört einem Kontakt
		$dir=$_SESSION["mansel"]."/".$CID;
	} else {  										// gehört einem User
		$dir=$_SESSION["mansel"]."/".$CRMUSER;
	};
	$dest="./dokumente/".$dir."/".$Name;
	$ok=chkdir($dir);
	if (copy($Datei["Datei"]["tmp_name"],$dest)) {
		//unlink($Datei["Datei"]['tmp_name']);	Macht doch PHP selber
		$id=mknewDocument();
		$sql="update documents set filename='$Name',descript='$Beschreibung',datum='$datum',size=$Size,employee=$CRMUSER,kunde=$CID where id=$id";
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
* delDokument
* in: id = int
* out:
* ein Dokument löschen
*****************************************************/
function delDokument($id) {
global $db;
	$data=getDokument($id); // gibt es das dokument
	if ($data) {
		$sql="delete from documents where id=$id";   // aus db löschen
		$rs=$db->query($sql);
		if($rs) { // auf platte löschen
			$pre=($data["kunde"]>0)?$data["kunde"]:$data["employee"];
			// fehlt noch was
		}
	}
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
* getDokument
* in: id = int
* out: rs = array(Felder der db)
* ein Dokument aus db holen
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
		$daten["Bezug"]=$rs[0]["bezug"];
		$daten["employee"]=$rs[0]["employee"];
		if ($rs[0]["dokument"]==1) {
			$daten["Files"]=getAllDokument($id);
		} else if ($rs[0]["dokument"]>1) {
			$dat=getDokument($rs[0]["dokument"]);
			if ($dat) {
				$daten["Kunde"]=($dat["kunde"]>0)?$dat["kunde"]:$dat["employee"];
				$daten["Datei"]=$dat["filename"];
				$daten["DCaption"]=$dat["descript"];
			} else {
				$daten["Datei"]="";
				$daten["DCaption"]="";
				$daten["Kunde"]="";
			}
		} else {
			$daten["Datei"]="";
			$daten["DCaption"]="";
			$daten["Kunde"]="";
		}
	}
	return $daten;
}

/****************************************************
* getWvl
* in: crmuser = int
* out: rs = array(Felder der db)
* alle wiedervorlagen eines Users auslesen
*****************************************************/
function getWvl($crmuser) {
global $db;
	$sql="select * from wiedervorlage where (employee=$crmuser or employee is null) and status > 0 order by  finishdate asc ,initdate asc";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$rs=false;
	} else {
		if (count($rs)==0) $rs=array(array("id"=>0,"initdate"=>date("Y-m-d H:i:00"),"cause"=>"Keine Einträge"));
	}
	return $rs;
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
		$data["LangTxt"]=stripslashes(ereg_replace("<br />","\n",$rs[0]["descript"]));
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
	$anz=($datei["Datei"]["name"][0]<>"")?count($datei["Datei"]["name"]):0;
	if ($anz>0) {  // ein neues Dokument
		if ($data["DateiID"]) delDokument($data["DateiID"]); // ein altes löschen
		for ($o=0; $o<$anz; $o++) {
			$dat["Datei"]["name"]=$datei["Datei"]["name"][$o];
			$dat["Datei"]["tmp_name"]=$datei["Datei"]["tmp_name"][$o];
			$dat["Datei"]["type"]=$datei["Datei"]["type"][$o];
			$dat["Datei"]["size"]=$datei["Datei"]["size"][$o];
			if (!$data["DCaption"]) $data["DCaption"]=$data["Cause"];
			$dateiID=saveDokument($dat,$data["DCaption"],$nun,0,$data["CRMUSER"]);
		}
		if ($anz>1) $dateiID=1;
	} else {
		$dateiID=$data["DateiID"];
	}
	if (empty($dateiID)) $dateiID=0;
	$finish=($data["Finish"]<>"")?", finishdate='".date2db($data["Finish"])." 0:0:00'":"";
	$descript=addslashes($data["LangTxt"]);
	$descript=nl2br($descript);
	$sql="update wiedervorlage set employee=".$data["CRMUSER"].", cause='".$data["Cause"]."', descript='$descript', document=$dateiID, status=".$data["status"];
	$sql.=",kontakt='".$data["kontakt"]."',changedate='$nun'".$finish;
	$sql.=" where id=".$data["WVLID"];
	$rs=$db->query($sql);
	if(!$rs) {
		$rs=false;
	} else {$rs=$data["WVLID"];};
	if ($data["cp_cv_id"]) {  // es wurd eine Zuweisung an einen Kunden gemacht
		//$id=moveWvl($data["WVLID"],$data["cp_cv_id"]);
		$id=kontaktWvl($data["WVLID"],$data["cp_cv_id"]);
		if ($id) {$rs=$id;} else {$rs=false;}
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
	/*
	if(empty($data["cp_cv_id"]) && $data["status"]<1) {
		$kontaktID=$data["CRMUSER"];
		//$data["cp_cv_id"]=$data["CRMUSER"];
	} else {  */
		$kontaktID=substr($data["cp_cv_id"],1);
		$kontaktTAB=substr($data["cp_cv_id"],0,1);
	//}
	if(!empty($kontaktID)) {
		$data["status"]=0;
		$nun=date("Y-m-d H:i:00");
		$data["kontakt"]="M";
		$did=false;
		if (!empty($data["dateien"])) {
			$srv=getUsrMailData($data["CRMUSER"]);
			$mbox = imap_open ("{".$srv["msrv"].":143/imap/notls}", $srv["postf"],$srv["kennw"]);
			//$mbox = imap_open ("{".$srv["msrv"].":143}", $srv["postf"],$srv["kennw"]);
			$data["DateiID"]=true;
			foreach($data["dateien"] as $mail){
				//trenne Anhang und speichere in tmp
				$file=split(",",$mail);
				$body=imap_fetchbody($mbox,$data["Mail"],$file[0]);
				$head=imap_header($mbox,$data["Mail"],$file[0]);
				if ($file[2]==3 ||
					eregi("GIF",$file[3]) 	||
 					eregi("JPEG",$file[3]) 	||
					eregi("PNG",$file[3])){
        			$body   =imap_base64($body);
				};
				$Datei["Datei"]["name"]=$file[1];
   				$Datei["Datei"]["tmp_name"]="./tmp/".$data["CRMUSER"]."_".$file[0];
				$f=fopen($Datei["Datei"]["tmp_name"],"w");
				fwrite($f,$body);
				fclose($f);
				$Datei["Datei"]["size"]=filesize($Datei["Datei"]["tmp_name"]);
				$did[]=saveDokument($Datei,$data["DCaption"],$nun,$kontaktID,$data["CRMUSER"]);
			}
		} else {
			$data["DateiID"]=false;
		}
		// bis hier ok
		$id=insWvl($data);
		if ($id) {
			moveMail($data["Mail"],$data["CID"]);
			if ($did) foreach ($did as $d) {
				documenttotc($id,$d);
			}
			$rs=true;
		} else { $rs=false; };
	} else { 
		$rs=false; 
	};
	return $rs;
}

/****************************************************
* moveWvl
* in: id,fid = int
* out: rs = id
* eine wiedervorlage nach telcall verschieben
*****************************************************/
function moveWvl($id,$fid) {
global $db;
	$sql="select * from wiedervorlage where id=$id";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$ok=-1;
	} else {
		$nun=date("Y-m-d H:i:00");
		// insCall($data,$datei)
		$tid=mknewTelCall();
		$sql="update telcall set cause='".$rs[0]["cause"]."',caller_id=$fid,calldate='$nun',c_long='".$rs[0]["descript"]."',employee=".$rs[0]["employee"].",kontakt='".$rs[0]["kontakt"]."',bezug=0,dokument=".$rs[0]["document"]." where id=$tid";
		$rc=$db->query($sql);
		if(!$rc) {
			$ok=-1;
		} else {
			$ok=$tid;
			$sql="update wiedervorlage set status=0, finishdate='$nun' where id=$id";
			$rc=$db->query($sql);
			if(!$rc) {
				$ok=-1;
			} else 	if ($rs[0]["document"] && $rs[0]["kontakt"]<>"M") {
				$sql="select * from documents where id=".$rs[0]["document"];
				$rsD=$db->getAll($sql);
				$von="dokumente/".$_SESSION["mansel"]."/".$rsD[0]["employee"]."/".$rsD[0]["filename"];
				$dir=$_SESSION["mansel"]."/".$fid;
				$ok=chkdir($dir);
				$nach="dokumente/".$dir."/".$rsD[0]["filename"];
				copy("$von $nach");
				unlink($von);
				$sql="update documents set kunde=".$fid." where id=".$rsD[0]["id"];
				$rc=$db->query($sql);
				if(!$rc) { $ok=-1; }
			}
		}
	}
	return $ok;
}

/****************************************************
* kontaktWvl
* in: id,fid = int
* out: rs = id
* eine wiedervorlage mit telcall verbinden
*****************************************************/
function kontaktWvl($id,$fid) {
global $db;
	$sql="select * from wiedervorlage where id=$id";
	$rs=$db->getAll($sql);
	$nun=date("Y-m-d H:i:00");
	$tab=substr($fid,0,1);
	$fid=substr($fid,1);
	if(!$rs) {
		$ok=-1;
	} else if ($rs[0]["kontaktid"]>0 and $fid<>$rs[0]["kontaktid"]){
		// bisherigen Kontakteintrag ungültig markieren
		$sql="update telcall set cause='gecancelt' where id=".$rs[0]["tellid"];
		$rc=$db->query($sql);
		// neuen Eintraag generieren
		$tid=mknewTelCall();
		$sql="update telcall set cause='".$rs[0]["cause"]."',caller_id=$fid,calldate='$nun',c_long='".$rs[0]["descript"]."',employee=".$rs[0]["employee"].",kontakt='".$rs[0]["kontakt"]."',bezug=0,dokument=".$rs[0]["document"]." where id=$tid";
		$rc=$db->query($sql);
		if(!$rc) { $ok=-1; } else { $ok=$tid; };
		// wvl updaten
		$sql="update wiedervorlage set kontaktid=$fid,kontakttab='$tab',tellid=$tid where id=$id";
		$rc=$db->query($sql);
	//} else if ($rs[0]["kontaktid"]>0) {
	} else if ($rs[0]["kontaktid"]>0 and $fid==$rs[0]["kontaktid"]) {
		// ok
	} else {
		$tid=mknewTelCall();
		$sql="update telcall set cause='".$rs[0]["cause"]."',caller_id=$fid,calldate='$nun',c_long='".$rs[0]["descript"]."',employee=".$rs[0]["employee"].",kontakt='".$rs[0]["kontakt"]."',bezug=0,dokument=".$rs[0]["document"]." where id=$tid";
		$rc=$db->query($sql);
		if(!$rc) {
			$ok=-1;
		} else {
			$ok=$tid;
			$sql="update wiedervorlage set kontaktid=$fid,kontakttab='$tab',tellid=$tid where id=$id";
			$rc=$db->query($sql);
		}
	}
	
	if ($rs[0]["status"]<1) {
		if ($rs[0]["document"] && $rs[0]["kontakt"]<>"M") {
				$sql="select * from documents where id=".$rs[0]["document"];
				$rsD=$db->getAll($sql);
				$von="dokumente/".$_SESSION["mansel"]."/".$rsD[0]["employee"]."/".$rsD[0]["filename"];
				$dir=$_SESSION["mansel"]."/".$fid;
				$ok=chkdir($dir);
				$nach="dokumente/".$dir."/".$rsD[0]["filename"];
				copy("$von $nach");
				$sql="update documents set kunde=".$fid." where id=".$rsD[0]["id"];
				$rc=$db->query($sql);
				if(!$rc) { $ok=-1; }
		}
	}
	return $ok;
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
	if ($srv["msrv"] && $srv["postf"]) {  /// gar kein Mailserver/Postfach eingetragen
		$mbox =@imap_open ("{".$srv["msrv"].":143/imap/notls}", $srv["postf"],$srv["kennw"]);
		// evtl noch pop3 einbauen
		if ($mbox) {
			$anzahl=imap_num_msg($mbox);
			if ($anzahl>0) {
				$overview = imap_fetch_overview ($mbox, "1:$anzahl", 0);
				$m=false;
				if (is_array ($overview )) {
    				reset ($overview);
    				while (list ($key, $val) = each ($overview)) {
						if (!$val->deleted) {
							$datum=substr($val->date,4,-9);
							$gelesen=($val->seen)?"-":"+";
							$m[]=array("Nr"=>$val->msgno,
									"Datum"=>$datum,
									"Betreff"=>htmlspecialchars(decode_string($val->subject)),
									"Abs"=>htmlspecialchars(decode_string($val->from)),
									"Gelesen"=>$gelesen);
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

/****************************************************
* getOneMail
* in: usr = int, nr = int
* out: data = array
* eine Mail holen
*****************************************************/
function getOneMail($usr,$nr) {
	$srv=getUsrMailData($usr);
	$mbox = imap_open ("{".$srv["msrv"].":143/imap/notls}", $srv["postf"],$srv["kennw"]);
	// hier dann auch pop einbauen
	$head=@imap_header( $mbox,$nr );
	if (!$head) return;
	//$fullheader	=imap_fetchheader($mbox,$nr);
	$mybody=decode_string($head->fromaddress)."\n".$head->date."\n";
	//$body.=imap_body($mbox,$nr);//,1.1,FTUID);
	$htmlbody="Empty Message Body";
	$structure=imap_fetchstructure($mbox,$nr);
	if ( eregi("MIXED",$structure->subtype) )  {
		$x=imap_fetchbody($mbox,$nr,1);
		$body   =imap_fetchbody($mbox,$nr,1.1,FTUID);
		$fullheader	=@imap_fetchheader($mbox,$nr,1.1);
		if ( eregi("Content-Type: text/html",$fullheader) ) {
			$htmlbody=$body;
		} else {
			$htmlbody=imap_qprint($body);
		}
	}
	if ( eregi("Empty Message",$htmlbody) ) {
		$fullheader	=imap_fetchheader($mbox,$nr);
		$body	=imap_fetchbody($mbox,$nr,1);
		if ( eregi("Content-Type: text/html",$fullheader) ) {
			$htmlbody=$body;
		} else {
			$htmlbody=imap_qprint($body);
		}
	}
	$body=$mybody.$htmlbody."\n".$x;
	$c=count($structure->parts);
	$files=array();
	for ($i=$start; $i<$c; $i++) {
		$part0=$structure->parts[$i];
    	if ( ! empty($part0->type) or $part0->type===0 ) {
			$part=$i+1;
			$parameters=$part0->parameters;
			$attach_type=$part0->subtype;
			$mytype=$part0->type;
			$encoding=$part0->encoding;
			$text_encoding=$mime_encoding[$encoding];
			if (empty($text_encoding)) {
				$text_encoding="unknown";
			}
			$description=$part0->description;
			if (eregi("RFC822",$attach_type)) {
				$tmp=imap_fetchbody($mbox,$nr,$part);
				$t=split("\n",$tmp);
				$hd=""; $bd=""; $s=true;
				foreach($t as $z){
					if ($s) {
						if(ord($z[0])<>13){
							$hd.=$z;
						} else {
							$s=false;
						}
					} else {
						$bd.=$z;
					}
				}
				if (strlen($bd)>1)	{
					$bodyX.="\n".imap_qprint($bd);
				} else {
					$bodyX.="\n".$hd;
				};
  			} else {
				$enc=$encoding;
				$typ=$attach_type;
				$att=$parameters[0]->attribute;
				$val=$parameters[0]->value;
				$val=eregi_replace(" ","_",$val);
				$size=sprintf("%0.2f",$part0->bytes / 1024);
				$files[]=array("size"=>$size,"name"=>$val,"nummer"=>$part,"type"=>$typ,"encode"=>$enc);
			}
		}
	}
	$body=htmlspecialchars(decode_string(imap_qprint($body)));
	$body.=$bodyX;
	$cause=htmlspecialchars(decode_string($head->Subject));
	$data["id"]=$nr;
	$data["Initdate"]=substr($head->date,4,-5);
	$data["Cause"]=$cause;
	$data["LangTxt"]=$body;
	$data["Datei"]=$anhang;
	$data["status"]="2";
	$data["InitCrm"]=$head[""];
	$data["CRMUSER"]=$head[""];
	$data["DCaption"]=($files)?$cause:"";
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
	$mbox = imap_open ("{".$srv["msrv"].":143/imap/notls}", $srv["postf"],$srv["kennw"]);
	imap_delete($mbox,$mail);
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
	$sql1="select name,'E' as src,id,email from employee where upper(email) like '$Pre".strtoupper($suche)."%' and email <> ''";
	$rs1=$db->getAll($sql1);
	$sql2="select name,'C' as src,id,email from customer where upper(email) like '$Pre".strtoupper($suche)."%' and email <> ''";
	$rs2=$db->getAll($sql2);
	$sql3="select cp_name as name,'K' as src,cp_id as id,cp_email as email from contacts where upper(cp_email) like '$Pre".strtoupper($suche)."%' and cp_email <> ''";
	$rs3=$db->getAll($sql3);
	$sql4="select shiptoname as name,'S' as src,trans_id as id,shiptoemail as email from shipto where upper(shiptoemail) like '$Pre".strtoupper($suche)."%' and shiptoemail <> ''";
	$rs4=$db->getAll($sql4);
	$sql5="select name,'V' as src,id,email from vendor where upper(email) like '$Pre".strtoupper($suche)."%' and email <> ''";
	$rs5=$db->getAll($sql5);
	$rs=array_merge($rs1,$rs2,$rs3,$rs4,$rs5);
	return $rs;
}

function chkMailAdr ($mailadr) {
	if (substr(",",$mailadr)) {
		$tmp=split(",",$mailadr);
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
function getReJahr($fid,$liefer=false) {
global $db;
	$lastYear=date("Y-m-d",mktime(0, 0, 0, date("m"), date("d"), date("Y")-1));
	if ($liefer) {
		$sql="select * from ap where vendor_id=$fid and transdate >= '$lastYear' order by transdate desc";
		$rs2=array();
	} else {
		$sql="select * from oe where customer_id=$fid and transdate >= '$lastYear' and closed = 'f' and quotation = 'f' order by transdate desc";
		$rs2=$db->getAll($sql);
		$sql="select * from ar where customer_id=$fid and transdate >= '$lastYear' order by transdate desc";
	};
	$rs1=$db->getAll($sql);
	$rs=array_merge($rs1,$rs2);
	$rechng=array();
	for ($i=11; $i>=0; $i--) {
		$dat=date("Ym",mktime(0, 0, 0, date("m")-$i, 1 , date("Y")));
		$rechng[$dat]=array("summe"=>0,"count"=>0,"curr"=>"Eur");
	}
	$rechng["Jahr  "]=array("summe"=>0,"count"=>0,"curr"=>"Eur");
	if ($rs) foreach ($rs as $re){
		$m=substr($re["transdate"],0,4).substr($re["transdate"],5,2);
		$rechng[$m]["summe"]+=$re["netamount"];
		$rechng[$m]["count"]++;
		$rechng["Jahr  "]["summe"]+=$re["netamount"];
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
function getAngebJahr($fid) {
global $db;
	$lastYear=date("Y-m-d",mktime(0, 0, 0, date("m"), date("d"), date("Y")-1));
	$sql="select * from oe where customer_id=$fid and transdate >= '$lastYear' and quotation = 't' order by transdate desc";
	$rs=$db->getAll($sql);
	$rechng=array();
	for ($i=11; $i>=0; $i--) {
		$dat=date("Ym",mktime(0, 0, 0, date("m")-$i, 1, date("Y")));
		$rechng[$dat]=array("summe"=>0,"count"=>0,"curr"=>"Eur");
	}
	$rechng["Jahr  "]=array("summe"=>0,"count"=>0,"curr"=>"Eur");
	if ($rs) foreach ($rs as $re){
		$m=substr($re["transdate"],0,4).substr($re["transdate"],5,2);
		$rechng[$m]["summe"]+=$re["netamount"];
		$rechng[$m]["count"]++;
		$rechng["Jahr  "]["summe"]+=$re["netamount"];
		$rechng["Jahr  "]["count"]++;
	}
	return $rechng;
}

/****************************************************
* getReMonat
* in: fid = int
* monat = char(2)
* liefern = boolean
* out: rs = array
* Rechnungsdaten für den Monat
*****************************************************/
function getReMonat($fid,$monat,$liefer=false){
global $db;
	if ($liefer) {
		$sql1="select * from ap where vendor_id=$fid and transdate like '$monat%' order by transdate desc";
		$rs2=array();
	} else {
		$sql1="select * from ar where customer_id=$fid and transdate like '$monat%' order by transdate desc";
		$sql2="select * from oe where customer_id=$fid and transdate like '$monat%' and closed = 'f' order by transdate desc";
		$rs2=$db->getAll($sql2);
	};
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
		$sql="select *,I.sellprice as endprice,I.fxsellprice as orgprice,I.discount,I.description as artikel from invoice I left join parts P on P.id=I.parts_id where trans_id=$id";
		if ($tab=="V") {
			$sql1="select amount as brutto, netamount as netto,transdate, intnotes, notes from ap where id=$id";
		} else {
			$sql1="select amount as brutto, netamount as netto,transdate, intnotes, notes from ar where id=$id";
		}
	} else {
		$sql="select *,O.sellprice as endprice,O.sellprice as orgprice,O.discount,O.description as artikel from orderitems O left join parts P on P.id=O.parts_id where trans_id=$id";
		$sql1="select amount as brutto, netamount as netto,transdate, intnotes, notes, quotation from oe where id=$id";
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
	if ($tab=="R") {
		$sql="select C.*,S.* from ar A left join shipto S on S.trans_id=A.id, customer C where A.id=$id and C.id=A.customer_id";
	} elseif ($tab=="V") {
		$sql="select V.*,S.* from ap A left join shipto S on S.trans_id=A.id, vendor V where A.id=$id and V.id=A.vendor_id";
	} else {
		$sql="select C.*,S.* from oe O left join shipto S on S.trans_id=O.id, customer C where O.id=$id and C.id=O.customer_id";
	}
	$rs=$db->getAll($sql);
	if($rs) {
		return $rs[0];
	} else {
		return false;
	}
}

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
	if ($empl) $sql[]="select 'E'||id as id,name from employee where  id in (".substr($empl,0,-1).")";
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
		if ($bis<$von) $bis=$von;
		if ((($bis==$von) || ($data["wdhlg"]<>"0")) && $data["bis"]<$data["von"] )   $data["bis"]=$data["von"];
		$sql="update termine set cause='".$data["grund"]."',c_cause='".$data["lang"];
		$sql.="',starttag='".date("Y-m-d",$von)."',stoptag='".date("Y-m-d",$bis)."',startzeit='".$data["von"]."',stopzeit='".$data["bis"]."',";
		$sql.="repeat=".$data["wdhlg"].",ft='".$data["ft"]."',uid=".$data["uid"];
		$sql.=" where id=".$termid;
		$rc=$db->query($sql);
		if ($rc) {
		$year=date("Y",$von);
		$ft=feiertage($year);
		$ftk=array_keys($ft);
		while ($bis>=$von) {
			if (date("Y",$von)<>$year) {
				$year=date("Y",$von);
				$ft=feiertage($year);
				$ftk=array_keys($ft);
			}
			$sql="insert into termdate (termid,tag,monat,jahr,kw) values (";
			$sql.="$termid,'".date("d",$von)."','".date("m",$von)."',".date("Y",$von).",".strftime("%V",$von).")";
			if (($data["ft"] && date("w",$von)<>6 && date("w",$von)<>0 && !in_array($von,$ftk)) || !$data["ft"])
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
				$sql="update telcall set cause='".$data["grund"]."',caller_id=$nr,calldate='$nun',c_long='".$data["lang"]."',employee='".$_SESSION["loginCRM"]."',kontakt='X',bezug=0 where id=$tid";
				$rc=$db->query($sql);
				if(!$rs) {
					$rs=-1;
				}
			}
		}
		}
	}
}
function checkTermin($start,$stop,$von,$bis) {
global $db;
	list($startT,$startM,$startJ)=split("\.",$start);
	list($stopT,$stopM,$stopJ)=split("\.",$stop);
	$grp=getGrp($_SESSION["loginCRM"],true);
	if ($grp) $rechte.=" M.member in $grp";
	$start=date2db($start).$von; $stop=date2db($stop).$bis;
	$sql="select distinct id from termine D left join terminmember M on M.termin=D.id  where ";
	$sql.="(starttag||startzeit between '$start' and '$stop' ) or ";
	$sql.="(stoptag||stopzeit between '$start' and '$stop')";
	$sql.=" and ($rechte)";
	$rs=$db->getAll($sql);
	return $rs;
}

function getTerminList($id) {
global $db;
	$sql="select id,cause,starttag,stoptag,startzeit,stopzeit from termine where id in ($id)";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		return $rs;
	}
}

function getTermin($day,$month,$year,$art) {
global $db;
	$grp=getGrp($_SESSION["loginCRM"],true);
	if ($grp) $rechte.=" M.member in $grp";
	if ($art=="M") {
		$min=mktime(0,0,0,$month,1,$year);
		$max=mktime(0,0,0,$month,date("t",$min),$year);
		$sql="select * from termdate D left join terminmember M on M.termin=D.termid  where jahr=$year and monat='$month' and ($rechte)  order by tag";
		$rs=$db->getAll($sql);
		if(!$rs) {
			return false;
		} else {
			return $rs;
		}
	} else if ($art=="T") {
		$sql="select * from termine T left join termdate D on T.id=D.termid left join terminmember M on M.termin=D.termid  where jahr=$year and monat='$month' and tag='$day' and ($rechte)  order by starttag, startzeit";
		$rs=$db->getAll($sql);
		if(!$rs) {
			return false;
		} else {
			return $rs;
		}
	} else if ($art=="W") {
		$stopmonth=date("m",mktime(0,0,0,$month,$day+6,$year));
		$stopday=date("d",mktime(0,0,0,$month,$day+6,$year));
		$sql="select * from termine T left join termdate D on T.id=D.termid where jahr=$year and ";
		$sql="select * from termine T left join termdate D on T.id=D.termid left join terminmember M on M.termin=D.termid  where jahr=$year and ";
		if ($stopmonth==$month) {
			$sql.="monat='$month' and (tag>='$day' and tag<='$stopday') ";
		} else {
			$sql.="((monat='$month' and tag>='$day') or (monat='$stopmonth' and tag<='$stopday')) ";
		}
		$sql.="and ($rechte) order by startzeit";
		$rs=$db->getAll($sql);
		if(!$rs) {
			return false;
		} else {
			return $rs;
		}
	}
}
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

function deleteTermin($id) {
global $db;
	$sql1="delete from termine where id=$id";
	$rc=$db->query($sql1);
	$sql2="delete from terminmember where termin=$id";
	$rc=$db->query($sql2);
	$sql3="delete from termdate where termid=$id";
	$rc=$db->query($sql3);
}
function getNextTermin($tid) {
global $db;
	$nun=date("Y-m-dH:i");
	$grp=getGrp($tid,true);
	if ($grp) $rechte.=" M.member in $grp";
	//$sql="select * from termine T left join termdate D on D.termid=T.id left join terminmember M on M.termin=T.id where D.jahr||'-'||D.monat||'-'||D.tag||T.startzeit>='$nun' and $rechte order by jahr,monat,tag,startzeit limit 1";
	$sql="select * from termine T left join termdate D on D.termid=T.id left join terminmember M on M.termin=T.id where D.jahr||'-'||D.monat||'-'||D.tag||T.startzeit>'$nun' and $rechte order by jahr,monat,tag,startzeit limit 1";
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
function advent($year= -1) {
	if ($year == -1) $year= date('Y');
	$s= mktime(0, 0, 0, 11, 26, $year);
	while (0 != date('w', $s)) $s+= 86400;
	return $s;
}
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
function feiertage($jahr) {
	$holiday= array();
	$CAL_SEC_DAY=86400;
	$easter=eastern($jahr);
	$advent=advent($jahr);
    // Feste Feiertage
    $holiday[mktime(0, 0, 0, 1,   1, $jahr)]= 'G,Neujahr';
    $holiday[mktime(0, 0, 0, 1,   6, $jahr)]= 'R,Heilige 3 K&ouml;nige';
    $holiday[mktime(0, 0, 0, 5,   1, $jahr)]= 'G,Tag der Arbeit';
    $holiday[mktime(0, 0, 0, 8,  15, $jahr)]= 'F,Maria Himmelfahrt';
    $holiday[mktime(0, 0, 0, 10,  3, $jahr)]= 'G,Tag der deutschen Einheit';
    $holiday[mktime(0, 0, 0, 10, 31, $jahr)]= 'R,Reformationstag';
    $holiday[mktime(0, 0, 0, 11,  1, $jahr)]= 'R,Allerheiligen';
    $holiday[mktime(0, 0, 0, 12, 24, $jahr)]= 'F,Heiligabend';
    $holiday[mktime(0, 0, 0, 12, 25, $jahr)]= 'G,1. Weihnachtsfeiertag';
    $holiday[mktime(0, 0, 0, 12, 26, $jahr)]= 'G,2. Weihnachtsfeiertag';
    $holiday[mktime(0, 0, 0, 12, 31, $jahr)]= 'F,Sylvester';

    // Bewegliche Feiertage, von Ostern abhängig
    $holiday[$easter - $CAL_SEC_DAY * 48]= 'F,Rosenmontag';
    $holiday[$easter - $CAL_SEC_DAY * 46]= 'F,Aschermittwoch';
    $holiday[$easter - $CAL_SEC_DAY *  2]= 'G,Karfreitag';
    $holiday[$easter]=                     'F,Ostersonntag';
    $holiday[$easter + $CAL_SEC_DAY *  1]= 'G,Ostermontag';
    $holiday[$easter + $CAL_SEC_DAY * 39]= 'G,Himmelfahrt';
    $holiday[$easter + $CAL_SEC_DAY * 49]= 'F,Pfingstsonntag';
    $holiday[$easter + $CAL_SEC_DAY * 50]= 'G,Pfingstmontag';
    $holiday[$easter + $CAL_SEC_DAY * 60]= 'R,Fronleichnam';

    // Bewegliche Feiertage, vom ersten Advent abhängig
    $holiday[$advent]=                      'F,1. Advent';
    $holiday[$advent + $CAL_SEC_DAY *  7]=  'F,2. Advent';
    $holiday[$advent + $CAL_SEC_DAY * 14]=  'F,3. Advent';
    $holiday[$advent + $CAL_SEC_DAY * 21]=  'F,4. Advent';
    $holiday[$advent - $CAL_SEC_DAY * 35]=  'F,Volkstrauertag';
    $holiday[$advent - $CAL_SEC_DAY * 32]=  'R,Bu&szlig;- und Bettag';
    $holiday[$advent - $CAL_SEC_DAY * 28]=  'F,Totensonntag';
    return $holiday;
}

function getCustMsg($id,$all=false) {
global $db;
	if ($all>1) { $where="id=$all"; }
	else {
		if ($id) {$where="fid=$id"; }
		else { return false; }
	}
	$sql="select * from custmsg where $where order by prio,id";
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

function saveCustMsg($data) {
global $db;
	if (!$data["cp_cv_id"] or !$data["message"]) return false;
	if (!$data["ID"]) {
		$newID=uniqid (rand());
		$sql="insert into custmsg (msg) values ('$newID')";
		$rc=$db->query($sql);
		$sql="select * from custmsg where msg='$newID'";
		$rs=$db->getAll($sql);
		if(!$rs) {
			return false;
		} else {
			$data["ID"]=$rs[0]["id"];
		}
	}
	$sql="update custmsg set msg='".$data["message"]."', prio=".$data["prio"].",fid=".$data["cp_cv_id"].",uid=".$_SESSION["loginCRM"]." where id=".$data["ID"];
	$rc=$db->query($sql);
}
function delCustMsg($id) {
global $db;
	$rc=false;
	if (id) {
		$sql="delete from custmsg where id=$id";
		$rc=$db->query($sql);
	}
	return $rc;
}

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
function getLableNames() {
		global $db;
		$sql="select id,name from labels";
		$rs=$db->getAll($sql);
		return $rs;
	}
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
	function insLable($data) {
		$data["id"]=mknewLable();
		$data["name"]=$data["custname"];
		$data["cust"]="C";
		return updLable($data);
	}
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
			foreach($data["Text"] as $row) {
				$sql=sprintf("insert into labeltxt (lid,font,zeile) values (%d,%d,'%s')",$data["id"],$data["Schrift"][$i],$row);
				$db->query($sql);
				$i++;
			}
		} else {
			return false;
		}
	}
?>
