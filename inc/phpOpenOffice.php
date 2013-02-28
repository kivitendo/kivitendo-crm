<?php
// +----------------------------------------------------------------------+
// | phpOpenOffice - Solution for modifying OpenOffice documents with PHP |
// | v0.1b - 18/12/2003                                                   |
// |                                                                      |
// | This software is published under BSD licence.                        |
// | http://www.opensource.org/licenses/bsd-license.php                   |
// +----------------------------------------------------------------------+
// | Written by Bjoern Kahle, Hamburg 2003 (phpopenoffice at pinasoft.de) |
// | http://www.pinasoft.de/projects/phpOpenOffice/                       |
// +----------------------------------------------------------------------+

// Where is phpOpenOffice going to store the documents temporarly
if (!defined('POO_TMP_PATH')) {
  define('POO_TMP_PATH', './tmp');
}


// Where are the OpenOffice templates
if (!defined('POO_TEMPLATE_PATH')) {
  define('POO_TEMPLATE_PATH', "");
}

// PhpConcept Library - Zip Module 2.0
// http://www.phpconcept.net
if (!defined('PCLZIP_INCLUDE_PATH')) {
//  define('PCLZIP_INCLUDE_PATH',"./pclzip/");
  define('PCLZIP_INCLUDE_PATH',"./inc/");
}
define( 'PCLZIP_TEMPORARY_DIR', POO_TMP_PATH );
require PCLZIP_INCLUDE_PATH . 'pclzip.lib.php';


// Use zlib from PHPMyAdmin for writing zipped files,
// because documents created with PclZip cannot be opened with OpenOffice
// Needs to be fixed in later version.
if (!defined('ZIPLIB_INCLUDE_PATH')) {
  define('ZIPLIB_INCLUDE_PATH', "./inc/");
}
require ZIPLIB_INCLUDE_PATH . 'zip.lib.php';


// How are variables defined within the document
if (!defined('POO_VAR_PREFIX')) {
  define('POO_VAR_PREFIX', '%');
}

if (!defined('POO_VAR_SUFFIX')) {
  define('POO_VAR_SUFFIX', '%');
}

if (!defined('POO_VAR_MINLEN')) {
  define('POO_VAR_MINLEN', 2);
}

// Callback function for pclzip
$archiveFiles = array();
function ooPreAdd($p_event, &$p_header)
{
	global $archiveFiles;
	if($p_header['folder'] == 0)
		$archiveFiles[] = $p_header["stored_filename"];
	return 0;
}


class phpOpenOffice
{
	var $tmpDirName = "";
	var $parserFiles = "";
	var $parserFilessave = "";
	var $parsedDocuments = "";
	var $mimetypeFile = "";
	var $mimetype = "";
	var $zipFile = "";
	var $downloadfile = "";
    var $serbrief = False;

    function savecontent() {
        $this->serbrief = True;
		foreach (array_keys($this->parserFiles) as $file) {
			$fp = fopen($this->parserFiles[$file], "r");
			$this->DocumentsContent[$file] = fread($fp, filesize($this->parserFiles[$file]));
			fclose($fp);
        }
    }

    function getoriginal() {
        empty($this->parsedDocuments);
    }
    function cleanTemplate() {
    }

	// Load document from filesystem
	function loadDocument($filename)
	{
		if(!file_exists($filename))
		{
			$this->handleError("File not found: ".$filename, E_USER_ERROR);
		}
		else
		{
			$this->zipFile = $filename;
		}

		// Find a random folder name for PCLZIP_OPT_ADD_PATH
		$this->tmpDirName = $this->getRandomString(16);
		$this->mimetypeFile = POO_TMP_PATH."/".$this->tmpDirName."/mimetype";
		$this->parserFiles = array();
		$this->parserFiles["content.xml"] = POO_TMP_PATH."/".$this->tmpDirName."/content.xml";
		$this->parserFiles["styles.xml"] = POO_TMP_PATH."/".$this->tmpDirName."/styles.xml";

		// Open archive and extract content.xml
		$archive = new PclZip($filename);
		$list = $archive->extract(PCLZIP_OPT_PATH, POO_TMP_PATH, PCLZIP_OPT_ADD_PATH, $this->tmpDirName);
	}
    function getTags() {
        $fp = fopen($this->parserFiles["content.xml"], "r+b");
        $file = fread($fp, filesize($this->parserFiles["content.xml"]));
        preg_match_all('/'.POO_VAR_PREFIX.'(<text:span[^>]+>)?([A-Z_0-9]{'.POO_VAR_MINLEN.',})(<\/text:span>)?'.POO_VAR_SUFFIX.'/i',$file,$hits);
        fseek($fp,0);
        fwrite($fp,$file);
        fclose($fp);
        return $hits[2];
    }
	function findtags($key,$string) {
		$pos=strpos($string,$key);
		if (!$pos) return false;	
		$max=strlen($string);
		$i=$pos+strlen($key);
		$after="";
		while ($string[$i]<>"<" and $i<$max) { $after.=$string[$i]; $i++; } 
		$von=$i;
		while ($string[$i]<>">" and $i<$max) { $i++; } 
		$len=$i-$von;
		$end=substr($string,$von,$len);
		$i=$pos+1;
		while ($string[$i]<>">" and $i>0) { $i--; } 
		$len=$pos-$i-1;
		$before=substr($string,$i+1,$len);
		$bis=$i;
		while ($string[$i]<>"<" and $i>0) { $i--; } 
		$len=$bis-$i;
		$start=substr($string,$i,$len);
		return array("open"=>"$start>","close"=>"$end>","before"=>$before,"after"=>$after);
	}

