<?php

App::import('Vendor', 'Chilexpress.ChilexpressSoapClient', array('file' => 'soap/ChilexpressSoapClient.php'));

class ConsultarRegiones{
}

class ConsultarRegionesResponse{
	var $CodEstado;//int
	var $GlsEstado;//String
	var $idRegion;//string
	var $GlsRegion;//String
}

class ConsultarCoberturas{
	var $CodTipoCobertura;//string
	var $CodRegion;//string
}

class ConsultarCoberturasResponse{
	var $CodEstado;//int
	var $GlsEstado;//string
	var $CodComuna;//string
	var $GLsComuna;//string
	var $CodRegion;//String
	var $CodComunalne;//int
}


class ConsultarCalles{
	var $GlsComuna;//string
	var $GlsCalle;//string
}

class ConsultarCallesResponse{
	var $CodEstado;//int
	var $GlsEstado;//string
	var $Calles;//Calles
	var $idNombreCalle;//string
	var $GlsComuna;//string
	var $GlsCalle;//string
}

class ConsultarNumeros{
	var $idNombreCalle;//string
	var $NumNumeracion;//int
}

class ConsultarNumerosResponse{
	var $CodEstado;//int
	var $GlsEstado;//string
	var $CodCAN;//string
	var $NumNumeracion;//int
	var $CodCPN;//string
}

class ValidacionDireccion{
	var $GlsComuna;//string
	var $CodCalle;//string
	var $CodCAN;//string
	var $NumNumeracion;//int
	var $CodCPN;//string
	var $CodComplemento;//string
}

class ValidacionDireccionResponse{
	var $CodEstado;//int
	var $GlsEstado;//string
	var $CodResultado;//int
	var $Iddireccion;//int
	var $idBlock;//int
	var $Lat;//float
	var $Lon;//float
}


class ConsultarOficinas{
	var $GlsComuna;//string
}

class ConsultarOficinasResponse{
	var $lpResultado;//int
	var $NombreCalle;//string
	var $NombreOficina;//string
	var $Numeracion;//int
	var $CaN;//string
	var $CpN;//string
	var $NombreComuna;//string
	var $Iddireccion;//int
	var $idBlock;//int
	var $Lat;//float
	var $Lon;//float
}

class ConsultarOficinas_REG{
	var $CodRegion;//string
}

class ConsultarOficinas_REGResponse{
	var $lpResultado;//int
	var $NombreCalle;//string
	var $NombreOficina;//string
	var $Numeracion;//int
	var $CaN;//string
	var $CpN;//string
	var $NombreComuna;//string
	var $Iddireccion;//int
	var $idBlock;//int
	var $Lat;//float
	var $Lon;//float
	var $GlsCalle;//string
	var $GlsComuna;//string
	var $CodComunaCar;//string
	var $GlsAliasOficina;//string
}


class GeoReferenciaWS {
	var $soapClient;
	 
	private static $classmap = array(
		'ConsultarCoberturas'=>'ConsultarCoberturas',
		'ConsultarCoberturasResponse'=>'ConsultarCoberturasResponse',
		'ConsultarCalles' => 'ConsultarCalles',
		'ConsultarCallesResponse' => 'ConsultarCallesResponse',
		'ConsultarNumeros' => 'ConsultarNumeros',
		'ConsultarNumerosResponse' => 'ConsultarNumerosResponse',
		'ValidacionDireccion' => 'ValidacionDireccion',
		'ValidacionDireccionResponse' => 'ValidacionDireccionResponse',
		'ConsultarOficinas' => 'ConsultarOficinas',
		'ConsultarOficinasResponse' => 'ConsultarOficinasResponse',
		'ConsultarOficinas_REG' => 'ConsultarOficinas_REG',
		'ConsultarOficinas_REGResponse' => 'ConsultarOficinas_REGResponse'
	);

	function __construct($url='http://qaws.ssichilexpress.cl/GeoReferencia?wsdl')
	{
		$this->soapClient = new ChilexpressSoapClient($url,array("classmap"=>self::$classmap,"trace" => true,"exceptions" => true));
	}

	function ConsultarCoberturas($ConsultarCoberturas)
	{

		$ConsultarCoberturasResponse = $this->soapClient->ConsultarCoberturas($ConsultarCoberturas);
		return $ConsultarCoberturasResponse;

	}

	function ConsultarRegiones($ConsultarRegiones)
	{
		try {
			$ConsultarRegionesResponse = $this->soapClient->ConsultarRegiones($ConsultarRegiones);
		} catch (Exception $e) {
			$ConsultarRegionesResponse = $e->getMessage();
		}
		
		return $ConsultarRegionesResponse;

	}

	function ConsultarCalles($ConsultarCalles)
	{

		$ConsultarCallesResponse = $this->soapClient->ConsultarCalles($ConsultarCalles);
		return $ConsultarCallesResponse;

	}

	function ConsultarNumeros($ConsultarNumeros)
	{

		$ConsultarNumerosResponse = $this->soapClient->ConsultarNumeros($ConsultarNumeros);
		return $ConsultarNumerosResponse;

	}

	function ValidacionDireccion($ValidacionDireccion)
	{

		$ValidacionDireccionResponse = $this->soapClient->ValidacionDireccion($ValidacionDireccion);
		return $ValidacionDireccionResponse;

	}

	function ConsultarOficinas($ConsultarOficinas)
	{

		$ConsultarOficinasResponse = $this->soapClient->ConsultarOficinas($ConsultarOficinas);
		return $ConsultarOficinasResponse;

	}

	function ConsultarOficinas_REG($ConsultarOficinas_REG)
	{

		$ConsultarOficinas_REGResponse = $this->soapClient->ConsultarOficinas_REG($ConsultarOficinas_REG);
		return $ConsultarOficinas_REGResponse;

	}
}
?>                            