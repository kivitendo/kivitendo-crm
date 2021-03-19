<?php
// $Id: FirmenLib.php $

/****************************************************
* getShipStamm
* in: id = int
* out: rs = array(Felder der db)
* hole die abweichenden Lieferdaten
*****************************************************/
function getShipStamm($id,$tab="C",$complete=false) {
    if ( $id < 1 ) return false;
    if ($complete) {
        $sql ="select trans_id,shiptoname,COALESCE(shiptostreet,street) as shiptostreet,COALESCE(shiptocity,city) as shiptocity,";
        $sql.="COALESCE(shiptocountry,country) as shiptocountry,";
        $sql.="COALESCE(shiptozipcode,zipcode) as shiptozipcode,";
        $sql.="COALESCE(shiptodepartment_1,department_1) as shiptodepartment_1,";
        $sql.="COALESCE(shiptodepartment_2,department_2) as shiptodepartment_2,";
        $sql.="COALESCE(shiptocontact,contact) as shiptocontact from shipto left join ".(($tab=="C")?"customer":"vendor")." on trans_id=id ";
        $sql.="where shipto_id=$id";
    } else {
        $sql="select S.*,BL.bundesland as shiptobundesland from shipto S left join bundesland BL on S.shiptobland=BL.id where S.shipto_id=$id ";
    }
    $rs2=$GLOBALS['dbh']->getAll($sql);
    if(!$rs2) {
        return false;
    } else {
        return $rs2[0];
    }
}

/****************************************************
* getAllFirmen
* in: sw = array(Art,suchwort)
* in: tab = string
* out: rs = array(Felder der db)
* hole alle Kunden
*****************************************************/
function getAllFirmen($sw,$usePre=true,$tab='C') {
    if ( $usePre ) { $Pre = $_SESSION["pre"]; }
    else { $Pre = ''; };
    $rechte=berechtigung();
    if (!$sw[0]) {
         $where="phone like '$Pre".$sw[1]."%' ";
    } else {
        if ($sw[1]=="~") { //Firmenname beginnt nicht mit einem Buchstaben
            $where="( upper(name) ~ '^\[^A-Z\].*$' or ";
            $where.="upper(department_1) ~ '^\[^A-Z\].*$' or ";
            $where.="upper(department_2) ~ '^\[^A-Z\].*$' or ";
            $where.="upper(sw) ~ '^\[^A-Z\].*$' ";

        } else  {
            $where="( name ilike '$Pre".$sw[1]."%' or ";
            $where.="department_1 ilike '$Pre".$sw[1]."%' or ";
            $where.="department_2 ilike '$Pre".$sw[1]."%' or ";
            $where.="sw ilike '$Pre".$sw[1]."%'";
        }
    }
    $where .= ") AND obsolete = false";
    if ($tab=="C") {
        $sql="select *,'C' as tab from customer where ($where) and $rechte order by name";
    } else if ($tab=="V") {
        $sql="select *,'V' as tab from vendor where ($where) and $rechte order by name";
    } else {
        return false;
    }
    $rs=$GLOBALS['dbh']->getAll($sql);
    if(!$rs) {
        $rs=false;
    };
    return $rs;
}
function getAllFirmenByMail($sw,$usePre=true,$tab='C') {
    if ($usePre) $Pre=$_SESSION["pre"];
    $rechte=berechtigung();
    $where = "email ilike '$Pre$sw%'";
    if ($tab=="C") {
        $sql="select name,email,'C' as tab from customer where ($where) and $rechte order by name";
    } else if ($tab=="V") {
        $sql="select name,email,'V' as tab from vendor where ($where) and $rechte order by name";
    } else {
        return false;
    }
    $rs=$GLOBALS['dbh']->getAll($sql);
    if(!$rs) {
        $rs=false;
    };
    return $rs;
}

/****************************************************
* getFirmaStamm
* in: id = int, ws = boolean
* out: daten = array
* Stammdaten einer Firma holen
*****************************************************/
function getFirmenStamm($id,$ws=true,$tab='C',$cvar=true) {
    if ($tab=="C") {
        // Umsätze holen
        $sql="select sum(amount) from oe where customer_id=$id and quotation='f' and closed = 'f'";
        $rs=$GLOBALS['dbh']->getOne($sql);
        $oa=$rs["sum"];
        $sql="select sum(amount) from ar where customer_id=$id and amount<>paid";
        $rs=$GLOBALS['dbh']->getOne($sql);
        $op=$rs["sum"];
        $sql="select C.*,E.name as verkaeufer,EMP.name as bearbeiter,B.description as kdtyp,B.discount as typrabatt,P.pricegroup,";
        $sql.="L.lead as leadname,BL.bundesland,T.terms_netto,LA.description as language from customer C ";
        $sql.="left join employee E on C.salesman_id=E.id left join employee EMP on C.employee=EMP.id ";
        $sql.="left join business B on B.id=C.business_id left join bundesland BL on BL.id=C.bland ";
        $sql.="left join payment_terms T on T.id=C.payment_id left join pricegroup P on P.id=C.pricegroup_id ";
        $sql.="left join leads L on C.lead=L.id left join language LA on LA.id = C.language_id ";
        $sql.="where C.id=$id";
    } else if ($tab=="V") {
        // Umsätze holen
        $sql="select sum(amount) as summe from ap where vendor_id=$id and amount<>paid";
        $rs=$GLOBALS['dbh']->getOne($sql);
        $op=$rs["summe"];
        $sql="select sum(amount) from oe where vendor_id=$id and quotation='f' and closed = 'f'";
        $rs=$GLOBALS['dbh']->getOne($sql);
        $oa=$rs["sum"];
        $sql="select C.*,E.name as verkaeufer,EMP.name as bearbeiter,B.description as kdtyp,B.discount as typrabatt,BL.bundesland,";
        $sql.="L.lead as leadname,LA.description as language from vendor C left join employee EMP on C.employee=EMP.id ";
        $sql.="left join employee E on C.salesman_id=E.id left join business B on B.id=C.business_id ";
        $sql.="left join language LA on LA.id = C.language_id left join leads L on C.lead=L.id ";
        $sql.="left join bundesland BL on BL.id=C.bland ";
        $sql.="where C.id=$id";
    } else {
        return false;
    }
    $row=$GLOBALS['dbh']->getOne($sql);  // Rechnungsanschrift
    if(!$row) {
        return false;
    } else {
        /* history_erp wird wohl nicht richtig gepflegt, also erst einmal raus
        if ($row["mtime"]=="") {
            $sql = "select * from history_erp where trans_id = $id and snumbers like '%rnumber_%' order by itime desc limit 1";
            $rs2 = $GLOBALS['dbh']->getOne($sql);  // Rechnungsanschrift
            if ($rs2) if ($rs2["itime"]<>$row["itime"])
               $row["mtime"] = $rs2["itime"];
            $row["modemployee"] = $rs2["employee_id"];
        } else {
            $row["modemployee"] = $row["employee"];
        }*/
        $row["modemployee"] = $row["employee"];
        if ($row["konzern"]) {
            $sql="select name from %s where id = %d";
            if ($tab=="C") {
                $krs=$GLOBALS['dbh']->getOne(sprintf($sql,"customer",$row["konzern"]));
            } else {
                $krs=$GLOBALS['dbh']->getOne(sprintf($sql,"vendor",$row["konzern"]));
            }
            if ($krs) $row["konzernname"]=$krs["name"];
        } else {
            $row["konzernname"] = '';
        }
        if ($tab=="C") {
            $sql="select count(*) from customer where konzern = ".$id;
        } else {
            $sql="select count(*) from vendor where konzern = ".$id;
        }
        $knr=$GLOBALS['dbh']->getOne($sql);
        $row["konzernmember"]=$knr["count"];
        if ($tab=="C") { $nummer=$row["customernumber"]; }
        else { $nummer=$row["vendornumber"]; };
        if ($row["grafik"]) {
            $DIR=$tab.$nummer;
            $image="./dokumente/".$_SESSION["dbname"]."/$DIR/logo.".$row["grafik"];
            if (file_exists($image)) {
                $size=@getimagesize($image);
                $row["size"]=$size[3];
                if ($size[1]>$size[0]) {
                    $faktor=ceil($size[1]/70);
                } else {
                    $faktor=ceil($size[0]/120);
                }
                $breite=floor($size[0]/$faktor);
                $hoehe=floor($size[1]/$faktor);
                $row["icon"]="width=\"$breite\" height=\"$hoehe\"";
            } else {
                $daten["name"]=getcwd()." $image: not found";
            }
        }
        $rs3=getAllShipto($id,$tab);
        $shipcnt=(count($rs3));
        $shipids=array();
        if ($shipcnt>0) {
            for ($sc=0; $sc<$shipcnt; $sc++) {
                $shipids[]="'".$rs3[$sc]["shipto_id"]."'";
            }
            $shipids=implode(",",$shipids);
        } else {
            $shipids="0";
        }
        if ( empty($rs3) ) {  // es ist keine abweichende Anschrift da
            if ($ws) {    // soll dann aber mit Re-Anschrift gefüllt werden
                $row2=Array(
                    'shiptoname' => $row["name"],
                    'shiptodepartment_1' => $row["department_1"],
                    'shiptodepartment_2' => $row["department_2"],
                    'shiptostreet' => $row["street"],
                    'shiptozipcode' => $row["zipcode"],
                    'shiptocity' => $row["city"],
                    'shiptocountry' => $row["country"],
                    'shiptobundesland' => $row["bundesland"],
                    'shiptocontact' => "",
                    'shiptophone' => $row["phone"],
                    'shiptofax' => $row["fax"],
                    'shiptoemail' => $row["email"],
                    'shiptocountry' => $row["country"],
                    'shipto_id' => -1
                );
            } else {  // leeres Array bilden
                $row2=Array(
                    'shiptoname' => "",
                    'shiptodepartment_1' => "",
                    'shiptodepartment_2' => "",
                    'shiptostreet' => "",
                    'shiptozipcode' => "",
                    'shiptocity' => "",
                    'shiptocountry' => "",
                    'shiptobundesland' => "",
                    'shiptocontact' => "",
                    'shiptophone' => "",
                    'shiptofax' => "",
                    'shiptoemail' => "",
                    'shiptocountrycountry' => "",
                    'shipto_id' => ""
                );
            }
        } else {
            $row2 = $rs3[0];
        }
        $daten=array_merge($row2,$row);
    }
    //benutzerdef. Variablen:
    if ($cvar) {
        $cvars = getFirmaCVars($id);
        if ($cvars) $daten = array_merge($daten,$cvars);
    }
    $daten["shiptocnt"]=($shipcnt>0)?$shipcnt:0;
    $daten["shiptoids"]=$shipids;
    $daten["op"]=$op;
    $daten["oa"]=$oa;
    $daten["nummer"]=$nummer;
    accessHistory(array($id,$daten['name'],$tab));//$daten['name'],$tab
    return $daten;
};

