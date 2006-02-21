<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<body>


<table width="99%" border="0"><tr><td class="norm">
<!-- Beginn Code ------------------------------------------->
<p class="listtop">Personensuche</p>
<p class="listheading">| 
<a href="{action}?first=A" class="bold">A</a> |
<a href="{action}?first=B" class="bold">B</a> |
<a href="{action}?first=C" class="bold">C</a> |
<a href="{action}?first=D" class="bold">D</a> |
<a href="{action}?first=E" class="bold">E</a> |
<a href="{action}?first=F" class="bold">F</a> |
<a href="{action}?first=G" class="bold">G</a> |
<a href="{action}?first=H" class="bold">H</a> |
<a href="{action}?first=I" class="bold">I</a> |
<a href="{action}?first=J" class="bold">J</a> |
<a href="{action}?first=K" class="bold">K</a> |
<a href="{action}?first=L" class="bold">L</a> |
<a href="{action}?first=M" class="bold">M</a> |
<a href="{action}?first=N" class="bold">N</a> |
<a href="{action}?first=O" class="bold">O</a> |
<a href="{action}?first=P" class="bold">P</a> |
<a href="{action}?first=Q" class="bold">Q</a> |
<a href="{action}?first=R" class="bold">R</a> |
<a href="{action}?first=S" class="bold">S</a> |
<a href="{action}?first=T" class="bold">T</a> |
<a href="{action}?first=U" class="bold">U</a> |
<a href="{action}?first=V" class="bold">V</a> |
<a href="{action}?first=W" class="bold">W</a> |
<a href="{action}?first=X" class="bold">X</a> |
<a href="{action}?first=Y" class="bold">Y</a> |
<a href="{action}?first=Z" class="bold">Z</a> |</p>

<form name="formular" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="FID1" value="{FID1}">
<input type="hidden" name="Quelle" value="{Quelle}">
<input type="hidden" name="employee" value="{employee}">
<table cellpadding="2" border="0">
	<tr>
		<td class="smal"><input type="radio" name="greeting" value="H" {cpsel1}>Herr <input type="radio" name="greeting" value="F" {cpsel2}>Frau    
			<input type="radio" name="greeting" value="O" {cpsel3}><input type="text" name="cp_greeting" size="10" maxlength="75" value="{Anrede}" tabindex="1"><br>Anrede</td>
		<td class="smal" colspan="2"><input type="text" name="cp_title" size="30" maxlength="75" value="{Titel}" tabindex="2"><br>Titel</td>
		<td class="smal"><input type="text" name="cp_abteilg" size="20" maxlength="25" value="{Abteilg}" tabindex="15"><br>Abteilung</td>
	</tr>
	<tr>
		<td class="smal"><input type="text" name="cp_givenname" size="25" maxlength="75" value="{Vname}" tabindex="3"><br>Vorname</td>
		<td class="smal" colspan="2"><input type="text" name="cp_name" size="30" maxlength="75" value="{Nname}" tabindex="4"><br>Nachname</td>
		<td class="smal"><input type="text" name="cp_position" size="20" maxlength="25" value="{Position}" tabindex="16"><br>Position</td>
	</tr>

	<tr>
		<td class="smal" colspan="2"><input type="text" name="cp_street" size="42" maxlength="75" value="{Strasse}" tabindex="5"><br>Strasse</td>
		<td class="smal">&nbsp;</td>
		<td class="smal"><b>{Msg}</b>&nbsp;</td>
	</tr>
	<tr>
		<td class="smal" colspan="3">
			<select name="cp_country" tabindex="20">
<!-- BEGIN LandListe -->
				<option value="{LandID}" {Lsel}>{Land}</option>
<!-- END LandListe -->
			</select>
			<input type="text" name="cp_zipcode" size="6" maxlength="10" value="{Plz}" tabindex="6">
			<input type="text" name="cp_city" size="30" maxlength="75" value="{Ort}" tabindex="7">
			<br>Land &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Postleitzahl Ort</td>
		<td class="smal" rowspan="4">
<!-- BEGIN sonder -->
				<input type="checkbox" name="cp_sonder[]" value="{sonder_id}">{sonder_name}<br>
<!-- END sonder -->			
		</td>
	</tr>
	<tr>
		<td class="smal"><input type="text" name="cp_phone1" size="25" maxlength="75" value="{Tel1}" tabindex="8"><br>Telefon</td>
		<td class="smal"><input type="text" name="cp_fax" size="25"  maxlength="75"value="{Fax}" tabindex="10"><br>Fax</td>
		<td class="smal" rowspan="5">&nbsp;</td>
	</tr>
	<tr>
		<td class="smal" colspan="2"><input type="text" name="cp_email" size="40" maxlength="125" value="{eMail}" tabindex="11"><br>eMail</td>
	</tr>
	<tr>
		<td class="smal" colspan="2"><input type="text" name="cp_homepage" size="40" maxlength="125" value="{Homepage}" tabindex="12"><br>Homepage</td>
	</tr>
	<tr>
		<td class="smal"><input type="text" name="cp_stichwort1" size="25" maxlength="50" value="{SW1}" tabindex="18"><br>Stichwort</td>
		<td class="smal"><input type="text" name="cp_gebdatum" size="12" maxlength="10" value="{GDate}" tabindex="22"><br>Geburtsdatum</td>
		<td class="smal" rowspan="2">
			<input type="checkbox" name="fuzzy" value="%" checked>Unscharf suchen<br>
			{Btn1}<br><br>{Btn3}<br><br><input type="submit" name="suche" value="suchen"><br><br><input type="submit" name="reset" value="clear">
		</td>
	</tr>
	<tr>
		<td class="smal" colspan="2"><textarea name="cp_notes" cols="66" rows="2" tabindex="17">{Notiz}</textarea><br>Bemerkungen</td>
	</tr>
</table>
</form>

			<!-- End Code ------------------------------------------->
</td></tr></table>
</body>
</html>

