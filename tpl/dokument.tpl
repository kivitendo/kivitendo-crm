<!-- $Id$ -->
<html>
    <head><title></title>
    {STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/tabcontent.css"></link>
    {JAVASCRIPTS}
    {AJAXJS}
    <script language="JavaScript">
    <!--
    function mkDir() {
        seite=document.getElementById("seite").value;
        name=document.getElementById("subdir").value;
        newDir();
        xajax_newDir(seite,pfadleft,name);
        xajax_showDir(seite,pfadleft);
    }
    var downloadfile = "";
    function download(file) {
        downloadfile=open("download.php?file="+file,"Download","width=250px,height=200px,top=50px,menubar=no,status=no,toolbar=no,dependent=yes");
        window.setTimeout("downloadfile.close()", 30000);
    }
    var onA = false;
    var onL = false;
    function editattribut() {
        if (onA) {
            onA = false;
            document.getElementById("attribut").style.visibility = "hidden";
        } else {
            onL = false;
            document.getElementById("fileDel").style.visibility = "hidden";
            onA = true;
            document.getElementById("attribut").style.visibility = "visible";
        }
    }
    function saveAttribut() {
        name=document.getElementById("docname").value;
        oldname=document.getElementById("docoldname").value;
        pfad=document.getElementById("docpfad").value;
        komment=document.getElementById("docdescript").value;
        id = document.getElementById("docid").value;
        xajax_saveAttribut(name,oldname,pfad,komment,id);
    }
    function deletefile() {
        if (onL) {
            onL = false;
            document.getElementById("fileDel").style.visibility = "hidden";
        } else {
            onA = false;
            document.getElementById("attribut").style.visibility = "hidden";
            onL = true;
            name = document.getElementById("docname").value;
            document.getElementById("delname").innerHTML = name;
            document.getElementById("fileDel").style.visibility = "visible";
        }
    }
    function movefile(file) {
        xajax_moveFile(file,pfadleft);
    }
    function filedelete() {
        id = document.getElementById("docid").value;
        name = document.getElementById("docname").value;
        pfad = document.getElementById("docpfad").value;
        if (!id) id=0;
        xajax_delFile(id,pfad,name);
        dateibaum('left',pfadleft)
        dateibaum('right',pfadright);
        deletefile();
    }
    var onD = false;
    function newDir(seite) {
        if (onD) {
            onD = false;
            document.getElementById("fixiert").style.visibility = "hidden";
        } else {
            onD = true;
            document.getElementById("seite").value=seite;
            document.getElementById("fixiert").style.visibility = "visible";
            document.getElementById("subdir").focus();
        }
    }
    var onF = false;
    function newFile(seite) {
        if (onF) {
            onF = false;
            document.getElementById("uploadfr").style.visibility = "hidden";
        } else {
            onF = true;
            document.getElementById("seite").value=seite;
            document.getElementById("uploadfr").style.visibility = "visible";
            frames["frupload"].document.getElementById("upldpath").value=pfadleft;
            frames["frupload"].document.getElementById("caption").focus();
       }
    }
    var pfadleft = "";
    var pfadright = "";
    function showFile(seite,file) {
        if(seite=="left") { 
            xajax_showFile(pfadleft,file); 
        } else { 
            xajax_showFile(pfadright,file); 
        };
        document.getElementById("subdelete").style.visibility="visible";
        document.getElementById("subdownload").style.visibility="visible";
        document.getElementById("subedit").style.visibility="visible";
        document.getElementById("lock").style.visibility="visible";
        document.getElementById("submove").style.visibility="visible";
    }
    function dateibaum(seite,start) {
        if(seite=="left") { pfadleft=start; }
        else { 
            pfadright=start; 
            document.getElementById("subdelete").style.visibility="hidden";
            document.getElementById("subdownload").style.visibility="hidden";
            document.getElementById("subedit").style.visibility="hidden";
            document.getElementById("lock").style.visibility="hidden";
            document.getElementById("submove").style.visibility="hidden";
        };
        xajax_showDir(seite,start);
        setTimeout("dateibaum('left',pfadleft)",100000) // 100sec
    }
    //-->
    </script>
<body onLoad="dateibaum('left','/');">
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
        <li id="subnewfile"><a href="#" onClick="newFile('left')">.:uploadDocument:.</a></li>
        <li id="subnewfolder"><a href="#" onClick="newDir('left')">.:newDirectory:.</a></li>
        <li id="subrefresh"><a href="#" onClick="dateibaum('left',pfadleft)">.:reread:.</a></li>
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
        <li id="subdownload" style="visibility:hidden;"><a href="#" >.:download:.</a></li>
        <li id="subdelete" style="visibility:hidden;"><a href="#" >.:delete:.</a></li>
        <li id="submove" style="visibility:hidden;"><a href="#" >.:move:.</a></li>
        <li id="subedit" style="visibility:hidden;"><a href="#" >.:edit attribute:.</a></li>
        <li id="lock" style="visibility:hidden;"><a href="#" >.:lock file:.</a></li>
    </ul><br>
        <span id="fbright"><!-- Platzhalter für den dynamischen Inhalt --></span>
    </div>
</span>


<!-- Neues Verzeichnis erstellen: -->
<div id="fixiert" style="visibility:hidden; position:absolute; left:5em; top:10em; z-index:1;" class="docfrm">
    <table width="99%" class="klein">
    <tr class="dochead"><td>.:Create a new Directory:.</td><td align="right"><a href="javascript:newDir()">(X)</a></td></tr>
    <tr><td height="100%">&nbsp;</td></tr>
    <tr><td class="ce"><input type="hidden" name="seite" id="seite">
    <input type="text" name="subdir" id="subdir" size="26"> <input type="button" name="sdok" value=".:create:." onClick="mkDir();"></td></tr>
    </table>
</div>

<!-- Eine Datei hochladen: -->
<div id="uploadfr" "class="docfrm" style="visibility:hidden; position:absolute; left:4em; top:10em; z-index:1; width:29em; height:21em;">
    <iframe id="frupload" name="frupload" src="upload.php" frameborder="0" width="100%" height="100%"></iframe>
</div>

<!-- Attribute editieren: -->
<div id="attribut" style="visibility:hidden; position:absolute; left:5em; top:10em; width:39em; z-index:1;" class="docfrm">
    <table width="99%" class="klein">
    <tr class="dochead"><td>.:edit attribute:.</td><td align="right"><a href="javascript:editattribut()">(X)</a></td></tr>
    </table>
    <input type="hidden" name="docid" id="docid" value="">
    <input type="hidden" name="docoldname" id="docoldname" value="">
    <input type="hidden" name="docpfad" id="docpfad" value="">
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
<div id="fileDel" style="visibility:hidden; position:absolute; left:4em; top:10em; width:39em; z-index:1;" class="docfrm">
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
