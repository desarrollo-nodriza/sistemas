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
		} else {
			$this->BoosmapCliente = new Boosmap($apitoken);
		}
	}


	public function comunasAlcance()
	{
		$list = to_array($this->BoosmapCliente->getDistrict());

		# volvemos a usar el entorno main
		$this->BoosmapCliente->useMainEnviroment();

		$respuesta = array();

		if ($list['httpCode'] >= 200 && $list['httpCode'] < 300) {
		}
	}


	public function obtener_token()
	{
		# Usamos dev mode
		if (Configure::read('ambiente') == 'dev') {
			$boosmapCliente = new Boosmap('', true);
		} else {
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
	public function generar_ot($venta, $embalaje, $CuentaCorrienteTransporte)
	{
		$volumenMaximo 	= $venta['MetodoEnvio']['volumen_maximo'] ?? (float) 5832000;
		$exito          = false;
		$log            = [];
		$transportes    = [];
		$paquetes = $this->LAFFPack->obtener_bultos_venta_por_embalaje_v2($embalaje, $volumenMaximo);

		# si no hay paquetes se retorna false
		if (empty($paquetes)) {

			$log[] = array(
				'Log' => array(
					'administrador' => "Boosmap vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}",
					'modulo' 		=> 'BoosmapComponent',
					'modulo_accion' => 'No fue posible generar la OT ya que no hay paquetes disponibles'
				)
			);

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);
			return $exito;
		}

		# Si los paquetes no tienen dimensiones se setean con el valor default
		foreach ($paquetes as $ip => $paquete) {

			if ($paquete['paquete']['length'] == 0)
				$paquetes[$ip]['paquete']['length'] = $venta['MetodoEnvio']['largo_default'];

			if ($paquete['paquete']['width'] == 0)
				$paquetes[$ip]['paquete']['width']  = $venta['MetodoEnvio']['ancho_default'];

			if ($paquete['paquete']['height'] == 0)
				$paquetes[$ip]['paquete']['height'] = $venta['MetodoEnvio']['alto_default'];

			# peso seteado al minimo para asegurar cobro por balanza
			if ($paquete['paquete']['weight'] == 0)
				$paquetes[$ip]['paquete']['weight'] = $venta['MetodoEnvio']['peso_default'];
		}

		$peso_total            = array_sum(Hash::extract($paquetes, '{n}.paquete.weight'));
		$peso_maximo_permitido = $venta['MetodoEnvio']['peso_maximo'];

		if ($peso_total > $peso_maximo_permitido) {

			$log[] = array(
				'Log' => array(
					'administrador' => "Boosmap vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}",
					'modulo' 		=> 'BoosmapComponent',
					'modulo_accion' => 'No fue posible generar la OT por restricción de peso: Peso bulto ' . $peso_total . ' kg - Peso máximo permitido ' . $peso_maximo_permitido
				)
			);

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);
			return $exito;
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

				'order_number' 		=> sprintf('B%d %d', count($transportes) + 1, $venta['Venta']['id']),
				'delivery_date' 	=> date('Y-m-d H:i:s'),
				'delivery_service' 	=> $CuentaCorrienteTransporte['delivery_service'],
				'notes' 			=> $note,
				'pickup' 			=> array(
					'location' => array(
						//'id' 		=> $CuentaCorrienteTransporte['boosmap_pick_up_id']
						'name' 		=> $CuentaCorrienteTransporte['informacion_bodega']['nombre'],
						'address' 	=> $CuentaCorrienteTransporte['informacion_bodega']['direccion'],
						'district' 	=> $CuentaCorrienteTransporte['informacion_bodega']['Comuna']['nombre'],
					)
				),
				'dropoff' 			=> array(
					'contact' 		=> array(
						'fullname' 	=> (empty($venta['Venta']['nombre_receptor'])) ? $venta['VentaCliente']['nombre'] . ' ' . $venta['VentaCliente']['apellido'] : $venta['Venta']['nombre_receptor'],
						'email' 	=> $venta['VentaCliente']['email'],
						'phone' 	=> $venta['Venta']['fono_receptor']
					),
					'location' 		=> array(
						'address'	=> $venta['Venta']['direccion_entrega'] . ' ' . $venta['Venta']['numero_entrega']  . ', ' . $venta['Venta']['comuna_entrega'],
						'district' 	=> $venta['Venta']['comuna_entrega'],
						'latitude' 	=> 0,
						'longitude' => 0
					)
				),
				'packages' => array(
					array(
						'code' 		=> $venta['Venta']['id'],
						'name' 		=> $venta['Venta']['referencia'],
						'price' 	=> $venta['Venta']['total'],
						'qty' 		=> 1
					)
				),
				'tags' 				=> array(
					'brand' 		=> 'Toolmania'
				),
				'delivery_end_time' 	=> '21:00:00',
				'delivery_start_time'	=> '09:00:00'
			);

			$dtes_url = Hash::extract($venta['Dte'], '{n}');

			foreach ($dtes_url as $dte) {
				$boosmapArr = array_replace_recursive($boosmapArr, array(
					'files' => array(
						array(
							'name' => sprintf('%s folio: %d', $this->LibreDte->tipoDocumento[$dte['tipo_documento']], $dte['folio']),
							'kind' => 'recipe',
							'url' => obtener_url_base() . 'Dte/' . $venta['Venta']['id'] . '/' . $dte['id'] . '/' .  $dte['pdf']
						)
					)
				));
			}

			$response = $this->BoosmapCliente->createOt($boosmapArr);

			$log[] = array(
				'Log' => array(
					'administrador' => "Boosmap vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}",
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
					'embalaje_id'   => $embalaje['id'],
					'metodo_envio' 	=> $venta['MetodoEnvio']['nombre'],
					'canal' 		=> $canal_venta,
					'medio_de_pago' => $venta['MedioPago']['nombre'],
					'fecha_venta' 	=> $venta['Venta']['fecha_venta']
				),
				'transportista' 	=> array(
					'nombre' 		=> 'BOOSMAP',
					'tipo_servicio' => $CuentaCorrienteTransporte['delivery_service'],
					'codigo_barra' 	=> $response['body'][0]['orderNumber']
				),
				'remitente' 		=> array(
					'nombre' 		=> $venta['Tienda']['nombre'],
					'rut' 			=> $venta['Tienda']['rut'],
					'fono' 			=> $venta['Tienda']['fono'],
					'url' 			=> $venta['Tienda']['url'],
					'email' 		=> 'ventas@toolmania.cl',
					'direccion' 	=> $venta['Tienda']['direccion']
				),
				'destinatario' 		=> array(
					'nombre' 		=> $response['body'][0]['contactName'],
					'rut' 			=> $venta['VentaCliente']['rut'],
					'fono' 			=> $response['body'][0]['contactPhone'],
					'email' 		=> $response['body'][0]['contactEmail'],
					'direccion' 	=> $response['body'][0]['destinyAddress']['address'],
					'comuna' 		=> $response['body'][0]['destinyAddress']['district']
				),
				'bulto' 			=> array(
					'referencia' 	=> $response['body'][0]['orderNumber'],
					'peso' 			=> $paquete['paquete']['weight'],
					'ancho' 		=> (int) $paquete['paquete']['width'],
					'alto' 			=> (int) $paquete['paquete']['height'],
					'largo' 		=> (int) $paquete['paquete']['length']
				),
				'pdf' 				=> array(
					'dir' 			=> 'ModuloBoosmap'
				)
			);

			# Guardamos el transportista y el/los numeros de seguimiento
			$carrier_name 	= 'BOOSMAP';
			$carrier_opt 	= array(
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
			} else {

				$log[] = array(
					'Log' => array(
						'administrador' => "Boosmap vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}",
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
					'TransportesVenta' =>
					[
						'transporte_id'             => ClassRegistry::init('Transporte')->obtener_transporte_por_nombre($carrier_name, true, $carrier_opt),
						'venta_id'                  => $venta['Venta']['id'],
						'cod_seguimiento'           => $response['body'][0]['orderNumber'],
						'etiqueta'                  => $union,
						'entrega_aprox'             => "",
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

		if ($pedido['httpCode'] < 300) {
			return $pedido['body']['data']['deliveries'][0];
		}

		return false;
	}


	public function obtener_estado_nombre($nombre)
	{
		return self::$estadosMap[Inflector::slug(strtolower($nombre), '_')];
	}


	public function obtener_estado_nombre_map($nombre)
	{
		return $this->BoosmapCliente::$STATES[Inflector::slug(strtolower($nombre), '_')];
	}

	public function obtener_estados($id, $test = false, $n = '')
	{
		$estados = array();

		if ($pedido = $this->obtener_ot($id)) {
			$i = 0;
			foreach ($pedido['state'] as $i => $estado) {
				$estados[$i]['nombre'] = ucfirst($estado['status']);
				$estados[$i]['fecha'] = date('Y-m-d H:i:s', strtotime($estado['date']));
			}

			if ($test) {
				$i++;

				$estados[$i] = $this->obtener_estado_nombre_map($n);
				$estados[$i]['fecha'] = date('Y-m-d H:i:s');
			}
		}

		return $estados;
	}


	public function registrar_estados($TransportesVenta, $total_en_espera)
	{
		$log 		= [];
		$historicos = [];

		# Obtenemos los estados del bulto
		$estados = $this->obtener_estados($TransportesVenta['cod_seguimiento']);

		$estadosHistoricosParcial = ClassRegistry::init('EnvioHistorico')->find('count', array(
			'conditions' => array(
				'EnvioHistorico.transporte_venta_id' 	=> $TransportesVenta['id'],
				'EnvioHistorico.nombre LIKE' 			=> '%parcial%'
			)
		));

		$es_envio_parcial = false;

		# si la venta tiene productos en espera, quiere decir que es un envio parcial
		# si tiene un registro de envio parcial, termina como envio parcial
		if ($estadosHistoricosParcial > 0 || $total_en_espera > 0) {
			$es_envio_parcial = true;
		}

		foreach ($estados as $e) {

			if ($es_envio_parcial) {
				$estado_nombre = $e['nombre'] . ' parcial';
			} else {
				$estado_nombre = $e['nombre'];
			}

			# Verificamos que el estado no exista en los registros
			if (ClassRegistry::init('EnvioHistorico')->existe($estado_nombre, $TransportesVenta['id'])) {
				continue;
			}

			$estado_existe = ClassRegistry::init('EstadoEnvio')->obtener_por_nombre($estado_nombre, 'Boosmap');

			if (!$estado_existe) {
				$estado_existe = ClassRegistry::init('EstadoEnvio')->crear($estado_nombre, null, 'Boosmap');
			}

			# Sólo se crean los estados nuevos
			$historicos[] = array(
				'EnvioHistorico' => array(
					'transporte_venta_id' 	=> $TransportesVenta['id'],
					'estado_envio_id' 		=> $estado_existe['EstadoEnvio']['id'],
					'nombre' 				=> $estado_nombre,
					'leyenda' 				=> $estado_existe['EstadoEnvio']['leyenda'],
					'canal' 				=> 'Boosmap',
					'created' 				=> $e['fecha']
				)
			);
		}
		if (count($historicos) > 0) {
			$log[] = array(
				'Log' => array(
					'administrador' => count($historicos) . 'nuevos historicos del vid - ' . $TransportesVenta['venta_id'],
					'modulo' 		=> 'BoosmapComponent',
					'modulo_accion' => json_encode($historicos)
				)
			);
		}


		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		if (empty($historicos)) {
			return false;
		}

		ClassRegistry::init('EnvioHistorico')->create();
		return ClassRegistry::init('EnvioHistorico')->saveMany($historicos);
	}

	public function regenerar_etiqueta($trackingNumber, $venta_id, $embalaje, $CuentaCorrienteTransporte)
	{

		$volumenMaximo 	= (float) 5832000;
		$paquetes 		= $this->LAFFPack->obtener_bultos_venta_por_embalaje_v2($embalaje, $volumenMaximo);
		$canal_venta 	= '';
		$log			= [];
		# si no hay paquetes se retorna false
		if (empty($paquetes)) {

			$log[] = array(
				'Log' => array(
					'administrador' => "Boosmap vid: $venta_id embalaje: {$embalaje['id']}",
					'modulo' 		=> 'BoosmapComponent',
					'modulo_accion' => 'No fue posible generar la OT ya que no hay paquetes disponibles'
				)
			);

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);
			return false;
		}

		$venta = $this->Venta->find('first',  [
			'conditions' => ['Venta.id' => $venta_id],
			'contain' => [
				'MedioPago' => [
					'fields' => ['MedioPago.nombre']
				],
				'MetodoEnvio' => [
					'fields' => [
						'MetodoEnvio.nombre'
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

		foreach ($paquetes as $ip => $paquete) {

			if ($paquete['paquete']['length'] == 0)
				$paquetes[$ip]['paquete']['length'] = $venta['MetodoEnvio']['largo_default'];

			if ($paquete['paquete']['width'] == 0)
				$paquetes[$ip]['paquete']['width']  = $venta['MetodoEnvio']['ancho_default'];

			if ($paquete['paquete']['height'] == 0)
				$paquetes[$ip]['paquete']['height'] = $venta['MetodoEnvio']['alto_default'];

			# peso seteado al minimo para asegurar cobro por balanza
			if ($paquete['paquete']['weight'] == 0)
				$paquetes[$ip]['paquete']['weight'] = $venta['MetodoEnvio']['peso_default'];
		}

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
				'embalaje_id'   => $embalaje['id'],
				'metodo_envio' 	=> $venta['MetodoEnvio']['nombre'],
				'canal' 		=> $canal_venta,
				'medio_de_pago' => $venta['MedioPago']['nombre'],
				'fecha_venta' 	=> $venta['Venta']['fecha_venta']
			),
			'transportista' => array(
				'nombre' 		=> 'BOOSMAP',
				'tipo_servicio' => $CuentaCorrienteTransporte['delivery_service'],
				'codigo_barra' 	=> $trackingNumber,
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
				'referencia' 	=> $trackingNumber,
				'peso' 			=> $paquete['paquete']['weight'],
				'ancho' 		=> (int) $paquete['paquete']['width'],
				'alto' 			=> (int) $paquete['paquete']['height'],
				'largo' 		=> (int) $paquete['paquete']['length']
			),
			'pdf' => array(
				'dir' => 'ModuloBoosmap'
			)
		);

		$log[] = array(
			'Log' => array(
				'administrador' => "Se regenera etiqueta Boosmap vid: {$venta['Venta']['id']}",
				'modulo' 		=> 'Ventas',
				'modulo_accion' => 'Response(regenerar_etiqueta): ' . json_encode($etiquetaArr)
			)
		);

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);

		return  $this->Etiquetas->generarEtiquetaTransporte($etiquetaArr);
	}
}
