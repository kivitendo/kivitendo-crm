<?php //Liest die Url eines Themes 
if ( $_POST['theme'] == "base" ) {
    echo "base";
    exit();
}
if(file_exists("../jquery-themes/".$_POST['theme']."/jquery-ui.css") ) {
    $themefile = fopen("../jquery-themes/".$_POST['theme']."/jquery-ui.css","r");
} 
else if ( file_exists("../jquery-themes/".$_POST['theme']."/jquery-ui-1.9.2.custom.css") ) {
    $themefile = fopen("../jquery-themes/".$_POST['theme']."/jquery-ui-1.9.2.custom.css","r");
}
else {   
    echo "noThemeFile";
    exit();
} 
if ($themefile) {
    while (($buffer = fgets($themefile, 4096)) !== false) {
        if ( preg_match("/To view and modify this theme, visit (.+)/",$buffer,$hits) ) echo $hits[1];
    }
    fclose($themefile);
}
?>