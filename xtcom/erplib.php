<?php
/*

shop muß gesetzt sein, obsolet nicht

!!!!! Kivi > 3.0.0 !!!!! currency_id

Multishop: Hierfür müssen benutzerdefinierte Variablen angelegt werden.
Typ:checkbox,  Name=shop[0-9A-Z]+, Bearbeitbar=nein 
Benutzerdefinierte Varisble "pepperkunde", Typ Text muß angelegt werden 

*/

class erp {

    var $db = false;
    var $error = false;
    var $pricegroup = 0;
    var $TAX = Array(1 => Array(),2 => Array(),3 => Array(),4 => Array());
    var $Bugru = Array();
    var $mkPart = true;
    var $divStd = false;
    var $divVerm = false;
    var $doordnr = false;
    var $docustnr = false;
    var $lager = 1;
    var $warehouse_id = 0;
    var $transtype = 0;
    var $preordnr = '';
    var $staffel = '';
    var $precustnr = '';
    var $OEinsPart = false;
    var $INVnetto = true; //Rechnungen mit Nettopreisen
    var $SHOPincl = true; //Shoppreise sind Brutto

    function erp($db,$error,$divStd,$divVerm,$doordnr,$docustnr,$preordnr,$precustnr,$INVnetto,$SHOPincl,$OEinsPart,$lager,$pricegroup,$staffel,$ERPusrID) {
        $this->db = $db;
        $this->pricegroup = (is_numeric($pricegroup))?$pricegroup:0;
        $this->staffel = $staffel;
        $this->employee_id = $ERPusrID;
        $this->error = $error;
        $this->divStd  = $divStd;
        $this->divVerm = $divVerm;
        $this->doordnr = $doordnr;
        $this->preordnr = $preordnr;
        $this->docustnr = $docustnr;
        $this->precustnr = $precustnr;
        $this->INVnetto = ($INVnetto == 1)?true:false;
        $this->SHOPincl = ($SHOPincl == 1)?true:false;
        $this->OEinsPart = ($OEinsPart == 1)?true:false;
        $this->lager = ($lager)?$lager:1;
        $tmp = getTax($this->db);
        $this->TAX = $tmp['TAX'];
        $this->Bugru = $tmp['Bugru'];        
        if ( $lager > 1 ) {
            $sql  = "SELECT warehouse_id from bin where id = ".$this->lager;
            $rs = $this->db->getOne($sql);
            if ( $rs['warehouse_id'] > 0 ) {
		        $this->warehouse_id = $rs['warehouse_id'];
                $sql = "SELECT id from transfer_type WHERE direction = 'in' and description = 'stock'";
                $rs = $this->db->getOne($sql);
                $this->transtype = $rs['id'];
            } else {
                $this->lager = 1;
            }
        }
        $sql = "SELECT * FROM custom_variable_configs WHERE name = 'pepperkunde'";
        $rs = $this->db->getOne($sql);
        if ( isset($rs['id'] ) ) {
            $this->cvarid = $rs['id'];
        } else {
            $this->cvarid = 0;
            $this->error->write('erplib','Benutzerdefinierte Variable pepperkunde fehlt!');    
        }
        $this->getcurr();
    }

    function getcurr() {
        $sql  = "SELECT * FROM currencies order by id";
        $rs   = $this->db->getAll($sql);
        if ( $rs ) foreach ( $rs as $row ) {
            $this->curr[$row['name']] = $row['id'];
        } else {
            $this->curr['EUR'] = 1;
        }
    }

