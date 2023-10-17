<?php
/****************************************************
* chkTable
* in: fid = int
* out: file = string
* ist das ein Kunde oder Lieferant
*****************************************************/
function chkTable($fid){
    $file="C";
    $sql="select count(*) from customer where id=$fid";
    $row=$GLOBALS['dbh']->getAll($sql);
    if ($row[0]["count"]<1) {
        $sql="select count(*) from vendor where id=$fid";
        $row=$GLOBALS['dbh']->getAll($sql);
        if ($row[0]["count"]==1) $file="V";
    }
    return $file;
};


/****************************************************
* getKontaktStamm
* in: id = int
* out: daten = array
* Stammdaten einer Person holen
* !! noch in eine andere Lib auslagern
* !! da auch von Lieferant und Person gebraucht wird
*****************************************************/
function getKontaktStamm($id,$pfad="") {
    $sql="select C.*,E.login from contacts C left join employee E on C.cp_employee=E.id where C.cp_id=$id";
    $rs=$GLOBALS['dbh']->getAll($sql);
    if(!$rs) {
        $daten=false;
    } else {
        $firma="Einzelperson";
        $tab="";
        $cnr="";
        if (!empty($rs[0]["cp_cv_id"])) {  // gehört zu einem Kunden oder Lieferanten
            $sql="select id,name,department_1,customernumber,language_id from customer where id=".$rs[0]["cp_cv_id"];
            $rs1=$GLOBALS['dbh']->getAll($sql);
            if (empty($rs1[0]["name"])) {  // nicht zu Kunde sondern zu Lieferant
                $sql="select id,name,department_1,vendornumber,language_id from vendor   where id=".$rs[0]["cp_cv_id"];
                $rs1=$GLOBALS['dbh']->getAll($sql);
                $tab="V";
                $cnr=$rs1[0]["vendornumber"];
                $firma=$rs1[0]["name"];
                $language_id=$rs1[0]["language_id"];
            } else {
                $tab="C";
                $cnr=$rs1[0]["customernumber"];
                $firma=$rs1[0]["name"];
                $language_id=$rs1[0]["language_id"];
            }
        }
        $daten=$rs[0];
        if ($daten["cp_grafik"]) {
            $image="$pfad./dokumente/".$_SESSION["dbname"]."/$tab$cnr/$id/kopf$id.".$daten["cp_grafik"];
            clearstatcache();
            if (file_exists($image)) {
                $size=@getimagesize($image);
                $daten["size"]=$size[3];
                if ($size[1]>$size[0]) {
                    $faktor=ceil($size[1]/70);
                } else {
                    $faktor=ceil($size[0]/120);
                }
                $breite=floor($size[0]/$faktor);
                $hoehe=floor($size[1]/$faktor);
                $daten["icon"]="width=\"$breite\" height=\"$hoehe\"";
            } else {
                $daten["icon"]="width=\"75\" height=\"100\"";
            }
        }
        $daten["Firma"] = $firma;
        $daten["Department_1"]=$rs1[0]["department_1"];
        $daten["tabelle"] = $tab;
        $daten["nummer"]=$cnr;
        $daten["language_id"]=$language_id;
    }
    return $daten;
};