/**
 * getFirmaCVars: benutzerdefinierte Variablen zurückgeben
 *
 * @param int $id
 *
 * @return array
 */
function getFirmaCVars($id,$search=false) {
    $sql = "select C.name,C.type,V.bool_value,V.timestamp_value,V.text_value,V.number_value,C.module ";
    $sql.= "from custom_variables V left join custom_variable_configs C on C.id=V.config_id ";
    $sql.= "where V.trans_id =".$id." and module = 'CT'";
    //if ($sql) $sql .= " and C.searchable='t' ";
    $sql .= "order by C.sortkey";
    $rs = $GLOBALS['dbh']->getAll($sql);
    if ($rs) {
        foreach ($rs as $row) {
            switch ($row["type"]) {
                case "text"     :
                case "textfield":
                case "select"   : $daten["vc_cvar_".$row["name"]] = $row["text_value"];
                                  break;
                case "customer"    : $daten["vc_cvar_".$row["name"]] = abs($row["number_value"]);
                                  break;
                case "number"   : $daten["vc_cvar_".$row["name"]] = $row["number_value"];
                                  break;
                case "bool"     : $daten["vc_cvar_".$row["name"]] = $row["bool_value"];
                                  break;
                case "date"     : $daten["vc_cvar_".$row["name"]] = db2date(substr($row["timestamp_value"],0,10));
                                  break;
            }
        }
        return $daten;
    } else {
        return false;
    }
}

function getCvars() {
    $sql = "select * from custom_variable_configs where module = 'CT' order by sortkey";
    $rs = $GLOBALS['dbh']->getAll($sql);
    return $rs;
}

function getCvarName($id) {
    $sql = "SELECT name,'C' as tab from customer where id = %d union select name,'V' as tab from vendor where id = %d";
    $rs = $GLOBALS['dbh']->getOne(sprintf($sql,$id,$id));
    return $rs['name'];
}
/****************************************************
* getAllShipto
* in: id = int
* out: daten = array
* Alle abweichende Anschriften einer Firma holen
*****************************************************/
function getAllShipto($id,$tab="C") {
    if (empty($id)) return false;
    //$sql="select distinct shiptoname,shiptodepartment_1,shiptodepartment_2,shiptostreet,shiptozipcode,";
    //$sql.="shiptocity,shiptocountry,shiptocontact,shiptophone,shiptofax,shiptoemail,shipto_id from shipto ";
    //$sql="select (module<>'CT') as vkdoc,* from shipto where trans_id=$id";
    $sql="select s.*,b.bundesland as shiptobundesland from shipto s left join bundesland b on s.shiptobland=b.id ";
    $sql.=" where trans_id=$id and module='CT' order by itime";
    $rs=$GLOBALS['dbh']->getAll($sql);
    return $rs;
}

/****************************************************
* getPaymet
* in: id = int
* out: daten = array
* Alle Zahlungsbedngungen
*****************************************************/
function getPayment() {
    $sqlpt = "select * from payment_terms";
    $rspt = $GLOBALS['dbh']->getAll($sqlpt);
    $leer=array(array("id"=>"","description"=>"----------"));
    return array_merge($leer,$rspt);
}

/****************************************************
* suchstr
* in: muster = string
* out: daten = array
* Suchstring über customer,shipto zusamensetzen
*****************************************************/
function suchstr($muster,$typ="C") {
    //$kenz = array("C" => "K","V" => "L");
    $kenz = array("C" => "C","V" => "V");
    $tab  = array("C" => "customer","V" => "vendor");
    //Suche in den CVars:
    $cvartemp  = 'EXISTS ( SELECT cvar.id FROM custom_variables cvar ';
    $cvartemp .= 'LEFT JOIN custom_variable_configs cvarcfg ON (cvar.config_id = cvarcfg.id) ';
    $cvartemp .= "WHERE (cvarcfg.module = 'CT') AND (cvarcfg.name  = '%s') AND ";
    $cvartemp .= '(cvar.trans_id  = %s.id) AND (%s)';
    $cvartemp .= "AND (cvar.sub_module = 'CT' or cvar.sub_module is null or cvar.sub_module = '') )";
    // Array zu jedem Formularfed: 0=String,2=Int
    $dbfld = array('name' => 0, 'street' => 0, 'zipcode' => 1,
            'city' => 0, 'phone' => 1, 'fax' => 1,
            'homepage' => 0, 'email' => 0, 'notes' => 0,
            'department_1' => 0, 'department_2' => 0,
            'country' => 0, 'sw' => 0,
            'language_id' => 2, 'business_id' => 2,
            'ustid' => 1, 'taxnumber' => 1, 'lead' => 2, 'leadsrc' => 0,
            'bank' => 0, 'bank_code' => 1, 'account_number' => 1,
            'vendornumber' => 0, 'v_customer_id' => 0,
            'kundennummer' => 0, 'customernumber' => 0, 'contact' => 0,
            'employee' => 2, 'branche' => 0, 'headcount' => 2);
    $dbfld2 = array('name' => 'shiptoname', 'street' => 'shiptostreet','zipcode' => 'shiptozipcode',
            'city' => 'shiptocity', 'phone' => 'shiptophone','fax' => 'shiptofax',
            'email' => 'shiptoemail', 'department_1' => 'shiptodepartment_1',
            'department_2' => 'shiptodepartment_2', 'country' => 'shiptocountry');
    $fuzzy2 = $muster["fuzzy"];
    $fuzzy1 = ( isset($muster['pre']) and $muster['pre'] != '' )?$_SESSION["pre"]:"";
    $andor = $muster["andor"];
    $keys = array_keys($muster);
    $suchfld = array_keys($dbfld);
    $anzahl = count($keys);
    $tbl0 = false;
    $tbl2 = false;
    $cols = '';
    $cvcnt = 0;
    $cvars = getAlleVariablen();
    if ( $cvars ) foreach ($cvars as $row) {
         $cvar[$row['name']] = $row['type'];
    };
    if ( $muster["shipto"] ) { $tbl1 = true; } else { $tbl1 = false; }
    $tmp1 = ""; $tmp2 = "";
    for ( $i=0; $i<$anzahl; $i++ ) {
        if ( in_array($keys[$i],$suchfld) and $muster[$keys[$i]]<>"" ) {
            $suchwort = trim($muster[$keys[$i]]);
            $suchwort = strtr($suchwort,"*?","%_");
            if ( $dbfld[$keys[$i]] == 1 ) {
                if ($suchwort[0] == '<' ||
                    $suchwort[0] == '>' ||
                    $suchwort[0] == '=' ) $dbfld[$keys[$i]] = 2;
            }
            if ( $dbfld[$keys[$i]] == 2 ) {
                $search = array();
                preg_match_all( "/([<=>]?[\d]+)/", $suchwort, $treffer );
                if ( $treffer[0] ) {
                    foreach ( $treffer[0] as $val ) {
                        if ($val[0] == '>' || $val[0] == '<' || $val[0] == '=') {
                            $search[] = $typ.'.'.$keys[$i].' '.$val[0]." '".substr($val,1)."'";
                        } else {
                            //Dropdown-Boxen liefern kein "=" mit.
                            $search[] = $typ.'.'.$keys[$i]." = '".$val."'";
                        }
                    }
                    $suchwort = '( '.implode(" $andor ",$search).' )';
                    $tmp1    .= $andor.' '.$suchwort.' ';
                }
            } else {
                if ( $tbl1 && $dbfld2[$keys[$i]] ) {
                    $tmp1 .= "$andor (S.".$dbfld2[$keys[$i]]." ilike '$fuzzy1".$suchwort."$fuzzy2' ";
                    $tmp1 .= 'or '.$typ.'.'.$keys[$i]." ilike '$fuzzy1".$suchwort."$fuzzy2' ) ";
                } else {
                    $tmp1 .= "$andor ".$typ.".".$keys[$i]." ilike '$fuzzy1".$suchwort."$fuzzy2' ";
                }
            }
        } else if ( substr($keys[$i],0,4) == "vc_c" ) {
                $suchwort = trim($muster[$keys[$i]]);
                $suchwort = strtr($suchwort,"*?","%_");
                if ( $suchwort != "" ) {
            $tbl2 = true;
                        $cvcnt ++;
                        preg_match("/vc_cvar_([a-z0-9]+)/",$keys[$i],$hits);
                        $n = $hits[1];
                        switch  ($cvar[$n]) {
                              case "bool"    :    $tmp2[] = sprintf($cvartemp,$n,$typ,"COALESCE(cvar.bool_value, false) = TRUE ");
                                             break;
                           case "number":    $tmp2[] = sprintf($cvartemp,$n,$typ,"COALESCE(cvar.number_value, '') = '$suchwort' ");
                                             break;
                           case 'customer':  $tmp2[] = sprintf($cvartemp,$n,$typ,"COALESCE(cvar.number_value, '') = '$suchwort' ");
                                             break;
                           case 'timestamp': $suchwort = date2db($suchwort);
                                              $tmp2[] = sprintf($cvartemp,$n,$typ,"COALESCE(cvar.timestamp_value, '') = '$suchwort' ");
                                             break;
                           default           : $tmp2[] = sprintf($cvartemp,$n,$typ,"COALESCE(cvar.text_value, '') ilike '$fuzzy1$suchwort$fuzzy2' ");
                        }
                }
    }
    }
    $cols = "distinct ".$typ.".*";
    if ( $tbl1 ) {
        $tabs = $tab[$typ]." ".$typ." left join shipto S on ".$typ.".id=S.trans_id";
    } else {
        $tabs = $tab[$typ]." ".$typ;
    }
    if ( $tbl2 ) {
       if ( $cvcnt > 1 ) {
           $tmp2 = join(" $andor ",$tmp2);
       } else {
           $tmp2 = $tmp2[0];
       };
       if ( $tmp1 ) {
           $tmp1 .= " $andor (".$tmp2.")";
       } else {
           $tmp1 = "   ".$tmp2;
       }
    }
    if ( $tmp1 ) $where = substr($tmp1,3);
    return array("where" => $where, "tabs" => $tabs, "cols" => $cols);
}

