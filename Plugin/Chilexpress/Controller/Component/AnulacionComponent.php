<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'Webpay.AnulacionWS', array('file' => 'anulacion/AnulacionWS.php'));


class AnulacionComponent extends Component
{
	public $components = array('Session');
	public $Controller = null;
	
	private $AnulacionWS;

	public function initialize(Controller $controller)
	{
		$this->Controller = $controller;
		try
		{
			Configure::load('webpay');
		}
		catch ( Exception $e )
		{
			throw new Exception('No se encontró el archivo Config/webpay.php');
		}
	}

	private function connect() 
	{	
		$this->AnulacionWS = new AnulacionWS(Configure::read('Webpay.Anulacion.endpoint'));
	}

	private function validate() 
	{	

		App::import('Vendor', 'Webpay.SoapValidation', array('file' => 'anulacion/wss/soap-validation.php'));

		$xmlResponse = $this->AnulacionWS->soapClient->__getLastResponse();

		$soapValidation = new SoapValidation($xmlResponse, Configure::read('Webpay.Anulacion.server_cert'));
		if ( ! $soapValidation->getValidationResult() )
		{
			throw new Exception('Respuesta de Transbank no está correctamente firmada o el certificado de servidor es incorrecto.');
		}

		CakeLog::write('debug', $xmlResponse);
	}

	/**
	* Método que permite anular una transacción de pago Webpay.
	*
	* @param 	$authorizationCode 		String 			Código de autorización de la transacción que se requiere anular. 
	* 													Para el caso que se esté anulando una transacción de captura en línea, 
	*													este código corresponde al código de autorización de la captura.
	*
	* @param 	$authorizedAmount 		Decimal			Monto autorizado de la transacción que se requiere anular. Para el caso que se 
	* 													esté anulando una transacción de captura en línea, este monto corresponde al monto de la captura.
	*
	* @param 	$buyOrder 				String 			Orden de compra de la transacción que se requiere anular
	*
	* @param 	$commerceId 			Long 			Código de comercio o tienda mall que realizó la transacción
	*
	* @param 	$nullifyAmount 			Decimal 		Monto que se desea anular de la transacción
	* @return Token, authorizationCode, authorizationDate, Balance, nullifiedAmount
	*/
	public function anularCompra($authorizationCode = null, $authorizedAmount = null, $buyOrder = null, $commerceId = null, $nullifyAmount = null) 
	{	
		$this->connect();

		$nullificationInput = new nullificationInput();

		$nullificationInput->authorizationCode = $authorizationCode;

		$nullificationInput->commerceId = $commerceId;

		$nullificationInput->buyOrder = $buyOrder;

		$nullificationInput->authorizedAmount = $authorizedAmount;

		$nullificationInput->nullifyAmount = $nullifyAmount;

		
		$nullificationOutput = $this->AnulacionWS->nullify(
			array( 'nullificationInput' => $nullificationInput )
		);
		
		$this->validate();

		return $nullificationOutput->return;
		
	}

}