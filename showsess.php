<?
session_start();
if ($_POST["ok"]) {
	while( list($key,$val) = each($_SESSION) ) {
		unset($_SESSION[$key]);
	}
}

echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
<form name="x" action="showsess.php" method="post">
<input type="submit" name="ok" value="Session löschen">
</form>

