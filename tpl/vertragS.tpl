<html>
	<head><title></title>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
        {STYLESHEETS}
        {JAVASCRIPTS}
	<script language="JavaScript">
	<!--
		function drucke(nr)  {
			f=open("prtWVertrag.php?aid="+nr,"drucke","width=10,height=10,left=10,top=10");
		}	
	//-->
	</script>
<body >
{PRE_CONTENT}
{START_CONTENT}
<table width="99%" border="0"><tr><td>
<!-- Beginn Code ------------------------------------------->
<p class="listtop">Vertr&auml;ge auswerten</p>
<form name="formular" enctype='multipart/form-data' action="{action}" method="post"">
<input type="hidden" name="vid" value="{VID}">
Auswertung {jahr} f&uuml;r Vertrag [<a href="vertrag3.php?vid={VID}">{VertragNr}</a>]  [<a href="firma1.php?id={FID}">{Firma}</a>]
<table style="width:550px">
	<tr><th class="norm" width="200">Maschine</th><th class="norm">Auftrag</th><th class="norm">Summe</th><th class="norm">Gesamtsumme</th></tr>
<!-- BEGIN Liste -->
	<tr><td class="norm" nowrap>{MID}</td><td width="100" class="norm ce">{RID}</td><td width="100" class="norm re">{BETRAG}</td><td width="100" class="norm re">{SUMME}</td></tr>
<!-- END Liste -->

</table>
<dev  class="norm">
Errechnete (nicht tats&auml;chliche) Einnahmen: {einnahme}<br>
Aufgelaufene Kosten: {kosten} &nbsp;&nbsp;&nbsp; <b>{diff} &euro;</b>
</div>
</form>
<!-- End Code ------------------------------------------->
</td></tr></table>
{END_CONTENT}
</body>
</html>