/****************************************************
* getAllPerson
* in: sw = array(Art,suchwort)
* out: rs = array(Felder der db)
* hole Liste der Kontaktpersonen
*****************************************************/
function getAllPerson($sw,$usePre=true) {
    if ($usePre) $Pre=$_SESSION["pre"];
    $rechte=berechtigung("cp_");
    if (!$sw[0]) { $where="cp_phone1 like '$Pre".$sw[1]."%' or cp_mobile1 like '$Pre".$sw[1]."%' "; }
    else { $where="cp_name ilike '$Pre".$sw[1]."%' or cp_givenname ilike '$Pre".$sw[1]."%' or cp_stichwort1 ilike '$Pre".$sw[1]."%'"; }
    $sql="select *,'P' as tab,cp_id as id,cp_name as name  from contacts where ($where) and $rechte";
    $rs=$GLOBALS['dbh']->getAll($sql);
    if($rs) return $rs;
    //Was geschieht wenn nach einer Person mit Vor- und Zuname gesucht wird??
    //Fall 1: Nachname wird zuerst eingeben "Byron Augusta Ada"
    $sw_array=explode(" ",$sw[1],9);
    if (!isset($sw_array[1])) return false;
    $name=array_shift($sw_array);
    $givenname=implode(" ",$sw_array);
    $where="cp_name ilike '$Pre".$name."%' and cp_givenname ilike '$Pre".$givenname."%'";
    $sql="select *,'P' as tab,cp_id as id,cp_name as name  from contacts where ($where) and $rechte";
    $rs=$GLOBALS['dbh']->getAll($sql);
    if ($rs) return $rs;
    //Fall 2: Vorname wird zuerst eingegeben "Augusta Ada Byron"
    $sw_array=explode(" ",$sw[1],9);
    $name=array_pop($sw_array);
    $givenname=implode(" ", $sw_array);
    $where="cp_name ilike '$Pre".$name."%' and cp_givenname ilike '$Pre".$givenname."%'";
    $sql="select *,'P' as tab,cp_id as id,cp_name as name  from contacts where ($where) and $rechte";
    $rs=$GLOBALS['dbh']->getAll($sql);
    if ($rs) return $rs;
    return false;
}

/****************************************************
* getAllKontakt
* in: id = int
* out: daten = array
* alle Kontakte eines Kunden/Lieferanten holen
*****************************************************/
function getAllKontakt($id) {
    $rechte=berechtigung("cp_");
    $sql="select * from contacts where cp_cv_id=$id  and $rechte order by cp_name,cp_givenname";
    $rs=$GLOBALS['dbh']->getAll($sql);
    return $rs;
}


