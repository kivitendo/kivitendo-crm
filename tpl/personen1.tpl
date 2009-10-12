<!-- $Id: personen1.tpl 4293 2009-06-14 08:21:08Z jbueren $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
    <script language="JavaScript">
        function setLetter(letter) {
            document.formular.cp_name.value=letter;
            document.formular.first.value="1";
            document.formular.submit();
        }
    </script>
<body onLoad="document.formular.cp_name.focus();">
<p class="listtop">Personensuche</p>

<form name="formular" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="FID1" value="{FID1}">
<input type="hidden" name="first" value="">
<input type="hidden" name="Quelle" value="{Quelle}">
<input type="hidden" name="employee" value="{employee}">

<span style="float:left; top:3.0em; left:1em; border: 0px solid black;">
<!-- Beginn Code ------------------------------------------->
<p class="listheading">| 
<a href="#" onClick="setLetter('A');" class="fett">A</a> |
<a href="#" onClick="setLetter('B');" class="fett">B</a> |
<a href="#" onClick="setLetter('C');" class="fett">C</a> |
<a href="#" onClick="setLetter('D');" class="fett">D</a> |
<a href="#" onClick="setLetter('E');" class="fett">E</a> |
<a href="#" onClick="setLetter('F');" class="fett">F</a> |
<a href="#" onClick="setLetter('G');" class="fett">G</a> |
<a href="#" onClick="setLetter('H');" class="fett">H</a> |
<a href="#" onClick="setLetter('I');" class="fett">I</a> |
<a href="#" onClick="setLetter('J');" class="fett">J</a> |
<a href="#" onClick="setLetter('K');" class="fett">K</a> |
<a href="#" onClick="setLetter('L');" class="fett">L</a> |
<a href="#" onClick="setLetter('M');" class="fett">M</a> |
<a href="#" onClick="setLetter('N');" class="fett">N</a> |
<a href="#" onClick="setLetter('O');" class="fett">O</a> |
<a href="#" onClick="setLetter('P');" class="fett">P</a> |
<a href="#" onClick="setLetter('Q');" class="fett">Q</a> |
<a href="#" onClick="setLetter('R');" class="fett">R</a> |
<a href="#" onClick="setLetter('S');" class="fett">S</a> |
<a href="#" onClick="setLetter('T');" class="fett">T</a> |
<a href="#" onClick="setLetter('U');" class="fett">U</a> |
<a href="#" onClick="setLetter('V');" class="fett">V</a> |
<a href="#" onClick="setLetter('W');" class="fett">W</a> |
<a href="#" onClick="setLetter('X');" class="fett">X</a> |
<a href="#" onClick="setLetter('Y');" class="fett">Y</a> |
<a href="#" onClick="setLetter('Z');" class="fett">Z</a> |
<a href="#" onClick="setLetter('~');" class="fett">*</a> |</p>
	<div class="zeile">
		<span class="label">.:gender:.</span>
		<span class="leftfeld">
             <select name="cp_gender" tabindex="1" style="width:9em;">
                    <option value="" {cp_gender}>
                    <option value="m" {cp_genderm}>.:male:.
                    <option value="f" {cp_genderf}>.:female:.
                </select>
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
		<span class="leftfeld"><input type="text" name="cp_gebdatum" size="12" maxlength="10" value="{cp_gebdatum}" tabindex="16"><span class="klein">TT.MM.JJJJ</span></span>
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
    <span class="label">Firmenname</span>
    <span class="leftfeld"><input type="text" name="customer_name" size="27" maxlength="75" value="{customer_name}" tabindex="11"></span>
    <!-- span class="label">Kundentyp</span>
    <span class="leftfeld"><input type="text" name="business_description" size="27" maxlength="75" value="{business_description}" tabindex="20"></span -->
  </div>
	<div class="zeile">
		<input type="checkbox" name="fuzzy" value="%" checked><span class="klein">Unscharf suchen&nbsp;&nbsp;<b>{Msg}</b></span> <input type="checkbox" name="vendor" checked><span class="klein">Lieferanten</span><input type="checkbox" name="customer" checked><span class="klein">Kunden</span><br>
		 <input type="checkbox" name="deleted"><span class="klein">gelöschte Ansprechpartner (Kunden und Lieferanten) oder private Adressen</span><br>
		{Btn1} {Btn3} <input type="submit" class="anzeige" name="suche" value="suchen"> <input type="submit" class="clear" name="reset" value="clear">
	</div>
</span>
<div style="margin-left:2.5em; float:left;   margin-top:3em; border: 0px solid black;">

<!-- Gibt es hier die Möglichkeit eine Fallentscheidung zu machen?  Falls sonder dann einblenden:-->
Attribute: <br>
<!-- BEGIN sonder -->
	<input class="klein" type="checkbox" name="cp_sonder[]" value="{sonder_id}"><span class="klein">{sonder_key}</span><br>
<!-- END sonder -->		
</div>	
</form>
<!-- End Code ------------------------------------------->
</body>
</html>

