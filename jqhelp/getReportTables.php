<?php
    include_once("../inc/stdLib.php");


//function getTable($tab) {
    $tab = $_GET['tab'];
    $tabellen=array("shipto"=>'shipto',
                    "contacts"=>'contacts');
    if ( $tab == 'C' ) {
        $tabellen['firma'] = 'customer';
    } else if ( $tab == 'V' ) {
        $tabellen['firma'] = 'vendor';
    /*if ( $tab == 'C' ) {
        $tabellen=array("customer"=>array("Kunden","K"),
                        "shipto"=>array("Abweichend","S"),
                        "contacts"=>array("Personen","P"));
    } else if ( $tab == 'V' ) {
        $tabellen=array("vendor"=>array("Lieferant","L"),
                        "shipto"=>array("Abweichend","S"),
                        "contacts"=>array("Personen","P"));*/
    } else {
        return '';
    }
    $noshow=array("itime","mtime");
 	foreach($tabellen as $key=>$val) {
		$sql="SELECT a.attname FROM pg_attribute a, pg_class c WHERE ";
		$sql.="c.relname = '$val' AND a.attnum > 0 AND a.attrelid = c.oid ORDER BY a.attnum";
		$rs=$_SESSION['db']->getAll($sql);
		if ($rs) { 
            $pre =  substr($key,0,1);
			foreach ($rs as $row) {
				if (!in_array($row["attname"],$noshow))
					$felder[$key][]=$row["attname"];
			}
		} else {
			$felder[$key]=false;
		}
	}
	$anzahl=count($tabellen);
    echo json_encode(array('tables'=>$felder,'count'=>$anzahl));
//}

//getTable($_GET['tab']);
?>
