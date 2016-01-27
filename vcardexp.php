<?php
    @session_start();
    require_once 'Contact_Vcard_Build.php';
    require_once __DIR__.'/inc/stdLib.php';
    require_once __DIR__.'/inc/FirmenLib.php';
    $Q=($_GET["Q"])?$_GET["Q"]:$_POST["Q"];
    // instantiate a builder object (defaults to version 3.0)
    //echo "test".$_SESSION['crmpath'];
    $vcard = new Contact_Vcard_Build();
    //printArray( $vcard );
    if (varExist($_GET, "pid")) {
        include( __DIR__.'/inc/persLib.php');
        $data=getKontaktStamm($_GET["pid"]);
        //printArray( $data );
                //UID
                $UID='P'.$_GET["pid"].'@'.$_SESSION['mandant'];
                $vcard->setKey($_GET["pid"]);
                $vcard->setUniqueID($UID);
                $vcard->setRevision(date('c'));
        // set a formatted name
        $vcard->setFormattedName($data["cp_givenname"]." ".$data["cp_name"]);
        // set the structured name parts
        $prefix = varExist($data, 'cp_greeting' ) ? $data["cp_greeting"]." ".$data["cp_title"] : $data["cp_title"];
        $vcard->setName($data["cp_name"],$data["cp_givenname"],"",$prefix,"");
        // add a work email.  note that we add the value
        // first and the param after -- Contact_Vcard_Build
        // is smart enough to add the param in the correct place.
        if ($data["cp_email"]) {
            $vcard->addEmail($data["cp_email"]);
            $vcard->addParam('TYPE', 'WORK');
            $vcard->addParam('TYPE', 'PREF');
        }
        // add a work address
        $vcard->addAddress('', '', $data["cp_street"], $data["cp_city"], '', $data["cp_zipcode"], $data["cp_country"]);
        $vcard->addParam('TYPE', 'WORK');
        if ($data["cp_birthday"]) $vcard->setBirthday($data["cp_birthday"]);
        if ($data["cp_notes"]) $vcard->setNote($data["cp_notes"]);
        if ($data["cp_phone1"]) {
            $vcard->addTelephone($data["cp_phone1"]);
            $vcard->addParam('TYPE', 'WORK');
            $vcard->addParam('TYPE', 'PREF');
        }
        if ($data["cp_fax"]) {
            $vcard->addTelephone($data["cp_fax"]);
            $vcard->addParam('TYPE', 'FAX');
        }
        if ($data["cp_position"]) $vcard->setTitle($data["cp_position"]);
        if ($data["cp_cv_id"] && $data["tabelle"]=="C") {
            $fa=getFirmenStamm($data["cp_cv_id"]);
            $vcard->addAddress('', '', $fa["street"], $fa["city"], '', $fa["zipcode"], $fa["country"]);
            $vcard->addParam('TYPE', 'DOM');
                        if ($fa["phone"]) {
                            $vcard->addTelephone($fa["phone"]);
                            $vcard->addParam('TYPE', 'DOM');
                        }
            if ($data["cp_abteilung"]) {
                $vcard->addOrganization(array($fa["name"],$data["cp_abteilung"]));
            } else {
                $vcard->addOrganization($fa["name"]);
            }

        } else if ($data["cp_cv_id"] && $data["tabelle"]=="V") {
            $fa=getFirmenStamm($data["cp_cv_id"],true,"V");
            $vcard->addAddress('', '', $fa["street"], $fa["city"], '', $fa["zipcode"], $fa["country"]);
            $vcard->addParam('TYPE', 'DOM');
                        if ($fa["phone"]) {
                            $vcard->addTelephone($fa["phone"]);
                            $vcard->addParam('TYPE', 'DOM');
                        }
            $vcard->addOrganization($fa["name"]);
        }
    } else if (varExist($_GET, "fid")) {
        $data=getFirmenStamm($_GET["fid"],true,$Q);
                //UID
                $UID=$Q.$_GET["fid"].'@'.$_SESSION['mandant'];
                $vcard->setKey($Q.$data["nummer"]);
                $vcard->setUniqueID($UID);
                $vcard->setRevision(date('c'));
        $vcard->setFormattedName($data["name"]);
        if ($data["department_1"]) {
            $vcard->setName($data["name"],$data["department_1"],"","","");
        } else {
            $vcard->setName($data["name"],"","","","");
        }
        $vcard->addAddress('', '', $data["street"], $data["city"], '', $data["zipcode"], $data["country"]);
        $vcard->addParam('TYPE', 'WORK');
        $vcard->addOrganization($data["name"]);
        $vcard->addOrganization($data["department_1"]);
        if ($data["email"]) {
            $vcard->addEmail($data["email"]);
            $vcard->addParam('TYPE', 'WORK');
            $vcard->addParam('TYPE', 'PREF');
        }
        if ($data["phone"]) {
            $vcard->addTelephone($data["phone"]);
            $vcard->addParam('TYPE', 'WORK');
            $vcard->addParam('TYPE', 'PREF');
        }
        if ($data["fax"]) {
            $vcard->addTelephone($data["fax"]);
            $vcard->addParam('TYPE', 'FAX');
        }
        //Kommt auf den Empfänger an, weiter testen.
        //if ($data["notes"]) $vcard->setNote(str_replace("\r",chr(10),$data["notes"]));
        if ($data["notes"]) $vcard->setNote($data["notes"]);
    } else {
        exit;
    }

    // get back the vCard and print it
    $text = $vcard->fetch();
    header("Content-type:text/x-vCard; charset=utf-8");
    header("Content-Disposition: attachment; filename=vcardexport.vcf");
    echo $text;

?>
