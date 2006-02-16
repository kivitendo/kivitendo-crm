<?
// $Id: report.php,v 1.4 2005/12/01 08:12:48 hli Exp $
require_once("inc/stdLib.php");
if ($_GET["tab"]=="customer") {
	$tabellen=array("customer"=>array("Kunden","K"),
			"shipto"=>array("Abweichend","S"),
			"contacts"=>array("Personen","P"));
	$noshow=array("itime","mtime");
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
			for (i=0; i < <?= $anzahl ?>; i++) {
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
<? foreach ($tabellen as $key=>$val) { ?> 	
 		<td><?= $val[0] ?><br>
 			<select name="name" size="10" multiple>
 <? 
 		foreach ($felder[$key] as $row) { 
 			echo 	"<option value='".$val[1].".".$row."'>".$row."</option>";
 		} 
 ?>
		</select></td>
<? } ?>
	</tr>
 </table>
 <input type="button" name="senden" value="&uuml;bernehmen" onClick="sende()"/>
  
 </body>
 </html>
