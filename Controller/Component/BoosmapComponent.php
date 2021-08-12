<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'Boosmap', array('file' => 'Boosmap/Boosmap.php'));
App::import('Vendor', 'PDFMerger', array('file' => 'PDFMerger/PDFMerger.php'));

class BoosmapComponent extends Component
{
	// Usamos laffpack para armar los bultos
    public $components = array('LAFFPack', 'LibreDte', 'Etiquetas');

    /**
     * @var obj
     */
    private $BoosmapCliente;


	private static $estadosMap = array(
		'pre_recepcion_virtual',
		'ingresado' => '',
		'aceptado' => '',
		'en_punto_de_retiro' => '',
		'en_despacho' => 'Enviado',
		'entregado' => 'Entregado',
		'sin_moradores' => '',
		'pedido_anulado_cliente' => '',
		'rechazado_cliente' => '',
		'error_direccion' => '',
		'devolucion_exitosa' => '',
		'extraviado' => '',
		'pedido_anulado' => '',
		'cancelado' => ''
	);

	private static $estadosDetalleMap = array(
		'pre_recepcion_virtual' => array(
			'nombre' => 'Pre Recepción Virtual',
			'leyenda' => 'El pedido fue creado en el sistema',
			'tipo' => 'inicial'
		),
		'ingresado' => array(
			'nombre' => 'Ingresado',
			'leyenda' => 'El pedido fue recibido por Boosmap',
			'tipo' => 'inicial'
		),
		'aceptado' => array(
			'nombre' => 'Aceptado',
			'leyenda' => 'El repartido está llegando a nuestra bodega',
			'tipo' => 'sin_especificar'
		),
		'en_punto_de_retiro' => array(
			'nombre' => 'En punto de retiro',
			'leyenda' => 'El repartido llegó a nuestra bodega',
			'tipo' => 'sin_especificar'
		),
		'en_despacho' => array(
			'nombre' => 'En despacho',
			'leyenda' => 'El pedido está en reparto',
			'tipo' => 'en_reparto'
		),
		'entregado' => array(
			'nombre' => 'Entregado',
			'leyenda' => 'El pedido fue entregado al destinatario',
			'tipo' => 'entregado'
		),
		'sin_moradores' => array(
			'nombre' => 'Sin moradores',
			'leyenda' => 'El repartidor no encontró a nadie en el domicilio',
			'tipo' => 'error'
		),
		'pedido_anulado_cliente' => array(
			'nombre' => 'Pedido anulado por cliente',
			'leyenda' => 'Se ha anulado el envio',
			'tipo' => 'error'
		),
		'rechazado_cliente' => array(
			'nombre' => 'Pedido rechazado por cliente',
			'leyenda' => 'El destinatario rechazó el pedido',
			'tipo' => 'error'
		),
		'error_direccion' => array(
			'nombre' => 'Error con la dirección',
			'leyenda' => 'No pudimos dar con la dirección proporcionada',
			'tipo' => 'error'
		),
		'devolucion_exitosa' => array(
			'nombre' => 'Devuelto a bodega',
			'leyenda' => 'El pedido volvió a nuestra bodega pero no te preocupes. Buscaremos una solución.',
			'tipo' => 'error'
		),
		'extraviado' => array(
			'nombre' => 'Pedido extraviado',
			'leyenda' => 'Lo sentimos pero el pedido se extravió en el camino a destino. Nos podremos en contacto con usted lo antes posible.',
			'tipo' => 'error'
		),
		'pedido_anulado' => array(
			'nombre' => 'Pedido anulado',
			'leyenda' => 'El pedido fue anulado',
			'tipo' => 'error'
		),
		'cancelado' => array(
			'nombre' => 'Pedido cancelado',
			'leyenda' => 'El pedido fue cancelado',
			'tipo' => 'error'
		)
	);

    /**
     * [crearCliente description]
     * @param  string $apitoken [description]
     * @return [type]         [description]
     */
    public function crearCliente($apitoken = '')
    {	
    	# Usamos dev mode
    	if (Configure::read('ambiente') == 'dev') {
    		$this->BoosmapCliente = new Boosmap($apitoken, true);
    	}else{
    		$this->BoosmapCliente = new Boosmap($apitoken);
    	}
    }


