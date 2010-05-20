<?php
session_start();
require_once("inc/stdLib.php");
require_once("inc/crmLib.php");

//Übersetzung der Platzhalter
$hli2erp["P"]=array("ANREDE"=>"cp_greeting","TITEL"=>"cp_title","NAME1"=>"cp_name","NAME2"=>"cp_givenname",
                "LAND"=>"cp_country","PLZ"=>"cp_zipcode","ORT"=>"cp_city","STRASSE"=>"cp_street",
                "TEL"=>"cp_phone","FAX"=>"cp_fax","EMAIL"=>"cp_email","FIRMA"=>"name","GESCHLECHT"=>"cp_gender","ID"=>"cp_id",
                "DATE"=>"DATE","SUBJECT"=>"SUBJECT","BODY"=>"BODY","TMPFILE"=>"TMPFILE");
$hli2erp["F"]=array("ANREDE"=>"greeting","NAME1"=>"name","NAME2"=>"department_1",
                "LAND"=>"country","PLZ"=>"zipcode","ORT"=>"city","STRASSE"=>"street",
                "TEL"=>"phone","FAX"=>"fax","EMAIL"=>"email","KONTAKT"=>"contact","ID"=>"id",
                "USTID"=>"ustid","STEUERNR"=>"taxnumber","LANG"=>"language","KDTYP"=>"business_id",
                "KTONR"=>"account_number","BANK"=>"bank","BLZ"=>"bank_code",
                "DATE"=>"DATE","SUBJECT"=>"SUBJECT","BODY"=>"BODY","TMPFILE"=>"tmpfile");

//Bibliothek nachladen
$typ=strtolower(substr($_SESSION["datei"],-3));
switch ($typ) {
    case "tex" :
                require('inc/phpTex.php');
                $doc = new phpTex();
                break;
    case "odt" : 
    case "swf" : 
    case "sxw" : 
                define('POO_TMP_PATH', $_SESSION["savefiledir"]);
                require("inc/phpOpenOffice.php");
                $doc = new phpOpenOffice();
                break;
}

echo "Bitte das Fenster erst nach Aufforderung schlie&szlig;en<br>";

$doc->loadDocument("./dokumente/".$_SESSION["mansel"]."/serbrief/".$_SESSION["datei"]);
$doc->savecontent();
$sql="select * from tempcsvdata where uid = '".$_SESSION["loginCRM"]."' limit 1";
$data=$db->getAll($sql);
$felder=explode(":",$data[0]["csvdaten"]);
$felder[]="DATE";
$felder[]="SUBJECT";
$felder[]="BODY";
$felder[]="TMPFILE";
$tmpfile=substr($_SESSION["datei"],0,-4);
$i=0;
foreach($felder as $value) {
    $name=strtoupper($value);
    $vars[$name]="";
    $pos[$name]=$i++;
}; 

//incCall vorbereiten
$tdata["CRMUSER"]=$_SESSION["loginCRM"];
$tdata["cause"]=$_SESSION["SUBJECT"];
$tdata["c_cause"]=$_SESSION["BODY"];
$tdata["Kontakt"]="S";
$tdata["Bezug"]=0;
$tdata["Zeit"]=date("H:i");
$tdata["Datum"]=date("d.m.Y");
$tdata["Status"]=1;
$tdata["DateiID"]=$_SESSION["dateiId"];

//Daten holen 
$sql="select * from tempcsvdata where uid = ".$_SESSION["loginCRM"]." and id >0"; //offset 1";
$data=$db->getAll($sql);
$cnt=1;
$_SESSION["src"]=($_GET["src"]<>"")?$_GET["src"]:"P";
if ($data) {
    foreach ($data as $row) {
        $tmp=explode(":",$row["csvdaten"]);
        $tmp[]=$_SESSION["DATE"];
        $tmp[]=$_SESSION["SUBJECT"];
        $tmp[]=$_SESSION["BODY"];
        $tmp[]=$tmpfile;
        foreach($felder as $name) {
            if ($_SESSION["rub"]==1) {
                //Künftig werden db-Feldnamen verwendet zZ nur RuB
                $nname=$hli2erp[$_SESSION["src"]][$name];
            } else {
                $nname=$name;
            }
            $vars[$nname] = $tmp[$pos[$name]];
        }
        $vars["DATUM"]=$vars["DATE"];
        $vars["BETREFF"]=$vars["SUBJECT"];
        $vars["INHALT"]=$vars["BODY"];
        $vars["NAME"]=$vars["NAME1"];
        $tdata["CID"]=$row["id"];
        insCall($tdata,false);
        $doc->parse($vars);
        $doc->cleanTemplate();
        $doc->save($_SESSION["savefiledir"]."/".$row["id"]."_".$_SESSION["datei"]);
        if ($cnt++ % 10 == 0) echo "."; flush();
        $doc->getoriginal();
        foreach ($vars as $key=>$val) { $vars[$key]=""; };
        empty($tmp); 
    }
}
//$doc->clean();
?>
<br>
Sie k&ouml;nnen das Fensten jetzt schlie&szlig;en;
