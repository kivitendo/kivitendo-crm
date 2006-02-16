<? 
// $Id: schnellsuche.php,v 1.3 2005/11/02 10:37:52 hli Exp $
   require_once("inc/stdLib.php");
   include("inc/crmLib.php");
?>
<html>
        <head><title></title>
	        <link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	</head>
<body>
<table width=100%>
  <tr>
      <th class=listtop>Schnellsuche Kunde/Lieferant/Kontakte</th>
  </tr>
  <tr>
      <td>
	<form name="suche" action="getData.php" method="post">
	<input type="hidden" name="login" value="<?= $_GET["login"] ?>">
	<input type="hidden" name="password" value="<?= $_GET["password"] ?>">
	<input type="text" name="swort" size="20">
	<input type="submit" name="ok" value="suchen">
	</form>
      </td>
  </tr>
  <tr>
      <td>
      	Suchbegriff
      </td>
  </tr>
</table>
</body>
</html>
