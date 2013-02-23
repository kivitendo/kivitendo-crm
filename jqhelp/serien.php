<?php

require_once("../inc/stdLib.php");
require_once("crmLib.php");
require_once 'Contact_Vcard_Build.php';

function doVcards($single,$extension,$targetcode,$zip) {

    $sql="select * from tempcsvdata where uid = '".$_SESSION["loginCRM"]."' order by id";
    $csvdata=$_SESSION['db']->getAll($sql);
    if ($csvdata) {
        $pfad = $_SESSION["loginCRM"]."/vcard";
        chkdir($pfad,'.');
        $pfad = "../dokumente/".$_SESSION["mansel"]."/".$pfad."/";
        $felder=explode(":",$csvdata[0]["csvdaten"]);
        $personen = False;
        if (in_array("TITEL",$felder)) $personen = True;
        $i=0;
        foreach ($felder as $feld) $felder[$feld] = $i++;
        array_shift($csvdata);
        if ($single) {
            if ($personen) {
                $filename= "Pvcard.".$extension;
            } else {
                $filename= "Fvcard.".$extension;
            }
            $f = fopen($pfad.$filename,"w");
        }
        $srvcode = strtoupper($_SESSION["charset"]);
        $cnt=0;
        foreach ($csvdata as $row) {
            $vcard = new Contact_Vcard_Build();
            if ($targetcode !=  $srvcode) 
                $row["csvdaten"] = iconv($srvcode,$targetcode,$row["csvdaten"]);
            $data = explode(":",$row["csvdaten"]);
            $vcard->setFormattedName($data[$felder["NAME1"]]);
            if ($data[$felder["NAME2"]]) {
                if ($personen) {
                    $vcard->setName($data[$felder["NAME2"]],$data[$felder["NAME1"]],"",$data[$felder["ANREDE"]],$data[$felder["TITEL"]]);
                } else {
                    $vcard->setName($data[$felder["NAME1"]],$data[$felder["NAME2"]],"","","");
                }
            } else {
                if ($personen) {
                    $vcard->setName($data[$felder["NAME1"]],"","",$data[$felder["ANREDE"]],$data[$felder["TITEL"]]);
                } else {
                    $vcard->setName($data[$felder["NAME1"]],"","","","");
                }
            }
            $vcard->addAddress('', '', $data[$felder["STRASSE"]], $data[$felder["ORT"]], 
                                    '', $data[$felder["PLZ"]], $data[$felder["LAND"]]);
            $vcard->addParam('TYPE', 'WORK');
            if ($personen) {
                $vcard->addOrganization($data[$felder["FIRMA"]]);
            } else {
                $vcard->addOrganization($data[$felder["NAME1"]]);
                $vcard->addOrganization($data[$felder["NAME2"]]);
            }
            if ($data["email"]) {
                $vcard->addEmail($data[$felder["EMAIL"]]);
                $vcard->addParam('TYPE', 'WORK');
            }
            if ($data["phone"]) {
                $vcard->addTelephone($data[$felder["TEL"]]);
                $vcard->addParam('TYPE', 'WORK');
            }
            if ($data["fax"]) {
                $vcard->addTelephone($data[$felder["FAX"]]);
                $vcard->addParam('TYPE', 'FAX');
            }
            // get back the vCard and print it
            $text = $vcard->fetch();
            if (!$single) {
                if ($personen) {
                    $f = fopen($pfad."/".$data[$felder["ID"]].$data[$felder["NAME1"]]."_vcard.".$extension,"w");
                } else {
                    $f = fopen($pfad."/".$data[$felder["KDNR"]]."_vcard.".$extension,"w");
                }
                fputs($f,$text);
                fclose($f);
            } else {
                fputs($f,$text);
            }
            unset($vcard);
            unset($text);
            $cnt++;
        };
        if ($single) fclose($f);
        if ($zip) {
            require 'pclzip.lib.php';
            require 'zip.lib.php';
            if (!$single) {
                $oldpath = getCWD();
                chdir($pfad);
                $archiveFiles = glob("*_vcard.".$extension);
                chdir($oldpath);
            } else {
                //$archiveFiles[] = "vcard.".$_POST["extension"];
                $archiveFiles[] = $filename; 
            }
            $filename= "vcard.".$extension.".zip";
            $archive = new PclZip($pfad.$filename);
            $v_list = $archive->create($pfad.$_SESSION["loginCRM"], '', $pfad.$_SESSION["loginCRM"], '', "vcardPreAdd");
            $zip = new zipfile();
            for($i = 0; $i < count($archiveFiles); $i++) {
                $file = $archiveFiles[$i];
                // zip.lib dirty hack
                $fp = fopen($pfad.$file, "r");
                $content = @fread($fp, filesize($pfad.$file));
                fclose($fp);
                $zip->addFile($content, $file);
                unlink($pfad.$file);
            }
            $fp = fopen($pfad.$filename, "w+");
            fputs($fp, $zip->file());
            fclose($fp);
        }
        if ($single || $zip) {
            echo "[<a href='".$pfad.$filename."'>download</a>]<br />";
        } else {
            
        }
    };
    echo "$cnt Adressen bearbeitet.";

}

function serbrief($data) {
        $rc = file_exists("../dokumente/".$_SESSION["mansel"]."/tmp/".$data['filename']);
        if ( $rc ) {
            require_once("documents.php");
            $dest = "./dokumente/".$_SESSION["mansel"]."/serbrief/";
            $ok = chkdir("serbrief");
            copy("../dokumente/".$_SESSION["mansel"]."/tmp/".$data['filename'],'.'.$dest.$data['filename']);
            unlink ("../dokumente/".$_SESSION["mansel"]."/tmp/".$data['filename']);

            //Verzeichnis anlegen für die Serienbriefe
            @mkdir(".".$dest.substr($_POST['filename'],0,-4));
            
            //Dokument in db speichern
            $dbfile=new document();
            $dbfile->setDocData("descript",$data["subject"]);
            $dbfile->setDocData("pfad","serbrief");
            $dbfile->setDocData("name",$data['filename']);
            $dbfile->setDocData("descript",$data["body"]);
            $rc=$dbfile->newDocument();
            $dbfile->saveDocument();
            
            //benötigte Daten in Session speichern
            $_SESSION["dateiId"] = $dbfile->id;
            $_SESSION["SUBJECT"]=$_POST["subject"];
            $_SESSION["BODY"]=$_POST["body"];
            $_SESSION["DATE"]=$_POST["datum"];
            $_SESSION["src"]=$_POST["src"];
            $_SESSION["savefiledir"]=$dest.substr($_POST['filename'],0,-4);
            $_SESSION["datei"]=$_POST['filename'];

            echo json_encode( array( 'rc'=>true, "msg"=>"Datei gesichert") );
        } else {
            echo json_encode( array( 'rc'=>false, "msg"=>"Fehler beim Upload ".$_POST['filename']));
        }

}

if ($_GET['task'] == 'vcard') {
     doVcards($_GET['single'],$_GET['extension'],$_GET['targetcode'],$_GET['zip']) ;
} else if ($_POST['task'] == 'brief') {
     serbrief($_POST);
};
?>
