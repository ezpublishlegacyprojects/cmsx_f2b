<?php
class cmsxF2bUtils
{
	/**
	 * Log de atividades
	 * 
	 * @var object
	 */
	private static $logger = array();	

    public static function sendEmailToAdmin( $subject = false, $body = false )
    {

        $mail = new eZMail();
        $mail->setReceiver( eZINI::instance( 'f2b.ini' )->variable( 'Settings', 'AdminEmail' ) );
        $mail->setSubject( 'eZ Publish F2b: ' . $subject );
        $mail->setBody( $body );

        // print_r( $mail ); die();
        $mailResult = eZMailTransport::send( $mail );

        return $mailResult;
    }
    public static function toLog( $str, $label = '', $log = null )
    {
	    $log = is_null( $log ) ? 'Billing' : $log;
	    if ( !isset( self::$logger[$log] ) )
	    {
		    self::$logger[$log] = eZPaymentLogger::CreateForAdd( "var/log/f2b{$log}.log" );
	    }
	    self::$logger[$log]->writeTimedString( $str, $label );
    }
}
?>