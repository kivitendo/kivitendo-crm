<?
$VERSION='1.4.0';
// Die volgenden Datenbanken müssen extra installiert werden. Siehe install.txt 
$GEODB=false; // Geodatenbank vorhanden
$BLZDB=false; // BLZ-Datenbank vorhanden
//php-db => "", mdb2 => "m"
$dbmodul="";
$logfile = true;
//Xajax => 0.5 evtl. auch 0.6
define("XajaxVer","05");
define("XajaxPath","./crmajax/");
//Xajax = 0.23
//define("XajaxVer","");
//define("XajaxPath","./crmajax/xajax");
$from=16;  // Anrufernummer (Telcall)
$to=18;    // Zielnummer (Telcall)
$constring="created"; // Eindeutiges Wort für Anwahlzeile (Telcall)
$connect=9; // Position eindeutiges Wort (Tellcall)
//Beispiellog von Capilog
//Mon Jul  7 15:31:25 2008 Connection 0x81324f8: Connection object created for incoming call PLCI 101 from 0731229 to 58322 CIP 0x4
//Mon Jul  7 15:31:25 2008 Connection 0x81324f8: rejecting with cause 34a9
//Mon Jul  7 15:31:25 2008 Connection 0x81324f8: Connection object deleted
$bgcol[1]="#ddddff";
$bgcol[2]="#ddffdd";
$bgcol[3]="#ff6666";
$bgcol[4]="#fffa05";
$typcol["T"]="#f3f702";
$typcol["M"]="#18f204";
$typcol["S"]="#07d3f7";
$typcol["P"]="#f70727";
$typcol["P"]="#ea0bd0";
$typcol["D"]="#ff4605";
$typcol["X"]="#fa05ff";
define("FPDF_FONTPATH","/usr/share/fpdf/font/");
define("FONTART","2");
define("FONTSTYLE","1");
$emptyVon = True;
$logmail=true;
$jcalendar=true;
$listLimit=20000;
$tinymce=false;
$zeigeextra=true;
$tools=true;
$jpg=false;
$showErr=false;
$CallEdit = true;
$CallDel = false;
//Verschiedene Map-Anbieter können hier eingestellt werden
//Leerzeichenersatz
//GoYellow
//$planspace="-";
//$stadtplan="http://www.goyellow.de/map/%TOZIPCODE%-%TOCITY%/%TOSTREET%";
//URL
//viamichelin
$planspace="+";
//$stadtplan="http://www.viamichelin.de/viamichelin/deu/dyn/controller/mapPerformPage?strAddress=%TOSTREET%&strCP=%TOZIPCODE%&strLocation=%TOCITY%&strDestCP=89073&strDestLocation=Ulm&strDestAddress=Ensingerstr+11";
//Google
//$planspace="+";
//$stadtplan="http://maps.google.de/maps?f=d&hl=de&daddr=%TOSTREET%,%TOZIPCODE%+%TOCITY%&saddr=%FROMSTREET%,%FROMZIPCODE%+%FROMCITY%";
$stadtplan="http://maps.google.de/maps?f=d&hl=de&daddr=%TOSTREET%,%TOZIPCODE%+%TOCITY%";
$ERPNAME="lx-office-erp";
?>
