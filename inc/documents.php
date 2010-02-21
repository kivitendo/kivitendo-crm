<?php
/**
*	document	
*	Klasse für Dokumentenhandling
*
*
*
*       @version        0.2
*       @author 	Holger Lindemann <hli@lx-system.de>
*
*	Paket	Lx-Office-CRM
*/
class document {


/**
*	aktuelle Datenbankinstanz
*       @var object $db
*/
 var $db = false;

/**
*	Die letzte Fehlermeldung
*       @var string $error
*/
 var $error = false;

/**
*	Dokumentenname
*       @var string $name
*/
 var $name = "";

/**
*	Dokumentenpfad
*       @var string $pfad
*/
 var $pfad = "";

/**
*	Dokumentenbeschreibung
*       @var string $descript
*/
 var $descript = "";

/**
*	Dateigröße
*       @var integer $size
*/
 var $size = 0;

/**
* lock	
*       @var int $lock
*/
 var $lock = 0;

/**
*	lockname
*       @var string $lockname
*/
 var $lockname = "";

/**
*	Dokumenten ID
*       @var integer $id
*/
 var $id = false;

	function document($id=false,$fname="",$fpath="",$descript="") {
		$this->db=$_SESSION["db"];
		if ($id>0) {
			//Variablen mit db-Eintrag vorbelegen
			getDokument($id);
		} else {
			$this->setDocData("name",$fname);
			if (substr($fpath,-1)=="/") $fpath=substr($fpath,0,-1);
			$this->setDocData("pfad",$fpath);
			$this->setDocData("descript",$descript);
		}
	}

/**
*	Dokumentvariable setzen
*
*       @access public
*       @param  string $key	Name der Variablen
*       @param  string $val	Wert der Variablen
*/
	function setDocData($key,$val=false) {
		$this->$key=$val;
	}

/**
*	Ein Dokument muß Namen und Pfad haben
*	Daten vorher einstellen mit setDocData()
*
*       @access public
*       @return boolean         Erfolg der Aktion
*/
	function chkDocData() {
		//Name und Pfad müssen gesetzt sein
		if (empty($this->name) || empty($this->pfad))  return false; 
		return true;
	}

/**
*	Dokumentdaten speichern
*	Daten vorher einstellen mit setDocData()
*
*       @access public
*       @return boolean         Erfolg der Aktion
*/
	function saveDocument() {
		//Dokumentdaten in db speichern
		if (!$this->chkDocData()) return false; 
		if (!$this->id) {
			//es ist ein neues Dokument, bzw. eines ohne db-Eintrag
			//wurde vielleicht ohne Anwendung ins Verzeichnis gestellt.
			return $this->newDocument();
		} else {
			//einen bestehenden Eintrag ändern
			$felder=array('filename','descript','datum','zeit','employee','pfad','lock');
			$werte=array($this->name,$this->descript,date("Y-m-d"),date("H:i:s"),$_SESSION["loginCRM"],$this->pfad,$this->lock);
			if ($this->size) {
				//wird bei einem Upload gesetzt.
				$felder[]='size';
				$werte[]=$this->size;
			}
			$rc=$this->db->update('documents',$felder,$werte,'id='.$this->id);
			if (!$rc) {
				$this->error="Datei '".$this->name."' nicht gesichert";
				return false;
			} else {
				$this->error="";
				return true;
			}
		}
	}

/**
*	Ein neues Dokument anlegen
*	Daten vorher einstellen mit setDocData()
*
*       @access public
*       @return boolean         Erfolg der Aktion
*/
	function newDocument() {
		$rc=$this->db->insert('documents',
					array('filename','descript','datum','zeit','size','employee','pfad'),
					array($this->name,$this->descript,date("Y-m-d"),date("H:i:s"),$this->size,$_SESSION["loginCRM"],$this->pfad)
			);
		if ($rc) {
			//sichern erfolgreich, ID holen.
			$this->id=$this->searchDocument($this->name,$this->pfad);
			$this->error="";
			return true;
		} else {
			$this->error="Datei '".$this->name."' nicht gesichert";
			$this->id=false;
			return false;
		}
	}

/**
*	Ein Dokument hochladen
*
*       @access public
*       @param  string $name	Name des Dokuments
*       @param  string $pfad	Pfad des Dokuments
*       @return boolean         Erfolg der Aktion
*/
	function uploadDocument($file,$pfad) {
		$this->name=$file["Datei"]["name"];
		$this->size=$file["Datei"]["size"];
		if (substr($pfad,-1)=="/") $pfad=substr($pfad,0,-1);
		$this->pfad=$pfad;
		//Gibt es das Dokument so schon in der db
		$this->id=$this->searchDocument($this->name,$pfad);
		$dest="./dokumente/".$_SESSION["mansel"]."/".$pfad."/".$this->name;
		if (chkdir($pfad)) {
			//Zielpfad vorhanden
			if (! copy($file["Datei"]["tmp_name"],$dest)) {
				$this->error="Datei '$dest' wurde nicht hochgeladen!";
				echo $this->error;
				return false;
			}
		} else {
			$this->error="Verzeichnis '$pfad' konte nicht angelegt werden!";
			return false;
		}
		return $this->saveDocument();
	}	

/**
*       Ein Dokument in der DB suchen
*
*       @access public
*       @param  string $name	Name des Dokuments
*       @param  string $pfad	Pfad des Dokuments
*       @return array  $id      ID des Dokuments oder "false"
*/
	function searchDocument($name,$pfad) {
		//kein abschließender Slash
		if (substr($pfad,-1)=="/") $pfad=substr($pfad,0,-1);
		$sql="select id from documents where filename = '$name' and pfad = '$pfad'";
		$rs=$this->db->getOne($sql);
		if(!$rs) {
			return false;
		}
		return $rs["id"];
	}

/**
*       Ein Dokument im Filesystem und in der DB löschen
*
*       @access public
*       @param  string  $p      woher kommt der Aufruf
*	@return boolean		Erfolg der Aktion
*/
	function deleteDocument($p="") {
		// $p=="" Aufruf aus Docroot, $p=="." Aufruf aus crmajax
		$dest="$p./dokumente/".$_SESSION["mansel"].$this->pfad."/".$this->name;
		$rc=unlink($dest);
		if (!$rc) {
			$this->error=$this->pfad."/".$this->name." kann nicht gelöscht werden.";
			return false;
		}
		if (!$this->id) $this->id=$this->searchDocument($this->name,$this->pfad);
		if ($this->id) {
			$sql="delete from documents where id = ".$this->id;
			$rc=$this->db->query($sql);	
			if (!$rc) {
				$this->error=$this->pfad."/".$this->name." kann nicht gelöscht werden.";
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		};
	}

/**
*       Ein Dokument aus  der DB holen
*
*       @access public
*       @param  integer $id      ID des Dokuments das angefordert wird
*       @return array   $rs      Dokumentendaten oder "false"
*/
	function getDokument($id) {
		$sql="SELECT d.*,COALESCE(e.name,e.login) as lockname from documents d left join employee e on d.lock=e.id where d.id = $id";
		$rs=$this->db->getOne($sql);
		$this->setDocData("name",($rs)?$rs["filename"]:false);
		$this->setDocData("pfad",($rs)?$rs["pfad"]:false);
		$this->setDocData("descript",($rs)?$rs["descript"]:false);
		$this->setDocData("size",($rs)?$rs["size"]:0);
		$this->setDocData("id",($rs)?$id:false);
		$this->setDocData("lock",($rs)?$rs["lock"]:0);
		$this->setDocData("lockname",($rs)?$rs["lockname"]:0);
		if(!$rs) return false;
		return $rs;
	}

} 
