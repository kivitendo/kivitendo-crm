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
<?php echo $head['THEME']; ?>
<?php echo $head['JQUERY']; ?>
<?php echo $head['JQUERYUI']; ?>
<?php echo $head['JQTABLE']; ?>
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
<?php    if ($feature_ac) { ?>
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
                minLength: '.$feature_ac_minLength.',                            
                delay: '.$feature_ac_delay.',
                select: function(e,ui) {                    
                    showD(ui.item.src,ui.item.id);
                }
            });
        });
    </script> 
<?php    }//end feature_ac 
?>
    <script>
    $(function() {
        $("#dialog").dialog();
    });
    $(function() {
        $("#treffer")
            .tablesorter({widthFixed: true, widgets: ['zebra']})
            .tablesorterPager({container: $("#pager"), size: 20, positionFixed: false})
    });    
    </script>
</head>
<body onload=$("#ac0").focus().val('<?php echo preg_replace("#[ ].*#",'',$_GET['swort']);?>');>
<?php echo $menu['pre_content']; ?>
<?php echo $menu['start_content']; ?>
<p class="listtop">Schnellsuche Kunde/Lieferant/Kontakte und Kontaktverlauf</p>
<form name="suche" action="getData.php" method="get">
    <input type="text" name="swort" size="25" id="ac0" autocomplete="off"> suche 
    <input type="submit" name="adress" value="Adresse" id="adress">
    <input type="submit" name="kontakt" value="Kontaktverlauf"> <br>
    <span class="liste">Suchbegriff</span>
</form>
<?php //wichtig: focus().val('ohneLeerZeichen')
if ($_GET["kontakt"] && $_GET['swort'] != '') { 
	$sw = strtoupper( $_GET["suchwort"] );
	$sw = strtr( $sw, "*?", "%_" );
	$sql  = "select calldate,cause,t.id,caller_id,bezug,V.name as lname,C.name as kname,P.cp_name as pname ";
	$sql .= "from telcall t left join customer C on C.id=caller_id left join vendor V on V.id=caller_id ";
	$sql .= "left join contacts P on caller_id=P.cp_id where UPPER(cause) like '%$sw%' or UPPER(c_long) like '%$sw%' ";
    $sql .= 'order by bezug,calldate desc limit '.$listLimit;
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
			if ($i>=$listLimit) {
				echo "$listLimit von ".count($rs)." Treffern";
				break;
			}
		}
		echo "</tbody></table>\n<br>";
	} else {
		echo "Keine Treffer!";
	}
} else if ($_GET["adress"]) {
	include("inc/FirmenLib.php");
	include("inc/persLib.php");
	include("inc/UserLib.php");
	//ToDo Dialogtexte verbessern?
	$msg='<div id="dialog" title="Kein Suchbegriff eingegeben">
	            <p>Bitte geben Sie mindestens ein Zeichen ein.</p>
	       </div>';
	$viele='<div id="dialog" title="Zu viele Suchergebnisse">
	            <p>Die Suche ergibt zu viele Resultate.</br> Bitte geben mehr Zeichen ein.</p>
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
            echo "<tr onClick='showD(\"C\",".$row["id"].");'>". 
                 "<td>".$row["customernumber"]."</td><td>".$row["name"]."</td>". 
                 "<td>".$row["city"].(($row["street"])?", ":"").$row["street"]."</td><td>".$row["phone"]."</td><td>K</td></tr>\n"; 
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
        echo "</tbody></table>\n"; ?>
        
<?php   } else { 
 	        echo $msg; 
        }; 
    } 
    if ($_GET['kontakt'] || $_GET['adress']) {
?>
        <br>
<span id="pager" class="pager">
    <form>
        <img src="<?php echo $_SESSION['baseurl']; ?>crm/jquery-ui/plugin/Table/addons/pager/icons/first.png" class="first"/>
        <img src="<?php echo $_SESSION['baseurl']; ?>crm/jquery-ui/plugin/Table/addons/pager/icons/prev.png" class="prev"/>
        <img src="<?php echo $_SESSION['baseurl']; ?>crm/jquery-ui/plugin/Table/addons/pager/icons/next.png" class="next"/>
        <img src="<?php echo $_SESSION['baseurl']; ?>crm/jquery-ui/plugin/Table/addons/pager/icons/last.png" class="last"/>
        <select class="pagesize" id='pagesize'>
            <option value="10">10</option>
            <option value="20" selected>20</option>
            <option value="30">30</option>
            <option value="40">40</option>
        </select>
    </form>
</span>
<?php } 
    echo $menu['end_content'];
    ob_end_flush(); 
?>
</body>
</html>
