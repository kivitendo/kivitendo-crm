<?php
// $Id$
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	include("inc/UserLib.php");
	include_once("Mail.php");
	include_once("Mail/mime.php");


	$user=getUserStamm($_SESSION["loginCRM"]);
	$MailSign=ereg_replace("\r","",$user["MailSign"]);
	$BodyText=" \n".$MailSign;
	$MailSign=ereg_replace("\n","<br>",$user["MailSign"]);
	$MailSign=ereg_replace("\r","",$MailSign);

	if ($_POST) {
		$Subject=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["Subject"]);
		$BodyText=preg_replace( "/(content-type:|bcc:|cc:|to:|from:)/im", "", $_POST["BodyText"]);
		//$Subject = $_POST["Subject"];
		//$BodyText = $_POST["BodaText"];
		$okC=true;
		if ($_POST["CC"]<>"") { 
			$CC=preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)]/im", "", $_POST["CC"]);
			$rc=chkMailAdr($CC); 
			if($rc<>"ok") { 
				$okC=false; $msg.=" CC:".$rc; 
			} else {
				insertCSVData(array("CC",$_POST["CC"],"","","","","","",$CC,""));
			}
		};
		if ($okC) {
			$anh="";
			if ($_FILES["Datei"]["name"]<>"") {
				move_uploaded_file($_FILES["Datei"]["tmp_name"],"tmp/".session_id());
				$dateiname=$_FILES["Datei"]["name"];
				$type=$_FILES["Datei"]["type"];
			}
			$limit=50;
			$logmail=true;
			$f=fopen("tmp/".session_id().".data","w");
			fputs($f,"<?php\n");	
			$abs=$user["Name"]." <".$user["eMail"].">";
			fputs($f,"\$abs='$abs';\n");
			fputs($f,"\$headers['Replay-To']='$abs';\n");	
			fputs($f,"\$headers['From']='$abs';\n");	
			fputs($f,"\$headers['Return-Path']='$abs';\n");	
			fputs($f,"\$headers['X-Mailer']='PHP/".phpversion()."';\n");
			//$subject=$_POST["Subject"];
			fputs($f,"\$headers['Subject']='$Subject';\n");
			fputs($f,"\$subject='$Subject';\n");
			//$bodytxt=strip_tags($_POST["BodyText"]);
			fputs($f,"\$bodytxt='$BodyText';\n");
			fputs($f,"\$dateiname='$dateiname';\n");
			fputs($f,"\$type='$type';\n");
			fputs($f,"\$limit='$limit';\n");
			fputs($f,"\$logmail='$logmail';\n");
			fputs($f,"?>\n");
			fclose($f);

			$sendtxt="Es &ouml;ffnet sich nun ein extra Fenster.<br>";
			$sendtxt.="Bitte schlie&szlig;en sie es nur wenn sie dazu aufgefordert werden,<br>";
			$sendtxt.="da sonst der Mailversand beendet wird.<br><br>";
			$sendtxt.="Sie k&ouml;nnen aber ganz normal mit anderen Programmteilen arbeiten.";
			$sendtxt.="<script language='JavaScript'>fx=open('sendsermail.php?first=1$anh','sendmail','width=200,height=100');</script>";
		}
	} 
	
		$t = new Template($base);
		$t->set_file(array("mail" => "sermail.tpl"));
		$t->set_var(array(
				Msg		=> $msg,
				CC		=> $_POST["CC"],
				Subject => $subject,
				BodyText => $_POST["BodyText"],
				Sign => $MailSign,
				SENDTXT => $sendtxt
		));
		$t->pparse("out",array("mail"));
			
?>
