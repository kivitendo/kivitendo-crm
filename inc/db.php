<?php
if (! @include_once('DB.php') ) {
    echo "Konnte das Modul DB nicht laden!<br>";
    echo "Pr&uuml;fen Sie Ihre Installation:<br>";
    echo "pear list | grep DB<br>";
    echo "Variable '\$include_path' in der php.ini<br>"; 
    echo "aktueller Wert: ".ini_get('include_path');
    echo "<br><br><a href='inc/install.php?check=1'>Installations - Check durchf&uuml;hren</a><br>";
    exit (1);
};

class myDB extends DB {

 var $db = false;
 var $rc = false;
 var $showErr = false; // Browserausgabe
 var $log = false;     // Alle Abfragen mitloggen
 var $errfile = "/tmp/lxcrm.err";
 var $logfile = "/tmp/lxcrm.log";
 var $lfh = false;

    /**********************************************
    * dbFehler - Fehler in ein Log-File ausgeben
    * IN: $sql - SQL-Statement
    * IN: $err - Fehlermeldung
    * OUT: NONE
    **********************************************/
    function dbFehler($sql,$err,$rc=false) {
        $efh=fopen($this->errfile,"a");
        fputs($efh,date("Y-m-d H:i:s \n"));
        fputs($efh,'SQL:'.$sql."\n");
        fputs($efh,'Msg:'.$err."\n");
        if ($rc) {
            fputs($efh,print_r($rc->backtrace[0],true)."\n");
            $cnt=count($rc->backtrace);
            for ($i=0; $i<$cnt; $i++) {
                fputs($efh,$rc->backtrace[$i]['line'].':'.$rc->backtrace[$i]['file']."\n");
            }
        };
           fputs($efh,"--------------------------------------------- \n");
        fputs($efh,"\n");
        fclose($efh);
        if ($this->showErr)
            echo "</td></tr></table><font color='red'>$sql : $err</font><br>";
    }

    /**********************************************
    * writelog - Texte in ein Log-File ausgeben
    * IN: $txt - SQL-Statement oder Text
    * OUT: NONE
    **********************************************/
    function writeLog($txt) {
        if ($this->lfh===false)
            $this->lfh=fopen($this->logfile,"a");
        fputs($this->lfh,date("Y-m-d H:i:s ->"));
        fputs($this->lfh,$txt."\n");
        fputs($this->lfh,"\n");
    }

    function closeLogfile() {
        fclose($this->lfh);
    }
    
    /**********************************************
    * myDB - Konstruktor
    * IN: $host,$user,$pwd,$db,$port - Parameter der Datenbank
    * OUT: DB-Objekt
    **********************************************/
    function myDB($host,$user,$pwd,$db,$port) {
        $dsn = array(
                    'phptype'  => 'pgsql',
                    'username' => $user,
                    'password' => $pwd,
                    'hostspec' => $host,
                    'database' => $db,
                    'port'     => $port
                );
        $this->db=DB::connect($dsn);
        if (!$this->db || DB::isError($this->db)) {
            if ($this->log) $this->writeLog("Connect Error: $dns");
            $this->dbFehler("Connect ".print_r($dsn,true),$this->db->getMessage()); 
            die ($this->db->getMessage());
        }
        if ($this->log) $this->writeLog("Connect: ok ");
        return $this->db;
    }

    /**********************************************
    * query - beliebiges SQL-Statement absetzen
    * IN: $sql - Statement
    * OUT: true/false
    **********************************************/
    function query($sql) {
        if (strpos($sql,";")>0) {
            //Sql-Injection? HTML-Sonderzeichen zulassen
            //if (!preg_match("/&[a-zA-Z]+$/",substr($sql,0,strpos($sql,";")))) {
            //ok, kommt nach einem Semikolon ein SQL-Befehl? Dann abweisen
            if (preg_match("/.+[^;]*[ ]*(drop|delete|insert|select|update) .*/i",$sql)) {
                $this->dbFehler($sql,"SQL-Injection?");
                return false;
                //
                //oder
                //Das Semikolon in Anführungsstriche setzen und weitermachen.
                //Hilft aber nicht wenn mehrere Kommandos nacheinander kommen. 
                //evtl. das "if" durch eine Whileschleife ersetzen.
                //$sql = preg_replace('/([^;]*)(;[ ]*)(drop|delete|insert|select|update)( .*)/i','${1}\"${2}\"${3}${4}',$sql);
                //
            }
        }
        $rc=@$this->db->query($sql);
        if ($this->log) $this->writeLog($sql);
        if(DB::isError($rc)) {
            $this->dbFehler($sql,$rc->getMessage(),$rc);
            $this->rollback();
            return false;
        } else {
            return $rc;
        }
    }

