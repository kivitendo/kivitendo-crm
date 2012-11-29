<html>
	<head><title></title>
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
	{STYLESHEETS}
	<script language="JavaScript">
	<!--
	function tag(tg) {
		self.location.href="termlist.php?cuid={CIUD}&ansicht=T&tag="+tg;
	}
	function kw(w) {
		self.location.href="termlist.php?cuid={CIUD}&ansicht=W&kw="+w;
	}
	//-->
	</script>
<body >
<center><br><br>
<table style="width:29em">
	<tr><th style="width:2.7em" class="gr">Kw</th><th style="width:4em" class="gr">Mo</th><th style="width:4em" class="gr">Di</th><th style="width:4em" class="gr">Mi</th><th style="width:4em" class="gr">Do</th><th style="width:4em" class="gr">Fr</th><th style="width:2.7em" class="gr">Sa</th><th style="width:2.7em" class="gr">So</th></tr>
<!-- BEGIN Woche -->
	<tr height="2em">
		<td class=" {col0} re" onClick="kw({KW})";>{KW}</td>
		<td class=" {col1} re" onClick="tag({TAG1})";>{TAG1}</td>
		<td class=" {col2} re" onClick="tag({TAG2})";>{TAG2}</td>
		<td class=" {col3} re" onClick="tag({TAG3})";>{TAG3}</td>
		<td class=" {col4} re" onClick="tag({TAG4})";>{TAG4}</td>
		<td class=" {col5} re" onClick="tag({TAG5})";>{TAG5}</td>
		<td class=" {col6} re" onClick="tag({TAG6})";>{TAG6}</td>
		<td class=" {col7} re" onClick="tag({TAG7})";>{TAG7}</td>
	</tr>
<!-- END Woche -->
</table>
</center>
</body>
</html>
