<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/crmLib.php");
	require_once 'Contact_Vcard_Parse.php';
    
function saveADR($dest,$value,&$container) {
	if ((empty($dest)||!isset($dest)||$dest=="") && !isset($container["ADR"]["HOME"])) {
		$dest="HOME";
	} else if ((empty($dest)||!isset($dest)||$dest=="") && !isset($container["ADR"]["WORK"])) {
		$dest="WORK";
	}
	if (isset($container["ADR"][$dest])) return;
	$container["ADR"][$dest]["POSTSTELLE"]	= $value[0][0];
	$container["ADR"][$dest]["ERWEITERT"]	= $value[1][0];
	$container["ADR"][$dest]["STRASSE"]	= $value[2][0];
	$container["ADR"][$dest]["ORT"]		= $value[3][0];
	$container["ADR"][$dest]["REGIO"]	= $value[4][0];
	$container["ADR"][$dest]["PLZ"]		= $value[5][0];
	$container["ADR"][$dest]["LAND"]	= $value[6][0];
}
function saveTEL($dest,$value,&$container) {
	$container["TEL"][$dest] = $value[0][0];
}
function saveMAIL($dest,$value,&$container) {
	$container["EMAIL"][$dest] = $value[0][0];
}
    
    
	if ($_POST["upload"]) {

		// instantiate a parser object
		$parse = new Contact_Vcard_Parse();
	    	// parse a vCard file and store the data in $cardinfo
		$cardinfo = $parse->fromFile($_FILES["datei"]["tmp_name"]);
		$adress=array();
		reset($cardinfo[0]);
		while (list($key,$line) = each($cardinfo[0]))  {
			switch ($key) {
			case "ADR":	foreach ($line as $row) {
						if ($row[param][TYPE])	foreach ($row[param][TYPE] as $typ) {
							saveADR($typ,$row["value"],$adress);
						} else {
							saveADR("",$row["value"],$adress);
						}
					}
					break;
			case "TITLE":	$adress["N"]["TITLE"]=$line[0]["value"][0][0]; break;
			case "X-GENDER": $adress["N"]["GENDER"]=$line[0]["value"][0][0]; break;	 
			case "N":	$adress["N"]["ANREDE"]=$line[0]["value"][0][0];
					$adress["N"]["NACHNAME"]=$line[0]["value"][0][0]; 
					$adress["N"]["VORNAME"]=$line[0]["value"][1][0];
					$adress["N"]["VORNAME"].=($line[0]["value"][2][0])?" ".($line[0]["value"][2][0]):"";
					$adress["N"]["PRE"]=$line[0]["value"][3][0];
					$adress["N"]["SUF"]=$line[0]["value"][4][0]; break;
			case "BDAY":	$adress["N"]["BDAY"]=$line[0]["value"][0][0]; break;
			case "URL":	$adress["URL"]=$line[0]["value"][0][0]; break;
			case "NOTE":	$adress["NOTE"]=$line[0]["value"][0][0]; break;
			case "ROLE":	$adress["N"]["ROLE"]=$line[0]["value"][0][0]; break;
			case "TEL":	foreach ($line as $row) {
						saveTEL($row[param][TYPE][0],$row["value"],$adress);
					}; break;
			case "EMAIL":	foreach ($line as $row) {
						saveMAIL($row[param][TYPE][0],$row["value"],$adress);
					}; break;
			case "ORG":	
					if (count($line[0]["value"])>1) {
						$adress["ORG"]["FIRMA"] = $line[0]["value"][0][0];
						$adress["ORG"]["ABTLG"] = $line[0]["value"][1][0];
					} else {
						$adress["ORG"]["FIRMA"] = $line[0]["value"][0][0];
					}; break;
			}		
		}	
		unlink($_FILES["datei"]['tmp_name']);
	}

?>

<html>
	<head><title>CRM - VCard</title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
