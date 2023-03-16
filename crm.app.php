<?php
	session_start();
    $baseUrl = isset( $_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
    $baseUrl.= '://'.$_SERVER['SERVER_NAME'].str_replace( basename(__FILE__), "", $_SERVER['REQUEST_URI'] );
    $url = $baseUrl.'controller.pl?action=Layout/empty&format=json';
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_TIMEOUT, 1 );
    curl_setopt( $ch, CURLOPT_ENCODING, 'gzip,deflate' );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array (
                "Connection: keep-alive",
                "Cookie: ".$_SESSION["cookie"]."=".$_SESSION['sessid']."; ".$_SESSION["cookie"]."_api_token=".$_SESSION["token"]['api_token']
                ));
    $result = curl_exec( $ch );

    if( $result === false || curl_errno( $ch )){
        die( 'Curl-Error: ' .curl_error($ch).'URL "'.$url.'" invalid!"');
    }
    curl_close( $ch );

    $objResult = json_decode( $result );
    $vars = get_object_vars( $objResult );
?>

<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />

<?php
	foreach($objResult->{'stylesheets'} as $style) echo '<link rel="stylesheet" href="'.$baseUrl.$style.'" type="text/css">'."\n";
?>

<!-- Fehlende Stylesheetdateien: -->
  <link rel="stylesheet" href="css/common.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="css/lx-office-erp/list_accounts.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="css/jquery.autocomplete.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="css/jquery.multiselect2side.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="css/ui-lightness/jquery-ui.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="css/lx-office-erp/jquery-ui.custom.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="css/tooltipster.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="css/themes/tooltipster-light.css" type="text/css" title="Stylesheet">
<!-- Ende -->

  <link rel="stylesheet" href="crm/css/crm.app/bootstrap-grid.min.css" type="text/css" title="Stylesheet">

<?php
	foreach($objResult->{'javascripts'} as $js) echo '<script type="text/javascript" src="'.$baseUrl.$js.'"></script>'."\n";
?>

<!-- Fehlende Javascriptdateien: -->
  <script type="text/javascript" src="js/kivi.CustomerVendor.js"></script>
  <script type="text/javascript" src="js/kivi.File.js"></script>
  <script type="text/javascript" src="js/chart.js"></script>
  <script type="text/javascript" src="js/kivi.CustomerVendorTurnover.js"></script>
  <script type="text/javascript" src="js/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="js/ckeditor/adapters/jquery.js"></script>
  <script type="text/javascript" src="js/follow_up.js"></script>
  <script type="text/javascript" src="js/kivi.Validator.js"></script>
  <script type="text/javascript" src="js/jquery.cookie.js"></script>
  <script type="text/javascript" src="js/jquery.checkall.js"></script>
  <script type="text/javascript" src="js/jquery.download.js"></script>
  <script type="text/javascript" src="js/jquery/jquery.form.js"></script>
  <script type="text/javascript" src="js/jquery/fixes.js"></script>
  <script type="text/javascript" src="js/client_js.js"></script>
  <script type="text/javascript" src="js/jquery/jquery.tooltipster.min.js"></script>
  <script type="text/javascript" src="js/jquery/ui/i18n/jquery.ui.datepicker-de.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Ende -->

<style>
	.ui-autocomplete-category {
		font-weight: bold;
	}
</style>

</head>

<body>

<?php
	echo $objResult->{'pre_content'};
?>
<div class="layout-actionbar">
	<input id="crm-widget-quicksearch" placeholder="Schnellsuche" maxlength="20" class="ui-autocomplete-input" style="margin-left: 10px" autocomplete="off">
	<div class="layout-actionbar-separator"></div>
	<div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div class="layout-actionbar-action layout-actionbar-submit">Workflow</div><span></span></div><div class="layout-actionbar-combobox-list"><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-edit">Bearbeiten</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-offer">Angebot erstellen</div><div id="crm-wf-order" class="layout-actionbar-action layout-actionbar-submit">Auftrag erstellen</div><div id="crm-wf-bill" class="layout-actionbar-action layout-actionbar-submit">Rechnung erstellen</div></div></div>
</div>
<?php
	echo $objResult->{'start_content'};
?>

<h1 id="crm-widget-title" class="tools" style="margin-top: 20px; height: 20px;">Detailansicht (Prototyp)</h1>
<div id="crm-main-view">

<!-- Prototype-Start: -->

<div class="container-fluid">

	<ul>
		<li><a href="#">Firmenstammdaten</a></li>
		<li><a href="#">Ansprechpartner</a></li>
		<li><a href="#">Umsätze</a></li>
		<li><a href="#">Dokumente</a></li>
		<li><a href="#">Ansprechpersonen</a></li>
	</ul>

<div id="lxc-widget-base-data" class="row justify-content-md-center">
	<div id="lxc-id-hq-view" class="col-md-auto pt-4">
		<div><strong><span id="lxc-id-name">Maria Mustermann</span></strong></div>
		<div class="pt-1"><span id="lxc-id-street">Bahnhofsstrasse 23</span></div>
		<div><span id="lxc-id-place">D-15345 Rehfelde</span></div>
		<div class="pt-4"><strong>Kontakt</strong></div>
		<div class="pt-2"><span id="lxc-id-contact-person">Maria Mustermann</span></div>
		<div class="pt-2">
			Telefon: <button id="lxc-id-tel1">+49175-1234567</botton>
			<button id="lxc-id-tel1-t">T</botton>
			<button id="lxc-id-tel1-c">C</botton>
			<button id="lxc-id-tel1-w">W</botton>
		</div>
		<div class="pt-2">
			Telefon: <button id="lxc-id-tel2">033433-123456</button>
			<button id="lxc-id-tel2-t">T</botton>
			<button id="lxc-id-tel2-c">C</botton>
			<button id="lxc-id-tel2-w">W</botton>
		</div>
		<div class="pt-2">E-Mail: <button>example@googlemail.com</button></div>
	</div>
	<div id="lxc-widget-contact" class="col-md-auto pt-4">
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Kontakte</a>
			</li>
			<li class="nav-item">
				<a class="nav-link active" aria-current="page" href="#">Angebote</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Aufträge</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Lieferscheine</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Rechnungen</a>
			</li>
		</ul>
		<table class="table table-striped">
			<thead>
			<tr>
				<th scope="col">Datum</th>
				<th scope="col">Erste Position</th>
				<th scope="col">Betrag</th>
				<th scope="col">Nummer</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			<tr>
				<th scope="row">11.11.2011</th>
				<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
				<td>5000,00 EUR</td>
				<td>11111</td>
			</tr>
			</tbody>
		</table>
		<div id="lxc-id-subview" class="pt-4">
			<strong>Subview</strong>
		</div>
	</div>
	<div id="lxc-widget-contact" class="col-md-auto ps-5 pt-4">
		<ul class="nav nav-tabs">
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Lieferanschrift</a>
			</li>
			<li class="nav-item">
				<a class="nav-link active" aria-current="page" href="#">Notizen</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Variablen</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">Finanzinfos</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" aria-current="page" href="#">zusätzliche Infos</a>
			</li>
		</ul>
		<div class="pt-2">
			<textarea rows="10" cols="60">Hier stehen dann die vielen wichtigen Notizen.</textarea>
		</div>
	</div>
</div>

<!-- Prototype-Ende -->

</div>

<?php
	echo $objResult->{'end_content'};
?>

<script src="crm/js/crm.app.js"></script>
<script>
<?php
	foreach($objResult->{'javascripts_inline'} as $js) echo $js."\n";
?>
</script>

</body>
</html>

