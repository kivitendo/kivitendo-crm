<?php
ob_start(); 
	require_once("inc/stdLib.php");
	include("inc/crmLib.php");
    $menu =  $_SESSION['menu'];
?>
<html>
<head><title></title>
    <?php echo $menu['stylesheets']; ?>
    <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"]; ?>/main.css"></link>
    <?php if (!$feature_ac) echo $menu['javascripts']; ?>
    <?php  
    if ($feature_ac) { 
        echo '<link rel="stylesheet" type="text/css" href="'.$_SESSION['basepath'].'crm/jquery-ui/themes/base/jquery-ui.css"></link>'."\n"; 
        echo   '<script type="text/javascript" src="'.$_SESSION['basepath'].'crm/jquery-ui/jquery.js"></script>'."\n"; 
        echo   '<script type="text/javascript" src="'.$_SESSION['basepath'].'crm/jquery-ui/ui/jquery-ui.js"></script>'."\n"; 
        echo   '<style>
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
                </script> ';           
        echo   '<script language="JavaScript"> 
                    $(function() {
                        $("#ac0").catcomplete({                          
                            source: "ac.php?case=fastsearch",                            
                            minLength: '.$feature_ac_minLength.',                            
                            delay: '.$feature_ac_delay.',
                            select: function(e,ui) {
                                        $("#adress").click();
                                    }
                        });
                    });
                </script>'; 
    }//end feature_ac 
    ?> 
</head>
<body onLoad="document.suche.swort.focus()";>
<?php
echo $menu['pre_content'];
echo $menu['start_content'];
if ($_GET["adress"]) {
	include("inc/FirmenLib.php");
	include("inc/persLib.php");
	include("inc/UserLib.php");
	
	$msg="Leider nichts gefunden!";
	$viele="Zu viele Treffer. Bitte einschr&auml;nken.";
	$found=false;
	$suchwort=mkSuchwort($_GET["swort"]);
	$anzahl=0;
	$db->debug=0;

	$rsE=getAllUser($suchwort);
	if (chkAnzahl($rsE,$anzahl)) {
		$rsV=getAllFirmen($suchwort,true,"V");	
		if (chkAnzahl($rsV,$anzahl)) {
			$rsC=getAllFirmen($suchwort,true,"C");
			if (chkAnzahl($rsC,$anzahl)) {
				$rsK=getAllPerson($suchwort);
				if (!chkAnzahl($rsK,$anzahl)) {
					$msg=$viele;
				} else {
					if ($anzahl===0) $msg="Keine Treffer";
				} 
			} else {
				$msg=$viele;
			}
		} else {
			$msg=$viele;
		}
	} else {
			$msg=$viele;
	}
?>
<script language="JavaScript">
<!--
	function showD (src,id) {
		if      (src=="C") {	uri="firma1.php?Q=C&id=" + id }
		else if (src=="V") {	uri="firma1.php?Q=V&id=" + id; }
		else if (src=="E") {	uri="user1.php?id=" + id; }
		else if (src=="K") {	uri="kontakt.php?id=" + id; }
		window.location.href=uri;
	}
//-->
</script>
<?php
	if ($anzahl>0) {
            if ($anzahl==1 && $rsC) header("Location: firma1.php?Q=C&id=".$rsC[0]['id']); 
            if ($anzahl==1 && $rsV) header("Location: firma1.php?Q=V&id=".$rsV[0]['id']); 
            if ($anzahl==1 && $rsK) header("Location: kontakt.php?id=".$rsK[0]['id']); 
            if ($anzahl==1 && $rsE) header("Location: user.php?id=".$rsE[0]['id']); 
            echo '<p class="listtop">Suchergebnis</p>'; 
            echo "<table class=\"liste\">\n"; 
            echo "<tr class='bgcol3'><th>KD-Nr</th><th class=\"liste\">Name</th><th class=\"liste\">Anschrift</th><th class=\"liste\">Telefon</th><th></th></tr>\n"; 
            $i=0; 
            if ($rsC) foreach($rsC as $row) { 
                echo "<tr onMouseover=\"this.bgColor='#FF0000';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showD(\"C\",".$row["id"].");'>". 
                             "<td class=\"liste\">".$row["customernumber"]."</td><td class=\"liste\">".$row["name"]."</td>". 
                                     "<td class=\"liste\">".$row["city"].(($row["street"])?",":"").$row["street"]."</td><td class=\"liste\">".$row["phone"]."</td><td class=\"liste\">K</td></tr>\n"; 
                $i++; 
            }  
            if ($rsV) foreach($rsV as $row) { 
                echo "<tr onMouseover=\"this.bgColor='#FF0000';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showD(\"V\",".$row["id"].");'>". 
                             "<td class=\"liste\">".$row["vendornumber"]."</td><td class=\"liste\">".$row["name"]."</td>". 
                                     "<td class=\"liste\">".$row["city"].(($row["street"])?",":"").$row["street"]."</td><td class=\"liste\">".$row["phone"]."</td><td class=\"liste\">L</td></tr>\n"; 
                $i++; 
            } 
            if ($rsK) foreach($rsK as $row) { 
                echo "<tr onMouseover=\"this.bgColor='#FF0000';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showD(\"K\",".$row["cp_id"].");'>". 
                                     "<td class=\"liste\">".$row["cp_id"]."</td><td class=\"liste\">".$row["cp_name"].", ".$row["cp_givenname"]."</td>". 
                                     "<td class=\"liste\">".$row["addr2"].(($row["addr1"])?",":"").$row["addr1"]."</td><td class=\"liste\">".$row["cp_phone1"]."</td><td class=\"liste\">P</td></tr>\n"; 
                $i++; 
            } 
            if ($rsE) foreach($rsE as $row) { 
                echo "<tr onMouseover=\"this.bgColor='#FF0000';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showD(\"E\",".$row["id"].");'>". 
                                     "<td class=\"liste\">".$row["id"]."</td><td class=\"liste\">".$row["name"]."</td>". 
                                     "<td class=\"liste\">".$row["addr2"].(($row["addr1"])?",":"").$row["addr1"]."</td><td class=\"liste\">".$row["workphone"]."</td><td class=\"liste\">U</td></tr>\n"; 
                $i++; 
            } 
            echo "</table>\n";
        } else { 
 	        echo $msg; 
        }; 
 	echo "<br>"; 
} else if ($_GET["kontakt"]){
?>
<script language="JavaScript">
	sw="<?php echo  $_GET["swort"]; ?>";
	if (sw != "") 
		F1=open("suchKontakt.php?suchwort="+sw+"&Q=S","Suche","width=400, height=400, left=100, top=50, scrollbars=yes");
</script>			

<?php }
echo $menu['end_content'];
ob_end_flush(); 
?> 
<p class="listtop">Schnellsuche Kunde/Lieferant/Kontakte und Kontaktverlauf</p>
	<form name="suche" action="getData.php" method="get">
	<input type="text" name="swort" size="25" id="ac0" autocomplete="off"> suche 
	<input type="submit" name="adress" value="Adresse" id="adress">
	<input type="submit" name="kontakt" value="Kontaktverlauf"> <br>
      	<span class="liste">Suchbegriff</span>
	</form>