<?php
    require_once __DIR__.'/inc/connection.php';// für Session wichtig
    $baseUrl = isset( $_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
    $baseUrl.= '://'.$_SERVER['SERVER_NAME'].str_replace( basename(__FILE__), "", $_SERVER['REQUEST_URI'] );
    $url = $baseUrl.'controller.pl?action=Layout/empty&format=json';
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_TIMEOUT, 1000 );
    curl_setopt( $ch, CURLOPT_ENCODING, 'gzip,deflate' );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array (
                "Connection: keep-alive",
                "Cookie: ".$_SESSION["cookie"]."=".$_SESSION['sessid']."; ".$_SESSION["cookie"]."_api_token=".$_SESSION["token"]['api_token']
                ));
    $result = curl_exec( $ch );

    if( $result === false || curl_errno( $ch ) ){
        header( 'Location: controller.pl?action=LoginScreen/user_login' );
        //echo curl_error( $ch );
        return;
    }
    curl_close( $ch );

    $objResult = json_decode( $result );
?>

<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />

<!--
<link rel="stylesheet" href="css/lx-office-erp/main.css" type="text/css">
<link rel="stylesheet" href="css/lx-office-erp/menu.css" type="text/css">
<link rel="stylesheet" href="css/lx-office-erp/frame_header/header.css" type="text/css">
<link rel="stylesheet" href="css/lx-office-erp/dhtmlsuite/menu-item.css" type="text/css">
<link rel="stylesheet" href="css/lx-office-erp/dhtmlsuite/menu-bar.css" type="text/css">
<link rel="stylesheet" href="css/icons16.css" type="text/css">
-->

<!--
<link rel="stylesheet" href="css/common.css" type="text/css" title="Stylesheet">
-->

<link rel="stylesheet" href="css/design40/main.css" type="text/css" title="Stylesheet">
<link rel="stylesheet" href="css/design40/dhtmlsuite/menu-item.css" type="text/css" title="Stylesheet">
<link rel="stylesheet" href="css/design40/dhtmlsuite/menu-bar.css" type="text/css" title="Stylesheet">

<link rel="stylesheet" href="css/lx-office-erp/list_accounts.css" type="text/css" title="Stylesheet">
<link rel="stylesheet" href="css/jquery.autocomplete.css" type="text/css" title="Stylesheet">
<link rel="stylesheet" href="css/jquery.multiselect2side.css" type="text/css" title="Stylesheet">
<link rel="stylesheet" href="css/ui-lightness/jquery-ui.css" type="text/css" title="Stylesheet">
<link rel="stylesheet" href="css/lx-office-erp/jquery-ui.custom.css" type="text/css" title="Stylesheet">
<link rel="stylesheet" href="css/tooltipster.css" type="text/css" title="Stylesheet">
<link rel="stylesheet" href="css/themes/tooltipster-light.css" type="text/css" title="Stylesheet">
<link rel="stylesheet" href="crm/app.plugins/datetimepicker/jquery.datetimepicker.min.css" type="text/css" title="Stylesheet">

<link rel="stylesheet" href="crm/css/crm.app/bootstrap-grid.min.css" type="text/css" title="Stylesheet">

