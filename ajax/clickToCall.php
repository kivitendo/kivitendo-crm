<?php
    require_once __DIR__.'/../inc/ajax2function.php';

    function newCall( $data ){
        $debug = TRUE;
        if( $debug ) writeLog( $data );
        /*

        $timeout = 10;
        $asterisk_ip = "127.0.0.1";
        $socket = fsockopen( $asterisk_ip,"5038", $errno, $errstr, $timeout );
        writeLog( $socket );
        fputs( $socket, "Action: Login\r\n" );
        fputs( $socket, "UserName: clickadmin\r\n" );
        fputs( $socket, "Secret: mypasswd\r\n\r\n" );

        $wrets=fgets( $socket, 128 );
        //writeLog( wrets );
        writeLog( "wrets 0: ".$wrets );
        //echo $wrets;

        fputs( $socket, "Action: Originate\r\n" );
        fputs( $socket, "Channel: SIP/".$data['contex']."\r\n" );
        fputs( $socket, "Exten: ".$data['number']."\r\n" );
        fputs( $socket, "Context: werkstatt\r\n" ); // very important to change to your outbound context
        fputs( $socket, "Priority: 1\r\n" );
        fputs( $socket, "Async: yes\r\n\r\n" );

        $wrets = fgets( $socket, 128 );

        writeLog( "wrets 1: ".$wrets );;
        writeLog( socket_last_error() );
        */

        $port = 5038;
        $username = 'clickadmin';
        $password = 'mypasswd';
        $internalPhoneline = $data['internal_contex'];
        // Context for outbound calls. See /etc/asterisk/extensions.ael if unsure.
        $context = $data['external_contex'];
        $socket = stream_socket_client("tcp://127.0.0.1:$port");
        if( $socket ){
            if( $debug ) writeLog( "Connected to socket, sending authentication request" );
            // Prepare authentication request
            $authenticationRequest = "Action: Login\r\n";
            $authenticationRequest .= "Username: $username\r\n";
            $authenticationRequest .= "Secret: $password\r\n";
            $authenticationRequest .= "Events: off\r\n\r\n";
            // Send authentication request
            $authenticate = stream_socket_sendto($socket, $authenticationRequest);
            if( $authenticate > 0 ){
                // Wait for server response
                usleep(200000);
                // Read server response
                $authenticateResponse = fread( $socket, 4096 );
                // Check if authentication was successful
                if( strpos( $authenticateResponse, 'Success' ) !== false ){
                    if( $debug ) writeLog( "Authenticated to Asterisk Manager Inteface. Initiating call." );
                    // Prepare originate request
                    $originateRequest = "Action: Originate\r\n";
                    $originateRequest .= "Channel: SIP/$internalPhoneline\r\n";
                    $originateRequest .= "Callerid: Click 2 Call\r\n";
                    $originateRequest .= "Exten: $target\r\n";
                    $originateRequest .= "Context: $context\r\n";
                    $originateRequest .= "Priority: 1\r\n";
                    $originateRequest .= "Async: yes\r\n\r\n";
                    // Send originate request
                    $originate = stream_socket_sendto( $socket, $originateRequest );
                    if( $originate > 0 ){
                        // Wait for server response
                        usleep(200000);
                        // Read server response
                        $originateResponse = fread( $socket, 4096 );
                        // Check if originate was successful
                        if( strpos( $originateResponse, 'Success' ) !== false ){
                            if( $debug ) writeLog( "Call initiated, dialing." );
                        }
                        else{
                            if( $debug ) writeLog(  "Could not initiate call." );
                        }
                    }
                    else{
                        if( $debug ) writeLog( "Could not write call initiation request to socket." );
                    }
                }
                else{
                    if( $debug ) writeLog( "Could not authenticate to Asterisk Manager Interface." );
                }
            }
            else{
                if( $debug ) writeLog(  "Could not write authentication request to socket." );
            }
        }
        else{
            if( $debug ) writeLog( "Unable to connect to socket." );
        }

        echo 0;
    }

?>