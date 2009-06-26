<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/persLib.php");
	include("inc/laender.php");
	include("inc/UserLib.php");
	include("inc/FirmenLib.php");
	$bgcol[1]="#ddddff";
	$bgcol[2]="#ddffdd";
	$t = new Template($base);
	$Quelle=($_POST["Quelle"])?$_POST["Quelle"]:$_GET["Quelle"];
	if (!$Quelle) $Quelle="C";
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
		} if (count($daten)==1 && $daten<>false && !$_POST["FID1"]) { //@holgi Eine Prüfung FID1 wird dreimal gemacht, vielleicht
                                                                  // kann man das vereinheitlichen?
			header ("location:kontakt.php?id=".$daten[0]["cp_id"]);
		} else if (count($daten)>=1) {
			$t->set_file(array("pers1" => "personen1L.tpl"));
			$t->set_block("pers1","Liste","Block");
			$i=0;
			$bgcol[1]="#ddddff";
			$bgcol[2]="#ddffdd";
            if ($_POST["FID1"]) { 
                $snd="<input type='submit' name='insk' value='zuordnen'><br><a href='firma2.php?Q=$Quelle&fid=".$_POST["FID1"]."'>zur&uuml;ck</a>";  
            } else { 
                $snd=""; $dest=""; 
            };
            clearCSVData();
            insertCSVData(array("ANREDE","TITEL","NAME1","NAME2","LAND","PLZ","ORT","STRASSE","TEL","FAX","EMAIL","FIRMA","GESCHLECHT","ID"),-1);
            if ($daten) foreach ($daten as $zeile) { //Diese Algorithmus macht die Suche bei einer großen Trefferzahl langsam ...
                                                     // TODO executeMultiple ... ;-) jb 16.6.2009
            /* 
             * Der Blog ist sowieso gut und sollte mal hier angemerkt werden 'google: "mokka mit schlag"
             * http://cafe.elharo.com/optimization/how-to-write-network-backup-software-a-lesson-in-practical-optimization/
            */
                insertCSVData(array($zeile["cp_greeting"],$zeile["cp_title"],$zeile["cp_name"],$zeile["cp_givenname"],
                $zeile["cp_country"],$zeile["cp_zipcode"],$zeile["cp_city"],$zeile["cp_street"],
                $zeile["cp_phone1"],$zeile["cp_fax"],$zeile["cp_email"],$zeile["name"],$zeile["cp_gender"],$zeile["cp_id"]),$zeile["cp_id"]);
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
					DEST => $dest,
					QUELLE => $Quelle,
					Q => $Quelle,
					//ANZAHL_ANSPRECHPARTNER => count($daten),	//brauch ich nicht unbedingt
					//laufende_nummer => $i		//die brauch ich unbedingt um die hidden PID_$i zu bilden   //die brauch ich jetzt auch nicht mehr tempcsvdata
				));
				$t->parse("Block","Liste",true);
				$i++;
				if ($i>=$listLimit) {
					$t->set_var(array(
						report => "$listLimit von ".count($daten)." Treffern",
					));
					break;
				}
			}
			/*
				Falls es entsprechende "Sonderflags", d.h. Attribute für An-
				sprechpartner gibt, dies als Liste anzeigen um direkt vielen
				Ansprechpartnern diese(s) Attribut(e) zuzuordnen
			*/
			$t->set_block("pers1","sonder","Block3");
			if ($cp_sonder) while (list($key,$val) = each($cp_sonder)) {
				$t->set_var(array(
					sonder_sel => "",
					sonder_id => $key,
					sonder_name => $val
				));
			$t->parse("Block3","sonder",true);
			} // Ende if $cp_sonder  (entsprechende "Sonderflags")

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
		leertplP($t,$_GET["fid"],"",1,false,$Quelle);
	}
	$t->pparse("out",array("pers1"));
?>
