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
	   } 
	   else alert(".:noGEOdb:.");
    }
    
    $(document).ready(function() {
        $( "input[type=button],input[type=submit]" ).button();
        $( ".fett" ).click(function() {
            if ( $(this).html() == '#' ) first = '~';
            else first = $(this).html(); 
            $.ajax({
                type: "POST",
                data: 'first=' + first + '&Q={Q}', 
                url: "jqhelp/getCompanies1.php",
                success: function(res) {
                    $( "#dialog_keine #dialog_viele #dialog_no_sw" ).dialog( "close" );
                    //ToDo: if ( res == 'keine' )
                    //if ( !res ) in der nächten Pension....
                    $( "#suchfelder_{Q}" ).hide();
                    $( "#companyResults_{Q}").html(res); 
                    $( "#companyResults_{Q}").show();                       
                }
            });
            return false;
        });
        $( "#suchbutton_{Q}" ).click(function() {
            $.ajax({
                type: "POST",
                data: $("#erwsuche_{Q}").serialize() + '&suche=suche', 
                url: "jqhelp/getCompanies1.php",
                success: function(res) {
                    $( "#dialog_keine #dialog_viele #dialog_no_sw" ).dialog( "close" );
                    if( res ) {
                        $( "#dialog_keine" ).dialog( "close" );
                        $( "#suchfelder_{Q}" ).hide();
                        $( "#companyResults_{Q}" ).html(res); 
                        $( "#companyResults_{Q}" ).show();
                    }
                    else {
                        $("#dialog_keine").dialog( "open"); 
                        $( "#name{Q}" ).focus();
                    }                                              
                }
            });
            return false;
        });
        $( "#reset_{Q}" ).click(function() {
        //Kein leeres Template laden, da sonst die IDs doppelt vergeben werden!
            $( "#dialog_keine #dialog_viele #dialog_no_sw" ).dialog( "close" );
            $( "#erwsuche_{Q}" ).find(':input').each(function() {
                switch(this.type) {
                    case 'text':
                        $(this).val('');
                    break;
                    case 'checkbox':
                    case 'radio':
                        this.checked = false
                }
            });
            $( "#andor{Q}, #shipto{Q}, #fuzzy{Q}, #pre{Q}, #obsolete{Q}" ).click();
            $( "#name{Q}" ).focus();
            return false;
        });
        $( "#name{Q}" ).focus();
        
    });	
</script>
<script type='text/javascript' src='inc/help.js'></script>



<div id="suchfelder_{Q}" >
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">  
<button class="fett">A</button> 
<button class="fett">B</button> 
<button class="fett">C</button> 
<button class="fett">D</button> 
<button class="fett">E</button> 
<button class="fett">F</button> 
<button class="fett">G</button> 
<button class="fett">H</button> 
<button class="fett">I</button> 
<button class="fett">J</button> 
<button class="fett">K</button> 
<button class="fett">L</button> 
<button class="fett">M</button> 
<button class="fett">N</button> 
<button class="fett">O</button> 
<button class="fett">P</button> 
<button class="fett">Q</button> 
<button class="fett">R</button> 
<button class="fett">S</button> 
<button class="fett">T</button> 
<button class="fett">U</button> 
<button class="fett">V</button> 
<button class="fett">W</button> 
<button class="fett">X</button> 
<button class="fett">Y</button> 
<button class="fett">Z</button> 
<button class="fett">#</button> 
</p>

<form name="erwsuche" id="erwsuche_{Q}" enctype='multipart/form-data' action="#" method="post">
<input type="hidden" name="felder" value="">
<input type="hidden" name="Q" value="{Q}">

<!-- Beginn Code ------------------------------------------>

	<div class="zeile">
		<span class="label">.:KdNr:.</span>
		<span class="leftfeld"><input type="text" name="customernumber" size="27" maxlength="15" value="{customernumber}" tabindex="1"></span>
		<span class="label">.:Contact:.</span>
		<span class="leftfeld"><input type="text" name="contact" size="27" maxlength="25" value="{contact}" tabindex="21"></span>
	</div>
	<div class="zeile">
		<span class="label">{FAART2}</span>
		<span class="leftfeld"><input type="text" name="name" id="name{Q}" size="27" maxlength="75" value="{name}" tabindex="1"></span>
		<span class="label">.:Industry:.</span>
		<span class="leftfeld"><input type="text" name="branche" size="27" maxlength="25" value="{branche}" tabindex="21"></span>
	</div>
	<div class="zeile">
		<span class="label">.:department:.</span>
		<span class="leftfeld"><input type="text" name="department_1" size="27" maxlength="75" value="{department_1}" tabindex="2"></span>
		<span class="label">.:Catchword:.</span>
		<span class="leftfeld"><input type="text" name="sw" size="27" maxlength="125" value="{sw}" tabindex="22"></span>
	</div>
	<div class="zeile">
		<span class="label">.:street:.</span>
		<span class="leftfeld"><input type="text" name="street" size="27" maxlength="75" value="{street}" tabindex="3"></span>
		<span class="label">.:Remarks:.</span>
		<span class="leftfeld"><input type="text" name="notes" size="27" maxlength="125" value="{notes}" tabindex="23"></span>
	</div>
	<div class="zeile">
		<span class="label">.:country:. / .:zipcode:.</span>
		<span class="leftfeld"><input type="text" name="country" size="2" maxlength="5" value="{country}" tabindex="4"> / 
					<input type="text" name="zipcode" size="7" maxlength="15" value="{zipcode}" tabindex="5"></span>
		<span class="label">.:bankname:.</span>
		<span class="leftfeld"><input type="text" name="bank" size="27" maxlength="50" value="{bank}" tabindex="24"></span>
	</div>
	<div class="zeile">
		<span class="label">.:city:.</span>
		<span class="leftfeld"><input type="text" name="city" size="27" maxlength="75" value="{city}" tabindex="6"></span>
		<span class="label">.:bankcode:.</span>
		<span class="leftfeld"><input type="text" name="bank_code" size="27" maxlength="25" value="{bank_code}" tabindex="26"></span>
	</div>
	<div class="zeile">
		<span class="label">.:phone:.</span>
		<span class="leftfeld"><input type="text" name="phone" size="27" maxlength="75" value="{phone}" tabindex="7"></span>
		<span class="label">.:account:.</span>
		<span class="leftfeld"><input type="text" name="account_number" size="27" maxlength="25" value="{account_number}" tabindex="27"></span>
	</div>
	<div class="zeile">
		<span class="label">.:fax:.</span>
		<span class="leftfeld"><input type="text" name="fax" size="27" maxlength="125" value="{fax}" tabindex="8"></span>
		<span class="label">UStID</span>
		<span class="leftfeld"><input type="text" name="ustid" size="27" maxlength="12" value="{ustid}" tabindex="28"></span>
	</div>
	<div class="zeile">
		<span class="label">.:email:.</span>
		<span class="leftfeld"><input type="text" name="email" size="27" maxlength="125" value="{email}" tabindex="9"></span>
		<span class="label">www</span>
		<span class="leftfeld"><input type="text" name="homepage" size="27" maxlength="125" value="{homepage}" tabindex="29"></span>
	</div>
	<div class="zeile">
		<span class="label">.:Business:.</span>
		<span class="leftfeld">
			<select name="business_id" tabindex="10">
