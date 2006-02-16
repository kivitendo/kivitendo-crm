<!-- $ID: $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>

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
Konflikte mit diesen Terminen:<br><br>
<table style="width:360px" bgcolor="#ffffff">
<!-- BEGIN Liste -->
	<tr onClick="zeige({tid})"><td class="smal we re" width="100px">{start}</td><td class="smal {we} re" width="100px">{stop}</td><td class="smal {we}">{cause}</td></tr>
	<tr><td class="gr" colspan="3"></td></tr>
<!-- END Liste -->
</table>
</center>
</body>
</html>
