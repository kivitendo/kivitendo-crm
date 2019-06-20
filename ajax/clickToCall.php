<?php
    require_once __DIR__.'/../inc/ajax2function.php';

    function newCall( $data ){

        /*Provisorium!!!!!!*/
        $myLogin = $_SESSION['userConfig']['login'];
        if(  $myLogin == 'kappe' || $myLogin == 'puddel' ){
            $data['internal_contex'] = 'werkstatt_fon';
            $data['external_contex'] = 'werkstatt';
        }
        if(  $myLogin == 'katrin' ){
            $data['internal_contex'] = 'autoprofis1_fon';
            $data['external_contex'] = 'autoprofis1';
        }
        if(  $myLogin == 'bea' ){
            $data['internal_contex'] = 'autoprofis2_fon';
            $data['external_contex'] = 'autoprofis2';
        }
        if(  $myLogin == 'ronny' ){
            $data['internal_contex'] = 'inter-data_fon';
            $data['external_contex'] = 'inter-data';
        }
        if(  $myLogin == 'thomas' || $myLogin == 'Thomas'){
            $data['internal_contex'] = 'flexrohr24_fon';
            $data['external_contex'] = 'flexrohr24';
        }
         /* END Provisorium!!!!!!*/

        $debug = FALSE;
        if( $debug ) writeLog( $data );
        $port = 5038;
        $username = 'clickToCall';
        $password = 'mypasswd';
        // Context for outbound calls. See /etc/asterisk/extensions.ael if unsure.
        $context = $data['external_contex'];
        $socket = stream_socket_client( "tcp://127.0.0.1:$port" );
        if( $socket ){
            if( $debug ) writeLog( "Connected to socket, sending authentication request." );
            // Prepare authentication request
            $authenticationRequest  = "Action: Login\r\n";
            $authenticationRequest .= "Username: $username\r\n";
            $authenticationRequest .= "Secret: $password\r\n";
            $authenticationRequest .= "Events: off\r\n\r\n";
            // Send authentication request
            $authenticate = stream_socket_sendto( $socket, $authenticationRequest );
            if( $authenticate > 0 ){
                if( $debug ) writeLog( "Authenticate: ".$authenticate );
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
                    $originateRequest .= "Channel: SIP/".$data['internal_contex']."@".$data['internal_contex']."\r\n";//ToDo
                    $originateRequest .= "Callerid: Click to call\r\n";
                    $originateRequest .= "Exten: ".$data['number']."\r\n";
                    $originateRequest .= "Context: ".$data['external_contex']."\r\n";
                    $originateRequest .= "Priority: 1\r\n";
                    $originateRequest .= "Async: true\r\n\r\n";
                    if( $debug ) writeLog( "Originate-Request: \n".$originateRequest );
                    // Send originate request
                    $originate = stream_socket_sendto( $socket, $originateRequest );
                    if( $debug ) writeLog( "Return stream_socket_sendto: ".$originate );
                    if( $originate > 0 ){
                        // Wait for server response
                        usleep(200000);
                        // Read server response
                        $originateResponse = fread( $socket, 4096 );
                        if( $debug ) writeLog( "Answer originateResponse: ".$originateResponse );
                        // Check if originate was successful
                        if( strpos( $originateResponse, 'Success' ) !== false ){
                            if( $debug ) writeLog( "Call initiated, dialing." );
                        }
                        else{
                            if( $debug ) writeLog(  "Could not initiate call." );
                        } //$originateResponse
                    }
                    else{
                        if( $debug ) writeLog( "Could not write call initiation request to socket." );
                    } //$originate
                }
                else{
                    if( $debug ) writeLog( "Could not authenticate to Asterisk Manager Interface." );
                } //$authenticateResponse
            }
            else{
                if( $debug ) writeLog(  "Could not write authentication request to socket." );
            } //$authenticate
        }
        else{
            if( $debug ) writeLog( "Unable to connect to socket." );
        } //$socket

        echo 0;
    }

?>