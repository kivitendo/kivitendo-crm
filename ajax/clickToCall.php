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

        define( "DEBUG", FALSE );
        if( DEBUG ) writeLog( $data );
        $port = 5038;
        // less /etc/asterisk/manager.conf
        $username = 'clickToCall';
        $sql = "SELECT val FROM crmdefaults WHERE employee = -1 AND key = 'asterisk_passwd'";
        $password = $GLOBALS['dbh']->getOne( $sql )['val'];
        // Context for outbound calls. See /etc/asterisk/extensions.ael if unsure.
        $context = $data['external_contex'];
        $socket = stream_socket_client( "tcp://127.0.0.1:$port" );
        if( $socket ){
            if( DEBUG ) writeLog( "Connected to socket, sending authentication request." );
            // Prepare authentication request
            $authenticationRequest  = "Action: Login\r\n";
            $authenticationRequest .= "Username: $username\r\n";
            $authenticationRequest .= "Secret: $password\r\n";
            $authenticationRequest .= "Events: off\r\n\r\n";
            // Send authentication request
            $authenticate = stream_socket_sendto( $socket, $authenticationRequest );
            if( $authenticate > 0 ){
                if( DEBUG ) writeLog( "Authenticate: ".$authenticate );
                // Wait for server response
                usleep(200000);
                // Read server response
                $authenticateResponse = fread( $socket, 4096 );
                if( DEBUG ) writeLog( "authenticateResponse: ".$authenticateResponse );
                // Check if authentication was successful
                if( strpos( $authenticateResponse, 'Success' ) !== false ){
                    if( DEBUG ) writeLog( "Authenticated to Asterisk Manager Inteface. Initiating call." );
                    // Prepare originate request
                    $originateRequest  = "Action: Originate\r\n";
                    $originateRequest .= "Channel: SIP/".$data['internal_contex']."@".$data['internal_contex']."\r\n";//ToDo
                    $originateRequest .= "Callerid: Click to call\r\n";
                    $originateRequest .= "Exten: ".$data['number']."\r\n";
                    $originateRequest .= "Context: ".$data['external_contex']."\r\n";
                    $originateRequest .= "Priority: 1\r\n";
                    $originateRequest .= "Async: true\r\n\r\n";
                    if( DEBUG ) writeLog( "Originate-Request: \n".$originateRequest );
                    // Send originate request
                    $originate = stream_socket_sendto( $socket, $originateRequest );
                    if( DEBUG ) writeLog( "Return stream_socket_sendto: ".$originate );
                    if( $originate > 0 ){
                        // Wait for server response
                        usleep(200000);
                        // Read server response
                        $originateResponse = fread( $socket, 4096 );
                        if( DEBUG ) writeLog( "Answer originateResponse: ".$originateResponse );
                        // Check if originate was successful
                        if( strpos( $originateResponse, 'Success' ) !== false ){
                            if( DEBUG ) writeLog( "Call initiated, dialing." );
                        }
                        else{
                            if( DEBUG ) writeLog(  "Could not initiate call." );
                        } //$originateResponse
                    }
                    else{
                        if( DEBUG ) writeLog( "Could not write call initiation request to socket." );
                    } //$originate
                }
                else{
                    if( DEBUG ) writeLog( "Could not authenticate to Asterisk Manager Interface." );
                } //$authenticateResponse
            }
            else{
                if( DEBUG ) writeLog(  "Could not write authentication request to socket." );
            } //$authenticate
        }
        else{
            if( DEBUG ) writeLog( "Unable to connect to socket." );
        } //$socket

        echo 0;
    }

    function getPhones(){

    }

?>