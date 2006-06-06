<?
session_start();
$version='$Id$';

require_once "DB.php";
require_once "inc/conf.php";
require_once "db.php";

/****************************************************
* decode_password
* in: string
* out: string
* dekodiert Perl-UU-kodierte Passwort-Strings
* sd@b-comp.de (bug #171)
*****************************************************/
function decode_password($string) { 
  $offset = 0;
  $register = 0;
  $result = '';

  if (strlen($string) == 0) return ''; 

  if ((ord($string{0}) & 0xf0) != 0x20) {
    print "password decoding error!\n";
    return null;
  }
  // oder doch vielleicht auf Perl zurückgreifen:
  //if (ord($string{0}) & 0xf0) != 0x20) return exec("echo print  pack 'u', ".$string." | /usr/bin/perl -") ;
  $length = ord($string{0}) & 0xf;
  for ($index = 0; $index < strlen($string); $index) {
    $x = (int)(ord($string{$index + 1}) - 32);
    switch ($offset) {
    case 0:
      $register = $x << 2;
      $offset = 6;
      break;
    case 2:
      $y = $register | $x;
      $result .= chr($y);
      $offset = 0;
      break;
    case 4:
      $y = $register | ($x >> 2);
      $result .= chr($y);
      $register = ($x << 6) & 0xff;
      $offset = 2;
      break;
    case 6:
      $y = $register | ($x >> 4);
      $result .= chr($y);
      $register = ($x << 4) & 0xff;
      $offset = 4;
    }
    if (strlen($result) == $length) break;
  }
  return $result;
}

/****************************************************
* uudecode
* in: string
* out: string
* dekodiert Perl-UU-kodierte Passwort-Strings
* http://de3.php.net/base64_decode (bug #171)
*****************************************************/
function uudecode($encode) {
  $b64chars="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";

  $encode = preg_replace("/^./m","",$encode);
  $encode = preg_replace("/\n/m","",$encode);
  for($i=0; $i<strlen($encode); $i++) {
    if ($encode[$i] == '')
      $encode[$i] = ' ';
    $encode[$i] = $b64chars[ord($encode[$i])-32];
  }
   
  while(strlen($encode) % 4)
    $encode .= "=";

  return base64_decode($encode);
}


/****************************************************
* db2date
* in: Datum = String
* out: Datum = String
* wandelt ein db-Datum in ein "normales" Datum um
*****************************************************/
 function db2date($datum) {
     $D=split("-",$datum);
     $datum=sprintf ("%02d.%02d.%04d",$D[2],$D[1],$D[0]);
     return $datum;
  }

/****************************************************
* date2db
* in: Datum = String
* out: Datum = String
* wandelt ein "normales" Datum in ein db-Datum um
*****************************************************/
  function date2db($Datum) {
     $Datum=ereg_replace("/","\.",$Datum);
     $Datum=ereg_replace("-","\.",$Datum);
     $Datum=ereg_replace(",","\.",$Datum);
     $Datum=ereg_replace(" ","\.",$Datum);
     $D=split("\.",$Datum);
	 if (count($D)==1) { $D[1]=date("m"); };
	 if (count($D)==2 || $D[2]=="") { $D[2]=date("Y"); };
	 if ($D[2]<38) { $D[2]=2000+$D[2]; }
	 else if ($D[2]>=38 && $D[2]<100) { $D[2]=1900+$D[2]; };
     $Datum=sprintf ("%04d-%02d-%02d",$D[2],$D[1],$D[0]);
     return $Datum;
  }

