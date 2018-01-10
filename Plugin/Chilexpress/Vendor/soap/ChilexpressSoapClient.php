<?php
App::import('Vendor', 'Chilexpress.XMLSecurityKey', array('file' => 'soap/wss/xmlseclibs.php'));
App::import('Vendor', 'Chilexpress.WSSESoap', array('file' => 'soap/wss/soap-wsse.php'));

class ChilexpressSoapClient extends SoapClient
{
	function __doRequest($request, $location, $saction, $version, $one_way = null)
	{
		$doc = new DOMDocument('1.0');
		$doc->loadXML($request);

		CakeLog::write('debug', $request);

		CakeLog::write('debug', $location);
		
		$objWSSE = new WSSESoap($doc);
		#$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'private'));
		#$objKey->loadKey(Configure::read('Chilexpress.private_key'), true);
		#$options = array('insertBefore' => false);
		#$objWSSE->signSoapDoc($objKey, $options);
		#$objWSSE->addIssuerSerial(Configure::read('Chilexpress.cert_file'));
		#$objKey = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
		#$objKey->generateSessionKey();
		$retVal = parent::__doRequest($request, $location, $saction, $version, $one_way);

		$doc = new DOMDocument();
		$doc->loadXML($retVal);

		return $doc->saveXML();
	}
	
}