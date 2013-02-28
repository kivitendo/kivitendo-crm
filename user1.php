<?php
    require_once("inc/stdLib.php");
    include("inc/grafik1.php");
    include("inc/template.inc");
    include("inc/crmLib.php");
    include("inc/UserLib.php");
    if ($_POST["ok"] and $_POST["termseq"]<61) {
        if ($_POST["proto"]==1) { $_POST["proto"] = 't';
        } else { $_POST["proto"] = 'f';}
        $rc=saveUserStamm($_POST);
        $id=$_POST["UID"];
        $_SESSION["termbegin"]=$_POST["termbegin"];
        $_SESSION["termend"]=$_POST["termend"];
        $_SESSION["termseq"]=$_POST["termseq"];
        $_SESSION["pre"]=$_POST["pre"];
        $_SESSION["preon"]=$_POST["preon"];
        $_SESSION["kdview"]=$_POST["kdview"];
        $_SESSION["planspace"]=$_POST["planspace"];
        $_SESSION["feature_ac"]=$_POST["feature_ac"];
        $_SESSION["feature_ac_minLength"]=$_POST["feature_ac_minLength"];
        $_SESSION["feature_ac_delay"]=$_POST["feature_ac_delay"];  
        $_SESSION["auftrag_button"]=$_POST["auftrag_button"]; 
        $_SESSION["angebot_button"]=$_POST["angebot_button"]; 
        $_SESSION["rechnung_button"]=$_POST["rechnung_button"]; 
        $_SESSION["zeige_extra"]=$_POST["zeige_extra"]; 
        $_SESSION["zeige_lxcars"]=$_POST["zeige_lxcars"];         
        $_SESSION['theme']=($_POST['theme']=='base')?'':'<link rel="stylesheet" type="text/css" href="'.$_SESSION['baseurl'].'crm/jquery-ui/themes/'.$_POST['theme'].'/jquery-ui.css">';  
        
        } else if ($_POST["mkmbx"]) {
        $rc=createMailBox($_POST["Postf2"],$_POST["Login"]);
    } 
    $t = new Template($base);
    $menu =  $_SESSION['menu'];
    $t->set_var(array(
        JAVASCRIPTS   => $menu['javascripts'],
        STYLESHEETS   => $menu['stylesheets'],
        PRE_CONTENT   => $menu['pre_content'],
        START_CONTENT => $menu['start_content'],
        END_CONTENT   => $menu['end_content'],
        'THEME'         => $_SESSION['theme'],
    ));
    if ($_GET["id"] && $_GET["id"]<>$_SESSION["loginCRM"]) {
        $fa=getUserStamm($_GET["id"]);
        $t->set_file(array("usr1" => "user1b.tpl"));
    } else {
        $fa=getUserStamm($_SESSION["loginCRM"]);
        $t->set_file(array("usr1" => "user1.tpl"));
    }
    if (empty($fa["ssl"])) $fa["ssl"] = "n";
    if (empty($fa["proto"])) $fa["proto"] = "t";
    if ($fa) foreach ($fa["gruppen"] as $row) {
        $gruppen.=$row["grpname"]."<br>";
    }
    $i=($fa["termbegin"]>=0 && $fa["termbegin"]<=23)?$fa["termbegin"]:8;
    $j=($fa["termend"]>=0 && $fa["termend"]<=23 && $fa["termend"]>$fa["termbegin"])?$fa["termend"]:19;
    for ($z=0; $z<24; $z++) {
        $tbeg.="<option value=$z".(($i==$z)?" selected":"").">$z";
        $tend.="<option value=$z".(($j==$z)?" selected":"").">$z";
    }

    if ($jahr=="") $jahr = date("Y");
    $re = getReJahr($fa["id"],$jahr,false,true);
    $an = getAngebJahr($fa["id"],$jahr,false,true);
    $IMG=getLastYearPlot($re,$an,false);
    $t->set_var(array(
            ERPCSS      => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
            IMG         => $IMG,
            login       => $fa["login"],
            name        => $fa["name"],
            addr1       => $fa["addr1"],
            addr2       => $fa["addr2"],
            addr3       => $fa["addr3"],
            uid         => $fa["id"],
            homephone   => $fa["homephone"],
            workphone   => $fa["workphone"],
            role        => $fa["role"],
            notes       => $fa["notes"],
            mailsign    => $fa["mailsign"],
            email       => $fa["email"],
            emailauth   => $_SESSION["email"],
            msrv        => $fa["msrv"],
            port        => $fa["port"],
            mailuser    => $fa["mailuser"],
            kennw       => $fa["kennw"],
            postf       => $fa["postf"],
            postf2      => $fa["postf2"],
            protopop    => ($fa["proto"]=="f")?"checked":"",
            protoimap   => ($fa["proto"]=="t")?"checked":"",
            ssl.$fa["ssl"] => "checked",
            interv      => $fa["interv"],
            pre         => $fa["pre"],
            kdview.$fa["kdview"] => "selected",
            abteilung   => $fa["abteilung"],
            position    => $fa["position"],
            termbegin   => $tbeg,
            termend     => $tend,
            termseq     => ($fa["termseq"])?$fa["termseq"]:30,
            GRUPPE      => $gruppen,
            DATUM       => date('d.m.Y'),
            icalext     => $fa["icalext"],
            icaldest    => $fa["icaldest"],
            icalart.$fa["icalart"] => "selected",
            preon       => ($fa["preon"])?"checked":"",
	        streetview  => ($fa['streetview'])?$fa['streetview']:$stadtplan,
	        planspace   => ($fa['planspace'])?$fa['planspace']:$planspace,
	        feature_ac             => ($fa['feature_ac']=='t')?'checked':'',
	        feature_ac_minlength   => $fa['feature_ac_minlength'],
	        feature_ac_delay       => $fa['feature_ac_delay'],
	        auftrag_button         => ($fa['auftrag_button']=='t')?'checked':'',
	        angebot_button         => ($fa['angebot_button']=='t')?'checked':'',
	        rechnung_button        => ($fa['rechnung_button']=='t')?'checked':'',
	        zeige_extra            => ($fa['zeige_extra']=='t')?'checked':'',
	        zeige_lxcars           => ($fa['zeige_lxcars']=='t')?'checked':'',
            ));
    if ($_GET["id"]) {    
        $t->set_var(array(vertreter => $fa["vertreter"]." ".$fa["vname"]));
    } else {
        $t->set_block("usr1","Selectbox","Block");
        $select=(!empty($fa["vertreter"]))?$fa["vertreter"]:$fa["id"];
        $user=getAllUser(array(0=>true,1=>""));
        if ($user) foreach($user as $zeile) {
            $t->set_var(array(
                Sel => ($select==$zeile["id"])?" selected":"",
                vertreter    =>    $zeile["id"],
                vname    =>    $zeile["name"]
            ));
            $t->parse("Block","Selectbox",true);
        }
        $t->set_block("usr1","SelectboxB","BlockB");
        $ALabels=getLableNames();
        if ($ALabels) foreach ($ALabels as $data) {
                $t->set_var(array(
                    FSel => ($data["id"]==$fa["etikett"])?" selected":"",
                    LID    =>    $data["id"],
                    FTXT =>    $data["name"]
                ));
                $t->parse("BlockB","SelectboxB",true);
        }
        $t->set_block("usr1","Liste","BlockD");
        $i=0;
        if ($items) foreach($items as $col){
            $t->set_var(array(
                IID => $col["id"],
                LineCol    => ($i%2+1),
                Datum    => db2date(substr($col["calldate"],0,10)),
                Zeit    => substr($col["calldate"],11,5),
                Name    => $col["cp_name"],
                Betreff    => $col["cause"],
                Nr        => $col["id"]
            ));
            $t->parse("BlockD","Liste",true);
            $i++;
        }
    }
    chdir("jquery-ui/themes");
    $theme = glob("*");
    $t->set_block('usr1','Theme','BlockT');
    if ($theme) foreach( $theme as $file) {
        $t->set_var(array(
               TSel => ($file==$fa["theme"])?" selected":"",
               themefile => $file,
               themename => ucwords(strtr($file,'-',' ')),
        ));
        $t->parse('BlockT','Theme',true);
    };
    chdir("../..");
    $t->pparse("out",array("usr1"));
?>
