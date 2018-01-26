<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'Webpay.DiferidoWS', array('file' => 'diferido/DiferidoWS.php'));


class DiferidoComponent extends Component
{
	public $components = array('Session');
	public $Controller = null;
	
	private $DiferidoWS;

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
		$this->DiferidoWS = new DiferidoWS(Configure::read('Webpay.Diferido.endpoint'));
	}

	private function validate() 
	{	
		App::import('Vendor', 'Webpay.SoapValidation', array('file' => 'diferido/wss/soap-validation.php'));
		
		$xmlResponse = $this->DiferidoWS->soapClient->__getLastResponse();
		$soapValidation = new SoapValidation($xmlResponse, Configure::read('Webpay.Diferido.server_cert'));
		
		if ( ! $soapValidation->getValidationResult() )
		{
			throw new Exception('Respuesta de Transbank no está correctamente firmada o el certificado de servidor es incorrecto.');
		}

		CakeLog::write('debug', $xmlResponse);
	}

	public function initTransaccion( $transactionType = null, $commerceId = null, $buyOrder = null, $sessionId = null, $returnUrl = null, $finalUrl = null , $commerceCode = null, $amount = null) 
	{

		$this->connect();

		$wsInitTrasaccionInput = new wsInitTransactionInput();
		$wsInitTrasaccionDetail = new wsTransactionDetail();


		$wsInitTrasaccionInput->wSTransactionType = $transactionType;
		$wsInitTrasaccionInput->commerceId = $commerceId;
		$wsInitTrasaccionInput->buyOrder = $buyOrder;
		$wsInitTrasaccionInput->sessionId = $sessionId;
		$wsInitTrasaccionInput->returnURL = $returnUrl;
		$wsInitTrasaccionInput->finalURL = $finalUrl;

		$wsInitTrasaccionDetail->commerceCode = $commerceCode;
		$wsInitTrasaccionDetail->buyOrder = $buyOrder;
		$wsInitTrasaccionDetail->amount = $amount;

		$wsInitTrasaccionInput->transactionDetails = $wsInitTrasaccionDetail;

		

		$initTransaccionResponse = $this->DiferidoWS->initTransaction(
			array(
				"wsInitTransactionInput" => $wsInitTrasaccionInput
			)
		);

		$this->validate();

		$wsInitTransaccionOutput = $initTransaccionResponse->return;


		$this->Session->write('Webpay.Diferido.initTransaction', $wsInitTransaccionOutput);
		
		//prx($wsInitTransaccionOutput);

		$tokenDiferido = $wsInitTransaccionOutput->token;
		$urlRedirect = $wsInitTransaccionOutput->url;

		$this->Session->write('Compra.token', $tokenDiferido);

		$this->Controller->redirect(array('controller' => 'pagosimultaneo', 'action' => 'transaccion', '?' => array('token' => $tokenDiferido, 'url' => $urlRedirect), 'plugin' => 'webpay'));

	}


	public function getTransaccionResult($token_ws) {

		$this->connect();

		$getTransactionResult = new getTransactionResult();

		$getTransactionResult->tokenInput = $token_ws;

		$getTransactionResultResponse = $this->DiferidoWS->getTransactionResult($getTransactionResult);

		if (!$getTransactionResultResponse) {
			throw new Exception('Error');
		}

		$transactionResultOutput = $getTransactionResultResponse->return;

		$this->validate();

		// URL donde se debe continuar el flujo
		$url = $transactionResultOutput->urlRedirection;

		// Detalles de la respuesta
		$wsTransactionDetailOutput = $transactionResultOutput->detailOutput;

		// Detalles de la tarjeta
		$carddetails = $transactionResultOutput->cardDetail;

		// Código de autorización
		$authorizationCode = $wsTransactionDetailOutput->authorizationCode;

		// Tipo de Pago
		$paymentTypeCode = $wsTransactionDetailOutput->paymentTypeCode;

		// Código de respuesta
		$responseCode = $wsTransactionDetailOutput->responseCode;

		// Número de cuotas
		$sharesNumber = $wsTransactionDetailOutput->sharesNumber;

		// Monto de la transacción
		$amount = $wsTransactionDetailOutput->amount;

		// Código comercio
		$commerceCode = $wsTransactionDetailOutput->commerceCode;

		// Orden de compra enviada por el comercio al inicio de la transacción
		$buyOrder = $wsTransactionDetailOutput->buyOrder;

		// Últimos números de tarjeta
		$cardNumber = $carddetails->cardNumber;

		// Fecha expiración
		$cardExpirationDate = $carddetails->cardExpirationDate;

		// Fecha autorización
		$accountingDate = $transactionResultOutput->accountingDate;

		// Fecha y hora transacción
		$transactionDate = $transactionResultOutput->transactionDate;

		// VCI resultado para comrecios webpay plus
		$vci = $transactionResultOutput->VCI;




		// Código respuesta  es igual a 0, la transacción está autorizada
		if ($responseCode == 0) {
	
				// Enviamos respuesta y los datos de la transacción para registrarlas en nuestro modelo
				$detallesCompra = array(
					'token_ws'					=> $token_ws,
					'url_comprobante'			=> $url,
					'tbk_codigo_autorizacion' 	=> $authorizationCode,
					'tbk_tipo_pago'				=> $paymentTypeCode,
					'tbk_respuesta'				=> $responseCode,
					'tbk_numero_cuotas'			=> $sharesNumber,
					'tbk_monto'					=> $amount,
					'cod_comercio'				=> $commerceCode,
					'tbk_orden_compra'			=> $buyOrder,
					'tbk_final_numero_tarjeta'	=> $cardNumber,
					'tbk_fecha_transaccion'		=> $transactionDate,
					'tbk_vci'					=> $vci

				);
				
			return $detallesCompra;

		}else{

			return $responseCode;
			
		}

	}

	public function acknowledgeTransaccion($token_ws) {	
		$this->connect();
		$acknowledgeTransaction = new acknowledgeTransaction();
		$acknowledgeTransaction->tokenInput = $token_ws;

		$acknowledgeTransactionResponse = $this->DiferidoWS->acknowledgeTransaction($acknowledgeTransaction);
		
		$this->validate();

		return $acknowledgeTransactionResponse;
	
	}

}