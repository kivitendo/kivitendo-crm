<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	$jscal ="<style type='text/css'>@import url(../../$ERPNAME/js/jscalendar/calendar-win2k-1.css);</style>\n";
	$jscal.="<script type='text/javascript' src='../../$ERPNAME/js/jscalendar/calendar.js'></script>\n";
    $jscal.="<script type='text/javascript' src='../../$ERPNAME/js/jscalendar/lang/calendar-de.js'></script>\n";
    $jscal.="<script type='text/javascript' src='../../$ERPNAME/js/jscalendar/calendar-setup.js'></script>\n";

	$data["id"]=0;
	$templ="wvl1.tpl";
	$js="";
	if ($_POST["save"]) {
		if ($_POST["WVLID"]>0) {
			$ok=updWvl($_POST,$_FILES);
		} else {
			if ($_POST["Mail"]) { 
				$ok=insWvlM($_POST);
				if ($_POST["cp_cv_id"]<>"") delMail($_POST["Mail"],$_POST["CID"]);
			} else {
				$ok=insWvl($_POST,$_FILES);
			}
		}
		if ($ok<1) {
				$data=$_POST;
				if ($_POST["Mail"]) $templ="wvl2.tpl";
				$data["Datei"]=$_FILES["Datei"]["Name"];
				if ($ok==-1) { $msg="Fehler beim Zuordnen";}
				else { $msg="Fehler beim Sichern";};
				$sel=$_SESSION["loginCRM"];
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
		delMail($_POST["Mail"],$_POST["CID"]);
		$msg="";
		$sel=$_POST["CRMUSER"];
	} else if ($_GET["mail"]) {
		$data=getOneMail($_SESSION["loginCRM"],$_GET["mail"]);
		$msg="";
		$sel=$_SESSION["loginCRM"];
		$templ="wvl2.tpl";
		$data["id"]=0;
	} else if ($_POST["reset"]) {
			$data["Cause"]="";
			$data["LangTxt"]="";
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
	$t->set_file(array("wvl" => $templ));
	     if ($data["kontakttab"]=="P") { $stammlink="kontakt.php?id=".$data["kontaktid"]; }
	else if ($data["kontakttab"]=="C") { $stammlink="firma1.php?Q=C&id=".$data["kontaktid"]; }
	else if ($data["kontakttab"]=="V") { $stammlink="liefer1.php?Q=V&id=".$data["kontaktid"]; };
	$t->set_var(array(
			Msg		=> $msg,
			hide	=> ($data["kontakt"]=="F")?"hidden":"visible",
			Cause => $data["Cause"],
			LangTxt => $data["LangTxt"],
			Datei => $data["Datei"],
			DLink => "dokumente/".$data["DPath"].$data["DName"],
			DName => $data["DName"],
			cpcvid => $data["kontakttab"].$data["kontaktid"],
			Fname => $data["kontaktname"],
			DCaption => $data["DCaption"],
			Status1 => ($data["status"]==1)?" checked":"",
			Status2 => ($data["status"]==2)?" checked":"",
			Status3 => ($data["status"]==3)?" checked":"",
			R1 => ($data["kontakt"]=="T" || $data["kontakt"]=="")?" checked":"",
			R2 => ($data["kontakt"]=="M")?" checked":"",
			R3 => ($data["kontakt"]=="S")?" checked":"",
			R4 => ($data["kontakt"]=="P")?" checked":"",
			R5 => ($data["kontakt"]=="D")?" checked":"",
			R6 => ($data["kontakt"]=="F")?" checked":"",
			CID => $_SESSION["loginCRM"],
			WVLID => $data["id"],
			noteid => $data["noteid"],
			Finish => $data["Finish"],
			jcal0 => ($jcalendar)?$jscal:"",
			jcal1 => ($jcalendar)?"<a href='#' id='trigger1' name='Finish' title='Zieldatum' onClick='false'><img src='image/date.png' border='0' align='middle'></a>":"",
			JS => $js,
			stammlink => $stammlink,
			Mail => $_GET["mail"],  
            mailtype => $data["mailtype"],
			));
	if ($templ=="wvl2.tpl") {
		$t->set_block("wvl","Filebox","Block2");
		if ($data["Anhang"]){
			$FILES="<td colspan='2'><input type='checkbox' name='dateien[]' value='%s,%s,%s' checked>[<a href='tmp/%s' class='klein'>%s</a>]</td>";
			foreach($data["Anhang"] as $zeile) {
				$t->set_var(array(
					file	=>	sprintf($FILES,$zeile["name"],$zeile["size"],$zeile["type"],$zeile["name"],$zeile["name"])
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
	$nouser[0]=array("login" => "-----");
	$user=array_merge($nouser,$user);
	if ($user) foreach($user as $zeile) {
		$t->set_var(array(
			Sel => ($sel==$zeile["id"])?" selected":"",
			UID	=>	$zeile["id"],
			Login	=>	$zeile["login"],
		));
		$t->parse("Block1","Selectbox",true);
	}
	$t->pparse("out",array("wvl"));
?>
