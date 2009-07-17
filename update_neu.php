<?php
    require_once("inc/stdLib.php");
    $sql = "SELECT tag || '.sql' as tag  from schema_info where tag like 'crm_%' order by tag";
    $rs  = $_SESSION["db"]->getAll($sql);
    if (!$rs) { $isnow=array();} 
    else { foreach ($rs as $row) { $isnow[]=$row["tag"];} };
    chdir("update");
    $update = glob("crm_*.sql");
    chdir("..");
    $code=false;
    if (!is_array($update)) $update=array();
    $todo = array_diff($update,$isnow);
    if ($todo) {
        $rc=$db->begin();
        foreach ($todo as $upd) {
            $f = fopen("update/".$upd,"r");
            while (!feof($f) ) {
                if (empty($zeile)) { $zeile=trim(fgets($f,1000)); continue; };
                if (preg_match("/^-- @([^:]+):(.+)/",$zeile,$treffer)) { 
                    $tmp=$treffer[1]; 
                    if ($tmp=="php") {
                        $query = "";
                        $code=true;
                    } else if ($tmp=="exec") {
                        $code=false;
                        eval($query);
                        $query = "";
                    } else {
                        ${$tmp}=$treffer[2];
                        echo $tmp.":".${$tmp}."<br>"; flush();
                    }
                    $zeile=trim(fgets($f,1000)); continue; 
                };
                if (preg_match("/^--/",$zeile)) { $zeile=trim(fgets($f,1000)); continue; };
                if (!preg_match("/;$/",$zeile) or $code) {
                    $query.=$zeile;
                    $zeile=trim(fgets($f,1000));
                } else {
                    $query.=substr($zeile,0,-1);
                    $rc = $_SESSION["db"]->query($query);
                    if (!$rc) {
                        echo "Probleme beim Update, alle &Auml;nderungen werden zur&uuml;ck genommen";
                        $db->rollback();
                        exit(1);
                    }
                    $zeile=trim(fgets($f,1000));
                    $query=""; 
                }
            } 
            $sql="insert into schema_info (tag,login) values ('crm_%s','%s')";
            $rc = $_SESSION["db"]->query(sprintf($sql,trim($tag),$_SESSION["employee"]));
            if ($rc) {
                $db->commit();
                echo "update ok<br>";
            } else {
                $db->rollback();
            }
        }
    } else {
        echo "System uptodate";
    };
    $sql="select tag,login,itime  from schema_info where tag ilike 'crm_%' order by itime";
    $liste = $_SESSION["db"]->getAll($sql);
    echo "<table>\n";     
    $zeile = "<tr><td>%s</td><td>%s</td><td>%s</td></tr>\n";
    if ($liste) foreach ($liste as $line) {
        echo sprintf($zeile,$line["tag"],$line["login"],$line["itime"]);
    };
    echo "</table>";     

?>
