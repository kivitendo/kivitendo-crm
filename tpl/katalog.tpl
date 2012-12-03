<html>
	<head><title></title>
        {STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>

        {AJAXJS}
        {JAVASCRIPTS}

<body >
{PRE_CONTENT}
{START_CONTENT}
<!-- Beginn Code ------------------------------------------->
<p class="listtop">.:catalog:.</p>
<table >
<tr><td valign='top'>
<form name="katalog" action="katalog.php" method="post">
<div class="zeile">
    <span class="label"></span>
    <span class="leftfeld">Mehrere Bedingungungen (AND)</span>
</div>
<div class="zeile">
    <span class="label">Artikelbezeichnung</span>
    <span class="leftfeld"><input type="text" name="description" value='{description}' size="20"></span>
</div>
<div class="zeile">
    <span class="label">Artikelnummer </span>
    <span class="leftfeld"><input type="text" name="partnumber" value='{partnumber}' size="20"> </span>
</div>
<div class="zeile">
    <span class="label">EAN </span>
    <span class="leftfeld"><input type="text" name="ean" value='{ean}' size="20"> </span>
</div>
<div class="zeile">
    <span class="label">Warengruppe </span>
    <span class="leftfeld"><input type="text" name="partsgroup" value='{partsgroup}' size="20"> </span>
</div>
<div class="zeile">
    <span class="label">Preis </span>
    <span class="leftfeld"><select name='preise'>
<!-- BEGIN Preise -->
        <option value='{preisid}' {select}>{preis}
<!-- END Preise -->
    </select>
    </span>
<div class="zeile">
    <span class="label">Steuer aufschlagen </span>
    <span class="leftfeld"><input type="checkbox" name="addtax" value="1" {addtax}>.:yes:.</span>
</div>
</div>
</td><td valign='top'>
<div class="zeile">
    <span class="label"></span>
    <span class="leftfeld">Nur eine Auswahl!! (cVars)</span>
</div>
<!-- BEGIN cvarListe -->
        <div class="zeile">
                <span class="label">{varlable}</span>
                <span class="leftfeld">{varfld}</span>
        </div>
<!-- END cvarListe -->
</td></tr>
</table>
<input type="submit" name="ok" value="erstellen"> <a href='{link}'>{link}</a>


</form>
</table>
<!-- End Code ------------------------------------------->
{END_CONTENT}
</body>
</html>