/****************************************************
* suchFirma
* in: muster = string
* out: daten = array
* KundenDaten suchen
*****************************************************/
function suchFirma($muster,$tab="C") {
    $rechte=berechtigung();
    $tmp=suchstr($muster,$tab);
    $andor = $muster["andor"];
    $umsatz = '';
    if ($muster["umsatz"]) {
        if ($muster["year"]) $year = " and  transdate between '".$muster["year"]."-01-01' and '".$muster["year"]."-12-31'";
        preg_match_all("/([<=>]?[\d]+)/",$muster["umsatz"],$treffer);
        if ($treffer[0]) {
            if ($tab=="C") {
                $umstpl = "(select sum(amount) from ar where customer_id=K.id $year)";
            } else {
                $umstpl = "(select sum(amount) from ap where customer_id=L.id $year)";
            }
            foreach ($treffer[0] as $val) {
                if ($val[0] == ">" || $val[0] == "<" || $val[0] == "=") {
                    $ums[] = $umstpl.$val[0].substr($val,1);
                }
            }
            $umsatz= " $andor (".implode(" and ",$ums).") ";
        }
    }
    $where=$tmp["where"];
    if ($muster['obsolete']) $where .= " and obsolete = '".$muster['obsolete']."' ";
    $tabs=$tmp["tabs"];
    $cols=$tmp["cols"];
    if ($where<>"") {
        if ( $umsatz != '' ) {
            $sql="select $cols,$umstpl as umsatz from $tabs where ($where $umsatz) and $rechte";
        } else {
            $sql="select $cols from $tabs where ($where) and $rechte";
        }
        $rs=$GLOBALS['dbh']->getAll($sql);
        if(!$rs) {
            $daten=false;
        } else {
            $daten=$rs;
        }
    }
    return $daten;
}
function getName($id,$typ="C") {
    $tab=array("C" => "customer","V" => "vendor");
    $sql="select name from ".$tab[$typ]." where id = $id";
    $rs=$GLOBALS['dbh']->getOne($sql);
    if ($rs) {
        return $rs["name"];
    } else {
        return false;
    }
}
function getFaID($name) {
    $sql="select id,C from customer where name ilike '%$name%'";
    $sql = "SELECT id,'C',name as tab from customer where name ilike '%$name%' union ";
    $sql.= "SELECT id,'V',name as tab from vendor   where name ilike '%$name%'";
    $rs=$GLOBALS['dbh']->getAll($sql);
    return $rs;
}

