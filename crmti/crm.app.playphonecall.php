<?php

if( isset( $_GET['file'] ) && !empty( $_GET['file'] ) ) {
        $file = $_GET['file'];
        header('Content-Type: audio/x-wav');
        header( 'Content-length: ' . filesize( '/var/spool/asterisk/monitor/'.$file ) );
        echo file_get_contents( '/var/spool/asterisk/monitor/'.$file );
}