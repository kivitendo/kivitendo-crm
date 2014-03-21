<?php
    require_once("inc/stdLib.php");
    require_once("inc/crmLib.php");
    include("inc/template.inc");

    $t = new Template($base);
    doHeader($t);
    $t->set_file(array("tt" => "timetrack.tpl"));
    $data['clear'] = 1;
    if ($_POST["action"] == "save") {
        //Sichert die obere Maske
        if ($_POST['fid'] <= 0 )             { $data = $_POST; $data['msg'] = '.:missinge:. .:company:.';   }
        else if ($_POST['ttname'] == '' )    { $data = $_POST; $data['msg'] = '.:missings:. .:project:.';   }
        else if ($_POST['startdate'] == '' ) { $data = $_POST; $data['msg'] = '.:missings:. .:startdate:.'; }
        else if ($_POST['aim'] == '' )       { $data = $_POST; $data['msg'] = '.:missinge:. .:hours:.';     }
        else {
            $data = saveTT($_POST);
        }
    } else if ($_POST["action"] == "clear") {
        if ($_POST["fid"] != '' && $_POST['clear'] < 2) {
            unset($data);
            $data['name'] = $_POST['name'];
	        $data['fid']  = $_POST['fid'];
	        $data['tab']  = $_POST['tab'];
            $data["active"] = "t";
            $data['clear'] = 2;
        }
    } else if ($_POST["action"] == "delete") {
        //Einen Zeiteintrag löschen, obere Maske
        $rc = deleteTT($_POST["id"]);
        if ($rc) {
            $msg = ".:deleted:.";
            $data['name'] = $_POST['name'];
	        $data['fid']  = $_POST['fid'];
	        $data['tab']  = $_POST['tab'];
        } else {
            $data = getOneTT($_POST["id"]);
            $msg = ".:not posible:.";
        };
    } else if ($_POST["action"] == "search" || $_GET["fid"]) {
        //Suchen eines Zeiteintrages, obere Maske
    	if ($_GET["fid"]) $_POST = $_GET;
        $data = searchTT($_POST);
        if (count($data)>1) {
            $t->set_block("tt","Liste","Block");
            foreach ($data as $row) {
                $t->set_var(array(
                    tid => $row["id"],
                    ttn => $row["ttname"]
                ));
                $t->parse("Block","Liste",true);
            }
            $visible = true;
            $data = $_POST;
	    if ($_GET["fid"])	$data["backlink"] = "firma1.php?Q=".$_GET["Q"]."&id=".$_GET["fid"];
        } else if (count($data)==0) {
            $data = $_POST;
            $data["msg"] = ".:not found:.";
	    if ($_GET["fid"])	$data["backlink"] = "firma1.php?Q=".$_GET["Q"]."&id=".$_GET["fid"];
        } else {
            $data = getOneTT($data[0]["id"]);
	    $delete = ($data["uid"]==$_SESSION["loginCRM"])?True:False;
        }
    } else if ($_POST["getone"]) {
        //Eintrag der Auswahlliste der Zeiteinträge einer Firma holen
        $data = getOneTT($_POST["tid"]);
        $delete = ($data["uid"]==$_SESSION["loginCRM"])?True:False;
    } else if ($_POST["savett"]) {
        //Einen Zeiteintrag sichern, untere Maske
        $rc = saveTTevent($_POST);
        $data = getOneTT($_POST["tid"]);
    } else if ($_GET["stop"]=="now") {
        //Endzeitpunkt für einen Zeiteintrag sichern, untere Maske
        $rc = stopTTevent($_GET["eventid"],date('Y-m-d H:i'));
        $data = getOneTT($_GET["tid"]);
        if ( !$rc ) $data['msg'] = ".:error:. .:close event:.";
    } else if ($_POST["clr"]) {
            if ($_POST["clrok"]=="1" || count($_POST["clear"])>0) {
                if ($_POST["tid"]) {
                    if (count($_POST["clear"])>0) $evids = "and t.id in (".implode(",",$_POST["clear"]).") ";
                    $msg = mkTTorder($_POST["tid"],$evids,$_POST['order']);
                    $data = getOneTT($_POST["tid"]);
                    $data["msg"] = $msg;
                } else {
                    $data["msg"] = ".:missing:. .:customer:.";
                }
            } else {
                $data = getOneTT($_POST["tid"]);
                $data["msg"] = ".:clrok:.";
            }
    } else {
        unset($data);
        if ( $data['fid'] ) {
            $curr = getCurrCompany($data['fid'],$data['tab']);
            $data["cur"] = $curr['name'];
        } else {
            $data["cur"] = getCurr();
        }        
        $data["active"] = "t";        
    }
    if ($data["events"]) {
	    $delete = False;
    }

    if ($data["fid"]) $data["backlink"] = "firma1.php?Q=".$data["tab"]."&id=".$data["fid"];
    $t->set_var(array(
        backlink => $data["backlink"],
        blshow  => ($data["backlink"])?"visible":"hidden",
        noevent => ($data["active"]=="t" && $data['id'])?"visible":"hidden",
        noown   => ($data["id"]>0 && $data["uid"]!=$_SESSION["loginCRM"])?"hidden":"visible",
        id      => $data["id"],
        budget  => sprintf('%0.2f',$data["budget"]),
        cur     => $data["cur"],
        clear   => $data['clear'],
        name    => $data["name"],
        fid     => $data["fid"],
        tab     => $data["tab"],
        aim     => $data["aim"],
        ttname  => $data["ttname"],
        ttdescription => $data["ttdescription"],
        startdate  => $data["startdate"],
        stopdate   => $data["stopdate"],
        active.$data["active"] => "checked",
        msg     => $data["msg"],
        visible => ($visible)?"visible":"hidden",
        delete  => ($delete)?"visible":"hidden",
        chkevent => ($data["id"]>0)?"onLoad='getEventListe();'":"",
    ));

    $t->Lpparse("out",array("tt"),$_SESSION['countrycode'],"work");
?>
