<?php
    require_once __DIR__.'/../inc/ajax2function.php';

    function newCall($data) {
        define("DEBUG", TRUE);
        if(DEBUG) writeLog($data);
        
        // Format phone number to replace '+' with '00'
        $phoneNumber = str_replace('+', '00', $data['number']);
        $myLogin = $_SESSION['userConfig']['login'];
        $port = 5038;
        
        // Asterisk manager.conf details
        $username = 'clickToCall';
        $result = $GLOBALS['dbh']->getKeyValueData('crmdefaults', array('ip_asterisk', 'asterisk_passwd', 'external_contexts', 'internal_phones', 'user_external_context', 'user_internal_phone','crmti_mobile_number' ), 'employee = -1 OR employee = '.$_SESSION['userConfig']['id'], FALSE);
        $passwd = $result['asterisk_passwd'];
        $ip = $result['ip_asterisk'];
        $crmti_mobile_number = $result['crmti_mobile_number'];
    
        
        // Fetch external and internal context
        $external_context = isset($result['user_external_context']) ? $result['user_external_context'] : substr($result['external_contexts'], 0, strpos($result['external_contexts'], ','));
        $internal_phone = isset($result['user_internal_phone']) ? $result['user_internal_phone'] : substr($result['internal_phones'], 0, strpos($result['internal_phones'], ','));

        // Define custom Caller ID for Click-to-Call to avoid confusion with Autoprofis
        $custom_callerid = "Click2Call <004988888888>";  // Set the custom number or name here

        // Check if internal_phone contains "Handy" or "Mobile"
        if (stripos($internal_phone, 'Handy') !== false || stripos($internal_phone, 'Mobile') !== false) {
            // Write debug log for "Handy" or "Mobile"
            if(DEBUG) writeLog("DEBUG: Internal context identified as 'Handy' or 'Mobile'. Using specific originate command with custom Caller ID.");

            // Use the correct originate command for mobile phones with custom Caller ID
            $originateRequest  = "Action: Originate\r\n";
            $originateRequest .= "Channel: Local/".$crmti_mobile_number."@from-external\r\n";  // Your mobile phone number here
            $originateRequest .= "Exten: ".$phoneNumber."\r\n";  // The number of the person to be called
            $originateRequest .= "Context: autoprofis1\r\n";     // The correct context (e.g., autoprofis1)
            $originateRequest .= "Callerid: ".$custom_callerid."\r\n";  // Custom Caller ID for Click-to-Call
            $originateRequest .= "Priority: 1\r\n";
            $originateRequest .= "Async: true\r\n\r\n";

        } else {
            // Use the standard SIP originate command for other phones (internal extensions)
            $originateRequest  = "Action: Originate\r\n";
            $originateRequest .= "Channel: SIP/".$internal_phone."@".$internal_phone."\r\n";
            $originateRequest .= "Callerid: # ".$data['name']."\r\n"; // Show (Customer|Vendor) Name
            $originateRequest .= "Exten: ".$phoneNumber."\r\n";  // External destination number
            $originateRequest .= "Context: ".$external_context."\r\n";  // External context for SIP phones
            $originateRequest .= "Priority: 1\r\n";
            $originateRequest .= "Async: true\r\n\r\n";
        }

        // Establish socket connection and handle the call
        $socket = stream_socket_client("tcp://$ip:$port");
        if ($socket) {
            if(DEBUG) writeLog("Connected to socket, sending authentication request.");

            // Prepare authentication request
            $authenticationRequest  = "Action: Login\r\n";
            $authenticationRequest .= "Username: $username\r\n";
            $authenticationRequest .= "Secret: $passwd\r\n";
            $authenticationRequest .= "Events: off\r\n\r\n";

            // Send authentication request
            $authenticate = stream_socket_sendto($socket, $authenticationRequest);
            if($authenticate > 0) {
                if(DEBUG) writeLog("Authenticate: ".$authenticate);

                // Wait for server response
                usleep(200000);
                
                // Read server response
                $authenticateResponse = fread($socket, 4096);
                if(DEBUG) writeLog("authenticateResponse: ".$authenticateResponse);

                // Check if authentication was successful
                if (strpos($authenticateResponse, 'Success') !== false) {
                    if(DEBUG) writeLog("Authenticated to Asterisk Manager Interface. Initiating call.");

                    // Send originate request
                    $originate = stream_socket_sendto($socket, $originateRequest);
                    if(DEBUG) writeLog("Originate-Request sent: ".$originateRequest);

                    if ($originate > 0) {
                        // Wait for server response
                        usleep(200000);
                        
                        // Read server response
                        $originateResponse = fread($socket, 4096);
                        if(DEBUG) writeLog("Answer originateResponse: ".$originateResponse);

                        // Check if originate was successful
                        if (strpos($originateResponse, 'Success') !== false) {
                            if(DEBUG) writeLog("Call initiated, dialing.");
                        } else {
                            if(DEBUG) writeLog("Could not initiate call.");
                        }
                    } else {
                        if(DEBUG) writeLog("Could not write call initiation request to socket.");
                    }
                } else {
                    if(DEBUG) writeLog("Could not authenticate to Asterisk Manager Interface.");
                }
            } else {
                if(DEBUG) writeLog("Could not write authentication request to socket.");
            }
        } else {
            if(DEBUG) writeLog("Unable to connect to socket.");
        }

        echo 0;
    }

    // Get phones and user default phones
    function getPhones() {
        echo $GLOBALS['dbh']->getKeyValueData('crmdefaults', array('external_contexts', 'internal_phones', 'user_external_context', 'user_internal_phone'), 'employee = -1 OR employee = '.$_SESSION['userConfig']['id']);
    }

    // Save user default phones
    function saveClickToCall($data) {
        $GLOBALS['dbh']->myquery("DELETE FROM crmdefaults WHERE employee = ".$_SESSION['userConfig']['id']." AND key = '".key($data)."'");
        echo $GLOBALS['dbh']->insert('crmdefaults', array('key', 'val', 'employee'), array(key($data), $data[key($data)], $_SESSION['userConfig']['id']), FALSE);
    }

?>
