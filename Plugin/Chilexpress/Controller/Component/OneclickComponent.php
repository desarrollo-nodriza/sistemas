<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'Webpay.OneclickWS', array('file' => 'oneclick/OneclickWS.php'));
App::import('Vendor', 'Webpay.SoapValidation', array('file' => 'oneclick/wss/soap-validation.php'));

class OneclickComponent extends Component
{
	public $components = array('Session');
	public $Controller = null;
	private $OneclickWS;

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
		$this->OneclickWS = new OneclickWS(Configure::read('Webpay.Oneclick.endpoint'));
	}

	private function validate()
	{
		$xmlResponse = $this->OneclickWS->soapClient->__getLastResponse();
		$soapValidation = new SoapValidation($xmlResponse, Configure::read('Webpay.Oneclick.server_cert'));
		if ( ! $soapValidation->getValidationResult() )
		{
			throw new Exception('Respuesta de Oneclick no está correctamente firmada o el certificado de servidor es incorrecto.');
		}
	}

	public function initInscripcion($username = null, $email = null, $responseURL = null)
	{
		if ( ! $username || ! $email || ! $responseURL )
		{
			throw new Exception('Faltan datos de entrada ($username, $email, $responseURL)');
		}

		/**
		 * Abre conexión al WS
		 */
		$this->connect();

		/**
		 * Datos de inscripción
		 */
		$oneClickInscriptionInput = new oneClickInscriptionInput();
		$oneClickInscriptionInput->username = $username;
		$oneClickInscriptionInput->email = $email;
		$oneClickInscriptionInput->responseURL = $responseURL;

		/**
		 * Envia el request al WS
		 */
		$oneClickInscriptionResponse = $this->OneclickWS->initInscription(array('arg0' => $oneClickInscriptionInput));

		/**
		 * Obtiene y valida la respuesta del WS
		 */
		$this->validate();

		/**
		 * Procesa la respuesta
		 */
		$oneClickInscriptionOutput = $oneClickInscriptionResponse->return;
		$this->Session->write('Webpay.Oneclick.initInscripcion', $oneClickInscriptionOutput);
		$tokenOneClick = $oneClickInscriptionOutput->token;
		$inscriptionURL = $oneClickInscriptionOutput->urlWebpay;

		$this->Controller->redirect(array('controller' => 'oneclick', 'action' => 'inscripcion', '?' => array('token' => $tokenOneClick, 'url' => $inscriptionURL), 'plugin' => 'webpay'));
	}

	public function finishInscripcion($token = null)
	{
		if ( ! $token )
		{
			throw new Exception('Faltan datos de entrada ($token)');
		}

		/**
		 * Abre conexión al WS
		 */
		$this->connect();

		/**
		 * Datos de inscripción
		 */
		$oneClickFinishInscriptionInput = new oneClickFinishInscriptionInput();
		$oneClickFinishInscriptionInput->token = $token;

		/**
		 * Envia el request al WS
		 */
		$oneClickFinishInscriptionResponse = $this->OneclickWS->finishInscription(array('arg0' => $oneClickFinishInscriptionInput));

		/**
		 * Obtiene y valida la respuesta del WS
		 */
		$this->validate();

		/**
		 * Procesa la respuesta
		 */
		$oneClickFinishInscriptionOutput = $oneClickFinishInscriptionResponse->return;
		$this->Session->write('Webpay.Oneclick.finishInscripcion', $oneClickFinishInscriptionOutput);

		/**
		 * Verifica codigos de error
		 */
		if ( $oneClickFinishInscriptionOutput->responseCode != 0 )
		{
			throw new Exception(sprintf('Proceso de inscripción rechazado por Oneclick. Código de error: %s', $oneClickFinishInscriptionOutput->responseCode));
		}

		return $oneClickFinishInscriptionOutput;
	}

	public function removeUser($tbkUser = null, $username = null)
	{
		if ( ! $tbkUser || ! $username )
		{
			throw new Exception('Faltan datos de entrada ($tbkUser, $username)');
		}

		/**
		 * Abre conexión al WS
		 */
		$this->connect();

		/**
		 * Datos de inscripción
		 */
		$oneClickReverseInput = new oneClickPayInput();
		$oneClickReverseInput->tbkUser = $tbkUser;
		$oneClickReverseInput->username = $username;

		/**
		 * Envia el request al WS
		 */
		$oneClickRemoveUserResponse = $this->OneclickWS->removeUser(array('arg0' => $oneClickRemoveUserInput));

		/**
		 * Obtiene y valida la respuesta del WS
		 */
		$this->validate();

		/**
		 * Procesa la respuesta
		 */
		$oneClickRemoveUserOutput = $oneClickRemoveUserResponse->return;
		$this->Session->write('Webpay.Oneclick.removeUser', (bool) $oneClickRemoveUserOutput);
		return $oneClickRemoveUserOutput;
	}

	public function authorize($amount = null, $buyOrder = null, $tbkUser = null, $username = null)
	{
		if ( ! $amount || ! $buyOrder || ! $tbkUser || ! $username )
		{
			throw new Exception('Faltan datos de entrada ($amount, $buyOrder, $tbkUser, $username)');
		}

		/**
		 * Abre conexión al WS
		 */
		$this->connect();

		/**
		 * Datos de inscripción
		 */
		$oneClickPayInput = new oneClickPayInput();
		$oneClickPayInput->amount = $amount;
		$oneClickPayInput->buyOrder = $buyOrder;
		$oneClickPayInput->tbkUser = $tbkUser;
		$oneClickPayInput->username = $username;

		/**
		 * Envia el request al WS
		 */
		$authorizeResponse = $this->OneclickWS->authorize(array('arg0' => $oneClickPayInput));
		if ( ! is_object($authorizeResponse) )
		{
			throw new Exception('Error al realizar pago');
		}

		/**
		 * Obtiene y valida la respuesta del WS
		 */
		$this->validate();

		/**
		 * Procesa la respuesta
		 */
		$oneClickPayOutput = $authorizeResponse->return;
		$this->Session->write('Webpay.Oneclick.authorize', $oneClickPayOutput);
		return $oneClickPayOutput;
	}

	public function codeReverseOneClick($buyorder = null)
	{
		if ( ! $buyorder )
		{
			throw new Exception('Faltan datos de entrada ($buyorder)');
		}

		/**
		 * Abre conexión al WS
		 */
		$this->connect();

		/**
		 * Datos de inscripción
		 */
		$oneClickReverseInput = new oneClickReverseInput();
		$oneClickReverseInput->buyorder = $buyorder;

		/**
		 * Envia el request al WS
		 */
		$codeReverseOneClickResponse = $this->OneclickWS->codeReverseOneClick(array('arg0' => $oneClickReverseInput));

		/**
		 * Obtiene y valida la respuesta del WS
		 */
		$this->validate();

		/**
		 * Procesa la respuesta
		 */
		$oneClickReverseOutput = $codeReverseOneClickResponse->return;
		debug( (int) $oneClickReverseOutput->reverseCode );
		$this->Session->write('Webpay.Oneclick.codeReverseOneClick', $oneClickReverseOutput);
		return $oneClickReverseOutput;
	}
}
