<!-- $Id$ -->
<html>
	<head><title></title>
	{STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="css/{ERPCSS}"></link>
        {JAVASCRIPTS}
        {AJAXJS}
    <!--script type="text/javascript" src="../js/common.js"></script-->
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
        fid = document.formular.fid.value
        if ( fid < 0 ) fid=0;
        xajax_listTevents(id,fid);
    }
    function doit(was) {
        document.formular.action.value=was; 
        document.formular.submit();
    }
    function chktime(wo) {
        var timeval = document.getElementById(wo).value;
        if ( timevall == '' ) return;
        var ausdruck = /(\d+):(\d+)/;
        erg = ausdruck.exec(timeval)
        if ( erg == null ) {
            alert('Fehlerhafter Ausdruck ('+timeval+')');
            document.getElementById(wo).value = '';
            return;
        }
        if (erg[1]*1 < 0 || erg[1]*1 > 24) {
            alert('Fehlerhafter Ausdruck:' + erg[1]);
            document.getElementById(wo).value = '';
            return;
        }
        if (erg[2] < 0 || erg[2] > 59) {
            alert('Fehlerhafter Ausdruck:' + erg[2]);
            document.getElementById(wo).value = '';
            return;
        }
    }
    function check_right_date_format(fld) {
        var datum = fld.value;
        if ( datum == '' ) return;
        datum = datum.replace(/[-\\\/]/g,'.');
        if ( datum.match(/\d+\.\d+\.\d+/)) {
            fld.value = datum;
        } else {
            alert("Fehlerhaftes Datumsformat: " + datum);
        }
    }
    function psuche(){  
        var part = document.getElementById('partnr').value;
        f1 = open('suchPart.php',"suche","width=650,height=250,left=100,top=100");
    }
    function pdelete() {
        nr = document.getElementById('parts').selectedIndex;
        document.getElementById('parts').options[nr] = null
    }
    function saveTT() {
        var data = new Array();
        for ( i=0; i < document.getElementById('parts').length; i++) {
            data[data.length]=document.getElementById('parts').options[i].value;
        }
        document.getElementById('parray').value = data.join('###');
        return true;
    }
    function doReset() {
        document.getElementById("savett").style.visibility="visible";
        document.getElementById("pdel").style.visibility="visible";
        document.getElementById("psearch").style.visibility="visible";
        for ( i=document.getElementById('parts').length - 1; i>=0; i--) {
            document.getElementById('parts').options[i] = null;
        };
    }
	//-->
	</script>
	{jcal0}
    <script type='text/javascript' src='inc/help.js'></script>

<body {chkevent}>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop" onClick="help('TimeTrack');">.:timetracker:. (?)</p>
<span style="position:absolute; left:1em; top:4.4em; width:95%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="formular" action="timetrack.php" method="post">
<input type="hidden" name="clear" value="{clear}">
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
		<span class="label klein">.:name:.</span>
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
		<span class="label klein"></span>
		<span class="klein">.:startdate:.
		<input type="text" size="10" name="startdate" id="START" value="{startdate}" >{jcal1} </span> &nbsp; &nbsp;
		<span class="klein">.:stopdate:.
		<input type="text" size="10" name="stopdate" id="STOP" value="{stopdate}" >{jcal2} </span>
	</div>
	<div class="zeile">
		<span class="label klein"></span>
		<span class="klein">.:aim:.</span>
		<input type="text" size="5" name="aim" value="{aim}" >.:hours:. &nbsp; &nbsp;
		<span class="klein">.:active:.</span>
		<input type="radio" value="t" name="active" {activet}>.:yes:.
		<input type="radio" value="f" name="active" {activef}>.:no:.
	</div>
	<div class="zeile">
		<span class="label klein"></span>
		<span class="klein">.:budget:.</span>
		<input type="text" size="9" name="budget" value="{budget}" >{cur} &nbsp; &nbsp;
	</div>
	<div class="zeile">
		<span class="label"></span>
        <span style="visibility:{noown}">
		<input type="hidden" name="action" value="">
		<img src="image/save_kl.png"   alt='.:save:.'   title='.:save:.'   name="save"   value=".:save:."   onclick="doit('save');"> &nbsp;
		<img src="image/cancel_kl.png" alt='.:delete:.' title='.:delete:.' name="delete" value=".:delete:." onclick="doit('delete');" style="visibility:{delete};"> &nbsp;
        </span>
        <span style="visibility:{blshow}">
		<a href={backlink}><image src="image/firma.png" alt='.:back:.' title='.:back:.' border="0" ></a>&nbsp;
        </span>
        <span>
		<img src="image/neu.png"    alt='.:new:.'    title='.:new:.'    name="clear"  value=".:new:."    onclick="doit('clear');"> &nbsp;
		<img src="image/suchen.png" alt='.:search:.' title='.:search:.' name="search" value=".:search:." onclick="doit('search');"> &nbsp;
        </span>
        <span id="summtime"></span>
	</div>

<!--/div-->
</form>
<br />
<div>
<form name="ttevent" method="post" action="timetrack.php" onSubmit="return saveTT();">
<input type="hidden" name="tid" value="{id}">
<input type="hidden" id="parray" name="parray" value="">
<input type="hidden" name="cleared" id='cleared' value="">
<input type="hidden" name="eventid" id="eventid" value="" >
<span id="work" style="visibility:{noevent}"><table>
<tr><td>.:start work:.</td><td>.:stop work:.</td><td>.:material:.</td>
</tr>
<tr><td><input type="text" size="8" name="startd" id="startd" onBlur="check_right_date_format(this)">{jcal3} 
	<input type="text" size="4" name="startt" id="startt" onblur="chktime('startt');"><input type="checkbox" name="start" value="1">.:now:.</td>
    <td><input type="text" size="8" name="stopd"  id="stopd" onBlur="check_right_date_format(this)">{jcal4}  
	<input type="text" size="4" name="stopt"  id="stopt" onblur="chktime('stopt');"> <input type="checkbox" name="stop"  value="1">.:now:.</td>
    <td><input type="text" name="partnr" id="partnr" style='width:19em;'>
    <input type="button" name="psearch" id="psearch" value=".:psearch:." onClick="psuche();"></td>
</tr>
<tr><td colspan="2"><textarea cols="62" rows="5" name="ttevent" id="ttevent"></textarea></td>
    <td><select name="parts" id="parts" size="5" style='width:19em;'></select>
    <input type="button" id="pdel" name="pdel" value=".:del:." onClick="pdelete();"></td>
</tr>
<tr>
    <td><input type="reset"  name="resett" value=".:reset:." onClick='doReset();'><</td>
    <td><input type="submit" name="savett" value=".:save:." id='savett' ><!--style='visibility:visible'--></td>
    <td></td>
</tr>
</table></span>
</form>
</div>
<div id="eventliste">
</div>
</table>
<!-- Hier endet die Karte ------------------------------------------->
</span>
{jcal5}
{END_CONTENT}
</body>
</html>
