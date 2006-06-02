<?
// $Id$
/****************************************************
* chkTable
* in: fid = int
* out: file = string
* ist das ein Kunde oder Lieferant
*****************************************************/
function chkTable($fid){
global $db;
	$file="firma2.php";
	$sql="select count(*) from customer where id=$fid";
	$row=$db->getAll($sql);
	if ($row[0]["count"]<1) {
		$sql="select count(*) from vendor where id=$fid";
		$row=$db->getAll($sql);
		if ($row[0]["count"]==1) $file="liefer2.php";
	}
	return $file;
};


/****************************************************
* getKontaktStamm
* in: id = int
* out: daten = array
* Stammdaten einer Person holen
* !! noch in eine andere Lib auslagern
* !! da auch von Lieferant und Person gebraucht wird
*****************************************************/
function getKontaktStamm($id) {
global $db;
	$sql="select C.*,E.login from contacts C left join employee E on C.cp_employee=E.id where C.cp_id=$id";
	$rs=$db->getAll($sql);
	if(!$rs) {
		$daten=false;
	} else {
		$firma="Einzelperson";
		$tab="";
		$cnd="";
		if (!empty($rs[0]["cp_cv_id"])) {  // gehört zu einem Kunden oder Lieferanten
			$sql="select id,name,department_1,customernumber from customer where id=".$rs[0]["cp_cv_id"];
			$rs1=$db->getAll($sql);
			$tab="C";
			$cnr=$rs1[0]["customernumber"];
			if (empty($rs1[0]["name"])) {  // nicht zu Kunde sondern zu Lieferant
				$sql="select id,name,department_1,vendornumber from vendor   where id=".$rs[0]["cp_cv_id"];
				$rs1=$db->getAll($sql);
				$tab="V";
				$cnr=$rs1[0]["vendornumber"];
			}
			if($rs1[0]["name"]) { 
				$firma=$rs1[0]["name"]; 
			}
		}
		$daten=$rs[0];
		$daten["Firma"] = $firma;
		$daten["Department_1"]=$rs1[0]["department_1"];
		$daten["tabelle"] = $tab;
		$daten["customernumber"]=$cnr;
	}
	return $daten;
};

/****************************************************
* getAllPerson
* in: sw = array(Art,suchwort)
* out: rs = array(Felder der db)
* hole Liste der Kontaktpersonen
*****************************************************/
function getAllPerson($sw,$Pre=true) {
global $db;
		if ($Pre) $Pre=$_SESSION["Pre"];
		$rechte=berechtigung("cp_");
		if (!$sw[0]) { $where="cp_phone1 like '$Pre".$sw[1]."%' or cp_phone2 like '$Pre".$sw[1]."%' "; }
		else { $where="upper(cp_name) like '$Pre".$sw[1]."%' "; }
		$sql="select *,'P' as tab,cp_id as id,cp_name as name  from contacts where ($where) and $rechte";
		$rs=$db->getAll($sql);
		if(!$rs) {
			$rs=false;
		};
		return $rs;
}

/****************************************************
* getAllKontakt
* in: id = int
* out: daten = array
* alle Kontakte eines Kunden/Lieferanten holen
*****************************************************/
function getAllKontakt($id) {
global $db;
	$rechte=berechtigung("cp_");
	$sql="select * from contacts where cp_cv_id=$id  and $rechte order by cp_name,cp_givenname";
	$rs=$db->getAll($sql);
	return $rs;
}