/****************************************************
* suchPerson
* in: muster = array
* out: daten = array
*****************************************************/
function suchPerson($muster) {
    //$pre = ($_SESSION["preon"])?"%":""; //wiederspricht sich mit der Zeile danach
    $pre = isset($muster["pre"])?$_SESSION["pre"]:"";
    $fuzzy=$muster["fuzzy"];
    $andor = $muster["andor"];
    $rechte=berechtigung("cp_");

    $joinCustomer = " left join customer K on C.cp_cv_id=K.id";
    $joinVendor        = " left join vendor V on C.cp_cv_id=V.id";
    if ($muster["cp_name"]=="~") {
        $where="and upper(cp_name) ~ '^\[^A-Z\].*$'";
    } else {
        $dbf = array("cp_name",     "cp_givenname", "cp_gender",    "cp_title" ,
                    "cp_street",    "cp_zipcode",   "cp_city",      "cp_country",   "country",
                    "cp_phone1",    "cp_fax",       "cp_homepage",  "cp_email",
                    "cp_notes",     "cp_stichwort1","cp_birthday",  "cp_beziehung",
                    "cp_abteilung", "cp_position",    "cp_cv_id",     "cp_owener");
        $keys=array_keys($muster);
        $anzahl=count($keys);
        $where="";
        if ( isset( $muster["customer_name"] ) ) {
            $whereCustomer    = "K.name ilike '$pre" . $muster["customer_name"] . "$fuzzy'";
            $whereVendor    = "V.name ilike '$pre" . $muster["customer_name"] . "$fuzzy'";
        } else {
        $whereCustomer = '1=1';
        $whereVendor   = '1=1';
    }

        $daten=false;
        $tbl0=false;

    $where = array();
        for ($i=0; $i<$anzahl; $i++) {
            if (in_array($keys[$i],$dbf) && $muster[$keys[$i]]) {
                $suchwort=trim($muster[$keys[$i]]);
                $suchwort=strtr($suchwort,"*?","%_");
                if ($keys[$i]=="cp_birthday") {$d=explode("\.",$suchwort); $suchwort=$d[2]."-".$d[1]."-".$d[0]; };
                if ($keys[$i]=="cp_phone1") {
                    //Telefonnummer in beliebigen Telefonfeld suchen.
                    $tmp  ="(cp_phone1 like '".$pre.$suchwort."$fuzzy' ";
                    $tmp .="or cp_phone2 like '".$pre.$suchwort."$fuzzy' ";
                    $tmp .="or cp_mobile1 like '".$pre.$suchwort."$fuzzy' ";
                    $tmp .="or cp_mobile2 like '".$pre.$suchwort."$fuzzy' ";
                    $tmp .="or cp_satphone like '".$pre.$suchwort."$fuzzy')";
            $where[] = $tmp;
                } else {
                    $where[].=$keys[$i]." ilike '".$pre.$suchwort."$fuzzy'";
                }
            }
        }
    $where = implode (" $andor ",$where);
        $x=0;
    }
    $felderContact="C.cp_id, C.cp_cv_id, C.cp_title, C.cp_name, C.cp_givenname, C.cp_fax, C.cp_email, C.cp_gender as cp_gender";
    $felderContcatOrCustomerVendor="COALESCE (C.cp_country, country) as cp_country,COALESCE (C.cp_zipcode, zipcode) as cp_zipcode,
                                                                    COALESCE (C.cp_city, city) as cp_city, COALESCE (C.cp_street, street) as cp_street,
                                                                    COALESCE (NULLIF (C.cp_phone1, ''), NULLIF (C.cp_mobile1, ''), phone) as cp_phone1";


    $rs0=array(); //leere arrays initialisieren, damit es keinen fehler bei der funktion array_merge gibt
    if ($muster["customer"]){     //auf checkbox customer mit Titel Kunden prüfen
        $sql0="select $felderContact, $felderContcatOrCustomerVendor, K.name as name, K.language_id as language_id,
                 'C' as tbl from contacts C$joinCustomer where C.cp_cv_id=K.id and ($whereCustomer $andor $where) and $rechte order by cp_name";
        $rs0=$GLOBALS['dbh']->getAll($sql0);
        if (!$rs0) $rs0=array();
    }
    $rs1=array(); //s.o.
    if ($muster["vendor"]){ //auf checkbox vendor mit Titel Lieferant prüfen
        $sql0="select $felderContact, $felderContcatOrCustomerVendor, V.name as name, V.language_id as language_id, 'V' as tbl
                 from contacts C$joinVendor where C.cp_cv_id=V.id and ($whereVendor $andor $where) and $rechte order by cp_name";
        $rs1=$GLOBALS['dbh']->getAll($sql0);
        if (!$rs1) $rs1=array();
    }
    $rs2=array(); //s.o.
    if ( isset( $muster["deleted"] ) ) { //auf checkbox deleted mit Titel "gelöschte Ansprechpartner (Kunden und Lieferanten)" prüfen
                            // es gibt nicht nur gelöschte Personen, sonder auch Personen ohne Zuordnung zu Firmen, z.B. private Adressen
        $sql0="select $felderContact, C.cp_country, C.cp_zipcode, C.cp_city, C.cp_street, C.cp_phone1,
                 '' as name,'P' as tbl from contacts C where $rechte and (".$where.") and C.cp_cv_id is null order by cp_name";
        $rs2=$GLOBALS['dbh']->getAll($sql0);
        if (!$rs2) $rs2=array();
    }
    return array_merge($rs0,$rs1,$rs2);    //alle ergebnisse zusammenziehen und zurückgeben
}

