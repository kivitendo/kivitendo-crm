<html>
    <head><title>.:usersettings:.</title>
{STYLESHEETS}
{CRMCSS}
{BOXCSS}
{JQUERY}
{JQUERYUI}
{JQTABLE}
{THEME}    
{JAVASCRIPTS}
{JQBOX}
<style type="text/css">
    input.b0 { width:50px; }
    input.b1, select.b1 { width: 200px;  }
    .selectboxit-container .selectboxit-options {width: 170px;}
    #selportSelectBoxItContainer.selectboxit-container .selectboxit-options {width: 10px;}
    #termbeginSelectBoxItContainer.selectboxit-container .selectboxit-options {width: 10px;}
    #termendSelectBoxItContainer.selectboxit-container .selectboxit-options {width: 10px;}
    .selectboxit-container span, .selectboxit-container .selectboxit-options a {height: 22px; line-height: 22px;}
    .inp-checkbox+label {
        margin: .5em;
        width:16px; 
        height:16px; 
        vertical-align:middle;
    }   
</style>
<script language="JavaScript">
    function showItem(Q,id) {
	    F1=open("getCall.php?hole="+id+Q,"Caller","width=800, height=650, left=100, top=50, scrollbars=yes");
    }
    var MailOn = false;
    function Mailonoff( reload ) {
        if ( $('#mailwin').dialog( "isOpen" ) && !reload) {
            $('#mailwin').dialog('close');
        } else {
            if ( !MailOn) {
                var Q, p, email;
                var content = '';
                $('#mailtable tbody').empty();
                $.ajax({
                    url: 'jqhelp/firmaserver.php?task=usermail&uid={uid}',
                    dataType: 'json',
                    success: function(data){
                        $.each(data, function(i, row) {
                            if ( row.cp_mail != null ) {
                                email = row.cp_email;
                                Q     = '&Q=XC&pid='+row.pid;
                            } else if ( row.cemail != null ) {
                                email = row.cemail;
                                Q     = '&Q=C&pid='+row.cid;
                            } else if ( row.vemail != null ) {
                                email = row.vemail;
                                Q     = '&Q=C&pid='+row.vid;
                            } else {
                                Q = '&Q=XX';
                                p = row.cause.indexOf('|');
                                if ( p>=0 ) {
                                    email = row.cause.substring(p+1);
                                    row.cause = row.cause.substring(0,p);
                                } else {
                                    email = '--------';
                                }
                            }
                            content += '<tr onClick="showItem(\''+Q+'\','+row.id+');"><td>'+row.datum+' '+row.zeit+'</td><td>'+email+'</td><td>'+row.cause+'</td></tr>';
                        });
                        $('#mailtable tbody').append(content);
                        $("#mailtable").trigger('update');
                        $("#mailtable")
                            .tablesorter({widthFixed: true, widgets: ['zebra'] })
                            .tablesorterPager({container: $("#pager"), size: 15, positionFixed: false})
                    }
                })
                MailOn = true;
            };
            $( "#mailwin" ).dialog( "open" )
            $( "#mails_button" ).button( "option", "label", ".:hide emails:." );
        }
        
    }
    function kal(fld) {
        f=open("terminmonat.php?datum={DATUM}&fld="+fld,"Name","width=410,height=390,left=200,top=100");
        f.focus();
    }
    function go(art) {
        document.termedit.action=art+".php";
        document.termedit.submit();
    }
    function getical() {
        document.user.icalart.value = document.termedit.icalart.options[document.termedit.icalart.selectedIndex].value;
        document.user.icaldest.value = document.termedit.icaldest.value;
        document.user.icalext.value = document.termedit.icalext.value;
        return true;
    }
    function selPort() {
        po = document.user.selport.selectedIndex;
        document.user.port.value=document.user.selport.options[po].value;
    }
    
       
    $(document).ready(function(){
        $("#dialog_saved, #noThemeFile, #cantEditBase" ).dialog({ 
            autoOpen: false,
            modal: true,
            width: 400,
            position: [200,400]
        });          
        $( "#mailwin" ).dialog({
            autoOpen: false,
            show: {
                effect: "blind",
                duration: 300
            },
            hide: {
                effect: "explode",
                duration: 300
            },
            minWidth: 600,
            minHeight: 550,
            title: "Mails",
            close: function() {
                $("#mails_button").button( "option", "label", ".:show emails:." );
            }
        });
        $( "#edit_theme" ).button().click(function( event ) {
            event.preventDefault();
            var theme = $("#theme").val()
            $.ajax({
                type: "POST",
                url:  "jqhelp/getThemeUrl.php",
                data: {theme: theme},
                success: function(result){ 
                    if( result == "noThemeFile" ) $("#noThemeFile").dialog( "open" );
                    else if( result == "base" ) $("#cantEditBase").dialog( "open" );                
                    else window.open(result);
                }   
            })
            return false; 
        });
        $( "input[type='submit']" ).button();
        $( "#save" ).button().click(function() {
            $.ajax({
                type: "POST",
                url: "jqhelp/saveUserData.php",
                data: $("#userform").serialize() ,
                success: function(res) {
                    $("#dialog_saved").dialog( "open" );
                    setTimeout("$('#dialog_saved').dialog('close')",1100);
                }
            });
            return false;
        });
        $('#streetview_default').click(function() {
            var $this = $(this);
            if ($this.is(':checked') ) {
                $("#streetview,#planspace").hide()  
            } else {
                $("#streetview,#planspace").show()           
            }
        });
        if( $('#streetview_default').is(':checked') ){
            $("#streetview,#planspace").hide()   
        }
        $('#external_mail').click(function() {
            var $this = $(this);
            if ($this.is(':checked') ) {
                $("#mails_button").hide()  
            } else {
                $("#mails_button").show()           
            }
        });
        if( $('#external_mail').is(':checked') ){
            $("#mails_button").hide()   
        }
        $( "#mails_button" ).button().css({  width: '171px'}).click(function() {
            $("#mails_button").button( "option", "label", ".:hide emails:." );
            Mailonoff(false) ;
            return false  ;
        })
        $("select").selectBoxIt({
            theme:       "jqueryui",
            autoWidth:   true,
            //hideCurrent:  true,
            
        })
        $( "td#mansig,span#proto,td#ssl" ).buttonset();
        $(".inp-checkbox").button({ text: false})
            .click(function(e) {
                $(this).button("option", {
                    icons: {
                        primary: $(this)[0].checked ? "ui-icon-check" : ""
                    }
                });
           
            });
        $( "#p1{feature_ac}{streetview_default}{preon}{tinymce}{angebot_button}{auftrag_button}{rechnung_button}"
         + "{liefer_button}{zeige_extra}{zeige_karte}{zeige_bearbeiter}{zeige_etikett}{zeige_dhl}{zeige_tools}"
         + "{zeige_lxcars}{feature_unique_name_plz}{external_mail}{sql_error}{php_error}" ).click();
    });

