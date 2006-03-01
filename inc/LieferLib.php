<?
// $Id$

/****************************************************
* getAllVendor
* in: sw = array(Art,suchwort)
* out: rs = array(Felder der db)
* hole alle Lieferanten
*****************************************************/
function getAllVendor($sw,$Pre=true) {
global $db;
	if ($Pre) $Pre=$_SESSION["Pre"];
	if (!$sw[0]) { 
		$where="phone like '$Pre".$sw[1]."%' "; 
	} else { 
		$where="upper(name) like '$Pre".$sw[1]."%' or ";
		$where.="upper(department_1) like '$Pre".$sw[1]."%' or ";
		$where.="upper(department_2) like '$Pre".$sw[1]."%'";  
	}
	$sql="select *,'V' as tab  from vendor where $where"; 
	$rs=$db->getAll($sql);
	if(!$rs) {
		$rs=false;
	};
	return $rs;
}

/****************************************************
* getLieferStamm
* in: id = int, ws = boolean
* out: daten = array
* Stammdaten eines Lieferanten holen
*****************************************************/
function getLieferStamm($id,$ws=true) {
global $db;
	$sql="select C.*,E.login,B.description as lityp,B.discount as typrabatt from vendor C ";
	$sql.="left join employee E on C.employee=E.id left join business B on B.id=C.business_id ";
	$sql.="where C.id=$id";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$daten=false;
	} else {
		$row = $rs[0];
		if ($row["grafik"]) {
			$image="./dokumente/".$_SESSION["mansel"]."/$id/logo.".$row["grafik"];
			$size=getimagesize(trim($image));
			$row["size"]=$size[3];
		};
		$sql="select * from shipto where trans_id=$id";
		$rs2=$db->getAll($sql);
		if (!$rs2) { // keine abweichende Lieferanschrift
			if ($ws) {
				$row2=Array(
					shiptoname => $row["name"],
					shiptodepartment_1 => $row["department_1"],
					shiptodepartment_2 => $row["department_2"],
					shiptostreet => $row["street"],
					shiptozipcode => $row["zipcode"],
					shiptocity => $row["city"],
					shiptocountry => $row["country"],
					shiptocontact => "",
					shiptophone => $row["phone"],
					shiptofax => $row["fax"],
					shiptoemail => $row["email"],
					shiptocountry => $row["country"]
				);
			} else {
				$row2=Array(
					shiptoname => "",
					shiptodepartment_1 => "",
					shiptodepartment_2 => "",
					shiptostreet => "",
					shiptozipcode => "",
					shiptocity => "",
					shiptocountry => "",
					shiptocontact => "",
					shiptophone => "",
					shiptofax => "",
					shiptoemail => "",
					shiptocountry => ""
				);
			}
		 } else {
		 	$row2 = $rs2[0];
		}
		$daten=array_merge($row,$row2);
	}
	return $daten;
};

