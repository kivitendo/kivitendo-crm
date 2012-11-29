<html>
	<head><title></title>
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/tabcontent.css"></link>
        {STYLESHEETS}

        {AJAXJS}
        {JAVASCRIPTS}

	<script language="JavaScript">
	<!--
	function chkfld() {
<!-- BEGIN RegEx -->
		if (! document.firma4.{fld}.value.match(/^{regul}*$/)) { alert("{fld}"); return false; };
<!-- END RegEx -->
		return true;
	}
	//-->
	</script>
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:generate document:.</p>
<span style="position:absolute; top:1.1em; left:0.5em;  width:98%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="firma4" action="firma4a.php" method="post" onsubmit="return chkfld();">
<input type="hidden" name="docid" value="{DOCID}">
<input type="hidden" name="fid" value="{FID}">
<input type="hidden" name="pid" value="{PID}">
<input type="hidden" name="tab" value="{TAB}">
<div style="position:absolute; left:1.0em; top:0.1em; width:99%; text-align:left; border: 0px solid black;" class="normal">
{Beschreibung}<br>
<table>
<!-- BEGIN Liste -->
	<tr><td>{Feldname} </td><td title=".:keyin:. {Feldname}">&nbsp;{EINGABE}</td></tr>
<!-- END Liste -->
</table><br>
{Knopf}
</div>
</form>
<!-- Hier endet die Karte ------------------------------------------->
</span>
{END_CONTENT}
</body>
</html>
