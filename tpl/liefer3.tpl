<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
	function showM (month) {
		Frame=eval("parent.main_window");
		uri="liefer3.php?jahr={JAHR}&monat=" + month + "&fid=" + {FID};
		Frame.location.href=uri;
	}
	//-->
	</script>
<body>
<p class="listtop">Detailansicht</p>
<div style="position:absolute; top:33px; left:8px;  width:770px;">
	<ul id="tabmenue">
	<li><a href="{Link1}">Liferantendaten</a><li>
	<li><a href="{Link2}">Kontakte</a></li>
	<li><a href="{Link3}" id="aktuell">Ums&auml;tze</a></li>
	<li><a href="{Link4}">Dokumente</a></li>
	</ul>
</div>

<span style="position:absolute; left:10px; top:67px; width:99%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<div style="position:absolute; left:0px; top:0px; width:450px; border:1px solid black" class="fett">
	{Name} &nbsp; {LInr}<br />
	{Plz} {Ort}
</div>
<div style="position:absolute; left:1px; top:45px; width:99%;text-align:center;" class="normal">
	Nettoums&auml;tze &uuml;ber 12 Monate 
	[<a href='liefer3.php?fid={FID}&jahr={JAHRZ}'>Fr&uuml;her</a>] [<a href='liefer3.php?fid={FID}&jahr={JAHRV}'>{JAHRVTXT}</a>]
	<div style="float:left; width:210px; text-align:left; " >
		<table>
			<tr>
				<th class="smal" width="10%">Monat</th>
				<th class="smal"></th><th class="smal">Umsatz</th>
				<th class="smal">Angebot</td><td width="10%"></td>
			</tr>
<!-- BEGIN Liste -->
			<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showM('{Month}');">
				<td class="smal">{Month}</td>
				<td class="smal">{Rcount}</td><td class="smal re">{RSumme}</td>
				<td class="smal re">{ASumme}</td><td class="smal">{Curr}</td>
			</tr>
<!-- END Liste -->
		</table>
	</div>
	<div style="float:left; text-align:right; width:520px;" class="fett">
		<img src="{IMG}" width="500" height="280" title="Umsatzdaten der letzten 12 Monate"><br /><br />
	</div>
</div>
<!-- Hier endet die Karte ------------------------------------------->
</span>
</body>
</html>
