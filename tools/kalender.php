<? include("../inc/conf.php"); ?>
<html style="background-color: buttonface; color: buttontext;">
<head>
<meta http-equiv="content-type" content="text/xml; charset=utf-8" />
<title>Simple calendar setup [flat calendar]</title>
<!--link type="text/css" REL="stylesheet" HREF="../<?= $ERPNAME ?>/js/jscalendar/calendar-win2k-1.css"></link-->
<style type='text/css'>@import url(../../../lx-office-erp/js/jscalendar/calendar-win2k-1.css);</style>

<script type='text/javascript' src='../../../<?= $ERPNAME ?>/js/jscalendar/calendar.js'></script>
<script type='text/javascript' src='../../../<?= $ERPNAME ?>/js/jscalendar/lang/calendar-de.js'></script>
<script type='text/javascript' src='../../../<?= $ERPNAME ?>/js/jscalendar/calendar-setup.js'></script>
</head>

<body onLoad="window.resizeTo(240, 215);" style="padding:0px; margin:0px;">
<div style="padding: 0em; margin: 0em;" id="calendar-container"></div>

<script type="text/javascript">
  function dateChanged(calendar) {
    // Beware that this function is called even if the end-user only
    // changed the month/year.  In order to determine if a date was
    // clicked you can use the dateClicked property of the calendar:
    if (calendar.dateClicked) {
      // OK, a date was clicked, redirect to /yyyy/mm/dd/index.php
      var y = calendar.date.getFullYear();
      var m = calendar.date.getMonth();     // integer, 0..11
      var d = calendar.date.getDate();      // integer, 1..31
      // redirect...
      //window.location = "/" + y + "/" + m + "/" + d + "/index.php";
    }
  };

  Calendar.setup(
    {
      flat         : "calendar-container", // ID of the parent element
      flatCallback : dateChanged           // our callback function
    }
  );
</script>
</body>
</html>