/****************************************************
* suchPerson
* in: muster = array
* out: daten = array
*****************************************************/
function suchPerson($muster) {
global $db;
	$rechte=berechtigung("cp_");
	if ($muster["cp_name"]=="~") {
		$where0=" and upper(cp_name) ~ '^\[^A-Z\].*$'  ";
	} else {
		// Array zu jedem Formularfed: Tabelle (0=contact,1=cust/vend), TabName, toUpper
	    	$dbfld=array("cp_name" => 1,"cp_givenname" => 1,"cp_greeting" => 1,"cp_title" => 1,
					"cp_street" => 1,"cp_zipcode" => 0,"cp_city" => 1,"cp_country" => 0,
					"cp_phone1" => 0,"cp_phone2" => 0,"cp_fax" => 0,
					"cp_homepage" => 1,"cp_email" => 1,
					"cp_notes" => 1,"cp_stichwort1" => 1,
					"cp_gebdatum" => 0,"cp_beziehung" => 1,
					"cp_abteilung" => 1,"cp_position" => 1,
					"cp_cv_id" => 0,"cp_owener" => 0);
		$keys=array_keys($muster);
		$dbf=array_keys($dbfld);
		$anzahl=count($keys);
		$where0="";
		$daten=false;
		$tbl0=false;
		$fuzzy=$muster["fuzzy"];
		if ($muster["greeting"]=="H") { $muster["cp_greeting"]="Herr"; }
		else if ($muster["greeting"]=="F") { $muster["cp_greeting"]="Frau"; };
		for ($i=0; $i<$anzahl; $i++) {
			if (in_array($keys[$i],$dbf) && $muster[$keys[$i]]) {
				if ($dbfld[$keys[$i]]==1)  {
					$case1="upper("; $case2=")";
					$suchwort=strtoupper(trim($muster[$keys[$i]]));
				} else {
					$case1=""; $case2="";
					$suchwort=trim($muster[$keys[$i]]);
				}
				$suchwort=strtr($suchwort,"*?","%_");
				if ($keys[$i]=="cp_gebdatum") {$d=split("\.",$suchwort); $suchwort=$d[2]."-".$d[1]."-".$d[0]; };
				$where0.="and $case1".$keys[$i]."$case2 like '".$suchwort."$fuzzy' ";
				if ($keys[$i]=="cp_phone1") $where0.="and cp_phone2 like '".$suchwort."$fuzzy' ";
			}
		}
		$x=0;
		if ($muster["cp_sonder"]) {
			foreach ($muster["cp_sonder"] as $row) {
				$x+=$row;
			}
			$where0.="and (cp_sonder & $x) = $x ";
		}
	}
	if ($where0<>"") $where=substr($where0,0,-1);
	$sql0="select *,1 as tbl from contacts,customer where cp_cv_id=id  and $rechte $where order by cp_name";
	$rs0=$db->getAll($sql0);
	$sql0="select *,2 as tbl from contacts,vendor where cp_cv_id=id $where  and $rechte order by cp_name";
	$rs1=$db->getAll($sql0);
	$sql0="select *,3 as tbl from contacts where $rechte ".$where." order by cp_name";
	$rs2=$db->getAll($sql0);
	$daten=array_merge($rs0,$rs1,$rs2);
	$key=array();
	foreach ($daten as $satz) {
		if (!in_array($satz["cp_id"],$key)) {
			$key[]=$satz["cp_id"];
			$daten_neu[]=$satz;
		}
	};
	return $daten_neu;
}

