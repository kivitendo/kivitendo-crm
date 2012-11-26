<html>
	<head><title></title>
        <link type="text/css" REL="stylesheet" HREF="css/{ERPCSS}/main.css"></link>
	<meta http-equiv="refresh" content="{Interv}; URL=wvll.php">
	<script language="JavaScript">
	<!--
	function showW (id,art) {
		Frame=eval("top.main_window");
		if (art=="D") {
 			uri="wvl1.php?show=" + id;
 		} else if (art=="T") {
 			uri="termin.php";
 		} else if (art=="F") {
 			uri="wvl1.php?erp=" + id;
		} else {
			uri="wvl1.php?mail=" + id;
		}
		Frame.location.href=uri;
	}
	//-->
	</script>

<body topmargin="0" leftmargin="0"  marginwidth="0" marginheight="0">
<!-- Beginn Code ------------------------------------------->
<table class="liste" width="100%">
<!-- BEGIN Liste -->
	<tr  class="klein" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showW({ID},'{Art}');">
		<td nowrap >{Initdate}</td>
		<td width="10px" style="background-color:{Type};">{Status}</td>
		<td width="50%">{Cause}</td>
		<td width="30%">{IniUser}</td>
	</tr>
<!-- END Liste -->
</table>
<a href="wvll.php" class="klein">refresh</a>
<!-- Hier endet die Karte ------------------------------------------->
</body>
</html>