<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/common.js"></script>
<script type="text/javascript" src="js/namespace.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/kivi.js"></script>
<script type="text/javascript" src="js/locale/de.js"></script>
<script type="text/javascript" src="js/kivi.QuickSearch.js"></script>
<script type="text/javascript" src="js/dhtmlsuite/menu-for-applications.js"></script>
<script type="text/javascript" src="js/kivi.ActionBar.js"></script>

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
<!--
<script type="text/javascript" src="js/jquery/ui/i18n/jquery.ui.datepicker-de.js"></script>
<script type="text/javascript" src="js/jquery/ui/i18n/jquery.ui.datepicker-de.js"></script>
<script type="text/javascript" src="crm/jquery-add-ons/date-time-picker.js"></script>
<script type="text/javascript" src="crm/jquery-add-ons/german-date-time-picker.js"></script>
-->
<script type="text/javascript" src='crm/app.plugins/datetimepicker/jquery.datetimepicker.full.min.js'></script>
<script type="text/javascript" src="crm/nodejs/node_modules/tinymce/tinymce.min.js"></script>
<script type="text/javascript" src='crm/app.plugins/fullcalendar/packages/moment/moment.min.js'></script>

    <!-- Section CSS -->
    <!-- jQuery UI (REQUIRED) -->
    <!--
    <link rel="stylesheet" href="jquery/jquery-ui-1.12.0.css" type="text/css">
    -->

    <!-- elfinder css -->
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/commands.css"    type="text/css">
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/common.css"      type="text/css">
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/contextmenu.css" type="text/css">
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/cwd.css"         type="text/css">
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/dialog.css"      type="text/css">
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/fonts.css"       type="text/css">
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/navbar.css"      type="text/css">
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/places.css"      type="text/css">
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/quicklook.css"   type="text/css">
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/statusbar.css"   type="text/css">
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/theme.css"       type="text/css">
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/toast.css"       type="text/css">
    <link rel="stylesheet" href="crm/app.plugins/elfinder/css/toolbar.css"     type="text/css">

    <!-- Section JavaScript -->
    <!-- jQuery and jQuery UI (REQUIRED) -->
    <!--
    <script src="jquery/jquery-1.12.4.js" type="text/javascript" charset="utf-8"></script>
    <script src="jquery/jquery-ui-1.12.0.js" type="text/javascript" charset="utf-8"></script>
    -->

    <!-- elfinder core -->
    <script src="crm/app.plugins/elfinder/js/elFinder.js"></script>
    <script src="crm/app.plugins/elfinder/js/elFinder.version.js"></script>
    <script src="crm/app.plugins/elfinder/js/jquery.elfinder.js"></script>
    <script src="crm/app.plugins/elfinder/js/elFinder.mimetypes.js"></script>
    <script src="crm/app.plugins/elfinder/js/elFinder.options.js"></script>
    <script src="crm/app.plugins/elfinder/js/elFinder.options.netmount.js"></script>
    <script src="crm/app.plugins/elfinder/js/elFinder.history.js"></script>
    <script src="crm/app.plugins/elfinder/js/elFinder.command.js"></script>
    <script src="crm/app.plugins/elfinder/js/elFinder.resources.js"></script>

    <!-- elfinder dialog -->
    <script src="crm/app.plugins/elfinder/js/jquery.dialogelfinder.js"></script>

    <!-- elfinder default lang -->
    <script src="crm/app.plugins/elfinder/js/i18n/elfinder.en.js"></script>

    <!-- elfinder ui -->
    <script src="crm/app.plugins/elfinder/js/ui/button.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/contextmenu.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/cwd.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/dialog.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/fullscreenbutton.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/navbar.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/navdock.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/overlay.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/panel.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/path.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/places.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/searchbutton.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/sortbutton.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/stat.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/toast.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/toolbar.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/tree.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/uploadButton.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/viewbutton.js"></script>
    <script src="crm/app.plugins/elfinder/js/ui/workzone.js"></script>

    <!-- elfinder commands -->
    <script src="crm/app.plugins/elfinder/js/commands/archive.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/back.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/chmod.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/colwidth.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/copy.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/cut.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/download.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/duplicate.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/edit.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/empty.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/extract.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/forward.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/fullscreen.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/getfile.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/help.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/hidden.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/hide.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/home.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/info.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/mkdir.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/mkfile.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/netmount.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/open.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/opendir.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/opennew.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/paste.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/places.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/preference.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/quicklook.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/quicklook.plugins.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/reload.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/rename.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/resize.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/restore.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/rm.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/search.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/selectall.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/selectinvert.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/selectnone.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/sort.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/undo.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/up.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/upload.js"></script>
    <script src="crm/app.plugins/elfinder/js/commands/view.js"></script>

    <!-- elfinder languages -->
    <script src="crm/app.plugins/elfinder/js/i18n/elfinder.de.js"></script>

    <!-- elfinder 1.x connector API support (OPTIONAL) -->
    <script src="crm/app.plugins/elfinder/js/proxy/elFinderSupportVer1.js"></script>

    <!-- Extra contents editors (OPTIONAL) -->
    <script src="crm/app.plugins/elfinder/js/extras/editors.default.js"></script>

    <!-- GoogleDocs Quicklook plugin for GoogleDrive Volume (OPTIONAL) -->
    <script src="crm/app.plugins/elfinder/js/extras/quicklook.googledocs.js"></script>

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

    .crm-pt3 {
        padding-top: 3em;
    }

    .crm-pb1 {
        padding-bottom: 3em;
    }

    .crm-mt2 {
        margin-top: 2em;
    }

    .crm-mt3 {
        margin-top: 3em;
    }

    .crm-tab {
        overflow: auto;
    }


    /** Font size **/

    .crm-fs {
        font-size: 13px;
    }

    .layout-actionbar div.layout-actionbar-action {
        font-size: 13px;
    }

    .tabwidget>ul li.ui-state-default a, .tabwidget>ul li.ui-state-default a:link {
        font-size: 13px;
    }

    button {
        font-size: 13px;
    }

    #content input, #content select, #content option, #content textarea {
        font-size: 13px;
    }

    .od-table-label {
        font-weight: bold;
    }

    /************/


     main button {
        min-width: 25px;
        min-height: 25px;
    }

    .crm-error {
        color: red;
        font-weight: bold;
    }

   .od-common-style {
        margin: auto;
    }

    .od-common-style td {
        padding: 0.25em;
        vertical-align: top;
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
        z-index:1032 !important;
    }

    .ui-menu {
        width: 15em;
        z-index:1000 !important;
    }

    .ui-widget-header {
        padding: 0.2em;
    }

    .ui-contact-btn {
        white-space: nowrap;
    }

    .ui-contact-fx-btn {
        width: 20px;
        height: 20px;
    }

    .crm-ui-button{
        color: #fff;
        background-color: #406449;
        border: 1px #406449 solid;
        display: inline-block;
        cursor: pointer;
        width: auto;
        padding: .2em .6em;
        font-size: 9pt;
        font-weight: normal;
        font-style: normal;
        text-align: center;
        border-style: solid;
        border-width: 1px;
        border-radius: 6px;
    }

    ul.ui-autocomplete.ui-menu{
        width:450px;
        z-index:1031 !important;
    }

    .crm-fast-search {
        font-size: 12px;
        box-sizing: border-box;
        position: relative;
        display: block;
        float: left;
        margin-top: 2px;
        margin-left: 10px;
        z-index: 1031;
    }

    .tabwidget>ul.ui-tabs-nav {
        position: unset;
        top: unset;
    }

    .table {
        width: 100%;
    }

    .contact-box{
        border: 1px solid #ccc;
        padding: 10px;
    }

    iframe{
        display: block;  /* iframes are inline by default */
        height: 100vh;  /* Set height to 100% of the viewport height */
        width: 100vw;  /* Set width to 100% of the viewport width */
        border: none; /* Remove default border */
        background: transparent; /* Just for styling */
    }

</style>

</head>

<body class="crm-fs">

<div id="message-dialog" style="display:none;">
    <div id="message-dialog-error" style="display:none"></div>
    <p id="message-dialog-text"></p>
    <p id="message-dialog-debug" style="display:none"></p>
</div>

<div id="crm-phone-call-config-dialog"></div>

<?php
    echo $objResult->{'pre_content'};
?>
<div id="crm-actionbar" class="layout-actionbar">
    <input id="crm-widget-quicksearch" placeholder="Schnellsuche" maxlength="20" class="crm-fast-search ui-autocomplete-input" style="" autocomplete="off">
    <div class="layout-actionbar-separator"></div>
    <div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div id="crm-hist-last" class="layout-actionbar-action layout-actionbar-submit">Verlauf</div><span></span></div><div id="crm-history-list" class="layout-actionbar-combobox-list"></div></div>
    <div class="layout-actionbar-separator"></div>
    <div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div class="layout-actionbar-action layout-actionbar-submit">Neu</div><span></span></div><div class="layout-actionbar-combobox-list"><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-new-customer">Kunde</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-new-vendor">Lieferant</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-new-person">Ansprechperson</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-scan"></div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-new-car">Neues Auto</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-new-offer">Angebot</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-new-order">Auftrag*</div></div></div>
    <div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div class="layout-actionbar-action layout-actionbar-submit">Bearbeiten</div><span></span></div><div class="layout-actionbar-combobox-list"><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-edit">Stammdaten</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-search-order">Auftragssuche</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-elfinder">Dokumente</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-calendar">Kalender</div></div></div>
    <div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-search-order-btn">Auftragssuche</div>
    <div class="layout-actionbar-action layout-actionbar-submit" id="crm-phonecall-list-btn">Anrufliste</div>
    <div class="layout-actionbar-action layout-actionbar-submit" id="crm-test-ajax-btn">Test</div>