/****************************************************
* savePersonStamm
* in: daten = array
* out: rc = int
* KontaktDaten sichern ( update )
*****************************************************/
function savePersonStamm($daten,$datei) {
    $tmp=0;
    $pid=$daten["PID"];
    $bildok=false;
    // Array zu jedem Formularfed: Tabelle (0=contact,1=cust/vend),  require(0=nein,1=ja), Regel
    // cp_greeting ist raus hli
    $dbfld=array("cp_name" => array(0,1,1,"Name",75),           "cp_givenname" => array(0,1,1,"Vorname",75),
            "cp_gender" => array(0,0,1,"Geschlecht",1),
            "cp_title" => array(0,0,1,"Titel",75),              "cp_street" => array(0,0,1,"Strasse",75),
            "cp_zipcode" => array(0,0,2,"Plz",10),
            "cp_city" => array(0,0,1,"Ort",75),                 "cp_country" => array(0,0,8,"Land",3),
            "cp_phone1" => array(0,0,3,"Telefon 1",30),         "cp_phone2" => array(0,0,3,"Telefon 2",30),
            "cp_mobile1" => array(0,0,3,"Mobiletelefon 1",30),  "cp_mobile2" => array(0,0,3,"Mobiletelefon 2",30),
            "cp_homepage" =>array(0,0,4,"Homepage",0),          "cp_fax" => array(0,0,3,"Fax",30),
            "cp_email" => array(0,0,5,"eMail",0),               "cp_privatemail" => array(0,0,5,"Private eMail",0),
            "cp_notes" => array(0,0,1,"Bemerkungen",0),         "cp_stichwort1" => array(0,0,1,"Stichworte",50),
            "cp_salutation" => array(0,0,1,"Briefanrede",125),  "cp_privatphone" => array(0,0,3,"Privattelefon 1",30),
            "cp_birthday" => array(0,0,7,"Geb-Datum",0),        "cp_beziehung" => array(0,0,6,"Beziehung",0),
            "cp_abteilung" => array(0,0,1,"Abteilung",25),      "cp_position" => array(0,0,1,"Position",25),
            "cp_cv_id" => array(0,0,6,"FID",0),                 "name" => array(1,0,1,"Firma",75),
            "cp_owener" => array(0,0,6,"CRM-User",0),           "cp_grafik" => array(0,0,9,"Grafik",4),);
    if (!empty($datei["Datei"]["name"]["bild"])) {          // eine Datei wird mitgeliefert
            $pictyp=array("gif","jpeg","png","jpg");
            $ext=strtolower(substr($datei["Datei"]["name"]["bild"],strrpos($datei["Datei"]["name"]["bild"],".")+1));
            if (in_array($ext,$pictyp)) {
                $daten["cp_grafik"]=$ext;
                $datei["Datei"]['name']["bild"]="kopf$pid.$ext";
                $bildok=true;
            }
    } else {
        $daten["cp_grafik"]=$daten["IMG_"];
    }
    if ($daten["cp_salutation_"]) $daten["cp_salutation"]=$daten["cp_salutation_"];
    $keys=array_keys($daten);
    $dbf=array_keys($dbfld);
    //$fid=$daten["fid"];
    $anzahl=count($keys);
    $fehler=-1;
    $tels=array();
    $query0='';
    for ($i=0; $i<$anzahl; $i++) {
        if (in_array($keys[$i],$dbf)) {
            $tmpval=trim($daten[$keys[$i]]);
            if ($dbfld[$keys[$i]][0]==1) { // Daten nicht für contacts
                continue;
            } else {
                if (!chkFld($tmpval,$dbfld[$keys[$i]][1],$dbfld[$keys[$i]][2],$dbfld[$keys[$i]][4])) {
                            $fehler=$dbfld[$keys[$i]][3]; $fehler.="::".$keys[$i];
                            $i=$anzahl+1;
                }
                if ($keys[$i]=="cp_phone1"||$keys[$i]=="cp_phone2"||$keys[$i]=="cp_fax") $tels[]=$tmpval;
                $query0.=$keys[$i]."=";
                if (in_array($dbfld[$keys[$i]][2],array(0,1,2,3,4,5,7,8,9))) {  //Stringwert
                        if (empty($tmpval)) {
                            $query0.="null,";
                        } else {
                            $query0.="'".$tmpval."',";
                        }
                } else {
                        if (empty($tmpval)) {
                            $query0.="null,";
                        } else {
                            $query0.=$tmpval.",";        //Zahlwert
                        }
                }
            }
        }
    }
    if ($fehler==-1) { //Kein Fehler aufgetreten
        if (!$daten["PID"] or $daten["PID"]<1) $pid=mknewPerson($daten["employee"]);  //Neue Person
        if (!$pid) return "keine PID";    //Hat keine PID
        if ($daten["nummer"]) {  //Gehört zu einem Cust./Vend.
            $dir=$daten["Quelle"].$daten["nummer"]."/".$pid;
        } else {
            $dir=$pid;
        };
        $ok=chkdir($dir);
        if ($bildok) {  //Ein Bild wird mitgeliefert
            require_once("documents.php");  // db-Eintrag und upload
            $dbfile=new document();
            $dbfile->setDocData("descript","Foto von ".$daten["cp_givenname"]." ".$daten["cp_name"]);
            $bild["Datei"]["name"]=$datei["Datei"]["name"]["bild"];
            $bild["Datei"]["tmp_name"]=$datei["Datei"]["tmp_name"]["bild"];
            $bild["Datei"]["size"]=$datei["Datei"]["size"]["bild"];
            $bild["Datei"]["type"]=$datei["Datei"]["type"]["bild"];
            $bild["Datei"]["error"]=$datei["Datei"]["error"]["bild"];
            $dbfile->uploadDocument($bild,"/$dir");
        }
        if ($datei["Datei"]["name"]["visit"]) {
            $bild["Datei"]["name"]="vcard$pid.".
                strtolower(substr($datei["Datei"]["name"]["visit"],strrpos($datei["Datei"]["name"]["visit"],".")+1));
            $bild["Datei"]["tmp_name"]=$datei["Datei"]["tmp_name"]["visit"];
            $bild["Datei"]["size"]=$datei["Datei"]["size"]["visit"];
            $bild["Datei"]["type"]=$datei["Datei"]["type"]["visit"];
            $bild["Datei"]["error"]=$datei["Datei"]["error"]["visit"];
            $dbfile=new document();
            $dbfile->setDocData("descript","Visitenkarte von ".$daten["cp_givenname"]." ".$daten["cp_name"]);
            $dbfile->uploadDocument($bild,"/$dir");
        }
        mkTelNummer($pid,"P",$tels);
        $sql0="update contacts set ".$query0."cp_employee=".$_SESSION["loginCRM"]." where cp_id=$pid";
        if($GLOBALS['dbh']->myquery($sql0)) {  //Erfolgreich gesichert
            return $pid;
        } else {
            return "unbekannt";
        }
    } else { return $fehler; };
}

