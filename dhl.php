<?php
    require_once("inc/stdLib.php");
    require_once("inc/FirmenLib.php");
    $menu =  $_SESSION['menu'];
    if ( isset($_GET['popup']) or isset($_POST['popup']) ) {
        $popup = true;
    } else {
        $popup = false;
        $menu = $_SESSION['menu'];
    };
    $link = '';
    if ( isset($_GET['del']) ) {
        $dhl = $_SESSION['DHL'];
        unset ( $_SESSION['DHL'] );
        for ($i=0; $i<count($dhl); $i++) {
            if ( $i != $_GET['del'] ) $_SESSION['DHL'][] = $dhl[$i];
        }
    }
    $head = mkHeader();    
    if ( isset($_GET['fid']) ) {
        if ( $_GET['Q'] == 'V' ) {
            $data = $_SESSION['db']->getOne('SELECT * FROM vendor WHERE id = '.$_GET['fid']);
        } else {
            $data = $_SESSION['db']->getOne('SELECT * FROM customer WHERE id = '.$_GET['fid']);
        };
                    
        $fa['RECV_NAME1']   = $data['name'];
        $fa['RECV_NAME2']   = $data[''];
        $fa['RECV_STREET']  = $data['street'];
        $fa['RECV_HOUSENUMBER'] = '';
        $fa['RECV_PLZ']     = $data['zipcode'];
        $fa['RECV_CITY']    = $data['city'];
        $fa['RECV_COUNTRY'] = strtoupper( substr( $data['country'],0,3 ) );
    };

    $sql = "SELECT * FROM crmdefaults WHERE grp = 'dhl'";
    $rs = $_SESSION['db']->getAll($sql);
    $produkte = array(); 
    foreach ( $rs as $row ) {
        if ( !preg_match( '/SEND/', $row['key'] ) ) { $produkte[$row['key']] = $row['val']; }
        else { $send[$row['key']] = $row['val']; };
    };
    $sender = $send['SEND_NAME1'].','.$send['SEND_NAME2'].','.$send['SEND_STREET'].','.$send['SEND_HOUSENUMBER'].','.$send['SEND_PLZ'].','.$send['SEND_CITY'].','.$send['SEND_COUNTRY'];
    if ( isset( $_POST['add'] ) ) {
        unset( $_POST['add'] ) ;
        unset( $_POST['popup'] ) ;
        mb_internal_encoding($_SESSION["charset"]);
        $_POST['RECV_NAME1'] = mb_convert_encoding($_POST['RECV_NAME1'],'Windows-1252');
        $_POST['RECV_STREET'] = mb_convert_encoding($_POST['RECV_STREET'],'Windows-1252');
        $_POST['RECV_CITY'] = mb_convert_encoding($_POST['RECV_CITY'],'Windows-1252');
        $_SESSION['DHL'][] = $_POST;
    } else if ( isset( $_POST['do'] ) ) {
        $header = 'SEND_NAME1,SEND_NAME2,SEND_STREET,SEND_HOUSENUMBER,SEND_PLZ,SEND_CITY,SEND_COUNTRY,RECV_NAME1,RECV_NAME2,RECV_STREET,RECV_HOUSENUMBER,RECV_PLZ,RECV_CITY,RECV_COUNTRY,PRODUCT,COUPON';
        $spacer = array('','','','','','','');
        if ( isset($_SESSION['DHL']) ) {
            $f = fopen('tmp/dhl.csv','w');
            fputs ( $f, $header."\n");
            $first = true;
            foreach ($_SESSION['DHL'] as  $address) {
                if ( $first ) {
                    fputcsv ( $f,array_merge($send,$address),',','"' );
                    $first = false;
                } else {
                    fputcsv ( $f,array_merge($spacer,$address),',','"' );
                };
            };
            fclose($f);
            $link = '<a href="tmp/dhl.csv">download</a>';
        }
    }
?>
<html>
<head><title>DHL</title>
<?php echo $menu['stylesheets']; 
      echo $head['CRMCSS']; 
      echo $head['JQUERY']; 
      echo $head['JQUERYUI']; 
      echo $head['THEME']; ?> 
</head>
<body>
<?php if ( !$popup)  { 
    echo $menu['pre_content']; 
    echo $menu['start_content']; }; ?>
<p class="listtop">DHL Adressexport</p>
<?php echo 'Absender: '.$sender.'<br>';
      $cnt = 0;
      if ( isset($_SESSION['DHL']) ) foreach ($_SESSION['DHL'] as  $address) {
          echo implode(',',$address)." <a href='dhl.php?del=$cnt&popup=$popup'>[entfernen]</a><br>";
          $cnt ++;
      }
?>
<form name='form' method='post' action='dhl.php'>
<input type='hidden' name='popup' value='<?php echo $popup; ?>'>
    <table>
      <tr><td>Empfänger 1</td><td><input type='text' name='RECV_NAME1'       value='<?php echo $fa['RECV_NAME1'] ?>'       size='20' maxlength='50'></td></tr>
      <tr><td>Empfänger 2</td><td><input type='text' name='RECV_NAME2'       value='<?php echo $fa['RECV_NAME2'] ?>'       size='20' maxlength='50'> </td></tr>
      <tr><td>Strasse</td>    <td><input type='text' name='RECV_STREET'      value='<?php echo $fa['RECV_STREET'] ?>'      size='20' maxlength='50'> </td></tr>
      <tr><td>Hausnr.</td>    <td><input type='text' name='RECV_HOUSENUMBER' value='<?php echo $fa['RECV_HOUSENUMBER'] ?>' size='20' maxlength='11'> wenn nicht in Strasse</td></tr>
      <tr><td>Plz</td>        <td><input type='text' name='RECV_PLZ'         value='<?php echo $fa['RECV_PLZ'] ?>'         size='20' maxlength='11'> </td></tr>
      <tr><td>Ort</td>        <td><input type='text' name='RECV_CITY'        value='<?php echo $fa['RECV_CITY'] ?>'        size='20' maxlength='38'> </td></tr>
      <tr><td>Land</td>       <td><input type='text' name='RECV_COUNTRY'     value='<?php echo $fa['RECV_COUNTRY'] ?>'     size='20' maxlength='50'> 3 Buchstaben, siehe: <a href='http://de.wikipedia.org/wiki/ISO-3166-1-Kodierliste' target='_blank'>Wikipedia Spalte ALPHA-3</a></td></tr>
      <tr><td>Produkt</td><td><select name='PRODUCT'> 
<?php
    while ( list( $key, $val ) = each($produkte) ) {
        echo "<option value='$key'>$val\n\t";
    }
?>
    </select></td></tr>
      <tr><td>Cupon</td><td><input type='text' name='CUPON' value='' size='20' maxlength='50'> </td></tr>
    </table>
    <input type='submit' name='add' value='aufnehmen'> &nbsp; &nbsp;
    <input type='submit' name='do' value='CSV erzeugen'> <?php echo $link; ?>
</form>
<?php 
   if ( !$popup ) { echo $menu['end_content']; }
   else { echo '[<a href="javaScript:self.close()">close</a>]'; };
?>
</body>
</html>
