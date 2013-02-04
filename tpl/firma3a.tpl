<html>
	<head><title></title>
        {STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
	<link type="text/css" REL="stylesheet" HREF="{ERPCSS}/tabcontent.css"></link> 
        {JAVASCRIPTS}
	<script language="JavaScript">
	<!--
	function showP (id,nr) {
		if (id!='') {
			Frame=eval("parent.main_window");
			f1=open("rechng.php?id="+id+"&nr="+nr,"rechng","width=700,height=420,left=10,top=10,scrollbars=yes");
		}
	}
	//-->
	</script>
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:detailview:. {FAART}</p>
<div style="position:absolute; top:5.4em; left:0.2em;  width:42em;">
	<ul id="maintab" class="shadetabs">
	<li><a href="{Link1}">.:Custombase:.</a><li>
	<li><a href="{Link2}">.:Contacts:.</a></li>
	<li class="selected"><a href="{Link3}" id="aktuell">.:Sales:.</a></li>
	<li><a href="{Link4}">.:Documents:.</a></li>
	</ul>
</div>

<span style="position:absolute; left:0.2em; top:7.2em; width:99%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<div style="position:absolute; left:0px; top:0em; width:35em; border:1px solid black">
	<span class="fett">{Name} &nbsp; {kdnr}</span><br />
	{Plz} {Ort}
</div>
<span style="position:absolute; left:38em; top:0.7em;">[<a href="opportunity.php?Q={Q}&fid={FID}">.:Opportunitys:.</a>]</span>
<div style="position:absolute; left:1em; top:5em; width:45em;text-align:center;" class="normal">
.:SalesOrder:. .:Month:. {Monat}
	<table width="100%">
		<tr>
			<th class="klein" style="width:6em">.:date:.</th>
			<th class="klein">.:number:.</th>
			<th class="klein">.:netto:.</th>
			<th class="klein">.:brutto:.</th>
			<th class="klein" width="4em"></th>
			<th class="klein">.:art:.</th>
			<th class="klein">.:OP:.</th>
		</tr>
<!-- BEGIN Liste -->
		<tr class="klein bgcol{LineCol}" onClick="showP('{Typ}{RNid}','{RNr}');">
			<td class="">{Datum}</td>
			<td class=" ce">&nbsp;{RNr}&nbsp;</td>
			<td class=" re">{RSumme}&nbsp;&nbsp;</td>
			<td class=" re">{RBrutto}&nbsp;</td>
			<td class="">{Curr}</td>
			<td class=" ce">&nbsp;{Typ}</td>
			<td class=" ce">&nbsp;{offen}</td>
		</tr>
<!-- END Liste -->
		<tr><td class="klein" colspan="6"><b>R</b>).:invoice:., <b>A</b>).:quotation:., <b>L</b>).:orders:.</td></tr>
		<tr><td class="klein" colspan="6"><b>o</b>).:open:., <b>c</b>).:closed:., <b>+</b>).:paid:., <b>-</b>).:not_paid:.</td></tr>
	</table>
</div>	
<!-- Hier endet die Karte ------------------------------------------->
</span>
{END_CONTENT}
</body>
</html>
