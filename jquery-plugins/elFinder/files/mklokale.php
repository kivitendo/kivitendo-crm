<?php

    if ($argc>0) {
        echo $argv[1]."\n";
        $templates = glob($argv[1]);
    } else {
        echo "tpl/*.tpl\n";
        $templates = glob("tpl/*.tpl");
    }
    if ($templates) foreach ($templates as $file) {
        echo "$file: ";
        $text = file_get_contents($file);
        preg_match_all("/\.:[a-z0-0 _]+:\./i",$text,$matches); 
        if ($matches[0]) {
            $f=fopen("inc/locale/".basename($file,".tpl"),"w");
            fputs($f,"<?php\n");
            fputs($f,"\$texts  = array(\n");
            foreach ($matches[0] as $word) {
                fputs($f,"  '$word'\t => '".substr($word,2,-2)."',\n");
            };
            fputs($f,");\n");
            fputs($f,"?>");
            fclose($f);
            echo count($matches[0]);
        }
        echo "\n";
    } 

?>
