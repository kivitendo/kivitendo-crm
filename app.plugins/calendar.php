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
    var crmEmployee  = <?php echo $_SESSION['loginCRM']; ?>;
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

  </style>

</head>
<body>

  <div id="crm-edit-event-dialog"></div>
  <div id="calendar">
    <div id="crm-cal-tabs">
      <ul id="crm-cal-tab-list">
      </ul>
    </div>
  </div>

</body>
</html>
