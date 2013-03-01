<html>
	<head><title>LX - CRM</title>
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{THEME}
{JQDATE}
{JQTABLE}
	<script language="JavaScript">
	<!--
		function artikel() {
			f1=open("artikel.php","artikel","width=580,height=480,left=250,top=100,scroppbars=yes");
		}
		function showCall(id) {
			if (id) {
				uri="getCall.php?hole=" + id + "&INIT={INIT}&Q={Q}";
				location.href=uri;
			}
		}
		function historyCall() {
			id=document.call.id.value;
			f1=open("callHistory.php?id="+id,"history","width=580,height=480,left=250,top=100,scroppbars=yes");
		}
		function histDelCall() {
			f1=open("callHistory.php?id={Bezug}&del=1","history","width=580,height=480,left=250,top=100,scroppbars=yes");
		}
        function showwv() {
            if (document.call.wvl.checked) {
                document.getElementById('wv').style.visibility='visible';
            } else {
                document.getElementById('wv').style.visibility='hidden';
            }
        }
        $(function() {
            $( "#wvldate" ).datepicker($.datepicker.regional[ "de" ]);
            $( "#Datum" ).datepicker($.datepicker.regional[ "de" ]);
        })
	//-->
	</script>
	<script>
    $(document).ready( function(){
        $('#calls')
        .tablesorter({widthFixed: true, widgets: ['zebra'], headers: { 0: { sorter: false }, 1: { sorter: false }, 2: { sorter: false } } })
        .tablesorterPager({container: $("#pager"), size: 10, positionFixed: false})
    });
	</script>

<body onLoad="focus(), showwv()">

<!-- Hier beginnt die Karte  ------------------------------------------->
<form name="call" action="getCall.php" enctype='multipart/form-data' method="post">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="2000000">
<table width="100%">
			<tr><td rowspan="3">
				<select name="CRMUSER">
<!-- BEGIN Selectbox -->
					<option value="{UID}"{Sel}>{Login}</option>
<!-- END Selectbox -->
				</select>
				<select name="CID">
<!-- BEGIN Selectbox2 -->
					<option value="{CID}"{Sel}>{CName}</option>
<!-- END Selectbox2 -->
				</select>
				<select name="TID">
<!-- BEGIN Selectbox3 -->
					<option value="{TID}"{Sel}>{TID}</option>
<!-- END Selectbox3 -->
				</select>
				<input type="submit" name="verschiebe" value="verschieben">
			</td></tr>
	<tr><td class="klein fett">{Firma}</td></tr>
	<tr><td class="klein fett">{Plz} {Ort}</td></tr>
</table>
<hr width="100%">
<input type="hidden" name="Bezug" value="{Bezug}">
<input type="hidden" name="bezug" value="{bezug}">
<input type="hidden" name="fid" value="{FID}">
<input type="hidden" name="pid" value="{PID}">
<input type="hidden" name="id" value="{ID}">
<input type="hidden" name="wvlid" value="{WVLID}">
<input type="hidden" name="nummer" value="{nummer}">
<input type="hidden" name="Q" value="{Q}">
<input type="hidden" name="datei" value="{DateiID}">
<input type="text" name="cause" value="{NBetreff}" size="43" maxlength="125"> &nbsp; 
<input type="text" name="Datum" value="{NDatum}" id="Datum" size="9" maxlength="10"> 
<input type="text" name="Zeit" value="{NZeit}" size="6" maxlength="5">&nbsp; 
WV<input type="checkbox" name="wvl" value="1" onClick="showwv()" {wvl}> <span id="wv" style="visibility:hidden"><input type="text" size="8" name="wvldate" value="{wvldate}" id="wvldate" maxlength="10"></span>
<!--input type="reset" value="reset" onClick="javascript:location.href='getCall.php?fid={FID}&id={ID}'"--><br>
<span class="klein">Betreff</span><br>
<textarea name="c_cause" cols="80" rows="10" wrap="physical" >{LangTxt}</textarea><br>
<span class="klein">Bemerkung &nbsp; &nbsp; &nbsp;<!--a href="#" onClick="artikel()">Artikelliste</a--></span><br>
<table>
<tr><td><input type="file" name="Datei[]" value="{Datei}" size="35" maxlength="125"><br>
	     <span class="klein">Datei/Dokument<b> {ODatei}</b>{DateiID}</span></td>
	<td rowspan="2">
<!-- BEGIN Files -->
	{Anhang} 
<!-- END Files -->
	</td></tr>
<tr><td><input type="text" name="DCaption" value="{DCaption}" size="46" maxlength="125"><br>
<span class="klein">Datei Beschreibung</span></td></tr>
<tr><td colspan="2">
<span class="klein">Kontaktart: 
<input type="radio" name="Kontakt" value="T" {R1}>Telefon	&nbsp;
<input type="radio" name="Kontakt" value="M" {R2}>eMail &nbsp;
<input type="radio" name="Kontakt" value="S" {R3}>Fax/Brief &nbsp;
<input type="radio" name="Kontakt" value="P" {R4}>Pers&ouml;nlich
<input type="radio" name="Kontakt" value="D" {R5}>Datei &nbsp;
<input type="radio" name="Kontakt" value="X" {R6}>Termin &nbsp; 
</span></td></tr>
<tr><td colspan="2">
<span class="klein">Richtung: 
<input type="radio" name="inout" value="i" {INOUTi}>von Kunde	&nbsp;
<input type="radio" name="inout" value="o" {INOUTo}>an Kunde	&nbsp;
<input type="radio" name="inout" value="" {INOUT}>undefiniert	&nbsp;
</span></td></tr>
<tr><td style="text-align:right" colspan="2">
	<input type="button" name="history" value="H" style="visibility:{HDEL}" onClick="histDelCall();"> 
	<input type="button" name="history" value="history" style="visibility:{HISTORY}" onClick="historyCall();"> 
	<input type="submit" name="delete" value="delete" style="visibility:{DELETE}"> 
	<input type="submit" name="update" value="sichern" style="visibility:{EDIT}"> 
	<input type="submit" name="reset" value="reset"> 
	<input type="submit" name="sichern" value="sichern neu">
</td></tr>
</form>
</table>
<table id='calls' class="tablesorter" width="100%">
<thead><tr><th></th><th></th><th></th></tr></thead><tbody>
<!-- BEGIN Liste -->
	<tr  class='bgcol{LineCol}' onClick="showCall({IID});">
		<td width="118px">{Datum}</td><td>{Betreff}</td><td class='typcol{Type}'>{Kontakt}{inout}</td></tr>
<!-- END Liste -->
</tbody></table>
<div id="pager" class="pager" style='position:absolute;'>
<img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/first.png" class="first"/>
<img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/prev.png" class="prev"/>
<button id='reload' name='reload' onClick="document.location.reload(true)">reload</button>
<img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/next.png" class="next"/>
<img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/last.png" class="last"/>
<select class="pagesize" id='pagesize'>
<option value="10" selected>10</option>
<option value="15">15</option>
<option value="20">20</option>
<option value="25">25</option>
<option value="30">30</option>
</select>
</div>

<!-- Hier endet die Karte ------------------------------------------->
</body>
</html>
