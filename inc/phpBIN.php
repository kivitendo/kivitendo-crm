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
        var $last_error = "";

        var $markers = array();
        var $VAR_MINLEN = 2;
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

    function getTags() {
        preg_match_all('/'.$this->START_DELIMETER.'([A-Z_0-9]{'.$this->VAR_MINLEN.',})'.$this->END_DELIMETER.'/i',$this->content,$hits);
        return $hits[1];        
    }

	function parse ($_marker_array) {
		if (!is_array($_marker_array)) return false;
		foreach ($_marker_array as $marker => $text) {
			$this->assign($marker, $text);
		}
	}

        /*
            Replace Markers
        */
        function assign ($_marker, $_marker_content) {
	    //wieso das??:
	    //$suche="/".$this->START_DELIMETER.$_marker."[ ]?".$this->END_DELIMETER."/i";
	    $suche="/".$this->START_DELIMETER.$_marker.$this->END_DELIMETER."/i";
	    preg_match($suche,$this->content,$gefunden);
	    if ($gefunden) {
		$length=strlen($gefunden[0]);
		$this->markers[$_marker]["length"]=$length;
		$this->markers[$_marker]["original"]=$suche;
		if (strlen($_marker_content)>$length) {
		        $content=substr($_marker_content,0,$length);
		} else if (strlen($_marker_content)<$length) {
			$content=$_marker_content;
		        while (strlen($content) < $length) {
	                	$content.=" ";
			}
	        } else {
			$content=$_marker_content;
		}
		$this->markers[$_marker]['content'] = $content;
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
