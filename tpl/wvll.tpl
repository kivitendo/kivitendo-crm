<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<meta http-equiv="refresh" content="{Interv}; URL=wvll.php">
	<script language="JavaScript">
	<!--
	function showW (id,art) {
		Frame=eval("top.main_window");
		if (art=="D") {
 			uri="wvl1.php?show=" + id;
 		} else if (art=="T") {
 			uri="termin.php";
		} else {
			uri="wvl1.php?mail=" + id;
		}
		Frame.location.href=uri;
	}
	//-->
	</script>

<body topmargin="0" leftmargin="0"  marginwidth="0" marginheight="0">
<!--table width="103%" class="karte" border="0"><tr><td class="karte" -->
<!-- Beginn Code ------------------------------------------->
<table class="liste" width="100%">
<!-- BEGIN Liste -->
	<tr  class="smal" onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showW({ID},'{Art}');">
		<td nowrap width="30%">{Initdate}</td>
		<td width="10px" style="background-color:{Type};">{Status}</td>
		<td width="*">{Cause}</td>
		<td width="20%">{IniUser}</td>
	</tr>
<!-- END Liste -->
</table>
<a href="wvll.php">refresh</a>
<!-- Hier endet die Karte ------------------------------------------->
<!--/td></tr></table-->
</body>
</html>
