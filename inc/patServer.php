<?PHP
/**
*	patServer
*	PHP socket server base class
*	Events that can be handled:
*	  * onStart
*	  * onConnect
*	  * onConnectionRefused
*	  * onClose
*	  * onShutdown
*	  * onReceiveData
*
*	@version	1.1
*	@author		Stephan Schmidt <schst@php-tools.de>
*	@package	patServer
*/
	class	patServer
{
/**
*	information about the project
*	@var	array	$systemVars
*/
	var	$systemVars			=	array(
										"appName"		=>	"patServer",
										"appVersion"	=>	"1.1",
										"author"		=>	array(
																	"Stephan Schmidt <schst@php-tools.de>",
																 )
									);

/**
*	port to listen
*	@var	integer		$port
*/
	var	$port		=	10000;

/**
*	domain to bind to
*	@var	string	$domain
*/
	var	$domain		=	"localhost";

/**
*	maximum amount of clients
*	@var	integer	$maxClients
*/
	var	$maxClients	=	-1;

/**
*	buffer size for socket_read
*	@var	integer	$readBufferSize
*/
	var	$readBufferSize		=	128;
	
/**
*	end character for socket_read
*	@var	integer	$readEndCharacter
*/
	var	$readEndCharacter	=	"\n";
	
/**
*	maximum of backlog in queue
*	@var	integer	$maxQueue
*/
	var	$maxQueue	=	500;

/**
*	debug mode
*	@var	boolean	$debug
*/
	var	$debug		=	true;
	
/**
*	debug mode
*	@var	string	$debugMode
*/
	var	$debugMode	=	"text";

/**
*	debug destination (filename or stdout)
*	@var	string	$debugDest
*/
	var	$debugDest	=	"stdout";

/**
*	empty array, used for socket_select
*	@var	array	$null
*/
	var	$null		=	array();
	
/**
*	all file descriptors are stored here
*	@var	array	$clientFD
*/
	var	$clientFD	=	array();

/**
*	needed to store client information
*	@var	array	$clientInfo
*/
	var	$clientInfo	=	array();

/**
*	needed to store server information
*	@var	array	$serverInfo
*/
	var	$serverInfo	=	array();

/**
*	amount of clients
*	@var	integer		$clients
*/
	var	$clients	=	0;

/**
*	create a new socket server
*
*	@access	public
*	@param	string		$domain		domain to bind to
*	@param	integer		$port		port to listen to
*/
	function	patServer( $domain = "localhost", $port = 10000 )
	{
		$this->domain	=	$domain;
		$this->port		=	$port;

		$this->serverInfo["domain"]	        =	$domain;
		$this->serverInfo["port"]	        =	$port;
		$this->serverInfo["servername"]	    =	$this->systemVars["appName"];
		$this->serverInfo["serverversion"]	=	$this->systemVars["appVersion"];
		
		set_time_limit( 0 );
	}

/**
*	set maximum amount of simultaneous connections
*
*	@access	public
*	@param	int	$maxClients
*/
	function	setMaxClients( $maxClients )
	{
		$this->maxClients	=	$maxClients;
	}

/**
*	set debug mode
*
*	@access	public
*	@param	mixed	$debug	[text|htmlfalse]
*	@param	string	$dest	destination of debug message (stdout to output or filename if log should be written)
*/
	function	setDebugMode( $debug, $dest = "stdout" )
	{
		if( $debug === false )
		{
			$this->debug	=	false;
			return	true;
		}
		
		$this->debug		=	true;
		$this->debugMode	=	$debug;
		$this->debugDest	=	$dest;
	}

/**
*	start the server
*
*	@access	public
*	@param	int	$maxClients
*/
	function	start()
	{
		$this->initFD	=	@socket_create( AF_INET, SOCK_STREAM, 0 );
		if( !$this->initFD )
			die( "patServer: Could not create socket." );

		//	adress may be reused
		socket_setopt( $this->initFD, SOL_SOCKET, SO_REUSEADDR, 1 );

		//	bind the socket
		if( !@socket_bind( $this->initFD, $this->domain, $this->port ) )
		{
			@socket_close( $this->initFD );
			die( "patServer: Could not bind socket to ".$this->domain." on port ".$this->port." ( ".$this->getLastSocketError( $this->initFd )." )." );
		}

		//	listen on selected port
		if( !@socket_listen( $this->initFD, $this->maxQueue ) )
			die( "patServer: Could not listen ( ".$this->getLastSocketError( $this->initFd )." )." );

		$this->sendDebugMessage( "Listening on port ".$this->port.". Server started at ".date( "H:i:s", time() ) );

		//	this allows the shutdown function to check whether the server is already shut down
		$_SESSION["_patServerStatus"]	=	"running";
		//	this ensures that the server will be sutdown correctly
		register_shutdown_function( array( $this, "shutdown" ) );

		if( method_exists( $this, "onStart" ) )
			$this->onStart();

		$this->serverInfo["started"]	=	time();
		$this->serverInfo["status"]		=	"running";
			
		while( true )
		{
			$readFDs	=	array();
			array_push( $readFDs, $this->initFD );

			//	fetch all clients that are awaiting connections
			for( $i = 0; $i < count( $this->clientFD ); $i++ )
				if( isset( $this->clientFD[$i] ) )
					array_push( $readFDs, $this->clientFD[$i] );

			//	block and wait for data or new connection
			$ready	=	@socket_select( $readFDs, $this->null, $this->null, NULL );

			if( $ready === false )
			{
				$this->sendDebugMessage( "socket_select failed." );
				$this->shutdown();
			}
			
			//	check for new connection
			if( in_array( $this->initFD, $readFDs ) )
			{
				$newClient	=	$this->acceptConnection( $this->initFD );

				//	check for maximum amount of connections
				if( $this->maxClients > 0 )
				{
					if( $this->clients > $this->maxClients )
					{
						$this->sendDebugMessage( "Too many connections." );
						
						if( method_exists( $this, "onConnectionRefused" ) )
							$this->onConnectionRefused( $newClient );

						$this->closeConnection( $newClient );
					}
				}

				if( --$ready <= 0 )
					continue;
			}

			//	check all clients for incoming data
			for( $i = 0; $i < count( $this->clientFD ); $i++ )
			{
				if( !isset( $this->clientFD[$i] ) )
					continue;

				if( in_array( $this->clientFD[$i], $readFDs ) )
				{
					$data	=	$this->readFromSocket( $i );
					
					//	empty data => connection was closed
					if( !$data )
					{
						$this->sendDebugMessage( "Connection closed by peer" );
						$this->closeConnection( $i );
					}
					else
					{
						$this->sendDebugMessage( "Received ".trim( $data )." from ".$i );

						if( method_exists( $this, "onReceiveData" ) )
							$this->onReceiveData( $i, $data );
					}
				}
			}
		}
	}

/**
*	read from a socket
*
*	@access	private
*	@param	integer	$clientId	internal id of the client to read from
*	@return	string	$data		data that was read
*/
	function	readFromSocket( $clientId )
	{
		//	start with empty string
		$data		=	"";
	
		//	read data from socket
		while( $buf = socket_read( $this->clientFD[$clientId], $this->readBufferSize ) )
		{
			$data	.=	$buf;

			$endString	=	substr( $buf, - strlen( $this->readEndCharacter ) );
			if( $endString == $this->readEndCharacter )
				break;
			if( $buf == NULL )
				break;
		}

		if( $buf === false )
			$this->sendDebugMessage( "Could not read from client ".$clientId." ( ".$this->getLastSocketError( $this->clientFD[$clientId] )." )." );

		return	$data;
	}
	
/**
*	accept a new connection
*
*	@access	public
*	@param	resource	&$socket	socket that received the new connection
*	@return	int			$clientID	internal ID of the client
*/
	function	acceptConnection( &$socket )
	{
		for( $i = 0 ; $i <= count( $this->clientFD ); $i++ )
		{
			if( !isset( $this->clientFD[$i] ) || $this->clientFD[$i] == NULL )
			{
				$this->clientFD[$i]	=	socket_accept( $socket );
				socket_setopt( $this->clientFD[$i], SOL_SOCKET, SO_REUSEADDR, 1 );
				$peer_host	=	"";
				$peer_port	=	"";
				socket_getpeername( $this->clientFD[$i], $peer_host, $peer_port );
				$this->clientInfo[$i]	=	array(
													"host"		=>	$peer_host,
													"port"		=>	$peer_port,
													"connectOn"	=>	time()
												);
				$this->clients++;

				$this->sendDebugMessage( "New connection ( ".$i." ) from ".$peer_host." on port ".$peer_port );

				if( method_exists( $this, "onConnect" ) )
					$this->onConnect( $i );
				return	$i;
			}
		}
	}

/**
*	check, whether a client is still connected
*
*	@access	public
*	@param	integer	$id	client id
*	@return	boolean	$connected	true if client is connected, false otherwise
*/
	function	isConnected( $id )
	{
		if( !isset( $this->clientFD[$id] ) )
			return	false;
		return	true;	
	}

/**
*	close connection to a client
*
*	@access	public
*	@param	int	$clientID	internal ID of the client
*/
	function	closeConnection( $id )
	{
		if( !isset( $this->clientFD[$id] ) )
			return	false;

		if( method_exists( $this, "onClose" ) )
			$this->onClose( $id );

		$this->sendDebugMessage( "Closed connection ( ".$id." ) from ".$this->clientInfo[$id]["host"]." on port ".$this->clientInfo[$id]["port"] );

		@socket_close( $this->clientFD[$id] );
		$this->clientFD[$id]	=	NULL;
		unset( $this->clientInfo[$id] );
		$this->clients--;
	}

/**
*	shutdown server
*
*	@access	public
*/
	function	shutDown()
	{
		if( $_SESSION["_patServerStatus"] != "running" )
			exit;
		$_SESSION["_patServerStatus"]	=	"stopped";
		
		if( method_exists( $this, "onShutdown" ) )
			$this->onShutdown();

		$maxFD	=	count( $this->clientFD );
		for( $i = 0; $i < $maxFD; $i++ )
			$this->closeConnection( $i );

		@socket_close( $this->initFD );

		$this->sendDebugMessage( "Shutdown server." );
		exit;
	}

/**
*	get current amount of clients
*
*	@access	public
*	@return	int	$clients	amount of clients
*/
	function	getClients()
	{
		return	$this->clients;
	}
	
/**
*	send data to a client
*
*	@access	public
*	@param	int		$clientId	ID of the client
*	@param	string	$data		data to send
*	@param	boolean	$debugData	flag to indicate whether data that is written to socket should also be sent as debug message
*/
	function	sendData( $clientId, $data, $debugData = true )
	{
		if( !isset( $this->clientFD[$clientId] ) || $this->clientFD[$clientId] == NULL )
			return	false;

		if( $debugData )
			$this->sendDebugMessage( "sending: \"" . $data . "\" to: $clientId"  );
			
		if( !@socket_write( $this->clientFD[$clientId], $data ) )
			$this->sendDebugMessage( "Could not write '".$data."' client ".$clientId." ( ".$this->getLastSocketError( $this->clientFD[$clientId] )." )." );
	}

/**
*	send data to all clients
*
*	@access	public
*	@param	string	$data		data to send
*	@param	array	$exclude	client ids to exclude
*/
	function	broadcastData( $data, $exclude = array(), $debugData = true )
	{
		if( !empty( $exclude ) && !is_array( $exclude ) )
			$exclude	=	array( $exclude );

		for( $i = 0; $i < count( $this->clientFD ); $i++ )
		{
			if( isset( $this->clientFD[$i] ) && $this->clientFD[$i] != NULL && !in_array( $i, $exclude ) )
			{
				if( $debugData )
					$this->sendDebugMessage( "sending: \"" . $data . "\" to: $i"  );

				if( !@socket_write( $this->clientFD[$i], $data ) )
					$this->sendDebugMessage( "Could not write '".$data."' client ".$i." ( ".$this->getLastSocketError( $this->clientFD[$i] )." )." );
			}
		}
	}

/**
*	get current information about a client
*
*	@access	public
*	@param	int		$clientId	ID of the client
*	@return	array	$info		information about the client
*/
	function	getClientInfo( $clientId )
	{
		if( !isset( $this->clientFD[$clientId] ) || $this->clientFD[$clientId] == NULL )
			return	false;
		return	$this->clientInfo[$clientId];
	}
	
/**
*	send a debug message
*
*	@access	private
*	@param	string	$msg	message to debug
*/
	function	sendDebugMessage( $msg )
	{
		if( !$this->debug )
			return	false;

		$msg	=	date( "Y-m-d H:i:s", time() ) . " " . $msg;	
			
		switch( $this->debugMode )
		{
			case	"text":
				$msg	=	$msg."\n";
				break;
			case	"html":
				$msg	=	htmlspecialchars( $msg ) . "<br />\n";
				break;
		}

		if( $this->debugDest == "stdout" || empty( $this->debugDest ) )
		{
			echo	$msg;
			flush();
			return	true;
		}
		
		error_log( $msg, 3, $this->debugDest );
		return	true;
	}

/**
*	return	string for last socket error
*
*	@access	public
*	@return	string	$error	last error
*/
	function	getLastSocketError( &$fd )
	{
		$lastError	=	socket_last_error( $fd );
		return	"msg: " . socket_strerror( $lastError ) . " / Code: ".$lastError;
	}
}
?>
