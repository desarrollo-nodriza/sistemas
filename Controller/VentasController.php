<?php
App::uses('AppController', 'Controller');
App::uses('VentaDetalleProductosController', 'Controller');
App::uses('DtesController', 'Controller');
App::uses('CakePdf', 'Plugin/CakePdf/Pdf');

//App::import('Vendor', 'Mercadopago', array('file' => 'Mercadopago/mercadopago.php'));
App::import('Vendor', 'Mercadolibre', array('file' => 'Meli/meli.php'));
App::import('Vendor', 'PDFMerger', array('file' => 'PDFMerger/PDFMerger.php'));

App::uses('CakeTime', 'Utility');

class VentasController extends AppController {

	//public $Mercadopago;
	public static $Mercadolibre;
	public $shell = false;


	public $components = array(
		'RequestHandler',
		'Linio',
		'Prestashop',
		'MeliMarketplace',
		'Mercadopago',
		'Toolmania',
		'LibreDte',
	);
	

	/**
     * Crea un redirect y agrega a la URL los parámetros del filtro
     * @param 		$controlador 	String 		Nombre del controlador donde redirijirá la petición
     * @param 		$accion 		String 		Nombre del método receptor de la petición
     * @return 		void
     */
    public function filtrar($controlador = '', $accion = '')
    {
    	$redirect = array(
    		'controller' => $controlador,
    		'action' => $accion
    		);

		foreach ($this->request->data['Venta'] as $campo => $valor) {
			if (!empty($valor)) {
				$redirect[$campo] = str_replace('/', '-', $valor);
			}
		}

    	$this->redirect($redirect);

    }


