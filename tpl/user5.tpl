<!-- $Id: user5.tpl,v 1.2 2005/09/16 12:01:12 hli Exp $ -->
<html>
	<head><title>User Stamm</title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--

	//-->
	</script>
<body>
<p class="listtop">Update</p>

<!--table width="95%" class="karte"><tr><td class="karte"-->
<!-- Beginn Code ----------------------------------------------->

	<br>Update Lx-CRM. Aktuelle Version: {Version} db: {Versiondb}
	<br><br>
		<form name="formular" action="user4.php" method="post">
			Update-Files:<br>
<!-- BEGIN lokale -->
			<input type="radio" name="ver" value="{val}">{val}<br>
<!-- END lokale -->
			Update-Verzeichnis<br>
			{Dir}
			<br>
			<input type="submit" name="install" value="installieren">

	</form>
	Achtung!! Der Benutzer des Webservers benötigt Schreibrechte im gesamten CRM-Verzeichnis.<br>
	cd /pfad/zu/lx-crm<br>
	chown -R apache .<br>
	Auf einigen System lautet der User auch: www-data, httpd
<!-- End Code ----------------------------------------------->
<!--/td></tr></table-->
</body>
</html>

