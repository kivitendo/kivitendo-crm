<html>
	<head><title></title>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{THEME}
{JQDATE}
{JQTABLE}
{JAVASCRIPTS}
	<script language="JavaScript">
	<!--
		function suchMa() {
			val=document.formular.masch.value;
			f1=open("suchMa.php?masch="+val,"suche","width=350,height=200,left=100,top=100");
		}
		function delMa() {	
			nr=document.formular.maschinen.selectedIndex;
			document.formular.maschinen.options[nr]=null	
	}
	//-->
	</script>
	<script>
    $(function() {
        $( "#inspdatum" ).datepicker($.datepicker.regional[ "de" ]);
		$("#treffer")
			.tablesorter({widthFixed: true, widgets: ['zebra'], headers: { 1: { sorter: false }, 2: { sorter: false }} })
			.tablesorterPager({container: $("#pager"), size: 20});
	});
	</script>
    <script type='text/javascript' src='inc/help.js'></script>
<body >
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop" onClick="help('MaschinenEingebenEditieren');">Maschine info/edit (?)<b>{msg}</b></p>
<form name="formular" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="mid" value="{mid}">
<table cellpadding="2">
	<tr>
		<td class="norm">
			<input type="text" name="serialnumber" value="{serialnumber}" maxlength="75">
			<br>Seriennummer
		</td>
		<td class="norm">
			<input type="text" name="partnumber" value="{partnumber}" maxlength="15"> 
			<input type="submit" name="search" value="suchen"> &nbsp; &nbsp; &nbsp;<span {disp}>[<a href="repauftrag.php?mid={mid}">neuer Auftrag</a>]</span>
			<br>Artikelnummer
		</td>
	</tr>
</table>
<table>
	<tr>
		<td class="norm" width="40%"><b>Maschine:</b> {description}</td><td width="60%"></td>
	</tr>
	<tr>
		<td class="norm" colspan="2">{notes}</td>
	</tr>
	<tr>
		<td class="norm" colspan="2">{maschzusatz}<br><br></td>
	</tr>
	<tr>
		<td class="norm" colspan="2"><b>Standort: </b><input type="text" name="standort" value="{standort}" size="25">
			<input type="submit" name="ort" value="sichern"></td>
	</tr>	
	<tr>
		<td class="norm"><b>Insp. Datum: </b><input type="text" name="inspdatum" id="inspdatum" size="9" value="{inspdatum}"> <input type="submit" name="idat" value="sichern"></td>
		<td class="norm"><b>Z&auml;hler: </b><input type="text" name="counter" size="12" value="{counter}"> <input type="submit" name="cnt" value="sichern"></td>
	</tr>
	<tr>
		<td class="norm" colspan="2"><br><b>Vertragsnummer: </b><a href="vertrag3.php?vid={cid}">[{contractnumber}]</a>
			&nbsp;&nbsp;&nbsp;<b>Kunde: </b><a href="firma1.php?Q=C&id={custid}">[{customer}]</a></td>
	</tr>
	<tr>
		<td class="norm"><br><b>History</b></td>
		<td class="norm"></td>
	</tr>
</table>
<table id="treffer" class="tablesorter" style="width:40em;">  
    <thead>
		<tr>
			<th>Datum</th>
			<th>Art</th>
			<th></th>
			<th>Bemerkung</th>
		</tr>
	</thead>
	<tbody>
<!-- BEGIN History -->		
	<tr>
		<td class="norm">{date}</td>
		<td class="norm">{art}</td>
		<td class="norm">{open}</td>
		<td class="norm">{beschreibung}</td>		
	</tr>
<!-- END History -->
</tbody>
</table>
</form>
<span id="pager" class="pager">
	<form>
		<img src="{CRMPATH}jquery-plugins/Table/addons/pager/icons/first.png" class="first"/>
		<img src="{CRMPATH}jquery-plugins/Table/addons/pager/icons/prev.png" class="prev"/>
		<img src="{CRMPATH}jquery-plugins/Table/addons/pager/icons/next.png" class="next"/>
		<img src="{CRMPATH}jquery-plugins/Table/addons/pager/icons/last.png" class="last"/>
		<select class="pagesize" id='pagesize'>
			<option value="10">10</option>
			<option value="20" selected>20</option>
			<option value="30">30</option>
			<option value="40">40</option>
		</select>
	</form>
</span>
{END_CONTENT}
</body>
</html>
