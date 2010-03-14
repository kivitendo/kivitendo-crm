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

$owner=($_GET["owner"])?$_GET["owner"]:$_POST["owner"];

function suchFelder($data) {
global $db;
    $tab = substr($data['owner'],0,1);
    foreach ($data as $key=>$val) { 
        if ($key == "suche") continue;
        if ($key == "owner") continue;
        if ($val) $where .= "(fkey = '$key' and  fval ilike '$val%' and tab = '$tab') or ";
    }
    $where = substr($where,0,-3) ;
    $sql="select owner from extra_felder where ".$where;
    if ($tab == "C") { 
        $sql = "select * from customer where id in ($sql)";
    } else if ($tab == "V") {
        $sql = "select * from vendor where id in ($sql)";
    }
    $rs = $db->getAll($sql);
    return $rs;
}

function saveFelder($data) {
global $db;
	$nosave=array("save","owner");
	$owner=$data["owner"];
	$rc=$db->query("BEGIN");
    $tab = substr($owner,0,1);
    $owner = substr($owner,1);
	$sql="delete from extra_felder where tab = '$tab' and owner = '$owner'";
	$rc=$db->query($sql);
	foreach ($data as $key=>$val) {
		if (in_array($key,$nosave)) continue;
		$val=trim($val);
		$rc=$db->insert('extra_felder',array('tab','owner','fkey','fval'),array($tab,$owner,$key,$val));
		if (!$rc) { $db->query("ROLLBACK"); return false; };
	}
	$rc=$db->query("COMMIT");
	return true;
}

function getFelder($owner,&$t) {
global $db;
	$t->set_var(array(owner => $owner));
    $tab = substr($owner,0,1);
    $owner = substr($owner,1);
	$sql="select * from extra_felder where tab = '$tab' and owner='$owner'";
	$rs=$db->getAll($sql);
	if ($rs) {
		foreach($rs as $row) {
			$key=$row["fkey"];
			$val=$row["fval"];
			$t->set_var(array(
				$key => $val,
				$key.$val => "selected",
				$key."_".$val => "checked"
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
            header ("location:firma1.php?Q=$maske&id=".$daten[0]["id"]);
    } else if (count($daten)>1) {
        require("firmacommon".XajaxVer.".php");
        $t->set_file(array("fa1" => "firmen1L.tpl"));
        $t->set_block("fa1","Liste","Block");
        $t->set_var(array(
            AJAXJS  => $xajax->printJavascript(XajaxPath),
            FAART => ($Q=="C")?"Customer":"Vendor",
            msg => $msg,
        ));
        $i=0;
        clearCSVData();
       insertCSVData(array("ANREDE","NAME1","NAME2","LAND","PLZ","ORT","STRASSE","TEL","FAX","EMAIL","KONTAKT","ID",
                    "KDNR","USTID","STEUERNR","KTONR","BANK","BLZ","LANG","KDTYP"),-1);
        foreach ($daten as $zeile) {
            insertCSVData(array($zeile["greeting"],$zeile["name"],$zeile["department_1"],
                        $zeile["country"],$zeile["zipcode"],$zeile["city"],$zeile["street"],
                        $zeile["phone"],$zeile["fax"],$zeile["email"],$zeile["contact"],$zeile["id"],
                        ($Q=="C")?$zeile["customernumber"]:$zeile["vendornumber"],
                        $zeile["ustid"],$zeile["taxnumber"],
                        $zeile["account_number"],$zeile["bank"],$zeile["bank_code"],
                        $zeile["language"],$zeile["business_id"]),$zeile["id"]);
            $t->set_var(array(
                    Q => $maske,
                    ID => $zeile["id"],
                    LineCol => $bgcol[($i%2+1)],
                    KdNr => ($maske=="C")?$zeile["customernumber"]:$zeile["vendornumber"],
                    Name => $zeile["name"],
                    Plz => $zeile["zipcode"],
                    Ort => $zeile["city"],
                    Telefon => $zeile["phone"],
                    eMail => $zeile["email"]
                ));
                $t->parse("Block","Liste",true);
        }
        $t->Lpparse("out",array("fa1"),$_SESSION["lang"],"firma");
        exit();
    } else {
        $msg="Sorry, not found.";
    }
}

$t->set_file(array("extra" => "extra$maske.tpl"));
getFelder($owner,$t);
$t->pparse("out",array("extra"));
?>
