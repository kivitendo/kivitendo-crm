<script language="JavaScript">
	function showK__ (id) {
		{no}
		uri="kontakt.php?id=" + id;
		location.href=uri;
	}
    $(document).ready(function() {
        //$( "input[type=button],input[type=submit]" ).button(); 
        $( ".fett_pers" ).click(function() {
            if ( $(this).html() == '#' ) first = '~';
            else first = $(this).html(); 
            $.ajax({
                type: "POST",
                data: "first=" + first + "&andor=and&vendor=on&customer=on",  
                url: "jqhelp/getPersons1.php",
                success: function(res) {
                    $( "#dialog_keine, #dialog_viele, #dialog_no_sw" ).dialog( "close" );
                    if ( !res ) $( "#dialog_keine" ).dialog( "open" );
                    else {
                        $( "#suchfelder_pers" ).hide();
                        $( "#results_pers").html(res); 
                        $( "#results_pers").show();
                    }                       
                }
            });
            return false;
        });           
        $( "#suche_pers" ).button().click(function() {
            $.ajax({
                type: "POST",
                data: $("#formular_pers").serialize() + '&suche=suchen', 
                url: "jqhelp/getPersons1.php",
                success: function(res) {
                    $( "#dialog_keine, #dialog_viele, #dialog_no_sw" ).dialog( "close" );
                    if ( !res ) $( "#dialog_keine" ).dialog( "open" );                    
                    else {
                        $( "#suchfelder_pers" ).hide();
                        $( "#results_pers" ).html(res); 
                        $( "#results_pers" ).show();
                    }                                             
                }
            });
            return false;
        });
        $( "#reset_pers" ).button().click(function() {
            $( "#dialog_keine, #dialog_viele, #dialog_no_sw" ).dialog( "close" );
            $( "#formular_pers" ).find(':input').each(function() {
                switch(this.type) {
                    case 'text':
                        $(this).val('');
                    break;
                    case 'checkbox':
                    case 'radio':
                        this.checked = false
                }
            });
            $( "#andor_pers, #fuzzy_pers, #vendor_pers, #customer_pers" ).click();
            $( "#cp_name" ).focus();
            return false;
        });
        $( "#cp_name" ).focus();
    });
</script>

