<?php
//
// Created on: <04-Mar-2003 10:22:42 bf>
//
// SOFTWARE NAME: eZ Publish
// SOFTWARE RELEASE: 4.1.3
// BUILD VERSION: 23650
// COPYRIGHT NOTICE: Copyright (C) 1999-2009 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//

$http = eZHTTPTool::instance();
$module = $Params['Module'];

require_once( 'kernel/common/template.php' );
$tpl = templateInit();

if ( $module->isCurrentAction( 'Cancel' ) )
{
    $module->redirectTo( '/shop/basket/' );
    return;
}

$user = eZUser::currentUser();
if (! $user->isLoggedIn() )
{
	$module->redirectTo( '/user/login/' );
}

$nome = $email = $cpfcnpj = '';
$isCpf = true;
if ( $user->isLoggedIn() )
{
    $userObject = $user->attribute( 'contentobject' );
    $userMap = $userObject->dataMap();
    $nome = $userMap['first_name']->content() . ' ' . $userMap['last_name']->content();
    $email = $user->attribute( 'email' );
    foreach ( $userMap as $att )
    {
    	if( $att->DataTypeString == 'cmsxcpfcnpj' )
    	{
    		$cpfcnpj = $att->content();
    		$isCpf = ( strlen( $cpfcnpj ) == 14 ) ? false : true;
    	}
    }
}

// Initialize variables
$email2 = $logradouro = $numero = $complemento = $bairro = $cidade = $estado = $cep = '';
$telDdd = $telNumero = $telDddCom = $telNumeroCom = $telDddCel = $telNumeroCel = $comment = '';


// Check if user has an earlier order, copy order info from that one
$orderList = eZOrder::activeByUserID( $user->attribute( 'contentobject_id' ) );
if ( count( $orderList ) > 0 and  $user->isLoggedIn() )
{
    $accountInfo = $orderList[0]->accountInformation();
    // email adicional
    $email2 = $accountInfo['email2'];
    // endereço de cobrança
    $logradouro = $accountInfo['logradouro'];
    $numero = $accountInfo['numero'];
    $complemento = $accountInfo['complemento'];
    $bairro = $accountInfo['bairro'];
    $cidade = $accountInfo['cidade'];
    $estado = $accountInfo['estado'];
    $cep = $accountInfo['cep'];
    // telefone
    $telDdd = $accountInfo['tel_ddd'];
    $telNumero = $accountInfo['tel_numero'];
    // telefone comercial
    $telDddCom = $accountInfo['tel_ddd_com'];
    $telNumeroCom = $accountInfo['tel_numero_com'];
    // telefone comercial
    $telDddCel = $accountInfo['tel_ddd_cel'];
    $telNumeroCel = $accountInfo['tel_numero_cel'];
}

