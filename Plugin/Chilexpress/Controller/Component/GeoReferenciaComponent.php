<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'Chilexpress.GeoReferenciaWS', array('file' => 'georeferencia/GeoReferenciaWS.php'));


class GeoReferenciaComponent extends Component
{	
	private $GeoReferenciaWS;


	public function initialize(Controller $controller)
	{
		$this->Controller = $controller;
		try
		{
			Configure::load('Chilexpress.chilexpress');
		}
		catch ( Exception $e )
		{
			throw new Exception('No se encontró el archivo Plugin/Config/chilexpress.php');
		}
	}



	/**
	 * Iniciar la conexión con el WS
	 * @return void
	 */
	public function conectar()
	{	
		if (Configure::read('Chilexpress.georeferencia.USAR_WSDL')) {
			$this->GeoReferenciaWS = new GeoReferenciaWS(Configure::read('Chilexpress.georeferencia.wsdl'));
		}else{
			$this->GeoReferenciaWS = new GeoReferenciaWS(Configure::read('Chilexpress.georeferencia.endpoint'));
		}
	}



	/**
	 * Setear cabecera de la petición
	 * @param  array  	$header 	cabeceras
	 * @return void
	 */
	public function setCabecerasSoap( array $header )
	{
		$this->GeoReferenciaWS->setSoapHeaderWS($header);
	}



	/**
	 * Esta operación permite extraer las regiones de Chile, el resultado entrega una lista de Regiones
	 * @return Obj 		Objeto con el resultado de la operación
	 */
	public function obtenerRegiones()	{
		$this->conectar();
		
		$ConsultarRegiones         = new ConsultarRegiones();
		
		$ConsultarRegionesResponse = $this->GeoReferenciaWS->ConsultarRegiones($ConsultarRegiones);
		
		return $ConsultarRegionesResponse;
	}



	/**
	 * Esta operación permite extraer todas las coberturas válidas para Chilexpress, el resultado 
	 * entrega una lista de “Comunas” válidas como coberturas.
	 * @param  string 	$tipocobertura  	1 Admisión, 2 Entrega, 3 Ambas 		ej: 3
	 * @param  string 	$region        		Código de Región
	 * @return Obj 							Objeto con el resultado de la operación 	ej: 99 Todos/ R2
	 */
	public function obtenerCoberturas(string $tipocobertura, string $region)
	{
		$this->conectar();
		
		$ConsultarCoberturas                                        = new ConsultarCoberturas();
		$ConsultarCoberturas->reqObtenerCobertura                   = new \stdClass();
		
		$ConsultarCoberturas->reqObtenerCobertura->CodTipoCobertura = $tipocobertura;
		$ConsultarCoberturas->reqObtenerCobertura->CodRegion        = $region;
		
		$ConsultarCoberturasResponse                                = $this->GeoReferenciaWS->ConsultarCoberturas($ConsultarCoberturas);
		
		return $ConsultarCoberturasResponse;
	}



	/**
	 * Esta  operación  permite  extraer  todas  las  calles  válidas  para  Chilexpress,  
	 * el resultado  entrega  una  lista  de  Calles pertenecientes  a  la Comuna o  cobertura seleccionada.
	 * @param  string 	$comuna 	Nombre de Comuna 		ej: TOCOPILLA
	 * @param  string 	$calle  	Nombre de la calle, puede ser parcial
	 * @return Obj 					Objeto con el resultado de la operación
	 */
	public function obtenerCalles(string $comuna, string $calle)
	{
		$this->conectar();
		
		$ConsultarCalles                             = new ConsultarCalles();
		$ConsultarCalles->reqObtenerCalle            = new \stdClass();
		
		$ConsultarCalles->reqObtenerCalle->GlsComuna = $comuna;
		$ConsultarCalles->reqObtenerCalle->GlsCalle  = $calle;
		
		$ConsultarCallesResponse                     = $this->GeoReferenciaWS->ConsultarCalles($ConsultarCalles);
		return $ConsultarCallesResponse;
	}


