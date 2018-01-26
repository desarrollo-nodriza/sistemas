                                                                    <?php

App::import('Vendor', 'Chilexpress.ChilexpressSoapClient', array('file' => 'soap/ChilexpressSoapClient.php'));

class Coberturas{
	var $CodComuna;//string
	var $GlsComuna;//string
	var $CodRegion;//string
	var $CodComunaIne;//int
}

class respObtenerCobertura {
	var $CodEstado;//int
	var $GlsEstado;//string
	var $Coberturas;// Coberturas
}

class ConsultarCoberturasResponse{
	var $respObtenerCobertura;//ConsultarCoberturas
}


class reqObtenerCobertura {
	var $CodTipoCobertura;//string
	var $CodRegion;//string
	
}

class ConsultarCoberturas{
	var $reqObtenerCobertura;//ConsultarCoberturas
}



class Regiones{
	var $idRegion;//string
	var $GlsRegion;//string
}

class respObtenerRegion {
	var $CodEstado;//int
	var $GlsEstado;//string
	var $Regiones;//Regiones
}

class ConsultarRegionesResponse{
	var $respObtenerRegion;//ConsultarRegiones
}

class reqObtenerRegion {

}

class ConsultarRegiones{
	var $reqObtenerRegion;//ConsultarRegiones
}



class Calles{
	var $idNombreCalle;//string
	var $GlsComuna;//string
	var $GlsCalle;//string
}

class respObtenerCalle{
	var $CodEstado;//int
	var $GlsEstado;//string
	var $Calles;//Calles
}

class ConsultarCallesResponse{
	var $respObtenerCalle;//ConsultarCalles
}

class reqObtenerCalle {
	var $GlsComuna;//string
	var $GlsCalle;//string
}

class ConsultarCalles{
	var $reqObtenerCalle;//ConsultarCalles
}



class Numeros{
	var $CaN;//string
	var $Numeracion;//int
	var $CpN;//string
}

class respObtenerNumero {
	var $CodEstado;//int
	var $GlsEstado;//string
	var $Numeros;//Numeros
}

class ConsultarNumerosResponse{
	var $respObtenerNumero;//ConsultarNumeros
}

class reqObtenerNumero {
	var $idNombreCalle;//string
	var $Numeracion;//int
}

class ConsultarNumeros{
	var $reqObtenerNumero;//ConsultarNumeros
}


class Direcciones{
	var $CodResultado;//int
	var $idDireccion;//int
	var $idBlock;//int
	var $Lat;//float
	var $Lon;//float
}

class respObtenerDireccion {
	var $CodEstado;//int
	var $GlsEstado;//string
	var $Direcciones;//Direcciones
}

class ConsultarDireccionesResponse{
	var $respObtenerDireccion;//ConsultarDirecciones
}

class reqObtenerDireccion {
	var $GlsComuna;//string
	var $GlsCalle;//string
	var $CaN;//string
	var $Numeracion;//int
	var $CpN;//string
	var $Complemento;//string
}

class ConsultarDirecciones{
	var $reqObtenerDireccion;//ConsultarDirecciones
}



class OficionasComuna{
	var $NombreCalle;//string
	var $NombreOficina;//string
	var $Numeracion;//int
	var $CaN;//string
	var $CpN;//string
	var $NombreComuna;//string
	var $IdDireccion;//int
	var $IdBlock;//int
	var $Lat;//float
	var $Lon;//float
}

class respObtenerOficinas {
	var $CodEstado;//int
	var $GlsEstado;//string
	var $OficinasComunas;//Calles
}

class ConsultarOficinasResponse{
	var $respObtenerOficinas;//ConsultarOficinas
}

class reqObtenerOficinas {
	var $GlsComuna;//string
}

class ConsultarOficinas{
	var $reqObtenerOficinas;//ConsultarOficinas
}



