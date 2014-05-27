<?php
class logging {

    var $lf = false;

    public function logging() {
        $this->lf = fopen('/tmp/android.log','a');
        fputs($this->lf,'Start debug: '.date("Y-m-d H:i:s")."\n");
    }

    public function write($txt) {
        fputs($this->lf,date("Y-m-d H:i:s ->")."\n");
        fputs($this->lf,$txt."\n");
    }
    
    public function close() {
        fputs($this->lf,'Stop debug: '.date("Y-m-d H:i:s")."\n");
        fclose($this->lf);

    }
}
?>
