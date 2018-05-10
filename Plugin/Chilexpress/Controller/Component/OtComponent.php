<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'Chilexpress.OtWS', array('file' => 'ot/OtWS.php'));
App::import('Vendor', 'Chilexpress.ChilexpressLAFFPack', array('file' => 'ChilexpressLAFFPack.php'));

class OtComponent extends Component
{	
	private $OtWS;

	private static $servicios = array(
        array(
            'code' => 1,
            'grade' => 9,
            'desc' => array(
                'en' => 'Ultra fast',
                'es' => 'Ultra rapido'
            )
        ),
        array(
            'code' => 2,
            'grade' => 8,
            'desc' => array(
                'en' => 'Overnight',
                'es' => 'Overnight'
            )
        ),
        array(
            'code' => 3,
            'grade' => 7,
            'desc' => array(
                'en' => 'Next business day',
                'es' => 'Dia habil siguiente'
            )
        ),
        array(
            'code' => 4,
            'grade' => 6,
            'desc' => array(
                'en' => 'Subsequent business day',
                'es' => 'Dia habil subsiguiente'
            )
        ),
        array(
            'code' => 5,
            'grade' => 5,
            'desc' => array(
                'en' => 'Third day',
                'es' => 'Tercer dia'
            )
        ),
        array(
            'code' => 8,
            'grade' => 9,
            'desc' => array(
                'en' => 'Am / pm',
                'es' => 'Am / pm'
            )
        ),
        array(
            'code' => 11,
            'grade' => 4,
            'desc' => array(
                'en' => 'Delivery day saturday',
                'es' => 'Entrega dia sabado'
            )
        ),
        array(
            'code' => 12,
            'grade' => 8,
            'desc' => array(
                'en' => 'Overnight priority',
                'es' => 'Overnight prioritario'
            )
    	)
	);

	private static $productos = array(
		3 => 'ENCOMIENDA',
		#2 => 'VALIJA',
		#1 => 'DOCUMENTO'
	);

	private static $eoc = array(
		0 => 'Despacho a domicilio',
		1 => 'Cliente retira en sucursal'
	);


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
		
		$this->OtWS = new OtWS(Configure::read('Chilexpress.ot.endpoint'));
		
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


	public function verEtiqueta($imagen = '', $ot = '', $barcode = '')
	{
		if (!empty($imagen) && !empty($ot) && !empty($barcode)) {
			if ($this->rutaEtiqueta($ot)) {

				$rutaEtiqueta = Configure::read('Chilexpress.ot.pathEtiquetas') . $ot . DS . $barcode . '.jpg';
				$rutaPublica = Configure::read('Chilexpress.ot.pathPublica') . $ot . DS . $barcode . '.jpg';
				
				
				if (file_put_contents($rutaEtiqueta , $imagen) === false) {
					return '';
				}
				
				return $rutaPublica;
			}
		}
	}


	private function rutaEtiqueta($ot = '')
	{
		if (!empty($ot)) {
			
			$pathEtiquetas = Configure::read('Chilexpress.ot.pathEtiquetas') . $ot . DS; 

			if (!is_dir($pathEtiquetas)) {
				if(mkdir($pathEtiquetas, 0755, true)){
					return true;
				}
			}else{
				return true;
			}

		}

		return false;
	}


	public function obtenerListaServicios()
	{
		$serviciosLista = array();
		$servicios = self::$servicios;

		foreach ($servicios as $is => $servicio) {
			$serviciosLista[$servicio['code']] = $servicio['desc']['es'];
		}

		return $serviciosLista;
	}


	public function obtenerListaProductos()
	{
		return self::$productos;
	}


	public function obtenerListaTCC()
	{
		$tcc = Configure::read('Chilexpress.tcc');
		
		if (!empty($tcc)) {
			return array($tcc => $tcc);
		}

		return array();
	}


	public function obtenerListaEoc()
	{
		return self::$eoc;
	}

	/**
	 * Crea una arreglo con las cajas de los productos segun sis dimensiones
	 * @param  array  $productos 	Listado de productos
	 * @param  string $modelo     	Nombre del modelo de los productos
	 * @return array
	 */
	public function obtenerCajasProductos($productos = array(), $modelo = '')
	{	
		foreach ($productos as $ip => $producto) {
			$product_width  = (float) $producto[$modelo]['width'];
			$product_height = (float) $producto[$modelo]['height'];
			$product_depth  = (float) $producto[$modelo]['depth'];
			$values = array(
                $product_width,
                $product_height,
                $product_depth
            );
            
            sort($values);

            $boxes[] = array_combine(array('height', 'width', 'length'), $values);
		}

		return $boxes;
	}


	public function obtenerDimensionesPaquete($cajas = array())
	{	
		# Inicia Clase LAFF
        $LAFF = new ChilexpressLAFFPack();

        # Se empaquetan las cajas en un paquete
        $LAFF->pack($cajas);
        
        # Se obtienen las dimensiones del paquete
        $paquete = $LAFF->get_container_dimensions();

        return $paquete;
        
	}
}