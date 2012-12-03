<html>
	<head><title></title>
	{STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
        {JAVASCRIPTS}
	<script language="JavaScript">
	<!--
	function showD (id) {
		if (id>0) {
			uri="dokument2.php?did=" + id;
			window.location.href=uri;
		}
	}
	//-->
	</script>
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">Dokumentvorlagen</p>
<table class="reiter">
	<tr>
		<td class="reiter sel">
			<a href="{Link1}" class="reiterA">Dokumente</a>
		</td>
		<td class="reiter desel">
			<a href="{Link2}" >neue Vorlage</a>
		</td>
		<td class="reiter desel">
			<a href="{Link3}" ></a>
		</td>
		<td class="reiter desel">
			<a href="{Link4}"></a>
		</td>
	</tr>
</table>

<!--table width="95%" class="karte"><tr><td class="karte"-->
<!-- Hier beginnt die Karte  ------------------------------------------->
<br>
<form name="firma4" action="{action}" method="post">
<table class="liste" style="width:300px">
<!-- BEGIN Liste -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showD({did});">
		<td class="norm">{Bezeichnung}</td><td class="norm">{Appl}</td>
	</tr>
<!-- END Liste -->
</table>
<!-- Hier endet die Karte ------------------------------------------->
<!--/td></tr></table-->
{END_CONTENT}
</body>
</html>
