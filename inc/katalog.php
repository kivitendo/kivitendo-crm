<?php
function getCustoms() {
    $sql = "SELECT * FROM custom_variable_configs WHERE module = 'IC' ORDER BY sortkey";
    $rs=$GLOBALS['dbh']->getAll($sql);
    return $rs;
}
function getPreise() {
    $sql = "SELECT * FROM pricegroup";
    $rs=$GLOBALS['dbh']->getAll($sql);
    $tmp[1] = "Verkaufspreis";
    $tmp[2] = "Listenpreis";
    if ($rs) foreach($rs as $row) {
      $tmp[$row['id']] = $row['pricegroup'];
    }
    return $tmp;
}
function getTax() {
    $sql  = "SELECT  BG.id AS bugru,T.rate,TK.startdate,C.taxkey_id, ";
    $sql .= "(SELECT id FROM chart WHERE accno = T.taxnumber) AS tax_id, ";
    $sql .= "BG.income_accno_id_0,BG.expense_accno_id_0 ";
    $sql .= "FROM buchungsgruppen BG LEFT JOIN chart C ON BG.income_accno_id_0=C.id ";
    $sql .= "LEFT JOIN taxkeys TK ON TK.chart_id=C.id ";
    $sql .= "LEFT JOIN tax T ON T.id=TK.tax_id WHERE TK.startdate <= now()";
    $rs = $GLOBALS['dbh']->getAll($sql);
    if ($rs) foreach ($rs as $row) {
        $nr = $row['bugru'];
        if (!$TAX[$nr]) {
            $data = array();
            $data['startdate'] =    $row['startdate'];
            $data['rate'] =         $row['rate'];
            $data['taxkey'] =       $row['taxkey_id'];
            $data['taxid'] =        $row['tax_id'];
            $data['income'] =       $row['income_accno_id_0'];
            $data['expense'] =      $row['expense_accno_id_0'];
            $TAX[$nr] = $data;
        } else if ($TAX[$nr]['startdate'] < $row['startdate']) {
            $TAX[$nr]["startdate"] =  $row['startdate'];
            $TAX[$nr]["rate"] =       $row['rate'];
            $TAX[$nr]["taxkey"] =     $row['taxkey_id'];
            $TAX[$nr]["taxid"] =      $row['tax_id'];
            $TAX[$nr]["income"] =     $row['income_accno_id_0'];
            $TAX[$nr]["expense"] =    $row['expense_accno_id_0'];
        }
    }
    return $TAX;
}

function getArtikel($data) {
    $no = array('ok','preise','addtax','pglist','prozent','pm','order');
    $where = '';
    $tmp[] = ' 1=1 ';
    if ($data) {
        foreach ($data as $key=>$val) {
           if (in_array($key,$no)) continue;
           if (preg_match('/vc_cvar_([a-z]+)_(.+)/',$key,$hit)) {
               if ($val == '') continue;
               $cvar[] = "(CVC.name='".$hit[2]."' and CV.".$hit[1]."_value='$val' and CV.trans_id=P.id)";
               continue;
           }
           if ($key == 'partnumber' and strpos($val,',')) {
               $pnr = split(',',$val);
               foreach ($pnr as $nr) $pnumber[] = "'$nr'";
               $tmp[] = ' partnumber in ('.implode(',',$pnumber).') ';
               continue;
           }
           if (trim($val))  $tmp[] = " ($key ilike '%$val%') ";
        }
        if (count($tmp)>0) $where = implode("and",$tmp);
        if (count($cvar)>0) {
             $where .= " and ".implode(" and ",$cvar);
             $cvarjoin = ",custom_variable_configs CVC LEFT JOIN custom_variables CV on CV.config_id=CVC.id ";
        }
    } 
    if ($data['preise']>2) { 
	$prices = ',PR.price '; 
        $pricejoin = ' LEFT JOIN prices PR on PR.parts_id=P.id';
        $pricewhere = ' and (PR.pricegroup_id='.$data['preise'].' or PR.pricegroup_id is null)';
    } else { 
	$prices = ''; 
        $pricejoin = '';
        $pricewhere = '';
    };
    //$sql  = "SELECT partnumber,P.description,notes,listprice,sellprice,image,P.ean,PG.partsgroup$prices,buchungsgruppen_id as bugru,P.gv ";
    $sql  = "SELECT P.*,PG.partsgroup$prices,buchungsgruppen_id as bugru ";
    $sql .= "FROM parts P LEFT JOIN partsgroup PG on PG.id=P.partsgroup_id $pricejoin $cvarjoin ";
    $sql .= "WHERE ".$where.$pricewhere;
    if ($data['order'] == 'spezial' and count($pnumber)>1) {
        $sql .= " order by idx(array[".implode(',',$pnumber)."], partnumber)";
    } else {
        $sql .= " order by ".$data['order'];
    }
    //echo $sql;
    $rs=$GLOBALS['dbh']->getAll($sql);
    return $rs;
}

