<?php
    session_start();  // muss sein????
    // den Rest würde ich auslagern require_once....
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
<!-- Ende -->

<style>
	.ui-autocomplete-category {
		font-weight: bold;
	}

	.crm-p1 {
		padding: 1em;
	}

	.crm-p2 {
		padding: 2em;
	}

	.crm-pt05 {
		padding-top: 0.5em;
	}

	.crm-pt025 {
		padding-top: 0.25em;
	}

	.crm-pt1 {
		padding-top: 1em;
	}

	.crm-pt2 {
		padding-top: 2em;
	}

	.crm-mt2 {
		margin-top: 2em;
	}

	.crm-tab {
		overflow: auto;
	}

	.crm-fs {
		font-size: 14px;
	}

	main button {
		min-width: 25px;
		min-height: 25px;
	}

	/* debug */
	/*
	* {
		outline: 1px solid red;
	}
	*/
</style>

</head>

<body class="crm-fs">
<div id="message-dialog" style="display:none;">
	<div id="message-dialog-error" style="display:none"></div>
	<p id="message-dialog-text"></p>
	<p id="message-dialog-debug" style="display:none"></p>
</div>

<script>
</script>

<?php
	echo $objResult->{'pre_content'};
?>
<div class="layout-actionbar">
	<input id="crm-widget-quicksearch" placeholder="Schnellsuche" maxlength="20" class="ui-autocomplete-input" style="margin-left: 10px" autocomplete="off">
	<div class="layout-actionbar-separator"></div>
	<div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div class="layout-actionbar-action layout-actionbar-submit">Workflow</div><span></span></div><div class="layout-actionbar-combobox-list"><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-edit">Bearbeiten</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-offer">Angebot erstellen</div><div id="crm-wf-order" class="layout-actionbar-action layout-actionbar-submit">Auftrag erstellen</div><div id="crm-wf-bill" class="layout-actionbar-action layout-actionbar-submit">Rechnung erstellen</div></div></div>
</div>

<!-- Prototype-Start: -->

<?php
	echo $objResult->{'start_content'};
?>

<h1 id="crm-widget-title" class="tools" style="margin-top: 20px; height: 20px;">Detailansicht (Prototyp)</h1>

<main>

<div id="crm-main-view" class="container-fluid">