</div>

<!-- Prototype-Start: -->

<?php
    echo $objResult->{'start_content'};
?>

<h1 class="tools"><span id="crm-wx-title"></span><span id="crm-wx-subtitle"></span></h1>

<main>

<div id="crm-main-view" class="container-fluid">

<div id="crm-edit-order-dialog" style="padding-top: 7em; display:none">

    <div id="od-oe-workflow" class="layout-actionbar" style="top: 140px; left: 28px; border: 0;">
        <div id="od-oe-close-btn" class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmEditOrderCloseView();">Schließen</div>
        <div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div class="layout-actionbar-action layout-actionbar-submit">Workflow</div><span></span></div><div class="layout-actionbar-combobox-list"><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-edit" onclick="crmConfirmInsertOfferFromOrder();">Vorlage für Angebot</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-search-order" onclick="crmConfirmInsertInvoiceFromOrder();">Vorlage für Rechnung</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-delete-order">Auftrag löschen</div></div></div>
        <div class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmEditOrderCallPrinter1();">Printer1</div>
        <div class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmEditOrderCallPrinter2();">Printer2</div>
        <div class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmEditOrderCallPDF();">PDF</div>
        <div class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmEditOrderCallCoparts();">Coparts</div>
        <div class="layout-actionbar-action layout-actionbar-submit" value="" style="margin-left: 2em;" onclick="crmEditOrderGotoCustomer();">Auftraggeber</div>
        <div id="od_lxcars_to_car_btn" class="layout-actionbar-action layout-actionbar-submit" value="">Auto</div>
        <div id="od_lxcars_aag_btn" class="layout-actionbar-action layout-actionbar-submit" value="">AAG-Online</div>
    </div>

    <div id="od-off-menus">
        <div class="layout-actionbar" style="top: 140px; left: 28px; border: 0;">
            <div id="od-off-close-btn" class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmEditOrderCloseView();">Schließen</div>
            <div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div class="layout-actionbar-action layout-actionbar-submit">Workflow</div><span></span></div><div class="layout-actionbar-combobox-list"><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-edit" onclick="crmConfirmXYZ();">Wiederverwenden*</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-delete-offer">Angebot löschen</div></div></div>
        </div>
        <div class="layout-actionbar" style="top: 140px; left: 350px; border: 0;">
           <div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div id="od-off-current-printer" value="" class="layout-actionbar-action layout-actionbar-submit" style="width: auto">Drucken</div><span></span></div><div id="od-off-printers" class="layout-actionbar-combobox-list"></div></div>
            <div class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmPrintOrder( crmOrderPrintTargetEnum.Printer );">Drucken</div>
            <div class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmPrintOrder( crmOrderPrintTargetEnum.Screen );">PDF-Druckvorschau</div>
            <div class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmEmailOrder()">E-Mail</div>
        </div>
    </div>

    <div  style="display:none">
        <form id="od-off-print-form" method="post" action="oe.pl">
        </form>
    </div>

    <div id="od-inv-menus">
        <div class="layout-actionbar" style="top: 140px; left: 28px; border: 0;">
            <div class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmEditOrderCloseView();">Schließen</div>
            <div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div class="layout-actionbar-action layout-actionbar-submit">Workflow</div><span></span></div><div class="layout-actionbar-combobox-list"><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-edit" onclick="crmConfirmXYZ();">Wiederverwenden*</div><div class="layout-actionbar-action layout-actionbar-submit" id="crm-wf-search-order" onclick="crmConfirmXYZ();">Vorlage für Auftrag*</div></div></div>
        </div>
        <div class="layout-actionbar" style="top: 140px; left: 350px; border: 0;">
           <div class="layout-actionbar-combobox"><div class="layout-actionbar-combobox-head"><div id="od-inv-current-printer" value="" class="layout-actionbar-action layout-actionbar-submit" style="width: auto">Drucken</div><span></span></div><div id="od-inv-printers" class="layout-actionbar-combobox-list"></div></div>
            <div class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmPrintOrder( crmOrderPrintTargetEnum.Printer );">Drucken</div>
            <div class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmPrintOrder( crmOrderPrintTargetEnum.Screen );">PDF-Druckvorschau</div>
            <div class="layout-actionbar-action layout-actionbar-submit" value="" onclick="crmEmailOrder()">E-Mail</div>
        </div>
    </div>

    <div  style="display:none">
        <form id="od-inv-print-form" method="post" action="is.pl">
        </form>
    </div>

    <div class="wrapper panel-wrapper">
        <div class="od-common-div input-panel control-panel" style="margin-bottom: 0.5em">
            <input id="od-customer-id" type="hidden"></input>
            <input id="od-lxcars-c_id" type="hidden"></input>
            <input id="od-oe-id" type="hidden"></input>
            <input id="od-off-id" type="hidden"></input>
            <input id="od-inv-id" type="hidden"></input>
            <!-- AR ist die chart_id auf die Forderung gebucht wird (ToDo: muss auswählbar sein; wo in DB???)-->
            <input id="od-ar-id" type="hidden" value="14"></input>
            <table id="od-oe-common-table" class="od-oe-common od-common-style" width="100%">
                <tr>
                    <td width="33%">
                        <table>
                            <tr>
                                <td class="od-table-label"><label for="od_customer_name" onclick="crmCloseView();">Auftraggeber:</label></td>
                                <td><input type="text" id="od_customer_name"></input></td>
                            </tr>
                            <tr>
                                <td class="od-table-label">Telefon1:</td>
                                <td><input type="text" id="crm_oe_contact_note_phone1" size="10" disabled></input></td>
                                <td><button id="crm_oe_contact_phone1" class="ui-contact-btn clickToCall1"></button></td>
                                <td><button id="crm_oe_contact_phone1_dialog_button" class="clickToCall1 ui-contact-fx-btn">T</button><div id="crm_oe_contact_phone1_dialog"></div></td>
                                <td><button id="crm_oe_copy_contact_phone1" class="copy clickToCall1 ui-contact-fx-btn" title="Copy">C</button></td>
                                <td ><button id="crm_oe_whatsapp1" class="whatsapp clickToCall1 ui-contact-fx-btn" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td>
                            </tr>
                            <tr>
                                <td class="od-table-label">Telefon2:</td>
                                <td><input type="text" id="crm_oe_contact_note_phone2" size="10" disabled></input></td>
                                <td><button id="crm_oe_contact_phone2" class="ui-contact-btn clickToCall2"></button></td>
                                <td><button id="crm_oe_contact_phone2_dialog_button" class="clickToCall2 ui-contact-fx-btn">T</button><div id="crm_oe_contact_phone2_dialog"></div></td>
                                <td><button id="crm_oe_copy_contact_phone2" class="copy clickToCall2 ui-contact-fx-btn"  title="Copy">C</button></td>
                                <td ><button id="crm_oe_whatsapp2" class="whatsapp clickToCall2 ui-contact-fx-btn" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td>
                            </tr>
                            <tr>
                                <td class="od-table-label">Telefon3:</td>
                                <td><input type="text" id="crm_oe_contact_note_phone3" size="10" disabled></input></td>
                                <td><button id="crm_oe_contact_phone3" class="ui-contact-btn clickToCall3"></button></td>
                                <td><button id="crm_oe_contact_phone3_dialog_button" class="clickToCall3 ui-contact-fx-btn">T</button><div id="crm_oe_contact_phone3_dialog"></div></td>
                                <td><button id="crm_oe_copy_contact_phone3" class="copy clickToCall3 ui-contact-fx-btn"  title="Copy">C</button></td>
                                <td ><button id="crm_oe_whatsapp3" class="whatsapp clickToCall3 ui-contact-fx-btn" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td>
                            </tr>
                            <tr>
                                <td class="od-table-label">E-Mail:</td>
                                <td colspan="4"><button id="crm_oe_contact_email">Kein Eintrag</button></td>
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
                                <td class="od-table-label"><label id="od_lxcars_to_car" for="od_lxcars_c_ln">Amtl.-Kennz.:</label></td>
                                <td><input id="od_lxcars_c_ln" type="text"></input><button onclick="crmEditOrderShowCarData();">Info</button></td>
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
                             <tr>
                                <td class="od-table-label"><label for="od-oe-printed">gedruckt:</label></td>
                                <td>
                                    <input type="checkbox" id="od-oe-printed" disabled="disabled">   
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
                                <td class="od-table-label"><label for="od-oe-delivery_time">Bringetermin:</label></td>
                                <td><input id="od-oe-delivery_time" type="text" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td class="od-table-label"><label for="od-oe-finish_time">Fertigstellung:</label></td>
                                <td><div><input id="od-oe-finish_time" type="text" autocomplete="off"><button id="od_oe_finish_now">Jetzt</button></div></td>
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
            <table id="od-inv-common-table" class="od-inv-common od-common-style" width="100%">
                <tr>
                    <td width="33%">
                        <table>
                            <tr>
                                <td class="od-table-label"><label for="od_inv_customer_name">Kunde:</label></td>
                                <td colspan="4"><input type="text" id="od_inv_customer_name"></input></td>
                            </tr>
                            <tr>
                                <td class="od-table-label">Telefon1:</td>
                                <td><input type="text" id="crm_inv_contact_note_phone1" size="10" disabled></input></td>
                                <td><button id="crm_inv_contact_phone1" class="ui-contact-btn clickToCall1"></button></td>
                                <td><button id="crm_inv_contact_phone1_dialog_button" class="clickToCall1 ui-contact-fx-btn">T</button><div id="crm_inv_contact_phone1_dialog"></div></td>
                                <td><button id="crm_inv_copy_contact_phone1" class="copy clickToCall1 ui-contact-fx-btn" title="Copy">C</button></td>
                                <td ><button id="crm_inv_whatsapp1" class="whatsapp clickToCall1 ui-contact-fx-btn" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td>
                            </tr>
                            <tr>
                                <td class="od-table-label">Telefon2:</td>
                                <td><input type="text" id="crm_inv_contact_note_phone2" size="10" disabled></input></td>
                                <td><button id="crm_inv_contact_phone2" class="ui-contact-btn clickToCall2"></button></td>
                                <td><button id="crm_inv_contact_phone2_dialog_button" class="clickToCall2 ui-contact-fx-btn">T</button><div id="crm_inv_contact_phone2_dialog"></div></td>
                                <td><button id="crm_inv_copy_contact_phone2" class="copy clickToCall2 ui-contact-fx-btn"  title="Copy">C</button></td>
                                <td ><button id="crm_inv_whatsapp2" class="whatsapp clickToCall2 ui-contact-fx-btn" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td>
                            </tr>
                            <tr>
                                <td class="od-table-label">Telefon3:</td>
                                <td><input type="text" id="crm_inv_contact_note_phone3" size="10" disabled></input></td>
                                <td><button id="crm_inv_contact_phone3" class="ui-contact-btn clickToCall3"></button></td>
                                <td><button id="crm_inv_contact_phone3_dialog_button" class="clickToCall3 ui-contact-fx-btn">T</button><div id="crm_inv_contact_phone3_dialog"></div></td>
                                <td><button id="crm_inv_copy_contact_phone3" class="copy clickToCall3 ui-contact-fx-btn"  title="Copy">C</button></td>
                                <td ><button id="crm_inv_whatsapp2" class="whatsapp clickToCall3 ui-contact-fx-btn" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td>
                            </tr>
                            <tr>
                                <td class="od-table-label">E-Mail:</td>
                                <td colspan="4"><button id="crm_inv_contact_email">Kein Eintrag</button></td>
                            </tr>
                         </table>
                    </td>
                    <td width="33%">
                        <table>
                            <tr>
                                <td class="od-table-label"><label for="od-inv-shipvia">KM-Stand:</label></td>
                                <td><input class="od-inv-shipvia" id="od-inv-shipvia" type="number"></td>
                             </tr>
                            <tr>
                                <td class="od-table-label"><label for="od-inv-shippingpoint">Amtl.-Kennz.:</label></td>
                                <td><input id="od-inv-shippingpoint" type="hidden" value=""></input><input id="od_inv_shippingpoint" type="text"></input></td>
                             </tr>
                       </table>
                    </td>
                    <td width="33%">
                        <table>
                            <tr>
                                <td class="od-table-label"><label for="od-inv-invnumber">Rechnungsnummer:</label></td>
                                <td><span id="od-inv-invnumber"></span></td>
                             </tr>
                            <tr>
                                <td class="od-table-label"><label for="od-inv-ordnumber">Auftragsnummer:</label></td>
                                <td><span id="od-inv-ordnumber"></span></td>
                             </tr>
                            <tr>
                                <td class="od-table-label"><label for="od-oe-itime">erstellt am:</label></td>
                                <td><span id="od-inv-itime"></span></td>
                             </tr>
                            <tr>
                                <td class="od-table-label"><label for="od-oe-mtime">bearbeitet am:</label></td>
                                <td><span id="od-inv-mtime"></span></td>
                             </tr>
                            <tr>
                                <td class="od-table-label"><label for="od-inv-employee_name">bearbeitet von:</label></td>
                                <td><input type="hidden" id="od-inv-employee_id"></input><span id="od-inv-employee_name"></span></td>
                             </tr>
                         </table>
                    </td>
                </tr>
            </table>
            <table id="od-off-common-table" class="od-off-common od-common-style" width="100%">
                <tr>
                    <td width="33%">
                        <table>
                            <tr>
                                <td class="od-table-label"><label for="od_off_customer_name">Kunde:</label></td>
                                <td><input type="text" id="od_off_customer_name"></input></td>
                            </tr>
                            <tr>
                                <td class="od-table-label">Telefon1:</td>
                                <td><input type="text" id="crm_off_contact_note_phone1" size="10" disabled></input></td>
                                <td><button id="crm_off_contact_phone1" class="ui-contact-btn clickToCall1"></button></td>
                                <td><button id="crm_off_contact_phone1_dialog_button" class="clickToCall1 ui-contact-fx-btn">T</button><div id="crm_off_contact_phone1_dialog"></div></td>
                                <td><button id="crm_off_copy_contact_phone1" class="copy clickToCall1 ui-contact-fx-btn" title="Copy">C</button></td>
                                <td ><button id="crm_off_whatsapp1" class="whatsapp clickToCall1 ui-contact-fx-btn" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td>
                            </tr>
                            <tr>
                                <td class="od-table-label">Telefon2:</td>
                                <td><input type="text" id="crm_off_contact_note_phone2" size="10" disabled></input></td>
                                <td><button id="crm_off_contact_phone2" class="ui-contact-btn clickToCall2"></button></td>
                                <td><button id="crm_off_contact_phone2_dialog_button" class="clickToCall2 ui-contact-fx-btn">T</button><div id="crm_off_contact_phone2_dialog"></div></td>
                                <td><button id="crm_off_copy_contact_phone2" class="copy clickToCall2 ui-contact-fx-btn"  title="Copy">C</button></td>
                                <td ><button id="crm_inv_whatsapp2" class="whatsapp clickToCall2 ui-contact-fx-btn" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td>
                            </tr>
                            <tr>
                                <td class="od-table-label">Telefon3:</td>
                                <td><input type="text" id="crm_off_contact_note_phone3" size="10" disabled></input></td>
                                <td><button id="crm_off_contact_phone3" class="ui-contact-btn clickToCall3"></button></td>
                                <td><button id="crm_off_contact_phone3_dialog_button" class="clickToCall3 ui-contact-fx-btn">T</button><div id="crm_off_contact_phone3_dialog"></div></td>
                                <td><button id="crm_off_copy_contact_phone3" class="copy clickToCall3 ui-contact-fx-btn"  title="Copy">C</button></td>
                                <td ><button id="crm_inv_whatsapp3" class="whatsapp clickToCall3 ui-contact-fx-btn" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td>
                            </tr>
                            <tr>
                                <td class="od-table-label">E-Mail:</td>
                                <td colspan="4"><button id="crm_inv_contact_email">Kein Eintrag</button></td>
                            </tr>
                         </table>
                    </td>
                    <td width="33%">
                        <table>
                            <tr>
                                <td class="od-table-label"><label for="od-off-quonumber">Angebotsnummer:</label></td>
                                <td><span id="od-off-quonumber"></span></td>
                            </tr>
                            <tr>
                                <td class="od-table-label"><label for="od-off-itime">erstellt am:</label></td>
                                <td><span id="od-off-itime"></span></td>
                            </tr>
                            <tr>
                                <td class="od-table-label"><label for="od-off-mtime">bearbeitet am:</label></td>
                                <td><span id="od-off-mtime"></span></td>
                             </tr>
                            <tr>
                                <td class="od-table-label"><label for="od-off-employee_name">bearbeitet von:</label></td>
                                <td><input type="hidden" id="od-off-employee_id" ></input><span id="od-off-employee_name"></span></td>
                            </tr>
                        </table>
                    </td>
                    <td width="33%">
                        <table>
                            <tr>
                                <td class="od-table-label"><label for="od-off-closed">Geschlossen:</label></td>
                                <td><input id="od-off-closed" type="checkbox"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>