    function getParts($stdprice=0,$shop=0,$start,$max=0) {
        $where = "WHERE 1=1 ";
        if ($stdprice>0) {
             $sql  = "SELECT P.partnumber,P.description,P.notes,P.weight,G.price as sellprice,P.sellprice as stdprice,";
             $sql .= "PG.partsgroup,P.image,P.buchungsgruppen_id as bugru,P.unit,P.ean ";
             if ( $this->staffel != '' ) $sql .= ','.$this->staffel.' as staffel';
             if ($this->lager>1) {
                   $sql .= ",(select sum(qty) from inventory where bin_id = ".$this->lager." and parts_id = P.id) as onhand ";
             } else {
                   $sql .= ",P.onhand ";
             }
             $sql .= "FROM parts P ";
             $sql .= "LEFT JOIN partsgroup PG on PG.id=P.partsgroup_id ";
             $sql .= "LEFT JOIN prices G on G.parts_id=P.id ";
             $where .= "AND (G.pricegroup_id=$stdprice ";
             $where .= "or G.pricegroup_id is null) ";
        } else {
             $sql  = "SELECT P.partnumber,P.description,P.notes,P.weight,P.sellprice,P.sellprice as stdprice,";
             $sql .= "PG.partsgroup,P.image,P.buchungsgruppen_id as bugru,P.unit,P.ean ";
             if ($this->lager>1) {
                   $sql .= ",(select sum(qty) from inventory where bin_id = ".$this->lager." and parts_id = P.id) as onhand ";
             } else {
                   $sql .= ",P.onhand ";
             }
             $sql .= "FROM parts P left join partsgroup PG on PG.id=P.partsgroup_id ";
        }
        if ($shop>0) {  
            $sql .= "LEFT JOIN custom_variables CV on CV.trans_id=P.id ";
            $where .= "AND (CV.config_id = (SELECT id FROM custom_variable_configs WHERE name = 'shop$shop') AND CV.bool_value = 't')";
        }
        $where .= "AND shop = 't' ";
        $where .= "AND obsolete = 'f' ";
        if ( $start > 0 ) $where .= "offset $start ";
        if ( $max > 0 ) $where   .= "limit $max ";
        //$where .= "ORDER BY P.partnumber";
        $rs = $this->db->getAll($sql.$where);
        if ($rs) for($i = 0; $i < count($rs); $i++) {
           $rs[$i]['tax'] = $this->TAX[4][$rs[$i]['bugru']]['rate'];
        }
        return $rs;
    }

