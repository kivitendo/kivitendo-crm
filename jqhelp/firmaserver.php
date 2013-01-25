<?php
    require_once("../inc/stdLib.php");
    include("FirmenLib.php");
    include("crmLib.php");

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

if ($_GET['task'] == 'bland') {
    Buland($_GET['land']);
} else if ($_GET['task'] == 'shipto') {
    getShipto($_GET['id'],$_GET['Q']);
} else if ($_GET['task'] == 'showCalls') {
   showCalls($_GET['id'],$_GET['start'],1);
} else if ($_GET['task'] == 'showShipadress') {
   showShipadress($_GET['id'],$_GET['Q']);
}
?>
