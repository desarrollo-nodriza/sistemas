<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'Chilexpress.TarificacionWS', array('file' => 'tarificacion/TarificacionWS.php'));


class TarificacionComponent extends Component
{	
	private $TarificacionWS;


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
		
		$this->TarificacionWS = new TarificacionWS(Configure::read('Chilexpress.tarificacion.endpoint'));
		
	}


	/**
	 * Setear cabecera de la petición
	 * @param  array  	$header 	cabeceras
	 * @return void
	 */
	public function setCabecerasSoap( array $header )
	{
		$this->TarificacionWS->setSoapHeaderWS($header);
	}


	/**
	 * Esta operación permite obtener los precios de los servicios Courier de Chilexpress.
	 * @param  string 	$origen  	Código cobertura de origen 		ej: PUDA
	 * @param  string	$destino 	Código cobertura Despacho 		ej: VALP
	 * @param  float  	$peso    	Peso de la Pieza (KG) 			ej: 1.5
	 * @param  float    $alto    	Alto (cm) 						ej: 15.5
	 * @param  float    $ancho   	Ancho (cm) 						ej: 10
	 * @param  float    $largo   	Largo (cm)						ej: 20
	 * @return Obj 					Objeto con el resultado de la operación
	 */
	public function obtenerTarifaPaquete(string $origen, string $destino, float $peso, float $alto, float $ancho, float $largo)
	{	
		$this->conectar();
		
		$TarificarCourier                                           = new TarificarCourier();
		$TarificarCourier->reqValorizarCourier                      = new \stdClass();
		
		$TarificarCourier->reqValorizarCourier->CodCoberturaOrigen  = $origen;
		$TarificarCourier->reqValorizarCourier->CodCoberturaDestino = $destino;
		$TarificarCourier->reqValorizarCourier->PesoPza             = $peso;
		$TarificarCourier->reqValorizarCourier->DimAltoPza          = $alto;
		$TarificarCourier->reqValorizarCourier->DimAnchoPza         = $ancho;
		$TarificarCourier->reqValorizarCourier->DimLargoPza         = $largo;
		
		$TarificarCourierResponse                                   = $this->TarificacionWS->TarificarCourier($TarificarCourier);
		
		return $TarificarCourierResponse;
	}
}