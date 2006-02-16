<?
// $Id: update.php,v 1.2 2005/12/22 13:35:02 hli Exp $
	echo "Update auf Version $VERSION<br>";
	$updatefile="update/update".$rc[0]["version"]."_$VERSION.php";
	if (is_file($updatefile)) {
		require($updatefile);
	} else {
		echo "Updatefile: $updatefile nicht gefunden.";
	}
?>
