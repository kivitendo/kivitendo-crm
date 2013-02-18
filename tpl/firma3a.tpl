<html>
	<head><title></title>
        {STYLESHEETS}
    <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css">
    <link rel="stylesheet" type="text/css" href="{JQUERY}/jquery-ui/themes/base/jquery-ui.css">
    {THEME}
    <script type="text/javascript" src="{JQUERY}jquery-ui/jquery.js"></script>
    <script type="text/javascript" src="{JQUERY}jquery-ui/ui/jquery-ui.js"></script>
        {JAVASCRIPTS}
	<script language="JavaScript">
	<!--
	function showP (id,nr) {
		if (id!='') {
			Frame=eval("parent.main_window");
			f1=open("rechng.php?id="+id+"&nr="+nr,"rechng","width=700,height=420,left=10,top=10,scrollbars=yes");
		}
	}
    $(function(){
         $('button')
          .button()
          .click( function(event) { event.preventDefault();  document.location.href=this.getAttribute('name'); });
    });
	//-->
	</script>
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:detailview:. {FAART}</p>
<div id="menubox2">
    <button name="{Link1}">.:Custombase:.</button>
    <button name="{Link2}">.:Contacts:.</button>
    <button name="{Link3}">.:Sales:.</button>
    <button name="{Link4}">.:Documents:.</button>
</div>

<span style="position:absolute; left:0.2em; top:7.2em; width:99%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<div style="position:absolute; left:1.5px; top:0em; width:35em; border:1px solid black">
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
