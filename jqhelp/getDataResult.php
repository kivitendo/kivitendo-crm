<?php
require_once("../inc/stdLib.php");
include_once("../inc/crmLib.php");
$head = mkHeader();
echo $head['JQTABLE']; 

$_SESSION['swort'] = $_POST['swort'];
$d = '';//dialog
if ( $_POST['swort'] == "" ) $d = "dialog_no_sw";

$anzahl = 0;
if ($_POST["submit"] =="kontakt" && $_POST['swort'] != '') { 
    $sw = strtoupper( $_POST["swort"] );
    $sw = strtr( $sw, "*?", "%_" );
    $sql  = "select calldate,cause,t.id,caller_id,bezug,V.name as lname,C.name as kname,P.cp_name as pname ";
    $sql .= "from telcall t left join customer C on C.id=caller_id left join vendor V on V.id=caller_id ";
    $sql .= "left join contacts P on caller_id=P.cp_id where UPPER(cause) like '%$sw%' or UPPER(c_long) like '%$sw%' ";
    $sql .= 'order by bezug,calldate desc limit '.$_SESSION['listLimit'];
    $rs = $_SESSION['db']->getAll( $sql );
    $used = Array();
    if( $anzahl = count($rs) ) {    
        echo "<table id='treffer' class='tablesorter'>\n"; 
        echo "<thead><tr ><th>Datum</th><th>Grund</th><th>Name</th>\n<tbody>\n"; 
        $i = 0;
        foreach ( $rs as $row ) {
            if ( $row["bezug"] > 0 and in_array($row["bezug"], $used) ) continue;
            if ( $row["bezug"]==0 ) $used[]=$row["id"];
            if ( strlen($row["cause"]) > 30 ) { $cause = substr($row["cause"], 0, 30).".."; }
            else { $cause = $row["cause"]; };
            if      ( $row["kname"] ) { $name = $row["kname"]; $src="C"; }
            else if ( $row["lname"] ) { $name = $row["lname"]; $src="V";  }
            else if ( $row["pname"] ) { $name = $row["pname"]; $src="CC"; }
            else { $name = ""; $src='S'; }
            echo "<tr onClick='showItem(".$row["id"].",\"$src\",".$row["caller_id"].");'>";
            echo "<td>".db2date($row["calldate"])."&nbsp;</td><td> ".$cause."</td><td>";
            echo "$name</td></tr>\n";
            $i++;
            if ($i>=$_SESSION['listLimit']) {
                $d = "dialog_viele";                
                break;
            }
        }
        echo "</tbody></table>\n<br>";
    } 
    else $d = "dialog_keine";
} 
else if ($_POST["submit"] == "adress") {
    include("../inc/FirmenLib.php");
    include("../inc/persLib.php");
    include_once("../inc/UserLib.php");
    $suchwort = mkSuchwort($_POST["swort"]);
    $rsE = $rsV = $rsC = $rsK = false;
    if ( $_POST['swort'] != '') {
        $rsC = getAllFirmen($suchwort,true,"C");
        if ( $rsC ) $anzahl = count($rsC);
        if ( $anzahl <= $_SESSION['listLimit'] ) {
            $rsV = getAllFirmen($suchwort,true,"V");    
            if ( $rsV ) $anzahl += count($rsV);
            if ( $anzahl <= $_SESSION['listLimit'] ) {
                $rsK = getAllPerson($suchwort);
                if ( $rsK ) $anzahl += count($rsK);
                if ( $anzahl <= $_SESSION['listLimit'] ) {
                    $rsE = getAllUser($suchwort);
                    if ( $rsE ) $anzahl += count($rsE);
                    if ( $anzahl >= $_SESSION['listLimit'] ) {
                        $d = "dialog_viele";
                    } 
                    else if ($anzahl === 0) $d = "dialog_keine";
                } 
                else $d = "dialog_viele";
            } 
            else $d = "dialog_viele";
        } 
        else $d = "dialog_viele";
    }
    if ( $anzahl > 0 ) {
        if ( $anzahl == 1 && $rsC ){
            echo '<script> showD("C","'.($rsC[0]["id"]).'");</script>';
            exit();
        }
        if ( $anzahl == 1 && $rsV ) {
            echo '<script> showD("V","'.($rsV[0]["id"]).'");</script>';
            exit();
        }
        if ( $anzahl == 1 && $rsK ) {
            echo '<script> showD("K","'.($rsK[0]["id"]).'");</script>';
            exit();
        }
        if ( $anzahl == 1 && $rsE ) {
            echo '<script> showD("E","'.($rsE[0]["id"]).'");</script>';
            exit();
        } 
        echo "<table id='treffer' class='tablesorter'>\n"; 
        echo "<thead><tr ><th>KD-Nr</th><th>Name</th><th>Anschrift</th><th>Telefon</th><th></th></tr></thead>\n<tbody>\n"; 
        $i=0; 
        if ( $rsC && $i < $_SESSION['listLimit'] ) foreach($rsC as $row) { 
            echo "<tr onClick='showD(\"C\",".$row["id"].");'>". 
                 "<td>".$row["customernumber"]."</td><td class=\"liste\">".$row["name"]."</td>". 
                 "<td>".$row["city"].(($row["street"])?", ":"").$row["street"]."</td><td class=\"liste\">".$row["phone"]."</td><td class=\"liste\">K</td></tr>\n"; 
            $i++; 
        }  
        if ( $rsV && $i < $_SESSION['listLimit'] ) foreach($rsV as $row) { 
            echo "<tr onClick='showD(\"V\",".$row["id"].");'>". 
                 "<td>".$row["vendornumber"]."</td><td class=\"liste\">".$row["name"]."</td>". 
                 "<td>".$row["city"].(($row["street"])?", ":"").$row["street"]."</td><td class=\"liste\">".$row["phone"]."</td><td class=\"liste\">L</td></tr>\n"; 
            $i++; 
        } 
        if ( $rsK && $i < $_SESSION['listLimit'] ) foreach($rsK as $row) { 
            echo "<tr onClick='showD(\"K\",".$row["cp_id"].");'>". 
                 "<td>".$row["cp_id"]."</td><td class=\"liste\">".$row["cp_name"].", ".$row["cp_givenname"]."</td>". 
                 "<td>".$row["cp_city"].(($row["cp_street"])?", ":"").$row["cp_street"]."</td><td class=\"liste\">".$row["cp_phone1"]."</td><td class=\"liste\">P</td></tr>\n"; 
            $i++; 
        } 
        if ( $rsE && $i < $_SESSION['listLimit']) foreach($rsE as $row) { 
            echo "<tr onClick='showD(\"E\",".$row["id"].");'>". 
                 "<td>".$row["id"]."</td><td class=\"liste\">".$row["name"]."</td>". 
                 "<td>".$row["addr2"].(($row["addr1"])?", ":"").$row["addr1"]."</td><td class=\"liste\">".$row["workphone"]."</td><td class=\"liste\">U</td></tr>\n"; 
            $i++; 
        } 
        echo "</tbody></table>\n"; 
    }  
} //END ELSEIF adress