</script>
<script type='text/javascript' src='inc/help.js'></script>   
<body>
{PRE_CONTENT}
{START_CONTENT}
<div id="dialog_saved" title=".:usersettingscrm:.">
    <p>.:usersettingssaved:.</p>
</div>
<div id="noThemeFile" title="Theme wechseln">
    <p>.:nothemefilefound:.</p>
</div>
 <div id="cantEditBase" title="Theme bearbeiten">
    <p>.:basecannotbechanged:.</p>
</div>   
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;" onClick="help('User');">.:usersettings:.  {login} : {uid}</p>
<form name="user" id="userform"  action="user1.php" method="post" onSubmit="return getical();">

<div style="height:30px;">
    <button id="mails_button">.:show emails:.</button> 
</div>
<table border="0">
    <input type="hidden" name="icalart" value="{icalart}">
    <input type="hidden" name="icaldest" value="{icaldest}">
    <input type="hidden" name="icalext" value="{icalext}">
    <input type="hidden" name="uid" value="{uid}">
    <input type="hidden" name="login" value="{login}">
    <tr><td class="norm">.:searchtab:.</td><td>
        <select class="b1" name="searchtab" data-size="39">
            <option value="1"{searchtab1}>.:fastsearch:.
            <option value="2"{searchtab2}>.:customers:.
            <option value="3"{searchtab3}>.:vendors:.
            <option value="4"{searchtab4}>.:persons:.
        </select>
        </td>
        <td class="norm">.:substitute:.</td><td class="norm"><select class="b1" name="vertreter">
                        <option value=""></option>
