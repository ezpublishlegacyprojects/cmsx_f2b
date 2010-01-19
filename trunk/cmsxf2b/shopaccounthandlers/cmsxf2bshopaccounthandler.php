<?php
/**
 * Handler de conta da loja virtual do eZ Publish
 *
 * @package cmsxf2b
 * @author Renan Leme <renan@cmsxpert.com.br>
 * @version 0.9
 */
class cmsxF2bShopAccountHandler
{
    public function __construct()
    {

    }

    /*!
     Will verify that the user has supplied the correct user information.
     Returns true if we have all the information needed about the user.
    */
    function verifyAccountInformation()
    {
        return false;
    }

    /*!
     Redirectes to the user registration page.
    */
    function fetchAccountInformation( &$module )
    {
        $module->redirectTo( '/f2b/confirmardados' );
    }

    /*!
     \return the account information for the given order
    */
    function email( $order )
    {
        $email = false;
        $xmlString = $order->attribute( 'data_text_1' );
        if ( $xmlString != null )
        {
            $dom = new DOMDocument( '1.0', 'utf-8' );
            $success = $dom->loadXML( $xmlString );
            $emailNode = $dom->getElementsByTagName( 'email' )->item( 0 );
            if ( $emailNode )
            {
                $email = $emailNode->textContent;
            }
        }

        return $email;
    }

    /*!
     \return the account information for the given order
    */
    function accountName( $order )
    {
        $accountName = '';
        $xmlString = $order->attribute( 'data_text_1' );
        if ( $xmlString != null )
        {
            $dom = new DOMDocument( '1.0', 'utf-8' );
            $success = $dom->loadXML( $xmlString );
            $nameNode = $dom->getElementsByTagName( 'nome' )->item( 0 );
            $accountName = $nameNode->textContent;
        }

        return $accountName;
    }

    function accountInformation( $order )
    {
        $nome = $email = $email2 = $id = $idType = $logradouro = $numero = $complemento = $bairro = $cidade = $estado = $cep = '';
        $tel_ddd = $tel_numero = $tel_ddd_com = $tel_numero_com = $tel_ddd_cel = $tel_numero_cel = $comment = '';
       
        $xmlString = $order->attribute( 'data_text_1' );
        if ( $xmlString != null )
        {
            $dom = new DOMDocument( '1.0', 'utf-8' );
            $success = $dom->loadXML( $xmlString );

            $nomeNode = $dom->getElementsByTagName( 'nome' )->item( 0 );
            if ( $nomeNode )
            {
                $nome = $nomeNode->textContent;
            }
			
            $emailNode = $dom->getElementsByTagName( 'email' )->item( 0 );
            if ( $emailNode )
            {
                $email = $emailNode->textContent;
            }
            
        	$email2Node = $dom->getElementsByTagName( 'email' )->item( 1 );
            if ( $email2Node )
            {
                $email2 = $email2Node->textContent;
            }
        	$idNode = $dom->getElementsByTagName( 'id' )->item( 0 );
            if ( $idNode )
            {
                $id = $idNode->textContent;
                $idType = $idNode->getAttribute( 'type' );
            }
            
            $logradouroNode = $dom->getElementsByTagName( 'logradouro' )->item( 0 );
            if ( $logradouroNode )
            {
                $logradouro = $logradouroNode->textContent;
                $numero = $logradouroNode->getAttribute( 'numero' );
                $complemento = $logradouroNode->getAttribute( 'complemento' );
            }     
            
            $bairroNode = $dom->getElementsByTagName( 'bairro' )->item( 0 );
            if ( $bairroNode )
            {
                $bairro= $bairroNode->textContent;
            }

            $cidadeNode = $dom->getElementsByTagName( 'cidade' )->item( 0 );
            if ( $cidadeNode )
            {
                $cidade = $cidadeNode->textContent;
            }

            $estadoNode = $dom->getElementsByTagName( 'estado' )->item( 0 );
            if ( $estadoNode )
            {
                $estado = $estadoNode->textContent;
            }

            $cepNode = $dom->getElementsByTagName( 'cep' )->item( 0 );
            if ( $cepNode )
            {
                $cep = $cepNode->textContent;
            }

            $tel_numeroNode = $dom->getElementsByTagName( 'telefone' )->item( 0 );
            if ( $tel_numeroNode )
            {
                $tel_numero = $tel_numeroNode->textContent;
                $tel_ddd = $tel_numeroNode->getAttribute( 'ddd' );
            }

            $tel_numero_comNode = $dom->getElementsByTagName( 'telefone_com' )->item( 0 );
            if ( $tel_numero_comNode )
            {
                $tel_numero_com = $tel_numero_comNode->textContent;
                $tel_ddd_com = $tel_numero_comNode->getAttribute( 'ddd' );
            }

            $tel_numero_celNode = $dom->getElementsByTagName( 'telefone_cel' )->item( 0 );
            if ( $tel_numero_celNode )
            {
                $tel_numero_cel = $tel_numero_celNode->textContent;
                $tel_ddd_cel = $tel_numero_celNode->getAttribute( 'ddd' );
            }      
            
            $commentNode = $dom->getElementsByTagName( 'comment' )->item( 0 );
            if ( $commentNode )
            {
                $comment = $commentNode->textContent;
            }
        }

        return array( 'nome' => $nome,
                      'email' => $email,
				      'email2' => $email2,
                      'id' => $id,
				      'id_type' => $idType,
                      'logradouro' => $logradouro,
                      'numero' => $numero,
                      'complemento' => $complemento,
                      'bairro' => $bairro,        
                      'cidade' => $cidade,
                      'estado' => $estado,
                      'cep' => $cep,
                      'tel_ddd' => $tel_ddd,        
                      'tel_numero' => $tel_numero,
                      'tel_ddd_com' => $tel_ddd_com,        
                      'tel_numero_com' => $tel_numero_com,
                      'tel_ddd_cel' => $tel_ddd_cel,        
                      'tel_numero_cel' => $tel_numero_cel,                
                      'comment' => $comment,
                      );
    }
    public static function isCEP( $CEP )
    {
         return (bool) preg_match('/^([0-9]{2}\.?[0-9]{3})[- ]?([0-9]{3})$/', addcslashes($CEP, "\n"));
    }
	public static function cleanNum( $num )
    {
        return preg_replace( '/\D+/i', '', $num );;
    }
}

?>
