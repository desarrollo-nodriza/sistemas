<?php

App::import('Vendor', 'Chilexpress.ChilexpressSoapClient', array('file' => 'soap/ChilexpressSoapClient.php'));


class IntegracionAsistidaOp {
	var $IntegracionAsistidaRequest;
}

class IntegracionAsistidaRequest {
	var $codigoProducto;
	var $codigoServicio;
	var $comunaOrigen;
	var $numeroTCC;
	var $referenciaEnvio;
	var $referenciaEnvio2;
	var $montoCobrar;
	var $eoc;
	var $Remitente;
	var $Destinatario;
	var $Direccion;
	var $DireccionDevolucion;
	var $Pieza;
}


class Remitente {
	var $nombre;
	var $email;
	var $celular;
}


class Destinatario {
	var $nombre;
	var $email;
	var $celular;
}


class Direccion {
	var $comuna;
	var $calle;
	var $numero;
	var $complemento;
}


class DireccionDevolucion {
	var $comuna;
	var $calle;
	var $numero;
	var $complemento;
}


class Pieza {
	var $peso;
	var $alto;
	var $ancho;
	var $largo;
}

class respGenerarIntegracionAsistida {

}


class OtWS 
{
	var $soapClient;
	var $headerSoap;


	private static $classmap = array(
		'IntegracionAsistidaOp'    => 'IntegracionAsistidaOp',
		'IntegracionAsistidaRequest' => 'IntegracionAsistidaRequest',
		'Remitente'                     => 'Remitente',
		'Destinatario'                  => 'Destinatario',
		'Direccion'                     => 'Direccion',
		'DireccionDevolucion'           => 'DireccionDevolucion',
		'Pieza'                         => 'Pieza',
		'respGenerarIntegracionAsistida' => 'respGenerarIntegracionAsistida'
	);


	function __construct($url='http://qaws.ssichilexpress.cl/OSB/GenerarOTDigitalIndividualC2C?wsdl')
	{
		$this->soapClient = new ChilexpressSoapClient(
			$url,
			array(
				'login'                => Configure::read('Chilexpress.ot.username'),
				'password'             => Configure::read('Chilexpress.ot.password'),
				'classmap'     => self::$classmap, 
				'trace'        => true,
				'exceptions'   => true,
				'soap_version'         => Configure::read('Chilexpress.ot.soap'),
				'location' => $url
			)
		);

		$this->addSoapHeaderWS();
		#prx($this->soapClient);
	}



	function addSoapHeaderWS()
	{	
		$this->headerSoap = array(
			'transaccion' => array(
				'fechaHora'            => date(DATE_ATOM),
				'idTransaccionNegocio' => Configure::read('Chilexpress.ot.negocio'),
				'sistema'              => Configure::read('Chilexpress.ot.sistema'), 
				'login'                => Configure::read('Chilexpress.ot.username'),
				'password'             => Configure::read('Chilexpress.ot.password'),
				'soap_version'         => Configure::read('Chilexpress.ot.soap')
			)
		);

		$headerBody = new SOAPHeader(Configure::read('Chilexpress.ot.namespace'), 'headerRequest', $this->headerSoap);  

		return $this->soapClient->__setSoapHeaders($headerBody);
	}
	 


	function IntegracionAsistidaOp($IntegracionAsistidaOp)
	{
		$funciones = $this->soapClient->__getFunctions();	

		$IntegracionAsistidaRequest = $this->soapClient->IntegracionAsistidaOp($IntegracionAsistidaOp);
		return $IntegracionAsistidaRequest;

	}
}
                       