    /**
     * Indice
     * @return [type] [description]
     */
	public function admin_index () {

		$condiciones = array();
		$joins = array();

		$FiltroVenta                = '';
		$FiltroCliente              = '';
		$FiltroTienda               = '';
		$FiltroMarketplace          = '';
		$FiltroMedioPago            = '';
		$FiltroVentaEstadoCategoria = '';
		$FiltroAtendida             = '';
		$FiltroFechaDesde           = '';
		$FiltroFechaHasta          = '';

		$backurl = array(
            'action' => 'index'
        );

		if (is_array($this->request->params['action'])) {
			$backurl = array_replace_recursive($backurl, $this->request->params['action']);	
		}
        
        $this->Session->write($this->request->params['controller'], $backurl);

		// Filtrado de ordenes por formulario
		if ( $this->request->is('post') ) {

			$this->filtrar('ventas', 'index');

		}


		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'filtroventa':
						$FiltroVenta = trim($valor);

						if ($FiltroVenta != "") {

							$condiciones["OR"] = array(
								"Venta.id LIKE '%" .$FiltroVenta. "%'",
								"Venta.id_externo LIKE '%" .$FiltroVenta. "%'",
								"Venta.referencia LIKE '%" .$FiltroVenta. "%'"
							);
							
						}
						break;
					case 'filtrocliente':
						$FiltroCliente = trim($valor);

						if ($FiltroCliente != "") {

							$joins[] = array(
								'table' => 'rp_venta_clientes',
								'alias' => 'clientes',
								'type' => 'INNER',
								'conditions' => array(
									'clientes.id = Venta.venta_cliente_id',
									'OR' => array(
										"clientes.nombre LIKE '%" .$FiltroCliente. "%'",
										"clientes.apellido LIKE '%" .$FiltroCliente. "%'",
										"clientes.rut LIKE '%" .$FiltroCliente. "%'",
										"clientes.email LIKE '%" .$FiltroCliente. "%'",
										"clientes.telefono LIKE '%" .$FiltroCliente. "%'"
									)
								)
							);
							
						}
						break;
					case 'tienda_id':
						$FiltroTienda = $valor;

						if ($FiltroTienda != "") {
							$condiciones['Venta.tienda_id'] = $FiltroTienda;
						} 
						break;
					case 'marketplace_id':
						$FiltroMarketplace = $valor;

						if ($FiltroMarketplace != "") {
							$condiciones['Venta.marketplace_id'] = $FiltroMarketplace;
						} 
						break;
					case 'medio_pago_id':
						$FiltroMedioPago = $valor;

						if ($FiltroMedioPago != "") {
							$condiciones['Venta.medio_pago_id'] = $FiltroMedioPago;
						} 
						break;
					case 'venta_estado_categoria_id':
						$FiltroVentaEstadoCategoria = $valor;

						if ($FiltroVentaEstadoCategoria != "") {

							$joins[] = array(
								'table' => 'rp_venta_estados',
								'alias' => 'ventas_estados',
								'type' => 'INNER',
								'conditions' => array(
									'ventas_estados.id = Venta.venta_estado_id',
									"ventas_estados.venta_estado_categoria_id = " .$FiltroVentaEstadoCategoria
								)
							);

						}
						break;
					case 'atendida':
						$FiltroAtendida = $valor;

						if ($FiltroAtendida != "") {
							$condiciones['Venta.atendida'] = $FiltroAtendida;
						} 
						break;
					case 'FechaDesde':
						$FiltroFechaDesde = trim($valor);

						if ($FiltroFechaDesde != "") {

							$ArrayFecha = explode("-", $FiltroFechaDesde);

							$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

							$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 00:00:00"));

							$condiciones["Venta.fecha_venta >="] = $Fecha;

						}
						break;
					case 'FechaHasta':
						$FiltroFechaHasta = trim($valor);

						if ($FiltroFechaHasta != "") {

							$ArrayFecha = explode("-", $FiltroFechaHasta);

							$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

							$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 23:59:59"));

							$condiciones["Venta.fecha_venta <="] = $Fecha;

						} 
						break;
				}
			}
		}


		# Es bodega
		try {
			$permisos = $this->hasPermission();
		} catch (Exception $e) {
			$permisos = $e;
		}

		if (isset($permisos['storage'])) {

			if ($permisos['storage']) {

				$joins[] = array(
					'table' => 'rp_venta_estados',
					'alias' => 'ventas_estados',
					'type' => 'INNER',
					'conditions' => array(
						'ventas_estados.id = Venta.venta_estado_id',
						"ventas_estados.permitir_retiro_oc"  => 1
					)
				);

				#$condiciones["Venta.subestado_oc !="] = 'entregado';	
			}
		}


		$paginate = array(
			'recursive' => 0,
			'contain' => array(
				'VentaEstado' => array(
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
						)
					),
					'fields' => array(
						'VentaEstado.id', 'VentaEstado.nombre', 'VentaEstado.venta_estado_categoria_id', 'VentaEstado.permitir_dte', 'VentaEstado.permitir_retiro_oc', 'VentaEstado.notificacion_cliente'
					)
				),
				'Tienda' => array(
					'fields' => array(
						'Tienda.id', 'Tienda.nombre'
					)
				),
				'Marketplace' => array(
					'fields' => array(
						'Marketplace.id', 'Marketplace.nombre'
					)
				),
				'MedioPago' => array(
					'fields' => array(
						'MedioPago.id', 'MedioPago.nombre'
					)
				),
				'VentaCliente' => array(
					'fields' => array(
						'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.email', 'VentaCliente.telefono',
					)
				),
				'VentaTransaccion' => array(
					'fields' => array(
						'VentaTransaccion.nombre', 'VentaTransaccion.monto'
					)
				),
				'Dte' => array(
					'fields' => array(
						'Dte.id', 'Dte.estado'
					)
				)
			),
			'conditions' => $condiciones,
			'joins' => $joins,
			'fields' => array(
				'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo',
				'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.venta_cliente_id', 'Venta.prioritario', 'Venta.picking_estado'
			),
			'order' => array('Venta.prioritario' => 'DESC', 'Venta.fecha_venta' => 'DESC'),
			'limit' => 20
		);

		//----------------------------------------------------------------------------------------------------
		$this->paginate = $paginate;

		$ventas = $this->paginate();

		//----------------------------------------------------------------------------------------------------
		$tiendas = $this->Venta->Tienda->find(
			'list',
			array(
				'conditions' => array(
					'Tienda.activo' => 1
				),
				'order' => 'Tienda.nombre ASC'
			)
		);

		//----------------------------------------------------------------------------------------------------
		$marketplaces = $this->Venta->Marketplace->find(
			'list',
			array(
				'conditions' => array(
					'Marketplace.activo' => 1
				),
				'order' => 'Marketplace.nombre ASC'
			)
		);

		//----------------------------------------------------------------------------------------------------
		$ventaEstadoCategorias = $this->Venta->VentaEstado->VentaEstadoCategoria->find('list');

		//----------------------------------------------------------------------------------------------------
		$medioPagos = $this->Venta->MedioPago->find(
			'list',
			array(
				'conditions' => array(
					'MedioPago.activo' => 1
				),
				'order' => 'MedioPago.nombre ASC'
			)
		);

		# Mercadolibre conectar
		$meliConexion = $this->admin_verificar_conexion_meli();

		BreadcrumbComponent::add('Ventas', '/ventas');

		$this->set(compact(
			'ventas', 'tiendas', 'marketplaces', 'ventaEstadoCategorias', 'medioPagos',
			'FiltroVenta', 'FiltroCliente', 'FiltroTienda', 'FiltroMarketplace', 'FiltroMedioPago', 'FiltroVentaEstadoCategoria', 'FiltroAtendida', 'FiltroFechaDesde', 'FiltroFechaHasta', 'meliConexion'
		));

	}


	/**
     * Indice bodega
     * @return [type] [description]
     */
	public function admin_index_bodega () {

		$condiciones = array(
			'AND' => array(
				array(
					'Venta.picking_estado' => 'empaquetar'
				),
				array(
					'Venta.picking_estado' => 'empaquetando'
				)
			)
		);
		$joins = array();

		$estados_ids = Hash::extract(ClassRegistry::init('VentaEstadoCategoria')->find('all', array('conditions' => array('venta' => 1, 'final' => 0), 'fields' => array('id'))), '{n}.VentaEstadoCategoria.id');

		// Listamos solo pagados
		$joins[] = array(
			'table' => 'rp_venta_estados',
			'alias' => 'ventas_estados',
			'type' => 'INNER',
			'conditions' => array(
				'ventas_estados.id = Venta.venta_estado_id',
				"ventas_estados.venta_estado_categoria_id" => $estados_ids,
				"ventas_estados.permitir_retiro_oc"  => 1
			)
		);

		$paginate = array(
			'recursive' => 0,
			'contain' => array(
				'VentaEstado' => array(
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
						)
					),
					'fields' => array(
						'VentaEstado.id', 'VentaEstado.nombre', 'VentaEstado.venta_estado_categoria_id', 'VentaEstado.permitir_dte', 'VentaEstado.permitir_retiro_oc', 'VentaEstado.notificacion_cliente'
					)
				),
				'Tienda' => array(
					'fields' => array(
						'Tienda.id', 'Tienda.nombre'
					)
				),
				'Marketplace' => array(
					'fields' => array(
						'Marketplace.id', 'Marketplace.nombre'
					)
				),
				'VentaCliente' => array(
					'fields' => array(
						'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.email', 'VentaCliente.telefono',
					)
				)
			),
			'conditions' => $condiciones,
			'joins' => $joins,
			'fields' => array(
				'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo',
				'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.venta_cliente_id', 'Venta.picking_estado', 'Venta.prioritario'
			),
			'order' => array('Venta.prioritario' => 'desc', 'Venta.fecha_venta' => 'asc'),
			'limit' => -1
		);

		//----------------------------------------------------------------------------------------------------
		$this->paginate = $paginate;

		$ventas = $this->paginate();

		//----------------------------------------------------------------------------------------------------
		$tiendas = $this->Venta->Tienda->find(
			'list',
			array(
				'conditions' => array(
					'Tienda.activo' => 1
				),
				'order' => 'Tienda.nombre ASC'
			)
		);

		//----------------------------------------------------------------------------------------------------
		$marketplaces = $this->Venta->Marketplace->find(
			'list',
			array(
				'conditions' => array(
					'Marketplace.activo' => 1
				),
				'order' => 'Marketplace.nombre ASC'
			)
		);

		//----------------------------------------------------------------------------------------------------
		$ventaEstadoCategorias = $this->Venta->VentaEstado->VentaEstadoCategoria->find('list');

		//----------------------------------------------------------------------------------------------------
		$medioPagos = $this->Venta->MedioPago->find(
			'list',
			array(
				'conditions' => array(
					'MedioPago.activo' => 1
				),
				'order' => 'MedioPago.nombre ASC'
			)
		);

		# Mercadolibre conectar
		$meliConexion = $this->admin_verificar_conexion_meli();

		BreadcrumbComponent::add('Ventas', '/ventas');

		$this->set(compact(
			'ventas', 'tiendas', 'marketplaces', 'ventaEstadoCategorias', 'medioPagos',
			'FiltroVenta', 'FiltroCliente', 'FiltroTienda', 'FiltroMarketplace', 'FiltroMedioPago', 'FiltroVentaEstadoCategoria', 'FiltroAtendida', 'FiltroFechaDesde', 'FiltroFechaHasta', 'meliConexion'
		));

	}


	/**
	 * [admin_obtener_ventas_preparacion description]
	 * @param  integer $limit1  [description]
	 * @param  integer $offset1 [description]
	 * @param  integer $limit2  [description]
	 * @param  integer $offset2 [description]
	 * @return [type]           [description]
	 */
	public function admin_obtener_ventas_preparacion($limit1 = 10, $offset1 = 0, $limit2 = 10, $offset2 = 0)
	{	
		$estados_ids = Hash::extract(ClassRegistry::init('VentaEstadoCategoria')->find('all', array('conditions' => array('venta' => 1, 'final' => 0), 'fields' => array('id'))), '{n}.VentaEstadoCategoria.id');

		$ventas_empaquetar         = $this->Venta->obtener_ventas_preparar('empaquetar', 10, 0, $estados_ids);
		$ventas_empaquetar_total   = $this->Venta->obtener_ventas_preparar('empaquetar', -1, 0, $estados_ids);
		$ventas_empaquetando       = $this->Venta->obtener_ventas_preparar('empaquetando', 10, 0, $estados_ids);
		$ventas_empaquetando_total = $this->Venta->obtener_ventas_preparar('empaquetando', -1, 0, $estados_ids);
		$ventas_empaquetado        = $this->Venta->obtener_ventas_preparadas('empaquetado', 10, 0, $estados_ids);
		$ventas_empaquetado_total  = $this->Venta->obtener_ventas_preparadas('empaquetado', -1, 0, $estados_ids);

		$this->layout = 'ajax';

		$html_empaquetar   = '';
		$html_empaquetando = '';
		$html_empaquetado  = '';

		$venta_estados = ClassRegistry::init('VentaEstado')->obtener_estados_logistica(true);

		foreach ($ventas_empaquetar as $iv => $ve) {

			$this->viewPath   = 'Ventas/ajax';
			$this->output     = '';
			
			$venta  = $this->preparar_venta($ve['Venta']['id']);

			# Si la venta no está completa se quita
			$reservado = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_reservada'));
			$comprados = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad'));

			if ($reservado != $comprados) {
				$ventas_empaquetando_total--;
				continue;
			}

			$url    = Router::url( sprintf('/api/ventas/%d.json', $venta['Venta']['id']), true);
			$tamano = '500x500';
			
			$this->set(compact('venta', 'url', 'tamano', 'venta_estados'));

			$vista = $this->render('venta_preparacion');
			$html_empaquetar .= $vista->body();
			$offset1++;

		}

		$html_empaquetar .= '<div class="task-drop push-down-10">
            <span class="fa fa-cloud"></span>
            Arrastra la venta aquí para reiniciar el proceso
        	</div>';


		foreach ($ventas_empaquetando as $iv => $ve) {
			$this->viewPath   = 'Ventas/ajax';
			$this->output     = '';

			$venta  = $this->preparar_venta($ve['Venta']['id']);

			# Si la venta no está completa se quita
			$reservado = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_reservada'));
			$comprados = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad'));

			if ($reservado != $comprados) {
				$ventas_empaquetado_total--;
				continue;
			}

			$url    = Router::url( sprintf('/api/ventas/%d.json', $venta['Venta']['id']), true);
			$tamano = '500x500';
			
			$this->set(compact('venta', 'url', 'tamano', 'venta_estados'));

			$vista = $this->render('venta_preparacion');
			$html_empaquetando .= $vista->body();

			$offset2++;

		}

		$html_empaquetando .= '<div class="task-drop push-down-10">
            <span class="fa fa-cloud"></span>
            Arrastra la venta aquí para comenzar a procesar
        </div>';


        foreach ($ventas_empaquetado as $iv => $ve) {
			$this->viewPath   = 'Ventas/ajax';
			$this->output     = '';

			$venta  = $this->preparar_venta($ve['Venta']['id']);
			$url    = Router::url( sprintf('/api/ventas/%d.json', $venta['Venta']['id']), true);
			$tamano = '500x500';
			
			$this->set(compact('venta', 'url', 'tamano', 'venta_estados'));

			$vista = $this->render('venta_preparacion');
			$html_empaquetado .= $vista->body();

			$offset2++;

		}

		$html_empaquetado .= '<div class="task-drop push-down-10">
            <span class="fa fa-cloud"></span>
            Arrastra la venta aquí para finalizar su prepración
        </div>';


		$response =  array(
			'code'    => 200,
			'message' => 'Ventas obtenidas con éxito',
			'data'    => array(
				'empaquetar'   => array(
					'html' => $html_empaquetar,
					'limit' => $limit1 + 10,
					'offset' => $offset1,
					'total' => count($ventas_empaquetar_total)
				),
				'empaquetando' => array(
					'html' => $html_empaquetando,
					'limit' => $limit2 + 10,
					'offset' => $offset2,
					'total' => count($ventas_empaquetando_total)
				),
				'empaquetado' => array(
					'html' => $html_empaquetado,
					'limit' => $limit2 + 10,
					'offset' => $offset2,
					'total' => count($ventas_empaquetado_total)
				)
			)
		);

		echo json_encode($response);
		exit;
	}


	/**
	 * [admin_cambiar_estado description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_cambiar_estado($id)
	{	

		$respuesta = array(
			'code' => 501,
			'message' => 'Error inexplicable'
		);

		if ( ! $this->Venta->exists($id) ) {
			$respuesta['code'] = 404;
			$respuesta['message'] = 'No se encontró la venta en nuestros registros.';
			echo json_encode($respuesta);
			exit;
		}

		if (!$this->request->is('post')) {
			$respuesta['code'] = 502;
			$respuesta['message'] = 'Método no permitido.';
			echo json_encode($respuesta);
			exit;
		}

		try {
			$cambiar_estado = $this->cambiarEstado($id, $this->request->data['Venta']['id_externo'], $this->request->data['Venta']['venta_estado_id'], $this->request->data['Venta']['tienda_id'], $this->request->data['Venta']['marketplace_id']);
		} catch (Exception $e) {
			$respuesta['code'] = 506;
			$respuesta['message'] = $e->getMessage();
			echo json_encode($respuesta);
			exit;
		}

		$subestado = (isset($this->request->data['Venta']['picking_estado'])) ? $this->request->data['Venta']['picking_estado'] : '';

		if ($cambiar_estado) {

			if ($subestado == 'empaquetado') {

				# Quitar los productos reservados de la bodega
				$detalles = ClassRegistry::init('VentaDetalle')->find('all', array(
					'conditions' => array(
						'VentaDetalle.venta_id' => $id,
						'VentaDetalle.completo' => 0,
						'VentaDetalle.cantidad_reservada >' => 0
					)
				));

				foreach ($detalles as $idd => $d) {

					# Pedido completado
					$detalles[$idd]['VentaDetalle']['completo']                   = ($detalles[$idd]['VentaDetalle']['cantidad'] == $d['VentaDetalle']['cantidad_reservada']) ? 1 : 0;
					$detalles[$idd]['VentaDetalle']['fecha_completado']			 = ($detalles[$idd]['VentaDetalle']['completo']) ? date('Y-m-d H:i:s') : '';

					$detalles[$idd]['VentaDetalle']['cantidad_reservada']         = 0;
					$detalles[$idd]['VentaDetalle']['cantidad_entregada']         = $d['VentaDetalle']['cantidad_reservada'];
					$detalles[$idd]['VentaDetalle']['cantidad_pendiente_entrega'] = $d['VentaDetalle']['cantidad'] - $d['VentaDetalle']['cantidad_reservada'];

					ClassRegistry::init('Bodega')->crearSalidaBodega($d['VentaDetalle']['venta_detalle_producto_id'], null, $d['VentaDetalle']['cantidad_reservada'], 'OC');
						
				}

				if (!empty($detalles)) {
					ClassRegistry::init('VentaDetalle')->saveMany($detalles);
				}

				$this->Venta->saveField('picking_fecha_termino', date('Y-m-d H:i:s'));
				$this->Venta->saveField('picking_estado', $subestado);

				# Sub estados OC de la venta
				if (array_sum(Hash::extract($detalles, '{n}VentaDetalle.cantidad_pendiente_entrega')) > 0 ) {
					$this->Venta->saveField('subestado_oc', 'parcialmente_entregado');
				}else{
					$this->Venta->saveField('subestado_oc', 'entregado');
				}	
			}

			$respuesta['code'] = 200;
			$respuesta['message'] = 'Ok';
		}

		echo json_encode($respuesta);
		exit;
	}


	/**
	 * [admin_cambiar_subestado description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_cambiar_subestado($id = null)
	{	
		$respuesta = array(
			'code' => 501,
			'message' => 'Error inexplicable'
		);

		if ( ! $this->Venta->exists($id) ) {
			$respuesta['code'] = 404;
			$respuesta['message'] = 'No se encontró la venta en nuestros registros.';
			echo json_encode($respuesta);
			exit;
		}

		if (!$this->request->is('post')) {
			$respuesta['code'] = 502;
			$respuesta['message'] = 'Método no permitido.';
			echo json_encode($respuesta);
			exit;
		}

		$subestado = $this->request->data['subestado'];

		
		$this->Venta->id = $id;
		$subestadoActual = $this->Venta->field('picking_estado');

		if ($subestado == $subestadoActual) {
			$respuesta['code'] = 200;
			$respuesta['message'] = 'Ok';
			echo json_encode($respuesta);
			exit;
		}

		if ($this->Venta->saveField('picking_estado', $subestado)) {

			if ($subestado == 'empaquetando') {

				$this->Venta->saveField('picking_email', $this->Auth->user('email'));
				$this->Venta->saveField('picking_fecha_inicio', date('Y-m-d H:i:s'));

				$venta = $this->Venta->obtener_venta_por_id($id);

				$this->cambiar_estado_preparada($venta);
			}

			if ($subestado == 'empaquetado') {

				# Quitar los productos reservados de la bodega
				$detalles = ClassRegistry::init('VentaDetalle')->find('all', array(
					'conditions' => array(
						'VentaDetalle.venta_id' => $id,
						'VentaDetalle.completo' => 0,
						'VentaDetalle.cantidad_reservada >' => 0
					)
				));

				foreach ($detalles as $idd => $d) {

					# Pedido completado
					$detalles[$idd]['VentaDetalle']['completo']                   = ($detalles[$idd]['VentaDetalle']['cantidad'] == $d['VentaDetalle']['cantidad_reservada']) ? 1 : 0;
					$detalles[$idd]['VentaDetalle']['fecha_completado']			 = ($detalles[$idd]['VentaDetalle']['completo']) ? date('Y-m-d H:i:s') : '';

					$detalles[$idd]['VentaDetalle']['cantidad_reservada']         = 0;
					$detalles[$idd]['VentaDetalle']['cantidad_entregada']         = $d['VentaDetalle']['cantidad_reservada'];
					$detalles[$idd]['VentaDetalle']['cantidad_pendiente_entrega'] = $d['VentaDetalle']['cantidad'] - $d['VentaDetalle']['cantidad_reservada'];

					ClassRegistry::init('Bodega')->crearSalidaBodega($d['VentaDetalle']['venta_detalle_producto_id'], null, $d['VentaDetalle']['cantidad_reservada'], 'OC');
						
				}

				if (!empty($detalles)) {
					ClassRegistry::init('VentaDetalle')->saveMany($detalles);
				}

				$this->Venta->saveField('picking_fecha_termino', date('Y-m-d H:i:s'));

				# Sub estados OC de la venta
				if (array_sum(Hash::extract($detalles, '{n}VentaDetalle.cantidad_pendiente_entrega')) > 0 ) {
					$this->Venta->saveField('subestado_oc', 'parcialmente_entregado');
				}else{
					$this->Venta->saveField('subestado_oc', 'entregado');
				}	
			}

			$respuesta['code'] = 200;
			$respuesta['message'] = 'Ok';
		}

		echo json_encode($respuesta);
		exit;
	}


	/**
	 * [admin_marcar_prioritaria description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_marcar_prioritaria($id = null)
	{
		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['prioritario'] = 1;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect($this->referer('/', true));
	}


	/**
	 * [admin_marcar_prioritaria description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_marcar_no_prioritaria($id = null)
	{
		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['prioritario'] = 0;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect($this->referer('/', true));
	}


	/**
	 * Activar venta
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_activar($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['activo'] = 1;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Registro activado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}


	/**
	 * Desactivar Venta
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_desactivar($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['activo'] = 0;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Registro desactivado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}


	/**
	 * Forzar venta como antendida
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_marcar_atendida($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['atendida'] = 1;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Venta marcada como Atendida', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}


	/**
	 * Forzar venta no atendida
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_marcar_no_atendida($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['atendida'] = 0;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Venta marcada como No Atendida', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}


	/****************************************************************************************************/
	//obtiene las tiendas (prestashop) que deben ser actualizadas
	private function obtener_tiendas () {

		return $this->Venta->Tienda->find(
			'all',
			array(
				'conditions' => array(
					'Tienda.activo' => 1,
					'Tienda.actualizacion_automatica_ventas' => 1
				),
				'contain' => array(
					'Marketplace' => array(
						'conditions' => array(
							'Marketplace.activo' => 1
						),
						'fields' => array(
							'Marketplace.id', 'Marketplace.tienda_id', 'Marketplace.nombre', 'Marketplace.fee', 'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key', 'Marketplace.marketplace_tipo_id', 'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token', 'Marketplace.seller_id'
						),
						'MarketplaceTipo' => array(
							'conditions' => array(
								'MarketplaceTipo.activo' => 1
							),
							'fields' => array(
								'MarketplaceTipo.nombre'
							)
						)
					)
				),
				'fields' => array(
					'Tienda.id', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop', 'Tienda.configuracion'
				)
			)
		);

	}


	/**
	 * Método antiguo para obtener ventas desde prestashop
	 * @param  array  $params [description]
	 * @param  array  $tienda [description]
	 * @return [type]         [description]
	 */
	private function prestashop_obtener_ventas_antiguo($params = array(), $tienda = array())
	{
		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden'), $tienda);

		$ordenes	= ClassRegistry::init('Orden')->find('all', $params);

		$result = array();

		if (!empty($ordenes)) {

			foreach ($ordenes as $key => $value) {
				$result['order'][$key]['id']                       = $value['Orden']['id_order'];
				$result['order'][$key]['id_address_delivery']      = $value['Orden']['id_address_delivery'];
				$result['order'][$key]['id_customer']              = $value['Orden']['id_customer'];
				$result['order'][$key]['current_state']            = $value['Orden']['current_state'];
				$result['order'][$key]['date_add']                 = $value['Orden']['date_add'];
				$result['order'][$key]['payment']                  = $value['Orden']['payment'];
				$result['order'][$key]['total_discounts_tax_incl'] = $value['Orden']['total_discounts_tax_incl'];
				$result['order'][$key]['total_paid']               = $value['Orden']['total_paid'];
				$result['order'][$key]['total_products']           = $value['Orden']['total_products'];
				$result['order'][$key]['total_shipping_tax_incl']  = $value['Orden']['total_shipping_tax_incl'];
				$result['order'][$key]['reference']                = $value['Orden']['reference'];
				$result['order'][$key]['id_carrier']               = $value['Orden']['id_carrier'];
			}
		}

		return $result;

	}


	/****************************************************************************************************/
	//obtiene las órdenes de una tienda
	private function prestashop_obtener_ventas ($tienda) {

		//se obtiene la última venta registrada para consultar solo las nuevas a prestashop
		$venta = $this->Venta->find(
			'first',
			array(
				'conditions' => array(
					'Venta.tienda_id' => $tienda['Tienda']['id'],
					'Venta.marketplace_id' => null
				),
				'fields' => array(
					'Venta.id_externo'
				),
				'order' => 'Venta.id_externo DESC'
			)
		);
		
		$DataVentas = $this->Prestashop->prestashop_obtener_ventas($tienda['Tienda']['id'], $venta);
		
		# si no logra obtener ventas via WS intenta obtenerlas directamente por la base de datos
		if (empty($DataVentas) && !empty($venta)) {

			$opt = array(
				'conditions'	=> array('Orden.id_order >' => $venta['Venta']['id_externo']),
				'fields' => array(
					'Orden.id_order',
					'Orden.id_address_delivery',
					'Orden.id_customer',
					'Orden.current_state',
					'Orden.date_add',
					'Orden.payment',
					'Orden.total_discounts_tax_incl',
					'Orden.total_paid',
					'Orden.total_products',
					'Orden.total_shipping_tax_incl',
					'Orden.reference',
					'Orden.id_carrier'
				)
			);

			$DataVentas = $this->prestashop_obtener_ventas_antiguo($opt, $tienda);
		}
	
		return $DataVentas;

	}


	/****************************************************************************************************/
	//guarda un producto si no existe
	private function prestashop_guardar_producto ($DetalleVenta, $excluir = array()) {

		$producto = $this->Venta->VentaDetalle->VentaDetalleProducto->find(
			'first',
			array(
				'conditions' => array(
					'VentaDetalleProducto.id_externo' => $DetalleVenta['product_id']
				),
				'fields' => array(
					'VentaDetalleProducto.id',
					'VentaDetalleProducto.cantidad_virtual'
				)
			)
		);

		$nuevaCantidad = 0;	

		if (empty($producto)) {

			$data = array();
			$data['VentaDetalleProducto']['id']         			= $DetalleVenta['product_id'];
			$data['VentaDetalleProducto']['id_externo'] 			= $DetalleVenta['product_id'];
			$data['VentaDetalleProducto']['nombre']     			= $DetalleVenta['product_name'];
			$data['VentaDetalleProducto']['cantidad_virtual']     	= 10 + $DetalleVenta['product_quantity'];

			$this->Venta->VentaDetalle->VentaDetalleProducto->create();
			$this->Venta->VentaDetalle->VentaDetalleProducto->save($data);
			
			$nuevaCantidad = $data['VentaDetalleProducto']['cantidad_virtual'] - $DetalleVenta['product_quantity'];

		}else{

			if ($producto['VentaDetalleProducto']['cantidad_virtual'] > $DetalleVenta['product_quantity']) {
				$nuevaCantidad = $producto['VentaDetalleProducto']['cantidad_virtual'] - $DetalleVenta['product_quantity'];
			}
		
		}
		
		# Descontar stock virtual y refrescar canales
		#$productosController = new VentaDetalleProductosController();

		#$productosController->descontar_stock_virtual($DetalleVenta['product_id'], $DetalleVenta['product_id'], $nuevaCantidad, $excluir);

	}


	/****************************************************************************************************/
	//guarda un producto si no existe
	private function linio_guardar_producto ($DetalleVenta, $excluir = array()) {

		$producto = $this->Venta->VentaDetalle->VentaDetalleProducto->find(
			'first',
			array(
				'conditions' => array(
					'VentaDetalleProducto.id_externo' => $DetalleVenta['Sku']
				),
				'fields' => array(
					'VentaDetalleProducto.id',
					'VentaDetalleProducto.cantidad_virtual'
				)
			)
		);

		$nuevaCantidad = 0;
		
		if (empty($producto)) {

			$data = array();
			$data['VentaDetalleProducto']['id']         = $DetalleVenta['Sku'];
			$data['VentaDetalleProducto']['id_externo'] = $DetalleVenta['Sku'];
			$data['VentaDetalleProducto']['nombre']     = $DetalleVenta['Name'];
			$data['VentaDetalleProducto']['cantidad_virtual']     	= 11;

			$this->Venta->VentaDetalle->VentaDetalleProducto->create();
			$this->Venta->VentaDetalle->VentaDetalleProducto->save($data);

			$nuevaCantidad = $data['VentaDetalleProducto']['cantidad_virtual'] - 1; # Linio separa los items de una compra aunque su cantidad sea mayor a 1

			
		}else{

			if ($producto['VentaDetalleProducto']['cantidad_virtual'] > 1) {
				$nuevaCantidad = $producto['VentaDetalleProducto']['cantidad_virtual'] - 1;
			}

		}

		# Descontar stock virtual y refrescar canales
		#$productosController = new VentaDetalleProductosController();

		#$productosController->descontar_stock_virtual($DetalleVenta['Sku'], $DetalleVenta['Sku'], $nuevaCantidad, $excluir);

		return $DetalleVenta['Sku'];

	}


	/****************************************************************************************************/
	//obtiene el estado de venta
	private function obtener_estado_id ($estado, $origen) {

		$VentaEstado = $this->Venta->VentaEstado->find(
			'first',
			array(
				'conditions' => array(
					'VentaEstado.nombre' => $estado
				),
				'fields' => array(
					'VentaEstado.id'
				)
			)
		);

		if (!empty($VentaEstado)) {
			return $VentaEstado['VentaEstado']['id'];
		}

		//si el estado de venta no existe, se crea
		$data = array();
		$data['VentaEstado']['nombre'] = $estado;
		$data['VentaEstado']['origen'] = $origen;

		$this->Venta->VentaEstado->create();
		$this->Venta->VentaEstado->save($data);

		return $this->Venta->VentaEstado->id;

	}


	/****************************************************************************************************/
	//obtiene el id de un medio de pago
	private function obtener_medio_pago_id ($medio_pago) {

		$MedioPago = $this->Venta->MedioPago->find(
			'first',
			array(
				'conditions' => array(
					'MedioPago.nombre' => $medio_pago
				),
				'fields' => array(
					'MedioPago.id'
				)
			)
		);

		if (!empty($MedioPago)) {
			return $MedioPago['MedioPago']['id'];
		}

		//si el medio de pago no existe, se crea
		$data = array();
		$data['MedioPago']['nombre'] = $medio_pago;

		$this->Venta->MedioPago->create();
		$this->Venta->MedioPago->save($data);

		return $this->Venta->MedioPago->id;

	}


	/****************************************************************************************************/
	//obtiene el id de un medio de pago
	private function obtener_metodo_envio_id ($metodo_envio) {

		$MetodoEnvio = $this->Venta->MetodoEnvio->find(
			'first',
			array(
				'conditions' => array(
					'MetodoEnvio.nombre' => $metodo_envio
				),
				'fields' => array(
					'MetodoEnvio.id'
				)
			)
		);

		if (!empty($MetodoEnvio)) {
			return $MetodoEnvio['MetodoEnvio']['id'];
		}

		//si el medio de pago no existe, se crea
		$data = array();
		$data['MetodoEnvio']['nombre'] = $metodo_envio;

		$this->Venta->MetodoEnvio->create();
		$this->Venta->MetodoEnvio->save($data);

		return $this->Venta->MetodoEnvio->id;

	}


	/****************************************************************************************************/
	//obtiene el estado de venta
	private function obtener_cliente_id ($DataVenta) {

		$rut = str_replace(".", "", $DataVenta['NationalRegistrationNumber']);

		$VentaCliente = $this->Venta->VentaCliente->find(
			'first',
			array(
				'conditions' => array(
					'VentaCliente.rut' => $rut
				),
				'fields' => array(
					'VentaCliente.id'
				)
			)
		);

		if (!empty($VentaCliente)) {
			return $VentaCliente['VentaCliente']['id'];
		}

		//si el cliente no existe, se crea
		$data = array();
		$data['VentaCliente']['nombre'] = $DataVenta['CustomerFirstName'];
		$data['VentaCliente']['apellido'] = $DataVenta['CustomerLastName'];
		$data['VentaCliente']['rut'] = $rut;

		$this->Venta->VentaCliente->create();
		$this->Venta->VentaCliente->save($data);

		return $this->Venta->VentaCliente->id;

	}


	/****************************************************************************************************/
	//sincroniza el stock de productos de una tienda y los marketplaces asociados
	// 1. se buscan los productos en prestashop para ver su stock
	// 2. se restan las cantidades vendidas al stock tomado de prestashop para actualizarlo.
	// 3. el nuevo stock se envía a la tienda y marketplaces asociados
	private function sincronizar_stock_productos ($tienda, $ArrayProductosSincronizacion, $ArrayCantidadesVendidos, $ConexionPrestashop) {

		//para actualizar los productos a cada marketplace Linio si existen
		//esto debido a que Linio va reuniendo los productos y luego actualiza en una sola llamada
		$ArrayLinios = array();

		//----------------------------------------------------------------------------------------------------
		//ciclo para recorrer los productos que estaban en las ventas
		foreach ($ArrayProductosSincronizacion as $producto_key => $producto_id) {

			//se obtiene el stock de prestashop
			$opt = array();
			$opt['resource'] = 'stock_availables';
			$opt['display'] = '[quantity]';
			$opt['filter[id_product]'] = '[' .$producto_id. ']';

			$xml = $ConexionPrestashop->get($opt);

			$PrestashopResources = $xml->children()->children();

			//para cambiar el objeto xml a un array
			$json = json_encode($PrestashopResources);
			$stock = json_decode($json, true);

			$NuevoStock = $stock['stock_available']['quantity'];

			//se resta la cantidad vendida en marketplaces para actualizar el stock
			//si el producto no existe en el array de vendidos (si no se vendió en los marketplaces) no es necesario actualizar el stock de prestashop
			if (!empty($ArrayCantidadesVendidos[$producto_key])) {

				$NuevoStock = $NuevoStock - $ArrayCantidadesVendidos[$producto_key];

				//se actualiza el stock en prestashop
				$resources->stock_available->quantity = $NuevoStock;

				$opt = array('resource' => 'stock_availables');
				$opt['putXml'] = $xml->asXML();
				$opt['id'] = $producto_id;
				$xml = $ConexionPrestashop->edit($opt);

			} //fin resta de cantidad vendida en marketplaces

			//----------------------------------------------------------------------------------------------------
			//si la tienda tiene marketplaces asociados para actualizar
			if (!empty($tienda['Marketplace'])) {

				//recorrido de marketplaces
				foreach ($tienda['Marketplace'] as $marketplace_key => $marketplace) {

					//----------------------------------------------------------------------------------------------------
					//si el marketplace es Linio
					if ($marketplace['marketplace_tipo_id'] == 1) {

						//si no existe la posición del marketplace para ir reuniendo los productos, se agrega
						if (!isset($ArrayLinios[$marketplace_key])) {
							$ArrayLinios[$marketplace_key] = Endpoints::product()->productUpdate();
						}

						$ArrayLinios[$marketplace_key]->updateProduct($producto_id)->setQuantity($NuevoStock);

					} //fin si el marketplace es Linio

					//----------------------------------------------------------------------------------------------------
					//si el marketplace es Mercado Libre

				} //fin recorrido de los marketplaces

			} //fin si la tienda tiene marketplaces asociados

		} //fin ciclo de productos que estaban en las ventas

		//si existen productos para actualizar en cuentas de Linio
		if (!empty($ArrayLinios)) {

			foreach ($ArrayLinios as $linio_key => $linio) {

				$ConexionLinio = Client::create(new Configuration($tienda['Marketplace'][$linio_key]['api_host'], $tienda['Marketplace'][$linio_key]['api_user'], $tienda['Marketplace'][$linio_key]['api_key']));

				$linio->build()->call($ConexionLinio);

			}

		} //fin si existen productos para actualizar en tiendas de linio

	}


	/**
	 * Método encargado de actualizar, obtener el token de MELI. 
	 * Sí no existe Token, crea la url para la conexión
	 * @return array
	 */
	public function admin_verificar_conexion_meli($redirect = '')
	{
		$tiendas     = $this->obtener_tiendas();
		if (empty($redirect)) {
			$redirectURI = Router::url( array('controller' => $this->request->controller, 'action' => 'index'), true );	
		}else{
			$redirectURI = Router::url( $redirect, true );
		}

		$siteId      = 'MLC';
		$results     = array();
		$response    = array();
		$code 		 = isset($_GET['code']) ? $_GET['code'] : '' ;

		foreach ($tiendas as $it => $tienda) {
			foreach ($tienda['Marketplace'] as $im => $marketplace) {
				if ($marketplace['marketplace_tipo_id'] == 2) {

					if (!empty($marketplace['api_user']) && !empty($marketplace['api_key'])) {

						$this->MeliMarketplace->crearCliente($marketplace['api_user'], $marketplace['api_key'], $marketplace['access_token'], $marketplace['refresh_token']);
						$results[$marketplace['id']] = $this->MeliMarketplace->mercadolibre_conectar($code, $marketplace, $redirectURI, $siteId);	
					}else{

						$results[$marketplace['id']]['errors'] = sprintf('%s no tiene configurado su API_USER y API_KEY', $marketplace['nombre']);
					}	
				}

			}
		}
		
		foreach ($results as $ir => $result) {
			if (!empty($result['success']) && !$this->shell) {
				SessionComponent::setFlash($this->crearAlertaUl(array($result['success']), 'Resultados'), null, array(), 'success');
			}

			if (!empty($result['errors']) && !$this->shell) {
				SessionComponent::setFlash($this->crearAlertaUl(array($result['errors']), 'Errores'), null, array(), 'danger');
			}

			if (!empty($result['access'])) {
				$response[] = $result['access'];
			}
		}

		return $response;
	}


	/**
	 * Método encargado de conectar MELI con Sistemas
	 * @param  array  $marketplace Arreglo con la información del Marketplace 
	 * @param  string $redirectURI Url de redirección para el login de MEli
	 * @param  string $siteId      Identificador de API (MLC = Mercadolibre Chile)
	 * @return array  			   Información del procedimiento	
	 */
	public function admin_mercadolibre_conectar($marketplace = array(), $redirectURI = '', $siteId = 'MLC') 
	{	
		$m = array();
		$response = array(
			'access' => array(),
			'success' => array(),
			'errors' => array()
		);

		if(isset($_GET['code']) || isset($marketplace['access_token'])) {
			// If code exist and session is empty
			if(isset($_GET['code']) && !isset($marketplace['access_token'])) {
				// //If the code was in get parameter we authorize
				try{
					$user = $this->Mercadolibre->authorize($_GET["code"], $redirectURI);
					
					if ($user['httpCode'] == 200) {
						// Now we save credentials with the authenticated user
						$m['Marketplace']['access_token']  = $user['body']->access_token;
						$m['Marketplace']['expires_token'] = time() + $user['body']->expires_in;
						$m['Marketplace']['refresh_token'] = $user['body']->refresh_token;
						$m['Marketplace']['seller_id']     = $user['body']->user_id;
					}
				}catch(Exception $e){
					$response['errors'] = $e->getMessage();
				}
			} else {
				// We can check if the access token in invalid checking the time
				if($marketplace['expires_token'] < time()) {

					try {
						// Make the refresh proccess
						$refresh = $this->Mercadolibre->refreshAccessToken();
						
						if ($refresh['httpCode'] == 200) {
							// Now we save credentials with the new parameters
							$m['Marketplace']['access_token']  = $refresh['body']->access_token;
							$m['Marketplace']['expires_token'] = time() + $refresh['body']->expires_in;
							$m['Marketplace']['refresh_token'] = $refresh['body']->refresh_token;
							$m['Marketplace']['seller_id']     = $refresh['body']->user_id;
						}
					} catch (Exception $e) {
					  	$response['errors'] = $e->getMessage();
					}
				}
			}
			
			// save in db marketplace tokens
			if (!empty($m) && empty($response[$marketplace['id']]['errors'])) {

				ClassRegistry::init('Marketplace')->id = $marketplace['id'];
				if (!ClassRegistry::init('Marketplace')->save($m)) {
					$response['errors'] = sprintf('%s no se logró conectar a %s', $marketplace['nombre'], $marketplace['MarketplaceTipo']['nombre']);
				}else{
					$response['success'] = sprintf('%s se ha conectado con éxito a %s', $marketplace['nombre'], $marketplace['MarketplaceTipo']['nombre']);
				}
			}

			return $response;

		} else {

			$response['access']['marketplace'] = $marketplace['nombre'];
			$response['access']['url'] = $this->Mercadolibre->getAuthUrl($redirectURI, Meli::$AUTH_URL[$siteId]);
			
			return $response;
		}
	}


	/****************************************************************************************************/
	//actualización de estatus de ventas marcadas como No Atendidas para marketplaces (mercado libre)
	public function actualizar_ventas_anteriores_mercadolibre ($ventas, $marketplace) {
		# Para la consola se carga el componente on the fly!
		if ($this->shell) {
			$this->MeliMarketplace = $this->Components->load('MeliMarketplace');
		}
		# Cliente y conexión Meli
		$this->MeliMarketplace->crearCliente( $marketplace['Marketplace']['api_user'], $marketplace['Marketplace']['api_key'], $marketplace['Marketplace']['access_token'], $marketplace['Marketplace']['refresh_token'] );
		$this->MeliMarketplace->mercadolibre_conectar('', $marketplace['Marketplace']);

		$dataToSave = array();

		foreach ($ventas as $venta) {

			$response = $this->MeliMarketplace->mercadolibre_obtener_venta_detalles($marketplace['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);
			
			if (empty($response)) {
				continue;
			}

			$EstatusMeli = $response['status'];
		
			$venta['Venta']['estado_anterior'] = $venta['Venta']['venta_estado_id'];
			$venta['Venta']['venta_estado_id'] = $this->obtener_estado_id($EstatusMeli, $marketplace['Marketplace']['marketplace_tipo_id']);

			if ($this->Session->check('Auth.Administrador.id')) {
				$venta['Venta']['venta_estado_responsable'] = $this->Auth->user('nombre');	
			}

			$dataToSave[] = $venta;

		}

		$this->Venta->saveMany($dataToSave);

	}


	/****************************************************************************************************/
	//actualización de estatus de ventas marcadas como No Atendidas para marketplaces (linio)
	public function actualizar_ventas_anteriores_linio ($ventas, $marketplace) {
		# Para la consola se carga el componente on the fly!
		if ($this->shell) {
			$this->Linio = $this->Components->load('Linio');
		}
		# Cliente Linio
		$this->Linio->crearCliente($marketplace['Marketplace']['api_host'], $marketplace['Marketplace']['api_user'], $marketplace['Marketplace']['api_key']);

		$dataToSave = array();

		foreach ($ventas as $venta) {

			$EstatusLinio = $this->Linio->linio_obtener_venta($venta['Venta']['id_externo']);

			sleep(1);

			$venta['Venta']['estado_anterior'] = $venta['Venta']['venta_estado_id'];
			$venta['Venta']['venta_estado_id'] = $this->obtener_estado_id($EstatusLinio, $marketplace['Marketplace']['marketplace_tipo_id']);

			if ($this->Session->check('Auth.Administrador.id')) {
				$venta['Venta']['venta_estado_responsable'] = $this->Auth->user('nombre');	
			}

			$dataToSave[] = $venta;

		}

		$this->Venta->saveMany($dataToSave);
		
	}


	/****************************************************************************************************/
	//actualización de estatus de ventas marcadas como No Atendidas para tiendas (prestashop)
	public function actualizar_ventas_anteriores_prestashop ($ventas, $tienda) {
		# Para la consola se carga el componente on the fly!
		if ($this->shell) {
			$this->Prestashop = $this->Components->load('Prestashop');
		}
		# Cliente Prestashop
		$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );

		$dataToSave = array();


		foreach ($ventas as $venta) {

			$dataWS = $this->Prestashop->prestashop_obtener_venta($venta['Venta']['id_externo']);
			$dataWS = array(); // Se debe quitar la caché del WS
			if (empty($dataWS)) {

				# Modelos que requieren agregar configuración
				$this->cambiarDatasource(array('Orden'), $tienda);
					
				# Obtenemos la venta directamente de la BD
				$data = ClassRegistry::init('Orden')->find('first', array(
					'conditions' => array(
						'Orden.id_order' => $venta['Venta']['id_externo']
					),
					'fields' => array(
						'Orden.current_state',
						'Orden.total_paid',
						'Orden.total_discounts_tax_incl',
						'Orden.total_shipping_tax_incl',
						'Orden.id_carrier'
					)
				));

				if (empty($data)) {
					$venta['Venta']['estado_anterior'] = 1;
					$venta['Venta']['venta_estado_id'] = 1; //Sin Estado
				}
				else {
					$venta['Venta']['estado_anterior'] = $venta['Venta']['venta_estado_id'];
					$venta['Venta']['venta_estado_id'] = $this->Prestashop->prestashop_obtener_venta_estado($data['Orden']['current_state']);
					$venta['Venta']['costo_envio']     = round($data['Orden']['total_shipping_tax_incl'], 2);
					$venta['Venta']['descuento']       = round($data['Orden']['total_discounts_tax_incl'], 2);
					$venta['Venta']['metodo_envio_id'] = $this->Prestashop->prestashop_obtener_transportista($data['Orden']['id_carrier']);
					$venta['Venta']['total']           = round($data['Orden']['total_paid'], 2);
				}

			}else{

				if (empty($dataWS)) {
					$venta['Venta']['estado_anterior'] = 1;
					$venta['Venta']['venta_estado_id'] = 1; //Sin Estado
				}
				else {
					$venta['Venta']['estado_anterior'] = $venta['Venta']['venta_estado_id'];
					$venta['Venta']['venta_estado_id'] = $this->Prestashop->prestashop_obtener_venta_estado($dataWS['current_state']);
					$venta['Venta']['costo_envio']     = round($dataWS['total_shipping_tax_incl'], 2);
					$venta['Venta']['descuento']       = round($dataWS['total_discounts_tax_incl'], 2);
					$venta['Venta']['metodo_envio_id'] = $this->Prestashop->prestashop_obtener_transportista($dataWS['id_carrier']);
					$venta['Venta']['total']           = round($dataWS['total_paid'], 2);
				}

			}

			if ($this->Session->check('Auth.Administrador.id')) {
				$venta['Venta']['venta_estado_responsable'] = $this->Auth->user('nombre');	
			}

			$dataToSave[] = $venta;

		}
		
		$this->Venta->saveMany($dataToSave);

	}


	/****************************************************************************************************/
	//recorre las tiendas y marketplaces, busca las ventas para actualizar y las envía a los métodos
	//actualizar_ventas_anteriores_prestashop, actualizar_ventas_anteriores_linio y actualizar_ventas_anteriores_mercadolibre, según corresponda
	public function actualizar_ventas_anteriores () {

		$this->layout = 'ajax';

		set_time_limit(0);

		//----------------------------------------------------------------------------------------------------
		//lista de tiendas (prestashop)
		$tiendas = $this->Venta->Tienda->find(
			'all',
			array(
				'conditions' => array(
					'Tienda.activo' => 1,
					'Tienda.apiurl_prestashop <>' => '',
					'Tienda.apikey_prestashop <>' => ''
				),
				'fields' => array(
					'Tienda.id', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop', 'Tienda.configuracion'
				)
			)
		);

		//recorrido de las tiendas (prestashop)
		foreach ($tiendas as $tienda) {

			$ventas = $this->Venta->find(
				'all',
				array(
					'conditions' => array(
						'Venta.atendida' => 0,
						'Venta.activo' => 1,
						'Venta.tienda_id' => $tienda['Tienda']['id'],
						'Venta.marketplace_id IS NULL'
					),
					'fields' => array(
						'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.venta_estado_id', 'Venta.estado_anterior'
					)
				)
			);
			
			if (!empty($ventas)) {
				$this->actualizar_ventas_anteriores_prestashop($ventas, $tienda);
			}
			
		}

		//----------------------------------------------------------------------------------------------------
		//lista de marketplaces (linio y mercado libre)
		$marketplaces = $this->Venta->Marketplace->find(
			'all',
			array(
				'contain' => array(
					'MarketplaceTipo' => array(
						'fields' => array(
							'MarketplaceTipo.nombre'
						)
					)
				),
				'conditions' => array(
					'Marketplace.activo' => 1
				),
				'fields' => array(
					'Marketplace.id', 'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key', 'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token', 'Marketplace.seller_id', 'Marketplace.marketplace_tipo_id', 'Marketplace.nombre'
				)
			)
		);

		//recorrido de los marketplaces (linio y mercado libre)
		foreach ($marketplaces as $marketplace) {

			$ventas = $this->Venta->find(
				'all',
				array(
					'conditions' => array(
						'Venta.atendida' => 0,
						'Venta.activo' => 1,
						'Venta.marketplace_id' => $marketplace['Marketplace']['id']
					),
					'fields' => array(
						'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.venta_estado_id', 'Venta.estado_anterior'
					)
				)
			);
			
			if (!empty($ventas)) {

				switch ($marketplace['Marketplace']['marketplace_tipo_id']) {

					case 1:
						$this->actualizar_ventas_anteriores_linio($ventas, $marketplace);
					break;

					case 2:
						$this->actualizar_ventas_anteriores_mercadolibre($ventas, $marketplace);
					break;

				}

			}
			
		}

	}


	/**
	 * Devuelve el stock de las ventas con eststados que tanga habilitada la opción de
	 * revertir stock
	 * @param  array  $excluir Id y tipo de canal el cual se excluirá la actualización
	 * @return void
	 */
	public function ventas_estados_revertidas( $excluir = array() )
	{	
		$ventas = $this->Venta->find('all', array(
			'conditions' => array(
				'Venta.activo' => 1,
				'Venta.atendida' => 0
			),
			'contain' => array(
				'VentaEstado' => array(
					'fields' => array(
						'VentaEstado.revertir_stock'
					)
				),
				'VentaDetalle' => array(
					'VentaDetalleProducto' => array(
						'fields' => array(
							'VentaDetalleProducto.cantidad_virtual',
							'VentaDetalleProducto.id_externo',
							'VentaDetalleProducto.id'
						)
					),
					'fields' => array(
						'VentaDetalle.cantidad'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.venta_estado_id',
				'Venta.estado_anterior'
			)
		));

		# agrupa los nuevos stocks de los productos
		$productoStocks = array();
		
		if (!empty($ventas)) {

			# Descontar stock virtual y refrescar canales
			$productosController = new VentaDetalleProductosController();

			foreach ($ventas as $iv => $venta) {

				# solo se procesa si el estado de la venta ha cambiado
				if ($venta['VentaEstado']['revertir_stock'] && $venta['Venta']['venta_estado_id'] != $venta['Venta']['estado_anterior'] ) {
					/*foreach ($venta['VentaDetalle'] as $ip => $producto) {

						$productoStocks[$producto['VentaDetalleProducto']['id']]['id_externo'] = $producto['VentaDetalleProducto']['id_externo'];
						//$productoStocks[$producto['VentaDetalleProducto']['id']]['descontar']  = $producto['cantidad'];

						if (!isset($productoStocks[$producto['VentaDetalleProducto']['id']]['nueva_cantidad'])) {
							$productoStocks[$producto['VentaDetalleProducto']['id']]['nueva_cantidad']   = $producto['VentaDetalleProducto']['cantidad_virtual'] + $producto['cantidad'];
						}else{
							$productoStocks[$producto['VentaDetalleProducto']['id']]['nueva_cantidad']   = $productoStocks[$producto['VentaDetalleProducto']['id']]['nueva_cantidad'] + $producto['cantidad'];
						}
						
					}*/

					# Devolvemos el stock reservado si corresponde
					$this->Venta->cancelar_venta($venta['Venta']['id']);



				}
			}
			
			# Se refrescan solo una vez cada producto
			foreach ($productoStocks as $id_producto => $data) {
				#$productosController->descontar_stock_virtual($id_producto, $data['id_externo'], $data['nueva_cantidad'], $excluir);
			}

		}

		return;

	}


	/**
	 * Según el estado que tenga la venta se marca como finalizada o no
	 * @return void
	 */
	public function ventas_estados_atendidos( )
	{
		$ventas = $this->Venta->find('all', array(
			'conditions' => array(
				'Venta.activo' => 1
			),
			'contain' => array(
				'VentaEstado' => array(
					'fields' => array(
						'VentaEstado.marcar_atendida'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.venta_estado_id',
				'Venta.estado_anterior',
				'Venta.fecha_venta',
				'Venta.atendida'
			)
		));


		$limite_mes  = mktime(0, 0, 0, date("m")-3, date("d"),   date("Y"));
		
		if (!empty($ventas)) {

			foreach ($ventas as $iv => $venta) {

				if ($venta['VentaEstado']['marcar_atendida'] && !$venta['Venta']['atendida']) {
					$this->Venta->save(array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'atendida' => 1
						)
					)); 
				}elseif ( strtotime($venta['Venta']['fecha_venta']) < $limite_mes && !$venta['Venta']['atendida']) { // si es una venta muy antigua se cierra
					$this->Venta->save(array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'atendida' => 1
						)
					)); 
				}elseif ($venta['VentaEstado']['marcar_atendida'] && $venta['Venta']['atendida'] && $venta['Venta']['venta_estado_id'] != $venta['Venta']['estado_anterior']) {
					$this->Venta->save(array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'atendida' => 1
						)
					)); 
				}elseif ($venta['Venta']['atendida']) {
					continue; // Ya fue atendida
				}else{
					$this->Venta->save(array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'atendida' => 0
						)
					));
				}
			}

		}

		return;
	}


	/**
	 * [ventas_estados_pagadas description]
	 * @return [type] [description]
	 */
	public function ventas_estados_pagadas()
	{
		$ventas = $this->Venta->find('all', array(
			'conditions' => array(
				'Venta.activo'        => 1,
				'Venta.atendida'      => 0
			),
			'fields' => array(
				'Venta.id',
				'Venta.atendida'
			)
		));
		
		if (!empty($ventas)) {

			foreach ($ventas as $iv => $venta) {

				$this->Venta->pagar_venta($venta['Venta']['id']);

			}
		}

		return;
	}



	/****************************************************************************************************/
	//actualización de ventas
	public function admin_actualizar_ventas () {
		
		$this->layout = 'ajax';

		set_time_limit(0);

		# Mercadolibre conectar
		#$this->admin_verificar_conexion_meli();

		//actualización de estatus de ventas marcadas como No Atendidas
		#$this->actualizar_ventas_anteriores();

		//ids de productos para actualizar su stock al finalizar la carga de ventas
		$ArrayProductosSincronizacion = array();
		$ArrayCantidadesVendidos = array();

		//se buscan las tiendas que se deben procesar
		$tiendas = $this->obtener_tiendas();

		//si hay tiendas para procesar
		if (!empty($tiendas)) {

			//ciclo para procesar cada tienda (prestashop)
			foreach ($tiendas as $tienda) {
				# Para la consola se carga el componente on the fly!
				if ($this->shell) {
					$this->Prestashop = $this->Components->load('Prestashop');
				}

				# Cliente Prestashop
				$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );

				//si se cargaron ventas
				if ($TiendaVentas = $this->prestashop_obtener_ventas($tienda)) {

					if (!isset($TiendaVentas['order'][0])) {
						$TiendaVentas = array(
							'order' => array(
								'0' => $TiendaVentas['order']
							)
						);
					}
					
					//ciclo de ventas
					foreach ($TiendaVentas['order'] as $DataVenta) {
						
						#Vemos si existe en la BD
						$existe = $this->Venta->find('first', array(
							'conditions' => array(
								'Venta.id_externo'     => $DataVenta['id'],
								'Venta.tienda_id'      => $tienda['Tienda']['id'],
								'Venta.marketplace_id' => null
							)
						));

						if (!empty($existe)) {
							continue;
						}


						//datos de la venta a registrar
						$NuevaVenta                         = array();
						$NuevaVenta['Venta']['tienda_id']   = $tienda['Tienda']['id'];
						$NuevaVenta['Venta']['id_externo']  = $DataVenta['id'];
						$NuevaVenta['Venta']['referencia']  = $DataVenta['reference'];
						$NuevaVenta['Venta']['fecha_venta'] = $DataVenta['date_add'];
						$NuevaVenta['Venta']['descuento']   = round($DataVenta['total_discounts_tax_incl'], 2);
						$NuevaVenta['Venta']['costo_envio'] = round($DataVenta['total_shipping_tax_incl'], 2);
						$NuevaVenta['Venta']['total']       = round($DataVenta['total_paid'], 2);

						//se obtienen las transacciones de una venta
						//si la venta tiene transacciones asociadas
						if ($VentaTransacciones = $this->Prestashop->prestashop_obtener_venta_transacciones($DataVenta['reference'])) {

							if (!isset($VentaTransacciones['order_payment'][0])) {
								$VentaTransacciones = array(
									'order_payment' => array(
										'0' => $VentaTransacciones['order_payment']
									)
								);
							}
							
							foreach ($VentaTransacciones['order_payment'] as $transaccion) {

								$NuevaTransaccion = array();

								if (!empty($transaccion['transaction_id'])) {
									$NuevaTransaccion['nombre'] = $transaccion['transaction_id'];
								}

								$NuevaTransaccion['monto'] = (!empty($transaccion['amount'])) ? $transaccion['amount'] : 0;

								$NuevaVenta['VentaTransaccion'][] = $NuevaTransaccion;
								
							}

						}

						# Direccion de entrega
						$direccionEntrega = $this->Prestashop->prestashop_obtener_venta_direccion($DataVenta['id_address_delivery']);


						// Dirección de entrega
						if (!isset($DataVenta['address'])) {
							
							$direccion_entrega = '';
							$comuna_entrega    = '';
							$nombre_receptor   = '';
							$fono_receptor     = '';

							if (!empty($direccionEntrega['address']['address1'])) {

								if (is_array($direccionEntrega['address']['address1'])) {
									$direccionEntrega['address']['address1'] = implode(', ', $direccionEntrega['address']['address1']);
								}

								$direccion_entrega .= $direccionEntrega['address']['address1'];
							}

							if (!empty($direccionEntrega['address']['address2'])) {

								if (is_array($direccionEntrega['address']['address2'])) {
									$direccionEntrega['address']['address2'] = implode(', ', $direccionEntrega['address']['address2']);
								}

								$direccion_entrega .= ', ' . $direccionEntrega['address']['address2'];
							}

							if (!empty($direccionEntrega['address']['other'])) {
								if (is_array($direccionEntrega['address']['other'])) {
									$direccionEntrega['address']['other'] = implode(', ', $direccionEntrega['address']['other']);
								}
								$direccion_entrega .= ', ' . $direccionEntrega['address']['other'];
							}

							if (!empty($direccionEntrega['address']['city'])) {
								$comuna_entrega .= $direccionEntrega['address']['city'];
							}

							if (!empty($direccionEntrega['address']['firstname'])) {
								$nombre_receptor .= $direccionEntrega['address']['firstname'] . ' ' . $direccionEntrega['address']['lastname'];
							}

							if (!empty($direccionEntrega['address']['phone'])) {

								if (is_array($direccionEntrega['address']['phone'])) {
									$direccionEntrega['address']['phone'] = implode(' - ', $direccionEntrega['address']['phone']);
								}

								$fono_receptor .= trim($direccionEntrega['address']['phone']);
							}

							if (!empty($direccionEntrega['address']['phone_mobile'])) {
								$fono_receptor .=  ' - ' . trim($direccionEntrega['address']['phone_mobile']);
							}


							$NuevaVenta['Venta']['direccion_entrega'] =  $direccion_entrega;
							$NuevaVenta['Venta']['comuna_entrega']    =  $comuna_entrega;
							$NuevaVenta['Venta']['nombre_receptor']   =  $nombre_receptor;
							$NuevaVenta['Venta']['fono_receptor']     =  $fono_receptor;
						}

						//se obtienen el detalle de la venta
						$VentaDetalles = $this->Prestashop->prestashop_obtener_venta_detalles($DataVenta['id']);

						if (isset($VentaDetalles['order_detail']) && !isset($VentaDetalles['order_detail'][0])) {
							$VentaDetalles = array(
								'order_detail' => array(
									'0' => $VentaDetalles['order_detail']
								)
							);
						}

						//se obtiene el estado de la venta
						if (empty($DataVenta['current_state']) || $DataVenta['current_state'] == 0) {
							$NuevaVenta['Venta']['venta_estado_id'] = 1; //Sin Estado
							$NuevaVenta['Venta']['estado_anterior'] = 1;
						}
						else {
							$NuevaVenta['Venta']['venta_estado_id'] = $this->Prestashop->prestashop_obtener_venta_estado($DataVenta['current_state']);
							$NuevaVenta['Venta']['estado_anterior'] = $NuevaVenta['Venta']['venta_estado_id'];
						}

						$NuevaVenta['Venta']['metodo_envio_id']  = $this->Prestashop->prestashop_obtener_transportista($DataVenta['id_carrier']);

						//se obtiene el medio de pago
						$NuevaVenta['Venta']['medio_pago_id']    = $this->Prestashop->prestashop_obtener_medio_pago($DataVenta['payment']);
						
						//se obtiene el cliente
						$NuevaVenta['Venta']['venta_cliente_id'] = $this->Prestashop->prestashop_obtener_cliente($DataVenta['id_customer']);

						// Existen ventas sin productos xD
						if (isset($VentaDetalles['order_detail'])) {
							//ciclo para recorrer el detalle de la venta
							foreach ($VentaDetalles['order_detail'] as $DetalleVenta) {
								if (!empty($DetalleVenta['product_id'])) {
									
									$NuevoDetalle = array();
									$NuevoDetalle['venta_detalle_producto_id']  = $DetalleVenta['product_id'];
									$NuevoDetalle['precio']                     = round($DetalleVenta['unit_price_tax_excl'], 2);
									$NuevoDetalle['precio_bruto']               = round($DetalleVenta['unit_price_tax_incl'], 2);
									$NuevoDetalle['cantidad']                   = $DetalleVenta['product_quantity'];
									$NuevoDetalle['cantidad_pendiente_entrega'] = $DetalleVenta['product_quantity'];
									$NuevoDetalle['cantidad_reservada'] 		= 0;

									if (ClassRegistry::init('VentaEstado')->es_estado_pagado($NuevaVenta['Venta']['venta_estado_id'])) {
										$NuevoDetalle['cantidad_reservada']     = ClassRegistry::init('Bodega')->calcular_reserva_stock($DetalleVenta['product_id'], $DetalleVenta['product_quantity']);	
									}

									$NuevaVenta['VentaDetalle'][] = $NuevoDetalle;

									# Evitamos que se vuelva actualizar el stock en prestashop
									$excluirPrestashop = array('Prestashop' => array($tienda['Tienda']['id']));

									//se guarda el producto si no existe
									$this->prestashop_guardar_producto($DetalleVenta, $excluirPrestashop);

									//se toma el id de producto para usarlo luego en la sincronización de stock
									if (!in_array($DetalleVenta['product_id'], $ArrayProductosSincronizacion)) {
										$ArrayProductosSincronizacion[] = $DetalleVenta['product_id'];
									}

								}
								
							} //fin ciclo detalle de venta
						}

						# si la venta tiene sus productos reservados ésta disponible para ser procesada
						if (array_sum(Hash::extract($NuevaVenta['VentaDetalle'], '{n}.cantidad_reservada')) == array_sum(Hash::extract($NuevaVenta['VentaDetalle'], '{n}.cantidad'))) {
							$NuevaVenta['Venta']['picking_estado'] = 'empaquetar';
						}
						
						//se guarda la venta
						$this->Venta->create();
						$this->Venta->saveAll($NuevaVenta);

					} //fin ciclo de ventas

				} //fin si se cargaron ventas

				//----------------------------------------------------------------------------------------------------
				//registro de las ventas de marketplaces asociados a la tienda
				if (!empty($tienda['Marketplace'])) {

					//recorrido de marketplaces
					foreach ($tienda['Marketplace'] as $marketplace) {

						//----------------------------------------------------------------------------------------------------
						//si el marketplace es Linio
						if ($marketplace['marketplace_tipo_id'] == 1) {
							# Para la consola se carga el componente on the fly!
							if ($this->shell) {
								$this->Linio = $this->Components->load('Linio');
							}

							# Cliente Linio	
							$this->Linio->crearCliente($marketplace['api_host'], $marketplace['api_user'], $marketplace['api_key']);

							$finalizarLinio = false;

							do {
								
								$LinioVentas = $this->Linio->linio_obtener_ventas($marketplace['id'], $finalizarLinio);
									
								//si se cargaron ventas
								if ($LinioVentas) {

									if (!isset($LinioVentas[0])) {
										$LinioVentas = array(
											'0' => $LinioVentas
										);
									}

									//ciclo de ventas
									foreach ($LinioVentas as $DataVenta) {

										#Vemos si existe en la BD
										$existe = $this->Venta->find('first', array(
											'conditions' => array(
												'Venta.id_externo'     => $DataVenta['OrderId'],
												'Venta.tienda_id'      => $tienda['Tienda']['id'],
												'Venta.marketplace_id' => $marketplace['id']
											)
										));

										if (!empty($existe)) {
											continue;
										}

										//datos de la venta a registrar
										$NuevaVenta = array();
										$NuevaVenta['Venta']['tienda_id']      = $tienda['Tienda']['id'];
										$NuevaVenta['Venta']['marketplace_id'] = $marketplace['id'];
										$NuevaVenta['Venta']['id_externo']     = $DataVenta['OrderId'];
										$NuevaVenta['Venta']['referencia']     = $DataVenta['OrderNumber'];
										$NuevaVenta['Venta']['fecha_venta']    = $DataVenta['CreatedAt'];
										$NuevaVenta['Venta']['total']          = $DataVenta['Price'];
										//$NuevaVenta['Venta']['total'] = 0;
										
										// Guardar transacción
										$NuevaTransaccion = array();

										if (!empty($DataVenta['OrderNumber'])) {
											$NuevaTransaccion['nombre'] = $DataVenta['OrderNumber'];
										}

										$NuevaTransaccion['monto'] = (!empty($DataVenta['Price'])) ? $DataVenta['Price'] : 0;
										$NuevaTransaccion['fee']   = ($NuevaTransaccion['monto'] * ($marketplace['fee'] / 100));

										$NuevaVenta['VentaTransaccion'][] = $NuevaTransaccion;

												
										//se obtienen el detalle de la venta
										$VentaDetalles = $this->Linio->linio_obtener_venta_detalles($DataVenta['OrderId']);
										
										if (!isset($VentaDetalles[0])) {
											$VentaDetalles = array(
												'0' => $VentaDetalles
											);
										}
										
										// Direccion despacho
										$NuevaVenta['Venta']['direccion_entrega'] =  $DataVenta['AddressShipping']['Address1'] . ', ' . $DataVenta['AddressShipping']['Address2'];
										$NuevaVenta['Venta']['comuna_entrega']    =  $DataVenta['AddressShipping']['City'];
										$NuevaVenta['Venta']['nombre_receptor']   =  $DataVenta['AddressShipping']['FirstName'] . ' ' . $DataVenta['AddressShipping']['LastName'];
										$NuevaVenta['Venta']['fono_receptor']     =  trim($DataVenta['AddressShipping']['Phone']) . '-' .  trim($DataVenta['AddressShipping']['Phone2']) ;
										
										$totalDespacho = (float) 0;

										$metodo_envio  = '';

										$NuevaVenta['Venta']['costo_envio']      = (float) $totalDespacho;
										
										//se obtiene el estado de la venta
										$NuevaVenta['Venta']['venta_estado_id']  = $this->obtener_estado_id($DataVenta['Statuses']['Status'], $marketplace['marketplace_tipo_id']);
										$NuevaVenta['Venta']['estado_anterior']  = $NuevaVenta['Venta']['venta_estado_id'];
										
										//se obtiene el medio de pago
										$NuevaVenta['Venta']['medio_pago_id']    = $this->obtener_medio_pago_id($DataVenta['PaymentMethod']);

										//se obtiene el metodo de envio
										$NuevaVenta['Venta']['metodo_envio_id']  = $this->obtener_metodo_envio_id($metodo_envio);
										
										//se obtiene el cliente
										$NuevaVenta['Venta']['venta_cliente_id'] = $this->obtener_cliente_id($DataVenta);

										$NuevaVenta['Venta']['total'] 			 = (float) 0; // El total se calcula en en base a la sumatoria de items

										//ciclo para recorrer el detalle de la venta
										foreach ($VentaDetalles as $DetalleVenta) {

											$DetalleVenta['Sku'] = intval($DetalleVenta['Sku']);

											# Evitamos que se vuelva actualizar el stock en linio
											$excluirLinio = array('Linio' => array($marketplace['id']));

											//se guarda el producto si no existe
											$idNuevoProducto = $this->linio_guardar_producto($DetalleVenta, $excluirLinio);

											$NuevoDetalle = array();
											$NuevoDetalle['venta_detalle_producto_id'] = $idNuevoProducto;

											if ( round($DetalleVenta['VoucherAmount']) > 0 ) {
												$NuevoDetalle['precio']                    = $this->precio_neto(round($DetalleVenta['PaidPrice'] + $DetalleVenta['VoucherAmount'], 2));
												$NuevoDetalle['precio_bruto']              = round($DetalleVenta['PaidPrice'] + $DetalleVenta['VoucherAmount'], 2);	
											}else{
												$NuevoDetalle['precio']                    = $this->precio_neto(round($DetalleVenta['PaidPrice'], 2));
												$NuevoDetalle['precio_bruto']              = $DetalleVenta['PaidPrice'];
											}
											
											$NuevoDetalle['cantidad_pendiente_entrega'] = 1;
											$NuevoDetalle['cantidad_reservada']         = 0;
											$NuevoDetalle['cantidad']         			= 1;

											if (ClassRegistry::init('VentaEstado')->es_estado_pagado($NuevaVenta['Venta']['venta_estado_id'])) {
												$NuevoDetalle['cantidad_reservada']    = ClassRegistry::init('Bodega')->calcular_reserva_stock($idNuevoProducto, 1);	
											}

											# OBtenemos el último metodo de envio
											$metodo_envio = $DetalleVenta['ShipmentProvider'];

											$totalDespacho = $totalDespacho + round($DetalleVenta['ShippingAmount'], 2);

											// Se agrega el valor de la compra sumanod el precio de los productos
											$NuevaVenta['Venta']['total'] = $NuevaVenta['Venta']['total'] + $NuevoDetalle['precio_bruto'];

											$NuevaVenta['VentaDetalle'][] = $NuevoDetalle;
											
											//se toma el id de producto para usarlo luego en la sincronización de stock
											if (!in_array($DetalleVenta['Sku'], $ArrayProductosSincronizacion)) {
												$ArrayProductosSincronizacion[] = $DetalleVenta['Sku'];
											}

											//se aumenta la cantidad de vendidos
											$pos = array_search($DetalleVenta['Sku'], $ArrayProductosSincronizacion);

											if (!isset($ArrayCantidadesVendidos[$pos])) {
												$ArrayCantidadesVendidos[$pos] = 1;
											}
											else {
												$ArrayCantidadesVendidos[$pos]++;
											}

										} //fin ciclo detalle de venta


										# si la venta tiene sus productos reservados ésta disponible para ser procesada
										if (array_sum(Hash::extract($NuevaVenta['VentaDetalle'], '{n}.cantidad_reservada')) == array_sum(Hash::extract($NuevaVenta['VentaDetalle'], '{n}.cantidad'))) {
											$NuevaVenta['Venta']['picking_estado'] = 'empaquetar';
										}

										/*
										obtener mensajes de la venta
										$mensajes = $this->Linio->linio_obtener_venta_mensajes ($DataVenta['OrderId'], $ConexionLinio);
										*/
										#prx($DataVenta);
										//se guarda la venta
										$this->Venta->create();
										$this->Venta->saveAll($NuevaVenta);

									} //fin ciclo de ventas

								} //fin si se cargaron ventas

								sleep(1); // Delay de 3 segunos para evitar excepción Too many request

							} while ( !$finalizarLinio );

						} //fin si el marketplace es Linio


						# Es mercadolibre
						if ($marketplace['marketplace_tipo_id'] == 2) {
							# Para la consola se carga el componente on the fly!
							if ($this->shell) {
								$this->MeliMarketplace = $this->Components->load('MeliMarketplace');
							}

							# cliente y conexion Meli
							$this->MeliMarketplace->crearCliente( $marketplace['api_user'], $marketplace['api_key'], $marketplace['access_token'], $marketplace['refresh_token'] );
							$this->MeliMarketplace->mercadolibre_conectar('', $marketplace);

							$ventasMercadolibre = $this->MeliMarketplace->mercadolibre_obtener_ventas($marketplace);
							
							if (count($ventasMercadolibre['ventasMercadolibre']) > 0) {
								//ciclo de ventas
								foreach ($ventasMercadolibre['ventasMercadolibre'] as $DataVenta) {
									
									#Vemos si existe en la BD
									$existe = $this->Venta->find('first', array(
										'conditions' => array(
											'Venta.id_externo'     => $DataVenta['id'],
											'Venta.tienda_id'      => $tienda['Tienda']['id'],
											'Venta.marketplace_id' => $marketplace['id']
										)
									));

									if (!empty($existe)) {
										continue;
									}
										
									//datos de la venta a registrar
									$NuevaVenta                            = array();
									$NuevaVenta['Venta']['tienda_id']      = $tienda['Tienda']['id'];
									$NuevaVenta['Venta']['marketplace_id'] = $marketplace['id'];
									$NuevaVenta['Venta']['id_externo']     = $DataVenta['id'];
									$NuevaVenta['Venta']['referencia']     = $DataVenta['id'];

									
									$NuevaVenta['Venta']['fecha_venta']    = CakeTime::format($DataVenta['date_created'], '%Y-%m-%d %H:%M:%S');
									$NuevaVenta['Venta']['total']          = round($DataVenta['total_amount'], 2);

									# costo envio
									if (isset($DataVenta['shipping']['cost'])) {
										$NuevaVenta['Venta']['costo_envio'] = $DataVenta['shipping']['cost'];
										$NuevaVenta['Venta']['total']       = round($DataVenta['total_amount'] + $DataVenta['shipping']['cost'], 2);
									}
									
									//se obtienen el detalle de la venta
									$VentaDetalles = $this->MeliMarketplace->mercadolibre_obtener_venta_detalles($marketplace['access_token'], $DataVenta['id'], true);
									
									// Detalles de envio
									$direccion_entrega = '';
									$comuna_entrega  = '';
									$nombre_receptor = '';
									$fono_receptor   = '';

									if (isset($VentaDetalles['shipping']['receiver_address']['address_line'])
										&& isset($VentaDetalles['shipping']['receiver_address']['city']['name'])) {
										$direccion_entrega = $VentaDetalles['shipping']['receiver_address']['address_line'] . ', ' . $VentaDetalles['shipping']['receiver_address']['city']['name'];
									}

									if (isset($VentaDetalles['shipping']['receiver_address']['city']['name'])) {
										$comuna_entrega = $VentaDetalles['shipping']['receiver_address']['city']['name'];
									}

									if (isset($VentaDetalles['shipping']['receiver_address']['receiver_name'])) {
										$nombre_receptor = $VentaDetalles['shipping']['receiver_address']['receiver_name'];
									}

									if (isset($VentaDetalles['shipping']['receiver_address']['receiver_phone'])) {
										$fono_receptor = $VentaDetalles['shipping']['receiver_address']['receiver_phone'];
									}

									// Direccion despacho
									$NuevaVenta['Venta']['direccion_entrega'] =  $direccion_entrega;
									$NuevaVenta['Venta']['comuna_entrega']    =  $comuna_entrega;
									$NuevaVenta['Venta']['nombre_receptor']   =  $nombre_receptor;
									$NuevaVenta['Venta']['fono_receptor']     =  $fono_receptor;
									
									if (!isset($VentaDetalles['order_items'][0])) {
										$VentaDetalles['order_items'] = array(
											'0' => $VentaDetalles['order_items']
										);
									}

									# Mercado libre puede tener más de 1 pago
									foreach ($DataVenta['payments'] as $venta) {
										//se obtiene el estado de la venta
										$NuevaVenta['Venta']['venta_estado_id'] = $this->obtener_estado_id($venta['status'], $marketplace['marketplace_tipo_id']);
										$NuevaVenta['Venta']['estado_anterior'] = $NuevaVenta['Venta']['venta_estado_id'];
										
										//se obtiene el medio de pago
										$NuevaVenta['Venta']['medio_pago_id']   = $this->obtener_medio_pago_id($venta['payment_type']);

										$NuevaTransaccion = array();

										if (!empty($venta['id'])) {
											$NuevaTransaccion['nombre'] = $venta['id'];
										}

										$NuevaTransaccion['monto'] = (!empty($venta['total_paid_amount'])) ? $venta['total_paid_amount'] : 0;
										$NuevaTransaccion['fee'] = (!empty($venta['marketplace_fee'])) ? $venta['marketplace_fee'] : 0;

										$NuevaVenta['VentaTransaccion'][] = $NuevaTransaccion;
									}


									if (isset($DataVenta['shipping']['shipping_option']['name'])) {
										//se obtiene el metodo de envio
										$NuevaVenta['Venta']['metodo_envio_id']  = $this->obtener_metodo_envio_id($DataVenta['shipping']['shipping_option']['name']);	
									}else{
										$NuevaVenta['Venta']['metodo_envio_id']  = $this->obtener_metodo_envio_id('Marketplace Externo');	
									}

									//se obtiene el cliente
									$NuevaVenta['Venta']['venta_cliente_id'] = $this->MeliMarketplace->mercadolibre_obtener_cliente($DataVenta);

									
									# Obtener mensajes de la venta
									$mensajes = $this->MeliMarketplace->mercadolibre_obtener_mensajes($marketplace['access_token'], $DataVenta['id']);

									foreach ($mensajes as $im => $mensaje) {
	
										$NuevaVenta['VentaMensaje'][$im]['nombre']   = $mensaje['subject'];
										$NuevaVenta['VentaMensaje'][$im]['fecha']    = CakeTime::format($mensaje['date'], '%Y-%m-%d %H:%M:%S');
										$NuevaVenta['VentaMensaje'][$im]['emisor']   = $mensaje['from']['user_id'];
										$NuevaVenta['VentaMensaje'][$im]['mensaje']  = $this->removeEmoji($mensaje['text']['plain']);

									}

									//ciclo para recorrer el detalle de la venta
									foreach ($VentaDetalles['order_items'] as $DetalleVenta) {
										if (!empty($DetalleVenta['item']['seller_custom_field']) ) {
											
											$DetalleVenta['Sku']  = intval($DetalleVenta['item']['seller_custom_field']);
											$DetalleVenta['Name'] = $DetalleVenta['item']['title'];

											if ($DetalleVenta['Sku'] == 0) {
												$DetalleVenta['Sku'] = $DetalleVenta['item']['seller_sku'];
											}

											if ($DetalleVenta['Sku'] == 0) {
												continue;
											}

											# Evitamos que se vuelva actualizar el stock en meli
											$excluirMeli = array('Mercadolibre' => array($marketplace['id']));

											//se guarda el producto si no existe
											$idNuevoProducto = $this->linio_guardar_producto($DetalleVenta, $excluirMeli);

											$NuevoDetalle                               = array();
											$NuevoDetalle['venta_detalle_producto_id']  = $idNuevoProducto;
											$NuevoDetalle['precio']                     = $this->precio_neto(round($DetalleVenta['unit_price'], 2));
											$NuevoDetalle['precio_bruto']               = round($DetalleVenta['unit_price'], 2);
											$NuevoDetalle['cantidad']                   = $DetalleVenta['quantity'];
											$NuevoDetalle['cantidad_pendiente_entrega'] = $DetalleVenta['quantity'];
											$NuevoDetalle['cantidad_reservada'] 		= 0;

											if (ClassRegistry::init('VentaEstado')->es_estado_pagado($NuevaVenta['Venta']['metodo_envio_id'])) {
												$NuevoDetalle['cantidad_reservada'] 	= ClassRegistry::init('Bodega')->calcular_reserva_stock($idNuevoProducto, $DetalleVenta['quantity']);	
											}											

											$NuevaVenta['VentaDetalle'][] = $NuevoDetalle;


											//se toma el id de producto para usarlo luego en la sincronización de stock
											if (!in_array($DetalleVenta['Sku'], $ArrayProductosSincronizacion)) {
												$ArrayProductosSincronizacion[] = $DetalleVenta['Sku'];
											}

											//se aumenta la cantidad de vendidos
											$pos = array_search($DetalleVenta['Sku'], $ArrayProductosSincronizacion);

											if (!isset($ArrayCantidadesVendidos[$pos])) {
												$ArrayCantidadesVendidos[$pos] = 1;
											}
											else {
												$ArrayCantidadesVendidos[$pos]++;
											}
											
										} // fin no empty
									
									} //fin ciclo detalle de venta


									# si la venta tiene sus productos reservados ésta disponible para ser procesada
									if (array_sum(Hash::extract($NuevaVenta['VentaDetalle'], '{n}.cantidad_reservada')) == array_sum(Hash::extract($NuevaVenta['VentaDetalle'], '{n}.cantidad'))) {
										$NuevaVenta['Venta']['picking_estado'] = 'empaquetar';
									}

									//se guarda la venta
									$this->Venta->create();
									$this->Venta->saveAll($NuevaVenta);

								} //fin ciclo de ventas
							} // Fin agregar mercadopago

						} // Fin mercadolibre

					} //fin ciclo de marketplaces

				} //fin si hay marketplaces para procesar

				//----------------------------------------------------------------------------------------------------
				//sincronizar stock de productos
				/*if (!empty($ArrayProductosSincronizacion)) {

					$this->sincronizar_stock_productos($tienda, $ArrayProductosSincronizacion, $ArrayCantidadesVendidos, $ConexionPrestashop);

				}*/ // fin si hay productos para sincronizar

			} //fin ciclo de tiendas

		} //fin si hay ventas para procesar


		# revertir el stock virtual del producto segun su estado
		$this->ventas_estados_revertidas();

		# Marca las ventas como atendidas segun su estado.
		$this->ventas_estados_atendidos();
		
		if (!$this->shell) {
			$this->Session->setFlash('Actualización realizada correctamente', null, array(), 'success');

			$this->redirect(array('action' => 'index'));
		}

	}


	/**
	 * [admin_exportar description]
	 * @return [type] [description]
	 */
	public function admin_exportar () {

		set_time_limit(0);

		ini_set('memory_limit', '-1');

		$condiciones = array();
		$joins = array();

		//----------------------------------------------------------------------------------------------------
		//info de venta
		$FiltroVenta = "";
		
		if (isset($this->request->data['Venta']['filtroventa'])) {

			$FiltroVenta = trim($this->request->data['Venta']['filtroventa']);

			if ($FiltroVenta != "") {

				$condiciones["OR"] = array(
					"Venta.id LIKE '%" .$FiltroVenta. "%'",
					"Venta.id_externo LIKE '%" .$FiltroVenta. "%'",
					"Venta.referencia LIKE '%" .$FiltroVenta. "%'"
				);
				
			}

		}

		//----------------------------------------------------------------------------------------------------
		//info de cliente
		$FiltroCliente = "";
		
		if (isset($this->request->data['Venta']['filtrocliente'])) {

			$FiltroCliente = trim($this->request->data['Venta']['filtrocliente']);

			if ($FiltroCliente != "") {

				$joins[] = array(
					'table' => 'rp_venta_clientes',
					'alias' => 'clientes',
					'type' => 'INNER',
					'conditions' => array(
						'clientes.id = Venta.venta_cliente_id',
						'OR' => array(
							"clientes.nombre LIKE '%" .$FiltroCliente. "%'",
							"clientes.apellido LIKE '%" .$FiltroCliente. "%'",
							"clientes.rut LIKE '%" .$FiltroCliente. "%'",
							"clientes.email LIKE '%" .$FiltroCliente. "%'",
							"clientes.telefono LIKE '%" .$FiltroCliente. "%'"
						)
					)
				);
				
			}

		}

		//----------------------------------------------------------------------------------------------------
		//tienda
		$FiltroTienda = "";
		
		if (isset($this->request->data['Venta']['tienda_id'])) {

			$FiltroTienda = $this->request->data['Venta']['tienda_id'];

			if ($FiltroTienda != "") {
				$condiciones['Venta.tienda_id'] = $FiltroTienda;
			} 

		}

		//----------------------------------------------------------------------------------------------------
		//marketplace
		$FiltroMarketplace = "";
		
		if (isset($this->request->data['Venta']['marketplace_id'])) {

			$FiltroMarketplace = $this->request->data['Venta']['marketplace_id'];

			if ($FiltroMarketplace != "") {
				$condiciones['Venta.marketplace_id'] = $FiltroMarketplace;
			} 

		}

		//----------------------------------------------------------------------------------------------------
		//medio de pago
		$FiltroMedioPago = "";
		
		if (isset($this->request->data['Venta']['medio_pago_id'])) {

			$FiltroMedioPago = $this->request->data['Venta']['medio_pago_id'];

			if ($FiltroMedioPago != "") {
				$condiciones['Venta.medio_pago_id'] = $FiltroMedioPago;
			} 

		}

		//----------------------------------------------------------------------------------------------------
		//estado de venta
		$FiltroVentaEstadoCategoria = "";
		
		if (isset($this->request->data['Venta']['venta_estado_categoria_id'])) {

			$FiltroVentaEstadoCategoria = $this->request->data['Venta']['venta_estado_categoria_id'];

			if ($FiltroVentaEstadoCategoria != "") {

				$joins[] = array(
					'table' => 'rp_venta_estados',
					'alias' => 'ventas_estados',
					'type' => 'INNER',
					'conditions' => array(
						'ventas_estados.id = Venta.venta_estado_id',
						"ventas_estados.venta_estado_categoria_id = " .$FiltroVentaEstadoCategoria
					)
				);

			} 

		}

		//----------------------------------------------------------------------------------------------------
		//venta atendida
		$FiltroAtendida = "";
		
		if (isset($this->request->data['Venta']['atendida'])) {

			$FiltroAtendida = $this->request->data['Venta']['atendida'];

			if ($FiltroAtendida != "") {
				$condiciones['Venta.atendida'] = $FiltroAtendida;
			} 

		}

		//----------------------------------------------------------------------------------------------------
		//fecha desde
		$FiltroFechaDesde = "";

		if (isset($this->request->data['Venta']['FechaDesde'])) {

			$FiltroFechaDesde = trim($this->request->data['Venta']['FechaDesde']);

			if ($FiltroFechaDesde != "") {

				$ArrayFecha = explode("-", $FiltroFechaDesde);

				$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

				$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 00:00:00"));

				$condiciones["Venta.fecha_venta >="] = $Fecha;

			} 

		}

		//----------------------------------------------------------------------------------------------------
		//fecha hasta
		$FiltroFechaHasta = "";

		if (isset($this->request->data['Venta']['FechaHasta'])) {

			$FiltroFechaHasta = trim($this->request->data['Venta']['FechaHasta']);

			if ($FiltroFechaHasta != "") {

				$ArrayFecha = explode("-", $FiltroFechaHasta);

				$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

				$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 23:59:59"));

				$condiciones["Venta.fecha_venta <="] = $Fecha;

			} 

		}

		//----------------------------------------------------------------------------------------------------
		$datos = $this->Venta->find('all',
			array(
				'contain' => array(
					'VentaEstado' => array(
						'VentaEstadoCategoria' => array(
							'fields' => array(
								'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
							)
						),
						'fields' => array(
							'VentaEstado.id', 'VentaEstado.venta_estado_categoria_id'
						)
					),
					'Tienda' => array(
						'fields' => array(
							'Tienda.id', 'Tienda.nombre'
						)
					),
					'Marketplace' => array(
						'fields' => array(
							'Marketplace.id', 'Marketplace.nombre'
						)
					),
					'MedioPago' => array(
						'fields' => array(
							'MedioPago.id', 'MedioPago.nombre'
						)
					),
					'VentaCliente' => array(
						'fields' => array(
							'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.email', 'VentaCliente.telefono',
						)
					),
					'Dte' => array(
						'fields' => array(
							'Dte.estado', 'Dte.folio', 'Dte.tipo_documento'
						)
					)
				),
				'conditions' => $condiciones,
				'joins' => $joins,
				'fields' => array(
					'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo',
					'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.venta_cliente_id'
				),
				'order' => 'Venta.fecha_venta DESC'
			)
		);
	
		$this->set(compact('datos'));

	}


	/**
	 * Ver los detalles de una venta
	 * @param  [type] $id Identificador de la venta
	 */
	public function admin_view ($id = null) 
	{

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}


		if ($this->request->is('post') || $this->request->is('put')) {

			if ($this->Session->check('Auth.Administrador.id')) {
				$this->request->data['Venta']['venta_estado_responsable'] = $this->Auth->user('nombre');	
			}

			try {
				$cambiar_estado = $this->cambiarEstado($id, $this->request->data['Venta']['id_externo'], $this->request->data['Venta']['venta_estado_id'], $this->request->data['Venta']['tienda_id'], $this->request->data['Venta']['marketplace_id']);
			} catch (Exception $e) {
				$this->Session->setFlash($e->getMessage(), null, array(), 'danger');
				$this->redirect(array('action' => 'view', $id));
			}

			if ($cambiar_estado) {
				$this->Session->setFlash('Cambio estado realizado con éxito y notificado al cliente.', null, array(), 'success');
				$this->redirect(array('action' => 'view', $id));
			}

		}

		$venta = $this->request->data = $this->preparar_venta($id);

		# Estados disponibles para esta venta
		$ventaEstados = ClassRegistry::init('VentaEstado')->find('list', array(
			'conditions' => array(
				'activo' => 1,
				'origen' => (empty($this->request->data['Venta']['marketplace_id'])) ? 0 : $this->request->data['Venta']['marketplace_id'] 
			)
		));

		$transportes = ClassRegistry::init('Transporte')->find('list', array('conditions' => array('activo' => 1)));
		
		BreadcrumbComponent::add('Listado de ventas', '/ventas');
		BreadcrumbComponent::add('Detalles de Venta');
		
		$this->set(compact('venta', 'ventaEstados', 'transportes'));

	}


	/**
	 * [admin_registrar_seguimiento description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_registrar_seguimiento($id = null)
	{
		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		if (!$this->request->is('put')) {
			$this->Session->setFlash('Acción no permittida', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		$dataToSave = array(
			'Venta' => array(
				'id' => $id
			)
		);

		foreach ($this->request->data['Transporte'] as $it => $t) {

			if (empty($t['transporte_id']) || empty($t['cod_seguimiento'])) {
				continue;
			}

			$dataToSave['Transporte'][$it]['transporte_id']   = $t['transporte_id'];
			$dataToSave['Transporte'][$it]['cod_seguimiento'] = $t['cod_seguimiento'];
			$dataToSave['Transporte'][$it]['created']         = date('Y-m-d H:i:s');
		}

		# Guardamos los códigos de seguimiento
		if ($this->Venta->saveAll($dataToSave)) {
			$this->Session->setFlash('N° seguimiento registrado con éxito.', null, array(), 'success');
			$this->redirect($this->referer('/', true));
		}else{
			$this->Session->setFlash('Error al registrar el n°de seguimiento. Intente nuevamente.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}
	}


	/**
	 * [cambiarEstado description]
	 * @param  [type] $id_venta         [description]
	 * @param  [type] $id_externo       [description]
	 * @param  [type] $estado_nuevo_id  [description]
	 * @param  [type] $tienda_id        [description]
	 * @param  [type] $marketplace_id   [description]
	 * @param  string $razonCancelado   [description]
	 * @param  string $detalleCancelado [description]
	 * @return [type]                   [description]
	 */
	public function cambiarEstado($id_venta, $id_externo, $estado_nuevo_id, $tienda_id, $marketplace_id = null, $razonCancelado = '', $detalleCancelado = '')
	{
		ClassRegistry::init('VentaEstado')->id = $estado_nuevo_id;
		ClassRegistry::init('Tienda')->id      = $tienda_id;

		# si es marketplace definimos el objeto
		if (!empty($marketplace_id)) {
			ClassRegistry::init('Marketplace')->id = $marketplace_id;				
		}

		$venta                = $this->preparar_venta($id_venta);
		
		$notificar            = ClassRegistry::init('VentaEstado')->field('notificacion_cliente');
		$notificado           = false;

		$estado_actual_nombre = ClassRegistry::init('VentaEstado')->obtener_estado_por_id($venta['Venta']['venta_estado_id'])['VentaEstado']['nombre'];
		$estado_nuevo_nombre  = ClassRegistry::init('VentaEstado')->field('nombre');
		
		$esPrestashop         = (empty($marketplace_id)) ? true : false;
		
		$plantillaEmail       = ClassRegistry::init('VentaEstadoCategoria')->field('plantilla', array('id' => ClassRegistry::init('VentaEstado')->field('venta_estado_categoria_id')));		

		$esMercadolibre = false;
		$esLinio        = false;

		$apiurlprestashop = '';
		$apikeyprestashop = '';
		$apiurllinio      = '';
		$apiuserlinio     = '';
		$apikeylinio      = '';

		# Es marketplace
		if (!$esPrestashop) {
			switch ( ClassRegistry::init('Marketplace')->field('marketplace_tipo_id') ) {
				case 1: // Linio
					$esLinio      = true;
					$apiurllinio  = ClassRegistry::init('Marketplace')->field('api_host');
					$apiuserlinio = ClassRegistry::init('Marketplace')->field('api_user');
					$apikeylinio  = ClassRegistry::init('Marketplace')->field('api_key');
					break;
				
				case 2: // Meli
					$esMercadolibre = true;
					break;
			}
		}else{
			$apiurlprestashop = ClassRegistry::init('Tienda')->field('apiurl_prestashop');
			$apikeyprestashop = ClassRegistry::init('Tienda')->field('apikey_prestashop');
		}
		
		# Prestashop
		if ( $estado_actual_nombre != $estado_nuevo_nombre && $esPrestashop && !empty($apiurlprestashop) && !empty($apikeyprestashop)) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Prestashop = $this->Components->load('Prestashop');
			}
			# Cliente Prestashop
			$this->Prestashop->crearCliente( $apiurlprestashop, $apikeyprestashop );

			# OBtenemos el ID prestashop del estado
			$estadoPrestashop = $this->Prestashop->prestashop_obtener_estado_por_nombre($estado_nuevo_nombre);

			if (empty($estadoPrestashop)) {
				throw new Exception("Error al cambiar el estado. No fue posible obtener el estado de Prestashop", 505);
			}

			$resCambio = $this->Prestashop->prestashop_cambiar_estado_venta($id_externo, $estadoPrestashop['id']);
			
			if ($resCambio) {
				if (!empty($plantillaEmail) && $notificar) {
					$notificado = $this->notificar_cambio_estado($id_venta, $plantillaEmail, $estado_nuevo_nombre);
				}

				# si es un estado pagado se reserva el stock disponible
				if ( $estado_actual_nombre != $estado_nuevo_nombre && ClassRegistry::init('VentaEstado')->es_estado_pagado($estado_nuevo_id)) {
					$this->Venta->pagar_venta($id_venta);
				}

				# si es un estado cancelado se devuelve el stock a la bodega
				if ( $estado_actual_nombre != $estado_nuevo_nombre && ClassRegistry::init('VentaEstado')->es_estado_cancelado($estado_nuevo_id)) {
					$this->Venta->cancelar_venta($id_venta);	
				}
				
			}else{
				throw new Exception('Error al cambiar el estado. No fue posible cambiar el estado en Prestashop.', 506);
			}
			
		# Linio
		}elseif ( $estado_actual_nombre != $estado_nuevo_nombre && $esLinio && !empty($apiurllinio) && !empty($apiuserlinio) && !empty($apikeylinio)) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Linio = $this->Components->load('Linio');
			}
			# cliente Linio
			$this->Linio->crearCliente( $apiurllinio, $apiuserlinio, $apikeylinio );

			$itemsVenta = $this->Linio->linio_obtener_venta_detalles($id_externo);

			if (!isset($itemsVenta[0])) {
				$itemsVenta = array(
					0 => $itemsVenta
				);
			}


			if (!array_key_exists($estado_nuevo_nombre, $this->Linio->estados)) {
				throw new Exception('¡Error! El estado seleccionado no está disponible en Linio', 507);
			}
			
			switch ($estado_nuevo_nombre) {
				case 'canceled':

					foreach ($itemsVenta as $ii => $item) {
						# Cancelamos pedido en Linio
						if(!$this->Linio->linio_cancelar_pedido($item['OrderItemId'], $razonCancelado, $detalleCancelado)){
							throw new Exception('Imposible cambiar el estado. Intente cancelarla directamente en Seller Center.', 508);
						}

					}

					break;
				case 'ready_to_ship':
					
					# Pedimos retiro del pedido en Linio
					foreach ($itemsVenta as $ii => $item) {

						# Listo para envio pedido en Linio Por defecto se usa Blue Express
						if(!$this->Linio->linio_listo_para_envio(array($item['OrderItemId']))){
							throw new Exception('Imposible cambiar el estado. Intente cancelarla directamente en Seller Center.', 508);
						}

					}

					break;
			}

			# si es un estado pagado se reserva el stock disponible
			if ( $estado_actual_nombre != $estado_nuevo_nombre && ClassRegistry::init('VentaEstado')->es_estado_pagado($estado_nuevo_id)) {
				$this->Venta->pagar_venta($id_venta);
			}

			# si es un estado cancelado se devuelve el stock a la bodega
			if ( $estado_actual_nombre != $estado_nuevo_nombre && ClassRegistry::init('VentaEstado')->es_estado_cancelado($estado_nuevo_id)) {
				$this->Venta->cancelar_venta($id_venta);	
			}
			
		# Meli
		}elseif ( $estado_actual_nombre != $estado_nuevo_nombre && $esMercadolibre ) {
			#throw new Exception('¡Error! No está habilitada la opción de cambios de estado en Meli.', 601);
			
		}else{
			throw new Exception('Error al cambiar el estado. Intente nuevamente.', 303);
		}

		if ($this->Venta->save($this->request->data)) {
			return true;
		}else{
			
			if ($notificar && !$notificado) {
				throw new Exception('No fue posible notificar al cliente el cambio de estado.', 707);
			}else{
				throw new Exception('Error al cambiar el estado. Intente nuevamente.', 303);
			}
		}
	}



	/**
	 * [admin_enviar_email_estado description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_enviar_email_estado($id = null)
	{	
		$notificar = $this->notificar_cambio_estado($id);

		if ($notificar) {
			$this->Session->setFlash('Email enviado con éxito', null, array(), 'success');
		}else{
			$this->Session->setFlash('Error al enviar el email.', null, array(), 'danger');
		}

		$refer_url = $this->referer('/', true);

		$this->redirect($refer_url);
	}


	/**
	 * Permite decontar desde bodega la cantidad de productos solicitadas por una venta.
	 * @return [type] [description]
	 */
	public function admin_procesar_ventas($id = null)
	{	

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}


		if ($this->request->is('post') || $this->request->is('put')) {


			$aceptados = array();
			$erorres   = array();

			foreach ($this->request->data['VentaDetalle'] as $idd => $detalle) {

				# vemos la cantidad de existencia que hay en bodega principal.
				$enBodega = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($detalle['venta_detalle_producto_id'], null, true);
				$enBodegas = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodegas($detalle['venta_detalle_producto_id'], true);
					
				# Guardar resultados del detalle
				if ($enBodega < $detalle['cantidad_entregar']) {

					$errores[] = 'Item ' . ClassRegistry::init('VentaDetalleProducto')->field('nombre', $detalle['venta_detalle_producto_id']) . ' no puede ser retirado: Stock bodega principal ('.$enBodega.') - Stock global ('.$enBodegas.') - Vendidos ('.$detalle['cantidad_entregar'].')';
					continue;

				}elseif ($detalle['cantidad_entregar'] > 0) {

					if (ClassRegistry::init('Bodega')->crearSalidaBodega($detalle['venta_detalle_producto_id'], null, $detalle['cantidad_entregar'], 'OC')) {

						$aceptados[] = 'Item ' . ClassRegistry::init('VentaDetalleProducto')->field('nombre', $detalle['venta_detalle_producto_id']) . ': Se descontaron ' . $detalle['cantidad_entregar'] . ' items de bodega principal';

						# se obtiene la cantidad reservada de éste producto para ésta venta.
						$cantidad_reservada = ClassRegistry::init('VentaDetalleProducto')->obtener_cantidad_reservada($detalle['venta_detalle_producto_id'], $id);
						
						# Actualizamos los campos
						$this->request->data['VentaDetalle'][$idd]['cantidad_pendiente_entrega'] = $detalle['cantidad'] - $detalle['cantidad_entregar'];
						$this->request->data['VentaDetalle'][$idd]['cantidad_reservada']         = $cantidad_reservada - $detalle['cantidad_entregar'];
						$this->request->data['VentaDetalle'][$idd]['cantidad_entregada'] 		 = $detalle['cantidad_entregar'];

						# si no quedan pendientes se marca como completado
						if (!$this->request->data['VentaDetalle'][$idd]['cantidad_pendiente_entrega']) {
							$this->request->data['VentaDetalle'][$idd]['completo']         = 1;
							$this->request->data['VentaDetalle'][$idd]['fecha_completado'] = date('Y-m-d H:i:s');
						}

					}else{
						$errores[] = 'Item ' . ClassRegistry::init('VentaDetalleProducto')->field('nombre', $detalle['venta_detalle_producto_id']) . ' no puede ser retirado: Stock bodega principal ('.$enBodega.') - Stock global ('.$enBodegas.') - Vendidos ('.$detalle['cantidad_entregar'].')';
					}
				}else{
					$this->request->data['VentaDetalle'][$idd]['cantidad_pendiente_entrega'] = $detalle['cantidad'];
				}
			}
			
			# Sub estados de la venta
			if (array_sum(Hash::extract($this->request->data['VentaDetalle'], '{n}.cantidad_pendiente_entrega')) > 0 ) {
				$this->request->data['Venta']['subestado_oc'] = 'parcialmente_entregado';
				$this->Session->setFlash('La venta se ha marcado como parcialmente entregado. Se recordará vía email la reposición del/los productos faltantes.', null, array(), 'warning');
			}else{
				$this->request->data['Venta']['subestado_oc'] = 'entregado';
			}


			if (!empty($aceptados)) {
				$this->Session->setFlash($this->crearAlertaUl($aceptados, 'Correcto'), null, array(), 'success');
			}

			if (!empty($errores)) {
				$this->Session->setFlash($this->crearAlertaUl($errores, 'Errores'), null, array(), 'danger');
			}

			
			if ($this->Venta->saveAll($this->request->data, array('callbacks' => false))) {
				$this->redirect(array('action' => 'index'));
			}
			

		}else {

			$this->request->data = $this->Venta->find('first', array(
				'conditions' => array(
					'Venta.id' => $id,
				),
				'contain' => array(
					'VentaDetalle' => array(
						'VentaDetalleProducto' => array(
							'Bodega'
						)
					),
					'VentaEstado' => array(
						'fields' => array(
							'VentaEstado.id', 'VentaEstado.permitir_retiro_oc', 'VentaEstado.nombre'
						)
					)
				),
				'fields' => array(
					'Venta.id'
				)
			));

		}


		BreadcrumbComponent::add('Ventas ', '/ventas');
		BreadcrumbComponent::add('Retirar productos ');

		

	} 


	/**
	 * Genera un paquete con los items selecionados
	 * @param  [type] $id  Venta
	 * @return [type]     [description]
	 */
	public function admin_linio_generar_paquete($id)
	{	
		if ($this->request->is('POST') || $this->request->is('PUT')) {

			$venta = $this->Venta->find(
				'first',
				array(
					'conditions' => array(
						'Venta.id' => $id,
						'Venta.paquete_generado' => false
					),
					'contain' => array(
						
						'Marketplace' => array(
							'fields' => array(
								'Marketplace.id', 'Marketplace.nombre', 'Marketplace.fee', 'Marketplace.marketplace_tipo_id',
								'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key',
								'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token'
							)
						)
					),
					'fields' => array(
						'Venta.id', 'Venta.id_externo', 'Venta.paquete_generado'
					)
				)
			);
			
			# cliente Linio
			$this->Linio->crearCliente( $venta['Marketplace']['api_host'], $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'] );

			$orderItemIds     = json_decode($this->request->data['Venta']['OrderItemIds']); // Please change the set of Order Item IDs for Your system.
			$deliveryType     = 'dropship';
			$shipmentProvider = $this->request->data['Venta']['ShippingProvider'];

			$response = $this->Linio->linio_paquete_embalado($orderItemIds, $deliveryType, $shipmentProvider);

			if ($response) {
			    
				$tipodocumentos = array(
					'invoice', 'exportInvoice', 'shippingLabel', 'shippingParcel', 'carrierManifest', 'serialNumber'
				);

				$documentos = array();

				foreach ($tipodocumentos as $tdocumento) {
					$documentos[] = $this->Linio->linio_obtener_documentos($orderItemIds, $tdocumento);
				}

				$this->Venta->id = $id;
				
				$this->Venta->saveField('Venta.documento', json_encode($documentos));
				$this->Session->setFlash('Paquete creado con éxito.', null, array(), 'success');
				$this->redirect(array('action' => 'view', $id));


			} else {

			    $this->Session->setFlash('No fue posible cambiar el estado del pedido.', null, array(), 'danger');
				$this->redirect(array('action' => 'view', $id));
			
			}	
		}
	}



	/**
	 * Obtiene la etiqueta segun el tipo y canal de venta
	 * @param  [type] $id   [description]
	 * @param  string $tipo [description]
	 * @return [type]       [description]
	 */
	public function admin_obtener_etiqueta($id, $tipo = '') 
	{
		$venta = $this->request->data = $this->Venta->obtener_venta_por_id($id);
		
		# Linio
		if ($venta['Marketplace']['marketplace_tipo_id'] == 1) {
			
			# cliente Linio
			$this->Linio->crearCliente( $venta['Marketplace']['api_host'], $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'] );

			$detallesVenta = $this->Linio->linio_obtener_venta($venta['Venta']['id_externo'], true);

			$documento = $this->Linio->linio_obtener_documentos(Hash::extract($detallesVenta['Products'], '{n}.OrderItemId'), $tipo);

			if (!empty($documento)) {
				$this->ver_documento($documento['mimeType'], $documento['pdf']);
			}else{
				exit;
			}
			
		}

		# MEli
		if ($venta['Marketplace']['marketplace_tipo_id'] == 2) {

			$this->MeliMarketplace->crearCliente($venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'], $venta['Marketplace']['access_token'], $venta['Marketplace']['refresh_token']);

			// Detalles de la venta externa
			$venta['VentaExterna'] = $this->MeliMarketplace->mercadolibre_obtener_venta_detalles($venta['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);

			$this->MeliMarketplace->mercadolibre_obtener_etiqueta_envio($venta['VentaExterna']);
			
		}	

		# PRestashop
		if (!$venta['Venta']['marketplace_id']) {

			# Cliente Prestashop
			$this->Prestashop->crearCliente( $venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop'] );	

			// Obtener detall venta externo
			$venta['VentaExterna'] = $this->Prestashop->prestashop_obtener_venta($venta['Venta']['id_externo']);

			if (!empty($venta['VentaExterna'])) {					

				$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

				$venta['VentaMensaje'] = $this->Prestashop->prestashop_obtener_venta_mensajes($venta['Venta']['id_externo']);

				$direccionEnvio       = $this->Prestashop->prestashop_obtener_venta_direccion($venta['VentaExterna']['id_address_delivery']);
				$direccionFacturacion = $this->Prestashop->prestashop_obtener_venta_direccion($venta['VentaExterna']['id_address_invoice']);
				
				// Detalles de envio
				$venta['Envio'][0] = array(
					'id'                      => $direccionEnvio['address']['id'],
					'tipo'                    => 'Dir. despacho',
					'estado'                  => (!$direccionEnvio['address']['deleted']) ? 'activo' : 'eliminada',
					'direccion_envio'         => @sprintf('%s %s, %s', $direccionEnvio['address']['address1'], implode(',', $direccionEnvio['address']['address2']), $direccionEnvio['address']['city']),
					'nombre_receptor'         => @sprintf('%s %s', $direccionEnvio['address']['firstname'], $direccionEnvio['address']['lastname']),
					'fono_receptor'           => '',
					'producto'                => null,
					'cantidad'                => 1, // No especifica
					'costo'                   => $venta['VentaExterna']['total_shipping_tax_incl'],
					'fecha_entrega_estimada'  => 'No especificado',
					'comentario'              => implode(',', $direccionEnvio['address']['other']),
					'mostrar_etiqueta'        => true,
					'paquete' 				  => false
				);

				if (is_array($direccionEnvio['address']['phone_mobile']) && !empty($direccionEnvio['address']['phone_mobile'])) {
					if (is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) {
						$venta['Envio'][0]['fono_receptor'] = sprintf('Cel: %s Tel: %s', implode(',', $direccionEnvio['address']['phone_mobile']), implode(',', $direccionEnvio['address']['phone']) );
					}else{
						$venta['Envio'][0]['fono_receptor'] = sprintf('Cel: %s Tel: %s', implode(',', $direccionEnvio['address']['phone_mobile']), $direccionEnvio['address']['phone'] );
					}  
				}else{
					if (is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) {
						$venta['Envio'][0]['fono_receptor'] = sprintf('Cel: %s Tel: %s', implode(',', $direccionEnvio['address']['phone_mobile']), implode(',', $direccionEnvio['address']['phone']) );
					}else{
						$venta['Envio'][0]['fono_receptor'] = sprintf('Cel: %s Tel: %s', implode(',', $direccionEnvio['address']['phone_mobile']), $direccionEnvio['address']['phone'] );
					}
				}  
			}
			
			$this->obtener_etiqueta_envio_default($venta);

		}
	}


	/**
	 * Busca y genera todos los PDFS disponibles para una venta: Transporte, etiqueta interna de venta, DTes.
	 * @param  int  		$id       id de la venta
	 * @param  boolean 		$ajax     es una peticion ajax o no
	 * @param  boolean 		$crearDte si la venta no tiene DTE generado, lo crea siempre y cuando tenga los datos de facturación cargados.
	 * @return url del documento
	 */
	public function admin_generar_documentos($id, $ajax = false, $crearDte = false)
	{	
		# Toda la información de la venta
		$venta = $this->Venta->obtener_venta_por_id($id);

		# Variable que contendrá los documentos
		$archivos = array();
		
		# Linio
		if ($venta['Marketplace']['marketplace_tipo_id'] == 1) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Linio = $this->Components->load('Linio');
			}
			# cliente Linio
			$this->Linio->crearCliente( $venta['Marketplace']['api_host'], $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'] );

			//$mensajes =  $this->Linio->linio_obtener_venta_mensajes($venta, $ConexionLinio);

			// Obtener detall venta externo
			$venta['VentaExterna'] = $this->Linio->linio_obtener_venta($venta['Venta']['id_externo'], true);

			// Datos d facturacion
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);

			// Se define transportista
			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

			// Detalles de envio
			$venta['Envio'][0] = array(
				'id'                      => null,
				'tipo'                    => null,
				'estado'                  => null,
				'direccion_envio'         => sprintf('%s, %s, %s', $venta['VentaExterna']['AddressShipping']['Address1'], $venta['VentaExterna']['AddressShipping']['Address2'], $venta['VentaExterna']['AddressShipping']['City']),
				'nombre_receptor'         => sprintf('%s %s', $venta['VentaExterna']['AddressShipping']['FirstName'], $venta['VentaExterna']['AddressShipping']['LastName']),
				'fono_receptor'           => $venta['VentaExterna']['AddressShipping']['Phone'],
				'producto'                => null,
				'cantidad'                => 1, // No especifica
				'costo'                   => 0,
				'fecha_entrega_estimada'  => $venta['VentaExterna']['PromisedShippingTime'],
				'comentario'              => '',
				'mostrar_etiqueta'        => false,
				'paquete' 				  => false
			);

			$documentoEnvio   = $this->Linio->linio_obtener_documentos(Hash::extract($venta['VentaExterna']['Products'], '{n}.OrderItemId'), 'shippingParcel');
			$documentoInvoice = $this->Linio->linio_obtener_documentos(Hash::extract($venta['VentaExterna']['Products'], '{n}.OrderItemId'), 'invoice');
			
			$rutaAbsoluta = APP . 'webroot' . DS. 'Venta' . DS . $id . DS;
			$rutaPublica  =  Router::url('/', true) . 'Venta/' . $id . '/';

			# Invoice Linio
			if (!empty($documentoInvoice)) {

				$invoice = $this->generar_pdf(base64_decode($documentoInvoice['pdf']), $id, 'invoice');
		 		
		 		if (!empty($invoice)) {
		 			$archivos[] = $invoice['path'];
		 		}

			}

			# Doc tranportista linio
			if (!empty($documentoEnvio)) {

				$archivoPdfEnvio = 'transporte' . rand() . '.pdf';

				$documentoEnvioPdfs = $this->guardar_pdf_base64($documentoEnvio['pdf'], $rutaAbsoluta, $rutaPublica, $archivoPdfEnvio);
		 		
		 		if (!empty($documentoEnvioPdfs)) {
		 			$archivos[] = $documentoEnvioPdfs['path'];
		 		}

			}
			
		}

		# MEli
		if ($venta['Marketplace']['marketplace_tipo_id'] == 2) {


			$this->MeliMarketplace->crearCliente( $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'], $venta['Marketplace']['access_token'], $venta['Marketplace']['refresh_token'] );
			$this->MeliMarketplace->mercadolibre_conectar('', $venta['Marketplace']);

			$mensajes = $this->MeliMarketplace->mercadolibre_obtener_mensajes($venta['Marketplace']['access_token'], $venta['Venta']['id_externo']);

			foreach ($mensajes as $mensaje) {
				$data = array();
				$data['mensaje'] = $this->removeEmoji($mensaje['text']['plain']);
				$data['fecha'] = CakeTime::format($mensaje['date'], '%d-%m-%Y %H:%M:%S');
				$data['asunto'] = $mensaje['subject'];
				$venta['VentaMensaje'][] = $data;
			}

			// Detalles de la venta externa
			$venta['VentaExterna'] = $this->MeliMarketplace->mercadolibre_obtener_venta_detalles($venta['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);

			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;
			
			// Datos d facturacion
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);


			if (isset($venta['VentaExterna']['shipping']['id'])) {

				// Detalles de envio
				$direccion_envio = '';
				$nombre_receptor = '';
				$fono_receptor   = '';
				$comentario      = '';

				if (isset($venta['VentaExterna']['shipping']['receiver_address']['address_line'])
					&& isset($venta['VentaExterna']['shipping']['receiver_address']['city']['name'])) {
					$direccion_envio = sprintf('%s, %s', $venta['VentaExterna']['shipping']['receiver_address']['address_line'], $venta['VentaExterna']['shipping']['receiver_address']['city']['name']);
				}

				if (isset($venta['VentaExterna']['shipping']['receiver_address']['receiver_name'])) {
					$nombre_receptor = $venta['VentaExterna']['shipping']['receiver_address']['receiver_name'];
				}

				if (isset($venta['VentaExterna']['shipping']['receiver_address']['receiver_phone'])) {
					$fono_receptor = $venta['VentaExterna']['shipping']['receiver_address']['receiver_phone'];
				}

				if (isset($venta['VentaExterna']['shipping']['receiver_address']['comment'])) {
					$comentario = $venta['VentaExterna']['shipping']['receiver_address']['comment'];
				}

				
				$venta['Envio'][0] = array(
					'id'                      => $venta['VentaExterna']['shipping']['id'],
					'tipo'                    => $venta['VentaExterna']['shipping']['shipping_option']['name'],
					'estado'                  => $venta['VentaExterna']['shipping']['status'],
					'direccion_envio'         => $direccion_envio,
					'nombre_receptor'         => $nombre_receptor,
					'fono_receptor'           => $fono_receptor,
					'producto'                => null,
					'cantidad'                => 1,
					'costo'                   => $venta['VentaExterna']['shipping']['shipping_option']['cost'],
					'fecha_entrega_estimada'  => (isset($venta['VentaExterna']['shipping']['shipping_option']['estimated_delivery_time'])) ? CakeTime::format($venta['VentaExterna']['shipping']['shipping_option']['estimated_delivery_time']['date'], '%d-%m-%Y %H:%M:%S') : __('No especificado') ,
					'comentario'              => $comentario,
					'mostrar_etiqueta'        => ($venta['VentaExterna']['shipping']['status'] == 'ready_to_ship') ? true : false,
					'paquete' 				  => false
				);	
				
			}

			$documentoEnvio = $this->MeliMarketplace->mercadolibre_obtener_etiqueta_envio($venta['VentaExterna'], 'Y');
			
			$rutaAbsoluta = APP . 'webroot' . DS. 'Venta' . DS . $id . DS;
			$rutaPublica  =  Router::url('/', true) . 'Venta/' . $id . '/';

			# Tranposrte Meli
			if (!empty($documentoEnvio)) {

				$archivoPdfEnvio = 'transporte' . rand() . '.pdf';

				$documentoEnvioPdfs = $this->guardar_pdf_base64($documentoEnvio, $rutaAbsoluta, $rutaPublica, $archivoPdfEnvio, false);
		 		
		 		if (!empty($documentoEnvioPdfs)) {
		 			$archivos[] = $documentoEnvioPdfs['path'];
		 		}

			}

		}	

		# Prestashop
		if (!$venta['Venta']['marketplace_id']) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Prestashop = $this->Components->load('Prestashop');
			}
			# Cliente Prestashop
			$this->Prestashop->crearCliente( $venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop'] );	

			// Obtener detall venta externo
			$venta['VentaExterna'] = $this->Prestashop->prestashop_obtener_venta($venta['Venta']['id_externo']);		

			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

			$venta['VentaMensaje'] = $this->Prestashop->prestashop_obtener_venta_mensajes($venta['Venta']['id_externo']);

			$direccionEnvio       = $this->Prestashop->prestashop_obtener_venta_direccion($venta['VentaExterna']['id_address_delivery']);				

			// Detalles de envio
			$telefonosEnvio = '';
			
			if (is_array($direccionEnvio['address']['phone_mobile']) && !empty($direccionEnvio['address']['phone_mobile'])) {
				$telefonosEnvio .= implode(' ', $direccionEnvio['address']['phone_mobile']);
			}

			if (!is_array($direccionEnvio['address']['phone_mobile']) && !empty($direccionEnvio['address']['phone_mobile'])) {
				$telefonosEnvio .= ' ' . $direccionEnvio['address']['phone_mobile'];
			}


			if (is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) {
				$telefonosEnvio .= implode(' ', $direccionEnvio['address']['phone']);
			}

			if (!is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) {
				$telefonosEnvio .= ' ' . $direccionEnvio['address']['phone'];
			}
			
			// Detalles de envio
			$venta['Envio'][0] = array(
				'id'                      => $direccionEnvio['address']['id'],
				'tipo'                    => 'Dir. despacho',
				'estado'                  => (!$direccionEnvio['address']['deleted']) ? 'activo' : 'eliminada',
				'direccion_envio'         => @sprintf('%s %s, %s', $direccionEnvio['address']['address1'], implode(',', $direccionEnvio['address']['address2']), $direccionEnvio['address']['city']),
				'nombre_receptor'         => @sprintf('%s %s', $direccionEnvio['address']['firstname'], $direccionEnvio['address']['lastname']),
				'fono_receptor'           => $telefonosEnvio,
				'producto'                => null,
				'cantidad'                => 1, // No especifica
				'costo'                   => $venta['VentaExterna']['total_shipping_tax_incl'],
				'fecha_entrega_estimada'  => 'No especificado',
				'comentario'              => implode(',', $direccionEnvio['address']['other']),
				'mostrar_etiqueta'        => true,
				'paquete' 				  => false
			);

			# Datos de facturación para compras por Prestashop
			ToolmaniaComponent::$api_url = $venta['Tienda']['apiurl_prestashop'];
			
			#Obtener información webpay si es necesario
			#$webpay                      = $this->Toolmania->obtenerWebpayInfo($this->request->data['Orden']['id_cart'], $this->Session->read('Tienda.apikey_prestashop'));
			$documentos                  = $this->Toolmania->obtenerDocumento($venta['Venta']['id_externo'], null, $venta['Tienda']['apikey_prestashop']);
			
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);
			
			if (!empty($documentos['content'])) {

				$tipoDoc = ($documentos['content'][0]['boleta']) ? 39 : 33;

				$facturacion = array(
					'tipo_documento'        => $tipoDoc,
					'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[$tipoDoc],
					'rut_receptor'          => $documentos['content'][0]['rut'],
					'razon_social_receptor' => $documentos['content'][0]['empresa'],
					'giro_receptor'         => $documentos['content'][0]['giro'],
					'direccion_receptor'    => $documentos['content'][0]['calle']
				);
				# Para la consola se carga el componente on the fly!
				if ($this->shell) {
					$this->LibreDte = $this->Components->load('LibreDte');
				}
				// Obtenemos la información del contribuyente desde el SII
				$this->LibreDte->crearCliente($venta['Tienda']['facturacion_apikey']);
		
				$info = $this->LibreDte->obtenerContribuyente($this->rutSinDv($documentos['content'][0]['rut']));
				
				// Agregamos comuna
				if (isset($info['comuna_glosa'])) {
					$facturacion['comuna_receptor'] = $info['comuna_glosa'];
				}

				// Agregamos razon social
				if (empty($documentos['content'][0]['empresa']) && isset($info['razon_social'])) {
					$facturacion['razon_social_receptor'] = $info['razon_social'];
				}

				// Agregamos giro
				if (empty($documentos['content'][0]['giro']) && isset($info['giro'])) {
					$facturacion['giro_receptor'] = $info['giro'];
				}	

				// Agregamos direccon
				if (empty($documentos['content'][0]['direccion_receptor']) && isset($info['direccion'])) {
					$facturacion['direccion_receptor'] = $info['direccion'];
				}	
				
				
				# Guardamos el rut de la persona
				ClassRegistry::init('VentaCliente')->id = $venta['VentaCliente']['id'];
				ClassRegistry::init('VentaCliente')->saveField('rut', $documentos['content'][0]['rut']);

				$this->request->data['VentaCliente']['rut'] = $documentos['content'][0]['rut'];

				$venta['VentaExterna']['facturacion'] = array_replace_recursive($venta['VentaExterna']['facturacion'], $facturacion);
			}
			
			
		}

		/*$url_etiqueta_qr = $this->obtener_codigo_qr_url($venta['Venta']['id']);
				
		$archivos[] = $url_etiqueta_qr['path'];*/

		if ($crearDte) {
			$resultDte = $this->crearDteAutomatico($venta);

			if (!empty($resultDte['success'])) {

				$nwDte = ClassRegistry::init('Dte')->find('all', array(
					'conditions' => array(
						'Dte.venta_id' => $venta['Venta']['id']
					),
					'fields' => array(
						'Dte.id', 'Dte.folio', 'Dte.tipo_documento', 'Dte.rut_receptor', 'Dte.razon_social_receptor', 'Dte.giro_receptor', 'Dte.neto', 'Dte.iva',
						'Dte.total', 'Dte.fecha', 'Dte.estado', 'Dte.venta_id', 'Dte.pdf', 'Dte.invalidado'
					),
					'order' => 'Dte.fecha DESC'
				));

				$venta['Dte'] = Hash::extract($nwDte, '{n}.Dte');
			}

		}

		# Obtenemos DTE
		if (!empty($venta['Dte'])) {
			$dtes = $this->obtener_dtes_pdf_venta($venta['Dte']);
		
			foreach ($dtes as $dte) {
				$archivos[] = $dte['path'];
			}
		}

		$venta              = $this->preparar_venta($id);
		$url_etiqueta_envio = $this->obtener_etiqueta_envio_default_url($venta);
		$archivos[]         = $url_etiqueta_envio['path'];


		# Unimos todos los PDFS obtenidos
		if (!empty($archivos)) {
			
			$this->layoutPath = '';
			$this->layout = 'ajax';

			$pdf = $this->unir_documentos($archivos, $id);

			if ($ajax) {
				echo json_encode($pdf);
				exit;
			}

		}else{

			if ($ajax) {
				echo '';
				exit;
			}

			$this->Session->setFlash('No hay documentos para generar.', null, array(), 'warning');
			$this->redirect(array('action' => 'view', $id));
		}

	}


	/**
	 * Genera la etiqueta de envio: Transporte, etiqueta interna de venta, DTes.
	 * @param  int  		$id       id de la venta
	 * @param  boolean 		$ajax     es una peticion ajax o no
	 * @param  boolean 		$crearDte si la venta no tiene DTE generado, lo crea siempre y cuando tenga los datos de facturación cargados.
	 * @return url del documento
	 */
	public function admin_generar_etiqueta($id, $ajax = false)
	{	
		# Toda la información de la venta
		$venta = $this->preparar_venta($id);

		# Variable que contendrá los documentos
		$archivos = array();
		
		$url_etiqueta_envio = $this->obtener_etiqueta_envio_default_url($venta);
		
		$archivos[] = $url_etiqueta_envio['path'];

		# Unimos todos los PDFS obtenidos
		if (!empty($archivos)) {
			
			$this->layoutPath = '';
			$this->layout = 'ajax';

			$pdf = $this->unir_documentos($archivos, $id);

			if ($ajax) {
				echo json_encode($pdf);
				exit;
			}

		}else{

			if ($ajax) {
				echo '';
				exit;
			}

			$this->Session->setFlash('No hay documentos para generar.', null, array(), 'warning');
			$this->redirect(array('action' => 'view', $id));
		}

	}


	/**
	 * Metodo utilizado para saber si una venta tien o no generado ya un dte valido
	 * @param  [type] $id_venta
	 * @return json object
	 */
	public function admin_consultar_dte($id_venta)
	{
		$res['dte_generado'] = !DtesController::unicoDteValido($id_venta);
		echo json_encode($res);
		exit;
	}


	/**
	 * [guardar_pdf_base64 description]
	 * @param  string $data         [description]
	 * @param  string $rutaAbsoluta [description]
	 * @param  string $rutaPublica  [description]
	 * @param  [type] $nombre       [description]
	 * @return [type]               [description]
	 */
	public function guardar_pdf_base64($data = '', $rutaAbsoluta = '', $rutaPublica = '', $nombre, $base64 = true)
	{
 		# Creamos la ruta absoluta
 		if( !@mkdir($rutaAbsoluta, 0777, true) ) {
 			# Ya existe ruta
 		}

 		$rutaCompletaAbsoluta = $rutaAbsoluta . $nombre;
 		$rutaCompletaPublica  = $rutaPublica . $nombre;

 		if ($base64) {
 			$data = base64_decode($data);
 		}

 		# guardamos el PDF
		if (file_put_contents($rutaCompletaAbsoluta, $data) == E_WARNING) {
			return array();
		}else{
			return array('public' => $rutaCompletaPublica, 'path' => $rutaCompletaAbsoluta);
		}
	}


	/**
	 * [obtener_etiqueta_envio_default description]
	 * @param  array  $venta [description]
	 * @return [type]        [description]
	 */
	public function obtener_etiqueta_envio_default($venta = array())
	{	
		$logo = FULL_BASE_URL . '/webroot/img/Tienda/' . $venta['Tienda']['id'] . '/' . $venta['Tienda']['logo'] ;
		
		$this->layoutPath = 'pdf';
		$this->viewPath   = 'Ventas/';
		$this->autoRender = false;
		$this->output     = '';
		$this->layout 	  = 'default';
		$this->pdfConfig  = array(
			'orientation' => 'landscape',
			'download' => true,
			'filename' => rand() .'.pdf'
		);

		$this->set(compact('venta', 'logo'));
		$this->render('etiqueta_envio_default');
		#$html  = $vista->body();
		#$url   = $this->generar_pdf($html, $venta['Venta']['id'], 'transporte', 'landscape');
		
	}


	/**
	 * Genera la etiqueta de envio default intenra y retorna la url púbica y absoluta del archivo.
	 * @param  array  $venta [description]
	 * @return [type]        [description]
	 */
	public function obtener_etiqueta_envio_default_url($venta = array())
	{	
		# Dejamos solo DTES validos
		if (!empty($venta['Dte'])) {
			$venta['Dte'] = ClassRegistry::init('Dte')->preparar_dte_venta_valido($venta['Dte']);
		}

		# Creamos la etiqueta de despacho interna
		$logo = FULL_BASE_URL . '/webroot/img/Tienda/' . $venta['Tienda']['id'] . '/' . $venta['Tienda']['logo'] ;
		
		$this->layoutPath = 'pdf';
		$this->viewPath   = 'Ventas/pdf';
		$this->output     = '';
		$this->layout     = 'default';

		$url    = Router::url( sprintf('/api/ventas/%d.json', $venta['Venta']['id']), true);
		$tamano = '500x500';

		$this->set(compact('venta', 'logo', 'url', 'tamano'));

		$vista = $this->render('etiqueta_envio_default');
		$html  = $vista->body();
		#prx($html);
		$url   = $this->generar_pdf($html, $venta['Venta']['id'], 'transporte', 'landscape');

		return $url;
	}


	/**
	 * Genera Código QR identificador de la venta
	 * @param  [type] $id_venta [description]
	 * @return [type]           [description]
	 */
	public function obtener_codigo_qr_url($id_venta)
	{	
		$this->layoutPath = 'pdf';
		$this->viewPath   = 'Ventas/pdf';
		$this->autoRender = false;
		$this->output     = '';
		$this->layout 	  = 'default';

		$url = Router::url( sprintf('/api/ventas/%d.json', $id_venta), true);
		$tamano = '500x500';

		$this->set(compact('url', 'tamano'));

		$vista = $this->render('qr');
		$html  = $vista->body();

		$url   = $this->generar_pdf($html, $id_venta, 'etiquetaqr');

		return $url;
	}


	/**
	 * [obtener_dtes_pdf_venta description]
	 * @param  array  $dtes [description]
	 * @return [type]       [description]
	 */
	public function obtener_dtes_pdf_venta($dtes = array())
	{	
		$rutas = array();

		foreach ($dtes as $i => $dte) {

			if ($dte['invalidado']) {
				continue;
			}

			if ($dte['estado'] != 'dte_real_emitido') {
				continue;
			}

			# solo boleta o factura no invalidada
			if ($dte['tipo_documento'] == 39 || $dte['tipo_documento'] == 33) {
				# Ruta absoluta PDF DTE
				$rutas[$i]['path'] = APP . 'webroot' . DS. 'Dte' . DS . $dte['venta_id'] . DS . $dte['id'] . DS . $dte['pdf'];
				$rutas[$i]['public'] = Router::url('/', true) . 'Dte/' . $dte['venta_id'] . '/' . $dte['id'] . $dte['pdf'] . '.pdf';	

				# Si es boleta la agregamos 2 veces
				if ($dte['tipo_documento'] == 39) {
					# Ruta absoluta PDF DTE
					$rutas[$i.$i]['path'] = APP . 'webroot' . DS. 'Dte' . DS . $dte['venta_id'] . DS . $dte['id'] . DS . $dte['pdf'];
					$rutas[$i.$i]['public'] = Router::url('/', true) . 'Dte/' . $dte['venta_id'] . '/' . $dte['id'] . $dte['pdf'] . '.pdf';
				}
			}
		}

		return $rutas;
	}


	/**
	 * [unir_documentos description]
	 * @param  array  $archivos [description]
	 * @param  string $venta_id [description]
	 * @return [type]           [description]
	 */
	public function unir_documentos($archivos = array(), $venta_id = '')
	{
		$pdfs       = array();
		$limite     = 500;
		$lote = 0;
		$ii = 1;

		foreach ($archivos as $i => $archivo) {

			if (file_exists($archivo)) {
				$pdfs[$lote][$ii] = $archivo;

				if ($ii%$limite == 0) {
					$lote++;
				}	
			}

			$ii++;
		}

		if (!is_dir(APP . 'webroot' . DS. 'Venta' . DS . $venta_id)) {
			@mkdir(APP . 'webroot' . DS. 'Venta' . DS . $venta_id, 0775);
		}

		# Se procesan por Lotes de 500 documentos para no volcar la memoria
		foreach ($pdfs as $ip => $lote) {
			$pdf = new PDFMerger;
			foreach ($lote as $id => $document) {
				$pdf->addPDF($document, 'all');	
			}
			try {
				
				$pdfname = 'documentos-' . date('YmdHis') .'.pdf';

				$res = $pdf->merge('file', APP . 'webroot' . DS. 'Venta' . DS . $venta_id . DS . $pdfname);
				if ($res) {
					$resultados['result'][]['document'] = Router::url('/', true) . 'Venta/' . $venta_id . '/' . $pdfname;
				}

			} catch (Exception $e) {
				$resultados['errors']['messages'][] = $e->getMessage();
			}
		}

		return $resultados;
	}


	/**
	 * Muestra en pantalla el contenido segun su tipo
	 * @param  string $mimetype [description]
	 * @param  string $cuerpo   [description]
	 * @return [type]           [description]
	 */
	public function ver_documento($mimetype = '', $cuerpo = '')
	{
		header('Content-type:' . $mimetype);

		switch ($mimetype) {
			case 'application/pdf':
				header('Content-Disposition:inline;filename="'.rand().'.pdf"');
				break;
			case 'application/zip':
				header('Content-Disposition:attachment;filename="'.rand().'.zip"');
				break;
		}

		echo base64_decode($cuerpo);
		exit;
		
	}

	/**
	 * Crea un archivo PDF con el contenido HTML pasado por argumento
	 * en la ruta deifinida para ventas
	 * @param  string $html        [description]
	 * @param  string $venta_id    [description]
	 * @param  string $nombre      [description]
	 * @param  string $orientacion [description]
	 * @return [type]              [description]
	 */
	public function generar_pdf($html = '', $venta_id = '', $nombre = '', $orientacion = 'potrait') {

		$nombre = $nombre . rand();
		$rutaAbsoluta = APP . 'webroot' . DS . 'Pdf' . DS . 'Venta' . DS . $venta_id . DS . $nombre . '.pdf';

		try {
			$this->CakePdf = new CakePdf();
			$this->CakePdf->orientation($orientacion);
			@$this->CakePdf->write($rutaAbsoluta, true, $html);	
		} catch (Exception $e) { 
			return array();
		}

		# Ruta para guardar en la Base de datos
		$archivo = Router::url('/', true) . 'Pdf/Venta/' . $venta_id . '/' . $nombre . '.pdf';

		return array('public' => $archivo, 'path' => $rutaAbsoluta);

	}


	/**
	 * Notifica al cliente sobre el cambi de estado de su pedido o venta
	 * @param  [type] $id_venta            [description]
	 * @param  [type] $plantillaEmail      [description]
	 * @param  string $nombre_estado_nuevo [description]
	 * @return [type]                      [description]
	 */
	public function notificar_cambio_estado($id_venta = null, $plantillaEmail = null, $nombre_estado_nuevo = '')
	{
		$venta = $this->Venta->obtener_venta_por_id($id_venta);

		$plantillaDefault = @$venta['VentaEstado']['VentaEstadoCategoria']['plantilla'];
		$estadoDefault    = @$venta['VentaEstado']['nombre'];

		if (empty($plantillaEmail) && !empty($plantillaDefault)) {
			$plantillaEmail = $plantillaDefault;
		}

		if (empty($plantillaEmail) && empty($plantillaDefault)) {
			return false;
		}

		if (empty($nombre_estado_nuevo) && !empty($estadoDefault)) {
			$nombre_estado_nuevo = $estadoDefault;
		}

		if (empty($nombre_estado_nuevo) && empty($estadoDefault)) {
			return false;
		}

		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'VentaEstados' . DS . 'emails';
		$this->View->layout		= 'backend' . DS . 'emails';
		$this->Correo	    = ClassRegistry::init('Correo');
		
		/**
		 * QR
		 */
		$urlQr = Router::url( sprintf('/api/ventas/%d.json', $id_venta), true);
		$tamanoQr = '500x500';

		/**
		 * Correo a ventas
		 */
		$this->View->set(compact('venta', 'urlQr', 'tamanoQr'));
		$html = $this->View->render($plantillaEmail);
		
		$data = array(
			'estado'					=> 'Notificación venta #' . $id_venta . '-' . $plantillaEmail,
			'html'						=> $html,
			'asunto'					=> '['.$venta['Tienda']['nombre'].'] Venta #' . $id_venta . ' - ' . $nombre_estado_nuevo,
			'destinatario_email'		=> trim($venta['VentaCliente']['email']),
			'destinatario_nombre'		=> $venta['VentaCliente']['nombre'] . ' ' . $venta['VentaCliente']['apellido'],
			'remitente_email'			=> 'ventas@toolmania.cl',
			'remitente_nombre'			=> 'Ventas ' . $venta['Tienda']['nombre'],
			'cc_email'					=> '',
			'bcc_email'					=> $venta['Tienda']['emails_bcc'],
			'traza'						=> null,
			'proceso_origen'			=> null,
			'procesado'					=> 0,
			'enviado'					=> 0,
			'reintentos'				=> 0,
			'atachado'					=> null
		);

		/**
		 * Guarda el email a enviar
		 */
		$this->Correo->create();
		if ( $this->Correo->save($data) ) {
			return true;
		}
		
		return false;

	}


	/**
	 * [crearDteAutomatico description]
	 * @param  [type] $venta [description]
	 * @return [type]        [description]
	 */
	public function crearDteAutomatico($venta)
	{	

		$respuesta =  array(
			'success', 'errors'
		);

		$tipo_documento = $venta['VentaExterna']['facturacion']['tipo_documento'];

		# Solo acepta boleta y factura
		if (!in_array($tipo_documento, array(33,39))) {
			$respuesta['errors'] = sprintf('Venta #%d: Tipo de documento no permitido', $venta['Venta']['id']);
			return $respuesta;
		}

		# Ya tiene DTE valido
		if ( ($tipo_documento == 33 || $tipo_documento == 39) && !DtesController::unicoDteValido($venta['Venta']['id'])) {
			$respuesta['errors'] = sprintf('La venta #%d ya tien un DTE de venta válido.', $venta['Venta']['id']);
			return $respuesta;
		}

		# si no tiene items no se puede procesar
		if (empty($venta['VentaDetalle'])) {
			$respuesta['errors'] = sprintf('La venta #%d no tiene cargado productos para facturar. Intente emitir el DTE manualmente.', $venta['Venta']['id']);
			return $respuesta;
		}

		# si es factura y no tiene todos los datos de facturación, retorna.
		if ($tipo_documento == 33 && 
			(empty($venta['VentaExterna']['facturacion']['rut_receptor'])
			|| empty($venta['VentaExterna']['facturacion']['razon_social_receptor'])
			|| empty($venta['VentaExterna']['facturacion']['giro_receptor'])
			|| empty($venta['VentaExterna']['facturacion']['direccion_receptor'])
			|| empty($venta['VentaExterna']['facturacion']['comuna_receptor']) )

		) {
			$respuesta['errors'] = sprintf('La venta #%d no tiene los datos de facturación cargados. Intente emitir el DTE manualmente.', $venta['Venta']['id']);
			return $respuesta;
		}

		# DTE
		$dte['Dte']['tipo_documento']        = $tipo_documento;
		$dte['Dte']['fecha']        		 = date('Y-m-d');
		$dte['Dte']['razon_social_receptor'] = $venta['VentaExterna']['facturacion']['razon_social_receptor'];
		$dte['Dte']['giro_receptor']         = $venta['VentaExterna']['facturacion']['giro_receptor'];
		$dte['Dte']['direccion_receptor']    = $venta['VentaExterna']['facturacion']['direccion_receptor'];
		$dte['Dte']['comuna_receptor']       = $venta['VentaExterna']['facturacion']['comuna_receptor'];
		$dte['Dte']['estado']                = 'no_generado';
		$dte['Dte']['venta_id']              = $venta['Venta']['id'];
		$dte['Dte']['tienda_id']             = $venta['Tienda']['id'];
		$dte['Dte']['externo']               = $venta['Venta']['id_externo'];
		$dte['Dte']['administrador_id']      = $this->Auth->user('id');

		$dte['Dte']['glosa'] = __('Dte generado automáticamente para la venta # ') . $venta['Venta']['id'];

		# Rut sin puntos
		if (!empty($venta['VentaExterna']['facturacion']['rut_receptor'])) {
			$dte['Dte']['rut_receptor'] = str_replace('.', '', $venta['VentaExterna']['facturacion']['rut_receptor']);
		}else{
			$dte['Dte']['rut_receptor'] = '66666666-6';
		}
	
		# Si existe costo de transporte se agrega como ITEM
		if (round($venta['Venta']['costo_envio']) > 0) {
			$cantidadItem = (count($venta['VentaDetalle']) + 1);

			$dte['DteDetalle'][$cantidadItem]['VlrCodigo'] = "COD-Trns";
			$dte['DteDetalle'][$cantidadItem]['NmbItem']   = "Transporte";

			# Para boleta se envia el valor bruto y así evitar que el monto aumente o disminuya por el calculo de iva
			if ($tipo_documento == 39) {
				$dte['DteDetalle'][$cantidadItem]['PrcItem'] = round($venta['Venta']['costo_envio']);
			}else{
				$dte['DteDetalle'][$cantidadItem]['PrcItem'] = $this->precio_neto($venta['Venta']['costo_envio']);
			}
			$dte['DteDetalle'][$cantidadItem]['QtyItem'] = 1;
		}


		foreach ($venta['VentaDetalle'] as $k => $item) {

			if ($item['precio'] <= 0) {
				continue;
			}

			$dte['DteDetalle'][$k]['VlrCodigo'] = sprintf('COD-%d', $item['venta_detalle_producto_id']);
			$dte['DteDetalle'][$k]['NmbItem'] = $item['VentaDetalleProducto']['nombre'];
			$dte['DteDetalle'][$k]['QtyItem'] = $item['cantidad'];

			if ($tipo_documento == 39) { # Boleta valores brutos o con iva
				$dte['DteDetalle'][$k]['PrcItem'] = $this->precio_bruto($item['precio']);	
			}else{
				$dte['DteDetalle'][$k]['PrcItem'] = $item['precio'];
			}

		}

		// Descuento Bruto en boletas
		if ($venta['Venta']['descuento'] > 0) {
			if ($tipo_documento == 39) { # Boleta valores brutos o con iva
				$dte['DscRcgGlobal']['ValorDR'] = $venta['Venta']['descuento'];
			}else{
				$dte['DscRcgGlobal']['ValorDR'] = $this->precio_neto($venta['Venta']['descuento']);
			}
		}

		$DteModel = ClassRegistry::init('Dte');

		# Guardar información del DTE en base de datos local
		if($DteModel->saveAll($dte)) {

			$this->LibreDte->crearCliente($venta['Tienda']['facturacion_apikey']);

			$nwDte  = $this->LibreDte->prepararDte($dte);
			$id_dte = $DteModel->id;
			
			if (!empty($id_dte)) {
				# Obtener DTE interno por id
				$dteInterno = ClassRegistry::init('Dte')->find('first', array('conditions' => array('id' => $id_dte)));
			}else{
				# Obtener último DTE guardado
				$dteInterno = ClassRegistry::init('Dte')->find('first', array('order' => array('id' => 'DESC')));
			}

			try {
				
				// crear DTE temporal
				$dte_temporal = $this->LibreDte->crearDteTemporal($nwDte, $dteInterno);

				if (empty($dte_temporal)) {
					$respuesta['errors'] = sprintf('No fue posible generar el DTE temporal para la venta #%d. Verifique los campos e intente nuevamente.', $venta['Venta']['id']);
					return $respuesta;
				}

				// crear DTE real
				$generar = $this->LibreDte->crearDteReal($dte_temporal, $dteInterno);

			} catch (Exception $e) {

				if($e->getCode() != 200) {
					$respuesta['errors'] = sprintf('Venta #%d error: %s', $venta['Venta']['id'], $e->getMessage());
					return $respuesta;
				}

			}

			try {
				$this->LibreDte->generarPDFDteEmitido($dteInterno['Dte']['venta_id'], $dteInterno['Dte']['id'], $dteInterno['Dte']['tipo_documento'], $dteInterno['Dte']['folio'], $dteInterno['Dte']['emisor'] );
			} catch (Exception $e) {
				if($e->getCode() != 200) {
					$respuesta['errors'] = sprintf('Venta #%d error: %s', $venta['Venta']['id'], $e->getMessage());
					return $respuesta;
				}
			}	


			# Enviamos doc al cliente
			if (!empty($venta['VentaCliente'])) {

				$emails = array(
					$venta['VentaCliente']['email']
				);

				$asunto = sprintf('Su Factura Electrónica ha sido emitida.', $this->LibreDte->tipoDocumento[$dteInterno['Dte']['tipo_documento']]);

				$mensaje = sprintf('Estimado/a %s %s. Hemos emitido su %s exitosamente para su compra referencia %s. El documento los encontrará adjunto a este email. Por favor NO RESPONDA ESTE EMAIL ya que es generado automáticamente.', $venta['VentaCliente']['nombre'], $venta['VentaCliente']['apellido'], $venta['Venta']['referencia'], $this->LibreDte->tipoDocumento[$dteInterno['Dte']['tipo_documento']]);

				$enviar = $this->LibreDte->enviarDteEmail(
					$emails, 
					$dteInterno['Dte']['tipo_documento'], 
					$dteInterno['Dte']['folio'], 
					$dteInterno['Dte']['emisor'],
					$asunto,
					$mensaje);
				
				if ($enviar) {
					$respuesta['success'] = sprintf('Venta #%d: DTE generado y enviado existosamente.', $venta['Venta']['id']);
				}else{
					$respuesta['success'] = sprintf('Venta #%d: DTE generado existosamente.', $venta['Venta']['id']);
				}
			}

			return $respuesta;

		}else{
			$respuesta['errors'] = sprintf('No fue posible generar el DTE para la venta #%d. Verifique los campos e intente nuevamente.', $venta['Venta']['id']);
			return $respuesta;
		}
	}


	/**
	 * Obtiene todos los datos necesarios de una venta
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function preparar_venta($id)
	{
		# Toda la información de la venta
		$venta = $this->Venta->obtener_venta_por_id($id);
		
		# Linio
		if ($venta['Marketplace']['marketplace_tipo_id'] == 1) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Linio = $this->Components->load('Linio');
			}
			# cliente Linio
			$this->Linio->crearCliente( $venta['Marketplace']['api_host'], $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'] );

			//$mensajes =  $this->Linio->linio_obtener_venta_mensajes($venta, $ConexionLinio);

			// Obtener detall venta externo
			$venta['VentaExterna'] = $this->Linio->linio_obtener_venta($venta['Venta']['id_externo'], true);

			// Datos d facturacion
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);

			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

			// Detalles de envio
			$venta['Envio'][0] = array(
				'id'                      => null,
				'tipo'                    => null,
				'estado'                  => null,
				'direccion_envio'         => sprintf('%s, %s, %s', $venta['VentaExterna']['AddressShipping']['Address1'], $venta['VentaExterna']['AddressShipping']['Address2'], $venta['VentaExterna']['AddressShipping']['City']),
				'nombre_receptor'         => sprintf('%s %s', $venta['VentaExterna']['AddressShipping']['FirstName'], $venta['VentaExterna']['AddressShipping']['LastName']),
				'fono_receptor'           => $venta['VentaExterna']['AddressShipping']['Phone'],
				'producto'                => null,
				'cantidad'                => 1, // No especifica
				'costo'                   => 0,
				'fecha_entrega_estimada'  => $venta['VentaExterna']['PromisedShippingTime'],
				'comentario'              => '',
				'mostrar_etiqueta'        => false,
				'paquete' 				  => false
			);
			
		}

		# MEli
		if ($venta['Marketplace']['marketplace_tipo_id'] == 2) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->MeliMarketplace = $this->Components->load('MeliMarketplace');
			}
			$this->MeliMarketplace->crearCliente( $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'], $venta['Marketplace']['access_token'], $venta['Marketplace']['refresh_token'] );
			$this->MeliMarketplace->mercadolibre_conectar('', $venta['Marketplace']);

			$mensajes = $this->MeliMarketplace->mercadolibre_obtener_mensajes($venta['Marketplace']['access_token'], $venta['Venta']['id_externo']);

			foreach ($mensajes as $mensaje) {
				$data = array();
				$data['mensaje'] = $this->removeEmoji($mensaje['text']['plain']);
				$data['fecha'] = CakeTime::format($mensaje['date'], '%d-%m-%Y %H:%M:%S');
				$data['asunto'] = $mensaje['subject'];
				$venta['VentaMensaje'][] = $data;
			}

			// Detalles de la venta externa
			$venta['VentaExterna'] = $this->MeliMarketplace->mercadolibre_obtener_venta_detalles($venta['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);
			
			// Datos d facturacion
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);


			if (isset($venta['VentaExterna']['shipping']['id'])) {

				$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

				// Detalles de envio
				$direccion_envio = '';
				$nombre_receptor = '';
				$fono_receptor   = '';
				$comentario      = '';

				if (isset($venta['VentaExterna']['shipping']['receiver_address']['address_line'])
					&& isset($venta['VentaExterna']['shipping']['receiver_address']['city']['name'])) {
					$direccion_envio = sprintf('%s, %s', $venta['VentaExterna']['shipping']['receiver_address']['address_line'], $venta['VentaExterna']['shipping']['receiver_address']['city']['name']);
				}

				if (isset($venta['VentaExterna']['shipping']['receiver_address']['receiver_name'])) {
					$nombre_receptor = $venta['VentaExterna']['shipping']['receiver_address']['receiver_name'];
				}

				if (isset($venta['VentaExterna']['shipping']['receiver_address']['receiver_phone'])) {
					$fono_receptor = $venta['VentaExterna']['shipping']['receiver_address']['receiver_phone'];
				}

				if (isset($venta['VentaExterna']['shipping']['receiver_address']['comment'])) {
					$comentario = $venta['VentaExterna']['shipping']['receiver_address']['comment'];
				}

				
				$venta['Envio'][0] = array(
					'id'                      => $venta['VentaExterna']['shipping']['id'],
					'tipo'                    => $venta['VentaExterna']['shipping']['shipping_option']['name'],
					'estado'                  => $venta['VentaExterna']['shipping']['status'],
					'direccion_envio'         => $direccion_envio,
					'nombre_receptor'         => $nombre_receptor,
					'fono_receptor'           => $fono_receptor,
					'producto'                => null,
					'cantidad'                => 1,
					'costo'                   => $venta['VentaExterna']['shipping']['shipping_option']['cost'],
					'fecha_entrega_estimada'  => (isset($venta['VentaExterna']['shipping']['shipping_option']['estimated_delivery_time'])) ? CakeTime::format($venta['VentaExterna']['shipping']['shipping_option']['estimated_delivery_time']['date'], '%d-%m-%Y %H:%M:%S') : __('No especificado') ,
					'comentario'              => $comentario,
					'mostrar_etiqueta'        => ($venta['VentaExterna']['shipping']['status'] == 'ready_to_ship') ? true : false,
					'paquete' 				  => false
				);	
				
			}

		}	

		# Prestashop
		if (!$venta['Venta']['marketplace_id']) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Prestashop = $this->Components->load('Prestashop');
			}
			# Cliente Prestashop
			$this->Prestashop->crearCliente( $venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop'] );	

			// Obtener detall venta externo
			$venta['VentaExterna'] = $this->Prestashop->prestashop_obtener_venta($venta['Venta']['id_externo']);		

			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

			$venta['VentaMensaje'] = $this->Prestashop->prestashop_obtener_venta_mensajes($venta['Venta']['id_externo']);

			$direccionEnvio       = $this->Prestashop->prestashop_obtener_venta_direccion($venta['VentaExterna']['id_address_delivery']);				

			// Detalles de envio
			$telefonosEnvio = '';
			
			if (is_array($direccionEnvio['address']['phone_mobile']) && !empty($direccionEnvio['address']['phone_mobile'])) {
				$telefonosEnvio .= implode(' ', $direccionEnvio['address']['phone_mobile']);
			}

			if (!is_array($direccionEnvio['address']['phone_mobile']) && !empty($direccionEnvio['address']['phone_mobile'])) {
				$telefonosEnvio .= ' ' . $direccionEnvio['address']['phone_mobile'];
			}


			if (is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) {
				$telefonosEnvio .= implode(' ', $direccionEnvio['address']['phone']);
			}

			if (!is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) {
				$telefonosEnvio .= ' ' . $direccionEnvio['address']['phone'];
			}
			
			// Detalles de envio
			$venta['Envio'][0] = array(
				'id'                      => $direccionEnvio['address']['id'],
				'tipo'                    => 'Dir. despacho',
				'estado'                  => (!$direccionEnvio['address']['deleted']) ? 'activo' : 'eliminada',
				'direccion_envio'         => @sprintf('%s %s, %s', $direccionEnvio['address']['address1'], implode(',', $direccionEnvio['address']['address2']), $direccionEnvio['address']['city']),
				'nombre_receptor'         => @sprintf('%s %s', $direccionEnvio['address']['firstname'], $direccionEnvio['address']['lastname']),
				'fono_receptor'           => $telefonosEnvio,
				'producto'                => null,
				'cantidad'                => 1, // No especifica
				'costo'                   => $venta['VentaExterna']['total_shipping_tax_incl'],
				'fecha_entrega_estimada'  => 'No especificado',
				'comentario'              => @implode(',', $direccionEnvio['address']['other']),
				'mostrar_etiqueta'        => true,
				'paquete' 				  => false
			);

			# Datos de facturación para compras por Prestashop
			ToolmaniaComponent::$api_url = $venta['Tienda']['apiurl_prestashop'];
			
			#Obtener información webpay si es necesario
			#$webpay                      = $this->Toolmania->obtenerWebpayInfo($this->request->data['Orden']['id_cart'], $this->Session->read('Tienda.apikey_prestashop'));
			$documentos                  = $this->Toolmania->obtenerDocumento($venta['Venta']['id_externo'], null, $venta['Tienda']['apikey_prestashop']);
			
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);
			
			if (!empty($documentos['content'])) {

				$tipoDoc = ($documentos['content'][0]['boleta']) ? 39 : 33;

				$facturacion = array(
					'tipo_documento'        => $tipoDoc,
					'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[$tipoDoc],
					'rut_receptor'          => $documentos['content'][0]['rut'],
					'razon_social_receptor' => $documentos['content'][0]['empresa'],
					'giro_receptor'         => $documentos['content'][0]['giro'],
					'direccion_receptor'    => $documentos['content'][0]['calle']
				);
				# Para la consola se carga el componente on the fly!
				if ($this->shell) {
					$this->LibreDte = $this->Components->load('LibreDte');
				}
				// Obtenemos la información del contribuyente desde el SII
				$this->LibreDte->crearCliente($venta['Tienda']['facturacion_apikey']);
		
				$info = $this->LibreDte->obtenerContribuyente($this->rutSinDv($documentos['content'][0]['rut']));
				
				// Agregamos comuna
				if (isset($info['comuna_glosa'])) {
					$facturacion['comuna_receptor'] = $info['comuna_glosa'];
				}

				// Agregamos razon social
				if (empty($documentos['content'][0]['empresa']) && isset($info['razon_social'])) {
					$facturacion['razon_social_receptor'] = $info['razon_social'];
				}

				// Agregamos giro
				if (empty($documentos['content'][0]['giro']) && isset($info['giro'])) {
					$facturacion['giro_receptor'] = $info['giro'];
				}	

				// Agregamos direccon
				if (empty($documentos['content'][0]['direccion_receptor']) && isset($info['direccion'])) {
					$facturacion['direccion_receptor'] = $info['direccion'];
				}	
				
				
				# Guardamos el rut de la persona
				ClassRegistry::init('VentaCliente')->id = $venta['VentaCliente']['id'];
				ClassRegistry::init('VentaCliente')->saveField('rut', $documentos['content'][0]['rut']);

				$venta['VentaCliente']['rut'] = $documentos['content'][0]['rut'];

				$venta['VentaExterna']['facturacion'] = array_replace_recursive($venta['VentaExterna']['facturacion'], $facturacion);
			}
		}

		return $venta;
	}


	/**
	 * [admin_crear_dte_one_click description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_crear_dte_one_click($id)
	{	
		$venta = $this->preparar_venta($id);

		try {
			$result = $this->crearDteAutomatico($venta);
		} catch (Exception $e) {
			$result['errors'] = $e->getMessage();
		}
		

		if (!empty($result['success'])) {
			$this->Session->setFlash($result['success'], null, array(), 'success');			
		}

		if (!empty($result['errors'])) {
			$this->Session->setFlash($result['errors'], null, array(), 'danger');			
		}

		$this->redirect(array('action' => 'view', $id));

	}


	/**
	 * Cambia el estado a preparación segun corresponda
	 * @param  [type] $venta [description]
	 * @return [type]        [description]
	 */
	public function cambiar_estado_preparada($venta)
	{	
		ClassRegistry::init('VentaEstado')->id = $venta['Venta']['venta_estado_id'];
		ClassRegistry::init('Tienda')->id      = $venta['Venta']['tienda_id'];

		if (!empty($venta['Venta']['marketplace_id'])) {
			ClassRegistry::init('Marketplace')->id = $venta['Venta']['marketplace_id'];				
		}
				
		$notificar        = ClassRegistry::init('VentaEstado')->field('notificacion_cliente');
		$esPrestashop     = (empty($venta['Venta']['marketplace_id'])) ? true : false;
		$estado_actual    = $venta['Venta']['venta_estado_id'];
		$estado_nuevo     = '';
		$estado_nuevo_arr = array();
		$id_externo       = $venta['Venta']['id_externo'];
		$plantillaEmail   = ClassRegistry::init('VentaEstadoCategoria')->field('plantilla', array('id' => ClassRegistry::init('VentaEstado')->field('venta_estado_categoria_id')));
		
		$esMercadolibre = false;
		$esLinio        = false;

		# Verificamos el canal de la venta
		if (!empty($venta['Venta']['marketplace_id'])) {
			switch ( ClassRegistry::init('Marketplace')->field('marketplace_tipo_id') ) {
				case 1: // Linio
					$esLinio      = true;
					$apiurllinio  = ClassRegistry::init('Marketplace')->field('api_host');
					$apiuserlinio = ClassRegistry::init('Marketplace')->field('api_user');
					$apikeylinio  = ClassRegistry::init('Marketplace')->field('api_key');
					break;
				
				case 2: // Meli
					$esMercadolibre = true;
					break;
			}
		}

		$apiurlprestashop = ClassRegistry::init('Tienda')->field('apiurl_prestashop');
		$apikeyprestashop = ClassRegistry::init('Tienda')->field('apikey_prestashop');

		
		# Prestashop
		if ( $esPrestashop && !empty($apiurlprestashop) && !empty($apikeyprestashop)) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Prestashop = $this->Components->load('Prestashop');
			}
			# Cliente Prestashop
			$this->Prestashop->crearCliente( $apiurlprestashop, $apikeyprestashop );

			# Obtenemos estado de en prepracion
			$preparacion      = ClassRegistry::init('VentaEstado')->obtener_estado_preparacion();

			if (empty($preparacion)) {
				return false;
			}

			$estado_nuevo     = $preparacion['VentaEstado']['nombre'];
			$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);
			
			# El estado ya se ha actualizado
			if ($estado_actual == $estado_nuevo_arr['VentaEstado']['id']) {
				return false;
			}

			# OBtenemos el ID prestashop del estado
			$estadoPrestashop = $this->Prestashop->prestashop_obtener_estado_por_nombre($estado_nuevo);
			
			if (empty($estadoPrestashop)) {
				return false;
			}

			$resCambio = $this->Prestashop->prestashop_cambiar_estado_venta($id_externo, $estadoPrestashop['id']);
			#$resCambio = true;
			if ($resCambio) {

				# Asignamos el nuevo estado a la venta intenra
				$venta['Venta']['venta_estado_id'] = $estado_nuevo_arr['VentaEstado']['id'];
				
				# Plantilla nuevo estado
				ClassRegistry::init('VentaEstado')->id = $venta['Venta']['venta_estado_id'];
				$notificar        = ClassRegistry::init('VentaEstado')->field('notificacion_cliente');
				$plantillaEmail   = ClassRegistry::init('VentaEstadoCategoria')->field('plantilla', array('id' => ClassRegistry::init('VentaEstado')->field('venta_estado_categoria_id')));	
				
				if (!empty($plantillaEmail) && $notificar) {
					$this->notificar_cambio_estado($venta['Venta']['id'], $plantillaEmail, $estado_nuevo);
				}
			}
		}
		
		# Guardamos el nuevo estado
		$this->Venta->id = $venta['Venta']['id'];
		if ($this->Venta->saveField('venta_estado_id', $venta['Venta']['venta_estado_id'])) {
			return true;

		}else{
			return false;
		}

	}


	/**
	 * [admin_facturacion_masiva description]
	 * @return [type] [description]
	 */
	public function admin_facturacion_masiva()
	{	
		$result = array(
			'success',
			'errors'
		);

		$pdfs = array();

		$pdfsEtiquetas = array();

		$url_retorno = $this->request->data['Venta']['return_url'];

		if ($this->request->is('post')) {

			foreach ($this->request->data['Venta'] as $iv => $v) {

				# No existe a venta
				if (!$this->Venta->exists($v['id'])) {
					$result['errors'][] = sprintf('La venta #%d no existe en los registros', $v['id']);
					continue;
				}

				$venta = $this->preparar_venta($v['id']);

				/*$url_etiqueta_qr = $this->obtener_codigo_qr_url($venta['Venta']['id']);
				
				$pdfs[] = $url_etiqueta_qr['path'];*/
				
				$result_dte = $this->crearDteAutomatico($venta);

				if (!empty($result_dte['success'])) {

					$result['success'][] = $result_dte['success'];

					$nwDte = ClassRegistry::init('Dte')->find('all', array(
						'conditions' => array(
							'Dte.venta_id' => $venta['Venta']['id']
						),
						'fields' => array(
							'Dte.id', 'Dte.folio', 'Dte.tipo_documento', 'Dte.rut_receptor', 'Dte.razon_social_receptor', 'Dte.giro_receptor', 'Dte.neto', 'Dte.iva',
							'Dte.total', 'Dte.fecha', 'Dte.estado', 'Dte.venta_id', 'Dte.pdf', 'Dte.invalidado'
						),
						'order' => 'Dte.fecha DESC'
					));

					if (empty($nwDte)) {
						continue;
					}

					$venta['Dte'] = Hash::extract($nwDte, '{n}.Dte');

					$dtes = $this->obtener_dtes_pdf_venta($venta['Dte']);
					
					if (empty($dtes)) {
						continue;
					}

					#$this->cambiar_estado_preparada($venta);

					foreach ($dtes as $dte) {
						$pdfs[] = $dte['path'];
					}

					$venta = $this->preparar_venta($v['id']);

					$url_etiqueta_envio = $this->obtener_etiqueta_envio_default_url($venta);
					
					$pdfs[] = $url_etiqueta_envio['path'];

					# solo etiquetas
					$pdfsEtiquetas[] = $url_etiqueta_envio['path'];
					
					continue;
				}

				if (!empty($result_dte['errors'])) {

					$result['errors'][] = $result_dte['errors'];
					continue;
				}

			}
		}
		
		if (!empty($result['errors'])) {
			$this->Session->setFlash($this->crearAlertaUl($result['errors'], 'Errores encontrados'), null, array(), 'danger');
		}
		
		if (!empty($result['success'])) {
			
			$this->Session->setFlash($this->crearAlertaUl($result['success'], 'Procesados correctamente'), null, array(), 'success');

			if (!empty($pdfs)) {
				
				$pdf = $this->unir_documentos($pdfs, 'todo');
				$pdfEtiqueta = $this->unir_documentos($pdfsEtiquetas, 'todo_etiquetas');

				$pdf_resultado = array();

				if (!empty($pdf['result'])) {
					foreach ($pdf['result'] as $ir => $url) {
						$pdf_resultado[] = '<a href="'.$url['document'].'" class="link" download><i class="fa fa-download"></i> Descargar PDF Dte, Envio, Etiqueta </a>';
					}
				}

				if (!empty($pdfEtiqueta['result'])) {
					foreach ($pdfEtiqueta['result'] as $ir => $url) {
						$pdf_resultado[] = '<a href="'.$url['document'].'" class="link" download><i class="fa fa-download"></i> Descargar PDF Etiquetas </a>';
					}
				}

				if (!empty($pdf_resultado)) {
					$this->Session->setFlash($this->crearAlertaUl($pdf_resultado, 'Descargas disponibles'), null, array(), 'success');	
				}
			}
		}
	
		$this->redirect($url_retorno);
	}


	/**
	 * Métodos REST
	 */


	/**
	 * [api_obtener_venta description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function api_obtener_venta($id)
	{	
		# Sólo método Get
		if (!$this->request->is('get')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only GET request allow'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

		# No existe venta
		if (!$this->Venta->exists($id)) {
			$response = array(
				'code'    => 404, 
				'message' => 'Sale not found'
			);

			throw new CakeException($response);
		}

		# Detalles de la venta
		$venta = $this->preparar_venta($id);


		$respuesta =  array(
			'cliente' => array(
				'rut'      => $venta['VentaCliente']['rut'],
				'nombre'   => $venta['VentaCliente']['nombre'],
				'apellido' => $venta['VentaCliente']['apellido'],
				'email'    => $venta['VentaCliente']['email'],
				'fono'     => $venta['VentaCliente']['telefono'],
			),
			'venta' => array(
				'id'          => $venta['Venta']['id'],
				'id_externo'  => $venta['Venta']['id_externo'],
				'referencia'  => $venta['Venta']['referencia'],
				'fecha_venta' => $venta['Venta']['fecha_venta'],
				'total'       => $venta['Venta']['total'],
				'total_clp'   => CakeNumber::currency($venta['Venta']['total'], 'CLP'),
				'descuento'   => $venta['Venta']['descuento'],
				'costo_envio' => $venta['Venta']['costo_envio'],
				'estado'      => $venta['VentaEstado']['VentaEstadoCategoria']['nombre'],
				'subestado'   => $venta['VentaEstado']['nombre'],
				'canal_venta' => (!empty($venta['Marketplace']['id'])) ? $venta['Marketplace']['nombre'] : $venta['Tienda']['nombre'], 
			),
			'entrega' => array(
				'metodo'                 => $venta['VentaExterna']['transportista'],
				'fecha_entrega_estimada' => $venta['Envio'][0]['fecha_entrega_estimada'],
			),
			'itemes' => array(),
			'confirm_url' => array(
				'host' => Router::url('/', true),
				'endpoint' => sprintf('api/ventas/change_state/%d.json', $id),
				'required_params' => array(
					'type' => 'shipped OR delivered',
					'token' => 'your access token'
				)
			),
		);


		foreach ($venta['VentaDetalle'] as $i => $item) {
			$respuesta['itemes'][$i] = array(
				'nombre'           => $item['VentaDetalleProducto']['nombre'],
				'cantidad'         => $item['cantidad'],
				'precio_neto'      => $item['precio'],
				'precio_bruto'     => $this->precio_bruto($item['precio']),
				'precio_bruto_clp' => CakeNumber::currency($this->precio_bruto($item['precio']), 'CLP')
			);
		}


		$this->set(array(
            'response' => $respuesta,
            '_serialize' => array('response')
        ));

	}


	/**
	 * [api_cambiar_estado description]
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function api_cambiar_estado($id = '')
	{	
		# Solo método POST
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only POST request allow'
			);

			throw new CakeException($response);
		}

		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		$token = $this->request->query['token'];
		$tipoEstado  = $this->request->query['type'];

		$tiposPermitidos = array(
			'shipped', // En transito
			'delivered',
			'enviado' // Enviado por carrier
		);

		# No vacios
		if (empty($id) || empty($token) || empty($tipoEstado)) {
			$response = array(
				'code'    => 503, 
				'message' => 'Empty value'
			);

			throw new CakeException($response);
		}

		# Tipos de cambios de estados disponibles
		if (!in_array($tipoEstado, $tiposPermitidos)) {
			$response = array(
				'code'    => 504, 
				'message' => 'Invalid value for type param'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($token)) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

		# No existe venta
		if (!$this->Venta->exists($id)) {
			$response = array(
				'code'    => 404, 
				'message' => 'Sale not found'
			);

			throw new CakeException($response);
		}

		# Detalles de la venta
		$venta = $this->preparar_venta($id);

		ClassRegistry::init('VentaEstado')->id = $venta['Venta']['venta_estado_id'];
		ClassRegistry::init('Tienda')->id      = $venta['Venta']['tienda_id'];

		if (!empty($this->request->data['Venta']['marketplace_id'])) {
			ClassRegistry::init('Marketplace')->id = $venta['Venta']['marketplace_id'];				
		}
				
		$notificar        = ClassRegistry::init('VentaEstado')->field('notificacion_cliente');
		$esPrestashop     = (empty($venta['Venta']['marketplace_id'])) ? true : false;
		$estado_actual    = $venta['Venta']['venta_estado_id'];
		$estado_nuevo     = '';
		$estado_nuevo_arr = array();
		$id_externo       = $venta['Venta']['id_externo'];
		$plantillaEmail   = ClassRegistry::init('VentaEstadoCategoria')->field('plantilla', array('id' => ClassRegistry::init('VentaEstado')->field('venta_estado_categoria_id')));
		
		$esMercadolibre = false;
		$esLinio        = false;

		# Verificamos el canal de la venta
		if (!empty($venta['Venta']['marketplace_id'])) {
			switch ( ClassRegistry::init('Marketplace')->field('marketplace_tipo_id') ) {
				case 1: // Linio
					$esLinio      = true;
					$apiurllinio  = ClassRegistry::init('Marketplace')->field('api_host');
					$apiuserlinio = ClassRegistry::init('Marketplace')->field('api_user');
					$apikeylinio  = ClassRegistry::init('Marketplace')->field('api_key');
					break;
				
				case 2: // Meli
					$esMercadolibre = true;
					break;
			}
		}

		$apiurlprestashop = ClassRegistry::init('Tienda')->field('apiurl_prestashop');
		$apikeyprestashop = ClassRegistry::init('Tienda')->field('apikey_prestashop');
		
		#$this->Venta->save($venta);
		#$enviado = $this->notificar_cambio_estado($id, $plantillaEmail, $estado_nuevo);

		# Prestashop
		if ( $esPrestashop && !empty($apiurlprestashop) && !empty($apikeyprestashop)) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Prestashop = $this->Components->load('Prestashop');
			}
			# Cliente Prestashop
			$this->Prestashop->crearCliente( $apiurlprestashop, $apikeyprestashop );

			switch ($tipoEstado) {
				case 'shipped':
					# Obtenemos el estado de enviado
					$estado_nuevo     = 'En transito';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);
					break;

				case 'enviado':
					# Obtenemos el estado de enviado
					$estado_nuevo     = 'Enviado';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);
					break;
				
				case 'delivered':
					# Obtenemos estado de entregado
					$estado_nuevo     = 'Entregado';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);
					break;
			}

			# Mensaje de error en caso de que no exista el estado
			if (empty($estado_nuevo)) {
				$response = array(
					'code'    => 404, 
					'message' => 'State not allowed'
				);

				throw new CakeException($response);
			}

			# El estado ya se ha actualizado
			if ($estado_actual == $estado_nuevo_arr['VentaEstado']['id']) {
				$response = array(
					'code'    => 500, 
					'message' => 'The current state is the same that you try to update'
				);

				throw new CakeException($response);
			}

			# OBtenemos el ID prestashop del estado
			$estadoPrestashop = $this->Prestashop->prestashop_obtener_estado_por_nombre($estado_nuevo);

			if (empty($estadoPrestashop)) {
				$response = array(
					'code'    => 507, 
					'message' => 'Can´t get state from Prestashop'
				);

				throw new CakeException($response);
			}

			$resCambio = $this->Prestashop->prestashop_cambiar_estado_venta($id_externo, $estadoPrestashop['id']);
			#$resCambio = true;
			if ($resCambio) {

				# Asignamos el nuevo estado a la venta intenra
				$venta['Venta']['venta_estado_id'] = $estado_nuevo_arr['VentaEstado']['id'];
			
				# Plantilla nuevo estado
				ClassRegistry::init('VentaEstado')->id = $venta['Venta']['venta_estado_id'];
				$notificar        = ClassRegistry::init('VentaEstado')->field('notificacion_cliente');
				$plantillaEmail   = ClassRegistry::init('VentaEstadoCategoria')->field('plantilla', array('id' => ClassRegistry::init('VentaEstado')->field('venta_estado_categoria_id')));	
				
				if (!empty($plantillaEmail) && $notificar) {
					$this->notificar_cambio_estado($id, $plantillaEmail, $estado_nuevo);
				}

			}else{

				$response = array(
					'code'    => 506, 
					'message' => 'Can´t save new state'
				);

				throw new CakeException($response);
			}
			
		# Linio
		}elseif ( $esLinio && !empty($apiurllinio) && !empty($apiuserlinio) && !empty($apikeylinio)) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Linio = $this->Components->load('Linio');
			}
			# cliente Linio
			$this->Linio->crearCliente( $apiurllinio, $apiuserlinio, $apikeylinio );

			$itemsVenta = $this->Linio->linio_obtener_venta_detalles($venta['Venta']['id_externo']);

			if (!isset($itemsVenta[0])) {
				$itemsVenta = array(
					0 => $itemsVenta
				);
			}

			switch ($tipoEstado) {
				case 'shipped':
					# Obtenemos el estado de enviado
					$estado_nuevo     = 'ready_to_ship';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);
					break;
			}

			# Mensaje de error en caso de que no exista el estado
			if (empty($estado_nuevo)) {
				$response = array(
					'code'    => 404, 
					'message' => 'State not allowed'
				);

				throw new CakeException($response);
			}

			if (!array_key_exists($estado_nuevo, $this->Linio->estados)) {
				$response = array(
					'code'    => 404, 
					'message' => 'State not allowed'
				);

				throw new CakeException($response);
			}


			# El estado ya se ha actualizado
			if ($estado_actual == $estado_nuevo_arr['VentaEstado']['id']) {
				$response = array(
					'code'    => 500, 
					'message' => 'The current state is the same that you try to update'
				);

				throw new CakeException($response);
			}

			
			# Pedimos retiro del pedido en Linio
			foreach ($itemsVenta as $ii => $item) {

				# Listo para envio pedido en Linio Por defecto se usa Blue Express
				if(!$this->Linio->linio_listo_para_envio(array($item['OrderItemId']))){
					$response = array(
						'code'    => 507, 
						'message' => 'Can´t update state in Marketplace'
					);

					throw new CakeException($response);
				}
			}

			# Asignamos el nuevo estado a la venta intenra
			$venta['Venta']['venta_estado_id'] = $estado_nuevo_arr['VentaEstado']['id'];
			
		# Meli
		}elseif ( $esMercadolibre && !empty($apiurl) && !empty($apikey)) {
			$response = array(
				'code'    => 507, 
				'message' => 'Can´t update state in Marketplace'
			);

			throw new CakeException($response);
		}else{
			$response = array(
				'code'    => 404, 
				'message' => 'State not allowed'
			);

			throw new CakeException($response);
		}
		
		# Guardamos el nuevo estado
		$this->Venta->id = $id;
		#$ts = true;
		#if ($ts) {
		if ($this->Venta->saveField('venta_estado_id', $venta['Venta']['venta_estado_id'])) {
			
			$this->set(array(
	            'response' => true,
	            '_serialize' => array('response')
	        ));

		}else{
			$response = array(
				'code'    => 510, 
				'message' => 'Can´t save state in local Database'
			);

			throw new CakeException($response);
		}

	}


	/**
	 * [api_registrar_seguimiento description]
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function api_registrar_seguimiento($id = '')
	{
		# Sólo método Get
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only POST request allow'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

		# No existe venta
		if (!$this->Venta->exists($id)) {
			$response = array(
				'code'    => 404, 
				'message' => 'Sale not found'
			);

			throw new CakeException($response);
		}

		# No hay codigos de seguimiento
		if (!isset($this->request->data['Tracking']) || empty($this->request->data['Tracking'])) {
			$response = array(
				'code'    => 507, 
				'message' => 'Tracking array is required'
			);

			throw new CakeException($response);
		}

		# Detalles de la venta
		$venta       = $this->preparar_venta($id);
		
		$transportes = $this->request->data['Tracking'];
		
		$token       = $this->request->query['token'];


		$error = 0;

		# Validamos que cada array tenga ambos campos con información
		foreach ($transportes as $it => $transporte) {
			if (!isset($transporte['transportista'])
				|| empty($transporte['transportista'])
				|| !isset($transporte['seguimiento'])
				|| empty($transporte['seguimiento'])
				|| !ClassRegistry::init('Transporte')->exists($transporte['transportista']) ) {
				$error = 1;
			}
		}

		if ($error) {
			$response = array(
				'code'    => 508, 
				'message' => 'Carrier and tracking code are required or carrier doesn´t exist'
			);

			throw new CakeException($response);
		}

		$dataToSave = array(
			'Venta' => array(
				'id' => $venta['Venta']['id']
			)
		);

		foreach ($transportes as $it => $t) {
			$dataToSave['Transporte'][$it]['transporte_id']   = $t['transportista'];
			$dataToSave['Transporte'][$it]['cod_seguimiento'] = $t['seguimiento'];
			$dataToSave['Transporte'][$it]['created']         = date('Y-m-d H:i:s');
		}

		# Guardamos los códigos de seguimiento
		if ($this->Venta->saveAll($dataToSave)) {

			# Hacemos un POST para cambiar el estado correspondiente
			App::uses('HttpSocket', 'Network/Http');
			$socket			= new HttpSocket();
			
			$request		= $socket->post(Router::url('/api/ventas/change_state/'.$this->Venta->id.'.json?type=enviado&token='.$token, true));
			
			$request->body = json_decode($request->body, true);
			
			if (isset($request->body['response']) && $request->body['response'] == true) {
				$respuesta = true;
			}else{
				$response = array(
					'code'    => $request->body['code'], 
					'message' => $request->body['message']
				);

				throw new CakeException($response);
			}

		}else{
			$response = array(
				'code'    => 509, 
				'message' => 'Can´t save tracking number for this sale'
			);

			throw new CakeException($response);
		}

		$this->set(array(
            'response' => $respuesta,
            '_serialize' => array('response')
        ));
		
	}

}
