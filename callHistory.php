<?php
	require_once("inc/stdLib.php");
	include("inc/crmLib.php");
?>
<html>
	<head><title>LX - CRM</title>
        <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['baseurl'].'crm/css/'.$_SESSION["stylesheet"]; ?>main.css"></link>
	<style type="text/css">
		td { padding-left:4px; }
	</style>
	</head>
<body>
<?php
	$data=getCallHistory($_GET["id"],($_GET["del"])?true:false);
	echo "<table width='100%'>\n";
	if ($data) foreach ($data as $daten) {
		echo "<tr><td>".$daten["cause"]."</td><td>".$daten["caller_id"]."</td><td>".$daten["employee"]."</td><td>".$daten["datum"]."</td><td>".$daten["chgid"]."</td><td>".$daten["grund"]."</td></tr>\n";
		echo "<tr><td colspan='6'>".$daten["c_long"]."</td></tr>\n";
		echo "<tr><td colspan='6'><hr></td></tr>\n";
	}
?>
</table>
[<a href="JavaScript:self.close()">Fenster schlie&szlig;en</a>]
</body>
</html>
