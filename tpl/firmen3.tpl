<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<link type="text/css" REL="stylesheet" HREF="css/tabcontent.css"></link>
	{AJAXJS}
	<script language="JavaScript">
	<!--
	function vcard() {
			f1=open("vcard.php?src=F","vcard","width=350,height=200,left=100,top=100");
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
	function mkBuland(tab) {
		if (tab=="bland") {
			L=document.neueintrag.country.value
		} else {
			L=document.neueintrag.shiptocountry.value
		};
		xajax_Buland(L,tab);
	}
	function getShipadress() {
		x=document.neueintrag.shiptoadress.selectedIndex;
		if (x>0) {
			y=document.neueintrag.shiptoadress.options[x].value;
			xajax_getShipto(y)
		} else {
			document.neueintrag.shipto_id.value="";
			document.neueintrag.shiptoname.value="";
			document.neueintrag.shiptodepartment_1.value="";
			document.neueintrag.shiptodepartment_2.value="";
			document.neueintrag.shiptostreet.value="";
			document.neueintrag.shiptocountry.value="";
			document.neueintrag.shiptozipcode.value="";
			document.neueintrag.shiptocity.value="";
			document.neueintrag.shiptophone.value="";
			document.neueintrag.shiptofax.value="";
			document.neueintrag.shiptoemail.value="";
			document.neueintrag.shiptocontact.value="";
			document.neueintrag.shiptobland.options[0].selected=true;
		}
	}
	function suchFa() {
			val=document.neueintrag.konzernname.value;
			if (val=="") val="%";
			f1=open("suchFa.php?tab={Q}&konzernname="+val,"suche","width=350,height=200,left=100,top=100");
		}
	//-->
	</script>
<body onLoad="submenu('tab1'); document.neueintrag.name.focus();">

<p class="listtop"> {FAART} eingeben/editieren</p>

<!-- Beginn Code ------------------------------------------->
<div style="position:absolute; top:2.8em; left:1em;  width:45em;">
	<ul id="maintab" class="shadetabs">
	<li id="subtab1" ><a href="#" onClick="submenu('tab1')">Rechnungsanschrift</a></li>
	<li id="subtab2" ><a href="#" onClick="submenu('tab2')">Lieferanschrift</a></li>
	<li id="subtab3" ><a href="#" onClick="submenu('tab3')">Sonstiges</a></li>
	<li>{Msg}
	</ul>
</div>
<form name="neueintrag" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="id" value="{id}">
<input type="hidden" id="shipto_id" name="shipto_id" value="{shipto_id}">
<input type="hidden" name="customernumber" value="{customernumber}">
<input type="hidden" name="employee" value="{employee}">
<input type="hidden" name="grafik" value="{grafik}">
<span id="tab1" style="visibility:visible; position:absolute; text-align:left;width:90%; left:0.8em; top:4.3em; border:1px solid black;">
	<div class="zeile2">
		<span class="label klein">Anrede </span>
		<span class="feldxx"> <input type="text" name="greeting_" size="15" maxlength="75" value="{greeting_}" tabindex="1">
				<select name="greeting" tabindex="2">
					<option value="">
<!-- BEGIN anreden -->
					<option value="{ANREDE}" {ASEL}>{ANREDE}
<!-- END anreden -->
				</select>
		</span>
	</div>
	<div class="zeile2">
		<span class="label klein">Firmenname </span>
		<span class="feldxx"> <input type="text" name="name" size="35" maxlength="75" value="{name}" tabindex="3"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Abteilung 1</span>
		<span class="feldxx"><input type="text" name="department_1" size="35" maxlength="75" value="{department_1}" tabindex="4"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Abteilung 2</span>
		<span class="feldxx"><input type="text" name="department_2" size="35" maxlength="75" value="{department_2}" tabindex="5"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Strasse</span>
		<span class="feldxx"><input type="text" name="street" size="35" maxlength="75" value="{street}" tabindex="6"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Land / Plz</span>
		<span class="feldxx">
			<input type="text" name="country" size="2" maxlength="75" value="{country}" tabindex="7" onBlur="mkBuland('bland')">/
			<input type="text" name="zipcode" size="5" maxlength="10" value="{zipcode}" tabindex="8">
			<select name="bland" id="bland" tabindex="9" style="width:150px;">
				<option value=""></option>
<!-- BEGIN buland -->
				<option value="{BUVAL}" {BUSEL}>{BUTXT}</option>
<!-- END buland -->
			</select>
		</span>
	</div>
	<div class="zeile2">
		<span class="label klein">Ort</span>
		<span class="feldxx"><input type="text" name="city" size="35" maxlength="75" value="{city}" tabindex="10"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Telefon</span>
		<span class="feldxx"><input type="text" name="phone" size="35" maxlength="30" value="{phone}" tabindex="11"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Fax</span>
		<span class="feldxx"><input type="text" name="fax" size="35" maxlength="30" value="{fax}" tabindex="12"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">eMail</span>
		<span class="feldxx"><input type="text" name="email" size="35" maxlength="125" value="{email}" tabindex="13"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Kontakt</span>
		<span class="feldxx"><input type="text" name="contact" size="35" maxlength="125" value="{contact}" tabindex="14"></span>
	</div>
	<div class="zeile2">
		<span class="klein">Bemerkungen</span><br>
		<textarea name="notes" cols="70" rows="3" tabindex="15">{notes}</textarea><br />
	</div>
	<span style="position:absolute; left:35em; top:3em;text-align:left;">
		<div class="zeile2">
			<span class="labelxx klein">Logo</span>
			<span class="feldxx">
				<input type="file" name="Datei" size="20" maxlength="125" accept="Image/*" tabindex="16">
			</span><br><br>
			<span class="feldxx">
			{IMG}
			</span>
		</div>
	</span>

</span>
<!-- Ende tab1 -->
<span id="tab2" style="visibility:hidden;  position:absolute; text-align:left;width:90%; left:0.8em; top:4.3em; border:1px solid black;">
	<br>
	<div class="zeile2">
		<span class="label klein"></span>
		<span class="feldxx"><select name="shiptoadress" style="width:19em;" tabindex="1" onChange="getShipadress();">
				<option value=""></option>
<!-- BEGIN shiptos -->
				<option value="{SHIPID}">{SHIPTO}</option>
<!-- END shiptos -->
		</select></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Firmenname</span>
		<span class="feldxx"><input type="text" id="shiptoname" name="shiptoname" size="35" maxlength="75" value="{shiptoname}" tabindex="2"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Abteilung 1</span>
		<span class="feldxx"><input type="text" id="shiptodepartment_1" name="shiptodepartment_1" size="35" maxlength="75" value="{shiptodepartment_1}" tabindex="3"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Abteilung 2</span>
		<span class="feldxx"><input type="text" id="shiptodepartment_2" name="shiptodepartment_2" size="35" maxlength="75" value="{shiptodepartment_2}" tabindex="4"></span
	</div>
	<div class="zeile2">
		<span class="label klein">Strasse</span>
		<span class="feldxx"><input type="text" id="shiptostreet" name="shiptostreet" size="35" maxlength="75" value="{shiptostreet}" tabindex="5"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Land / Plz</span>
		<span class="feldxx">
			<input type="text" id="shiptocountry" name="shiptocountry" size="2" value="{shiptocountry}" tabindex="6" onBlur="mkBuland('shiptobland'ch);">/
			<input type="text" id="shiptozipcode" name="shiptozipcode" size="5" maxlength="10" value="{shiptozipcode}" tabindex="7">
			<select id="shiptobland" name="shiptobland" tabindex="8" style="width:12em;">
				<option value=""></option>
<!-- BEGIN buland2 -->
				<option value="{SBUVAL}" {SBUSEL}>{SBUTXT}</option>
<!-- END buland2 -->
			</select>
		</span>
	</div>
	<div class="zeile2">
		<span class="label klein">Ort</span>
		<span class="feldxx"><input type="text" id="shiptocity" name="shiptocity" size="35" maxlength="75" value="{shiptocity}" tabindex="9"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Telefon</span>
		<span class="feldxx"><input type="text" id="shiptophone" name="shiptophone" size="35" maxlength="30" value="{shiptophone}" tabindex="10"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Fax</span>
		<span class="feldxx"><input type="text" id="shiptofax" name="shiptofax" size="35" maxlength="30" value="{shiptofax}" tabindex="11"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">eMail</span>
		<span class="feldxx"><input type="text" id="shiptoemail" name="shiptoemail" size="35" maxlength="125" value="{shiptoemail}" tabindex="12"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Kontakt</span>
		<span class="feldxx"><input type="text" id="shiptocontact" name="shiptocontact" size="35" maxlength="75" value="{shiptocontact}" tabindex="13"></span>
	</div>
	<br><br>
	<br><br>
</span>
<!-- Ende tab2 -->
<span id="tab3" style="visibility:hidden;  position:absolute; text-align:left;width:90%; left:0.8em; top:4.3em; border:1px solid black; display:inline;">
	<div class="zeile2">
		<span class="label klein">Branche</span>
		<span class="feldxx"><input type="text" name="branche_" size="15" maxlength="25" value="{branche_}" tabindex="1">
				<select name="branche" tabindex="2" style="width:11em;">
					<option value="">
<!-- BEGIN branchen -->
					<option value="{BRANCHE}" {BSEL}>{BRANCHE}
<!-- END branchen -->
				</select>
		</span>
	</div>
	<div class="zeile2">
		<span class="label klein">Stichwort</span>
		<span class="feldxx"><input type="text" name="sw" size="35" value="{sw}" maxlength="50" tabindex="3"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Homepage</span>
		<span class="feldxx"><input type="text" name="homepage" size="35" maxlength="75" value="{homepage}" tabindex="4"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">UStId</span>
		<span class="feldxx"><input type="text" name="ustid" size="35" maxlength="15" value="{ustid}" tabindex="5"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Steuernr.</span>
		<span class="feldxx"><input type="text" name="taxnumber" size="35" maxlength="35" value="{taxnumber}" tabindex="6"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Bank</span>
		<span class="feldxx"><input type="text" name="bank" size="35" maxlength="55" value="{bank}" tabindex="7"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Blz</span>
		<span class="feldxx"><input type="text" name="bank_code" size="35" maxlength="10" value="{bank_code}" tabindex="8"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Konto-Nr</span>
		<span class="feldxx"><input type="text" name="account_number" size="35" maxlength="15" value="{account_number}" tabindex="9"></span>
	</div>
	<div class="zeile2">
		<span class="label klein">Leadquelle</span>
		<span class="feldxx">
			<select name="lead" tabindex="10" style="width:10em;">
<!-- BEGIN LeadListe -->
				<option value="{Lid}" {Lsel}>{Lead}</option>
<!-- END LeadListe -->
			</select>
			<input type="text" name="leadsrc" size="15" maxlength="15" value="{leadsrc}" tabindex="11">
		</span>
	</div>
	<div class="zeile2">
		<span class="label klein">Kundentyp</span>
		<span class="feldxx">
			<select name="business_id" tabindex="12">
<!-- BEGIN TypListe -->
				<option value="{Bid}" {Bsel}>{Btype}</option>
<!-- END TypListe -->
			</select>
		</span>
	</div>
	<div class="zeile2">
		<span class="label klein">Steuerzone</span>
		<span class="feldxx">
			<select name="taxzone_id" tabindex="13">
				<option value="0" {txid0}>Inland
				<option value="1" {txid1}>EU mit UStID
				<option value="2" {txid2}>EU ohne UStID
				<option value="3" {txid3}>Ausland
			</select> 
		</span>
	</div>
	<div class="zeile2">
		<span class="label klein">Konzern</span>
		<input type="hidden" name="konzern" value="{konzern}">
		<span class="feldxx"><input type="text" name="konzernname" size="30" value="{konzernname}" maxlength="50" tabindex="14">{konzern}<input type="button" name="suche" value="suchen" onClick="suchFa();"></span>
	</div>

	<div class="zeile2">
		<span class="label klein">Verk&auml;ufer</span>
		<span class="feldxx">
			<select name="salesman_id" tabindex="15">
				<option value=""></option>
<!-- BEGIN SalesmanListe -->
				<option value="{salesmanid}" {Ssel}>{Salesman}</option>
<!-- END SalesmanListe -->
			</select>
		</span>
	</div>
	<div class="zeile2">
		<span class="label klein">Berechtig.</span>
		<span class="feldxx">
			<select name="owener" tabindex="16">
<!-- BEGIN OwenerListe -->
				<option value="{grpid}" {Gsel}>{Gname}</option>
<!-- END OwenerListe -->
			</select> &nbsp; <span class="klein">{init}</span>
		</span>
	</div>
	<div class="zeile2">
<!-- BEGIN sonder -->
	<input type="checkbox"  name="sonder[]" value="{sonder_id}" {sonder_sel} tabindex="17"><span class="klein">{sonder_name} </span>
<!-- END sonder -->	
	</div>
</span>
<span id="tab4" style="position:absolute; text-align:left;width:48%; left:0.8em; top:32em;"> 			
			{Btn1} &nbsp;{Btn2} &nbsp; 
			<input type="submit" name="saveneu" value="sichern neu" tabindex="37"> &nbsp;
			<input type="submit" name="reset" value="clear" tabindex="38"> &nbsp;
			<input type="button" name="" value="VCard" onClick="vcard()" tabindex="39">
</span>
<!-- End Code ------------------------------------------->
</span>
</form>
</body>
</html>
			
