<?
class myDB extends DB {

 var $rc = false;
 var $showErr = false;
 var $db = false;
 var $debug = false;
 var $log = true;
 var $errfile = "tmp/lxcrm.err";
 var $efh = false;
 var $stime = false;
 var $ltime = 1000;
 
 	function valid() {
 		return ((time - $this->stime)<1000)?true:false;
 	}
	function dbFehler($sql,$err) {
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

	function writeErr($txt) {
		if (!$this->efh) $this->efh=fopen($this->errfile,"a");
		fputs($this->efh,date("Y-m-d H:i:s ->"));
		fputs($this->efh,$txt."\n");
		fputs($this->efh,print_r($this->rc,true));
		fputs($this->efh,"\n");
	}

	function closeErr() {
		fclose($this->efh);
	}
	
	function myDB($dns) {
		$dns="pgsql://".$dns;
		$this->db=DB::connect($dns);
		if (!$this->db) DB::dbFehler("",$this->db->getDebugInfo());
		if (DB::isError($this->db)) {
			$this->writeErr("Connect",$this->db->getDebugInfo());
			$this->dbFehler("Connect",$this->db->getDebugInfo());
			die ($this->db->getDebugInfo());
		}
		$this->stime=time();
		return $this->db;
	}

	function query($sql) {
		$this->stime=time();
		$this->rc=@$this->db->query($sql);
		if ($this->debug) $this->showDebug($sql);
		if ($this->log) $this->writeErr($sql);
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
		if ($this->log) $this->writeErr($sql);
		if(DB::isError($this->rc)) {
			$this->dbFehler($sql,$this->rc->getMessage());
			return false;
		} else {
			return $this->rc;
		}
	}

}
?>
