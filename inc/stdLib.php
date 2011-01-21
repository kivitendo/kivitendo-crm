<?php
session_start();

$inclpa=ini_get('include_path');
ini_set('include_path',$inclpa.":../:./crmajax:./inc:../inc");

require_once "conf.php";
require_once "version.php";
require_once $dbmodul."db.php";
//if ($DEBUG && !$_SESSION["DEBUG"]) {
if (!$_SESSION["db"] || !$_SESSION["cookie"] ||
    ($_SESSION["cookie"] && !$_COOKIE[$_SESSION["cookie"]]) ) {
    require_once "login.php";
};


/****************************************************
* db2date
* in: Datum = String
* out: Datum = String
* wandelt ein db-Datum in ein "normales" Datum um
*****************************************************/
function db2date($datum) {
   if (strpos($datum,"-")) {
       $D=explode("-",$datum);
       $datum=sprintf ("%02d.%02d.%04d",$D[2],$D[1],$D[0]);
   }
   return $datum;
}

/****************************************************
* date2db
* in: Datum = String
* out: Datum = String
* wandelt ein "normales" Datum in ein db-Datum um
*****************************************************/
function date2db($Datum) {
   if (empty($Datum)) return date("Y-m-d");
   $repl = array ("/","-",","," ");
   $Datum = str_replace($repl,".",$Datum);
   $D=explode(".",$Datum);
   if (count($D)==1) { $D[1]=date("m"); };
   if (count($D)==2 || $D[2]=="") { $D[2]=date("Y"); };
   if ($D[2]<38) { $D[2]=2000+$D[2]; }
   else if ($D[2]>=38 && $D[2]<100) { $D[2]=1900+$D[2]; };
   $Datum=sprintf ("%04d-%02d-%02d",$D[2],$D[1],$D[0]);
   return $Datum;
}

function translate($word,$file) {
    include("locale/$file.".$_SESSION['lang']);
    if ($texts[$word]) {
            return $texts[$word];
    } else {
        return $word;
    }
}
function authuser($dbhost,$dbport,$dbuser,$dbpasswd,$dbname,$cookie) {
    global $ERPNAME;
    $db=new myDB($dbhost,$dbuser,$dbpasswd,$dbname,$dbport);
    $sql="select sc.session_id,u.id from auth.session_content sc left join auth.\"user\" u on ";
    $sql.="('--- ' || u.login || E'\\n')=sc.sess_value left join auth.session s on s.id=sc.session_id ";
    //$sql.="u.login=sc.sess_value left join auth.session s on s.id=sc.session_id ";
    $sql.="where session_id = '$cookie' and sc.sess_key='login'";// order by s.mtime desc";
    $rs=$db->getAll($sql,"authuser_0");
    //echo $sql.count($rs);
    if (!$rs) return false;
    $stmp="";
    if (count($rs)>1) {
        header("location:../../$ERPNAME/login.pl?action=logout");
        /*foreach($rs as $row) {
            $stmp.=$row["session_id"].",";
        }
        $sql1="delete from session where id in (".substr($stmp,-1).")";    
        $sql2="delete from session_content where session_id in (".substr($stmp,-1).")";    
        $db->query($sql1,"authuser_A");
        $db->query($sql2,"authuser_B");
        $sql3="insert into session ";*/
    }
    $sql="select * from auth.\"user\" where id=".$rs[0]["id"];
    $rs1=$db->getAll($sql,"authuser_1");
    if (!$rs1) return false;
    $auth=array();
    $auth["login"]=$rs1[0]["login"];
    $sql="select * from auth.user_config where user_id=".$rs[0]["id"];
    $rs1=$db->getAll($sql,"authuser_2");
    $keys=array("dbname","dbpasswd","dbhost","dbport","dbuser","countrycode","stylesheet",
                "company","address","vclimit","signature","email","tel");
    foreach ($rs1 as $row) {
        if (in_array($row["cfg_key"],$keys)) {
            $auth[$row["cfg_key"]]=$row["cfg_value"];
        }
    }
    $auth["dbhost"]=(!$auth["dbhost"])?"localhost":$auth["dbhost"];
    $auth["dbport"]=(!$auth["dbport"])?"5432":$auth["dbport"];
    $auth["mansel"]=$auth["dbname"];
    $auth["employee"]=$auth["login"];
    $auth["lang"]=$auth["countrycode"];
    $auth["stylesheet"]=$auth["stylesheet"];
    $sql = "SELECT granted from auth.group_rights G where G.right = 'sales_all_edit' ";
    $sql.= "and G.group_id in (select group_id from auth.user_group where user_id = ".$rs[0]["id"].")";
    $rs=$db->getAll($sql,"authuser_3");
    $auth["sales_edit_all"] = 'f';
    if ($rs) {
        foreach ($rs as $row) {
             if ($row["granted"] == 't') {
                   $auth["sales_edit_all"] = 't';
                   break;
              }
         }
    }
    $sql="update auth.session set mtime = '".date("Y-M-d H:i:s.100001")."' where id = '".$rs[0]["session_id"]."'"; 
    $db->query($sql,"authuser_3");
    return $auth;
}

