<?
    chdir("..");
    require_once("inc/stdLib.php");
    include("FirmenLib.php"); 
    $punkt = ".";
    require("crmajax/firmacommon".XajaxVer.".php");
    $tmp = split("crm/",$_SERVER['REQUEST_URI']); 
    $path=$tmp[0]; 
?>
<html style="background-color: buttonface; color: buttontext;">
<head>
<title>Simple calendar setup [flat calendar]</title>
<meta http-equiv="content-type" content="text/xml; charset=utf-8" />
<link type="text/css" REL="stylesheet" HREF="<?= $path ?>js/jscalendar/calendar-win2k-1.css"></link>
<?= $xajax->printJavascript("../".XajaxPath); ?>
<script language="JavaScript">
    <!--
    function getCustTermin(day) {
       xajax_getCustomTermin(<?=$_GET["id"]?>,'<?=$_GET["Q"]?>',day); 
    };
    function getCall(id) {
        F1=open("../getCall.php?Q=<?=$_GET["Q"]?>&fid=<?=$_GET["id"]?>&Bezug="+id,"Caller","width=680, height=680, left=100, top=50, scrollbars=yes");
    }
    //-->
</script>
<script type='text/javascript' src='<?= $path ?>js/jscalendar/calendar.js'></script>
<script type='text/javascript' src='<?= $path ?>js/jscalendar/lang/calendar-de.js'></script>
<script type='text/javascript' src='<?= $path ?>js/jscalendar/calendar-setup.js'></script>
</head>

<body onLoad="window.resizeTo(300, 320); getCustTermin(0);" style="padding:0px; margin:0px;">
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
      m++;
      if (m<10) m = "0"+m;
      var d = calendar.date.getDate();      // integer, 1..31
      day = d+"."+m+"."+y;
      getCustTermin(day);
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
<div style="padding: 0em; margin: 0em;" id="termin-container"></div>
</body>
</html>
