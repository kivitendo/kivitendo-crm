<?php
    require_once($_SESSION['crmpath']."inc/stdLib.php");
    if($_GET['showdl']){
		echo "dokumente/".$_SESSION["dbData"]["dbname"];
		return;
    }
    else {
		$pfad = 'dokumente/'.$_SESSION["dbData"]["dbname"].$_GET['file'];
        if (isset($pfad)) {
            $fullPath = $pfad;
            if($fullPath) {
                $fsize = filesize($fullPath);
                $path_parts = pathinfo($fullPath);
                $ext = strtolower($path_parts["extension"]);
                switch ($ext) {
                    case "pdf":
                    header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
                    header("Content-type: application/pdf");
                    break;
					case "png":
                    header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
                    header("Content-type: image/png");
                    break;
                    case "jpg":
                    header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
                    header("Content-type: image/jpeg");
                    break;
                    default;
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
                }
                if($fsize) {
                  header("Content-length: $fsize");
                }
                ob_clean();
                readfile($fullPath);
                exit;
            }
        }
    }
?>
