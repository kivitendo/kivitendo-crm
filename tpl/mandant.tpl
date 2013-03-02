<html>
    <head><title>Mandanten Stamm</title>
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JAVASCRIPTS}
</head>
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">Mandanteneinstellungen {msg}</p>
<table style='visibility:{hide}'>
    <form name="mandant" id="mandant" action="mandant.php" method="post">
    <tr><td colspan='2'><b>externe DBs</b></td></tr>
    <tr><td>Geo-DB</td>                             <td><input type='checkbox' name='GEODB' id='GEODB' value='t' {GEODB}></td></tr>
    <tr><td>Blz-DB</td>                             <td><input type='checkbox' name='BLZDB' id='BLZDB' value='t' {BLZDB}></td></tr>
    <tr><td colspan='2'><b>Kontakthred</b></td></tr>
    <tr><td>Editieren</td>                          <td><input type='checkbox' name='CallEdit' id='CallEdit' value='t' {CallEdit}></td></tr>
    <tr><td>L&ouml;schen</td>                       <td><input type='checkbox' name='CallDel' id='CallDel' value='t' {CallDel}></td></tr>
    <tr><td colspan='2'><b>E-Mail</b></td></tr>
    <tr><td>Gesehen markieren als</td><td><select name='MailFlag'>
                                             <option value='Flagged' {Flagged}>Flagged
                                             <option value='Answered' {Answered}>Answered
                                             <option value='Seen' {Seen}>Seen
                                             <option value='Deleted' {Deleted}>Deleted
                                             <option value='Draft' {Draft}>Draft
                                          </select></td></tr>
    <tr><td>Mailordner bereinigen</td>          <td><input type='checkbox' name='Expunge' id='Expunge' value='t' {Expunge}></td></tr>
    <tr><td>versendete Mails loggen</td>        <td><input type='checkbox' name='logmail' id='logmail' value='t' {logmail}></td></tr>
    <tr><td colspan='2'><b>Zeiterfassung</b></td></tr>
    <tr><td>Artikelnummer für Arbeitszeit</td>  <td><input type='text' name='ttpart' id='ttpart' value='{ttpart}'></td></tr>
    <tr><td>Minuten je Einheit</td>             <td><input type='text' name='tttime' id='tttime' value='{tttime}' size='5'></td></tr>
    <tr><td>Ab hier eine Einheit</td>           <td><input type='text' name='ttround' id='ttround' value='{ttround}' size='5'>min.</td></tr>
    <tr><td>Nur eigene Aufträge abrechen</td>   <td><input type='checkbox' name='ttclearown' id='ttclearown' {ttclearown} value='t'></td></tr>
    <tr><td colspan='2'><b>Benutzerfreundliche Links</b></td></tr>
    <tr><td>Links zu Verzeichenissen</td><td>
             Gruppe: <input type="text" name="dir_group" size="12" value='{dir_group}'>
             &nbsp;&nbsp; Rechte: <input type="text" name="dir_mode" size="4" value='{dir_mode}'>
             <input type="checkbox" name="sep_cust_vendor"  value='t' {sep_cust_vendor}>Kunden/Lieferanten trennen</td>
    <tr><td colspan='2'><b>Diverse</b></td></tr>
    <tr><td>TinyMCE</td>                    <td><input type='checkbox' name='tinymce' id='tinymce' {tinymce} value='t'></td></tr>
    <tr><td>Show-Error</td>                 <td><input type='checkbox' name='showErr' id='showErr' {showErr} value='t'></td></tr>
    <tr><td>Logfile (/tmp/lxcrm.err)</td>   <td><input type='checkbox' name='logfile' id='logfile' {logfile} value='t'></td></tr>
    <tr><td>Listenlimit</td>                <td><input type='text' name='listLimit' id='listLimit' value='{listLimit}' size='8'></td></tr>
</table>
<input type="submit" name="save" id="save" value="sicher">
</form>
{END_CONTENT}
</body>
</html>

