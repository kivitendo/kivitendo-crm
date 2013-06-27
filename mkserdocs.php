<?php
session_start();
    @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);

echo "Bitte das Fenster erst nach Aufforderung schlie&szlig;en<br>";
ob_end_flush();

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
    case "swf" : 
    case "sxw" : 
                define('POO_TMP_PATH', $_SESSION["savefiledir"]);
                require("inc/phpOpenOffice.php");
                $doc = new phpOpenOffice();
                break;
    case "rtf" : 
                 require('inc/phpRtf.php');
                 $doc = new phpRTF();
                 break;
}


$doc->loadDocument("./dokumente/".$_SESSION["dbname"]."/serbrief/".$_SESSION["datei"]);
$doc->savecontent();
$sql="select * from tempcsvdata where uid = '".$_SESSION["loginCRM"]."' AND id = -255";
$data=$db->getAll($sql);
$felder=explode(":",$data[0]["csvdaten"]);
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

function decoder($txt) {
    if (ini_get("default_charset")=='utf-8') {
        return utf8_decode($txt);
    } else {
       return $txt;
    }
}

//Daten holen 
$sql="select * from tempcsvdata where uid = ".$_SESSION["loginCRM"]." and id >0"; //offset 1";
$data=$db->getAll($sql);
$cnt=1;
$_SESSION["src"]=($_GET["src"]<>"")?$_GET["src"]:"P";
if ($data) {
    foreach ($data as $row) {
        $tmp=explode(":",$row["csvdaten"]);
        foreach($felder as $name) {
            if ($_SESSION["rub"]==1) {
                //Künftig werden db-Feldnamen verwendet zZ nur RuB
                $nname=$hli2erp[$_SESSION["src"]][$name];
            } else {
                $nname=$name;
            }
            $vars[$nname] = decoder($tmp[$pos[$name]]);
        }
        $vars["DATUM"]=$_SESSION["DATE"];
        $vars["BETREFF"]=decoder($_SESSION["SUBJECT"]);
        $vars["INHALT"]=decoder($_SESSION["BODY"]);
        $vars["NAME"]=$vars["NAME1"];
        $vars["TMPFILE"] = $tmpfile;
        $tdata["CID"]=$row["id"];
        insCall($tdata,false);
        $doc->parse($vars);
        $doc->cleanTemplate();
        $doc->save($_SESSION["savefiledir"]."/".$_SESSION['src'].$row["id"]."_".$_SESSION["datei"]);
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
<br><center><a href='javascript:self.close();'>close</a></center>
