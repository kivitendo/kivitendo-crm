<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<body onLoad="document.formular.cp_greeting.focus();">
<p class="listtop">Personensuche</p>

<form name="formular" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="FID1" value="{FID1}">
<input type="hidden" name="Quelle" value="{Quelle}">
<input type="hidden" name="employee" value="{employee}">

<span style="float:left; top:47px; border: 0px solid black;">
<!-- Beginn Code ------------------------------------------->
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
	<div class="zeile">
		<span class="label">Anrede</span>
		<span class="leftfeld">
			<input type="radio" name="greeting" value="H" {cpsel1}>Herr 
			<input type="radio" name="greeting" value="F" {cpsel2}>Frau    
			<input type="radio" name="greeting" value="O" {cpsel3}>
				<input type="text" name="cp_greeting" size="7" maxlength="75" value="{cp_greeting}" tabindex="1">
		</span>
		<span class="label">Abteilung</span>
		<span class="leftfeld"><input type="text" name="cp_abteilung" size="20" maxlength="25" value="{cp_abteilung}" tabindex="12"></span>
	</div>
	<div class="zeile">
		<span class="label">Titel</span>
		<span class="leftfeld"><input type="text" name="cp_title" size="27" maxlength="75" value="{cp_title}" tabindex="2"></span>
		<span class="label">Position</span>
		<span class="leftfeld"><input type="text" name="cp_position" size="20" maxlength="25" value="{cp_position}" tabindex="13"></span>
	</div>
	<div class="zeile">
		<span class="label">Vorname</span>
		<span class="leftfeld"><input type="text" name="cp_givenname" size="27" maxlength="75" value="{cp_givenname}" tabindex="3"></span>
		<span class="label">Stichwort</span>
		<span class="leftfeld"><input type="text" name="cp_stichwort1" size="25" maxlength="50" value="{cp_stichwort1}" tabindex="14"></span>
	</div>
	<div class="zeile">
		<span class="label">Nachname</span>
		<span class="leftfeld"><input type="text" name="cp_name" size="27" maxlength="75" value="{cp_name}" tabindex="4"></span>
		<span class="label">Bemerkung</span>
		<span class="leftfeld"><input type="text" name="cp_notes" size="25" maxlength="50" value="{cp_notes}" tabindex="15"></span>
	</div>
	<div class="zeile">
		<span class="label">Strasse</span>
		<span class="leftfeld"><input type="text" name="cp_street" size="27" maxlength="75" value="{cp_street}" tabindex="5"></span>
		<span class="label">Geb.-Datum</span>
		<span class="leftfeld"><input type="text" name="cp_gebdatum" size="12" maxlength="10" value="{cp_gebdatum}" tabindex="16"> TT.MM.JJJJ</span>
	</div>
	<div class="zeile">
		<span class="label">Land / Plz</span>
		<span class="leftfeld">
			<input type="text" name="cp_country" size="2" maxlength="3" value="{cp_country}" tabindex="6">
			<input type="text" name="cp_zipcode" size="7" maxlength="7" value="{cp_zipcode}" tabindex="7">
		</span>
	</div>
	<div class="zeile">
		<span class="label">Ort</span>
		<span class="leftfeld"><input type="text" name="cp_city" size="27" maxlength="75" value="{cp_city}" tabindex="8"></span>
		<span class="label">Fax</span>
		<span class="leftfeld"><input type="text" name="cp_fax" size="27" maxlength="75" value="{cp_fax}" tabindex="17"></span>
	</div>
	<div class="zeile">
		<span class="label">Telefon</span>
		<span class="leftfeld"><input type="text" name="cp_phone1" size="27" maxlength="75" value="{cp_phone1}" tabindex="9"></span>
		<span class="label">eMail</span>
		<span class="leftfeld"><input type="text" name="cp_email" size="27" maxlength="75" value="{cp_email}" tabindex="18"></span>
	</div>
	<div class="zeile">
		<span class="label">Mobiltelefon</span>
		<span class="leftfeld"><input type="text" name="cp_phone2" size="27" maxlength="75" value="{cp_phone2}" tabindex="10"></span>
		<span class="label">www</span>
		<span class="leftfeld"><input type="text" name="cp_homepage" size="27" maxlength="25" value="{cp_homepage}" tabindex="19"></span>
	</div>
	<div class="zeile">
		<input type="checkbox" name="fuzzy" value="%" checked>Unscharf suchen&nbsp;&nbsp;<b>{Msg}</b>  <br>
		{Btn1} {Btn3} <input type="submit" name="suche" value="suchen"> <input type="submit" name="reset" value="clear">
	</div>
</span>
<span style="float:left;   top:47px; border: 0px solid black;">
	<br><br><br><br>
<!-- BEGIN sonder -->
	<input type="checkbox" name="cp_sonder[]" value="{sonder_id}">{sonder_name}<br>
<!-- END sonder -->			
</form>
<!-- End Code ------------------------------------------->
</span>
</body>
</html>

