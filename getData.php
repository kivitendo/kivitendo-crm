<?php
ob_start(); 
	require_once("inc/stdLib.php");
	include("inc/crmLib.php");
    $menu = $_SESSION['menu'];
    $head = mkHeader();
?>
<html>
<head><title></title>
<?php echo $menu['stylesheets']; ?>
<?php echo $head['CRMCSS']; ?>
<?php echo $head['JQUERY']; ?>
<?php echo $head['JQUERYUI']; ?>
<?php echo $head['THEME']; ?>
<?php echo $head['JQTABLE']; ?>
<?php echo $head['JUI-DROPDOWN']; ?>
    <script language="JavaScript">
        function showD (src,id) {
	       if      (src=="C") { uri="firma1.php?Q=C&id=" + id }
		   else if (src=="V") { uri="firma1.php?Q=V&id=" + id; }
		   else if (src=="E") { uri="user1.php?id=" + id; }
  		   else if (src=="K") { uri="kontakt.php?id=" + id; }
		   window.location.href=uri;
	    }
        function showItem(id,Q,FID) {
		    F1=open("<?php echo $_SESSION["basepath"]; ?>crm/getCall.php?Q="+Q+"&fid="+FID+"&hole="+id,"Caller","width=610, height=600, left=100, top=50, scrollbars=yes");
	    }        
    </script> 
<?php    
//print_r($_SESSION);
if ($_SESSION['feature_ac']) { //funktioniert wegen der Ersetzungen für minLength und delay nur mit echo
    echo '
    <style>
        .ui-autocomplete-category {
            font-weight: bold;
            padding: .2em .4em;
            margin: .8em 0 .2em;
            line-height: 1.5;
        }
    </style>
    <script>
        $.widget("custom.catcomplete", $.ui.autocomplete, {
            _renderMenu: function(ul,items) {
                var that = this,
                currentCategory = "";
                $.each( items, function( index, item ) {
                    if ( item.category != currentCategory ) {
                        ul.append( "<li class=\'ui-autocomplete-category\'>" + item.category + "</li>" );
                        currentCategory = item.category;
                    }
                    that._renderItemData(ul,item);
                });
             }
         });     
    </script>            
    <script language="JavaScript"> 
        $(function() {
            $("#ac0").catcomplete({                          
                source: "jqhelp/autocompletion.php?case=name",                            
                minLength: '.$_SESSION['feature_ac_minlength'].',                            
                delay: '.$_SESSION['feature_ac_delay'].',
                select: function(e,ui) {                    
                    showD(ui.item.src,ui.item.id);
                }
            });
        });
    </script>';  
   }//end feature_ac 
?>  
<style>
    table.tablesorter {
	   width: 900;
    }  
    #jui_dropdown_demo {
        height: 400px;
    }
    #jui_dropdown_demo button {
        padding: 3px !important;
    }
    #jui_dropdown_demo ul li {
        background: none;
        display: inline-block;
        list-style: none;
    }   

    .drop_container {
        margin: 10px 10px 10px 10px ;
        display: inline-block;
    }   
    .menu {
        position: absolute;
        width: 240px !important;
        margin-top: 3px !important;
    }     
</style>
<script>
     $(function() {
        $("#dialog").dialog();
    });
    $(function() {
        $("#treffer")
            .tablesorter({widthFixed: true, widgets: ['zebra']})
            .tablesorterPager({container: $("#pager"), size: 20, positionFixed: false})
    }); 

    $(function() {
        $.ajax({
            url: "jqhelp/getHistory.php",
            context: $('#menu'),
            success: function(data) {
                $(this).html(data);
                $("#drop").jui_dropdown({
                    launcher_id: 'launcher',
                    launcher_container_id: 'launcher_container',
                    menu_id: 'menu',
                    containerClass: 'drop_container',
                    menuClass: 'menu',
                    launchOnMouseEnter:true,
                    onSelect: function(event, data) {
                        showD(data.id.substring(0,1), data.id.substring(1));
                    }
                });
            }
        });
          
    });
