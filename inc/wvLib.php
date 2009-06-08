<?

/****************************************************
* getVorlagen
* in: 
* out: Array
* hole alle Vertragsvorlagen
*****************************************************/
function getWVorlagen() {
	$dh = opendir("./vorlage");
	$vorlagen=array();
	while (false !== ($filename = readdir($dh))) {
		if (!is_dir($filename) and ereg("wv.+\.pdf",$filename) ) { $vorlagen[]=substr($filename,2); };
	}
	return $vorlagen;
}

function getAllMaschinen($suchw) {
	global $db;
	$sql="select M.*,P.partnumber,P.description from maschine M left join parts P on P.id=M.parts_id ";
	$sql.="left join contmasch C on C.mid=M.id where C.cid is null and (P.partnumber like '%$suchw%' or P.description like '%$suchw%')";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$rs=false;
	};
	return $rs;
}

function getArtikel($sw) {
	global $db;
	$sw=strtr($sw,"*?","%_");
	$sql="select * from parts where partnumber like '$sw'";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$rs=false;
	};
	return $rs;
}

function getMaschSer($ser,$pid) {
	global $db;
	$sql="select * from maschine left join history on id=mid where serialnumber=$ser and parts_id=$pid and art='neu'";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$rs=false;
	};
	$sql="select * from parts where id=$pid";
	$rs2=$db->getAll($sql);
	$data=$rs[0]; 
	$data["partnumber"]=$rs2[0]["partnumber"];
	$data["notes"]=$rs2[0]["notes"];
	$data["description"]=$rs2[0]["description"];	
	return $data;
}

function getNumber($nr) {
	global $db;
	//$sql="select * from invoice where parts_id=$nr";
	$sql=" select * from invoice where parts_id=$nr and serialnumber is not null and serialnumber<>'' and serialnumber  not in (select serialnumber from maschine where parts_id=$nr)";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$rs=false;
	};
	return $rs;
}
function getBekannt($nr) {
	global $db;
	$sql="select * from maschine where  parts_id=$nr";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$rs=false;
	};
	return $rs;
}
function saveNewMaschine($data) {
	global $db;
	if (!$data["parts_id"]) return false;
	$serialnumber=($data["snumber"])?$data["snumber"]:$data["serialnumber"];
	if ($serialnumber) {
		$sql="select count(*) from maschine where parts_id='".$data["parts_id"]."' and serialnumber = '".$serialnumber."'";
		$rs=$db->getAll($sql);
		if ($rs[0]["count"]>0) return false;
		$newID=uniqid (rand());
		$db->begin();
		$sql="insert into maschine (parts_id,serialnumber,standort,inspdatum) values (%d,'%s','$newID',%s)";
		$inspd=($data["inspdatum"]=="00.00.0000" || empty($data["inspdatum"]))?"null":"'".date2db($data["inspdatum"])."'";
		$rc=$db->query(sprintf($sql,$data["parts_id"],$serialnumber,$inspd));
		if ($rc) {
			$sql="select * from maschine where standort='$newID'";
			$rs=$db->getAll($sql);
			$mid=$rs[0]["id"];
			$sql="update maschine set standort='Lager' where id=$mid";
			$rc=$db->query($sql);
			if ($rc) {
				$rc=insHistory($mid,'neu',$data["beschreibung"]);
			};
			if ($rc) { $db->commit(); }
			else { $db->rollback(); };
		}
		return $rc;
	} else  { 
		return false;
	}
}

function updateMaschine($data) {
	global $db;
	$inspd=($data["inspdatum"]=="00.00.0000" || empty($data["inspdatum"]))?"null":"'".date2db($data["inspdatum"])."'";
	$sql="update maschine set inspdatum = $inspd where id=".$data["mid"];
	$rc=$db->query($sql);
	if ($rc) {
		$sql="update history set beschreibung='".$data["beschreibung"]."' where art='neu' and mid=".$data["mid"];
		$rc=$db->query($sql);
		return $rc;
	} else  { 
		return false;
	}
}
function getCounter($mid) {
	global $db;
	$sql="select * from maschine where id = $mid";
	$rs=$db->getAll($sql);
	return $rs[0]["counter"];
}

