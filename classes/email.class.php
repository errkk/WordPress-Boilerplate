<?php
/**
 * Build an email message using a template, and some variables, and send it to someone
 */
class EmailMessage
{

    private $template = 'general';
    private $data = array( );

    public function __construct( $data, $template = false, $email, $subject )
    {
	$this->data = $data;
	$this->email = $email;
	$this->subject = $subject;
	$this->template = ($template) ? $template : $this->template;
    }
    
    /**
     * Find template tags in a string, and replace them with matching key values in data array
     * @param string $text
     * @param array $data
     * @return string 
     */
    private function rep_templates( $text, $data )
    {
	preg_match_all( '/{\%(\w*)\%\}/', $text, $matches );
	foreach ( $matches[1] as $match ) {
	    
	    
	    if ( array_key_exists( $match, $data ) ) {
		$pattern = "/{\%" . $match . "\%\}/";
		$text = preg_replace( $pattern, $data[$match], $text );
	    }
	}
	return $text;
    }
    
    /**
     * Get email text from options, and put in data
     * @return string 
     */
    public function build_message_text()
    {
	// Get message
	$message_raw = file_get_contents( dirname(dirname(__FILE__)) . '/templates/email/' . $this->template . '.txt' );
	
	// Apply Variables to message
	$text = $this->rep_templates( $message_raw, $this->data );
	
	return nl2br($text);
    }


    
    /**
     * Build the message and send it to this email.
     * @param string $email 
     */
    public function send_email()
    {
	// Make the message
	$message = $this->build_message_text();
	
	$mail = new mail();

	if( defined( 'MAILER' ) ){
	    $mail->Mailer = MAILER;
	}else{
	    $mail->Mailer = 'smtp';
	}
	
	$res = $mail->sendHtml( $this->email, $this->email . $mail->Mailer, $this->subject, $message );
	
	if( $res ){
	    log_message( 'Email ' . $this->template . ' sent to: ' . $this->email, 'Mailform' );
	    return true;
	}else{
	    log_message( 'Email could not be sent to: ' . $this->email, 'Mailform' );
	    return false;
	}
	
	
    }

}
/**
 * Writes to the log file defined in the config
 * @param string $text
 * @param string $log 
 */
function log_message( $text, $log="default" )
{
    $line = gmdate( 'D, d M Y H:i:s' ) . " -- " . $text . "\r\n";
    $handle = fopen( LOGS_PATH . $log . ".txt", "a+" );
    fwrite( $handle, $line );
    fclose( $handle );
}
?>
