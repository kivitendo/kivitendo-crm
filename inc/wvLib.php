<?php

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
		if (!is_dir($filename) and preg_match('/wv.+\.pdf/',$filename) ) { $vorlagen[]=substr($filename,2); };
	}
	return $vorlagen;
}

function getAllMaschinen($suchw) {
	$sql="select M.*,P.partnumber,P.description from maschine M left join parts P on P.id=M.parts_id ";
	$sql.="left join contmasch C on C.mid=M.id where C.cid is null and (P.partnumber like '%$suchw%' or P.description like '%$suchw%')";
	$rs=$_SESSION['db']->getAll($sql);
	if(!$rs) {
		$rs=false;
	};
	return $rs;
}

function getArtikel($sw) {
	$sw=strtr($sw,"*?","%_");
	$sql="select * from parts where partnumber like '$sw'";
	$rs=$_SESSION['db']->getAll($sql);
	if(!$rs) {
		$rs=false;
	};
	return $rs;
}

function getMaschSer($ser,$pid) {
	$sql="select * from maschine left join history on id=mid where serialnumber='$ser' and parts_id=$pid and art='neu'";
	$rs=$_SESSION['db']->getAll($sql);
	if(!$rs) {
		$rs=false;
	};
	$sql="select * from parts where id=$pid";
	$rs2=$_SESSION['db']->getAll($sql);
	$data=$rs[0]; 
	$data["partnumber"]=$rs2[0]["partnumber"];
	$data["notes"]=$rs2[0]["notes"];
	$data["description"]=$rs2[0]["description"];	
	return $data;
}

function getNumber($nr) {
	//$sql="select * from invoice where parts_id=$nr";
	$sql=" select * from invoice where parts_id=$nr and serialnumber is not null and serialnumber<>'' and serialnumber  not in (select serialnumber from maschine where parts_id=$nr)";
	$rs=$_SESSION['db']->getAll($sql);
	if(!$rs) {
		$rs=false;
	};
	return $rs;
}
function getBekannt($nr) {
	$sql="select * from maschine where  parts_id=$nr";
	$rs=$_SESSION['db']->getAll($sql);
	if(!$rs) {
		$rs=false;
	};
	return $rs;
}
function saveNewMaschine($data) {
	if (!$data["parts_id"]) return false;
	$serialnumber=($data["snumber"])?$data["snumber"]:$data["serialnumber"];
	if ($serialnumber) {
		$sql="select count(*) from maschine where parts_id='".$data["parts_id"]."' and serialnumber = '".$serialnumber."'";
		$rs=$_SESSION['db']->getAll($sql);
		if ($rs[0]["count"]>0) return false;
		$newID=uniqid (rand());
		$_SESSION['db']->begin();
		$sql="insert into maschine (parts_id,serialnumber,standort,inspdatum) values (%d,'%s','$newID',%s)";
		$inspd=($data["inspdatum"]=="00.00.0000" || empty($data["inspdatum"]))?"null":"'".date2db($data["inspdatum"])."'";
		$rc=$_SESSION['db']->query(sprintf($sql,$data["parts_id"],$serialnumber,$inspd));
		if ($rc) {
			$sql="select * from maschine where standort='$newID'";
			$rs=$_SESSION['db']->getAll($sql);
			$mid=$rs[0]["id"];
			$sql="update maschine set standort='Lager' where id=$mid";
			$rc=$_SESSION['db']->query($sql);
			if ($rc) {
				$rc=insHistory($mid,'neu','Aufnahme',null);
			};
			if ($rc) { $_SESSION['db']->commit(); }
			else { $_SESSION['db']->rollback(); };
		}
		return $rc;
	} else  { 
		return false;
	}
}

