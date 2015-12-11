<?php
session_start();
/*
--Das kommt in den Livebetrieb
CREATE SEQUENCE extraid
    INCREMENT BY 1
    MAXVALUE 2147483647
    NO MINVALUE
    CACHE 1;
create table extra_felder (
id	 integer DEFAULT nextval('extraid'::text) NOT NULL,
owner	 char(10),	-> [CVP]id
fkey	 text,		-> Feldname
fval	 text		-> Wert zum Feld
);
CREATE INDEX extrafld_key ON extra_felder USING btree (owner);
*/
require_once("inc/stdLib.php");
include("inc/template.inc");
$menu = $_SESSION['menu'];
$head = mkHeader();
$owner=($_GET["owner"])?$_GET["owner"]:$_POST["owner"];

function suchFelder($data) {
    $tab = substr($data['owner'],0,1);
    foreach ($data as $key=>$val) { 
        if ($key == "suche") continue;
        if ($key == "owner") continue;
        if ($val) $where .= "(fkey = '$key' and  fval ilike '$val%' and tab = '$tab') or ";
    }
    $where = substr($where,0,-3) ;
    $sqle="select owner from extra_felder where ".$where;
    if ($tab == "C") { 
        $sql = "select * from customer where id in ($sqle)";
    } else if ($tab == "V") {
        $sql = "select * from vendor where id in ($sqle)";
    } else if ($tab == "P") {
        $sql = "SELECT contacts.*,C.name as cfirma,V.name as vfirma  ";
        $sql.= "from contacts left join customer C on C.id=cp_cv_id ";
        $sql.= "left join vendor V on V.id=cp_cv_id where cp_id in ($sqle)";
    }
    $rs = $_SESSION['db']->getAll($sql);
    return $rs;
}

function saveFelder($data) {
	$nosave=array("save","owner","suche");
	$owner=$data["owner"];
	$rc=$_SESSION['db']->query("BEGIN");
        $tab = substr($owner,0,1);
        $owner = substr($owner,1);
	$sql="delete from extra_felder where tab = '$tab' and owner = '$owner'";
	$rc=$_SESSION['db']->query($sql);
	foreach ($data as $key=>$val) {
		if (in_array($key,$nosave)) continue;
		$val=trim($val);
		$rc=$_SESSION['db']->insert('extra_felder',array('tab','owner','fkey','fval'),array($tab,$owner,$key,$val));
		if (!$rc) { $_SESSION['db']->query("ROLLBACK"); return false; };
	}
	$rc=$_SESSION['db']->query("COMMIT");
	return true;
}

function getFelder($owner,&$t) {
	$t->set_var(array(owner => $owner));
    $tab = substr($owner,0,1);
    $owner = substr($owner,1);
	$sql="select * from extra_felder where tab = '$tab' and owner='$owner'";
	$rs=$_SESSION['db']->getAll($sql);
	if ($rs) {
		foreach($rs as $row) {
			$key=$row["fkey"];
			$val=$row["fval"];
			$t->set_var(array(
				$key => $val,
				$key.$val => ($val)?"selected":"",
				$key."_".$val => ($val)?"checked":""
			));
		}
	}
}
$maske=substr($owner,0,1);

