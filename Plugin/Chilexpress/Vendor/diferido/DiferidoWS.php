<?php

// Carga Verificador de firmas
App::import('Vendor', 'Webpay.DiferidoSoapClient', array('file' => 'diferido/DiferidoSoapClient.php'));

class getTransactionResult{
var $tokenInput;//string
}
class getTransactionResultResponse{
var $return;//transactionResultOutput
}
class transactionResultOutput{
var $accountingDate;//string
var $buyOrder;//string
var $cardDetail;//cardDetail
var $detailOutput;//wsTransactionDetailOutput
var $sessionId;//string
var $transactionDate;//dateTime
var $urlRedirection;//string
var $VCI;//string
}
class cardDetail{
var $cardNumber;//string
var $cardExpirationDate;//string
}
class wsTransactionDetailOutput{
var $authorizationCode;//string
var $paymentTypeCode;//string
var $responseCode;//int
}
class wsTransactionDetail{
var $sharesAmount;//decimal
var $sharesNumber;//int
var $amount;//decimal
var $commerceCode;//string
var $buyOrder;//string
}
class acknowledgeTransaction{
var $tokenInput;//string
}
class acknowledgeTransactionResponse{
}
class initTransaction{
var $wsInitTransactionInput;//wsInitTransactionInput
}
class wsInitTransactionInput{
var $wSTransactionType;//wsTransactionType
var $commerceId;//string
var $buyOrder;//string
var $sessionId;//string
var $returnURL;//anyURI
var $finalURL;//anyURI
var $transactionDetails;//wsTransactionDetail
var $wPMDetail;//wpmDetailInput
}
class wpmDetailInput{
var $serviceId;//string
var $cardHolderId;//string
var $cardHolderName;//string
var $cardHolderLastName1;//string
var $cardHolderLastName2;//string
var $cardHolderMail;//string
var $cellPhoneNumber;//string
var $expirationDate;//dateTime
var $commerceMail;//string
var $ufFlag;//boolean
}
class initTransactionResponse{
var $return;//wsInitTransactionOutput
}
class wsInitTransactionOutput{
var $token;//string
var $url;//string
}
class DiferidoWS 
 {
 var $soapClient;
 
private static $classmap = array('getTransactionResult'=>'getTransactionResult'
,'getTransactionResultResponse'=>'getTransactionResultResponse'
,'transactionResultOutput'=>'transactionResultOutput'
,'cardDetail'=>'cardDetail'
,'wsTransactionDetailOutput'=>'wsTransactionDetailOutput'
,'wsTransactionDetail'=>'wsTransactionDetail'
,'acknowledgeTransaction'=>'acknowledgeTransaction'
,'acknowledgeTransactionResponse'=>'acknowledgeTransactionResponse'
,'initTransaction'=>'initTransaction'
,'wsInitTransactionInput'=>'wsInitTransactionInput'
,'wpmDetailInput'=>'wpmDetailInput'
,'initTransactionResponse'=>'initTransactionResponse'
,'wsInitTransactionOutput'=>'wsInitTransactionOutput'

);

 function __construct($url = null)
 {
  $this->soapClient = new DiferidoSoapClient($url,array("classmap"=>self::$classmap,"trace" => true,"exceptions" => true));
 }
 
function getTransactionResult($getTransactionResult)
{
	try {
		$getTransactionResultResponse = $this->soapClient->getTransactionResult($getTransactionResult);	
		return $getTransactionResultResponse;
	} catch (Exception $e) {
		return false;
	}
	

}
function acknowledgeTransaction($acknowledgeTransaction)
{
	try {
		$acknowledgeTransactionResponse = $this->soapClient->acknowledgeTransaction($acknowledgeTransaction);
		return $acknowledgeTransactionResponse;
	} catch (Exception $e) {
		return false;
	}
	

}
function initTransaction($initTransaction)
{
	try {
		$initTransactionResponse = $this->soapClient->initTransaction($initTransaction);
		return $initTransactionResponse;
	} catch (Exception $e) {
		return false;
	}
	

}

}
?>