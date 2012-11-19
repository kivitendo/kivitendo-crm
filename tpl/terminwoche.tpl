<!-- $ID: $ -->
<html>
	<head><title></title>
	{STYLESHEETS}
	<link type="text/css" REL="stylesheet" HREF="css/{ERPCSS}"></link>
	<script language="JavaScript">
	<!--
	function showlist(was) {
			self.location.href="termlist.php?cuid={CUID}&ansicht="+was+"&datum={tag}"; //month="+mo+"&year="+ja+"&day="+tg;
	}
	function tag(tg) {
		self.location.href="termlist.php?cuid={CUID}&ansicht=T&datum="+tg;
	}
	function womin() {
			self.location.href="termlist.php?cuid={CUID}&ansicht=W&kw={kw1}&year={year1}";
	}
	function woplu() {
			self.location.href="termlist.php?cuid={CUID}&ansicht=W&kw={kw2}&year={year2}";
	}
	function zeige(tid) {
		if (tid>0)
			f=open("showTermin.php?termid="+tid,"termine","width=400,height=300,left=300,top=150");
	}
	//-->
	</script>
<body >
<CENTER>
<input type="button" value="<--" onClick="womin()"> [<a href="prtwkal.php?kw={kw}&year={year}">KW {kw}</a>] <input type="button" value="-->" onClick="woplu()"> <input type="button" value="Monat" onClick="showlist('M')"> 

<br><br>
<table style="width:29em" bgcolor="#ffffff" class="klein">
<!-- BEGIN Woche -->
	<tr><td class="{col} re"  style="width:4.8em" onClick="tag('{datum}')">{S1}</td><td style="width:24em" onClick="zeige({tid})">{S2}</td></tr>
<!-- END Woche -->
</table>
</center>
</body>
</html>