function prepTex($tex='katalog',$upload=false) {
    $pre = '';
    $post = '';
    $artikel = '';
    $postline = false;
    $artline = false;
    if ($upload) {
            $vorlage = fopen('tmp/'.$tex.'.org','r');
    } else {
            $vorlage = fopen('vorlage/'.$tex.'.tex','r');
    }
    $line = fgets($vorlage,1024);
    while (!feof($vorlage)) {
        if (preg_match('/%end_part%/i',$line)) {
            $artline = false;
            $postline = true;
            $line = fgets($vorlage,1024);
            continue;
        }
        if ($artline) {
            $artikel .= $line;
            $line = fgets($vorlage,1024);
            continue;
        }
        if (preg_match('/%foreach_part%/i',$line)) {
            $artline = true;
            $line = fgets($vorlage,1024);
            continue;
        }
        if ($postline) {
            $post .= $line;
        } else {
            $pre .= $line;
        }
        $line = fgets($vorlage,1024);
    }
    return array("pre"=>$pre,"artikel"=>$artikel,"post"=>$post);
}


function getLagerOrte() {
    $sql = "SELECT w.description as ort,b.description as platz,warehouse_id,b.id from bin b left join warehouse w on w.id=warehouse_id order by warehouse_id,b.id";
    $rs = $GLOBALS['dbh']->getAll($sql,DB_FETCHMODE_ASSOC);
    return $rs;
}
function getPgList() {
    $sql = "SELECT partsgroup from partsgroup order by partsgroup";
    $rs = $GLOBALS['dbh']->getAll($sql,DB_FETCHMODE_ASSOC);
    $list = "<option value=''></option>\n";
    if ($rs) foreach ($rs as $pg) {
        $list .= "<option value='".$pg['partsgroup']."'>".$pg['partsgroup']."</option>\n";
    }
    return $list;
}
function getLager($data) {
   if ($data['lager'] == '0') {
        $lager = '1=1';
   } else if (substr($data['lager'],0,1) == '_') {
        $lager = 'warehouse_id = '.substr($data['lager'],1);
   } else {
        $lager = 'bin_id = '.$data['lager'];
   }
   $order = 'p.';
   if ($data['wg'] == '1') $order .= 'partsgroup_id,p.';
   $order .= $data['sort'];
   $sql  = 'SELECT p.partsgroup_id,pg.partsgroup,p.partnumber,p.description,p.unit,p.lastcost as ep,';
   $sql .= '(select sum(qty) from inventory where '.$lager.' AND parts_id=p.id %s) as bestand ';
   $sql .= 'from parts p left join partsgroup pg on pg.id = p.partsgroup_id ';
   $sql .= 'where 1=1 ';
   if ( $data['erzeugnis']== 1 AND $data['dienstl'] != 1) { 
        $where = "AND inventory_accno_id is NULL AND assembly = 't' or inventory_accno_id is not NULL AND assembly = 'f'  ";
   } else if ( $data['erzeugnis']!= 1 AND $data['dienstl'] == 1)  { 
        $where = "AND assembly = 'f'  ";
   } else if ( $data['erzeugnis']== 1 AND $data['dienstl'] == 1)  { 
        $where = '';
   } else {
        $where = "AND inventory_accno_id is not NULL  ";
   };
   $sql .= $where;
   //if ( $data['erzeugnis']!=1 or $data['dienstl'] != 1 ) $where .= "AND inventory_accno_id is not NULL  ";
   if ($data['datum'] != '') {
      $tmp = split('\.',$data['datum']);
      $date = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];
      $and = "AND shippingdate <= '$date' ";
   } else {
      $and = '';
   }
   $sql = sprintf($sql,$and).' order by '.$order;
   echo $sql."<br>";
   $artikel = $GLOBALS['dbh']->getAll($sql,DB_FETCHMODE_ASSOC);
   return $artikel;
}
function getPartsgroup() {
   $sql = "SELECT * FROM partsgroup order by partsgroup";
   $pg = $GLOBALS['dbh']->getAll($sql,DB_FETCHMODE_ASSOC);
   return $pg;
}
function closeinventur($art,$name) {
    $home = getenv('HOME');
    $openin_any = getenv('openin_any');
    putenv('HOME='.getcwd().'/tmp');
    putenv('openin_any=p');
    $rc = @exec("pdflatex -interaction=nonstopmode -output-directory=tmp/ tmp/$art.tex",$out,$ret);
    $rc = @exec("pdflatex -interaction=nonstopmode -output-directory=tmp/ tmp/$art.tex",$out,$ret);
    if ($name != $art) {
        $rc = @exec("mv tmp/$art.pdf tmp/$name.pdf");
    }
    putenv('HOME='.$home);
    putenv('openin_any='.$openin_any);
}
function getPartBin($pg,$partnumber,$obsolete,$bin) {
    if ($pg == '' and $partnumber == '') {
	$pg = "(partsgroup_id is NULL or partsgroup_id = 0) ";
    } else if ($pg == '' and $partnumber != '') {
        $pg = "partnumber like '$partnumber' ";
    } else if ($pg != '' and $partnumber != '') {
       $pg = "partsgroup_id = $pg and partnumber like '$partnumber' ";
    } else { 
       $pg = "partsgroup_id = $pg ";
    };
    $sql  = 'SELECT p.description AS partdescription, p.partnumber AS partnumber, i.chargenumber AS chargenumber, ';
    $sql .= 'i.bestbefore AS bestbefore, p.id AS parts_id,i.bin_id, SUM(i.qty) AS qty, p.unit AS partunit, p.onhand ';
    $sql .= 'FROM parts p LEFT JOIN inventory i  ON i.parts_id  = p.id  LEFT JOIN bin   b ON i.bin_id  = b.id WHERE '.$pg;
    if ( $obsolete != '' ) $sql .= " AND obsolete ='$obsolete' ";
    $sql .= 'AND  (b.id = '.$bin.' or b.id is NULL )  ';
    $sql .= 'GROUP BY partdescription, partnumber, chargenumber, bestbefore,  p.id, partunit,i.bin_id, p.onhand ';
    $sql .= 'union ';
    $sql .= 'SELECT p.description AS partdescription, p.partnumber AS partnumber, null AS chargenumber, null AS bestbefore, ';
    $sql .= 'p.id AS parts_id, null, 0 as qty, p.unit AS partunit, p.onhand FROM parts p WHERE '.$pg;
    if ( $obsolete != '' ) $sql .= " AND obsolete ='$obsolete' ";
    $sql .= 'order by  partnumber asc, bin_id asc';
    $pg = $GLOBALS['dbh']->getAll($sql,DB_FETCHMODE_ASSOC);
    return $pg;
    /*$sql  = "SELECT id from parts where partnumber ilike '$part'" ;
    $rs  = $GLOBALS['dbh']->getOne($sql);
    if ($rs) {
        $sql  = "SELECT DISTINCT chargenumber,";
        $sql .= "(select sum(qty) from inventory where bin_id = $bin and parts_id = $part and chargenumber = i.chargenumber) as bestand";
        $sql .= ",p.description,p.id as parts_id from inventory i left join parts p ";
        $sql .= "on p.id=i.parts_id where bin_id = $bin and parts_id = $part ";
        $sql .= "group by chargenumber,qty,description,p.id";
        $rs =  $GLOBALS['dbh']->getAll($sql);
        echo $sql;
        return $rs;
    } else {
        return false;
    }*/
}
function getLagername($wh,$bin) {
    $sql  = "SELECT w.description||' '||bin.description as name ";
    $sql .= "FROM bin left join warehouse w on warehouse_id = w.id WHERE bin.id = $bin";
    $rs = $GLOBALS['dbh']->getOne($sql);
    return $rs['name'];
} 
function getTransType() {
   $sql = "SELECT * from transfer_type order by direction";
   $rs = $GLOBALS['dbh']->getAll($sql,DB_FETCHMODE_ASSOC);
   return $rs; 
}
function updatePartBin($row) {
    $in = array('','correction','stock','fount');
    $out = array('','correction','used','missing');
    $sql = "SELECT id from transfer_type WHERE direction = 'in' and description = '".$in[$row['transtype']]."'";
    $rs = $GLOBALS['dbh']->getOne($sql);
    $in = $rs['id'];
    $sql = "SELECT * from transfer_type WHERE direction = 'out' and description = '".$out[$row['transtype']]."'";
    $rs = $GLOBALS['dbh']->getOne($sql);
    $out = $rs['id'];
    $len = count($row['parts_id']);
    if ($row['budatum']) {
        $d = preg_match('/(\d+)\.(\d+).(\d+)/',$row['budatum'],$tmp);
        if (count($tmp) == 4) {
            $now = $tmp[3]."-". $tmp[2]."-". $tmp[1];
        } else {
            $now = date('Y-m-d');
        }
    } else {
        $now = date('Y-m-d');
    }
    for ($i=0; $i<$len; $i++) {
       if ($row['qty'][$i] == $row['oldqty'][$i]) continue;
       if ($row['qty'][$i] == '') continue;
       $row['qty'][$i] = strtr($row['qty'][$i],',','.');
       $diff = $row['qty'][$i] - $row['oldqty'][$i];
       if ($diff > 0) {
          //$sqltyp = "SELECT id FROM transfer_type WHERE direction = 'in' and description = 'found'";
          $tt = $in;
          $x = " Einbuchen. Menge: ".$diff;
       } else if ($diff<0) {
          //$sqltyp = "SELECT id FROM transfer_type WHERE direction = 'out' and description = 'missing'";
          $tt = $out;
          $x = " Ausbuchen. Menge: ".$diff;
       } else {
          continue;
       }
       if ($row['chargenumber'][$i] == '') { $charge = "''"; } else { $charge = "'".$row['chargenumber'][$i]."'"; };
       if ($row['bestbefor'][$i] == '') { $best = 'NULL'; } else { $best = "'".$row['bestbefor'][$i]."'"; };
       $sql =  "INSERT INTO inventory (warehouse_id,bin_id,parts_id,employee_id,qty, trans_id,trans_type_id,shippingdate,comment,chargenumber,bestbefore)";
       $sql .= " VALUES (".$row['warehouse'].",".$row['bin'].",".$row['parts_id'][$i].",".$_SESSION['loginCRM'];
       $sql .= ",$diff,nextval(('id'::text)::regclass),$tt,'".$now."','".$row["comment"]."',$charge,$best)";
       $rc = $GLOBALS['dbh']->query($sql);
       echo $row[$i]["part_id"].$x;
       if ($rc) { echo "ok<br>";} else { echo "error<br>"; };
    };
}
?>