/****************************************************
* suchLieferer
* in: muster = array
* out: daten = array
* Alle Lieferanten suchen deren Name == B
*****************************************************/
function suchLieferer($muster) {
global $db;
	// Array zu jedem Formularfed: Tabelle (0=cust,1=ship), TabName, toUpper
	$dbfld=array(name => array(0,1),street => array(0,1),zipcode => array(0,0),
			city => array(0,1),phone => array(0,0),fax => array(0,0),
			email => array(0,1),department_1 => array(0,1),
			department_2 => array(0,1),country => array(0,1),
			notes => array(0,1),homepage =>array(0,1),
			vendornumber => array(0,0),sw => array(0,1),
			v_customer_id => array(0,0));
	$dbfld2=array(name => "shiptoname", street=>"shiptostreet",
			ziptocode=>"shiptozipcode",city=>"shiptocity",
			phone=>"shiptophone",fax=>"shiptofax",email=>"shiptoemail",
			department_1=>"shiptodepartment_1",country=>"shiptocountry");
	$fuzzy=$muster["fuzzy"];
	$keys=array_keys($muster);
	$suchfld=array_keys($dbfld);
	$anzahl=count($keys);
	$tbl0=false; 
	if ($muster["shipto"]){$tbl1=true;} else {$tbl1=false;}
	$tmp1=""; $tmp2="";
	for ($i=0; $i<$anzahl; $i++) {
		if (in_array($keys[$i],$suchfld) and $muster[$keys[$i]]<>"") {
			if ($dbfld[$keys[$i]][1]==1) {
				$case1="upper("; $case2=")";
				$suchwort=strtoupper(trim($muster[$keys[$i]]));
			} else {
				$case1=""; $case2="";
				$suchwort=trim($muster[$keys[$i]]);
			}
			$suchwort=strtr($suchwort,"*?","%_");
			$tmp1.="and $case1".$keys[$i]."$case2 like '".$suchwort."$fuzzy' ";
			if ($tbl1 && $dbfld2[$keys[$i]]) 
				$tmp2.="and $case1".$dbfld2[$keys[$i]]."$case2 like '".$suchwort."$fuzzy' ";
		}
	}
	if ($tbl1) {
		$tabs="vendor left join shipto on id=trans_id";
		if ($tmp1) $where="(".substr($tmp1,3). ") or ( ";
		if ($tmp2) { 
			$where.=substr($tmp2,3).")"; 
		} else { 
			$where.=" 1)"; 
		}
	} else {
		$tabs="vendor";
		if ($tmp1) $where=substr($tmp1,3);
	}
	$daten=false;
	if ($where<>"") {
		$sql="select vendor.* from $tabs where $where";
		$rs=$db->getAll($sql);
		if($rs) {
			$daten=$rs;
		}
	}
	return $daten;
}