$t = new Template($base);
if ($_POST["save"]) saveFelder($_POST);
if ($_POST["suche"]) {
    $daten = suchFelder($_POST);
    if (count($daten)==1 && $daten<>false) {
	    if ($maske=="P") {
            	header ("location:kontakt.php?id=".$daten[0]["cp_id"]);
	    } else {
            	header ("location:firma1.php?Q=$maske&id=".$daten[0]["id"]);
	    }
    } else if (count($daten)>1) {
        clearCSVData();
        if ($maske=="P") {
            $t->set_file(array("fa1" => "personen1L.tpl"));
            insertCSVData(array("ANREDE","TITEL","NAME1","NAME2","LAND","PLZ","ORT","STRASSE",
                    "TEL","FAX","EMAIL","FIRMA","FaID","GESCHLECHT","ID"),-1);
        } else {
            $t->set_file(array("fa1" => "firmen1L.tpl"));
            insertCSVData(array("ANREDE","NAME1","NAME2","LAND","PLZ","ORT","STRASSE","TEL","FAX","EMAIL",
                    "KONTAKT","ID","KDNR","USTID","STEUERNR","KTONR","BANK","BLZ","LANG","KDTYP"),-1);
        }
        $t->set_block("fa1","Liste","Block");
        $t->set_var(array(
            'CRMCSS' => $head['CRMCSS'], 
            'FAART'  => ($Q=="C")?"Customer":"Vendor",
            'msg'    => $msg,
        ));
        $i=0;
        if ($maske=="P") {
            foreach ($daten as $zeile) {
                if ($zeile["cp_gender"] =="f"){
                    if ($zeile["language_id"]) {
                        $zeile["cp_greeting"]= $anredenFrau[$zeile["language_id"]];
                    } else {
                        $zeile["cp_greeting"]="Frau";
                    }
                } else if ($zeile["cp_gender"] =="m"){
                    if ($zeile["language_id"]) {
                        $zeile["cp_greeting"]= $anredenHerr[$zeile["language_id"]];
                    } else {
                        $zeile["cp_greeting"]="Herr";
                    }
                } else {
                        $zeile["cp_greeting"]="";
                }
                insertCSVData(array($zeile["cp_greeting"],$zeile["cp_title"],$zeile["cp_name"],$zeile["cp_givenname"],
                        $zeile["cp_country"],$zeile["cp_zipcode"],$zeile["cp_city"],$zeile["cp_street"],
                        $zeile["cp_phone1"],$zeile["cp_fax"],$zeile["cp_email"],(($zeile["cfirma"])?$zeile["cfirma"]:$zeile["vfirma"]),
                        $zeile["cp_cv_id"],$zeile["cp_gender"]),$zeile["cp_id"]);
                if ($zeile["cfirma"]) { $Quelle = "C"; }
                else if ($zeile["vfirma"]) { $Quelle = "V"; }
                else { $quelle = ""; };
                if ($_POST["FID1"]) {
                    $insk="<input type='checkbox' name='kontid[]' value='".$zeile["cp_id"]."'>";
                    $js="";
                } else {
                    $js='showK('.$zeile["cp_id"].',"'.$Quelle.'");'; //showK({PID},'{TBL}')
                    $insk="";
                };
                $t->set_var(array(
                        'js' => $js,
                        'LineCol' => ($i%2+1),
                        'Name' => $zeile["cp_name"].", ".$zeile["cp_givenname"],
                        'Plz' => $zeile["cp_zipcode"],
                        'Ort' => $zeile["cp_city"],
                        'Telefon' => $zeile["cp_phone1"],
                        'eMail' => $zeile["cp_email"],
                        'Firma' => ($zeile["cfirma"])?$zeile["cfirma"]:$zeile["vfirma"],
                        'insk' => $insk,
                        'DEST' => "",
                        'QUELLE' => $Quelle,
                        'Q' => $Quelle,
                    ));
               $t->parse("Block","Liste",true);
	       $i++;
            }
        } else {
            foreach ($daten as $zeile) {
                insertCSVData(array($zeile["greeting"],$zeile["name"],$zeile["department_1"],
                            $zeile["country"],$zeile["zipcode"],$zeile["city"],$zeile["street"],
                            $zeile["phone"],$zeile["fax"],$zeile["email"],$zeile["contact"],$zeile["id"],
                            ($Q=="C")?$zeile["customernumber"]:$zeile["vendornumber"],
                            $zeile["ustid"],$zeile["taxnumber"],
                            $zeile["account_number"],$zeile["bank"],$zeile["bank_code"],
                            $zeile["language"],$zeile["business_id"]),$zeile["id"]);
                $t->set_var(array(
                        'Q' => $maske,
                        'ID' => $zeile["id"],
                        'LineCol' => ($i%2+1),
                        'KdNr' => ($maske=="C")?$zeile["customernumber"]:$zeile["vendornumber"],
                        'Name' => $zeile["name"],
                        'Plz' => $zeile["zipcode"],
                        'Ort' => $zeile["city"],
                        'Telefon' => $zeile["phone"],
                        'eMail' => $zeile["email"]
                    ));
                    $t->parse("Block","Liste",true);
            }
        }
        $t->Lpparse("out",array("fa1"),$_SESSION['countrycode'],"firma");
        exit();
    } else {
        $msg="Sorry, not found.";
    }
}
$t->set_file(array("extra" => "extra$maske.tpl"));
$visible = 'style="visibility:visible"';
$hidden = 'style="visibility:hidden"';
$t->set_var(array(
    'CRMCSS'         => $head['CRMCSS'], 
    'STYLESHEETS'    => $menu['stylesheets'],
    'THEME'          => $head['THEME'],
    'JQUERY'         => $_SESSION['baseurl'].'crm/',
    'visiblesichern' => ($owner=='P0')?$hidden:$visible,
    'visiblesuchen'  => ($owner=='P0')?$visible:$hidden,
));
getFelder($owner,$t);
$t->pparse("out",array("extra"));
?>
