<?php
/****************************************************************************************
*** Erzeugt eine Tabelle mit offenen bzw. allen Aufträgen, Angeboten oder Rechungen   ***
****************************************************************************************/

//ToDo: is Customer or Vendor!!!!!!


include ("../inc/crmLib.php");
include ("../inc/stdLib.php");
if($_GET['was']=="ord") {
    echo "Tab Aufträge: Tabellen noch nicht implementiert";
    $rs = getIOQ($_GET['fid'],"ord" );
     print_r($rs);
}
else if($_GET['was']=="quo") {
    echo "Tab Angebote: Tabellen noch nicht implementiert";
    $rs = getIOQ($_GET['fid'],"quo" );
    print_r($rs);
    }
else if($_GET['was']=="inv") {
    echo "Tab Rechnungen: Tabellen";
    $rs = getIOQ($_GET['fid'],"inv" );
    print_r($rs);
    }
?>
    