function updateMaschine($data) {
	$inspd=($data["inspdatum"]=="00.00.0000" || empty($data["inspdatum"]))?"null":"'".date2db($data["inspdatum"])."'";
	$sql="update maschine set inspdatum = $inspd where id=".$data["mid"];
	$rc=$_SESSION['db']->query($sql);
	if ($rc) {
		$sql="update history set beschreibung='".$data["beschreibung"]."' where art='neu' and mid=".$data["mid"];
		$rc=$_SESSION['db']->query($sql);
		return $rc;
	} else  { 
		return false;
	}
}
function getCounter($mid) {
	$sql="select * from maschine where id = $mid";
	$rs=$_SESSION['db']->getAll($sql);
	return $rs[0]["counter"];
}

function saveNewVertrag($data) {
	$newID=uniqid (rand());
	$_SESSION['db']->begin();
	$sql="insert into contract (contractnumber,template,bemerkung,customer_id,betrag,anfangdatum,endedatum) values ('%s','%s','%s','%d','%s','%s','%s')";
	$start=($data["anfangdatum"]<>"00.00.0000" && !empty($data["anfangdatum"]))?date2db($data["anfangdatum"]):date("Y-m-d");	
	$stop=($data["endedatum"]<>"00.00.0000" && !empty($data["endedatum"]))?date2db($data["endedatum"]):$start;
    if ($stop<$start) $stop=$start;
	$rc=$_SESSION['db']->query(sprintf($sql,$newID,$data["vorlage"],$data["bemerkung"],$data["cp_cv_id"],$data["betrag"],$start,$stop));
	if ($rc) {
		$sql="select * from contract where contractnumber='$newID'";
		$rs=$_SESSION['db']->getAll($sql);
		$cid=$rs[0]["cid"];
		$cn=newContract();
		$sql="update contract set contractnumber='$cn' where cid=$cid";
		$rc=$_SESSION['db']->query($sql);
		if ($rc) {
			$i=0;
			foreach ($data["maschinen"] as $row) {
				$standort=($row[2]<>"")?$row[2]:"Kunde";
				$rc1=newCM($row[0],$cid);
				$rc2=insHistory($row[0],"contadd",'Vertrag zugeordnet',$cn);
				$rc3=$_SESSION['db']->query("update maschine set standort = '$standort' where id=".$row[0]);
				if (!$rc1 || !$rc2 || !$rc3) {
					$_SESSION['db']->rollback();
					return false;
				}
				$i++;
			}
		} else {
			$_SESSION['db']->rollback();
			return false;
		}
		$_SESSION['db']->commit();
		return $cid;
	}
}
function updateVertrag($data) {
	$_SESSION['db']->begin();
	$start=($data["anfangdatum"]<>"00.00.0000" && !empty($data["anfangdatum"]))?date2db($data["anfangdatum"]):date("Y-m-d");	
	$stop=($data["endedatum"]<>"00.00.0000" && !empty($data["endedatum"]))?date2db($data["endedatum"]):$start;
	if ($data["new"]) {
		$sql="update contract set template='%s',bemerkung='%s',betrag='%s',anfangdatum='%s',endedatum='%s' where cid=%d";
		$rc=$_SESSION['db']->query(sprintf($sql,$data["vorlage"],$data["bemerkung"],$data["betrag"],$start,$stop,$data["vid"]));
	} else {
		$sql="update contract set bemerkung='%s',betrag='%s',anfangdatum='%s',endedatum='%s' where cid=%d";
		$rc=$_SESSION['db']->query(sprintf($sql,$data["bemerkung"],$data["betrag"],$start,$stop,$data["vid"]));
	}
	if ($rc) {
		//$sql="select mid from contmasch where cid=".$data["contractnumber"];
		$sql="select mid from contmasch where cid=".$data["vid"];
		$rs=$_SESSION['db']->getAll($sql);
		//$sql="delete from contmasch where cid=".$data["contractnumber"];
		$sql="delete from contmasch where cid=".$data["vid"];
		$rc=$_SESSION['db']->query($sql);
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
				$rc3=$_SESSION['db']->query("update maschine set standort = '$standort' where id=".$row[0]);
				if (!$rc1 || !$rc2 || !$rc3) {
					$_SESSION['db']->rollback();
					return false;
				}
			}
			foreach($rs as $line) {
				if (!in_array($line["mid"],$in)) {
					$rc2=insHistory($line["mid"],"contsub",$data["contractnumber"]);
					$rc3=$_SESSION['db']->query("update maschine set standort = 'unbekannt' where id=".$line["mid"]);
				}
			}
		} else {
			$_SESSION['db']->rollback();
			return false;
		}
		$_SESSION['db']->commit();
		return $data["vid"];
	}
}
function insHistory($mid,$art,$beschreibung,$bezug) {
        if ( $bezug ) {
	    $sql="insert into history (mid,art,beschreibung,bezug) values ($mid,'$art','$beschreibung',$bezug)";
        } else {
	    $sql="insert into history (mid,art,beschreibung,bezug) values ($mid,'$art','$beschreibung',null)";
        }
	$rc=$_SESSION['db']->query($sql);
	return $rc;
}
function newCM($mid,$cid) {
	$sql="insert into contmasch (mid,cid) values ($mid,$cid)";
	$rc=$_SESSION['db']->query($sql);
	return $rc;
}
function newContract() {
	$sql="select * from defaults";
	$rs=$_SESSION['db']->getAll($sql);
	$cid=++$rs[0]["contnumber"];
	$sql="update defaults set contnumber='$cid'";
	$rc=$_SESSION['db']->query($sql);
	return $cid;	
}
function suchVertrag($vid) {
	$sql="select V.*,C.name from contract V left join customer C on V.customer_id=C.id where V.contractnumber like '%$vid%'";
	$rs=$_SESSION['db']->getAll($sql);
	return $rs;	
}
function getVertrag($vid) {
	$sql="select C.*,K.name,K.customernumber from contract C left join customer K on K.id=C.customer_id where C.cid=$vid";
	$rs=$_SESSION['db']->getAll($sql);
	return $rs[0];		
}
function getVertragMaschinen($cnr) {
	$sql="select * from contmasch C left join maschine M on M.id=C.mid left join parts P on P.id=M.parts_id  where C.cid=$cnr";
	$rs=$_SESSION['db']->getAll($sql);
	return $rs;			
}
function getSernumber($sn,$pn=false) {
	$sql="select M.*,K.name,K.street,K.zipcode,K.city,K.phone,P.partnumber,P.description,P.notes,V.*,C.mid,P.id as parts_id ";
	$sql.="from maschine M left join parts P on P.id=M.parts_id ";
	$sql.="left join contmasch C on C.mid=M.id ";
	//$sql.="left join contract V on V.contractnumber=cast (C.cid as text) ";	
	$sql.="left join contract V on V.cid=C.cid ";	
	$sql.="left join customer K on K.id=V.customer_id ";
	$sql.="where M.serialnumber like '%$sn%' ";
	$sql.=($pn)?"and parts_id=$pn":"";
	$rs=$_SESSION['db']->getAll($sql);	
	return $rs;
}
function getArtnumber($sn) {
	$sql="select M.*,K.name,K.street,K.zipcode,K.city,K.phone,P.partnumber,P.description,P.notes,V.*,C.mid ";
	$sql.="from maschine M left join parts P on P.id=M.parts_id ";
	$sql.="left join contmasch C on C.mid=M.id ";
	//$sql.="left join contract V on V.contractnumber=C.cid ";	
	$sql.="left join contract V on V.cid=C.cid ";	
	$sql.="left join customer K on K.id=V.customer_id ";
	$sql.="where P.partnumber like '%$sn%'";
	$rs=$_SESSION['db']->getAll($sql);	
	return $rs;
}
function getHistory($nr) {
	$sql="SELECT H.*,R.status from history H left join repauftrag R on bezug = aid where H.mid = $nr order by itime  desc";
	$rs=$_SESSION['db']->getAll($sql);	
	return $rs;	
}
function saveNewStandort($ort,$mid) {
	$sql="update maschine set standort='$ort' where id=$mid";
	$rc=$_SESSION['db']->query($sql);
}
function getCustContract($fid) {
	$sql="select contractnumber,cid from contract where customer_id=$fid";
	$rs=$_SESSION['db']->getAll($sql);	
	return $rs;		
}
function getAllMaschine($mid) {
	//$sql="select X.mid,V.*,M.*,P.description from contmasch X left join contract V on V.contractnumber=cast (X.cid as text) ";
	$sql="select X.mid,V.*,M.*,P.description from contmasch X left join contract V on V.cid=X.cid ";
	$sql.="left join maschine M on M.id=X.mid left join parts P on P.id=M.parts_id where X.mid=$mid";
	$rs=$_SESSION['db']->getAll($sql);	
	return $rs[0];		
}
function saveRAuftrag($data) {
	if ($data["aid"]) { $rc=updRAuftrag($data); }
	else { $rc=insRAuftrag($data); }
	return $rc;
}
function insRAuftrag($data) {
	$_SESSION['db']->begin();
	$rs=$_SESSION['db']->getAll("select sonumber from defaults");	
	$aid=$rs[0]["sonumber"]+1;
	$rc=$_SESSION['db']->query("update defaults set sonumber=".$aid);
	$sql="insert into repauftrag (aid,mid,kdnr,cause,schaden,anlagedatum,bearbdate,employee,bearbeiter,status,counter) ";
	$sql.="values ($aid,%d,'%s','%s','%s','%s','%s',%d,%d,%d,%d)";
	$rc=$_SESSION['db']->query(sprintf($sql,$data["mid"],$data["kdnr"],$data["cause"],$data["schaden"],
			date("Y-m-d"),date2db($data["datum"]),$_SESSION["loginCRM"],$data["bid"],$data["status"],$data["counter"]));
	if ($rc) { 
		insHistory($data["mid"],"RepAuftr","$aid|".$data["cause"],$aid);
		if ($data["counter"]) {
			$rc=updateCounter($data["counter"],$data["mid"]);
		}
		$_SESSION['db']->commit(); $rc=$data; $rc["aid"]=$aid; }
	else { $_SESSION['db']->rollback(); $rc=false; };
	return $rc;
}
function updRAuftrag($data) {
	$sql="update repauftrag set cause='%s', schaden='%s', reparatur='%s', status=%d, ";
	$sql.="employee=%d, bearbeiter=%d, bearbdate='%s', counter=%d where aid=%d";
	$rc=$_SESSION['db']->query(sprintf($sql,$data["cause"],$data["schaden"],$data["behebung"],$data["status"],
			$_SESSION["loginCRM"],$data["bid"],date2db($data["datum"]),$data["counter"],$data["aid"]));
	if ($rc) { 
		$rc=updateCounter($data["counter"],$data["mid"]);	
		return getRAuftrag($data["aid"]); }
	else { return false; }
}

