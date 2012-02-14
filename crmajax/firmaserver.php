<?php
    require_once("../inc/stdLib.php");
    include("FirmenLib.php");
    include("persLib.php");
    include("crmLib.php");
    require_once("documents.php");

    function getCustomTermin($id,$tab,$day) {
        $termine = getCustTermin($id,$tab,$day);
        if ($termine)  {
            foreach ($termine as $term) {
               $inhalt .= "<span onClick='getCall(".$term["cid"].")'>";
               $inhalt .= db2date(substr($term["start"],0,10))." ".$term["cause"].":".$term["cp_name"]."<br /></span>";
            }
        } else {
            $inhalt = "Keine Termine";
        };
        $objResponse = new xajaxResponse();
        $objResponse->assign("termin-container", "innerHTML", $inhalt);
        return $objResponse;
    }
    function getShipto($id) {
        $data=getShipStamm($id);
        $objResponse = new xajaxResponse();
        $objResponse->assign("shiptoname",        "value", $data["shiptoname"]);
        $objResponse->assign("shiptodepartment_1","value", $data["shiptodepartment_1"]);
        $objResponse->assign("shiptodepartment_2","value", $data["shiptodepartment_2"]);
        $objResponse->assign("shiptostreet",      "value", $data["shiptostreet"]);
        $objResponse->assign("shiptocity",        "value", $data["shiptocity"]);
        $objResponse->assign("shiptocontact",     "value", $data["shiptocontact"]);
        $objResponse->assign("shiptocountry",     "value", $data["shiptocountry"]);
        $objResponse->assign("shiptophone",       "value", $data["shiptophone"]);
        $objResponse->assign("shiptofax",         "value", $data["shiptofax"]);
        $objResponse->assign("shiptoemail",       "value", $data["shiptoemail"]);
        $objResponse->assign("shiptozipcode",     "value", $data["shiptozipcode"]);
        $objResponse->assign("shipto_id",         "value", $data["shipto_id"]);
        $objResponse->assign("module",            "value", $data["module"]);
        $objResponse->assign("shiptobland",       "value", $data["shiptobland"]);
        return $objResponse;
    }
    function Buland($land,$bl) {
        $data=getBundesland(strtoupper($land));
        $objResponse = new XajaxResponse();
        $sScript = "var i = document.getElementById('".$bl."').length;";
        $sScript.= "while ( i > 0) {";
        $sScript.= "document.getElementById('".$bl."').options[i-1]=null;";
        $sScript.= "i--;}";
        $objResponse->script($sScript);
        if (preg_match("/UTF-8/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="UTF-8"; }
        else if (preg_match("/ISO-8859-15/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-15"; }
        else if (preg_match("/ISO-8859-1/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-1"; }
        else { $charset="UTF-8"; };
        $sScript = "var objOption = new Option('', '');";
        $sScript .= "document.getElementById('".$bl."').options.add(objOption);";
        foreach ($data as $row) {
            $sScript .= "var objOption = new Option('".html_entity_decode($row["bundesland"],ENT_NOQUOTES,$charset)."', '".$row["id"]."');";
            $sScript .= "document.getElementById('".$bl."').options.add(objOption);";
        }
        $objResponse->script($sScript);
        return $objResponse;
    }
    function showShipadress($id,$tab){
        $data=getShipStamm($id);
        if (preg_match("/UTF-8/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="UTF-8"; }
        else if (preg_match("/ISO-8859-15/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-15"; }
        else if (preg_match("/ISO-8859-1/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-1"; }
        else { $charset="UTF-8"; };
        $maillink="<a href='mail.php?TO=".$data["shiptoemail"]."&KontaktTO=$tab".$data["trans_id"]."'>".$data["shiptoemail"]."</a>";
        $htmllink="<a href='".$data["shiptohomepage"]."' target='_blank'>".$data["shiptohomepage"]."</a>";
        $objResponse = new xajaxResponse();
        $objResponse->assign("SID",                  "innerHTML", $id);
        $objResponse->assign("shiptoname",           "innerHTML", htmlentities($data["shiptoname"],ENT_NOQUOTES,$charset));
        $objResponse->assign("shiptodepartment_1",   "innerHTML", htmlentities($data["shiptodepartment_1"],ENT_NOQUOTES,$charset));
        $objResponse->assign("shiptodepartment_2",   "innerHTML", htmlentities($data["shiptodepartment_2"],ENT_NOQUOTES,$charset));
        $objResponse->assign("shiptostreet",         "innerHTML", htmlentities($data["shiptostreet"],ENT_NOQUOTES,$charset));
        $objResponse->assign("shiptocountry",        "innerHTML", $data["shiptocountry"]);
        $objResponse->assign("shiptobland",          "innerHTML", html_entity_decode($data["shiptobundesland"],ENT_NOQUOTES,$charset));
        $objResponse->assign("shiptozipcode",        "innerHTML", $data["shiptozipcode"]);
        $objResponse->assign("shiptocity",           "innerHTML", htmlentities($data["shiptocity"],ENT_NOQUOTES,$charset));
        $objResponse->assign("shiptocontact",        "innerHTML", $data["shiptocontact"]);
        $objResponse->assign("shiptophone",          "innerHTML", $data["shiptophone"]);
        $objResponse->assign("shiptofax",            "innerHTML", $data["shiptofax"]);
        $objResponse->assign("shiptocontact",        "innerHTML", $data["shiptocontact"]);
        $objResponse->assign("shiptoemail",          "innerHTML", $maillink);
        $objResponse->assign("shiptohomepage",       "innerHTML", $htmllink);
        return $objResponse;
    }
    function showContactadress($id){
        $data=getKontaktStamm($id,".");
        if (preg_match("/UTF-8/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="UTF-8"; }
        else if (preg_match("/ISO-8859-15/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-15"; }
        else if (preg_match("/ISO-8859-1/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-1"; }
        else { $charset="UTF-8"; };
        $root="dokumente/".$_SESSION["mansel"]."/".$data["tabelle"].$data["nummer"]."/".$data["cp_id"];
        if (!empty($data["cp_grafik"]) && $data["cp_grafik"]<>"     ") {
            $img="<img src='$root/kopf$id.".$data["cp_grafik"]."' ".$data["icon"]." border='0'>";
            $data["cp_grafik"]="<a href='$root/kopf$id.".$data["cp_grafik"]."' target='_blank'>$img</a>";
        };
        $data["extraF"] = '<a href="extrafelder.php?owner=P'.$id.'" target="_blank" title="'.translate('.:extra data:.','firma').'"><img src="image/extra.png" alt="Extras" border="0" /></a>';
        $tmp=glob("../$root/vcard".$data["cp_id"].".*");
        $data["cp_vcard"]="";
        $objResponse = new xajaxResponse();
        if ($tmp)  foreach ($tmp as $vcard) {
            $ext=explode(".",$vcard);
            $ext=strtolower($ext[count($ext)-1]);
            if (in_array($ext,array("jpg","jpeg","gif","png","pdf","ps"))) {
                $data["cp_vcard"]="<a href='$root/vcard$id.$ext' target='_blank'>Visitenkarte</a>";
                //$data["cp_vcard"]="<a href='$vcard' target='_blank'>Visitenkarte</a>";
                break;
            }
        }
        $data["cp_email"]="<a href='mail.php?TO=".$data["cp_email"]."&KontaktTO=P".$data["cp_id"]."'>".$data["cp_email"]."</a>";
        $data["cp_homepage"]="<a href='".$data["cp_homepage"]."' target='_blank'>".$data["cp_homepage"]."</a>";
        if (strpos($data["cp_birthday"],"-")) {
            $data["cp_birthday"]=db2date($data["cp_birthday"]);
        }
         if ($data["cp_gender"]=='m') { $data["cp_greeting"]=translate('.:greetmale:.','firma'); 
         } else { $data["cp_greeting"]=translate('.:greetfemale:.','firma'); };
        if ($data["cp_phone2"]) $data["cp_phone2"]="(".$data["cp_phone2"].")";
        if ($data["cp_privatphone"]) $data["cp_privatphone"]="Privat: ".$data["cp_privatphone"];
        if ($data["cp_mobile2"]) $data["cp_mobile2"]="(".$data["cp_mobile2"].")";
        if ($data["cp_privatemail"]) $data["cp_privatemail"]="Privat: <a href='mail.php?TO=".$data["cp_privatemail"]."&KontaktTO=P".$data["cp_id"]."'>".$data["cp_privatemail"]."</a>";;
        $nocodec = array("cp_email","cp_homepage","cp_zipcode","cp_birthday","cp_grafik","cp_privatemail","cp_vcard","extraF");
        foreach ($data as $key=>$val) {
            if (in_array($key,$nocodec)) {
                        $objResponse->assign($key,            "innerHTML", $val);
            } else {
                        $objResponse->assign($key,            "innerHTML", htmlentities($val,ENT_NOQUOTES,$charset));
            }
        }
        $objResponse->assign("cp_id",     "value", $data["cp_id"]);
        if ($data["cp_phone1"] || $data["cp_phone2"]) {
                $objResponse->script("document.getElementById('phone').style.visibility='visible'");
        } else {
                $objResponse->script("document.getElementById('phone').style.visibility='hidden'");
        }
        if ($data["cp_mobile1"] || $data["cp_mobile2"]) {
                $objResponse->script("document.getElementById('mobile').style.visibility='visible'");
        } else {
                $objResponse->script("document.getElementById('mobile').style.visibility='hidden'");
        }
        if ($data["cp_fax"]) {
                $objResponse->script("document.getElementById('fax').style.visibility='visible'");
        } else {
                $objResponse->script("document.getElementById('fax').style.visibility='hidden'");
        }
        $objResponse->script("document.getElementById('cpinhalt2').style.visibility='visible'");
        $objResponse->script("document.getElementById('cpbrief').style.visibility='visible'");
                return $objResponse;
    }
    function showCalls($id,$start,$fa=false) {
        if (preg_match("/UTF-8/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="UTF-8"; }
        else if (preg_match("/ISO-8859-15/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-15"; }
        else if (preg_match("/ISO-8859-1/i",$_SERVER["HTTP_ACCEPT_CHARSET"])) { $charset="ISO-8859-1"; }
        else { $charset="UTF-8"; };
        $i=0;
        $nun=date("Y-m-d h:i");
        $itemN[]=array(id => 0,calldate => $nun, caller_id => $employee, cause => translate('.:newItem:.','firma') );
        $zeile ="<tr class='calls%d' onClick='showItem(%d);'>";
        $zeile.="<td class='calls' nowrap width='15%%'>%s %s</td>";
        $zeile.="<td class='calls re' width='6%%'>%s%s&nbsp;</td>";
        $zeile.="<td class='calls le' width='54%%'>%s</td>";
        $zeile.="<td class='calls le' width='15%%'>%s</td></tr>\n";
        $items=getAllTelCall($id,$fa,$start);
        if ($items) {
            $item=array_merge($itemN,$items);
        } else {
            $item=$itemN;
        }
        $tmp="<table class='calls' width='99%'>";
        //$tmp="";
        if ($item) foreach($item as $col){
            if ($col["new"]) { $cause="<b>".htmlentities($col["cause"],ENT_NOQUOTES,$charset)."</b>"; }
            else { $cause=htmlentities($col["cause"],ENT_NOQUOTES,$charset); }
            if ($col["inout"]=="i") { $inout="<"; }
            else if ($col["inout"]=="o") { $inout=">"; }
            else { $inout="-"; } 
            $tmp.=sprintf($zeile,$i,$col["id"],db2date(substr($col["calldate"],0,10)),substr($col["calldate"],11,5),
                        $col["id"],$inout,$cause,htmlentities($col["cp_name"],ENT_NOQUOTES,$charset));
            $i=($i==1)?0:1;
        }
        $tmp.="</table>";
        $objResponse = new xajaxResponse();
        $objResponse->assign("tellcalls",     "innerHTML", $tmp);
        if ($start==0) {
            $max=getAllTelCallMax($id,$firma);
            $objResponse->script("max = $max;");
        }
        $objResponse->script("document.getElementById('threadtool').style.visibility='visible'");
        return $objResponse;
    }
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
        $objResponse->assign("fb$id",     "innerHTML", $inhalt);
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
        chmod($newdir,$dir_mode);
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
    function editTevent($id) {
        $data = getOneTevent($id);
        $objResponse = new xajaxResponse();
        $objResponse->assign("cleared",        "value", $data["cleared"] );
        $objResponse->assign("ttevent",        "value", $data["ttevent"]);
        $objResponse->assign("eventid",        "value", $id);
        $a = explode(" ",$data["ttstart"]);
        $objResponse->assign("startd",         "value", db2date($a[0]));
        $objResponse->assign("startt",         "value", substr($a[1],0,5));
        if ($data["ttstop"]) {
            $b = explode(" ",$data["ttstop"]);
            $sd = db2date($b[0]);
            $st = substr($b[1],0,5);
        } else {
            $sd = "";
            $st = "";
        }
        $objResponse->assign("stopd",          "value", $sd);
        $objResponse->assign("stopt",          "value", $st);
        if ( $data['cleared'] > 0 )	$objResponse->script("document.getElementById('savett').style.visibility='hidden'");
	else				$objResponse->script("document.getElementById('savett').style.visibility='visible'");
        return $objResponse;
    }
    function listTevents($id) {
        $events = getTTEvents($id,"a",false);
        $objResponse = new xajaxResponse();
        if (!$events) return  $objResponse;;
        $tt = getOneTT($events[0]["ttid"]);
        $liste = "<form name='cleared' method='post' action='timetrack.php'>";
        $liste.= "<input type='hidden' name='tid' value='".$events[0]["ttid"]."'>";
        $liste.= "<table>";
        $i = 0;
        $now = time();
        $diff = 0;
        if ($events) foreach ($events as $row) {
            $a = explode(" ",$row["ttstart"]);
            $t1 = strtotime($row["ttstart"]);
            if ($row["ttstop"]) {
                $b = explode(" ",$row["ttstop"]);   
                $stop = db2date($b[0])." ".substr($b[1],0,5);
                $t2 = strtotime($row["ttstop"]);
            } else {
                $t2 = $now;
                $stop = "<a href='timetrack.php?tid=".$row["ttid"]."&eventid=".$row["id"]."&stop=now'><b>".translate('.:stop now:.','work')."</b></a>";
            };
	    $min = $t2 - $t1;
            $diff += $min;
            $i++; 
            if ($row["cleared"] > 0) {
                $clear = "<td>-</td>";
            } else {
                if ($row["uid"] == $_SESSION["loginCRM"]) {
                    $clear = "<td><input type='checkbox' name='clear[]' value='".$row["id"]."'></td>";
                } else {
                    $clear = "<td>+</td>";
                };
            }
            $liste .= "<tr class='calls".($i%2)."' onClick='editrow(".$row["id"].");'>$clear<td>".db2date($a[0])." ".substr($a[1],0,5)."</td>";
            $liste .= "<td>".$stop."</td><td align='right'>".floor($min/60)."</td><td>".$row["user"]."</td><td>";
            if (strlen($row["ttevent"])>40) {
                $liste .= substr($row["ttevent"],0,40)."...</td><td>";
            } else {
                $liste .= $row["ttevent"]."</td><td>";
            }
	    $liste .= $row["ordnumber"]."</td></tr>";
        };
        $diff = floor($diff / 60);
        if ($tt["aim"]>0) {
            $rest = $tt["aim"] * 60 - $diff;
            $rstd = floor($rest/60);
            $rmin = ($rest%60);
            $rest = translate('.:remain:.','work')." $rstd:$rmin ".translate('.:hours:.','work');
        } else {
            $rest = "";
        }
        if ($diff>60) {
            $min = sprintf("%02d",($diff % 60));
            $std = floor($diff/60);
            $use = "$std:$min ".translate('.:hours:.','work');
        } else {    
            $use = $diff." ".translate('.:minutes:.','work');
        }
        $liste .= "</table><input type='checkbox' name='clrok' value='1'>".translate(".:all:.",'work')." ";
	$liste .= "<input type='submit' name='clr' value='".translate(".:clearing:.","work")."'></form>";
        $objResponse->assign("summtime",       "innerHTML", translate(".:used:.","work")." $use $rest");
        $objResponse->assign("eventliste",     "innerHTML", $liste);
        return $objResponse;
    }
    require("crmajax/firmacommon".XajaxVer.".php");
    $xajax->processRequest();

?>
