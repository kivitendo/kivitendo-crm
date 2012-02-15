<?php
/**********************************************************************
*** Erzeugt benutzerfreundliche Links zu den Verzeichnissen         ***
*** Autor: Ronny Kumke, ronny@lxcars.de   02.02.2012                ***
**********************************************************************/  
require_once("inc/stdLib.php");
require("inc/conf.php");

$dir_abs = getcwd()."/dokumente/$_SESSION[dbname]";
$link_dir_cust = $sep_cust_vendor ? "/link_dir_cust" : "/link_dir";
$link_dir_vend = $sep_cust_vendor ? "/link_dir_vend" : "/link_dir";
global $db;

if (!is_dir($dir_abs)) mkdir($dir_abs);
chmod($dir_abs,$dir_mode);
if (!is_dir($dir_abs.$link_dir_cust)) mkdir($dir_abs.$link_dir_cust);
chmod($dir_abs.$link_dir_cust, $dir_mode);
if (!is_dir($dir_abs.$link_dir_vend)) mkdir($dir_abs.$link_dir_vend);
chmod($dir_abs.$link_dir_vend, $dir_mode);

//Alle Links erzeugen (wird von status.php ausgelöst)
if ($_GET[all]) { 
	echo "Benutzerfreundliche Links in $dir_abs anlegen... </br>";
	if (is_dir($dir_abs.$link_dir_cust)&&($link_dir_cust != $link_dir_vend)) {
  		if ($dh = opendir($dir_abs.$link_dir_cust)) {
            echo "Alte Links in ".$dir_abs.$link_dir_cust." löschen... </br>";
            while (($link = readdir($dh)) !== false) {
                if ($link != '.' && $link != '..') {
                    echo "lösche: $link </br>";
                    unlink($dir_abs.$link_dir_cust."/".$link);	
                }
            }
            closedir($dh);
        }
    }
    if (is_dir($dir_abs.$link_dir_vend)) {
		if ($dh = opendir($dir_abs.$link_dir_vend)) {
        	echo "Alte Links in ".$dir_abs.$link_dir_vend." löschen... </br>";
   		   	while (($link = readdir($dh)) !== false) {
	     		if ($link != '.' && $link != '..' ) {
	     			echo "lösche: $link </br>";
                    unlink($dir_abs.$link_dir_vend."/".$link);	
                }  
            }
            closedir($dh);
        }
    }
	$sql = "SELECT  name, customernumber FROM customer ORDER BY customernumber"; //::INT ";
    $rs = $db->getall($sql);
    if ($rs) { 
    	foreach ($rs as $key => $value) {
			if (!is_dir($dir_abs."/C".$rs[$key]['customernumber'])) {
    			echo "Erzeuge Verzeichnis: ".$dir_abs."/C".$rs[$key]['customernumber']." </br>";
    			mkdir($dir_abs."/C".$rs[$key]['customernumber']);
    		}
    		chmod($dir_abs."/C".$rs[$key]['customernumber'],$dir_mode);
    		echo "Erzeuge Symlink: ".$dir_abs.$link_dir_cust."/".mkDirName($rs[$key]['name'])."_C".$rs[$key]['customernumber']."</br>";
    	 	symlink($dir_abs."/C".$rs[$key]['customernumber'], $dir_abs.$link_dir_cust."/".mkDirName($rs[$key]['name'])."_C".$rs[$key]['customernumber']);
		}
    }
    $sql = "SELECT  name, vendornumber FROM vendor ORDER BY vendornumber"; //::INT ";
    $rs = $db->getall($sql);
    if ($rs) { 
    	foreach ($rs as $key => $value) {
			if (!is_dir($dir_abs."/V".$rs[$key]['vendornumber'])) {
    			echo "Erzeuge Verzeichnis: ".$dir_abs."/V".$rs[$key]['vendornumber']." </br>";
    			mkdir($dir_abs."/V".$rs[$key]['vendornumber']);
    		}
    		chmod($dir_abs."/V".$rs[$key]['vendornumber'],$dir_mode);
    		echo "Erzeuge Symlink: ".$dir_abs.$link_dir_vend."/".mkDirName($rs[$key]['name'])."_V".$rs[$key]['vendornumber']."</br>";
    	 	symlink($dir_abs."/V".$rs[$key]['vendornumber'], $dir_abs.$link_dir_vend."/".mkDirName($rs[$key]['name'])."_V".$rs[$key]['vendornumber']);
		}
    }
	echo "...done"; 
}
       
/***************************************************************
*** erzeugt valide Verzeichnisnamen für viele Betriebsysteme ***
*** invalide Zeichen sind: Umlaute  \ / : * ? " < > |        ***
***************************************************************/
function mkDirName($name) { 
//sollte eigentlich in stdLib, diese müsste dann jedoch utf-8 codiert sein! 
	$ers = array( 
		' ' => '_',  'ä' => 'ae', 'â' => 'ae', 'ã' => 'ae', 'à' => 'ae', 'á' => 'ae', 'ç' => 'c', 
		'ï' => 'i',  'í' => 'i',  'ì' => 'i',  'î' => 'i',  'ö' => 'oe', 'ó' => 'oe', 'ò' => 'oe', 
		'õ' => 'oe', 'ü' => 'ue', 'ú' => 'ue', 'ù' => 'ue', 'û' => 'ue', 'Ä' => 'Ae', 'Â' => 'Ae',
		'Ã' => 'Ae', 'Á' => 'Ae', 'À' => 'Ae', 'Ç' => 'C',  'É' => 'E',  'È' => 'E',  'Ê' => 'E',
		'Ë' => 'E',  'Í' => 'I',  'Ì' => 'I',  'Î' => 'I',  'Ï' => 'I',  'Ö' => 'Oe', 'Ó' => 'Oe',
		'Ò' => 'Oe', 'Õ' => 'Oe', 'Ô' => 'Oe', 'Ü' => 'Ue', 'Ú' => 'Ue', 'Ù' => 'Ue', 'Û' => 'Ue',
		'\\'=> '_',  'ß' => 'ss', '/' => '_' , ':' => '_',  '*' => '_',  '?' => '_',  '"' => '_',
		'<' => '_',  '>' => '_',  '|' => '_'  );
	return strtr($name,$ers);
}
?>