<!--
    <div class="input-panel control-panel" style="width: 100%">
-->
    <div class="wrapper panel-wrapper">
        <div class="input-panel control-panel" style="min-width: 50%; width: auto;">
            <table class="ui-sortable tbl-list" id="edit-order-table" width="100%">
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
                        <th id="od-listheading-workers" class="listheading" nowrap width="2" >Mechaniker <div><select id="od-ui-items-workers"></select></div></th>
                        <th id="od-listheading-status" class="listheading" nowrap width="2" >
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
            <div style="float: right; padding-top: 0.5em; padding-bottom: 0.5em;">
                <table>
                    <tr>
                        <td class="od-perform">
                            <label for="od-performance" class="od-table-label">Performance:</label>
                        </td>
                        <td class="od-perform">
                            <input id="od-performance" type="text" readonly="readonly" size="10"></input>
                        </td>
                        <td>
                            <label for="od-netamount" class="od-table-label" style="margin-left: 2em">Netto:</label>
                        </td>
                        <td>
                            <input id="od-netamount" type="text" readonly="readonly" size="10"></input>
                        </td>
                        <td>
                            <label for="od-amount" class="od-table-label" style="margin-left: 2em">Brutto:</label>
                        </td>
                        <td>
                            <input id="od-amount" type="text" readonly="readonly" size="10"></input>
                            <input id="od-hidden-amount" type="hidden" readonly="readonly" size="10"></input>
                        </td>
                     </tr>
                </table>
            </div>
        </div>
    </div>
    <div id="od-inv-payment" style="display:none;">
        <div class="wrapper panel-wrapper">
            <div class="input-panel control-panel">
                <table id="od-inv-payment-list" class="tbl-list" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Datum</th>
                            <th>Beleg</th>
                            <th>Memo</th>
                            <th>Betrag</th>
                            <th>Konto</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div style="float: right; padding-top: 0.5em; padding-bottom: 0.5em;">
                    <button id="od_inv_book_deficit">Zahlungseingang buchen</button>
                </div>
            </div>
        </div>
    </div>
    <div class="wrapper panel-wrapper">
        <div class="od-comments-div input-panel control-panel">
            <table class="od-comments">
                <tr>
                     <td class="od-table-label">
                        Interne Bemerkungen zum Kunden
                    </td>
                    <td class="od-table-label">
                        <span id="od-lxcars-c_text-label">Interne Bemerkungen zum Auto</span>
                    </td>
                    <td class="od-table-label">
                        Interne Bemerkungen zur Rechnung
                    </td>
                    <td class="od-table-label">
                        Bemerkungen zur Rechnung
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
                    <td>
                        <textarea id="od-oe-notes" cols="50" rows="8"></textarea>
                    </td>
                 </tr>
            </table>
        </div>
    </div>
