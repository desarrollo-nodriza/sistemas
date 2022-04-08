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
		$volumenMaximo = $venta['MetodoEnvio']['volumen_maximo'] ?? (float) 5832000;

		$embalajes = Hash::extract($venta['EmbalajeWarehouse'], "{n}[estado=procesando]");
		
		$exito = false;
		$log = array();		

		if (!$embalajes) {

			$log[] = array(
				'Log' => array(
					'administrador' => 'Boosmap vid:' . $venta['Venta']['id'],
					'modulo' 		=> 'BoosmapComponent',
					'modulo_accion' => json_encode(["No posee embalajes en procesando" => $venta])
				)
			);
		}
		foreach ($embalajes as $embalaje) {

			# Algoritmo LAFF para ordenamiento de productos
			$paquetes = $this->LAFFPack->obtener_bultos_venta_por_embalaje_v2($embalaje, $volumenMaximo);

			# si no hay paquetes se retorna false
			if (empty($paquetes)) {

				$log[] = array(
					'Log' => array(
						'administrador' => "Boosmap vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}" ,
						'modulo' 		=> 'BoosmapComponent',
						'modulo_accion' => 'No fue posible generar la OT ya que no hay paquetes disponibles'
					)
				);

				continue;
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
						'administrador' => "Boosmap vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}" ,
						'modulo' 		=> 'BoosmapComponent',
						'modulo_accion' => 'No fue posible generar la OT por restricción de peso: Peso bulto ' . $peso_total . ' kg - Peso máximo permitido ' . $peso_maximo_permitido
					)
				);

				continue;
			}

			$transportes = array();

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
							'name' =>  $venta['MetodoEnvio']['Bodega']['nombre'],
							'address' => $venta['MetodoEnvio']['Bodega']['direccion'],
							'district' => $venta['MetodoEnvio']['Bodega']['Comuna']['nombre'],
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
				
				$response = $this->BoosmapCliente->createOt($boosmapArr);
		
				$log[] = array(
					'Log' => array(
						'administrador' => "Boosmap vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}" ,
						'modulo' 		=> 'BoosmapComponent',
						'modulo_accion' => json_encode([
							'code'					  => $response['httpCode'],
							'Respuesta de generar OT' => $response,
							'Request para generar OT' => $boosmapArr
							
						])
					)
				);
			
				if ($response['httpCode'] > 299) {
                    continue;
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

					$log[] = array(
						'Log' => array(
							'administrador' => "Boosmap vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}" ,
							'modulo' 		=> 'BoosmapComponent',
							'modulo_accion' => 'Problemas con la URL de la etiqueta: ' . json_encode($etiquetaArr)
						)
					);
				}

				$union = null;
				
				if (!empty($ruta_pdfs)) {

					$union = $this->Etiquetas->unir_documentos($ruta_pdfs, $venta['Venta']['id']);
					
					if (!empty($union['result'])) {			
						$union = $union['result'][0]['document'];
					}
				}

				$transportes[] = 
                [
                    'TransportesVenta'=>
                        [
                            'transporte_id'             => ClassRegistry::init('Transporte')->obtener_transporte_por_nombre($carrier_name, true, $carrier_opt),
                            'venta_id'                  => $venta['Venta']['id'],
                            'cod_seguimiento'           => $response['body'][0]['orderNumber'],
                            'etiqueta'                  => $union,
                            'entrega_aprox'             => "" ,
                            'paquete_generado'          => count($paquetes),
                            'costo_envio'               => null,
							'etiqueta_envio_externa' 	=> $union,
                            'embalaje_id'               => $embalaje["id"]
                        ]
                ];

				if (empty($transportes)) {
                    continue;
				}
			}
		}

		if ($transportes) {
			# Se guarda la información del tracking en la venta
			if (ClassRegistry::init('TransportesVenta')->saveAll($transportes)) {
				$log[] = array(
					'Log' => array(
						'administrador' => 'Boosmap, se registro ot vid:' . $venta['Venta']['id'],
						'modulo'        => 'BoosmapComponent',
						'modulo_accion' => json_encode($transportes)
					)
				);

				$exito = true;
				
			} else {
				$log[] = array(
					'Log' => array(
						'administrador' => 'Boosmap, dificultades para guardar información ot vid:' . $venta['Venta']['id'],
						'modulo'        => 'BoosmapComponent',
						'modulo_accion' => json_encode($transportes)
					)
				);
			}
		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		try {
			$this->registrar_estados($venta['Venta']['id']);
		} catch (\Throwable $th) {

		}
		return $exito;
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
		return $this->BoosmapCliente::$STATES[Inflector::slug(strtolower($nombre), '_')];
	}

	public function obtener_estados($id, $test = false, $n = '')
	{	
		$estados = array();
		
		if ($pedido = $this->obtener_ot($id))
		{	
			$i = 0;
			foreach ($pedido['state'] as $i => $estado) 
			{	
				$estados[$i]['nombre'] = ucfirst($estado['status']);
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
					continue;
				}
				
				$estado_existe = ClassRegistry::init('EstadoEnvio')->obtener_por_nombre($estado_nombre,'Boosmap');

				if (!$estado_existe)
				{
					$estado_existe = ClassRegistry::init('EstadoEnvio')->crear($estado_nombre, null, 'Boosmap');
				}

				# Sólo se crean los estados nuevos
				$historicos[] = array(
					'EnvioHistorico' => array(
						'transporte_venta_id' => $trans['TransportesVenta']['id'],
						'estado_envio_id' => $estado_existe['EstadoEnvio']['id'],
						'nombre' => $estado_nombre,
						'leyenda' => $estado_existe['EstadoEnvio']['leyenda'],
						'canal' => 'Boosmap',
						'created' => $e['fecha']
					)
				);

				
				
			}
			if (count($historicos)>0) {
				$log[] = array(
					'Log' => array(
						'administrador' => count($historicos) . 'nuevos historicos del vid - ' . $id,
						'modulo' => 'BoosmapComponent',
						'modulo_accion' => json_encode($historicos)
					)
				);
			}
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

	public function regenerar_etiqueta($transportes_venta,$venta_id)
	{

	
		$venta = $this->Venta->find('first',  [
			'conditions' => ['Venta.id' => $venta_id],
			'contain' => [
				'MedioPago' => [
					'fields' => ['MedioPago.nombre']
				],
				'MetodoEnvio' => [
					'fields' => [
						'MetodoEnvio.nombre',
						'MetodoEnvio.boosmap_service'
					]
				],
				'Tienda' => [
					'fields' => [
						'Tienda.nombre',
						'Tienda.rut',
						'Tienda.fono',
						'Tienda.url',
						'Tienda.direccion',
					]
				],
				'VentaCliente' => [
					'fields' => [
						'VentaCliente.email',
					]
				],
			],
		]);

		$venta_detalle =  ClassRegistry::init('VentaDetalle')->find(
			'all',
			[
				'conditions' => ['VentaDetalle.venta_id' => $venta_id],
				'contain' => [
					'VentaDetalleProducto' => [
						'fields' => [
							'VentaDetalleProducto.id',
							'VentaDetalleProducto.alto',
							'VentaDetalleProducto.ancho',
							'VentaDetalleProducto.largo',
							'VentaDetalleProducto.peso',
						]
					],
				],
				'fields' => [
					'VentaDetalle.venta_id',
					'VentaDetalle.cantidad_reservada',
				]
			]
		);
		$venta_detalle_filtrado 			= Hash::extract($venta_detalle, '{n}.VentaDetalle');
		$Venta_detalle_producto_filtrado 	= Hash::extract($venta_detalle, '{n}.VentaDetalleProducto');
		$venta_detalle_final 				= [];

		foreach ($venta_detalle_filtrado as $key => $value) {
			$value['VentaDetalleProducto']	= $Venta_detalle_producto_filtrado[$key];
			$venta_detalle_final[]			= $value;
		}

		$volumenMaximo = (float) 5832000;
		$bulto = $this->LAFFPack->obtener_bultos_venta(['VentaDetalle' => $venta_detalle_final], $volumenMaximo);

		$canal_venta = '';

		if ($venta['Venta']['venta_manual']) {
			$canal_venta = 'POS de venta';
		} else if ($venta['Venta']['marketplace_id']) {
			$canal_venta = $venta['Marketplace']['nombre'];
		} else {
			$canal_venta = $venta['Tienda']['nombre'];
		}

		$etiquetaArr = array(
			'venta' => array(
				'id' 			=> $venta['Venta']['id'],
				'metodo_envio' 	=> $venta['MetodoEnvio']['nombre'],
				'canal' 		=> $canal_venta,
				'medio_de_pago' => $venta['MedioPago']['nombre'],
				'fecha_venta' 	=> $venta['Venta']['fecha_venta']
			),
			'transportista' => array(
				'nombre' 		=> 'BOOSMAP',
				'tipo_servicio' => $venta['MetodoEnvio']['boosmap_service'],
				'codigo_barra' 	=> $transportes_venta['TransportesVenta']['cod_seguimiento'],
			),
			'remitente' => array(
				'nombre' 	=> $venta['Tienda']['nombre'],
				'rut' 		=> $venta['Tienda']['rut'],
				'fono' 		=> $venta['Tienda']['fono'],
				'url' 		=> $venta['Tienda']['url'],
				'email' 	=> 'ventas@toolmania.cl',
				'direccion' => $venta['Tienda']['direccion']
			),
			'destinatario' => array(
				'nombre' 	=> $venta['Venta']['nombre_receptor'],
				'rut'		=> $venta['Venta']['rut_receptor'],
				'fono' 		=> $venta['Venta']['fono_receptor'],
				'email' 	=> $venta['VentaCliente']['email'],
				'direccion' => $venta['Venta']['direccion_entrega'] . ' ' . $venta['Venta']['numero_entrega'],
				'comuna' 	=> $venta['Venta']['comuna_entrega']
			),
			'bulto' => array(
				'referencia' 	=> $transportes_venta['TransportesVenta']['cod_seguimiento'],
				'peso' 			=> $bulto[$venta_id]['paquete']['weight'],
				'ancho' 		=> $bulto[$venta_id]['paquete']['width'],
				'alto' 			=> $bulto[$venta_id]['paquete']['height'],
				'largo' 		=> $bulto[$venta_id]['paquete']['length']
			),
			'pdf' => array(
				'dir' => 'ModuloBoosmap'
			)
		);

		$log = array(
			'Log' => array(
				'administrador' => 'Se regenera etiqueta Boosmap vid:' . $venta_id,
				'modulo' 		=> 'Ventas',
				'modulo_accion' => 'Response(regenerar_etiqueta): ' . json_encode($etiquetaArr)
			)
		);

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);

		return  $this->Etiquetas->generarEtiquetaTransporte($etiquetaArr);

		
	}

}