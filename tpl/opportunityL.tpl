<html>
	<head><title></title>
	{STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
        <script type="text/javascript" src="{JQUERY}jquery-ui/jquery.js"></script>
        {JAVASCRIPTS}
	<script language="JavaScript">
	<!--
		function showO(id) {
			self.location="opportunity.php?id="+id
		}
	//-->
	</script>
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:opportunity:.</p>
<span style="position:absolute; left:10px; top:47px; width:95%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<table>
	<tr >
		<td class="norm">.:company:.&nbsp;</td>
		<td class="norm">&nbsp;.:order:.</td>
		<td class="norm" style="width:20;text-align:right">%</td>
		<td class="norm" style="width:80;text-align:center">&euro;</td>
		<td>.:status:.</td>
		<td class="norm">&nbsp;.:targetdate:.</td>
		<td class="norm">&nbsp;.:employee:.</td>
		<td>.:changed:.</td>
	</tr>
<!-- BEGIN Liste -->
	<tr  class="norm bgcol{LineCol}" onClick="showO({id});" colspan="0">
		<td >{firma}&nbsp;</td><td>&nbsp;{title}</td><td style="width:20;text-align:right">{chance}</td>
		<td  style="width:80;text-align:right"> {betrag}</td><td>&nbsp;{status}</td>
		<td  style="width:60;text-align:right"> {datum}</td><td>&nbsp;{user}</td><td>&nbsp;{chgdate}</td>
        <!--td><a href="opportunity.php?history={oppid}"><img src="image/extra.png" border="0" style="visibility:{show}"></a></td-->
</tr>
<!-- END Liste -->
</table>
<!-- Hier endet die Karte ------------------------------------------->
</span>
{END_CONTENT}
</body>
</html>
