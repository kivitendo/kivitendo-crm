<?php
    require_once("inc/stdLib.php");
    include('inc/katalog.php');
    $menu =  $_SESSION['menu'];
?>
<html>
    <head><title></title>
    <link type="text/css" REL="stylesheet" HREF="css/<?php echo $_SESSION["stylesheet"]; ?>/main.css"></link>
    <?php echo $menu['stylesheets']; ?>
    <?php echo $menu['javascripts']; ?>

<body>
<?php
echo $menu['pre_content'];
echo $menu['start_content'];
if ($_POST['ok'] == 'sichern') {
    $rc = updatePartBin($_POST);
    echo $rc;
    echo "<br /><a href='inventurlager.php'>neue Eingabe</a>";
} else if ($_POST['ok'] == 'suchen') {
    $tmp = explode(':',$_POST['lager']);
    $wh = $tmp[0];
    $bin = $tmp[1];
    $artikel = getPartBin($_POST['pg'],$bin);
    echo getLagername($wh,$bin);
    if ($artikel) {
?>
      <form name="inventur" action="inventurlager.php" method="post">
      <input type="hidden" name="warehouse" value="<?php echo $wh; ?>">
      <input type="hidden" name="bin"       value="<?php echo $bin; ?>">
      Kommentar: <input type="text"   name="comment" value="Inventurbuchung"><br />
      Transfertype: 
      <input type="radio" name="transtype" value="1" checked>Korrektur
      <input type="radio" name="transtype" value="2">Einlagern / Entnahme
      <input type="radio" name="transtype" value="3">Gefunden / Fehlbestand
      <table>
      <tr><td>Nummer</td><td>Artikel</td><td>Chargenumber</td><td>Bestbefore JJJJ-MM-DD</td><td>Menge</td></tr>
<?php foreach ($artikel as $part) { 
         echo "<tr><td>".$part['partnumber']."</td><td>".$part['partdescription']."</td><td>"; 
         if ($part['qty'] == '') {
             $qty = '';
         } else {
             $qty = abs($part['qty']);
             if ($part['qty']<0) $qty *= -1;
         };
         $onhand = (abs($part['onhand']<0))?abs($part['onhand'])*-1:abs($part['onhand']);
         $lager = '';
         if ($part['bin_id']) $lager=' *';
         echo '<input type="hidden" name="parts_id[]"     value="'.$part['parts_id'].'">';
         echo '<input type="hidden" name="oldqty[]"       value="'.$part['qty'].'">';
         if ($lager=='') {
            echo '<input type="type" name="chargenumber[]" value=""></td><td>';
         } else { 
            echo '<input type="hidden" name="chargenumber[]" value="'.$part['chargenumber'].'">'.$part['chargenumber'].'</td><td>';
         }
         if ($lager=='') {
             echo '<input type="text" name="bestfefore[]"   value=""></td><td>';
         } else { 
             echo '<input type="hidden" name="bestfefore[]"   value="'.$part['bestbefore'].'">'.$part['bestbefore'].'</td><td>';
         };
         echo '<input type="text"   name="qty[]"          value="'.$qty.'">'.$part['partunit'].' ('.$onhand.')</td></tr>';
      };
?>
      </table>
      <input type="submit" name="ok" value="sichern">
      </form>
<?php   
   } else {
      echo "Artikel nicht gefunden: ".$_POST['partnumber']; 
   }
   echo "<br /><a href='inventurlager.php'>neue Eingabe</a>";
} else {
   $orte = getLagerOrte();
   $pg = getPartsGroup();
   $Ooptions = "";
   if ($orte) foreach ($orte as $row) {
      $Ooptions .= '<option value="'.$row['warehouse_id'].":".$row['id'].'">'.$row['ort'].' '.$row['platz'];
   };
   $Poptions = "";
   if ($pg) foreach ($pg as $row) {
      $Poptions .= '<option value="'.$row['id'].'">'.$row['partsgroup'];
   };

?>
Inventurbuchung<br />
<form name="inventur" action="inventurlager.php" method="post">
Warengruppe:
<select name="pg">
<option value="">Artikel ohne Warengruppe
<?php echo $Poptions; ?>
</select><br />
Lager:
<select name="lager">
<?php echo $Ooptions; ?>
</select><br />
<input type="submit" name="ok" value="suchen">
</form>
<?php }
echo $menu['end_content'];
 ?>
</body>
</html>
