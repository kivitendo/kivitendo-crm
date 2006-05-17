<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript" type="text/javascript">
  		function report() {
  			f1=open("report.php?tab=vendor","Report","width=600; height=500; left=100; top=100");
  		}
	</script>
<body onLoad="document.erwsuche.name.focus();">

<form name="erwsuche" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="felder" value="">
<p class="listtop">Lieferantenensuche</p>
<span style="position:absolute; left:10px; top:47px; border: 0px solid black;width:99%;">
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
<a href="{action}?first=Z" class="bold">Z</a> |
<a href="{action}?first=~" class="bold">*</a> |</p>
	<div class="zeile">
		<span class="label">Firmenname</span>
		<span class="leftfeld"><input type="text" name="name" size="27" maxlength="75" value="{name}" tabindex="1"></span>
		<span class="label">Branche</span>
		<span class="leftfeld"><input type="text" name="branche" size="27" maxlength="25" value="{branche}" tabindex="13"></span>
	</div>
	<div class="zeile">
		<span class="label">Abteilung</span>
		<span class="leftfeld"><input type="text" name="department_1" size="27" maxlength="75" value="{department_1}" tabindex="2"></span>
		<span class="label">Stichwort</span>
		<span class="leftfeld"><input type="text" name="sw" size="27" maxlength="125" value="{sw}" tabindex="14"></span>
	</div>
	<div class="zeile">
		<span class="label">Strasse</span>
		<span class="leftfeld"><input type="text" name="street" size="27" maxlength="75" value="{street}" tabindex="3"></span>
		<span class="label">Bemerkung</span>
		<span class="leftfeld"><input type="text" name="notes" size="27" maxlength="125" value="{notes}" tabindex="15"></span>
	</div>
	<div class="zeile">
		<span class="label">Land / Plz</span>
		<span class="leftfeld"><input type="text" name="country" size="2" maxlength="5" value="{country}" tabindex="4"> / 
					<input type="text" name="zipcode" size="7" maxlength="7" value="{zipcode}" tabindex="5"></span>
		<span class="label">Bank</span>
		<span class="leftfeld"><input type="text" name="bank" size="27" maxlength="50" value="{bank}" tabindex="16"></span>
	</div>
	<div class="zeile">
		<span class="label">Ort</span>
		<span class="leftfeld"><input type="text" name="city" size="27" maxlength="75" value="{city}" tabindex="6"></span>
		<span class="label">Blz</span>
		<span class="leftfeld"><input type="text" name="bank_code" size="27" maxlength="15" value="{bank_code}" tabindex="17"></span>
	</div>
	<div class="zeile">
		<span class="label">Telefon</span>
		<span class="leftfeld"><input type="text" name="phone" size="27" maxlength="75" value="{phone}" tabindex="7"></span>
		<span class="label">Konto</span>
		<span class="leftfeld"><input type="text" name="account_number" size="27" maxlength="15" value="{account_number}" tabindex="18"></span>
	</div>
	<div class="zeile">
		<span class="label">Fax</span>
		<span class="leftfeld"><input type="text" name="fax" size="27" maxlength="125" value="{fax}" tabindex="8"></span>
		<span class="label">UStID</span>
		<span class="leftfeld"><input type="text" name="ustid" size="27" maxlength="12" value="{ustid}" tabindex="19"></span>
	</div>
	<div class="zeile">
		<span class="label">eMail</span>
		<span class="leftfeld"><input type="text" name="email" size="27" maxlength="125" value="{email}" tabindex="9"></span>
		<span class="label">Steuernr.</span>
		<span class="leftfeld"><input type="text" name="taxnumber" size="27" maxlength="12" value="{taxnumber}" tabindex="20"></span>
	</div>
	<div class="zeile">
		<span class="label">Unsere KdNr</span>
		<span class="leftfeld"><input type="text" name="kundennummer" size="27" maxlength="125" value="{kundennummer}" tabindex="10"></span>
	</div>
	<div class="zeile">
		<span class="label">Typ</span>
		<span class="leftfeld">
			<select name="business_id" tabindex="11">
<!-- BEGIN TypListe -->	
				<option value="{Bid}" {Bsel}>{Btype}</option>
<!-- END TypListe -->				
			</select>
		</span>
		<span class="label">Sprache</span>
		<span class="leftfeld">	<select name="language" tabindex="21">
				<option value="">
				<option value="de">deutsch
				<option value="en">englisch
				<option value="fr">franz&ouml;sisch
			</select>
		</span>
	</div>
	<div class="zeile">
		<span class="label">SonderFlag</span>
<!-- BEGIN sonder -->
	<input type="checkbox" name="sonder[]" value="{sonder_id}">{sonder_name} 
<!-- END sonder -->	
	</div>
	<div class="zeile">
			<b>{Msg}</b><br>
			<input type="checkbox" name="shipto" value="1" tabindex="23">auch in abweichender Lieferanschrift suchen<br>
			<input type="checkbox" name="fuzzy" value="%" checked tabindex="24">Unscharf suchen<br>
			<input type="submit" name="suche" value="suchen" tabindex="25">&nbsp;
			<input type="submit" name="reset" value="clear" tabindex="26"> &nbsp;<input type="button" name="rep" value="Report" onClick="report()" tabindex="23">
	</div>
</form>
<!-- End Code ------------------------------------------->
</span>
</body>
</html>

