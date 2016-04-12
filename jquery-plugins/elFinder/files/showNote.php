<?php
	require_once("inc/stdLib.php");
	if ($_GET["fid"]) {
		include("inc/FirmenLib.php");
		$data=getFirmenStamm($_GET["fid"],true,"C");
	} else if ($_GET["lid"]) {
		include("inc/FirmenLib.php");
		$data=getFirmenStamm($_GET["lid"],true,"V");
	} else if ($_GET["pid"]) {
		include("inc/persLib.php");
		$data=getKontaktStamm($_GET["pid"]);
	}
?>
<html>
<head><title>Lx-Notiz</title>
</head>
<body>
<?php
if ($data["notes"]) {
	echo strtr($data["notes"],array("\n" => "<br>"));
} else if ($data["cp_notes"]) {
	echo strtr($data["cp_notes"],array("\n" => "<br>"));
} else {
	echo "Es ist keine Bemerkung hinterlegt";
}
?>
<hr><br>
<input type="button" onClick="self.close()" value="schlie&szlig;en"> 
<script language='JavaScript'>self.focus();</script>
</body>
</html>
