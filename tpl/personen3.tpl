<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<link type="text/css" REL="stylesheet" HREF="css/tabcontent.css"></link>

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
	var last = 'tab2';
	function submenu(id) {
			document.getElementById(last).style.visibility='hidden';
			document.getElementById(id).style.visibility='visible';
			men='sub' + id; 
			document.getElementById('sub'+id).className="selected";
			document.getElementById('sub'+last).className="shadetabs";
			last=id;
		}
	//-->
	</script>
<body onLoad="submenu('tab1'); goFld();">

<p class="listtop">Personen eingeben/editieren</p>

<!--span style="position:absolute; left:10px; top:47px; width:99%;"-->
<!-- Beginn Code ------------------------------------------->
<div style="position:absolute; top:3.5em; left:10px;  width:770px;">
	<ul id="maintab" class="shadetabs">
	<li id="subtab1" ><a href="#" onClick="submenu('tab1')">Person</a></li>
	<li id="subtab2" ><a href="#" onClick="submenu('tab2')">Firma</a></li>
	<li id="subtab3" ><a href="#" onClick="submenu('tab3')">Sonstiges</a></li>
	<li>{Msg}
	</ul>
</div>

<form name="formular" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="PID" value="{PID}">
<input type="hidden" name="FID1" value="{FID1}">
<input type="hidden" name="Quelle" value="{Quelle}">
<input type="hidden" name="employee" value="{employee}">
<input type="hidden" name="IMG_" value="{IMG_}">
<input type="hidden" name="nummer" value="{nummer}">
<span id="tab1" style="visibility:visible; position:absolute; text-align:left;width:90%; left:6px; top:6em; border:1px solid black;">
	<div class="zeile2">
		<span class="label2">Anrede</span>
		<span class="feld">
				<input type="text" name="cp_greeting_" size="8" value="{cp_greeting_}" tabindex="1">
				<select name="cp_greeting" tabindex="2" style="width:116px;">
					<option value="">
<!-- BEGIN anreden -->
					<option value="{ANREDE}" {ASEL}>{ANREDE}
<!-- END anreden -->
				</select>
		</span>
		<span class="label">Telefon 1</span>
		<span class="feld"><input type="text" name="cp_phone1" size="25" maxlength="75" value="{cp_phone1}" tabindex="12"></span>
	</div>
	<div class="zeile2">
		<span class="label2">Briefanrede</span>
		<span class="feld"><select name="cp_salutation" tabindex="3" style="width:199px;">
					<option value="">
<!-- BEGIN briefanred -->
					<option value="{BRIEFAN}" {BSEL}>{BRIEFAN}
<!-- END briefanred -->
				</select></span>
		<span class="label">2</span>
		<span class="feld"><input type="text" name="cp_phone2" size="25" maxlength="75" value="{cp_phone2}" tabindex="12"></span>
	</div>
	<div class="zeile2">
		<span class="label2"></span>
		<span class="feld"><input type="text" name="cp_salutation_" size="25" maxlength="125" value="{cp_salutation_}" tabindex="5"></span>
		<span class="label">Mobiltelefon 1</span>
		<span class="feld"><input type="text" name="cp_mobile1" size="25" maxlength="75" value="{cp_mobile1}" tabindex="13"></span>
	</div>
	<div class="zeile2">
		<span class="label2">Titel</span>
		<span class="feld"><input type="text" name="cp_title" size="25" maxlength="75" value="{cp_title}" tabindex="5"></span>
		<span class="label">2</span>
		<span class="feld"><input type="text" name="cp_mobile2" size="25" maxlength="75" value="{cp_mobile2}" tabindex="13"></span>
	</div>
	<div class="zeile2">
		<span class="label2">Vorname</span>
		<span class="feld"><input type="text" name="cp_givenname" size="25" maxlength="75" value="{cp_givenname}" tabindex="6"></span>
		<span class="label">Fax</span>
		<span class="feld"><input type="text" name="cp_fax" size="25" maxlength="75" value="{cp_fax}" tabindex="14"></span>
	</div>
	<div class="zeile2">
		<span class="label2">Nachname</span>
		<span class="feld"><input type="text" name="cp_name" size="25" maxlength="75" value="{cp_name}" tabindex="7"></span>
		<span class="label">Privat</span>
		<span class="feld"><input type="text" name="cp_privatphone" size="25" maxlength="75" value="{cp_privatphone}" tabindex="12"></span>
	</div>
	<div class="zeile2">
		<span class="label2">Strasse</span>
		<span class="feld"><input type="text" name="cp_street" size="25" maxlength="75" value="{cp_street}" tabindex="8"></span>
		<span class="label">Privat eMail </span>
		<span class="feld"><input type="text" name="cp_privatemail" size="25" maxlength="125" value="{cp_privatemail}" tabindex="15"></span>
	</div>
	<div class="zeile2">
		<span class="label2">Land / Plz</span>
		<span class="feld"><input type="text" name="cp_country" size="2" maxlength="3" value="{cp_country}" tabindex="9"> / 
				  <input type="text" name="cp_zipcode" size="5" maxlength="10" value="{cp_zipcode}" tabindex="10"></span>
	</div>
	<div class="zeile2">
		<span class="label2">Ort</span>
		<span class="feld"><input type="text" name="cp_city" size="25" maxlength="75" value="{cp_city}" tabindex="11"></span>
	</div>
	<div class="zeile2">
		<span class="label2">Homepage</span>
		<span class="feld"><input type="text" name="cp_homepage" size="25" maxlength="125" value="{cp_homepage}" tabindex="16"></span>
		<span class="label">eMail </span>
		<span class="feld"><input type="text" name="cp_email" size="25" maxlength="125" value="{cp_email}" tabindex="15"></span>
	</div>
	<br><br>
