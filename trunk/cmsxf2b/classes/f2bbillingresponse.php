<?php

class cmsxF2bBillingResponse extends cmsxF2bWs
{
	
	public function __construct( $xml )
	{
		$this->Document = new DOMDocument( '1.0', 'utf-8' );
		$this->Document->loadXML( $xml );
		$this->xml = $this->Document->saveXML();
		$this->xPath = new DOMXPath( $this->Document );
		$xpathLog = $this->xPath->evaluate( '//log' )->item( 0 );
		$this->Error = $xpathLog ? $xpathLog->nodeValue : 'Não foi possível encontrar o elemento <b>&lt;log&gt;</b>';
	}
	public function getCobranca()
	{
		$xpathBilling = $this->xPath->evaluate( '//cobranca' );
		$billing  = false;
		if( $xpathBilling )
		{
			foreach( $xpathBilling as $i => $pathBilling )
			{
				$billing[$i] = $this->getAttibutes( $pathBilling );
				$billing[$i]["nome"] = $pathBilling->getElementsByTagName( 'nome' )->item( 0 )->nodeValue;
				$billing[$i]["email1"] = $pathBilling->getElementsByTagName( 'email' )->item( 0 )->nodeValue;
				$billing[$i]["email2"] = '';
				if ( $pathBilling->getElementsByTagName( 'email' )->item( 1 ) )
				{
					$billing[$i]["email2"] = $pathBilling->getElementsByTagName( 'email' )->item( 1 )->nodeValue;
				}				
				$billing[$i]["url"] = $pathBilling->getElementsByTagName( 'url' )->item( 0 )->nodeValue;
			}
		}
		return $billing;
	}
	public function getAgendamento()
	{
		$pathScheduled = $this->xPath->evaluate( '//agendamento' )->item( 0 );
		$scheduled  = false;
		if( $pathScheduled )
		{
				$scheduled = $this->getAttibutes( $pathScheduled );
				$scheduled['texto'] = $pathScheduled->nodeValue;
		}
		return $scheduled;
	}
	public function getSacado()
	{
		$xpathClient = $this->xPath->evaluate( '//sacado' );
		$client  = false;
		if( $xpathClient )
		{
			foreach( $xpathClient as $i => $pathClient )
			{
				$client[$i] = $this->getAttibutes( $pathClient );
				$client[$i]["nome"] = $pathClient->getElementsByTagName( 'nome' )->item( 0 )->nodeValue;
				$client[$i]["email1"] = $pathClient->getElementsByTagName( 'email' )->item( 0 )->nodeValue;
				$client[$i]["email2"] = '';
				if ( $pathClient->getElementsByTagName( 'email' )->item( 1 ) )
				{
					$client[$i]["email2"] = $pathClient->getElementsByTagName( 'email' )->item( 1 )->nodeValue;
				}
			}
		}
		return $client;		
	}
	public function isFault()
	{
		return ( $this->Error != '' && strtoupper( trim( preg_replace( "/\n/", '', $this->Error ) ) ) != 'OK' );
	}	
}
?>