function chkTimeStamp($tabelle,$id,$stamp,$begin=false) {
    if ($tabelle=="contacts") {
        $sql = "select mtime from $tabelle where cp_id = $id";
    } else {
        $sql = "select mtime from $tabelle where id = $id";
    }
    $rs = $GLOBALS['dbh']->getOne($sql);
    if ($rs["mtime"]<=$stamp) {
        if ($begin) $GLOBALS['dbh']->begin();
        return true;
    } else {
        return false;
    }
}
/****************************************************
* saveFirmaStamm
* in: daten = array
* out: rc = int
* KundenDaten sichern ( update )
*****************************************************/
function saveFirmaStamm($daten,$datei,$typ="C",$neu=false) {
    $kenz=array("C" => "K","V" => "L");
    $tab=array("C" => "customer","V" => "vendor");
    if ($neu && $_SESSION['feature_unique_name_plz']=='t') {
        $sql="SELECT id FROM ".$tab[$typ]." WHERE name = '".strtr($daten['name'],array("'"=>"''"))."' AND zipcode = '".$daten['zipcode']."'";
        $rs=$GLOBALS['dbh']->getAll($sql);
        if ($rs[0]['id']) return array(-1,".:Customer / Vendor exist with same zipcode:.");
    }
    if (!empty($datei["Datei"]["name"])) {          // eine Datei wird mitgeliefert
            $pictyp=array("gif","jpeg","png","jpg");
            $ext=substr($datei["Datei"]["name"],strrpos($datei["Datei"]["name"],".")+1);
            if (in_array($ext,$pictyp)) {
                $daten["grafik"]=$ext;
                $datei["Datei"]['name']="logo.$ext";
                $bildok=true;
            }
    };
    // Array zu jedem Formularfed: Tabelle (0=customer/vendor,1=shipto), require(0=nein,1=ja), Spaltenbezeichnung, Regel
    $dbfld=array(    name => array(0,1,1,"Name",75),        greeting => array(0,0,1,"Anrede",75),
        department_1 => array(0,0,1,"Zusatzname",75),       department_2 => array(0,0,1,"Abteilung",75),
        country => array(0,0,8,"Land",25),                  zipcode => array(0,1,2,"Plz",10),
        city => array(0,1,1,"Ort",75),                      street => array(0,1,1,"Strasse",75),
        fax => array(0,0,3,"Fax",30),                       phone => array(0,0,3,"Telefon",30),
        email => array(0,0,5,"eMail",0),                    homepage =>array(0,0,4,"Homepage",0),
        contact => array(0,0,1,"Kontakt",75),               v_customer_id => array(0,0,1,"Kundennummer",50),
        sw => array(0,0,1,"Stichwort",50),                  notes => array(0,0,0,"Bemerkungen",0),
        ustid => array(0,0,0,"UStId",0),                    taxnumber => array(0,0,0,"Steuernummer",0),
        bank => array(0,0,1,"Bankname",50),                 bank_code => array(0,0,6,"Bankleitzahl",15),
        iban => array(0,0,1,"IBAN",24),                     bic => array(0,0,1,"BIC",15),
        account_number => array(0,0,6,"Kontonummer",15),    language_id =>  array(0,0,6,"Sprache",0),
        payment_id => array(0,0,6,"Zahlungsbedingungen",0), employee => array(0,0,6,"Bearbeiter",0),
        branche => array(0,0,1,"Branche",25),               business_id => array(0,0,6,"Kundentyp",0),
        owener => array(0,0,6,"CRM-User",0),                grafik => array(0,0,9,"Grafik",4),
        lead => array(0,0,6,"Leadquelle",0),                leadsrc => array(0,0,1,"Leadquelle",15),
        bland => array(0,0,6,"Bundesland",0),               taxzone_id => array(0,1,6,"Steuerzone",0),
        salesman_id => array(0,0,6,"Vertriebler",0),        konzern    => array(0,0,6,"Konzern",0),
        shiptoname => array(1,0,1,"Liefername",75),         shiptostreet => array(1,0,1,"Lieferstrasse",75),
        shiptobland => array(1,0,6,"Liefer-Bundesland",0),  shiptocountry => array(1,0,8,"Lieferland",3),
        shiptozipcode => array(1,0,2,"Liefer-Plz",10),      shiptocity => array(1,0,1,"Lieferort",75),
        shiptocontact => array(1,0,1,"Kontakt",75),         shiptophone => array(1,0,3,"Liefer Telefon",30),
        shiptofax => array(1,0,3,"Lieferfax",30),           shiptoemail => array(1,0,5,"Liefer-eMail",0),
        shiptodepartment_1 => array(1,0,1,"Lieferzusatzname",75),
        shiptodepartment_2 => array(1,0,1,"Lieferabteilung",75),
        headcount => array(0,0,6,"Anzahl Mitarbeiter",10),   currency_id => array(0,0,6,"Currency",0));
        $keys=array_keys($daten);
    $dbf=array_keys($dbfld);
    $anzahl=count($keys);
    $fid=$daten["id"];
    $fehler="ok";
    $ala=false;
    if ($daten["greeting_"]<>"") $daten["greeting"]=$daten["greeting_"];
    if ($daten["branche_"]<>"") $daten["branche"]=$daten["branche_"];
    $tels1=array();$tels2=array();
    for ($i=0; $i<$anzahl; $i++) {
        if (in_array($keys[$i],$dbf)) {
            $tmpval=trim($daten[$keys[$i]]);
            if ($dbfld[$keys[$i]][0]==1) {  // select für Lieferanschrift bilden
                if ($tmpval) $ala=true;
                if (!chkFld($tmpval,$dbfld[$keys[$i]][1],$dbfld[$keys[$i]][2],$dbfld[$keys[$i]][4])) {
                    $fehler=$dbfld[$keys[$i]][3];
                    $i=$anzahl;
                } else {
                    if (in_array($dbfld[$keys[$i]][2],array(0,1,2,3,4,5,7,8,9))) { //Daten == Zeichenkette
                        if (empty($tmpval)) {
                            $query1.=$keys[$i]."=null,";
                        } else {
                            $query1.=$keys[$i]."='".$tmpval."',";
                        }
                    } else {                            //Daten == Zahl
                        if (empty($tmpval) && !$tmpval===0) {
                            $query1.=$keys[$i]."=null,";
                        } else {
                            $query1.=$keys[$i]."=".$tmpval.",";
                        }
                    }
                    if ($keys[$i]=="shiptophone"||$keys[$i]=="shiptofax") $tels2[]=$tmpval;
                }
            } else {            // select für Rechnungsanschrift bilden
                if (!chkFld($tmpval,$dbfld[$keys[$i]][1],$dbfld[$keys[$i]][2],$dbfld[$keys[$i]][4])) {
                    $fehler=$dbfld[$keys[$i]][3];
                    $i=$anzahl;
                } else {
                    if (in_array($dbfld[$keys[$i]][2],array(0,1,2,3,4,5,7,8,9))) {
                        if (empty($tmpval)) {
                            $query0.=$keys[$i]."=null,";
                        } else {
                            $query0.=$keys[$i]."='".$tmpval."',";
                        }
                    } else {                            //Daten == Zahl
                        if (empty($tmpval) && !$tmpval===0) {
                            $query0.=$keys[$i]."=null,";
                        } else {
                            $query0.=$keys[$i]."=".$tmpval.",";
                        }
                    }
                    if ($keys[$i]=="phone"||$keys[$i]=="fax") $tels1[]=$tmpval;
                }
            }
        }
    }
    if ($daten["direct_debit"]=="t") {
        if (empty($daten["bank"]) or empty($daten["account_number"]) or empty($daten["bank_code"])) {
            $fehler="Lastschrift: Bankverbindung fehlt";
        } else {
            $query0.="direct_debit='t',";
        }
    } else {
            $query0.="direct_debit='f',";
    }
    if ($fehler=="ok") {
        if ($daten["customernumber"]) {
            $query0=substr($query0,0,-1);
            $DIR="C".$daten["customernumber"];
        } else if ($daten["vendornumber"]) {
            $query0=substr($query0,0,-1);
            $DIR="V".$daten["vendornumber"];
        } else {
            $tmpnr=newnr($tab[$typ],$daten["business_id"]);
            if ($typ=="C") {
                $DIR="C".$tmpnr;
                $query0=$query0."customernumber='$tmpnr' ";
            } else {
                $DIR="V".$tmpnr;
                $query0=$query0."vendornumber='$tmpnr' ";
            }
        }
        include("links.php");
        if (!is_dir($dir_abs."/".$DIR)) { // Wird wo definiert???
            mkdir($dir_abs."/".$DIR);
        }
        chmod($dir_abs."/".$DIR,octdec($_SESSION['dir_mode']));
        if ( $_SESSION['dir_group'] ) chgrp($dir_abs."/".$DIR,$_SESSION['dir_group']);
        $link_dir_cv=$typ=="C"?$link_dir_cust:$link_dir_vend;
         if (!$dir_abs.$link_dir_cv."/".mkDirName($daten['name'])."_".$DIR) {
            if (is_dir($dir_abs.$link_dir_cv)) {
                if ($dh = opendir($dir_abs.$link_dir_cv)) {
                    while (($link = readdir($dh)) !== false) {
                              $split = preg_split("/(_".$typ.")([\d]{1,})/",$link, 2, PREG_SPLIT_DELIM_CAPTURE);
                          if ($split[1].$split[2] == "_".$DIR) {
                             unlink($dir_abs.$link_dir_cv."/".$link);
                        }
                    }
                      }
                   closedir($dh);
            }
            symlink($dir_abs."/".$DIR, $dir_abs.$link_dir_cv."/".mkDirName($daten['name'])."_".$DIR);
        }
        $query1=substr($query1,0,-1)." ";
        $sql0="update ".$tab[$typ]." set $query0 where id=$fid";
        mkTelNummer($fid,$typ,$tels1);
        if ($bildok) {
            require_once("documents.php");
            $dbfile=new document();
            $dbfile->setDocData("descript","Firmenlogo von ".$daten["name"]);
            $dbfile->uploadDocument($datei,"/$DIR");
        }
        $rc1=true;
        if ($ala) {
            if ($daten["shipto_id"]>0) {
                $sql1="update shipto set $query1 where shipto_id=".$daten["shipto_id"];
                $rc1=$GLOBALS['dbh']->query($sql1);
            } else {
                $sid=newShipto($fid);
                if ($sid) {
                    $sql1="update shipto set $query1 where shipto_id=".$sid;
                    $rc1=$GLOBALS['dbh']->query($sql1);
                }
            };
            if ($rc1) mkTelNummer($fid,"S",$tels2);
        }
        $rc0=$GLOBALS['dbh']->query($sql0);
        if ($rc0 and $rc1) {
            $rc=$fid;
            //ab hier CVARS
            //Alle möglichen Vars holen
            $sql = "SELECT id,name,type from custom_variable_configs where module = 'CT'";
            $vars = $GLOBALS['dbh']->getAll($sql);
            if ($vars) foreach ($vars as $row) $vartype[$row["name"]] = array("id"=>$row["id"],"type"=>$row["type"]);
            $sqltpl = "insert into custom_variables (config_id,trans_id,bool_value,timestamp_value,text_value,number_value)";
            $sqltpl.= "values (%d,%d,%s,%s,%s,%s)";
            //bisherige Einträge löschen.
            $sql = "delete from custom_variables where trans_id = ".$daten["id"];
            $rcc = $GLOBALS['dbh']->query($sql);
            //Insert bilden
            foreach ($daten as $key=>$val) {
                if (substr($key,0,8) == "vc_cvar_") {
                //eine CVar
                   $name = substr($key,8);
                    //Values erzeugen
                    $date = "null";
                    $num = "null";
                    $bool = "null";
                    $text = "null";
                    switch ($vartype[$name]["type"]) {
                        case "select"   :
                        case "textfield":
                        case "text"     : $text = "'$val'"; break;
                        case "number"   : $num  = sprintf("%0.2f",$val); break;
                        case "customer" : $num  = $val; break;
                        case "date"     : $date = "'".date2db($val)."'"; break;
                        case "bool"     : $bool = ($val=='t')?"'t'":"'f'"; break;
                        default        : $text = "'$val'"; break;
                    };
                    $sql = sprintf($sqltpl,$vartype[$name]["id"],$daten["id"],$bool,$date,$text,$num);
                    $rcc = $GLOBALS['dbh']->query($sql);
                }
            }
        } else { $rc=-1; $fehler=".:unknown:."; };
        return array($rc,$fehler);
    } else {
        if ($daten["saveneu"]){
            $sql="delete from ".$tab[$typ]." where id=".$daten["id"];
            $rc0=$GLOBALS['dbh']->query($sql);
        };
        return array(-1,$fehler);
    };
}