</div>

<!-- Anrufliste -->
<div id="crm-phonecall-list-view" class="crm-p2" style="display:none">
    <div>
        <h1>Telefonate</h1>
        <button id="crm-phonecall-list-view-close-btn">Schließen</button>
    </div>
    <div class="input-panel control-panel">
            <table class="tbl-list">
                <thead>
                    <tr>
                        <th class="date">Datum</th>
                        <th class="source">Quelle</th>
                        <th class="dest">Ziel</th>
                        <th class="number">Nummer</th>
                        <th class="typ">Typ</th>
                        <th class="dir">Richtung</th>
                        <th></th>
                        <th class="id" style="display:none">ID</th>
                    </tr>
                </thead>
                <tbody id="phonecall-list-table"></tbody>
            </table>
    </div>
</div>

<div id="crm-contact-assign-phone-dialog" style="display:none">
    <div style="display:none">
        <input type="hidden" id="crm-contact-assign-phone-number"></input>
        <input type="hidden" id="crm-contact-assign-phone-id"></input>
        <input type="hidden" id="crm-contact-assign-phone-src"></input>
    </div>
    <table>
        <tr>
            <td><label for="crm-assign-phone-contact">Kunde oder Lieferant</label></td><td><input type="text" id="crm-assign-phone-contact" size="23"></input></td>
        </tr>
    </table>
    <div style="padding-top: 10px">
        <button id="crm-contact-assign-phone-btn">Übernehmen</button>
        <button id="crm-contact-assign-phone-cancel-btn">Abbrechen</button>
    </div>
