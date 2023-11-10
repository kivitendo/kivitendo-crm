<!DOCTYPE html>
<html>
  <head>
  <?php
    require_once("../inc/stdLib.php");
  ?>

  <meta charset='utf-8' />
  <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
  <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
  <script src='fullcalendar/dist/index.global.min.js'></script>
  <script src='fullcalendar/packages/core/locales-all.global.js'></script>
  <script src='fullcalendar/packages/moment/moment.min.js'></script>

  <!--
  <link rel="stylesheet" href="../../css/design40/main.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="../../css/design40/dhtmlsuite/menu-item.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="../../css/design40/dhtmlsuite/menu-bar.css" type="text/css" title="Stylesheet">

  <link rel="stylesheet" href="../../css/lx-office-erp/list_accounts.css" type="text/css" title="Stylesheet">
  -->
  <link rel="stylesheet" href="../../css/jquery.autocomplete.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="../../css/jquery.multiselect2side.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="../../css/ui-lightness/jquery-ui.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="../../css/lx-office-erp/jquery-ui.custom.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="../../css/tooltipster.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" href="../../css/themes/tooltipster-light.css" type="text/css" title="Stylesheet">
  <link rel="stylesheet" type="text/css" href="../jquery-plugins/colorPicker/syronex-colorpicker.css">

  <link rel="stylesheet" href="../css/crm.app/bootstrap-grid.min.css" type="text/css" title="Stylesheet">

  <script type="text/javascript" src="../../js/jquery.js"></script>
  <script type="text/javascript" src="../../js/common.js"></script>
  <script type="text/javascript" src="../../js/namespace.js"></script>
  <script type="text/javascript" src="../../js/jquery-ui.js"></script>
  <script type="text/javascript" src="../../js/kivi.js"></script>
  <script type="text/javascript" src="../../js/locale/de.js"></script>
  <script type="text/javascript" src="../../js/kivi.QuickSearch.js"></script>
  <script type="text/javascript" src="../../js/dhtmlsuite/menu-for-applications.js"></script>
  <script type="text/javascript" src="../../js/kivi.ActionBar.js"></script>
  <script type="text/javascript" src="../jquery-plugins/colorPicker/syronex-colorpicker.js"></script>

  <script type="text/javascript" src="../../js/kivi.CustomerVendor.js"></script>
  <script type="text/javascript" src="../../js/kivi.File.js"></script>
  <script type="text/javascript" src="../../js/chart.js"></script>
  <script type="text/javascript" src="../../js/kivi.CustomerVendorTurnover.js"></script>
  <script type="text/javascript" src="../../js/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="../../js/ckeditor/adapters/jquery.js"></script>
  <script type="text/javascript" src="../../js/follow_up.js"></script>
  <script type="text/javascript" src="../../js/kivi.Validator.js"></script>
  <script type="text/javascript" src="../../js/jquery.cookie.js"></script>
  <script type="text/javascript" src="../../js/jquery.checkall.js"></script>
  <script type="text/javascript" src="../../js/jquery.download.js"></script>
  <script type="text/javascript" src="../../js/jquery/jquery.form.js"></script>
  <script type="text/javascript" src="../../js/jquery/fixes.js"></script>
  <script type="text/javascript" src="../../js/client_js.js"></script>
  <script type="text/javascript" src="../../js/jquery/jquery.tooltipster.min.js"></script>
  <script type="text/javascript" src="../../js/jquery/ui/i18n/jquery.ui.datepicker-de.js"></script>
  <script type="text/javascript" src="../../js/jquery/ui/i18n/jquery.ui.datepicker-de.js"></script>
  <script type="text/javascript" src="../../crm/jquery-add-ons/date-time-picker.js"></script>
  <script type="text/javascript" src="../../crm/jquery-add-ons/german-date-time-picker.js"></script>
  <script type="text/javascript" src="../../crm/nodejs/node_modules/tinymce/tinymce.min.js"></script>

  <script>
    //$_COOKIE["kivitendo_session_id"] => Verküpft mit kivitendo_auth (Handle: $GLOBALS['dbh_auth'])
    //kivitendo_auth: SELECT sess_value FROM auth.session_content WHERE session_id = '47d193eb3c904984d553c92bbc3560f8' AND sess_key = 'login'; => Bsp.: '--- dirk'
    //autoprofis: SELECT id FROM employee WHERE login = 'dirk'
    var crmEmployee  = <?php echo $_SESSION['loginCRM']; ?>;
    <?php
        $grps = getAllERPgroups(1);
        array_unshift( $grps, array( 'value' => '0', 'text' => 'Benutzer' ) );
        array_unshift( $grps, array( 'value' => '-1', 'text' => 'Alle' ) );
    ?>
    var crmEmployeeGroups = <?php echo json_encode( $grps ); ?>;
    //alert( JSON.stringify(crmEmployeeGroups) );
  </script>
  <script src="../js/crm.app/calendar.js"></script>

  <style>

    html, body {
      margin: 0;
      padding: 0;
      font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
      font-size: 14px;
      background-color: transparent;
    }

    #calendar {
      max-width: 1600px;
      margin: 40px auto;
      background-color: rgb(255, 255, 255);
    }

    .ui-autocomplete {
        position: absolute;
        cursor: default;
        z-index: 1032 !important;
    }

    .crm-ui-table-block label {
      display: inline-block;
      min-width: 6em;
    }

    .crm-ui-row-block label {
      display: inline-block;
      margin-right: 0.5em;
    }

    .crm-ui-table-block2 label {
      display: inline-block;
      min-width: 8em;
    }

    .crm-ui-table-item-right {
      margin-left: 3.5em;
    }

  #crm-edit-event-dialog button {
    min-width: 137px;
    min-height: 25px;
    display: inline-block;
    cursor: pointer;
    width: auto;
    padding: 0.2em 0.6em;
    font-weight: normal;
    font-style: normal;
    text-align: center;
    border-style: solid;
    border-width: 1px;
  }

