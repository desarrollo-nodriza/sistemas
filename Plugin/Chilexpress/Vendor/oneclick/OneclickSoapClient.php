<?php
App::import('Vendor', 'Webpay.XMLSecurityKey', array('file' => 'oneclick/wss/xmlseclibs.php'));
App::import('Vendor', 'Webpay.WSSESoap', array('file' => 'oneclick/wss/soap-wsse.php'));

class OneclickSoapClient extends SoapClient
{
	function __doRequest($request, $location, $saction, $version, $one_way = null)
	{
		$doc = new DOMDocument('1.0');
		$doc->loadXML($request);
		$objWSSE = new WSSESoap($doc);
		$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'private'));
		$objKey->loadKey(Configure::read('Webpay.Oneclick.private_key'), true);
		$options = array('insertBefore' => true);
		$objWSSE->signSoapDoc($objKey, $options);
		$objWSSE->addIssuerSerial(Configure::read('Webpay.Oneclick.cert_file'));
		$objKey = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
		$objKey->generateSessionKey();
		$retVal = parent::__doRequest($objWSSE->saveXML(), $location, $saction, $version, $one_way);
		$doc = new DOMDocument();
		$doc->loadXML($retVal);
		return $doc->saveXML();
	}
}
