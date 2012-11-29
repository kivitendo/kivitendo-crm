<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    $did=($_GET["did"])?$_GET["did"]:$_POST["did"];
    if ($_POST["ok"]) {
        $did=saveDocVorlage($_POST,$_FILES);
    } else if ($_POST["del"]) {
        $did=delDocVorlage($_POST);
    }
    $link1="dokument1.php";
    $link2="dokument2.php?did=".$_GET["did"];
    if ($did) {
        $link3="dokument3.php?docid=".$did;
    } else {
        $link3="";
    }
    $link4="";
    if ($did) {
        $docdata=getDOCvorlage($did);
    }
    $t = new Template($base);
    $menu =  $_SESSION['menu'];
    $t->set_var(array(
        JAVASCRIPTS   => $menu['javascripts'],
        STYLESHEETS   => $menu['stylesheets'],
        PRE_CONTENT   => $menu['pre_content'],
        START_CONTENT => $menu['start_content'],
        END_CONTENT   => $menu['end_content']
    ));
    $t->set_file(array("doc" => "dokument2.tpl"));
    $t->set_var(array(
            ERPCSS => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
            Link1 => $link1,
            Link2 => $link2,
            Link3 => $link3,
            Link4 => $link4,
            vorlage    => $docdata["document"]["vorlage"],
            beschreibung =>    $docdata["document"]["beschreibung"],
            file =>    $docdata["document"]["file"],
            sel1 =>    ($docdata["document"]["applikation"]=="O")?"checked":"",
            sel2 =>    ($docdata["document"]["applikation"]=="R")?"checked":"",
            sel3 => ($docdata["document"]["applikation"]=="B")?"checked":"",
            sel4 =>    ($docdata["document"]["applikation"]=="T")?"checked":"",
            did => $did
        ));
    $t->pparse("out",array("doc"));

?>
