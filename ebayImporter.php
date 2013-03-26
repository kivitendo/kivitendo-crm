<?php
/***************************************************************************************************
***** EbayImporter: Importiert Kundendaten und Rechnungsdaten der EbayCSV-Exporte in Kivitendo *****
***************************************************************************************************/
//Script im Alpha-Stadium
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
table.bgcol1 {background-color:#efd; }
table.bgcol2 {background-color:#666; }
</style>  
<?php
echo '
</head>
<body>
'.$menu['pre_content'].
$menu['start_content'].
'<table width="600">
<form action="getEbay.php?" method="post" enctype="multipart/form-data">

<tr>
<td width="20%">Select file</td>
<td width="80%"><input type="file" name="file" id="file" /></td>
</tr>

<tr>
<td>Submit</td>
<td><input type="submit" name="submit" /></td>
</tr>

</form>
</table>

';
//Datei nach UTF8 konvertieren
$command = "iconv -f ISO-8859-15 -t UTF8 -c -o upload/test-utf8.csv upload/test.csv";
system($command);
//$data = array();
$row = 1;
if (($handle = fopen("upload/test-utf8.csv", "r")) !== FALSE) {
 
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
$end = "Privatkunde"; //ANPASSEN
$werk = "Werksatt";
$hand = "Handel";
$ah = "Autohaus";

$sql = "SELECT id FROM business WHERE description ILIKE '$end'";
$rs = $db->getOne($sql);
$end_id = $rs['id'];

$sql = "SELECT id FROM business WHERE description ILIKE '$werk'";
$rs = $db->getOne($sql);
$werk_id = $rs['id'];

$sql = "SELECT id FROM business WHERE description ILIKE '$hand'";
$rs = $db->getOne($sql);
$hand_id = $rs['id'];

$sql = "SELECT id FROM business WHERE description ILIKE '$ah'";
$rs = $db->getOne($sql);
$ah_id = $rs['id'];



array_shift($csvArray);//Erste Zeile löschen
 //print_r($csvArray);
echo "<table id='treffer' class='tablesorter'>\n"; 
echo "<thead><tr ><th>Ebayname</th><th>Name</th><th>Anschrift</th><th>Email</th><th>1.Artikel</th></tr></thead>\n<tbody>\n"; 
$i = 0;

if ($csvArray) foreach($csvArray as $row) {
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
    if (!$row['1'] && !$row['2']) {
        unset($csvArray[$row]);
        //break -1;
        $ok = false;
                
    }
    if ($ok) { 
        $sql = "SELECT * FROM customer WHERE name ILIKE '".$row["2"]."' AND zipcode = '".$row["8"]."' OR department_1 ILIKE '".$row["1"]."' OR email ILIKE '".$row["3"]."'";
        $rs=$db->getAll($sql);
        //if ($rs) $row['9'] = 'vorhanden';
        echo "<tr class='bgcol".($i%2+1)."'>". 
             "<td class=\"liste\">".$row["1"]."</td><td class=\"liste\">".$row["2"]."</td>". 
             "<td class=\"liste\">".$row["4"].$row["5"].", ".$row["8"]." ".$row["6"]."</td><td class=\"liste\">".$row["3"]."</td><td class=\"liste\">".$row["13"]."</td>
             <td class=\"liste\">".$row['attention']."</td><td class=\"liste\">".$row['9']."</td></tr>\n"; 
        $i++;
        if (!$rs['0']) {
            $CustNb = newnr('customer'); 
            echo "Nummer: ".$CustNb;           
            $sql = "INSERT INTO customer (name, department_1, street, zipcode, city, country, business_id, customernumber  ) VALUES ('".$row["2"]."', '".$row["1"]."', '".$row["4"].$row["5"]."', '".$row["8"]."','".$row["6"]."', '".$row['9']."', ".$end_id.", '".$CustNb."' )";
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