<!-- BEGIN Selectbox -->
                        <option value="{vertreter}"{Sel}>{vname}</option>
<!-- END Selectbox -->
                        </select>
        </td></tr>
    <tr><td class="norm">.:kdviewli:.</td><td>
        <select class="b1" name="kdviewli">
        <option value="1"{kdviewli1}>.:shipto:.
        <option value="2"{kdviewli2}>.:remarks:.
        <option value="3"{kdviewli3}>.:variablen:.
        <option value="4"{kdviewli4}>.:financial:.
        <option value="5"{kdviewli5}>.:miscInfo:.
        </select>
        </td>
        <td class="norm">.:label:.</td><td class="norm"><select class="b1" name="etikett">
<!-- BEGIN SelectboxB -->
                        <option value="{LID}"{FSel}>{FTXT}</option>
<!-- END SelectboxB -->
                        </select>
        </td></tr>
        <tr>
        <td class="norm">.:kdviewre:.</td><td>
        <select class="b1" name="kdviewre">
        <option value="1"{kdviewre1}>.:contact:.
        <option value="2"{kdviewre2}>.:quotations:.
        <option value="3"{kdviewre3}>.:orders:.
        <option value="4"{kdviewre4}>.:invoices:.
        </select>
        </td>
        <td class="norm"></td><td>
        </td></tr>
    <tr><td class="norm">.:name:.</td><td><input class="b1 ui-widget-content ui-corner-all" type="text" name="name" value="{name}" maxlength="75"></td>
        <td class="norm">.:department:.</td>    <td ><input class="b1 ui-widget-content ui-corner-all" type="text" name="abteilung" value="{abteilung}" maxlength="75"></td></tr>
    <tr><td class="norm">.:street:.</td><td><input class="b1 ui-widget-content ui-corner-all" type="text" name="addr1" value="{addr1}" maxlength="75"></td>
        <td class="norm">.:position:.</td><td><input class="b1 ui-widget-content ui-corner-all" type="text" name="position" value="{position}" maxlength="75"></td></tr>
    <tr><td class="norm">.:zipcode:. .:city:.</td><td><input class="b0 ui-widget-content ui-corner-all" type="text" name="addr2" value="{addr2}" size="6" maxlength="10"> <input style="width:145px;" class="ui-widget-content ui-corner-all" type="text" name="addr3" value="{addr3}"  maxlength="75"></td>
        <td class="norm">.:email:.</td><td><input class="b1 ui-widget-content ui-corner-all" type="text" name="email" value="{email}" size="30" maxlength="125"></td></tr>
    <tr><td class="norm">.:privatephone:.</td><td><input class="b1 ui-widget-content ui-corner-all" type="text" name="homephone" value="{homephone}" maxlength="30"></td>
        <td class="norm">.:officephone:.</td><td><input class="b1 ui-widget-content ui-corner-all" type="text" name="workphone" value="{workphone}" maxlength="30"></td></tr>
    <tr><td class="norm">.:remark:.</td><td><textarea class="ui-widget-content ui-corner-all" name="notes" cols="37" rows="3">{notes}</textarea></td>
        <td class="norm">.:email:.<br>.:signature:.</td><td><textarea class="ui-widget-content ui-corner-all" name="mailsign" cols="37" rows="3">{mailsign}</textarea></td></tr>
    <tr><td class="norm">Mandantensignatur</td><td id="mansig"><form>
              <input type="radio" id="mandsig0" name="mandsig" value='0' {mandsig0}><label for="mandsig0">ignorieren</label>
              <input type="radio" id="mandsig1" name="mandsig" value='1' {mandsig1}><label for="mandsig1">nur diese</label>
              <input type="radio" id="mandsig2" name="mandsig" value='2' {mandsig2}><label for="mandsig2">voran stellen</label>
              <input type="radio" id="mandsig3" name="mandsig" value='3' {mandsig3}><label for="mandsig3">anhängen</label></form></td>
        <td class="norm">.:member:.</td><td><a href="user2.php" >{GRUPPE}</a></td>
    </tr>
    <tr><td class="norm">.:emailserver:.</td><td><input class="b1 ui-widget-content ui-corner-all" type="text" name="msrv" value="{msrv}"  maxlength="75"></td>
        <td class="norm">.:emailuser:.</td>
        <td class="norm"><input class="b1 ui-widget-content ui-corner-all" type="text" name="mailuser" value="{mailuser}" size="25" maxlength="75">
        </td></tr>
    <tr><td class="norm">.:emailbox:.</td><td class="norm"><input class="b1 ui-widget-content ui-corner-all" type="text" name="postf" value="{postf}" size="10" maxlength="75">
        </td>
        <td class="norm">.:password:.</td>
        <td class="norm"><input class="b1 ui-widget-content ui-corner-all" type="password" name="kennw" value="{kennw}" maxlength="75">
    <!--tr><td>Backup-Pf</td><td><input type="text" name="Postf2" value="{Postf2}" size="10"> </td><td></td></tr-->
        </td></tr>
    <tr><td class="norm">.:protocol:.</td><td><span id="proto">
            <input type="radio" id="proto0" name="proto" value="0" {protopop}><label for="proto0">.:POP:.</label> 
            <input type="radio" id="proto1" name="proto" value="1" {protoimap}><label for="proto1">.:IMAP:.</label></span>
         .:port:. <input class="ui-widget-content ui-corner-all" style="width:28px;" type="text" name="port" value="{port}" size="4" maxlength="6">
        <select name="selport" id="selport" onChange="selPort();">
            <option value=""></option>
            <option data-selectedtext=" " value="110">110</option>
            <option data-selectedtext=" " value="143">143</option>
            <option data-selectedtext=" " value="993">993</option>
            <option data-selectedtext=" " value="995">995</option>
        </select> 
        </td>
        <td class="norm">SSL</td><td id="ssl">
            <input type="radio" id="ssln" name="ssl" value="n" {ssln}><label for="ssln">.:notls:.</label> 
            <input type="radio" id="sslt" name="ssl" value="t" {sslt}><label for="sslt">ssl</label> 
            <input type="radio" id="sslf" name="ssl" value="f" {sslf}><label for="sslf">tls</label>
        </td></tr>
        <tr><td class="norm">.:theme:.</td><td>
        <select style="width:115px;" name="theme" id="theme">
        <!-- BEGIN Theme -->
            <option value="{themefile}" {TSel}>{themename}
        <!-- END Theme -->
       </select>
       <button id="edit_theme">.:edit:.</button>
       </td>
       <td class="norm">.:tinymce:.</td><td><input class="inp-checkbox" type='checkbox' name='tinymce' id='tinymce'  value='t'><label for="tinymce"></label></td>
    </tr>
    <tr><td class="norm">.:deadlines:.</td><td>
            .:from_t:. <select id="termbegin" name="termbegin">{termbegin}</select> 
            .:to_t:. <select  id="termend" name="termend">{termend}</select> .:uhr:.</td>
        <td class="norm">.:deadlinespacing:.</td><td><input class="ui-widget-content ui-corner-all" style="width:30px;" type="text" name="termseq" value="{termseq}" size="3"> .:minutes:.</td></tr>
    <tr><td class="norm">.:interval:.</td><td>
            <input class="ui-widget-content ui-corner-all" style="width:30px;" type="text" name="interv" value="{interv}" size="4" maxlength="5">.:sec.:. &nbsp;&nbsp; </td></tr>
    <tr><td class="normal">.:presearch:. </td><td><input class="ui-widget-content ui-corner-all" style="width:30px;" type="text" name="pre" value="{pre}" size="10"></td>
        <td class="norm">.:awpre:.</td><td><input class="inp-checkbox" type="checkbox" value='t' name="preon" id="preon"><label for="preon"></label>.:yes:.</td></tr>
    <!--tr><td colspan="4"><input type="submit" name="mkmbx" value=".:createmailbox:."></td><td></td><td></td></tr-->
    <tr><td class="norm">.:mapservice:.</td><td colspan="4">
             <input class="ui-widget-content ui-corner-all" style="width:750px;" type="text" name="streetview" id="streetview" size="80" value='{streetview}'><input class="inp-checkbox" type="checkbox" name="streetview_default" id="streetview_default"  value='t'><label for="streetview_default"></label>.:mandant:.
        </td></tr>
    <tr><td class="norm">.:spacecharsubst:.</td><td colspan="4">
             <input class="ui-widget-content ui-corner-all" style="width:30px;" type="text" name="planspace" id="planspace"size="3" value='{planspace}'>
    </td></tr>
    <tr><td class="norm">.:autocompletion:.</td><td colspan="4">
             <input class="inp-checkbox" type="checkbox" name="feature_ac" id="feature_ac" value='t' ><label for="feature_ac"></label>&nbsp;&nbsp; .:minentry:.: <input style="width:20px;" type="text" name="feature_ac_minlength"  value='{feature_ac_minlength}'>
             &nbsp;&nbsp; .:delay:.: <input style="width:40px;" type="text" name="feature_ac_delay" size="3" value='{feature_ac_delay}'>.:ms:.</td>
   </tr>
   <tr><td class="norm">.:firmabuttons:.</td><td colspan="4">
        <input class="inp-checkbox" type="checkbox" name="angebot_button" id="angebot_button" value='t'><label for="angebot_button"></label>.:quotation:.&nbsp;&nbsp; 
        <input class="inp-checkbox" type="checkbox" name="auftrag_button" id="auftrag_button" value='t'><label for="auftrag_button"></label>.:order:.&nbsp;&nbsp;  
        <input class="inp-checkbox" type="checkbox" name="rechnung_button"id="rechnung_button"value='t'><label for="rechnung_button"></label>.:invoice:.&nbsp;&nbsp; 
        <input class="inp-checkbox" type="checkbox" name="liefer_button"  id="liefer_button"  value='t'><label for="liefer_button"></label>.:delivery order:.&nbsp;&nbsp;
        <input class="inp-checkbox" type="checkbox" name="zeige_extra"    id="zeige_extra"    value='t'><label for="zeige_extra"></label>.:extra:.&nbsp;&nbsp;
        <input class="inp-checkbox" type="checkbox" name="zeige_karte"    id="zeige_karte"    value='t'><label for="zeige_karte"></label>.:map:.&nbsp;&nbsp;
        <input class="inp-checkbox" type="checkbox" name="zeige_bearbeiter" id="zeige_bearbeiter" value='t'><label for="zeige_bearbeiter"></label>.:employee:.&nbsp;&nbsp;
        <input class="inp-checkbox" type="checkbox" name="zeige_etikett"  id="zeige_etikett"  value='t'><label for="zeige_etikett"></label>.:label:.&nbsp;&nbsp;
        <input class="inp-checkbox" type="checkbox" name="zeige_dhl"      id="zeige_dhl"      value='t'><label for="zeige_dhl"></label>.:DHL:.&nbsp;&nbsp;
        <input class="inp-checkbox" type="checkbox" name="zeige_tools"    id="zeige_tools"    value='t'><label for="zeige_tools"></label>.:tools:.&nbsp;&nbsp;
        <input class="inp-checkbox" type="checkbox" name="zeige_lxcars"   id="zeige_lxcars"   value='t'><label for="zeige_lxcars"></label>LxCars&nbsp;&nbsp;
        <div id="p1"></div>
        </td>
    </tr>
    <tr><td class="norm">.:createmultiuser:.</td><td >
             <input class="inp-checkbox" type="checkbox" name="feature_unique_name_plz" id="feature_unique_name_plz" value='t'><label for="feature_unique_name_plz"></label>.:disallow:.</td>
        <td class="norm">.:external_mail:.</td><td colspan="4">
             <input class="inp-checkbox"type="checkbox" id="external_mail" name="external_mail" value='t'><label for="external_mail"></label>.:use:.</td>
    </tr>
    <tr><td class="norm">.:show errors:.</td><td colspan="4">
            <input class="inp-checkbox" type="checkbox" name="sql_error" id="sql_error" value='t'><label for="sql_error"></label>.:sqlerror:.&nbsp;&nbsp; <input class="inp-checkbox"type="checkbox" name="php_error" id="php_error"  value='t'><label for="php_error"></label>.:phperror:. &nbsp;&nbsp;
    </tr>
    <tr><td>&nbsp;</td><td><button id="save">.:save:.</button></td></tr>

    </form>
    