function newShipto($fid) {
    $rc=$GLOBALS['dbh']->query("BEGIN");
    $newID=uniqid (rand());
    $sql="insert into shipto (trans_id,shiptoname,module) values ($fid,'$newID','CT')";
    $rc=$GLOBALS['dbh']->query($sql);
    $sql="select shipto_id from shipto where shiptoname='$newID'";
    $rs=$GLOBALS['dbh']->getOne($sql);
    if ($rs["shipto_id"]) {
        $GLOBALS['dbh']->query("COMMIT");
        return $rs["shipto_id"];
    } else {
        $GLOBALS['dbh']->query("ROLLBACK");
        return false;
    }
}

/****************************************************
* newcustnr
* out: id = string
* eine Kundennummer erzeugen
*****************************************************/
function newnr($typ,$bid=0) {
    $rc=$GLOBALS['dbh']->query("BEGIN");
    if ($bid>0) {
        $rs=$GLOBALS['dbh']->getOne("select customernumberinit  as ".$typ."number from business where id = $bid");
    } else {
        $rs=$GLOBALS['dbh']->getOne("select ".$typ."number from defaults");
    };
    preg_match("/([0-9]*)([^0-9]*)([0-9]*)/",$rs[$typ."number"],$t);
    if ( $t[3] != '' ) {
        $pre = $t[1].$t[2];
        $nr = $t[3];
    } else if ( $t[2] != '' ) {
        $pre = $t[1];
        $nr = $t[2];
    } else {
        $pre = '';
        $nr = $t[1];
    };
    $len = strlen($nr);
    $y = sprintf("%0".$len."d",$nr+1);
    $newnr=$pre.$y;
    if ($bid>0) {
        $rc=$GLOBALS['dbh']->query("update business set customernumberinit='$newnr' where id = $bid");
    } else {
        $rc=$GLOBALS['dbh']->query("update defaults set ".$typ."number='$newnr'");
    }
    if ($rc) { $GLOBALS['dbh']->query("COMMIT"); }
    else { $GLOBALS['dbh']->query("ROLLBACK"); $newnr=""; };
    return $newnr;
}

/****************************************************
* mknewFirma
* in: id = int
* out: id = int
* Kundensatz erzeugen ( insert )
*****************************************************/
function mknewFirma($id,$typ) {
    $tab=array("C" => "customer","V" => "vendor");
    $tmpName_0="01010101";
    $tmpName_1=uniqid (rand());
    $sql="DELETE FROM ".$tab[$typ]." WHERE name LIKE '".$tmpName_0."%'";
   // $rc=$GLOBALS['dbh']->query($sql); Kommentiert bis ERP-Bug #2201 gefixt ist
    if (!$id) {$uid='null';} else {$uid=$id;};
    $sql="insert into ".$tab[$typ]." (name,employee,currency_id,taxzone_id) values ('$tmpName_0$tmpName_1',$uid,1,4)";
    $rc=$GLOBALS['dbh']->query($sql);
    if ($rc) {
        $sql="select id from ".$tab[$typ]." where name = '$tmpName_0$tmpName_1'";
        $rs=$GLOBALS['dbh']->getAll($sql);
        if ($rs) {
            $id=$rs[0]["id"];
        } else {
            $id=false;
        }
    } else {
        $id=false;
    }
return $id;
}


/****************************************************
* saveNeuFirmaStamm
* in: daten = array
* out: rc = int
* KundenDaten sichern ( insert )
*****************************************************/
function saveNeuFirmaStamm($daten,$files,$typ="C") {
    $daten["id"]=mknewFirma($_SESSION["loginCRM"],$typ);
    $rs=saveFirmaStamm($daten,$files,$typ,true);
    return $rs;
}


