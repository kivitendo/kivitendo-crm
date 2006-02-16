<!-- $Id: maschinen1.tpl,v 1.3 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
		function suchFa() {
			val=document.formular.name.value;
			f1=open("suchFa.php?nq=1&name="+val,"suche","width=350,height=200,left=100,top=100");
		}
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
<body >

<table width="99%" border="0"><tr><td>
<!-- Beginn Code ------------------------------------------->
<p class="listtop">Maschine info/edit <b>{msg}</b></p>
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
			<input type="submit" name="search" value="suchen"> &nbsp; &nbsp; &nbsp;<span {disp}><a href="repauftrag.php?mid={mid}"><button>neuer Auftrag</button></a></span>
			<br>Artikelnummer
		</td>
	</tr>
</table>
<table>
	<tr>
		<td class="norm" width="40%"><b>Maschine</b></td><td width="60%"></td>
	</tr>
	<tr>
		<td class="norm" colspan="2">{description}</td>
	</tr>
	<tr>
		<td class="norm" colspan="2">{notes} <br><br></td>
	</tr>
	<tr>
		<td class="norm" colspan="2"><b>Standort: </b><input type="text" name="standort" value="{standort}" size="25">
			<input type="submit" name="ort" value="sichern"></td>
	</tr>	
	<tr>
		<td class="norm">Insp. Datum: <input type="text" name="inspdatum" size="10" value="{inspdatum}"> <input type="submit" name="idat" value="sichern"></td>
		<td class="norm">Z&auml;hler: <input type="text" name="counter" size="12" value="{counter}"> <input type="submit" name="cnt" value="sichern"></td>
	</tr>
	<tr>
		<td class="norm" colspan="2"><br><b>Vertragsnummer: </b><a href="vertrag3.php?vid={cid}">[{contractnumber}]</a>
			&nbsp;&nbsp;&nbsp;<a href="firma1.php?id={custid}">[{customer}]</a></td>
	</tr>
	<tr>
		<td class="norm"><br><b>History</b></td>
		<td class="norm"></td>
	</tr>
<!-- BEGIN History -->		
	<tr>
		<td class="norm">{date} {art}</td>
		<td class="norm">{beschreibung}</td>		
	</tr>
<!-- END History -->
</table>
</form>

<!-- End Code ------------------------------------------->
</td></tr></table>
</body>
</html>
