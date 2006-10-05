<?
require_once("inc/stdLib.php");
require_once("inc/crmLib.php");
include_once("Mail.php");
include_once("Mail/mime.php");

function replace_var($body) {
	
	return $body;
}

$offset=($_GET["offset"])?$_GET["offset"]:1;
$mime = new Mail_Mime("\n");
$mail =& Mail::factory("mail");
			
require("tmp/".session_id().".data");

if ($logmail) $f=fopen("tmp/maillog.txt","a");

if ($_GET["datei"]) {
	$mime->addAttachment("tmp/".session_id(), $type,$dateiname, true );
}
$hdr = $mime->headers($headers);

$sql="select * from tempcsvdata where uid = '".$_SESSION["loginCRM"]."' limit 1";
$data=$db->getAll($sql);
$felder=split(";",$data[0]["csvdaten"]);
$pemail=array_search("EMAIL",$felder);
$pkont=array_search("KONTAKT",$felder);

$sql="select * from tempcsvdata where uid = '".$_SESSION["loginCRM"]."' offset ".$offset." limit ".$limit;
$data=$db->getAll($sql);
if ($data) {
	foreach ($data as $row) {
		$text=$bodytxt;
		$to="";
		$tmp=split(";",$row["csvdaten"]);
		if ($tmp[$pkont]<>"" and $tmp[$pemail]<>"") {
			$to=$tmp[$pkont]." <".$tmp[$pemail].">";
		} else {
			$to=$tmp[$pemail];
		}
		$to=preg_replace( "/[^a-z0-9 !?:;,.\/_\-=+@#$&\*\(\)]/im", "", $to);
		if ($to<>"") {
			preg_match_all("/%([A-Z0-9_]+)%/U",$text,$ph, PREG_PATTERN_ORDER);
			if ($ph) {
				$ph=array_slice($ph,1);
				if ($ph[0]) { foreach ($ph as $x) {
					foreach ($x as $u) {
						$p=array_search($u,$felder);
						if ($p!==false) {
							$y=$tmp[$p];
							$text=str_replace("%".$u."%",$y,$text);
						} else {
							$text=str_replace("%".$u."%","",$text);
						}
					}
				}};
			};
			$mime->setTXTBody($text);
			$body = $mime->get(array("text_encoding"=>"quoted-printable"));
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