/****************************************************
* anmelden
* in: name,pwd = String
* out: rs = integer
* prüft ob name und kennwort in db sind und liefer die UserID
*****************************************************/
function anmelden($name) {
global $ERPNAME,$showErr;
	ini_set("gc_maxlifetime","3600");
	$tmp = @file_get_contents("../".$ERPNAME."/users/".$_GET["login"].".conf");
	preg_match("/dbname => '(.+)'/",$tmp,$hits);
	$dbname=$hits[1];
	preg_match("/dbpasswd => '(.+)'/",$tmp,$hits);
        if ($hits[1]) {
		$dbpasswd=uudecode(stripslashes($hits[1]));
	} else {
        	$dbpasswd="";
	};
	preg_match("/dbuser => '(.+)'/",$tmp,$hits);
	$dbuser=$hits[1];
	preg_match("/dbhost => '(.+)'/",$tmp,$hits);
	$dbhost=$hits[1];
	if (!$dbhost) $dbhost="localhost";
	if ($dbpasswd) {
        	$dns=$dbuser.":".$dbpasswd."@".$dbhost."/".$dbname;
	} else {
        	$dns=$dbuser."@".$dbhost."/".$dbname;
	};
	chkdir($dbname);
	$_SESSION["dns"]=$dns;
	$_SESSION["db"]=new myDB($_SESSION["dns"],$showErr);
   	$_SESSION["employee"]=$_GET["login"];
   	$_SESSION["password"]=$_GET["password"];
	$_SESSION["mansel"]=$dbname;
	$_SESSION["dbname"]=$dbname;
	$_SESSION["dbhost"]=$dbhost;
	$_SESSION["dbuser"]=$dbuser;
	$_SESSION["dbpasswd"]=$dbpasswd;		
	$sql="select * from employee where login='$name'";
	$rs=$_SESSION["db"]->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		if ($rs) {
			$tmp=$rs[0];
			$_SESSION["termbegin"]=(($tmp["termbegin"]>=0)?$tmp["termbegin"]:8);
			$_SESSION["termend"]=($tmp["termend"])?$tmp["termend"]:19;
			$_SESSION["Pre"]=$tmp["pre"];
			$_SESSION["loginCRM"]=$tmp["id"];
			$_SESSION["lang"]=$tmp["countrycode"]; //"de";
			$_SESSION["kdview"]=$tmp["kdview"];
			return true;
		} else {
			return false;
		}
	}
}


function chkVer() {
global $VERSION;
	$db=$_SESSION["db"];
	$tmp=$db->showErr;
	$db->showErr=false;
	$rc=$db->getAll("select * from crm order by datum desc");
	if (!$rc || $rc[0]["version"]=="" || $rc[0]["version"]==false) {
		echo "CRM-Tabellen sind nicht (vollst&auml;ndig) installiert"; 
		flush(); 
		require("inc/install.php");
		exit;
	} else if($rc[0]["version"]<>$VERSION) {
		echo "Istversion: ".$rc[0]["version"]." Sollversion: ".$VERSION."<br>";
		require("inc/update.php");
		//require("inc/update.php$von=".$rc[0]["version"]."&auf=$VERSION");
		exit;
	} else {
		return true;
		//Alles ok
	}
}

/****************************************************
* chkdir
* in: dir = String
* out: boolean
* prüft, ob Verzeichnis besteht und legt es bei Bedarf an
*****************************************************/
function chkdir($dir) {
	if (file_exists("dokumente/".$dir)) {	
		return true;
	} else {
		$ok=mkdir("dokumente/".$dir);
		return $ok;
	}
}

/****************************************************
* liesdir
* in: dir = String
* out: files = Array
* liest die Dateien eines Verzeichnisses
*****************************************************/
function liesdir($dir) {
	$dir="./dokumente/$dir/";
	if (!file_exists($dir)) return false;
	$cdir = dir($dir);
	while ($entry = $cdir->read()) {
		if (!is_dir($entry)) {
			$files[]=array("size"=>filesize($dir.$entry),"date"=>date("d.m.Y H:i:s",filemtime($dir.$entry)),"name"=>$entry);
		}
	}
	return $files;
}
function toUpper($text) {
$arrayLower=array('ç'
   ,'â','ã','à','á','ä'
   ,'é','è','ê','ë'
   ,'í','ì','î','ï'
   ,'ó','ò','ô','õ','ö'
   ,'ú','ù','û','ü');
  
   $arrayUpper=array('Ç'
   ,'Â','Ã','Á','À','Ä'
   ,'É','È','Ê','Ë'
   ,'Í','Ì','Î','Ï'
   ,'Ó','Ò','Õ','Ô','Ö'
   ,'Ú','Ù','Û','Ü');
   $text=strtoupper($text);
   $text=str_replace($arrayLower, $arrayUpper, $text);
   return $text;
}

