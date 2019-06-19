<?php
    require_once __DIR__.'/../inc/ajax2function.php';

    function newCall( $data ){
        $debug = TRUE;
        if( $debug ) writeLog( $data );
        $port = 5038;
        $username = 'clickToCall';
        $password = 'mypasswd';
	$target = $data['number'];
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
	    	writeLog( "Authenticate: ".$authenticate );
                // Wait for server response
                usleep(200000);
                // Read server response
                $authenticateResponse = fread( $socket, 4096 );
		writeLog( "authenticateResponse: ".$authenticateResponse );
                // Check if authentication was successful
                if( strpos( $authenticateResponse, 'Success' ) !== false ){
                    if( $debug ) writeLog( "Authenticated to Asterisk Manager Inteface. Initiating call." );
                    // Prepare originate request
                    $originateRequest  = "Action: Originate\r\n";
                    $originateRequest .= "Channel: SIP/werkstatt_fon@werkstatt_fon\r\n";//ToDo
                    $originateRequest .= "Callerid: $internalPhoneline\r\n";
                    $originateRequest .= "Exten: ".$data['number']."\r\n";
                    $originateRequest .= "Context: werkstatt\r\n";
		    //$originateRequest .= "Timeout: 30\r\n";
                    $originateRequest .= "Priority: 1\r\n";
                    $originateRequest .= "Async: true\r\n\r\n";
		    if( $debug ) writeLog( "Originate-Request: \n".$originateRequest );
                    // Send originate request
                    $originate = stream_socket_sendto( $socket, $originateRequest );
		    writeLog( "Return stream_socket_sendto: ".$originate );
                    if( $originate > 0 ){
                        // Wait for server response
                        usleep(200000);
                        // Read server response
                        $originateResponse = fread( $socket, 4096 );
			writeLog( "Answer originateResponse: ".$originateResponse );
                        // Check if originate was successful
                        if( strpos( $originateResponse, 'Success' ) !== false ){
                            if( $debug ) writeLog( "Call initiated, dialing." );
                        }
                        else{
                            if( $debug ) writeLog(  "Could not initiate call.".$originateResponse );
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