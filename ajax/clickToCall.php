<?php
    require_once __DIR__.'/../inc/ajax2function.php';

    function newCall( $data ){
        //$extension = $_REQUEST['internalnum'];
        //$dialphonenumber = $_REQUEST['outboundnum'];
        writeLog( $data );
        $timeout = 10;
        $asterisk_ip = "127.0.0.1";
        $socket = fsockopen( $asterisk_ip,"5038", $errno, $errstr, $timeout );
        fputs( $socket, "Action: Login\r\n" );
        fputs( $socket, "UserName: clickadmin\r\n" );
        fputs( $socket, "Secret: mypasswd\r\n\r\n" );

        $wrets=fgets( $socket, 128 );

        writeLog( $wrets );
        //echo $wrets;

        fputs( $socket, "Action: Originate\r\n" );
        fputs( $socket, "Channel: SIP/".$data['context']."\r\n" );
        fputs( $socket, "Exten: ".$data['number']."\r\n" );
        fputs( $socket, "Context: werkstatt\r\n" ); // very important to change to your outbound context
        fputs( $socket, "Priority: 1\r\n" );
        fputs( $socket, "Async: yes\r\n\r\n" );

        $wrets = fgets( $socket, 128 );

        writeLog( $wrets );
        echo $wrets;
        echo 1;
    }    

?>