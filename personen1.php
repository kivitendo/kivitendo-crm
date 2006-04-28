<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/persLib.php");
	include("inc/laender.php");
	include("inc/UserLib.php");
	
	$bgcol[1]="#ddddff";
	$bgcol[2]="#ddffdd";
	$t = new Template($base);
	
	if ($_GET["first"]) {
		$_POST["cp_name"]=$_GET["first"];
		$_POST["fuzzy"]="%";
	}
	if ($_POST["suche"]=="suchen" || $_GET["first"]) {
		$daten=suchPerson($_POST);
		if (!chkAnzahl($daten,$tmp)) {
			$msg="Trefferanzahl zu gro&szlig;. Bitte einschr&auml;nken.";
			$btn1="";
			vartplP($t,$_POST,$msg,$btn1,$btn1,$btn1,"Anrede","white",$_POST["FID1"],1);
		} if (count($daten)==1 && $daten<>false && !$_POST["FID1"]) {
			header ("location:kontakt.php?id=".$daten[0]["cp_id"]);
		} else if (count($daten)>=1) {
			$t->set_file(array("pers1" => "personen1L.tpl"));
			$t->set_block("pers1","Liste","Block");
			$i=0;
			$bgcol[1]="#ddddff";
			$bgcol[2]="#ddffdd";
			if ($_POST["FID1"]) { 
				$dest=($_POST["Quelle"]=="F")?"firma":"liefer"; 
				$snd="<input type='submit' name='insk' value='zuordnen'><br><a href='".$dest."2.php?fid=".$_POST["FID1"]."'>zur&uuml;ck</a>";  //<input type='checkbox' value='".$zeile["cp_id"]."'>alle
                        } else { $snd=""; $dest=""; };
			clearCSVData();
            		insertCSVData(array("ANREDE","TITEL","NAME1","NAME2","LAND","PLZ","ORT","STRASSE","TEL","FAX","EMAIL","FIRMA","ID"));
			if ($daten) foreach ($daten as $zeile) {
				insertCSVData(array($zeile["cp_greeting"],$zeile["cp_title"],$zeile["cp_name"],$zeile["cp_givenname"],
							$zeile["cp_country"],$zeile["cp_zipcode"],$zeile["cp_city"],$zeile["cp_street"],
							$zeile["cp_phone1"],$zeile["cp_fax"],$zeile["cp_email"],$zeile["name"],$zeile["cp_id"]));
				if ($_POST["FID1"]) {
					$insk="<input type='checkbox' name='kontid[]' value='".$zeile["cp_id"]."'>"; 
				} else { 
					$insk=""; 
				};
				$t->set_var(array(
					PID => $zeile["cp_id"],
					LineCol => $bgcol[($i%2+1)],
					Name => $zeile["cp_name"].", ".$zeile["cp_givenname"],
					Plz => $zeile["cp_zipcode"],
					Ort => $zeile["cp_city"],
					Telefon => $zeile["cp_phone1"],
					eMail => $zeile["cp_email"],
					Firma => $zeile["name"],
					TBL => $zeile["tbl"],
					insk => $insk,
					DEST => $dest
				));
				$t->parse("Block","Liste",true);
				$i++;
			}
			$t->set_var(array(
				snd => $snd,
				FID => $_POST["FID1"],
				no => ($_POST["FID1"])?"return;":"",
			));
		} else {
			$msg="Leider nichts gefunden.";
			$btn1="";
			vartplP($t,$_POST,$msg,$btn1,$btn1,$btn1,"Anrede","white",$_POST["FID1"],1);
		}
	} else {
		leertplP($t,$_GET["fid"],"",1,false,$_GET["Quelle"]);
	}
	$t->pparse("out",array("pers1"));
?>