</script>
</head>
<body onload="$('#ac0').focus().val('<?php echo preg_replace("#[ ].*#",'',$_GET['swort']);?>');">
<?php 
echo $menu['pre_content'];
echo $menu['start_content'];
echo '<p class="listtop">'.translate('.:fast search customer/vendor/contacts and contact history:.','firma').'</p>
<form name="suche" action="getData.php" method="get">
    <input type="text" name="swort" size="25" id="ac0" autocomplete="off"> '.translate('.:Search:.','firma').' 
    <input type="submit" name="adress" value="'.translate('.:adress:.','firma').'" id="adress">
    <input type="submit" name="kontakt" value="'.translate('.:contact history:.','firma').'"> <br>
    <span class="liste">'.translate('.:search keyword:.','firma').'</span>
</form>';
echo '<div id="drop">
  <div id="launcher_container">
    <button id="launcher">'.translate('.:history tracking:.','firma').'</button>
  </div>
  <ul id="menu"> </ul>';

if ($_GET["kontakt"] && $_GET['swort'] != '') { 
	$sw = strtoupper( $_GET["suchwort"] );
	$sw = strtr( $sw, "*?", "%_" );
	$sql  = "select calldate,cause,t.id,caller_id,bezug,V.name as lname,C.name as kname,P.cp_name as pname ";
	$sql .= "from telcall t left join customer C on C.id=caller_id left join vendor V on V.id=caller_id ";
	$sql .= "left join contacts P on caller_id=P.cp_id where UPPER(cause) like '%$sw%' or UPPER(c_long) like '%$sw%' ";
    $sql .= 'order by bezug,calldate desc limit '.$_SESSION['listLimit'];
	$rs = $db->getAll( $sql );
	$used = Array();
	if( $rs ) {	
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
				echo $_SESSION['listLimit']." von ".count($rs)." Treffern";
				break;
			}
		}
		echo "</tbody></table>\n<br>";
	} else {
		echo "Keine Treffer!";
	}
} 
else if ($_GET["adress"]) {
	include("inc/FirmenLib.php");
	include("inc/persLib.php");
	include("inc/UserLib.php");
	//ToDo Dialogtexte verbessern?
	$msg='<div id="dialog" title="Kein Suchbegriff eingegeben">
	            <p>Bitte geben Sie mindestens ein Zeichen ein.</p>
	       </div>';
	$viele='<div id="dialog" title="Zu viele Suchergebnisse">
	            <p>Die Suche ergibt zu viele Resultate.</br> Bitte geben Sie mehr Zeichen ein.</p>
	       </div>';
	$keine='<div id="dialog" title="Nichts gefunden">
                <p>Dieser Suchbegriff ergibt kein Resultat.</br>Bitte überprüfen Sie die Schreibweise!</p>
            </div>';
	$found=false;
	$suchwort=mkSuchwort($_GET["swort"]);
	$anzahl=0;
	$db->debug=0;

	$rsE=getAllUser($suchwort);
	if (chkAnzahl($rsE,$anzahl) && $_GET["swort"]) {
		$rsV=getAllFirmen($suchwort,true,"V");	
		if (chkAnzahl($rsV,$anzahl)) {
			$rsC=getAllFirmen($suchwort,true,"C");
			if (chkAnzahl($rsC,$anzahl)) {
				$rsK=getAllPerson($suchwort);
				if (!chkAnzahl($rsK,$anzahl)) {
					$msg=$viele;
				} else {
					if ($anzahl===0) $msg=$keine;
				} 
			} else {
				$msg=$viele;
			}
		} else {
			$msg=$viele;
		}
	} 

	if ($anzahl>0) {
        if ($anzahl==1 && $rsC) header("Location: firma1.php?Q=C&id=".$rsC[0]['id']); 
        if ($anzahl==1 && $rsV) header("Location: firma1.php?Q=V&id=".$rsV[0]['id']); 
        if ($anzahl==1 && $rsK) header("Location: kontakt.php?id=".$rsK[0]['id']); 
        if ($anzahl==1 && $rsE) header("Location: user1.php?id=".$rsE[0]['id']); 
        echo "<table id='treffer' class='tablesorter'>\n"; 
        echo "<thead><tr ><th>KD-Nr</th><th>Name</th><th>Anschrift</th><th>Telefon</th><th></th></tr></thead>\n<tbody>\n"; 
        $i=0; 
        if ($rsC) foreach($rsC as $row) { 
            echo "<tr class='bgcol".($i%2+1)."' onClick='showD(\"C\",".$row["id"].");'>". 
                 "<td class=\"liste\">".$row["customernumber"]."</td><td class=\"liste\">".$row["name"]."</td>". 
                 "<td class=\"liste\">".$row["city"].(($row["street"])?", ":"").$row["street"]."</td><td class=\"liste\">".$row["phone"]."</td><td class=\"liste\">K</td></tr>\n"; 
            $i++; 
        }  
        if ($rsV) foreach($rsV as $row) { 
            echo "<tr class='bgcol".($i%2+1)."' onClick='showD(\"V\",".$row["id"].");'>". 
                 "<td class=\"liste\">".$row["vendornumber"]."</td><td class=\"liste\">".$row["name"]."</td>". 
                 "<td class=\"liste\">".$row["city"].(($row["street"])?", ":"").$row["street"]."</td><td class=\"liste\">".$row["phone"]."</td><td class=\"liste\">L</td></tr>\n"; 
            $i++; 
        } 
        if ($rsK) foreach($rsK as $row) { 
            echo "<tr class='bgcol".($i%2+1)."' onClick='showD(\"K\",".$row["cp_id"].");'>". 
                 "<td class=\"liste\">".$row["cp_id"]."</td><td class=\"liste\">".$row["cp_name"].", ".$row["cp_givenname"]."</td>". 
                 "<td class=\"liste\">".$row["cp_city"].(($row["cp_street"])?", ":"").$row["cp_street"]."</td><td class=\"liste\">".$row["cp_phone1"]."</td><td class=\"liste\">P</td></tr>\n"; 
            $i++; 
        } 
        if ($rsE) foreach($rsE as $row) { 
            echo "<tr class='bgcol".($i%2+1)."' onClick='showD(\"E\",".$row["id"].");'>". 
                 "<td class=\"liste\">".$row["id"]."</td><td class=\"liste\">".$row["name"]."</td>". 
                 "<td class=\"liste\">".$row["addr2"].(($row["addr1"])?", ":"").$row["addr1"]."</td><td class=\"liste\">".$row["workphone"]."</td><td class=\"liste\">U</td></tr>\n"; 
            $i++; 
        } 
        echo "</tbody></table>\n"; 
        echo '
        <span id="pager" class="pager">
            <form>
                <img src="'.$_SESSION['baseurl'].'crm/jquery-ui/plugin/Table/addons/pager/icons/first.png" class="first"/>
                <img src="'.$_SESSION['baseurl'].'crm/jquery-ui/plugin/Table/addons/pager/icons/prev.png" class="prev"/>
                <img src="'.$_SESSION['baseurl'].'crm/jquery-ui/plugin/Table/addons/pager/icons/next.png" class="next"/>
                <img src="'.$_SESSION['baseurl'].'crm/jquery-ui/plugin/Table/addons/pager/icons/last.png" class="last"/>
                <select class="pagesize" id="pagesize">
                    <option value="10">10</option>
                    <option value="20" selected>20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                </select>
            </form>
        </span>';
   
    } 
    else { 
        echo $msg; 
    }; 
} //END ELSEIF adress
    if ($_GET['kontakt'] || $_GET['adress']) {
?>

  
        <br>



<?php } 
    echo $menu['end_content'];
    ob_end_flush(); 
?>
</body>
</html>
