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


	function parse ($_marker_array) {
		if (!is_array($_marker_array)) return false;
		foreach ($_marker_array as $marker => $text) {
			$this->assign($marker, $text);
		}
	}

        /*
            finds a single marker and saves start- and endpositions in member variable
        */

        function findMarker ($_marker) {
            $marker = $this->START_DELIMETER.$_marker;
            // startposition of marker
            $start_pos =  strpos ($this->content, $marker);
            // endposition of marker
            $end_pos = strpos($this->content, $this->END_DELIMETER, $start_pos);
            $length = $end_pos - $start_pos + strlen($this->END_DELIMETER);

            if ($start_pos && $end_pos) {

                $this->markers[$_marker] = array(
                        "start" => $start_pos,
                        "end" => $end_pos,
                        "length" => $length,
                        "original" => substr($this->content, $start_pos, $length)
                );
                return true;
            } else return false;
        }


        /*
            Replace Markers
        */
        function assign ($_marker, $_marker_content) {
            if ($this->findMarker($_marker)) {
                $content_length = strlen($_marker_content);

                switch ($diff = $content_length - $this->markers[$_marker]['length']) {
                    case ($diff < 0):
                        // Marker > Content
                        for ($i=0; $i < abs($diff); $i++) $empty .= $this->SPACE;
                        $content = $_marker_content.$empty;
                        break;
                    case ($diff > 0):
                        // Content > Marker
                        $content = substr($_marker_content, 0, $this->markers[$_marker]['length']);
                        break;
                    default:
                        $content = $_marker_content;
                        break;
                }

                if (strlen($content) != $this->markers[$_marker]['length'])
                    die("assign: Markerlength differs from marker content.");

                $this->markers[$_marker]['content'] = $content;

            } else return false;
        }

        function replaceMarkers() {
            $count = 0;
            foreach ($this->markers as $marker => $replace) {
                if (strlen($replace['original']) != $replace['length']) {
                    die("replaceMarkers: Markerlength differs from marker content.");
                }

                $new_content = str_replace($replace['original'], $replace['content'], $this->content);
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
#    $xls->assign("ADRESSE1", "Dieses ist eine lange Adresse");
#    $xls->replaceMarkers();
#    $xls->sendFile();

?>
