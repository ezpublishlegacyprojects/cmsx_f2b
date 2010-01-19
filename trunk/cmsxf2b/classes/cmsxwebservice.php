<?php
class cmsxWebService
{
	protected $xml;
	protected $Document;
	protected $ini;
	// xml encoding
	public $Encoding = 'utf-8';
    /// The name or IP of the server to communicate with
    public $Server = 'www.cmsxpert.com.br';
    /// The path to the SOAP server
    public $Path = '/soap';
    /// The port of the server to communicate with.
    public $Port = 443;
    /// How long to wait for the call.
    public $TimeOut = 30;
    protected $UseSSL = false;
    protected $Error = '';
	
	public function __construct( $iniFile )
	{
		// documento
		$this->Document = new DOMDocument( '1.0', $this->Encoding );
		$this->ini = eZINI::instance( $iniFile );
	}
	public function setAttibutes( $node, $values )
	{
		foreach ( $values as $key => $value )
		{
			$node->setAttribute( $key, $value );
		}
		return $node;
	}
	public function getAttibutes( $node )
	{
		$result = array();
		if( $node->hasAttributes() )
		{ 
            $attributes = $node->attributes; 
            if( !is_null( $attributes ) )  
            {
            	foreach ( $attributes as $attr )
            	{
            		$result[$attr->name] = $attr->value; 
            	}
            }
        }
		return $result;
	}
	public function getValue( $node )
	{ 
    	$result = ''; 
    	if( $node->nodeType == XML_TEXT_NODE )
    	{ 
        	$result = $node->nodeValue; 
    	}
    	return $result;
	}
	public function send( $doc = null )
	{
		if ( !$doc )
		{
			$doc = $this->xml;
		}
		// check if curl is loaded
		if ( !in_array( "curl", get_loaded_extensions() )  )
		{
			$this->Error = "<b>Error:</b> curl extension is not loaded.";
			return false;
		}
		if ( strtolower( $this->Encoding ) == 'iso-8859-1' )
		{
			$doc = utf8_decode( $doc );
		}
		// define ssl by port
		if ( $this->Port == 443 )
		{
			$this->UseSSL = true;
		}
		// http headers + content
        $HTTPCall = "POST " . $this->Path . " HTTP/1.1\r\n" .
                    "Host: " . $this->Server . ":" . $this->Port . "\r\n" .
                    "User-Agent: eZ Publish\r\n" .                    
                    "Content-Type: text/xml; charset=\"$this->Encoding\"\r\n" .
                    "Content-Length: " . strlen( $doc ) . "\r\n";
        $HTTPCall .= "\r\n" . $doc;
        // url
        $url = ( $this->UseSSL ? 'https://' : 'http://' ) . $this->Server . ":" . $this->Port . $this->Path;
        $ch = curl_init ( $url );
        curl_setopt( $ch, CURLOPT_URL, $url );
	    if ( $this->TimeOut != 0 )
        {
            curl_setopt( $ch, CURLOPT_TIMEOUT, $this->TimeOut );
        }
        if ( $ch == 0 )
        {
        	$this->Error = "<b>Error:</b> curl is not initialized.";
        	return false;
        }
        // use ssl call?
	    if ( $this->UseSSL )
        {
            // FALSE to stop cURL from verifying the peer's certificate. Alternate certificates to verify against can be specified with the CURLOPT_CAINFO option or a certificate directory can be specified with the CURLOPT_CAPATH option. CURLOPT_SSL_VERIFYHOST may also need to be TRUE or FALSE if CURLOPT_SSL_VERIFYPEER is disabled (it defaults to 2).
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
            // 1 to check the existence of a common name in the SSL peer certificate. 2 to check the existence of a common name and also verify that it matches the hostname provided.
            curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 1 );
            curl_setopt( $ch, CURLOPT_HEADER, 1 );
        }

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $HTTPCall );
        if ( $ch != 0 )
        {
            $response = curl_exec( $ch );
        }
        if ( !$response )
        {
            $this->Error = '<b>Error:</b> could not send the XML-SOAP' . ( $this->UseSSL ? ' with SSL ' : ' ' ) . 'call. Could not write to the socket.';
            return false;
        }
        curl_close( $ch );
        $start = strpos( $response, "<?xml" );
        $response = substr( $response, $start, strlen( $response ) - $start );
        return $response;
	}
	public function isFault()
	{
		return ( $this->Error != '' );
	}
	public function getError()
	{
		return $this->Error;
	}
}
?>