<?
    chdir("..");
    require_once("inc/stdLib.php");
?>
<html>
<head>
<title>Kalender</title>
<meta http-equiv="content-type" content="text/xml; charset=utf-8" />
    <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION["basepath"] ?>crm/css/<?php echo $_SESSION["stylesheet"] ?>/main.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['basepath'] ?>crm/jquery-ui/themes/base/jquery-ui.css">
    <script type="text/javascript" src="<?php echo $_SESSION['basepath'] ?>crm/jquery-ui/jquery.js"></script>
    <script type="text/javascript" src="<?php echo $_SESSION['basepath'] ?>crm/jquery-ui/ui/jquery-ui.js"></script>
    <script type="text/javascript" src="<?php echo $_SESSION['basepath'] ?>crm/jquery-ui/ui/jquery.ui.datepicker-de.js"></script>

<script language="JavaScript">
    <!--
    function getCustDay(datum) {
           date = datum.split("/");
           getCustTermin(date[2],date[0],date[1]);
    }
    function getCustTermin(year,month,day) {
       $.get('../jqhelp/firmaserver.php?task=getCustomTermin&id=<?php echo $_GET["id"] ?>&tab=<?php echo $_GET["Q"] ?>&day='+day+'&month='+month+'&year='+year,
             function(data) {
                $('#termin-container').empty().append(data); 
             }
       ); 
    };
    function getCall(id) {
        F1=open("../getCall.php?Q=<?=$_GET["Q"]?>&fid=<?=$_GET["id"]?>&Bezug="+id,"Caller","width=680, height=680, left=100, top=50, scrollbars=yes");
    }
    //-->
</script>
</head>

<body onLoad="window.resizeTo(310, 380); getCustTermin(0);" style="padding:0px; margin:0px;">
<div style="valign:middle:" id="calendar-container"></div>
<div style="padding: 0em; margin: 0em;" id="termin-container"></div>
</body>
<script>
    $( "#calendar-container" ).datepicker(  $.datepicker.regional[ "de" ] );
    $( "#calendar-container" ).datepicker( { regional: "de",  
                                             inline:true, 
                                             selectWeek:true, 
                                             firstDay:0, 
                                             onChangeMonthYear:function(year,month) { getCustTermin(year,month) },
                                             onSelect:function(date) { getCustDay(date) }
                                          });
</script>
</html>
