<?
class document {

 var $db = false;
 var $error = false;
 var $name = "";
 var $pfad = "";
 var $descript = "";
 var $size = false;
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
	function setDocData($key,$val=false) {
		$this->$key=$val;
	}
	function chkDocData() {
		//Name und Pfad müssen gesetzt sein
		if (empty($this->name) || empty($this->pfad))  return false; 
		return true;
	}
	function saveDocument() {
		//Dokumentdaten in db speichern
		if (!$this->chkDocData()) return false; 
		if (!$this->id) {
			//es ist ein neues Dokument, bzw. eines ohne db-Eintrag
			//wurde ohne Anwendung ins Verzeichnis gestellt.
			return $this->newDocument();
		} else {
			//einen bestehenden Eintrag ändern
			$sql ="update documents set filename='".$this->name."',";
			$sql.="descript='".$this->descript."',datum='".date("Y-m-d")."',zeit='".date("H:i:s")."',";
			//wird bei Upload gesetzt.
			if ($this->size) $sql.="size=".$this->size.",";
			$sql.="employee=".$_SESSION["loginCRM"].",pfad='".$this->pfad."' where id=".$this->id;
			if (!$this->db->query($sql)) {
				$this->error="Datei '".$this->name."' nicht gesichert";
				return false;
			} else {
				$this->error="";
				return true;
			}
		}
	}
	function newDocument() {
		$sql ="insert into documents (filename,descript,datum,zeit,size,employee,pfad) ";
		$sql.="values ('%s','%s','%s','%s',%d,%d,'%s')";
		$sql=sprintf($sql,$this->name,$this->descript,date("Y-m-d"),date("H:i:s"),$this->size,$_SESSION["loginCRM"],$this->pfad);
		$rc=$this->db->query($sql);
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

	function searchDocument($name,$pfad) {
		if (substr($pfad,-1)=="/") $pfad=substr($pfad,0,-1);
		$sql="select * from documents where filename = '$name' and pfad = '$pfad'";
		$rs=$this->db->getAll($sql);
		if(!$rs) {
			return false;
		}
		return $rs[0]["id"];
	}

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

	function getDokument($id) {
		$sql="select * from documents where id=$id";
		$rs=$this->db->getAll($sql);
		$this->setDocData("name",($rs)?$rs[0]["filename"]:false);
		$this->setDocData("pfad",($rs)?$rs[0]["pfad"]:false);
		$this->setDocData("descript",($rs)?$rs[0]["descript"]:false);
		$this->setDocData("size",($rs)?$rs[0]["size"]:false);
		$this->setDocData("id",($rs)?$id:false);
		if(!$rs) return false;
		return $rs[0];
	}

} 