	/**
	 * 
	 */
    public function comunasAlcance()
    {   
        $list = to_array($this->BoosmapCliente->getDistrict());
        
        # volvemos a usar el entorno main
        $this->BoosmapCliente->useMainEnviroment();
        
        $respuesta = array();

        if ($list['httpCode'] >= 200 && $list['httpCode'] < 300)
        {

        }
    }


	public function obtener_token()
	{
		# Usamos dev mode
    	if (Configure::read('ambiente') == 'dev') {
    		$boosmapCliente = new Boosmap('', true);
    	}else{
    		$boosmapCliente = new Boosmap('');
    	}

		prx($boosmapCliente->getToken('cristian.rojas@nodriza.cl', 'GRj38f3cJFfwrf'));
	}


    /**
     * Crea una órden de transporte en Boosmap
     * @param array $venta Información de la venta a embalar
     * 
     * @return bool
     */
    public function generar_ot($venta)
	{	
		$volumenMaximo = (float) 5832000;
		
		# Algoritmo LAFF para ordenamiento de productos
		$paquetes = $this->LAFFPack->obtener_bultos_venta($venta, $volumenMaximo);

		$log = array();		

		# si no hay paquetes se retorna false
		if (empty($paquetes)) {

			$log[] = array(
				'Log' => array(
					'administrador' => 'Boosmap vid:' . $venta['Venta']['id'],
					'modulo' => 'Ventas',
					'modulo_accion' => 'No fue posible generar la OT ya que no hay paquetes disponibles'
				)
			);

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);

			return false;
		}

		# Si los paquetes no tienen dimensiones se setean con el valor default
		foreach ($paquetes as $ip => $paquete) {
			
			if($paquete['paquete']['length'] == 0)
				$paquetes[$ip]['paquete']['length'] = $venta['MetodoEnvio']['largo_default'];

			if($paquete['paquete']['width'] == 0)
				$paquetes[$ip]['paquete']['width']  = $venta['MetodoEnvio']['ancho_default'];

			if($paquete['paquete']['height'] == 0)
				$paquetes[$ip]['paquete']['height'] = $venta['MetodoEnvio']['alto_default'];

			# peso seteado al minimo para asegurar cobro por balanza
			if($paquete['paquete']['weight'] == 0)
				$paquetes[$ip]['paquete']['weight'] = $venta['MetodoEnvio']['peso_default'];
		}

		$peso_total            = array_sum(Hash::extract($paquetes, '{n}.paquete.weight'));
		$peso_maximo_permitido = $venta['MetodoEnvio']['peso_maximo'];

		if ($peso_total > $peso_maximo_permitido) {
			$log[] = array(
				'Log' => array(
					'administrador' => 'Boosmap vid:' . $venta['Venta']['id'],
					'modulo' => 'Ventas',
					'modulo_accion' => 'No fue posible generar la OT por restricción de peso: Peso bulto ' . $peso_total . ' kg - Peso máximo permitido ' . $peso_maximo_permitido
				)
			);

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);

