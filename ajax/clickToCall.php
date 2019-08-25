<?php
    require_once __DIR__.'/../inc/ajax2function.php';

    function newCall( $data ){
        define( "DEBUG", FALSE );
        if( DEBUG ) writeLog( $data );
        $myLogin = $_SESSION['userConfig']['login'];
        $port = 5038;
        // less /etc/asterisk/manager.conf
        $username = 'clickToCall';
        $sql = "SELECT key, val FROM crmdefaults WHERE ( employee = -1  OR employee = ".$_SESSION['userConfig']['id']." ) AND key = 'asterisk_passwd' OR key = 'ip_asterisk' OR key = 'user_external_context' OR key ='user_internal_phone' OR key = 'external_contexts' OR key = 'internal_phones'";
        //writeLog( $sql );
        $result = $GLOBALS['dbh']->getAll( $sql );
        $resultArray = array();
        foreach( $result as $value ) $resultArray[$value['key']] = $value['val'];

        //writeLog( $resultArray );
        $passwd = $resultArray['asterisk_passwd'];
        $ip = $resultArray['ip_asterisk'];
        // if user_external_context not set, use string external_contexts to first comma
        $external_context = isset( $resultArray['user_external_context']) ? $resultArray['user_external_context'] : substr( $resultArray['external_contexts'], 0, strpos( $resultArray['external_contexts'] , ',' ) );
        $internal_phone = isset( $resultArray['user_internal_phone']) ? $resultArray['user_internal_phone'] : substr( $resultArray['internal_phones'], 0, strpos( $resultArray['internal_phones'] , ',' ) );
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
                    $originateRequest .= "Callerid: ".$_SESSION['crmUserData']['name']."\r\n"; //Show kivitendo uSer NAME
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
        $sql = "SELECT val FROM crmdefaults WHERE ( employee = -1 OR employee = ".$_SESSION['userConfig']['id']." ) AND key = 'external_contexts' OR key = 'internal_phones' OR key = 'user_external_context' OR key = 'user_internal_phone' ORDER BY key";
        echo $GLOBALS['dbh']->getALL( $sql, TRUE );
    }

    // save user default phones
    function saveClickToCall( $data ){
        //writeLog( $data );
        //writeLog($_SESSION['crmUserData']['loginCRM']);
        $GLOBALS['dbh']->query( "DELETE FROM crmdefaults WHERE employee = ".$_SESSION['userConfig']['id']." AND key = '".key( $data )."'" );
        echo $GLOBALS['dbh']->insert( 'crmdefaults', array( 'key', 'val', 'employee' ), array( key( $data) , $data[key( $data )], $_SESSION['userConfig']['id'] ), FALSE );
    }

?>