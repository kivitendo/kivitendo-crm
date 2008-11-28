<?php
// $Id: konzern.php hlindemann $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/FirmenLib.php");
	require("firmacommon.php");

	$fid=($_GET["fid"])?$_GET["fid"]:$_POST["fid"];
	$Q=($_GET["Q"])?$_GET["Q"]:$_POST["Q"];	

	function getToechter($id,$Q,$ebene) {
	global $fid;
		$konzerne = getKonzerne($id,$Q,"T");
		if ($konzerne) foreach ($konzerne as $konz) {
			echo '<li class="ptr" onClick="show('.$konz["id"].')">';
			if ($konz["id"]==$fid) echo '=> ';
			echo $konz["name"].' ('.$konz["number"].') ';
			echo $konz["country"].''.$konz["zipcode"].' '.$konz["city"].'</li><br>'."\n";	
			$t=getKonzerne($konz["id"],$Q,"T");
			if (count($t)>0) { 
				echo "<ul>"; 
				getToechter($konz["id"],$Q,$ebene."-"); 
				echo "</ul>"; 
			};
		}
		
	}

	//Oberste Ebene ermitteln
	$konzern = getKonzerne($fid,$Q,"M");
	while ($konzern[0]["konzern"]) {
		$konzern = getKonzerne($konzern[0]["konzern"],$Q,"M");
	}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
        <head><title>Firma Stamm</title>
        <link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
        <link type="text/css" REL="stylesheet" HREF="css/tabcontent.css"></link>
	<script language="JavaScript">
		function show(id) {
			Frame=eval("parent.main_window");
			uri="firma1.php?Q=<?= $Q ?>&id="+id;
			Frame.location.href=uri;
		}
	</script>
	</head>
<body>
<h2>Konzernansicht</h2>
<?php
	echo '<span class="ptr" onClick="show('.$konzern[0]["id"].');">'.$konzern[0]["name"].' ('.$konzern[0]["number"].') '.$konzern[0]["country"].''.$konzern[0]["zipcode"].' '.$konzern[0]["city"].''."</span><br>\n";
	//Ab hier töchter holen
	echo "<ul>";
	getToechter($konzern[0]["id"],$Q,"-");
?>