/****************************************************
* insFaKont
* in: data = array
* out: id = int
* eine Auswahl Kontakte einer Firma zuordnen
*****************************************************/
function insFaKont($data) {
    $fa=$data["fid"];
    foreach ($data["kontid"] as $row) {
        $sql="update contacts set cp_cv_id=".$fa." where cp_id=".$row;
        $rc=$GLOBALS['dbh']->myquery($sql);
    }
}

/****************************************************
* mknewPerson
* in:
* out: id = int
* Personensatz erzeugen ( insert )
*****************************************************/
function mknewPerson($id) {
    $newID=uniqid (rand());
    //Wird zur Zeit nicht verwendet
    //if (!$id) {$uid='null';} else {$uid=$id;};
    $sql="insert into contacts (cp_name,cp_employee) values ('$newID',$id)";
    $rc=$GLOBALS['dbh']->myquery($sql);
    if ($rc) {
        $sql="select cp_id from contacts where cp_name = '$newID'";
        $rs=$GLOBALS['dbh']->getAll($sql);
        if ($rs) {
            $id=$rs[0]["cp_id"];
        } else {
            $id=false;
        }
    } else {
        $id=false;
    }
    return $id;
}
/****************************************************
* getCpAnreden
* in:
* out: rs = array
* Gespeicherte Anreden von Personen holen
*****************************************************/
function getCpAnreden() {
    $sql="select translation from generic_translations where translation_type ILIKE '%greeting%'";
    $rs=$GLOBALS['dbh']->getAll($sql);
    return $rs;
}
/****************************************************
* getCpAnredenGeneric
* in:
* out: rs = array
* Gespeicherte Anreden sprachbezogen aus generic_translations holen
* return mixed
*****************************************************/
function getCpAnredenGeneric($gender) {
    $greet = array('male'=>'Herr','female'=>'Frau');
    $sql = "select language_id,COALESCE(translation,'".$greet[$gender]."') from generic_translations where translation_type ILIKE 'greetings::$gender%'";
    $rs=$GLOBALS['dbh']->getAll($sql);
    return $rs;
}
/****************************************************
* getCpBriefAnreden
* in:
* out: rs = array
* Gespeicherte Briefanreden von Personen holen
*****************************************************/
function getCpBriefAnreden() {
    $sql="select distinct (cp_salutation) from contacts";
    $rs=$GLOBALS['dbh']->getAll($sql);
    return $rs;
}

