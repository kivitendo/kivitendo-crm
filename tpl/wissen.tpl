<!-- $Id: liefer3.tpl 946 2006-03-01 12:42:11Z hlindemann $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	{tiny}
<body>
<p class="listtop">Wissensdatenbank</p>

<span style="position:absolute; left:10px; top:67px; width:95%; border: 0px solid black">
<!-- Hier beginnt die Karte  ------------------------------------------->
	<div style="float:left; width:33%; text-align:left; border: 0px solid red" >
		<form name="wissen" action="wissen.php" method="post">
		<input type="hidden" name="id" value="{id}">
		<input type="hidden" name="m" value="{menuitem}">
		<input type="hidden" name="version" value="{version}">
		<strong><a href='wissen.php?m=0'>Kategorien</a></strong><br>
		{menu}
	 	<br />
		{catinput}
		Eine neue Kategorie unter &quot;<b>{catname}</b>&quot;
		<input type="image" src="image/neu.png" name="newcat" title="Neue Kategorie erstellen" value="erstellen">
	</div>
	<div style="float:left; width:65%; text-align:left; border: 0px solid blue; padding-left: 10px;" >
		{headline}<br />
		<hr />
		{pre}{content}{post}<br />
		{button1} &nbsp; {button2}
		</form>
	</div>
<!-- Hier endet die Karte ------------------------------------------->
</span>
</body>
</html>