</span>
<span id="tab2" style="visibility:hidden; position:absolute; text-align:left;width:90%; left:6px; top:6em; border:1px solid black;">
<!--span style="float:left; width:290px; height:330px; text-align:left; border: 0px solid black;"-->
	<div class="zeile2">
		<span class="label">Firma</span>
		<span class="feld"><input type="text" name="name" size="25" maxlength="75" value="{Firma}" tabindex="18"><input type="button" name="fa" value="suchen" onClick="suchFa();"  tabindex="19"></span>
	</div>
	<div class="zeile2">
		<span class="label">Abteilung</span>
		<span class="feld"><input type="text" name="cp_abteilung" size="25" maxlength="30" value="{cp_abteilung}" tabindex="20"></span>
		<span class="label">Beziehung</span>
		<span class="feld"><input type="text" name="cp_beziehung" size="10" maxlength="10" value="{cp_beziehung}" tabindex="24"></span>
	</div>
	<div class="zeile2">
		<span class="label">Position</span>
		<span class="feld"><input type="text" name="cp_position" size="25" maxlength="25" value="{cp_position}" tabindex="21"></span>
	</div>
	<div class="zeile2">
		<span class="label">Stichworte</span>
		<span class="feld"><input type="text" name="cp_stichwort1" size="25" maxlength="50" value="{cp_stichwort1}" tabindex="22"></span>
	</div>
	<div class="zeile2">
		<span class="label">Geb.-Datum</span>
		<span class="feld"><input type="text" name="cp_birthday" size="10" maxlength="10" value="{cp_birthday}" tabindex="17"> TT.MM.JJJJ</span>
	</div>
	<div class="zeile2">
		<span class="label">Bild</span>
		<span class="feld"><input type="file" name="Datei" size="10" maxlength="75" tabindex="23"></span>
		<span class="label"> </span>
		<span class="feld"></span>
	</div>
	<div class="zeile2" style="align:center;">
		Bemerkungen<br>
		<span class="feldxx"><textarea name="cp_notes" cols="50" rows="3" tabindex="25">{cp_notes}</textarea></span>
		<span class="feld">{IMG}{IMG_}</span>
	</div>
	<div class="zeile2" style="align:center;">
		<br>
	</div>
	<br>
</span>
<span id="tab3" style="visibility:hidden; position:absolute; text-align:left;width:90%; left:6px; top:6em; border:1px solid black;">
<!--span style="float:left; text-align:left; border: 0px solid black;"-->
	Sonderflags:
<!-- BEGIN sonder -->
	<div class="zeile2"><input type="checkbox" name="cp_sonder[]" value="{sonder_id}" {sonder_sel}>{sonder_name}</div>
<!-- END sonder -->					
	<br><br>
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
