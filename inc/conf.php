<?
$VERSION='1.4.0';
// Die volgenden Datenbanken müssen extra installiert werden. Siehe install.txt 
$GEODB=true; // Geodatenbank vorhanden
$BLZDB=false; // BLZ-Datenbank vorhanden
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
//Zahl ist Bit-stelle 1 = 1, 2 = 2, 3 = 4, 4 = 8...
$cp_sonder=array(1 => "News", 2 => "Test 1");
$logmail=true;
$jcalendar=true;
$listLimit=200;
$tinymce=false;
$zeigeextra=true;
$jpg=false;
//$jpg=true;
$showErr=false;
$CallEdit = true;
$CallDel = true;
//Verschiedene Map-Anbieter können hier eingestellt werden
//Leerzeichenersatz
//GoYellow
//$planspace="-";
//$stadtplan="http://www.goyellow.de/map/%TOZIPCODE%-%TOCITY%/%TOSTREET%";
//URL
//viamichelin
$planspace="+";
$stadtplan="http://www.viamichelin.de/viamichelin/deu/dyn/controller/mapPerformPage?strAddress=%TOSTREET%&strCP=%TOZIPCODE%&strLocation=%TOCITY%&strDestCP=89073&strDestLocation=Ulm&strDestAddress=Ensingerstr+11";
//Google
//http://maps.google.de/maps?f=d&hl=de&saddr=89073+ulm,+ulmergasse+11&daddr=Hafenstra%C3%9Fe+5,+24837+Schleswig&sll=51.399206,9.887695&sspn=11.934881,29.443359&ie=UTF8&z=5&ll=51.454007,9.887695&spn=11.920677,29.443359&om=1
//$planspace="+";
//$stadtplan="http://maps.google.de/maps?f=d&hl=de&daddr=%TOSTREET%,%TOZIPCODE%+%TOCITY%&saddr=%FROMSTREET%,%FROMZIPCODE%+%FROMCITY%";
$ERPNAME="lx-office-erp";
?>
