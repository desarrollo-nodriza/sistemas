<?php

App::import('Vendor', 'Chilexpress.ChilexpressSoapClient', array('file' => 'soap/ChilexpressSoapClient.php'));

##
# Regiones
#
class ConsultarRegiones{
	var $reqObtenerRegion;
}

class reqObtenerRegion{
	var $CodEstado;//int
	var $GlsEstado;//String
	var $Regiones;
	
}

class Regiones {
	var $idRegion;
	var $GlsRegion;//String
}

##
# Fin regiones
# 


##
# Comunas / Coberturas
# 
class ConsultarCoberturas{
	var $reqObtenerCobertura;
}

class reqObtenerCobertura{
	var $CodEstado;//int
	var $GlsEstado;//string
	var $CodTipoCobertura;//string
	var $CodRegion;//string
	var $Comunas;
}

Class Comunas {
	var $CodComuna;//string
	var $GLsComuna;//string
	var $CodRegion;//String
	var $CodComunalne;//int
}

##
# Fin Comunas
# 


##
# Calles
# 
class ConsultarCalles{
	var $reqObtenerCalle;
}

class reqObtenerCalle{
	var $GlsComuna;//string
	var $GlsCalle;//string

	var $CodEstado;//int
	var $GlsEstado;//string
	var $Calles;//Calles
}

class Calles {
	var $idNombreCalle;//string
	var $GlsComuna;//string
	var $GlsCalle;//string
}

##
# Fin calles
# 


##
# Numeracion
# 
class ConsultarNumeros{
   	var $reqObtenerNumero;
}

class reqObtenerNumero{
	var $CodEstado;//int
	var $GlsEstado;//string

	var $idNombreCalle;//string
	var $Numeracion;//int
	var $Numeros;
}

class Numeros {
	var $CaN;//string
	var $Numeracion;//int
	var $CpN;//string
}

##
# Fin números
# 


##
# Validar direccion
# 
class ConsultarDirecciones{
	var $reqObtenerDireccion;
}

class reqObtenerDireccion{
	var $CodEstado;//int
	var $GlsEstado;//string
	
	var $GlsComuna;//string
	var $GlsCalle;//string
	var $CaN;//string
	var $Numeracion;//int
	var $CpN;//string
	var $Complemento;//string
	var $Direcciones;
}

class Direcciones {
	var $CodResultado;//int
	var $Iddireccion;//int
	var $idBlock;//int
	var $Lat;//float
	var $Lon;//float
}

##
# Fin validar dirección
# 


##
# Consultar direcciones
class ConsultarOficinas{
	var $GlsComuna;//string

}

class reqObtenerOficinas{
	var $lpResultado;//int
	var $Oficinas;
}

class Oficinas {

}
##
# Fin oficinas comuna
# 



##
# Consultar oficinas por región
# 
class ConsultarOficinas_REG{
	var $CodRegion;//string
}

class reqObtenerOficinas_REG{
	var $lpResultado;//int
	var $CodRegion;
	var $OficinasRegion;
}

Class OficinasRegion {
	
}


class GeoReferenciaWS {

	var $soapClient;

	var $headerSoap;
	 
	private static $classmap = array(
		'ConsultarRegiones'      =>'ConsultarRegiones',
		'reqObtenerRegion'       =>'reqObtenerRegion',
		'Regiones'               =>'Regiones',
		'ConsultarCoberturas'    =>'ConsultarCoberturas',
		'reqObtenerCobertura'    =>'reqObtenerCobertura',
		'Comunas'                => 'Comunas',
		'ConsultarCalles'        => 'ConsultarCalles',
		'reqObtenerCalle'        => 'reqObtenerCalle',
		'Calles'                 => 'Calles',
		'ConsultarNumeracion'    => 'ConsultarNumeros',
		'reqObtenerNumero'       => 'reqObtenerNumero',
		'Numeros'                => 'Numeros',
		'ConsultarDirecciones'   => 'ConsultarDirecciones',
		'reqObtenerDireccion'    => 'reqObtenerDireccion',
		'ConsultarOficinas'      => 'ConsultarOficinas',
		'reqObtenerOficinas'     => 'reqObtenerOficinas',
		'Oficinas'               => 'Oficinas',
		'ConsultarOficinas_REG'  => 'ConsultarOficinas_REG',
		'reqObtenerOficinas_REG' => 'reqObtenerOficinas_REG',
		'OficinasRegion'         => 'OficinasRegion'
	);

	function __construct($url='http://qaws.ssichilexpress.cl/GeoReferencia?wsdl')
	{
		$this->soapClient = new ChilexpressSoapClient(
			$url,
			array(
				'login'        => Configure::read('Chilexpress.georeferencia.username'),
				'password'     => Configure::read('Chilexpress.georeferencia.password'),
				'classmap'     => self::$classmap, 
				'trace'        => true,
				'exceptions'   => true,
				'soap_version' => Configure::read('Chilexpress.georeferencia.soap'),
				'location'     => $url,
			)
		);

		$this->addSoapHeaderWS();
	}


	function setSoapHeaderWS( array $data )
	{
		$this->headerSoap = $data;
	}


	function addSoapHeaderWS()
	{	
		$this->headerSoap = array(
			'transaccion' => array(
				'fechaHora'            => date(DATE_ATOM),
				'idTransaccionNegocio' => Configure::read('Chilexpress.georeferencia.negocio'),
				'sistema'              => Configure::read('Chilexpress.georeferencia.sistema'), 
				'login'                => Configure::read('Chilexpress.georeferencia.username'),
				'password'             => Configure::read('Chilexpress.georeferencia.password'),
				'soap_version'         => Configure::read('Chilexpress.georeferencia.soap')
			)
		);

		$headerBody = new SOAPHeader(Configure::read('Chilexpress.georeferencia.namespace'), 'headerRequest', $this->headerSoap);  

		return $this->soapClient->__setSoapHeaders($headerBody);
	}

	function ConsultarCoberturas($ConsultarCoberturas)
	{

		$reqObtenerCobertura = $this->soapClient->ConsultarCoberturas($ConsultarCoberturas);
		return $reqObtenerCobertura;

	}

	function ConsultarRegiones($ConsultarRegiones)
	{	
		$reqObtenerRegion = $this->soapClient->ConsultarRegiones($ConsultarRegiones);
		return $reqObtenerRegion;

	}

	function ConsultarCalles($ConsultarCalles)
	{

		$reqObtenerCalle = $this->soapClient->ConsultarCalles($ConsultarCalles);
		return $reqObtenerCalle;

	}

	function ConsultarNumeros($ConsultarNumeros)
	{

		$reqObtenerNumero = $this->soapClient->ConsultarNumeros($ConsultarNumeros);
		return $reqObtenerNumero;

	}

	function ConsultarDirecciones($ConsultarDirecciones)
	{

		$reqObtenerDireccion = $this->soapClient->ConsultarDirecciones($ConsultarDirecciones);
		return $reqObtenerDireccion;

	}

	function ConsultarOficinas($ConsultarOficinas)
	{

		$reqObtenerOficinas = $this->soapClient->ConsultarOficinas($ConsultarOficinas);
		return $reqObtenerOficinas;

	}

	function ConsultarOficinas_REG($ConsultarOficinas_REG)
	{

		$reqObtenerOficinas_REG = $this->soapClient->ConsultarOficinas_REG($ConsultarOficinas_REG);
		return $reqObtenerOficinas_REG;

	}
}
?>                            