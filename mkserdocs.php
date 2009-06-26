<?php
session_start();
require_once("inc/stdLib.php");
require_once("inc/crmLib.php");


$hli2erp["P"]=array("ANREDE"=>"cp_greeting","TITEL"=>"cp_title","NAME1"=>"cp_name","NAME2"=>"cp_givenname",
                "LAND"=>"cp_country","PLZ"=>"cp_zipcode","ORT"=>"cp_city","STRASSE"=>"cp_street",
                "TEL"=>"cp_phone","FAX"=>"cp_fax","EMAIL"=>"cp_email","FIRMA"=>"name","GESCHLECHT"=>"cp_gender","ID"=>"cp_id");
$hli2erp["F"]=array("ANREDE"=>"greeting","NAME1"=>"name","NAME2"=>"department_1",
                "LAND"=>"country","PLZ"=>"zipcode","ORT"=>"city","STRASSE"=>"street",
                "TEL"=>"phone","FAX"=>"fax","EMAIL"=>"email","KONTAKT"=>"contact","ID"=>"id",
                "USTID"=>"ustid","STEUERNR"=>"taxnumber","LANG"=>"language","KDTYP"=>"business_id",
                "KTONR"=>"account_number","BANK"=>"bank","BLZ"=>"bank_code");

$typ=strtolower(substr($_SESSION["datei"],-3));
switch ($typ) {
    case "tex" :
                require('inc/phpTex.php');
                $doc = new phpBIN();
                break;
    case "rtf" :
                break;
    case "odt" :
    case "swf" :
                break;
}
echo "Bitte das Fenster erst nach Aufforderung schlie&szlig;en<br>";

$doc->loadDocument("vorlage/".$_SESSION["datei"]);
$doc->savecontent();
$sql="select * from tempcsvdata where uid = '".$_SESSION["loginCRM"]."' limit 1";
$data=$db->getAll($sql);
$felder=split(":",$data[0]["csvdaten"]);
$felder[]="DATE";
$felder[]="SUBJECT";
$felder[]="BODY";
$i=0;
foreach($felder as $value) {
    $name=strtoupper($value);
    $vars[$name]="";
    $pos[$name]=$i++;
}; 
$data1["CRMUSER"]=$_SESSION["loginCRM"];
$data1["cause"]=$_SESSION["SUBJECT"];
$sql="select * from tempcsvdata where uid = ".$_SESSION["loginCRM"]." offset 1";
$data=$db->getAll($sql);
if ($data) {
    foreach ($data as $row) {
        $tmp=split(":",$row["csvdaten"]);
        $tmp[]=$_SESSION["DATE"];
        $tmp[]=$_SESSION["SUBJECT"];
        $tmp[]=$_SESSION["BODY"];
        foreach($felder as $name) {
            $nname=$hli2erp["P"][$name];
            $vars[$nname] = $tmp[$pos[$name]];
        }
        $doc->parse($vars);
        $doc->save($_SESSION["savefiledir"]."/".$row["id"]."_".$_SESSION["datei"]);
        echo $row["id"]; flush();
        $doc->getoriginal();
    }
}

?>
<br>
Sie k&ouml;nnen das Fensten jetzt schlie&szlig;en;
