<?php
    require_once("inc/stdLib.php");
    require_once('inc/wvLib.php');
    $menu =  $_SESSION['menu'];
?>
<html>
	<head><title>LX - CRM - Partsedit</title>
    <?php echo $menu['stylesheets']; ?>
    <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['basepath'].'css/'.$_SESSION["stylesheet"]; ?>/main.css">
    <script type="text/javascript" src="<?php echo $_SESSION['basepath']; ?>crm/jquery-ui/jquery.js"></script>
    <?php echo $menu['javascripts']; ?>
	</head>
<body>
<?php 
 echo $menu['pre_content'];
 echo $menu['start_content']; 
	if ( isset( $_POST["sichern"] ) ) {
        $sql = "SELECT * FROM pricegroup ORDER by id";
        $prgr = $_SESSION['db']->getAll($sql);
        $sqlparts = "UPDATE parts SET partnumber = '%s', description = '%s', lastcost = %0.5f, listprice = %0.5f, sellprice = %0.5f WHERE id = %d";
        $chkparts = "SELECT id FROM parts WHERE partnumber = '%s'";
        $delprice = "DELETE FROM prices WHERE parts_id = %d";
        $insprice = "INSERT INTO prices (parts_id,pricegroup_id,price) VALUES (%d,%d,%0.5f)";
        if ($_POST) while( list($key,$val) = each( $_POST['partnumber'] ) ) {
            $ok = true;
            if ( $_POST['oldpnr'][$key] != $val ) {
                $rs = $_SESSION['db']->getAll(sprintf($chkparts,$val));
                if ( count($rs) > 0 ) {
                    echo "$val doppelt <br>";
                    $ok = false;
                }
            }
            if ( $ok ) {
                echo "$key ";
                $sql = sprintf($sqlparts,$val,$_POST['description'][$key],
                                              strtr($_POST['lastcost'][$key] ,",","."),
                                              strtr($_POST['listprice'][$key],",","."),
                                              strtr($_POST['sellprice'][$key],",","."),$key);
                $rc = $_SESSION['db']->query($sql);
                if ( $rc and $prgr ) {
                    $rc = $_SESSION['db']->begin();
                    $rc = $_SESSION['db']->query(sprintf($delprice,$key));
                    foreach( $prgr as $price ) {
                        if ( $_POST[$price['id']][$key] ) {
                            $rc = $_SESSION['db']->query(sprintf($insprice,$key,$price['id'],strtr($_POST[$price['id']][$key],",",".") ) );
                            if ( !$rc ) {
                                $_SESSION['db']->rollback();
                                echo "Error Preisgruppe (".$price['pricegroup'].")<br>";
                                break;
                            }
                        }
                    }
                    $_SESSION['db']->commit();
                    echo "update ok<br>";
               };
           };
        };
    };
	if ( isset( $_POST["such"] ) ) {
        $where = array('1=1');
        $sql  = "SELECT P.id as pid,P.partnumber,P.description,P.lastcost,P.listprice,P.sellprice,";
        $sql .= "PC.pricegroup_id,PC.price,PC.id FROM parts P left join prices PC on P.id=PC.parts_id WHERE ";
        if ( isset($_POST['partnumber'] )  and  $_POST['partnumber'] != '' )      $where[] = "partnumber ilike '".strtr($_POST['partnumber'],"*?","%_")."'";
        if ( isset($_POST['description'] ) and  $_POST['description'] != '' )     $where[] = "description ilike '".strtr($_POST['description'],"*?","%_")."%'";
        if ( isset($_POST['partsgroup_id'] ) and  $_POST['partsgroup_id'] != '' ) $where[] = "partsgroup_id = ".$_POST['partsgroup_id'];
        $sql .= implode(' and ',$where)." ORDER BY P.partnumber,PC.pricegroup_id";
        $rs = $_SESSION['db']->getAll($sql);
        $lastid = 0;
        if ( $rs ) {
            $sql = "SELECT * FROM pricegroup ORDER by id";
            $prgr = $_SESSION['db']->getAll($sql);
            $prices = array();
            if ( $prgr ) foreach ($prgr as $row ) { $prices[$row['id']] = '';};
            foreach ( $rs as $row ) {
                if ( $lastid != $row['pid'] ) {
                    //$parts[$row['pid']] = array_merge(array_slice($row,0,6),$prices);
                    //tut nicht wie es soll. Keine Ahnung warum.
                    $parts[$row['pid']] = array_slice($row,0,6);
                    if ( $prgr ) foreach ($prgr as $price ) { $parts[$row['pid']][$price['id']] = '';};
                    if ( $row['pricegroup_id'] ) $parts[$row['pid']][$row['pricegroup_id']] = $row['price']; 
                    $lastid = $row['pid'];
                } else {
                    $parts[$lastid][$row['pricegroup_id']] = $row['price']; 
                }
            };
        }
	};
	$partsgrp=getAllPG();
?>

<form name="parts" method="post" action="partsedit.php">
<?php
    if ( $parts ) {
        echo "<table>";
        echo "<tr><td>ID</td><td>ArtNr</td><td>Artikel</td><td>EK</td><td>Liste</td><td>VK</td>";
        $cnt = count($parts[0]);
        if ( $prgr ) foreach($prgr as $cell) { echo "<td>".$cell['pricegroup']."</td>"; };
        echo "</tr>\n";
        foreach ($parts as $row) {
            echo '<tr><td>'.$row['pid'].'</td>';
            $pid = $row['pid'];
            unset($row['pid']);
            echo '<input type="hidden" name="oldpnr['.$pid.']" value="'.$row['partnumber'].'" size="35">';
            while(list($key,$val) = each($row)) {
                if ( $key == 'description' ) {
                    echo '<td><input type="text" name="'.$key.'['.$pid.']" value="'.$val.'" size="35"></td>';
                } else {
                    echo '<td><input type="text" name="'.$key.'['.$pid.']" value="'.$val.'" size="10"></td>';
                }
            };
            echo "</tr>\n";
        };
        echo "</table>\n";
        echo "<input type='submit' name='sichern' value='sichern'><br>";
        echo "<br><a href='partsedit.php'>neue Suche</a>";
    } else {
?>
<form name="artikel" method="post" action="partsedit.php">
<table class="karte" width="100%">
	<tr><td>Artikelnummer</td>
	    <td><input type="text" name="partnumber" value=""></td></tr>
	<tr><td>Artikel:</td>
	    <td><input type="text" name="description" value="" size="30"></td></tr>
	<tr><td>Warengruppe:</td>
	    <td>
			<select name="partsgroup_id" Style="width:450px" >
			<option value=''>Artikel ohne Warengruppe</option>
<?php
	if ($partsgrp) foreach ($partsgrp as $zeile) {
 		echo "\t<option value='".$zeile["id"]."'>".$zeile["partsgroup"]."</option>\n";
	}
?>
			</select>
		</td></tr>
		<tr><td><input type="submit" name="such" value="suchen" ></td><td></td></tr>
</table>
<?php }; echo "</form>"; echo $menu['end_content']; ?>
</body>
</html>