</table>
.:exportcal:.: 
<form name="termedit" method="post" action="mkics.php" onSubmit="return false;">
<table><tr>
    <td><input class="ui-widget-content ui-corner-all" type="text" size="10" id="start" name="start"><img src='image/date.png' border='0' align='middle' onClick="kal('start')";></td>
    <td><input class="ui-widget-content ui-corner-all" type="text" size="10" id="stop" name="stop"><img src='image/date.png' border='0' align='middle' id='triggerStop' onClick="kal('stop')";></td>
    <td><select name="icalart">
        <option value="file" {icalartfile}>.:fileserver:.
        <option value="mail" {icalartmail}>.:email:.
        <option value="client" {icalartclient}>.:browser:.
        </select>
    </td>
    <td><input class="ui-widget-content ui-corner-all" type="text" size="4"  id="ext"  name="icalext" value="{icalext}"></td>
    <td><input class="ui-widget-content ui-corner-all" type="text" size="40"  id="dest"  name="icaldest" value="{icaldest}"></td>
    <td><a href="#" onClick="go('mkics')">.:go:.</a></td>
    </tr><tr>
    <td class="klein">.:from_t:.</td>
    <td class="klein">.:to_t:.</td>
    <td class="klein">.:type:.</td>
    <td class="klein">.:fileextention:.</td>
    <td class="klein">.:destination:.</td>
    <td></td>