<!-- BEGIN TypListe -->	
				<option value="{BTid}" {BTsel}>{BTtext}</option>
<!-- END TypListe -->				
			</select>
		</span>
		<span class="label">.:lang:.</span>
		<span class="leftfeld">	<select name="language_id" tabindex="30">
				<option value="">
<!-- BEGIN LAnguage -->	
				<option value="{LAid}" {LAsel}>{LAtext}
<!-- END LAnguage -->	
			</select>
		</span>
	</div>
	<div class="zeile">
		<span class="label">.:leadsource:.</span>
		<span class="leftfeld">
			<select name="lead" tabindex="11" style="width:110px;">
<!-- BEGIN LeadListe -->	
				<option value="{LLid}" {LLsel}>{LLtext}</option>
<!-- END LeadListe -->				
			</select>
			<input type="text" name="leadsrc" size="5" value="{leadsrc}" tabindex="12">
		</span>
		<span class="label">.:headcount:.</span>
		<span class="leftfeld"><input type="text" name="headcount" size="7" maxlength="7" value="{headcount}" tabindex="32"></span>
	</div>
	<div class="zeile">
        <span class="label">.:sales volume:.</span>
        <span class="leftfeld"><input type="text" name="umsatz" size="7" maxlength="25" value="{umsatz}" tabindex="32"> .:year:. 
			<select name="year" tabindex="11" >
<!-- BEGIN YearListe -->	
				<option value="{YLid}" {YLsel}>{YLtext}</option>
<!-- END YearListe -->				
			</select></span>
	</div>
<!-- BEGIN cvarListe -->	
	<div class="zeile">
		<span class="label">{varlable1}</span>
		<span class="leftfeld">{varfld1}</span>
		<span class="label">{varlable2}</span>
		<span class="leftfeld">{varfld2}</span>
	</div>
<!-- END cvarListe -->	
	<div class="zeile">
                        <br>
			<b>{Msg}</b><br>  
			.:search:. <input type="radio" name="andor"  id="andor{Q}" value="and" checked tabindex="40">.:all:. <input type="radio" name="andor" value="or" tabindex="40">.:some:.<br>
			<input type="checkbox" name="shipto" id="shipto{Q}" value="1" checked tabindex="40">.:also in:. .:shipto:.<br>
			<input type="checkbox" name="fuzzy" id="fuzzy{Q}" value="%" checked tabindex="41">.:fuzzy search:. <input type="checkbox" name="pre" id="pre{Q}" value="1" {preon}>.:with prefix:.<br>
			<input type="checkbox" name="employee" value="{employee}" tabindex="42">.:only by own:.<br>
			.:obsolete:. <input type="radio" name="obsolete" value="t" >.:yes:. <input type="radio" name="obsolete" value="f" >.:no:.  <input type="radio" name="obsolete" id="obsolete{Q}" value="" checked >.:equal:.<br>
			<input type="submit" class="anzeige" name="suchbutton" id="suchbutton_{Q}" value=".:search:." tabindex="43">&nbsp;
			<input type="submit" class="clear" name="reset" id="reset_{Q}" value=".:clear:." tabindex="44"> &nbsp;
			<input type="button" name="rep" value="Report" onClick="report()" tabindex="45"> &nbsp;
			<input type="button" name="geo" value="GeoDB" onClick="surfgeo()" tabindex="46" style="visibility:{GEOS}"> &nbsp;
            <a href="extrafelder.php?owner={Q}0"><img src="image/extra.png" alt="Extras" title="Extras" border="0" /></a>
			<br>
			{report}
	</div>
</form>
</div>
<div id="companyResults_{Q}"></div>

