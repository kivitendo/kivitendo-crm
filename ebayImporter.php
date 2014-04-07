<?php
/***************************************************************************************************
***** EbayImporter: Importiert Kundendaten und Rechnungsdaten der EbayCSV-Exporte in Kivitendo *****
***************************************************************************************************/
//Script im Alpha-Stadium
//ToDo0: Nur Mitglieder der Gruppe Admin erlauben, naxFilesize beschränken
//ToDo1: 3 Phasen realisieren: Upload+Einstellung, Darstellung+Korrektur, Datenimport  
//ToDo2: Rechnungen importieren
require_once("inc/stdLib.php");
require_once("inc/crmLib.php");
require_once("inc/FirmenLib.php");
$menu = $_SESSION['menu'];
$head = mkHeader();
echo '<html>
<head><title></title>';
echo $menu['stylesheets'];
echo $head['CRMCSS'];
echo $head['JQUERY'];
echo $head['JQUERYUI'];
echo $head['THEME'];
echo $head['JQTABLE'];  
?>
<style type="text/css">
tr.bgcol1 {background-color:#efd; }
td.bgcol2 {background-color:#666; }

table.bgcol1 th {
background-color: #e3e3e3;
}

</style>  

<script>
    $(function() {
        $('#treffer')
            .tablesorter({widthFixed: true, widgets: ['zebra']})
            .tablesorterPager({container: $("#pager"), size: 120, positionFixed: false})
    }); 
</script>
<?php

$form_select_file = '
</head>
<body>'.
$menu['pre_content'].
$menu['start_content'].
'<table width="600">
<form action="ebayImporter.php" method="post" enctype="multipart/form-data">
<tr>
<td width="20%">.:Select file:.</td>
<td width="80%"><input type="file" name="file" id="file" /></td>
</tr>
<tr>
<td>.:Submit:.</td>
<td><input type="submit" name="select_file" /></td>
</tr>
</form>
</table>
';

if ( !$_POST['select_file'] ) echo $form_select_file; // in Phase 3 $_POST['select_file'] = true;

if ($_FILES['file']['type'] != 'text/csv') exit();
move_uploaded_file($_FILES['file']['tmp_name'], "tmp/import-src.csv");


//Datei nach UTF8 konvertieren


system("file tmp/import-src.csv | grep 'UTF-8'; echo $?>test_utf8");
$dateihandle = fopen("test_utf8","r");
$zeichen = fgetc($dateihandle);
fclose($dateihandle);


echo "*****************************************";
echo $zeichen;
//readfile("test_utf8");
echo "*****************************************";

// Uploaded file is UTF-8 encoded? i know, it's ugly, but it's working.
if("$zeichen"!="0"){
   echo "NOT UTF-8";
// die daten sind anscheinend ISO-8859-2 (laut chardet)

$command = "iconv -f ISO-8859-15 -t UTF8 -c -o tmp/import-utf8.csv tmp/import-src.csv";
#$command = "iconv -f ISO-8859-15 -t UTF8 -c -o upload/import-src.csv upload/import-utf8.csv";
system($command);
}
else {
  echo "UTF-8";
  system("cp tmp/import-src.csv tmp/import-utf8.csv");
}

//Testkommentar
//Ausführen:
system($command);
//$data = array();
$row = 1;
if (($handle = fopen("tmp/import-utf8.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        //$num = count($data);
        //echo "<p> $num Felder in Zeile $row: <br /></p>\n";
        $row++;
        //for ($c=0; $c < $num; $c++) {
            //echo $data[$c] . "<br />\n";
        //}
        $csvArray[$row] = $data;    

    }
    
   
    fclose($handle);
}

//BusinessIds ermitteln
$end = "Endverbraucher"; //ANPASSEN


$sql = "SELECT id FROM business WHERE description ILIKE '$end'";
$rs = $_SESSION['db']->getOne($sql);
$end_id = $rs['id'];


$sql = "SELECT id FROM leads WHERE lead ILIKE 'ebay'";
$rs = $_SESSION['db']->getOne($sql);
$ebayLeadId = $rs['id'];  

//SELECT id FROM payment_terms WHERE description ILIKE 'paypal' OR description_long ILIKE '%paypal%';

$sql = "SELECT id FROM payment_terms WHERE description ILIKE 'paypal' OR description_long ILIKE '%paypal%'";
$rs = $_SESSION['db']->getOne($sql);
$paypalId = $rs['id']; 

$sql = "SELECT id FROM payment_terms WHERE description ILIKE 'vorkasse' OR description_long ILIKE '%vorkasse%'";
$rs = $_SESSION['db']->getOne($sql);
$payotherId =  $rs['id'];

array_shift($csvArray);//Erste Zeile löschen
 //print_r($csvArray);
echo "<table id='treffer' class='tablesorter'>\n"; 
echo "<thead><tr ><th>Ebayname</th><th>Name</th><th>Anschrift</th><th>Email</th><th>1.Artikel</th></tr></thead>\n<tbody>\n"; 
$i = 0;
/*
echo "<pre>";
print_r ($csvArray);
echo "</pre>";

*/
if ($csvArray) foreach($csvArray as $key => $row) {
    $ok = true;
    if( $row['4'] == $row['2'] ){
        $row['4'] = $row['5'];
        $row['5'] = '';
        $row['attention'] = "!!!changed by Importer!!!";
    }
    if ($row['5'] != '') {
        $row['6'] .= " ".$row['5'];
        $row['5'] = '';
        $row['attention'] = "!!!changed by Importer!!!";
    }
    $row['2'] = ucwords($row['2']);
    $row['4'] = ucwords($row['4']);
    $row['6'] = ucwords($row['6']);
    $row['9'] = $row['9'][0]; //Deutschland to D
    
    
    $del = false;
    //Dublikate entsorgen
    if (!$row['1'] && !$row['2']) {
        unset($csvArray[$row]);
        //break -1;
        $ok = false;
                
    }
    echo "row: ".$csvArray[$key - 1]['1'];
    if ( $csvArray[$key ]['1'] == $csvArray[$key + 1]['1'] ){
         unset($csvArray[$key]);
         $ok = false;
    }
    if ($ok) { 
        $i++;
        $sql = "SELECT * FROM customer WHERE name ILIKE '".$row["2"]."' AND zipcode = '".$row["8"]."' OR department_1 ILIKE '".$row["1"]."' OR email ILIKE '".$row["3"]."'";
        $rs=$_SESSION['db']->getAll($sql);
        echo "Vorhanden??: ".$rs[0]['name']."<br />";
        echo "<tr class='bgcol2'>". 
             "<td class=\"liste\">".$row["1"]."</td><td class=\"liste\">".$row["2"]."</td>". 
             "<td class=\"liste\">".$row["4"].$row["5"].", ".$row["8"]." ".$row["6"]."</td><td class=\"liste\">".$row["3"]."</td><td class=\"liste\">".$row["13"]."</td>
             <td class=\"liste\">".$row['attention']."</td><td class=\"liste\">".$row['9']."</td><td class=\"liste\">".$row['20']."</td></tr>\n"; 
        $i++;
        if (!$rs['0']) {
            $CustNb = newnr('customer',$end_id); 
            echo "Nummer: ".$CustNb; 
            $payment = $row['20']=='PayPal'?$paypalId:$payotherId;         
            $sql = "INSERT INTO customer (email, name, department_1, street, zipcode, city, country, business_id, customernumber, lead, payment_id, currency_id  ) VALUES ";
            $sql.= "('".$row['3']."', '".$row["2"]."', '".$row["1"]."', '".$row["4"].$row["5"]."', '".$row["8"]."','".$row["6"]."', '".$row['9']."', ".$end_id.", '".$CustNb."', ".$ebayLeadId.", ".$paypalId." , 1 )";
            echo "SQL: ".$sql;            
            $rcc = $_SESSION['db']->query($sql);
        }
    }
  //$row['3']  

}
echo "</tbody></table>\n";
echo 
$menu['end_content'].
'</body>';



?>
