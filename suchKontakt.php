<?php
    require_once("inc/stdLib.php");
    include("inc/crmLib.php");
    $sw=strtoupper($_GET["suchwort"]);
    $sw=strtr($sw,"*?","%_");
    if ($_GET["Q"]=="S") {
        $sql="select calldate,cause,t.id,caller_id,contact_reference,V.name as lname,C.name as kname,P.cp_name as pname ";
        $sql.="from contact_events t left join customer C on C.id=caller_id left join vendor V on V.id=caller_id ";
        $sql.="left join contacts P on caller_id=P.cp_id where UPPER(cause) like '%$sw%' or UPPER(cause_long) like '%$sw%' ";
    } else {
        $id=$_GET["id"];
        $sql="select calldate,cause,id,caller_id,contact_reference from contact_events where ( UPPER(cause) like '%$sw%' or UPPER(cause_long) like '%$sw%') ";
        $sql.="and (caller_id in (select cp_id from contacts where cp_cv_id=$id) or caller_id=$id)";
    }
    $rs=$GLOBALS['dbh']->getAll($sql." order by contact_reference,calldate desc");
    $used= Array();
?>
<html>
<head><title>Suche im Kontaktverlauf</title>
<link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION["baseurl"].'crm/css/'.$_SESSION["stylesheet"] ?>/main.css">
<script language="JavaScript">
    function showItem(id,Q,FID) {
        F1=open("<?php echo $_SESSION["baseurl"]; ?>crm/getCall.php?Q="+Q+"&fid="+FID+"&hole="+id,"Caller","width=610, height=600, left=100, top=50, scrollbars=yes");

    }
</script>
</head>
<body>
<center>Gesucht wurde nach: <?php echo  $_GET["suchwort"] ?></center><br>
<?php
    if($rs) {
        echo "<table width='95%'>\n";
        $i=0;
        foreach ($rs as $row) {
            if ($row["contact_reference"]>0 and in_array($row["contact_reference"],$used)) continue;
            if ($row["contact_reference"]==0) $used[]=$row["id"];
            if (strlen($row["cause"])>30) { $cause=substr($row["cause"],0,30).".."; }
            else { $cause=$row["cause"]; };
            if ($row["kname"]) { $name=$row["kname"]; $src="C"; }
            else if ($row["lname"]) { $name=$row["lname"]; $src="V";  }
            else if ($row["pname"]) { $name=$row["pname"]; $src="CC"; }
            else { $name=""; $src=$_GET["Q"]; }
            echo "<tr height='14px' class='bgcol".($i%2+1)."'  onClick='showItem(".$row["id"].",\"$src\",".$row["caller_id"].");'>";
            echo "<td>".db2date($row["calldate"])."&nbsp;</td><td> ".$cause."</td><td>";
            echo "$name</td></tr>\n";
            $i++;
            if ($i>=$_SESSION['listLimit']) {
                echo $_SESSION['listLimit']." von ".count($rs)." Treffern";
                break;
            }
        }
        echo "</table>\n";
    } else {
        echo "Keine Treffer!";
    }
?>
<!--<center><a href="javascript:self.close()">close</a></center>-->

</body>
</html>