/****************************************************
* chkFld
* in: val = mixed, empty = boolean, rule = int
* out: ok = boolean
* Daten nach Regeln prüfen
*****************************************************/
function chkFld(&$val,$empty,$rule,$len) {
	if ($empty===0) $leer="|^$";
	switch ($rule) {
		case 1 : $ok=ereg("[[:alnum:]\xE4\xF6\xFC\xC4\xD6\xDC\xDF]+$leer",$val); // String
			 if (strlen($val)>$len && $len>0) $val=substr($val,0,$len);
			 break;
		case 2 : if ($empty===0 && empty($val)) { $ok=true; $val="null"; }
			 else {$ok=ereg("^[0-9]{4,5}$",$val);}; // Plz
			 if (strlen($val)>$len && $len>0) $val=substr($val,0,$len);
			 break;
		case 3 : if ($empty===0 && empty($val)) { $ok=true; $val=""; }
			 else { $ok=ereg("^070[01][ A-Z]{6,9}$", $val) || ereg("^\+?[0-9\(\)/ \-]+$", $val); }; //Telefon
			 //else { $ok=eregi("^([+][ ]?[1-9][0-9][ ]?(\(0\))?[ ]?|[(]?[0][ ]?)[0-9]{2,4}[-)/ ]*[ ]?[1-9][0-9 -]{2,16}$", $val); }; //Telefon
			 if (strlen($val)>$len && $len>0) $val=substr($val,0,$len);
			 break;
		case 4 : $ok=ereg("^(http(s)?://)?([a-zA-Z0-9\-]*\.)?[a-zA-Z0-9\-]{2,}(\.[a-zA-Z0-9\-]{2,})?(\.[a-zA-Z0-9\-]{2,})(/.*)?$".$leer,$val); // www
			 if (strlen($val)>$len && $len>0) $val=substr($val,0,$len);
			 break;
		case 5 : $ok=ereg("^([A-Za-z_0-9]+)([A-Za-z_0-9\.\-]+)([A-Za-z_0-9]*)\@([a-zA-Z0-9][a-zA-Z0-9._-]*\\.)*[a-zA-Z0-9][a-zA-Z0-9._-]*\\.[a-zA-Z]{2,5}$".$leer,$val); //eMail
			 if (strlen($val)>$len && $len>0) $val=substr($val,0,$len);
			 break;
		case 6 : if ($empty===0 && empty($val)) { $ok=true; $val="null"; }
				 else {$ok=ereg("^[0-9]+$",$val); } // Ganzzahlen
				break;
		case 7 : if ($empty===0 && empty($val)) { $ok=true; $val="0000-00-00";} // Datum
			 else {
			  	$ok=ereg("^[0-3][0-9]\.[0-1][0-9]\.([0-9][0-9]|[012][0-9][0-9][0-9])$",$val);
				$t=split("\.",$val);
				if ($ok) $val=$t[2]."-".$t[1]."-".$t[0];
			 }
			 break;
		case 8 : $val=toUpper($val); $ok=ereg("[[:alnum:]\xE4\xF6\xFC\xC4\xD6\xDC\xDF]+$leer",$val); // String
			 if (strlen($val)>$len && $len>0) $val=substr($val,0,$len);
			 break;
		default : $ok=true;
	}
	return $ok;
}
	 
function getVersiondb() {
global $db;
	$rs=$db->getAll("select * from crm order by datum desc limit 1");
	if (!$rs[0]["version"]) return "V n.n.n";
	return $rs[0]["version"];
}



function berechtigung($tab="") {
	$grp=getGrp($_SESSION["loginCRM"]);
	$rechte="( ".$tab."owener=".$_SESSION["loginCRM"]." or ".$tab."owener is null";
	if ($grp) $rechte.=" or ".$tab."owener in $grp";
	return $rechte.")";
}


function chkAnzahl(&$data,&$anzahl) {	
global $listLimit;
	if ($data) { $cnt=count($data);
	} else { $cnt=0; }
	if (($cnt+$anzahl)>$listLimit) {
		$anzahl=0;
		return false;
	 } else {
		$anzahl+=$cnt;
		return true;
	}
}
/****************************************************
* getLeads
* out: array
* Leadsquellen holen
*****************************************************/
function getLeads() {
global $db;
	$sql="select * from leads order by lead";
	$rs=$db->getAll($sql);
	$tmp[]=array("id"=>"","lead"=>"unbekannt");
	$rs=array_merge($tmp,$rs);
	return $rs;
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
	$leer=array(array("id"=>"","description"=>"----------"));
	return array_merge($leer,$rs);
}
/****************************************************
* mkTelNummer
* in: id = int, tab = char, tels = array
* out: rs = int
* Telefonnummern genormt speichern
*****************************************************/
function mkTelNummer($id,$tab,$tels,$delete=true) {
global $db;
	if ($delete) {
		$sql="delete from telnr where id=$id and tabelle='$tab'";
		$rs=$db->query($sql);
	}
	foreach($tels as $tel) {
		$tel=strtr($tel,array(" "=>"","-"=>"","/"=>"","("=>"",")"=>""));
		if (substr($tel,0,1)=="+") $tel=substr($tel,3);
		if (substr($tel,0,1)=="0") $tel=substr($tel,1);
		if (trim($tel)<>"") {
			$sql="insert into telnr (id,tabelle,nummer) values (%d,'%s','%s')";
			$sql=sprintf($sql,$id,$tab,$tel);
			$rs=$db->query($sql);
		}
	}
}