/****************************************************
* anmelden
* in: name,pwd = String
* out: rs = integer
* prüft ob name und kennwort in db sind und liefer die UserID
*****************************************************/
function anmelden() {
global $ERPNAME;
    ini_set("gc_maxlifetime","3600");
    $tmp = @file_get_contents("../".$ERPNAME."/config/authentication.pl");
    preg_match("/'db'[ ]*=> '(.+)'/",$tmp,$hits);
    $dbname=$hits[1];
    preg_match("/'password'[ ]*=> '(.+)'/",$tmp,$hits);
    $dbpasswd=$hits[1];
    preg_match("/'user'[ ]*=> '(.+)'/",$tmp,$hits);
    $dbuser=$hits[1];
    preg_match("/'host'[ ]*=> '(.+)'/",$tmp,$hits);
    $dbhost=($hits[1])?$hits[1]:"localhost";
    preg_match("/'port'[ ]*=> (.+)/",$tmp,$hits);
    $dbport=($hits[1])?$hits[1]:"5432";
     preg_match("/[ ]*\\\$self->\{cookie_name\}[ ]*=[ ]*'(.+)'/",$tmp,$hits);
    $cookiename=$hits[1];
    if (!$cookiename) $cookiename='lx_office_erp_session_id';
    $cookie=$_COOKIE[$cookiename];
    if (!$cookie) header("location: ups.html");
    $auth=authuser($dbhost,$dbport,$dbuser,$dbpasswd,$dbname,$cookie);
    if (!$auth) { return false; };
    chkdir($auth["dbname"]);
    $_SESSION = $auth;
    $_SESSION["sessid"]=$cookie;
    $_SESSION["cookie"]=$cookiename;
    $_SESSION["db"]=new myDB($_SESSION["dbhost"],$_SESSION["dbuser"],$_SESSION["dbpasswd"],$_SESSION["dbname"],$_SESSION["dbport"]);
    $_SESSION["authcookie"]=$authcookie;
    $sql="select * from employee where login='".$_SESSION["login"]."'";
    $rs=$_SESSION["db"]->getAll($sql);
    if(!$rs) {
        return false;
    } else {
    //    if ($rs) { //ist schon oben abgefangen jb 09.12.09
       $tmp = @file_get_contents("../".$ERPNAME."/config/lx-erp.conf");
       preg_match("/dbcharset[\s]*=[\s]*'(.+)';/",$tmp,$hits);
       $charset=$hits[1];
       $_SESSION["charset"]=$charset;
       $tmp=$rs[0];
       $_SESSION["termbegin"]=(($tmp["termbegin"]>=0)?$tmp["termbegin"]:8);
       $_SESSION["termend"]=($tmp["termend"])?$tmp["termend"]:19;
       $_SESSION["termseq"]=($tmp["termseq"])?$tmp["termseq"]:30;
       $_SESSION["Pre"]=$tmp["pre"];
       $_SESSION["preon"]=$tmp["preon"];
       $_SESSION["interv"]=($tmp["interv"]>0)?$tmp["interv"]:60;
       $_SESSION["loginCRM"]=$tmp["id"];
       $_SESSION["kdview"]=$tmp["kdview"];
       $sql="select * from defaults";
       $rs=$_SESSION["db"]->getAll($sql);
       $_SESSION["ERPver"]=$rs[0]["version"];
       /*$_SESSION["countries"] = array(array('',''));
       $sql="select id,country from countries";
       $rs=$_SESSION["db"]->getAssoc($sql);
       $_SESSION["countries"] = $rs;
       $_SESSION["countries"] = array(0=>'') + $_SESSION["countries"];    //unshift_array mit beibehaltung der schlüssel
      */
   return true;
/*        } else {
            return false;
        }*/
    }
}


