<!-- $Id$ -->
<html>
    <head><title></title>
    <link type="text/css" REL="stylesheet" HREF="../css/{ERPCSS}"></link>
    <link type="text/css" REL="stylesheet" HREF="css/{ERPCSS}"></link>
	<script language="JavaScript">
	<!--
        function hide(nr) {
		document.getElementById(nr).style.display="none";
	}
        function show(nr) {
		document.getElementById(nr).style.display="inline";
	}
	function toggle(was1,was2) {
		document.getElementById(was1).style.display="none";
		document.getElementById(was2).style.display="block";
	}
	function sichern() {
		document.getElementById("ok").style.display="block";
	}
	function suchFa() {
		val=document.formular.name.value;
		f1=open("suchFa.php?op=1&name="+val,"suche","width=350,height=200,left=100,top=100");
	}
	function quotation(nr) {
		if (nr>0) {
			f=open("rechng.php?id=L{auftrag}","Auftrag","width=650,height=400,left=100,top=100");
		} 
	}
	//-->
	</script>
	{jcal0}
<body>
<p class="listtop">.:opportunity:.</p>
<span style="position:absolute; left:1em; top:3.3em; width:95%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="formular" action="opportunity.php" method="post">
<input type="hidden" name="id" value="{id}">
<input type="hidden" name="oppid" value="{oppid}">
<input type="hidden" name="tab" value="{tab}">
<input type="hidden" name="fid" value="{fid}">
<input type="hidden" name="firma" value="{firma}">
<span style="display:{stamm};">
	<a href="firma1.php?Q={tab}&id={fid}"><img src="image/addressbook.png" border="0" alt=".:masterdata:." title=".:masterdata:."></a>
	<a href="opportunity.php?Q={tab}&fid={fid}"><img src="image/listen.png" border="0" alt=".:opportunitys:." title=".:opportunitys:."></a>
	<a href="opportunity.php?Q={tab}&fid={fid}&new=1"><img src="image/new.png" border="0" alt=".:new:./.:search:." title=".:new:./.:search:."></a>
	<!--a href="opportunity.php?history={oppid}"><img src="image/history.png" border="0" alt=".:history:." title=".:history:."></a-->
	<img src="image/nummer.png" border="0" alt=".:quotation:." title=".:quotation:." onClick="quotation({auftrag});" style="visibility:{auftragshow};">
        .:changed:. {chgdate} .:by:. {user}
	<br /><br />
</span>				
<!--div style="position:absolute; left:1px; width:65em; top:3em; border: 1px solid black; text-align:center;" -->
<div style="position:absolute;  left:1px;  top:3.3em; border: 0px solid black; text-align:center;" >
	<div class="zeile">
		<span class="label klein" onClick='toggle("fa1","fa2");'>.:company:.</span>
		<span class="leftfeld pad value" style="width:50em; display:{block}" id="fa2" onClick='toggle("fa2","fa1");'>{firma}</span>
		<span class="leftfeld"     style="width:50em; display:{none};" id="fa1">
			<input type="text" size="60" name="name" value="{firma}" onChange="sichern()"> 
		<a href="javascript:suchFa();"><img src="image/suchen_kl.png" border="0" title=".:searchcompany:." ></a>
		</span>
	</div>
	<div class="zeile">
		<span class="label klein" onClick='toggle("ti1","ti2");'>.:subject:.</span>
		<span class="leftfeld pad value" style="width:50em; display:{block}" onClick='toggle("ti2","ti1");' id="ti2">{title}</span>
		<span class="leftfeld"     style="width:50em; display:{none};" id="ti1">
			<input type="text" size="65" name="title" value="{title}" onChange="sichern()">
		</span>
	</div>
	<div class="zeile">
		<span class="label klein" onClick='toggle("be1","be2");'>.:ordersum:.</span>
		<span class="leftfeld pad value" style="width:50em; display:{block}" onClick='toggle("be2","be1");' id="be2">{betrag} &euro;</span>
		<span class="leftfeld"     style="width:50em; display:{none};" id="be1">
			<input type="text" size="10" name="betrag" value="{betrag}" onChange="sichern()"> &euro;
		</span>
	</div>
	<div class="zeile">
		<span class="label klein" onClick='toggle("zi1","zi2");'>.:targetdate:.</span>
		<span class="leftfeld pad value" style="width:50em; display:{block}" onClick='toggle("zi2","zi1");' id="zi2">{zieldatum}</span>
		<span class="leftfeld"     style="width:50em; display:{none};" id="zi1">
			<input type="text" size="10" name="zieldatum" id="zieldatum" value="{zieldatum}" onChange="sichern()"> tt.mm.jjjj {jcal1}
		</span>
	</div>
	<div class="zeile">
		<span class="label klein">.:chance:.</span>
		<span class="leftfeld"><select name="chance" onChange="sichern()">
			<option value="" {csel}>---</option>
			<option value="1" {csel1}>10%</option>
			<option value="2" {csel3}>20%</option>
			<option value="3" {csel3}>30%</option>
			<option value="4" {csel4}>40%</option>
			<option value="5" {csel5}>50%</option>
			<option value="6" {csel6}>60%</option>
			<option value="7" {csel7}>70%</option>
			<option value="8" {csel8}>80%</option>
			<option value="9" {csel9}>90%</option>
			<option value="10" {csel10}>100%</option>
			</select>
		</span>
	</div>
	<div class="zeile">
		<span class="label klein">.:status:.</span>
		<span class="leftfeld"><select name="status" onChange="sichern()">
			<option value="" {ssel}>---</option>
