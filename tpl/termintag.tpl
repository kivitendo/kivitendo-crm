<!-- $ID: $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>

	<script language="JavaScript">
	<!--
	function showlist(was) {
			self.location.href="termlist.php?ansicht="+was+"&datum={tag}"; //month="+mo+"&year="+ja+"&day="+tg;
	}
	function tamin() {
			self.location.href="termlist.php?ansicht=T&datum={dat1}";
	}
	function taplu() {
			self.location.href="termlist.php?ansicht=T&datum={dat2}";
	}
	function zeige(tid) {
		if (tid>0)
			f=open("showTermin.php?termid="+tid,"termine","width=400,height=300,left=300,top=150");
	}
	function fill(zeit) {
		for (i = 0; i < top.main_window.document.termedit.von.length; i++)
			if (top.main_window.document.termedit.von.options[i].value==zeit)
				top.main_window.document.termedit.von.options[i].selected=true
		top.main_window.document.termedit.vondat.value="{tag}";
	}
	//-->
	</script>
<body >
<center>
<input type="button" value="Woche" onClick="showlist('W')">
<input type="button" value="<--" onClick="tamin()"> [<a href="prttkal.php?day={day}&month={month}&year={year}">{tag}</a>] <input type="button" value="-->" onClick="taplu()">
<input type="button" value="Monat" onClick="showlist('M')">
<table bgcolor="#ffffff" style="width:29em;" class="klein">
<!-- BEGIN Stunden -->
	<tr><td class="{col} re" style="width:3em;" onClick="fill('{zeit}');">{zeit}</td><td style="width:25em;">{text}</td></tr>
<!-- END Stunden -->
</table>
</center>
</body>
</html>
