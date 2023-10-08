<?php

include "../inc/stdLib.php";
if( isset( $_GET['file'] ) && !empty( $_GET['file'] ) ) {
        $file = $_GET['file'];
        //header( 'Content-Type: audio/x-wav' );
        //header( 'Content-length: ' . filesize( '/var/spool/asterisk/monitor/'.$file ) );
        //echo file_get_contents( '/var/spool/asterisk/monitor/'.$file );
        //Versuch die Datei direkt im Browser abzuspielen - funktioniert so leider nicht....
        echo '<audio autoplay="true" style="display:none;"><source src="http://localhost/kivitendo/crm/asterisk/'.$file.'" type="audio/wav"></audio>';
        //echo "<script>alert( 'http://localhost/kivitendo/crm/asterisk/".$file."' );var audio = new Audio( 'http://localhost/kivitendo/crm/asterisk/".$file."' );audio.play();alert('hello2');</script>";


}