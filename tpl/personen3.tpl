<!-- $Id: personen3.tpl,v 1.4 2005/11/02 11:35:45 hli Exp $ -->
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

<table width="99%" border="0"><tr><td>
<!-- Beginn Code ------------------------------------------->
<p class="listtop">Personen eingeben/editieren</p>
<form name="formular" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="PID" value="{PID}">
<input type="hidden" name="FID1" value="{FID1}">
<input type="hidden" name="Quelle" value="{Quelle}">
<input type="hidden" name="employee" value="{employee}">
<input type="hidden" name="IMG_" value="{IMG_}">
<table>
	
	<tr>
		<td class="smal"><input type="radio" name="greeting" value="H" {cpsel1} tabindex="1">Herr <input type="radio" name="greeting" value="F" {cpsel2} tabindex="2">Frau    
			<input type="radio" name="greeting" value="O" {cpsel3} tabindex="3"><input type="text" name="cp_greeting" size="10" value="{Anrede}" tabindex="4"><br>Anrede</td>
		<td class="smal" colspan="2"><input type="text" name="cp_title" size="30" maxlength="75" value="{Titel}" tabindex="5"><br>Titel</td>
		<td class="smal"><input type="text" name="cp_abteilung" size="15" maxlength="25" value="{Abteilg}" tabindex="21"><br>Abteilung</td>
		<td class="smal" rowspan="11">
<!-- BEGIN sonder -->
			<input type="checkbox" name="cp_sonder[]" value="{sonder_id}" {sonder_sel}>{sonder_name}<br>
<!-- END sonder -->					
		</td>
	</tr>
	<tr>
		<td class="smal"><input type="text" name="cp_givenname" size="25" maxlength="75" value="{Vname}" tabindex="6"><br>Vorname</td>
		<td class="smal" colspan="2"><input type="text" name="cp_name" size="30" maxlength="75" value="{Nname}" tabindex="7"><br>Nachname</td>
		<td class="smal"><input type="text" name="cp_position" size="15" maxlength="25" value="{Position}" tabindex="22"><br>Position</td>
	</tr>

	<tr>
		<td class="smal" colspan="2"><input type="text" name="cp_street" size="40" maxlength="75" value="{Strasse}" tabindex="8"><br>Strasse</td>
		<td class="smal" class="norm">{init}</td>
		<td class="smal"><select name="cp_owener"  tabindex="23">
<!-- BEGIN OwenerListe -->
					<option value="{grpid}" {Gsel}>{Gname}</option>
<!-- END OwenerListe -->
			</select><br>Berechtigung</td>
	</tr>
	<tr>
		<td class="smal" colspan="3">
			<select name="cp_country" tabindex="9">
<!-- BEGIN LandListe -->
				<option value="{LandID}" {Lsel}>{Land}</option>
<!-- END LandListe -->
			</select>
			<input type="text" name="cp_zipcode" size="5" maxlength="10" value="{Plz}" tabindex="10">
			<input type="text" name="cp_city" size="30" maxlength="75" value="{Ort}" tabindex="11">
			<br>Land &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
				 &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; Postleitzahl Ort</td>
		<td class="smal" rowspan="5" class="smal">{Msg}<br>{Btn1}<br><br><input type='submit' name='neu' value='sichern als neu' tabindex="26"><br><br>{Btn3}<br><br><input type="submit" name="reset" value="clear"><br><input type="button" name="" value="VCard" onClick="vcard()"></td>
	</tr>
	<tr>
		<td class="smal"><input type="text" name="cp_phone1" size="25" maxlength="75" value="{Tel1}" tabindex="12"><br>Telefon</td>
		<td class="smal"><input type="text" name="cp_phone2" size="23" maxlength="75" value="{Tel2}" tabindex="13"><br>Mobiletelefon</td>
		<td class="smal"><input type="text" name="cp_gebdatum" size="10" maxlength="10" value="{GDate}" tabindex="24"><br>Geburstdatum</td>
	</tr>
	<tr>
		<td class="smal" colspan="2"><input type="text" name="cp_fax"" size="25" maxlength="75" value="{Fax}" tabindex="14"><br>Fax</td>
		<td class="smal"><input type="text" name="Bez" size="10" maxlength="10" value="{Bez}" tabindex="26"><br>Beziehung</td>
	</tr>
	<tr>
		<td class="smal" colspan="2"><input type="text" name="cp_email" size="40" maxlength="125" value="{eMail}" tabindex="15"><br>eMail</td>
		<td class="smal">&nbsp;</td>
	</tr>
	<tr>
		<td class="smal" colspan="2"><input type="text" name="cp_homepage" size="40" maxlength="125" value="{Homepage}" tabindex="16"><br>Homepage</td>
		<td class="smal">&nbsp;</td>
	</tr>
	<tr>
		<td class="smal" colspan="2"><input type="text" name="name" size="40" maxlength="75" value="{Firma}" tabindex="17"> <input type="button" name="fa" value="suchen" onClick="suchFa();"  tabindex="18"><br>Firma</td>
		<td class="smal"><input type="text" name="cp_cv_id" size="7" maxlength="10" value="{FID}" tabindex="32"><br>FID</td>
		<td class="smal">&nbsp;</td>
	</tr>
	<tr>
		<td class="smal" colspan="2"><input type="text" name="cp_stichwort1" size="45" maxlength="50" value="{SW1}" tabindex="19"><br>Stichworte</td>
		<td class="smal" colspan="2"><input type="file" name="bild" size="10" maxlength="75"><br>Foto</td>
	</tr>
	<tr>
		<td class="smal" colspan="3"><textarea name="cp_notes" cols="66" rows="3" tabindex="20">{Notiz}</textarea><br>Bemerkungen</td>
		<td class="smal">{IMG}<br>{IMG_}</td>
	</tr>
</table>
</form>

<!-- End Code ------------------------------------------->
</td></tr></table>
</body>
</html>
