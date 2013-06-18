<?php
    require_once("inc/stdLib.php");
    include("inc/template.inc");
    include("inc/crmLib.php");
    include_once("inc/UserLib.php");
    $data["id"]=0;
    $templ="wvln.tpl";
    $showmail = false;
    if ($_POST["save"]) {
        if ($_POST["WVLID"]>0) {
            $ok = updWvl($_POST,$_FILES);
        } else {
            if ($_POST["mail"]) { 
                $ok = insWvlM($_POST,$_SESSION['MailFlag'],$_SESSION['Expunge']);
            } else {
                $ok = insWvl($_POST,$_FILES);
            }
        }
        if ( $ok < 1 ) {
                $data = $_POST;
                if ($_POST["mail"]) {
                    $showmail = true; 
                    $data['id']=0;
                    $data['flags']['flagged'] = $data['flagged'];
                    $data['flags']['answered'] = $data['answered'];
                    $data['flags']['deleted'] = $data['deleted'];
                    $data['flags']['seen'] = $data['seen'];
                    $data['flags']['draft'] = $data['draft'];
                    $data['flags']['recend'] = $data['recend'];
                };
                $data["Datei"]=$_FILES["Datei"]["Name"];
                if ( $ok == -2 ) { $msg="Fehler beim Zuordnen";}
                else if ( $ok == -1 ) { $msg = 'Gruppe nicht zulässig oder User fehlt!'; }
                else { $msg="Fehler beim Sichern";};
                $msg .= " $ok!";
                $sel = $_POST['CRMUSER'];
        } else {
            $data["id"]=0;
            header ("location:wvl1.php");
            exit;
        }
    } else if ($_GET["erp"]) {
        $data=getOneERP($_GET["erp"]);
        $msg="";
        $sel=$_SESSION["loginCRM"];
    } else if ($_GET["show"]) {
        $data=getOneWvl($_GET["show"]);
        $msg="";
        $sel=$data["CRMUSER"];
    } else if ($_POST["delete"]) {
        delMail($_POST["mail"],$_POST["CID"],$_SESSION['Expunge']);
        $msg="";
        $sel=$_POST["CRMUSER"];
    } else if ($_GET["mail"]) {
        $data=getOneMail($_SESSION["loginCRM"],$_GET["mail"]);
        $data['mail'] = $_GET['mail'];
        $msg="";
        $sel=$_SESSION["loginCRM"];
        $showmail = true;
        $data["id"]=0;
    } else if ($_POST["reset"]) {
            $data["cause"]="";
            $data["c_long"]="";
            $data["Datei"]="";
            $data["DCaption"]="";
            $data["status"]=1;
            $data["Finish"]="";
            $data["id"]=0;
            $data["kontakt"]="T";
            $msg="";
            $sel=$_SESSION["loginCRM"];
    } else {
        $data["status"]=1;
        $msg="";
        $sel=$_SESSION["loginCRM"];
    }
    $t = new Template($base);
    doHeader($t);
    $t->set_file(array("wvl" => $templ));
         if ($data["kontakttab"]=="P") { $stammlink="kontakt.php?id=".$data["kontaktid"]; }
    else if ($data["kontakttab"]=="C") { $stammlink="firma1.php?Q=C&id=".$data["kontaktid"]; }
    else if ($data["kontakttab"]=="V") { $stammlink="firma1.php?Q=V&id=".$data["kontaktid"]; };
    $t->set_var(array(
            'hidenomail'  => ($showmail)?'none':'run-in',
            'hidemail'    => ($showmail)?'run-in':'none',
            'timeout'     => $_SESSION['interv']*1000,
            'Msg'         => $msg,
            'hide'        => ($data["kontakt"]=="F")?"hidden":($showmail)?"hidden":"visible",
            'cause'       => $data["cause"],
            'c_long'      => $data["c_long"],
            'Datei'       => $data["Datei"],
            'DLink'       => "dokumente/".$data["DPath"].$data["DName"],
            'DName'       => $data["DName"],
            'cpcvid'      => $data["kontakttab"].$data["kontaktid"],
            'Fname'       => $data["kontaktname"],
            'DCaption'    => $data["DCaption"],
            'Status'.$data["status"] => " checked",
            'R1'          => ($data["kontakt"]=="T" || $data["kontakt"]=="")?" checked":"",
            'R2'          => ($data["kontakt"]=="M")?" checked":"",
            'R3'          => ($data["kontakt"]=="S")?" checked":"",
            'R4'          => ($data["kontakt"]=="P")?" checked":"",
            'R5'          => ($data["kontakt"]=="D")?" checked":"",
            'R6'          => ($data["kontakt"]=="F")?" checked":"",
            'CID'         => $_SESSION["loginCRM"],
            'WVLID'       => $data["id"],
            'noteid'      => $data["noteid"],
            'Finish'      => $data["Finish"],
            'stammlink'   => $stammlink,
            'mail'        => $data["mail"],  
            'muid'        => $data["muid"],  
            'mailtype'    => $data["mailtype"],
            'flagged'.$data['flags']['flagged'] => 'checked',
            'answered'.$data['flags']['answered'] => 'checked',
            'deleted'.$data['flags']['deleted'] => 'checked',
            'seen'.$data['flags']['seen'] => 'checked',
            'draft'.$data['flags']['draft'] => 'checked',
            'recend'.$data['flags']['recend'] => 'checked',
            ));
    if ($showmail) {
        $t->set_block("wvl","Filebox","Block2");
        if ($data["Anhang"]){
            $FILES="<input type='checkbox' name='dateien[]' value='%s,%s,%s' checked>[<a href='/tmp/%s' class='klein'>%s</a>]<br>";
            foreach($data["Anhang"] as $zeile) {
                $t->set_var(array(
                    file    =>    sprintf($FILES,$zeile["name"],$zeile["size"],$zeile["type"],$zeile["name"],$zeile["name"])
                ));
                $t->parse("Block2","Filebox",true);
            }
        } else {
            $t->set_var(array(file => ""));
            $t->parse("Block2","Filebox",true);
        }
    }
    $t->set_block("wvl","Selectbox","Block1");
    $user=getAllUser("%");
    $gruppen = getGruppen(true);
    $nouser[0]=array("login" => "-----");
    $user=array_merge($nouser,$user);
    $user = array_merge($user,$gruppen);
    if ($user) foreach($user as $zeile) {
        $t->set_var(array(
            Sel     => ($sel==$zeile["id"])?" selected":"",
            UID     => $zeile["id"],
            Login   => ( $zeile['name'] != '' )?$zeile['name']:$zeile["login"],
        ));
        $t->parse("Block1","Selectbox",true);
    }
    $t->pparse("out",array("wvl"));
?>