function getKonzerne($fid,$Q,$typ="T") {
    if ($Q=="C") $tab="customer";
    else $tab="vendor";
    if ($typ=="T") {
        if ($Q=="C") $sql="select id,name,zipcode,city,country,customernumber as number,konzern from customer where konzern = $fid";
        else $sql="select id,name,zipcode,city,country,vendornumber as number,konzern from $tab where konzern = $fid";
    } else {
        if ($Q=="C") $sql="select id,name,zipcode,city,country,customernumber as number,konzern from customer where id = $fid";
        else $sql="select id,name,zipcode,city,country,vendornumber as number,konzern from $tab where id = $fid";
    }
    $rs=$GLOBALS['dbh']->getAll($sql);
    return $rs;
}
function getCustTermin($id,$tab,$day,$month,$year) {
    if ($tab=="P") {
        $sql="select * from  termine T left join terminmember M on T.id=M.termin where M.member = $id ";
    } else {
        $sql="select T.*,C.cp_name,X.id as cid from  termine T left join terminmember M on T.id=M.termin ";
        $sql.="left join contacts C on C.cp_id=M.Member left join telcall X on X.termin_id=T.id ";
        $sql.="where M.member in ";
        $sql.="($id,(select cp_id from contacts where cp_cv_id = 473))";
    }
    if ($day>0 and $month>0 and $year>0) {
        $sql.=" and start = '$year-$month-$day 00:00:00'";
    } else if ($month>0 and $year>0) {
        if  ( $month < 12 ) { $month2 = $month+1; $year2 = $year; }
        else { $month2 = '01'; $year2 = $year + 1; };
        $sql .= " and  ( start between '$year-$month-01 00:00:00' and '$year2-$month2-01 00:00:00' ) ";
    } else {
        $day=date("Y-m-d 00:00:00");
        $sql.= " and start >= '$day' order by start limit 5 ";
    }
    $rs = $GLOBALS['dbh']->getAll($sql);
    return $rs;
}
/****************************************************
* doReportC
* in: data = array
* out: rc = int
* Einen Report �ber Kunden,abweichende Lieferanschrift
* und Kontakte erzeugen
*****************************************************/
function doReport($data,$typ="C") {
    $tab      = array("C" => "customer","V" => "vendor");
    $login    = $_SESSION["login"];
    $felder   = substr($data['felder'],0,-1);
    $tmp      = suchstr($data,$typ);
    $where    = $tmp["where"]; $tabs = $tmp["tabs"];
    if ($typ=="C") {
        $rechte="(".berechtigung("C.").")";
    } else {
        $rechte="true";
    }
    if (!preg_match('/P./',$felder)) {
        $where=($where=="")?"":"and $where";
        if (preg_match('/shipto/i',$tabs) or preg_match('/S./',$felder)) {
            $sql="select $felder from ".$tab[$typ]." ".$typ." left join shipto S ";
            $sql.="on S.trans_id=".$typ.".id where (S.module='CT' or S.module is null or S.module='') and $rechte $where order by ".$typ.".name";
        } else {
            $sql="select $felder from ".$tab[$typ]." ".$typ." where $rechte $where order by ".$typ.".name";
        }
    } else {
        $rechte.=(($rechte)?" and (":"(").berechtigung("P.cp_").")";
        $where=($where=="")?"":"and $where";
        if (preg_match('/shipto/i',$tabs) or preg_match('/S./',$felder)) {
            $sql="select $felder from ".$tab[$typ]." ".$typ." left join shipto S ";
            $sql.="on S.trans_id=".$typ.".id left join contacts P on ".$typ.".id=P.cp_cv_id ";
            $sql.="where (S.module='CT' or S.module is null or S.module='')  and $rechte $where order by ".$typ.".name,P.cp_name";
        } else {
            $sql="select $felder from  ".$typ." ".$typ." left join contacts P ";
            $sql.="on ".$typ.".id=P.cp_cv_id where $rechte $where order by ".$typ.".name,P.cp_name";
        }
    }
    $rc=$GLOBALS['dbh']->getAll($sql);
    $f=fopen('../tmp/report_'.$login.'.csv',"w");
    fputs($f,$felder."\n");
    if ($rc) {
        foreach ($rc as $row) {
            $tmp="";
            foreach($row as $fld) {
                $tmp.="$fld,";
            }
            fputs($f,substr($tmp,0,-1)."\n");
        };
        fclose($f);
        return true;
    } else {
        fputs($f,"Keine Treffer.\n");
        fclose($f);
          return false;
    }
}
function getAnreden() {
    $sql="SELECT distinct (greeting) FROM customer WHERE greeting != '' UNION SELECT distinct (greeting) FROM vendor WHERE greeting != ''";
    $rs=$GLOBALS['dbh']->getAll($sql);
    return $rs;
}
function getBranchen() {
    $sql="select distinct (branche) from customer";
    $rs=$GLOBALS['dbh']->getAll($sql);
    return $rs;
}
function getVariablen($id) {
    if (!($id>0)) return false;
    $sql="select C.name,C.description,C.type,C.options,C.default_value,V.text_Value,V.bool_value,V.timestamp_value,V.number_value from  ";
    $sql.="custom_variables V left join custom_variable_configs C on V.config_id=C.id ";
    $sql.="where trans_id = $id and module = 'CT'";
    $rs=$GLOBALS['dbh']->getAll($sql);
    return $rs;
}
function getAlleVariablen() {
    $sql = "select id,name,description,type,default_value,options from custom_variable_configs where module = 'CT' order by sortkey";
    $rs  = $GLOBALS['dbh']->getAll($sql);
    return $rs;
}
function getUmsatzJahre($tab) {
    $sql="select distinct(substr(CAST(transdate as text),1,4)) as year from $tab";
    $rs=$GLOBALS['dbh']->getAll($sql);
    $leer=array(array("year"=>""));
    return array_merge($leer,$rs);
    return $rs;
}
function getTopParts($cid) {
    $limit = 5;
    $sql  = 'SELECT trans_id,parts_id,description,qty,discount,sellprice,fxsellprice,(qty*sellprice) as summe,ar.transdate,unit ';
    $sql .= 'from ar left join invoice on ar.id=trans_id where  customer_id = '.$cid.' order by  ';
    $ums = $GLOBALS['dbh']->getAll($sql.'summe desc limit '.$limit);
    $qty = $GLOBALS['dbh']->getAll($sql.'qty desc,ar.transdate desc limit '.$limit);
    $headU[] = array('trans_id'=>'','parts_id'=>'','description'=>'','qty'=>'','discount'=>'','sellprice'=>'','fxsellprice'=>'','summe'=>'','transdate'=>'','unit'=>'');
    $headQ = $headU;
    $headU[0]['description'] = '<b>Umsatzstärkste Artikel</b>';
    $headQ[0]['description'] = '<b>Am meisten verkaufte Artikel</b>';
    $top = array_merge($headU, $ums, $headQ, $qty);
    return $top;
}
/****************************************************
* getLeads
* out: array
* Leadsquellen holen
*****************************************************/
function getLeads() {
    $sql = "select * from leads order by lead";
    $rs = $GLOBALS['dbh']->getAll($sql);
    $tmp[] = array("id"=>"","lead"=>".:unknown:.");
    if ( !$rs )
        $rs = array();
    $rs = array_merge($tmp,$rs);
    return $rs;
}
/****************************************************
* getBusiness
* out: array
* Kundentype holen
*****************************************************/
function getBusiness() {
    $sql = "select * from business order by description";
    $rs = $GLOBALS['dbh']->getAll($sql);
    $leer = array(array("id"=>"","description"=>"----------"));
    return array_merge($leer,$rs);
}
function cvar_edit($id,$new=false) {
    $cvar = getVariablen($id);
    if ($cvar) foreach ($cvar as $row) {
        switch ($row["type"]) {
            case "select"   :
            case "text"     :
            case "textfield": ${$row["name"]} = $row["text_value"];
                              break;
            case "number"   : preg_match("/PRECISION[ ]*=[ ]*([0-9]+)/i",$row["options"],$pos);
                              if ($pos[1]) { ${$row["name"]} = sprintf("%0.".$pos[1]."f",$row["number_value"]);  }
                              else {${$row["name"]} = $row["number_value"];}
                              break;
            case "date"     : ${$row["name"]} = ($row["timestamp_value"])?db2date(substr($row["timestamp_value"],0,10)):"";
                              break;
            case "bool"     : ${$row["name"]} = ($row["bool_value"]=='t')?'checked':' ';
                              break;
        case "customer" : ${$row["name"]} = abs($row["number_value"]);
        }
    }
    $cvar = getAlleVariablen();
    $output = '';
    $kal = '';
    if ($cvar) foreach ($cvar as $row) {
        $input = "";
        switch ($row["type"]) {
                case "select"   : if (!${$row["name"]}) ${$row["name"]}=$row["options"];
                                  $input = "<select name='cvar_".$row["name"]."'>";
                                  $opt = explode("##",$row["default_value"]);
                                  if ($opt) foreach ($opt as $o) {
                                    $input .= "<option value='".$o."' ";
                                    $input .= (${$row["name"]}==$o)?"selected>$o":">$o";
                                  };
                                  $input .= "</select>";
                                  break;
                case "date"     : $kal = '<input name="cvar_'.$row["name"].'_button" id="cvar_'.$row["name"].'_trigger" type="button" value="?"> ';
                                  $kal.= '<script type="text/javascript"><!-- '."\n";
                                  $kal.= 'Calendar.setup({ inputField : "cvar_'.$row["name"].'",';
                                  $kal.= 'ifFormat   : "%d.%m.%Y",';
                                  $kal.= 'align      : "BL",';
                                  $kal.= 'button     : "cvar_'.$row["name"].'_trigger"});';
                                  $kal.= "\n".'--></script>'."\n";
                case "customer" :
                case "number"   :
                case "text"     : $input = "<input type='text' name='cvar_".$row["name"]."' id='cvar_".$row["name"]."'  value='";
                                  if ($new) {
                                     $input .= ( isset(${$row["name"]}) ) ? ${$row["name"]} : $row["default_value"];
                                     $input .= "'>".$kal;
                                  } else {
                                     $input.= ${$row["name"]}."'>".$kal;
                                  }
                                  $kal = "";
                                  break;
                case "textfield": preg_match("/width[ ]*=[ ]*(\d+)/i",$row["options"],$hit); $w = (isset($hit[1])&&$hit[1]>5)?$hit[1]:30;
                                  preg_match("/height[ ]*=[ ]*(\d+)/i",$row["options"],$hit); $h = (isset($hit[1])&&$hit[1]>1)?$hit[1]:3;
                                  //$input = "<textarea cols='$w' rows='$h' name='cvar_".$row["name"]."'>".${c_var.$row["name"]}."</textarea>";
                                  $input = "<textarea cols='$w' rows='$h' name='cvar_".$row["name"]."'>".$row["name"]."</textarea>";
                                  break;
                case "bool"     : if ( (isset(${$row["name"]}) and ${$row["name"]} == '') && $new) ${$row["name"]}=($row["default_value"])?"checked":"";
                                  $input = "<input type='checkbox' name='cvar_".$row["name"]."' value='t' ";
                                  $input .= ( isset(${$row["name"]}) ) ? ${$row["name"]}.'>' : '>';
                                  break;
            }
            $output .= "<div class='zeile2'><span class='label klein'>";
            $output .= $row["description"]."</span><span class='value'>".$input."</span></div>\n";
    }
    return $output;
}

