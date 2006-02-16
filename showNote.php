<?
// $Id: showNote.php,v 1.3 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	if ($_GET["fid"]) {
		include("inc/FirmaLib.php");
		$data=getFirmaStamm($_GET["fid"]);
	} else if ($_GET["lid"]) {
		include("inc/LieferLib.php");
		$data=getLieferStamm($_GET["lid"]);
	} else if ($_GET["pid"]) {
		include("inc/persLib.php");
		$data=getKontaktStamm($_GET["pid"]);
	}
?>
<html>
<head><title>Lx-Notiz</title>
</head>
<body>
<?
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
