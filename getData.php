<?
// $Id$
	require_once("inc/stdLib.php");
	include("inc/crmLib.php");
?>
<html>
<head><title></title>
<link REL="stylesheet" HREF="css/main.css" TYPE="text/css" TITLE="Lx-System stylesheet">
</head>
<body onLoad="document.suche.swort.focus()";>
<?	
if ($_POST["adress"]) {
	include("inc/FirmenLib.php");
	include("inc/persLib.php");
	include("inc/UserLib.php");
	
	$msg="Leider nichts gefunden!";
	$viele="Zu viele Treffer. Bitte einschr&auml;nken.";
	$found=false;
	$suchwort=mkSuchwort($_POST["swort"]);
	$anzahl=0;
	$db->debug=0;

	$rsE=getAllUser($suchwort);
	if (chkAnzahl($rsE,$anzahl)) {
		$rsV=getAllFirmen($suchwort,true,"V");	
		if (chkAnzahl($rsV,$anzahl)) {
			$rsC=getAllFirmen($suchwort,true,"C");
			if (chkAnzahl($rsC,$anzahl)) {
				$rsK=getAllPerson($suchwort);
				if (!chkAnzahl($rsK,$anzahl)) {
					$msg=$viele;
				} else {
					if ($anzahl===0) $msg="Keine Treffer";
				} 
			} else {
				$msg=$viele;
			}
		} else {
			$msg=$viele;
		}
	} else {
			$msg=$viele;
	}
?>
<script language="JavaScript">
<!--
	function showD (src,id) {
		Frame=eval("parent.main_window");
		if      (src=="C") {	uri="firma1.php?Q=C&id=" + id }
		else if (src=="V") {	uri="firma1.php?Q=V&id=" + id; }
		else if (src=="E") {	uri="user1.php?id=" + id; }
		else if (src=="K") {	uri="kontakt.php?id=" + id; }
		Frame.location.href=uri;
	}
//-->
</script>
<p class="listtop">Suchergebnis</p>
<?
	if ($anzahl>0) {
		echo "<table class=\"liste\">\n";
		echo "<tr class='bgcol3'><th>KD-Nr</th><th class=\"liste\">Name</th><th class=\"liste\">Anschrift</th><th class=\"liste\">Telefon</th><th></th></tr>\n";
		$i=0;
		if ($rsC) foreach($rsC as $row) {
			echo "<tr onMouseover=\"this.bgColor='#FF0000';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showD(\"C\",".$row["id"].");'>".
				"<td class=\"liste\">".$row["customernumber"]."</td><td class=\"liste\">".$row["name"]."</td>".
				"<td class=\"liste\">".$row["city"].(($row["street"])?",":"").$row["street"]."</td><td class=\"liste\">".$row["phone"]."</td><td class=\"liste\">K</td></tr>\n";
			$i++;
		}
		if ($rsV) foreach($rsV as $row) {
			echo "<tr onMouseover=\"this.bgColor='#FF0000';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showD(\"V\",".$row["id"].");'>".
				"<td class=\"liste\">".$row["vendornumber"]."</td><td class=\"liste\">".$row["name"]."</td>".
				"<td class=\"liste\">".$row["city"].(($row["street"])?",":"").$row["street"]."</td><td class=\"liste\">".$row["phone"]."</td><td class=\"liste\">L</td></tr>\n";
			$i++;
		}
		if ($rsK) foreach($rsK as $row) {
			echo "<tr onMouseover=\"this.bgColor='#FF0000';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showD(\"K\",".$row["cp_id"].");'>".
				"<td class=\"liste\">".$row["cp_id"]."</td><td class=\"liste\">".$row["cp_name"].", ".$row["cp_givenname"]."</td>".
				"<td class=\"liste\">".$row["addr2"].(($row["addr1"])?",":"").$row["addr1"]."</td><td class=\"liste\">".$row["cp_phone1"]."</td><td class=\"liste\">P</td></tr>\n";
			$i++;
		}
		if ($rsE) foreach($rsE as $row) {
			echo "<tr onMouseover=\"this.bgColor='#FF0000';\" onMouseout=\"this.bgColor='".$bgcol[($i%2+1)]."';\" bgcolor='".$bgcol[($i%2+1)]."' onClick='showD(\"E\",".$row["id"].");'>".
				"<td class=\"liste\">".$row["id"]."</td><td class=\"liste\">".$row["name"]."</td>".
				"<td class=\"liste\">".$row["addr2"].(($row["addr1"])?",":"").$row["addr1"]."</td><td class=\"liste\">".$row["workphone"]."</td><td class=\"liste\">U</td></tr>\n";
			$i++;
		}
		echo "</table>\n";
        } else {
		echo $msg;
	};
	echo "<br>";
} else if ($_POST["kontakt"]){
?>
<script language="JavaScript">
	sw="<?= $_POST["swort"]; ?>";
	if (sw != "") 
		F1=open("suchKontakt.php?suchwort="+sw+"&Q=S","Suche","width=400, height=400, left=100, top=50, scrollbars=yes");
</script>			

<? }?>
<p class="listtop">Schnellsuche Kunde/Lieferant/Kontakte und Kontaktverlauf</p>
	<form name="suche" action="getData.php" method="post">
	<input type="text" name="swort" size="20"> suche 
	<input type="submit" name="adress" value="Adresse"> 
	<input type="submit" name="kontakt" value="Kontaktverlauf"> <br>
      	<span class="liste">Suchbegriff</span>
	</form>

