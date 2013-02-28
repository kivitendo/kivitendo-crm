<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    $did=($_GET["did"])?$_GET["did"]:$_POST["did"];
    if ($_POST["ok"]) {
        $doc = False;
        $ext = strtolower(substr($_FILES['file']['name'],-3));
        if ( $ext == 'sxw' ) {
            $_POST["applikation"] = "O";
            include('inc/phpOpenOffice.php');
            $doc = new phpOpenOffice();
        } else if ( $ext == 'rtf' ) {
            $_POST["applikation"] = "R";
            include('inc/phpRtf.php');
            $doc = new phpRTF();
        } else if ( $ext == 'tex' ) {
            $_POST["applikation"] = "T";
            include('inc/phpTex.php');
            $doc = new phpTex();
        } else if ( $ext == 'xls' ) {
            $_POST["applikation"] = "B";
            require('inc/phpBIN.php');
            $doc = new phpBIN();
        };
        if ( $doc ) {
            $did=saveDocVorlage($_POST,$_FILES);
            if ( $_POST['did'] != $did ) {
                $doc->loadDocument("vorlage/".$_FILES['file']['name']);
                $vars = $doc->getTags();
                if ( $ext == 'rtf') {
                    $doc->savefile("vorlage/".$_FILES['file']['name']);
                };
                $data['beschreibung'] = '';
                $data['zeichen'] = '.';
                $data['laenge'] = 25;
                $data['docid'] = $did;
                $p = 1;
                foreach($vars as $hit) {
                     $data['feldname'] = $hit; 
                     $data['platzhalter'] = $hit; 
                     $data['position'] = $p;
                     insDocFld($data);
                     $p++;
                }
            }
            $msg = '';
        } else {
            $msg = 'Falsches Dateiformat';
        }
    } else if ($_POST["del"]) {
        $did=delDocVorlage($_POST);
    }
    $link2="dokument2.php?did=".$_GET["did"];
    if ($did) {
        $link3="dokument3.php?docid=".$did;
    } else {
        $link3="";
    }
    if ($did) {
        $docdata=getDOCvorlage($did);
    }
    $t = new Template($base);
    doHeader($t);
    $t->set_file(array("doc" => "dokument2.tpl"));
    $t->set_var(array(
            Link2 => $link2,
            Link3 => $link3,
            vorlage      => $docdata["document"]["vorlage"],
            beschreibung =>    $docdata["document"]["beschreibung"],
            file =>     $docdata["document"]["file"],
            did  => $did,
            msg  => $msg,
        ));
    $t->pparse("out",array("doc"));

?>
