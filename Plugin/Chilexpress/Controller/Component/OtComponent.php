<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'Chilexpress.OtWS', array('file' => 'ot/OtWS.php'));


class OtComponent extends Component
{	
	private $OtWS;


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
		if (Configure::read('Chilexpress.ot.USAR_WSDL')) {
			$this->OtWS = new OtWS(Configure::read('Chilexpress.ot.wsdl'));
		}else{
			$this->OtWS = new OtWS(Configure::read('Chilexpress.ot.endpoint'));
		}
	}


	/**
	 * Setear cabecera de la petición
	 * @param  array  	$header 	cabeceras
	 * @return void
	 */
	public function setCabecerasSoap( array $header )
	{
		$this->OtWS->setSoapHeaderWS($header);
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
		
		$TarificarCourierResponse                                   = $this->OtWS->TarificarCourier($TarificarCourier);
		
		return $TarificarCourierResponse;
	}


	public function generarOt(int $codigoProducto, int $codigoServicio, string $comunaOrigen, int $tcc, string $refEnvio1, string $refEnvio2, int $montoCobrar, int $eoc, string $nombreDestinatario, string $emailDestinatario, string $fonoDestinatario, string $nombreRemitente, string $emailRemitente, string $fonoRemitente, string $comunaDestino, string $calleDestino, string $numeroDestino, string $complementoDestino, string $comunaDevolucion, string $calleDevolucion, string $numeroDevolucion, string $complementoDevolucion, float $pesoPieza, float $largoPieza, float $altoPieza, float $anchoPieza)
	{	
		#ini_set('memory_limit', '256M');
		$this->conectar();

		$IntegracionAsistidaOp    = new IntegracionAsistidaOp();
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida = new \stdClass();
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Remitente                     = new \stdClass();
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Destinatario                 = new \stdClass();
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Direccion                     = new \stdClass();
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->DireccionDevolucion           = new \stdClass();
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Pieza                         = new \stdClass();


		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->codigoProducto = $codigoProducto;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->codigoServicio = $codigoServicio;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->comunaOrigen = $comunaOrigen;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->numeroTCC = $tcc;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->referenciaEnvio = $refEnvio1;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->referenciaEnvio2 = $refEnvio2;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->montoCobrar = $montoCobrar;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->eoc = $eoc;

		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Remitente->nombre = $nombreRemitente;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Remitente->email = $emailRemitente;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Remitente->celular = $fonoRemitente;

		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Destinatario->nombre = $nombreDestinatario;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Destinatario->email = $emailDestinatario;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Destinatario->celular = $fonoDestinatario;

		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Direccion->comuna = $comunaDestino;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Direccion->calle = $calleDestino;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Direccion->numero = $numeroDestino;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Direccion->complemento = $complementoDestino;


		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->DireccionDevolucion->comuna = $comunaDevolucion;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->DireccionDevolucion->calle = $calleDevolucion;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->DireccionDevolucion->numero = $numeroDevolucion;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->DireccionDevolucion->complemento = $complementoDevolucion;


		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Pieza->peso = $pesoPieza;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Pieza->alto = $altoPieza;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Pieza->ancho = $anchoPieza;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Pieza->largo = $largoPieza;


		$IntegracionAsistidaOpResponse = $this->OtWS->IntegracionAsistidaOp($IntegracionAsistidaOp);

		return $IntegracionAsistidaOpResponse;
	}
}