function saveNewVertrag($data) {
	global $db;
	$newID=uniqid (rand());
	$db->begin();
	$sql="insert into contract (contractnumber,template,bemerkung,customer_id,betrag,anfangdatum,endedatum) values ('%s','%s','%s','%d','%s','%s','%s')";
	$start=($data["anfangdatum"]<>"00.00.0000" && !empty($data["anfangdatum"]))?date2db($data["anfangdatum"]):date("Y-m-d");	
	$stop=($data["endedatum"]<>"00.00.0000" && !empty($data["endedatum"]))?date2db($data["endedatum"]):$start;
	$rc=$db->query(sprintf($sql,$newID,$data["vorlage"],$data["bemerkung"],$data["cp_cv_id"],$data["betrag"],$start,$stop));
	if ($rc) {
		$sql="select * from contract where contractnumber='$newID'";
		$rs=$db->getAll($sql);
		$cid=$rs[0]["cid"];
		$cn=newContract();
		$sql="update contract set contractnumber='$cn' where cid=$cid";
		$rc=$db->query($sql);
		if ($rc) {
			$i=0;
			foreach ($data["maschinen"] as $row) {
				$standort=($row[2]<>"")?$row[2]:"Kunde";
				$rc1=newCM($row[0],$cn);
				$rc2=insHistory($row[0],"contadd","$cn");
				$rc3=$db->query("update maschine set standort = '$standort' where id=".$row[0]);
				if (!$rc1 || !$rc2 || !$rc3) {
					$db->rollback();
					return false;
				}
				$i++;
			}
		} else {
			$db->rollback();
			return false;
		}
		$db->commit();
		return $cid;
	}
}
function updateVertrag($data) {
	global $db;
	$db->begin();
	$start=($data["anfangdatum"]<>"00.00.0000" && !empty($data["anfangdatum"]))?date2db($data["anfangdatum"]):date("Y-m-d");	
	$stop=($data["endedatum"]<>"00.00.0000" && !empty($data["endedatum"]))?date2db($data["endedatum"]):$start;
	if ($data["new"]) {
		$sql="update contract set template='%s',bemerkung='%s',betrag='%s',anfangdatum='%s',endedatum='%s' where cid=%d";
		$rc=$db->query(sprintf($sql,$data["vorlage"],$data["bemerkung"],$data["betrag"],$start,$stop,$data["vid"]));
	} else {
		$sql="update contract set bemerkung='%s',betrag='%s',anfangdatum='%s',endedatum='%s' where cid=%d";
		$rc=$db->query(sprintf($sql,$data["bemerkung"],$data["betrag"],$start,$stop,$data["vid"]));
	}
	if ($rc) {
		//$sql="select mid from contmasch where cid=".$data["contractnumber"];
		$sql="select mid from contmasch where cid=".$data["vid"];
		$rs=$db->getAll($sql);
		//$sql="delete from contmasch where cid=".$data["contractnumber"];
		$sql="delete from contmasch where cid=".$data["vid"];
		$rc=$db->query($sql);
		$in=array();
		if ($rc) {
			$i=count($data["maschinen"]);
			for ($j=0; $j<$i; $j++) {
				$row=$data["maschinen"][$j];
				if (!$row[0]) continue;
				if ($row[3]) continue;
				$in[]=$row[0];
				$standort=($row[2]<>"")?$row[2]:"Kunde";
				//$rc1=newCM($row[0],$data["contractnumber"]);
				$rc1=newCM($row[0],$data["vid"]);
				if (!in_array(array(mid => $row[0]),$rs)) { $rc2=insHistory($row[0],"contadd",$data["contractnumber"]); } else { $rc2=true; };
				$rc3=$db->query("update maschine set standort = '$standort' where id=".$row[0]);
				if (!$rc1 || !$rc2 || !$rc3) {
					$db->rollback();
					return false;
				}
			}
			foreach($rs as $line) {
				if (!in_array($line["mid"],$in)) {
					$rc2=insHistory($line["mid"],"contsub",$data["contractnumber"]);
					$rc3=$db->query("update maschine set standort = 'unbekannt' where id=".$line["mid"]);
				}
			}
		} else {
			$db->rollback();
			return false;
		}
		$db->commit();
		return $data["vid"];
	}
}
function insHistory($mid,$art,$beschreibung) {
	global $db;
	$sql="insert into history (mid,datum,art,beschreibung) values ($mid,'".date("Y-m-d")."','$art','$beschreibung')";
	$rc=$db->query($sql);
	return $rc;
}
function newCM($mid,$cid) {
	global $db;
	$sql="insert into contmasch (mid,cid) values ($mid,$cid)";
	$rc=$db->query($sql);
	return $rc;
}
function newContract() {
	global $db;
	$sql="select * from defaults";
	$rs=$db->getAll($sql);
	$cid=++$rs[0]["contnumber"];
	$sql="update defaults set contnumber='$cid'";
	$rc=$db->query($sql);
	return $cid;	
}
function suchVertrag($vid) {
	global $db;
	$sql="select V.*,C.name from contract V left join customer C on V.customer_id=C.id where V.contractnumber like '%$vid%'";
	$rs=$db->getAll($sql);
	return $rs;	
}
function getVertrag($vid) {
	global $db;
	$sql="select C.*,K.name,K.customernumber from contract C left join customer K on K.id=C.customer_id where C.cid=$vid";
	$rs=$db->getAll($sql);
	return $rs[0];		
}
function getVertragMaschinen($cnr) {
	global $db;
	$sql="select * from contmasch C left join maschine M on M.id=C.mid left join parts P on P.id=M.parts_id  where C.cid=$cnr";
	$rs=$db->getAll($sql);
	return $rs;			
}
function getSernumber($sn,$pn=false) {
	global $db;
	$sql="select M.*,K.name,K.street,K.zipcode,K.city,K.phone,P.partnumber,P.description,P.notes,V.*,C.mid,P.id as parts_id ";
	$sql.="from maschine M left join parts P on P.id=M.parts_id ";
	$sql.="left join contmasch C on C.mid=M.id ";
	$sql.="left join contract V on V.contractnumber=cast (C.cid as text) ";	
	//$sql.="left join contract V on V.cid=C.cid ";	
	$sql.="left join customer K on K.id=V.customer_id ";
	$sql.="where M.serialnumber like '%$sn%' ";
	$sql.=($pn)?"and parts_id=$pn":"";
	$rs=$db->getAll($sql);	
	return $rs;
}
function getArtnumber($sn) {
	global $db;
	$sql="select M.*,K.name,K.street,K.zipcode,K.city,K.phone,P.partnumber,P.description,P.notes,V.*,C.mid ";
	$sql.="from maschine M left join parts P on P.id=M.parts_id ";
	$sql.="left join contmasch C on C.mid=M.id ";
	//$sql.="left join contract V on V.contractnumber=C.cid ";	
	$sql.="left join contract V on V.cid=C.cid ";	
	$sql.="left join customer K on K.id=V.customer_id ";
	$sql.="where P.partnumber like '%$sn%'";
	$rs=$db->getAll($sql);	
	return $rs;
}
function getHistory($nr) {
	global $db;
	$sql="select * from history where mid=$nr order by datum desc, oid  desc";
	$rs=$db->getAll($sql);	
	return $rs;	
}
function saveNewStandort($ort,$mid) {
	global $db;
	$sql="update maschine set standort='$ort' where id=$mid";
	$rc=$db->query($sql);
}
function getCustContract($fid) {
	global $db;
	$sql="select contractnumber,cid from contract where customer_id=$fid";
	$rs=$db->getAll($sql);	
	return $rs;		
}
function getAllMaschine($mid) {
	global $db;
	$sql="select X.mid,V.*,M.*,P.description from contmasch X left join contract V on V.contractnumber=cast (X.cid as text) ";
	//$sql="select X.mid,V.*,M.*,P.description from contmasch X left join contract V on V.cid=X.cid ";
	$sql.="left join maschine M on M.id=X.mid left join parts P on P.id=M.parts_id where X.mid=$mid";
	$rs=$db->getAll($sql);	
	return $rs[0];		
}
function saveRAuftrag($data) {
	global $db;
	if ($data["aid"]) { $rc=updRAuftrag($data); }
	else { $rc=insRAuftrag($data); }
	return $rc;
}
function insRAuftrag($data) {
	global $db;
	$db->begin();
	$rs=$db->getAll("select sonumber from defaults");	
	$aid=$rs[0]["sonumber"]+1;
	$rc=$db->query("update defaults set sonumber=".$aid);
	$sql="insert into repauftrag (aid,mid,kdnr,cause,schaden,anlagedatum,bearbdate,employee,bearbeiter,status,counter) ";
	$sql.="values ($aid,%d,'%s','%s','%s','%s','%s',%d,%d,%d,%d)";
	$rc=$db->query(sprintf($sql,$data["mid"],$data["kdnr"],$data["cause"],$data["schaden"],
			date("Y-m-d"),date2db($data["datum"]),$_SESSION["loginCRM"],$data["bid"],$data["status"],$data["counter"]));
	if ($rc) { 
		insHistory($data["mid"],"RepAuftr","$aid|".$data["cause"]);
		if ($data["counter"]) {
			$rc=updateCounter($data["counter"],$data["mid"]);
		}
		$db->commit(); $rc=$data; $rc["aid"]=$aid; }
	else { $db->rollback(); $rc=false; };
	return $rc;
}
function updRAuftrag($data) {
	global $db;
	$sql="update repauftrag set cause='%s', schaden='%s', reparatur='%s', status=%d, ";
	$sql.="employee=%d, bearbeiter=%d, bearbdate='%s', counter=%d where aid=%d";
	$rc=$db->query(sprintf($sql,$data["cause"],$data["schaden"],$data["behebung"],$data["status"],
			$_SESSION["loginCRM"],$data["bid"],date2db($data["datum"]),$data["counter"],$data["aid"]));
	if ($rc) { 
		$rc=updateCounter($data["counter"],$data["mid"]);	
		return getRAuftrag($data["aid"]); }
	else { return false; }
}

