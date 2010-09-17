<!-- $Id$ -->
<html>
	<head><title></title>
    <link type="text/css" REL="stylesheet" HREF="../css/{ERPCSS}"></link>
    <link type="text/css" REL="stylesheet" HREF="css/{ERPCSS}"></link>
	<script language="JavaScript">
		function material() {
			//F1=open("getCall.php?Q=C&fid={FAD}","Caller","width=610, height=600, left=100, top=50, scrollbars=yes");
			f=open("artikel.php?aid={AID}&mid={mid}","artikel","width=625,height=490,left=10,top=10");
		}
		function drucke(nr)  {
			f=open("prtRAuftrag.php?aid="+nr,"drucke","width=10,height=10,left=10,top=10");
		}
	</script>
<body >

<table><tr><td>
<!-- Beginn Code ------------------------------------------->
<p class="listtop">Reparaturauftrag eingeben/editieren</p>
<form name="formular" enctype='multipart/form-data' action="{action}" method="post"">
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
		<td class="norm" width="*"><input type="text" size="9" name="datum" value="{datum}"><br>Bearbeitet am</td>		
	</tr>
	<tr>
		<td class="norm" colspan="2">
			<b>Maschine</b><br>
			{description}<br>
			{serialnumber}
		</td>
		<td class="norm">{msg}<br>
			<a href="maschine1.php?sernr={serialnumber}"><input name="back" type="button" value="abbruch"></a>
		</td>		
	</tr>
	<tr>
		<td class="norm">
			<input type="text" name="cause" value="{cause}" size="40" maxlength="75"><br>Kurzbeschreibung
		</td>
		<td class="norm">
			<input type="text" name="counter" value="{counter}" size="14" maxlength="15"><br>Z&auml;hlerstand
		</td>
		<td class="norm" rowspan="3" nowrap>
			<span  {disp1}><input type="radio" name="status" value="1" {sel1}>offen<br></span>
			<span  {disp2}><input type="radio" name="status" value="2" {sel2}>erledigt<br></span>
			<span  {disp3}><input type="radio" name="status" value="3" {sel3}>wieder offen<br><br></span>
			<input type="submit" name="ok" value="sichern"><br><br><br>
			<input type="button" name="prt" value="drucken" onCLick="drucke({AID})"><br><br>
			<input type="button" name="prt" value="Material" onCLick="material()"><br><br>
		</td>
	</tr>	
	<tr>
		<td class="norm" colspan="2"><textarea name="schaden" cols="80" rows="5">{schaden}</textarea><br>Schadensmeldung lang</td>
	</tr>
	<tr>
		<td class="norm" colspan="2"><textarea name="behebung" cols="80" rows="5">{behebung}</textarea><br>durchgef&uuml;hrte Reparatur (zuletzt: {bearbdate})</td>
	</tr>

<!-- BEGIN History -->		
	<tr>
		<td class="norm">{beschreibung}</td>	
		<td class="norm">{date} {art}</td>
		<td class="norm"></td>		
	</tr>
<!-- END History -->
</table>
</form>

<!-- End Code ------------------------------------------->
</td></tr></table>
</body>
</html>
