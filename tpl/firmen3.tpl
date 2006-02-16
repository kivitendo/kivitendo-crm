<!-- $Id: firmen3.tpl,v 1.3 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function vcard() {
			f1=open("vcard.php?src=F","vcard","width=350,height=200,left=100,top=100");
		}
	//-->
	</script>
<body>

<table width="99%" border="0"><tr><td>
<!-- Beginn Code ------------------------------------------->
<p class="listtop"> Firma/Kunde eingeben/editieren</p>
<form name="neueintrag" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="id" value="{ID}">
<input type="hidden" name="customernumber" value="{KDNR}">
<input type="hidden" name="employee" value="{employee}">
<input type="hidden" name="grafik" value="{grafik}">
<table>
	<tr>
		<td class="smal"><input type="text" name="name" size="28" maxlength="75" value="{name}" tabindex="1"><br>Rechnungsadresse 1</td>
		<td class="smal"><input type="text" name="shiptoname" size="28" maxlength="75" value="{shiptoname}" tabindex="15"><br>Lieferadresse 1</td>
		<td class="smal" width="80" class="norm">{init}</td>
		<td class="norm">{Msg}&nbsp;
	</tr>
	<tr>
		<td class="smal"><input type="text" name="department_1" size="28" maxlength="75" value="{department_1}" tabindex="2"><br>Rechnungsadresse 2</td>
		<td class="smal"><input type="text" name="shiptodepartment_1" size="28" maxlength="75" value="{shiptodepartment_1}" tabindex="16"><br>Lieferadresse 2</td>
		<td class="smal" colspan="2">
			<select name="owener" tabindex="25">
<!-- BEGIN OwenerListe -->
				<option value="{grpid}" {Gsel}>{Gname}</option>
<!-- END OwenerListe -->
			</select><br>Berechtigung
		</td>
	</tr>
	<tr>
		<td class="smal"><input type="text" name="street" size="28" maxlength="75" value="{street}" tabindex="3"><br>Strasse</td>
		<td class="smal"><input type="text" name="shiptostreet" size="28" maxlength="75" value="{shiptostreet}" tabindex="17"><br>Strasse</td>
		<td class="smal" colspan="2" rowspan="3">{IMG}&nbsp;</td>
	</tr>
	<tr>
		<td class="smal" nowrap><input type="text" name="country" size="1" maxlength="75" value="{country}" tabindex="4">
			<input type="text" name="zipcode" size="5" maxlength="10" value="{zipcode}" tabindex="5">
			<input type="text" name="city" size="17" maxlength="75" value="{city}" tabindex="6"><br>Land Postleitzahl Ort</td>
		<td class="smal" nowrap><input type="text" name="shiptocountry" size="1" value="{shiptocountry}" tabindex="18">
		 	<input type="text" name="shiptozipcode" size="5" maxlength="10" value="{shiptozipcode}" tabindex="19">
			<input type="text" name="shiptocity" size="17" maxlength="75" value="{shiptocity}" tabindex="20"><br>Land Postleitzahl Ort</td></tr>
	<tr>
		<td class="smal"><input type="text" name="phone" size="20" maxlength="30" value="{phone}" tabindex="7"><br>Telefon</td>
		<td class="smal"><input type="text" name="shiptophone" size="20" maxlength="30" value="{shiptophone}" tabindex="21"><br>Telefon</td></tr>
	<tr>
		<td class="smal"><input type="text" name="fax" size="20" maxlength="30" value="{fax}" tabindex="8"><br>Fax</td>
		<td class="smal"><input type="text" name="shiptofax" size="20" maxlength="30" value="{shiptofax}" tabindex="22"><br>Fax</td>
		<td class="smal" colspan="2"><input type="file" name="Datei" size="13" maxlength="125" accept="Image/*" tabindex="26"><br>Logo</td>
	</tr>
	<tr>	<td class="smal"><input type="text" name="email" size="28" maxlength="125" value="{email}" tabindex="9"><br>eMail</td>
		<td class="smal"><input type="text" name="shiptoemail" size="28" maxlength="125" value="{shiptoemail}" tabindex="23"><br>eMail</td>
		<td class="smal" rowspan="2">&nbsp;</td>
		<td class="smal" rowspan="4" valign="top">
			{Btn1}<br><br>{Btn2}<br><br>
			<input type="submit" name="saveneu" value="sichern neu"><br>
			<input type="submit" name="reset" value="clear"><br>
			<input type="button" name="" value="VCard" onClick="vcard()">
		</td></tr>
	<tr><td class="smal"><input type="text" name="homepage" size="28" maxlength="125" value="{homepage}" tabindex="10"><br>Homepage</td>
		<td class="smal"><input type="text" name="shiptocontact" size="20" maxlength="75" value="{shiptocontact}" tabindex="24"><br>Liefer-Kontakt</td></tr>
	<tr><td class="smal"><input type="text" name="sw" size="28" value="{sw}" maxlength="50" tabindex="11"><br>Stichwort/e</td>
		<td class="smal" rowspan="3" colspan="2"><textarea name="notes" cols="45" rows="5" tabindex="14">{notes}</textarea><br>Bemerkungen</td></tr>
	<tr><td class="smal"><input type="text" name="branche" size="28" maxlength="25" value="{branche}" tabindex="12"><br>Branche</td></tr>
	<tr><td class="smal"><input type="text" name="taxnumber" size="28" maxlength="75" value="{ustid}" tabindex="13"><br>Steuernummer</td>
		<td class="smal">
			<select name="business_id" tabindex="25">
<!-- BEGIN TypListe -->
				<option value="{Bid}" {Bsel}>{Btype}</option>
<!-- END TypListe -->
			</select><br>Kundentyp
		</td></tr>
</table>
</form>
<!-- End Code ------------------------------------------->
</td></tr></table>
</body>
</html>
