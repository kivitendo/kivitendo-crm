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

    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/common.js"></script>
    <script type="text/javascript" src="../../js/namespace.js"></script>
    <script type="text/javascript" src="../../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../../js/kivi.js"></script>
    <script type="text/javascript" src="../../js/locale/de.js"></script>
    
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
        max-width: 1100px;
        margin: 40px auto;
        background-color: rgb(255, 255, 255);
      }
  
    </style>
  
  </head>
  <body>
    <div id='calendar'></div>
  </body>
</html>