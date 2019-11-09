<?php
    require_once __DIR__.'/../inc/ajax2function.php';

    function newCall( $data ){
        define( "DEBUG", FALSE );
        if( DEBUG ) writeLog( $data );
        $myLogin = $_SESSION['userConfig']['login'];
        $port = 5038;
        // less /etc/asterisk/manager.conf
        $username = 'clickToCall';
        $result = $GLOBALS['dbh']->getKeyValueData( 'crmdefaults', array( 'ip_asterisk', 'asterisk_passwd', 'external_contexts', 'internal_phones', 'user_external_context', 'user_internal_phone' ), 'employee = -1 OR employee = '.$_SESSION['userConfig']['id'], FALSE );
        writeLog( $result );
        $passwd = $result['asterisk_passwd'];
        $ip = $result['ip_asterisk'];
        // if user_external_context not set, use string external_contexts to first comma
        $external_context = isset( $result['user_external_context']) ? $result['user_external_context'] : substr( $result['external_contexts'], 0, strpos( $result['external_contexts'] , ',' ) );
        $internal_phone = isset( $result['user_internal_phone']) ? $result['user_internal_phone'] : substr( $result['internal_phones'], 0, strpos( $result['internal_phones'] , ',' ) );
        //$sql = "SELECT greeting || ' ' || name AS name FROM customer WHERE id = $data['cust_vend_id']"; //ToDo: distinguish between customers and vendors
        //$customer = $GLOBALS['dbh']->getOne( $sql );

        // Context for outbound calls. See /etc/asterisk/extensions.ael
        $socket = stream_socket_client( "tcp://$ip:$port" );
        if( $socket ){
            if( DEBUG ) writeLog( "Connected to socket, sending authentication request." );
            // Prepare authentication request
            $authenticationRequest  = "Action: Login\r\n";
            $authenticationRequest .= "Username: $username\r\n";
            $authenticationRequest .= "Secret: $passwd\r\n";
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
                    $originateRequest .= "Channel: SIP/".$internal_phone."@".$internal_phone."\r\n";//ToDo
                    $originateRequest .= "Callerid: ".$data['name']."\r\n"; //Show (Customer|Vendor) Name
                    $originateRequest .= "Exten: ".$data['number']."\r\n";
                    $originateRequest .= "Context: ".$external_context."\r\n";
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

    // get phones and user default phones
    function getPhones(){
        echo $GLOBALS['dbh']->getKeyValueData( 'crmdefaults', array( 'external_contexts', 'internal_phones', 'user_external_context', 'user_internal_phone'), 'employee = -1 OR employee = '.$_SESSION['userConfig']['id'] );
    }

    // save user default phones
    function saveClickToCall( $data ){
        $GLOBALS['dbh']->query( "DELETE FROM crmdefaults WHERE employee = ".$_SESSION['userConfig']['id']." AND key = '".key( $data )."'" );
        echo $GLOBALS['dbh']->insert( 'crmdefaults', array( 'key', 'val', 'employee' ), array( key( $data) , $data[key( $data )], $_SESSION['userConfig']['id'] ), FALSE );
    }

?>