<html>
    <head><title>User Stamm</title>
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{JQTABLE}
{THEME}    
{JAVASCRIPTS}
<script language="JavaScript">
    function showItem(Q,id) {
	    F1=open("getCall.php?hole="+id+Q,"Caller","width=800, height=650, left=100, top=50, scrollbars=yes");
    }
    var MailOn = false;
    function Mailonoff( reload ) {
        if ( $('#mailwin').dialog( "isOpen" ) && !reload) {
             $('#mailwin').dialog('close');
            document.user.mails.value="Mails zeigen";
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
            document.user.mails.value="Mails verstecken";
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
            title: "Mails"
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
                    setTimeout("$('#dialog_saved').dialog('close')",2000);
                }
            });
            //$( "#angebot_button" ).focus(); //dass der Save-Button nicht gedrückt (klein) bleibt, ToDo: verbessern...
            return false;
        });
    });
</script>
<script type='text/javascript' src='inc/help.js'></script>   
<body>
{PRE_CONTENT}
{START_CONTENT}
<div id="dialog_saved" title="Benutzer Stammdaten CRM">
    <p>Benutzereinstellungen wurden gesichert.</p>
</div>
<div id="noThemeFile" title="Theme wechseln">
    <p>Kein Themefile gefunden.</p>
</div>
 <div id="cantEditBase" title="Theme bearbeiten">
    <p>Base kann nicht bearbeitet werde!</p>
</div>   
<p class="listtop" onClick="help('User');">Benutzer Stammdaten (?)</p>
<form name="user" id="userform"  action="user1.php" method="post" onSubmit="return getical();">
<div id="user">
<input type="reset" name="mails" value="Mails zeigen" onClick="Mailonoff(false)"> {login} : {uid}

<table border="0">
    <input type="hidden" name="icalart" value="{icalart}">
    <input type="hidden" name="icaldest" value="{icaldest}">
    <input type="hidden" name="icalext" value="{icalext}">
    <input type="hidden" name="uid" value="{uid}">
    <input type="hidden" name="login" value="{login}">
    <tr><td class="norm">.:search tab:.</td><td>
        <select name="searchtab">
        <option value="1"{searchtab1}>.:fastsearch:.
        <option value="2"{searchtab2}>.:customers:.
        <option value="3"{searchtab3}>.:vendors:.
        <option value="4"{searchtab4}>.:persons:.
        <option value="5"{searchtab5}>.:remember:.
        </select>
        </td>
        <td class="norm">Vertreter</td><td class="norm"><select name="vertreter">
                        <option value=""></option>
<!-- BEGIN Selectbox -->
                        <option value="{vertreter}"{Sel}>{vname}</option>
<!-- END Selectbox -->
                        </select>
        </td></tr>
    <tr><td class="norm">Kd-Ansicht links</td><td>
        <select name="kdviewli">
        <option value="1"{kdviewli1}>.:shipto:.
        <option value="2"{kdviewli2}>.:remarks:.
        <option value="3"{kdviewli3}>.:variablen:.
        <option value="4"{kdviewli4}>.:financial:.
        <option value="5"{kdviewli5}>.:miscInfo:.
        </select>
        </td>
        <td class="norm">Etikett</td><td class="norm"><select name="etikett">
<!-- BEGIN SelectboxB -->
                        <option value="{LID}"{FSel}>{FTXT}</option>
