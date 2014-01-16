<?php
    require_once("../inc/stdLib.php");
    include_once("crmLib.php");
    include_once("UserLib.php");
    include_once("FirmenLib.php");

    function getMailTpl($id,$KontaktTO='') {
        $data=getOneMailVorlage($id);
        $Subject=$data["cause"];
        $BodyText=$data["c_long"];
        if ($KontaktTO<>'') {
            $user=getUserStamm($_SESSION["loginCRM"]);
            if (substr($KontaktTO,0,1)=="K") {
                include("inc/persLib.php");
                $empf=getKontaktStamm(substr($KontaktTO,1));
                $tmp = getFirmaCVars($empf["cp_cv_id"]);
                if ($tmp) foreach($tmp as $key=>$val) { $empf[$key]=$val; };
            //} else if (substr($KontaktTO,0,1)=="C") {
            } else if (substr($KontaktTO,0,1)=="S") {
                $empf= getShipStamm(substr($KontaktTO,1),'C',True); // <- Da noch mal ran. Hart Kundenstamm
            } else if ($KontaktTO) {
                $empf=getFirmenStamm(substr($KontaktTO,1),true,substr($KontaktTO,0,1));
            };
            foreach ($user as $key=>$val) {
                $empf['employee'.strtolower($key)]=$val;
            }
            preg_match_all("/%([A-Z0-9_]+)%/iU",$BodyText,$ph, PREG_PATTERN_ORDER);
            $ph=array_slice($ph,1);
            if ($ph[0]) {
                $anrede=false;
                foreach ($ph[0] as $x) {
                    $y=$empf[$x];
                    if ($x=="cp_greeting") $anrede=$y;
                    $BodyText=preg_replace("/%".$x."%/i",$y,$BodyText);
                }
                if ($anrede=="Herr") { $BodyText=preg_replace("/%cp_anrede%/","r",$BodyText); }
                else if ($anrede) { $BodyText=preg_replace("/%cp_anrede%/","",$BodyText); }
            }
        }
        //$MailSign=ereg_replace("\r","",$user["mailsign"]);
        $Response = array('subject'=>$Subject,'bodytxt'=>$BodyText); //." \n".$MailSign);
        echo json_encode($Response);
    }
    function saveMailTpl($sub,$txt,$mid=0) {
        $rc=saveMailVorlage(array("Subject"=>$sub,"BodyText"=>$txt,"MID"=>$mid));
        if ($rc){
            echo json_encode(array('rc'=>$rc));
        } else {
            echo json_encode(array('rc'=>''));
        }
    }
    function delMailTpl($id) {
        $rc=deleteMailVorlage($id);
        if ($rc) {
            echo json_encode(array('rc'=>'ok'));
        } else {
            echo json_encode(array('rc'=>''));
        }
    }

    function suchmail($mail) {
        $rsC = getAllFirmenByMail($mail,true,"C"); 
        $rsV = getAllFirmenByMail($mail,true,"V");
        $rs = array();
        if ($rsC) foreach ( $rsC as $key => $value ) { 
            if (count($rs) > 11) break;
            array_push($rs,array('label'=>$value['name'],'category'=>'Kunden')); 
        } 
        if ($rsV) foreach ( $rsV as $key => $value ) {
            if (count($rs) > 11) break; 
            array_push($rs,array('label'=>$value['name'],'category'=>'Lieferanten'));//ToDo translate 
        }
        echo json_encode($rs);
    }
if ($_GET['case'] == 'get') {
    getMailTpl($_GET['template'],$_GET['to']);
} else if ($_POST['case'] == 'save') {
    saveMailTpl($_POST['subject'],$_POST['bodytxt'],$_POST['template']);
} else if ($_GET['case'] == 'del') {
    delMailTpl($_GET['template']);
} else if ($_GET['case'] == 'mailsearch') {
    require_once("../inc/FirmenLib.php");
    suchmail($_GET['term']);
}
?>
