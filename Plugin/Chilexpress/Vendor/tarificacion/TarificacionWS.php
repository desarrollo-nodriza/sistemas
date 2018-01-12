<?php

App::import('Vendor', 'Chilexpress.ChilexpressSoapClient', array('file' => 'soap/ChilexpressSoapClient.php'));

class respValorizarCourier{
	var $CodEstado;//int
	var $GlsEstado;//string
	var $CodEstadoInterno;//int
	var $GlsEstadoInterno;//string
	var $Servicios;//Servicios
}


class Servicios{
	var $CodServicio;//int
	var $GlsServicio;//string
	var $IndPesoVolumentrico;//int
	var $PesoCalculo;//decimal
	var $ValorServicio;//decimal
}


class TarificarCourier{
	var $reqValorizarCourier;//reqValorizarCourier
}


class reqValorizarCourier{
	var $CodCoberturaOrigen;//string
	var $CodCoberturaDestino;//string
	var $PesoPza;//decimal
	var $DimAltoPza;//int
	var $DimAnchoPza;//int
	var $DimLargoPza;//int
}


class TarificarCourierResponse{
	var $respValorizarCourier;//respValorizarCourier
}


class TarificacionWS 
{
	var $soapClient;
	var $headerSoap;


	private static $classmap = array(
		'respValorizarCourier'     => 'respValorizarCourier',
		'Servicios'             => 'Servicios',
		'reqValorizarCourier'      => 'reqValorizarCourier',
		'datosTransaccion'         => 'datosTransaccion',
		'datosHeaderResponse'      => 'datosHeaderResponse',
		'datosConsumidor'          => 'datosConsumidor',
		'datosTransaccion'         => 'datosTransaccion',
		'nodoHeader'               => 'nodoHeader',
		'anyNode'                  => 'anyNode',
		'datosHeaderRequest'       => 'datosHeaderRequest',
		'TarificarCourier'         => 'TarificarCourier',
		'TarificarCourierResponse' => 'TarificarCourierResponse'
	);


	function __construct($url='http://qaws.ssichilexpress.cl/TarificarCourier?wsdl')
	{
		$this->soapClient = new ChilexpressSoapClient(
			$url,
			array(
				'login'        => Configure::read('Chilexpress.tarificacion.username'),
				'password'     => Configure::read('Chilexpress.tarificacion.password'),
				'classmap'     => self::$classmap, 
				'trace'        => true,
				'exceptions'   => true,
				'soap_version' => Configure::read('Chilexpress.tarificacion.soap'),
				'location'     => $url,
			)
		);

		$this->addSoapHeaderWS();

	}



	function addSoapHeaderWS()
	{	
		$this->headerSoap = array(
			'transaccion' => array(
				'fechaHora'            => date(DATE_ATOM),
				'idTransaccionNegocio' => Configure::read('Chilexpress.tarificacion.negocio'),
				'sistema'              => Configure::read('Chilexpress.tarificacion.sistema'), 
				'login'                => Configure::read('Chilexpress.tarificacion.username'),
				'password'             => Configure::read('Chilexpress.tarificacion.password'),
				'soap_version'         => Configure::read('Chilexpress.tarificacion.soap')
			)
		);

		$headerBody = new SOAPHeader(Configure::read('Chilexpress.tarificacion.namespace'), 'headerRequest', $this->headerSoap);  

		return $this->soapClient->__setSoapHeaders($headerBody);
	}
	 


	function TarificarCourier($TarificarCourier)
	{
		$TarificarCourierResponse = $this->soapClient->TarificarCourier($TarificarCourier);
		return $TarificarCourierResponse;
	}
}

?>                            