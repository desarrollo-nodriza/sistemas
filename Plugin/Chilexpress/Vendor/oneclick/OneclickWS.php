<?php
App::import('Vendor', 'Webpay.OneclickSoapClient', array('file' => 'oneclick/OneclickSoapClient.php'));

class removeUser {
	var $arg0;
}
class oneClickRemoveUserInput {
	var $tbkUser;
	var $username;
}
class baseBean {
}
class removeUserResponse {
	var $return;
}
class initInscription {
	var $arg0;
}
class oneClickInscriptionInput {
	var $email;
	var $responseURL;
	var $username;
}
class initInscriptionResponse {
	var $return;
}
class oneClickInscriptionOutput {
	var $token;
	var $urlWebpay;
}
class finishInscription {
	var $arg0;
}
class oneClickFinishInscriptionInput {
	var $token;
}
class finishInscriptionResponse {
	var $return;
}
class oneClickFinishInscriptionOutput {
	var $authCode;
	var $creditCardType;
	var $last4CardDigits;
	var $responseCode;
	var $tbkUser;
}
class codeReverseOneClick {
	var $arg0;
}
class oneClickReverseInput {
	var $buyorder;
}
class codeReverseOneClickResponse {
	var $return;
}
class oneClickReverseOutput {
	var $reverseCode;
	var $reversed;
}
class authorize {
	var $arg0;
}
class oneClickPayInput {
	var $amount;
	var $buyOrder;
	var $tbkUser;
	var $username;
}
class authorizeResponse {
	var $return;
}
class oneClickPayOutput {
	var $authorizationCode;
	var $creditCardType;
	var $last4CardDigits;
	var $responseCode;
	var $transactionId;
}
class reverse {
	var $arg0;
}
class reverseResponse {
	var $return;
}
class OneclickWS {
	var $soapClient;

	private static $classmap = array(
		'removeUser'						=> 'removeUser',
		'oneClickRemoveUserInput'			=> 'oneClickRemoveUserInput',
		'baseBean'							=> 'baseBean',
		'removeUserResponse'				=> 'removeUserResponse',
		'initInscription'					=> 'initInscription',
		'oneClickInscriptionInput'			=> 'oneClickInscriptionInput',
		'initInscriptionResponse'			=> 'initInscriptionResponse',
		'oneClickInscriptionOutput'			=> 'oneClickInscriptionOutput',
		'finishInscription'					=> 'finishInscription',
		'oneClickFinishInscriptionInput'	=> 'oneClickFinishInscriptionInput',
		'finishInscriptionResponse'			=> 'finishInscriptionResponse',
		'oneClickFinishInscriptionOutput'	=> 'oneClickFinishInscriptionOutput',
		'codeReverseOneClick'				=> 'codeReverseOneClick',
		'oneClickReverseInput'				=> 'oneClickReverseInput',
		'codeReverseOneClickResponse'		=> 'codeReverseOneClickResponse',
		'oneClickReverseOutput'				=> 'oneClickReverseOutput',
		'authorize'							=> 'authorize',
		'oneClickPayInput'					=> 'oneClickPayInput',
		'authorizeResponse'					=> 'authorizeResponse',
		'oneClickPayOutput'					=> 'oneClickPayOutput',
		'reverse'							=> 'reverse',
		'reverseResponse'					=> 'reverseResponse'
	);

	public function __construct($url = null)
	{
		$this->soapClient = new OneclickSoapClient($url, array('classmap' => self::$classmap, 'trace' => true, 'exceptions' => true));
	}
	public function removeUser($removeUser)
	{
		return $this->soapClient->removeUser($removeUser);
	}
	public function initInscription($initInscription)
	{
		return $this->soapClient->initInscription($initInscription);
	}
	public function finishInscription($finishInscription)
	{
		return $this->soapClient->finishInscription($finishInscription);
	}
	public function authorize($authorize)
	{
		try { return $this->soapClient->authorize($authorize); } catch ( Exception $e ) { return false; }
	}
	public function codeReverseOneClick($codeReverseOneClick)
	{
		return $this->soapClient->codeReverseOneClick($codeReverseOneClick);
	}
	public function reverse($reverse)
	{
		return $this->soapClient->reverse($reverse);
	}
}
