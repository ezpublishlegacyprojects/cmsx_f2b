<?php
class cmsxF2bBillingRequest extends cmsxF2bWs 
{
	protected $conta, $senha, $sacador;
	protected $valor, $numDocumento, $tipo, $banco;
	protected $description = array();
	protected $avalista;
	protected $descontoValor, $descontoTipo, $descontoAntecedencia;
	protected $multaValor, $multaTipo, $multaDiaValor, $multaDiaTipo, $multaAtraso;
	protected $agen, $agenDescricao, $agenVencimento, $agenUltimoDia, $agenAntecedencia, $agenPeriodicidade, $agenPeriodos, $agenSemVencimento;
    protected $sacNome, $sacEmail, $sacEmail2, $sacId, $sacIdType, $sacEnder, $sacGrupo, $sacEnvio, $sacCod, $sacDdd, $sacTel, $sacDddCom, $sacTelCom, $sacDddCel, $SacTelCel; 
    
    /**
	 * define conta F2b
	 */
	public function setContaF2b( $conta, $senha, $sacador = '' )
	{
		$this->conta = $conta;
		$this->senha = $senha;
		$this->sacador = $sacador;
		return $this;
	}
	/**
	 * Retorna o trecho interno DOM com as informaçõies da conta F2b
	 */
	public function buildContaF2b()
	{
		if ( !$this->conta && !$this->senha )
		{
			$this->conta = $this->ini->variable( 'Settings', 'conta' );
			$this->senha = $this->ini->variable( 'Settings', 'senha' );
		}
		if ( !$this->sacador )
		{
			$this->sacador = $this->ini->variable( 'Settings', 'sacador' );
		}
		// Cria o elemento sacador
		$sacador = $this->Document->createElement( 'sacador', $this->sacador );
		$this->setAttibutes( $sacador,  array( 'conta' => $this->conta, 
		                                       'senha' => $this->senha ) );
		return $sacador;
	}
	/**
	 * define cobrança
	 */
	public function setCobranca( $valor, $numDocumento, $tipo = null, $banco = null  )
	{
		$this->valor = $valor;
		$this->numDocumento = $numDocumento;
		$this->tipo = $tipo;
		$this->banco = $banco;
		return $this;
	}
	/**
	 * Retorna o trecho interno DOM com as informaçõies da cobrança
	 */
	public function buildCobranca()
	{
	    if ( !$this->banco )
		{
			$this->banco = $this->ini->variable( 'Pagamento', 'cod_banco' );
		}
		if ( !$this->tipo )
		{
			$tipos = $this->ini->variable( 'Pagamento', 'tipos' );
			$this->tipo = $tipos[0];
			if ( count( $tipos[0] ) > 1 )
			{
				$this->tipo = $this->ini->variable( 'Pagamento', 'tipo_padrao' );
			}
		}		
		// Cria o elemento cobranca		
		$invoice = $this->Document->createElement( 'cobranca' );
		// Tipo de cobrança:
		// B - Boleto; C - Cartão de crédito; D - Cartão de débito; T - Transferência On-line
		// Caso queira permitir cobrança por mais de um tipo, enviar as letras juntas. Ex.: "BCD" (Aceitar Boleto, Crédito e Débito)
		// num_document - serve para enviar à F2b um número de controle próprio, facilitando a busca na administração
		$this->setAttibutes( $invoice,  array( 'valor' => $this->valor, 
                                               'tipo_cobranca' => substr( strtoupper( $this->tipo ), 0, 1 ) ,
                                               'num_documento' => $this->numDocumento,
                                               'cod_banco' => $this->banco ) );
		return $invoice;
	}
	/**
	 * Adiciona linha de descrição no boleto ou fatura F2b (max 10 linhas)
	 */
	public function addDescricao( $line )
	{
		if ( $line != '' && ( count( $this->description ) + count( $this->ini->variable( 'Settings', 'demonstrativo' ) ) ) < 9 )
		{
			$this->description[] = substr( trim( $line ), 0, 80 );
			return true;
		}
		return false;
	}
	/**
	 * Define os demonstrativos a serem anexados a fatura F2b
	 * Dinamico e via arquivo de configuração
	 */
	protected function buildDescricao()
	{
		# define se o texto de demonstração deve vir antes ou depois dos descritivos gerados pelo sistema
		$pre = ( $this->ini->variable( 'Settings', 'demonstrativo_pre' ) == 'enabled' ) ? true : false ;
		$lines = $this->ini->variable( 'Settings', 'demonstrativo' );
	    if ( count( $lines ) >= 1 )
		{
			$lines = array_reverse( $lines );
			foreach ( $lines as $line )
			{
				if ( trim( $line ) != '' )
				{
				    $pre ? array_unshift( $this->description, $line ) : array_push( $this->description, $line );
				}
			}
		}
		if ( trim( $this->ini->variable( 'Settings', 'demonstrativo_titulo' ) != '' ) )
		{
			array_unshift( $this->description, $this->ini->variable( 'Settings', 'demonstrativo_titulo' ) );
		}
		// Cria o elemento descrição
		// Cria os elementos demonstrativos (Até 10 linhas com 80 caracteres cada)
		$descriptions = array();
		if ( count( $this->description ) >= 1 )
		{
			foreach ( $this->description as $line )
			{ 

				$descriptions[] = $this->Document->createElement( 'demonstrativo', $line );
			}			
		}
		else
		{
			$descriptions[] = $this->Document->createElement( 'demonstrativo', 'Demonstrativo de compras' );
		}
		return $descriptions;
	}
	/**
	 * define sacador/avalista
	 */
	public function setSacador( $avalista )
	{
		$this->avalista = $avalista;
		return $this;
	}
	/**
	 * Retorna o trecho interno DOM com as informaçõies do sacador/avalista
	 */
	public function buildSacador()
	{
		if ( !$this->avalista )
		{
			$this->avalista = $this->ini->variable( 'Settings', 'sacador_avalista' );
		}
		return $this->Document->createElement( 'sacador_avalista', $this->avalista );
	}
    /**
	 * define desconto
	 */
	public function setDesconto( $valor, $tipo = null, $antecedencia = null )
	{
		$this->descontoValor = $valor;
		if ( $tipo )
		$this->descontoTipo = $tipo;
		if ( $antecedencia )
		$this->descontoAntecedencia = $antecedencia;
		return $this;
	}
	/**
	 * Retorna o trecho interno DOM com as informaçõies do desconto
	 */
	public function buildDesconto()
	{
		if ( !$this->descontoValor )
		{
			$this->descontoValor = $this->ini->variable( 'Desconto', 'valor' );
		}
		if ( !$this->descontoTipo )
		{
			$this->descontoTipo = $this->ini->variable( 'Desconto', 'tipo' );
		}
		if ( !$this->descontoAntecedencia )
		{
			$this->descontoAntecedencia = $this->ini->variable( 'Desconto', 'antecedencia' );
		}				
        // Cria o elemento desconto
		$discount = $this->Document->createElement( 'desconto' );
		$this->setAttibutes( $discount, array( 'valor' => $this->descontoValor, 
		                                       'tipo_desconto' => ( $this->descontoTipo == 'real' ? 0 : 1 ), 
                                               'antecedencia' => $this->descontoAntecedencia ) );
		return $discount;
	}
    /**
	 * define multa
	 */
	public function setMulta( $valor, $tipo = null, $diaValor = null, $diaTipo = null, $atraso = null )
	{
		$this->multaValor = $valor;
		if ( $tipo )
		$this->multaTipo = $tipo;
		if ( $diaValor )
		$this->multaDiaValor = $diaValor;
		if ( $diaTipo )
		$this->multaDiaTipo = $diaTipo;
		if ( $atraso )
		$this->multaAtraso = $atraso;		
		return $this;
	}
	/**
	 * Retorna o trecho interno DOM com as informaçõies da multa
	 */
	public function buildMulta()
	{
		if ( !$this->multaTipo )
		{
			$this->multaTipo = $this->ini->variable( 'Multa', 'tipo' );
		}
		if ( !$this->multaValor )
		{
			$this->multaValor = (int) $this->ini->variable( 'Multa', 'valor' );
		}
		if ( !$this->multaDiaTipo )
		{
			$this->multaDiaTipo = $this->ini->variable( 'Multa', 'dia_tipo' );
		}
		if ( !$this->multaDiaValor )
		{
			$this->multaDiaValor = $this->ini->variable( 'Multa', 'dia_valor' );
		}		
		// valor da multa maior que o permitido?
		if ( $this->multaTipo != 'real' )
		{
			$this->multaValor = ( $this->multaValor > 20  ) ? 20 : $this->multaValor;
		}
		else
		{
			$multaMax = $this->valor + ( 0.2 * $this->valor );
			$multaAplicada = $this->valor + $this->multaValor;
		    $this->multaValor = ( $multaAplicada > $multaMax  ) ? 0.2 * $this->valor : $this->multaValor;		
		}
		// valor da multa diária maior que o permitido?
		if ( $this->multaDiaTipo != 'real' )
		{
			$this->multaDiaValor = ( $this->multaDiaValor > 2  ) ? 2 : $this->multaDiaValor;
		}
		else
		{
			$multaMax = $this->valor + ( 0.02 * $this->valor );
			$multaAplicada = $this->valor + $this->multaDiaValor;
		    $this->multaDiaValor = ( $multaAplicada > $multaMax  ) ? 0.02 * $this->valor : $this->multaDiaValor;		
		}	
		// Cria o elemento multa
		$penalty = $this->Document->createElement( 'multa' );
		$this->setAttibutes( $penalty, array( 'valor' => $this->multaValor,
		                                      'tipo_multa' => ( $this->multaTipo == 'real' ? 0 : 1 ), 
		                                      'valor_dia' => (int) $this->multaDiaValor,
		                                      'tipo_multa_dia' => ( $this->multaDiaTipo == 'real' ? 0 : 1 ), 
		                                      'atraso' => (int) $this->multaAtraso ) );
		return $penalty;
	}
    /**
	 * define agendamento
	 */
	public function setAgendamento( $descricao = null, $vencimento = null, $ultimoDia = null, $antecedencia = null, $periodicidade = null, $periodos = null, $semVencimento = null )
	{
		if ( $descricao )
		$this->agenDescricao = $descricao;		
		if ( $vencimento )
		$this->agenVencimento = $vencimento;
		if ( $ultimoDia )
		$this->agenUltimoDia = $ultimoDia;
		if ( $antecedencia )
		$this->agenAntecedencia = $antecedencia;
		if ( $periodicidade )
		$this->agenPeriodicidade = $periodicidade;
		if ( $periodos )
		$this->agenPeriodos = $periodos;
		if ( $semVencimento )
		$this->agenSemVencimento = $semVencimento;			
		return $this;
	}	
	/**
	 * Retorna o trecho interno DOM com as informaçõies do agendamento
	 */
	public function buildAgendamento()
	{
		if ( !$this->agenDescricao )
		{
			$this->agenDescricao = $this->ini->variable( 'Agendamento', 'descricao' );
		}
		if ( !$this->agenVencimento )
		{
			$this->agenVencimento = $this->ini->variable( 'Agendamento', 'vencimento' );
		}
		if ( !$this->agenUltimoDia )
		{
			$this->agenUltimoDia = $this->ini->variable( 'Agendamento', 'ultimo_dia' );
		}
		if ( !$this->agenAntecedencia )
		{
			$this->agenAntecedencia = $this->ini->variable( 'Agendamento', 'antecedencia' );
		}
		if ( !$this->agenPeriodicidade )
		{
			$this->agenPeriodicidade = $this->ini->variable( 'Agendamento', 'periodicidade' );
		}
		if ( !$this->agenPeriodos )
		{
			$this->agenPeriodos = $this->ini->variable( 'Agendamento', 'periodos' );
		}
		if ( !$this->agenSemVencimento )
		{
			$this->agenSemVencimento = $this->ini->variable( 'Agendamento', 'sem_vencimento' );
		}										
		//Cria o elemento agendamento
		$scheduled = $this->Document->createElement( 'agendamento', $this->agenDescricao );
		$scheduled->setAttribute( 'vencimento' , date( 'Y-m-d', time() + ( $this->agenVencimento * 86400 ) ) );
		if ( $this->agenPeriodos != 1 )
		{
			$scheduled->setAttribute( 'ultimo_dia' , ( $this->agenUltimoDia == 'enabled' ? 's' : 'n' ) );
			if ( intval( $this->agenPeriodicidade ) > 0 )
			{
				$scheduled->setAttribute( 'antecedencia' , $this->agenAntecedencia );
			}			
			if ( intval( $this->agenPeriodicidade ) > 0 )
			{
				$scheduled->setAttribute( 'periodicidade' , $this->agenPeriodicidade );
			}
			$scheduled->setAttribute( 'periodos' , $this->agenPeriodos );
		}
		$scheduled->setAttribute( 'sem_vencimento' , ( $this->agenSemVencimento == 'enabled' ? 's' : 'n' ) );
		return $scheduled;
	}
	public function setSacado( $sacado = array(), $codigo, $envio = null, $grupo = null )
	{
		if ( $grupo )
		{
			$this->sacGrupo = $grupo;
		}		
		if ( $envio )
		{
			$this->sacEnvio = $envio;
		}
		$this->sacCod = $codigo;
		$this->sacNome = $sacado['nome'];
		$this->sacEmail = $sacado['email'];
		$this->sacEmail2 = $sacado['email2'];
		$ender = array();
		if ( $sacado['logradouro'] != '' )
			$ender['logradouro'] = $sacado['logradouro'];
		if ( $sacado['numero'] != '' )
			$ender['numero'] = $sacado['numero'];
		if ( $sacado['complemento'] != '' )
			$ender['complemento'] = $sacado['complemento'];
		if ( $sacado['bairro'] != '' )
			$ender['bairro'] = $sacado['bairro'];		
		if ( $sacado['cidade'] != '' )
			$ender['cidade'] = $sacado['cidade'];		
		if ( $sacado['estado'] != '' )
			$ender['estado'] = $sacado['estado'];
		if ( $sacado['cep'] != '' )
			$ender['cep'] = $sacado['cep'];
		// endereco
		$this->sacEnder = $ender;
		// telefone
		$this->sacDdd = $sacado['tel_ddd'];
		$this->sacTel = $sacado['tel_numero'];
		// telefone comercial
		$this->sacDddCom = $sacado['tel_ddd_com'];
		$this->sacTelCom = $sacado['tel_numero_com'];
		// telefone celular
		$this->sacDddCel = $sacado['tel_ddd_cel'];
		$this->sacTelCel = $sacado['tel_numero_cel'];		
		$this->sacIdType = ( $sacado['id_type'] == 'cpf' ? 'cpf' : 'cnpj' );
		// cpf ou cnpj
		$this->sacId = $sacado['id'];
		return $this;
	}
	public function buildSacado()
	{
		if ( !$this->sacEnvio == '' )
		{
			$this->sacEnvio = $this->ini->variable( 'Settings', 'envio' );
		}
		$envio = $this->sacEnvio;
		switch ( $envio )
		{
			case 'email':
			$envio = 'e';
			break;
			
			case 'impressa':
			$envio = 'p';
			break;
			
			case 'ambas':
			$envio = 'b';
			break;			
									
			default:
			// nenhum
			$envio = 'n';;
			break;
		}
		
		if ( !$this->sacGrupo == '' )
		{
			$this->sacGrupo = $this->ini->variable( 'Settings', 'grupo' );
		}
		// Cria o elemento sacado
		$client = $this->Document->createElement( 'sacado' );
		$this->setAttibutes( $client, array( 'grupo' => $this->sacGrupo, 
                                             'codigo' => $this->sacCod, 
                                             'envio' => $envio ) );
		// Cria o elemento nome
		$client->appendChild( $this->Document->createElement( 'nome', $this->sacNome ) );
		if ( eZMail::validate( $this->sacEmail ) )
		{
			// Cria o elemento email
			$client->appendChild( $this->Document->createElement( 'email', $this->sacEmail ) );
		}
		if ( eZMail::validate( $this->sacEmail2 ) )
		{
			// Cria o elemento email
			$client->appendChild( $this->Document->createElement( 'email', $this->sacEmail2 ) );
		}
		// Cria o elemento endereco
		$address = $this->Document->createElement( 'endereco' );
		$this->setAttibutes( $address,  $this->sacEnder );
		$client->appendChild( $address );
		
		if ( strlen( $this->sacDdd ) == 2 && $this->sacTel != '' )
		{
			// telefone
			$tel = $this->Document->createElement( 'telefone' );
			$this->setAttibutes( $tel, array( 'ddd' => $this->sacDdd, 'numero' => $this->sacTel ) );
			$client->appendChild( $tel );	
		}
		if ( strlen( $this->sacDddCom ) == 2 && $this->sacTelCom != '' )
		{
			// telefone comercial
			$telcom = $this->Document->createElement( 'telefone_com' );
			$this->setAttibutes( $telcom, array( 'ddd_com' => $this->sacDddCom, 'numero_com' => $this->sacTelCom ) );
			$client->appendChild( $telcom );
		}
		if ( strlen( $this->sacDddCel ) == 2 && $this->SacTelCel != '' )
		{
			// telefone celular
			$telcel = $this->Document->createElement( 'telefone_cel' );
			$this->setAttibutes( $telcel, array( 'ddd_cel' => $this->sacDddCel, 'numero_cel' => $this->SacTelCel ) );
			$client->appendChild( $telcel );
		}
		// cpf ou cnpj
		$client->appendChild( $this->Document->createElement( $this->sacIdType, $this->sacId ) );
		return $client;
	}	
	public function build()
	{
		// soap envelope
		$soapEnv = $this->Document->createElement( 'soap-env:Envelope' );
		$soapEnv->setAttribute( 'xmlns:soap-env', 'http://schemas.xmlsoap.org/soap/envelope/' );
		$this->Document->appendChild( $soapEnv );
		// soap corpo
		$soapBody = $this->Document->createElement( 'soap-env:Body' );
		$soapEnv->appendChild( $soapBody );
		// cobrança f2b
		$mF2bCobranca = $this->Document->createElement( 'm:F2bCobranca' );
		$mF2bCobranca->setAttribute( 'xmlns:m', 'http://www.f2b.com.br/soap/wsbilling.xsd' );
		$soapBody->appendChild( $mF2bCobranca );
		// mensagem
		$mensagem = $this->Document->createElement( 'mensagem' );
		$this->setAttibutes( $mensagem, array( 'data' => date( 'Y-m-d' ), 
		                                       'numero' => date( 'His' ), 
		                                       'tipo_ws' => 'eZ Publish' ) );
		$mF2bCobranca->appendChild( $mensagem );
		
		$mF2bCobranca->appendChild( $this->buildContaF2b() );
		// cobranca
		$cobranca = $mF2bCobranca->appendChild( $this->buildCobranca() );
		// descricao
		$descricoes = $this->buildDescricao();
		foreach ( $descricoes as $descricao )
		{
			$cobranca->appendChild( $descricao );
		}
		// sacador/avalista
		$cobranca->appendChild( $this->buildSacador() );
		// desconto
		if ( strtolower( $this->ini->variable( 'Desconto', 'desconto' ) ) == 'enabled' )
		{
			$cobranca->appendChild( $this->buildDesconto() );
		}
		// multa
		if ( strtolower( $this->ini->variable( 'Multa', 'multa' ) ) == 'enabled' )
		{
			$cobranca->appendChild( $this->buildMulta() );
		}
		// multa
		if ( strtolower( $this->ini->variable( 'Agendamento', 'agendamento' ) ) == 'enabled' )
		{
			$mF2bCobranca->appendChild( $this->buildAgendamento() );
		}
		$mF2bCobranca->appendChild( $this->buildSacado() );
		$xml = 	$this->Document->saveXML();
		if ( strtolower( $this->Encoding ) == 'iso-8859-1' )
		{
			$xml = utf8_decode( $xml );
		}
		$this->xml = $xml;
		return $xml;
	}	
}
?>