<?php	if ($adress and $_POST["src"]=="P") {  ?>
		opener.document.formular.cp_title.value="<?= $adress["N"]["PRE"]; ?>";
		opener.document.formular.cp_position.value="<?= $adress["N"]["TITLE"]; ?>";
		opener.document.formular.cp_givenname.value="<?= $adress["N"]["VORNAME"]; ?>";
		opener.document.formular.cp_name.value="<?= $adress["N"]["NACHNAME"]; ?>";
		opener.document.formular.cp_street.value="<?= ($adress["ADR"]["HOME"]["STRASSE"])?$adress["ADR"]["HOME"]["STRASSE"]:$adress["ADR"]["WORK"]["STRASSE"]; ?>";
		opener.document.formular.cp_zipcode.value="<?= ($adress["ADR"]["HOME"]["PLZ"])?$adress["ADR"]["HOME"]["PLZ"]:$adress["ADR"]["WORK"]["PLZ"]; ?>";
		opener.document.formular.cp_city.value="<?= ($adress["ADR"]["HOME"]["ORT"])?$adress["ADR"]["HOME"]["ORT"]:$adress["ADR"]["WORK"]["ORT"]; ?>";
		opener.document.formular.cp_country.value="<?= ($adress["ADR"]["HOME"]["REGIO"])?$adress["ADR"]["HOME"]["REGIO"]:$adress["ADR"]["WORK"]["REGIO"]; ?>";
		opener.document.formular.cp_phone1.value="<?= $adress["TEL"]["WORK"]; ?>";
		opener.document.formular.cp_phone2.value="<?= $adress["TEL"]["HOME"]; ?>";
		opener.document.formular.cp_fax.value="<?= $adress["TEL"]["FAX"]; ?>";
		opener.document.formular.cp_email.value="<?= $adress["EMAIL"]["INTERNET"]; ?>";
		opener.document.formular.cp_homepage.value="<?= $adress["URL"]; ?>";
		opener.document.formular.name.value="<?= $adress["ORG"]["FIRMA"]; ?>";
		opener.document.formular.cp_abteilung.value="<?= $adress["ORG"]["ABTLG"]; ?>";
		opener.document.formular.cp_birthday.value="<?= db2date($adress["N"]["BDAY"]); ?>";
		opener.document.formular.cp_notes.value="<?= $adress["NOTE"]; ?>";
		self.close();
<?php } else if ($adress and $_POST["src"]=="F") { 
		if ($adress["EMAIL"]["PREF"]) {
			$email=$adress["EMAIL"]["PREF"];
		} else if ($adress["EMAIL"]["WORK"]) {
			$email=$adress["EMAIL"]["WORK"];
		} else {
			$email=$adress["EMAIL"]["INTERNET"]; 
		}
?>		opener.document.neueintrag.name.value="<?= ($adress["ORG"]["FIRMA"])?$adress["ORG"]["FIRMA"]:$adress["N"]["VORNAME"];; ?>";
		opener.document.neueintrag.department_1.value="<?= ($adress["ORG"]["ABTLG"])?$adress["ORG"]["ABTLG"]:$adress["N"]["NACHNAME"].", ".$adress["N"]["VORNAME"]; ?>";
		opener.document.neueintrag.street.value="<?= ($adress["ADR"]["WORK"]["STRASSE"])?$adress["ADR"]["WORK"]["STRASSE"]:$adress["ADR"]["HOME"]["STRASSE"]; ?>";
		opener.document.neueintrag.country.value="<?= ($adress["ADR"]["WORK"]["LAND"])?$adress["ADR"]["WORK"]["LAND"]:$adress["ADR"]["HOME"]["LAND"]; ?>";
		opener.document.neueintrag.zipcode.value="<?= ($adress["ADR"]["WORK"]["PLZ"])?$adress["ADR"]["WORK"]["PLZ"]:$adress["ADR"]["HOME"]["PLZ"]; ?>";
		opener.document.neueintrag.city.value="<?= ($adress["ADR"]["WORK"]["ORT"])?$adress["ADR"]["WORK"]["ORT"]:$adress["ADR"]["HOME"]["ORT"]; ?>";
		opener.document.neueintrag.phone.value="<?= ($adress["TEL"]["WORK"])?$adress["TEL"]["WORK"]:$adress["TEL"]["HOME"]; ?>";
		opener.document.neueintrag.fax.value="<?= $adress["TEL"]["FAX"]; ?>";
		opener.document.neueintrag.email.value="<?= $email ?>";
		opener.document.neueintrag.homepage.value="<?= $adress["URL"]; ?>";
		opener.document.neueintrag.sw.value="";
		opener.document.neueintrag.notes.value="<?= $adress["NOTE"]; ?>";
		self.close();
<?php } ?>
	//-->
	</script>
<body>
	<h2>VCard einlesen</h2>
	<form name="vcard" enctype='multipart/form-data' action="vcard.php" method="post">
		<input type="file" name="datei" size="25" maxlength="10000" accept="text/*"><br><br>
		<input type="hidden" value="<?= ($_GET["src"])?$_GET["src"]:$_POST["src"] ?>" name="src">
		<input type="submit" name="upload" value="einlesen"><br><br>
		<input type="button" value="Abbruch" onClick="self.close()">
	</form>
</body>
</html>