<!-- END SelectboxB -->
                        </select>
        </td></tr>
        <tr>
        <td class="norm">Kd-Ansicht rechts</td><td>
        <select name="kdviewre">
        <option value="1"{kdviewre1}>.:contact:.
        <option value="2"{kdviewre2}>.:quotations:.
        <option value="3"{kdviewre3}>.:orders:.
        <option value="4"{kdviewre4}>.:invoices:.
        <option value="5"{kdviewre5}>.:remember:.
        </select>
        </td>
        <td class="norm"></td><td>
        </td></tr>
    <tr><td class="norm">Name</td><td><input type="text" name="name" value="{name}" maxlength="75"></td>
        <td class="norm">Abteilung</td>    <td><input type="text" name="abteilung" value="{abteilung}" maxlength="75"></td></tr>
    <tr><td class="norm">Strasse</td><td><input type="text" name="addr1" value="{addr1}" maxlength="75"></td>
        <td class="norm">Position</td><td><input type="text" name="position" value="{position}" maxlength="75"></td></tr>
    <tr><td class="norm">Plz Ort</td><td><input type="text" name="addr2" value="{addr2}" size="6" maxlength="10"> <input type="text" name="addr3" value="{addr3}"  maxlength="75"></td>
        <td class="norm">E-Mail</td><td><input type="text" name="email" value="{email}" size="30" maxlength="125">{emailauth}</td></tr>
    <tr><td class="norm">Telefon priv.</td><td><input type="text" name="homephone" value="{homephone}" maxlength="30"></td>
        <td class="norm">gesch&auml;ftl.</td><td><input type="text" name="workphone" value="{workphone}" maxlength="30"></td></tr>
    <tr><td class="norm">Bemerkung</td><td><textarea name="notes" cols="37" rows="3">{notes}</textarea></td>
        <td class="norm">Mail-<br>unterschrift</td><td><textarea name="mailsign" cols="37" rows="3">{mailsign}</textarea></td></tr>
    <tr><td class="norm"></td><td></td>
        <td>&nbsp;</td><td>{GRUPPE}</td></tr>
    <tr><td class="norm">Mailserver</td><td><input type="text" name="msrv" value="{msrv}" size="25" maxlength="75"></td>
        <td class="norm">Mailuser</td>
        <td class="norm"><input type="text" name="mailuser" value="{mailuser}" size="25" maxlength="75">
        </td></tr>
    <tr><td class="norm">Postfach</td><td class="norm"><input type="text" name="postf" value="{postf}" size="10" maxlength="75"> Port <input type="text" name="port" value="{port}" size="4" maxlength="6">
        <select name="selport" onChange="selPort();">
            <option value=""></option>
            <option value="110">110</option>
            <option value="143">143</option>
            <option value="993">993</option>
            <option value="995">995</option>
        </select>
        </td>
        <td class="norm">Kennwort</td>
        <td class="norm"><input type="password" name="kennw" value="{kennw}" maxlength="75">
    <!--tr><td>Backup-Pf</td><td><input type="text" name="Postf2" value="{Postf2}" size="10"> </td><td></td></tr-->
        </td></tr>
    <tr><td class="norm">Protokoll</td><td><input type="radio" name="proto" value="0" {protopop}>POP <input type="radio" name="proto" value="1" {protoimap}>IMAP</td>
        <td class="norm">SSL</td>
        <td class="norm"><input type="radio" name="ssl" value="n" {ssln}>notls <input type="radio" name="ssl" value="t" {sslt}>ssl <input type="radio" name="ssl" value="f" {sslf}>tls
        </td></tr>
    <tr><td class="norm">Theme</td><td>
        <select name="theme" id="theme">
<!-- BEGIN Theme -->
            <option value="{themefile}" {TSel}>{themename}