function getAnruf($nr) {
global $db;
	$nun = date("H:i");
	$name="_0;$nun $nr unbekannt";
	$sql="select * from telnr where nummer = '$nr'";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		$i=1;
		$more="";
		while (count($rs)==0 && $i<5) {
			$sql="select * from telnr where nummer like '".substr($nr,0,-$i)."%'";
			$rs=$db->getAll($sql);
			$i++;
			$more="?";
		};
		if ($i<5) {
			if ($rs[0]["tabelle"]=="P") {
				$sql="select cp_name as name2,cp_givenname as name1 from contacts where cp_id=".$rs[0]["id"];
			} else if ($rs[0]["tabelle"]=="S") {
				$sql="select shipto_name as name1,'' as name2 from shipto where transid=".$rs[0]["id"];
			} else if ($rs[0]["tabelle"]=="C") {
				$sql="select name as name1,'' as name2 from customer where id=".$rs[0]["id"];
			} else if ($rs[0]["tabelle"]=="V") {
				$sql="select name as name1,'' as name2 from vendor where id=".$rs[0]["id"];
			} else if ($rs[0]["tabelle"]=="E") {
				$sql="select name as name1,'' as name2 from employee where id=".$rs[0]["id"];
			} else {
				$name="_0;".$nun." ".$nr." unbekannt"; return $name;
			}
			$rs1=$db->getAll($sql);
			$name=$rs[0]["tabelle"].$rs[0]["id"].$nun." ".$rs1[0]["name1"]." ".$rs1[0]["name2"].$more;
		} else {
			$name="_00000".$nun." ".$nr." unbekannt";
		}
	}
	return $name;
}

function getVertretung($user) {
global $db;
	$sql="select workphone from employee where vertreter=(select id from employee where workphone='$user')";
	$rs=$db->getAll($sql);
	if (count($rs)>0) { return $rs; }
	else { return false; };
}

function getVorlagen() {
global $db;
	$sql="select * from docvorlage";
	$rs=$db->getAll($sql);
	if (count($rs)>0) { return $rs; }
	else { return false; };
}

function getDocVorlage($did) {
global $db;
	if (!$did) return false;
	$sql="select * from docvorlage where docid=$did";
	$rs1=$db->getAll($sql);
	if (!$rs1[0]) return false;
	$sql="select * from docfelder where docid=$did order by position";
	$rs2=$db->getAll($sql);
	$rs["document"]=$rs1[0];
	$rs["felder"]=$rs2;
	if (count($rs)>0) { return $rs; }
	else { return false; };

}

function getDOCvar($did) {
global $db;
	$sql="select * from docvorlage where docid=$did";
	$rs1=$db->getAll($sql);
	return $rs1[0];
}

function updDocFld($data) {
global $db;
	$sql="update docfelder set feldname='".$data["feldname"]."', platzhalter='".$data["platzhalter"];
	$sql.="', beschreibung='".$data["beschreibung"]."',laenge=".$data["laenge"].",zeichen='".$data["zeichen"];
	$sql.="',position=".$data["position"].",docid=".$data["docid"]." where fid=".$data["fid"];
	$rs=$db->query($sql);
	if(!$rs) {
		return false;
	}
	return $data["fid"];
}

function insDocFld($data) {
	$fid=mknewDocFeld();
	if (!$fid) return false;
	$data["fid"]=$fid;
	$fid=updDocFld($data);
	return $fid;
}

function delDocFld($data) {
global $db;
	$sql="delete from docfelder where fid=".$data["fid"];
	$rs=$db->query($sql);
}

/****************************************************
* mknewDocFeld
* in:
* out: id = int
* Dokumentsatz erzeugen ( insert )
*****************************************************/
function mknewDocFeld() {
global $db;
	$newID=uniqid (rand());
	$sql="insert into docfelder (beschreibung) values ('$newID')";
	$rc=$db->query($sql);
	if ($rc) {
		$sql="select fid from docfelder where beschreibung = '$newID'";
		$rs=$db->getAll($sql);
		if ($rs) {
			$id=$rs[0]["fid"];
		} else {
			$id=false;
		}
	} else {
		$id=false;
	}
	return $id;
}

/****************************************************
* mknewDocVorlage
* in:
* out: id = int
* Dokumentsatz erzeugen ( insert )
*****************************************************/
function mknewDocVorlage() {
global $db;
	$newID=uniqid (rand());
	$sql="insert into docvorlage (vorlage) values ('$newID')";
	$rc=$db->query($sql);
	if ($rc) {
		$sql="select docid from docvorlage where vorlage = '$newID'";
		$rs=$db->getAll($sql);
		if ($rs) {
			$id=$rs[0]["docid"];
		} else {
			$id=false;
		}
	} else {
		$id=false;
	}
	return $id;
}

