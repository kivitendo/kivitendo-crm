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

    if( $result === false || curl_errno( $ch ) ){
        header( 'Location: http://localhost/kivitendo/controller.pl?action=LoginScreen/user_login' );//ToDo  Das ist definitiv falsch
        return;
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

    .crm-error {
        color: red;
        font-weight: bold;
    }

    .od-table-label {
        background-color: #EEEEEE;
    }

    .od-common-div {
        padding-top: 2em;
        padding-bottom: 2em;
    }

    .od-common {
        margin: auto;
        background-color: white;
    }

    .od-common td {
        padding: 0.25em;
        vertical-align: top;
    }

    .od-comments-div {
        padding-top: 2em;
    }

    .od-comments {
        background-color: white;
    }

    /* debug */
    /*
    * {
        outline: 1px solid red;
    }
    */

    .ui-autocomplete {
        position: absolute;
        cursor: default;
        z-index:1000 !important;
    }
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
    <div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div id="crm-hist-last" class="layout-actionbar-action layout-actionbar-submit">Zuletzt</div><span></span></div><div id="crm-history-list" class="layout-actionbar-combobox-list"></div></div>
    <div class="layout-actionbar-separator"></div>
    <div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div class="layout-actionbar-action layout-actionbar-submit">Neu</div><span></span></div><div class="layout-actionbar-combobox-list"><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-new-customer">Kunde</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-new-vendor">Lieferant</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-new-person">Person</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-scan"></div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-new-order">Auftrag</div></div></div>
    <div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div class="layout-actionbar-action layout-actionbar-submit">Bearbeiten</div><span></span></div><div class="layout-actionbar-combobox-list"><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-edit">Stammdaten</div></div></div>
</div>

<!-- Prototype-Start: -->

<?php
    echo $objResult->{'start_content'};
?>

<h1 class="tools" style="margin-top: 20px; height: 20px;"><span id="crm-wx-title"></span></h1>

<main>

<div id="crm-main-view" class="container-fluid">

<div id="crm-edit-order-dialog" style="display:none">
    <div class="od-common-div">
    <input id="od-customer-id" type="hidden"></input>
    <input id="od-lxcars-c_id" type="hidden"></input>
    <input id="od-oe-id" type="hidden"></input>
    <table class="od-common" width="100%">
        <tr>
            <td width="33%">
                <table>
                    <tr>
                        <td class="od-table-label"><label for="od-customer-name">Auftraggeber:</label></td>
                        <td><span id="od-customer-name"></span></td>
                    </tr>
                 </table>
            </td>
            <td width="33%">
                <table>
                    <tr>
                        <td class="od-table-label"><label for="od-lxcars-km_stnd">KM-Stand:</label></td>
                        <td><input class="od-oe-km_stnd" id="od-oe-km_stnd" type="number"></td>
                     </tr>
                    <tr>
                        <td class="od-table-label"><label for="od-lxcars-c_ln">Amtl.-Kennz.:</label></td>
                        <td><label id="od-lxcars-c_ln" type="text"></td>
                     </tr>
                    <tr>
                        <td class="od-table-label"><label for="od-oe-car_status">KfZ Ort:</label></td>
                        <td>
                            <select id="od-oe-car_status" type="select">
                                <option value=""></option>
                                <option value="Auto nicht hier">Auto nicht hier</option>
                                <option value="Auto hier">Auto hier</option>
                                <option value="Sonstiges zur Reparatur gebracht">Sonstiges zur Reparatur gebracht</option>
                                <option value="Bestellung">Bestellung</option>
                            </select>
                        </td>
                     </tr>
                    <tr>
                        <td class="od-table-label"><label for="od-oe-status">Status:</label></td>
                        <td>
                            <select id="od-oe-status" type="select">
                                <option value=""></option>
                                <option value="angenommen">angenommen</option>
                                <option value="bearbeitet">bearbeitet</option>
                                <option value="abgerechnet">abgerechnet</option>
                            </select>
                        </td>
                     </tr>
                </table>
            </td>
            <td width="33%">
                <table>
                    <tr>
                        <td class="od-table-label"><label for="od-oe-ordnumber">Auftragsnummer:</label></td>
                        <td><span id="od-oe-ordnumber"></span></td>
                     </tr>
                    <tr>
                        <td class="od-table-label"><label for="od-oe-internalorder">Interner Auftrag:</label></td>
                        <td><input id="od-oe-internalorder" type="checkbox"></td>
                    </tr>
                    <tr>
                        <td class="od-table-label"><label for="od-oe-finish_time">Fertigstellung:</label></td>
                        <td><input id="od-oe-finish_time" type="text"></td>
                    </tr>
                     <tr>
                        <td class="od-table-label"><label for="od-oe-itime">erstellt am:</label></td>
                        <td><span id="od-oe-itime"></span></td>
                     </tr>
                    <tr>
                        <td class="od-table-label"><label for="od-oe-mtime">bearbeitet am:</label></td>
                        <td><span id="od-oe-mtime"></span></td>
                     </tr>
                    <tr>
                        <td class="od-table-label"><label for="od-oe-employee_name">bearbeitet von:</label></td>
                        <td><input type="hidden" id="od-oe-employee_id"></input><span id="od-oe-employee_name"></span></td>
                     </tr>
                 </table>
            </td>
        </tr>
    </table>
    </div>

    <table class="ui-sortable" id="edit-order-table" width="100%">
        <thead>
            <tr class="listheading pin">
                <th class="listheading" nowrap width="25px" >Pos.</th>
                <th class="listheading" style='text-align:center' nowrap width="1"><img src="image/updown.png" alt="umsortieren"></th>
                <th class="listheading" style='text-align:center' nowrap width="1"><img src="image/close.png" alt="löschen"></th>
                <th class="listheading" style='text-align:center' nowrap width="1">E</th>
                <th id="od-partnumber-thead"  class="listheading" nowrap width="1" style="min-width: 4em">ArtNr.</th>
                <th id="od-partclass-thead"   class="listheading" nowrap width="2">Typ</th>
                <th id="od-desc-thead" class="listheading" nowrap  width="10">Beschreibung</th>
                <th id="od-longdesc-thead" class="listheading" nowrap>Langtext</th>
                <th id="od-qty-thead" class="listheading" nowrap width="5" >Menge</th>
                <th class="listheading" nowrap width="5" >Einheit </th>
                <th id="od-sellprice-thead" class="listheading" nowrap width="2" >Preis</th>
                <th id="od-discount-thead" class="listheading" nowrap width="5" >Rabatt in %</th>
                <th id="od-discount_thead-100" class="listheading" nowrap width="5" ><input value="100" type="hidden" id="od-ui-discount-100-all"></input><button id="od-ui-discount-100-all-btn" style="width: 3em;">100%</button></th>
                <th class="listheading" nowrap width="10">Gesamt </th>
                <th class="listheading" nowrap width="2" >Mechaniker <div><select id="od-ui-items-workers"></select></div></th>
                <th class="listheading" nowrap width="2" >
                    Status
                    <div>
                        <select id="od-ui-items-status-all">
                            <option value="gelesen">gelesen</option>
                            <option value="Bearbeitung">Bearbeitung</option>
                            <option value="erledigt">erledigt</option>
                        </select>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <div class="od-comments-div" style="float: right">
        <table>
            <tr>
                <td>
                    <label for="od-netamount" class="od-table-label">Netto:</label>
                </td>
                <td>
                    <input id="od-netamount" type="text" readonly="readonly" size="5"></input>
                </td>
                <td>
                    <label for="od-amount" class="od-table-label" style="margin-left: 2em">Brutto:</label>
                </td>
                <td>
                    <input id="od-amount" type="text" readonly="readonly" size="5"></input>
                </td>
             </tr>
        </table>
    </div>
    <div class="od-comments-div">
    <table class="od-comments">
        <tr>
             <td class="od-table-label">
                Interne Bemerkungen zum Kunden
            </td>
            <td class="od-table-label">
                Interne Bemerkungen zum Auto
            </td>
             <td class="od-table-label">
                Interne Bemerkungen zur Rechnung
            </td>
        </tr>
        <tr>
            <td>
                <textarea id="od-customer-notes" cols="50" rows="8"></textarea>
            </td>
             <td>
                <textarea id="od-lxcars-c_text" cols="50" rows="8"></textarea>
            </td>
            <td>
                <textarea id="od-oe-intnotes" cols="50" rows="8"></textarea>
            </td>
         </tr>
    </table>
    </div>
</div>

<div id="crm-edit-article-dialog" style="display:none">
    <div id="edit-article-hidden"></div>
    <table id="edit-article-form">
        <thead></thead>
        <tbody></tbody>
    </table>
</div>

<div id="crm-edit-car-dialog" style="display:none">
    <div id="edit-car-hidden"></div>
    <table id="edit-car-form">
        <thead></thead>
        <tbody></tbody>
    </table>
</div>

<div id="crm-wx-customer-dialog" style="display:none">
    <div id="crm-tabs-main" class="tabwidget">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a id="crm-nav-billaddr" class="nav-link active" aria-current="page" href="#crm-tab-billaddr">Rechnungsaddresse</a>
            </li>
            <li class="nav-item">
                <a id="crm-nav-deladdr" class="nav-link" aria-current="page" href="#crm-tab-deladdr">Lieferanschrif</a>
            </li>
            <li class="nav-item">
                <a id="crm-nav-banktax" class="nav-link" aria-current="page" href="#crm-tab-banktax">Bank/Steuer</a>
            </li>
            <li class="nav-item">
                <a id="crm-nav-extras" class="nav-link" aria-current="page" href="#crm-tab-extras">Sonstiges</a>
            </li>
            <li class="nav-item">
                <a id="crm-nav-vars" class="nav-link" aria-current="page" href="#crm-tab-vars">Variablen</a>
            </li>
        </ul>
        <div id="crm-billaddr-cv"></div>
        <div id="crm-tab-billaddr" class="crm-tab">
            <table id="billaddr-form">
                <thead></thead>
                <tbody></tbody>
            </table>
            <table id="car-form" style="padding-top: 1em; display:none">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
        <div id="crm-tab-deladdr" class="crm-tab">
            <table id="deladdr-form">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
        <div id="crm-tab-banktax" class="crm-tab">
            <table id="banktax-form">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
        <div id="crm-tab-extras" class="crm-tab">
            <table id="extras-form">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
        <div id="crm-tab-vars" class="crm-tab">
            <table id="vars-form">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div id="crm-fsscan-dlg" style="display:none;">
    <div>
        <table id="crm-fsscan-list0">
            <thead>
              <tr class="listheading">
                <th class="xlistheading" id="date">Datum</th>
                <th class="xlistheading" id="firstname">Vorname</th>
                <th class="xlistheading" id="name1">Name</th>
                <th class="xlistheading" id="licenseplate">Kennzeichen</th>
              </tr>
            </thead>
            <tbody id="crm-fsscan-list"></tbody>
        </table>
    </div>
</div>

<div id="crm-fsscan-customer-dlg" style="display:none">
    <div><input id="crm-fsscan-edit-customer" type="text" size="42"></input></div>
    <div style="padding-top: 1em">
        <table id="crm-fsscan-customer-list0">
            <thead>
              <tr class="listheading">
                <th class="xlistheading" id="date">Datum</th>
                <th class="xlistheading" id="firstname">Vorname</th>
                <th class="xlistheading" id="name1">Name</th>
                <th class="xlistheading" id="licenseplate">Stadt</th>
              </tr>
            </thead>
             <tbody id="crm-fsscan-customer-list"></tbody>
        </table>
    </div>
</div>

<div id="crm-wx-base-data" class="row crm-p2">
    <div class="col-lg-3">
        <div id="crm-wx-contact">
            <input type="hidden" id="crm-cvpa-id"></input>
            <input type="hidden" id="crm-cvpa-src"></input>
            <div><strong><span id="crm-contact-name"></span></strong></div>
            <div class="crm-pt05"><span id="crm-contact-street"></span></div>
            <div class="crm-pt025"><span id="crm-contact-country"></span>-<span id="crm-contact-city"></span></div>
            <div class="crm-pt2"><strong>Hauptkontakt</strong></div>
            <div class="crm-pt05"><span id="crm-contact-contact"></span></div>
                <table>
                  <tr>
                    <td>Telefon:</td><td><button id="crm-contact-phone1"></button></td><td><button id="tel1_dialog_button">T</button><div id="tel1_dialog"></div></td><td><button class="copy" number="{Telefon}" title="Copy">C</button></td><td ><button class="whatsapp" number="{Telefon}" name="{Fname1}" title="Whatsapp" ><img src="../image/whatsapp.png" alt="Whatsapp" ></button></td>
                  </tr>
                  <tr>
                    <td>Telefon:</td><td><button id="crm-contact-phone2"></button></td><td><button id="tel2_dialog_button">T</button><div id="tel2_dialog"></div></td><td><button class="copy" number="{Fax}" title="Copy">C</button></td><td ><button class="whatsapp" number="{Fax}" name="{Fname1}" title="Whatsapp" ><img src="../image/whatsapp.png" alt="Whatsapp" ></button></td>
                  </tr>
               </table>
           <div id="crm-wx-contact-email" class="row crm-pt025">
                <div class="col-md-2">E-Mail:</div>
                <div class="col-md-10"><button id="crm-contact-email">Kein Eintrag</button></div>
            </div>
        </div>
        <div id="crm-wx-cars" style="display:none;">
            <table width="100%" class="crm-pt2">
                <thead>
                <tr>
                    <td class="listheading">Kenzeichen</th>
                    <td class="listheading">Hersteller</th>
                    <td class="listheading">Fahrzeugtyp</th>
                    <td class="listheading">Fahrzeugart</th>
                </tr>
                </thead>
                <tbody id="crm-cars-table">
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-6">
        <div id="crm-tabs-main" class="tabwidget">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a id="crm-nav-offers" class="nav-link active" aria-current="page" href="#crm-tab-offers">Angebote</a>
                </li>
                <li class="nav-item">
                    <a id="crm-nav-orders" class="nav-link" aria-current="page" href="#crm-tab-orders">Aufträge</a>
                </li>
                <li class="nav-item">
                    <a id="crm-nav-deliveries" class="nav-link" aria-current="page" href="#crm-tab-deliveries">Lieferscheine</a>
                </li>
                <li class="nav-item">
                    <a id="crm-nav-invoices" class="nav-link" aria-current="page" href="#crm-tab-invoices">Rechnungen</a>
                </li>
            </ul>
            <div id="crm-tab-offers" class="crm-tab">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <td class="listheading">Datum</th>
                        <td class="listheading">Erste Position</th>
                        <td class="listheading">Betrag</th>
                        <td class="listheading">Nummer</th>
                    </tr>
                    </thead>
                    <tbody id="crm-offers-table">
                    </tbody>
                </table>
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
                    <tbody id="crm-orders-table">
                    </tbody>
                </table>
            </div>
            <div id="crm-tab-deliveries" class="crm-tab">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <td class="listheading">Datum</th>
                        <td class="listheading">Erste Position</th>
                        <td class="listheading">Lieferdatum</th>
                        <td class="listheading">Nummer</th>
                    </tr>
                    </thead>
                    <tbody id="crm-deliveries-table">
                    </tbody>
                </table>
            </div>
            <div id="crm-tab-invoices" class="crm-tab">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <td class="listheading">Datum</th>
                        <td class="listheading">Erste Position</th>
                        <td class="listheading">Betrag</th>
                        <td class="listheading">Nummer</th>
                    </tr>
                    </thead>
                    <tbody id="crm-invoices-table">
                    </tbody>
                </table>
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
                    <a class="nav-link" aria-current="page" href="#crm-tab-contact-hist">Ansprechpartner</a>
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
    echo $objResult->{'end_content'};
?>


<script>
<?php
    foreach($objResult->{'javascripts_inline'} as $js) echo $js."\n";
?>
</script>
<script src="crm/js/crm.app/app.js"></script>
<script src="crm/js/crm.app/form.js"></script>
<script src="crm/js/crm.app/cvp.js"></script>
<script src="crm/js/crm.app/car.js"></script>
<script src="crm/js/crm.app/order.js"></script>
<script src="crm/js/crm.app/article.js"></script>
<script src="crm/js/crm.app/phonecall.js"></script>
</body>
</html>