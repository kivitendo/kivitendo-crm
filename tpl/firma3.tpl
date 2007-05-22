<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<link type="text/css" REL="stylesheet" HREF="css/tabcontent.css"></link>
	<script language="JavaScript">
	<!--
	function showM (month) {
		Frame=eval("parent.main_window");
		uri="firma3.php?Q={Q}&jahr={JAHR}&monat=" + month + "&fid=" + {FID};
		Frame.location.href=uri;
	}
	//-->
	</script>
<body>
<p class="listtop">Detailansicht {FAART}</p>
<div style="position:absolute; top:2.7em; left:1.2em;  width:42em;">
	<ul id="maintab" class="shadetabs">
	<li><a href="{Link1}">Kundendaten</a><li>
	<li><a href="{Link2}">Ansprechpartner</a></li>
	<li class="selected"><a href="{Link3}" id="aktuell">Ums&auml;tze</a></li>
	<li><a href="{Link4}">Dokumente</a></li>
	</ul>
</div>

<span style="position:absolute; left:1em; top:4.1em; width:99%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<div style="position:absolute; left:0px; top:0.0em; width:35em; border:1px solid black">
	<span class="fett">{Name} &nbsp; {kdnr}</span><br />
	{Plz} {Ort}
</div>
<span style="position:absolute; left:38em; top:0.7em;">[<a href="opportunity.php?fid={FID}">Auftragschancen</a>]</span>
<div style="position:absolute; left:1em; top:5em; width:99%;text-align:center;" class="normal">
	<div style="float:left; width:23em; text-align:left; " >
		<table style="width:100%;">
			<tr>
				<th class="klein" width="10%">Monat</th>
				<th class="klein"></th><th class="klein">Umsatz</th>
				<th class="klein">Angebot</td><td width="10%"></td>
			</tr>
<!-- BEGIN Liste -->
			<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showM('{Month}');">
				<td class="klein">{Month}</td>
				<td class="klein">{Rcount}</td><td class="klein re">{RSumme}</td>
				<td class="klein re">{ASumme}</td><td class="klein">&nbsp;{Curr}</td>
			</tr>
<!-- END Liste -->
		</table>
	</div>
	<div style="float:left; text-align:right; width:520px;" class="fett">
	<center>Nettoums&auml;tze &uuml;ber 12 Monate 
	[<a href='firma3.php?fid={FID}&jahr={JAHRZ}'>Fr&uuml;her</a>] [<a href='firma3.php?fid={FID}&jahr={JAHRV}'>{JAHRVTXT}</a>]</center>
		<img src="{IMG}" width="500" height="280" title="Umsatzdaten der letzten 12 Monate"><br /><br />
	</div>
</div>
<!-- Hier endet die Karte ------------------------------------------->
</span>
</body>
</html>
