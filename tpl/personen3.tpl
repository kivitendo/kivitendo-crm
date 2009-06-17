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
			document.getElementById('sub'+last).className="";
			last=id;
		}
	//-->
	</script>
<body onLoad="submenu('tab1'); goFld();">

<p class="listtop">Personen eingeben/editieren</p>

<!-- Beginn Code ------------------------------------------->
<div style="position:absolute; top:2.80em; left:1em;  width:45em;">
	<ul id="maintab" class="shadetabs">
	<li id="subtab1" ><a href="#" onClick="submenu('tab1')">Person</a></li>
	<li id="subtab2" ><a href="#" onClick="submenu('tab2')">Firma</a></li>
	<li id="subtab3" ><a href="#" onClick="submenu('tab3')">Sonstiges</a></li>
	<li>{Msg}
	</ul>
</div>

<form name="formular" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="PID" value="{PID}">
<input type="hidden" name="mtime" value="{mtime}">
<input type="hidden" name="FID1" value="{FID1}">
<input type="hidden" name="Quelle" value="{Quelle}">
<input type="hidden" name="employee" value="{employee}">
<input type="hidden" name="IMG_" value="{IMG_}">
<input type="hidden" name="nummer" value="{nummer}">
<span id="tab1" style="visibility:visible; position:absolute; text-align:left;width:95%; left:0.8em; top:4.4em; border:1px solid black;">
	<div class="zeile2">
		<span class="label2 klein">Anrede</span>
		<span class="feld">
				<input type="text" name="cp_greeting_" size="8" value="{cp_greeting_}" tabindex="1">
				<select name="cp_greeting" tabindex="2" style="width:9em;">
					<option value="">
<!-- BEGIN anreden -->
					<option value="{ANREDE}" {ASEL}>{ANREDE}
<!-- END anreden -->
				</select>
		</span>
		<span class="label klein">Telefon 1</span>
		<span class="feld"><input type="text" id="phone" name="cp_phone1" size="25" maxlength="75" value="{cp_phone1}" tabindex="12"></span>
	</div>
	<div class="zeile2">
		<span class="label2 klein">Briefanrede</span>
		<span class="feld"><select name="cp_salutation" tabindex="3" style="width:15em;">
					<option value="">
<!-- BEGIN briefanred -->
					<option value="{BRIEFAN}" {BSEL}>{BRIEFAN}
<!-- END briefanred -->
				</select></span>
		<span class="label klein">2</span>
		<span class="feld"><input type="text" name="cp_phone2" size="25" maxlength="75" value="{cp_phone2}" tabindex="12"></span>
	</div>
	<div class="zeile2">
		<span class="label2 klein"></span>
		<span class="feld"><input type="text" name="cp_salutation_" size="25" maxlength="125" value="{cp_salutation_}" tabindex="5"></span>
		<span class="label klein">Mobiltelefon 1</span>
		<span class="feld"><input type="text" name="cp_mobile1" size="25" maxlength="75" value="{cp_mobile1}" tabindex="13"></span>
	</div>
	<div class="zeile2">
		<span class="label2 klein">Titel</span>
		<span class="feld"><input type="text" name="cp_title" size="25" maxlength="75" value="{cp_title}" tabindex="5"></span>
		<span class="label klein">2</span>
		<span class="feld"><input type="text" name="cp_mobile2" size="25" maxlength="75" value="{cp_mobile2}" tabindex="13"></span>
	</div>
	<div class="zeile2">
		<span class="label2 klein">Vorname</span>
		<span class="feld"><input type="text" name="cp_givenname" size="25" maxlength="75" value="{cp_givenname}" tabindex="6"></span>
		<span class="label klein">Fax</span>
		<span class="feld"><input type="text" name="cp_fax" size="25" maxlength="75" value="{cp_fax}" tabindex="14"></span>
	</div>
	<div class="zeile2">
		<span class="label2 klein">Nachname</span>
		<span class="feld"><input type="text" name="cp_name" size="25" maxlength="75" value="{cp_name}" tabindex="7"></span>
		<span class="label klein">Privat</span>
		<span class="feld"><input type="text" name="cp_privatphone" size="25" maxlength="75" value="{cp_privatphone}" tabindex="12"></span>
	</div>
	<div class="zeile2">
		<span class="label2 klein">Strasse</span>
		<span class="feld"><input type="text" name="cp_street" size="25" maxlength="75" value="{cp_street}" tabindex="8"></span>
		<span class="label klein">Privat eMail </span>
		<span class="feld"><input type="text" name="cp_privatemail" size="25" maxlength="125" value="{cp_privatemail}" tabindex="15"></span>
	</div>
	<div class="zeile2">
		<span class="label2 klein">Land / Plz</span>
		<span class="feld"><input type="text" id="country" name="cp_country" size="2" maxlength="3" value="{cp_country}" tabindex="9"> / 
				  <input type="text" id="zipcode" name="cp_zipcode" size="5" maxlength="10" value="{cp_zipcode}" tabindex="10">
		</span>
	</div>
	<div class="zeile2">
		<span class="label2 klein">Ort</span>
		<span class="feld"><input type="text" id="city" name="cp_city" size="25" maxlength="75" value="{cp_city}" tabindex="11"></span>
				  <span id="geosearchP" class="feldxx"></span>
	</div>
	<div class="zeile2">
		<span class="label2 klein">Homepage</span>
		<span class="feld"><input type="text" name="cp_homepage" size="25" maxlength="125" value="{cp_homepage}" tabindex="16"></span>
		<span class="label klein">eMail </span>
		<span class="feld"><input type="text" name="cp_email" size="25" maxlength="125" value="{cp_email}" tabindex="15"></span>
	</div>
	<br><br>
