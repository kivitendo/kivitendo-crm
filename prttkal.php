<?php
    require_once("inc/stdLib.php");
    include("inc/crmLib.php");
    include_once("inc/UserLib.php");
    include('inc/phpOpenOffice.php');
    $usr=getUserStamm($_SESSION["loginCRM"]);
    $kalterm=array("zeit"=>"","txt"=>"");
    $year=$_GET["year"];
    $month=$_GET["month"];
    $day=$_GET["day"];
    $vars=array("JAHR"=>$year,"MONAT"=>$month,"TAG"=>$day,"NAME"=>$usr["name"]);
    $data=getTermin($day,$month,$year,"T");
    $ft=feiertage($year);
    $ftk=array_keys($ft);
    for($i=0; $i<24; $i++) {
        $x = sprintf("%02d",$i);
        $vars[$x."00"]="$x:00";
        $vars[$x."30"]="$x:30";
        $vars["TERMIN$x"."00"]="";
        $vars["TERMIN$x"."30"]="";
    }
    $termdata=array();
    $tlist=array();
    if ($data) foreach($data as $row) {
        if (!in_array($row["id"],$tlist)) {
            if ($row["stoptag"]>"$year-$month-$day" && $row["repeat"]=="0") $row["stopzeit"]="24:00";
            if ($row["starttag"]<"$year-$month-$day" && $row["repeat"]=="0") $row["startzeit"]="00:00";
            $tmp=explode(":",$row["startzeit"]);
            $v=mktime($tmp[0],$tmp[1],0,$month,$day,$year);
            $tmp=explode(":",$row["stopzeit"]);
            $b=mktime($tmp[0],$tmp[1],0,$month,$day,$year);
            $grund = utf8_decode($row["cause"]);
            $tid=$row["termid"];
            for($v; $v<=$b; $v+=1800) {
                if (date("G",$v)>=$_SESSION["termbegin"] && date("G",$v)<=$_SESSION["termend"]) {
                    $vars["TERMIN".date("Hi",$v)].=$grund;
                    $grund="|| ";
                    $tid=0;
                } else {
                    $grund =  utf8_decode($row["cause"]);
                    $tid=$row["termid"];
                }
            }
            $tlist[]=$row["id"];
        }
    }
    $doc = new phpOpenOffice();
    if (file_exists("vorlage/kaltag_".$usr["login"].".sxw")) {
        $doc->loadDocument("vorlage/kaltag_".$usr["login"].".sxw");
    } else {
        $doc->loadDocument("vorlage/kaltag.sxw");
    }
    $doc->parse($vars);
    $doc->download("");
    $doc->clean();
?>