</div>

<div id="crm-contact-person-view" class="crm-p2" style="display:none">
    <div>
        <button id="crm-edit-contact-person-save-btn">Speichern</button>
        <button id="crm-edit-contact-person-cancel-btn">Abbrechen</button>
    </div>
    <div class="input-panel control-panel">
        <form>
            <div id="crm-contact-person-hidden"></div>
            <table id="contact-person-form">
                <thead></thead>
                <tbody></tbody>
            </table>
        </form>
    </div>
</div>

<div id="crm-show-car-data-dialog" style="display:none">
    <div id="show-car-data-hidden"></div>
    <table id="show-car-data-form">
        <thead></thead>
        <tbody></tbody>
    </table>
    <div class="crm-pt1">
        <button class="crm-ui-button" onclick="$( '#crm-show-car-data-dialog' ).dialog( 'close' );">Schließen</button>
    </div>
</div>

<!-- FS-Scan: Ändern des Autoname/Autotyps (lxckba.name in der DB) -->
<div id="crm-change-car-name-dialog" style="display:none">
    <div style="display:none">
        <input type="hidden" id="crm-change-car-name-id"></input>
    </div>
    <table>
        <tr>
            <td><label for="crm-change-car-name">Typenname: </label></td><td><input type="text" id="crm-change-car-name" size="23"></input></td>
        </tr>
    </table>
    <div style="padding-top: 10px">
        <button id="crm-change-car-name-btn">Übernehmen</button>
        <button id="crm-change-car-name-cancel-btn">Abbrechen</button>
    </div>
</div>

<div id="crm-confirm-order-to-invoice-dialog" style="display:none">
    <p>Aus dem Auftrag eine Rechnung erzeugen?</p>
    <button class="crm-ui-button" onclick="crmInsertInvoiceFromOrder();">Weiter</button>
    <button class="crm-ui-button" onclick="$( '#crm-confirm-order-to-invoice-dialog' ).dialog( 'close' );">Abbrechen</button>
</div>

<!-- generischer Dialog, kann für alles verwendet werden...  -->
<div id="generic-dialog"> </div>

<div id="crm-confirm-order-to-offer-dialog" style="display:none">
    <p style="font-weight: bold">Aus dem Auftrag ein Angebot erzeugen?</p>
    <button class="crm-ui-button" onclick="crmInsertOfferFromOrder();">Weiter</button>
    <button class="crm-ui-button" onclick="$( '#crm-confirm-order-to-offer-dialog' ).dialog( 'close' );">Abbrechen</button>
</div>

<div id="crm-order-email-dialog" style="display:none">
    <div id="order-email-hidden"></div>
    <table id="order-email-form">
        <thead></thead>
        <tbody></tbody>
    </table>
    <div class="crm-pt1">
        <button class="crm-ui-button" onclick="crmPrintOrder( crmOrderPrintTargetEnum.Email ); $( '#crm-order-email-dialog' ).dialog( 'close' );">E-Mail verschicken</button>
        <button class="crm-ui-button" onclick="$( '#crm-order-email-dialog' ).dialog( 'close' );">Abbrechen</button>
    </div>
</div>

<div id="crm-search-order-view" class="crm-p2" style="display:none">
    <div style="margin-bottom: 20px;">
        <button onclick="crmSearchOrderCloseView();">Schließen</button>
        <button id="show_hide_orderlist_filter_button"></button>
        <button id="delete_filter_values_button" onclick="crmSearchOrderClearView();">Löschen</button>
    </div>
    <div id="search-order-hidden"></div>
    <div id="filter_order_list" class="input-panel control-panel">
        <table id="search-order-form" style="padding-bottom: 1em">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>
    <table class="tbl-list table table-striped" style="min-width: 90%; width: auto;">
        <thead>
        <tr>
            <th>Name</th>
            <th>Kennzeichen</th>
            <th>Erste Position</th>
            <th>Hersteller</th>
            <th>Fahrzeugtyp</th>
            <th>Bringe-Datum</th>
            <th>Auftragsnummer</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody id="crm-search-order-table">
        </tbody>
    </table>
</div>

<div id="crm-plugin-elfinder" class="crm-p2" style="display:none">
    <div>
        <button onclick="crmCVDocumentsCloseView();">Schließen</button>
    </div>
    <div id="elfinder"></div>
