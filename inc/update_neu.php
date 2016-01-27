<?php
    $menu = false;
    if ( isset($_GET['menu']) && $_GET['menu'] == '1' ) {
        require_once __DIR__.'/stdLib.php';
        $menu = $_SESSION['menu'];
        $head = mkHeader();
        echo '<html><head><title></title>';
        echo $menu['stylesheets'];
        echo $menu['javascripts'];
        echo $head['CRMCSS'];
        echo $head['THEME'];
        echo '</head><body>';
        echo $menu['pre_content'];
        echo $menu['start_content'];
        echo '<h1>Updatecheck</h1>';
    }
    if (!function_exists('updatever')) {
        function updatever($VERSION) {
            $sql = "INSERT INTO crm (uid,datum,version) values (".$_SESSION["loginCRM"].",now(),'".$VERSION."')";
            $rc = $GLOBALS['db']->query($sql);
                    $GLOBALS['db']->commit();
            echo "Versionsnummer gesetzt<br>";
            if (is_file("update/update$VERSION.txt")) {
                echo "<h2>Wichtig!</h2>";
                echo readfile("update/update$VERSION.txt");
            }
        }
    };


    if (!function_exists('schemainfo')) {
        function schemainfo() {
            $sql = "SELECT tag || '.sql' as tag  from schema_info where tag like 'crm_%' order by tag";
            $rs  = $GLOBALS['db']->getAll($sql);
            if ( !$rs ) { $isnow = array(); }
            else { foreach ( $rs as $row ) { $isnow[] = $row["tag"];} };
            return $isnow;
        }
    }
    chdir($_SESSION['erppath'].'crm/');
    chdir("update");
    $update = glob("crm_*.sql");
    chdir("..");
    $isnow = schemainfo();
    $code = false;
    $ok = true;
    if ( !is_array($update) ) $update = array();
    $todo = array_diff($update,$isnow);
    if ( count($todo) > 0 ) {
        $skip = false;
        echo "<br>";
        $rc = $GLOBALS['db']->begin();
        //foreach ( $todo as $upd ) {
        while ( count($todo) > 0 ) {
            $upd = array_shift($todo);
            $f = fopen("update/".$upd,"r");
            while (!feof($f) ) {
                if ( empty($zeile)) { $zeile=trim(fgets($f,1000) ); continue; };
                $key = ''; $val = '';
                if ( preg_match("/^-- @([^:]+):(.+)/",$zeile,$treffer) ) {
                    $key = $treffer[1];
                    $val = trim($treffer[2]);
                    if ( $key == "php" ) {
                        //echo "PHP<br>\n";
                        $query = "";
                        $code = true;
                    } else if ( $key == "depends" ) {
                        $sql = "SELECT tag  from schema_info where tag = '".$val."' order by tag";
                        $rs  = $GLOBALS['db']->getAll($sql);
                        if ( count($rs) == 0 ) {
                           echo "Probleme beim Update, alle &Auml;nderungen werden zur&uuml;ck genommen<br>";
                           echo "Abhängigkeit: <b>'$val'</b> nicht erfüllt<br>";
                           $GLOBALS['db']->rollback();
                           exit(1);
                        } else {
                            $zeile = trim(fgets($f,1000));
                            continue;
                        }
                    } else if ( $key == "check" ) {
                       echo 'Check: '.$val."<br>\n";
                    } else if ( $key == "exec" ) {
                        //echo "Exec:".$val."<br>\n";
                        $code = false;
                        $rc = eval($query);
                        $query = "";
                        if ( $rc < 0 ) {
                           echo "Probleme beim Update, alle &Auml;nderungen werden zur&uuml;ck genommen";
                           $GLOBALS['db']->rollback();
                           exit(1);
                        }
                    } else if ( $key == "require" ) {
                        //echo "Require:".'crm_'.$val.'.sql'."<br>\n";
                        if ( in_array('crm_'.$val.'.sql',$todo) and ($val != '*') ) { // Dir Abhängigkeit soll auch noch installiert werden
                            array_push($todo,$upd);          // also hinten anhängen
                            fseek($f,-1,SEEK_END);           // und abbrechen
                            $skip = true;
                            $zeile = trim(fgets($f,1000));
                            continue;
                        }
                    } else {
                        ${$key} = $treffer[2];
                        echo $key.":".${$key}."<br>"; flush();
                    }
                    $zeile = trim(fgets($f,1000));
                    continue;
                };
                if ( preg_match("/^--/",$zeile) ) { $zeile = trim(fgets($f,1000)); continue; }; //Kommentare
                if ( !preg_match("/;$/",$zeile) or $code ) {
                    if ( !preg_match("#\\s?//#",$zeile) )  $query .= $zeile;
                } else {
                    $query .= substr($zeile,0,-1);
                    $rc = $GLOBALS['db']->query($query, True);
                    if ( !$rc ) {
                        echo "Probleme beim Update, alle &Auml;nderungen werden zur&uuml;ck genommen";
                        $GLOBALS['db']->rollback();
                        exit(1);
                    }
                    $query = "";
                }
                $zeile = trim(fgets($f,1000));
            }
            if ( !$skip ) {
                 $sql = "insert into schema_info (tag,login) values ('crm_%s','%s')";
                 $rc = $GLOBALS['db']->query(sprintf($sql,trim($tag),$_SESSION["login"]));
                 if ( !$rc ) {
                     $ok = false;
                     $GLOBALS['db']->rollback();
                     exit(2);
                 } else {
                     echo "ok<br>";
                 }
                 $GLOBALS['db']->commit();
                 $isnow = schemainfo(); // Neu einlesen, da evtl. Inserts in Schema durch Updatefile gemacht wurde.
                 $todo = array_diff($update,$isnow);
            } else {
                 $skip = false;
            }
        }
        if ( $ok ) {
            //require ('version.php');
            //updatever($VERSION);
            echo "<br>update ok<br>";
        }
    } else {
        if ( $menu ) {
            if ( $isnow ) foreach ( $isnow as $row ) echo $row.'<br>';
            echo 'Keine Updates notwendig';
        }
        if ( isset($dbver) and $dbver <> $VERSION ) updatever($VERSION);
    };
    if ( $menu ) {
        echo $menu['end_content'];
        echo '</body></html>';
    }
?>