function updateCounter($cnt,$mid) {
	global $db;
	$sql="update maschine set counter=$cnt where id = $mid";
	return $db->query($sql);
}

function updateIdat($idat,$mid) {
	global $db;
	$sql="update maschine set inspdatum='".date2db($idat)."' where id = $mid";
	return $db->query($sql);
}

function getRAuftrag($nr) {
	global $db;
	$sql="select * from repauftrag where aid=$nr";
	$rs=$db->getAll($sql);
	if ($rs) { return $rs[0]; }
	else { return false;};
}
function getAllMat($aid,$mid) {
	global $db;
	$sql="select *,P.description from maschmat M left join parts P on M.parts_id=P.id where mid=$mid and aid=$aid";
	$rs=$db->getAll($sql);
	if ($rs) { return $rs; }
	else { return false;};
}
function safeMaschMat($mid,$aid,$material) {
	global $db;
	$db->begin();
	if ($material) {
		$old=$db->getAll("select * from maschmat where aid=$aid");
		// Bestandsanpassung, zurück ins Lager
		if ($old) foreach ($old as $row) {
			$sql="update parts set onhand=(onhand+".$row["menge"].") where id=".$row["parts_id"];
			$rc=$db->query($sql);
		}
		$rc=$db->query("delete from maschmat where aid=$aid");
		foreach ($material as $zeile) {
			$tmp=split(";",$zeile);
			$sql="insert into maschmat (mid,aid,menge,parts_id,betrag) values (%d,%d,%f,%d,'%s')";
			$rc=$db->query(sprintf($sql,$mid,$aid,$tmp[0],$tmp[1],$tmp[2]));
			if (!$rc) break;
			// Bestandsanpassung, raus aus dem Lager
			$sql="update parts set onhand=(onhand-".$tmp[0].") where id=".$tmp[1];
			$rc=$db->query($sql);
		}
		if ($rc) { $db->commit(); return true;}
		else { $db->rollback(); return false;};
	} else {
		$rs=$db->getAll("select * from maschmat where aid=$aid");
		if ($rs) {
			// Bestandsanpassung, zurück ins Lager
			foreach ($rs as $zeile) {
				$sql="update parts set onhand=(onhand+".$zeile["menge"].") where id=".$zeile["parts_id"];
				$rc=$db->query($sql);
				if (!$rc) { $db->rollback(); return false;};
			}
			$rc=$db->query("delete from maschmat where aid=$aid");
			if ($rc) { $db->commit(); return true;}
                	else { $db->rollback(); return false;};
		} else {
			$db->commit(); return true;
		}
	}
	return true;
}
function getVertragStat($vid,$jahr) {
	global $db;
	$sql="select M.parts_id as artnr,A.mid,A.aid,sum((A.betrag*menge)) as summe,M.serialnumber ";
	//$sql.="from maschmat A left join maschine M on M.id=A.mid, contract V left join contmasch C on C.cid=V.contractnumber, ";
	$sql.="from maschmat A left join maschine M on M.id=A.mid, contract V left join contmasch C on C.cid=V.cid, ";
	$sql.="repauftrag R, parts P where V.cid=$vid and  C.mid=M.id  and R.aid=A.aid and P.id=A.parts_id and ";
	$sql.="(R.anlagedatum >= '$jahr-01-01' and R.anlagedatum <= '$jahr-12-31') ";
	$sql.="group by artnr,A.mid,A.aid,M.serialnumber";
	#$sql="select A.parts_id,P.partnumber,M.parts_id as artnr,A.mid,A.aid,(A.betrag*menge) as summe,M.serialnumber ";
	#$sql.="from maschmat A left join maschine M on M.id=A.mid, contract V left join contmasch C on C.cid=V.contractnumber, ";
	#$sql.="repauftrag R, parts P where V.cid=$vid and  C.mid=M.id  and R.aid=A.aid and P.id=A.parts_id and R.anlagedatum like '$jahr%'";
	$rs=$db->getAll($sql);
	if ($rs) { return $rs; }
	else { return false;};
}

function getAllPG() {
	global $db;
	$sql="select * from partsgroup order by partsgroup";
	$rs=$db->getAll($sql);
	if ($rs) { return $rs; }
	else { return false;};	
}

function getGrpArtikel($pg) {
global $db;
	if (empty($pg)) {
		$sql="SELECT * from parts where partsgroup_id is null or partsgroup_id=0 order by description";
	} else {
		$sql="SELECT * from parts where partsgroup_id=$pg order by description";
	}
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		return $rs;
	}
}
?>