function chkVer() {
global $VERSION;
    $db=$_SESSION["db"];
    $rc=$db->getAll("select * from crm order by datum desc");
    if (!$rc || $rc[0]["version"]=="" || $rc[0]["version"]==false) {
        echo "CRM-Tabellen sind nicht (vollst&auml;ndig) installiert"; 
        flush(); 
        require("install.php");
        require("inc/update_neu.php");
        exit;
    } else if($rc[0]["version"]<>$VERSION) {
        echo "Istversion: ".$rc[0]["version"]." Sollversion: ".$VERSION."<br>";
        $GLOBALS["oldver"] = $rc[0]["version"];
        require("inc/update_neu.php");
        //require("update.php");
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
function chkdir($dir,$p="") {
    if (file_exists("$p./dokumente/".$_SESSION["mansel"]."/".$dir)) {    
        return true;
    } else {
        $dirs=explode("/",$dir);
        $tmp=$_SESSION["mansel"]."/";
        foreach ($dirs as $dir) {
            if (!file_exists("$p./dokumente/$tmp".$dir)) {
                $ok=mkdir("$p./dokumente/$tmp".$dir);
                if (!$ok) return false;
            };
            $tmp.=$dir."/";
        };
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
        case 1 : $ok = preg_match('/[a-z0-9äöüÄÖÜß ]'.$leer.'/i',$val,$hit); //String
                 if (strlen($val)>$len && $len>0) $val=substr($val,0,$len);
                 break;
        case 2 : if ($empty===0 && empty($val)) { $ok=true; $val=""; }
                 else {$ok=preg_match('/^[0-9]{4,5}$/',$val,$hit);}; // Plz
                 if (strlen($val)>$len && $len>0) $val=substr($val,0,$len);
                 break;
        case 3 : if ($empty===0 && empty($val)) { $ok=true; $val=""; }
                 else { $ok=preg_match('°^070[01][ A-Z]{6,9}$°', $val,$hit) || preg_match('°^\+?[0-9\(\)/ \-]+$°', $val,$hit); }; //Telefon
                 //else { $ok=eregi("^([+][ ]?[1-9][0-9][ ]?(\(0\))?[ ]?|[(]?[0][ ]?)[0-9]{2,4}[-)/ ]*[ ]?[1-9][0-9 -]{2,16}$", $val); }; //Telefon
                 if (strlen($val)>$len && $len>0) $val=substr($val,0,$len);
                 break;
        case 4 : $ok=preg_match('°^(http(s)?://)?([a-zA-Z0-9\-]*\.)?[a-zA-Z0-9\-]{2,}(\.[a-zA-Z0-9\-]{2,})?(\.[a-zA-Z0-9\-]{2,})(/.*)?$'.$leer.'°',$val,$hit); // www
                 if (strlen($val)>$len && $len>0) $val=substr($val,0,$len);
                 break;
        case 5 : $ok=preg_match('°^([a-z_0-9]+)([a-z_0-9\.\-]+)([a-z_0-9]*)\@([a-z0-9][a-z0-9._-]*\\.)*[a-z0-9][a-z0-9._-]*\\.[a-z]{2,5}$'.$leer.'°i',$val,$hit); //eMail
                 if (strlen($val)>$len && $len>0) $val=substr($val,0,$len);
                 break;
        case 6 : if ($empty===0 && empty($val)) { $ok=true; $val="null"; }
                 else {$ok=preg_match('/^[0-9]+$/',$val,$hit); } // Ganzzahlen
                 break;
        case 7 : if ($empty===0 && empty($val)) { $ok=true; $val="0000-00-00";} // Datum
                 else {
                     $ok=preg_match('/^[0-3][0-9]\.[0-1][0-9]\.([0-9][0-9]|[012][0-9][0-9][0-9])$/',$val,$hit);
                     $t=explode(".",$val);
                     if ($ok) $val=$t[2]."-".$t[1]."-".$t[0];
                 }
                 break;
        case 8 : $val=toUpper($val); $ok=preg_match("/[A-ZÄÖÜß]+$leer/",$val,$hit); // String
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
    $tmp[]=array("id"=>"","lead"=>".:unknown:.");
    if (!$rs)
        $rs = array();
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
* getBundesland
* out: array
* Bundesländer holen
*****************************************************/
function getBundesland($land) {
global $db;
    if ($land) {
        $sql="select * from bundesland where country = '$land' order by country,bundesland";
    } else {
        $sql="select * from bundesland order by country,bundesland";
    }
    $rs=$db->getAll($sql);
    return $rs;
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
        $tel=strtr($tel,array(" "=>"","-"=>"","/"=>"","\\"=>"","("=>"",")"=>""));
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
    $sql="update docfelder set feldname='".$data["feldname"]."', platzhalter='".strtoupper($data["platzhalter"]);
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
        $data["did"] = mknewDocVorlage();
        if (!$data["did"]) { return false; };
    }
    if ($files["file"]["name"]) {
        exec("cp ".$files["file"]["tmp_name"]." ./vorlage/".$files["file"]["name"]);
        $file = $files["file"]["name"];
    } else {
        $file = $data["file_"];
    }
    if (!$data["vorlage"]) $data["vorlage"]="Kein Titel ".datum("d.m.Y");
    $sql  = "update docvorlage set vorlage='".$data["vorlage"]."', beschreibung='".$data["beschreibung"];
    $sql .= "', file='".$file."', applikation='".$data["applikation"]."' where docid=".$data["did"];
    $rs = $db->query($sql);
    if(!$rs) {
        return false;
    } else {
        return $data["did"];
    }
}

function shopartikel() {
global $db;
    $sql  = "SELECT t.rate,PG.partsgroup,P.partnumber,P.description,P.notes,P.sellprice,P.priceupdate FROM ";
    $sql .= "chart c left join partstax pt on pt.chart_id = c.id,";
    $sql .= "tax t, parts P left join partsgroup PG on PG.id=P.partsgroup_id ";
    $sql .= "where c.category='I' AND t.taxnumber=c.accno  and pt.parts_id = P.id and P.shop=1";
    $rs = $db->getAll($sql);
    if(!$rs) {
        return false;
    } else {
        return $rs;
    }
}

function getAllArtikel($art="A") {
global $db;
    if ($art == "A") { $where = ""; }
    else if ($art == "W") { $where = "where inventory_accno_id is not null and expense_accno_id is not null"; }
    else if ($art == "D") { $where = "where inventory_accno_id is null and expense_accno_id is not null"; }
    else if ($art == "E") { $where = "where inventory_accno_id is null and expense_accno_id is null"; };
    $sql = "SELECT * from parts $where order by description";
    $rs = $db->getAll($sql);
    if(!$rs) {
        return false;
    } else {
        return $rs;
    }
}

function getGrp($usrid,$inkluid=false){
global $db;
    $sql = "select distinct(grpid) from grpusr where usrid=$usrid";
    $rs = $db->getAll($sql);
    if(!$rs) {
        if ($inkluid) { return "($usrid)"; }
        else { $data = false; };
    } else {
        if ($rs) {
           $data = "(";
            foreach($rs as $row) {
                $data .= $row["grpid"].",";
            };
            if ($inkluid) { $data .= "$usrid)"; }
            else {$data = substr($data,0,-1).")";};
        } else {
            if ($inkluid) { $data .= "($usrid)"; }
            else { $data = false; };
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


/**
 * Schreibt eine :-separierte Zeichenkette in die Tabelle tempcsvdata unter csvdaten
 * Die Einträge sind über die Benutzer-Session wieder auffindbar und werden immer 
 * wieder gelöscht. TODO: Soll das immer so sein?, dann vielleicht das Löschen in diese
 * Funktion integrieren?
 * Der erste Eintrag soll immer der INDEX sein, d.h. die entsprechenden Spaltennamen
 * TODO Ist es möglich den ersten Eintrag zu prüfen, um somit weniger Fehler beim 
 * Aufrufen zu erzeugen? jb 16.6.2009
 *
 * @param string $data 
 *
 * @return $rc TODO auf boolean setzen und korrekte Fehlerbehandlung umsetzen
 */
function insertCSVData($data,$id){
    global $db;
    $tmpstr = implode(":",$data);                                               // ANREDE:NAME:STRASSE (...)
    $sql = "insert into tempcsvdata (uid,csvdaten,id) values ('"
            . $_SESSION["loginCRM"] . "','" . $db->saveData($tmpstr) . "','" 
            . $db->saveData($id) . "')";                                        // saveData escapt die Zeichenkette
                                                                                // je nach DB-Modul (mdb2 db) (s.a. db.php)
    $rc=$db->query($sql);
    return $rc;     //Fehlerbehandlung? Wie sieht es aus mit exceptions? Müssen wir php4-kompatibel sein?
                    //Sollte eigentlich schon immer in den Funktionen direkt passieren 
                    // http://pear.php.net/manual/de/standars.errors.php
}

/**
 * Liest die entsprechenden Daten der :-separierte Zeichenkette aus der Tabelle tempcsvdata.
 * Ruft selber nur getAll auf und schiebt die Fehlermeldung dann nach oben. Frage:
 * Kann man das eleganter gestalten?
 * 
 * @return false, mixed
 */
function getCSVData(){
    global $db;
    $sql = "select * from tempcsvdata where uid = '" . $_SESSION["loginCRM"] .  "'";
    return $db->getAll($sql);   //liefert false bei misserfolg
}

/**
 * Liefert die aktuell in der CRM-Session gespeicherte IDs.
 * Klammert die CSV-Kopfzeile aus, da nur ids größer 0 aus der DB gelesen werden
 * False bei Misserfolg. Leider ansonsten Kopie von getCSVData. Ideen?
 * 
 * @return false, mixed
 */
function getCSVDataID(){
    global $db;
    $sql = "select id from tempcsvdata where uid = '" . $_SESSION["loginCRM"] .  "' and id > 0";
    return $db->getAll($sql);   //liefert false bei misserfolg
}
function startTime() {
    $zeitmessung = microtime();
    $zeittemp = explode(" ",$zeitmessung);
    $zeitmessung = $zeittemp[0]+$zeittemp[1];
    return $zeitmessung;
}    
function stopTime($start) {
    $stop = startTime();
    $zeit = $stop - $start;
    return substr($zeit,0,8);
}

/**
 * TODO: Listen in Templates füllen.
 *
 * @param object $t Template
 * @param string $tpl Templatename
 * @param string $pre Platzhalter / Blockname
 * @param array  $data Daten
 * @param string $id  Spaltenname für ID
 * @param string $text  Spaltenname für Text
 *
 * @return void
 */
function  doBlock(&$t,$tpl,$liste,$pre,$data,$id,$text,$selid) {
    $t->set_block($tpl,$liste,'Block'.$pre);
    if ($data) foreach ($data as $row) {
        if (is_array($text)) {
            $textval = '';
            foreach ($text as $txt) {
                $textval .= $row[$txt]." ";
            }
        } else {
            $textval =  $row[$text];
        }
        $x[$pre.'id']   = $row[$id];
        $x[$pre.'text'] = $textval;
        if ($selid) $x[$pre.'sel']  =  ($row[$id]==$selid)?"selected":"";
        $t->set_var($x);
        $t->parse('Block'.$pre,$liste,true);
    }
}

/**
 * TODO: short description.
 * 
 * @return TODO
 */
function getLanguage() {
    global $db;
    $sql = "select id,description from language";
    return $db->getAll($sql);
}
function nextNumber($number) {
    global $db;
    $db->begin();
    $sql = "select $number from defaults";
    $rs = $db->getOne($sql);
    preg_match("/([^\d]*)([\d]+)([^\d]*)/",$rs[$number],$hit);
    $nr = $hit[1].($hit[2]+1).$hit[3];
    $sql = "update defaults set $number = '$nr'";
    $rc = $db->query($sql,"nextnumber");
    if (!$rc) {
        $db->rollback();
        return false;
    } else {
        $db->commit();
        return $nr;
    }
}
require_once "login".$_SESSION["loginok"].".php";
if ($_SESSION["xajax"]) {
	define("XajaxVer","");
	define("XajaxPath","./crmajax/xajax");
} else {
	define("XajaxVer","05");
	define("XajaxPath","./crmajax/");
}

?>