function delDocVorlage($data) {
global $db;
	$sql="delete from docfelder where docid=".$data["did"];
	$rs=$db->query($sql);
	if ($rs) {
		$sql="delete from docvorlage where docid=".$data["did"];
		$rs=$db->query($sql);
	}
}

function saveDocVorlage($data,$files) {
global $db;
	if (!$data["did"]) {
		$data["did"]=mknewDocVorlage();
		if (!$data["did"]) { return false; };
	}
	if ($files["file"]["name"]) {
		exec("cp ".$files["file"]["tmp_name"]." ./vorlage/".$files["file"]["name"]);
		$file=$files["file"]["name"];
	} else {
		$file=$data["file_"];
	}
	if (!$data["vorlage"]) $data["vorlage"]="Kein Titel ".datum("d.m.Y");
	$sql="update docvorlage set vorlage='".$data["vorlage"]."', beschreibung='".$data["beschreibung"]."', file='".$file."', applikation='".$data["applikation"]."' where docid=".$data["did"];
	$rs=$db->query($sql);
	if(!$rs) {
		return false;
	} else {
		return $data["did"];
	}
}

function shopartikel() {
global $db;
	$sql="SELECT t.rate,PG.partsgroup,P.partnumber,P.description,P.notes,P.sellprice,P.priceupdate FROM ";
	$sql.="chart c left join partstax pt on pt.chart_id = c.id,";
	$sql.="tax t, parts P left join partsgroup PG on PG.id=P.partsgroup_id ";
	$sql.="where c.category='I' AND t.taxnumber=c.accno  and pt.parts_id = P.id and P.shop=1";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		return $rs;
	}
}

function getAllArtikel($art="A") {
global $db;
	if ($art=="A") { $where=""; }
	else if ($art=="W") { $where="where inventory_accno_id is not null and expense_accno_id is not null"; }
	else if ($art=="D") { $where="where inventory_accno_id is null and expense_accno_id is not null"; }
	else if ($art=="E") { $where="where inventory_accno_id is null and expense_accno_id is null"; };
	$sql="SELECT * from parts $where order by description";
	$rs=$db->getAll($sql);
	if(!$rs) {
		return false;
	} else {
		return $rs;
	}
}

function getGrp($usrid,$inkluid=false){
global $db;
	$sql="select distinct(grpid) from grpusr where usrid=$usrid";
	$rs=$db->getAll($sql);
	if(!$rs) {
		if ($inkluid) { return "($usrid)"; }
		else { $data=false; };
	} else {
		if ($rs) {
		   $data="(";
			foreach($rs as $row) {
				$data.=$row["grpid"].",";
			};
			if ($inkluid) { $data.="$usrid)"; }
			else {$data=substr($data,0,-1).")";};
		} else {
			if ($inkluid) { $data.="($usrid)"; }
			else { $data=false; };
		}
		return $data;
	}
};

function firstkw($jahr) {
	$erster = mktime(0,0,0,1,1,$jahr);
	$wtag = date('w',$erster);
	if ($wtag <= 4) {
		// Donnerstag oder kleiner: auf den Montag zurückrechnen.
		$montag = mktime(0,0,0,1,1-($wtag-1),$jahr);
	} else {
		// auf den Montag nach vorne rechnen.
		$montag = mktime(0,0,0,1,1+(7-$wtag+1),$jahr);
	}
	return $montag;
}

function mondaykw($kw,$jahr) {
	$firstmonday = firstkw($jahr);
	$mon_monat = date('m',$firstmonday);
	$mon_jahr = date('Y',$firstmonday);
	$mon_tage = date('d',$firstmonday);
	$tage = ($kw-1)*7;
	$mondaykw = mktime(0,0,0,$mon_monat,$mon_tage+$tage,$mon_jahr);
	return $mondaykw;

}
function clearCSVData() {
global $db;
	return $db->query("delete from tempcsvdata where uid = '".$_SESSION["loginCRM"]."'");
}
function insertCSVData($data) {
global $db;
	$tmpstr="";
	foreach ($data as $row) {
		$tmpstr.=$row.";";
	};
	$sql="insert into tempcsvdata (uid,csvdaten) values (";
	$sql.="'".$_SESSION["loginCRM"]."','".substr($tmpstr,0,-1)."')";
	$rc=$db->query($sql);
	return $rc;
}

require_once "inc/login".$_SESSION["loginok"].".php";
?>