/****************************************************
* saveLieferStamm
* in: daten = array
* out: rc = int
* Stammdaten eines Lieferanten sichern ( undate )
*****************************************************/
function saveLieferStamm($daten,$datei) {
global $db;
	if (!empty($datei["Datei"]["name"])) { 	// eine Datei wird mitgeliefert
			$typ=array(1=>"gif",2=>"jpeg",3=>"png",4=>false);
			$imagesize=getimagesize($datei["Datei"]['tmp_name'],&$info);
			if ($imagesize[2]>0 && $imagesize[2]<4) {
				$bildok=chkdir($_SESSION["mansel"]."/".$daten["id"]);
				$daten["grafik"]=$typ[$imagesize[2]];
			}
	};
	// Array zu jedem Formularfed: TabName, Tabelle (0=cust,1=ship),  require(0=nein,1=ja), Regel, Fehlertext
	$dbfld=array(name => array(0,1,1,"Name",75),street => array(0,1,1,"Strasse",75),
			zipcode => array(0,1,2,"Plz",10),city => array(0,1,1,"Ort",75),
			phone => array(0,0,3,"Telefon",30),fax => array(0,0,3,"Fax",30),
			homepage =>array(0,0,4,"Homepage",0),email => array(0,0,5,"eMail",0),
			notes => array(0,0,0,"Bemerkungen",0),
			department_1 => array(0,0,1,"Zusatzname",75),
			department_2 => array(0,0,1,"Abteilung",75),
			country => array(0,0,8,"Land",3),
			vendornumber => array(0,0,0,"Lieferantennummer",20),
			v_customer_id => array(0,0,0,"Kundennummer",20),
			taxnumber => array(0,0,0,"Steuernummer",0),
			sw => array(0,0,1,"Stichwort",50), 
			business_id => array(0,0,6,"Lieferantentyp",0), 
			contact => array(0,0,1,"Kontakt",50), 
			owener => array(0,0,6,"CRM-User",0),grafik => array(0,0,9,"Grafik",4),
			shiptoname => array(1,0,1,"Liefername",75),
			shiptostreet => array(1,0,1,"Lieferstrasse",75),
			shiptocountry => array(1,0,8,"Lieferland",3),
			shiptozipcode => array(1,0,2,"Liefer-Plz",10),
			shiptocity => array(1,0,1,"Liefer-Ort",75),
			shiptocontact => array(1,0,1,"Kontakt",75),
			shiptophone => array(1,0,3,"Liefer-Telefon",30),
			shiptofax => array(1,0,3,"Liefer-Fax",30),
			shiptoemail => array(1,0,5,"Liefer-eMail",0),
			shiptodepartment_1 => array(1,0,1,"Lieferzusatz",75),
			shiptodepartment_2 => array(1,0,1,"Abteilung",75));
	$keys=array_keys($daten);
	$dbf=array_keys($dbfld);
	$anzahl=count($keys);
	$fid=$daten["id"];
	$fehler=-1;
	$ala=false;
	$tels1=array();$tels2=array();
	for ($i=0; $i<$anzahl; $i++) {
		if (in_array($keys[$i],$dbf)) {
			$tmpval=trim($daten[$keys[$i]]);
			if ($dbfld[$keys[$i]][0]==1) {
				if (!empty($tmpval)) $ala=true;
				if (!chkFld($tmpval,$dbfld[$keys[$i]][1],$dbfld[$keys[$i]][2],$dbfld[$keys[$i]][4])) { 
					$fehler=$dbfld[$keys[$i]][3]; 
					$i=$anzahl; 
				}
				if (in_array($dbfld[$keys[$i]][2],array(0,1,3,4,5,7,8,9))) {
					$query1.=$keys[$i]."='".$tmpval."',";
				 } else {
				 	$query1.=$keys[$i]."=".$tmpval.",";
				 }
				if ($keys[$i]=="shiptophone"||$keys[$i]=="shiptofax") $tels2[]=$tmpval;
			} else {
				if (!chkFld($tmpval,$dbfld[$keys[$i]][1],$dbfld[$keys[$i]][2],$dbfld[$keys[$i]][4])) { 
					$fehler=$dbfld[$keys[$i]][3]; 
					$i=$anzahl; 
				}
				if (in_array($dbfld[$keys[$i]][2],array(0,1,3,4,5,7,8,9,10,11,12,13))) {
					$query0.=$keys[$i]."='".$tmpval."',";
				} else {
					$query0.=$keys[$i]."=".$tmpval.",";
				}
				if ($keys[$i]=="phone"||$keys[$i]=="fax") $tels1[]=$tmpval;
			}
		}
	}
	if ($fehler==-1) {
		$sql0="update vendor set ".substr($query0,0,-1)." where id=$fid";
		mkTelNummer($fid,"V",$tels1);
		if ($bildok) {
			$dir="./dokumente/".$_SESSION["mansel"]."/".$fid;
			$dest=$dir."/logo.".$typ[$imagesize[2]];
			move_uploaded_file($datei["Datei"]["tmp_name"],"$dest");
			if (($imagesize[1]/$imagesize[0]) > 2.4) {
				$hoehe=ceil($imagesize[1]/$imagesize[0]*120);
				$breite=120;
			} else {
				$breite=ceil($imagesize[0]/$imagesize[1]*80);
				$hoehe=80;
			}
			$image1 = imagecreatetruecolor($breite,$hoehe);
			$tue="\$image=imagecreatefrom".$typ[$imagesize[2]]."('$dest');";
			eval($tue);
			imagecopyresized($image1, $image, 0,0, 0,0,$breite,$hoehe,$imagesize[0],$imagesize[1]);
			$tue="image".$typ[$imagesize[2]]."(\$image1,'$dest');";
			eval($tue);
		}
		$rc1=true;
		if ($ala) {
			$sql1q="select count(*) from shipto where trans_id=$fid";
			$rc=$db->getAll($sql1q);
			$x=$rc[0];
			if ($x["count"]==0) {
				$sql1="insert into shipto (trans_id) values ($fid)";
				$rc1=$db->query($sql1);
			};
			//$query1=$query1."shiptoowener=".$ow." ";
			$sql1="update shipto set ".substr($query1,0,-1)." where trans_id=$fid";
			$rc1=$db->query($sql1);
			mkTelNummer($fid,"S",$tels2);
		} else {
			$sql1q="select count(*) from shipto where trans_id=$fid"; //gibt es schon eine Lieferanschrift
			$x=$db->getAll($sql1q);
			if ($x[0]["count"]>0) {
				$sql="delete from shipto where trans_id=$fid";
				$rc=$db->query($sql);
			}
		}
		$rc0=$db->query($sql0);
		if ($rc0 and $rc1) { $rc=$fid; }
		else { $rc="unbekannt"; };
		return $rc;
	} else { 
		if ($daten["saveneu"]){
			$sql="delete from vendor where trans_id=".$daten["id"];
			$rc0=$db->query($sql); 
		};
		return $fehler; 
	};
}

