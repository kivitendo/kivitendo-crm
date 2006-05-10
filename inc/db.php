<?
class myDB extends DB {

 var $db = false;
 var $rc = false;
 var $showErr = false; // Browserausgabe
 var $debug = false; // 1 = SQL-Ausgabe, 2 = zusätzlich Ergebnis
 var $log = false;  // Alle Abfragen mitloggen
 var $errfile = "tmp/lxcrm.err";
 var $logfile = "tmp/lxcrm.log";
 var $efh = false;
 var $lfh = false;
 var $stime = false;
 var $ltime = 1000;
 
 	function valid() {
 		return ((time - $this->stime)<1000)?true:false;
 	}
	function dbFehler($sql,$err) {
		$this->efh=fopen($this->errfile,"a");
		fputs($this->efh,date("Y-m-d H:i:s ->"));
		fputs($this->efh,$sql."\n");
		fputs($this->efh,$err."\n");
		fputs($this->efh,print_r($this->rc,true));
		fputs($this->efh,"\n");
		fclose($this->efh);
		if ($this->showErr)
			echo "</td></tr></table><font color='red'>$sql : $err</font><br>";
	}

	function showDebug($sql) {
		echo $sql."<br><pre>";
		if ($this->debug==2) {
			print_r($this->rc);
			echo "</pre>";
		};
	}

	function writeLog($txt) {
		if (!$this->lfh) $this->lfh=fopen($this->logfile,"a");
		fputs($this->lfh,date("Y-m-d H:i:s ->"));
		fputs($this->lfh,$txt."\n");
		fputs($this->lfh,print_r($this->rc,true));
		fputs($this->lfh,"\n");
	}

	function closeErr() {
		fclose($this->efh);
	}
	function closeLog() {
		fclose($this->lfh);
	}
	
	function myDB($dns) {
		$dns="pgsql://".$dns;
		$this->db=DB::connect($dns);
		if (!$this->db || DB::isError($this->db)) {
			if ($this->log) $this->writeLog("Connect $dns");
			$this->dbFehler("Connect $dns",$this->db->getMessage()); 
			die ($this->db->getMessage());
		}
		if ($this->log) $this->writeLog("Connect: ok ");
		$this->stime=time();
		return $this->db;
	}

	function query($sql) {
		$this->stime=time();
		$this->rc=@$this->db->query($sql);
		if ($this->debug) $this->showDebug($sql);
		if ($this->log) $this->writeLog($sql);
		if(DB::isError($this->rc)) {
			$this->dbFehler($sql,$this->rc->getMessage());
			$this->rollback();
			return false;
		} else {
			return $this->rc;
		}
	}

	function begin() {
		$this->query("BEGIN");
	}
	function commit() {
		$this->query("COMMIT");
	}
	function rollback() {
		$this->query("ROLLBACK");
	}

	function getAll($sql) {
		$this->stime=time();
		$this->rc=$this->db->getAll($sql,DB_FETCHMODE_ASSOC);
		if ($this->debug) $this->showDebug($sql);
		if ($this->log) $this->writeLog($sql);
		if(DB::isError($this->rc)) {
			$this->dbFehler($sql,$this->rc->getMessage());
			return false;
		} else {
			return $this->rc;
		}
	}

}
?>
