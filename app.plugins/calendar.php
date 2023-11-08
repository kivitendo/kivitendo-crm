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
        z-index:1032 !important;
    }

  </style>

</head>
<body>
  <div id="crm-edit-event-dialog" title="Ereignis">
    <input type="hidden" id="crm-edit-event-id" name="crm-edit-event-id" value=""></input>
    <table width="100%">
      <tr>
        <td><label for="crm-edit-event-title">Titel:</label></td>
        <td><input type="text" id="crm-edit-event-title" name="crm-edit-event-title" value=""></input></td>
        <td><input type="radio" id="crm-edit-event-termin" name="crm-edit-event-termin" value=""></input> <label for="crm-edit-event-termin">Termin</label></td>
      </tr>
      <tr>
        <td><label for="crm-edit-event-start">Start:</label></td>
        <td><input type="text" id="crm-edit-event-start" name="crm-edit-event-start" value="" size="10"><input type="text" id="crm-edit-event-start-time" name="crm-edit-event-start-time" value="" size="4" style="margin-left:0.25em"></td>
        <td><input type="radio" id="crm-edit-event-task" name="crm-edit-event-task" value=""></input> <label for="crm-edit-event-task">Aufgabe</label></td>
        <td><input type="checkbox" id="crm-edit-event-task-done" name="crm-edit-event-task-done" class="crm-edit-event-task-done" style="display:none"><label class="crm-edit-event-task-done" for="crm-edit-event-task-done" style="margin-left: 0.25em;display:none"">erledigt</label></td>
      </tr>
      <tr>
        <td><label for="crm-edit-event-end">Ende:</label></td>
        <td><input type="text" id="crm-edit-event-end" name="crm-edit-event-end" value="" size="10"><input type="text" id="crm-edit-event-end-time" name="crm-edit-event-end-time" value="" size="4" style="margin-left:0.25em"></td>
        <td><input type="checkbox" id="crm-edit-event-full-time" name="crm-edit-event-full-time" ><label for="crm-edit-event-full-time" style="margin-left: 0.25em">ganztags</label></td>
      </tr>
      <tr>
        <td><label for="crm-edit-event-category">Kategorie:</label></td>
        <td><select id="crm-edit-event-category"></select></td>
        <td><label for="crm-edit-event-prio">Priorität:</label></td>
        <td><select id="crm-edit-event-prio" name="crm-edit-event-prio"><option value="0">Niedrig</option><option value="1">Normal</option><option value="2">Hoch</option></select></td>
      </tr>
      <tr>
        <td><label for="crm-edit-event-customer">Kunde:</label></td>
        <td><input type="text" id="crm-edit-event-customer" name="crm-edit-event-customer" value=""></input></td>
        <td><label for="crm-edit-event-visibility">Sichtbarkeit:</label></td>
        <td><select id="crm-edit-event-visibility"></select></td>
      </tr>
      <tr>
        <td><label for="crm-edit-event-repaet-factor">Wiederholungen:</label></td>
        <td><input type="text" id="crm-edit-event-repeat-factor" name="crm-edit-event-repeat-factor" value=""></input></td>
        <td><select id="crm-edit-event-repeat" name="crm-edit-event-repeat"><option value="day">täglich</option><option value="week">wöchentlich</option><option value="month">monatlich</option><option value="year">jährlich</option></td>
      </tr>
      <tr>
        <td></td>
        <td><input type="text" id="crm-edit-event-repeat-quantity" name="crm-edit-event-repeat-quantity" value=""></input></td>
        <td><label for="crm-edit-event-repeat-end">mal bis:</label></td>
        <td><input type="text" id="crm-edit-event-repeat-end" name="crm-edit-event-repeat-end" value=""></input></td>
      </tr>
      <tr>
        <td><label for="crm-edit-event-color">Farbe:</label></td>
        <td><input type="text" id="crm-edit-event-color" name="crm-edit-event-color" value=""></td>
      </tr>
      <tr>
        <td></td>
        <td></input><div id="crm-edit-event-colorpicker"></div></td>
      </tr>
      <tr>
        <td>Beschreibung:</td>
      </tr>
    </table>
    <textarea id="crm-edit-event-description" name="crm-edit-event-description" rows="10" cols="74" style="margin-top: 0.25em"></textarea>
  </div>
  <div id="calendar">
    <div id="crm-cal-tabs">
      <ul id="crm-cal-tab-list">
      </ul>
    </div>
  </div>

</body>
</html>
