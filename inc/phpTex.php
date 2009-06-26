<?php

    /**
        xlsTemplate - Simple Class for LxOffice CRM to replace some markers in an Excel Sheet.
        Start and end delimeters are defined as member variables and can be changed.
        Replaces text must have same length as placeholder in excel sheet.

        If the text is too long it will be stripped, if the text is too short it wuill be filled up with
        a special char defined in member var $SPACE;

        (c) 2006 LINET Services -
        @author Timo Springmann t.springmann@linet-services.de

    */
    if (!defined('TMP_PATH')) {
        define('TMP_PATH', './tmp');
    }

    class phpBIN {

        var $content = "";
        var $original = "";
        var $last_error = "";

        var $markers = array();

        var $START_DELIMETER = "<%";
        var $END_DELIMETER = "%>";
        var $SPACE = " ";

	var $loaded = false;
        /*
            Constructor. Expects filename of xls-template which must be readable
        */
    function loadDocument ($filename) {
	    if (!$this->loaded) {
            	if (!file_exists($filename) || !is_readable($filename)) {
                	$this->last_error = "File $filename does not exists or is not readable";
                	return false;
            	}
            	$this->content = file_get_contents ($filename);
		        $this->loaded = true;
            	return true;
	    }
    }

    function savecontent() {
        $this->original = $this->content;
    }

    function getoriginal() {
        $this->content = $this->original;
        $this->last_error = "";
    }

	function parse ($_marker_array) {
		if (!is_array($_marker_array)) return false;
		foreach ($_marker_array as $marker => $text) {
			$this->assign($marker, $text);
		}
        $this->replaceMarkers();
	}

        /*
            Replace Markers
        */
    function assign ($_marker, $_marker_content) {
	    $suche="/".$this->START_DELIMETER.$_marker.$this->END_DELIMETER."/i";
	    preg_match($suche,$this->content,$gefunden);
	    if ($gefunden) {
            $this->markers[$_marker]["original"]=$suche;
            $this->markers[$_marker]['content'] = $_marker_content;
        } else return false;
	}

    function replaceMarkers() {
        $count = 0;
        foreach ($this->markers as $marker => $replace) {
    		$new_content = preg_replace($replace['original'], $replace['content'], $this->content);
            $this->content = $new_content;
            $count++;
        }
        return $count;
    }


	function save($filename) {
		$fp = fopen($filename, "w");
        fputs($fp, $this->content);
		fclose($fp);
	}

	function prepsave($_name) {
		$this->replaceMarkers();
		//$name = $_name.$this->EXT;
		$this->downloadFile = TMP_PATH."/".$_name;
		$this->save($this->downloadFile);	
	}

        /*
            Send file to browser
        */
        function sendFile ($name = "export.xls") {
            header('Content-type: application/msexcel');
	        header('Content-Transfer-Encoding: binary');
            header("Content-Disposition: attachment; filename=$name");
            echo $this->content;
        }

 	function clean() {
	}

	function debug($mode) {
		$returnValue = "";
		switch ($mode) {
			case 1:
				foreach ($this->markers as $marker => $content) {
					$returnValue .= "<b>".htmlentities($marker)."</b><br>";	
					$returnValue .= "<pre>".htmlentities(print_r($content, true))."</pre>";
				}
				break;
		}
		return $returnValue;
	}
 
 }



    /*
        TEST
    */

#    $xls = new xlsTemplate("test.xls");
#    $xls->assign("NAME", "Timo");
#    $xls->replaceMarkers();
#    $xls->sendFile();

?>