<!-- BEGIN status -->
			<option value="{sval}" {ssel}>{sname}</option>
<!-- END status -->
			</select>
		</span>
	</div>
	<div class="zeile">
		<span class="label klein">.:salesman:.</span>
		<span class="leftfeld"><select name="salesman" onChange="sichern()">
			<option value="" {esel}>---</option>
<!-- BEGIN salesman -->
			<option value="{evals}" {esel}>{ename}</option>
<!-- END salesman -->
			</select>
		</span>
	</div>
	<div class="zeile">
		<span class="label klein">.:quotation:.</span>
		<span class="leftfeld"><select name="auftrag" onChange="sichern()">
			<option value="" {asel}>---</option>
<!-- BEGIN auftrag -->
			<option value="{aval}" {asel}>{aname}</option>
<!-- END auftrag -->
			</select>
		</span>
	</div>
	<div class="zeile">
		<span class="label klein" onClick='toggle("ne1","ne2");'>.:nextstep:.</span>
		<span class="leftfeld pad value" style="width:50em; display:{block}" onClick='toggle("ne2","ne1");' id="ne2">{next}</span>
		<span class="leftfeld"     style="width:50em; display:{none};" id="ne1">
			<input type="text" size="65" name="next" value="{next}" onChange="sichern()">
		</span>
	</div>
	<div class="zeile klein">
		<span class="label" onClick='toggle("no1","no2");'>.:notes:.</span>
		<span class="leftfeld pad value" style="width:50em; display:{block}" onClick='toggle("no2","no1");' id="no2">
			{notxt}
		</span>
		<span class="leftfeld" style="width:45em; display:{none};" id="no1">
			<textarea name="notiz" cols="80" rows="10" onChange="sichern()">{notiz}</textarea>
		</span>
	</div>
	<div class="zeile">
		<span class="label"></span>
		<span class="leftfeld" style="width:350px; display:{none};" id="ok">
                        <input type="hidden" name="action" value="">
			<img src="image/suchen_kl.png" alt='.:search:.' title='.:search:.' name="suchen" value=".:search:." style="visibility:{search};" onclick="document.formular.action.value='suchen'; document.formular.submit();"> &nbsp;
			<img src='image/save_kl.png' alt='.:save:.' title='.:save:.' name='save' value='.:new:.' style="visibility:{save};" onclick="document.formular.action.value='save'; document.formular.submit();"> &nbsp; 
			<a href={backlink}><input type='image' src='image/firma.png' alt='.:back:.' title='.:back:.' name='back' value='.:back:.' style="visibility:{blshow};"></a> &nbsp; 
			{msg}
		</span>
	</div>
</form>
        <table border="0" width="100%">
	<tr><td>.:subject:.</td><td>.:ordersum:.</td><td>.:targetdate:.</td><td>.:chance:.</td><td>.:status:.</td><td>.:quotation:.</td><td>.:nextstep:.</td><td>.:employee:.</td><td>.:changed:.</td></tr>
<!-- BEGIN Liste --> 
        <tr  onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="show('n{nr}');" colspan="0">
                <td class="norm"> {histtitle}</td>
                <td class="norm" style="width:7em;text-align:right"> {histbetrag}</td>
                <td class="norm" style="width:6em;text-align:right"> {histdatum}</td>
		<td class="norm" style="width:2em;text-align:right"> {histchance}</td>
		<td class="norm" style="width:10em;text-align:left"> {histstatus}</td>
		<td class="norm"> {histauftrag}</td>
		<td class="norm"> {histnext}</td>
		<td class="norm"> {user}</td>
		<td class="norm" style="width:6em;text-align:left">&nbsp;{chgdate}</td></tr>
        <tr  onMouseover="this.bgColor='#FF0000';" onMouseout="this.bgColor='{LineCol}';" bgcolor="{LineCol}" onClick="hide('n{nr}');" colspan="0">
                <td style="display:none" id='n{nr}'  class"norm" colspan="9">{histnotiz}</td></tr>
<!-- END Liste -->
       </table>
</div>
<!-- Hier endet die Karte ------------------------------------------->
</span>
{jcal2}
</body>
</html>
