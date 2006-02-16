<?
// $Id: etiketten.php 4 2006-01-28 16:11:22Z root $
	require_once("inc/stdLib.php");
	include("inc/crmLib.php");
	include("inc/UserLib.php");	
	$usr=getUserStamm($_SESSION["loginCRM"]);
	$ALabels=getLableNames();
	if (!$_POST["format"] || empty($_POST["format"])) {
		$_POST["format"]=$usr["etikett"];
	} else {
		$tmp=split("=",$_POST["src"]);
		$_GET[$tmp[0]]=$tmp[1];
	}
	$form=$_POST["format"]; 
	$label=getOneLable($form);
	if ($_POST["print"]) {
		$platzhalter=array("ANREDE"=>"anredeF","NAME"=>"name1F","NAME2"=>"name2F","STRASSE"=>"strasseF","PLZ"=>"plzF","ORT"=>"ortF","KONTAKT"=>"name1P",
							"ANREDEPERS"=>"anredeP","TITLE"=>"title","NAMEPERS"=>"name1P","STRASSEPERS"=>"strasseP","PLZPERS"=>"plzP","ORTPERS"=>"ortP","LAND"=>"landF");
		$lableformat=array("paper-size"=>$label["papersize"],'name'=>$label["name"], 'metric'=>$label["metric"], 
							'marginLeft'=>$label["marginleft"], 'marginTop'=>$label["margintop"], 
							'NX'=>$label["nx"], 'NY'=>$label["ny"], 'SpaceX'=>$label["spacex"], 'SpaceY'=>$label["spacey"],
							'width'=>$label["width"], 'height'=>$label["height"], 'font-size'=>6);
		require_once('inc/PDF_Label.php');
		$tmp=split(":",$_POST["xy"]);
		$SX=substr($tmp[0],1);
		$SY=substr($tmp[1],1);
		$pdf = new PDF_Label($lableformat, $label["metric"], $SX, $SY);
		$pdf->Open(); 
		unset($tmp);
		if ($SX<>1 or $SY<>1)	$pdf->AddPage();
		$fn=fopen("tmp/suche_".$_SESSION["loginCRM"].".csv","r");
		$header=fgetcsv($fn,1000,",","'");
		$data=fgetcsv($fn,1000,",","'");
		while (!feof($fn)) {
			unset($tmp);
			foreach ($label["Text"] as $row) {
				preg_match_all("/%([A-Z0-9_]+)%/U",$row["zeile"],$ph, PREG_PATTERN_ORDER);
				if ($ph) {
					$ph=array_slice($ph,1);
					if ($ph[0]) { foreach ($ph as $x) {
						foreach ($x as $u) {
							$p=array_search($u,$header);
							if ($p) {
								$y=$data[$p];
								$row["zeile"]=str_replace("%".$u."%",$y,$row["zeile"]);
							} else {
								$row["zeile"]=str_replace("%".$u."%","",$row["zeile"]);
							}
						}
					}};
				};
				$text=$row["zeile"];
				$tmp[]=array("text"=>$text,"font"=>$row["font"]);
			};
			$pdf->Add_PDF_Label2($tmp);
			$data=fgetcsv($fn,1000,",","'");
		}
		
		$pdf->Output();
	}
?>
<html>
<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<body>

<form name='form' method='post' action='etiketten.php'>
	<input type="hidden" name="src" value="<?= $dest ?>">
	&nbsp;Etikett:<br>
	&nbsp;<select name='format' >
<?	foreach ($ALabels as $data) { ?>
		<option value='<?= $data["id"]?>'<?= ($data["id"]==$_POST["format"])?" selected":"" ?>><?= $data["name"] ?>
<?	} ?>
	</select>&nbsp;<input type='submit' name='chfrm' value='wechseln'><br><br>
	&nbsp;Bitte Startposition ausw&auml;hlen.<br>
	&nbsp;Es wird Spaltenweise verarbeitet.<br>
<?
	$sel=" checked";
	for ($y=1; $y<=$label["ny"];$y++) {
		echo "\t\t\t\t".sprintf("&nbsp;%02d",$y);
		for ($x=1; $x<=$label["nx"];$x++) {
			echo "<input type='radio' name='xy' value='x$x:y$y'$sel>";
			$sel=false;
		}
		echo "<br>\n";
	}
?>
	&nbsp;<input type='submit' name='print' value='erzeugen'></form>
</form>
</body>
</html>
