<html>
        <head><title></title>
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{THEME}
{JQTABLE}
{JAVASCRIPTS}
	<link rel="stylesheet" href="jquery-plugins/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
	<script type="text/javascript" src="jquery-plugins/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script> 
    <script language="JavaScript">
    <!--
        function showItem(id) {
            pid = $('#liste option:selected').val();
            F1=open("getCall.php?Q={Q}C&pid="+pid+"&Bezug="+id,"Caller","width=770, height=680, left=100, top=50, scrollbars=yes");
        }
        function qrcode() {
            pid = $('#liste option:selected').val();
          	//  document.location.href="vcardexp.php?qr=1&Q={Q}&pid="+pid; 
			$.ajax({
               url: 'vcardexp.php?qr=1&Q={Q}&pid='+pid,
               type: 'GET',
               success: function(data){
               		$(".fancybox").trigger('click');
               }
             })                        
        }
        function anschr() {
            pid = $('#liste option:selected').val();
            $( "#etikett" ).dialog( "option", "maxWidth",  400 );
            $( "#etikett" ).dialog( "option", "maxHeight", 600 );
            $( "#etikett" ).dialog( { title: "Adresse" } );
            $('#iframe1').attr('src', 'showAdr.php?Q={Q}&pid='+pid+'&nojs=1'+'{ep}');
            $( "#etikett" ).dialog( "open" );
            //$( "#etikett" ).load("showAdr.php?Q={Q}&nojs=1&pid="+pid+"{ep}");
        }
        function notes() {
            pid = $('#liste option:selected').val();
            F1=open("showNote.php?Q={Q}&pid="+pid,"Notes","width=400, height=400, left=100, top=50, scrollbars=yes");
        }
        function vcard(){
            pid = $('#liste option:selected').val();
            document.location.href="vcardexp.php?Q={Q}&pid="+pid;
        }        
        function cedit(ed){                
            pid=false;
            if (ed) pid = $('#liste option:selected').val();
            document.location.href="personen3.php?id="+pid+"&edit="+ed+"&Quelle={Q}&fid={FID}";
        }
        function sellist(){                
            pid = $('#liste option:selected').val();
            document.location.href="personen1.php?fid={FID}&Quelle={Q}";
        }
        function doclink(){                
            pid = $('#liste option:selected').val();
            document.location.href="firma4.php?Q={Q}&fid={FID}&pid="+pid;
        }
        var start = 0;
        var max = 0;
        var y = 0;
        function showCall() {
            pid = $('#liste option:selected').val();
            $('#calls tr[group="usr"]').remove();
            $.ajax({
               url: 'jqhelp/firmaserver.php?task=showCalls&firma=0&id='+pid,
               dataType: 'json',
               success: function(data){
                            var content;
                            $.each(data.items, function(i) {
                                 content = '';
                                 content += '<tr group="usr" onClick="showItem('+data.items[i].id+');">'
                                 content += '<td>' + data.items[i].calldate + '</td>';
                                 content += '<td>' + data.items[i].id + '</td>';
                                 content += '<td>' + data.items[i].kontakt;
                                 if (data.items[i].inout == 'o') {
                                      content += ' &gt;</td>';
                                 } else if (data.items[i].inout == 'i') {
                                      content += ' &lt;</td>';
                                 } else {
                                      content += ' -</td>';
                                 }
                                 if ( data.items[i].new == 1 ) {
                                     content += '<td><b>' + data.items[i].cause + '</b></td>';
                                 } else {
                                     content += '<td>' + data.items[i].cause + '</td>';
                                 }
                                 content += '<td>' + data.items[i].cp_name + '</td></tr>';
                                 $('#calls tr:last').after(content);
                            })
                                $("#calls").trigger('update');
                                $("#calls")
                                    .tablesorter({widthFixed: true, widgets: ['zebra'], headers: { 2: { sorter: false } } })
                                    .tablesorterPager({container: $("#pager"), size: 15, positionFixed: false})
                        }
               });
            return false;
        }
        function showOne(id) {
            //was wollte ich hier?
        }
        function showContact() {
            pid = $('#liste option:selected').val();
            $.ajax({
                url: "jqhelp/firmaserver.php?task=showContact&id="+pid,
                dataType: 'json',
                success: function(data){
                              if (data.cp_id > 0) {
                                   $('#cp_greeting').empty().append(data.cp_greeting);
                                   $('#cp_title').empty().append(data.cp_title);
                                   $('#cp_givenname').empty().append(data.cp_givenname);
                                   $('#cp_name').empty().append(data.cp_name);
                                   $('#cp_street').empty().append(data.cp_street);
                                   $('#cp_country').empty().append(data.cp_country); 
                                   $('#cp_zipcode').empty().append(data.cp_zipcode);
                                   $('#cp_city').empty().append(data.cp_city);
                                   $('#cp_phone1').empty().append(data.cp_phone1); 
                                   $('#cp_phone2').empty().append(data.cp_phone2);
                                   $('#cp_mobile1').empty().append(data.cp_mobile1); 
                                   $('#cp_mobile2').empty().append(data.cp_mobile2);
                                   $('#cp_fax').empty().append(data.cp_fax);
                                   $('#cp_email').empty().append(data.cp_email);
                                   $('#cp_privatemail').empty().append(data.cp_privatemail);
                                   $('#cp_homepage').empty().append(data.cp_homepage);
                                   $('#cp_grafik').empty().append(data.cp_grafik);
                                   $('#cp_birthday').empty().append(data.cp_birthday);
                                   $('#cp_position').empty().append(data.cp_position);
                                   $('#cp_abteilung').empty().append(data.cp_abteilung);
                                   $('#cp_vcard').empty().append(data.cp_vcard);
                                   $('#cp_stichwort1').empty().append(data.cp_stichwort1);
                                   $('#cp_notes').empty().append(data.cp_notes);
                              } else {
                                  $('#cp_name').empty().append(data.cp_name);
                                  $('#cp_greeting').empty();
                                  $('#cp_title').empty();
                                  $('#cp_givenname').empty();
                                  $('#cp_street').empty();
                                  $('#cp_country').empty(); 
                                  $('#cp_zipcode').empty();
                                  $('#cp_city').empty();
                                  $('#cp_phone1').empty(); 
                                  $('#cp_phone2').empty();
                                  $('#cp_mobile1').empty(); 
                                  $('#cp_mobile2').empty();
                                  $('#cp_fax').empty();
                                  $('#cp_email').empty();
                                  $('#cp_privatemail').empty();
                                  $('#cp_homepage').empty();
                                  $('#cp_grafik').empty();
                                  $('#cp_birthday').empty();
                                  $('#cp_position').empty();
                                  $('#cp_abteilung').empty();
                                  $('#cp_vcard').empty();
                                  $('#cp_stichwort1').empty();
                                  $('#cp_notes').empty();
                              }
                         }
            })
            showCall();
        }
        function KdHelp() {
            link = $('#kdhelp option:selected').val();
            if (  $('#kdhelp').prop("selectedIndex") > 0 ) {
                f1=open("wissen.php?kdhelp="+link,"Wissen","width=750, height=600, left=50, top=50, scrollbars=yes");
                $('#kdhelp option')[0].selected = true;
            }
        }        
    var f1 = null;
    function toolwin(tool,_pid) {
        leftpos=Math.floor(screen.width/2);
        if ( _pid>0 ) tool = tool + $('#liste option:selected').val();
        f1=open(tool,"Adresse","width=350, height=200, left="+leftpos+", top=50, status=no,toolbar=no,menubar=no,location=no,titlebar=no,scrollbars=no,fullscreen=no");
    }
    //-->
    </script>
    <script>
    $(document).ready(
        function(){
        $( "#etikett" ).dialog({
          autoOpen: false,
          show: {
            effect: "blind",
            duration: 300
          },
          hide: {
            effect: "explode",
            duration: 300
          },
        });
        showContact();
    });
    $(function(){
         $('button')
          .button()
          .click( function(event) { 
              event.preventDefault();  
              name = this.getAttribute('name');
              if ( name == 'ks' ) {
                  var sw = $('#suchwort').val();
                  F1=open("suchKontakt.php?suchwort="+sw+"&Q=C&id={FID}","Suche","width=400, height=400, left=100, top=50, scrollbars=yes");
              } else if ( name == 'reload' ) {
                  showCall();
              } else if ( name == 'Link1' ) {
                  document.location.href = '{Link1}';
              } else if ( name == 'Link2' ) {
                  document.location.href = '{Link2}';
              } else if ( name == 'Link3' ) {
 					   document.location.href = '{Link3}'; 
              } else if ( name == 'close' ) {
                  event.preventDefault();
              } else if ( name == 'null' ) {
                  event.preventDefault();
              } else if ( name ){
                  var pid = $('#liste option:selected').val();
                  document.location.href = name + pid; 
              } else {
                  event.preventDefault();
              }
          });
         $( "input[type=submit]")
          .button()
         .click(function( event ) {
              event.preventDefault();
         });
         
         $(".fancybox").fancybox();
         
         $("#cpqr").click(function( event ) {
         	qrcode();
         });
         
    });


    </script>
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop" >.:detailview:. {FAART} <span title=".:important note:.">{Cmsg}</span></p>
<div id="menubox1">
    <div class="fancybox" rel="group" href="tmp/qr_{loginname}.png"><img src="" alt="" /></div> 
    <span style="float:left;" class="top1">
    <button name="Link1">.:Custombase:.</button>
    <button name="Link2">.:Contacts:.</button>
    <button name="Link3">.:Sales:.</button>
    <button name="firma4.php?Q={Q}&fid={FID}&pid=">.:Documents:.</button>
    <select style="visibility:{chelp}" id='kdhelp' name="kdhelp" onChange="KdHelp()">
