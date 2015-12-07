<html>
	<head><title></title>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
{STYLESHEETS}
{CRMCSS}
{JAVASCRIPTS}
<body >
{PRE_CONTENT}
{START_CONTENT}
<div class="ui-widget-content" style="height:600px">
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">Maschinen Trefferliste</p>
<form name="formular"  action="{action}" method="post">
<input type="hidden" name="MID" value="{MID}">
<select name="{fldname}" size="25">
<!-- BEGIN Sernumber -->
    <option value="{number}">{description}
<!-- END Sernumber -->
</select>
<input type="submit" name="search" value="sichern">
</div>
{END_CONTENT}
</body>
</html>
	
