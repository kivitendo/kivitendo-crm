<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript" type="text/javascript">
  		function report() {
  			f1=open("report.php?tab={Q}","Report","width=600; height=300; left=100; top=100");
  		}
		function surfgeo() {
			if ({GEODB}) {
				fuzzy=(document.erwsuche.fuzzy.checked==true)?1:0;
				plz=document.erwsuche.zipcode.value;
				ort=document.erwsuche.city.value;
				tel=document.erwsuche.phone.value;
				F1=open("surfgeodb.php?ao=and&plz="+plz+"&ort="+ort+"&tel="+tel+"&fuzzy="+fuzzy,"GEO","width=550, height=350, left=100, top=50, scrollbars=yes");
			} else {
				alert("GEO-Datenbank nicht aktiviert");
			}
		}
	</script>
<body onLoad="document.erwsuche.name.focus();">

<form name="erwsuche" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="felder" value="">
<input type="hidden" name="Q" value="{Q}">
<p class="listtop">Company search {FAART}</p>
<span style="position:absolute; left:1em; top:3.0em; border: 0px solid black;">
<!-- Beginn Code ------------------------------------------->
<p class="listheading">| 
<a href="{action}&first=A" class="fett">A</a> |
<a href="{action}&first=B" class="fett">B</a> |
<a href="{action}&first=C" class="fett">C</a> |
<a href="{action}&first=D" class="fett">D</a> |
<a href="{action}&first=E" class="fett">E</a> |
<a href="{action}&first=F" class="fett">F</a> |
<a href="{action}&first=G" class="fett">G</a> |
<a href="{action}&first=H" class="fett">H</a> |
<a href="{action}&first=I" class="fett">I</a> |
<a href="{action}&first=J" class="fett">J</a> |
<a href="{action}&first=K" class="fett">K</a> |
<a href="{action}&first=L" class="fett">L</a> |
<a href="{action}&first=M" class="fett">M</a> |
<a href="{action}&first=N" class="fett">N</a> |
<a href="{action}&first=O" class="fett">O</a> |
<a href="{action}&first=P" class="fett">P</a> |
<a href="{action}&first=Q" class="fett">Q</a> |
<a href="{action}&first=R" class="fett">R</a> |
<a href="{action}&first=S" class="fett">S</a> |
<a href="{action}&first=T" class="fett">T</a> |
<a href="{action}&first=U" class="fett">U</a> |
<a href="{action}&first=V" class="fett">V</a> |
<a href="{action}&first=W" class="fett">W</a> |
<a href="{action}&first=X" class="fett">X</a> |
<a href="{action}&first=Y" class="fett">Y</a> |
<a href="{action}&first=Z" class="fett">Z</a> |
<a href="{action}&first=~" class="fett">*</a> |</p>
	<div class="zeile">
		<span class="label">Company Name</span>
		<span class="leftfeld"><input type="text" name="name" size="27" maxlength="75" value="{name}" tabindex="1"></span>
		<span class="label">Industry</span>
		<span class="leftfeld"><input type="text" name="branche" size="27" maxlength="25" value="{branche}" tabindex="21"></span>
	</div>
	<div class="zeile">
		<span class="label">Department</span>
		<span class="leftfeld"><input type="text" name="department_1" size="27" maxlength="75" value="{department_1}" tabindex="2"></span>
		<span class="label">Catchword</span>
		<span class="leftfeld"><input type="text" name="sw" size="27" maxlength="125" value="{sw}" tabindex="22"></span>
	</div>
	<div class="zeile">
		<span class="label">Street</span>
		<span class="leftfeld"><input type="text" name="street" size="27" maxlength="75" value="{street}" tabindex="3"></span>
		<span class="label">Remarks</span>
		<span class="leftfeld"><input type="text" name="notes" size="27" maxlength="125" value="{notes}" tabindex="23"></span>
	</div>
	<div class="zeile">
		<span class="label">Country / Zipcode</span>
		<span class="leftfeld"><input type="text" name="country" size="2" maxlength="5" value="{country}" tabindex="4"> / 
					<input type="text" name="zipcode" size="7" maxlength="7" value="{zipcode}" tabindex="5"></span>
		<span class="label">Bank</span>
		<span class="leftfeld"><input type="text" name="bank" size="27" maxlength="50" value="{bank}" tabindex="24"></span>
	</div>
	<div class="zeile">
		<span class="label">City</span>
		<span class="leftfeld"><input type="text" name="city" size="27" maxlength="75" value="{city}" tabindex="6"></span>
		<span class="label">Bank code</span>
		<span class="leftfeld"><input type="text" name="bank_code" size="27" maxlength="15" value="{bank_code}" tabindex="26"></span>
	</div>
	<div class="zeile">
		<span class="label">Phone</span>
		<span class="leftfeld"><input type="text" name="phone" size="27" maxlength="75" value="{phone}" tabindex="7"></span>
		<span class="label">Account</span>
		<span class="leftfeld"><input type="text" name="account_number" size="27" maxlength="15" value="{account_number}" tabindex="27"></span>
	</div>
	<div class="zeile">
		<span class="label">Fax</span>
		<span class="leftfeld"><input type="text" name="fax" size="27" maxlength="125" value="{fax}" tabindex="8"></span>
		<span class="label">UStID</span>
		<span class="leftfeld"><input type="text" name="ustid" size="27" maxlength="12" value="{ustid}" tabindex="28"></span>
	</div>
	<div class="zeile">
		<span class="label">eMail</span>
		<span class="leftfeld"><input type="text" name="email" size="27" maxlength="125" value="{email}" tabindex="9"></span>
		<span class="label">www</span>
		<span class="leftfeld"><input type="text" name="homepage" size="27" maxlength="125" value="{homepage}" tabindex="29"></span>
	</div>
	<div class="zeile">
		<span class="label">Business</span>
		<span class="leftfeld">
			<select name="business_id" tabindex="10">