/****************************************************
* savePersonStamm
* in: daten = array
* out: rc = int
* KontaktDaten sichern ( update )
*****************************************************/
function savePersonStamm($daten,$datei) {
global $db;
	$tmp=0;
	if ($daten["cp_sonder"]) foreach ($daten["cp_sonder"] as $data) {
		$tmp+=$data;
	}
	$daten["cp_sonder"]=$tmp;
	// Array zu jedem Formularfed: Tabelle (0=contact,1=cust/vend),  require(0=nein,1=ja), Regel
	$dbfld=array(	"cp_name" => array(0,1,1,"Name",75),	"cp_givenname" => array(0,1,1,"Vorname",75),	"cp_greeting" => array(0,0,1,"Anrede",75),
			"cp_title" => array(0,0,1,"Titel",75),	"cp_street" => array(0,0,1,"Strasse",75),	"cp_zipcode" => array(0,0,2,"Plz",10),
			"cp_city" => array(0,0,1,"Ort",75),	"cp_country" => array(0,0,8,"Land",3), 		"cp_sonder" => array(0,0,10,"SonderFlag",0),
			"cp_phone1" => array(0,0,3,"Telefon",30),"cp_phone2" => array(0,0,3,"Mobile",30),	"cp_fax" => array(0,0,3,"Fax",30),
			"cp_homepage" =>array(0,0,4,"Homepage",0),"cp_email" => array(0,0,5,"eMail",0),
			"cp_notes" => array(0,0,1,"Bemerkungen",0),"cp_stichwort1" => array(0,0,1,"Stichworte",50),
			"cp_gebdatum" => array(0,0,7,"Geb-Datum",0),"cp_beziehung" => array(0,0,6,"Beziehung",0),
			"cp_abteilung" => array(0,0,1,"Abteilung",25),"cp_position" => array(0,0,1,"Position",25),
			"cp_cv_id" => array(0,0,6,"FID",0),	"name" => array(1,0,1,"Firma",75),		
			"cp_owener" => array(0,0,6,"CRM-User",0),"cp_grafik" => array(0,0,9,"Grafik",4),);				
	if (!empty($datei["bild"]["name"])) {  		// eine Datei wird mitgeliefert
			$typ=array(1=>"gif",2=>"jpeg",3=>"png",4=>false);
			$imagesize=getimagesize($datei["bild"]['tmp_name'],&$info);
			if ($imagesize[2]>0 && $imagesize[2]<4) {
				$bildok=chkdir($_SESSION["mansel"]."/".$pid);
				$daten["cp_grafik"]=$typ[$imagesize[2]];
				$bildok=true;
			}
	} else {
		$daten["cp_grafik"]=$daten["IMG_"];
	}
	$keys=array_keys($daten);
	$dbf=array_keys($dbfld);
	$fid=$daten["fid"];
	$anzahl=count($keys);
	$pid=$daten["PID"];
	$fehler=-1;
	$tels=array();
	if ($daten["greeting"]=="H") { $daten["cp_greeting"]="Herr"; }
	else if ($daten["greeting"]=="F") { $daten["cp_greeting"]="Frau"; };	
	for ($i=0; $i<$anzahl; $i++) {
		if (in_array($keys[$i],$dbf)) {
			$tmpval=trim($daten[$keys[$i]]);
			if ($dbfld[$keys[$i]][0]==1) {
				continue;
			} else {
				if (!chkFld($tmpval,$dbfld[$keys[$i]][1],$dbfld[$keys[$i]][2],$dbfld[$keys[$i]][4])) {  $fehler=$dbfld[$keys[$i]][3]; $fehler=$keys[$i]; $i=$anzahl+1;}
				if ($keys[$i]=="cp_phone1"||$keys[$i]=="cp_phone2"||$keys[$i]=="cp_fax") $tels[]=$tmpval;
				$query0.=$keys[$i]."="; 
				if (in_array($dbfld[$keys[$i]][2],array(0,1,3,4,5,7,8,9))) {
						$query0.="'".$tmpval."',";
				} else {
						$query0.=$tmpval.",";
				}
			}
		}
	}
	if ($fehler==-1) {
		if (!$daten["PID"] or $daten["PID"]<1) $pid=mknewPerson($daten["employee"]);
		if (!$pid) return "unbekannt";
		if ($bildok) {
			$dir=$_SESSION["mansel"]."/".$pid;
			$dest="./dokumente/".$dir."/kopf.".$typ[$imagesize[2]];
			$ok=chkdir($dir);
			move_uploaded_file($datei["bild"]["tmp_name"],"$dest");
			if (($imagesize[1] < $imagesize[0]) ) {
				$hoehe=ceil($imagesize[1]/$imagesize[0]*200);
				$breite=200;
			} else {
				$breite=ceil($imagesize[0]/$imagesize[1]*100);
				$hoehe=100;
			}
			if ($typ[$imagesize[2]]=="gif") {
				$image1 = imagecreate($breite,$hoehe);
			} else {
				$image1 = imagecreatetruecolor($breite,$hoehe);
			}
			$tue="\$image=imagecreatefrom".$typ[$imagesize[2]]."('$dest');";
			//echo "!$tue!<br>";
			eval($tue);
			imagecopyresized($image1, $image, 0,0, 0,0,$breite,$hoehe,$imagesize[0],$imagesize[1]);
			//echo "!imagecopyresized(".$image1.",".$image.",0,0,0,0,".$breite.",".$hoehe.",".$imagesize[0].",".$imagesize[1].")!";
			$tue="image".$typ[$imagesize[2]]."(\$image1,'$dest');";
			//echo "!$tue!<br>";
			eval($tue);
		}
		mkTelNummer($pid,"P",$tels);
		$sql0="update contacts set ".$query0."cp_employee=".$_SESSION["loginCRM"]." where cp_id=$pid";
		if($db->query($sql0)) {
			return $pid;			
		} else {
			return "unbekannt";
		}
	} else { return $fehler; };
}

