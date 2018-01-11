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
		$objWSSE->signAllHeaders = true;
		$objWSSE->addTimestamp();
		if (isset($this->_login, $this->_password)) {
			$objWSSE->addUserToken($this->_login, $this->_password);	
		}
		$retVal = parent::__doRequest($objWSSE->saveXML(), $location, $saction, $version, $one_way);

		$doc = new DOMDocument();
		$doc->loadXML($retVal);

		return $doc->saveXML();
	}
	
}