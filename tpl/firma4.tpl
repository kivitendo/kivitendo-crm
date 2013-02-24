<html>
    <head><title></title>
{STYLESHEETS}
    <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css">
    <link rel="stylesheet" type="text/css" href="{JQUERY}/jquery-ui/themes/base/jquery-ui.css">
    <script type="text/javascript" src="{JQUERY}jquery-ui/jquery.js"></script>
    <script type="text/javascript" src="{JQUERY}jquery-ui/ui/jquery-ui.js"></script>
    {THEME}
    <script type="text/javascript" src="{JQUERY}inc/dokument.js"></script>
{JAVASCRIPTS}
    <script language="JavaScript">
    <!--
    function showD () {
        id = $('#vorlage option:selected').val();
        if (id>0) {
            hidelinks(0);
            $('#subfilebrowser').hide();
            if ("{PID}"=="") { pid=0; } else { pid="{PID}"; };
            $.get('jqhelp/firmaserver.php?task=getDocVorlage&fid={FID}&tab={Q}&pid='+pid+"&id="+id,function(data) { $('#fbright').empty().append(data); });
        }
    }
    function showV () {
        nr = $('#wv option:selected').val();
        if (nr>0) {
            uri="vertrag3.php?vid=" + nr;
            window.location.href=uri;
        }
    }    

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
    <button name="{Link1}">.:Custombase:.</button>
    <button name="{Link2}">.:Contacts:.  </button>
    <button name="{Link3}">.:Sales:.     </button>
    <button name="{Link4}">.:Documents:. </button>
</div>
<!--span style="position:absolute; left:0.2em; top:7.2em; width:99%; height:90%;"-->
<span id='contentbox'>
<br>
<!-- Hier beginnt die Karte  ------------------------------------------->
<span style="float:left; width:40%; height:90%;  text-align:center; padding:2px; border: 1px solid black; border-bottom: 0px;">
    <div style="float:left; width:100%; height:5.5em; text-align:left; border-bottom: 1px solid black;" >
    <table>
    <tr><td class="fett normal">{Name}</td><td></td></tr>
    <tr><td class="fett">.:KdNr:.: {customernumber}</td><td>ID: {PID}</td></tr>
    </table>
    </div>
    <div style="float:left; width:100%;min-height:300px;  text-align:left; border-bottom: 0px solid black;" >
        <button name="onClick=newFile('left')">.:uploadDocument:.   </button>
        <button name="onClick=newDir('left')">.:newDirectory:.      </button>
        <button name="onClick=dateibaum('left',pfadleft)">.:reread:.</button>
    <br>
    .:current path:.: <span id="path"></span>
    <span id="fbleft"><!-- Platzhalter für den dynamischen Inhalt --></span>
    </div>
</span>

<span style="float:left; width:58%; height:90%; text-align:left; border: 1px solid black; border-bottom: 2px; padding:2px; border-left:0px;">
    <div style="float:left; width:100%; height:5.5em; text-align:left; padding-top: 0; border-top: 0; border-bottom: 1px solid black;" class="fett">
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
    <div style="float:left; width:100%;min-height:300px;   text-align:left; border-bottom: 0px solid black;" class="normal">
        <button id="subfilebrowser" name="onClick=dateibaum('right',pfadleft)">.:Filebrowser:.</button>
        <button id="subdownload"    name="onClick=download();">.:download:.                   </button>
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
    
<!-- Hier endet die Karte ------------------------------------------->
</span>
{END_CONTENT}
</body>
</html>
