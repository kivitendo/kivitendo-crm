<html>
<head><title></title>
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{THEME}
{JAVASCRIPTS}

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
    $(document).ready(
        function(){
            $( "#maintab" ).tabs({ heightStyle: "auto" });
            $( "#cp_birthday" ).datepicker($.datepicker.regional[ "de" ]);
            $(function(){
                $("#company_name").autocomplete({                          
                    source: "jqhelp/autocompletion.php?case=name&src=cv",                            
                    minLength: '2',
                    select: function(e,ui) {               
                        $("#cp_cv_id").val(ui.item.id);
                    }
                });
            });
        });
    //-->
    </script>
<body onLoad="goFld();">
{PRE_CONTENT}
{START_CONTENT}
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">.:persons:. .:keyin:./.:edit:.</p>

<!-- Beginn Code ------------------------------------------->
<div id="maintab">
    <ul>
    <li><a href="#tab1">.:person:.</a></li>
    <li><a href="#tab2">.:Company:.</a></li>
    <li><a href="#tab3">.:misc:.</a></li>
    </ul>

    <form name="formular" enctype='multipart/form-data' action="{action}" method="post">
    <input type="hidden" name="PID" value="{PID}">
    <input type="hidden" name="mtime" value="{mtime}">
    <input type="hidden" name="FID1" value="{FID1}">
    <input type="hidden" name="Quelle" value="{Quelle}">
    <input type="hidden" name="employee" value="{employee}">
    <input type="hidden" name="IMG_" value="{IMG_}">
    <input type="hidden" name="nummer" value="{nummer}">
    <input type="hidden" name="cp_cv_id" id="cp_cv_id" size="7" maxlength="10" value="{FID}" tabindex="32">

    <span id="tab1">
        <div class="zeile2">
            <span class="label2 klein">.:gender:.</span>
            <span class="feld">
                    <select name="cp_gender" tabindex="2" style="width:9em;">
                        <option value="m" {cp_genderm}>.:male:.
                        <option value="f" {cp_genderf}>.:female:.
                    </select>
            </span>
            <span class="label klein">.:phone:. 1</span>
            <span class="feld"><input type="text" id="phone" name="cp_phone1" size="25" maxlength="75" value="{cp_phone1}" tabindex="12"></span>
        </div>
        <div class="zeile2">
            <span class="label2 klein">.:salutation:.</span>
            <span class="feld"><select name="cp_salutation" tabindex="3" style="width:15em;">
                        <option value="">
<!-- BEGIN briefanred -->
                        <option value="{BAid}" {BAsel}>{BAtext}
