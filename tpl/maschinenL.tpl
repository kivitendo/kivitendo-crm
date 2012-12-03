<html>
	<head><title></title>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
        {STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
        {JAVASCRIPTS}
<body >
{PRE_CONTENT}
{START_CONTENT}
<table width="99%" border="0"><tr><td>
<!-- Beginn Code ------------------------------------------->
<p class="listtop">Maschinen Trefferliste</p>
<form name="formular"  action="{action}" method="post">
<input type="hidden" name="MID" value="{MID}">
<select name="serialnumber" size="15">
<!-- BEGIN Sernumber -->
    <option value="{number}">{description}
<!-- END Sernumber -->
</select>
<input type="submit" name="search" value="sichern">
{END_CONTENT}
</body>
</html>
	