<div id="suchfelder_pers">

    <p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;"> 
        <button class="fett_pers">A</button> 
        <button class="fett_pers">B</button>     
        <button class="fett_pers">C</button> 
        <button class="fett_pers">D</button> 
        <button class="fett_pers">E</button> 
        <button class="fett_pers">F</button> 
        <button class="fett_pers">G</button> 
        <button class="fett_pers">H</button> 
        <button class="fett_pers">I</button> 
        <button class="fett_pers">J</button> 
        <button class="fett_pers">K</button> 
        <button class="fett_pers">L</button> 
        <button class="fett_pers">M</button> 
        <button class="fett_pers">N</button> 
        <button class="fett_pers">O</button> 
        <button class="fett_pers">P</button> 
        <button class="fett_pers">Q</button> 
        <button class="fett_pers">R</button> 
        <button class="fett_pers">S</button> 
        <button class="fett_pers">T</button> 
        <button class="fett_pers">U</button> 
        <button class="fett_pers">V</button> 
        <button class="fett_pers">W</button> 
        <button class="fett_pers">X</button> 
        <button class="fett_pers">Y</button> 
        <button class="fett_pers">Z</button> 
        <button class="fett_pers">#</button> 
    </p>

    <form name="formular" id="formular_pers" enctype='multipart/form-data' action="#" method="post">
        <input type="hidden" name="FID1" value="{FID1}">
        <input type="hidden" name="first" value="">
        <input type="hidden" name="Quelle" value="{Quelle}">
        <input type="hidden" name="employee" value="{employee}">

        <div class="zeile">
            <span class="label">.:gender:.</span>
            <span class="leftfeld">
                <select name="cp_gender" tabindex="1" style="width:9em;">
                <option value="" {cp_gender}>
                    <option value="m" {cp_genderm}>.:male:.
                    <option value="f" {cp_genderf}>.:female:.
                </select>
            </span>
            <span class="label">.:department:.</span>
            <span class="leftfeld">
                <input type="text" name="cp_abteilung" size="20" maxlength="25" value="{cp_abteilung}" tabindex="12">
            </span>
        </div>
        <div class="zeile">
            <span class="label">.:title:.</span>
            <span class="leftfeld">
                <input type="text" name="cp_title" size="27" maxlength="75" value="{cp_title}" tabindex="2">
            </span>
            <span class="label">.:position:.</span>
	        <span class="leftfeld">
                <input type="text" name="cp_position" size="20" maxlength="25" value="{cp_position}" tabindex="13">
            </span>
        </div>
        <div class="zeile">
            <span class="label">.:givenname:.</span>
            <span class="leftfeld">
                <input type="text" name="cp_givenname" size="27" maxlength="75" value="{cp_givenname}" tabindex="3">
            </span>
            <span class="label">.:Catchword:.</span>
            <span class="leftfeld">
                <input type="text" name="cp_stichwort1" size="25" maxlength="50" value="{cp_stichwort1}" tabindex="14">
            </span>
        </div>
        <div class="zeile">
            <span class="label">.:lastname:.</span>
            <span class="leftfeld">
                <input type="text" name="cp_name" id="cp_name" size="27" maxlength="75" value="{cp_name}" tabindex="4">
            </span>
            <span class="label">.:remark:.</span>
            <span class="leftfeld">
                <input type="text" name="cp_notes" size="25" maxlength="50" value="{cp_notes}" tabindex="15">
            </span>
        </div>
        <div class="zeile">
            <span class="label">.:street:.</span>
            <span class="leftfeld">
                <input type="text" name="cp_street" size="27" maxlength="75" value="{cp_street}" tabindex="5">
            </span>
            <span class="label">.:birthday:.</span>
            <span class="leftfeld">
                <input type="text" name="cp_gebdatum" size="12" maxlength="10" value="{cp_gebdatum}" tabindex="16">
            <span class="klein">TT.MM.JJJJ</span></span>
        </div>
        <div class="zeile">
            <span class="label">.:country:. / .:zipcode:.</span>
			<span class="leftfeld">
                <input type="text" name="cp_country" size="2" maxlength="3" value="{cp_country}" tabindex="6">
				<input type="text" name="cp_zipcode" size="7" maxlength="7" value="{cp_zipcode}" tabindex="7">
			</span>
			<!-- span class="label">Land</span>
			<span class="leftfeld">
                <select name="country" tabindex="10">
			    <!-- BEGIN countries -->
                    <option value="{country}" {country_sel}>{country}</option>
                <!-- END countries -->
                </select>
            </span -->
        </div>
        <div class="zeile">
            <span class="label">Ort</span>
			<span class="leftfeld">
                <input type="text" name="cp_city" size="27" maxlength="75" value="{cp_city}" tabindex="8">
            </span>
            <span class="label">Fax</span>
			<span class="leftfeld">
                <input type="text" name="cp_fax" size="27" maxlength="75" value="{cp_fax}" tabindex="17">
            </span>
		</div>
		<div class="zeile">
            <span class="label">Telefon</span>
			<span class="leftfeld">
                <input type="text" name="cp_phone1" size="27" maxlength="75" value="{cp_phone1}" tabindex="9">
            </span>
			<span class="label">eMail</span>
				<span class="leftfeld"><input type="text" name="cp_email" size="27" maxlength="75" value="{cp_email}" tabindex="18">
            </span>
		</div>
        <div class="zeile">
            <span class="label">Mobiltelefon</span>
            <span class="leftfeld">
                <input type="text" name="cp_phone2" size="27" maxlength="75" value="{cp_phone2}" tabindex="10">
            </span>
            <span class="label">www</span>
            <span class="leftfeld">
                <input type="text" name="cp_homepage" size="27" maxlength="25" value="{cp_homepage}" tabindex="19">
            </span>
		</div>
        <div class="zeile">
			<span class="label">Firmenname</span>
			<span class="leftfeld">
                <input type="text" name="customer_name" size="27" maxlength="75" value="{customer_name}" tabindex="11">
            </span>
			<!-- span class="label">Kundentyp</span>
			<span class="leftfeld">
                <input type="text" name="business_description" size="27" maxlength="75" value="{business_description}" tabindex="20">
            </span -->
        </div>
        <div class="zeile"> 
            <span class="klein">
                .:search:. <input type="radio" name="andor" id="andor_pers" value="and" checked tabindex="40">
                .:all:.    <input type="radio" name="andor" value="or" tabindex="40">.:some:.
            </span><br>
            <input type="checkbox" name="fuzzy" id="fuzzy_pers" value="%" checked>
            <span class="klein">Unscharf suchen&nbsp;&nbsp;</span><input type="checkbox" name="vendor" id="vendor_pers" checked>
            <span class="klein">Lieferanten</span><input type="checkbox" name="customer" id="customer_pers" checked>
            <span class="klein">Kunden</span><br><input type="checkbox" name="deleted">
            <span class="klein">.:deletetcontact:.</span><br>
			{Btn1} {Btn3} 
			<button id="suche_pers" >suchen</button> 
			<button id="reset_pers" >clear</button>
			<a href="extrafelder.php?owner=P0"><img src="image/extra.png" alt="Extras" title="Extras" border="0" /></a>
        </div>
		<div style="margin-left:2.5em; float:left; border: 0px solid black;">
        <!-- Gibt es hier die Möglichkeit eine Fallentscheidung zu machen?  Falls sonder dann einblenden:-->
            Attribute:
            <!-- BEGIN sonder -->
	        <input class="klein" type="checkbox" name="cp_sonder[]" value="{sonder_id}"><span class="klein">{sonder_key}</span><br>
            <!-- END sonder -->		
        </div>	
    </form>
</div>





