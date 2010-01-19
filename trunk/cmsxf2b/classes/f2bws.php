<?php
class cmsxF2bWs extends cmsxWebService
{
	// xml encoding
	public $Encoding = 'utf-8';
    /// The name or IP of the server to communicate with
    public $Server = 'www.f2b.com.br';
    /// The path to the SOAP server
    public $Path = '/WSBilling';
    /// The port of the server to communicate with.
    public $Port = 443;
    /// How long to wait for the call.
    public $Timeout = 30;
    protected $UseSSL = true;
    protected $Error = '';
	
	public function __construct()
	{
		parent::__construct( 'f2b.ini' );
	}
	public function send( $doc = null )
	{
		if ( !$doc )
		{
			$doc = $this->xml;
		}
        return new cmsxF2bBillingResponse( parent::send( $doc ) );
	}
}
?>