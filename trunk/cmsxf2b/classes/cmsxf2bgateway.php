<?php
/**
 * Gateway de cobrança F2b
 *
 * @package cmsxf2b
 * @author Renan Leme <renan@cmsxpert.com.br>
 * @version 0.9
 */
class cmsxF2bPaymentGateway extends eZPaymentGateway
{
	const GATEWAY = "cmsxF2b";
	const BILLING = 1;
	const FAILURE = 2;
	const SUCESS = 3;
	const BILLING_RETRY = 4;		
	/**
	 * Configurações específicas f2b
	 */
	private $ini = null;
	/**
	 * Handler de requisições http
	 */
	private $http = null;
	/**
	 * Informações do pedido
	 * 
	 * @var object
	 */
	private $order = null;

	/**
	 * Id do pedido
	 * 
	 * @var integer
	 */
	private $orderId = null;
	
	/**
	 * Construtor
	 */
	public function __construct()
	{
		// Load ini
		$this->ini = eZINI::instance( 'f2b.ini' );
		$this->http = eZHTTPTool::instance();
	}
	function execute( $process, $event )
	{
		// get order
		$processParams = $process->attribute( 'parameter_list' );
		$this->orderId = $orderID = $processParams['order_id'];
		$this->order = eZOrder::fetch( $orderID );
		$hasTipo = $this->hasTipoPagamento();
		
		if ( !$this->http->hasPostVariable( 'tipo' ) && $this->ini->variable( 'Settings', 'EscolherTipoCheckout' ) == 'enabled' && !$hasTipo )
		{
			$allowedTypes = $this->ini->variable( 'Pagamento', 'tipos' );
     		$process->Template = array( 'templateName' => 'design:f2b/escolha_tipo_pagamento.tpl',
                                        'path' => array( array( 'url' => false,
										                                'text' => 'Escolha uma forma de pagamento' ) ),
	    			                    'templateVars' => array( 'page_title' => 'Escolha uma forma de pagamento',
								                                 'tipos' => $allowedTypes )
		                              );
		    $process->setAttribute( 'event_state', self::BILLING );
		    $this->log( 'Iniciando meio de pagamento, escolhendo tipo de pagamento' );
    		return eZWorkflowType::STATUS_FETCH_TEMPLATE_REPEAT;
		}
		elseif ( $this->http->hasPostVariable( 'tipo' ) )
		{
			$this->log( 'Meio de pagamento escolhido: ' . $this->http->postVariable( 'tipo' ) );
		}
		// If data was posted to shop/checkout then change state
		// Switch between states of workflow process
		switch ( $process->attribute('event_state') )
		{
			case self::FAILURE :
			{
				return eZWorkflowType::STATUS_REJECTED;
			}
			break;			
			case self::SUCESS :
			{
				//return eZWorkflowType::STATUS_REJECTED;
				return eZWorkflowType::STATUS_ACCEPTED;
			}
			break;
			default:
				$this->register( $process );
	     		$process->Template = array( 'templateName' => 'design:f2b/pagamento.tpl',
	                                        'path' => array( array( 'url' => false,
											                                'text' => 'Informações sobre o pagamento' ) ),
	    				                    'templateVars' => array( 'page_title' => 'Informações sobre o pagamento',
			    					                                 'account_information' => $this->order->accountInformation(),
	     		                                                     'cobranca' => self::getCobranca( $this->order) ) );
	            return eZWorkflowType::STATUS_FETCH_TEMPLATE_REPEAT;
		}
	}