<!-- BEGIN TypListe -->	
				<option value="{Bid}" {Bsel}>{Btype}</option>
<!-- END TypListe -->				
			</select>
		</span>
		<span class="label">Language</span>
		<span class="leftfeld">	<select name="language" tabindex="30">
				<option value="">
				<option value="de">deutsch
				<option value="en">englisch
				<option value="fr">franz&ouml;sisch
			</select>
		</span>
	</div>
	<div class="zeile">
		<span class="label">Leadsource</span>
		<span class="leftfeld">
			<select name="lead" tabindex="11" style="width:110px;">
<!-- BEGIN LeadListe -->	
				<option value="{Lid}" {Lsel}>{Lead}</option>
<!-- END LeadListe -->				
			</select>
			<input type="text" name="leadsrc" size="5" value="{leadsrc}" tabindex="12">
		</span>
		<span class="label">KdNr.</span>
		<span class="leftfeld"><input type="text" name="customernumber" size="27" maxlength="15" value="{customernumber}" tabindex="32"></span>

	</div>
	<div class="zeile">
		<span class="label">Special Flag</span>
<!-- BEGIN sonder -->
	<input type="checkbox" name="sonder[]" tabindex="33" value="{sonder_id}">{sonder_name} 
<!-- END sonder -->	
	</div>
	<div class="zeile">
			<b>{Msg}</b><br>
			<input type="checkbox" name="shipto" value="1" checked tabindex="40">also in Shipto<br>
			<input type="checkbox" name="fuzzy" value="%" checked tabindex="41">fuzzy search <input type="checkbox" name="pre" value="1">with prefix<br>
			<input type="checkbox" name="employee" value="{employee}" tabindex="42">only by own<br>
			<input type="submit" class="anzeige" name="suche" value="search_" tabindex="43">&nbsp;
			<input type="submit" class="clear" name="reset" value="clear_" tabindex="44"> &nbsp;
			<input type="button" name="rep" value="Report" onClick="report()" tabindex="45"> &nbsp;
			<input type="button" name="geo" value="GeoDB" onClick="surfgeo()" tabindex="46" style="visibility:{GEOS}"> &nbsp;
			<br>
			{report}
	</div>
</form>
<!-- End Code ------------------------------------------->
</span>
</body>
</html>