</span>
<span id="tab2" style="visibility:hidden; position:absolute; text-align:left;width:90%; left:0.8em; top:4.4em; border:1px solid black;">
	<br><br>
	<div class="zeile2">
		<span class="label klein">Firma</span>
		<span class="feld"><input type="text" name="name" size="25" maxlength="75" value="{Firma}" tabindex="18"><input type="button" name="fa" value="suchen" onClick="suchFa();"  tabindex="19"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Abteilung</span>
		<span class="feld"><input type="text" name="cp_abteilung" size="25" maxlength="30" value="{cp_abteilung}" tabindex="20"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Position</span>
		<span class="feld"><input type="text" name="cp_position" size="25" maxlength="25" value="{cp_position}" tabindex="21"></span>
	</div>

	<div class="zeile2" style="align:center;">
		<br>
	</div>
	<br>
</span>
<span id="tab3" style="visibility:hidden; position:absolute; text-align:left;width:90%; left:0.8em; top:4.4em; border:1px solid black;">
<!--span style="float:left; text-align:left; border: 0px solid black;"-->
	<span  style="float:left;">
		<div class="zeile2">
			<span class="label klein">Stichworte</span>
			<span class="feld"><input type="text" name="cp_stichwort1" size="25" maxlength="50" value="{cp_stichwort1}" tabindex="22"></span>
		</div>
		<div class="zeile2">
			<span class="label klein">Geb.-Datum</span>
			<span class="feld"><input type="text" name="cp_birthday" size="10" maxlength="10" value="{cp_birthday}" tabindex="17"><span class="klein"> TT.MM.JJJJ</span></span>
		</div>
		<div class="zeile2">
			<span class="label klein">Bild</span>
			<span class="feld"><input type="file" name="Datei[bild]" size="10" maxlength="75" tabindex="23"></span>
		</div>
		<div class="zeile2">
			<span class="label klein">Visitenkarte</span>
			<span class="feld"><input type="file" name="Datei[visit]" size="10" maxlength="75" tabindex="23"></span>
		</div>
		<div class="zeile2" style="align:left;">
			<span class="klein">Bemerkungen</span><br>
			<span class="feldxx" style="border:0px solid black;"><textarea class="klein" name="cp_notes" cols="55" rows="4" tabindex="25">{cp_notes}</textarea></span>
		</div>
		<div class="zeile2">
			<span class="label klein">Beziehung</span>
			<span class="feld"><input type="text" name="cp_beziehung" size="8" maxlength="10" value="{cp_beziehung}" tabindex="24"></span>
		</div>
	</span><span style="float:left;">
		<!--div class="zeile2"-->Attribute:
	<!-- BEGIN sonder -->
			<div class="zeile2"><input type="checkbox" name="cp_sonder[]" value="{sonder_id}" {sonder_sel}>{sonder_name}</div>
	<!-- END sonder -->					
		<!--/div-->
			<span class="label">{IMG}{IMG_}<br>
			{visitenkarte}</span>
	</span>
</span>
<span style="position:absolute; left:1.2em; top:20em; width:52em; height:2em; text-align:left; border: 0px solid red;">
	<br><br>
	{Btn3} {Btn1} <input type='submit' class='sichernneu' name='neu' value='sichern als neu' tabindex="28">
	<input type="submit" class="clear" name="reset" value="clear" tabindex="29"> <input type="button" name="" value="VCard" onClick="vcard()" tabindex="30">
	<span class="klein">Berechtigung</span> <select name="cp_owener"  tabindex="31"> 
<!-- BEGIN OwenerListe -->
		<option value="{grpid}" {Gsel}>{Gname}</option>
<!-- END OwenerListe -->
	</select> {init}
</span>

</span>
<input type="hidden" name="cp_cv_id" size="7" maxlength="10" value="{FID}" tabindex="32">
</form>
<!-- End Code ------------------------------------------->
	<script type='text/javascript' src='inc/geosearchP.js'></script>
	<script type='text/javascript' src='inc/geosearch.js'></script>
</body>
</html>