</tr></table>
</form>
<img src="{IMG}" width="500" height="280" title="Netto sales over 12 Month">

<div id="mailwin"> 
    <table id="mailtable" class="tablesorter">
    <thead>
        <tr><th>.:date:.</th><th>.:emailaddress:.</th><th>.:subject:.</th></tr>
    </thead>
    <tbody id='mtablebody'>
    </tbody>
    </table>
    <div id="pager" class="pager">
        <img src="{CRMPATH}jquery-plugins/Table/addons/pager/icons/first.png" class="first"/>
        <img src="{CRMPATH}jquery-plugins/Table/addons/pager/icons/prev.png" class="prev"/>
        <button id='reload' name='reload' onClick="MailOn=false; Mailonoff(true)">.:reload:.</button>
        <img src="{CRMPATH}jquery-plugins/Table/addons/pager/icons/next.png" class="next"/>
        <img src="{CRMPATH}jquery-plugins/Table/addons/pager/icons/last.png" class="last"/>
        <select class="pagesize" id='pagesize'>
            <option value="10">10</option>
            <option value="15" selected>15</option>
            <option value="20">20</option>
            <option value="25">25</option>
            <option value="30">30</option>
        </select>
        </form>
     </div>
</div>

{END_CONTENT}
</body>
</html>

