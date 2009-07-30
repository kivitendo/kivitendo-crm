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
<p class="listtop">.:detailview:. {FAART}</p>
<div style="position:absolute; top:2.7em; left:1.2em;  width:42em;">
	<ul id="maintab" class="shadetabs">
	<li><a href="{Link1}">.:Custombase:.</a><li>
	<li><a href="{Link2}">.:Contacts:.</a></li>
	<li class="selected"><a href="{Link3}" id="aktuell">.:Sales:.</a></li>
	<li><a href="{Link4}">.:Documents:.</a></li>
	</ul>
</div>

<span style="position:absolute; left:1em; top:4.3em; width:99%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<div style="position:absolute; left:0px; top:0.0em; width:35em; border:1px solid black">
	<span class="fett">{Name} &nbsp; {kdnr}</span><br />
	{Plz} {Ort}
</div>
<span style="position:absolute; left:38em; top:0.7em;">[<a href="opportunity.php?Q={Q}&fid={FID}">.:Opportunitys:.</a>]</span>
<div style="position:absolute; left:1em; top:5em; width:99%;text-align:center;" class="normal">
	<div style="float:left; width:23em; text-align:left; " >
		<table style="width:100%;">
			<tr>
				<th class="klein" width="10%">.:Month:.</th>
				<th class="klein"></th><th class="klein">.:Sales:.</th>
				<th class="klein">.:Quotation:.</td><td width="10%"></td>
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
	<center>.:Netto sales over 12 Month:. 
	[<a href='firma3.php?Q={Q}&fid={FID}&jahr={JAHRZ}'>.:earlier:.</a>] [<a href='firma3.php?Q={Q}&fid={FID}&jahr={JAHRV}'>{JAHRVTXT}</a>]</center>
		<img src="{IMG}" width="500" height="280" title="Netto sales over 12 Month"><br /><br />
	</div>
</div>
<!-- Hier endet die Karte ------------------------------------------->
</span>
</body>
</html>
