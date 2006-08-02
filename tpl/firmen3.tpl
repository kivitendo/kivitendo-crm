<!-- $Id$ -->
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
<body onLoad="document.neueintrag.name.focus();">

<p class="listtop"> Firma/Kunde eingeben/editieren</p>

<span style="position:absolute; left:10px; top:47px; width:99%;">
<!-- Beginn Code ------------------------------------------->
<form name="neueintrag" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="id" value="{id}">
<input type="hidden" name="customernumber" value="{customernumber}">
<input type="hidden" name="employee" value="{employee}">
<input type="hidden" name="grafik" value="{grafik}">
{Msg}
<span style="float:left; width:485px;  text-align:left; border: 0px solid black;">
<span style="float:left; width:295px; height:265px; text-align:left; border: 0px solid black;">
	<div class="zeile">
		<span class="label"></span>
		<span class="feld" style="font-wight:bold; text-align:center;">Rechnungsadresse</span>
	</div>
	<div class="zeile">
		<span class="label">Firmenname</span>
		<span class="feld"><input type="text" name="name" size="25" maxlength="75" value="{name}" tabindex="1"</span>
	</div>
	<div class="zeile">
		<span class="label">Abteilung 1</span>
		<span class="feld"><input type="text" name="department_1" size="25" maxlength="75" value="{department_1}" tabindex="2"></span>
	</div>
	<div class="zeile">
		<span class="label">Abteilung 2</span>
		<span class="feld"><input type="text" name="department_2" size="25" maxlength="75" value="{department_2}" tabindex="2"></span>
	</div>
	<div class="zeile">
		<span class="label">Strasse</span>
		<span class="feld"><input type="text" name="street" size="25" maxlength="75" value="{street}" tabindex="3"></span>
	</div>
	<div class="zeile">
		<span class="label">Land / Plz</span>
		<span class="feld">
			<input type="text" name="country" size="2" maxlength="75" value="{country}" tabindex="4">/
			<input type="text" name="zipcode" size="4" maxlength="10" value="{zipcode}" tabindex="5">
			<select name="bundesland" tabindex="6" style="width:100px;">
				<option value=""></option>
<!-- BEGIN buland -->
				<option value="{BUVAL}" {BUSEL}>{BUTXT}</option>
<!-- END buland -->
			</select>
		</span>
	</div>
	<div class="zeile">
		<span class="label">Ort</span>
		<span class="feld"><input type="text" name="city" size="25" maxlength="75" value="{city}" tabindex="6"></span>
	</div>
	<div class="zeile">
		<span class="label">Telefon</span>
		<span class="feld"><input type="text" name="phone" size="25" maxlength="30" value="{phone}" tabindex="7"></span>
	</div>
	<div class="zeile">
		<span class="label">Fax</span>
		<span class="feld"><input type="text" name="fax" size="25" maxlength="30" value="{fax}" tabindex="8"></span>
	</div>
	<div class="zeile">
		<span class="label">eMail</span>
		<span class="feld"><input type="text" name="email" size="25" maxlength="125" value="{email}" tabindex="9"></span>
	</div>
	<div class="zeile">
		<span class="label">Kontakt</span>
		<span class="feld"><input type="text" name="contact" size="25" maxlength="125" value="{contact}" tabindex="10"></span>
	</div>

</span>
<span style="float:right; width:185px; height:265px; text-align:left; border: 0px solid black;">
	<div class="zeile">
		<span class="feld" style="font-wight:bold; text-align:center;">Lieferadresse</span>
	</div>
	<div class="zeile">
		<span class="smalfeld"><input type="text" name="shiptoname" size="22" maxlength="75" value="{shiptoname}" tabindex="11"></span>
	</div>
	<div class="zeile">
		<span class="smalfeld"><input type="text" name="shiptodepartment_1" size="22" maxlength="75" value="{shiptodepartment_1}" tabindex="12"></span>
	</div>
	<div class="zeile">
		<span class="smalfeld"><input type="text" name="shiptodepartment_2" size="22" maxlength="75" value="{shiptodepartment_2}" tabindex="12"></span>
	</div>
	<div class="zeile">
		<span class="smalfeld"><input type="text" name="shiptostreet" size="22" maxlength="75" value="{shiptostreet}" tabindex="13"></span>
	</div>
	<div class="zeile">
		<span class="smalfeld">
			<input type="text" name="shiptocountry" size="1" value="{shiptocountry}" tabindex="14">/
			<input type="text" name="shiptozipcode" size="4" maxlength="10" value="{shiptozipcode}" tabindex="15">
			<select name="shiptobundesland" tabindex="6" style="width:80px;">
				<option value=""></option>
<!-- BEGIN buland2 -->
				<option value="{SBUVAL}" {SBUSEL}>{SBUTXT}</option>
