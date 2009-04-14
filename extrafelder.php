<?
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

function saveFelder($data) {
global $db;
	$nosave=array("save","owner");
	$owner=$data["owner"];
	$rc=$db->query("BEGIN");
	$sql="delete from extra_felder where owner = '$owner'";
	$rc=$db->query($sql);
	foreach ($data as $key=>$val) {
		if (in_array($key,$nosave)) continue;
		$val=trim($val);
		$rc=$db->insert('extra_felder',array('owner','fkey','fval'),array($owner,$key,$val));
		if (!$rc) { $db->query("ROLLBACK"); return false; };
	}
	$rc=$db->query("COMMIT");
	return true;
}

function getFelder($owner,&$t) {
global $db;
	$t->set_var(array(owner => $owner));
	$sql="select * from extra_felder where owner='$owner'";
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

if ($_POST["save"]) saveFelder($_POST);

$t = new Template($base);
$t->set_file(array("extra" => "extra$maske.tpl"));
getFelder($owner,$t);
$t->pparse("out",array("extra"));
?>
