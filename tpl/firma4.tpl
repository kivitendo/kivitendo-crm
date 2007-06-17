<!-- $Id$ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<link type="text/css" REL="stylesheet" HREF="css/tabcontent.css"></link>
	{AJAXJS}
	<script language="JavaScript">
	<!--
	function showD () {
		document.getElementById("subdelete").style.visibility="hidden";
		document.getElementById("subdownload").style.visibility="hidden";
		document.getElementById("subedit").style.visibility="hidden";
		document.getElementById("submove").style.visibility="hidden";
		sel=document.getElementById("vorlage").selectedIndex;
		id=document.getElementById("vorlage").options[sel].value;
		if (id>0) {
			if ("{PID}"=="") { pid=0; } else { pid="{PID}"; };
			xajax_getDocVorlage_(id,{FID},pid,"{Q}");
		}
	}
	function showV () {
		sel=document.getElementById("wv").selectedIndex;
		nr=document.getElementById("wv").options[sel].value;
		if (nr>0) {
			Frame=eval("parent.main_window");
			uri="vertrag3.php?vid=" + nr;
			Frame.location.href=uri;
		}
	}	
	function openfile() {
		sel=document.firma4.dateien.selectedIndex;
		filename=document.firma4.dateien.options[sel].value;
		open("dokumente/"+filename,"File");
	}
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
		id=	document.getElementById("docid").value;
		name=	document.getElementById("docname").value;
		pfad=	document.getElementById("docpfad").value;
		if (!id) id=0;
		xajax_delFile(id,name,pfad);
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
		document.getElementById("submove").style.visibility="visible";
	}
	function dateibaum(seite,start) {
		if(seite=="left") { pfadleft=start; }
		else { 
			pfadright=start; 
			document.getElementById("subdelete").style.visibility="hidden";
			document.getElementById("subdownload").style.visibility="hidden";
			document.getElementById("subedit").style.visibility="hidden";
			document.getElementById("submove").style.visibility="hidden";
		};
		xajax_showDir(seite,start);
		setTimeout("dateibaum('left',pfadleft)",100000) // 100sec
	}
	//-->
	</script>
<body onLoad="dateibaum('left','/{Q}{customernumber}/{PID}');">
<p class="listtop">Detailansicht {FAART}</p>
<form name="firma4" enctype='multipart/form-data' action="{action}" method="post">
<input type="hidden" name="pid" value="{PID}">
<input type="hidden" name="fid" value="{FID}">
<input type="hidden" name="Q" value="{Q}">
<div style="position:absolute; top:2.7em; left:1.2em;  width:42em;">
	<ul id="maintab" class="shadetabs">
	<li><a href="{Link1}">Kundendaten</a><li>
	<li><a href="{Link2}">Ansprechpartner</a></li>
	<li><a href="{Link3}">Ums&auml;tze</a></li>
	<li class="selected"><a href="{Link4}" id="aktuell">Dokumente</a></li>
	</ul>
</div>

<span style="position:absolute; left:1em; top:4.3em; width:99%; height:90%;">
<!-- Hier beginnt die Karte  ------------------------------------------->
<span style="float:left; width:40%; height:90%; text-align:center; padding:2px; border: 1px solid black; border-bottom: 0px;">
	<div style="float:left; width:100%; height:3.5em; text-align:left; border-bottom: 1px solid black;" >
	<table>
	<tr><td class="fett">{Name}</td><td></td></tr>
	<tr><td class="fett">{customernumber}</td><td> {PID}</td></tr>
	</table>
	</div>
	<div style="float:left; width:100%; text-align:left; border-bottom: 0px solid black;" >
	<ul id="submenu" class="subshadetabs">
		<li id="subnewfile"><a href="#" onClick="newFile('left')">Dokument hochladen</a></li>
		<li id="subnewfolder"><a href="#" onClick="newDir('left')">neues Verzeichnis</a></li>
		<li id="subrefresh"><a href="#" onClick="dateibaum('left',pfadleft)">neu Einlesen</a></li>
	</ul>
	<br>
	Aktueller Pfad: <span id="path"></span>
	<span id="fbleft"></span>
	</div>
</span>

<span style="float:left; width:58%; height:90%; text-align:left; border: 1px solid black; border-bottom: 0px; padding:2px; border-left:0px;">
	<div style="float:left; width:100%; height:3.5em; text-align:left; border-bottom: 1px solid black;" class="fett">
	<table>
	<tr><td>Dokumentvorlagen:</td><td>
	<select name="vorlage" id="vorlage" onChange="showD();" style="width:150px;">
		<option value=""></option>
