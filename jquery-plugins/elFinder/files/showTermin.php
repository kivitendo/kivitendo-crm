<?php
    require_once("inc/stdLib.php");
    include("inc/crmLib.php");
    if ($_GET["del"]) {
        deleteTermin($_GET["del"]);
        echo "<script language='JavaScript'>self.close();</script>";
    }
    $data=getTerminData($_GET["termid"]);
    $usr=getTerminUser($_GET["termid"]);
    $privat = ($data["privat"]=='t' && $data["uid"]!=$_SESSION["loginCRM"]);
    $edit = false;
    if ($data["uid"]==$_SESSION["loginCRM"]) $edit = true;
    $links="";
    if ($usr) foreach ($usr as $row) {
        if (substr($row["uid"],0,1)<>"f" and $row["uid"]<>"E".$_SESSION["loginCRM"]) {
            $user[]=$row["uid"];
        } else if ($row["uid"]=="E".$_SESSION["loginCRM"]) {
             $edit = true;
        }
    }
    if ($user) {
        $selusr=getUsrNamen($user);
        foreach($selusr as $row) {
            if (substr($row["id"],0,1)=="C") { $tmp="firma1.php?Q=C&id=".substr($row["id"],1); }
            else if (substr($row["id"],0,1)=="V") { $tmp="firma1.php?Q=V&id=".substr($row["id"],1); }
            else if (substr($row["id"],0,1)=="P") { $tmp="kontakt.php?id=".substr($row["id"],1); }
            if (substr($row["id"],0,1)=="G") {  
                $links.="(".$row["name"].")&nbsp;";
            } else {
                $links.="[<a href='#' onClick='openstamm(\"$tmp\")'>".(($row["name"])?$row["name"]:$row["login"])."</a>] &nbsp; \n";
            }
        }
    }
    list($tt,$mm,$yy)=explode("\.",$data["starttag"]);
    $ft=feiertage($yy);
    $x=mktime(0,0,0,$mm,$tt,$yy);
    $wdhlg=array("0"=>"einmalig","1"=>"t&auml;glich","2"=>"2-t&auml;gig","7"=>"w&ouml;chentlich",
        "14"=>"2-w&ouml;chenltich","30"=>"monatlich","365"=>"j&auml;hlich");
?>
<html>
<head><title>Lx-Termin</title>
    <link type="text/css" REL="stylesheet" HREF="../css/<?php echo $_SESSION["stylesheet"]; ?>"></link>
    <link type="text/css" REL="stylesheet" HREF="css/<?php echo $_SESSION["stylesheet"]; ?>"></link>

<script language="JavaScript">
    function editterm() {
        opener.parent.location.href='termin.php?holen=<?php echo  $_GET["termid"] ?>';
        self.close();
    }
    function delterm() {
        Check = confirm("Wirklich löschen?");
        if(Check == false) return false;
        document.location.href='showTermin.php?del=<?php echo  $_GET["termid"] ?>';
    }
    function openstamm(link) {
        opener.top.main_window.location.href=link;
        self.close();
    }
</script>
</head>
<body>
<?php
    echo "Termin: <b>".(($privat)?"Privattermin":$data["cause"])."</b><br>";
    if ($data["location"]) echo "Ort: ".$data["location"]."<br />";
    if ($data["catname"]) echo $data["catname"].'<br />';
    echo db2date($data["starttag"])." ".$data["startzeit"]." - ";
    echo ($data["stoptag"]<>$data["starttag"])?db2date($data["stoptag"])." ".$data["stopzeit"]:$data["stopzeit"];
    echo "<br>";
    echo "Wiederholung: ".$wdhlg[$data["repeat"]].", ";
    echo ($data["ft"]==1)?"nur Arbeitstage":"auch Feiertage";
    echo "<br>";
    if ($ft[$x]) echo $ft[$x];
    echo "<hr><br>";
    if ($privat) {
        echo "Privattermin<br />";
    } else {
        echo $data["c_cause"]."<br />";
    }
?>
<hr>
<?php echo  $links ?>
<br>
<br>
<input type="button" onClick="self.close()" value="schlie&szlig;en"> &nbsp; &nbsp;
<?php if (!$privat && $edit) { ?>
<input type="button" onClick="delterm()" value="l&ouml;schen"> &nbsp; &nbsp;
<input type="button" onClick="editterm()" value="&auml;ndern">
<?php } ?>
<script language='JavaScript'>self.focus();</script>
</body>
</html>
