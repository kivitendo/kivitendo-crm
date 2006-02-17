<?
require_once("inc/stdLib.php");
require_once("inc/crmLib.php");
include_once("Mail.php");
include_once("Mail/mime.php");

$offset=($_GET["offset"])?$_GET["offset"]:0;
$mime = new Mail_Mime("\n");
$mail =& Mail::factory("mail");
			
require("tmp/".session_id().".data");

if ($logmail) $f=fopen("tmp/maillog.txt","a");

$mime->setTXTBody($bodytxt);
if ($_GET["datei"]) {
	$mime->addAttachment("tmp/".session_id(), $type,$dateiname, true );
}
$body = $mime->get(array("text_encoding"=>"quoted-printable"));
$hdr = $mime->headers($headers);

$sql="select * from tempcsvdata where sessid = '".session_id()."' offset ".$offset." limit ".$limit;
$data=$db->getAll($sql);
if ($data) {
	foreach ($data as $row) {
		$to="";
		$tmp=split(",",$row["csvdaten"]);
		if ($tmp[9]<>"") {
			$to=$tmp[9]." <".$tmp[8].">";
		} else {
			$to=$tmp[8];
		}
		if ($to<>"") {
			$rc=$mail->send($to, $hdr, $body);
			if ($rc) {
				$data["CRMUSER"]=$_SESSION["loginCRM"];
				$data["cause"]=$subject;
				$data["c_cause"]=$bodytxt."\nAbs: ".$abs;
				$data["CID"]="";
				$data["Kontakt"]="m";
				$data["Bezug"]=0;
				$data['Zeit']=date("H:i");
				$data['Datum']=date("d.m.Y");
				$data["DateiID"]=0;
				$data["Status"]=1;
				$data["DCaption"]=$subject;
				$stamm=false;
				// Einträge in den Kontaktverlauf
				$data["CID"]=$tmp[10];
				insCall($data,false);
				if ($_GET["first"]==1) {
					$data["CID"]=$_SESSION["loginCRM"];		// Dann halt beim Absender in den Thread eintragen
					$data["cause"]=$data["cause"]."|Serienmail";
					$data["c_cause"]=$data["c_cause"]."\n$dateiname";
					$data["Kontakt"]="M";
					insCall($data,false);
					$_GET["first"]=0;
				}		
				if ($logmail) fputs($f,date("Y-m-d H:i").";ok;$to;$abs;$subject\n");
			} else {
				if ($logmail) fputs($f,date("Y-m-d H:i").";error;$to;$abs;$subject\n");
			} // if $rc
		} //if to
	} // foreach
	header("location: sendsermail.php?offset=".($offset+$limit));
} else {
?>
	<center>
	Keine weiteren Mails.<br>
	<a href="javascript:self.close()">Sie k&ouml;nnen das Fenster 
	jetzt schie&szlig;en</a>
	</center>
	
<? } ?>
