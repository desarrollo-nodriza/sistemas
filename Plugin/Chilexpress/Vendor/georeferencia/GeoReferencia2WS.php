<?php
App::import('Vendor', 'Chilexpress.ChilexpressSoapClient', array('file' => 'soap/ChilexpressSoapClient.php'));

class ConsultarCoberturas{
var $CodTipoCobertura;//string
var $CodRegion;//string
}
class ConsultarCoberturas{
var $CodEstado;//int
var $GlsEstado;//string
var $Coberturas;//Coberturas
}
class Coberturas{
var $CodComuna;//string
var $GlsComuna;//string
var $CodRegion;//string
var $CodComunaIne;//int
}

class ConsultarRegiones{

}
class Regiones{
var $idRegion;//string
var $GlsRegion;//string
}
class ConsultarCalles{
var $GlsComuna;//string
var $GlsCalle;//string
}
class ConsultarCalles{
var $CodEstado;//int
var $GlsEstado;//string
var $Calles;//Calles
}
class Calles{
var $idNombreCalle;//string
var $GlsComuna;//string
var $GlsCalle;//string
}
class ConsultarNumeros{
var $idNombreCalle;//string
var $Numeracion;//int
}
class ConsultarNumeros{
var $CodEstado;//int
var $GlsEstado;//string
var $Numeros;//Numeros
}
class Numeros{
var $CaN;//string
var $Numeracion;//int
var $CpN;//string
}
class ConsultarDirecciones{
var $GlsComuna;//string
var $GlsCalle;//string
var $CaN;//string
var $Numeracion;//int
var $CpN;//string
var $Complemento;//string
}
class ConsultarDirecciones{
var $CodEstado;//int
var $GlsEstado;//string
var $Direcciones;//Direcciones
}
class Direcciones{
var $CodResultado;//int
var $idDireccion;//int
var $idBlock;//int
var $Lat;//float
var $Lon;//float
}
class ConsultarOficinas{
var $GlsComuna;//string
}
class ConsultarOficinas{
var $CodEstado;//int
var $GlsEstado;//string
var $Calles;//Calles
}
class Calles{
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
class ConsultarOficinas_REG{
var $CodRegion;//string
}
class ConsultarOficinas_REG{
var $CodEstado;//int
var $GlsEstado;//string
var $Calles;//Calles
}
class Calles{
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
class datosConsumidor{
}
class datosTransaccion{
var $fechaHora;//fechaHora
var $idTransaccionNegocio;//idTransaccionNegocio
var $idTransaccionOSB;//idTransaccionOSB
var $sistema;//sistema
var $usuario;//usuario
var $oficinaCaja;//oficinaCaja
var $nodoHeader;//nodoHeader
}
class nodoHeader{
var $_;//anyNode
}
class anyNode{
var $_;//anyType
var $any;//<anyXML>
}
class datosHeaderRequest{
var $transaccion;//datosTransaccion
}
class datosTransaccion{
var $internalCode;//string
var $idTransaccionNegocio;//string
var $fechaHoraInicioTrx;//dateTime
var $fechaHoraFinTrx;//dateTime
var $estado;//string
}
class datosHeaderResponse{
var $transaccion;//datosTransaccion
}
class ConsultarCoberturas{
var $reqObtenerCobertura;//ConsultarCoberturas
}
class ConsultarCoberturasResponse{
var $respObtenerCobertura;//ConsultarCoberturas
}
class ConsultarRegiones{
var $reqObtenerRegion;//ConsultarRegiones
}
class reqObtenerRegion {

}
class ConsultarRegionesResponse{
var $respObtenerRegion;//ConsultarRegiones
}
class ConsultarCalles{
var $reqObtenerCalle;//ConsultarCalles
}
class ConsultarCallesResponse{
var $respObtenerCalle;//ConsultarCalles
}
class ConsultarNumeros{
var $reqObtenerNumero;//ConsultarNumeros
}
class ConsultarNumerosResponse{
var $respObtenerNumero;//ConsultarNumeros
}
class ConsultarDirecciones{
var $reqObtenerDireccion;//ConsultarDirecciones
}
class ConsultarDireccionesResponse{
var $respObtenerDireccion;//ConsultarDirecciones
}
class ConsultarOficinas{
var $reqObtenerOficinas;//ConsultarOficinas
}
class ConsultarOficinasResponse{
var $respObtenerOficinas;//ConsultarOficinas
}
class ConsultarOficinas_REG{
var $reqObtenerOficinas_REG;//ConsultarOficinas_REG
}
class ConsultarOficinas_REGResponse{
var $respObtenerOficinas_REG;//ConsultarOficinas_REG
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
class GeoReferenciaWS 
 {
 var $soapClient;
 var $headerSoap;
 
private static $classmap = array('ConsultarCoberturas'=>'ConsultarCoberturas'
,'ConsultarCoberturas'=>'ConsultarCoberturas'
,'Coberturas'=>'Coberturas'
,'ConsultarRegiones'=>'ConsultarRegiones'
,'ConsultarRegiones'=>'ConsultarRegiones',
'reqObtenerRegion' => 'reqObtenerRegion'
,'Regiones'=>'Regiones'
,'ConsultarCalles'=>'ConsultarCalles'
,'ConsultarCalles'=>'ConsultarCalles'
,'Calles'=>'Calles'
,'ConsultarNumeros'=>'ConsultarNumeros'
,'ConsultarNumeros'=>'ConsultarNumeros'
,'Numeros'=>'Numeros'
,'ConsultarDirecciones'=>'ConsultarDirecciones'
,'ConsultarDirecciones'=>'ConsultarDirecciones'
,'Direcciones'=>'Direcciones'
,'ConsultarOficinas'=>'ConsultarOficinas'
,'ConsultarOficinas'=>'ConsultarOficinas'
,'Calles'=>'Calles'
,'ConsultarOficinas_REG'=>'ConsultarOficinas_REG'
,'ConsultarOficinas_REG'=>'ConsultarOficinas_REG'
,'Calles'=>'Calles'
,'ConsultarLocalidades'=>'ConsultarLocalidades'
,'ConsultarLocalidades'=>'ConsultarLocalidades'
,'Localidades'=>'Localidades'
,'ConsultarCallesFiltro'=>'ConsultarCallesFiltro'
,'ConsultarCallesFiltro'=>'ConsultarCallesFiltro'
,'Calle'=>'Calle'
,'datosConsumidor'=>'datosConsumidor'
,'datosTransaccion'=>'datosTransaccion'
,'nodoHeader'=>'nodoHeader'
,'anyNode'=>'anyNode'
,'datosHeaderRequest'=>'datosHeaderRequest'
,'datosTransaccion'=>'datosTransaccion'
,'datosHeaderResponse'=>'datosHeaderResponse'
,'ConsultarCoberturas'=>'ConsultarCoberturas'
,'ConsultarCoberturasResponse'=>'ConsultarCoberturasResponse'
,'ConsultarRegiones'=>'ConsultarRegiones'
,'ConsultarRegionesResponse'=>'ConsultarRegionesResponse'
,'ConsultarCalles'=>'ConsultarCalles'
,'ConsultarCallesResponse'=>'ConsultarCallesResponse'
,'ConsultarNumeros'=>'ConsultarNumeros'
,'ConsultarNumerosResponse'=>'ConsultarNumerosResponse'
,'ConsultarDirecciones'=>'ConsultarDirecciones'
,'ConsultarDireccionesResponse'=>'ConsultarDireccionesResponse'
,'ConsultarOficinas'=>'ConsultarOficinas'
,'ConsultarOficinasResponse'=>'ConsultarOficinasResponse'
,'ConsultarOficinas_REG'=>'ConsultarOficinas_REG'
,'ConsultarOficinas_REGResponse'=>'ConsultarOficinas_REGResponse'
,'ConsultarLocalidades'=>'ConsultarLocalidades'
,'ConsultarLocalidadesResponse'=>'ConsultarLocalidadesResponse'
,'ConsultarCallesFiltroRequest'=>'ConsultarCallesFiltroRequest'
,'ConsultarCallesFiltroResponse'=>'ConsultarCallesFiltroResponse'

);

 function __construct($url='http://qaws.ssichilexpress.cl/GeoReferencia?wsdl')
 {
 		$this->soapClient = new ChilexpressSoapClient(
			$url,
			array(
				'login'                => Configure::read('Chilexpress.georeferencia.username'),
				'password'             => Configure::read('Chilexpress.georeferencia.password'),
				'classmap'     => self::$classmap, 
				'trace'        => true,
				'exceptions'   => true,
				'soap_version'         => Configure::read('Chilexpress.georeferencia.soap'),
				'location' => $url
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
				'usuario'				=> Configure::read('Chilexpress.georeferencia.usuario'),
				'oficinaCaja'				=> Configure::read('Chilexpress.georeferencia.oficinaCaja'),
				'soap_version' => SOAP_1_2
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

}}


?>                                
                            