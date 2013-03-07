<html>
	<head><title></title>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{JAVASCRIPTS}
<body >
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">Maschinen Trefferliste</p>
<form name="formular"  action="{action}" method="post">
<input type="hidden" name="MID" value="{MID}">
<select name="{fldname}" size="25">
<!-- BEGIN Sernumber -->
    <option value="{number}">{description}
<!-- END Sernumber -->
</select>
<input type="submit" name="search" value="sichern">
{END_CONTENT}
</body>
</html>
	
