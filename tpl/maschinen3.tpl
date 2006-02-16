<!-- $Id: maschinen3.tpl,v 1.3 2005/11/02 10:38:59 hli Exp $ -->
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
		function getData() {
			nr=document.formular.bekannt.selectedIndex;
			val=document.formular.bekannt.options[nr].value;
			document.formular.parts_sernr.value=val;
			document.formular.submit();
		}
	//-->
	</script>
<body >

<table><tr><td class="ce">
<!-- Beginn Code ------------------------------------------->
<p class="listtop">Maschinen eingeben/editieren <b>{msg}</b></p>
<form name="formular" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="parts_id" value="{parts_id}">
<input type="hidden" name="mid" value="{mid}">
<input type="hidden" name="parts_sernr" value="{snumber}">
<table>
	<tr>
		<td class="norm" width="470px">
			<input type="text" name="partnumber" value="{partnumber}" maxlength="75"> <input type="submit" name="search" value="suchen">
			<br>Artikelnummer
		</td>
		<td class="norm le" rowspan="4" width="*">
			<select name="bekannt" size="10" style='width:220px;z-index: 1;' onDblClick="getData()">
<!-- BEGIN Bekannt -->		
				<option>{maschine}</option>>
<!-- END Bekannt -->
			</select><br>
			Bereits aufgenommene SerNr.
		</td>
	</tr>
	<tr>
		<td class="norm">{description}</td>
	</tr>
	<tr>
		<td class="norm">{notes}  </td>
	</tr>
	<tr>
		<td class="norm"><textarea cols="60" rows="4" name="beschreibung">{beschreibung}</textarea><br>Bemerkungen</td>
	</tr>	
	<tr>
		<td class="norm"><input type="text" name="snumber" size="40" maxlength="75" value="{snumber}" tabindex="6"><br>neue Seriennummer</td>
		<td class="norm"><input type="text" name="inspdatum" size="12" maxlength="10" value="{inspdatum}" tabindex="6"><br>n&auml;chstes Inspektionsdatum
	</tr>
	<tr>
		<td class="norm">
			<select name="serialnumber" tabindex="8" size="10" style='width:400px;z-index: 1;'>
<!-- BEGIN Sernumber -->
				<option value="{Snumber}">{Snumber}</option>
<!-- END Sernumber -->
			</select><br>bekannte freie Serienummern aus Verk&auml;ufen
		</td>
		<td class="norm">
			<br>
			<br><br><br>
			<input type="submit" name="ok" value="sichern">
		</td>
	</tr>

</table>
</form>

<!-- End Code ------------------------------------------->
</td></tr></table>
</body>
</html>
