<?php
    require_once("../inc/stdLib.php");
    include("FirmenLib.php");
    include("crmLib.php");
    include("persLib.php");

    function getCustomTermin($id,$tab,$day) {
        $termine = getCustTermin($id,$tab,$day);
        if ($termine)  {
            foreach ($termine as $term) {
               $inhalt .= "<span onClick='getCall(".$term["cid"].")'>";
               $inhalt .= db2date(substr($term["start"],0,10))." ".$term["cause"].":".$term["cp_name"]."<br /></span>";
            }
        } else {
            $inhalt = "Keine Termine";
        };
        echo $inhalt;
    }

    function Buland($land) {
        $data=getBundesland(strtoupper($land));
        $rs = array(array('id'=>'','val'=>''));
        foreach ($data as $row) {
            array_push($rs,array('id'=>$row['id'],'val'=>$row['bundesland']));
        }
        echo json_encode($rs);
    }
    function getShipto($id,$tab='C') {
        if ($id) $data=getShipStamm($id,$tab);
        if ( !$data or !$id ) {
            $data = array('trans_id'=>'','shiptoname'=>'','shiptostreet'=>'',
                          'shiptocity'=>'','shiptocountry'=>'','shiptozipcode'=>'',
                          'shiptodepartment_1'=>'','shiptodepartment_2'=>'','shiptocontact'=>'',
                          'shiptobland'=>'');
        }
        echo json_encode($data);
    }
    function showCalls($id,$start,$fa=false) {
        $max=0;
        $nun=date("d.m.Y h:i");
        $item[]=array('id' => 0, 'new' => '', 'inout' => '', 'calldate' => $nun, 'caller_id' => '', 'cp_name' => $_SESSION['employee'], 'cause' => translate('.:newItem:.','firma') );
        $items=getAllTelCall($id,$fa,$start);
        if ($items) {
            foreach ($items as $row) {
                $row['calldate'] = db2date(substr($row["calldate"],0,10))." ".substr($row["calldate"],11,5);
                if ( !$row['cp_name'] ) $row['cp_name'] = '';
                $item[] = $row;
            }
        } 
        if ($start==0) $max=getAllTelCallMax($id,$firma);
        $data = array('items'=>$item,'max'=>$max);
        echo json_encode($data);
    }
    function showShipadress($id,$tab){
        $data=getShipStamm($id,$tab);
        $karte=str_replace(array("%TOSTREET%","%TOZIPCODE%","%TOCITY%"),
                           array(strtr($data["shiptostreet"]," ",$_SESSION['planspace']),$dataa["shiptozipcode"],$data["shiptocity"]),$_SESSION['streetview']);
        if (preg_match("/%FROM/",$karte)) {
            include "inc/UserLib.php";
            $user=getUserStamm($_SESSION["loginCRM"]);
            if ($user["addr1"]<>"" and $user["addr3"]<>"" and $user["addr2"]) {
                $karte=str_replace(array("%FROMSTREET%","%FROMZIPCODE%","%FROMCITY%"),
                                   array(strtr($user["addr1"]," ",$_SESSION['planspace']),$user["addr2"],$user["addr3"]),$karte);
            } else {
                $karte="";
            };
        }
        $maillink="<a href='mail.php?TO=".$data["shiptoemail"]."&KontaktTO=$tab".$data["trans_id"]."'>".$data["shiptoemail"]."</a>";
        echo json_encode(array('karte'=>$karte,'mail'=>$maillink,'www'=>$htmllink,'adr'=>$data));
    }
    function showContactadress($id){
        $data=getKontaktStamm($id,".");
        if ( !$data ) { 
            $data = array('cp_id'=>-1,'cp_name'=> translate('.:no contact:.','firma'));
        } else {
            $data["cp_email"]="<a href='mail.php?TO=".$data["cp_email"]."&KontaktTO=P".$data["cp_id"]."'>".$data["cp_email"]."</a>";
            if ($data["cp_privatemail"]) $data["cp_privatemail"]="Privat: <a href='mail.php?TO=".$data["cp_privatemail"]."&KontaktTO=P".$data["cp_id"]."'>".$data["cp_privatemail"]."</a>";;
            $data["cp_homepage"]="<a href='".$data["cp_homepage"]."' target='_blank'>".$data["cp_homepage"]."</a>";
            if (strpos($data["cp_birthday"],"-")) { $data["cp_birthday"]=db2date($data["cp_birthday"]); };
            if ($data["cp_gender"]=='m') { $data["cp_greeting"]=translate('.:greetmale:.','firma'); 
            } else { $data["cp_greeting"]=translate('.:greetfemale:.','firma'); };
            $root="dokumente/".$_SESSION["mansel"]."/".$data["tabelle"].$data["nummer"]."/".$data["cp_id"];
            if (!empty($data["cp_grafik"]) && $data["cp_grafik"]<>"     ") {
                $img="<img src='$root/kopf$id.".$data["cp_grafik"]."' ".$data["icon"]." border='0'>";
                $data["cp_grafik"]="<a href='$root/kopf$id.".$data["cp_grafik"]."' target='_blank'>$img</a>";
            };
            $tmp=glob("../$root/vcard".$data["cp_id"].".*");
            $data["cp_vcard"]="";
            if ($tmp)  foreach ($tmp as $vcard) {
                $ext=explode(".",$vcard);
                $ext=strtolower($ext[count($ext)-1]);
                if (in_array($ext,array("jpg","jpeg","gif","png","pdf","ps"))) {
                    $data["cp_vcard"]="<a href='$root/vcard$id.$ext' target='_blank'>Visitenkarte</a>";
                    break;
                }
            } 
            $data["extraF"] = '<a href="extrafelder.php?owner=P'.$id.'" target="_blank" title="'.translate('.:extra data:.','firma').'"><img src="image/extra.png" alt="Extras" border="0" /></a>';
        }
        echo json_encode($data);
    }

    function listTevents($id,$fid=0) {
        $events = getTTEvents($id,"a",false);
        $link = "<a href='../oe.pl?action=edit&type=sales_order&vc=customer&id=%d&callback=crm/timetrack.php%%3ffid=$fid'>";
        if (!$events) echo json_encode(array('ok'=>0));
        $tt = getOneTT($events[0]["ttid"]);
        $liste = "<form name='cleared' method='post' action='timetrack.php'>";
        $liste.= "<input type='hidden' name='tid' value='".$events[0]["ttid"]."'>";
        $liste.= "<table>";
        $i = 0;
        $now = time();
        $diff = 0;
        $lastcleared = 0;
        if ($events) foreach ($events as $row) {
            $a = explode(" ",$row["ttstart"]);
            $t1 = strtotime($row["ttstart"]);
            if ($row["ttstop"]) {
                $b = explode(" ",$row["ttstop"]);   
                $stop = db2date($b[0])." ".substr($b[1],0,5);
                $t2 = strtotime($row["ttstop"]);
            } else {
                if ( $t1 <= $now ) {
                    $t2 = $now;
                } else {
                    $t2 = $t1;
                }
                $stop = "<a href='timetrack.php?tid=".$row["ttid"]."&eventid=".$row["id"]."&stop=now'><b>".translate('.:stop now:.','work')."</b></a>";
            };
	        $min = $t2 - $t1;
            $diff += $min;
            $i++; 
            if ($row["cleared"] > 0) {
                $clear = "<td>-</td>";
                if ( $row['cleared'] > $lastcleared )  $lastcleared  = $row['cleared']; 
            } else {
                if ($row["uid"] == $_SESSION["loginCRM"]) {
                    $clear = "<td><input type='checkbox' name='clear[]' value='".$row["id"]."'></td>";
                } else {
                    $clear = "<td>+</td>";
                };
            }
            $liste .= "<tr class='calls".($i%2)."' onClick='editrow(".$row["id"].");'>$clear<td>".db2date($a[0])." ".substr($a[1],0,5)."</td>";
            $liste .= "<td>".$stop."</td><td align='right'>".floor($min/60)."</td><td>".$row["user"]."</td><td>";
            if (strlen($row["ttevent"])>40) {
                $liste .= substr($row["ttevent"],0,40)."...</td><td>";
            } else {
                $liste .= $row["ttevent"]."</td><td>";
            }
	        $liste .= sprintf($link,$row['cleared']).$row["ordnumber"]."</a>";
            if ( $row['closed'] == 't' ) $liste .= "*";
            $liste .= "</td></tr>";
        };
        $diff = floor($diff / 60);
        if ($tt["aim"]>0) {
            $rest = $tt["aim"] * 60 - $diff;
            $rstd = floor($rest/60);
            $rmin = ($rest%60);
            $rest = translate('.:remain:.','work')." $rstd:$rmin ".translate('.:hours:.','work');
        } else {
            $rest = "";
        }
        if ($diff>60) {
            $min = sprintf("%02d",($diff % 60));
            $std = floor($diff/60);
            $use = "$std:$min ".translate('.:hours:.','work');
        } else {    
            $use = $diff." ".translate('.:minutes:.','work');
        }
        $liste .= "</table><input type='checkbox' name='clrok' value='1'>".translate(".:all:.",'work')." ";
        if ( $lastcleared > 0 ) {
            $liste .= "<input type='radio' name='order' value='0' checked>".translate('.:new Order:.','work').' ';
            $liste .= "<input type='radio' name='order' value='$lastcleared'>".translate('.:add to last Order:.','work').' ';
        };
     	$liste .= "<input type='submit' name='clr' value='".translate(".:clearing:.","work")."'></form>";
        echo json_encode(array('liste'=>$liste,'used'=>$use,'rest'=>$rest,'ok'=>1));
    }
    function editTevent($id) {
        $data = getOneTevent($id);
        $a = explode(" ",$data['t']["ttstart"]);
        $data['startd'] = db2date($a[0]);
        $data['startt'] = substr($a[1],0,5);
        if ($data['t']["ttstop"]) {
            $b = explode(" ",$data['t']["ttstop"]);
            $data['stopd'] = db2date($b[0]);
            $data['stopt'] = substr($b[1],0,5);
        } else {
            $data['stopd'] = "";
            $data['stopt'] = "";
        };
        echo json_encode($data);
    }

if ($_GET['task'] == 'bland') {
    Buland($_GET['land']);
} else if ($_GET['task'] == 'shipto') {
    getShipto($_GET['id'],$_GET['Q']);
} else if ($_GET['task'] == 'showCalls') {
   showCalls($_GET['id'],$_GET['start'],$_GET['firma']);
} else if ($_GET['task'] == 'showShipadress') {
   showShipadress($_GET['id'],$_GET['Q']);
} else if ($_GET['task'] == 'showContact') {
   showContactadress($_GET['id']);
} else if ($_GET['task'] == 'geteventlist') {
   listTevents($_GET['id'],$_GET['fid']);
} else if ($_GET['task'] == 'editTevent') {
   editTevent($_GET['id']);
} else if ($_GET['task'] == 'getCustomTermin') {
   getCustomTermin($_GET['id'],$_GET['tab'],$_GET['day']);
}
?>