if ( $anzahl >= $_SESSION['listLimit'] ) $d = "dialog_viele";
echo '
    <script>
        $( "#dialog_no_sw,#dialog_viele,#dialog_keine" ).dialog( "close" );
        '.($d?'$( "#'.$d.'" ).dialog( "open" );':'').'
        $("#ac0").focus();
    </script>';
 echo '     
<script>
    $("#treffer")
        .tablesorter({widthFixed: true, widgets: ["zebra"]})
        .tablesorterPager({container: $("#pager"), size: 20, positionFixed: false})
</script>
<style>
    table.tablesorter { width: 900;} 
</style>';   
if ( $anzahl > 10 ) 
    echo '
        <span id="pager" class="pager">
            <form>
                <img src="'.$_SESSION['baseurl'].'crm/jquery-ui/plugin/Table/addons/pager/icons/first.png" class="first"/>
                <img src="'.$_SESSION['baseurl'].'crm/jquery-ui/plugin/Table/addons/pager/icons/prev.png" class="prev"/>
                <img src="'.$_SESSION['baseurl'].'crm/jquery-ui/plugin/Table/addons/pager/icons/next.png" class="next"/>
                <img src="'.$_SESSION['baseurl'].'crm/jquery-ui/plugin/Table/addons/pager/icons/last.png" class="last"/> '.($anzahl < $_SESSION['listLimit'] ? $anzahl : $_SESSION['listLimit']).'
                <select class="pagesize" id="pagesize">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                </select>
            </form> 
        </span>';
?>