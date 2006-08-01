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
 
	function dbFehler($sql,$err) {
		if (!$this->efh)
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
		echo $sql."<br>";
		if ($this->debug==2) {
			echo "<pre>";
			print_r($this->rc);
			echo "</pre>";
		};
	}

	function writeLog($txt) {
		if (!$this->lfh)
			$this->lfh=fopen($this->logfile,"a");
		fputs($this->lfh,date("Y-m-d H:i:s ->"));
		fputs($this->lfh,$txt."\n");
		fputs($this->lfh,print_r($this->rc,true));
		fputs($this->lfh,"\n");
	}

	function closeErrfile() {
		fclose($this->efh);
	}
	function closeLogfile() {
		fclose($this->lfh);
	}
	
	function myDB($host,$user,$pwd,$db,$showErr=false) {
		if ($pwd>"") {
			$passwd=":".$this->uudecode($pwd);
		} else {
			$passwd="";
		}
		$dns="pgsql://$user$passwd@$host/$db";
		$this->showErr=$showErr;
		$this->db=DB::connect($dns);
		if (!$this->db || DB::isError($this->db)) {
			if ($this->log) $this->writeLog("Connect $dns");
			$this->dbFehler("Connect $dns",$this->db->getMessage()); 
			die ($this->db->getMessage());
		}
		if ($this->log) $this->writeLog("Connect: ok ");
		return $this->db;
	}

	function query($sql) {
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

	/****************************************************
	* uudecode
	* in: string
	* out: string
	* dekodiert Perl-UU-kodierte Passwort-Strings
	* http://de3.php.net/base64_decode (bug #171)
	*****************************************************/
	function uudecode($encode) {
		$encode=stripslashes($encode);
		$b64chars="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		$encode = preg_replace("/^./m","",$encode);
		$encode = preg_replace("/\n/m","",$encode);
		for($i=0; $i<strlen($encode); $i++) {
			if ($encode[$i] == '') $encode[$i] = ' ';
			$encode[$i] = $b64chars[ord($encode[$i])-32];
		}
		while(strlen($encode) % 4) $encode .= "=";
		return base64_decode($encode);
	}
}
?>
