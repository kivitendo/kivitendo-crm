<html>
    <head><title></title>
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{THEME}
{JQTABLE}
{JQDATE}
{JAVASCRIPTS}    
    <script language="JavaScript">
    <!--
        function showW (id,art) {
            if (art=="D") {
                uri="wvl1.php?show=" + id;
            } else if (art=="T") {
                uri="termin.php";
            } else if (art=="F") {
                uri="wvl1.php?erp=" + id;
            } else {
                uri="wvl1.php?mail=" + id;
            }
            location.href=uri;
        }

        function doInit() {
            $.ajax({
                url: 'jqhelp/wvll.php?task=wvl',
                dataType: 'json',
                success: function(data){
                    $('#wvliste tr[group="tc"]').remove();
                    var content;
                    var mailcnt = 0;
                    var noread = 0;
                    $.each(data, function(i) {
                        if ( data[i].Type == 'M' ) {
                            mailcnt++;
                            if ( data[i].Status == '+' ) noread++;
                        }
                        content  = '<tr group="tc" onClick="showW('+data[i].ID+',\''+data[i].Art+'\');">';
                        content += '<td>' + data[i].Initdate + '</td>';
                        content += '<td>' + data[i].Status + ' ' + data[i].Type;
                        if ( data[i].End == 3 ) content += ' <b>!!</b>';
                        content += '</td>';
                        content += '<td>' + data[i].cause + '</td>';
                        content += '<td>' + data[i].IniUser + '</td>';
                        content += '</tr>';
                        $('#wvliste tr:last').after(content);
                    })
                    $("#wvliste").trigger('update');
                    $("#wvliste").tablesorter({widgets: ['zebra']});
                    $("#wvliste").tablesorterPager({container: $("#pager"), size: 15, positionFixed: false});
                    $("#mails").html(mailcnt);
                    $("#unread").html(noread);
                }
            });
            var to = setTimeout(function(){ doInit(); },{timeout});
        }
        function suchDst() {
            val=document.formular.name.value;
            f1=open("suchFa.php?pers=1&name="+val,"suche","width=350,height=200,left=100,top=100");
        }
    //-->
    </script>
    <script>
        $(function() {
            $( "#Finish" ).datepicker($.datepicker.regional[ "de" ]);
        });
        </script>
<body onLoad="doInit();" >
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">Wiedervorlage</p>
<div style="float:left; width:45em; height:40em; text-align:center; border: 1px solid lightgray;" >
<form name="formular" action="wvl1.php" enctype='multipart/form-data' method="post">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="2000000">
<input type="hidden" name="CID" value="{CID}">
<input type="hidden" name="WVLID" value="{WVLID}">
<input type="hidden" name="mail" value="{mail}">
<input type="hidden" name="muid" value="{muid}">
<input type="hidden" name="noteid" value="{noteid}">
<input type="hidden" name="DateiID" value="{Datei}">
<input type="hidden" name="cp_cv_id" value="{cpcvid}">
<input type="hidden" name="cp_cv_id_old" value="{cpcvid}">
<input type="hidden" name="DCaption" value="{DCaption}">
<table border="0px" width="100%">
<tr style="display:run-in;">
    <td class="klein" width='30%'>
        <select name="CRMUSER" tabindex="1">
<!-- BEGIN Selectbox -->
            <option value="{UID}"{Sel}>{Login}</option>
<!-- END Selectbox -->
        </select>
        <br><span class="klein" style='width:35em'>CRM-User</span>
    </td><td width='*'>
        <span>
            <input type="text" name="name" size="25" maxlength="75"  value="{Fname}" tabindex="2"> <input type="button" name="dst" value=" ? " onClick="suchDst();" tabindex="99"> 
        </span>
        <br>
        <span class="klein" >Zugewiesen an &nbsp;[<a href="{stammlink}" name="addresse">Adresse</a>]</span>
    </td>
</tr><tr style="display:{hidenomail}; width:100%">
    <td class="klein" width='30%' >
        <input type="text" name="Finish" id="Finish" size="11" maxlength="10" value="{Finish}" tabindex="3">
        <br><span class="klein">Zu Erledigen bis</span>
    </td><td class="klein" width='*'>
        <input type="radio" name="status" value="1" tabindex="5" {Status1}>1&nbsp;
        <span style="visibility:{hide};">
        <input type="radio" name="status" value="2" tabindex="6" {Status2}>2&nbsp;
        <input type="radio" name="status" value="3" tabindex="7" {Status3}>3&nbsp;
        </span>
        <input type="radio" name="status" value="0" tabindex="8" >Erledigt
        <br><span class="klein">Priorit&auml;t</span>
    </td>