</style>

</head>
<body>
  <div id="crm-edit-event-dialog" title="Ereignis">
    <input type="hidden" id="crm-edit-event-id" value=""></input>
    <input type="hidden" id="crm-edit-event-cvp-id" value=""></input>
    <input type="hidden" id="crm-edit-event-cvp-type" value=""></input>
    <input type="hidden" id="crm-edit-event-car-id" value=""></input>
    <input type="hidden" id="crm-edit-event-order-id" value=""></input>
    <table class="crm-ui-table-block">
      <tr>
        <td><label for="crm-edit-event-title">Titel:</label></td>
        <td><input type="text" id="crm-edit-event-title" name="crm-edit-event-title" value=""></input></td>
        <td><button id="crm-edit-event-to-order" name="crm-edit-event-to-order" class="crm-edit-event-to-order crm-ui-table-item-right">Auftrag öffnen</button></td>
      </tr>
    </table>
    <table class="crm-ui-table-block2" style="margin-top: 2em">
      <tr>
        <td><label for="crm-edit-event-start">Start:</label></td>
        <td><input type="text" id="crm-edit-event-start" name="crm-edit-event-start" value="" size="10"><input type="text" id="crm-edit-event-start-time" name="crm-edit-event-start-time" value="" size="4" style="margin-left:0.25em"></td>
      </tr>
      <tr>
        <td><label for="crm-edit-event-end">Ende:</label></td>
        <td><input type="text" id="crm-edit-event-end" name="crm-edit-event-end" value="" size="10"><input type="text" id="crm-edit-event-end-time" name="crm-edit-event-end-time" value="" size="4" style="margin-left:0.25em"></td>
        <td><input type="checkbox" id="crm-edit-event-full-time" name="crm-edit-event-full-time" class="crm-ui-table-item-right"><label for="crm-edit-event-full-time" style="margin-left: 0.25em">ganztags</label></td>
      </tr>
    </table>
    <table class="crm-ui-row-block">
      <tr>
        <td><label for="crm-edit-event-repaet-factor">Wiederholungen:</label></td>
        <td><input type="number" id="crm-edit-event-repeat-factor" name="crm-edit-event-repeat-factor" value="" style="width: 4em"></input></td>
        <td><select id="crm-edit-event-repeat" name="crm-edit-event-repeat"><option value="day">tägig</option><option value="week">wöchig</option><option value="month">monatig</option><option value="year">jährig</option></td>
        <td><input type="number" id="crm-edit-event-repeat-quantity" name="crm-edit-event-repeat-quantity" value="" style="margin-left: 0.5em; margin-right: 0.5em; width: 4em"></input></td>
        <td><label for="crm-edit-event-repeat-end">mal bis:</label></td>
        <td><input type="text" id="crm-edit-event-repeat-end" name="crm-edit-event-repeat-end" value="" size="10"></input></td>
      </tr>
    </table>
    <table class="crm-ui-table-block" style="margin-top: 2em">
      <tr>
        <td><label for="crm-edit-event-customer">Kunde:</label></td>
        <td><input type="text" id="crm-edit-event-customer" name="crm-edit-event-customer" value=""></input></td>
        <td><button id="crm-edit-event-to-cvp" name="crm-edit-event-to-cvp" class="crm-edit-event-to-cvp crm-ui-table-item-right">Kunde anzeigen</button></td>
      </tr>
    </table>
    <table class="crm-ui-table-block">
      <tr>
        <td><label for="crm-edit-event-car">Auto:</label></td>
        <td><select id="crm-edit-event-car" style="min-width: 15em"></select></td>
        <td><button id="crm-edit-event-to-car" name="crm-edit-event-to-car" class="crm-edit-event-to-car crm-ui-table-item-right">Auto anzeigen</button></td>
      </tr>
    </table>
    <table class="crm-ui-table-block">
      <tr>
        <td><label for="crm-edit-event-location">Location:</label></td>
        <td><input type="text" id="crm-edit-event-location" name="crm-edit-event-location" value=""></input></td>
      </tr>
    </table>
    <table class="crm-ui-table-block" style="margin-top: 2em">
      <tr>
        <td><label for="crm-edit-event-category">Kategorie:</label></td>
        <td><select id="crm-edit-event-category"></select></td>
        <td><label for="crm-edit-event-prio" class="crm-ui-table-item-right">Priorität:</label></td>
        <td><select id="crm-edit-event-prio" name="crm-edit-event-prio"><option value="0">Niedrig</option><option value="1">Normal</option><option value="2">Hoch</option></select></td>
      </tr>
      <tr>
        <td><label for="crm-edit-event-color">Farbe:</label></td>
        <td><input type="text" id="crm-edit-event-color" name="crm-edit-event-color" value=""></td>
        <td><label for="crm-edit-event-visibility" class="crm-ui-table-item-right">Sichtbarkeit:</label></td>
        <td><select id="crm-edit-event-visibility"></select></td>
      </tr>
      <tr>
        <td></td>
        <td></input><div id="crm-edit-event-colorpicker"></div></td>
      </tr>
    </table>
    <div style="margin-top: 2em"><label for="crm-edit-event-description">Beschreibung:</label></div>
    <textarea id="crm-edit-event-description" name="crm-edit-event-description" rows="5" cols="65" style="margin-top: 0.25em"></textarea>
  </div>
  <div id="calendar">
    <div id="crm-cal-tabs">
      <ul id="crm-cal-tab-list">
      </ul>
    </div>
  </div>

</body>
</html>