<div id="lxc-widget-base-data" class="row crm-p2">
	<div id="lxc-widget-contact" class="col-lg-3">
		<div><strong><span id="lxc-id-name">Maria Mustermann</span></strong></div>
		<div class="crm-pt05"><span id="crm-id-street">Bahnhofsstrasse 23</span></div>
		<div class="crm-pt025"><span id="crm-id-place">D-15345 Rehfelde</span></div>
		<div class="crm-pt2"><strong>Kontakt</strong></div>
		<div class="crm-pt05"><span id="crm-id-contact-person">Maria Mustermann</span></div>
		<div class="row crm-pt05">
			<div class="col-md-2">Telefon:</div>
			<div class="col-md-10">
				<button id="crm-id-tel1">+49175-1234567</botton>
				<button id="crm-id-tel1-t">T</botton>
				<button id="crm-id-tel1-c">C</botton>
				<button id="crm-id-tel1-w">W</botton>
			</div>
		</div>
		<div class="row crm-pt025">
			<div class="col-md-2">Telefon:</div>
			<div class="col-md-10">
				<button id="crm-id-tel2">033433-123456</button>
				<button id="crm-id-tel2-t">T</botton>
				<button id="crm-id-tel2-c">C</botton>
				<button id="crm-id-tel2-w">W</botton>
			</div>
		</div>
		<div class="row crm-pt025">
			<div class="col-md-2">E-Mail:</div>
			<div class="col-md-10"><button id="crm-id-email">example@googlemail.com</button></div>
		</div>
		<table width="100%" class="crm-pt2">
			<thead>
			<tr>
				<td class="listheading">Kenzeichen</th>
				<td class="listheading">Hersteller</th>
				<td class="listheading">Fahrzeugtyp</th>
				<td class="listheading">Fahrzeugart</th>
			</tr>
			</thead>
			<tbody>
			<tr class="listrow0">
				<th>MOL-AB123</th>
				<td>Mercedes</td>
				<td>101</td>
				<td>Pkw</td>
			</tr>
			<tr class="listrow1">
				<th>MOL-AB123</th>
				<td>BMW</td>
				<td>i3</td>
				<td>Pkw</td>
			</tr>
			<tr class="listrow0">
				<th>MOL-AB123</th>
				<td>Audi</td>
				<td>A5</td>
				<td>Pkw</td>
			</tr>
			<tr class="listrow1">
				<th>MOL-AB123</th>
				<td>VW</td>
				<td>Golf 7</td>
				<td>Pkw</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div id="crm-widget-main" class="col-lg-6">
		<div id="crm-tabs-main" class="tabwidget">
			<ul class="nav nav-tabs">
				<li class="nav-item">
					<a class="nav-link active" aria-current="page" href="#crm-tab-offers">Angebote</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" aria-current="page" href="#crm-tab-orders">Aufträge</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" aria-current="page" href="#crm-tab-deliveries">Lieferscheine</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" aria-current="page" href="#crm-tab-invoices">Rechnungen</a>
				</li>
			</ul>
			<div id="crm-tab-offers" class="crm-tab">
			</div>
			<div id="crm-tab-orders" class="crm-tab">
				<table class="table table-striped">
					<thead>
					<tr>
						<td class="listheading">Datum</th>
						<td class="listheading">Erste Position</th>
						<td class="listheading">Betrag</th>
						<td class="listheading">Nummer</th>
					</tr>
					</thead>
					<tbody>
					<tr class="listrow0">
						<th>11.11.2011</th>
						<td>Langer Text wie zum Beispiel: Fehlerdiagnose, Gasanlag instandsetzen</td>
						<td>5000,00 EUR</td>
						<td>11111</td>
					</tr>
					<tr class="listrow1">
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
			</div>
			<div id="crm-tab-deliveries" class="crm-tab">
			</div>
			<div id="crm-tab-invoices" class="crm-tab">
			</div>
		</div>
	</div>
	<div id="lxc-id-hq-view" class="col-lg-3">
		<div id="crm-tabs-infos" class="tabwidget">
			<ul class="nav nav-tabs">
				<li class="nav-item">
					<a class="nav-link" aria-current="page" href="#crm-tab-contact-hist">Kontakthistorie</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" aria-current="page" href="#crm-tab-delivery-addr">Lieferanschrift</a>
				</li>
				<li class="nav-item">
					<a class="nav-link active" aria-current="page" href="#crm-tab-notes">Notizen</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" aria-current="page" href="#crm-tab-vars">Variablen</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" aria-current="page" href="#crm-tab-finance-infos">Finanzinfos</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" aria-current="page" href="#crm-tab-extra-infos">Infos</a>
				</li>
			</ul>
			<div id="crm-tab-contact-hist" class="crm-tab">
			</div>
			<div id="crm-tab-delivery-addr" class="crm-tab">
			</div>
			<div id="crm-tab-notes" class="crm-tab">
				<p>Hier stehen dann die vielen wichtigen Notizen.</p>
			</div>
			<div id="crm-tab-vars" class="crm-tab">
			</div>
			<div id="crm-tab-finance-infos" class="crm-tab">
			</div>
			<div id="crm-tab-extra-infos" class="crm-tab">
			</div>
		</div>
	</div>
</div>
</div>

</main>
<!-- Prototype-Ende -->

<?php
	//echo $objResult->{'end_content'};
?>

<script src="crm/js/crm.app.js"></script>
<script>
<?php
	foreach($objResult->{'javascripts_inline'} as $js) echo $js."\n";
?>
</script>

</body>
</html>