<!-- BEGIN kdhelp -->
        <option value="{cid}">{cname}</option>
<!-- END kdhelp -->
    </select>
    </span>
    <span style="float:left; padding-left:12em; padding-top:2em; visibility:{tools};">
    <img src="tools/rechner.png"  onClick="toolwin('tools/Rechner.html',0)" title=".:simple calculator:."> &nbsp;
    <img src="tools/notiz.png"  onClick="toolwin('postit.php?popup=1',0)" title=".:postit notes:."> &nbsp;
    <img src="tools/kalender.png"  onClick="toolwin('tools/kalender.php?Q=P&id=',1)" title=".:calender:."> &nbsp;
    <a href="javascript:void(s=prompt('.:ask leo:.',''));if(s)leow=open('http://dict.leo.org/?lp=ende&search='+escape(s),'LEODict','width=750,height=550,scrollbars=yes,resizeable=yes');if(leow)leow.focus();"><img src="tools/leo.png"  title="LEO .:english/german:." border="0"></a> &nbsp;
    </span>
</div>
<!--span style="position:absolute; left:0.2em; top:7.2em; width:99%;" -->
<span id='contentbox' >
<br>
<!-- Beginn Code ------------------------------------------->
<div style="float:left; width:45em; height:37em;  border: 1px solid lightgray;" >
        <div style="float:left; width:45em; height:4.5em; text-align:left; border-bottom: 1px solid lightgray;">
            <form name="contact">
            <span class="fett" >
            &nbsp;{Fname1} &nbsp; &nbsp; .:KdNr:.: {customernumber}<br />
            &nbsp;{Fdepartment_1}<br />
            &nbsp;{Plz} {Ort} </span>
            <select name="liste" id="liste" style="width:150px;float:right;" onChange="showContact();">
            {kontakte}</select>
            </form>
        </div>
        <div style="float:left; width:70%; height:13em; text-align:left; border-bottom: 0px solid lightgray;" >
            &nbsp;<span id="cp_greeting"></span> <span id="cp_title"></span><br />
            &nbsp;<span id="cp_givenname"></span> <span id="cp_name"></span><br />
            &nbsp;<span id="cp_street"></span><br />
            <span class="mini">&nbsp;<br /></span>
            &nbsp;<span id="cp_country"></span><span id="cp_zipcode"></span> <span id="cp_city"></span><br />
            <span class="mini">&nbsp;<br /></span>
            &nbsp;<img src="image/telefon.gif" style="visibility:{none};" id="phone"> <span id="cp_phone1"></span> <span id="cp_phone2"></span><br />
            &nbsp;&nbsp;<img src="image/mobile.gif" style="visibility:{none};" id="mobile"> <span id="cp_mobile1"></span> <span id="cp_mobile2"></span><br />
            &nbsp;<img src="image/fax.gif" style="visibility:{none};" id="fax"> <span id="cp_fax"></span><br />
            &nbsp;<span id="cp_email"></span><br />
            &nbsp;<span id="cp_homepage"></span><br /><br />
        </div>
        <div style="float:left; width:29%; height:13em; text-align:right; border-bottom: 0px ;" id="cpinhalt2">
            <span id="extraF"></span>
            <a href="#" onCLick="vcard();"><img src="image/vcard.png" border="0" style="visibility:{none};" id="cpvcard" height='30'></a> &nbsp; 
            <a href="#"><img src="image/qr.png" border="0" style="visibility:{none};" id="cpqr" height='30'></a> &nbsp; 
            <a href="#" onCLick="anschr();"><img src="image/brief.png" border="0" style="visibility:{none};" id="cpbrief" height='30'></a><br />
            <span id="cp_grafik" style="padding-right:1px;"></span></br >
            <span id="cp_birthday" style="padding-right:1px;"></span></br />
            <span id="cp_position" style="padding-right:1px;"></span><br />
            <span id="cp_abteilung" style="padding-right:1px;"></span><br />
            <span id="cp_vcard" style="padding-right:1px;"></span><br />
        </div>
        <div style="position:absolute;top:20em; left:0em; width:45em;  text-align:left; border-bottom: 0px;">
            &nbsp;<span id="cp_privatphone"></span> <span id="cp_privatemail"></span><br />
             <hr width="100%">
                &nbsp;<!--input type='submit' value='VCard' onClick="vcard()" -->
                <b>.:Contacts:.:</b> 
                <input type='submit' value='{Edit}' onClick="cedit(1)" >
                <input type='submit' value='.:keyin:.' onClick="cedit(0)" >
                <input type='submit' value='.:fromList:.' onClick="sellist()">
            <hr>
            <span id="cp_stichwort1" class="klein fett" style="width:45em; padding-left:1em;"></span><br />
            <span id="cp_notes" class="klein" style="width:45em; padding-left:1em;"></span>
        </div>