/****************************************************
* insFaKont
* in: data = array
* out: id = int
* eine Auswahl Kontakte einer Firma zuordnen
*****************************************************/
function insFaKont($data) {
global $db;
	$fa=$data["fid"];
	foreach ($data["kontid"] as $row) {
		$sql="update contacts set cp_cv_id=".$fa." where cp_id=".$row;
		$rc=$db->query($sql);
	}
}

/****************************************************
* mknewPerson
* in:
* out: id = int
* Kundensatz erzeugen ( insert )
*****************************************************/
function mknewPerson($id) {
global $db;
	$newID=uniqid (rand());
	if (!$id) {$uid='null';} else {$uid=$id;};
	$sql="insert into contacts (cp_name,cp_employee) values ('$newID',$id)";
	$rc=$db->query($sql);
	if ($rc) {
		$sql="select cp_id from contacts where cp_name = '$newID'";
		$rs=$db->getAll($sql);
		if ($rs) {
			$id=$rs[0]["cp_id"];
		} else {
			$id=false;
		}
	} else {
		$id=false;
	}
return $id;
}

function leertplP (&$t,$fid,$msg,$tab,$suche=false,$Quelle="") {
global $laender,$cp_sonder;
		$t->set_file(array("pers1" => "personen".$tab.".tpl"));
		$t->set_var(array(
			Fld	=> "cp_title",
			JS      => "goFld();",
			color	=> "white",
			BgC	=> 0,
			Btn1 	=> "",
			Btn3 	=> "",
			Msg 	=> $msg,
			action  => "personen".$tab.".php",
			PID 	=> "",
			cpsel1  => "checked",
			cpsel2  => "",
			cpsel3  => "",
			cp_greeting	=> "",
			cp_title 	=> "",
			cp_givenname 	=> "",
			cp_name 	=> "",
			cp_street 	=> "",
			cp_country	=> "",
			cp_zipcode 	=> "",
			cp_city 	=> "",
			cp_phone1 	=> "",
			cp_phone2 	=> "",
			cp_fax 		=> "",
			cp_email 	=> "",
			cp_homepage 	=> "",
			cp_gebdatum	=> "",
			cp_beziehung	=> "",
			cp_abteilung	=> "",
			cp_position 	=> "",
			Firma 	=> "",
			FID 	=> ($suche)?$fid:"",
			FID1 	=> $fid,
			cp_stichwort1	=> "",
			cp_notes 	=> "",
			sond1	=> "",
			sond2	=> "",
			sond3	=> "",
			sond4	=> "",
			Quelle  => $Quelle,
			IMG	=> "",
			IMG_	=> "",
			employee => $_SESSION["loginCRM"],
			init    => $_SESSION["employee"]
			));
			$t->set_block("pers1","OwenerListe","Block2");
			$first[]=array("grpid"=>"","rechte"=>"w","grpname"=>"Alle");
			$first[]=array("grpid"=>$daten["OwenID"],"rechte"=>"w","grpname"=>"Pers&ouml;nlich");
			$user=array_merge($first,getGruppen());
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
				$t->parse("Block2","OwenerListe",true);
			}
			$t->set_block("pers1","sonder","Block3");
			if ($cp_sonder) while (list($key,$val) = each($cp_sonder)) {
				$t->set_var(array(
					sonder_id => $key,
					sonder_name => $val
				));
				$t->parse("Block3","sonder",true);
			}
}