	// Put variables into extracted content file
	function parse($variables)
	{
		// Has file been extracted ?
		if($this->tmpDirName == "")
		{
			$this->handleError("No document loaded. Use loadDocument function first.", E_USER_ERROR);
		}

		// Is dir still there
		if(!is_dir(POO_TMP_PATH."/".$this->tmpDirName))
		{
			$this->handleError("Directory not found: ".$this->tmpDirName, E_USER_ERROR);
		}

		// Is argument valid ?
		if(!is_array($variables))
		{
			$this->handleError("First parameter need to been an array.", E_USER_ERROR);
		}

		// Read mimetype
		$fp = fopen($this->mimetypeFile, "r");
		$this->mimetype = fread($fp, filesize($this->mimetypeFile));
		fclose($fp);

		// Open files and start parsing
		$parsedDocuments = array();
		foreach (array_keys($this->parserFiles) as $file)
		{
            if ($this->serbrief) {
                $this->parsedDocuments[$file] = $this->DocumentsContent[$file];
            } else {
			    $fp = fopen($this->parserFiles[$file], "r");
			    $this->parsedDocuments[$file] = fread($fp, filesize($this->parserFiles[$file]));
			    fclose($fp);
            }
			foreach(array_keys($variables) as $key)
			{
				$value = $this->xmlencode( $variables[$key] );
                $this->parsedDocuments[$file] = preg_replace('/'.POO_VAR_PREFIX.'(<text:span[^>]+>)?'.$key.'(<\/text:span>)?'.POO_VAR_SUFFIX.'/i',$value,$this->parsedDocuments[$file]);
				//$this->parsedDocuments[$file] = str_replace(POO_VAR_PREFIX.$key.POO_VAR_SUFFIX, $value, $this->parsedDocuments[$file]);
				/*
				if (!strpos($value,"\n")) {
					$this->parsedDocuments[$file] = str_replace(POO_VAR_PREFIX.$key.POO_VAR_SUFFIX, $value, $this->parsedDocuments[$file]);
				} else {
					$tmp=explode("\n",$value);
					$first=true;
					$ersetze="";
					$tags=$this->findtags(POO_VAR_PREFIX.$key.POO_VAR_SUFFIX,$this->parsedDocuments[$file]);
					$max=count($tmp);
					if($tags["after"]) $max--;
					for($ii=0; $ii<$max; $ii++) {
						if($first) {
							$ersetze.=$tags["open"].$tags["before"].$tmp[$ii].$tags["close"];
							$first=false;
						} else {
							$ersetze.=$tags["open"].$tmp[$ii].$tags["close"];
						}
					}
					if($tags["after"]) $ersetze.=$tags["open"].$tmp[$max].$tags["after"].$tags["close"];
					$suche=$tags["open"].$tags["before"].POO_VAR_PREFIX.$key.POO_VAR_SUFFIX.$tags["after"].$tags["close"];
					$this->parsedDocuments[$file] = str_replace($suche, $ersetze, $this->parsedDocuments[$file]);
				}
				*/
			}
		}
	}


	// encode string xml compatible
	function xmlencode($param)
	{
		$xml = $param;

		$xml = str_replace("&", "&amp;", $xml);
		$xml = str_replace(">", "&gt;", $xml);
		$xml = str_replace("<", "&lt;", $xml);
		$xml = str_replace("'", "&apos;", $xml);
		$xml = str_replace("\"", "&quot;", $xml);
		$xml = str_replace("\n", "<text:line-break/>", $xml);
		$xml = str_replace("\r", "", $xml);

		$xml = utf8_encode($xml);
		return $xml;
	}


    function save($filename) {
        $this->savefile($filename);
    }

