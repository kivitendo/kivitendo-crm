<?php
    $wiam = getCwd();
    if (substr($wiam,-3) == "inc") {
        chdir("..");
        require_once("inc/stdLib.php");
    } else {
        require_once("stdLib.php");
    };

require "version.php";

    if (!function_exists('updatever')) {
	    function updatever($db,$VERSION) {
		    $sql = "INSERT INTO crm (uid,datum,version) values (".$_SESSION["loginCRM"].",now(),'".$VERSION."')";
		    $rc = $_SESSION["db"]->query($sql);
	    	$db->commit();
		    echo "Versionsnummer gesetzt<br>";
		    if (is_file("update/update$VERSION.txt")) {
		        echo "<h2>Wichtig!</h2>";
		        echo readfile("update/update$VERSION.txt");
		    }
	    }
    };

    if (!function_exists('sorttodo')) {
	    function sorttodo($todo) {
		    $todonew = array();
		    foreach ($todo as $sql) {
			    $file = file_get_contents("update/".$sql);
			    if (preg_match("/@depends: ([^\n;]+)/",$file,$hit)) {
				    if (in_array("crm_".$hit[1].".sql",$todo) && !in_array("crm_".$hit[1].".sql",$todonew)) {
					    $todonew[] = "crm_".$hit[1].".sql";
				    } 
			    }
			    if (!in_array($sql,$todonew)) $todonew[] = $sql;
		    }
		    return $todonew;
	    }
    }

    $sql = "SELECT tag || '.sql' as tag  from schema_info where tag like 'crm_%' order by tag";
    $rs  = $_SESSION["db"]->getAll($sql);
    if ( !$rs ) { $isnow = array(); } 
    else { foreach ( $rs as $row ) { $isnow[] = $row["tag"];} };
    chdir("update");
    $update = glob("crm_*.sql");
    chdir("..");
    $code = false;
    $ok = true;
    if ( !is_array($update) ) $update = array();
    $todo = array_diff($update,$isnow);
    if ( $todo ) {
    	$todonew = sorttodo($todo);
	    echo "<br>";
    	while ( array_diff($todo,$todonew) ) {
	    	$todo = $todonew;	
		    $todonew = sorttodo($todo);
    	}
	    $todo = $todonew;	
        $rc = $db->begin();
        foreach ( $todo as $upd ) {
            $f = fopen("update/".$upd,"r");
            while (!feof($f) ) {
                if ( empty($zeile)) { $zeile=trim(fgets($f,1000) ); continue; };
                if ( preg_match("/^-- @([^:]+):(.+)/",$zeile,$treffer) ) { 
                    $tmp = $treffer[1]; 
                    if ( $tmp == "php" ) {
                        $query = "";
                        $code = true;
                    } else if ( $tmp == "exec" ) {
                        $code = false;
                        $rc = eval($query);
                        $query = "";
                        if ( $rc < 0 ) {
                           echo "Probleme beim Update, alle &Auml;nderungen werden zur&uuml;ck genommen";
                           $db->rollback();
                           exit(1); 
                        }
                    } else {
                        ${$tmp} = $treffer[2];
                        echo $tmp.":".${$tmp}."<br>"; flush();
                    }
                    $zeile = trim(fgets($f,1000)); 
                    continue; 
                };
                if ( preg_match("/^--/",$zeile) ) { $zeile = trim(fgets($f,1000)); continue; };
                if ( !preg_match("/;$/",$zeile) or $code ) {
                    $query .= $zeile;
                    $zeile = trim(fgets($f,1000));
                } else {
                    $query .= substr($zeile,0,-1);
                    $rc = $_SESSION["db"]->query($query);
                    if ( !$rc ) {
                        echo "Probleme beim Update, alle &Auml;nderungen werden zur&uuml;ck genommen";
                        $db->rollback();
                        exit(1);
                    }
                    $zeile = trim(fgets($f,1000));
                    $query = ""; 
                }
            } 
            $sql = "insert into schema_info (tag,login) values ('crm_%s','%s')";
            $rc = $_SESSION["db"]->query(sprintf($sql,trim($tag),$_SESSION["employee"]));
            if ( !$rc ) {
            $ok = false;
                $db->rollback();
                exit(2);
            } 
        }
        if ( $ok ) {
            updatever($db,$VERSION);
            echo "update ok<br>";
        }
    } else {
        if ( $GLOBALS["oldver"] and $GLOBALS["oldver"] <> $VERSION ) updatever($db,$VERSION);
        if ( !$LOGIN ) echo "System uptodate<br />";
    };
    if ( !$LOGIN ) {
        $sql = "select tag,login,itime  from schema_info where tag ilike 'crm_%' order by itime";
        $liste = $_SESSION["db"]->getAll($sql);
        echo "<br /><table>\n";     
        $zeile = "<tr><td>%s</td><td>%s</td><td>%s</td></tr>\n";
        if ( $liste ) foreach ( $liste as $line ) {
            echo sprintf($zeile,$line["tag"],$line["login"],$line["itime"]);
        };
        echo "</table>";     
    }
?>
