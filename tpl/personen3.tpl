<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
		function goFld() {
			if ({BgC}) document.formular.{Fld}.style.backgroundColor = "red";
   			document.formular.{Fld}.focus();
		}
		function suchFa() {
			val=document.formular.name.value;
			f1=open("suchFa.php?nq=1&name="+val,"suche","width=350,height=200,left=100,top=100");
		}
		function vcard() {
			f1=open("vcard.php?src=P","vcard","width=350,height=200,left=100,top=100");
		}
	//-->
	</script>
<body onLoad="goFld();">

<p class="listtop">Personen eingeben/editieren</p>

<span style="position:absolute; left:10px; top:47px; width:99%;">
<!-- Beginn Code ------------------------------------------->
<form name="formular" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="PID" value="{PID}">
<input type="hidden" name="FID1" value="{FID1}">
<input type="hidden" name="Quelle" value="{Quelle}">
<input type="hidden" name="employee" value="{employee}">
<input type="hidden" name="IMG_" value="{IMG_}">
{Msg}		
<span style="float:left; width:300px; height:330px; text-align:left; border: 0px solid black;">
	<div class="zeile">
		<span class="label">Anrede</span>
		<span class="feld"><input type="radio" name="greeting" value="H" {cpsel1} tabindex="1">Herr 
				<input type="radio" name="greeting" value="F" {cpsel2} tabindex="2">Frau    
				<input type="radio" name="greeting" value="O" {cpsel3} tabindex="3">
				<input type="text" name="cp_greeting" size="7" value="{cp_greeting}" tabindex="4">
		</span>
	</div>
	<div class="zeile">
		<span class="label">Titel</span>
		<span class="feld"><input type="text" name="cp_title" size="25" maxlength="75" value="{cp_title}" tabindex="5"></span>
	</div>
	<div class="zeile">
		<span class="label">Vorname</span>
		<span class="feld"><input type="text" name="cp_givenname" size="25" maxlength="75" value="{cp_givenname}" tabindex="6"></span>
	</div>
	<div class="zeile">
		<span class="label">Nachname</span>
		<span class="feld"><input type="text" name="cp_name" size="25" maxlength="75" value="{cp_name}" tabindex="7"></span>
	</div>
	<div class="zeile">
		<span class="label">Strasse</span>
		<span class="feld"><input type="text" name="cp_street" size="25" maxlength="75" value="{cp_street}" tabindex="8"></span>
	</div>
	<div class="zeile">
		<span class="label">Land / Plz</span>
		<span class="feld"><input type="text" name="cp_country" size="2" maxlength="3" value="{cp_country}" tabindex="9"> / 
				  <input type="text" name="cp_zipcode" size="5" maxlength="10" value="{cp_zipcode}" tabindex="10"></span>
	</div>
	<div class="zeile">
		<span class="label">Ort</span>
		<span class="feld"><input type="text" name="cp_city" size="25" maxlength="75" value="{cp_city}" tabindex="11"></span>
	</div>
	<div class="zeile">
		<span class="label">Telefon</span>
		<span class="feld"><input type="text" name="cp_phone1" size="25" maxlength="75" value="{cp_phone1}" tabindex="12"></span>
	</div>
	<div class="zeile">
		<span class="label">Mobiltelefon</span>
		<span class="feld"><input type="text" name="cp_phone2" size="25" maxlength="75" value="{cp_phone2}" tabindex="13"></span>
	</div>
	<div class="zeile">
		<span class="label">Fax</span>
		<span class="feld"><input type="text" name="cp_fax" size="25" maxlength="75" value="{cp_fax}" tabindex="14"></span>
	</div>
	<div class="zeile">
		<span class="label">eMail </span>
		<span class="feld"><input type="text" name="cp_email" size="25" maxlength="125" value="{cp_email}" tabindex="15"></span>
	</div>
	<div class="zeile">
		<span class="label">Homepage</span>
		<span class="feld"><input type="text" name="cp_homepage" size="25" maxlength="125" value="{cp_homepage}" tabindex="16"></span>
	</div>
	<div class="zeile">
		<span class="label">Geb.-Datum</span>
		<span class="feld"><input type="text" name="cp_gebdatum" size="10" maxlength="10" value="{cp_gebdatum}" tabindex="17"> TT.MM.JJJJ</span>
	</div>
</span>
<span style="float:left; width:290px; height:330px; text-align:left; border: 0px solid black;">
	<div class="zeile">
		<span class="label">Firma</span>
		<span class="feld"><input type="text" name="name" size="20" maxlength="75" value="{Firma}" tabindex="18">&nbsp;
				   <input type="button" name="fa" value="suchen" onClick="suchFa();"  tabindex="19"></span>
	</div>
	<div class="zeile">
		<span class="label">Abteilung</span>
		<span class="feld"><input type="text" name="cp_abteilung" size="25" maxlength="30" value="{cp_abteilung}" tabindex="20"></span>
	</div>
	<div class="zeile">
		<span class="label">Position</span>
		<span class="feld"><input type="text" name="cp_position" size="25" maxlength="25" value="{cp_position}" tabindex="21"></span>
	</div>
	<div class="zeile">
		<span class="label">Stichworte</span>
		<span class="feld"><input type="text" name="cp_stichwort1" size="25" maxlength="50" value="{cp_stichwort1}" tabindex="22"></span>
	</div>
	<div class="zeile">
		<span class="label">Foto</span>
		<span class="feld"><input type="file" name="bild" size="10" maxlength="75" tabindex="23"></span>
	</div>
	<div class="zeile">
		<span class="label"> </span>
		<span class="feld">{IMG}{IMG_}</span>
	</div>
	<div class="zeile">
		<span class="label">Beziehung</span>
		<span class="feld"><input type="text" name="cp_beziehung" size="10" maxlength="10" value="{cp_beziehung}" tabindex="24"></span>
	</div>
	<div class="zeile" style="align:center;">
		<textarea name="cp_notes" cols="42" rows="3" tabindex="25">{cp_notes}</textarea><br>Bemerkungen
	</div>
</span>
<span style="float:left; text-align:left; border: 0px solid black;">
<!-- BEGIN sonder -->
	<input type="checkbox" name="cp_sonder[]" value="{sonder_id}" {sonder_sel}>{sonder_name}<br>
<!-- END sonder -->					
</span>
<span style="position:absolute; left:1px; top:350px; width:680px; height:25px; text-align:left; border: 0px solid red;">
	<br><br>
	{Btn3} {Btn1} <input type='submit' name='neu' value='sichern als neu' tabindex="28">
	<input type="submit" name="reset" value="clear" tabindex="29"> <input type="button" name="" value="VCard" onClick="vcard()" tabindex="30">
	Berechtigung <select name="cp_owener"  tabindex="31"> 
<!-- BEGIN OwenerListe -->
		<option value="{grpid}" {Gsel}>{Gname}</option>
<!-- END OwenerListe -->
	</select> {init}
</span>

</span>
<input type="hidden" name="cp_cv_id" size="7" maxlength="10" value="{FID}" tabindex="32">
</form>
<!-- End Code ------------------------------------------->
</body>
</html>
