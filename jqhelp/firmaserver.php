<?php
    require_once("../inc/stdLib.php");
    include("FirmenLib.php");

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

if ($_GET['task'] == 'bland') {
    Buland($_GET['land']);
} else if ($_GET['task'] == 'shipto') {
    getShipto($_GET['id'],$_GET['Q']);
}
?>