    function getPartsLang($lang,$alle) {
        $sql  = "SELECT P.partnumber,L.translation,P.description,L.longdescription,P.notes,PG.partsgroup ";
        $sql .= "FROM parts P left join translation L on L.parts_id=P.id left join partsgroup PG on PG.id=P.partsgroup_id ";
        $sql .= "WHERE P.shop='t' and (L.language_id = $lang";
        if ($alle) {
            $sql .= " or L.language_id is Null)";
        } else { 
            $sql.=")"; 
        };
        $rs = $this->getAll($sql);
        $data=array();
        if ($rs) foreach ($rs as $row) {
            if (!$data[$row["partnumber"]]) $data[$row["partnumber"]]=$row;
        }
        return $data;
    }
    function getNewNr($typ) {
        /*
          so = Auftragsnummer
          customer = Kundennummer 
        */
        $typ .= "number";
        $sql = 'SELECT '.$typ.' FROM defaults';
        $rs = $this->db->getOne($sql);
        $i=strlen($rs["$typ"])-1;
        //Nummern können Buchstaben, Zeichen und Zahlen enthalten
        //nur die Zahlen von rechts werden aber inkrementiert.
        while($i>=0) {
            if ($rs["$typ"][$i] >= "0" and $rs["$typ"][$i]<="9") {
                $n=$rs["$typ"][$i].$n;
                $i--;
            } else {
                $pre = substr($rs["$typ"],0,$i+1);
                $i=-1;
            }
        };
        $nr = (int)$n + 1;
        $sonr = $pre.$nr;
        $sql = "UPDATE defaults SET $typ = '$sonr'";
        $rc = $this->db->query($sql);
        if (!$rc) {
            $this->error->write('erplib','Neue Nummer ($typ) nicht gesichert: '.$sonr);
        }
        return $sonr;
    }
    function newOrder($data) {
        /*Einen neuen Auftrag anlegen. Folgendes Array muß übergeben werden:
        $data = array(ordnumber,customer_id,employee_id,taxzone_id,amount,netamount,transdate,notes,intnotes,shipvia)
        Rückgabe oe.id */
        $this->db->begin();
        $incltax = ($this->INVnetto)?'f':'t';
        $sql  = "INSERT INTO oe (ordnumber,customer_id,employee_id,taxzone_id,taxincluded,currency_id,amount,netamount,transdate,notes,intnotes,shipvia,cusordnumber) ";
        $sql .= "values (:ordnumber,:customer_id,:employee_id,:taxzone_id,'$incltax',:currency_id,:amount,:netamount,:transdate,:notes,:intnotes,:shipvia,:cusordnumber)";
        $rc = $this->db->insert($sql,$data);
        $sql = "SELECT * FROM oe where ordnumber = '".$data["ordnumber"]."'";
        $rs = $this->db->getOne($sql);
        if (!$rs['id']) {
            $this->error->write('erplib','Auftrag erzeugen: '.$data["ordnumber"]);
            $this->db->rollback();
            return false;
        } else {
            $this->error->out(" Auftrag: ".$data["ordnumber"],true);
            return $rs['id'];
        }
    }
    function insParts($trans_id,$data,$longtxt) {
        /*Artikel in die orderitem einfügen. Folgende Daten müssen übergeben werden:
        $trans_id = (int) oe.id
        $data = array(trans_id,partnumber,description,longdescription,qty,sellprice,unit)*/
        $position = 1;
        foreach ($data as $row) {
             $row['trans_id'] = $trans_id;
             //$sql = "SELECT id FROM parts WHERE partnumber = '".$row['partnumber']."'";
             //$tmp = $this->db->getOne($sql);
             $tmp = $this->chkPartnumber($row,$this->OEinsPart,true);
             if ($tmp) {
                 $row['parts_id'] = $tmp['id'];
             } else {
                 if ($row['taxrate']>0 AND $this->TAX[$this->divStd['BUGRU']]['rate'] == $row['taxrate']/100) {
                      $row['parts_id'] = $this->divStd['ID'];
                      $row['partnumber'] = $this->divStd['NR'];
                      $row['taxrate']  = $this->divStd['TAX'];
                      $row['unit'] = $this->divStd['UNIT'];
                 } else if ($row['taxrate']>0 AND $this->TAX[$this->divVerm['BUGRU']]['rate'] == $row['taxrate']/100) {
                      $row['parts_id'] = $this->divVerm['ID'];
                      $row['partnumber'] = $this->divVerm['NR'];
                      $row['taxrate']  = $this->divVerm['TAX'];
                      $row['unit'] = $this->divVerm['UNIT'];
                 } else {
                      $row['parts_id'] = $this->divStd['ID'];
                      $row['partnumber'] = $this->divStd['NR'];
                      $row['taxrate']  = $this->divStd['TAX'];
                      $row['unit'] = $this->divStd['UNIT'];
                 };
                 $row['description'] .= ' Ersatzartikel';
                 $this->error->out('Ersatzartkel '.$row['parts_id']);
             }
             if ($this->INVnetto) {
                 if ($this->SHOPincl) 
                     $row['sellprice'] = round($row['sellprice'] / (100 + $row['taxrate']) * 100,2);
             } else {
                 if (!$this->SHOPincl) 
                     $row['sellprice'] = round($row['sellprice'] * (100 + $row['taxrate']) * 100,2);
             }
             $row['unit'] = $this->chkUnit($row['unit']);
             if ($longtxt == 1) {
                 //$row['longdescription'] = addslashes($row['longdescription']);
                 $row['longdescription'] = $row['longdescription'];
             } else {
                 //$row['longdescription'] = addslashes($tmp['longdescription']);
                 $row['longdescription'] = $tmp['longdescription'];
             }
             //$row['description'] = addslashes($row['description']);
             // brauche ich die pricegroup_id wirklich? Ja!!!
             $sql  = "INSERT INTO orderitems (trans_id,parts_id,description,longdescription,qty,sellprice,unit,pricegroup_id,discount,position) ";
             $sql .= "VALUES (:trans_id,:parts_id,:description,:longdescription,:qty,:sellprice,:unit,:pricegroup,0,:position)";
             $row["trans_id"]=$trans_id;
             $row["pricegroup"]=$self->pricegroup;
             $row['position'] = $position++;
             $rc = $this->db->insert($sql,$row);
             if (!$rc) {
                 $this->db->rollback();
                 return false;
             };
        };
        $this->db->commit();
        return true;
    }
    function insCustomer($data) {
        $this->error->out('Insert Kunde: '.$data["name"].' '); 
        if ($this->docustnr == 1) {
            $data['customernumber'] = $this->getNewNr('customer');
        } else {
            if ( $data['kunden_nr'] > 0 ) {
                $data['customernumber'] = $data['kunden_nr'];
                $sql = "SELECT id FROM customer WHERE customernumber = '".$data['customernumber']."'";
                $rs = $this->db->getOne($sql);
                if ( isset($rs['id']) ) {
                   $data['customernumber'] = $this->getNewNr('customer');
                   $this->error->out("Kd-Nr bereits vergeben ");
                }
            } else {
                $data['customernumber'] = $this->getNewNr('customer');
            }
        }
        $data['customernumber'] = $this->precustnr.$data['customernumber'];
        if ($data['customernumber']>0) {
            if (!$data['greeting']) $data['greeting'] = '';
            $sql  = "INSERT INTO customer (greeting,name,street,city,zipcode,country,contact,phone,email,customernumber,currency_id,taxzone_id)";
            $sql .= " VALUES (:greeting,:name,:street,:city,:zipcode,:country,:contact,:phone,:email,:customernumber,:currency_id,:taxzone_id)";
            $rc =  $this->db->insert($sql,$data);
            //echo "(".print_r($rc,true).")";
            $sql = "SELECT id FROM customer WHERE customernumber = '".$data['customernumber']."'";
            $rs = $this->db->getOne($sql);
            if ( isset($rs['id']) ) {
                $rc = $rs['id'];
                if ( $this->cvarid > 0 ) {
                    $sql = "INSERT INTO custom_variables (config_id,trans_id,text_value) VALUES (%d,%d,'%s')";
                    if ( $data['kunden_nr'] > 0 ) {
                        $cvar = $this->db->query(sprintf($sql,$this->cvarid,$rc,$data['kunden_nr']));
                    } else {
                        $cvar = $this->db->query(sprintf($sql,$this->cvarid,$rc,$data['shopid']));
                    }
                    if ( $cvar ) {
                        $this->error->out("Kd-Nr: ".$data['customernumber'].":".$rs['id']." CV");
                    } else {
                        $this->error->out("Kd-Nr: ".$data['customernumber'].":".$rs['id']." CVar-Fehler");
                    }
                } else {
                    $this->error->out("Kd-Nr: ".$data['customernumber'].":".$rs['id']." noCV");
                }
            } else {
                $this->error->write('erplib','Kunde anlegen: '.$data["name"]);
                $this->db->rollback();
                return false;
            }
        } else {
            $this->error->write('erplib','Kunde anlegen: '.$data["name"]);
            $this->db->rollback();
            return false;
        }
        return $rc;
    }
    function chkCustomer($data) {
        $sql  = "SELECT c.* FROM customer c ";
        $sql .= "LEFT JOIN custom_variables cv ON cv.trans_id=c.id ";
        $sql  .= "WHERE cv.config_id=".$this->cvarid." AND cv.text_value='%s'";
        if ( $data['kunden_nr'] > 0 ) {
            $rs = $this->db->getOne(sprintf($sql,$data['kunden_nr']));
        } else {
            $rs = $this->db->getOne(sprintf($sql,$data['shopid']));
        }
        if ( isset($rs['id']) ) {  //Kunde bekannt
            $data['customer_id'] = $rs['id'];
            $this->error->out('Update Kunde: '.$data['name'].' '.$data['customer_id'].' ');
            $sql  = "UPDATE customer SET greeting = :greeting,name = :name,street = :street,city = :city,country = :country,";
            $sql .= "zipcode = :zipcode,contact = :contact,phone = :phone,email = :email WHERE id = :customer_id";
            $rc =  $this->db->update($sql,$data);
            if ( !isset($rc->rownum) ) return -1;
            if ($rc) $rc = $data['customer_id'];
        } else {  // Neukunde
                $rc = $this->insCustomer($data);
        }
        /*if ($data['customer_id']>0) {
            $sql = "SELECT * FROM customer WHERE id = ".$data['customer_id'];
            $rs = $this->db->getOne($sql);
            if ($rs['id'] == $data['customer_id']) {
                 $this->error->out('Update:'.$data['customer_id'].' ');
                 $sql  = "UPDATE customer SET greeting = :greeting,name = :name,street = :street,city = :city,country = :country,";
                 $sql .= "zipcode = :zipcode,contact = :contact,phone = :phone,email = :email WHERE id = :customer_id";
                 $rc =  $this->db->update($sql,$data);
                 if ($rc) $rc = $data['customer_id'];
            } else {
                $rc = $this->insCustomer($data);
            }
        } else {
            $rc = $this->insCustomer($data);
        }*/
        $this->error->out('',true);
        return $rc;
    }
    function mkAuftrag($data,$shop,$longtxt) {
        $data['currency_id'] = $this->curr[$data['curr']];
        $data['customer']['currency_id'] = $data['currency_id'];
        $data['customer']['taxzone_id']  = $data['taxzone_id'];
        if ( $data['currency_id'] == '' || $data['currency_id'] < 1) $data['currency_id'] = 1;
        $this->db->Begin();
        $data["notes"] .= "\nBezahlung:".$data['bezahlung']."\n";
        if ($data['bezahlung'] == "Kreditkarte")   $data["notes"] .= $data['kreditkarte']."\n"; 
        if ($shop) { 
           $data["intnotes"] = "Shop: $shop";
        } else {
           $data["intnotes"] = "";
        };
        $data["customer_id"] = $this->chkCustomer($data['customer']);
        if ( $data["customer_id"] < 0 ) {
             $this->error->write('erplib','Update Kundendaten');
             $this->error->out(" Kundendaten ");
             return -1;
        }
        $parts = $data['parts'];
        unset($data['parts']);
        unset($data['customer']);
        if ($this->doordnr == 1) {
            $data["ordnumber"] = $this->getNewNr('so');
        } else {
            $data["ordnumber"] = $data['cusordnumber'];
        }
        $data["ordnumber"] = $this->preordnr.$data["ordnumber"];
        $tid = $this->newOrder($data);
        if ($tid) {
            $rc = $this->insParts($tid,$parts,$longtxt);  
            if (!$rc) {
                 $this->error->write('erplib','Artikel zu Auftrag');
                 return -1;
            }
        } else {
            $this->error->write('erplib','Auftrag anlegen');
            return -1;
        }
        $this->error->out($data["customer"]["firma"]." ");
        $rc = $this->db->Commit();
        return $data["customer_id"];
    }
    function chkPartsgroup($pg,$new=True) {
       /*gibt es die Warengruppe?
       Rückgabe nichts oder die partsgroup.id
       ggf neu anlegen*/
       $sql = "SELECT * FROM partsgroup WHERE partsgroup = '".$pg."'";
       $rs = $this->db->getOne($sql);
       if ($rs) {
           return $rs['id'];
       } else if ($this->mkPart and $new) {
           return $this->mkNewPartsgroup($pg);
       } else {
           return '';
       };
    }
    function mkNewPartsgroup($name) {
       $sql = "INSERT INTO partsgroup (partsgroup) VALUES ('".$name."')";
       $rc = $this->db->query($sql);
       if ($rc) {
           return $this->chkPartsgroup($name,False);
       } else {
           return '';
       }
    }
    function chkUnit($unit) {
       /*Prüfen ob es die Unit gibt.
         wenn nicht, die Standardunit zurückgeben*/
       if ($unit == '') {
           return $this->stdUnit();
       } else {
           $sql = "SELECT * FROM units WHERE name ilike '".$unit."'";
           $rs = $this->db->getOne($sql);
           if ($rs) {
              return $rs["name"];
           } else {
               return $this->stdUnit();
           }
       }
    }
    function stdUnit() {
       $sql = "SELECT * FROM units WHERE type = 'dimension' ORDER BY sortkey LIMIT 1";
       $rs = $this->db->getOne($sql);
       return $rs["name"];
    }
    function chkPartnumber($data,$new=True,$long=false,$mknew=false) {
       if ( ! $data["partnumber"] ) {
           $this->error->out('Artikel '.$data["partnumber"].'/'.$data['description'].' unbekannt ');
           return '';
       }
       if ( $this->pricegroup > 0 ) {
           $sql  = "SELECT parts.*,prices.price FROM parts LEFT JOIN prices ON parts_id = parts.id WHERE partnumber = '".$data["partnumber"];
           $sql .= "' AND pricegroup_id = ".$this->pricegroup;
           $rs = $this->db->getOne($sql);
           if ( !$rs ) {
               $sql = "SELECT * FROM parts WHERE partnumber = '".$data["partnumber"]."'";
               $rs = $this->db->getOne($sql);
               if ( $rs ) { 
                   $data['parts_id'] = $rs['id'];
                   $rc = $this->newPG($data);
                   if ( $rc ) $rs['price'] = $data['shoppreis'];
               };
           } else if ( $rs['price'] == 0 ) {
                   $data['parts_id'] = $rs['id'];
                   $rc = $this->updPG($data);
                   if ( $rc ) $rs['price'] = $data['shoppreis'];
           }
       } else {
           $sql = "SELECT * FROM parts WHERE partnumber = '".$data["partnumber"]."'";
           $rs = $this->db->getOne($sql);
       }
       if ($rs) {
           if ( $mknew ) return $rs['id'];
           $this->showDiff($data,$rs); 
           if ($long) {
               return $rs;
           } else {
               return $rs['id'];
           }
       } else if ($new and $this->mkPart) {
           $data['id'] = $this->mkNewPart($data);
           if ($long) {
               return $data;
           } else {
               return $data['id'];
           }
       } else {
           return '';
       };
    }
    function showDiff($shop,$erp) {
        $diff = '';
        if ( $shop['onhand'] != $erp['onhand'] ) {
            if ( floor($erp['onhand']) == $erp['onhand'] ) {
                $diff = sprintf('Menge: Shop %d ERP %d',$shop['onhand'],$erp['onhand']);
            } else {
                $diff = sprintf('Menge: Shop %f ERP %f',$shop['onhand'],$erp['onhand']);
            }
        }
        if ( $this->pricegroup > 0 ) {
            if ( $erp['price'] > 0 ) { 
                if ( $shop['shoppreis'] !=  $erp['price'] )    $diff .= sprintf(' PG-Price: Shop %0.2f ERP %0.2f',$shop['shoppreis'], $erp['price']); 
            } else {
                if ( $shop['sellprice'] != $erp['sellprice'] ) $diff .= sprintf(' Sellprice: Shop %0.2f ERP %0.2f',$shop['sellprice'],$erp['sellprice']);
            }
        } else {
            if ( $shop['sellprice'] != $erp['sellprice'] ) $diff .= sprintf(' Sellprice: Shop %0.2f ERP %0.2f',$shop['sellprice'],$erp['sellprice']);
        };
        if ( $diff != '' ) {
            $this->error->out(' ---- '.$shop['partnumber'].' '.substr($shop['description'],0,40).' '.$diff,true);
        }
    }
    function mkNewPart($data) {
       /*eine neue Ware anlegen, sollte nicht direkt aufgerufen werden.
       Auf vorhandene partnumber wird nicht geprüft.
       Folgendes Array muß übergeben werden:
       $data = array(partnumber,description,longdescription,weight,sellprice,taxrate,partsgroup,unit)
       Rückgabe parts.id
       */
       $link = '<a href="../../ic.pl?action=edit&id=%d" target="_blank">';
       if ($data['partnumber'] == '') {
           $this->error->write('erplib','Artikelnummer fehlt');
           return false;
       }
       if ($data['description'] == '') {
           $this->error->write('erplib','Artikelbezeichnung fehlt');
           return false;
       }
       $data['notes'] = addslashes($data['longdescription']);
       if ($data['weight']*1 != $data['weight']) $data['weight']=0;
       if ($data['sellprice']*1 != $data['sellprice']) $data['sellprice']=0;
       if ( !array_key_exists($data['taxrate'],$this->Bugru) ) {
            $this->error->write('TAXRATE',$data['taxrate']);
            $this->error->write('TAX',print_r($this->TAX,true));
            $this->error->write('Bugru',print_r($this->Bugru,true));
            $this->error->write('erplib','Buchungsgruppe konnte nicht zugeordnet werden');
            return false;
       }
       $data["buchungsgruppen_id"] = $this->Bugru[$data["taxrate"]];
       /*if (!in_array($data["buchungsgruppen_id"],$this->TAX)) { //??? Kann nie drin sein.
           foreach ($this->TAX as $key=>$tax) {
                if ($tax["rate"] == $data["taxrate"]/100) {
                    $data["buchungsgruppen_id"] = $key;
                    break;
                }
           }
           if (!$data["buchungsgruppen_id"]) {
               $this->error->write('erplib','Buchungsgruppe konnte nicht zugeordnet werden');
               return false;
           }
       };*/
       if ($data["partsgroup"]) {
           $data["partsgroup_id"] = $this->chkPartsgroup($data["partsgroup"]);
       } else {
           $data["partsgroup_id"] = '';
       };
       $data['unit'] = $this->chkUnit($data['unit']);
       if ($data['unit'] == '') {
           $this->error->write('erplib','Artikeleinheit fehlt oder falsch');
           return false;
       }
       $data['shop'] = 't';
       $data['listprice'] = ( $this->pricegroup > 0 )?$data['shoppreis']:$data['sellprice']; // Nur für GTU
       $data['sellprice'] = $data['sellprice'] * 100 / (100+$data['taxrate']);
       $this->error->write('Data',print_r($data,true));
       $sql  = "INSERT INTO parts (partnumber,description,sellprice,listprice,weight,notes,shop,unit,partsgroup_id,";
       $sql .= "image,buchungsgruppen_id,inventory_accno_id,income_accno_id,expense_accno_id) ";
       $sql .= "VALUES (:partnumber,:description,:sellprice,:listprice,:weight,:notes,:shop,:unit,:partsgroup_id,";
       $sql .= ":image,:buchungsgruppen_id,1,1,1)";
       $rc = $this->db->insert($sql,$data);
       $data['parts_id'] = $this->chkPartnumber($data,false,false,true);
       //if ( $this->pricegroup > 0 ) $rc = $this->newPG($data);
       if ( $data['onhand'] > 0 and $this->lager > 1) $this->insLager($data);
       //$x =  $this->chkPartnumber($data,False);
       $this->error->write('erplib',$data['description'].' '.$data['partnumber']);
       $this->error->out(sprintf($link,$data['parts_id']).$data['description'].' '.$data['partnumber'].'</a>',true);
       return $data['parts_id'];
    }
    function newPG($data) {
            $sql  = "INSERT INTO prices (parts_id,pricegroup_id,price) VALUES (:parts_id,:pricegroup,:shoppreis)";
            $data['pricegroup'] = $this->pricegroup;
            $rc = $this->db->insert($sql,$data);
            return $rc;
    }
    function updPG($data) {
            $sql  = "UPDATE prices SET price = :shoppreis WHERE pricegroup_id = :pricegroup AND parts_id = :parts_id";
            $data['pricegroup'] = $this->pricegroup;
            $rc = $this->db->update($sql,$data);
            return $rc;
    }
    function insLager($data) {
        $rc = $this->db->Begin();
        $sql = "SELECT nextval(('id'::text)::regclass) as id from id";
        $rs = $this->db->getOne($sql);
        $sql  = "INSERT INTO inventory (warehouse_id,parts_id,shippingdate,employee_id,bin_id,qty,trans_id,trans_type_id,comment) ";
        $sql .= "VALUES (:wid,:parts_id,now(),:employee_id,:bid,:onhand,:next,:tt,'Shopübernahme')";
        $data['next'] = $rs['id'];
        $data['tt'] = $this->transtype;
        $data['bid'] = $this->lager;
        $data['wid'] = $this->warehouse_id;
        $data['employee_id'] = $this->employee_id;
        $rc = $this->db->insert($sql,$data);
        if ( $rc ) {
           $this->db->Commit();
        } else {
           $this->db->Rollback();
        }
    }
}
    function getTax($db) {
        $TAX = Array(1 => Array(),2 => Array(),3 => Array(),4 => Array());
        $Bugru = Array();
        $sql  = "SELECT  BG.id AS bugru,T.rate,TK.startdate,C.taxkey_id,";
        $sql .= "T.chart_id,C.description,C.link,T.id as taxid,";
        $sql .= "TC.income_accno_id,TC.expense_accno_id,TC.taxzone_id ";
        $sql .= "FROM buchungsgruppen BG LEFT JOIN taxzone_charts TC ON BG.id=TC.buchungsgruppen_id ";
        $sql .= "LEFT JOIN chart C ON TC.income_accno_id=C.id LEFT JOIN taxkeys TK ON TK.chart_id=C.id ";
        $sql .= "LEFT JOIN tax T ON T.id=TK.tax_id WHERE TK.startdate <= now() order by startdate";
        $rs = $db->getAll($sql);
        if ($rs) foreach ($rs as $row) {
            $nr = $row['bugru'];
            if (!isset($TAX[$row['taxzone_id']][$nr])) {
                $data = array();
                $data['startdate'] =    $row['startdate'];
                $data['rate'] =         $row['rate'];
                $data['taxkey'] =       $row['taxkey_id'];
                $data['taxid'] =        $row['taxid'];
                $data['chartid'] =      $row['chart_id'];
                $data['link'] =         $row['link'];
                $data['taxdescription'] = $row['description'];
                $data['income'] =       $row['income_accno_id'];
                $data['expense'] =      $row['expense_accno_id'];
                $TAX[$row['taxzone_id']][$nr] = $data;
                //$this->Bugru[sprintf('%0.f',$row['rate']*100)] =  $row['bugru'];
                $Bugru[$row['rate']*100] =  $row['bugru'];
            } else if ($TAX[$row['taxzone_id']][$nr]['startdate'] < $row['startdate']) {
                $TAX[$row['taxzone_id']][$nr]["startdate"] =  $row['startdate'];
                $TAX[$row['taxzone_id']][$nr]["rate"] =       $row['rate'];
                $TAX[$row['taxzone_id']][$nr]["taxkey"] =     $row['taxkey_id'];
                $TAX[$row['taxzone_id']][$nr]["taxid"] =      $row['taxid'];
                $TAX[$row['taxzone_id']][$nr]['chartid'] =      $row['chart_id'];
                $TAX[$row['taxzone_id']][$nr]['link'] =         $row['link'];
                $TAX[$row['taxzone_id']][$nr]['taxdescription'] = $row['description'];
                $TAX[$row['taxzone_id']][$nr]["income"] =     $row['income_accno_id'];
                $TAX[$row['taxzone_id']][$nr]["expense"] =    $row['expense_accno_id'];
            }
        }
        return array('TAX'=>$TAX,'Bugru'=>$Bugru);
    }
?>
