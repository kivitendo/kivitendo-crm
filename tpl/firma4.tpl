<html>
    <head><title></title>
{STYLESHEETS}
{CRMCSS}
{JAVASCRIPTS}
{THEME} 
	<link rel="stylesheet" href="jquery-plugins/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
    <script type="text/javascript" src="jquery-plugins/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script language="JavaScript">
    <!--
    var serreg;
    function showD () {
        did = $('#vorlage option:selected').val();
        if (did>0) {
            hidelinks(0);
            $('#subfilebrowser').hide();
            if ("{PID}"=="") { pid=0; } else { pid="{PID}"; };
            $('#iframe1').attr('src', 'firma4a.php?fid={FID}&tab={Q}&pid='+pid+'&did='+did);
            //window.frames["iframe1"].location.reload();

            //$( "#serbrief" ).load( 'firma4a.php?fid={FID}&tab={Q}&pid='+pid+'&did='+did );
            $( "#serbrief" ).dialog( "option", "minWidth",  600 );
            $( "#serbrief" ).dialog( "option", "minHeight", 600 );
            $( "#serbrief" ).dialog( { title: "Briefvorlage" } );
            $( "#serbrief" ).dialog( "open" );
        };
    }
    function showV () {
        nr = $('#wv option:selected').val();
        if (nr>0) {
            uri="vertrag3.php?vid=" + nr;
            window.location.href=uri;
        }
    }    
    $(document).ready(
        function(){
        $( "#serbrief" ).dialog({
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
        $("#subdownload").button().click(
			function( event ) {
				window.open("download.php?file="+aktfile);
		});
	   $(".fancybox").fancybox();
       $("#showFile").button().click(
            function( event ) {
				$.ajax({
					type: "GET",
					url: "download.php?showdl=1",
					success: function(strResponse){
						$(".fancybox").attr("href", strResponse + aktfile);
						$(".fancybox").trigger('click');
						$(".fancybox").empty();
						$(".fancybox").attr("href", "");
					}
			});
		});
    });

    //-->
    </script>
<body onLoad="dateibaum('left','/{Q}{customernumber}/{PID}'), hidelinks(0) ;">
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:detailview:. {FAART}</p>
<form name="firma4" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="pid" value="{PID}">
<input type="hidden" name="fid" value="{FID}">
<input type="hidden" name="Q" value="{Q}">
<!--div style="position:absolute; top:5.4em; left:0.2em;  width:42em;"-->
<div id='menubox2'>
	<div class="fancybox" data-fancybox-type="iframe" href=""></div>
    <button name="{Link1}">.:Custombase:.</button>
    <button name="{Link2}">.:Contacts:.  </button>
    <button name="{Link3}">.:Sales:.     </button>
    <button name="{Link4}">.:Documents:. </button>
</div>
<!--span style="position:absolute; left:0.2em; top:7.2em; width:99%; height:90%;"-->
<span id='contentbox'>
<br>
<!-- Hier beginnt die Karte  ------------------------------------------->
<span style="float:left; width:40%; height:90%;  text-align:center; padding:2px; border: 1px solid lightgray; border-bottom: 0px;">
    <div style="float:left; width:100%; height:5.5em; text-align:left; border-bottom: 1px solid lightgray;" >
    <table>
    <tr><td class="fett normal">{Name}</td><td></td></tr>
    <tr><td class="fett">.:KdNr:.: {customernumber}</td><td>ID: {PID}</td></tr>
    </table>
    </div>
    <div style="float:left; width:100%;min-height:300px;  text-align:left; border-bottom: 0px;" >
        <button name="onClick=newFile('left')">.:uploadDocument:.   </button>
        <button name="onClick=newDir('left')">.:newDirectory:.      </button>
        <button name="onClick=dateibaum('left',pfadleft)">.:reread:.</button>
    <br>
    .:current path:.: <span id="path"></span>
    <span id="fbleft"><!-- Platzhalter für den dynamischen Inhalt --></span>
    </div>
</span>

<span style="float:left; width:58%; height:90%; text-align:left; border: 1px solid lightgray; border-bottom: 2px; padding:2px; border-left:0px;">
    <div style="float:left; width:100%; height:5.5em; text-align:left; padding-top: 0; border-top: 0; border-bottom: 1px solid lightgray;" class="fett">
    <table>
    <tr><td>.:Templates:.:</td><td>
    <select name="vorlage" id="vorlage" onChange="showD();" style="width:150px;">
        <option value=""></option>
<!-- BEGIN Liste -->
        <option value="{ID}">{Bezeichnung}</option>
<!-- END Liste -->
    </select></td></tr>
    <tr><td>.:Service contract:.:</td><td>
    <select name="wv" id="wv" onChange="showV();" style="width:150px;">
        <option value=""></option> 
<!-- BEGIN Vertrag -->
        <option value="{cid}">{vertrag}</option>
<!-- END Vertrag -->
    </select></td></tr>
    </table>
    </div>
    <div style="float:left; width:100%;min-height:300px;   text-align:left; border-bottom: 0px;" class="normal">
        <button id="subfilebrowser" name="onClick=dateibaum('right',pfadleft)">.:Filebrowser:.</button>
        <button id="subdownload">.:download:.               </button>
		<button id="showFile"   >.:view:.                   </button>
        <button id="subdelete"      name="onClick=deletefile();">.:delete:.                   </button>
        <button id="submove"        name="onClick=movefile();">.:move:.                       </button> 
        <button id="subedit"        name="onClick=editattribut();">.:edit attribute:.         </button>
        <button id="lock"           name="onClick=lockFile();">.:lock file:.                  </button>
    <br>
        <span id="fbright" style='height:100%;min-height:300px'><!-- Platzhalter für den dynamischen Inhalt --></span>
    </div>
</span>


<!-- Neues Verzeichnis  -->
<div id="newwindir" title=".:newDirectory:.">
    <p valign="center"><input type="hidden" name="seite" id="seite">
    <input type="text" name="subdir" id="subdir" size="26"><br /><br />
    <input type="button" name="sdok" value=".:create:." onClick="mkDir();"><br />
    <br />
    <center><button name="close" onClick="$('#newwindir').dialog('close');">.:close:.</button></center>
    </p>
</div>

<!-- Datei upload  -->
<div id="uploadfr" title=".:uploadDocument:.">
    <iframe id="frupload" name="frupload" src="upload.php" frameborder="0" width="100%" height="80%"></iframe>
    <center><button name="close" onClick="$('#uploadfr').dialog('close');">.:close:.</button></center>
</div>

<!-- Dateiattribute ändern  -->
<div id="attribut" title=".:edit attribute:.">
    <input type="hidden" name="docid"      id="docid" value="">
    <input type="hidden" name="wvid"       id="wvid" value="">
    <input type="hidden" name="docoldname" id="docoldname" value="">
    <input type="hidden" name="docpfad"    id="docpfad" value="">
    <center>
    <table >
    <tr><td ><textarea name="docdescript" id="docdescript" cols="65" rows="8"></textarea></td></tr>
    <tr><td class="klein">.:Description:.</td></tr>
    <tr><td ><input type="text" name="docname" id="docname" size="45" value=""></td></tr>
    <tr><td class="klein">.:Filename:.</td></tr>
    <!--tr><td class="klein"><input type="text" name="iwvdate" id="wvdate" size="15" value=""></td></tr>
    <tr><td class="mini">.:wvdate:.</td></tr-->
    <tr><td class="re"><input type="button" name="saveAtr" value=".:save:." onClick="saveAttribut();"></td></tr>
    </table>
    <button name="close" onClick="$('#attribut').dialog('close');">.:close:.</button>
    </center>
</div>

<!-- Datei löschen -->
<div id="fileDel" title=".:delete:.">
    <p><center>
    <span class="fett" id="delname"></span><br />
    <br />
    <a href="javascript:filedelete();"><img src="image/eraser.png" border="0">.:Really:.</a><br />
    <br />
    <a href="javascript:deletefile();"><img src="image/fileclose.png" border="0">.:Better not:.</a><br />
    <br />
    <br />
    <button name="close" onClick="$('#fileDel').dialog('close');">.:close:.</button></center>
    </p>
</div>

<!-- Briefvorlage  -->
<div id="serbrief">
<iframe id="iframe1" width='100%' height='500px'  scrolling="auto" border="0" frameborder="0"><img src='image/wait.gif'></iframe>
</div>    

<!-- Hier endet die Karte ------------------------------------------->
</span>
{END_CONTENT}
<script type="text/javascript" src="{CRMURL}inc/dokument.js"></script>
</body>
</html>
