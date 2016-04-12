<?php
    require_once("inc/stdLib.php");
    include ('inc/katalog.php');
    $link = "";
    $menu =  $_SESSION['menu'];
    $head = mkHeader();
?>
<html>
    <head><title></title>
<?php
echo $menu['stylesheets'];
echo $menu['javascripts'];
echo $head['CRMCSS'];
echo $head['THEME'];
?>
    <script type='text/javascript' src='inc/help.js'></script>
    <script>
        $(function() {
            $( "#datum" ).datepicker($.datepicker.regional[ "de" ]);
        });
    </script>
<body>
<?php
 echo $menu['pre_content'];
 echo $menu['start_content'];        
if ($_POST["erstellen"]=="erstellen") {
   $artikel = getLager($_POST);
   $art = $_POST['art'];
   $vorlage = prepTex($art,false);
   if (file_exists('tmp/'.$art.'.pdf')) unlink('tmp/'.$art.'.pdf');
   if (file_exists('tmp/'.$art.'.tex')) unlink('tmp/'.$art.'.tex');
   if (file_exists('tmp/tabelle.tex')) unlink('tmp/tabelle.tex');
   $suche = array('&','_','"','!','#','%');
   $ersetze = array('\&','\_','\"',' : ','\#','\%');
   if ($artikel)  {
        $pg = $artikel[0]['partsgroup_id'];
        $qty = 0;
        if ($_POST['wg'] == 1) {
            $fname = $art.'_'.$pg;
            $link = "<a href='tmp/$fname.pdf'>WG $pg</a> <br />";
        } else {
            $fname = $art;
            $link = '<a href="tmp/'.$art.'.pdf">Liste</a>';
        }    
        $f = fopen('tmp/'.$art.'.tex','w');
        $pre = preg_replace("/<%partsgroup%>/i",$artikel[0]['partsgroup'],$vorlage['pre']);
        $pre = preg_replace("/<%datum%>/i",date('d.m.Y'),$pre);
        $rc = fputs($f,$pre);
	$gesamtsumme = 0;
        $pgsumme = 0;
        foreach($artikel as $part) {
            //print_r($part); echo "<br>";
            if ($pg != $part['partsgroup_id'] AND $_POST['wg'] == 1) {
                $line = preg_replace("/<%gesamtsumme%>/i",sprintf('%0.2f',$pgsumme),$vorlage['post']);
                $rc = fputs($f,$line);
                $pgsumme = 0;
                fclose($f);
                closeinventur($art,$fname);
                $pg = $part['partsgroup_id'];
                $fname = $art.'_'.$pg;
                $link .= "<a href='tmp/$fname.pdf'>".$part['partsgroup']."</a><br />";
                $f = fopen('tmp/'.$art.'.tex','w');
                $pre = preg_replace("/<%partsgroup%>/i",$part['partsgroup'],$vorlage['pre']);
                $rc = fputs($f,$pre);
            }
            $line = $vorlage['artikel'];
            $ep = 1;
            $qty = 0;
            foreach ($part as $key=>$val) {
                if ($key == 'description') $val = str_replace($suche,$ersetze,$val);
                if ($key == 'partnumber') $val = str_replace($suche,$ersetze,$val);
                if ($key == 'bestand') {
                };
                if ( $key == 'ep' ) {
			$ep = $val;
			$val = sprintf('%0.2f',$val);
		}
                if ( $key == 'bestand' ) {
   		     	$qty = $val * 1;
			if ( floor($qty) == $qty ) {
				$val = sprintf('%7d',$qty);
			} else {
				while (substr($val,-1) == '0') { $val = substr($val,0,-1); }
				while (strlen($val) < 7 ) { $val = ' '.$val; };
			}
                        if ($_POST['bestand'] != '1' and $art == 'inventur') $val = '';
		};
                $line = preg_replace("/<%$key%>/i",$val,$line);
            }
            $summe = sprintf('%0.2f',$qty*$ep);
            $gesamtsumme += $qty*$ep;
            $pgsumme += $qty*$ep;
            $line = preg_replace("/<%summe%>/i",$summe,$line);
            $qty ++;
            $rc = fputs($f,$line);
        }
        $line = preg_replace("/<%gesamtsumme%>/i",sprintf('%0.2f',$gesamtsumme),$vorlage['post']);
        $rc = fputs($f,$line);
        fclose($f); 
        closeinventur($art,$fname);
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
<div class="ui-widget-content" style="height:600px">
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">Inventur</p>
<form name="inventur" action="inventur.php" method="post">
<input type='radio' name='art' value='inventur' checked>Inventurliste <br>
<input type='radio' name='art' value='bestand'>Bestandsliste ab: <input type='text' name='datum' size='10' id='datum'><br />
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
</div>
<?php }; echo $menu['end_content']; ?>
</body>
</html>
