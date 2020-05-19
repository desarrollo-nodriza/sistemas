<?php 
App::uses('Component', 'Controller');
App::import('Vendor', 'Mandrill', array('file' => 'Mandrill/Mandrill.php'));

class MandrillComponent extends Component
{

	private $apikey;

	/**
	 * [conectar description]
	 * @param  string $apikey Mailchimp apikey
	 * @return void
	 */
	public function conectar($apikey)
	{
		$this->apikey = $apikey;
	}


	/**
	 * [enviar_email via mandrill]
	 * @param  string $html          the full HTML content to be sent
	 * @param  string $asunto        the message subject
	 * @param  array  $remitente     the sender email address and name(optional).
	 * @param  array  $destinatarios an array of recipient information.
	 * @param  string $cabeceras   	 optional extra headers to add to the message (most headers are allowed)
	 * @param  array  $adjuntos      an array of supported attachments to add to the message
	 * @param  array  $imagenes      an array of embedded images to add to the message
	 * @return mixed   
	 */
	public function enviar_email($html = '', $asunto = '', $remitente = array(), $destinatarios = array(), $cabeceras = array(), $adjuntos = array(), $imagenes = array())
	{
		try {

		    $mandrill = new Mandrill($this->apikey);

		    $message = array(
		        'html' => $html,
		        'subject' => $asunto,
		        'from_email' => $remitente['email'],
		        'from_name' => $remitente['nombre'],
		        'to' => $destinatarios,
		        	
		            /*array(
		                'email' => 'recipient.email@example.com',
		                'name' => 'Recipient Name',
		                'type' => 'to'
		            )*/
		        'headers' => $cabeceras,//array('Reply-To' => $responder_a),
		        'important' => true,
		        'track_opens' => null,
		        'track_clicks' => null,
		        'auto_text' => null,
		        'auto_html' => null,
		        'inline_css' => null,
		        'url_strip_qs' => null,
		        'preserve_recipients' => null,
		        'view_content_link' => null,
		        'bcc_address' => 'cristian.rojas@nodriza.cl',
		        'tracking_domain' => null,
		        'signing_domain' => null,
		        'return_path_domain' => null,
		        'merge' => true,
		        'merge_language' => 'mailchimp',
		        'attachments' =>  $adjuntos
		         /*array(
		            array(
		                'type' => 'text/plain',
		                'name' => 'myfile.txt',
		                'content' => 'ZXhhbXBsZSBmaWxl'
		            )
		        )*/,
		        'images' => $imagenes
		        /*array(
		            array(
		                'type' => 'image/png',
		                'name' => 'IMAGECID',
		                'content' => 'ZXhhbXBsZSBmaWxl'
		            )
		        )*/
		    );

		    $async = false;
		    $ip_pool = '';
		    $send_at = '';
		    
		    $result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);
		  	
		    /*
		    Array
		    (
		        [0] => Array
		            (
		                [email] => recipient.email@example.com
		                [status] => sent
		                [reject_reason] => hard-bounce
		                [_id] => abc123abc123abc123abc123abc123
		            )
		    
		    )
		    */
		} catch(Mandrill_Error $e) {
		    // Mandrill errors are thrown as exceptions
		    //echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
		    //exit;
		    // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
		    //throw $e;
		    //
		    return false;
		}

		return true;
	}


	public function ver_email_por_id($id)
	{
		#f25f4188d51a4fb6a0ba7a365315af51 
		try {

			$mandrill = new Mandrill($this->apikey);
			$result   = $mandrill->messages->info($id);

		    /*
		    Array
		    (
		        [ts] => 1365190000
		        [_id] => abc123abc123abc123abc123
		        [sender] => sender@example.com
		        [template] => example-template
		        [subject] => example subject
		        [email] => recipient.email@example.com
		        [tags] => Array
		            (
		                [0] => password-reset
		            )
		    
		        [opens] => 42
		        [opens_detail] => Array
		            (
		                [0] => Array
		                    (
		                        [ts] => 1365190001
		                        [ip] => 55.55.55.55
		                        [location] => Georgia, US
		                        [ua] => Linux/Ubuntu/Chrome/Chrome 28.0.1500.53
		                    )
		    
		            )
		    
		        [clicks] => 42
		        [clicks_detail] => Array
		            (
		                [0] => Array
		                    (
		                        [ts] => 1365190001
		                        [url] => http://www.example.com
		                        [ip] => 55.55.55.55
		                        [location] => Georgia, US
		                        [ua] => Linux/Ubuntu/Chrome/Chrome 28.0.1500.53
		                    )
		    
		            )
		    
		        [state] => sent
		        [metadata] => Array
		            (
		                [user_id] => 123
		                [website] => www.example.com
		            )
		    
		        [smtp_events] => Array
		            (
		                [0] => Array
		                    (
		                        [ts] => 1365190001
		                        [type] => sent
		                        [diag] => 250 OK
		                    )
		    
		            )
		    
		    )
		    */
		} catch(Mandrill_Error $e) {
		    // Mandrill errors are thrown as exceptions
		    //echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
		    // A mandrill error occurred: Mandrill_Unknown_Message - No message exists with the id 'McyuzyCS5M3bubeGPP-XVA'
		    //throw $e;
			return $e->getMessage();
		}

		return $result;
	}

}
