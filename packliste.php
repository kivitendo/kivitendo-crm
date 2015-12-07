<?php
    include_once('inc/stdLib.php');
    include_once("Mail.php");
    include_once("Mail/mime.php");
    define('FPDF_FONTPATH','font/');
    require('fpdf.php');
    $menu =  $_SESSION['menu'];
    $head = mkHeader();    
    class PDF extends FPDF {
        //Load data
        function LoadData($file)  {
            //Read file lines
            $f = fopen($file,'r');
            $data=array();
            while (($data[] = fgetcsv($f,1000, ";", '"')) !== FALSE ) {};
            fclose($f);
            return $data;
        }

        function BasicTable($data)  {
            //Header
            $this->Cell(20,7,'Menge',1);
            $this->Cell(40,7,'Nummer',1);
            $this->Cell(130,7,'Artikel',1);
            $this->Ln();
            $tmp = array_shift($data);
            $tmp = array_pop($data); // Warum auch immer ein leeres Array am Ende ist.
            //Data
            foreach($data as $row)   {
               if ( substr($row[0],0,3) == '###' ) {
                     $this->MultiCell(190,6,utf8_decode(substr($row[0],3)),0,'L',0);
               } else {
                   $this->Cell(20,6,$row[2],1);
                   $this->Cell(40,6,$row[1],1);
                   $this->Cell(130,6,utf8_decode(substr($row[0],0,70)),1);
               }
               $this->Ln();
           }
        }
        function Titel($txt) {
             $this->Cell(200,10,$txt,0);
             $this->Ln();
        }
    };
    if ( $_POST ) {
        $dir = 'tmp/';
        $filecsv = 'packliste.csv';
        $filepdf = 'packliste.pdf';
        $datum = $_POST['datum'];
        $output = 'Packliste ab: '.$datum.'<br>';
        if ( $_POST['read'] != '' ) {
            $tmp  = split('\.',$datum);
            $date  = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];
            $sql  = 'select parts.description,partnumber,sum(qty) as qty ';
            $sql .= 'from invoice left join parts on parts.id=parts_id ';
            $sql .= 'where trans_id in ';
            $sql .= '(SELECT id from ar where  amount > 0 and transdate between \''.$date.'\' and now() ) '; //= \''.$date.'\') ';
            $sql .= 'group by partnumber,parts.description order by qty desc'; //partnumber';
            $rs = $_SESSION['db']->getAll($sql);
            $line  = "<tr><td><input type='text' size='3'  name='data[qty][]' value='%s'></td>";
            $line .=     "<td><input type='text' size='10' name='data[partnumber][]' value='%s'></td>";
            $line .=     "<td><input type='text' size='70' name='data[description][]' value='%s'></td></tr>\n";
            if ( $rs ) { 
                $output .= '<form name="packliste" action="packliste.php" method="post"><table id="tabelle">'."\n";
                $output .= "<input type='hidden' name='datum' value='$datum'>\n";
                $output .= '<tr><th>Menge</th><th>Art-Nr.</th><th>Bezeichnung</th></tr>';
                foreach ( $rs as $row ) {
                    $output .= sprintf($line,$row['qty'],$row['partnumber'],$row['description']);
                };
                #$output .= sprintf($line,'','','');
                $output .= "</table>\n";
                $output .= 'Notizen:<br><textarea name="notiz" cols="80" rows="10" ></textarea><br>';
                $output .= '<input type="button" name="zeile" value="Zeilen +" onClick="ZeileEinfuegen();">'."\n";
                $output .= '<input type="submit" name="generate" value="PDF">';
                $output .= '<input type="radio" name="weg" value="1" checked>download <input type="radio" name="weg" value="2">per E-Mail';
                $output .= '</form>'."\n";
            } else {
                $output .= 'Keine Treffer<br>';
            }
        } else if ( $_POST['generate'] != '' ) {
            $output = '';
            $f = fopen($dir.$filecsv,'w');
            fputs($f,'description;partnumber;qty'."\n");
            $data = $_POST['data'];
            for ( $i = 0; $i < count($data['qty']); $i++) {
                if ( $data['qty'][$i] > 0 )
                    fputs($f,'"'.$data['description'][$i].'";"'.$data['partnumber'][$i].'";'.$data['qty'][$i]."\n");
            };
            if ( $_POST['notiz']    != '' ) { fputs($f,'"###'.$_POST['notiz'].'"'); };
            fclose($f);
            $pdf=new PDF();
            $data=$pdf->LoadData($dir.$filecsv);
            $pdf->SetFont('Arial','',10);
            $pdf->AddPage();
            $pdf->SetTitle('Packliste ab '.$datum);
            $pdf->Titel('Packliste ab '.$datum);
            $pdf->SetDrawColor(255, 255, 255);
            $pdf->BasicTable($data);
            $pdf->Output($dir.$filepdf,"F");
            if ( file_exists($dir.$filepdf) ) {
                if ( $_POST['weg'] == 2 ){
                    $headers = array(
                            "From"        => $_SESSION['email'],
                            "X-Mailer"    => "PHP/".phpversion(),
                            "Subject"    => 'Packliste ab '.$datum);
                    $mime = new Mail_Mime(array('eol'=>"\n"));
                    $csv = $mime->addAttachment($dir.$filecsv,mime_content_type($dir.$filecsv),$filecsv);
                    $pdf = $mime->addAttachment($dir.$filepdf,mime_content_type($dir.$filepdf),$filepdf);
                    $mime->setTXTBody('Packliste');
                    $hdrs = $mime->headers($headers);
                    $body = $mime->get();
                    $mail = Mail::factory("mail");
                    $mail->_params = "-f ".$_SESSION['email'];
                    $rc = $mail->send($_SESSION['email'], $hdrs, $body);
                    $output = 'E-Mail verschickt';
                    #unlink($dir.$filepdf);
                } else {
                    header('Content-type: application/pdf');
                    header('Content-Disposition: attachment; filename="packliste.pdf"');
                    readfile($dir.$filepdf);
                    #unlink($dir.$filepdf);
                }
            } else {
                $output .= 'Konnte kein PDF erstellen.<br>';
            };
        } else {
            $output .= 'Keine Treffer<br>';
        };
    };
?>
<html>
<head>
<?php 
      echo $menu['stylesheets']; 
      echo $head['CRMCSS'];
      echo $menu['javascripts']; 
      echo $head['THEME']; ?>
<script language="JavaScript">
        $(function() {
            $( "#datum" ).datepicker($.datepicker.regional[ "de" ]);
        });
</script>
<script type="text/javascript" src="inc/packliste.js"></script>
<body>
<?php
 echo $menu['pre_content'];
 echo $menu['start_content'];
 echo $output; ?>
 <div class="ui-widget-content" style="height:600px">
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">Packliste</p>
<form name="pl" method='post' action='packliste.php'>
Packliste erstellen ab:<br>
<input type="text" size="10" name="datum" id="datum" value="" > tt.mm.jjjj <br>
<input type="submit" name="read" value="erstellen">
</form>
</div>
<?php echo $menu['end_content']; ?>
</body>
</html>
