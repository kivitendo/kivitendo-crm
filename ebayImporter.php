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
print_r($_POST);

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
move_uploaded_file($_FILES['file']['tmp_name'], "upload/import-src.csv"); 

//Datei nach UTF8 konvertieren
$command = "iconv -f ISO-8859-15 -t UTF8 -c -o upload/import-utf8.csv upload/import-src.csv";
system($command);
//$data = array();
$row = 1;
if (($handle = fopen("upload/import-utf8.csv", "r")) !== FALSE) {
 
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
global $db;
$end = "Endverbraucher"; //ANPASSEN


$sql = "SELECT id FROM business WHERE description ILIKE '$end'";
$rs = $db->getOne($sql);
$end_id = $rs['id'];


$sql = "SELECT id FROM leads WHERE lead ILIKE 'ebay'";
$rs = $db->getOne($sql);
$ebayLeadId = $rs['id'];  

//SELECT id FROM payment_terms WHERE description ILIKE 'paypal' OR description_long ILIKE '%paypal%';

$sql = "SELECT id FROM payment_terms WHERE description ILIKE 'paypal' OR description_long ILIKE '%paypal%'";
$rs = $db->getOne($sql);
$paypalId = $rs['id']; 

$sql = "SELECT id FROM payment_terms WHERE description ILIKE 'vorkasse' OR description_long ILIKE '%vorkasse%'";
$rs = $db->getOne($sql);
$payotherId =  $rs['id'];

array_shift($csvArray);//Erste Zeile löschen
 //print_r($csvArray);
echo "<table id='treffer' class='tablesorter'>\n"; 
echo "<thead><tr ><th>Ebayname</th><th>Name</th><th>Anschrift</th><th>Email</th><th>1.Artikel</th></tr></thead>\n<tbody>\n"; 
$i = 0;

if ($csvArray) foreach($csvArray as $key => $row) {
    $ok = true;
    if( $row['4'] == $row['2'] ){
        $row['4'] = $row['5'];
        $row['5'] = '';
        $row['attention'] = "!!!changed by Importer!!!";
    }
    if ($row['5'] != '') {
        $row['2'] .= " ".$row['5'];
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
        $rs=$db->getAll($sql);
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
            $sql = "INSERT INTO customer (name, department_1, street, zipcode, city, country, business_id, customernumber, lead, payment_id  ) VALUES ";
            $sql.= "('".$row["2"]."', '".$row["1"]."', '".$row["4"].$row["5"]."', '".$row["8"]."','".$row["6"]."', '".$row['9']."', ".$end_id.", '".$CustNb."', ".$ebayLeadId.", ".$paypalId." )";
            echo "SQL: ".$sql;            
            $rcc = $db->query($sql);
        }
    }
    

}
echo "</tbody></table>\n";
echo 
$menu['end_content'].
'</body>';




?>