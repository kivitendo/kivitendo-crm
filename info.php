<?php 
phpinfo(); 
include ("inc/conf.php");
echo "<center><table>";
echo "<tr><td>Grafik:</td><td>".(($jpg)?"jpgraph":"PEAR_IMAGE")."</td></tr>";
echo "<tr><td>Xajax-Ver:</td><td>".XajaxVer."</td></tr>";
echo "<tr><td>Xajax-Pfad:</td><td>".XajaxPath."</td></tr>";
if (XajaxVer == "05") {
	$xc = "False";
	if (file_exists(XajaxPath."xajax_core/xajax.inc.php")) $xc = "ok";
	if (file_exists(XajaxPath."xajax_js")) $xj = "ok";
echo "<tr><td>xajax_core:</td><td>".$xc."</td></tr>";
echo "<tr><td>xajax_js:</td><td>".$xj."</td></tr>";
} else {
	$x = "False";
	if (file_exists(XajaxPath."xajax/xajax.inc.php")) $x = "ok";
echo "<tr><td>xajax:</td><td>".$x."</td></tr>";
}
echo "<tr><td>LxO Pfad:</td><td>".getCwd()."</td></tr>";
?>
</table>
<a href="status.php">Status</a>
</center>
