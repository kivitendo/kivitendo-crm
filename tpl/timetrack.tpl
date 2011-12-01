<!-- $Id$ -->
<html>
	<head><title></title>
    <link type="text/css" REL="stylesheet" HREF="../css/{ERPCSS}"></link>
    <link type="text/css" REL="stylesheet" HREF="css/{ERPCSS}"></link>
    {AJAXJS}
	<script language="JavaScript">
	<!--
	function suchFa() {
		val=document.formular.name.value;
		f1=open("suchFa.php?op=1&name="+val,"suche","width=350,height=200,left=100,top=100");
	}
    function editrow(id) {
        xajax_editTevent(id);
    }
    function getEventListe() {
	id = document.formular.id.value
        xajax_listTevents(id);
    }
	//-->
	</script>
	{jcal0}
    <script type='text/javascript' src='inc/help.js'></script>

<body {chkevent}>
<p class="listtop" onClick="help('TimeTrack');">.:timetracker:. (?)</p>
<span style="position:absolute; left:1em; top:1.4em; width:95%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="formular" action="timetrack.php" method="post">
<input type="hidden" name="id" value="{id}">
<input type="hidden" name="tab" value="{tab}">
<input type="hidden" name="fid" value="{fid}">
<input type="hidden" name="backlink" value="{backlink}">
<span style='visibility:{visible}'>
<br />
<select name="tid">
<!-- BEGIN Liste -->
<option value={tid}>{ttn}</option>
<!-- END Liste -->
</select><input type="submit" name="getone" value="ok">
</span>
<font color="red"><b>{msg}</b></font>
<!--div style="position:absolute; left:1px; width:65em; top:3em; border: 1px solid black; text-align:center;" -->
<!--div style="position:absolute;  left:1px;  top:3.3em; border: 0px solid black; text-align:left;" -->
	<div class="zeile">
		<span class="label klein">.:company:.</span>
			<input type="text" size="60" name="name" value="{name}" > 
		    <a href="javascript:suchFa();"><img src="image/suchen_kl.png" border="0" title=".:searchcompany:." ></a>
	</div>
	<div class="zeile">
		<span class="label klein">.:project:.</span>
		<input type="text" size="60" name="ttname" value="{ttname}" > 
	</div>
	<div class="zeile">
		<span class="label klein">.:description:.</span>
		<textarea cols="60" rows="5" name="ttdescription">{ttdescription}</textarea> 
	</div>
	<div class="zeile">
		<span class="label klein">.:startdate:.</span>
		<input type="text" size="10" name="startdate" id="START" value="{startdate}" >{jcal1} 
	</div>
	<div class="zeile">
		<span class="label klein">.:stopdate:.</span>
		<input type="text" size="10" name="stopdate" id="STOP" value="{stopdate}" >{jcal2} 
	</div>
	<div class="zeile">
		<span class="label klein">.:aim:.</span>
		<input type="text" size="5" name="aim" value="{aim}" >.:hours:.
	</div>
	<div class="zeile">
		<span class="label klein">.:active:.</span>
		<input type="radio" value="t" name="active" {activet}>.:yes:.
		<input type="radio" value="f" name="active" {activef}>.:no:.
	</div>
	<div class="zeile">
		<span class="label"></span>
        <span style="visibility:{noown}">
		<input type="hidden" name="action" value="">
		<img src="image/save_kl.png"   alt='.:save:.'   title='.:save:.'   name="save"   value=".:save:."   onclick="document.formular.action.value='save'; document.formular.submit();"> &nbsp;
		<img src="image/cancel_kl.png" alt='.:delete:.' title='.:delete:.' name="delete" value=".:delete:." onclick="document.formular.action.value='delete'; document.formular.submit();"style="visibility:{delete};"> &nbsp;
        </span>
        <span style="visibility:{blshow}">
		<a href={backlink}><image src="image/firma.png" alt='.:back:.' title='.:back:.' border="0" ></a>&nbsp;
        </span>
        <span>
		<img src="image/neu.png"    alt='.:new:.'    title='.:new:.'    name="clear"  value=".:new:."    onclick="document.formular.action.value='clear'; document.formular.submit();"> &nbsp;
		<img src="image/suchen.png" alt='.:search:.' title='.:search:.' name="search" value=".:search:." onclick="document.formular.action.value='search'; document.formular.submit();"> &nbsp;
        </span>
        <span id="summtime"></span>
	</div>

<!--/div-->
</form>
<br />
<div>
<form name="ttevent" method="post" action="timetrack.php">
<input type="hidden" name="cleared" value="{cleared}">
<input type="hidden" name="tid" value="{id}">
<input type="hidden" name="eventid" value="" id="eventid">
<span style="visibility:{noevent}"><table>
<tr><td>.:start work:.</td><td>.:stop work:.</td></tr>
<tr><td><input type="text" size="8" name="startd" id="startd">{jcal3} <input type="text" size="4" name="startt" id="startt"><input type="checkbox" name="start" value="1">.:now:.</td>
    <td><input type="text" size="8" name="stopd"  id="stopd">{jcal4}  <input type="text" size="4" name="stopt"  id="stopt"> <input type="checkbox" name="stop"  value="1">.:now:.</td><td></td></tr>
<tr><td colspan="2"><textarea cols="60" rows="3" name="ttevent" id="ttevent"></textarea></td>
    <td><input type="reset" name="resett" value=".:reset:."><br />
        <input type="submit" name="savett" value=".:save:.">
	</td></tr>
</table></span>
</form>
</div>
<div id="eventliste">
</div>
</table>
<!-- Hier endet die Karte ------------------------------------------->
</span>
{jcal5}
</body>
</html>
