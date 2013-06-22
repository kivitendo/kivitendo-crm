<html>
    <head><title></title>
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{THEME}
{JQTABLE}
{JQDATE}
{JQFILEUP}
{JQWIDGET}
{JAVASCRIPTS}   
    <script language="JavaScript">
    <!--
        var WVLID = 0;
        var MailID = 0;
        var KontaktID = 0;
        var KontaktTab = '';
        var DName = '';
        var DateiID = 0;
        var noteid = 0;
        var newfile = 0;
        function kontakt(tab,id,name) {
            if ( tab == 'C' || tab == 'V' ) {
                KontaktID = id;
                $('#cp_cv_id').val(tab+id);
                KontaktTab = tab;
                $('#addresse').prop('href','firma1.php?Q='+tab+'&id='+id);
                $('#addresse').text(name);
            } else if ( tab == 'P' ) {
                $('#cp_cv_id').val(tab+id);
                KontaktID = id;
                KontaktTab = 'P';
                $('#addresse').prop('href','kontakt.php?id='+id);
                $('#addresse').text(name);
            } else {
                $('#addresse').prop('href','');
                $('#addresse').text('');
                KontaktID = 0;
                KontaktTab = '';
            };
        };
        function showWvl(id) {
            $.ajax({
                url: 'jqhelp/wvll.php?task=show&id='+id,
                dataType: 'json',
                success: function(data){
                    console.log(JSON.stringify(data));
                    $('.wvl').show();
                    $('.mail').hide();
                    $('.hideK').show();
                    $('.hideD').show();
                    $('.hideE').show();
                    WVLID = data.id;
                    kontakt(data.kontakttab,data.kontaktid,data.kontaktname);
                    $('#cause').val(data.cause);
                    $('#c_long').val(data.c_long);
                    $('#Finish').val(data.Finish);
                    $('#DCaption').val(data.DCaption);
                    $('#status'+data.status).prop('checked',true);
                    $('#Sradio').buttonset('refresh');
                    $('#kontakt'+data.kontakt).prop('checked',true);
                    $('#Kradio').buttonset('refresh');
                    $('#DLink').prop('href','dokumente/'+data.DPath+data.DName);
                    $('#DLink').text(data.DName);
                    DName = data.DName;
                    DateiID = data.DateiID;
                }
            });
        }
        function showERP(id) {
            $.ajax({
                url: 'jqhelp/wvll.php?task=erp&id='+id,
                dataType: 'json',
                success: function(data){
                    console.log(JSON.stringify(data));
                    $('.erp').hide();
                    WVLID = data.id;
                    noteid = data.noteid;
                    kontakt(data.kontakttab,data.kontaktid,data.kontaktname);
                    $('#cause').val(data.cause);
                    $('#c_long').val(data.c_long);
                    $('#Finish').val(data.Finish);
                    $('#status1').prop('checked',true);
                    $('#kontaktF').prop('checked',true);
                    $('.mail').hide();
                    $('.hideK').hide();
                    $('.hideD').hide();
                    $('.hideE').show();
                }
            });
        }
        function showMail(id) {
            console.log('Mail-ID:'+id);
            $.ajax({
                url: 'jqhelp/wvll.php?task=mail&id='+id,
                dataType: 'json',
                success: function(data){
                    console.log(JSON.stringify(data));
                    if ( data.rc == -9 ) {
                         Fehler(-9);
                    } else {
                        $('.mail').show();
                        $('.hideK').hide();
                        $('.hideD').hide();
                        $('.hideE').hide();
                        WVLID = data.id;
                        MailID = data.muid;
                        $('#cause').val(data.cause);
                        $('#c_long').val(data.c_long);
                        $('#kontaktE').prop('checked',true);
                        $('#status'+data.status).prop('checked',true);
                        $('#Sradio').buttonset('refresh');
                        $.each(data.flags, function(index, item) {
                            if ( item > 0 ) $('#'+index).prop('checked',true) 
                            else $('#'+index).prop('checked', false) 
                        });
                        files = '';
                        $.each(data.Anhang, function(index, item) {
                            files += "<input type='checkbox' class='dateien' name='dateien[]' value='"+item.name+","+item.size+","+item.type+"' checked> [<a href='tmp/"+item.name+"'>"+item.name+"</a>]</br>"
                        });
                        $('#files').html(files);
                    }
                }
            });
        }
        function showW (id,art) {
            resetShow();
            if (art=="D") {
                showWvl(id);
                return;
            } else if (art=="T") {
                location.href = "termin.php";
            } else if (art=="F") {
                showERP(id);
                return;
            } else {
                if ( id > 0 ) showMail(id);
            }
        }
        function resetShow() {
            WVLID = 0;
            MailID = 0;
            KontaktID = 0;
            KontaktTab = '';
            DName = '';
            DateiID = 0;
            noteid = 0;
            newFile = false;
            $('.mail').hide();
            $('.hideK').show();
            $('#name').val('');
            $('#cause').val('');
            $('#c_long').val('');
            $('#Finish').val('');
            $('#status1').prop('checked',true);
            $('#kontaktT').prop('checked',true);
            //$('#Kradio').buttonset("refresh");
            $('#DLink').prop('href','');
            $('#DLink').text('');
            $('#cp_cv_id').val('');
            $('#DCaption').empty();
            $('#uplfile').empty();
            kontakt(0,'','');
        };
        function Fehler(nr) {
                 if ( nr == -2 ) { msg = 'Fehler beim Zuordnen';}
            else if ( nr == -1 ) { msg = 'Gruppe nicht zulässig oder User fehlt!'; }
            else if ( nr == -3 ) { msg = 'Fehler beim Anlegen!'; }
            else if ( nr == -4 ) { msg = 'saveDocument!'; }
            else if ( nr == -5 ) { msg = 'Datei nicht gefunden!'; }
            else if ( nr == -6 ) { msg = 'Zuweisung fehlheschlagen!'; }
            else if ( nr == -7 ) { msg = 'Mail nicht zugewiesen!'; }
            else if ( nr == -8 ) { msg = 'Fileupload fehlgeschlagen!'; }
            else if ( nr == -9 ) { msg = 'Mail konnte nich abgeholt werden'; }
            else                 { msg = "Fehler beim Sichern";};
            alert("Error: "+nr+"\n"+msg);
        }
        function delWV() {
                $.ajax({                  
                    url: 'jqhelp/wvll.php',
                    dataType: 'json',
                    type:     'POST',
                    data:  { 'id':MailID, 'task':'delmail' },
                    success: function(rc){
                        console.log(JSON.stringify(rc));
                        if ( rc == 1 ) {
                            resetShow();
                        } else {
                            Fehler(rc);
                        }
                    }
                });
        }
        function saveWV() {
            var crmuser = $('#CRMUSER option:selected').val();
            var cause   = $('#cause').val();
            var c_long  = $('#c_long').val();
            var Finish  = $('#Finish').val();
            var cid     = $('#cp_cv_id').val();
            var DCaption= $('#DCaption').val();
            if ( $('#kontaktF').prop('checked') ) {
                var done  = ($('#status0').prop('checked'))?'t':'f';
                $.ajax({                  
                    url: 'jqhelp/wvll.php',
                    dataType: 'json',
                    type:     'POST',
                    data:  { 'status':done, 'cause':cause, 'c_long':c_long, 
                             'noteid':noteid, 'Finish':Finish, 'cp_cv_id':cid, 'WVLID':WVLID, 
                             'CRMUSER':crmuser, 'kontakt':'F', 'task':'erp' },
                    success: function(rc){
                        console.log(JSON.stringify(data));
                        console.log(JSON.stringify(rc));
                        if ( rc == 1 ) {
                            resetShow();
                        } else {
                            Fehler(rc);
                        }
                    }
                });
            } else if ( $('#kontaktE').prop('checked') ) {
                var done  = ($('#status0').prop('checked'))?'t':'f';
                var dateien = new Array();
                $(".dateien:checked").each(function() {
                    dateien.push($(this).val());
                });
                $.ajax({                  
                    url: 'jqhelp/wvll.php',
                    dataType: 'json',
                    type:     'POST',
                    data:  { 'status':done, 'cause':cause, 'c_long':c_long, 
                             'Finish':Finish, 'cp_cv_id':cid, 'muid':MailID, 
                             'CRMUSER':crmuser, 'kontakt':'E', 
                             'dateien[]':dateien, 'task':'mail' },
                    success: function(rc){
                        if ( rc == 1 ) {
                            resetShow();
                        } else {
                            Fehler(rc);
                        }
                    }
                });
            } else {
                var stat  = $('input[name=status]:radio:checked').val();
                var kontakt  = $('input[name=kontakt]:radio:checked').val();
                $.ajax({                  
                    url: 'jqhelp/wvll.php',
                    dataType: 'json',
                    type:     'POST',
                    data:  { 'status':stat,     'cause':cause,     'c_long':c_long, 
                             'Finish':Finish,   'cp_cv_id':cid,    'WVLID':WVLID, 
                             'CRMUSER':crmuser, 'kontakt':kontakt, 'filename':DName,
                             'newfile':newfile, 'DateiID':DateiID, 'DCaption':DCaption,
                             'task':'wvl'},
                    success: function(rc){
                        if ( rc < 0 ) {
                            Fehler(rc);
                        } else {
                            resetShow();
                        }
                    }
                });            
            }
        }
        function doInit() {
            var pagesize = $('#pagesize option:selected').val();
            $.ajax({
                url: 'jqhelp/wvll.php?task=wvl',
                dataType: 'json',
                success: function(data){
                    //console.log(JSON.stringify(data));
                    $('#wvliste tr[group="tc"]').remove();
                    $("#wvliste").trigger('update');
                    var content = '';
                    var mailcnt = 0;
                    var noread = 0;
                    $.each(data, function(i) {
                        if ( data[i].Art == 'E' && data[i].ID > 0) {
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
                    //console.log(JSON.stringify(content));
                    $("#wvliste").trigger('update');
                    $("#wvliste").tablesorter({widthFixed: true, widgets: ['zebra']})
                    $("#mails").html(mailcnt);
                    $("#unread").html(noread);
                    $("#wvliste")
                        .tablesorterPager({container: $("#pager"), size: pagesize, positionFixed: false});
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
        $(document).ready(
            $(function () {
                $( "#Kradio" ).buttonset();
                $( "#Sradio" ).buttonset();
                $('button').button().click( 
                    function(event) {
                        event.preventDefault();
                        name = this.getAttribute('name');
                        alert(name);
                        if ( name == 'saveWV' ) {
                           saveWV();
                        } else if ( name == 'delWV' ) {
                            delWV();
                        } else if ( name == 'resetShow' ) {
                            resetShow();
                        } else if ( name == 'dst' ) {
                            suchDst();
                        }
                 });

                $( "#Finish" ).datepicker($.datepicker.regional[ "de" ]);
                resetShow();
                doInit();
                $('#fileupload').fileupload({
                    dataType: 'json',
                    add: function (e, data) {
                        $('#uplfile').empty().append(data.files[0].name+' ');
                        $('#uplfile').append(data.files[0].size+' ');
                        $('#progress .bar').css('width','0%');
                        $('#uplfile').append($('<button/>').text('Upload + Save')
                                               .click(function () {
                                                   $('#msg').empty().append('Uploading...');
                                                   data.submit();
                                               })
                        );
                    },
                    done: function (e, data) {
                        $.each(data.result.files, function (index, file) {
                            if ( file.error != undefined ) { alert(file.error); return; };
                            $('#uplfile').empty().append(file.name+' done');
                            $('#msg').empty();
                            DName = file.name;
                            newfile = 1;
                            saveWV();
                        });
                    },
                    progressall: function (e, data) {
                        var progress = parseInt(data.loaded / data.total * 100, 10);
                        $('#progress .bar').css(
                            'width',
                            progress + '%'
                        );
                        //$('#percent').replaceWith(progress+' %');
                        
                    },
                });
            })
        );
   </script>
                    
<body >
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">Wiedervorlage</p>
<div style="float:left; width:45em; height:40em; text-align:center; border: 1px solid lightgray;" >
<form name="formular" action="wvl1.php" enctype='multipart/form-data' method="post" onSubmit='return false;'>
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="2000000">
<INPUT TYPE="hidden" name="cp_cv_id" id="cp_cv_id" value="">
<table border="0px" width="100%">
<tr style="width:100%;">
    <td class="klein" width='30%'>
        <select id='CRMUSER' name="CRMUSER" tabindex="1">
<!-- BEGIN Selectbox -->
            <option value="{UID}"{Sel}>{Login}</option>
<!-- END Selectbox -->
        </select>
        <br><span class="klein" style='width:35em'>CRM-User</span>
    </td><td width='*'>
        <span>
            <input type="text" id="name" name="name" size="25" maxlength="75"  value="" tabindex="2"> 
            <button name="dst" id="dst"> ? </button> 
        </span>
        <br>
        <span class="klein" >Zugewiesen an &nbsp;[<a href="" id="addresse" name="addresse"></a>]</span>
    </td>
</tr><tr  style="width:100%">
    <td class="klein" width='30%' >
        <input type="text" name="Finish" id="Finish" size="11" maxlength="10" value="" tabindex="3">
        <br><span class="klein">Zu Erledigen bis</span>
    </td><td class="klein" width='*'>
        <div id="Sradio">
            <input type="radio" name="status" id="status1" value="1" tabindex="5" checked><label for="status1">1&nbsp;</label>
            <span class='hideK' >
                <input type="radio" name="status" id="status2" value="2" tabindex="6"><label for="status2">2&nbsp;</label>
                <input type="radio" name="status" id="status3" value="3" tabindex="7"><label for="status3">3&nbsp;</label>
            </span>
            <input type="radio" name="status" id="status0" value="0" tabindex="8" ><label for="status0">Erledigt</label>
        </div>
        <br><span class="klein">Priorit&auml;t</span>
    </td>
</tr><tr>
    <td class="klein" colspan="2">
        <input type="text" name="cause" id="cause" size="60" maxlength="60" value="" tabindex="4">
        <br><span class="klein">Betreff</span>
    </td>
</tr><tr>
    <td class="klein" colspan="2">
        <textarea name="c_long" id="c_long" cols="65" rows="11" tabindex="9"></textarea>
        <br><span class="klein">Beschreibung</span>
    </td>
</tr><tr>
    <td class="klein hideD" colspan="2">
        <input id="fileupload" type="file" name="files[]" size="14"  tabindex="10" data-url="jqhelp/uploader.php">
        <div id="progress" class="progress" >
            <div class="bar" id='bar' style="width: 0%;"></div>
            <!--div class='percent' id='percent'>0 %</div-->
        </div>
        <br><span class="klein">Dokument</span> <b><a href="" id='DLink' target="_blank"></a></b><div id="uplfile"><div>
    </td>
</tr><tr>
    <td class="klein hideD" colspan="2">
        <input type="text" name="DCaption" id="DCaption" size="60" maxlength="75" value="" tabindex="11">
        <br><span class="klein">Dokumentbeschreibung</span>
    </td>
    </td>
</tr><tr style="width:100%">
    <td class="klein" colspan="2">
       <div id="Kradio">
           <span class='hideK'>
               <input type="radio" name="kontakt" id="kontaktT" value="T" tabindex="12" checked><label for="kontaktT">Telefon &nbsp;</label>
               <input type="radio" name="kontakt" id="kontaktM" value="M" tabindex="13"><label for="kontaktM">E-Mail &nbsp;</label>
               <input type="radio" name="kontakt" id="kontaktS" value="S" tabindex="14"><label for="kontaktS">Fax/Brief &nbsp;</label>
               <input type="radio" name="kontakt" id="kontaktP" value="P" tabindex="15"><label for="kontaktP">Pers&ouml;nlich &nbsp;</label>
               <input type="radio" name="kontakt" id="kontaktD" value="D" tabindex="16"><label for="kontaktD">Datei &nbsp;</label>
           </span>
           <span class='hideE'>
               <input type="radio" name="kontakt" id="kontaktF" value="F" tabindex="16"><label for="kontaktF">ERP</label>
           </span>
       </div> 
        <span style="visibility:hidden"><input type="radio" name="kontakt" id="kontaktE" value="E" tabindex="16"><label for="kontaktE">E-Mail &nbsp;</label></span><!-- vom Mailserver -->
        <br>
        <span class="klein">Kontaktart</span> 
    </td>
</tr><tr class='mail' >
    <td colspan="2">
       <span id="files"></span> 
    </td>
</tr><tr width="100%">
	<td colspan="2">
        <button id='reset' name='resetShow'>.:reset:.</button>
        <button id='save'  name='saveWV'>.:save:.</button>
        <button id='del'  class='mail' name='delWV'>.:delete:.</button>
    </td>
</tr><tr class='mail' style="width:100%;">
    <td colspan="2">
        <input type='checkbox' name='flagged'  id='flagged' value='1' > Flagged
        <input type='checkbox' name='answered' id='answered'value='1' > Answered
        <input type='checkbox' name='deleted'  id='deleted' value='1' > Deleted
        <input type='checkbox' name='seen'     id='seen'    value='1' > Seen   
        <input type='checkbox' name='draft'    id='draft'   value='1' > Draft  
        <input type='checkbox' name='recend'   id='recend'  value='1' > Recend 
    </td>
</tr><tr>
    <td colspan="2" id="msg">
        <b></b>
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
