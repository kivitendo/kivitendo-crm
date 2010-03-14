<?php
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