	// Save parsed document
	function savefile($filename)
	{
		global $archiveFiles;

		// Has file been extracted ?
		if($this->tmpDirName == "") {
			$this->handleError("No document loaded. Use loadDocument function first.", E_USER_ERROR);
		}

		// Is dir still there
		if(!is_dir(POO_TMP_PATH."/".$this->tmpDirName)) {
			$this->handleError("Directory not found: ".$this->tmpDirName, E_USER_ERROR);
		}

		// Overwrite parsed documents
		foreach (array_keys($this->parserFiles) as $file) {
			$fp = fopen($this->parserFiles[$file], "w+");
			fputs($fp, $this->parsedDocuments[$file]);
			fclose($fp);
		}

		// Create new (zip-)file - Add all files and subdirectories from temporary directory
		$archive = new PclZip($filename);
		$v_list = $archive->create(POO_TMP_PATH."/".$this->tmpDirName, PCLZIP_OPT_REMOVE_PATH, POO_TMP_PATH."/".$this->tmpDirName."/", PCLZIP_CB_PRE_ADD, "ooPreAdd");

		// zip.lib dirty hack
		$zip = new zipfile();

		// Add specials files without compression
		for($i = 0; $i < count($archiveFiles); $i++)
		{
			$file = $archiveFiles[$i];

			// zip.lib dirty hack
			$fp = fopen(POO_TMP_PATH."/".$this->tmpDirName."/".$file, "r");
			$content = @fread($fp, filesize(POO_TMP_PATH."/".$this->tmpDirName."/".$file));
			fclose($fp);
			$zip->addFile($content, $file);
		}

		// Finally write file to disk => zip.lib dirty hack
		$fp = fopen($filename, "w+");
		fputs($fp, $zip->file());
		fclose($fp);
	}

	function prepsave($filename) {
		if($filename == "") $filename = $this->getRandomString(16);
		$info = pathinfo($this->zipFile);
		$fullfile = $filename; //.".".$info["extension"];
		$this->downloadFile = POO_TMP_PATH."/".$fullfile;
		$this->savefile($this->downloadFile);
		//echo "!".$filename;
		//echo $this->downloadFile."!";

		// Read temp file
		//$fp = fopen($this->downloadFile, "r");
		//$content = fread($fp, filesize($this->downloadFile));
		//fclose($fp);		
	}

	function download($filename)
	{
		// Build filename and save file temporarly to harddisk
		if($filename == "") $filename = $this->getRandomString(16);
		$info = pathinfo($this->zipFile);
		$fullfile = $filename.".".$info["extension"];
		$this->downloadFile = POO_TMP_PATH."/".$fullfile;
		$this->savefile($this->downloadFile);

		// Read temp file
		$fp = fopen($this->downloadFile, "r");
		$content = fread($fp, filesize($this->downloadFile));
		fclose($fp);

		// Build HTTP header and send file
		header("Expires: ".date("D, d M Y H:i:s", time() - 24 * 60 * 60)." GMT");	// expires in the past
		header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");			// Last modified, right now
		header("Cache-Control: no-cache, must-revalidate");				// Prevent caching, HTTP/1.1
		header("Pragma: no-cache");
		header("Content-Type: ".$this->mimetype);
		header('Content-Length: '.filesize($this->downloadFile));
		header('Content-Transfer-Encoding: binary');


		// (Browser specific)
		$browser = $_SERVER["HTTP_USER_AGENT"];
		if( preg_match('/MSIE 5.5/', $browser) || preg_match('/MSIE 6.0/', $browser) )
		{
			header('Content-Disposition: filename="'.$fullfile.'"');
		}
		else
		{
			header('Content-Disposition: attachment; filename="'.$fullfile.'"');
		}


		// Data
		echo $content;

		// Delete temp file
		unlink($this->downloadFile);
	}


	// Cleans up filesystem after job is done
	function clean()
	{
		if($this->tmpDirName == "")
			return;
		$tmpPath = POO_TMP_PATH."/".$this->tmpDirName;
		@unlink($this->downloadFile);
		$this->deldir($tmpPath);
	}


	// Returns random string..easy, eh ?
	function getRandomString($length)
	{
		srand(date("s"));
		$possible_charactors = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$string = "";
		while(strlen($string)<$length)
		{
			$string .= substr($possible_charactors, rand()%(strlen($possible_charactors)), 1);
		}
		return($string);
	}


	// Default error handler
	function handleError($errorMessage, $errorType = E_USER_WARNING)
	{
		$prefix = 'phpOpenOffice ' . (($errorType == E_USER_ERROR) ? 'Error' : 'Warning') . ': ';
		echo $prefix . $errorMessage;

		if($errorType == E_USER_ERROR) die;
    	}


	// Borrowed from marcelognunez at hotmail dot com
	function deldir($dir)
	{
		$current_dir = opendir($dir);
		while($entryname = readdir($current_dir))
		{
			if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!=".."))
			{
				$this->deldir("${dir}/${entryname}");
			}
			elseif($entryname != "." and $entryname!="..")
			{
				unlink("${dir}/${entryname}");
		}
		}
		closedir($current_dir);
		rmdir(${dir});
	}

}

?>