function leertplP (&$t,$data,$msg,$tab,$suche=false,$Quelle="",$ui=false ) {
//global $laender;
//cp_greeting raus hli
        if ($fid && $Quelle) {
            $fa=getFirmenstamm($fid,false,$Quelle);
            $nummer=($Quelle=="C")?$fa["customernumber"]:$fa["vendornumber"];
        }
        if ( $ui ) $t->set_file(array("pers1" => "persons".$tab.".tpl"));
        else       $t->set_file(array("pers1" => "personen".$tab.".tpl"));
        $t->set_var(array(
            'ERPCSS'          => $_SESSION['baseurl'].'crm/css/'.$_SESSION["stylesheet"],
            'Fld'             => "cp_title",
            'JS'              => "goFld();",
            'color'           => "white",
            'BgC'             => 0,
            'Btn1'            => "",
            'Btn3'            => "",
            'Msg'             => $msg,
            'action'          => "personen".$tab.".php",
            'PID'             => "",
            'preon'           => ($_SESSION["preon"])?"checked":"",
            'cp_salutation_'  => "",
            'cp_title'        => $data['greeting'],
            'cp_givenname'    => $data['firstname'],
            'cp_name'         => $data['lastname'],
            'cp_gender'       => ($suche)?"":"selected",
            'cp_genderm'      => ($suche)?"selected":"",
            'cp_street'       => $data['street'],
            'cp_country'      => "D",
            'cp_zipcode'      => $data['zipcode'],
            'cp_city'         => $data['city'],
            'cp_phone1'       => $data['phone'],
            'cp_phone2'       => "",
            'cp_fax'          => "",
            'cp_privatphone'  => "",
            'cp_mobile1'      => "",
            'cp_mobile2'      => "",
            'cp_email'        => "",
            'cp_privatemail'  => "",
            'cp_homepage'     => "",
            'cp_birthday'     => "",
            'cp_beziehung'    => "",
            'cp_abteilung'    => "",
            'cp_position'     => "",
            'Firma'           => isset( $fa ) ? $fa["name"] : '',
            'FID'             => ($suche)?$data['fid']:"",
            'FID1'            => $fid,
            'cp_stichwort1'   => "",
            'cp_notes'        => "",
            'nummer'          => isset( $nummer ) ? $nummer : '',
            'Quelle'          => $Quelle,
            'IMG'             => "",
            'IMG_'            => "",
            'employee'        => $_SESSION["loginCRM"],
            'init'            => $_SESSION["login"]
        ));
        //$first[]=array("grpid"=>"","rechte"=>"w","grpname"=>".:public:.");
        //$first[]=array("grpid"=>$daten["cp_employee"],"rechte"=>"w","grpname"=>".:personal:.");
        //$grp=getGruppen();
        //if ($grp) {    $user=array_merge($first,$grp); }
        //else { $user=$first; };
        //doBlock($t,"pers1","OwenerListe","OL",$user,"grpid","grpname","1");
        //$anreden=getCpBriefAnreden();
        //doBlock($t,"pers1","briefanred","BA",$anreden,"cp_salutation","cp_salutation");

        /*** Wird doch im leerem Template gar nicht verwendet... ***/
}

