<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    $t = new Template($base);
    $t->set_file(array("dhl" => "dhladm.tpl"));
    $t->set_block("dhl","produkte","Block");
    $sender = array('SEND_NAME1','SEND_NAME2','SEND_STREET','SEND_HOUSENUMBER','SEND_PLZ','SEND_CITY','SEND_COUNTRY');
    doHeader($t);
    if ( $_POST['save'] ) {
        $ok = true;
        $msg = 'Daten gesichert.';
        $rc = $_SESSION['db']->begin();
        $sql = "DELETE FROM crmdefaults WHERE grp = 'dhl'";
        $rc = $_SESSION['db']->query($sql);
        if ( !$rc ) {
            $_SESSION['db']->rollback();
            $ok = false;
            $msg = 'Fehler beim Löschen';
        } else {
            $cnt = count($_POST['prodname']);
            if ( $cnt > 0 ) for( $i=0; $i<$cnt; $i++) {
                $key = $_POST['prodname'][$i];
                $val = $_POST['produkt'][$i];
                if ( $key != '' and $val != '' ) {
                    $sql = "INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('$key','$val','dhl',".$_SESSION['loginCRM'].")";
                    $rc = $_SESSION['db']->query($sql);
                    if ( !$rc ) {
                        $_SESSION['db']->rollback();
                        $msg = 'Fehler beim Sichern der Produktdaten.';
                        $ok = false;
                        break;
                    }
                };
            };
            unset ( $_POST['prodname'] );
            unset ( $_POST['delprodname'] );
            unset ( $_POST['produkt'] );
            unset ( $_POST['save'] );
            if ( $ok ) while ( list($key,$val)  = each($_POST) ) {
                $sql = "INSERT INTO crmdefaults (key,val,grp,employee) VALUES ('$key','$val','dhl',".$_SESSION['loginCRM'].")";
                $rc = $_SESSION['db']->query($sql);
                if ( !$rc ) {
                        $_SESSION['db']->rollback();
                        $msg = 'Fehler beim Sichern der Absenderdaten.';
                        $ok = false;
                        break;
                };
            };
            if ( $ok ) $_SESSION['db']->commit();
        };
    };
    $sql = "SELECT * FROM crmdefaults WHERE grp = 'dhl'";
    $rs = $_SESSION['db']->getAll($sql);
    if ( $rs ) foreach ( $rs as $row ) {
        if ( preg_match('/SEND/',$row['key']) ) { 
            $t->set_var( array($row['key'] => $row['val']) );
        } else {
            $t->set_var( array('prodname' => $row['key'], 'produkt' => $row['val']) );
            $t->parse("Block","produkte",true);
        }
    };
    $t->set_var( array('msg' => $msg) );
    $t->pparse("out",array("dhl"));
?>
    
