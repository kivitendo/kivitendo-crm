<html>
	<head><title></title>
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{THEME}
{JQTABLE}
{JAVASCRIPTS}
	<script language="JavaScript">
	<!--
	function showP (id,nr) {
		if (id!='') {
			Frame=eval("parent.main_window");
            f1=open("rechng.php?Q={Q}&id="+id+"&nr="+nr,"rechng","width=700,height=420,left=10,top=10,scrollbars=yes");
		}
	}
    $(function(){
         $('button')
          .button()
          .click( function(event) { event.preventDefault();  document.location.href=this.getAttribute('name'); });
    });
	//-->
	</script>
	<script>
    $(document).ready(
        function(){
            $("#ums").tablesorter({widthFixed: true, widgets: ['zebra'], headers: { 
                0: { sorter: false }, 1: { sorter: false }, 2: { sorter: false }, 3: { sorter: false }, 4: { sorter: false } } 
            });
        })
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
<div style="position:absolute; left:1.5px; top:0em; width:35em; border:1px solid lightgray">
	<span class="fett">{Name} &nbsp; {kdnr}</span><br />
	{Plz} {Ort}
</div>
<span style="position:absolute; left:38em; top:0.7em;">[<a href="opportunity.php?Q={Q}&fid={FID}">.:Opportunitys:.</a>]</span>
<div style="position:absolute; left:1em; top:5em; width:45em;text-align:center;" class="normal">
.:SalesOrder:. .:Month:. {Monat}
	<table id="ums" class="tablesorter" width="100%">
		<thead><tr>
			<th style="width:6em">.:date:.</th>
			<th>.:number:.</th>
			<th>.:netto:.</th>
			<th>.:brutto:.</th>
			<th width="4em"></th>
			<th>.:art:.</th>
			<th>.:OP:.</th>
		</tr></thead><tbody style='cursor:pointer'>
<!-- BEGIN Liste -->
		<tr class="klein bgcol{LineCol}" onClick="showP('{Typ}{RNid}','{RNr}');">
			<td >{Datum}</td>
			<td >&nbsp;{RNr}&nbsp;</td>
			<td class='re'>{RSumme}&nbsp;&nbsp;</td>
			<td class='re'>{RBrutto}&nbsp;</td>
			<td >{Curr}</td>
			<td >&nbsp;{Typ}</td>
			<td >&nbsp;{offen}</td>
		</tr>
<!-- END Liste -->
		<tr><td colspan="7"><b>R</b>).:invoice:., <b>A</b>).:quotation:., <b>L</b>).:orders:.</td></tr>
		<tr><td colspan="7"><b>o</b>).:open:., <b>c</b>).:closed:., <b>+</b>).:paid:., <b>-</b>).:not_paid:.</td></tr>
	</tbody></table>
</div>	
<!-- Hier endet die Karte ------------------------------------------->
</span>
{END_CONTENT}
</body>
</html>
