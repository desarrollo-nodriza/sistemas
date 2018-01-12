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
	public function setCabecerasSoap( $header = array() )
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
	public function obtenerTarifaPaquete($origen = '', $destino = '', $peso = null, $alto = null, $ancho = null, $largo = null)
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


	/**
	 * Generar Orden de transporte
	 * @param  Integer 		$codigoProducto        Código del producto
	 * @param  Integer  	$codigoServicio        Tipo de servicio solicitado
	 * @param  string 		$comunaOrigen          Comuna origen de cobertura
	 * @param  Integer  	$tcc                   Numero de TCC a la cual se le harán los cargos
	 * @param  string 		$refEnvio1             Identificación única de la transacción del cliente
	 * @param  string 		$refEnvio2             Segunda referencia
	 * @param  Integer  	$montoCobrar           Monto por cobrar
	 * @param  Integer  	$eoc                   Indicador para configurar si la entrega es en oficina comercial de Chilexpress 0 = No /  1 = Si
	 * @param  string 		$nombreDestinatario    Nombre del destinatario
	 * @param  string 		$emailDestinatario     Email del destinatario
	 * @param  string 		$fonoDestinatario      Celular del destinatario
	 * @param  string 		$nombreRemitente       Nombre del remitente
	 * @param  string 		$emailRemitente        Email del remitente
	 * @param  string 		$fonoRemitente         Celular del remitente
	 * @param  string 		$comunaDestino         Comuna de despacho de la pieza
	 * @param  string 		$calleDestino          Dirección del destinatario
	 * @param  string 		$numeroDestino         Número de la calle, esta puede contener el carácter anterior o posterior de una calle, por ejemplo 0-960
	 * @param  string 		$complementoDestino    Complemento a la dirección, por ejemplo casa, piso, block, etc.
	 * @param  string 		$comunaDevolucion      Comuna de devolución de la pieza
	 * @param  string 		$calleDevolucion       Dirección de devolución
	 * @param  string 		$numeroDevolucion      Número de la calle, esta puede contener el carácter anterior o posterior de una calle, por ejemplo 0-960
	 * @param  string 		$complementoDevolucion Complemento a la dirección, por ejemplo casa, piso, block, etc.
	 * @param  float 		$pesoPieza             Peso de la pieza
	 * @param  float 		$largoPieza            Largo de la pieza
	 * @param  float 		$altoPieza             Alto de la pieza
	 * @param  float 		$anchoPieza            Ancho de la pieza
	 * @return Obj 								   Objeto con el resultado de la operación
	 */
	public function generarOt( $codigoProducto = null, $codigoServicio = null, $comunaOrigen = '', $tcc = null, $refEnvio1 = '', $refEnvio2 = '', $montoCobrar = null, $eoc = null, $nombreDestinatario = '', $emailDestinatario = '', $fonoDestinatario = '', $nombreRemitente = '', $emailRemitente = '', $fonoRemitente = '', $comunaDestino = '', $calleDestino = '', $numeroDestino = '', $complementoDestino = '', $comunaDevolucion = '', $calleDevolucion = '', $numeroDevolucion = '', $complementoDevolucion = '', $pesoPieza = null, $largoPieza = null, $altoPieza = null, $anchoPieza = null)
	{	
		#ini_set('memory_limit', '256M');
		$this->conectar();

		$IntegracionAsistidaOp                                                                  = new IntegracionAsistidaOp();
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida                                   = new \stdClass();
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Remitente                        = new \stdClass();
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Destinatario                     = new \stdClass();
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Direccion                        = new \stdClass();
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->DireccionDevolucion              = new \stdClass();
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Pieza                            = new \stdClass();
		
		
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->codigoProducto                   = $codigoProducto;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->codigoServicio                   = $codigoServicio;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->comunaOrigen                     = $comunaOrigen;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->numeroTCC                        = $tcc;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->referenciaEnvio                  = $refEnvio1;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->referenciaEnvio2                 = $refEnvio2;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->montoCobrar                      = $montoCobrar;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->eoc                              = $eoc;
		
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Remitente->nombre                = $nombreRemitente;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Remitente->email                 = $emailRemitente;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Remitente->celular               = $fonoRemitente;
		
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Destinatario->nombre             = $nombreDestinatario;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Destinatario->email              = $emailDestinatario;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Destinatario->celular            = $fonoDestinatario;
		
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Direccion->comuna                = $comunaDestino;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Direccion->calle                 = $calleDestino;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Direccion->numero                = $numeroDestino;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Direccion->complemento           = $complementoDestino;
		
		
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->DireccionDevolucion->comuna      = $comunaDevolucion;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->DireccionDevolucion->calle       = $calleDevolucion;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->DireccionDevolucion->numero      = $numeroDevolucion;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->DireccionDevolucion->complemento = $complementoDevolucion;
		
		
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Pieza->peso                      = $pesoPieza;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Pieza->alto                      = $altoPieza;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Pieza->ancho                     = $anchoPieza;
		$IntegracionAsistidaOp->reqGenerarIntegracionAsistida->Pieza->largo                     = $largoPieza;
		
		
		$IntegracionAsistidaOpResponse                                                          = $this->OtWS->IntegracionAsistidaOp($IntegracionAsistidaOp);

		return $IntegracionAsistidaOpResponse;
	}
}