<?php
require_once("inc/stdLib.php");
if ($_GET["tab"]=="C") {
	$tabellen=array("customer"=>array("Kunden","K"),
			"shipto"=>array("Abweichend","S"),
			"contacts"=>array("Personen","P"));
	$noshow=array("itime","mtime");
} else if ($_GET["tab"]=="V") {
	$tabellen=array("vendor"=>array("Lieferant","L"),
			"shipto"=>array("Abweichend","S"),
			"contacts"=>array("Personen","P"));
	$noshow=array("itime","mtime");
}
if ($_GET["tab"]) {
	foreach($tabellen as $key=>$val) {
		$sql="SELECT a.attname FROM pg_attribute a, pg_class c WHERE ";
		$sql.="c.relname = '$key' AND a.attnum > 0 AND a.attrelid = c.oid ORDER BY a.attnum";
		$rs=$db->getAll($sql);
		if ($rs) { 
			foreach ($rs as $row) {
				if (!in_array($row["attname"],$noshow))
					$felder[$key][]=$row["attname"];
			}
		} else {
			$felder[$key]=false;
		}
	}
	$anzahl=count($tabellen);
}
$anzahl=count($tabellen);
?>
<html>
<head><title></title>
	<script language="JavaScript">
		function sende() { 
			felder="";
			for (i=0; i < <?php echo  $anzahl ?>; i++) {
				anz=document.report.elements[i].length;
				for (j=0; j<anz; j++) {
					if (document.report.elements[i].options[j].selected==true) {
						felder += document.report.elements[i].options[j].value + ","; 
					}
				}
			}
			
			opener.document.erwsuche.felder.value=felder;
  			opener.document.erwsuche.submit();
  			self.close();
  		}
  	</script>
 </head>
 <body>
 <center>
 <h3>Reportgenerator</h3>
 Bitte w&auml;hlen Sie die gew&uuml;nschten Tabellenfelder aus.
 <form name="report" method="post" action="report.php">
 <table>
 	<tr>
<?php foreach ($tabellen as $key=>$val) { ?> 	
 		<td><?php echo  $val[0] ?><br>
 			<select name="name" size="10" multiple>
 <?php 
 		foreach ($felder[$key] as $row) { 
 			echo 	"<option value='".$val[1].".".$row."'>".$row."</option>";
 		} 
 ?>
		</select></td>
<?php } ?>
	</tr>
 </table>
 <input type="button" name="senden" value="&uuml;bernehmen" onClick="sende()"/>
  
 </body>
 </html>
