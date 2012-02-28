<?php
function getCustoms() {
global $db;
    $sql = "SELECT * FROM custom_variable_configs WHERE module = 'IC' ORDER BY sortkey";
    $rs=$db->getAll($sql);
    return $rs;
}
function getPreise() {
global $db;
    $sql = "SELECT * FROM pricegroup";
    $rs=$db->getAll($sql);
    $tmp[1] = "Verkaufspreis";
    $tmp[2] = "Listenpreis";
    if ($rs) foreach($rs as $row) {
      $tmp[$row['id']] = $row['pricegroup'];
    }
    return $tmp;
}
function getTax() {
global $db;
    $sql  = "SELECT  BG.id AS bugru,T.rate,TK.startdate,C.taxkey_id, ";
    $sql .= "(SELECT id FROM chart WHERE accno = T.taxnumber) AS tax_id, ";
    $sql .= "BG.income_accno_id_0,BG.expense_accno_id_0 ";
    $sql .= "FROM buchungsgruppen BG LEFT JOIN chart C ON BG.income_accno_id_0=C.id ";
    $sql .= "LEFT JOIN taxkeys TK ON TK.chart_id=C.id ";
    $sql .= "LEFT JOIN tax T ON T.id=TK.tax_id WHERE TK.startdate <= now()";
    $rs = $db->getAll($sql);
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
global $db;
    $no = array('ok','preise','addtax');
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
           if (trim($val))  $tmp[] = " ($key ilike '%$val%') ";
        }
        if (count($tmp)>0) $where = implode("and",$tmp);
        if (count($cvar)>0) {
             $where .= " and ".implode(" and ",$cvar);
             $cvarjoin = ",custom_variable_configs CVC LEFT JOIN custom_variables CV on CV.config_id=CVC.id ";
        }
    } 
    if ($data['preise']>1) { 
	$prices = ',PR.price '; 
        $pricejoin = ' LEFT JOIN prices PR on PR.parts_id=P.id';
        $pricewhere = ' and (PR.pricegroup_id='.$data['preise'].' or PR.pricegroup_id is null)';
    } else { 
	$prices = ''; 
        $pricejoin = '';
        $pricewhere = '';
    };
    $sql  = "SELECT partnumber,P.description,notes,listprice,sellprice,image,P.ean,PG.partsgroup$prices,buchungsgruppen_id as bugru ";
    $sql .= "FROM parts P LEFT JOIN partsgroup PG on PG.id=P.partsgroup_id $pricejoin $cvarjoin ";
    $sql .= "WHERE ".$where.$pricewhere;
    $sql .= " order by PG.partsgroup,partnumber";
    //echo $sql;
    $rs=$db->getAll($sql);
    return $rs;
}