$tpl->setVariable( "input_error", false );
if ( $module->isCurrentAction( 'Store' ) )
{
    $inputErrors = array();
    $nome = trim( $http->postVariable( "Nome" ) );
    if ( strlen( $nome ) <= 3 )
        $inputErrors[] = $isCpf ? 'O nome é obrigatorio' : 'A razão social é obrigatória';
    // email principal
    $email = $http->postVariable( "EMail" );
    if ( ! eZMail::validate( $email ) )
        $inputErrors[] = 'O email é obrigatorio e deve ser válido';
	// email adicional
	$email2 = $http->postVariable( "EMail2" );
    if ( trim( $email2 ) != "" && ! eZMail::validate( $email2 ) )
        $inputErrors[] = 'O email adicional deve ser válido';       
    // logradouro (rua, avenida etc)    
    $logradouro = trim( $http->postVariable( "Logradouro" ) );
    if (  strlen( $logradouro ) <= 3 )
        $inputErrors[] = 'O endereço é obrigatorio';    
    // numero
    $numero = cmsxF2bShopAccountHandler::cleanNum( $http->postVariable( "Numero" ) );
    if ( $numero == "" )
        $inputErrors[] = 'O número é obrigatorio';    
    // complemento
    $complemento = $http->postVariable( "Complemento" );
	// Bairro
    $bairro = $http->postVariable( "Bairro" );
    if ( strlen( $bairro ) <= 3 )
        $inputErrors[] = 'O bairro é obrigatorio';    
	// CEP
    $cep = $http->postVariable( "CEP" );
    if ( ! cmsxF2bShopAccountHandler::isCEP( $cep ) )
        $inputErrors[] = 'O CEP é obrigatorio e deve ser válido';   
    // cidade
    $cidade = trim( $http->postVariable( "Cidade" ) );
    if ( strlen( $cidade ) <= 3 )
        $inputErrors[] = 'A cidade é obrigatorio';   
    // estado
    $estado = trim( $http->postVariable( "Estado" ) );
    if ( strlen( $cidade ) < 2 )
        $inputErrors[] = 'O estado é obrigatorio';   

    // telefone
    $telNumero = cmsxF2bShopAccountHandler::cleanNum( $http->postVariable( "Telefone" ) );
    if ( strlen( $telNumero ) < 7 )
        $inputErrors[] = 'O telefone é obrigatorio e deve ser válido';  
 
    // ddd
    $telDdd = substr( cmsxF2bShopAccountHandler::cleanNum( $http->postVariable( "DDD" ) ), 0 , 2 );
    if ( ( strlen( $telDdd ) != 2 ) )
        $inputErrors[] = 'O DDD é obrigatorio e deve ser válido';  
    
    // telefone comercial
    $telNumeroCom = cmsxF2bShopAccountHandler::cleanNum( $http->postVariable( "Telefone_com" ) );
    $telDddCom = substr( cmsxF2bShopAccountHandler::cleanNum( $http->postVariable( "DDD_com" ) ), 0 , 2 );
    
    // telefone celular
    $telNumeroCel = cmsxF2bShopAccountHandler::cleanNum( $http->postVariable( "Telefone_cel" ) );
    $telDddCel = substr( cmsxF2bShopAccountHandler::cleanNum( $http->postVariable( "DDD_cel" ) ), 0 , 2 );   

    $comment = $http->postVariable( "Comment" );

    if ( count( $inputErrors ) == 0 )
    {
        // Check for validation
        $basket = eZBasket::currentBasket();

        $db = eZDB::instance();
        $db->begin();
        $order = $basket->createOrder();

        $doc = new DOMDocument( '1.0', 'utf-8' );

        $root = $doc->createElement( "shop_account" );
        $doc->appendChild( $root );

		$idNode = $doc->createElement( 'id', $cpfcnpj );
        $idNode->setAttribute( 'type', ( $isCpf ? 'cpf' : 'cnpj' ) );
        $root->appendChild( $idNode );
        
        $nomeNode = $doc->createElement( "nome", $nome );
        $root->appendChild( $nomeNode );

        $emailNode = $doc->createElement( "email", $email );
        $root->appendChild( $emailNode );

        $email2Node = $doc->createElement( "email", $email2 );
        $root->appendChild( $email2Node ); 
        
        $logradouroNode = $doc->createElement( "logradouro", $logradouro );
        $logradouroNode->setAttribute( "numero", $numero );
        $logradouroNode->setAttribute( "complemento", $complemento );        
        $root->appendChild( $logradouroNode );
        
        $bairroNode = $doc->createElement( "bairro", $bairro );
        $root->appendChild( $bairroNode );

        $cepNode = $doc->createElement( "cep", $cep );
        $root->appendChild( $cepNode );

        $cidadeNode = $doc->createElement( "cidade", $cidade );
        $root->appendChild( $cidadeNode );

        $estadoNode = $doc->createElement( "estado", $estado );
        $root->appendChild( $estadoNode );

        $commentNode = $doc->createElement( "comment", $comment );
        $root->appendChild( $commentNode );
        
        $telNode = $doc->createElement( 'telefone', $telNumero );
        $telNode->setAttribute( 'ddd', $telDdd );
        $root->appendChild( $telNode );

        $telComNode = $doc->createElement( 'telefone_com', $telNumeroCom );
        $telComNode->setAttribute( 'ddd', $telDddCom );
        $root->appendChild( $telComNode );
        
        $telCelNode = $doc->createElement( 'telefone_cel', $telNumeroCel );
        $telCelNode->setAttribute( 'ddd', $telDddCel );
        $root->appendChild( $telCelNode );             
        
        $xmlString = $doc->saveXML();

        $order->setAttribute( 'data_text_1', $xmlString );
        
        $shopAccountINI = eZINI::instance( 'shopaccount.ini' );    
        $order->setAttribute( 'account_identifier', $shopAccountINI->variable( 'AccountSettings', 'Handler' ) );

        $order->setAttribute( 'ignore_vat', 0 );

        $order->store();
        $db->commit();
        eZShopFunctions::setPreferredUserCountry( 'BR' );
        $http->setSessionVariable( 'MyTemporaryOrderID', $order->attribute( 'id' ) );

        $module->redirectTo( '/shop/confirmorder/' );
        return;
    }
    else
    {
        $tpl->setVariable( "input_error", true );
        $tpl->setVariable( "input_errors", $inputErrors );
    }
}

$tpl->setVariable( "nome", $nome );
$tpl->setVariable( "email", $email );
$tpl->setVariable( "email2", $email2 );

$tpl->setVariable( "logradouro", $logradouro );
$tpl->setVariable( "numero", $numero );
$tpl->setVariable( "complemento", $complemento );
$tpl->setVariable( "bairro", $bairro );
$tpl->setVariable( "cep", $cep );
$tpl->setVariable( "cidade", $cidade );
$tpl->setVariable( "estado", $estado );

$tpl->setVariable( "tel_numero", $telNumero );
$tpl->setVariable( "tel_ddd", $telDdd );

$tpl->setVariable( "tel_numero_com", $telNumeroCom );
$tpl->setVariable( "tel_ddd_com", $telDddCom );

$tpl->setVariable( "tel_numero_cel", $telNumeroCel );
$tpl->setVariable( "tel_ddd_cel", $telDddCel );

$tpl->setVariable( "comment", $comment );
$tpl->setVariable( 'is_cpf', $isCpf );
$tpl->setVariable( 'cpfcnpj', $cpfcnpj );

$Result = array();
$Result['content'] = $tpl->fetch( "design:f2b/confirmar_dados.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => 'Informações para o pagamento' ) );
?>
