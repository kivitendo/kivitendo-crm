<!-- $ID: $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function tag(tg) {
		self.location.href="termlist.php?ansicht=T&tag="+tg;
	}
	function kw(w) {
		self.location.href="termlist.php?ansicht=W&kw="+w;
	}
	//-->
	</script>
<body >
<center><br><br>
<table style="width:350px">
	<tr><th width="30px" class="gr">Kw</th><th width="50px" class="gr">Mo</th><th width="50px" class="gr">Di</th><th width="50px" class="gr">Mi</th><th width="50px" class="gr">Do</th><th width="50px" class="gr">Fr</th><th width="30px" class="gr">Sa</th><th width="30px" class="gr">So</th></tr>
<!-- BEGIN Woche -->
	<tr height="50px">
		<td class="smal {col0} re" onClick="kw({KW})";>{KW}</td>
		<td class="smal {col1} re" onClick="tag({TAG1})";>{TAG1}</td>
		<td class="smal {col2} re" onClick="tag({TAG2})";>{TAG2}</td>
		<td class="smal {col3} re" onClick="tag({TAG3})";>{TAG3}</td>
		<td class="smal {col4} re" onClick="tag({TAG4})";>{TAG4}</td>
		<td class="smal {col5} re" onClick="tag({TAG5})";>{TAG5}</td>
		<td class="smal {col6} re" onClick="tag({TAG6})";>{TAG6}</td>
		<td class="smal {col7} re" onClick="tag({TAG7})";>{TAG7}</td>
	</tr>
<!-- END Woche -->
</table>
</center>
</body>
</html>
