<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<link type="text/css" REL="stylesheet" HREF="css/tabcontent.css"></link>
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
	function editattribut() {
        if (onA) {
            onA = false;
            document.getElementById("attribut").style.visibility = "hidden";
        } else {
            onA = true;
            document.getElementById("attribut").style.visibility = "visible";
	    }
	}
	function saveAttribut() {
		name=document.getElementById("docname").value;
		oldname=document.getElementById("docoldname").value;
		pfad=document.getElementById("docpfad").value;
		komment=document.getElementById("docdescript").value;
		id=	document.getElementById("docid").value;
		xajax_saveAttribut(name,oldname,pfad,komment,id);
	}
	var onL = false;
	function deletefile() {
		if (onL) {
            onL = false;
            document.getElementById("fileDel").style.visibility = "hidden";
        } else {
            onL = true;
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
    var pickup = {PICUP};
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
        if (pickup) document.getElementById("picup").style.visibility="visible";
	}
    function picup(pfad,file) {
        //opener.document.getElementById("elm1").value="<a href='"+pfad+file+"'>"+file+"</a>";
        text = "<a href='"+pfad+file+"'>"+file+"</a>";
        var input = opener.document.getElementById("elm1");
        input.focus();
        /* für Internet Explorer */
        if(typeof document.selection != 'undefined') {
            /* Einfügen des Formatierungscodes */
            var range = document.selection.createRange();
            range.text = text;
            /* Anpassen der Cursorposition */
            range = document.selection.createRange();
            range.moveStart('character', text.length);      
            range.select();
        } else if(typeof input.selectionStart != 'undefined') {
        /* für neuere auf Gecko basierende Browser */
            var start = input.selectionStart;
            input.value =  input.value.substr(0, start) + text + input.value.substr(start);
            /* Anpassen der Cursorposition */
            var pos;
            pos = start + text.length;
            input.selectionStart = pos;
            input.selectionEnd = pos;
      } else {
      /* für die übrigen Browser */
      /* Abfrage der Einfügeposition */
          var pos;
          var re = new RegExp('^[0-9]{0,3}$');
          while(!re.test(pos)) {
            pos = prompt("Einfügen an Position (0.." + input.value.length + "):", "0");
          }
          if(pos > input.value.length) {
            pos = input.value.length;
          }
          /* Einfügen des Formatierungscodes */
          var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
          input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
      }
        self.close();
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
<p class="listtop">.:documents:. </p>
<form name="dokument.php" enctype='multipart/form-data' action="{action}" method="post">

<span style="position:absolute; left:1em; top:4.3em; width:99%; height:90%;">
<!-- Hier beginnt die Karte  ------------------------------------------->

<!-- linker Dateibaum: -->
<span style="float:left; width:40%; height:90%; text-align:center; padding:2px; border: 1px solid black; border-bottom: 0px;">
    <div style="float:left; width:100%; text-align:left; border-bottom: 0px solid black;" >
    <ul id="submenu" class="subshadetabs">
        <li id="subnewfile"><a href="#" onClick="newFile('left')">.:uploadDocument:.</a></li>
        <li id="subnewfolder"><a href="#" onClick="newDir('left')">.:newDirectory:.</a></li>
        <li id="subrefresh"><a href="#" onClick="dateibaum('left',pfadleft)">.:reread:.</a></li>
    </ul>
    <br>
    .:current path:.: <span id="path"></span>
    <span id="fbleft"></span>
    </div>
</span>

<!-- rechter Dateibaum: -->
<span style="float:left; width:58%; height:90%; text-align:left; border: 1px solid black; border-bottom: 0px; padding:2px; border-left:0px;">
    <div style="float:left; width:100%;  text-align:left; border-bottom: 0px solid black;" class="normal">
    <ul id="submenu2" class="subshadetabs">
        <li id="subfilebrowser"><a href="#" onClick="dateibaum('right',pfadleft)">.:Filebrowser:.</a></li>
        <li id="subdownload" style="visibility:hidden;"><a href="#" >.:download:.</a></li>
        <li id="subdelete" style="visibility:hidden;"><a href="#" >.:delete:.</a></li>
        <li id="submove" style="visibility:hidden;"><a href="#" >.:move:.</a></li>
        <li id="subedit" style="visibility:hidden;"><a href="#" >.:edit attribute:.</a></li>
        <li id="lock" style="visibility:hidden;"><a href="#" >.:lock file:.</a></li>
        <li id="picup" style="visibility:hidden;"><a href="#" >.:picup:.</a></li>
    </ul><br>
        <span id="fbright"></span>
    </div>
</span>


<!-- Neues Verzeichnis erstellen: -->
<div id="fixiert" style="visibility:hidden; position:absolute; left:5em; width:20em; height:6em; z-index:1; top:10em; 
	text-align:center; border:3px solid black; background-image: url('css/fade.png');  " class="klein">
	<table width="99%" class="klein lg">
	<tr style="border-bottom:1px solid black;"><td>.:Create a new Directory:.</td><td align="right"><a href="javascript:newDir()">(X)</a></td></tr>
	</table>
	<br>
	<input type="hidden" name="seite" id="seite">
	<input type="text" name="subdir" id="subdir" size="20"> <input type="button" name="sdok" value=".:create:." onClick="mkDir();">
</div>

<!-- Eine Datei hochladen: -->
<div id="uploadfr" style="visibility:hidden; position:absolute; left:4em; z-index:1; top:10em; height:16em; width:20em; border:3px solid black; "  >
    <iframe id="frupload" name="frupload" src="upload.php?fid=0&pid=0" frameborder="0" width="100%" height="100%"></iframe>
</div>

<!-- Attribute editieren: -->
<div id="attribut" style="visibility:hidden; position:absolute; left:5em; z-index:1; top:10em; width:23em; 
	text-align:center; border:3px solid black; background-image: url('css/fade.png');" class="klein" >
	<table width="99%" class="klein lg">
	<tr style="border-bottom:1px solid black;"><td>.:edit attribute:.</td><td align="right"><a href="javascript:editattribut()">(X)</a></td></tr>
	</table>
	<input type="hidden" name="docid" id="docid" value="">
	<input type="hidden" name="docoldname" id="docoldname" value="">
	<input type="hidden" name="docpfad" id="docpfad" value="">
	<center>
	<table >
	<tr><td class="klein"><textarea name="docdescript" id="docdescript" cols="34" rows="4"></textarea></td></tr>
	<tr><td class="mini">.:Description:.</td></tr>
	<tr><td class="klein"><input type="text" name="docname" id="docname" size="35" value=""></td></tr>
	<tr><td class="mini">.:Filename:.</td></tr>
	<tr><td class="re"><input type="button" name="saveAtr" value=".:save:." onClick="saveAttribut();"></td></tr>
	</table>
	</center>
</div>

<!-- Eine Datei löschen: -->
<div id="fileDel" style="visibility:hidden; position:absolute; left:4em; z-index:1; top:10em; height:16em; width:23em; border:3px solid black; 
	text-align:center;  background-image: url('css/fade.png');" class="klein" >
	<table width="99%" class="klein lg">
	<tr style="border-bottom:1px solid black;"><td>.:Delete a File:.</td><td align="right"><a href="javascript:deletefile()">(X)</a></td></tr>
	</table>
	<h4 id="delfilename"></h4>
	<a href="javascript:filedelete();"><img src="image/eraser.png" border="0">.:Really:.</a><br \><br \>
	<a href="javascript:deletefile();"><img src="image/fileclose.png" border="0">.:Better not:.</a>
</div>
	
<!-- Hier endet die Karte ------------------------------------------->
</span>
</body>
</html>
