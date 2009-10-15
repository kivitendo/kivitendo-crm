<?
// $Id: firma2.php 4299 2009-06-15 10:06:58Z hlindemann $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/FirmenLib.php");
	include("inc/persLib.php");
    require("firmacommon".XajaxVer.".php");
	$fid=($_GET["fid"])?$_GET["fid"]:$_POST["fid"];
	$Q=($_GET["Q"])?$_GET["Q"]:$_POST["Q"];	
	$kdhelp=getWCategorie(true);
	if ($_POST["insk"]) {
		insFaKont($_POST);
	}
	if ($_GET["ldap"]) {
		include("inc/ldapLib.php");
		$rc=Ldap_add_Customer($_GET["fid"]);
	}
	/*
	 * Ein Attribut zu einem oder vielen Ansprechpartnern speichern
	 * Die Abfrage ist deswegen hier, weil personen1L.tpl ansonsten einzelne Ansprechpartner in der Firma aussucht
	*/
	if (	 $_POST["ansprechpartnern_attribute_zuordnen"]	//			Der Knopf wurde gedrückt
			&& $_POST["cp_sonder"]														// UND	Sonderflag gesetzt == Ansprechpartner-Attribut
			&& $_POST["PID_0"]){															// UND	Mindestens eine Ansprechpartner-ID

		/*
		 *	Alle übergebenen personen-ids (ansprechpartner) werden in einem array 
		 *  zusammengebaut und dann später als where-bedingung (cp_id für contacts)
		 *  verwendet. Man spart sich den Zähler "mitzuschleifen" (solange eine 
		 *  letzte laufende_nummer da ist, sind noch Ansprechpartner vorhanden) jb 13.6.09
		 *	Eigentlich war die ursprüngliche Idee einen update analog zu addBatch (java)
		 *  umzusetzen, scheint leider in der PEAR-DB nicht umgesetzt zu sein (autocommit on/off ist
		 *	ja was anderes). Deswegen jede Eingabe zwar als Prepared Statement aber doch leider einzeln
		 *	Oder kann ich die Funktion in db.php ändern?
		 *	mdb2 executeMultiple scheint hier mein Freund zu sein.
		 *  ERGÄNZUNG 14.6.: Tada, gibt es auch in der PEAR:DB Somit alles vom Feinsten.
		*/
		if ($_POST["cp_sonder"]<0) $_POST["cp_sonder"]=0;
		$i=0;																				//schleifenzähler auf 0
		$ansprechpartner_array=array();						//initialisierung mit einem leeren Array
		while ($_POST["PID_$i"]){						// Über alle Ansprechpartner
			array_push ($ansprechpartner_array,				// Fügen wir den Array (cp_sonder, cp_id) hinzu
					array($_POST["cp_sonder"], $_POST["PID_$i"]));
			$i++;
		}
		/*
		 * In db gekapselte Funktion von executeMultiple (s.a. PEAR-Dokumentation db oder mdb2)
		 * Wir wollen 'update contacts set cp_sonder=$BITWERT where cp_id=PID_$i'
		 * Stand 14.6. Transaktionssicher über alle Werte und als PreparedStatement als Batch (perfetto!)
		 * Wie können wir hier einen Rückgabewert prüfen und eine ordentliche Rückmeldung geben
		*/
		if ($db->executeMultiple('UPDATE contacts SET cp_sonder= ? WHERE cp_id= ?', $ansprechpartner_array)){
			/* Das gefällt mir auch noch nicht so ganz, aber ich gebe lieber eine unschöne Erfolgsmeldung aus,
			 * als gar keine... jb 14.6.09
			*/
//			ob_start();
			echo "Alle Ansprechpartner erfolgreich mit dem Wert versehen";
//			Header("Location: ");
//			ob_flush();
		}else{
			echo "Fehler beim Speichern der Werte. Details befinden sich unter \$Pfad_zur_CRM/tmp/lxcrm.log";
		}


		// Debug-Ausgabe ANFANG Anm. jb 14.6. in der db.php gibt es den Parameter showError,
		// vielleicht kann/sollte man den global setzen?
		if ($BROWSERDEBUG == true){
			foreach ($ansprechpartner_array as $ansprechpartner){
				echo "Ansprechpartner: $ansprechpartner  <br>";
			}
			echo "<br> Attribut-Wert zu Ansprechparnter " . $_POST["cp_sonder"];
			echo "Ansprechpartner";
			echo "<br> Anzahl" . $_POST["ANZAHL_ANSPRECHPARTNER"];
			echo "<br> Attribute" . $_POST["cp_sonder"];
			echo "<br> Attribute 2" . $_POST["FID"];
			echo "<br> Attribute 3" . $_POST["ansprechpartnern_attribute_zuordnen"];
		}// Debug-Ausgabe ENDE
		exit;
	}// Ende if von ansprechpartnern_attribute_zuordnen

	// Einen Kontakt anzeigen lassen
	if ($_GET["id"]) {
		$co=getKontaktStamm($_GET["id"]);
		if (empty($co["cp_cv_id"])) {
			// Ist keiner Firma zugeordnet
			$id=$_GET["id"];
			$fa["name"]="Einzelperson";
			$fa["department_1"]="";
			$fa["department_2"]="";
			$fa["zipcode"]="";
			$fa["city"]="";
			$fa["id"]=0;
			$link1="#";
			$link2="#";
			$link3="#";
			$link4="firma4.php?pid=$id";
			$ep="&ep=1";
			$init="";
		} else {
			$id=$_GET["id"];
			$fid=$co["cp_cv_id"];
			$fa["id"]=0;
			$ep="";
		}
	} 
	if ($fid>0){ 
		// Aufruf mit einer Firmen-ID
		$co=getAllKontakt($fid);
		$liste="";
		if (count($co)>1) {
			// Mehr als einen Kontakt gefunden
			foreach ($co as $row) {
				$liste.="<option value='".$row["cp_id"];
				$liste.=($row["cp_id"]==$id)?"' selected>":"'>";
				$liste.=$row["cp_name"].", ".$row["cp_givenname"]."\n";
			}
			$co=$co[0];
			$init=$co["cp_id"];
			$id=$co["cp_id"];
		} else if (count($co)==0 || $co==false) {
			// Keinen Kontakt gefunden
			$co["cp_name"]="Leider keine Kontakte gefunden";
			$init="";
		} else {
			// Genau ein Kontakt
			$co=$co[0]; 
			$id=$co["cp_id"];
		}
		$fa=getFirmenStamm($fid,true,$Q);
		$KDNR=($Q=="C")?$fa["customernumber"]:$fa["vendornumber"];
		$link1="firma1.php?Q=$Q&id=$fid";
		$link2="firma2.php?Q=$Q&fid=$fid";
		$link3="firma3.php?Q=$Q&fid=$fid";
		$link4="firma4.php?Q=$Q&fid=$fid&pid=".$co["cp_id"];
	} else if ($ep=="") {
		$co["cp_name"]="Fehlerhafter Aufruf";
		$init="";
		$link1="#";
		$link2="#";
		$link3="#";
		$link4="#";
	}
	if (trim($co["cp_grafik"])<>"") {
		$Image="<img src='dokumente/".$_SESSION["mansel"]."/$Q$KDNR/".$_GET["id"]."/kopf.".$co["cp_grafik"]."' ".$co["size"].">";
	} else {
		$Image="";
	}
	if ($co["cp_homepage"]<>"") {
		$internet=(preg_match("^://^",$co["cp_homepage"]))?$co["cp_homepage"]:"http://".$co["cp_homepage"];
	};
	$sonder="";
	if ($cp_sonder) while (list($key,$val) = each($cp_sonder)) {
		$sonder.=($co["cp_sonder"] & $key)?"($val) ":"";
	}
	$t = new Template($base);
	$t->set_file(array("co1" => "firma2.tpl"));
	$t->set_var(array(
			INIT	=> ($init=="")?"showOne($id)":"showContact()",
			AJAXJS  => $xajax->printJavascript(XajaxPath),
			FAART => ($Q=="C")?".:Customer:.":".:Vendor:.",   //"Kunde":"Lieferant",
			interv	=> $_SESSION["interv"]*1000,
			Q => $Q,
			Link1 => $link1,
			Link2 => $link2,
			Link3 => $link3,
			Link4 => $link4,
			Fname1 => $fa["name"],
			Fdepartment_1 => $fa["department_1"],
			Fdepartment_2 => $fa["department_2"],
			Plz => $fa["zipcode"],
			Ort => $fa["city"],
			Street => $fa["street"],
			PID => $co["cp_id"],
			FID => ($co["cp_cv_id"])?$co["cp_cv_id"]:$fid,
			customernumber	=> $KDNR,
			moreC => ($liste<>"")?"visible":"hidden",
			kontakte => $liste,
            tools => ($tools)?"visible":"hidden",
			ep => $ep,
			Edit => ".:edit:.",
			none => ($ep=="" && $init=="")?"hidden":"visible",
			chelp 		=> ($kdhelp)?"visible":"hidden"
	));
	if ($kdhelp) { 
		$t->set_block("co1","kdhelp","Block1");
		$tmp[]=array("id"=>-1,"name"=>"Online Kundenhilfe");
		$kdhelp=array_merge($tmp,$kdhelp); 
		foreach($kdhelp as $col) {
			$t->set_var(array(
				cid => $col["id"],
				cname => $col["name"]
			));	
			$t->parse("Block1","kdhelp",true);
		};
	}
	$t->Lpparse("out",array("co1"),$_SESSION["lang"],"firma");
?>