	/**
	 * Esta operación retorna la numeración disponible para la calle solicitada, junto con su 
	 * carácter anterior y posterior en caso que existiese.
	 * @param  string 	$idCalle 	Código Id Calle 	ej: 133826
	 * @param  int    	$numero  	Numeración de la Calle 		ej: 225
	 * @return Obj 					Objeto con el resultado de la operación
	 */
	public function obtenerNumeracionCalles(string $idCalle, int $numero)
	{
		$this->conectar();
		
		$ConsultarNumeros                                  = new ConsultarNumeros();
		$ConsultarNumeros->reqObtenerNumero                = new \stdClass();
		
		$ConsultarNumeros->reqObtenerNumero->idNombreCalle = $idCalle;
		$ConsultarNumeros->reqObtenerNumero->Numeracion    = $numero;
		
		$ConsultarNumerosResponse                          = $this->GeoReferenciaWS->ConsultarNumeros($ConsultarNumeros);
		return $ConsultarNumerosResponse;
	}


	/**
	 * Este servicio permite validar la dirección completa, Comuna, Calle y numeración.
	 * @param  string $comuna       Nombre de Comuna 	ej: TOCOPILLA
	 * @param  string $calle        Nombre de la Calle 		ej: AGUILERA
	 * @param  string $can          Código anterior a la numeración
	 * @param  int    $numero       Número de la calle 		ej: 205
	 * @param  string $cpn          Código posterior a la numeración
	 * @param  string $complemtento Complemento de la dirección
	 * @return Obj               	Respuesta de la petición
	 */
	public function validarDireccion(string $comuna, string $calle, string $can, int $numero, string $cpn, string $complemtento)
	{
		$this->conectar();
		
		$ConsultarDirecciones                                   = new ConsultarDirecciones();
		$ConsultarDirecciones->reqObtenerDireccion              = new \stdClass();
		
		$ConsultarDirecciones->reqObtenerDireccion->GlsComuna   = $comuna;
		$ConsultarDirecciones->reqObtenerDireccion->GlsCalle    = $calle;
		$ConsultarDirecciones->reqObtenerDireccion->CaN         = $can;
		$ConsultarDirecciones->reqObtenerDireccion->Numeracion  = $numero;
		$ConsultarDirecciones->reqObtenerDireccion->CpN         = $cpn;
		$ConsultarDirecciones->reqObtenerDireccion->Complemento = $complemtento;
		
		$ConsultarDireccionesResponse                           = $this->GeoReferenciaWS->ConsultarDirecciones($ConsultarDirecciones);
		return $ConsultarDireccionesResponse;
	}


	/**
	 * Esta operación permite extraer todas las Direcciones de Las Oficinas Comerciales de Chilexpress en 
	 * @param  string $comuna      	Nombre de Comuna 	ej: TEMUCO
	 * @return Obj               	Respuesta de la petición
	 */
	public function obtenerDireccionOficinasComuna(string $comuna)
	{
		$this->conectar();
		
		$ConsultarOficinas                                = new ConsultarOficinas();
		$ConsultarOficinas->reqObtenerOficinas            = new \stdClass();
		
		$ConsultarOficinas->reqObtenerOficinas->GlsComuna = $comuna;
		
		$ConsultarOficinasResponse                        = $this->GeoReferenciaWS->ConsultarOficinas($ConsultarOficinas);
		return $ConsultarOficinasResponse;
	}



	/**
	 * Esta   operación   permite   extraer   todas   las   Direcciones   de   Las   Oficinas 
	 * Comerciales  de  Chile  de acuerdo  a una  región  ingresada, el  resultado  entrega una lista 
	 * con las direcciones de las oficinas comerciales.
	 * @param  string 	$region 	Código de región 	ej: RM
	 * @return Obj               	Respuesta de la petición
	 */
	public function obtenerDireccionOficinasRegion(string $region)
	{
		$this->conectar();
		
		$ConsultarOficinas_REG                                    = new ConsultarOficinas_REG();
		$ConsultarOficinas_REG->reqObtenerOficinas_REG            = new \stdClass();
		
		$ConsultarOficinas_REG->reqObtenerOficinas_REG->CodRegion = $region;
		
		$ConsultarOficinas_REGResponse                            = $this->GeoReferenciaWS->ConsultarOficinas_REG($ConsultarOficinas_REG);
		return $ConsultarOficinas_REGResponse;
	}
}