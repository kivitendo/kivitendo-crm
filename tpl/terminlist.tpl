<!-- $ID: $ -->
<html>
	<head><title></title>
    <link type="text/css" REL="stylesheet" HREF="../css/{ERPCSS}"></link>
    <link type="text/css" REL="stylesheet" HREF="css/{ERPCSS}"></link>

	<script language="JavaScript">
	<!--
	function zeige(tid) {
		if (tid>0)
			f=open("showTermin.php?termid="+tid,"termine","width=400,height=300,left=300,top=150");
	}
	//-->
	</script>
<body >
<center>
<br>
{HEADLINE}<br><br>
<table style="width:29em" bgcolor="#ffffff" class="normal">
<!-- BEGIN Liste -->
	<tr onClick="zeige({tid})"><td class="we le" style="width:7.0em">{start}</td><td class="{we} le" style="width:7.0em">{stop}</td><td class="{we}">{cause}</td></tr>
	<tr><td class="gr" colspan="3"></td></tr>
<!-- END Liste -->
</table>
</center>
</body>
</html>