<!-- END buland2 -->
			</select>
		</span>
	</div>
	<div class="zeile">
		<span class="smalfeld"><input type="text" name="shiptocity" size="22" maxlength="75" value="{shiptocity}" tabindex="16"></span>
	</div>
	<div class="zeile">
		<span class="smalfeld"><input type="text" name="shiptophone" size="22" maxlength="30" value="{shiptophone}" tabindex="17"></span>
	</div>
	<div class="zeile">
		<span class="smalfeld"><input type="text" name="shiptofax" size="22" maxlength="30" value="{shiptofax}" tabindex="18"></span>
	</div>
	<div class="zeile">
		<span class="smalfeld"><input type="text" name="shiptoemail" size="22" maxlength="125" value="{shiptoemail}" tabindex="19"></span>
	</div>
	<div class="zeile">
		<span class="smalfeld"><input type="text" name="shiptocontact" size="22" maxlength="75" value="{shiptocontact}" tabindex="20"></span>
	</div>
</span>
<span style="float:both;  text-align:left; border: 0px solid black;">
	<div class="zeile">
		<span >
<!-- BEGIN sonder -->
	<input type="checkbox" name="sonder[]" value="{sonder_id}" {sonder_sel}>{sonder_name} 
<!-- END sonder -->	
		</span>
	</div>
		Bemerkungen<br>
		<textarea name="notes" cols="70" rows="3" tabindex="21">{notes}</textarea><br />
			{Btn1} &nbsp;{Btn2} &nbsp; 
			<input type="submit" name="saveneu" value="sichern neu" tabindex="37"> &nbsp;
			<input type="submit" name="reset" value="clear" tabindex="38"> &nbsp;
			<input type="button" name="" value="VCard" onClick="vcard()" tabindex="39">
</span>
</span>
<span style="float:left; width:270px; height:350px; text-align:left; border: 0px solid black;">
	<div class="zeile">
		<span class="label"></span>
		<span class="smalfeld" style="font-wight:bold; text-align:center;">&nbsp;</span>
	</div>
	<div class="zeile">
		<span class="label">Branche</span>
		<span class="smalfeld"><input type="text" name="branche" size="22" maxlength="25" value="{branche}" tabindex="22"></span>
	</div>
	<div class="zeile">
		<span class="label">Stichwort</span>
		<span class="smalfeld"><input type="text" name="sw" size="22" value="{sw}" maxlength="50" tabindex="23"></span>
	</div>
	<div class="zeile">
		<span class="label">Homepage</span>
		<span class="smalfeld"><input type="text" name="homepage" size="22" maxlength="75" value="{homepage}" tabindex="24"></span>
	</div>
	<div class="zeile">
		<span class="label">UStId</span>
		<span class="smalfeld"><input type="text" name="ustid" size="22" maxlength="15" value="{ustid}" tabindex="25"></span>
	</div>
	<div class="zeile">
		<span class="label">Steuernr.</span>
		<span class="smalfeld"><input type="text" name="taxnumber" size="22" maxlength="15" value="{taxnumber}" tabindex="26"></span>
	</div>
	<div class="zeile">
		<span class="label">Bank</span>
		<span class="smalfeld"><input type="text" name="bank" size="22" maxlength="15" value="{bank}" tabindex="27"></span>
	</div>
	<div class="zeile">
		<span class="label">Blz</span>
		<span class="smalfeld"><input type="text" name="bank_code" size="22" maxlength="15" value="{bank_code}" tabindex="28"></span>
	</div>
	<div class="zeile">
		<span class="label">Konto-Nr</span>
		<span class="smalfeld"><input type="text" name="account_number" size="22" maxlength="15" value="{account_number}" tabindex="29"></span>
	</div>
	<div class="zeile">
		<span class="label">Leadquelle</span>
		<span class="smalfeld">
			<select name="lead" tabindex="30" style="width:110px;">
<!-- BEGIN LeadListe -->
				<option value="{Lid}" {Lsel}>{Lead}</option>
<!-- END LeadListe -->
			</select>
			<input type="text" name="leadsrc" size="5" maxlength="15" value="{leadsrc}" tabindex="30">
		</span>
	</div>
	<div class="zeile">
		<span class="label">Kundentyp</span>
		<span class="smalfeld">
			<select name="business_id" tabindex="32">
<!-- BEGIN TypListe -->
				<option value="{Bid}" {Bsel}>{Btype}</option>
<!-- END TypListe -->
			</select>
		</span>
	</div>
	<div class="zeile">
		<span class="label">Logo</span>
		<span class="smalfeld">{IMG}</span>
	</div>
	<div class="zeile">
		<span style="text-align:center;">
			<center>
			<input type="file" name="Datei" size="14" maxlength="125" accept="Image/*" tabindex="33">
			</center>
		</span>
	</div>

	<div>
		<span class="label">Berechtig.</span>
		<span class="smalfeld">
			<select name="owener" tabindex="34">
<!-- BEGIN OwenerListe -->
				<option value="{grpid}" {Gsel}>{Gname}</option>
<!-- END OwenerListe -->
			</select> &nbsp; {init}
		</span>
	</div>

</span>


<!-- End Code ------------------------------------------->
</span>
</form>
</body>
</html>
			