function vartplP (&$t,$daten,$msg,$btn1,$btn2,$btn3,$fld,$bgcol,$fid,$tab) {
	global $laender,$cp_sonder;
		if (trim($daten["cp_grafik"])<>"") {
			$Image="<img src='dokumente/".$_SESSION["mansel"]."/".$daten["cp_id"]."/kopf.".$daten["cp_grafik"]."' ".$daten["size"].">";
		}
		$t->set_file(array("pers1" => "personen".$tab.".tpl"));
		$t->set_var(array(
			Fld	=> $fld,
			JS      => "goFld();",
			color	=> $bgcol,
			BgC	=>  $fid,
			Btn1 	=> $btn1,
			Btn3 	=> $btn3,
			Msg 	=> $msg,
			action	=> "personen".$tab.".php",
			PID 	=> $daten["cp_id"],
			cpsel1  => ($daten["cp_greeting"]=="Herr")?"checked":"",
			cpsel2  => ($daten["cp_greeting"]=="Frau")?"checked":"",
			cpsel3  => ($daten["cp_greeting"]<>"Herr" && $daten["cp_greeting"]<>"Frau")?"checked":"",
			cp_greeting	=> ($daten["cp_greeting"]!="Herr" && $daten["cp_greeting"]!="Frau")?$daten["cp_greeting"]:"",
			cp_title 	=> $daten["cp_title"],
			cp_givenname 	=> $daten["cp_givenname"],
			cp_name 	=> $daten["cp_name"],
			cp_street 	=> $daten["cp_street"],
			cp_country	=> $daten["cp_country"],
			cp_zipcode 	=> $daten["cp_zipcode"],
			cp_city 	=> $daten["cp_city"],
			cp_phone1 	=> $daten["cp_phone1"],
			cp_phone2 	=> $daten["cp_phone2"],
			cp_fax 		=> $daten["cp_fax"],
			cp_email 	=> $daten["cp_email"],
			cp_homepage 	=> $daten["cp_homepage"],
			cp_gebdatum	=> ($daten["cp_gebdatum"])?db2date($daten["cp_gebdatum"]):"",
			cp_beziehung	=> $daten["cp_beziehung"],
			cp_abteilung	=> $daten["cp_abteilung"],
			cp_position 	=> $daten["cp_position"],
			Firma 	=> $daten["Firma"],
			FID 	=> $daten["cp_cv_id"],
			FID1 	=> $fid,
			cp_stichwort1	=> $daten["cp_stichwort1"],
			cp_notes 	=> $daten["cp_notes"],
			Quelle  => $daten["Quelle"],
			IMG	=> $Image,
			IMG_	=> $daten["cp_grafik"],
			init    => ($daten["cp_employee"])?$daten["cp_employee"]:"ERP",
			employee => $_SESSION["loginCRM"]
			));
			$t->set_block("pers1","OwenerListe","Block2");
			if ($daten["cp_employee"]==$_SESSION["loginCRM"]) {
				$first[]=array("grpid"=>"","rechte"=>"w","grpname"=>"Alle");
				$first[]=array("grpid"=>$daten["cp_employee"],"rechte"=>"w","grpname"=>"Pers&ouml;nlich");
				$grp=getGruppen();
				if ($grp) {	$user=array_merge($first,$grp); }
				else { $user=$first; };
				$selectOwen=$daten["cp_owener"];
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
					$t->parse("Block2","OwenerListe",true);
				}
			} else {
				$t->set_var(array(
					grpid => $daten["cp_owener"],
					Gsel => "selected",
					Gname => ($daten["cp_owener"])?getOneGrp($daten["cp_owener"]):"&ouml;ffentlich",
				));
				$t->parse("Block2","OwenerListe",true);
			}
			$t->set_block("pers1","sonder","Block3");
			if ($cp_sonder) while (list($key,$val) = each($cp_sonder)) {
				$t->set_var(array(
					sonder_sel => ($daten["cp_sonder"] & $key)?"checked":"",
					sonder_id => $key,
					sonder_name => $val
				));
				$t->parse("Block3","sonder",true);
			}
}

?>
