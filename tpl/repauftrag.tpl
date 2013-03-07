<html>
	<head><title></title>
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{THEME}
{JQDATE}
{JQTABLE}
{JAVASCRIPTS}

	<script language="JavaScript">
		function material() {
			f=open("artikel.php?aid={AID}&mid={mid}","artikel","width=625,height=490,left=10,top=10");
		}
		function drucke(nr)  {
			f=open("prtRAuftrag.php?aid="+nr,"drucke","width=10,height=10,left=10,top=10");
		}
	</script>
	<script>
    $(function() {
        $( "#datum" ).datepicker($.datepicker.regional[ "de" ]);
  		$("#treffer")
			.tablesorter({widthFixed: true, widgets: ['zebra'], headers: { 0: { sorter: false }, 2: { sorter: false }} })
    });
    $(document).ready( function () {
        $('#cause').focus();
    })
	</script>
<body >
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">Reparaturauftrag eingeben/editieren</p>
<form name="formular" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="mid" value="{mid}">
<input type="hidden" name="kdnr" value="{kdnr}">
<input type="hidden" name="aid" value="{AID}">
<table style="width:80%">
	<tr>
		<td class="norm">
			<b>Kunde</b><br>
			{name}<br>
			{strasse}<br>
			{plz} {ort}<br>
		</td>
		<td class="norm re">
			Tel.: {telefon}<br>
			Firma: [<a href="firma1.php?Q=C&id={kdnr}">{customernumber}</a>]<br>
			Vertrag: [<a href="vertrag3.php?vid={cid}">{contractnumber}</a>]
		</td>
		<td class="norm"><b>{AID}</b></td>
	</tr>
	<tr>
		<td class="norm" width="40%">
			<b>Standort</b><br>
			{standort}
		</td>
		<td class="norm re"  width="25%">{anlagedatum}</td>
		<td class="norm">{msg}<br>
			<a href="maschine1.php?sernr={serialnumber}"><input name="back" type="button" value="abbruch"></a>
		</td>		
	</tr>
	<tr>
		<td class="norm" colspan="2">
			<b>Maschine</b><br>
			{description}<br>
			{serialnumber}
		</td>
        <td></td>
	</tr>
	<tr>
		<td class="norm">
			<input type="text" name="cause" id='cause' value="{cause}" size="40" tabindex='1' maxlength="75"><br>Kurzbeschreibung
		</td>
		<td class="norm">
			<input type="text" name="counter" value="{counter}" size="14" tabindex='2' maxlength="15"><br>Z&auml;hlerstand
		</td>
		<td class="norm" width="*"><input type="text" size="9" name="datum" id="datum" tabindex='3' value="{datum}"><br>Bearbeitet am</td>		
	</tr>	
	<tr>
		<td class="norm" colspan="2"><textarea name="schaden" tabindex='4' cols="80" rows="5">{schaden}</textarea><br>Schadensmeldung lang</td>
		<td class="norm" rowspan="2" nowrap>
			<span  {disp1}><input type="radio" name="status" value="1" {sel1}>offen<br></span>
			<span  {disp2}><input type="radio" name="status" value="2" {sel2}>erledigt<br></span>
			<span  {disp3}><input type="radio" name="status" value="3" {sel3}>wieder offen<br><br></span>
			<input type="submit" name="ok" value="sichern"><br><br><br>
			<input type="button" name="prt" value="drucken" onCLick="drucke({AID})"><br><br>
			<input type="button" name="prt" value="Material" onCLick="material()"><br><br>
		</td>
	</tr>
	<tr>
		<td class="norm" colspan="2"><textarea name="behebung" tabindex='5' cols="80" rows="5">{behebung}</textarea><br>durchgef&uuml;hrte Reparatur (zuletzt: {bearbdate})</td>
	</tr>
</table>
</form>
<table id="treffer" class="tablesorter" style="width:40em;">  
    <thead>
		<tr>
			<th>Bemerkung</th>
			<th>Datum</th>
			<th>Art</th>
		</tr>
	</thead>
	<tbody>
<!-- BEGIN History -->		
	<tr>
		<td class="norm">{beschreibung}</td>	
		<td class="norm">{date}</td>
        <td class="norm">{art} {open}</td>
	</tr>
<!-- END History -->
</tbody>
</table>


{END_CONTENT}
</body>
</html>
