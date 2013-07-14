<html>
    <head><title>DHL</title>
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{THEME}
{JAVASCRIPTS}
</head>
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">DHL {msg}</p>
<table style='visibility:{hide}'>
    <form name="dhl" id="dhl" action="dhladm.php" method="post">
    <tr><td colspan='2'><b>Absenderdaten</b></td></tr>
    <tr><td>Absendername 1</td>                     <td><input type='text' name='SEND_NAME1'   id='SEND_NAME1'   value='{SEND_NAME1}'   size='20' maxlength='50'></td></tr>
    <tr><td>Absendername 2</td>                     <td><input type='text' name='SEND_NAME2'   id='SEND_NAME2'   value='{SEND_NAME2}'   size='20' maxlength='50'></td></tr>
    <tr><td>Strasse</td>                            <td><input type='text' name='SEND_STREET'  id='SEND_STREET'  value='{SEND_STREET}'  size='20' maxlength='50'></td></tr>
    <tr><td>Haus-Nr</td>                            <td><input type='text' name='SEND_HOUSENUMBER' id='SEND_HOUSENUMBER' value='{SEND_HOUSENUMBER}' size='5' maxlength='11'></td></tr>
    <tr><td>Plz</td>                                <td><input type='text' name='SEND_PLZ'     id='SEND_PLZ'     value='{SEND_PLZ}'     size='5'  maxlength='11'></td></tr>
    <tr><td>Ort</td>                                <td><input type='text' name='SEND_CITY'    id='SEND_CITY'    value='{SEND_CITY}'    size='20' maxlength='38'></td></tr>
    <tr><td>Land</td>                               <td><input type='text' name='SEND_COUNTRY' id='SEND_COUNTRY' value='{SEND_COUNTRY}' size='5'  maxlength='3'> 
 3 Buchstaben, siehe: <a href='http://de.wikipedia.org/wiki/ISO-3166-1-Kodierliste' target='_blank'>Wikipedia Spalte ALPHA-3</a></td></tr>
    <tr><td colspan='2'><b>Produkte</b></td></tr>
<!-- BEGIN produkte -->    
    <tr><td><input type='text' name='prodname[]' value='{prodname}'>    </td><td valign='top'><input type='text' name='produkt[]' value='{produkt}'></td></tr>
<!-- END produkte -->    
    <tr><td><input type='text' name='prodname[]' value=''>    </td><td><input type='text' name='produkt[]' value=''></td></tr>
    <tr><td><input type="submit" name="save" id="save" value="sichern"></td><td></td></tr>
</table>
</form>
{END_CONTENT}
</body>
</html>

