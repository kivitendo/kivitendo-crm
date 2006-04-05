<!-- $ID: $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showlist(was) {
			self.location.href="termlist.php?ansicht="+was+"&datum={tag}"; //month="+mo+"&year="+ja+"&day="+tg;
	}
	function tag(tg) {
		self.location.href="termlist.php?ansicht=T&datum="+tg;
	}
	function womin() {
			self.location.href="termlist.php?ansicht=W&kw={kw1}&year={year1}";
	}
	function woplu() {
			self.location.href="termlist.php?ansicht=W&kw={kw2}&year={year2}";
	}
	function zeige(tid) {
		if (tid>0)
			f=open("showTermin.php?termid="+tid,"termine","width=400,height=300,left=300,top=150");
	}
	//-->
	</script>
<body >
<CENTER>
<input type="button" value="<--" onClick="womin()"> KW {kw} <input type="button" value="-->" onClick="woplu()"> <input type="button" value="Monat" onClick="showlist('M')">

<br><br>
<table style="width:350px" bgcolor="#ffffff">
<!-- BEGIN Woche -->
	<tr><td class="smal {col} re" width="70px" onClick="tag('{datum}')">{S1}</td><td class="smal" onClick="zeige({tid})">{S2}</td></tr>
<!-- END Woche -->
</table>
</center>
</body>
</html>