<!-- BEGIN Liste -->
		<option value="{ID}">{Bezeichnung}</option>
<!-- END Liste -->
	</select></td></tr>
	<tr><td>Wartungsvertr&auml;ge:</td><td>
	<select name="wv" id="wv" onChange="showV();" style="width:150px;">
		<option value=""></option> 
<!-- BEGIN Vertrag -->
		<option value="{cid}">{vertrag}</option>
<!-- END Vertrag -->
	</select></td></tr>
	</table>
	</div>
	<div style="float:left; width:100%;  text-align:left; border-bottom: 0px solid black;" class="normal">
	<ul id="submenu2" class="subshadetabs">
		<li id="subfilebrowser"><a href="#" onClick="dateibaum('right',pfadleft)">Filebrowser</a></li>
		<li id="subdownload" style="visibility:hidden;"><a href="#" >download</a></li>
		<li id="subdelete" style="visibility:hidden;"><a href="#" >l&ouml;schen</a></li>
		<li id="submove" style="visibility:hidden;"><a href="#" >verschieben</a></li>
		<li id="subedit" style="visibility:hidden;"><a href="#" >Attribute bearbeiten</a></li>
	</ul><br>
	    <span id="fbright"></span>
	</div>
</span>
<div id="fixiert" style="visibility:hidden; position:absolute; left:5em; width:20em; height:6em; z-index:1; top:10em; 
	text-align:center; border:3px solid black; background-image: url('css/fade.png');  " class="klein">
	<table width="99%" class="klein lg">
	<tr style="border-bottom:1px solid black;"><td>Ein neues Verzeichnis anlegen</td><td align="right"><a href="javascript:newDir()">(X)</a></td></tr>
	</table>
	<br>
	<input type="hidden" name="seite" id="seite">
	<input type="text" name="subdir" id="subdir" size="20"> <input type="button" name="sdok" value="anlegen" onClick="mkDir();">
</div>
<div id="uploadfr" style="visibility:hidden; position:absolute; left:4em; z-index:1; top:10em; height:16em; width:20em; border:3px solid black; "  >
                <iframe id="frupload" name="frupload" src="upload.php?fid={FID}&pid={PID}" frameborder="0" width="100%" height="100%"></iframe>
</div>
<div id="attribut" style="visibility:hidden; position:absolute; left:5em; z-index:1; top:10em; width:23em; 
	text-align:center; border:3px solid black; background-image: url('css/fade.png');" class="klein" >
	<table width="99%" class="klein lg">
	<tr style="border-bottom:1px solid black;"><td>Attribute bearbeiten</td><td align="right"><a href="javascript:editattribut()">(X)</a></td></tr>
	</table>
	<input type="hidden" name="docid" id="docid" value="">
	<input type="hidden" name="docoldname" id="docoldname" value="">
	<input type="hidden" name="docpfad" id="docpfad" value="">
	<center>
	<table >
	<tr><td class="klein"><textarea name="docdescript" id="docdescript" cols="34" rows="4"></textarea></td></tr>
	<tr><td class="mini">Kommentar</td></tr>
	<tr><td class="klein"><input type="text" name="docname" id="docname" size="35" value=""></td></tr>
	<tr><td class="mini">Dateiname</td></tr>
	<tr><td class="re"><input type="button" name="saveAtr" value="sichern" onClick="saveAttribut();"></td></tr>
	</table>
	</center>
</div>
<div id="fileDel" style="visibility:hidden; position:absolute; left:4em; z-index:1; top:10em; height:16em; width:23em; border:3px solid black; 
	text-align:center;  background-image: url('css/fade.png');" class="klein" >
	<table width="99%" class="klein lg">
	<tr style="border-bottom:1px solid black;"><td>Eine Datei l&ouml;schen</td><td align="right"><a href="javascript:deletefile()">(X)</a></td></tr>
	</table>
	<h4 id="delfilename"></h4>
	<a href="javascript:filedelete();"><img src="image/eraser.png" border="0">Wirklich l&ouml;schen.</a><br \><br \>
	<a href="javascript:deletefile();"><img src="image/fileclose.png" border="0">Nee - lieber nicht</a>
</div>
	
<!-- Hier endet die Karte ------------------------------------------->
</span>
</body>
</html>
