<?
require_once("inc/stdLib.php");

//Sonderflags jetzt aus der DB
$cp_sonder=getSonder(False);

include("inc/crmLib.php");  //brauch ich die hier? kommen hier die globalen Werte her? jb 17.6.2009
/*
 * Ein Attribut zu einem oder vielen Ansprechpartnern speichern
 * Die Abfrage ist deswegen hier, weil personen1L.tpl ansonsten 
 * TODO Wird jetzt geändert einzelne Ansprechpartner in der Firma aussucht
*/
if (   $_POST["ansprechpartnern_attribute_zuordnen"]        // Der Knopf wurde gedrückt
    && $_POST["cp_sonder"]){                                  // UND Sonderflag gesetzt == Ansprechpartner-Attribut
    /*
     * Alle übergebenen personen-ids (ansprechpartner) werden in einem array 
     * zusammengebaut und dann später als where-bedingung (cp_id für contacts) 
     * verwendet. Man spart sich den Zähler "mitzuschleifen" (solange eine 
     * letzte laufende_nummer da ist, sind noch Ansprechpartner vorhanden) jb 13.6.09
     * Eigentlich war die ursprüngliche Idee einen update analog zu addBatch (java)
     * umzusetzen, scheint leider in der PEAR-DB nicht umgesetzt zu sein (autocommit on/off ist
     * ja was anderes). Deswegen jede Eingabe zwar als Prepared Statement aber doch leider einzeln
     * Oder kann ich die Funktion in db.php ändern?
     * mdb2 executeMultiple scheint hier mein Freund zu sein.
     * ERGÄNZUNG 14.6.: Tada, gibt es auch in der PEAR:DB Somit alles vom Feinsten.
    */
        if ($_POST["cp_sonder"]<0){                         //@holgi, if ohne Klammern, finde ich nicht angenehm. 
                                                            //S.a. http://pear.php.net/manual/de/standards.control.php
        /*
         * @holgi Kommentare mag ich ;-) Dein Gegenüber versteht Dich dann besser
         * Falls ein negativer Wert übergeben wird, sollen alle Attribute gelöscht werden (s.a. attribute.php)
        */
             $_POST["cp_sonder"]=0;
        }
    $ansprechpartner_array=array();                         //initialisierung mit einem leeren Array
    $cp_id_array  = getCSVDataID();                         //Holt nur die Ansprechpartner-IDs aus der DB
    if ($cp_id_array == false){
        /* Abbruch falls hier false kommt. Getestet mit
         *getAll-> return false. Korrekte Fehlermeldung an der Oberfläche jb 17.6.2009
        */
        echo "Fehler beim Lesen der temporären Werte aus der Datenbank. Breche ab, 
                bitte entsprechend die Log-Dateien unter tmp/ auswerten";
        exit;
    }

    //DEBUG
    if ($BROWSERDEBUG == true){
        foreach ($cp_id_array as $rs){
            foreach ($rs as $v => $wert){
                echo "$v:: $wert <br>";
            }
        }
    }
    //DEBUG ENDE

    foreach ($cp_id_array as $rs){                              // Über alle ResultSets der SQL-Abfrage
        foreach ($rs as $currentNumber_ID => $cp_id){           // Müssen wir jetzt noch die cp_ids als Values abfragen
            array_push ($ansprechpartner_array,                 // und dann entsprechend den Array (cp_sonder, cp_id) hinzufügen
                        array($_POST["cp_sonder"], $cp_id));
        }
    }
    /*
     * In db gekapselte Funktion von executeMultiple (s.a. PEAR-Dokumentation db oder mdb2)
     * Wir wollen 'update contacts set cp_sonder=$BITWERT where cp_id=PID_$i'
     * Stand 14.6. Transaktionssicher über alle Werte und als PreparedStatement als Batch (perfetto!)
     * Wie können wir hier einen Rückgabewert prüfen und eine ordentliche Rückmeldung geben
     * Weil der Holger so lustig ist: cp_sonder==alter_wert bitwise und verknüpfen mit neuem wert
     * Beim Löschen wird entsprechend 0 AND 8 = 0 gesetzt.
     * Schade, schade funktioniert leider nicht auf Anhieb mit executeMultiple -> Demnach wieder raus jb 17.6.2009
     * @holgi Vielleicht übersehe ich hier etwas simples?
    */
    if ($db->executeMultiple('UPDATE contacts SET cp_sonder= ? WHERE cp_id= ?', $ansprechpartner_array)){
        /* Das gefällt mir auch noch nicht so ganz, aber ich gebe lieber eine unschöne Erfolgsmeldung aus,
         * als gar keine... jb 14.6.09  Schön gemacht am 16.6.2009:
         * Ok, jetzt ist die Funktion im iframe in personen1L.tpl als attribute.php untergebracht und das ist dann i.O.
         */
        $rueckmeldungSpeichern = "Alle Ansprechpartner erfolgreich mit dem Wert versehen";
    }else{
        $rueckmeldungSpeichern =  "Fehler beim Speichern der Werte. Details befinden sich unter \$Pfad_zur_CRM/tmp/lxcrm.log";
    }

    /*
     * Debug-Ausgabe ANFANG Anm. jb 14.6. in der db.php gibt es den Parameter showError,
     * vielleicht kann/sollte man den global setzen? 
    */
    if ($BROWSERDEBUG == true){
        foreach ($ansprechpartner_array as $ansprechpartner){
            foreach ($ansprechpartner as $ap => $al){
                echo "Ansprechpartner: $ap :: $al  <br>";
            }
        }
        echo "<br> Attribut-Wert zu Ansprechparnter " . $_POST["cp_sonder"];
        echo "Ansprechpartner";
        echo "<br> Anzahl" . $_POST["ANZAHL_ANSPRECHPARTNER"];
        echo "<br> Attribute" . $_POST["cp_sonder"];
        echo "<br> Attribute 2" . $_POST["FID"];
        echo "<br> Attribute 3" . $_POST["ansprechpartnern_attribute_zuordnen"];
    }
    // DEBUG ENDE
}// Ende if von ansprechpartnern_attribute_zuordnen
?>
<html>
<head><title></title>
<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<body>
<form name='form' method='post' action='attribute.php'>
<?php  if ($cp_sonder){
        echo "&nbsp;Attribut:<br>";
        echo "&nbsp;<select class=\"klein\" name=\"cp_sonder\">";
        foreach ($cp_sonder as $row) {
            echo '<option value="'.$row["svalue"].'">'.$row["skey"].'</option>';
        }
    }
?> <option value="-1">Zuordnung l&ouml;schen</option> <br><br><br>
<input type="submit" name="ansprechpartnern_attribute_zuordnen" class="klein" value="zuordnen"> <br><br><span class="mini">&nbsp;Hinweis: Das ausgewählte Attribut überschreibt alle vorhergehenden gesetzten Attribute. Beim Löschen werden alle Attribute wieder entfernt.</span>
</form>
<hr>
<?php
    if ($rueckmeldungSpeichern){
        echo $rueckmeldungSpeichern;
    }
?>
</body>
</html>
