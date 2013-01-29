<?php
    require_once("../inc/stdLib.php");
    include("FirmenLib.php");
    include("persLib.php");
    include("crmLib.php");
    require_once("documents.php");

    function showDir($id,$directory) {
         if ($directory != '/') 
            $directory = trim( rtrim( $directory, " /\\" ) ); //entferne rechts Leezeichen und Slash bzw Backslash
        chkdir($directory,".");
        if ( is_dir("../dokumente/".$_SESSION["mansel"]."/".$directory)) {
            $dir_object = dir( "../dokumente/".$_SESSION["mansel"]."/".$directory );
            // Gibt neues Verzeichnis aus
            $inhalt="<ul>";
            $dir="<li class='ptr' onClick='dateibaum(\"$id\",\"%s\")'>%s";
            $datei="<li class='ptr' onClick='showFile(\"$id\",\"%s\")'>%s";
            clearstatcache();
            $Eintrag = array();
            while ( false !== ( $entry = $dir_object->read() ) ) {
                    // '.' interessiert nicht
                    if ( $entry !== '.' ) {
                        if ($entry === '..' ) {
                            if ($directory=="/" || $directory=="") {
                                continue;
                            } else {
                                $tmp=substr($directory,0,strrpos($directory,"/"));
                                $Eintrag[]=sprintf($dir,$tmp,"[ .. ]");
                            }
                        } else if (is_dir("../dokumente/".$_SESSION["mansel"]."/".$directory."/".$entry)) {
                            $Eintrag[]=sprintf($dir,$directory."/".$entry,"[ $entry ]");
                        } else {
                            $Eintrag[]=sprintf($datei,$entry,"$entry");
                        }
                    }
            }
            sort($Eintrag);
            $inhalt.=join("",$Eintrag);
            $inhalt.="</ul>";
            $dir_object->close();
        }
        $objResponse = new xajaxResponse();
        $objResponse->assign("fb$id",    "innerHTML", $inhalt);
        $objResponse->assign("path",     "innerHTML", ($directory)?$directory:"/");
        return $objResponse;

    }
    function showFile($pfad,$file) {
        if (substr($pfad,-1)=="/" and $pfad != "/") $pfad=substr($pfad,0,-1);
        if (substr($pfad,0,2) == "//" ) $pfad = substr($pfad,1);
        clearstatcache();
        $zeit=date("d.m.Y H:i:s",filemtime("../dokumente/".$_SESSION["mansel"]."/$pfad/$file"));
        $size=filesize("../dokumente/".$_SESSION["mansel"]."/$pfad/$file");
        $ext=strtoupper(substr($file,strrpos($file,".")+1));
        $pic="file.gif";
             if ($ext=="PDF") { $type="PDF-File"; $pic="pdf.png"; }
        else if (in_array($ext,array("ODT","ODF","SXW","STW","WPD","DOC","TXT","RTF","LWP","WPS"))) { $type="Textdokument"; $pic="text.png";}
        else if (in_array($ext,array("ODS","SXC","STC","VOR","XLS","CSV","123"))) { $type="Tabellendokument"; $pic="calc.png"; }
        else if (in_array($ext,array("ODP","SXI","SDP","POT","PPS"))) { $type="Pr&auml;sentation"; $pic="praesent.png";}
        else if (in_array($ext,array("ODG","SXD","SDA","SVG","SDD","DXF"))) { $type="Zeichnungen"; $pic="zeichng.png";}
        else if (in_array($ext,array("HTM","HTML","STW","SSI","OTH"))) { $type="Webseiten"; $pic="web.png"; }
        else if (in_array($ext,array("DBF","ODB"))) { $type="Datenbank"; $pic="db.png";}
        else if (in_array($ext,array("PS", "EPS"))) { $type="Postscript"; $pic="ps.png";}
        else if (in_array($ext,array("GZ", "TGZ","BZ","ZIP","TBZ"))) { $type="Komprimiert"; $pic="zip.png"; }
        else if (in_array($ext,array("MP3","OGG","WAV"))) { $type="Audiodatei"; $pic="sound.png";}
        else if (in_array($ext,array("BMP","GIF","JPG","JPEG","PNG","TIF","PGM","PPM","PCX","PSD","TIFF"))) { $type="Grafik-Datei"; $pic="grafik.png";}
        else if (in_array($ext,array("WMF","MOV","AVI","VOB","MPG","MPEG","WMV","RM"))) { $type="Video-Datei"; $pic="video.png";}
        else if ($ext=="XML") { $type="XML-Datei"; $pic="xml.png";}
        else if (in_array($ext,array("SH","BAT"))) { $type="Shell-Script"; $pic="exe.png";}
        else { $type="Unbekannt"; $pic="foo.png"; };
        $info ="<br>$pfad".(($pfad == "/")?'':'/')."<b>$file</b><br><br>";
        $info.=translate('.:filetyp:.','firma').": <img src='image/icon/$pic'> $type<br>";
        $info.=translate('.:filesize:.','firma').": $size<br>".translate('.:filetime:.','firma').": $zeit<br>";
        $dbfile=new document();
        $rs=$dbfile->searchDocument($file,$pfad);
        $id=0;
        if ($rs) {
            $rs=$dbfile->getDokument($rs);
            if ($rs["lock"]>0) $info.="<br /><font color='red'>".translate('.:locked:.','firma')." : ".$rs["lockname"]."</font><br />";
            $info.="<br>".translate('.:Description update:.','firma').": ".db2date($rs["datum"])." ".$rs["zeit"]."<br>";
            $info.=translate('.:Description:.','firma').": ".nl2br($rs["descript"])."<br>";
            $id=$rs["id"];
        }
        $objResponse = new xajaxResponse();
        $objResponse->assign("docname",    "value", $file);
        $objResponse->assign("docoldname",    "value", $file);
        $objResponse->assign("docpfad",    "value", $pfad);
        $objResponse->assign("docid",    "value", $id);
        $objResponse->assign("docdescript",    "value", $rs["descript"]);
        $objResponse->assign("fbright",     "innerHTML", $info);
        $objResponse->assign("subdownload",    "innerHTML",
                "<a href='#' onClick='download(\"dokumente/".$_SESSION["mansel"]."$pfad/$file\")'>".translate('.:download:.','firma')."</a>");
        $objResponse->assign("subdelete",    "innerHTML",
                "<a href='#' onClick='deletefile(\"dokumente/".$_SESSION["mansel"]."$pfad/$file\",$id)'>".translate('.:delete:.','firma')."</a>");
        $objResponse->assign("submove",    "innerHTML",
                "<a href='#' onClick='movefile(\"dokumente/".$_SESSION["mansel"]."$pfad/$file\",$id)'>".translate('.:move:.','firma')."</a>");
        $objResponse->assign("subedit",    "innerHTML",
                "<a href='#' onClick='editattribut($id)'>".translate('.:edit attribute:.','firma')."</a>");
        $objResponse->assign("lock",    "innerHTML",
                "<a href='#' onClick='xajax_lockFile(\"$file\",\"$pfad\",$id)'>".translate('.:lock file:.','firma')."</a>");
        return $objResponse;
    }
    /**
     * TODO: short description.
     * 
     * @param mixed $file 
     * @param mixed $path 
     * @param int   $id   Optional, defaults to 0. 
     * 
     * @return TODO
     */
    function lockFile($file,$path,$id=0) {
        $dbfile=new document();
        if ($id==0) {
            $id=$dbfile->searchDocument($file,$path);
            if ($id) {
                $rs=$dbfile->getDokument($id);
            } else {
                $dbfile->setDocData("pfad",$path);
                $dbfile->setDocData("name",$file);
            }
        } else {
            $rs=$dbfile->getDokument($id);
        }
        if ($dbfile->lock>0) {
            if ($dbfile->lock==$_SESSION["loginCRM"])
                $dbfile->setDocData("lock",0);
        } else {
            $dbfile->setDocData("lock",$_SESSION["loginCRM"]);
        }
        $rc=$dbfile->saveDocument();
        $objResponse = new xajaxResponse();
        $objResponse->script("showFile('left','$file')");
        return $objResponse;
    }

    function moveFile($file,$pfadleft) {
        $objResponse = new xajaxResponse();
        $oldpath=substr($file,0,strrpos($file,"/"));
        $file=substr($file,strrpos($file,"/")+1);
        if ($oldpath<>$pfadleft) {
            $pre="../dokumente/".$_SESSION["mansel"];
            $dbfile=new document();
            $tmp = explode("/",$oldpath);
            $opath = "/".implode("/",array_slice($tmp,2));
            $id=$dbfile->searchDocument($file,$opath);
            if ($id) {
                $rs=$dbfile->getDokument($id);
                if ($dbfile->lock>0) {
                    return;
                }
                $dbfile->setDocData("pfad",$pfadleft);
                $rc=$dbfile->saveDocument();
            }
            rename("../".$oldpath."/".$file,$pre.$pfadleft."/".$file);
        }
        if ($file[0]=="/") $file=substr($file,1);
        $objResponse->script("dateibaum('left','$pfadleft')");
        $objResponse->script("showFile('left','$file')");
        return $objResponse;
    };
    function saveAttribut($name,$oldname,$pfad,$komment,$id=0) {
        $dbfile=new document();
        if ($id>0) {
            $rc=$dbfile->getDokument($id);
            if ($dbfile->lock>0) {
                $objResponse = new xajaxResponse();
                $objResponse->script("showFile('left','$oldname')");
                $objResponse->script("editattribut()");
                return $objResponse;
            }
        } else {
            $dbfile->setDocData("pfad",$pfad);
        };
        if ($oldname<>$name) {
            $path="../dokumente/".$_SESSION["mansel"]."$pfad/";
            $dbfile->setDocData("name",$name);
            rename($path.$oldname,$path.$name);
            $oldname=$name;
        } else {
            $dbfile->setDocData("name",$oldname);
        }
        $dbfile->setDocData("descript",$komment);
        $rc=$dbfile->saveDocument();
        $objResponse = new xajaxResponse();
        if ($rc) {
            /*if (validDate($wvdate)) {
                $data["DateiID"]=$dbfile->id;
                $data["Finish"]=$wvdate;
                $data["LangTxt"]=$komment;
                $data["Cause"]=$name;
                $data["Kontakt"]='D';
                $data["cp_cv_id"]=$faid;
                $data["WVLID"]=$wvid;
                $data["status"]='1';
                if ($wvid>0) { $ok=updWvl($data,false); }
                else { $ok=insWvl($data,false); };
            }*/
            //$objResponse->script("dateibaum('left','$pfad');showFile('left','$name');editattribut();");
            $objResponse->script("dateibaum('left','$pfad')");
            $objResponse->script("showFile('left','$oldname')");
            $objResponse->script("editattribut()");
        } else {
            $objResponse->script("alert('Fehler beim Sichern')");
        }
        return $objResponse;
    }
    function newDir($id,$pfad,$newdir) {
        chdir("../dokumente/".$_SESSION["mansel"]."/$pfad");
        mkdir($newdir);
        chmod($newdir,$GLOBALS['dir_mode']);
        chgrp($newdir,$GLOBALS['dir_group']);
        return new xajaxResponse();
    }
    function delFile($id=0,$pfad="",$file="") {
        $dbfile=new document();
        if ($id>0) {
            $dbfile->getDokument($id);
            if ($dbfile->lock>0) {
                $objResponse = new xajaxResponse();
                $objResponse->script("showFile('left','$file')");
                return $objResponse;
            }
        } else {
            $dbfile->setDocData("name",$file);
            $dbfile->setDocData("pfad",$pfad);
        }
        $dbfile->deleteDocument(".");
        $objResponse = new xajaxResponse();
        return $objResponse;
    }
    function getDocVorlage_($did,$fid=0,$pid=0,$tab="C") {
        $inhalt="<div id='iframe2'>";
        $inhalt.="        <iframe id='newdoc' width='100%' height='100%' name='newdoc' src='firma4a.php?did=$did&fid=$fid&tab=$tab&pid=$pid' frameborder='0'></iframe>";
        $inhalt.="</div>";
        $objResponse = new xajaxResponse();
        $objResponse->assign("fbright",     "innerHTML", $inhalt);
        return $objResponse;
    }
    function getDocVorlage__($did,$fid=0,$pid=0,$tab="C") {
        $document=getDOCvorlage($did);
        if ($pid>0) {
            $co=getKontaktStamm($pid);
            if ($data["cp_gender"]=='m') { $anredepers=translate('.:greetmale:.','firma'); 
            } else { $anredepers=translate('.:greetfemale:.','firma'); };
            $anredepers.=($co["cp_title"])?" ".$co["cp_title"]:"";
            $namepers=$co["cp_givenname"]." ".$co["cp_name"];
            $plzpers=$co["cp_zipcode"];
            $ortpers=$co["cp_city"];
            $strassepers=$co["cp_street"];
            if (!$co["cp_cv_id"]) $art="Einzelperson";
        };
        if ($fid>0) {
            $fa=getFirmenStamm($fid,$tab);
            $anrede=$fa["greeting"];
            $name=$fa["name"];
            $name1=$name;
            $name2=$fa["department_1"];
            $kontakt=$fa["contact"];
            $plz=$fa["zipcode"];
            $ort=$fa["city"];
            $strasse=$fa["street"];
            if ($pid>0) { $art="Firma/Kontakt"; } else { $art="Firmendokumente"; };
        };
        $datum=date("d.m.Y");
        $zeit=date("H:i");
        $input="<iframe width='100%'><html><body><form name='' action='' method='post'><table>\n";
        if ($document["felder"]) {
             foreach($document["felder"] as $zeile) {
                $value=strtolower($zeile["platzhalter"]);
                $input.="<tr><td>".$zeile["feldname"]."</td><td>";
                if ($zeile["laenge"]>60) {
                    $rows=floor($zeile["laenge"]/60)+1;
                    $input.="<textarea cols=60 rows=$rows name='".$zeile["platzhalter"]."'>".${$value}."</textarea>";
                } else {
                    $input.="<input type='text' name='".$zeile["platzhalter"]."' size='".$zeile["laenge"]."' value='".${$value}."'>";
                }
                $input.="</td></tr>\n";
                $i++;
             }
        };
        $input.="</table><input type='submit' name='send' value='erzeugen'></form></body></html>";
        $objResponse = new xajaxResponse();
        $objResponse->assign("fbright",     "innerHTML", $input);
        return $objResponse;
    }
    require("crmajax/firmacommon".XajaxVer.".php");
    $xajax->processRequest();

?>