    /**********************************************
    * update - einen Datensatz modifizieren
    * IN: $table - Tabelle
    * IN: $fields - betroffene Felder
    * IN: $values - dazugehörige Werte
    * IN: $where - welcher Datensatz
    * OUT: true/false
    **********************************************/
    function update($table,$fields,$values,$where) {
        if (strpos($where,"=")<1) {
            $this->dbFehler('Update','Where missing or wrong: '.$where);
            $this->dbFehler('Update',print_r(debug_backtrace(),true));
            return false;
        }
        if ($this->log) {
            $this->writeLog('Update auf: '.$table);
            $this->writeLog(print_r($fields,true));
            $this->writeLog(print_r($values,true));
        }
        //SQL-Statement vorbereiten
        $sth = $this->db->autoPrepare($table, $fields, DB_AUTOQUERY_UPDATE,$where);
        if (PEAR::isError($sth)) {
            $this->dbFehler($sql,$sth->getMessage());
            $this->rollback();
            return false;
        }
        //wenn ok, ausführen
        $rc=@$this->db->execute($sth, $values);
        if(DB::isError($rc)) {
            $this->dbFehler($sql,$rc->getMessage());
            $this->dbFehler(print_r($fields,true),print_r($values,true));
            $this->rollback();
            return false;
        }
        return true;
    }

    /**********************************************
    * insert - einen neuen Datensatz anlegen
    * IN: $table - Tabelle
    * IN: $fields - betroffene Felder
    * IN: $values - dazugehörige Werte
    * OUT: true/false
    **********************************************/
    function insert($table,$fields,$values) {
        if ($this->log) {
            $this->writeLog('Insert in: '.$table);
            $this->writeLog(print_r($fields,true));
            $this->writeLog(print_r($values,true));
        }
        //SQL-Statement vorbereiten
        $sth = $this->db->autoPrepare($table, $fields, DB_AUTOQUERY_INSERT);
        if (PEAR::isError($sth)) {
            $this->dbFehler($sql,$sth->getMessage());
            $this->dbFehler(print_r($fields,true),print_r($values,true));
            $this->rollback();
            return false;
        }
        //wenn ok, ausführen
        $rc=@$this->db->execute($sth, $values);
        if(DB::isError($rc)) {
            $this->dbFehler($sql,$rc->getMessage(),$rc);
            $this->rollback();
            return false;
        }
        return true;
    }

    function begin() {
        if ($this->log) $this->writeLog('BEGIN');
        return $this->query("BEGIN");
    }
    function commit() {
        if ($this->log) $this->writeLog('COMMIT');
        return $this->query("COMMIT");
    }
    function rollback() {
        if ($this->log) $this->writeLog('ROLLBACK');
        return $this->query("ROLLBACK");
    }

    function getAll($sql) {
        if (strpos($sql,";")>0) return false;
        $rc=$this->db->getAll($sql,DB_FETCHMODE_ASSOC);
        if ($this->log) $this->writeLog($sql);
        if(DB::isError($rc)) {
            $this->dbFehler($sql,$rc->getMessage(),$rc);
            return false;
        } else {
            return $rc;
        }
    }
/**
 * Holt die Daten als assoziatives Array aus der DB.
 * S.a. PEAR::getAssoc
 * return mixed, false
 */
  function getAssoc($sql){
    $rc=$this->db->getAssoc($sql);
    if ($this->log) $this->writeLog($sql);
    if(DB::isError($rc)) {
      $this->dbFehler($sql,$rc->getMessage(),$rc);
      return false;
    } else {
//          echo "Mem D4:" . memory_get_usage() . "\n"; // 36640
      return $rc;
    }
  }

    function getOne($sql) {
        $rs = $this->getAll($sql);
        if ($rs) {
            return $rs[0];
        } else {
            return false;
        }
    }
    function saveData($txt) {
        if (get_magic_quotes_runtime()) {
            return $txt;
        } else {
            return $this->db->escapeSimple($txt);
        }
    }
    /**
     *
     * Benutzt PEAR::executeMultiple. Erwartet als
     * Zeichenkette das PreparedStatement
     * und die entsprechenden Werte für das Statement
     * Ist ferner transaktionssicher (autocommit off)
     * @param string $statement
     * @param mixed $data
     * 
     * @return boolean
     */
    function executeMultiple($statement, $data){
        if ($this->log) {                            //Logging
            $this->writeLog("executeMultiple: $statement");
            $this->writeLog("mit den Werten:" . $data);
        }

        $sth = $this->db->prepare($statement);                        //Prepare
        if (PEAR::isError($sth)) {
            $this->dbFehler($sql,$sth->getMessage());
            $this->rollback();
            return false;
        }

        $this->db->autoCommit(false);                                            //Autocommit aus
        $res =& $this->db->executeMultiple($sth, $data);     //Daten senden
        $this->db->commit();                                                            // Commit
        if (PEAR::isError($res)) {
            $this->dbFehler($sql,$sth->getMessage());
            $this->rollback();
            return false;
        }
        return true;
    }
}
?>
