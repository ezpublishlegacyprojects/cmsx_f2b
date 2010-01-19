<?php
/**
 * Dados sobre a cobrança F2b
 * @package cmsxf2b
 * @author Renan Leme <renan@cmsxpert.com.br>
 * @version 0.9
 */
class cmsxF2bTemplateUtils
{
    function __construct()
    {
    }

    function operatorList()
    {
        return array( 'f2b_info_cobranca' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'f2b_info_cobranca' => array( 'order_id' => array( 'type' => 'integer',
                                                                         'required' => true,
                                                                         'default' => '' ),
                                                   ));
    }
    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        $ret = '';

        
        switch ( $operatorName )
        {
            case 'f2b_info_cobranca':
                {
	                $order = eZOrder::fetch( $namedParameters['order_id'] );
	                if ( is_object( $order ) )
	                {
                		$ret = cmsxF2bPaymentGateway::getCobranca( $order );
                 	}  
                } break;
        }
        $operatorValue = $ret;
    }
}

?>