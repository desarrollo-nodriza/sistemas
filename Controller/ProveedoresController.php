<?php
App::uses('AppController', 'Controller');
App::uses('OrdenComprasController', 'Controller');
class ProveedoresController extends AppController
{

	public $HORAS = [
		"00:00:00" => "00:00:00",
		"00:30:00" => "00:30:00",
		"01:00:00" => "01:00:00",
		"01:30:00" => "01:30:00",
		"02:00:00" => "02:00:00",
		"02:30:00" => "02:30:00",
		"03:00:00" => "03:00:00",
		"03:30:00" => "03:30:00",
		"04:00:00" => "04:00:00",
		"04:30:00" => "04:30:00",
		"05:00:00" => "05:00:00",
		"05:30:00" => "05:30:00",
		"06:00:00" => "06:00:00",
		"06:30:00" => "06:30:00",
		"07:00:00" => "07:00:00",
		"07:30:00" => "07:30:00",
		"08:00:00" => "08:00:00",
		"08:30:00" => "08:30:00",
		"09:00:00" => "09:00:00",
		"09:30:00" => "09:30:00",
		"10:00:00" => "10:00:00",
		"10:30:00" => "10:30:00",
		"11:00:00" => "11:00:00",
		"11:30:00" => "11:30:00",
		"12:00:00" => "12:00:00",
		"12:30:00" => "12:30:00",
		"13:00:00" => "13:00:00",
		"13:30:00" => "13:30:00",
		"14:00:00" => "14:00:00",
		"14:30:00" => "14:30:00",
		"15:00:00" => "15:00:00",
		"15:30:00" => "15:30:00",
		"16:00:00" => "16:00:00",
		"16:30:00" => "16:30:00",
		"17:00:00" => "17:00:00",
		"17:30:00" => "17:30:00",
		"18:00:00" => "18:00:00",
		"18:30:00" => "18:30:00",
		"19:00:00" => "19:00:00",
		"19:30:00" => "19:30:00",
		"20:00:00" => "20:00:00",
		"20:30:00" => "20:30:00",
		"21:00:00" => "21:00:00",
		"21:30:00" => "21:30:00",
		"22:00:00" => "22:00:00",
		"22:30:00" => "22:30:00",
		"23:00:00" => "23:00:00",
		"23:30:00" => "23:30:00",
	];

	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);

		BreadcrumbComponent::add('Proveedores ');

		$proveedores	= $this->paginate();
		$this->set(compact('proveedores'));
	}

	public function admin_add()
	{
		if ($this->request->is('post')) {
			$this->Proveedor->create();
			if ($this->Proveedor->save($this->request->data)) {
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Proveedores ', '/proveedores');
		BreadcrumbComponent::add('Agregar ');
	}

	public function admin_update()
	{
		$mensaje =  $this->actualizar_proveedores_base();

		$this->Session->setFlash($this->crearAlertaUl($mensaje), null, array(), 'warning');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_edit($id = null)
	{
		if (!$this->Proveedor->exists($id)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->Proveedor->MonedasProveedor->deleteAll(array('MonedasProveedor.proveedor_id' => $id));

			# Guardamos los emails en un objeto json
			if (isset($this->request->data['ProveedoresEmail'])) {
				$this->request->data['Proveedor']['meta_emails'] = json_encode($this->request->data['ProveedoresEmail'], true);
			}
			if (isset($this->request->data['FrecuenciaGenerarOC'])) {

				$this->Proveedor->FrecuenciaGenerarOC->deleteAll(array('proveedor_id' => $id));
				$this->Proveedor->TipoEntregaProveedorOC->deleteAll(array('proveedor_id' => $id));


				$this->request->data['FrecuenciaGenerarOC'] = array_filter($this->request->data['FrecuenciaGenerarOC'], function ($v, $k) {
					return !empty($v['hora']);
				}, ARRAY_FILTER_USE_BOTH);

				$this->request->data['TipoEntregaProveedorOC'] = array_filter($this->request->data['TipoEntregaProveedorOC'], function ($v, $k) {
					return !empty($v['bodega_id']) && !empty($v['tienda_id']) && !empty($v['tipo_entrega']);
				}, ARRAY_FILTER_USE_BOTH);

				$this->request->data['ReglasGenerarOC'] = array_filter($this->request->data['ReglasGenerarOC'], function ($v, $k) {
					return !empty($v['regla_generar_oc_id']);
				}, ARRAY_FILTER_USE_BOTH);
			}

			$this->Proveedor->RangoDespacho->deleteAll(array('proveedor_id' => $id));
			$this->request->data['RangoDespacho'] = array_filter($this->request->data['RangoDespacho'], function ($v, $k) {
				return is_numeric($v['rango_desde']) && is_numeric($v['rango_hasta']) && is_numeric($v['despacho']);
			}, ARRAY_FILTER_USE_BOTH);


			if ($this->Proveedor->saveAll($this->request->data)) {

				if ($this->request->data['Proveedor']['actualizar_canales']) {
					if (!$this->actualizar_proveedor($id, $this->request->data['Proveedor']['nombre'])) {
						$this->Session->setFlash('No fue posible actualizar el proveedor en Prestashop', null, array(), 'warning');
					}
				}

				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'edit', $id));
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		} else {
			$this->request->data	= $this->Proveedor->find('first', array(
				'conditions'	=> array('Proveedor.id' => $id),
				'contain' => array(
					'Moneda',
					'Saldo' => array(
						'order' => array('Saldo.id' => 'DESC')
					),
					'FrecuenciaGenerarOC',
					'TipoEntregaProveedorOC',
					'ReglasGenerarOC' => [
						'order' 	=> array('ReglasGenerarOC.mayor_que' => 'ASC')
					],
					'RangoDespacho'
				)
			));
		}
		// prx($this->request->data);
		$this->request->data['Proveedor']['saldo'] = ClassRegistry::init('Saldo')->obtener_saldo_total_proveedor($id);

		$monedas = ClassRegistry::init('Moneda')->find('list', array('conditions' => array('activo' => 1)));

		$tipo_email = $this->Proveedor->obtener_tipo_email();

		$reglasGenerarOC = ClassRegistry::init('ReglasGenerarOC')->find('list', []);
		$reglasGenerarOC_2 = $reglasGenerarOC;
		foreach ($this->request->data['ReglasGenerarOC'] as $value) {
			unset($reglasGenerarOC[$value['id']]);
		}

		$horas 				= $this->HORAS;
		$bodegas 			= ClassRegistry::init('Bodega')->obtener_bodegas();
		$tiendas 			= ClassRegistry::init('Tienda')->obtener_activas();
		// $administradores	= ClassRegistry::init('Administrador')->find('list', ['conditions' => ['Administrador.activo' => true]]);
		$tipo_entrega 		=
			[
				'retiro' 	=> 'Retiro',
				'despacho' 	=> 'Despacho'
			];

		BreadcrumbComponent::add('Proveedores ', '/proveedores');
		BreadcrumbComponent::add('Editar ');

		$this->set(compact(
			'monedas',
			'tipo_email',
			'reglas',
			'reglasGenerarOC',
			'horas',
			'reglasGenerarOC_2',
			'bodegas',
			'tiendas',
			'tipo_entrega'
		));
	}

	public function admin_delete($id = null)
	{
		$this->Proveedor->id = $id;
		if (!$this->Proveedor->exists()) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ($this->Proveedor->delete()) {
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Proveedor->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Proveedor->_schema);
		$modelo			= $this->Proveedor->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	public function actualizar_proveedor($id, $nombre)
	{
		# Se carga el componente directamente para ser usado por la consola
		$this->Prestashop = $this->Components->load('Prestashop');

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array(
				'Tienda.activo' => 1
			),
			'contain' => array(
				'Marketplace' => array(
					'MarketplaceTipo'
				)
			),
			'fields' => array(
				'Tienda.apiurl_prestashop',
				'Tienda.apiurl_prestashop',
				'Tienda.apikey_prestashop',
				'Tienda.nombre'
			)
		));

		$return = true;

		foreach ($tiendas as $it => $tienda) {

			# Cliente Prestashop
			$this->Prestashop->crearCliente($tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop']);

			if (!$this->Prestashop->prestashop_actualizar_proveedor($id, $nombre)) {
				$return =  false;
			}
		}

		return $return;
	}

	/**
	 * Obtiene los proveedores y los agrega y/o actualiza los proveedores locales
	 * @return array 	Mensaje de la operación
	 */
	public function actualizar_proveedores_base()
	{
		# Se carga el componente directamente para ser usado por la consola
		$this->Prestashop = $this->Components->load('Prestashop');

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array(
				'Tienda.activo' => 1
			),
			'contain' => array(
				'Marketplace' => array(
					'MarketplaceTipo'
				)
			),
			'fields' => array(
				'Tienda.apiurl_prestashop',
				'Tienda.apiurl_prestashop',
				'Tienda.apikey_prestashop',
				'Tienda.nombre'
			)
		));

		foreach ($tiendas as $it => $tienda) {

			# Cliente Prestashop
			$this->Prestashop->crearCliente($tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop']);

			$proveedores = $this->Prestashop->prestashop_obtener_proveedores();

			$proveedoresLocales = array();
			$arrMessage 	  = array('No hay cambios disponibles.');

			foreach ($proveedores['supplier'] as $ip => $p) {

				# Verificamos que exista en la BD local
				$local = $this->Proveedor->find('first', array('conditions' => array('id' => $p['id']), 'fields' => array('id')));

				# Crear proveedor
				if (empty($local)) {
					$proveedoresLocales[$ip]['Proveedor']['id'] = $p['id'];
					$proveedoresLocales[$ip]['Proveedor']['nombre'] = $p['name'];
				}
			}
		}

		if (!empty($proveedoresLocales)) {

			if ($this->Proveedor->saveMany($proveedoresLocales)) {

				$this->relacionarProveedorProductos($this->Prestashop, $proveedoresLocales);
				$arrMessage = array(sprintf('Se han creado/modificado %d proveedores', count($proveedoresLocales)));
			}
		}

		return $arrMessage;
	}


	/**
	 * Permite relacionar los proveedores con los productos según la base de prestashop
	 * @param  obj $conexion instancia de prestashop   
	 * @param    $proveedores Arreglo de proveedores
	 * @return void
	 */
	private function relacionarProveedorProductos($conexion, $proveedores = array())
	{

		foreach ($proveedores as $i => $proveedor) {

			$filtroProductos = array(
				'filter[active]' => '[1]',
				'filter[id_supplier]' => '[' . $proveedor['Proveedor']['id'] . ']'
			);

			$productos = $conexion->prestashop_obtener_productos($filtroProductos);

			if (!empty($productos)) {
				foreach ($productos['product'] as $ip => $producto) {

					if (!isset($producto['id'])) {
						continue;
					}

					$data = array(
						'VentaDetalleProducto' => array(
							'id' => $producto['id'],
							'codigo_proveedor' => $producto['supplier_reference']
						),
						'Proveedor' => array(
							'Proveedor' => $proveedor['Proveedor']['id']
						)
					);

					if (ClassRegistry::init('VentaDetalleProducto')->exists($producto['id'])) {
						$this->Proveedor->VentaDetalleProducto->save($data);
					}
				}
			}
		}

		return;
	}


	public function admin_obtenerProveedor($id = null)
	{
		$res = array(
			'code' => 500,
			'message' => 'Error al procesar la solicitud',
			'data' => array()
		);

		$this->Proveedor->id = $id;
		if (!$this->Proveedor->exists()) {
			echo json_encode($res, true);
			exit;
		}

		$proveedor = $this->Proveedor->find('first', array(
			'conditions' => array(
				'Proveedor.id' => $id
			),
			'fields' => array(
				'id',
				'nombre',
				'email_contacto',
				'fono_contacto',
				'rut_empresa',
				'giro',
				'direccion',
				'nombre_encargado'
			)
		));

		$res = array(
			'code' => 200,
			'message' => 'Proveedor obtenido con éxito',
			'data' => $proveedor['Proveedor']
		);

		echo json_encode($res, true);
		exit;
	}

	public function admin_regla_create($proveedor_id)
	{

		$reglas = array_filter($this->request->data, function ($v, $k) {
			return !empty($v['proveedor_id']) and !empty($v['regla_generar_oc_id']);
		}, ARRAY_FILTER_USE_BOTH);

		$datos_a_guardar = [];

		foreach ($reglas as  $value) {
			$datos_a_guardar[] = ['ReglasProveedor' => $value];
		}

		ClassRegistry::init('ReglasProveedor')->create();
		ClassRegistry::init('ReglasProveedor')->saveAll($datos_a_guardar);

		$this->redirect(array('action' => 'edit', $proveedor_id));
	}

	public function api_delete_regla($id)
	{

		if (!isset($this->request->query['token'])) {

			throw new UnauthorizedException('Requiere un token validado');
		}

		ClassRegistry::init('ReglasProveedor')->id = $id;

		if (!ClassRegistry::init('ReglasProveedor')->exists()) {

			throw new NotFoundException('No existe elemento');
		}

		ClassRegistry::init('ReglasProveedor')->delete($id);

		$response = array(
			'code'    	=> 200,
			'name' 		=> "success $id",
			'message' 	=> 'Eliminado con exito',
		);

		$this->set(array(
			'response' => $response,
			'_serialize' => array('response')
		));
	}

	public function api_delete_frecuencia($id)
	{
		if (!isset($this->request->query['token'])) {

			throw new UnauthorizedException('Requiere un token validado');
		}

		ClassRegistry::init('FrecuenciaGenerarOC')->id = $id;

		if (!ClassRegistry::init('FrecuenciaGenerarOC')->exists()) {

			throw new NotFoundException('No existe elemento');
		}

		ClassRegistry::init('FrecuenciaGenerarOC')->delete($id);

		$response = array(
			'code'    	=> 200,
			'name' 		=> "success $id",
			'message' 	=> 'Eliminado con exito',
		);

		$this->set(array(
			'response' => $response,
			'_serialize' => array('response')
		));
	}

	public function api_delete_configuracion($id)
	{
		if (!isset($this->request->query['token'])) {

			throw new UnauthorizedException('Requiere un token validado');
		}

		ClassRegistry::init('TipoEntregaProveedorOC')->id = $id;

		if (!ClassRegistry::init('TipoEntregaProveedorOC')->exists()) {

			throw new NotFoundException('No existe elemento');
		}

		ClassRegistry::init('TipoEntregaProveedorOC')->delete($id);

		$response = array(
			'code'    	=> 200,
			'name' 		=> "success $id",
			'message' 	=> 'Eliminado con exito',
		);

		$this->set(array(
			'response' => $response,
			'_serialize' => array('response')
		));
	}

	/**
	 * admin_crearOcsAutomaticas
	 * Se crean OCs automaticas para el proveedor sin importar la frecuencia configurada
	 * @param  mixed $proveedor_id
	 * @return void
	 */
	public function admin_crearOcsAutomaticas($proveedor_id)
	{
		if (!$this->Proveedor->exists($proveedor_id)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$OrdenComprasController = new OrdenComprasController();
		$respuesta 				= $OrdenComprasController->CrearOCAutomaticas([$proveedor_id]);

		if ($respuesta['respuesta']) {
			$OCs = [];
			foreach ($respuesta['OCs'] as $value) {
				$OCs[] = "<a href='/ordenCompras/view/$value' target='_blank' class='link'>Ir a Oc $value</a>";
			}
			$this->Session->setFlash($this->crearAlertaUl($OCs, 'Ordenes de compra creadas'), null, array(), 'success');
		} else {
			$this->Session->setFlash("No hay productos del proveedor para crear OC", null, array(), 'warning');
		}

		$this->redirect(array('action' => 'edit', $proveedor_id));
	}


	public function admin_despacho_pedido()
	{

		if ($this->request->is('post')) {


			$RangoDespacho = array_filter($this->request->data['RangoDespacho'], function ($v, $k) {
				return is_numeric($v['rango_desde']) && is_numeric($v['rango_hasta']) && is_numeric($v['despacho']) && !empty($v['proveedor_id']);
			}, ARRAY_FILTER_USE_BOTH);

			ClassRegistry::init('RangoDespacho')->create();
			if (ClassRegistry::init('RangoDespacho')->saveAll($RangoDespacho)) {
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
			$this->redirect(array('action' => 'despacho_pedido'));
		}

		$proveedores =  ClassRegistry::init('Proveedor')->find('all', [
			'fields'	=>	[
				'id',
				'nombre'
			],
			'conditions' => ['activo' => 1],
			'contain' => ['RangoDespacho']
		]);

		BreadcrumbComponent::add('Proveedores ', '/proveedores');
		BreadcrumbComponent::add('Rango despacho proveedores ');
		$this->set(compact('proveedores'));
	}

	public function admin_delete_despacho_pedido($id, $proveedor_id = null)
	{

		ClassRegistry::init('RangoDespacho')->delete($id);

		if ($proveedor_id) {
			$this->redirect(array('action' => 'edit', $proveedor_id));
		}

		$this->redirect(array('action' => 'despacho_pedido'));
	}

	public function admin_cronjob_despacho_pedido()
	{

		ClassRegistry::init('Proveedor')->actualizar_tiempo_despacho_proveedor();
		
		$tiempo_despacho_producto_con_stock =  ClassRegistry::init('VentaDetalle')->tiempo_despacho_producto_con_stock();
		$ids_con_stock 						= Hash::extract($tiempo_despacho_producto_con_stock, '{n}.VentaDetalle.venta_detalle_producto_id');

		$proveedores = ClassRegistry::init('Proveedor')->find('list', [
			'fields' => [
				'Proveedor.id',
				'Proveedor.tiempo_despacho',
			],
			'conditions' => ['Proveedor.tiempo_despacho is not null']
		]);

		$data = [];

		foreach ($proveedores as $proveedor_id => $tiempo_despacho) {

			$productos_del_proveedor = ClassRegistry::init('VentaDetalleProducto')->find('all', [
				'fields' => [
					'VentaDetalleProducto.id',
					'VentaDetalleProducto.tiempo_despacho'
				],
				'conditions' => [
					'VentaDetalleProducto.id !=' 	 => $ids_con_stock,
					'ProveedorProducto.proveedor_id' => $proveedor_id
				],
				'joins' => array(
					array(
						'table' => 'rp_proveedores_venta_detalle_productos',
						'alias' => 'ProveedorProducto',
						'type' => 'INNER',
						'conditions' => array(
							'ProveedorProducto.venta_detalle_producto_id = VentaDetalleProducto.id',

						)
					)
				)
			]);

			$productos_ids = Hash::extract($productos_del_proveedor, '{n}.VentaDetalleProducto.id');

			ClassRegistry::init('VentaDetalleProducto')->updateAll(
				['VentaDetalleProducto.tiempo_despacho' => $tiempo_despacho],
				['VentaDetalleProducto.id' 				=> $productos_ids]
			);
			
		}

		$data = array_merge(array_map(function ($data) {
			return ['VentaDetalleProducto' => [
				'id' 				=> $data['VentaDetalle']['venta_detalle_producto_id'],
				'tiempo_despacho' 	=> $data[0]['tiempo_despacho'],
			]];
		}, $tiempo_despacho_producto_con_stock), $data);


		if ($data) {
			foreach (array_chunk($data, 500) as $value) {
				ClassRegistry::init('VentaDetalleProducto')->create();
				ClassRegistry::init('VentaDetalleProducto')->saveAll($value);
			}
		}

		prx($data);
	}
	
}