class OficinasRegion {
	var $idCalle;//string
	var $NombreOficina;//string
	var $Numeracion;//int
	var $CaN;//string
	var $CpN;//string
	var $CodComunaIne;//string
	var $IdDireccion;//int
	var $IdBlock;//int
	var $Lat;//float
	var $Lon;//float
	var $GlsCalle;//string
	var $GlsComuna;//string
	var $CodComuna;//string
	var $GlsAlias;//string
}

class respObtenerOficinas_REG {
	var $OficinasRegion;
}

class ConsultarOficinas_REGResponse{
	var $respObtenerOficinas_REG;//ConsultarOficinas_REG
}

class reqObtenerOficinas_REG {
	var $CodRegion;//string
}

class ConsultarOficinas_REG{
	var $reqObtenerOficinas_REG;//ConsultarOficinas_REG
}

/*
class ConsultarLocalidades{
}
class ConsultarLocalidades{
var $CodEstado;//int
var $GlsEstado;//string
var $Localidades;//Localidades
}
class Localidades{
var $CodComunaIne;//string
var $CodOficina;//string
var $CodScalfa;//string
var $GlsComuna;//string
var $GlsSector;//string
var $IdRegion;//string
}
class ConsultarCallesFiltro{
var $GlsComuna;//string
var $GlsCalle;//string
var $IndPuntoInteres;//boolean
var $CodTipoBusqueda;//int
var $CantResultados;//int
}
class ConsultarCallesFiltro{
var $CodEstado;//int
var $GlsEstado;//string
var $Calles;//Calle
}
class Calle{
var $idNombreCalle;//string
var $GlsComuna;//string
var $GlsCalle;//string
var $GlsTipoVia;//string
}

class ConsultarLocalidades{
var $reqObtenerLocalidades;//ConsultarLocalidades
}
class ConsultarLocalidadesResponse{
var $respObtenerLocalidades;//ConsultarLocalidades
}
class ConsultarCallesFiltroRequest{
var $reqConsultarCallesFiltro;//ConsultarCallesFiltro
}
class ConsultarCallesFiltroResponse{
var $respConsultarCallesFiltro;//ConsultarCallesFiltro
}
*/

class GeoReferenciaWS 
{
	var $soapClient;
	var $headerSoap;

	function __construct($url='http://qaws.ssichilexpress.cl/GeoReferencia?wsdl')
	{	
	 		$this->soapClient = new ChilexpressSoapClient(
				$url,
				array(
					'login'        => Configure::read('Chilexpress.georeferencia.username'),
					'password'     => Configure::read('Chilexpress.georeferencia.password'),
					'trace'        => true,
					'exceptions'   => true,
					'soap_version' => Configure::read('Chilexpress.georeferencia.soap'),
					'location'     => $url
				)
			);

			$this->addSoapHeaderWS();
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

		$ConsultarCoberturasResponse = $this->soapClient->ConsultarCoberturas($ConsultarCoberturas);
		return $ConsultarCoberturasResponse;

	}

	function ConsultarRegiones($ConsultarRegiones)
	{

		$ConsultarRegionesResponse = $this->soapClient->ConsultarRegiones($ConsultarRegiones);
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

	function ConsultarDirecciones($ConsultarDirecciones)
	{

		$ConsultarDireccionesResponse = $this->soapClient->ConsultarDirecciones($ConsultarDirecciones);
		return $ConsultarDireccionesResponse;

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

	function ConsultarLocalidades($ConsultarLocalidades)
	{

		$ConsultarLocalidadesResponse = $this->soapClient->ConsultarLocalidades($ConsultarLocalidades);
		return $ConsultarLocalidadesResponse;

	}

	function ConsultarCallesFiltro($ConsultarCallesFiltroRequest)
	{

		$ConsultarCallesFiltroResponse = $this->soapClient->ConsultarCallesFiltro($ConsultarCallesFiltroRequest);
		return $ConsultarCallesFiltroResponse;

	}
}
?>                                                            