/****************************************************
* mknewVendor
* in:
* out: id = int
* Kundensatz erzeugen ( insert )
*****************************************************/
function mknewVendor($id) {
global $db;
	$newID=uniqid (rand());
	if (!$id) {$uid='null';} else {$uid=$id;};
	$sql="insert into vendor (name,employee) values ('$newID',$uid)";
	$rc=$db->query($sql);
	if ($rc) {
		$sql="select id from vendor where name = '$newID'";
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
* saveNeuLieferStamm
* in: daten = array
* out: rc = int
* Stammdaten eines Lieferanten sichern ( insert )
*****************************************************/
function saveNeuLieferStamm($daten,$datei) {
	$daten["id"]=mknewVendor($_SESSION["loginCRM"]);
	$rs=saveLieferStamm($daten,$datei);
	return $rs;
}

function doReportL($data) {
global $db;
	$felder=substr($data,0,-1);
	if (!ereg("P.",$felder)) {
		$sql="select $felder from vendor L order by L.name";
	} else if (!ereg("L.",$felder)) {
		$sql="select $felder from contacts P order by P.cp_name";
	} else {
		$sql="select $felder from vendor L left join contacts P on L.id=P.cp_cv_id order by L.name,P.cp_name";
	}
	$rc=$db->getAll($sql);
	$f=fopen("tmp/report_".$_SESSION["loginCRM"].".csv","w");
	fputs($f,$felder."\n");
	if ($rc) {
		foreach ($rc as $row) {
			$tmp="";
			foreach($row as $fld) {
				$tmp.="$fld,";	
			}
			fputs($f,substr($tmp,0,-1)."\n");
		};
		fclose($f);
		return true;
	} else {
		fputs($f,"Keine Treffer.\n");
		fclose($f);
		return false;
	} 
}
/****************************************************
* getBusiness
* out: array
* Kundentype holen
*****************************************************/
function getBusiness() {
global $db;
	$sql="select * from business order by description";
	$rs=$db->getAll($sql);
	$leer=array(array("id"=>"","discription"=>""));
	return array_merge($leer,$rs);
}
function leertplL (&$t,$tpl,$msg="") {
		$typ=getBusiness();
		$t->set_file(array("li1" => "liefern".$tpl.".tpl"));
		$t->set_var(array(
			Btn1 => "",
			Btn2 => "",
			MSG =>	$msg,
			action => "liefern".$tpl.".php",
			sw		=> "",
			name	=> "",
			department_1	=> "",
			department_2	=> "",
			street	=> "",
			country	=> "",
			zipcode	=> "",
			city	=> "",
			phone	=> "",
			fax		=> "",
			notes   => "",
			email	=> "",
			homepage => "",
			ustid	=> "",
			vendornumber	=> "",
			v_customer_id	=> "",
			notes 	=> "",
			init	=> $_SESSION["employee"],
			employee	=> $_SESSION["loginCRM"]
			));
			$t->set_block("li1","TypListe","BlockT");
			if ($typ) foreach ($typ as $row) {
				$t->set_var(array(
					Bid => $row["id"],
					Bsel => ($row["id"]==$daten["business_id"])?"selected":"",
					Btype => $row["description"],
				));
				$t->parse("BlockT","TypListe",true);
			}
			$t->set_block("li1","OwenerListe","Block");
				$first[]=array("grpid"=>"","rechte"=>"w","grpname"=>"Alle");
				$first[]=array("grpid"=>$_SESSION["loginCRM"],"rechte"=>"w","grpname"=>"Pers&ouml;nlich");
				$grp=getGruppen();
				if ($grp) {
					$user=array_merge($first,$grp);
				} else {
					$user=$first;
				}
				$selectOwen=1;
				if ($user) foreach($user as $zeile) {
					if ($zeile["grpid"]==$selectOwen) {
						$sel="selected";
					} else {
						$sel="";
					}
					$t->set_var(array(
						grpid => $zeile["grpid"],
						Gsel => $sel,
						Gname => $zeile["grpname"],
					));
					$t->parse("Block","OwenerListe",true);
				}
}
function vartplL (&$t,$daten,$msg,$btn1,$btn2,$tpl) {
		$typ=getBusiness();
		if ($daten["grafik"]) {
			$Image="<img src='dokumente/".$_SESSION["mansel"]."/".$daten["id"]."/logo.".$daten["grafik"]."' ".$daten["size"].">";
		}
		$t->set_file(array("li1" => "liefern".$tpl.".tpl"));
		$t->set_var(array(
				Btn1	=> $btn1,
				Btn2	=> $btn2,
				Msg	=>	$msg,
				action	=> "liefern".$tpl.".php",
				ID 	=> $daten["id"],
				sw 	=> $daten["sw"],
				contact	=> $daten["contact"],
				name 	=> $daten["name"],
				department_1 	=> $daten["department_1"],
				department_2 	=> $daten["department_2"],
				street	=> $daten["street"],
				country	=> $daten["country"],
				zipcode	=> $daten["zipcode"],
				city 	=> $daten["city"],
				phone 	=> $daten["phone"],
				fax 	=> $daten["fax"],
				branche	=> $daten["branche"],
				email 	=> $daten["email"],
				homepage => $daten["homepage"],
				ustid 	=> $daten["ustid"],
				taxnumber 	=> $daten["taxnumber"],
				bank	=> $daten["bank"],
				bank_code	=> $daten["bank_code"],
				account_number	=> $daten["account_number"],
				terms	=> $daten["terms"],
				kreditlim	=> $daten["creditlimit"],	
				vendornumber	=> $daten["vendornumber"],
				v_customer_id	=> $daten["v_customer_id"],
				shiptoname	=> $daten["shiptoname"],
				shiptodepartment_1	=> $daten["shiptodepartment_1"],
				shiptodepartment_2	=> $daten["shiptodepartment_2"],
				shiptostreet	=> $daten["shiptostreet"],
				shiptocountry	=> $daten["shiptocountry"],
				shiptozipcode 	=> $daten["shiptozipcode"],
				shiptocity	=> $daten["shiptocity"],
				shiptophone 	=> $daten["shiptophone"],
				shiptofax	=> $daten["shiptofax"],
				shiptoemail 	=> $daten["shiptoemail"],
				shiptocontact 	=> $daten["shiptocontact"],
				notes		=> $daten["notes"],
				IMG	=> $Image,
				grafik	=> $daten["grafik"],
				init	=> ($daten["owener"])?$daten["owener"]:"ERP",
				employee	=> $daten["employee"]
		));
		$t->set_block("li1","TypListe","BlockT");
		if ($typ) foreach ($typ as $row) {
			$t->set_var(array(
				Bid => $row["id"],
				Bsel => ($row["id"]==$daten["business_id"])?"selected":"",
				Btype => $row["description"],
			));
			$t->parse("BlockT","TypListe",true);
		}
		if ($daten["owener"]==$_SESSION["loginCRM"]) {
			$t->set_block("li1","OwenerListe","Block");
			$first[]=array("grpid"=>"","rechte"=>"w","grpname"=>"Alle");
			$first[]=array("grpid"=>$_SESSION["loginCRM"],"rechte"=>"w","grpname"=>"Pers&ouml;nlich");
			$grp=getGruppen();
			if ($grp) {
				$user=array_merge($first,$grp);
			} else {
				$user=$first;
			}
			$selectOwen=$daten["employee"];
			if ($user) foreach($user as $zeile) {
				if ($zeile["grpid"]==$selectOwen) {
					$sel="selected";
				} else {
					$sel="";
				}
				$t->set_var(array(
					grpid => $zeile["grpid"],
					Gsel => $sel,
					Gname => $zeile["grpname"],
				));
				$t->parse("Block","OwenerListe",true);
			}
		} else {
			$t->set_var(array(
				grpid => $daten["owener"],
				Gsel => "selected",
				Gname => ($daten["owener"])?getOneGrp($daten["owener"]):"&ouml;ffentlich",
			));
			$t->parse("Block","OwenerListe",true);
		}
}
?>