<!-- END Theme -->
       </select>
       <button id="edit_theme">bearbeiten</button>
        </td>
       <td>TinyMCE</td><td><input type='checkbox' name='tinymce' id='tinymce' {tinymce} value='t'></td>
        </tr>
    <tr><td class="norm">Termine</td><td>
            von <select name="termbegin">{termbegin}</select> 
            bis <select name="termend">{termend}</select> Uhr</td>
        <td class="norm">Terminabstand</td><td><input type="text" name="termseq" value="{termseq}" size="3"> Minuten</td></tr>
    <tr><td class="norm">Intervall</td><td>
            <input type="text" name="interv" value="{interv}" size="4" maxlength="5">sec. &nbsp;&nbsp; PreSearch <input type="text" name="pre" value="{pre}" size="10"></td>
        <td class="norm">immer mit Pre</td><td><input type="checkbox" value='t' name="preon" {preon}>Ja</td></tr>
    <!--tr><td colspan="4"><input type="submit" name="mkmbx" value="Mailbox erzeugen"></td><td></td><td></td></tr-->
    <tr><td class="norm">Kartendienst</td><td colspan="4">
             <input type="text" name="streetview" size="80" value='{streetview}'>
        </td></tr>
    <tr><td class="norm">Leerzeichenersatz</td><td colspan="4">
             <input type="text" name="planspace" size="3" value='{planspace}'>
    </td></tr>
    <tr><td class="norm">Autocompletion</td><td colspan="4">
             <input type="checkbox" name="feature_ac" value='t' {feature_ac}>&nbsp;&nbsp; Mindesteingabe: <input type="text" name="feature_ac_minlength" size="1" value='{feature_ac_minlength}'>
             &nbsp;&nbsp; Verzögerung: <input type="text" name="feature_ac_delay" size="3" value='{feature_ac_delay}'>ms</td>
   </tr>
   <tr><td class="norm">Firma Buttons</td><td colspan="4">
             <input type="checkbox" name="angebot_button" id="angebot_button"  value='t' {angebot_button}>Angebot&nbsp;&nbsp; <input type="checkbox" name="auftrag_button"  value='t' {auftrag_button}>Auftrag&nbsp;&nbsp;  
             <input type="checkbox" name="rechnung_button" value='t' {rechnung_button}>Rechnung&nbsp;&nbsp; <input type="checkbox" name="liefer_button" value='t' {liefer_button}>Lieferschein&nbsp;&nbsp;
             <input type="checkbox" name="zeige_extra" value='t' {zeige_extra}>Extra&nbsp;&nbsp;<input type="checkbox" name="zeige_karte" value='t' {zeige_karte}>Karte&nbsp;&nbsp;
             <input type="checkbox" name="zeige_bearbeiter" value='t' {zeige_bearbeiter}>Bearbeiter&nbsp;&nbsp;<input type="checkbox" name="zeige_etikett" value='t' {zeige_etikett}>Etikett&nbsp;&nbsp;
             <input type="checkbox" name="zeige_dhl" value='t' {zeige_dhl}>DHL&nbsp;&nbsp;<input type="checkbox" name="zeige_tools" value='t' {zeige_tools}>Tools&nbsp;&nbsp;
             <input type="checkbox" name="zeige_lxcars"    value='t' {zeige_lxcars}>LxCars&nbsp;&nbsp;</td>
   </tr>
   <tr><td class="norm">Doppelten Kunden anlegen</td><td colspan="4">
             <input type="checkbox" name="feature_unique_name_plz" value='t' {feature_unique_name_plz}>verbieten</td>
   </tr>
  <tr><td class="norm">Fehler anzeigen</td><td colspan="4">
            <input type="checkbox" name="sql_error"  value='t' {sql_error}>SQL-Fehler&nbsp;&nbsp; <input type="checkbox" name="php_error"  value='t' {php_error}>Php-Fehler &nbsp;&nbsp;
   </tr>
       <tr><td>&nbsp;</td><td><button id="save">sichern</button></td></tr>

    </form>
    
</table>
Kalenderexport: 
<form name="termedit" method="post" action="mkics.php" onSubmit="return false;">
<table><tr>
    <td><input type="text" size="10" id="start" name="start"><img src='image/date.png' border='0' align='middle' onClick="kal('start')";></td>
    <td><input type="text" size="10" id="stop" name="stop"><img src='image/date.png' border='0' align='middle' id='triggerStop' onClick="kal('stop')";></td>
    <td><select name="icalart">
        <option value="file" {icalartfile}>File (Server)
        <option value="mail" {icalartmail}>E-Mail
        <option value="client" {icalartclient}>Browser
        </select>
    </td>
    <td><input type="text" size="4"  id="ext"  name="icalext" value="{icalext}"></td>
    <td><input type="text" size="40"  id="dest"  name="icaldest" value="{icaldest}"></td>
    <td><a href="#" onClick="go('mkics')">go</a></td>
    </tr><tr>
    <td class="klein">von</td>
    <td class="klein">bis</td>
    <td class="klein">Art</td>
    <td class="klein">Endung</td>
    <td class="klein">Ziel</td>
    <td></td>
</tr></table>
</form>
<img src="{IMG}" width="500" height="280" title="Netto sales over 12 Month">
</div>
<div id="mailwin"> 
    <table id="mailtable" class="tablesorter">
    <thead>
        <tr><th>Datum</th><th>E-Mail</th><th>Betreff</th></tr>
    </thead>
    <tbody id='mtablebody'>
    </tbody>
    </table>
    <div id="pager" class="pager">
        <img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/first.png" class="first"/>
        <img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/prev.png" class="prev"/>
        <button id='reload' name='reload' onClick="MailOn=false; Mailonoff(true)">reload</button>
        <img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/next.png" class="next"/>
        <img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/last.png" class="last"/>
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