</div>

<div id="crm-plugin-calendar" class="crm-p2" style="display:none">
    <div style="margin-bottom: 2em;">
        <button onclick="crmCalendarCloseView();">Schließen</button>
    </div>
    <iframe id="calendar-frame" name="calendar-frame" src="about:blank" title="Kalendar" width="100%" height="100%">
        <div id="calendar"></div>
    </iframe>
</div>

<div id="crm-edit-article-dialog" class="input-panel control-panel" style="display:none">
    <div id="edit-article-hidden"></div>
    <table id="edit-article-form">
        <thead></thead>
        <tbody></tbody>
    </table>
    <div class="crm-pt1">
        <button class="crm-ui-button" onclick="crmEditArticleSave();">Speichern</button>
        <button class="crm-ui-button" onclick="$( '#crm-edit-article-dialog' ).dialog( 'close' );">Abbrechen</button>
    </div>
</div>

<div id="crm-edit-car-dialog" class="crm-p2" style="display:none">
    <div>
        <button onclick="crmEditCarSaveView();">Speichern</button>
        <button onclick="crmEditCarCloseView();">Abbrechen</button>
        <button id="edit_car_new_order_btn" onclick="crmEditCarNewOrder();" style="margin-left: 2em">Neuer Auftrage</button>
        <button id="edit_car_register_btn">Zulassen/Umschreiben...</button>
        <button id="edit_car_special_btn">Special</button>
    </div>
    <div id="edit-car-hidden"></div>
    <div class="input-panel control-panel">
        <div style="padding: 0.25em">
            <div class="crm-dialog-error-view" style="color: red"></div>
        </div>
        <table>
            <tr>
                <td style="vertical-align: top;">
                    <table id="edit-car-form">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table id="edit-car-kba-form">
                        <thead></thead>
                        <tbody></tbody>
                    </table>
                </td>
                <td>
                    <div style="padding-left: 2em" id="crm-edit-car-orders-table-div">
                        <div style="padding-bottom: 1em; font-weight: bold">Neuste Aufträge</div>
                            <table class="tbl-list table table-striped">
                                <thead>
                                <tr>
                                    <th>Datum</th>
                                    <th>Erste Position</th>
                                    <th>Betrag</th>
                                    <th>Nummer</th>
                                </tr>
                                </thead>
                                <tbody id="crm-edit-car-orders-table">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </td>
             </tr>
        </table>
    </div>
</div>

<div id="crm-edit-kba-dialog" style="display:none">
    <div id="edit-kba-hidden"></div>
    <table id="edit-kba-form">
        <thead></thead>
        <tbody></tbody>
    </table>
</div>

<div id="crm-wx-customer-view" class="crm-p2" style="display:none">
    <div>
        <button onclick="crmEditCuVeViewSave()">Speichern</button>
        <button onclick="crmEditCuVeViewCancel();">Abbrechen</button>
    </div>
    <div class="input-panel control-panel">
        <div style="padding: 0.25em">
            <div class="crm-dialog-error-view" style="color: red"></div>
        </div>
        <div id="crm-tabs-main" class="tabwidget">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a id="crm-nav-billaddr" class="nav-link active" aria-current="page" href="#crm-tab-billaddr">Rechnungsaddresse</a>
                </li>
                <li class="nav-item">
                    <a id="crm-nav-deladdr" class="nav-link" aria-current="page" href="#crm-tab-deladdr">Lieferanschrift</a>
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
                <table style="padding-top: 1em;">
                    <tr>
                        <td style="vertical-align: top;">
                            <div id="car-form-hidden"></div>
                            <table id="car-form" style="display:none">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </td>
                        <td style="vertical-align: top; padding-left: 1em;">
                            <table id="car-kba-form" style="display:none">
                                <thead></thead>
                                <tbody></tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="crm-deladdr-hidden"></div>
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
</div>

<div id="crm-fsscan-dlg" class="crm-p2" style="display:none;">
    <button onclick="crmNewCarFromScanCancelView1();">Abbrechen</button>
    <div class="crm-pt1">
        <table id="crm-fsscan-list0" class="tbl-list">
            <thead>
              <tr class="listheading">
                <th class="xlistheading" id="date">Datum</th>
                <th class="xlistheading" id="firstname">Vorname</th>
                <th class="xlistheading" id="name1">Name</th>
                <th class="xlistheading" id="licenseplate">Kennzeichen</th>
                <th class="xlistheading" id="licenseplate">Hersteller</th>
                <th class="xlistheading" id="licenseplate">Typ</th>
                <th class="xlistheading" id="licenseplate">Hubraum</th>
              </tr>
            </thead>
            <tbody id="crm-fsscan-list"></tbody>
        </table>
    </div>
</div>

<div id="crm-fsscan-customer-dlg" class="crm-p2" style="display:none">
    <button onclick="crmNewCarFromScanNewCuView();">Neuer Kunde</button>
    <button onclick="crmNewCarFromScanCancelView2();">Abbrechen</button>
    <div class="crm-pt1">
        <div><input id="crm-fsscan-edit-customer" type="text" size="42"></input></div>
        <div style="padding-top: 1em">
            <table id="crm-fsscan-customer-list0" class="tbl-list">
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
</div>

