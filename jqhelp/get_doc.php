<?php
/****************************************************************************************
*** Erzeugt eine Tabelle mit offenen bzw. allen Aufträgen, Angeboten oder Rechungen   ***
****************************************************************************************/

//ToDo: bgCol, sortierbar, suche, LimitAnzahl, klickbar!!


include ("../inc/crmLib.php");
include ("../inc/stdLib.php");
$rs = getIOQ($_GET['fid'],$_GET['Q'],$_GET["type"]);
echo "<table id='result_ioq' class='tablesorter'>\n"; 
echo "<thead><tr><th>.:date:.</th><th>.:decription:.</th><th>.:amount:.</th><th>.:number:.</th></tr></thead>\n<tbody>\n";
$i=0; 
if ($rs) 
    foreach($rs as $row) { 
        echo "<tr class='bgcol".($i%2+1)."' onClick='OpenIOQ(\"C\",".$row["id"].");'>". 
             "<td class=\"liste\">".$row["date"]."</td><td class=\"liste\">".$row["description"]."</td>". 
             "<td class=\"liste\">".$row["amount"]."</td><td class=\"liste\">".$row["number"]."</td></tr>\n"; 
        $i++; 
    } 
    echo "</tbody></table>\n"; 

?>
    