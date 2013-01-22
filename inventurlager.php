<?php
    require_once("inc/stdLib.php");
    include('inc/katalog.php');
    $menu =  $_SESSION['menu'];
?>
<html>
    <head><title></title>
    <link type="text/css" REL="stylesheet" HREF="css/<?php echo $_SESSION["stylesheet"]; ?>/main.css">
    <style type='text/css'>@import url(<?php echo $_SESSION['basepath']; ?>/js/jscalendar/calendar-win2k-1.css)</style>
    <script type='text/javascript' src='<?php echo $_SESSION['basepath'] ?>/js/jscalendar/calendar.js'></script>
    <script type='text/javascript' src='<?php echo $_SESSION['basepath'] ?>/js/jscalendar/lang/calendar-de.js'></script>
    <script type='text/javascript' src='<?php echo $_SESSION['basepath'] ?>/js/jscalendar/calendar-setup.js'></script>
    <script type='text/javascript'>
         function getData() {
             document.inventurs.comment.value = document.inventur.comment.value;
             document.inventurs.budatum.value = document.inventur.budatum.value;
             return true;
         }
    </script>
    <?php echo $menu['stylesheets']; ?>
    <?php echo $menu['javascripts']; ?>

<body>
<?php
echo $menu['pre_content'];
echo $menu['start_content'];
if ($_POST) {
    $comment = $_POST["comment"];
    if ( $comment == '') $comment = 'Inventurbuchung';
    if ($_POST['budatum'] != '') {
        $now = $_POST['budatum'];
    } else {
        $now = date('d.m.Y');
    }
    $js = 'onSubmit="return getData();"';
}
if ($_POST['ok'] == 'sichern') {
    $rc = updatePartBin($_POST);
    echo $rc;
} else if ($_POST['ok'] == 'suchen') {
    $tmp = explode(':',$_POST['lager']);
    $wh = $tmp[0];
    $bin = $tmp[1];
    $artikel = getPartBin($_POST['pg'],$bin);
    echo getLagername($wh,$bin);
    if ($artikel) {
?>
      <form name="inventur" action="inventurlager.php" method="post" onSubmit="return getData();">
      <input type="hidden" name="warehouse" value="<?php echo $wh; ?>">
      <input type="hidden" name="bin"       value="<?php echo $bin; ?>">
      Kommentar: <input type="text"   name="comment" value="<?php echo $comment ?>">
      Datum der Buchung: <input type="text"   name="budatum" id="budatum" value="<?php echo $now; ?>" size="10" onFocus="blur();">
      <a href="#" title='Datum' name="Ddate" id="triggerD" onClick="false" ><img src='image/date.png' align='middle' border="0"></a>
      <br />
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
<script type='text/javascript'>
<!--
Calendar.setup( {
inputField : 'budatum',ifFormat :'%d.%m.%Y',align : 'BL', button : 'triggerD'} );
//-->
</script>
<?php   
   } else {
      echo "Artikel nicht gefunden: ".$_POST['partnumber']; 
   }
} 
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
<form name="inventurs" action="inventurlager.php" method="post" <?php echo $js ?>>
<input type="hidden" name="comment" value="<?php echo $comment ?>">
<input type="hidden" name="budatum" value="<?php echo $now ?>">
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
<?php 
echo $menu['end_content'];
 ?>
</body>
</html>
