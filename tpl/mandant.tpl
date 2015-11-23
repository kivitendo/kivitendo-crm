<html>
    <head><title>.:Mandanten Stamm:.</title>
{STYLESHEETS}
{JAVASCRIPTS}
{THEME}
<script>
    $(document).ready(function(){
         $("#dialogKlicktelInfo").dialog({
            autoOpen: false,
            title: 'Achtung! Wichtige Information.',
            modal: true,
            width:'auto',
            buttons: {
                Ok: function() {
                    $(this).dialog("close");
                }
            }
        });
        $("#klicktelInfo").button().click(function() {
            $('#dialogKlicktelInfo').dialog("open").html('Wir empfehlen aus Datenschutzgründen einen eigenen API Key zu benutzen!<br> <a target="_blank" href="http://openapi.klicktel.de/login">Key anfordern: http://openapi.klicktel.de/login</a>');
            return false;

        });
        $("input:text").button().addClass('ui-textfield');
        $("#save").button();
        //$("#mailFlag").selectmenu(); ToDo: neue jQuerx-UI version in ERP einfügen
        if( '{klicktel_key_db}' != '95d5a5f8d8ef062920518592da992cba' ) $("#klicktelInfo").hide();
    });
</script>
<style>
    .ui-textfield {
        font: inherit;
        color: inherit;
        background: #FFFFEF !important;
        text-align: inherit;
        outline: none;
    }
</style>
</head>
<body>
<div class="ui-widget-content">
{PRE_CONTENT}
{START_CONTENT}
<p class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0.6em;">.:Mandanteneinstellungen:. {msg}</p>
<table style="visibility:{hide}">
    <div id="dialogKlicktelInfo"></div>
    <form name="mandant" id="mandant" action="mandant.php" method="post">
    <tr><td colspan='2'><b>.:Telefonintegration:.</b></td></tr>
    <tr><td>Klicktel API Key</td>            <td><input type='text' name='klicktel_key' id='klicktel_key' value='{klicktel_key}' size='35'> <button id="klicktelInfo">Info</button></td></tr>

    <tr><td colspan='2'><b>externe DBs</b></td></tr>
    <tr><td>Geo-DB</td>                             <td><input type='checkbox' name='GEODB' id='GEODB' value='t' {GEODB}> siehe install.txt</td></tr>
    <tr><td>Blz-DB</td>                             <td><input type='checkbox' name='BLZDB' id='BLZDB' value='t' {BLZDB}> siehe install.txt</td></tr>
    <tr><td colspan='2'><b>Kontakthred</b></td></tr>
    <tr><td>Editieren</td>                          <td><input type='checkbox' name='CallEdit' id='CallEdit' value='t' {CallEdit}></td></tr>
    <tr><td>Löschen</td>                            <td><input type='checkbox' name='CallDel' id='CallDel' value='t' {CallDel}></td></tr>
    <tr><td colspan='2'><b>E-Mail</b></td></tr>
    <tr><td>Gesehen markieren als</td><td><select name='MailFlag' id='mailFlag'>
                                             <option value='Flagged' {Flagged}>Flagged
                                             <option value='Answered' {Answered}>Answered
                                             <option value='Seen' {Seen}>Seen
                                             <option value='Deleted' {Deleted}>Deleted
                                             <option value='Draft' {Draft}>Draft
                                          </select></td></tr>
    <tr><td>Mailordner bereinigen</td>          <td><input type='checkbox' name='Expunge' id='Expunge' value='t' {Expunge}> -Expunge- </td></tr>
    <tr><td>versendete Mails loggen</td>        <td><input type='checkbox' name='logmail' id='logmail' value='t' {logmail}></td></tr>
    <tr><td colspan='2'><b>Vorgabe Map</b></td></tr>
    <tr><td>Map-URL</td>            <td><input type='text' name='streetview_man' id='streetview_man' value='{streetview_man}' size='60'></td></tr>
    <tr><td colspan='2'>Platzhalter: %TOSTREET% %TOZIPCODE% %TOCITY% %FROMSTREET% %FROMZIPCODE% %FROMCITY%</td></tr>
    <tr><td>Leerzeichenersatz</td>  <td><input type='text' name='planspace_man' id='planspace_man' value='{planspace_man}' size='1' maxlength='1'> (GoYellow: '-', Viamichelin, Google: '+')</td></tr>
    <tr><td colspan='2'><b>Zeiterfassung</b></td></tr>
    <tr><td>Artikelnummer für Arbeitszeit</td>  <td><input type='text' name='ttpart' id='ttpart' value='{ttpart}' size='10'></td></tr>
    <tr><td>Minuten je Einheit</td>             <td><input type='text' name='tttime' id='tttime' value='{tttime}' size='5'></td></tr>
    <tr><td>Ab hier eine Einheit</td>           <td><input type='text' name='ttround' id='ttround' value='{ttround}' size='5'>min.</td></tr>
    <tr><td>Nur eigene Aufträge abrechnen</td>  <td><input type='checkbox' name='ttclearown' id='ttclearown' {ttclearown} value='t'></td></tr>
    <tr><td colspan='2'><b>Benutzerfreundliche Links</b></td></tr>
    <tr><td>Links zu Verzeichnissen</td><td>
             Gruppe: <input type="text" name="dir_group" size="12" value='{dir_group}'>
             &nbsp;&nbsp; Rechte: <input type="text" name="dir_mode" size="4" value='{dir_mode}'>
             <input type="checkbox" name="sep_cust_vendor"  value='t' {sep_cust_vendor}>Kunden/Lieferanten trennen</td>
    <tr><td colspan='2'><b>Diverse</b></td></tr>
    <tr><td>CRM-Pfad</td>                   <td>{crmpath}</td></tr>
    <tr><td>Logfile (tmp/lxcrm.err)</td>    <td><input type='checkbox' name='logfile' id='logfile' {logfile} value='t'></td></tr>
    <tr><td>Listenlimit</td>                <td><input type='text' name='listLimit' id='listLimit' value='{listLimit}' size='8'></td></tr>
    <tr><td><input type="submit" name="save" id="save" value="sichern"></td><td></td></tr>
</table>
</form>
{END_CONTENT}
</div>
</body>
</html>
