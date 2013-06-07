<?php
    include("../inc/stdLib.php");
    include("../inc/crmLib.php");

    function mkdate($dat) {
        $t=explode(" ",substr($dat,0,-3));
        $n=explode("-",$t[0]);
        $d=date("d.M Y",mktime(0, 0, 0, $n[1], $n[2], $n[0]));
        return $d." ".$t[1];
    }

    function getWvListe() {
        $ret = array();
        $mailcnt = 0;
        //Mails holen
        $mail = holeMailHeader($_SESSION["loginCRM"],$_SESSION['MailFlag']);
        if ($mail) foreach($mail as $col){
            $ret[] = array(
                Type     => "M",
                Status   => $col["Gelesen"],
                cause    => $col["Betreff"],
                Initdate => $col["Datum"],
                ID       => $col["Nr"],
                IniUser  => htmlspecialchars($col["Abs"]),
                Art      => "M",
                End      => 0
            );
            $mailcnt++;
        }
        //Termine holen
        $termine = getTermin(date("d"),date("m"),date("Y"),"T",$_SESSION["loginCRM"]);
        //Wiedervorlagen holen
        $wvl = getWvl($_SESSION["loginCRM"]);
        if ( $termine && $wvl ) { $wvl = array_merge($termine,$wvl); }
        else if ( $termine ) { $wvl = $termine; }
        $nunD = date("Y-m-d 00:00:00");
        $nunT = date("Y-m-d H:i");
        if ($wvl) foreach($wvl as $col){
            if ($col["finishdate"] || $col["stoptag"]) {
                if ( ($col["finishdate"]<>"" && $col["finishdate"]<$nunD) || 
                     ($col["stoptag"]<>"" && $col["stoptag"]." ".$col["stopzeit"]<$nunT) ) {
                    $end=3;
                } else {
                    $end=2;
                }
                $datum=mkdate(($col["finishdate"])?$col["finishdate"]:$col["stoptag"]." ".$col["stopzeit"].":00");
            } else {
                if ($col["trans_module"]) {
                    $datum=mkdate($col["initdate"]." 00:00:00");
                } else {
                    $datum=mkdate(($col["initdate"])?$col["initdate"]:$col["starttag"]." ".$col["startzeit"].":00");
                }
                $end=1;
            }
            if ($col["status"]=="F") { $Art="F"; }
            else if ($col["starttag"]) { $Art="T"; }
            else { $Art="D"; };
            $ret[] = array(
                Type     => ($col["kontakt"])?$col["kontakt"]:"X",
                Status   => ($col["status"])?$col["status"]:"-",
                cause    => $col["cause"],
                Initdate => $datum,
                ID       => $col["id"],
                IniUser  => ($col["ename"])?$col["ename"]:$col["employee"],
                Art      => $Art,
                End      => $end
            );
        };
        echo json_encode( $ret );
    }

switch ($_GET['task']) {
    case 'wvl'    : getWvListe();
                    break;
    default       : echo $_GET['task'].' nicht erlaubt';          
};
?>