function vartplP (&$t,$daten,$msg,$btn1,$btn2,$btn3,$fld,$bgcol,$fid,$tab,$ui=false) {
    //global $laender;
//cp_greeting raus hli
        if ($daten["cp_cv_id"] && $daten["Quelle"]) {
            $fa=getFirmenstamm($daten["cp_cv_id"],false,$daten["Quelle"]);
            $nummer=($daten["Quelle"]=="C")?$fa["customernumber"]:$fa["vendornumber"];
        }
        if (trim($daten["cp_grafik"])<>"") {
            if ($nummer) {
                $root="dokumente/".$_SESSION["dbname"]."/".$daten["Quelle"].$nummer."/".$daten["cp_id"];
            } else {
                $root="dokumente/".$_SESSION["dbname"]."/".$daten["cp_id"];
            };
            $Image="<img src='$root/kopf".$daten["cp_id"].".".$daten["cp_grafik"]."' ".$daten["icon"].">";
            $tmp=glob("$root/vcard".$daten["cp_id"].".*");
            if ($tmp)  foreach ($tmp as $vcard) {
                $ext=explode(".",$vcard);
                $ext=strtolower($ext[count($ext)-1]);
                if (in_array($ext,array("jpg","jpeg","gif","png","pdf","ps"))) {
                    $VCARD="<img src='$root/vcard".$daten["cp_id"].".$ext' width='110' height='80'>";
                    break;
                }
            }
        }
        if ( $ui ) $t->set_file(array("pers1" => "persons1Result.tpl"));
        else       $t->set_file(array("pers1" => "personen".$tab.".tpl"));
        $t->set_var(array(
            'ERPCSS'          => $_SESSION['baseurl'].'crm/css/'.$_SESSION["stylesheet"],
            'Fld'             => $fld,
            'JS'              => "goFld();",
            'color'           => $bgcol,
            'BgC'             =>  $fid,
            'Btn1'            => $btn1,
            'Btn3'            => $btn3,
            'Msg'             => $msg,
            'preon'           => (isset($daten["pre"]))?"checked":"",
            'action'          => "personen".$tab.".php",
            'mtime'           => $daten["mtime"],
            'PID'             => $daten["cp_id"],
            'tabelle'         => $daten["tabelle"],
            'nummer'          => isset( $nummer ) ? $nummer : '',
            'cp_title'        => $daten["cp_title"],
            'cp_givenname'    => $daten["cp_givenname"],
            'cp_name'         => $daten["cp_name"],
            'cp_gender'.$daten["cp_gender"] => "selected",
            'cp_salutation_'  => isset($daten["cp_salutation_"])?$daten["cp_salutation_"]:'',
            'cp_street'       => $daten["cp_street"],
            'cp_country'      => $daten["cp_country"],
            'cp_zipcode'      => $daten["cp_zipcode"],
            'cp_city'         => $daten["cp_city"],
            'cp_phone1'       => $daten["cp_phone1"],
            'cp_phone2'       => $daten["cp_phone2"],
            'cp_privatphone'  => $daten["cp_privatphone"],
            'cp_mobile1'      => $daten["cp_mobile1"],
            'cp_mobile2'      => $daten["cp_mobile2"],
            'cp_fax'          => $daten["cp_fax"],
            'cp_email'        => $daten["cp_email"],
            'cp_privatemail'  => $daten["cp_privatemail"],
            'cp_homepage'     => $daten["cp_homepage"],
            'cp_birthday'     => ($daten["cp_birthday"])?db2date($daten["cp_birthday"]):"",
            'cp_beziehung'    => $daten["cp_beziehung"],
            'cp_abteilung'    => $daten["cp_abteilung"],
            'cp_position'     => $daten["cp_position"],
            'Firma'           => $daten["Firma"],
            'FID'             => $daten["cp_cv_id"],
            'FID1'            => $fid,
            'cp_stichwort1'   => $daten["cp_stichwort1"],
            'cp_notes'        => $daten["cp_notes"],
            'Quelle'          => $daten["Quelle"],
            'IMG'             => isset($Image)?$Image:'',
            'IMG_'            => $daten["cp_grafik"],
            'visitenkarte'    => isset($VCARD)?$VCARD:'',
            'init'            => ($daten["cp_employee"])?$daten["cp_employee"]:"ERP",
            'employee'        => $_SESSION["loginCRM"]
        ));
        if ($daten["cp_employee"]==$_SESSION["loginCRM"]) {
            $first[]=array("grpid"=>"","rechte"=>"w","grpname"=>".:public:.");
            $first[]=array("grpid"=>$daten["cp_employee"],"rechte"=>"w","grpname"=>".:personal:.");
            $grp=getGruppen();
            if ($grp) {    $user=array_merge($first,$grp); }
            else { $user=$first; };
            doBlock($t,"pers1","OwenerListe","OL",$user,"grpid","grpname",$daten["cp_owener"]);
        } else {
            $user[0] = array("grpid"=>$daten["cp_owener"],"grpname"=>($daten["cp_owener"])?getOneGrp($daten["cp_owener"]):".:public:.");
            doBlock($t,"pers1","OwenerListe","OL",$user,"grpid","grpname",$daten["cp_owener"]);
        }
        $anreden=getCpBriefAnreden();
        doBlock($t,"pers1","briefanred","BA",$anreden,"cp_salutation","cp_salutation",$daten["cp_salutation"]);
}
?>
