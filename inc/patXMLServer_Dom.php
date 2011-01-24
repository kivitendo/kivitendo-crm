<?PHP
/**
*	patXMLServer_Dom
*	PHP socket xml server base class
*	Events that can be handled:
*	  * onStart
*	  * onConnect
*	  * onConnectionRefused
*	  * onClose
*	  * onShutdown
*	  * onReceiveRequest
*
*	Methods used to send responses:
*	  * sendResponse
*	  * broadcastResponse
*
*	@version	0.2
*	@author		Stephan Schmidt <schst@php-tools.net>
*   @author     Gerd Schaufelberger <gerd@php-tools.net>
*	@package	patServer
*/
	class	patXMLServer_Dom extends patServer
{
/**
*	Flash terminates xml messages with a nullbyte
*	@var	integer	$readEndCharacter
*/
	var	$readEndCharacter	=	"\0";

/**
*	allow introspection, disable this before running in a production environment
*	@var	boolean
*/
	var	$allowIntrospection =   true;

/**
*	xml special chars
*	@var	array	$xmlSpecialChars
*/
	var	$specialChars	=	array(
									'&'  => '&amp;',
									'<'  => '&lt;',
									'>'  => '&gt;',
									'"'  => '&quot;',
									'\'' => '&apos'
								);

/**
*	server received data
*	decodes the request
*
*	@access	private
*	@param	integer	$clientId	id of the client that sent the data
*	@param	string	$xml		xml data
*/
	function	onReceiveData( $clientId, $xml )
	{
		//	create dom tree
		$xmldoc		=	@xmldoc( trim( $xml ) );
		
		if( !is_object($xmldoc) )
		{
			$this->sendDebugMessage( "Invalid XML received from $clientId" );
			return	false;
		}
		
		//	get root element (type of request)
		$root			=	$xmldoc->root();
		$requestType	=	$root->node_name();
		
		//	extract request parameters
		$requestParams		=	array();
		foreach( $root->children() as $child )
		{
			if( $child->node_type() != XML_ELEMENT_NODE )
				continue;
	
			$content	=	"";
			foreach( $child->children() as $tmp )
			{
				if( $tmp->node_type() != XML_TEXT_NODE && $tmp->node_type() != XML_CDATA_SECTION_NODE )
					continue;
				$content	.=	$tmp->node_value();
			}
			$requestParams[$child->node_name()]	=	$content;
		}
		
        // if introspection is allowed, check for 
        // introspection method
        if( $this->allowIntrospection )
        {
    		// check for requestType pat_*
    		$regs	=	array();
    		if( preg_match( '/^pat_(.+)$/', $requestType, $regs ) )
    		{
    			$method	=	"_internal_".$regs[1];
                $this->sendDebugMessage( "trying to call introspection method $method." );
    			if( method_exists( $this, $method ) )
    			{
                    $this->sendDebugMessage( "calling introspection method $method." );
    				$this->$method( $clientId, $requestParams );
    			}
                return  true;
    		}
        }

		if( method_exists( $this, "onReceiveRequest" ) )
		{
			$this->onReceiveRequest( $clientId, $requestType, $requestParams );
		}
	}

/**
*	send a response
*
*	@access	public
*	@param	integer	$clientId	id of the client to that the response should be sent
*	@param	string	$responseType	type of response
*	@param	array	$responseParams	all params
*	@return	boolean	$success
*/
	function	sendResponse( $clientId, $responseType, $responseParams )
	{
		$xml	=	$this->encodeResponse( $responseType, $responseParams );
		$this->sendData( $clientId, $xml );
	}

/**
*	send response to all clients
*
*	@access	public
*	@param	string	$data		data to send
*	@param	array	$exclude	client ids to exclude
*/
	function	broadcastResponse( $responseType, $responseParams, $exclude = array() )
	{
		$xml	=	$this->encodeResponse( $responseType, $responseParams );
		$this->broadcastData( $xml, $exclude );
	}
	
/**
*	encode a request
*
*	@access	public
*	@param	string	$responseType	type of response
*	@param	array	$responseParams	all params
*	@return	string	$xml	encoded reponse
*/
	function	encodeResponse( $responseType, $responseParams )
	{
		if( empty( $responseParams ) )
			return	sprintf( "<%s/>\0", $responseType );

		$xml	=	sprintf( "<%s>", $responseType );
		foreach( $responseParams as $key => $value )
		{
			if( $value == "" )
				$xml	.=	sprintf( "<%s/>", $key );
			else
				$xml	.=	sprintf( "<%s>%s</%s>", $key, $this->replaceSpecialChars( $value ), $key );
		}
		$xml	.=	sprintf( "</%s>\0", $responseType );

		return	$xml;
	}

/**
 * replace XML specialchars
 *
 *
 * @param   string  $string
 * @return  string  $string
 */
	function   replaceSpecialChars( $string )
	{
	   $string	=	strtr( $string, $this->specialChars );
       return   $string;
	}

/**
 *	internal function: shutdown server
 *
 *  this will shutdown the server and close the connection to all clients
 *
 *  @access private
 */
	function	_internal_shutdown( $clientId, $params )
	{
		$this->shutdown();
	}	

/**
 *	internal function: close connection to a client
 *
 *  @access private
 */
	function	_internal_closeconnection( $clientId, $params )
	{
		$this->closeConnection( $params["connection"] );
	}	

/**
 *	internal function: status
 *
 *  @access private
 */
	function	_internal_status( $clientId, $params )
	{
		$response					=	$this->serverInfo;
		$response["time"]			=	date( "Y-m-d H:i:s", time() );
		$response["started"]		=	date( "Y-m-d H:i:s", $response["started"] );
		$response["connections"]	=	$this->getClients();
        
		$this->sendResponse( $clientId, "pat_status", $response );
	}
}
?>
