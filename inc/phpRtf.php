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
class phpRTF
{

	var $parserFiles = "";
	var $parsedDocuments = "";

	// Load document from filesystem
	function loadDocument($filename)
	{
		if(!file_exists($filename))
		{
			$this->handleError("File not found: ".$filename, E_USER_ERROR);
		}

		// Find a random folder name for PCLZIP_OPT_ADD_PATH
		$info = pathinfo($filename);
		$this->parserFiles = POO_TMP_PATH."/".$this->getRandomString(16).".".$info["extension"];
		$rc = exec ("cp $filename ".$this->parserFiles);
	}

    function getTags() {
        $fp = fopen($this->parserFiles, "r+b");
        $file = fread($fp, filesize($this->parserFiles));
        preg_match_all('/'.POO_VAR_PREFIX.'([A-Z_0-9\{\}]{'.POO_VAR_MINLEN.',})'.POO_VAR_SUFFIX.'/i',$file,$hits);
        foreach ($hits[1] as $hit) { 
            $tmp = str_replace(array( '{', '}' ), '', $hit);
            $hits[2][] = $tmp;
            $file = str_replace(POO_VAR_PREFIX.$hit.POO_VAR_SUFFIX,POO_VAR_PREFIX.$tmp.POO_VAR_SUFFIX,$file);
        };
        fseek($fp,0);
        fwrite($fp,$file);
        fclose($fp);
        $this->parsedDocuments = $file;
        return $hits[2];
    }

	// Put variables into extracted content file
	function parse($variables)
	{
		// Is dir still there
		if(!is_file($this->parserFiles))
		{
			$this->handleError("File not found: ".$this->parserFiles, E_USER_ERROR);
		}

		// Is argument valid ?
		if(!is_array($variables))
		{
			$this->handleError("First parameter need to been an array.", E_USER_ERROR);
		}

		// Open files and start parsing
		$fp = fopen($this->parserFiles, "r");
		$this->parsedDocuments = fread($fp, filesize($this->parserFiles));
		fclose($fp);

		foreach(array_keys($variables) as $key)
		{
            //Zeilenümbrüche müssen noch implementiert werden
            //Dazu muß die GANZE Zeile in der der Platzhalter steht eingelesen und kopiert werden. Für JEDEN Umbruch
			$this->parsedDocuments = str_replace(POO_VAR_PREFIX.$key.POO_VAR_SUFFIX, $variables[$key], $this->parsedDocuments);
		}
	}


	// Save parsed document
	function savefile($filename)
	{
		// Has file been extracted ?
		if($this->parserFiles == "")
		{
			$this->handleError("No document loaded. Use loadDocument function first.", E_USER_ERROR);
		}

		// Is dir still there
		if(!is_file($this->parserFiles))
		{
			$this->handleError("File not found: ".$this->parserFiles, E_USER_ERROR);
		}

		// Overwrite parsed documents
		$fp = fopen($filename, "w+");
		fputs($fp, $this->parsedDocuments);
		fclose($fp);
	}

        function prepsave($filename) {
                if($filename == "") {
		            $info = pathinfo($this->parserFiles);
                    $filename = $this->getRandomString(16);
                }
                $this->downloadFile = POO_TMP_PATH."/".$filename;
                $this->savefile($this->downloadFile);
        }

	function download($filename)
	{
		// Build filename and save file temporarly to harddisk
		if($filename == "") $filename = $this->getRandomString(16);
		$info = pathinfo($this->parserFiles);
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
		header('Content-Length: '.filesize($downloadFile));
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
	}


	// Cleans up filesystem after job is done
	function clean()
	{
		if($this->parserFiles == "") return;
		@unlink($this->downloadFile);
		@unlink($this->parserFiles);
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


}

?>
