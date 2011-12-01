<?php
require_once("inc/stdLib.php");
if ($_POST) {
    require_once("inc/crmLib.php");
    require_once 'Contact_Vcard_Build.php';
    $sql="select * from tempcsvdata where uid = '".$_SESSION["loginCRM"]."' order by id";
    $csvdata=$db->getAll($sql);
    if ($csvdata) {
        $pfad = $_SESSION["loginCRM"]."/vcard";
        chkdir($pfad);
        $pfad = "./dokumente/".$_SESSION["mansel"]."/".$pfad."/";
        $felder=explode(":",$csvdata[0]["csvdaten"]);
        $personen = False;
        if (in_array("TITEL",$felder)) $personen = True;
        $i=0;
        foreach ($felder as $feld) $felder[$feld] = $i++;
        array_shift($csvdata);
        if ($_POST["single"]) {
            if ($personen) {
                $filename= "Pvcard.".$_POST["extension"];
            } else {
                $filename= "Fvcard.".$_POST["extension"];
            }
            $f = fopen($pfad.$filename,"w");
        }
        $srvcode = strtoupper($_SESSION["charset"]);
        $cnt=0;
        foreach ($csvdata as $row) {
            $vcard = new Contact_Vcard_Build();
            if ($_POST["targetcode"] !=  $srvcode) 
                $row["csvdaten"] = iconv($srvcode,$_POST["targetcode"],$row["csvdaten"]);
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
            if (!$_POST["single"]) {
                if ($personen) {
                    $f = fopen($pfad."/".$data[$felder["ID"]].$data[$felder["NAME1"]]."_vcard.".$_POST["extension"],"w");
                } else {
                    $f = fopen($pfad."/".$data[$felder["KDNR"]]."_vcard.".$_POST["extension"],"w");
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
        if ($_POST["single"]) fclose($f);
        if ($_POST["zip"]) {
            require 'inc/pclzip.lib.php';
            require 'inc/zip.lib.php';
            if (!$_POST["single"]) {
                $oldpath = getCWD();
                chdir($pfad);
                $archiveFiles = glob("*_vcard.".$_POST["extension"]);
                chdir($oldpath);
            } else {
                //$archiveFiles[] = "vcard.".$_POST["extension"];
                $archiveFiles[] = $filename; 
            }
            $filename= "vcard.".$_POST["extension"].".zip";
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
        if ($_POST["single"] || $_POST["zip"]) {
            echo "<a href='".$pfad.$filename."'>download</a><br />";
        } else {
            
        }
    };
    echo "$cnt Adressen bearbeitet.";
} else {
    $codecs = array("ISO_8859-1","ISO_8859-15","ASCII","UTF-8","UTF-7","Windows-1252");
    $srvcode = strtoupper($_SESSION["charset"]);
    $p = sprintf("sel%d",array_search($srvcode,$codecs));
    ${$p} = "selected";
?>
<form name="vcard" method="post" action="servcard.php">
   Serverkodierung: <?php echo $srvcode ?>
   Zielkodierung:  <select name="targetcode">
        <option value="ISO_8859-1" <?php echo $sel0 ?>>ISO_8859-1</option>
        <option value="ISO_8859-15" <?php echo $sel1 ?>>ISO_8859-15</option>
        <option value="ASCII" <?php echo $sel2 ?>>ASCII</option>
        <option value="UTF-8" <?php echo $sel3 ?>>UTF-8</option>
        <option value="UTF-7" <?php echo $sel4 ?>>UTF-7</option>
        <option value="Windows-1252" <?php echo $sel5 ?>>Windows-1252</option>
    </select><br />
   Extension: <input type="input" name="extension" size="5" value="vcf"><br />
   Zip-Komprimierung <input type="radio" name="zip" value="0" checked>Nein <input type="radio" name="zip" value="1">Ja<br />
   Singel-File <input type="radio" name="single" value="1" checked><br /> Je Adresse ein File  <input type="radio" name="single" value="0"><br />
   <input type="submit" name="send" value="erstellen"><br />
</form>
<?php } ?>