			return false;
		}

		$transportes = array();

		# Mantenemos las ot ya generadas
		foreach ($venta['Transporte'] as $key => $t) {
			$transportes[] = array(
				'id'              => $t['TransportesVenta']['id'],
				'transporte_id'   => $t['id'],
				'cod_seguimiento' => $t['TransportesVenta']['cod_seguimiento'],
				'etiqueta'        => $t['TransportesVenta']['etiqueta'],
				'entrega_aprox'   => $t['TransportesVenta']['entrega_aprox']
			);
		}
		
		$ruta_pdfs = array();
        
		foreach ($paquetes as $paquete) {
			
			$tramo = $paquete['paquete'];
			$tramo_1 = 80;
			$note = 'Tramo 2';
			if ($tramo['weight'] <= 20) {
				if ($tramo['length'] <= $tramo_1 && $tramo['width'] <= $tramo_1 && $tramo['weight'] <= $tramo_1) {
					$note = 'Tramo 1';
				}
			}
			# creamos el arreglo para generar la OT
			$boosmapArr = array(
                
				'order_number' => sprintf('B%d %d', count($transportes) + 1, $venta['Venta']['id']),
                'delivery_date' => date('Y-m-d H:i:s'),
                'delivery_service' => $venta['MetodoEnvio']['boosmap_service'],
                'notes' => $note,
                'pickup' => array(
                    'location' => array(
                        //'id' => $venta['MetodoEnvio']['boosmap_pick_up_id']
                        'name' => 'Bodega Toolmania',
                        'address' => 'Los vientos',
                        'district' => 'Pudahuel'
                    )
                ),
                'dropoff' => array(
                    'contact' => array(
                        'fullname' => (empty($venta['Venta']['nombre_receptor'])) ? $venta['VentaCliente']['nombre'] . ' ' . $venta['VentaCliente']['apellido'] : $venta['Venta']['nombre_receptor'],
                        'email' => $venta['VentaCliente']['email'],
                        'phone' => $venta['Venta']['fono_receptor']
                    ),
                    'location' => array(
                        'address' => $venta['Venta']['direccion_entrega'] . ' ' . $venta['Venta']['numero_entrega']  . ', ' . $venta['Venta']['comuna_entrega'],
                        'district' => $venta['Venta']['comuna_entrega'],
                        'latitude' => 0,
                        'longitude' => 0
                    )
                ),
                'packages' => array(
                    array(
                        'code' => $venta['Venta']['id'],
                        'name' => $venta['Venta']['referencia'],
                        'price' => $venta['Venta']['total'],
                        'qty' => 1
                    )
                ),
                'tags' => array(
                    'brand' => 'Toolmania'
				),
				'delivery_end_time' 	=> '21:00:00',
  				'delivery_start_time'	=> '09:00:00'
			);

			$dtes_url = Hash::extract($venta['Dte'], '{n}');

            foreach ($dtes_url as $dte)
            {
                $boosmapArr = array_replace_recursive($boosmapArr, array(
                    'files' => array(
                        array(
                            'name' => sprintf('%s folio: %d', $this->LibreDte->tipoDocumento[$dte['tipo_documento']], $dte['folio']),
                            'kind' => 'recipe',
                            'url' => obtener_url_base() . 'Dte/' . $venta['Venta']['id'] . '/' . $dte['id'] . '/'.  $dte['pdf']
                        )
                    )
                ));
            }
			
			$log[] = array(
				'Log' => array(
					'administrador' => 'Boosmap vid:' . $venta['Venta']['id'],
					'modulo' => 'Ventas',
					'modulo_accion' => 'Request: ' . json_encode($boosmapArr)
				)
			);
			
			$response = $this->BoosmapCliente->createOt($boosmapArr);
	
			$log[] = array(
				'Log' => array(
					'administrador' => 'Boosmap vid:' . $venta['Venta']['id'],
					'modulo' => 'Ventas',
					'modulo_accion' => 'Response: ' . json_encode($response)
				)
			);

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);
			
			if ($response['httpCode'] > 299) {
				return false;
			}
			
			$canal_venta = '';

			if ($venta['Venta']['venta_manual'])
			{
				$canal_venta = 'POS de venta';
			}
			else if ($venta['Venta']['marketplace_id'])
			{
				$canal_venta = $venta['Marketplace']['nombre'];
			}
			else
			{
				$canal_venta = $venta['Tienda']['nombre'];
			}
		
			$etiquetaArr = array(
                'venta' => array(
                    'id' => $venta['Venta']['id'],
                    'metodo_envio' => $venta['MetodoEnvio']['nombre'],
                    'canal' => $canal_venta,
                    'medio_de_pago' => $venta['MedioPago']['nombre'],
                    'fecha_venta' => $venta['Venta']['fecha_venta']
                ),
                'transportista' => array(
                    'nombre' => 'BOOSMAP',
                    'tipo_servicio' => $venta['MetodoEnvio']['boosmap_service'],
                    'codigo_barra' => $response['body'][0]['orderNumber']
                ),
                'remitente' => array(
                    'nombre' => $venta['Tienda']['nombre'],
                    'rut' => $venta['Tienda']['rut'],
					'fono' => $venta['Tienda']['fono'],
                    'url' => $venta['Tienda']['url'],
                    'email' => 'ventas@toolmania.cl',
                    'direccion' => $venta['Tienda']['direccion']
                ),
                'destinatario' => array(
					'nombre' => $response['body'][0]['contactName'],
                    'rut' => $venta['VentaCliente']['rut'],
                    'fono' => $response['body'][0]['contactPhone'],
                    'email' => $response['body'][0]['contactEmail'],
                    'direccion' => $response['body'][0]['destinyAddress']['address'],
                    'comuna' => $response['body'][0]['destinyAddress']['district']
                ),
                'bulto' => array(
					'referencia' => $response['body'][0]['orderNumber'],
                    'peso' => $paquete['paquete']['weight'],
                    'ancho' => (int) $paquete['paquete']['width'],
                    'alto' => (int) $paquete['paquete']['height'],
                    'largo' => (int) $paquete['paquete']['length']
                ),
                'pdf' => array(
                    'dir' => 'ModuloBoosmap'
                )
            );

			# Guardamos el transportista y el/los numeros de seguimiento
			$carrier_name = 'BOOSMAP';
			$carrier_opt = array(
				'Transporte' => array(
					'codigo' => 'BOOSMAP-WS',
					'url_seguimiento' => '',
				)
			);

			$etiqueta = $this->Etiquetas->generarEtiquetaTransporte($etiquetaArr);

			if (!empty($etiqueta['path']))
				$ruta_pdfs[] = $etiqueta['path'];

			if (!empty($etiqueta['url'])) {
				$carrier_opt = array_replace_recursive($carrier_opt, array(
					'Transporte' => array(
						'etiqueta' => $etiqueta['url']
					)
				));	
			}else{

				$log_1 = array(
					'Log' => array(
						'administrador' => 'Boosmap vid:' . $venta['Venta']['id'],
						'modulo' => 'Ventas',
						'modulo_accion' => 'Response(generar_ot): ' . json_encode($etiquetaArr)
					)
				);
	
				ClassRegistry::init('Log')->create();
				ClassRegistry::init('Log')->save($log_1);

			}

			$transportes[] = array(
				'transporte_id'   => ClassRegistry::init('Transporte')->obtener_transporte_por_nombre($carrier_name, true, $carrier_opt),
				'cod_seguimiento' => $response['body'][0]['orderNumber'],
				'etiqueta'        => $etiqueta['url'],
				'entrega_aprox'   => ''
			);
			
		}

		if (empty($transportes)) {
			return false;
		}

		# Se guarda la información del tracking en la venta
		$nwVenta = array(
			'Venta' => array(
				'id' => $venta['Venta']['id'],
				'paquete_generado' => 1
			),
			'Transporte' => $transportes
		);

		# unificar pdfs en 1 solo
		if (!empty($ruta_pdfs)) {

			$union = $this->Etiquetas->unir_documentos($ruta_pdfs, $venta['Venta']['id']);
			
			if (!empty($union['result'])) {			
				# Tomamos el primer indice ya que jamás tendremos más de 500 etiquetas unidas pra una venta
				$nwVenta = array_replace_recursive($nwVenta, array(
					'Venta' => array(
						'etiqueta_envio_externa' => $union['result'][0]['document'],
					)
				));
			}
		}

		if (ClassRegistry::init('Venta')->saveAll($nwVenta))
		{	
			return true;
		}
		else
		{
			return false;
		}

	}

    public function obtener_pickups()
    {
        return BOOSMAP::$PICKUPS;
    }

 	public function obtener_tipo_servicios()
 	{
 		return BOOSMAP::$SERVICE;
 	}


	public function obtener_ot($id)
	{
		$pedido = to_array($this->BoosmapCliente->getOT($id));
	
		if ($pedido['httpCode'] < 300)
		{
			return $pedido['body']['data']['deliveries'][0];
		}

		return false;
	}


	public function obtener_estado_nombre($nombre)
	{
		return self::$estadosMap[ Inflector::slug(strtolower($nombre), '_') ];
	}


	public function obtener_estado_nombre_map($nombre)
	{	
		$estado = (!isset($this->BoosmapCliente::$STATES[Inflector::slug(strtolower($nombre), '_')])) ? $this->BoosmapCliente::$STATES['no_informado'] : $this->BoosmapCliente::$STATES[Inflector::slug(strtolower($nombre), '_')];

		return $estado;
	}

	public function obtener_estados($id, $test = false, $n = '')
	{	
		$estados = array();
		
		if ($pedido = $this->obtener_ot($id))
		{	
			$i = 0;
			foreach ($pedido['state'] as $i => $estado) 
			{
				$estados[$i] = $this->obtener_estado_nombre_map($estado['status']);
				$estados[$i]['fecha'] = date('Y-m-d H:i:s', strtotime($estado['date']));
			}

			if ($test)
			{	
				$i++;

				$estados[$i] = $this->obtener_estado_nombre_map($n);
				$estados[$i]['fecha'] = date('Y-m-d H:i:s'); 
			}
		}
		
		return $estados;
	}

	
	public function registrar_estados($id)
	{	
		$log = [];

		# Obtenemos los transportes de la venta
		$v = ClassRegistry::init('Venta')->find('first', array(
			'conditions' => array(
				'Venta.id' => $id
			),
			'contain' => array(
				'Transporte' => array(
					'fields' => array(
						'Transporte.id'
					)
				),
				'VentaDetalle' => array(
					'fields' => array(
						'VentaDetalle.id',
						'VentaDetalle.cantidad_en_espera'
					)
				)
			),
			'fields' => array(
				'Venta.id'
			)
		));

		$log[] = array(
			'Log' => array(
				'administrador' => 'registrar_estados - vid ' . $id,
				'modulo' => 'BoosmapComponent',
				'modulo_accion' => json_encode($v)
			)
		);
		
		$historicos = array();

		$total_en_espera = array_sum(Hash::extract($v, 'VentaDetalle.{n}.cantidad_en_espera'));

		# Registramos el estado de los bultos
		foreach ($v['Transporte'] as $it => $trans) 
		{	
			# Obtenemos los estados del bulto
			$estados = $this->obtener_estados($trans['TransportesVenta']['cod_seguimiento']);
			
			$estadosHistoricosParcial = ClassRegistry::init('EnvioHistorico')->find('count', array(
				'conditions' => array(
					'EnvioHistorico.transporte_venta_id' => $trans['TransportesVenta']['id'],
					'EnvioHistorico.nombre LIKE' => '%parcial%' 
				)
			));
			
			$es_envio_parcial = false;
			
			# si la venta tiene productos en espera, quiere decir que es un envio parcial
			# si tiene un registro de envio parcial, termina como envio parcial
			if ($estadosHistoricosParcial > 0 || $total_en_espera > 0)
			{
				$es_envio_parcial = true;
			}

			$log[] = array(
				'Log' => array(
					'administrador' => 'registrar_estados - vid ' . $id,
					'modulo' => 'BoosmapComponent',
					'modulo_accion' => 'Estados embalaje: ' . json_encode($estados)
				)
			);
			
			foreach ($estados as $e) 
			{	
				if ($es_envio_parcial)
				{
					$estado_nombre = $e['nombre'] . ' parcial';
				}
				else
				{
					$estado_nombre = $e['nombre'];
				}

				# Verificamos que el estado no exista en los registros
				if (ClassRegistry::init('EnvioHistorico')->existe($estado_nombre, $trans['TransportesVenta']['id']))
				{	
					$log[] = array(
						'Log' => array(
							'administrador' => 'registrar_estados - vid ' . $id,
							'modulo' => 'BoosmapComponent',
							'modulo_accion' => 'Estado ya registrado: ' . json_encode($estado_nombre)
						)
					);

					continue;
				}
				
				$estado_id = ClassRegistry::init('EstadoEnvio')->obtener_id_por_nombre($estado_nombre);

				if (empty($estado_id))
				{
					$estado_id = ClassRegistry::init('EstadoEnvio')->crear($estado_nombre, null, 'Boosmap', $e['leyenda']);
				}

				# Sólo se crean los estados nuevos
				$historicos[] = array(
					'EnvioHistorico' => array(
						'transporte_venta_id' => $trans['TransportesVenta']['id'],
						'estado_envio_id' => $estado_id,
						'nombre' => $estado_nombre,
						'leyenda' => $e['leyenda'],
						'canal' => 'Boosmap',
						'created' => $e['fecha']
					)
				);

				$log[] = array(
					'Log' => array(
						'administrador' => 'registrar_estados - vid ' . $id,
						'modulo' => 'BoosmapComponent',
						'modulo_accion' => 'Nuevo estado historico: ' . json_encode($historicos)
					)
				);
				
			}

			$log[] = array(
				'Log' => array(
					'administrador' => 'registrar_estados - vid ' . $id,
					'modulo' => 'BoosmapComponent',
					'modulo_accion' => 'Finaliza estados transporte: ' . json_encode($trans)
				)
			);
		}
		
		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);
		
		if (empty($historicos))
		{
			return false;
		}
		
		ClassRegistry::init('EnvioHistorico')->create();
		return ClassRegistry::init('EnvioHistorico')->saveMany($historicos);
	}

}