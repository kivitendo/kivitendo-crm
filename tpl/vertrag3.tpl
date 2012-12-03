<html>
	<head><title></title>
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
	{STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
        {JAVASCRIPTS}
	<script language="JavaScript">
	<!--

		function suchFa() {
			val=document.formular.name.value;
			f1=open("suchFa.php?tab=C&nq=1&name="+val,"suche","width=350,height=200,left=100,top=100");
		}
		function suchMa() {
			val=document.formular.masch.value;
			f1=open("suchMa.php?masch="+val,"suche","width=350,height=200,left=100,top=100");
		}
	
	//-->
	</script>
<body >
{PRE_CONTENT}
{START_CONTENT}
<table width="99%" border="0"><tr><td>
<!-- Beginn Code ------------------------------------------->
<p class="listtop">Wartungsvertr&auml;ge eingeben/editieren</p>
<form name="formular" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="Quelle" value="">
<table>
	
	<tr>
		<td class="norm" width="40%">
			<select name="vorlage"  tabindex="1" style='width:300px;z-index: 1;'>
<!-- BEGIN Vorlage -->
				<option value="{Vertrag}" {Vsel}>{Vertrag}</option>
<!-- END Vorlage -->
			</select>
			<br>Vertragsvorlage<br><br>
		</td>
		<td class="norm" width="60%"></td>
	</tr>
	<tr>
		<td class="norm" colspan="2"><textarea name="bemerkung" cols="80" rows="3" tabindex="2">{Notiz}</textarea><br>Bemerkungen<br><br></td>
	</tr>
	<tr>
		<td class="norm"><input type="text" name="name" size="30" maxlength="75" value="{Firma}"> <input type="button" name="fa" value="suchen" onClick="suchFa();"  tabindex="4"> <br>Firma<br><br></td>
		<td class="norm"><input type="hidden" name="cp_cv_id" value="{FID}">
			<input type="text" name="anfangdatum" size="10" maxlength="10" value="{anfangdatum}" tabindex="6">&nbsp; <input type="text" name="endedatum" size="10" maxlength="10" value="{endedatum}" tabindex="6"><br>
			<b>Vertragsdatum von &nbsp; bis</b></td>
	</tr>
	<tr>
		<td class="norm"><input type="text" name="masch" size="30" maxlength="15" value="" tabindex="6"> <input type="button" name="ma" value="suchen" onClick="suchMa();"  tabindex="7"><br>ArtNr. Maschine<br><br></td>
		<td class="norm"><input type="text" name="betrag" size="10" maxlength="12" value="{betrag}" align="right" tabindex="6">&euro;<br>Betrag </td>
	</tr>	
	<tr>
		<td class="norm"><input id="neuid" type="hidden" name="maschinen[0][0]" value=""><input id="neuname" type="text" name="maschinen[0][1]" size="30" value="" tabindex="8"><br>neue Maschine<br><br></td>
		<td class="norm"><input type="text" name="maschinen[0][2]" size="30" value="" tabindex="9"><br>Standort</td>		
	</tr>	
<!-- BEGIN Maschinen -->		
	<tr>
		<td class="norm"><input type="hidden" name="maschinen[{I}][0]" value="{MID}"><input type="text" name="maschinen[{I}][1]" size="30" maxlength="15" value="{Maschine}" tabindex="8"><br>Maschine</td>
		<td class="norm"><input type="text" name="maschinen[{I}][2]" size="30" maxlength="15" value="{Standort}" tabindex="9">
			<input type="checkbox" name="maschinen[{I}][3]" value="1"> <a href="maschine1.php?sernr={SerNr}">[mehr]</a><br>Standort</td>		
	</tr>
<!-- END Maschinen -->
	<tr>
		<td class="norm">
			<input type="button" name="prt" value="drucken" onCLick="drucke({VertragNr})">
		</td>
		<td class="norm">
			<input type="submit" name="ok" value="WV sichern">
		</td>
	</tr>

</table>
</form>

<!-- End Code ------------------------------------------->
</td></tr></table>
{END_CONTENT}
</body>
</html>