<!-- END briefanred -->
                    </select></span>
            <span class="label klein">2</span>
            <span class="feld"><input type="text" name="cp_phone2" size="25" maxlength="75" value="{cp_phone2}" tabindex="12"></span>
        </div>
        <div class="zeile2">
            <span class="label2 klein"></span>
            <span class="feld"><input type="text" name="cp_salutation_" size="25" maxlength="125" value="{cp_salutation_}" tabindex="5"></span>
            <span class="label klein">.:mobile:. 1</span>
            <span class="feld"><input type="text" name="cp_mobile1" size="25" maxlength="75" value="{cp_mobile1}" tabindex="13"></span>
        </div>
        <div class="zeile2">
            <span class="label2 klein">.:title:.</span>
            <span class="feld"><input type="text" name="cp_title" size="25" maxlength="75" value="{cp_title}" tabindex="5"></span>
            <span class="label klein">2</span>
            <span class="feld"><input type="text" name="cp_mobile2" size="25" maxlength="75" value="{cp_mobile2}" tabindex="13"></span>
        </div>
        <div class="zeile2">
            <span class="label2 klein">.:givenname:.</span>
            <span class="feld"><input type="text" name="cp_givenname" size="25" maxlength="75" value="{cp_givenname}" tabindex="6"></span>
            <span class="label klein">.:fax:.</span>
            <span class="feld"><input type="text" name="cp_fax" size="25" maxlength="75" value="{cp_fax}" tabindex="14"></span>
        </div>
        <div class="zeile2">
            <span class="label2 klein">.:name:.</span>
            <span class="feld"><input type="text" name="cp_name" size="25" maxlength="75" value="{cp_name}" tabindex="7"></span>
            <span class="label klein">Privat</span>
            <span class="feld"><input type="text" name="cp_privatphone" size="25" maxlength="75" value="{cp_privatphone}" tabindex="12"></span>
        </div>
        <div class="zeile2">
            <span class="label2 klein">.:street:.</span>
            <span class="feld"><input type="text" name="cp_street" size="25" maxlength="75" value="{cp_street}" tabindex="8"></span>
            <span class="label klein">.:privat:. .:email:. </span>
            <span class="feld"><input type="text" name="cp_privatemail" size="25" maxlength="125" value="{cp_privatemail}" tabindex="15"></span>
        </div>
        <div class="zeile2">
            <span class="label2 klein">.:country:. / .:zipcode:.</span>
            <span class="feld"><input type="text" id="country" name="cp_country" size="2" maxlength="3" value="{cp_country}" tabindex="9"> / 
                      <input type="text" id="zipcode" name="cp_zipcode" size="5" maxlength="10" value="{cp_zipcode}" tabindex="10">
            </span>
        </div>
        <div class="zeile2">
            <span class="label2 klein">.:city:.</span>
            <span class="feld"><input type="text" id="city" name="cp_city" size="25" maxlength="75" value="{cp_city}" tabindex="11"></span>
                      <span id="geosearchP" class="feldxx"></span>
        </div>
        <div class="zeile2">
            <span class="label2 klein">.:homepage:.</span>
            <span class="feld"><input type="text" name="cp_homepage" size="25" maxlength="125" value="{cp_homepage}" tabindex="16"></span>
            <span class="label klein">.:email:. </span>
            <span class="feld"><input type="text" name="cp_email" size="25" maxlength="125" value="{cp_email}" tabindex="15"></span>
        </div>
        <br />
    </span>

    <span id="tab2">
        <div class="zeile2">
            <span class="label klein">.:Company:.</span>
            <span class="feld"><input type="text" name="name" id="company_name" size="25" maxlength="75" value="{Firma}" tabindex="18">
        </div>
        <div class="zeile2">
            <span class="label klein">.:department:.</span>
            <span class="feld"><input type="text" name="cp_abteilung" size="25" maxlength="30" value="{cp_abteilung}" tabindex="19"></span>
        </div>
        <div class="zeile2">
            <span class="label klein">.:position:.</span>
            <span class="feld"><input type="text" name="cp_position" size="25" maxlength="25" value="{cp_position}" tabindex="20"></span>
        </div>
    </span>

    <span id="tab3">
        <span  style="float:left;">
            <div class="zeile2">
                <span class="label klein">.:Catchword:.</span>
                <span class="feld"><input type="text" name="cp_stichwort1" size="25" maxlength="50" value="{cp_stichwort1}" tabindex="21"></span>
            </div>
            <div class="zeile2">
                <span class="label klein">.:birthday:.</span>
                <span class="feld"><input type="text" name="cp_birthday" id="cp_birthday" size="10" maxlength="10" value="{cp_birthday}" tabindex="17"><span class="klein"> TT.MM.JJJJ</span></span>
            </div>
            <div class="zeile2">
                <span class="label klein">.:image:.</span>
                <span class="feld"><input type="file" name="Datei[bild]" size="10" maxlength="75" tabindex="22"></span>
            </div>
            <div class="zeile2">
                <span class="label klein">.:vcard:.</span>
                <span class="feld"><input type="file" name="Datei[visit]" size="10" maxlength="75" tabindex="23"></span>
            </div>
            <div class="zeile2" style="align:left;">
                <span class="klein">.:Remarks:.</span><br>
                <span class="feldxx" style="border:0px solid black;"><textarea class="klein" name="cp_notes" cols="55" rows="4" tabindex="25">{cp_notes}</textarea></span>
            </div>
            <div class="zeile2">
                <span class="label klein">.:correlation:.</span>
                <span class="feld"><input type="text" name="cp_beziehung" size="8" maxlength="10" value="{cp_beziehung}" tabindex="24"></span>
            </div>
        </span>
        <span style="float:left;">
            <span class="label">{IMG}{IMG_}<br>
            {visitenkarte}</span>
        </span>
    </span>
</div>
<span >
    <span class="fett">{Msg}<br /></span>
    {Btn3} {Btn1} <input type='submit' class='sichernneu' name='neu' value='.:save:. .:new:.' tabindex="28">
    <input type="submit" class="clear" name="reset" value=".:clear:." tabindex="29"> <input type="button" name="" value="VCard" onClick="vcard()" tabindex="30">
    <span class="klein">.:authority:.</span> <select name="cp_owener"  tabindex="31"> 
<!-- BEGIN OwenerListe -->
        <option value="{OLid}" {OLsel}>{OLtext}</option>
<!-- END OwenerListe -->
    </select> {init}
</span>

</form>
<!-- End Code ------------------------------------------->
    <script type='text/javascript' src='inc/geosearchP.js'></script>
    <script type='text/javascript' src='inc/geosearch.js'></script>
{END_CONTENT}
</body>
</html>
