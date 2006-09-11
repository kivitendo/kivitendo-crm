<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<link type="text/css" REL="stylesheet" HREF="css/tabcontent.css"></link>
	<script language="JavaScript">
	<!--
	function showK (id) {
		Frame=eval("parent.main_window");
		uri="liefer2.php?id=" + id;
		Frame.location.href=uri;
	}
	//-->
	</script>
<body>
<p class="listtop">Lieferantenkontakte</p>
<div style="position:absolute; top:44px; left:10px;  width:770px;">
	<ul id="maintab" class="shadetabs">
	<li><a href="{Link1}">Lieferantendaten</a><li>
	<li class="selected"><a href="{Link2}" id="aktuell">Ansprechpartner</a></li>
	<li><a href="{Link3}">Ums&auml;tze</a></li>
	<li><a href="{Link4}">Dokumente</a></li>
	<span title="Wichtige MItteilung">{Cmsg}</span>
	</ul>
</div>

<span style="position:absolute; left:10px; top:67px; width:95%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<div style="position:absolute; left:1px; width:450px; height:40px; text-align:left; border: 1px solid black;" class="fett">
		{Lname} &nbsp; &nbsp; {LInr}<br />
		{Plz} {Ort}<br />
</div>
<div style="position:absolute; left:1px; top:50px; width:100%; text-align:left; border: 0px solid black;" class="normal">
Kontakt [<a href="personen3.php?fid={FID}&Quelle=L" class="bold">neu eingeben</a>] - [<a href="personen1.php?fid={FID}&Quelle=L" class="bold">aus Bestand zuf&uuml;gen</a>] - oder ausw&auml;hlen:
<br />
<table class="liste">
<!-- BEGIN Liste -->
	<tr onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="showK({KID});" colspan="0">
		<td class="norm"> {Nname}, {Vname}</td><td class="norm">{Anrede} {Titel}</td><td class="norm">{Tel}</td><td class="norm">{eMail}</td></tr>
<!-- END Liste -->
</table>
</div>
<!-- Hier endet die Karte ------------------------------------------->
</span>
</body>
</html>
