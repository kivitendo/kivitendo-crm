<html>
    <head><title></title>
{STYLESHEETS}
    <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css">
    <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/tabcontent.css">
    <link rel="stylesheet" type="text/css" href="{JQUERY}/jquery-ui/themes/base/jquery-ui.css">
    <script type="text/javascript" src="{JQUERY}jquery-ui/jquery.js"></script>
    <script type="text/javascript" src="{JQUERY}jquery-ui/ui/jquery-ui.js"></script>
    <script type="text/javascript" src="{JQUERY}inc/dokument.js"></script>
{JAVASCRIPTS}
<body onLoad="dateibaum('left','/'), hidelinks() ;">
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:documents:. </p>
<form name="dokument.php" enctype='multipart/form-data' action="{action}" method="post">

<span id='contentbox2'>
<!-- Hier beginnt die Karte  ------------------------------------------->

<!-- linker Dateibaum: -->
<span style="float:left; width:40%; height:90%; text-align:center; padding:2px; border: 1px solid black; border-bottom: 0px;">
    <div style="float:left; width:100%; min-height:300px; text-align:left; border-bottom: 0px solid black;" >
    <ul id="submenu" class="subshadetabs">
        <li id="subnewfile"  ><a href="#" onClick="newFile('left')">.:uploadDocument:.</a></li>
        <li id="subnewfolder"><a href="#" onClick="newDir('left')">.:newDirectory:.</a></li>
        <li id="subrefresh"  ><a href="#" onClick="dateibaum('left',pfadleft)">.:reread:.</a></li>
    </ul>
    <br>
    .:current path:.: <span id="path"></span>
    <span id="fbleft"><!-- Platzhalter für den dynamischen Inhalt --></span>
    </div>
</span>

<!-- rechter Dateibaum: -->
<span style="float:left; width:58%; height:90%; text-align:left; border: 1px solid black; border-bottom: 0px; padding:2px; border-left:0px;">
    <div style="float:left; width:100%; min-height:300px;  text-align:left; border-bottom: 0px solid black;" class="normal">
    <ul id="submenu2" class="subshadetabs">
        <li id="subfilebrowser"><a href="#" onClick="dateibaum('right',pfadleft)">.:Filebrowser:.</a></li>
        <li id="subdownload"   ><a href="#" onClick='download();'    >.:download:.</a></li>
        <li id="subdelete"     ><a href="#" onClick='deletefile();'  >.:delete:.</a></li>
        <li id="submove"       ><a href="#" onClick='movefile();'    >.:move:.</a></li>
        <li id="subedit"       ><a href="#" onClick='editattribut();'>.:edit attribute:.</a></li>
        <li id="lock"          ><a href="#" onClick='lockFile();'    >.:lock file:.</a></li>
    </ul><br>
        <span id="fbright"><!-- Platzhalter für den dynamischen Inhalt --></span>
    </div>
</span>


<!-- Neues Verzeichnis erstellen: -->
<div id="newwindir"  style="position:absolute; left:4em; top:10em; z-index:1; width:29em; height:8em;" class="docfrm">
    <table width="99%" class="klein">
    <tr class="dochead"><td>.:Create a new Directory:.</td><td align="right"><a href="javascript:newDir()">(X)</a></td></tr>
    <tr><td height="100%">&nbsp;</td></tr>
    <tr><td class="ce"><input type="hidden" name="seite" id="seite">
    <input type="text" name="subdir" id="subdir" size="26"> <input type="button" name="sdok" value=".:create:." onClick="mkDir();"></td></tr>
    </table>
</div>

<!-- Eine Datei hochladen: -->
<div id="uploadfr" class="docfrm" style="position:absolute; left:4em; top:10em; z-index:1; width:29em; height:21em;">
    <iframe id="frupload" name="frupload" src="upload.php" frameborder="0" width="100%" height="100%"></iframe>
</div>

<!-- Attribute editieren: -->
<div id="attribut" style="position:absolute; left:5em; top:10em; width:39em; z-index:1;" class="docfrm">
    <table width="99%" class="klein">
    <tr class="dochead"><td>.:edit attribute:.</td><td align="right"><a href="javascript:editattribut()">(X)</a></td></tr>
    </table>
    <input type="hidden" name="docid"      id="docid" value="">
    <input type="hidden" name="docoldname" id="docoldname" value="">
    <input type="hidden" name="docpfad"    id="docpfad" value="">
    <center>
    <table >
    <tr><td class="klein"><textarea name="docdescript" id="docdescript" cols="60" rows="8"></textarea></td></tr>
    <tr><td class="mini">.:Description:.</td></tr>
    <tr><td class="klein"><input type="text" name="docname" id="docname" size="35" value=""></td></tr>
    <tr><td class="mini">.:Filename:.</td></tr>
    <tr><td class="re"><input type="button" name="saveAtr" value=".:save:." onClick="saveAttribut();"></td></tr>
    </table>
    </center>
</div>

<!-- Eine Datei löschen: -->
<div id="fileDel" style=" position:absolute; left:4em; top:10em; width:39em; z-index:1;" class="docfrm">
    <table width="99%" class="klein">
    <tr class="dochead"><td>.:Delete a File:.</td><td align="right"><a href="javascript:deletefile()">(X)</a></td></tr>
    <tr><td height="100%">&nbsp;</td></tr>
    <tr><td height="100%" class="ce"><b><span id="delname"></span></b></td></tr>
    <tr><td class="ce"><a href="javascript:filedelete();"><img src="image/eraser.png" border="0">.:Really:.</a></td></tr>
    <tr><td class="ce"><a href="javascript:deletefile();"><img src="image/fileclose.png" border="0">.:Better not:.</a></td></tr>
    </table>
</div>
    
<!-- Hier endet die Karte ------------------------------------------->
</span>
{END_CONTENT}
</body>
</html>
