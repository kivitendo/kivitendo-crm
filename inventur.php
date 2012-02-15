<?php
    require_once("inc/stdLib.php");
    include ('inc/katalog.php');
    $link = "";

?>
<html>
        <head><title></title>
        <link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<body>
<?php
if ($_POST["erstellen"]=="erstellen") {
   $artikel = getLager($_POST);
   $vorlage = prepTex(false);
   if (file_exists('tmp/inventur.pdf')) unlink('tmp/inventur.pdf');
   if (file_exists('tmp/inventur.tex')) unlink('tmp/inventur.tex');
   if (file_exists('tmp/tabelle.tex')) unlink('tmp/tabelle.tex');
   $suche = array('&','_','"','!','#','%');
   $ersetze = array('\&','\_','\"',' : ','\#','\%');
   if ($artikel)  {
        $pg = $artikel[0]['partsgroup_id'];
        $qty = 0;
        if ($_POST['wg'] == 1) {
            $fname = "tmp/inventur_$pg.pdf";
            $link = "<a href='$fname'>WG $pg</a> <br />";
        } else {
            $fname = false;
            $link = '<a href="tmp/inventur.pdf">Liste</a>';
        }    
        $f = fopen('tmp/inventur.tex','w');
        $pre = preg_replace("/<%partsgroup%>/i",$artikel[0]['partsgroup'],$vorlage['pre']);
        $rc = fputs($f,$pre);
        foreach($artikel as $part) {
            //print_r($part); echo "<br>";
            if ($pg != $part['partsgroup_id'] AND $_POST['wg'] == 1) {
                $rc = fputs($f,$vorlage['post']);
                fclose($f);
                closeinventur($fname);
                $pg = $part['partsgroup_id'];
                $fname = "tmp/inventur_$pg.pdf";
                $link .= "<a href='$fname'>".$part['partsgroup']."</a><br />";
                $f = fopen('tmp/inventur.tex','w');
                $pre = preg_replace("/<%partsgroup%>/i",$part['partsgroup'],$vorlage['pre']);
                $rc = fputs($f,$pre);
            }
            $line = $vorlage['artikel'];
            foreach ($part as $key=>$val) {
                if ($key == 'description') $val = str_replace($suche,$ersetze,$val);
                if ($key == 'partnumber') $val = str_replace($suche,$ersetze,$val);
                if ($key == 'bestand') {
                   if ($_POST['bestand'] == '1') {
                       if (val == '')  $val = '?';
                   } else {
                      $val = '';
                   }
                }
                $line = preg_replace("/<%$key%>/i",$val,$line);
            }
            $qty ++;
            $rc = fputs($f,$line);
        }
        $rc = fputs($f,$vorlage['post']);
        fclose($f);
        closeinventur($fname);
        echo $link;
   } else {
      echo "Kein Artikel gefunden";
   }
} else {
   $orte = getLagerOrte();
   $options = "";
   $ort = "";
   if ($orte) foreach ($orte as $row) {
      if ($row["ort"] != $ort) {
           $options .= '<option value="_'.$row['warehouse_id'].'">'.$row['ort'].' Gesamt';
           $ort = $row['ort'];
      }
      $options .= '<option value="'.$row['id'].'">'.$row['ort'].' '.$row['platz'];
}
?>
Inventurliste<br />
<form name="inventur" action="inventur.php" method="post">
Sortierung nach <input type="radio" name="sort" value="partnumber" checked>Artikelnummer <input type="radio" name="sort" value="description">Artikelname<br />
Jede Warengruppe auf ein neue Seite <input type="checkbox" name="wg" value="1"><br />
Dienstleistungen ausgeben <input type="checkbox" name="dienstl" value="1"><br />
Erzeugnisse ausgeben <input type="checkbox" name="erzeugnis" value="1"><br />
Ist-Bestand ausgeben <input type="checkbox" name="bestand" value="1"><br />
<select name="lager"><option value="0">Gesamtbestand
<?php echo $options; ?>
</select><br />
<input type="submit" name="erstellen" value="erstellen">
</form>
<?php } ?>
</body>
</html>