function leertpl (&$t,$tpl,$param,$msg="",$suchmaske=false,$ui=false ) { //$param = Array mit den Get- oder PostWerten
        $jscal ="<style type='text/css'>@import url(../js/jscalendar/calendar-win2k-1.css);</style>\n";
        $jscal.="<script type='text/javascript' src='../js/jscalendar/calendar.js'></script>\n";
        $jscal.="<script type='text/javascript' src='../js/jscalendar/lang/calendar-de.js'></script>\n";
        $jscal.="<script type='text/javascript' src='../js/jscalendar/calendar-setup.js'></script>\n";
        if ( $ui ) $t->set_file(array("fa1" => "companies".$tpl.".tpl"));
        else       $t->set_file(array("fa1" => "firmen".$tpl.".tpl"));
        $menu =  $_SESSION['menu'];
        $t->set_var(array(
            'FAART'         => ($param['Q']=="C")?".:Customer:.":".:Vendor:.",
            'FAART2'        => ($param['Q']=="C")?".:Customer Name:.":".:Vendor Name:.",
            'Q'             => $param['Q'],
            'Btn1'          => "",
            'Btn2'          => "",
            'Msg'           => $msg,
            'action'        => $ui?"":"firmen".$tpl.".php?Q=$typ",
            'id'            => "",
            'greeting_'     => $param['greeting'],
            'name'          => $param['name'],
            'department_1'  => "",
            'department_2'  => "",
            'street'        => $param['street'],
            'country'       => "D",
            'zipcode'       => $param['zipcode'],
            'city'          => $param['city'],
            'phone'         => $param['phone'],
            'fax'           => "",
            'email'         => "",
            'homepage'      => "",
            'sw'            => "",
            'branche_'      => "",
            'vendornumber'  => "",
            'customernumber'  => "",
            'kdnr'          => "",
            'v_customer_id' => "",
            'ustid'         => "",
            'taxnumber'     => "",
            'contact'       => "",
            'leadsrc'       => "",
            'notes'         => "",
            'bank'          => "",
            'bank_code'     => "",
            'iban'          => "",
            'bic'           => "",
            'headcount'     => "",
            'account_number' => "",
            'direct_debitf'  => "checked",
            'preon'         => ($_SESSION["preon"])?"checked":"",
            'terms'         => "",
            'kreditlim'     => "",
            'op'            => "",
            'preisgrp'      => "",
            'shiptoname'    => "",
            'shiptodepartment_1' => "",
            'shiptodepartment_2' => "",
            'shiptostreet'   => "",
            'shiptocountry'  => "",
            'shiptozipcode'  => "",
            'shiptocity'     => "",
            'shiptophone'    => "",
            'shiptofax'      => "",
            'shiptoemail'    => "",
            'shiptocontact'  => "",
            'GEODB'          => ($_SESSION['GEODB']=='t')?'1==1':'1>2',
            //'GEOS'           => ($_SESSION['GEODB']=='t')?"visible":"hidden",
            'showGeo'        => ($_SESSION['GEODB']=='t')?'':'style="display:none"',
            'GEO1'           => ($_SESSION['GEODB']=='t')?"":"!--",
            'GEO2'           => ($_SESSION['GEODB']=='t')?"":"--",
            'BLZ1'           => ($_SESSION['BLZDB']=='t')?"":"!--",
            'BLZ2'           => ($_SESSION['BLZDB']=='t')?"":"--",
            'employee'       => $_SESSION["loginCRM"],
            'init'           => $_SESSION["login"],
            'txid4'          => "selected",
            'cvars'          => cvar_edit(0,TRUE),
            'variablen'      => ""
            ));
        $jahre = getUmsatzJahre(($param['Q']=="C")?"ar":"ap");
        doBlock($t,"fa1","YearListe","YL",$jahre,"year","year",false);
        $lang = getLanguage();
        doBlock($t,"fa1","LAnguage","LA",$lang,"id","description",false);
        $kdtyp=getBusiness();
        doBlock($t,"fa1","TypListe","BT",$kdtyp,"id","description",false);
        $anreden=getAnreden();
        doBlock($t,"fa1","anreden","A",$anreden,"greeting","greeting",'');
        $payment=getPayment();
        doBlock($t,"fa1","payment","P",$payment,"id","description",'');
        $branchen=getBranchen();
        doBlock($t,"fa1","branchen","BR",$branchen,"branche","branche",'');
        $lead=getLeads();
        doBlock($t,"fa1","LeadListe","LL",$lead,"id","lead",'');
        $curr=getCurrencies();
        doBlock($t,"fa1","currency","C",$curr,"id","name",( isset($daten["currency_id"]) )?$daten["currency_id"]:'');
        if (!$suchmaske) {
            doBlock($t,"fa1","shiptos","ST",$shiptos,"shipto_id",array("shiptoname","shiptodepartment_1"),false);
        }
        $bundesland=getBundesland(false);
        doBlock($t,"fa1","buland","BL",$bundesland,"id","bundesland",'');
        if (!$suchmaske) {
            doBlock($t,"fa1","buland2","BS",$bundesland,"id","bundesland",'');
            $employees=getAllUser(array(0=>true,1=>"%"));
            doBlock($t,"fa1","SalesmanListe","SM",$employees,"id","name",'');
        }
        $cvars = getCvars();
        $t->set_block('fa1','cvarListe','BlockCV');
        if ($cvars) {
            $i = 1;
            foreach ($cvars as $cvar) {
               switch ($cvar["type"]) {
                   case "bool"   : $fld = "<input type='checkbox' name='vc_cvar_".$cvar["name"]."' value='t'>";
                                   break;
                   case "date"   : $fld = "<input type='text' name='vc_cvar_".$cvar["name"]."' size='10' id='cvar_".$cvar["name"]."' value=''>";
                                   $fld.="<input name='cvar_".$cvar["name"]."_button' id='cvar_".$cvar["name"]."_trigger' type='button' value='?'>";
                                   $fld.= '<script type="text/javascript"><!-- '."\n";
                                   $fld.= 'Calendar.setup({ inputField : "cvar_'.$cvar["name"].'",';
                                   $fld.= 'ifFormat   : "%d.%m.%Y",';
                                   $fld.= 'align      : "BL",';
                                   $fld.= 'button     : "cvar_'.$cvar["name"].'_trigger"});';
                                   $fld.= "\n".'--></script>'."\n";

                                   break;
                   case "select" : $o = explode("##",$cvar["options"]);
                                   $fld = "<select name='vc_cvar_".$cvar["name"]."'>\n<option value=''>---------\n";
                                   foreach($o as $tmp) {
                                     $fld .= "<option value='$tmp'>$tmp\n";
                                   }
                                   $fld .= "</select>";
                                   break;
                   case "customer" :
                                   $fld = "<input type='hidden' name='vc_cvar_".$cvar["name"]."' value=''>";
                                   break;
                   default       : $fld = "<input type='text' name='vc_cvar_".$cvar["name"]."' value=''>";
               }
               $t->set_var(array(
                  'varlable'.$i => $cvar["description"],
                  'varfld'.$i   => $fld,
               ));
               if ($i==1) { $i = 2; }
               else { $i = 1;  $t->parse('BlockCV','cvarListe',true); }
            }
           if ($i==2) {
               $t->set_var(array(
                  'varlable2' => "",
                  'varfld2'   => "",
               ));
               $t->parse('BlockCV','cvarListe',true); }
        }
        $first[]=array("grpid"=>"","rechte"=>"w","grpname"=>".:public:.");
        $first[]=array("grpid"=>$_SESSION["loginCRM"],"rechte"=>"w","grpname"=>".:personal:.");
        $tmp=getGruppen();
        if ($tmp) { $user=array_merge($first,$tmp); }
        else { $user=$first; };
        doBlock($t,"fa1","OwenerListe","OL",$user,"grpid","grpname",false);
} // leertpl