function prepTex($katalog=true) {
    $pre = '';
    $post = '';
    $artikel = '';
    $postline = false;
    $artline = false;
    if ($katalog) {
    	$vorlage = fopen('vorlage/katalog.tex','r');
    } else {
	    $vorlage = fopen('vorlage/inventur.tex','r');
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
global $db;
    $sql = "SELECT w.description as ort,b.description as platz,warehouse_id,b.id from bin b left join warehouse w on w.id=warehouse_id order by warehouse_id,b.id";
    $rs = $_SESSION["db"]->getAll($sql,DB_FETCHMODE_ASSOC);
    return $rs;
}

function getLager($data) {
global $db;
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
   $sql  = 'SELECT p.partsgroup_id,pg.partsgroup,p.partnumber,p.description,p.unit,';
   $sql .= '(select sum(qty) from inventory where '.$lager.' AND parts_id=p.id) as bestand ';
   $sql .= 'from parts p left join partsgroup pg on pg.id = p.partsgroup_id ';
   $sql .= 'where 1=1 ';
   if ($data['dienstl'] != 1) $sql .= 'AND inventory_accno_id is not NULL ';
   if ($data['erzeugn'] != 1) $sql .= 'AND expense_accno_id is not NULL ';
   $sql .= 'order by '.$order;
   $artikel = $db->getAll($sql,DB_FETCHMODE_ASSOC);
   return $artikel;
}
function getPartsgroup() {
global $db;
   $sql = "SELECT * FROM partsgroup order by partsgroup";
   $pg = $db->getAll($sql,DB_FETCHMODE_ASSOC);
   return $pg;
}
function closeinventur($name) {
    $rc = @exec("pdflatex -interaction=batchmode -output-directory=tmp/ tmp/inventur.tex",$out,$ret);
    $rc = @exec("pdflatex -interaction=batchmode -output-directory=tmp/ tmp/inventur.tex",$out,$ret);
    if ($name) {
        $rc = @exec("mv tmp/inventur.pdf $name");
    }
}
function getPartBin($pg,$bin) {
global $db;
    if ($pg == '') {
	$pg = 'partsgroup_id is NULL ';
    } else { 
       $pg = "partsgroup_id = $pg ";
    };
    $sql  = 'SELECT p.description AS partdescription, p.partnumber AS partnumber, i.chargenumber AS chargenumber, ';
    $sql .= 'i.bestbefore AS bestbefore, p.id AS parts_id,i.bin_id, SUM(i.qty) AS qty, p.unit AS partunit ';
    $sql .= 'FROM parts p LEFT JOIN inventory i  ON i.parts_id  = p.id  LEFT JOIN bin   b ON i.bin_id  = b.id ';
    $sql .= 'WHERE 1=1 AND  (b.id = '.$bin.' or b.id is NULL) AND '.$pg;
    $sql .= 'GROUP BY partdescription, partnumber, chargenumber, bestbefore,  p.id, partunit,i.bin_id ';
    $sql .= 'ORDER BY partnumber  ASC';
    $pg = $db->getAll($sql,DB_FETCHMODE_ASSOC);
    return $pg;
    $sql  = "SELECT id from parts where partnumber ilike '$part'" ;
    /*$rs  = $db->getOne($sql);
    if ($rs) {
        $sql  = "SELECT DISTINCT chargenumber,";
        $sql .= "(select sum(qty) from inventory where bin_id = $bin and parts_id = $part and chargenumber = i.chargenumber) as bestand";
        $sql .= ",p.description,p.id as parts_id from inventory i left join parts p ";
        $sql .= "on p.id=i.parts_id where bin_id = $bin and parts_id = $part ";
        $sql .= "group by chargenumber,qty,description,p.id";
        $rs =  $db->getAll($sql);
        echo $sql;
        return $rs;
    } else {
        return false;
    }*/
}
function getLagername($wh,$bin) {
global $db;
    $sql  = "SELECT w.description||' '||bin.description as name ";
    $sql .= "FROM bin left join warehouse w on warehouse_id = w.id WHERE bin.id = $bin";
    $rs = $db->getOne($sql);
    return $rs['name'];
} 
function getTransType() {
global $db;
   $sql = "SELECT * from transfer_type order by direction";
   $rs = $db->getAll($sql,DB_FETCHMODE_ASSOC);
   return $rs; 
}
function updatePartBin($row) {
global $db;
    $in = array('','correction','stock','fount');
    $out = array('','correction','used','missing');
    $sql = "SELECT id from transfer_type WHERE direction = 'in' and description = '".$in[$row['transtype']]."'";
    $rs = $db->getOne($sql);
    $in = $rs['id'];
    $sql = "SELECT * from transfer_type WHERE direction = 'out' and description = '".$out[$row['transtype']]."'";
    $rs = $db->getOne($sql);
    $out = $rs['id'];
    $len = count($row['parts_id']);
    for ($i=0; $i<$len; $i++) {
       if ($row['qty'][$i] == $row['oldqty'][$i]) continue;
       if ($row['qty'][$i] == '') continue;
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
       $sql .= ",$diff,nextval(('id'::text)::regclass),$tt,now(),'".$row["comment"]."',$charge,$best)";
       $rc = $db->query($sql);
       echo $row[$i]["part_id"].$x;
       if ($rc) { echo "ok<br>";} else { echo "error<br>"; };
    };
}
?>