<div id="crm-wx-base-data" class="row crm-p2">
    <div class="col-lg-3">
        <div id="crm-wx-contact">
            <input type="hidden" id="crm-cvpa-id"></input>
            <input type="hidden" id="crm-cvpa-src"></input>
            <input type="hidden" id="crm-cvpa-name"></input>
            <input type="hidden" id="crm-userconf-defprn" value="<?php echo $_SESSION['userConfig']['default_printer_id']; ?>"></input>
            <div><strong><span id="crm-contact-name"></span></strong> (<span id="crm-contact-cvnumber"></span>)</div>
            <div class="crm-pt05"><span id="crm-contact-street"></span></div>
            <div class="crm-pt025"><span id="crm-contact-country"></span>-<span id="crm-contact-zipcode"></span> <span id="crm-contact-city"></span><button id="crm-route" tooltip="Route anzeigen"><img src="crm/image/karte.png" alt="Karte"  ></button><button id="crm-route-qrcode" tooltip="QR-Code für Route anzeigen" ><img src="crm/image/qrn.png" alt="QR-Code Karte" ></button></div>
            <div class="crm-pt2"><strong>Hauptkontakt</strong></div>
            <div class="crm-pt05"><span id="crm-contact-contact"></span></div>
                <table>
                    <tr>
                        <td>Telefon1:</td><td><input type="text" id="crm-contact-note_phone1" size="10" disabled></input></td><td><button id="crm-contact-phone1"></button></td><td><button id="crm-contact-phone1_dialog_button" class='clickToCall1'>T</button><div id="crm-contact-phone1_dialog"></div></td><td><button id="crm-copy-contact-phone1" class="copy clickToCall1" title="Copy">C</button></td><td ><button id="crm-whatsapp1" class="whatsapp clickToCall1" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td>
                    </tr>
                    <tr>
                        <td>Telefon2:</td><td><input type="text" id="crm-contact-note_phone2" size="10" disabled></input></td><td><button id="crm-contact-phone2" class='clickToCall2'></button></td><td><button id="crm-contact-phone2_dialog_button" class='clickToCall2'>T</button><div id="crm-contact-phone2_dialog"></div></td><td><button id="crm-copy-contact-phone2" class="copy clickToCall2"  title="Copy">C</button></td><td ><button id="crm-whatsapp2" class="whatsapp clickToCall2" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td>
                    </tr>
                    <tr>
                        <td>Telefon3:</td><td><input type="text" id="crm-contact-note_phone3" size="10" disabled></input></td><td><button id="crm-contact-phone3" class='clickToCall3'></button></td><td><button id="crm-contact-phone3_dialog_button" class='clickToCall3'>T</button><div id="crm-contact-phone3_dialog"></div></td><td><button id="crm-copy-contact-phone3" class="copy clickToCall3"  title="Copy">C</button></td><td ><button id="crm-whatsapp3" class="whatsapp clickToCall3" title="Whatsapp" ><img src="crm/image/whatsapp.png" alt="Whatsapp" ></button></td>
                    </tr>
                </table>
                <div id="crm-wx-contact-email" class="row crm-pt025">
                <div class="col-md-2">E-Mail:</div>
                <div class="col-md-10"><button id="crm-contact-email">Kein Eintrag</button></div>
            </div>
        </div>
        <div id="crm-wx-cars" class="crm-pt2" tyle="display:none;">
            <table width="100%" class="tbl-list">
                <thead>
                <tr>
                    <th>Kenzeichen</th>
                    <th>Hersteller</th>
                    <th>Fahrzeugtyp</th>
                    <th>Fahrzeugart</th>
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
                <table class="tbl-list table table-striped">
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Erste Position</th>
                        <th>Betrag</th>
                        <th>Nummer</th>
                    </tr>
                    </thead>
                    <tbody id="crm-offers-table">
                    </tbody>
                </table>
            </div>
            <div id="crm-tab-orders" class="crm-tab">
                <table class="tbl-list table table-striped">
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Erste Position</th>
                        <th>Betrag</th>
                        <th>Nummer</th>
                    </tr>
                    </thead>
                    <tbody id="crm-orders-table">
                    </tbody>
                </table>
            </div>
            <div id="crm-tab-deliveries" class="crm-tab">
                <table class="tbl-list table table-striped">
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Erste Position</th>
                        <th>Lieferdatum</th>
                        <th>Nummer</th>
                    </tr>
                    </thead>
                    <tbody id="crm-deliveries-table">
                    </tbody>
                </table>
            </div>
            <div id="crm-tab-invoices" class="crm-tab">
                <table class="tbl-list table table-striped">
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Erste Position</th>
                        <th>Betrag</th>
                        <th>Nummer</th>
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
                    <a class="nav-link" aria-current="page" href="#crm-tab-contact-hist">Ansprechpersonen</a>
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
                <div id="crm-contacts-table"></div>
            </div>
            <div id="crm-tab-delivery-addr" class="crm-tab">
            </div>
            <div id="crm-tab-notes" class="crm-tab">
                <p>Hier stehen dann die wichtigen Notizen.</p>
            </div>
            <div id="crm-tab-vars" class="crm-tab">
                <table width="100%" class="tbl-list">
                    <thead>
                        <tr>
                            <th>Beschreibung</th>
                            <th>Eintrag</th>
                        </tr>
                    </thead>
                    <tbody id="crm-vars-table"></tbody>
                </table>
            </div>
            <div id="crm-tab-finance-infos" class="crm-tab">
            </div>
            <div id="crm-tab-extra-infos" class="crm-tab">
            </div>
            <div id="crm-new_customer-dialog" style="display:none">
                <div id="crm-new_customer-main" class="tabwidget">
                    <ul class="new_customer nav-tabs">
                        <li class="nav-item">
                            <a id="crm-nav-billaddr_customer" class="nav-link active" aria-current="page" href="#crm-tab-billaddr_customer">Rechnungsaddresse</a>
                        </li>
                        <li class="nav-item">
                            <a id="crm-nav-deladdr_customer" class="nav-link" aria-current="page" href="#crm-tab-deladdr_customer">Lieferanschrift</a>
                        </li>
                        <li class="nav-item">
                            <a id="crm-nav-banktax_customer" class="nav-link" aria-current="page" href="#crm-tab-banktax_customer" >Bank/Steuer</a>
                        </li>
                        <li class="nav-item">
                            <a id="crm-nav-extras_customer" class="nav-link" aria-current="page" href="#crm-tab-extras_customer" >Sonstiges</a>
                        </li>
                        <li class="nav-item">
                            <a id="crm-nav-vars_customer" class="nav-link" aria-current="page" href="#crm-tab-vars_customer">Variablen</a>
                        </li>
                    <div id="edit-new_Constumer-hidden"> </div>
                <table id="crm-tab-billaddr_customer">
                    <thead></thead>
                    <tbody></tbody>
                </table>
                <table id="crm-tab-extras_customer">
                    <thead></thead>
                    <tbody></tbody>
                </table>
                <table id="crm-tab-deladdr_customer">
                    <thead></thead>
                    <tbody></tbody>
                </table>
                <table id="crm-tab-banktax_customer">
                    <thead></thead>
                    <tbody></tbody>
                </table>
                <table id="crm-tab-vars_customer">
                    <thead></thead>
                    <tbody></tbody>
                </table>
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
<script src="crm/js/crm.app/searchorder.js"></script>
<script src="crm/js/crm.app/person.js"></script>
<script src="crm/js/crm.app/newcustomer.js"></script>
<script src="crm/js/crm.app/phonecall_list.js"></script>
<script src="crm/js/crm.app/phonecall.js"></script>
</body>
</html>