function vartpl( &$t, $daten, $typ, $msg, $btn1, $btn2, $tpl, $suchmaske=false, $ui=false ) {
        $jscal ="<style type='text/css'>@import url(../js/jscalendar/calendar-win2k-1.css);</style>\n";
        $jscal.="<script type='text/javascript' src='../js/jscalendar/calendar.js'></script>\n";
        $jscal.="<script type='text/javascript' src='../js/jscalendar/lang/calendar-de.js'></script>\n";
        $jscal.="<script type='text/javascript' src='../js/jscalendar/calendar-setup.js'></script>\n";
        if ( isset($daten["grafik"]) ) {
            if ($typ=="C") { $DIR="C".$daten["customernumber"]; }
            else { $DIR="V".$daten["vendornumber"]; };
            if (file_exists("dokumente/".$_SESSION["dbname"]."/$DIR/logo.".$daten["grafik"])) {
                $Image="<img src='dokumente/".$_SESSION["dbname"]."/$DIR/logo.".$daten["grafik"]."' ".$daten["icon"].">";
            } else {
                $Image="Bild ($DIR/logo.".$daten["grafik"].") nicht<br>im Verzeichnis";
            }
        } else {
           $Image = '';
        };
        $tmp = false;
        if ( !$suchmaske ) $tmp = getVariablen($daten["id"]);
        $varablen=($tmp>0)?count($tmp)." Variablen":"";
        if ( $ui ) $t->set_file(array("fa1" => "companies".$tpl.".tpl"));
        else       $t->set_file(array("fa1" => "firmen".$tpl.".tpl"));
        if ( isset($daten["employee"]) and $daten['employee'] > 0 ) {
            $employee = $daten["employee"];
        } else if ( isset($daten["modemployee"]) ) {
            $employee = "ERP ".$daten["modemployee"];
        } else {
            $employee = '';
        };
        $t->set_var(array(
                'FAART'         => ($typ=="C")?".:Customer:.":".:Vendor:.",
                'FAART2'        => ($typ=="C")?".:Customer Name:.":".:Vendor Name:.",
                'mtime'         => ( isset($daten["mtime"]) )         ? $daten['mtime'] : '' ,
                'Q'             => $typ,
                'Btn1'          => $btn1,
                'Btn2'          => $btn2,
                'Msg'           => $msg,
                'preon'         => ( isset($daten["pre"]) )            ? "checked":'',
                'action'        => $ui?"":"firmen".$tpl.".php?Q=$typ",
                'id'            => ( isset($daten["id"]) )             ? $daten['id']:'',
                'customernumber'=> ( isset($daten["customernumber"]) ) ? $daten["customernumber"]:'',
                'vendornumber'  => ( isset($daten["vendornumber"]) )   ? $daten["vendornumber"]:'',
                'kdnr'          => ( isset($daten["nummer"]) )         ? $daten["nummer"]:'',
                'v_customer_id' => ( isset($daten["v_customer_id"]) )  ? $daten["v_customer_id"]:'',
                'name'          => ( isset($daten["name"]) )           ? $daten["name"]:'',
                'greeting_'     => ( isset($daten["greeting_"]) )      ? $daten["greeting_"]:'',
                'department_1'  => ( isset($daten["department_1"]) )   ? $daten["department_1"]:'',
                'department_2'  => ( isset($daten["department_2"]) )   ? $daten["department_2"]:'',
                'street'        => ( isset($daten["street"]) )         ? $daten["street"]:'',
                'country'       => ( isset($daten["country"]) )        ? $daten["country"]:'',
                'zipcode'       => ( isset($daten["zipcode"]) )        ? $daten["zipcode"]:'',
                'city'          => ( isset($daten["city"]) )           ? $daten["city"]:'',
                'phone'         => ( isset($daten["phone"]) )          ? $daten["phone"]:'',
                'fax'           => ( isset($daten["fax"]) )            ? $daten["fax"]:'',
                'email'         => ( isset($daten["email"]) )          ? $daten["email"]:'',
                'homepage'      => ( isset($daten["homepage"]) )       ? $daten["homepage"]:'',
                'sw'            => ( isset($daten["sw"]) )             ? $daten["sw"]:'',
                'konzern'       => ( isset($daten["konzern"]) )        ? $daten["konzern"]:'',
                'konzernname'   => ( isset($daten["konzernname"]) )    ? $daten["konzernname"]:'',
                'branche_'      => ( isset($daten["branche_"]) )       ? $daten["branche_"]:'',
                'ustid'         => ( isset($daten["ustid"]) )          ? $daten["ustid"]:'',
                'taxnumber'     => ( isset($daten["taxnumber"]) )      ? $daten["taxnumber"]:'',
                'contact'       => ( isset($daten["contact"]) )        ? $daten["contact"]:'',
                'leadsrc'       => ( isset($daten["leadsrc"]) )        ? $daten["leadsrc"]:'',
                'notes'         => ( isset($daten["notes"]) )          ? $daten["notes"]:'',
                'bank'          => ( isset($daten["bank"]) )           ? $daten["bank"]:'',
                'bank_code'     => ( isset($daten["bank_code"]) )      ? $daten["bank_code"]:'',
                'iban'          => ( isset($daten["iban"]) )           ? $daten["iban"]:'',
                'bic'           => ( isset($daten["bic"]) )            ? $daten["bic"]:'',
                'headcount'     => ( isset($daten["headcount"]) )      ? $daten["headcount"]:'',
                'direct_debit'.(( isset($daten['direct_debit']) )?$daten['direct_debit']:'') => "checked",
                'account_number' => ( isset($daten["account_number"]) )? $daten["account_number"]:'',
                'terms'         => ( isset($daten["terms"]) )          ? $daten["terms"]:'',
                'kreditlim'     => ( isset($daten["creditlimit"]) )    ? $daten["creditlimit"]:'',
                'umsatz'        => ( isset($daten["umsatz"]) )         ? $daten["umsatz"]:'',
                'op'            => ( isset($daten["op"]) )             ? $daten["op"]:'',
                'preisgrp'      => ( isset($daten["preisgroup"]) )     ? $daten["preisgroup"]:'',
                'IMG'           => $Image,
                'grafik'        => ( isset($daten["grafik"]) )         ? $daten["grafik"]:'',
                'init'          => $employee,
                'login'         => $_SESSION{"login"},
                'employee'      => $_SESSION["loginCRM"],
                'password'      => ( isset($_SESSION["password"]) )    ? $_SESSION["password"]:'',
                'txid'.(( isset($daten["taxzone_id"]) )?$daten["taxzone_id"]:'') => "selected",
                'GEODB'         => ($_SESSION['GEODB']=='t')?'1==1':'1>2',
                'GEOS'          => ($_SESSION['GEODB']=='t')?"visible":"hidden",
                'GEO1'          => ($_SESSION['GEODB']=='t')?'':"!--",
                'GEO2'          => ($_SESSION['GEODB']=='t')?'':"--",
                'BLZ1'          => ($_SESSION['BLZDB']=='t')?'':"!--",
                'BLZ2'          => ($_SESSION['BLZDB']=='t')?'':"--",
                'cvars'         => ( isset($daten['id']) )?cvar_edit($daten["id"]):'',
                'variablen'     => $varablen
        ));
        $jahre = getUmsatzJahre(($typ=="C")?"ar":"ap");
        doBlock($t,"fa1","YearListe","YL",$jahre,"year","year",$daten["year"]);
        $lang = getLanguage();
        doBlock($t,"fa1","LAnguage","LA",$lang,"id","description",$daten["language_id"]);
        $kdtyp=getBusiness();
        doBlock($t,"fa1","TypListe","BT",$kdtyp,"id","description",$daten["business_id"]);
        $lead=getLeads();
        doBlock($t,"fa1","LeadListe","LL",$lead,"id","lead",$daten["lead"]);
        if ( isset($daten['id']) ) {
            $shiptos=getAllShipto($daten["id"],$typ);
            doBlock($t,"fa1","shiptos","ST",$shiptos,"shipto_id",array("shiptoname","shiptostreet","shiptocity"),false);
        };
        $anreden=getAnreden();
        doBlock($t,"fa1","anreden","A",$anreden,"greeting","greeting",( isset($daten["greeting"]) )?$daten["greeting"]:'');
        $payment=getPayment();
        doBlock($t,"fa1","payment","P",$payment,"id","description",( isset($daten["payment_id"]) )?$daten["payment_id"]:'');
        $branchen=getBranchen();
        doBlock($t,"fa1","branchen","BR",$branchen,"branche","branche",( isset($daten["branche"]) )?$daten["branche"]:'');
        $bundesland=getBundesland(strtoupper($daten["country"]));
        doBlock($t,"fa1","buland","BL",$bundesland,"id","bundesland",( isset($daten["bland"]) )?$daten["bland"]:'');
        $curr=getCurrencies();
        doBlock($t,"fa1","currency","C",$curr,"id","name",( isset($daten["currency_id"]) )?$daten["currency_id"]:'');
        $cvars = getCvars();
        $t->set_block('fa1','cvarListe','BlockCV');
        if ($cvars) {
            $i = 1;
            foreach ($cvars as $cvar) {
               switch ($cvar["type"]) {
                   case "bool"   : $fld = "<input type='checkbox' name='vc_cvar_".$cvar["name"]."' value='t'";
                                   if ( isset($daten["vc_cvar_".$cvar["name"]]) and $daten["vc_cvar_".$cvar["name"]]=="t") $fld .= " checked";
                                   $fld.= ">";
                                   break;
                   case "date"   : $fld = "<input type='text' name='vc_cvar_".$cvar["name"]."' size='10' value='";
                                   $fld.= db2date($daten["vc_cvar_".$cvar["name"]])."' id='cvar_".$cvar["name"]."'>";
                                   $fld.="<input name='cvar_".$cvar["name"]."_button' id='cvar_".$cvar["name"]."_trigger' type='button' value='?'>";
                                   $fld.= '<script type="text/javascript"><!-- '."\n";
                                   $fld.= 'Calendar.setup({ inputField : "cvar_'.$cvar["name"].'",';
                                   $fld.= 'ifFormat   : "%d.%m.%Y",';
                                   $fld.= 'align      : "BL",';
                                   $fld.= 'button     : "cvar_'.$cvar["name"].'_trigger"});';
                                   $fld.= "\n".'--></script>'."\n";
                                   break;
                   case "select" : $o = explode("##",$cvar["options"]);
                                   $fld = "<select name='vc_cvar_".$cvar["name"]."'>\n<option value=''>---------\n";
                                   foreach($o as $tmp) {
                                     $fld .= "<option value='$tmp'";
                                     if ($daten["vc_cvar_".$cvar["name"]]==$tmp) $fld .= " selected";
                                     $fld .= ">$tmp\n";
                                   }
                                   $fld .= "</select>";
                                   break;
                   case "customer" : $name = getCvarName($daten['vc_cvar_'.$cvar['name']]);
                                     $fld = "<input type='hidden' name='vc_cvar_".$cvar["name"]."' value='";
                                     $fld.= $daten['vc_cvar_'.$cvar['name']]."'>";
                                     $fld .= $name.' ('.$daten['vc_cvar_'.$cvar['name']].')';
                                     break;
                   default     :
                   $fld = '<input type="text" name="vc_cvar_'.$cvar['name'].'" value="';
                                   $fld.= $daten['vc_cvar_'.$cvar['name']].'">';
               }
               $t->set_var(array(
                  'varlable'.$i => $cvar["description"],
                  'varfld'.$i   => $fld,
               ));
               if ($i==1) { $i = 2; }
               else { $i = 1;  $t->parse('BlockCV','cvarListe',true); }
            }
           if ($i==2) {
               $t->set_var(array(
                  'varlable2' => "",
                  'varfld2'   => "",
               ));
               $t->parse('BlockCV','cvarListe',true); }
        }
        if (!$suchmaske) {
            $bundesland=getBundesland(strtoupper($daten["shiptocountry"]));
            doBlock($t,"fa1","buland2","BS",$bundesland,"id","bundesland",$daten["shiptobland"]);
            $employees=getAllUser(array(0=>true,1=>"%"));
            doBlock($t,"fa1","SalesmanListe","SM",$employees,"id","name",$daten["salesman_id"]);
            /* Check if the user is allowed to change the access group - Behaviour changed by DO:
                Let (all) users change the group if none is set yet */
            if (!isset($daten["employee"]) || $daten["employee"]==$_SESSION["loginCRM"] || $daten["modemployee"]==$_SESSION["loginCRM"] ) {
                    $first[]=array("grpid"=>"","rechte"=>"w","grpname"=>".:public:.");
                    $first[]=array("grpid"=>$_SESSION["loginCRM"],"rechte"=>"w","grpname"=>".:personal:.");
                    $grps=getGruppen();
                    if ($grps) {
                        $user=array_merge($first,$grps);
                    } else {
                        $user=$first;
                    };
                    doBlock($t,"fa1","OwenerListe","OL",$user,"grpid","grpname",$daten["owener"]);
            } else {
                    $user[0] = array("grpid"=>$daten["owener"],"grpname"=>($daten["owener"])?getOneGrp($daten["owener"]):".:public:.");
                    doBlock($t,"fa1","OwenerListe","OL",$user,"grpid","grpname",$daten["owener"]);
                    /*$t->set_var(array(
                        grpid => $daten["owener"],
                        Gsel => "selected",
                        Gname => ($daten["owener"])?getOneGrp($daten["owener"]):".:public:.",
                    ));
                    $t->parse("Block","OwenerListe",true);*/
            }
        } //if (!$suchmaske)
} // vartpl

?>