</tr><tr>
    <td class="klein" colspan="2">
        <input type="text" name="cause" size="60" maxlength="60" value="{cause}" tabindex="4">
        <br><span class="klein">Betreff</span>
    </td>
</tr><tr>
    <td class="klein" colspan="2">
        <textarea name="c_long" cols="65" rows="11" tabindex="9">{c_long}</textarea>
        <br><span class="klein">Beschreibung</span>
    </td>
</tr><tr>
    <td class="klein" style="visibility:{hide};" colspan="2">
        <input type="file" name="Datei[]" maxlength="2000000" size="14"  tabindex="10">
        <br><span class="klein">Dokument</span> <a href="{DLink}" target="_blank"><b><font color="black">{DName}</font></b></a>
    </td>
</tr><tr>
    <td class="klein"  style="visibility:{hide};" colspan="2">
        <input type="text" name="DCaption" size="60" maxlength="75" value="{DCaption}" tabindex="11">
        <br><span class="klein">Dokumentbeschreibung</span>
    </td>
    </td>
</tr><tr style="display:{hidenomail}; width:100%">
    <td class="klein" colspan="2">
        <span style="visibility:{hide};">
        <input type="radio" name="kontakt" value="T" {R1} tabindex="12">Telefon    &nbsp;
        <input type="radio" name="kontakt" value="M" {R2} tabindex="13">eMail &nbsp;
        <input type="radio" name="kontakt" value="S" {R3} tabindex="14">Fax/Brief &nbsp;
        <input type="radio" name="kontakt" value="P" {R4} tabindex="15">Pers&ouml;nlich&nbsp;
        <input type="radio" name="kontakt" value="D" {R5} tabindex="16">Datei&nbsp;
        </span>
        <input type="radio" name="kontakt" value="F" {R6} tabindex="16">ERP&nbsp;
        <br>
        <span class="klein">Kontaktart</span> 
    </td>
</tr><tr style="display:{hidemail};">
    <td colspan="2">
    <!-- BEGIN Filebox -->
        {file}<br>
    <!-- END Filebox -->
    </td>
</tr><tr width="100%">
	<td colspan="2">
        <input type="submit" value="reset" tabindex="18"> &nbsp; <input type="submit" name="save" value="sichern" tabindex="17"> &nbsp; 
        <input type="submit" name="delete" value="l&ouml;schen" tabindex="17" style="display:{hidemail};">
    </td>
</tr><tr style="display:{hidemail}; width:100%;">
    <td colspan="2">
        <input type='checkbox' name='flagged'  value='1' {flagged1} > Flagged
        <input type='checkbox' name='answered' value='1' {answered1}> Answered
        <input type='checkbox' name='deleted'  value='1' {deleted1} > Deleted
        <input type='checkbox' name='seen'     value='1' {seen1}    > Seen   
        <input type='checkbox' name='draft'    value='1' {draft1}   > Draft  
        <input type='checkbox' name='recend'   value='1' {recend1}  > Recend 
    </td>
</tr><tr>
    <td colspan="2">
        <b>{Msg}</b>
    </td>
</tr>
</table>
</form>
</div>
<div style="float:left; width:45%; height:40em; text-align:left; border: 1px solid lightgrey; border-left:0px;">
    <table id='wvliste' class='tablesorter' width='100%'>
        <thead>
            <tr><th width='20%' nowrap>Datum</th><th width='10%'>Status</th><th width="50%">Betreff</th><th width='20%'>User</th></tr>
        </thead>
        <tbody>
            <tr group="x" style="display:none"><td></td><td></td><td></td><td></td></tr>
            <tr group="tc"><td></td><td></td><td>Kein Eintrag</td><td></td></tr>
        </tbody>
    </table>
      <div id="pager" class="pager" style='position:absolute;'>
        <img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/first.png" class="first"/>
        <img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/prev.png" class="prev"/>
        <img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/next.png" class="next"/>
        <img src="{CRMPATH}jquery-ui/plugin/Table/addons/pager/icons/last.png" class="last"/>
        <select class="pagesize" id='pagesize'>
        <option value="10">10</option>
        <option value="15" selected>15</option>
        <option value="20">20</option>
        <option value="25">25</option>
        <option value="30">30</option>
        </select>
    [<a href='javascript:doInit();'>reload</a>] Mails:<span id='mails'> </span> ungelesen: <span id='unread'></span>
    </div>
  
</div>
{END_CONTENT}
</body>
</html>