</div>
<div style="float:left; width:46%; height:37em; text-align:left; border: 1px solid lightgray; border-left:0px;">
        <table id="calls" class="tablesorter" width="100%" style='margin:0px;'>
        <thead><tr><th>Datum</th><th>id</th><th class="{ sorter: false }"></th><th>Betreff</th><th>.:contakt:.</th></tr></thead>
        <tbody style='cursor:pointer'>
        <tr onClick="showItem(0)"><td></td><td>0</td><td></td><td>.:newItem:.</td><td></td></tr>
        </tbody>
        </table><br>
        <div id="pager" class="pager" style='position:absolute;'>
        <form name="ksearch" onSubmit="return ks();"> &nbsp; 
 	   	<img src="{CRMPATH}jquery-plugins/Table/addons/pager/icons/first.png" class="first"/>
 	   	<img src="{CRMPATH}jquery-plugins/Table/addons/pager/icons/prev.png" class="prev"/>
            <input type="text" id="suchwort" name="suchwort" size="20"><input type="hidden" name="Q" value="{Q}">
            <button id='ks' name='ks'>.:search:.</button> 
            <button id='reload' name='reload'>reload</button>             
 	   	<img src="{CRMPATH}jquery-plugins/Table/addons/pager/icons/next.png" class="next"/>
 	   	<img src="{CRMPATH}jquery-plugins/Table/addons/pager/icons/last.png" class="last"/>
 	   	<select class="pagesize" id='pagesize'>
 	   		<option value="10">10</option>
 	   		<option value="15" selected>15</option>
 	   		<option value="20">20</option>
 	   		<option value="25">25</option>
 	   		<option value="30">30</option>
 	   	</select>
        </form>
        </div>
</div>
<!-- End Code ------------------------------------------->
</span>
<div id="etikett">
<iframe id="iframe1" width='100%' height='450'  scrolling="auto" border="0" frameborder="0"><img src='image/wait.gif'></iframe>
</div>
{END_CONTENT}
</body>
</html>