function updateCounter($cnt,$mid) {
	$sql="update maschine set counter=$cnt where id = $mid";
	return $_SESSION['db']->query($sql);
}

function updateIdat($idat,$mid) {
	$sql="update maschine set inspdatum='".date2db($idat)."' where id = $mid";
	return $_SESSION['db']->query($sql);
}

function getRAuftrag($nr) {
	$sql="select * from repauftrag where aid=$nr";
	$rs=$_SESSION['db']->getAll($sql);
	if ($rs) { return $rs[0]; }
	else { return false;};
}
function getAllMat($aid,$mid) {
	$sql="select *,P.description from maschmat M left join parts P on M.parts_id=P.id where mid=$mid and aid=$aid";
	$rs=$_SESSION['db']->getAll($sql);
	if ($rs) { return $rs; }
	else { return false;};
}
function safeMaschMat($mid,$aid,$material) {
	$_SESSION['db']->begin();
	if ($material) {
		$old=$_SESSION['db']->getAll("select * from maschmat where aid=$aid");
		// Bestandsanpassung, zurück ins Lager
		if ($old) foreach ($old as $row) {
			$sql="update parts set onhand=(onhand+".$row["menge"].") where id=".$row["parts_id"];
			$rc=$_SESSION['db']->query($sql);
		}
		$rc=$_SESSION['db']->query("delete from maschmat where aid=$aid");
		foreach ($material as $zeile) {
			$tmp=explode(";",$zeile);
			$sql="insert into maschmat (mid,aid,menge,parts_id,betrag) values (%d,%d,%f,%d,'%s')";
			$rc=$_SESSION['db']->query(sprintf($sql,$mid,$aid,$tmp[0],$tmp[1],$tmp[2]));
			if (!$rc) break;
			// Bestandsanpassung, raus aus dem Lager
			$sql="update parts set onhand=(onhand-".$tmp[0].") where id=".$tmp[1];
			$rc=$_SESSION['db']->query($sql);
		}
		if ($rc) { $_SESSION['db']->commit(); return true;}
		else { $_SESSION['db']->rollback(); return false;};
	} else {
		$rs=$_SESSION['db']->getAll("select * from maschmat where aid=$aid");
		if ($rs) {
			// Bestandsanpassung, zurück ins Lager
			foreach ($rs as $zeile) {
				$sql="update parts set onhand=(onhand+".$zeile["menge"].") where id=".$zeile["parts_id"];
				$rc=$_SESSION['db']->query($sql);
				if (!$rc) { $_SESSION['db']->rollback(); return false;};
			}
			$rc=$_SESSION['db']->query("delete from maschmat where aid=$aid");
			if ($rc) { $_SESSION['db']->commit(); return true;}
                	else { $_SESSION['db']->rollback(); return false;};
		} else {
			$_SESSION['db']->commit(); return true;
		}
	}
	return true;
}
function getVertragStat($vid,$jahr) {
	$sql="select M.parts_id as artnr,A.mid,A.aid,sum((A.betrag*menge)) as summe,M.serialnumber ";
	//$sql.="from maschmat A left join maschine M on M.id=A.mid, contract V left join contmasch C on C.cid=V.contractnumber, ";
	$sql.="from maschmat A left join maschine M on M.id=A.mid, contract V left join contmasch C on C.cid=V.cid, ";
	$sql.="repauftrag R, parts P where V.cid=$vid and  C.mid=M.id  and R.aid=A.aid and P.id=A.parts_id and ";
	$sql.="(R.anlagedatum >= '$jahr-01-01' and R.anlagedatum <= '$jahr-12-31') ";
	$sql.="group by artnr,A.mid,A.aid,M.serialnumber";
	#$sql="select A.parts_id,P.partnumber,M.parts_id as artnr,A.mid,A.aid,(A.betrag*menge) as summe,M.serialnumber ";
	#$sql.="from maschmat A left join maschine M on M.id=A.mid, contract V left join contmasch C on C.cid=V.contractnumber, ";
	#$sql.="repauftrag R, parts P where V.cid=$vid and  C.mid=M.id  and R.aid=A.aid and P.id=A.parts_id and R.anlagedatum like '$jahr%'";
	$rs=$_SESSION['db']->getAll($sql);
	if ($rs) { return $rs; }
	else { return false;};
}

function getAllPG() {
	$sql="select * from partsgroup order by partsgroup";
	$rs=$_SESSION['db']->getAll($sql);
	if ($rs) { return $rs; }
	else { return false;};	
}

function getGrpArtikel($pg) {
	if (empty($pg)) {
		$sql="SELECT * from parts where partsgroup_id is null or partsgroup_id=0 order by description";
	} else {
		$sql="SELECT * from parts where partsgroup_id=$pg order by description";
	}
	$rs=$_SESSION['db']->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		return $rs;
	}
}
?>
