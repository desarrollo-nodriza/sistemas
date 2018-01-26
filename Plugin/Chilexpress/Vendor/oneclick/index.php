<?php

require_once('OneclickWS.php');
require_once('wss/soap-validation.php');
require_once('wss/xmlseclibs.php');
require_once('wss/soap-wsse.php');

define('SERVER_CERT', dirname(__FILE__) . '/cert/tbk.pem');

$oneClickService = new OneClickWS('https://webpay3gint.transbank.cl/webpayserver/wswebpay/OneClickPaymentService?wsdl');
$oneClickInscriptionInput = new oneClickInscriptionInput();
$oneClickInscriptionInput->username = 'alvaro@reach-latam.com';
$oneClickInscriptionInput->email = 'alvaro@reach-latam.com';
$oneClickInscriptionInput->responseURL = 'http://dev.brandon.cl/code/oneclick/index.php';
$oneClickInscriptionResponse = $oneClickService->initInscription(array('arg0' => $oneClickInscriptionInput));
$xmlResponse = $oneClickService->soapClient->__getLastResponse();
$soapValidation = new SoapValidation($xmlResponse, SERVER_CERT);
$soapValidation->getValidationResult();//Esto valida si el mensaje está firmado por Transbank
$oneClickInscriptionOutput = $oneClickInscriptionResponse->return; //Esto obtiene el resultado de la operación
$tokenOneClick = $oneClickInscriptionOutput->token; //Token de resultado
$inscriptionURL = $oneClickInscriptionOutput->urlWebpay; //URL para realizar el post


echo '<pre>';
var_dump($soapValidation->getValidationResult());
echo "-----------------------------------------<br>";
print_r($oneClickInscriptionOutput);
echo "-----------------------------------------<br>";
print_r($_GET);
echo "-----------------------------------------<br>";
print_r($_POST);
echo "-----------------------------------------<br>";
echo '</pre>';
?>

<form id="webpay" action="<?= $inscriptionURL; ?>" method="POST">
	<input type="text" name="TBK_TOKEN" value="<?= $tokenOneClick; ?>">
	<input type="submit" value="Inscribir en Oneclick">
</form>
<script type="text/javascript">
//document.getElementById('webpay').submit();
</script>
4051885600446623