	/*!
	    Handle request
	*/
	function register( $process )
	{
		// Get process params
		$processParams = $process->attribute('parameter_list');
		$orderID = $processParams['order_id'];
		$processID = $process->attribute( 'id' );	
   		
		// Get order
		$order = $this->order;
		
		$f2b = new cmsxF2bBillingRequest();
		// tipo de cobrança
		$allowedTypes = $this->ini->variable( 'Pagamento', 'tipos' );
		$tipo = null;
		if ( in_array( $this->http->postVariable( 'tipo' ), $allowedTypes ) )
		{
			$tipo = $this->http->postVariable( 'tipo' );
		}
		// cobrança	
		$f2b->setCobranca( $order->attribute('total_inc_vat'), $orderID, $tipo );
		//descricao dos produtos
		if ( $this->ini->variable( 'Settings', 'demonstrativo_produtos' ) == 'enabled' )
		{
			$f2b->addDescricao( $this->ini->variable( 'Settings', 'demonstrativo_produtos_label' ) );
			$produtos = $order->attribute("product_items");
			foreach( $produtos as $produto )
			{
				$f2b->addDescricao( $produto['object_name'] );
			}
		}
		// sacaco		
		$f2b->setSacado( $order->attribute('account_information') , $order->attribute('user_id') );
		$f2b->build();
		$response = $f2b->send();
		$error = false;
		if ( $f2b->isFault() || $response->isFault() )
		{
			$error = $f2b->isFault() ? $f2b->getError() : $response->getError();
			$error = strip_tags( preg_replace( '/\<br(\s*)?\/?\>/i', "\n", $error ) );
			$error = str_ireplace( 'ERRO', '', $error );
			$error = 'Erro F2b /' . str_replace( '  ', ' ', str_replace( array( "\r\n", "\n", "\r" ), ' ', $error ) );
			if ( $process->attribute('event_state') == self::BILLING_RETRY )
			{
				cmsxF2bUtils::sendEmailToAdmin( 'Erro na cobrança F2b' , 'Cobrança cancelada  - ' . $error );
				$this->log( 'Cobrança cancelada  - ' . $error );
				$process->setAttribute( 'event_state', self::FAILURE );
			}
			else
			{
				$this->log( 'Falha na cobrança, nova tentativa  - ' . $error );
				$process->setAttribute( 'event_state', self::BILLING_RETRY );
			}	
		}
		else
		{
              $cobranca = $response->getCobranca();
	          $cobranca = $cobranca[0];
	          $sacado = $response->getSacado();
	          $sacado = $sacado[0];
        	  $this->addCobrancaF2b( $order, array( 'numero' => $cobranca['numero'], 'url' => $cobranca['url'], 'cliente' => $sacado['numero'] ) );
              $process->setAttribute( 'event_state', self::SUCESS );
              $this->log( 'Sucesso ao realizar cobrança F2b - número: ' . $cobranca['numero'] . ', cliente: ' . $sacado['numero'] . ', url de pagamento: ' . $cobranca['url']  );
		}
	}
	public function addCobrancaF2b( $order, $dados = array() )
	{
		$xmlString = $order->attribute( 'data_text_2' );
        $doc = new DOMDocument( '1.0', 'utf-8' );
        if ( $xmlString != null )
 	       $sucess = $doc->loadXML( $xmlString );
        $cobranca = $hasCobranca = $doc->getElementsByTagName( 'cobranca_f2b' )->item( 0 );
        if ( !$cobranca )
        {
            $cobranca = $doc->createElement( 'cobranca_f2b', '' );
        }
        if ( isset( $dados['numero'] ) )
        {
            $cobranca->setAttribute( 'numero', $dados['numero'] );
        }
        if ( isset( $dados['url'] ) )
        {
            $cobranca->setAttribute( 'url', $dados['url'] );
        }
        if ( isset( $dados['cliente'] ) )
        {
            $cobranca->setAttribute( 'cliente', $dados['cliente'] );
        }
        if ( isset( $dados['tipo'] ) )
        {
            $cobranca->setAttribute( 'tipo', $dados['tipo'] );
        }          
        if ( !$hasCobranca )
        	$doc->appendChild( $cobranca );
        $order->setAttribute( 'data_text_2', $doc->saveXML() );
        $order->store();
	}
	protected function hasTipoPagamento( $get = false )
	{
		$allowedTypes = $this->ini->variable( 'Pagamento', 'tipos' );
		if ( $this->http->hasPostVariable( 'tipo' ) || in_array( $this->http->postVariable( 'tipo' ), $allowedTypes ) )
		{
			 $this->addCobrancaF2b( $this->order, array( 'tipo' => $this->http->postVariable( 'tipo' ) ) );
			 return true;
		}
		$xmlString = $this->order->attribute( 'data_text_2' );
        $doc = new DOMDocument( '1.0', 'utf-8' );
        if ( $xmlString != null )
 	       $sucess = $doc->loadXML( $xmlString );
        $cobranca = $doc->getElementsByTagName( 'cobranca_f2b' )->item( 0 );
        if ( $cobranca && $cobranca->hasAttribute( 'tipo' ) )
        {
	        if ( $get )
	        {
		        return $cobranca->getAttribute( 'tipo' );
	        }
	        return true;
        }
        return false;
	}	
    public static function getCobranca( $order )
    {
        $numero = $url = $cliente = $tipo = '';

        $xmlString = $order->attribute( 'data_text_2' );
        if ( $xmlString != null )
        {
            $dom = new DOMDocument( '1.0', 'utf-8' );
            $success = $dom->loadXML( $xmlString );

            $cobrancaNode = $dom->getElementsByTagName( 'cobranca_f2b' )->item( 0 );
            if ( $cobrancaNode )
            {
                $numero = $cobrancaNode->getAttribute( 'numero' );
                $url = $cobrancaNode->getAttribute( 'url' );
                $cliente = $cobrancaNode->getAttribute( 'cliente' );
                $tipo = $cobrancaNode->getAttribute( 'tipo' );
            }
        }
		if ( $numero == '' || $url == '' || $cliente == '' )
		{
			return false;
		}
        return array( 'numero' => $numero,
        			  'url' => $url,
 			          'cliente' => $cliente,
                      'tipo' => $tipo,
                    );
    }
    private function log( $str )
    {
	     cmsxF2bUtils::toLog( $str, 'OrderID=' . $this->orderId , 'Billing' );
    }
}

eZPaymentGatewayType::registerGateway( cmsxF2bPaymentGateway::GATEWAY , "cmsxF2bPaymentGateway", "Cobrança F2b" );

?>