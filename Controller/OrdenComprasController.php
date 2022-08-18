<?php
App::uses('AppController', 'Controller');
App::uses('VentaDetalleProductosController', 'Controller');
App::uses('PagosController', 'Controller');

class OrdenComprasController extends AppController
{

	public $components = array(
		'WarehouseNodriza',
	);

	/**
	 * Crea un redirect y agrega a la URL los parámetros del filtro
	 * @param 		$controlador 	String 		Nombre del controlador donde redirijirá la petición
	 * @param 		$accion 		String 		Nombre del método receptor de la petición
	 * @return 		void
	 */
	public function filtrar($controlador = '', $accion = 'index')
	{
		$redirect = array(
			'controller' => $controlador,
			'action' => $accion
		);

		foreach ($this->request->data['OrdenCompra'] as $campo => $valor) {
			if (!empty($valor)) {
				$redirect[$campo] = $valor;
			}
		}

		$this->redirect($redirect);
	}


	/**
	 * [reemplazar_filtro_recursivamente description]
	 * @param  [type] &$filtro [description]
	 * @return [type]          [description]
	 */
	private function reemplazar_filtro_recursivamente(&$filtro)
	{
		foreach ($this->request->params['named'] as $campo => $valor) {
			switch ($campo) {
				case 'id':

					$filtro = array_replace_recursive($filtro, array(
						'conditions' => array(
							'OrdenCompra.id' => trim($valor)
						)
					));


					break;
				case 'venta':

					# Buscamos las OC padres que tengan relacionada la venta
					$oc_venta =  ClassRegistry::init('Venta')->find('first', array(
						'conditions' => array(
							'Venta.id' => $valor
						),
						'contain' => array(
							'OrdenCompra' => array(
								'ChildOrdenCompra' => array(
									'ChildOrdenCompra.id'
								),
								'fields' => array(
									'OrdenCompra.id'
								)
							)
						),
						'fields' => array(
							'Venta.id'
						)
					));

					if (empty($oc_venta['OrdenCompra']))
						break;

					$idsOC = Hash::extract($oc_venta['OrdenCompra'], '{n}.ChildOrdenCompra.{n}.id');

					$filtro = array_replace_recursive($filtro, array(
						'conditions' => array(
							'OrdenCompra.id' => $idsOC
						)
					));

					break;
				case 'prod':

					$filtro = array_replace_recursive($filtro, array(
						'joins' => array(
							array(
								'table' => 'orden_compras_venta_detalle_productos',
								'alias' => 'OrdenComprasVentaDetalleProducto',
								'type'  => 'inner',
								'conditions' => array(
									'OrdenComprasVentaDetalleProducto.orden_compra_id = OrdenCompra.id',
									'OrdenComprasVentaDetalleProducto.venta_detalle_producto_id' => trim($valor)
								)
							)
						)
					));

					break;
				case 'sta':

					$filtro = array_replace_recursive($filtro, array(
						'conditions' => array('OrdenCompra.estado' => $valor)
					));

					break;
				case 'prov':

					$filtro = array_replace_recursive($filtro, array(
						'conditions' => array(
							'OrdenCompra.proveedor_id' => $valor
						)
					));

					break;
				case 'bodega_id':

					$filtro = array_replace_recursive($filtro, array(
						'conditions' => array(
							'OrdenCompra.bodega_id' => $valor
						)
					));

					break;
				case 'ret':

					$filtro = array_replace_recursive($filtro, array(
						'conditions' => array(
							'OrdenCompra.retiro' => ($valor == 'si') ? 1 : 0
						)
					));

					break;
				case 'dtf':

					$filtro = array_replace_recursive($filtro, array(
						'conditions' => array('OrdenCompra.created >=' => trim($valor))
					));

					break;
				case 'dtt':

					$filtro = array_replace_recursive($filtro, array(
						'conditions' => array('OrdenCompra.created <=' => trim($valor))
					));

					break;
			}
		}
	}


	private function paginacion_index($estado = array())
	{
		$qry = array(
			'recursive'			=> -1,
			'contain' => array(
				'Administrador' => array(
					'fields' => array(
						'Administrador.nombre'
					)
				),
				'Proveedor' => array(
					'fields' => array(
						'Proveedor.nombre'
					)
				),
				'Tienda' => array(
					'fields' => array(
						'Tienda.nombre'
					)
				),
				'Bodega' => array(
					'fields' => array(
						'Bodega.nombre'
					)
				)
			),
			'conditions' => array(
				'OrdenCompra.proveedor_id !=' => '',
				'OrdenCompra.bodega_id' => Hash::extract($this->Auth->user('Bodega'), '{n}.id')
			),
			'fields' => array(
				'OrdenCompra.id',
				'OrdenCompra.estado',
				'OrdenCompra.created',
				'OrdenCompra.tienda_id',
				'OrdenCompra.parent_id',
				'OrdenCompra.administrador_id',
				'OrdenCompra.email_finanza',
				'OrdenCompra.oc_manual',
				'OrdenCompra.retiro',
				'OrdenCompra.bodega_id'
			),
			'order' => array(
				'OrdenCompra.id' => 'DESC'
			),
			'limit' => 20
		);

		if (!empty($estado)) {
			$qry['conditions']['OrdenCompra.estado'] = $estado;
		}

		return $qry;
	}


	public function admin_index()
	{
		$paginate = $this->paginacion_index();

		$titulo_index = '<i class="fa fa-list"></i> Todas las Órdenes de Compra';

		# Filtrar
		if (isset($this->request->params['named'])) {
			$this->reemplazar_filtro_recursivamente($paginate);

			if (isset($this->request->params['named']['sta'])) {
				$titulo_index = sprintf('<i class="fa %s"></i> Órdenes de compra %s', $this->OrdenCompra->estadosColor[$this->request->params['named']['sta']]['ico'], $this->OrdenCompra->estados[$this->request->params['named']['sta']]);
			}
		}

		if ($this->request->is('post')) {
			$this->filtrar('ordenCompras');
		}

		$this->paginate = $paginate;

		$ordenCompras	= $this->paginate();

		BreadcrumbComponent::add('Ordenes de compra ');

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array(
			'order' => array(
				'Proveedor.nombre'
			)
		));

		$bodegas = [];

		foreach ($this->Auth->user('Bodega') as $b) {
			$bodegas[$b['id']] = $b['nombre'];
		}

		$this->set(compact('ordenCompras', 'estados', 'proveedores', 'titulo_index', 'bodegas'));
	}


	/**
	 * Muetsra por proveedor, la cantidad de OC en sus respectivos estados.
	 * @return [type] [description]
	 */
	public function admin_resumen()
	{
		$qry = array(
			'conditions' => array(
				'OrdenCompra.proveedor_id !=' => '',
			),
			'fields' => array(
				'OrdenCompra.id',
				'OrdenCompra.estado',
				'OrdenCompra.proveedor_id'
			)
		);

		$estados = $this->OrdenCompra->estados;
		$proveedores = $this->OrdenCompra->Proveedor->find('list');

		$ocs = $this->OrdenCompra->find('all', $qry);

		$matriz = array();

		foreach ($proveedores as $idp => $p) {

			$matriz['proveedor'][$idp]['nombre'] = $p;

			$ocsProveedor = Hash::extract($ocs, '{n}.OrdenCompra[proveedor_id=' . $idp . ']');

			foreach ($estados as $slug => $e) {

				$matriz['proveedor'][$idp]['total'][$slug] = 0;

				foreach ($ocsProveedor as $iocp => $oc) {
					if ($oc['estado'] == $slug) {
						$matriz['proveedor'][$idp]['total'][$slug] = $matriz['proveedor'][$idp]['total'][$slug] + 1;
					}
				}
			}
		}

		BreadcrumbComponent::add('Ordenes de compra', '/ordenCompras');
		BreadcrumbComponent::add('Resumen');

		$this->set(compact('matriz', 'estados'));
	}


	/**
	 * Para finalizar una OC como recibida debe indicarse el/las facturas
	 * que respaldan los porductos ingresados.
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_reception($id)
	{
		if (!$this->OrdenCompra->exists($id)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index', 'sta' => 'espera_recepcion'));
		}

		$productosActualizado   = array();
		$productosNoActualizado = array();

		$res                    = array(
			'incompletos'           => array(),
			'completos'             => array()
		);

		if ($this->request->is('post') || $this->request->is('put')) {

			ini_set('max_execution_time', 0);

			# Se debe inngresar un Documeto para hacer la recepcón
			if (!isset($this->request->data['OrdenCompraFactura']) || empty($this->request->data['OrdenCompraFactura'])) {
				$this->Session->setFlash('No ha asignado pagos a esta OC.', null, array(), 'danger');
				$this->redirect(array('action' => 'reception', $id));
			}

			foreach ($this->request->data['VentaDetalleProducto'] as $key => $producto) {

				# Calcula la cantidad  de productos que faltan por recibir.
				$cantidadFaltante      = $producto['cantidad_validada_proveedor'] - $producto['cantidad_recibida'];
				$cantidadRecibidaAhora = $producto['cantidad_recibida_ahora'];
				$bodegaDestino         = $producto['bodega_id'];

				if ($cantidadFaltante == 0) {
					continue;
				}

				if ($cantidadFaltante == $cantidadRecibidaAhora) {
					$res['completos'][] = sprintf('#%s - %s (agregados: %d)', $producto['id'], $producto['descripcion'], $cantidadRecibidaAhora);
				}

				if ($cantidadFaltante > $cantidadRecibidaAhora) {
					$res['incompletos'][] = sprintf('#%s - %s (agregados: %d - faltantes: %d)', $producto['id'], $producto['descripcion'], $cantidadRecibidaAhora, ($cantidadFaltante - $cantidadRecibidaAhora));
				}

				ClassRegistry::init('OrdenComprasVentaDetalleProducto')->id = $producto['id_ocp'];
				ClassRegistry::init('OrdenComprasVentaDetalleProducto')->saveField('cantidad_recibida', ($cantidadRecibidaAhora + $producto['cantidad_recibida'])); # Actualiamos la cantidad recibida

				# Se crea la entrada de productos
				$precioCompra = round($producto['total_neto'] / $producto['cantidad_validada_proveedor'], 2);

				if (ClassRegistry::init('Bodega')->crearEntradaBodega($producto['id'], $bodegaDestino, $cantidadRecibidaAhora, $precioCompra, 'OC', $id)) {
					$productosActualizado[] = $producto['id'];
				} else {
					$productosNoActualizado[] = $producto['id'];
				}
			}


			$this->OrdenCompra->id = $id;
			$oc_manual             = $this->OrdenCompra->field('oc_manual');

			# Reservamos los productos de las ventas relacionadas a la OC padre
			if (!$oc_manual) {
				// ! Se mueve metodo al controllador
				// ! Queda en desuzo el metodo en el Modelo
				// ClassRegistry::init('Venta')->reservar_stock_por_oc($id);
				$this->reservar_stock_por_oc($id);
			} else {

				# Reservamos las ventas mas antiguas
				$ventasSinReserva = ClassRegistry::init('Venta')->obtener_ventas_sin_reserva();

				foreach ($ventasSinReserva as $venta) {
					foreach ($venta['VentaDetalle'] as $detalle) {

						if (ClassRegistry::init('Venta')->reservar_stock_producto($detalle['id']) > 0) {
							// * Se sigue misma logica de instanciar metodo que hay en metodo "reservar_stock_producto"
							$this->WarehouseNodriza->procesar_embalajes($detalle['venta_id']);
						}
					}
				}
			}

			if (!empty($res['completos'])) {
				$this->Session->setFlash($this->crearAlertaUl($res['completos'], 'Completos'), null, array(), 'success');
			}

			if (!empty($res['incompletos'])) {
				$this->Session->setFlash($this->crearAlertaUl($res['incompletos'], 'Agregados'), null, array(), 'warning');
			}

			if (empty($res['incompletos']) && empty($res['completos'])) {
				$this->Session->setFlash('La OC #' . $id . ' ya fue procesada.', null, array(), 'success');
			}

			$ocSave = array(
				'OrdenCompra' => array(
					'id' => $id,
					'estado' => 'recepcion_completa',
					'retiro' => 0
				)
			);

			# Guardamos la fecha de la primera recepción
			if (empty($this->OrdenCompra->field('fecha_recibido'))) {
				$ocSave = array_replace_recursive($ocSave, array(
					'OrdenCompra' => array(
						'fecha_recibido' => date('Y-m-d H:i:s')
					)
				));
			}

			if (!empty($res['incompletos'])) {
				$ocSave = array_replace_recursive($ocSave, array(
					'OrdenCompra' => array(
						'estado' => 'recepcion_incompleta'
					)
				));
			}

			$folios = array();

			foreach ($this->request->data['OrdenCompraFactura'] as $iocf => $ocf) {

				# si no viene con folio no se procesa
				if (empty($ocf['folio']))
					continue;

				# si ya es una factura creada, no se procesa
				if (isset($ocf['id']))
					continue;

				# si la factura ya existe, no se procesa
				if (ClassRegistry::init('OrdenCompraFactura')->find_by_invoice($ocf['folio'], $ocf['proveedor_id'])) {
					# No se guarda
					unset($this->request->data['OrdenCompraFactura'][$iocf]);
					continue;
				}

				# Se obtiene el dTE desde el sii y se verifican los datos
				$emisor   = $this->rutSinDv($this->request->data['OrdenCompra']['rut_proveedor']);
				$tipo_dte = $ocf['tipo_documento']; // Facturas
				$folio    = $ocf['folio'];
				$receptor = $this->rutSinDv($this->request->data['OrdenCompra']['rut_tienda']);

				if (empty($ocf['id'])) {
					# Creamos el id antes de setear sus valores
					$id_factura = ClassRegistry::init('OrdenCompraFactura')->crear(array(
						'OrdenCompraFactura' => array(
							'orden_compra_id' => $id,
							'proveedor_id'    => $ocf['proveedor_id']
						)
					));
				} else {
					$id_factura = $ocf['id'];
				}

				$this->request->data['OrdenCompraFactura'][$iocf]['id']              = $id_factura; // seteamos el id de la factura para crear el saldo
				$this->request->data['OrdenCompraFactura'][$iocf]['monto_facturado'] = $ocf['monto_facturado'];
				$this->request->data['OrdenCompraFactura'][$iocf]['proveedor_id']    = $ocf['proveedor_id'];
				$this->request->data['OrdenCompraFactura'][$iocf]['emisor']          = $emisor;
				$this->request->data['OrdenCompraFactura'][$iocf]['receptor']        = $receptor;

				# Es factura
				if ($tipo_dte == 33) {
					# Descontamos el saldo usado solo al crearla
					if (!isset($ocf['id']))
						ClassRegistry::init('Saldo')->descontar($ocf['proveedor_id'], $id, $id_factura, null, $this->request->data['OrdenCompraFactura'][$iocf]['monto_facturado']);
				}
			}

			$total_oc = $this->OrdenCompra->field('total');

			# OC queda en estado de espera de factura
			if ($ocSave['OrdenCompra']['estado'] == 'recepcion_completa' && count(Hash::extract($this->request->data, 'OrdenCompraFactura.{n}[tipo_documento=33]')) == 0) {
				$ocSave['OrdenCompra']['estado'] = 'espera_dte';
			} elseif ($ocSave['OrdenCompra']['estado'] == 'recepcion_completa' && array_sum(Hash::extract($this->request->data, 'OrdenCompraFactura.{n}[tipo_documento=33].monto_facturado')) < $total_oc) {
				$ocSave['OrdenCompra']['estado'] = 'espera_dte';
			}

			$ocSave = array_replace_recursive($ocSave, array(
				'OrdenCompraFactura' => $this->request->data['OrdenCompraFactura']
			));

			$ocSave['OrdenCompraHistorico'] = array(
				array(
					'estado' => $ocSave['OrdenCompra']['estado'],
					'responsable' => $this->Auth->user('email'),
					'evidencia' => json_encode($ocSave)
				)
			);

			# Al guardar relacionamos todas las facturas a los pagos que existan para ésta OC
			if ($this->OrdenCompra->saveAll($ocSave)) {

				# Pagos relacionados
				$pagos = ClassRegistry::init('Pago')->find('all', array(
					'conditions' => array(
						'Pago.orden_compra_id' => $id,
					),
					'fields' => array(
						'Pago.id', 'Pago.pagado'
					)
				));

				# Facturas recien creadas
				$facturas = ClassRegistry::init('OrdenCompraFactura')->find('all', array(
					'conditions' => array(
						'OrdenCompraFactura.orden_compra_id' => $id,
						'OrdenCompraFactura.tipo_documento' => 33 // Fatura
					),
					'contain' => array(
						'Pago' => array(
							'fields' => array(
								'Pago.id'
							)
						)
					),
					'fields' => array(
						'OrdenCompraFactura.id'
					),
				));

				# Relacionamos pagos facturas
				foreach ($pagos as $ip => $p) {
					foreach ($facturas as $if => $f) {

						# si tiene pago/s relaconados continua el ciclo
						foreach ($f['Pago'] as $ifp => $fp) {
							if ($fp['id'] == $p['Pago']['id']) {
								continue;
							}
						}

						$pagos[$ip]['OrdenCompraFactura'][$if] = array(
							'factura_id' => $f['OrdenCompraFactura']['id']
						);
					}
				}

				# Guardamos para que valide los pagos y faturas
				if (!empty($pagos)) {
					ClassRegistry::init('Pago')->saveMany($pagos, array('deep' => true));

					# Notificamos los pagos si corresponde
					$pagosController = new PagosController;

					foreach ($pagos as $ip => $p) {
						$pagosController->guardarEmailPagoFactura($p['Pago']['id']);
					}
				}
			}


			if (!empty($folios)) {
				$this->Session->setFlash($this->crearAlertaUl($folios, 'Errores'), null, array(), 'warning');
				$this->redirect(array('action' => 'reception', $id));
			}

			$this->redirect(array('action' => 'index', 'sta' => 'espera_recepcion'));
		}

		$this->request->data = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'VentaDetalleProducto' => array(
					'Bodega' => array(
						'fields' => array(
							'Bodega.id'
						)
					),
					'fields' => array(
						'VentaDetalleProducto.id'
					)
				),
				'Tienda' => array(
					'fields' => array(
						'Tienda.rut'
					)
				),
				'Proveedor' => array(
					'fields' => array(
						'Proveedor.rut_empresa', 'Proveedor.nombre'
					)
				),
				'OrdenCompraFactura' => array(
					'fields' => array(
						'OrdenCompraFactura.id', 'OrdenCompraFactura.tipo_documento', 'OrdenCompraFactura.folio', 'OrdenCompraFactura.pagada', 'OrdenCompraFactura.nota', 'OrdenCompraFactura.monto_facturado'
					)
				)
			),
			'fields' => array(
				'OrdenCompra.id', 'OrdenCompra.estado', 'OrdenCompra.tienda_id', 'OrdenCompra.proveedor_id', 'OrdenCompra.total_neto', 'OrdenCompra.iva', 'OrdenCompra.descuento_monto', 'OrdenCompra.total', 'OrdenCompra.moneda_id'
			)
		));

		$bodegas = [];

		foreach ($this->Auth->user('Bodega') as $b) {
			$bodegas[$b['id']] = $b['nombre'];
		}

		$url_retorno = Router::url($this->referer(), true);

		# Array de tipos de documentos
		$libreDte = $this->Components->load('LibreDte');
		$tipo_documento = array(
			33 => 'Factura electrónica',
			52 => 'Guia de despacho electrónica',
			50 => 'Guia de despacho manual'
		);

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras/index/sta:' . $this->request->data['OrdenCompra']['estado']);
		BreadcrumbComponent::add('Recepción OC');

		$this->set(compact('bodegas', 'url_retorno', 'tipo_documento'));
	}


	/**
	 * [admin_validateReception description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_validateReception($id)
	{
		$res = array(
			'faltantes' => array(),
			'completos' => array()
		);

		if ($this->request->is('put')) {

			foreach ($this->request->data['OrdenCompra'] as $key => $value) {

				$pedido = ClassRegistry::init('OrdenComprasVentaDetalleProducto')->find('first', array(
					'conditions' => array(
						'orden_compra_id'           => $id,
						'venta_detalle_producto_id' => $value['VentaDetalleProducto']['id']
					)
				));

				if ($value['Bodega'][0]['cantidad'] < $pedido['OrdenComprasVentaDetalleProducto']['cantidad']) {
					$res['faltantes'][] = array(
						'producto_id'     => $value['VentaDetalleProducto']['id'],
						'producto_nombre' => $pedido['OrdenComprasVentaDetalleProducto']['descripcion'],
						'cantidad'        => $pedido['OrdenComprasVentaDetalleProducto']['cantidad'] - $value['Bodega'][0]['cantidad']
					);
				}

				if ($value['Bodega'][0]['cantidad'] == $pedido['OrdenComprasVentaDetalleProducto']['cantidad']) {
					$res['completos'][] = array(
						'producto_id'     => $value['VentaDetalleProducto']['id'],
						'producto_nombre' => $pedido['OrdenComprasVentaDetalleProducto']['descripcion'],
						'cantidad'        => $pedido['OrdenComprasVentaDetalleProducto']['cantidad'] - $value['Bodega'][0]['cantidad']
					);
				}
			}
		}

		echo json_encode($res);
		exit;
	}

	/**
	 * [admin_view description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_view($id)
	{
		if (!$this->OrdenCompra->exists($id)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->OrdenCompra->save($this->request->data)) {
				$this->Session->setFlash('Se ha cambiado bodega', null, array(), 'success');
				$this->redirect(array('controller' => 'ordenCompras', 'action' => 'index'));
			} else {
				$this->Session->setFlash('se ha podido cambiar bodega, intente nuevamente', null, array(), 'warning');
			}
		}

		$oc = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'Moneda',
				'VentaDetalleProducto',
				'Administrador',
				'Tienda',
				'Proveedor',
				'OrdenCompraFactura',
				'OrdenCompraPago',
				'OrdenCompraHistorico'
			)
		));

		$bodegas = [];

		foreach ($this->Auth->user('Bodega') as $b) {
			$bodegas[$b['id']] = $b['nombre'];
		}

		BreadcrumbComponent::add('Ordenes de compra ', array('action' => 'index'));
		BreadcrumbComponent::add('Ver OC ');

		$estados = ["espera_dte", "recepcion_incompleta", "recepcion_completa"];

		$this->set(compact('oc', 'bodegas', 'estados'));
	}


	/**
	 * [admin_generar_pdf description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_generar_pdf($id)
	{

		if (!$this->OrdenCompra->exists($id)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		$ocs = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'Moneda',
				'VentaDetalleProducto',
				'Administrador',
				'Tienda',
				'Proveedor' => array(
					'Moneda'
				),
				'Bodega'
			)
		));

		$nombreOC = 'orden_compra_' . $ocs['OrdenCompra']['id'] . '_' . Inflector::slug($ocs['Proveedor']['nombre']) . '_' . rand(1, 100) . '.pdf';

		$this->generar_pdf($ocs, $nombreOC);

		$this->OrdenCompra->id = $id;

		if ($this->OrdenCompra->saveField('pdf', $nombreOC)) {
			$this->Session->setFlash('OC generada en PDF con éxito.', null, array(), 'success');
		} else {
			$this->Session->setFlash('No fue posible generar el PDF.', null, array(), 'danger');
		}

		$this->redirect(array('action' => 'view', $id));
	}


	/**
	 * Envia la OC a los destinatarios correspondientes que se configuraron en Proveedores
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_ready($id)
	{
		if (!$this->OrdenCompra->exists($id)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index_pagadas'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$ocs = $this->OrdenCompra->find('first', array(
				'conditions' => array(
					'OrdenCompra.id' => $id
				),
				'contain' => array(
					'Moneda',
					'VentaDetalleProducto',
					'Administrador',
					'Tienda',
					'Proveedor' => array(
						'Moneda'
					),
					'Bodega'
				)
			));

			# si no se ha gnerado se intenta generar nuevamente
			if (empty($this->request->data['OrdenCompra']['pdf'])) {

				$nombreOC = 'orden_compra_' . $ocs['OrdenCompra']['id'] . '_' . Inflector::slug($ocs['Proveedor']['nombre']) . '_' . rand(1, 100) . '.pdf';

				$this->generar_pdf($ocs, $nombreOC);

				$this->OrdenCompra->id = $id;
				$this->OrdenCompra->saveField('pdf', $nombreOC);
			}


			$rutaArchivos = array(
				sprintf('order_compra_%d.pdf', rand(1000, 100000)) => array(
					'file' => APP . 'webroot' . DS . 'Pdf' . DS . 'OrdenCompra' . DS . $id . DS . $this->request->data['OrdenCompra']['pdf'],
					#'mimetype' => $this->getFileMimeType(APP . 'webroot' . DS . 'Pdf' . DS . 'OrdenCompra' . DS . $id . DS . $this->request->data['OrdenCompra']['pdf']),
				)
			);

			if (!empty($this->request->data['OrdenCompra']['adjunto'])) {

				$ext = pathinfo(APP . 'webroot' . DS . 'img' . DS . str_replace('/', DS, $this->request->data['OrdenCompra']['adjunto']), PATHINFO_EXTENSION);

				$rutaArchivos[sprintf('adjunto_%d.%s', rand(1000, 100000), $ext)] = array(
					'file' => APP . 'webroot' . DS . 'img' . DS . str_replace('/', DS, $this->request->data['OrdenCompra']['adjunto']),
					#'mimetype' => $this->getFileMimeType(APP . 'webroot' . DS . 'img' . DS . str_replace('/', DS, $this->request->data['OrdenCompra']['adjunto'])),
				);
			}

			$mensaje = $this->request->data['OrdenCompra']['mensaje_final'];

			$to  = Hash::extract($this->request->data, 'email_contacto_empresa.{n}[tipo=destinatario].email');
			$cc  = Hash::extract($this->request->data, 'email_contacto_empresa.{n}[tipo=copia].email');
			$bcc = Hash::extract($this->request->data, 'email_contacto_empresa.{n}[tipo=copia oculta].email');

			App::uses('CakeEmail', 'Network/Email');

			$this->Email = new CakeEmail();
			$this->Email
				#->config('gmail')
				->viewVars(compact('mensaje'))
				->emailFormat('html')
				->from(array($this->Session->read('Auth.Administrador.email') => 'Nodriza Spa'))
				->replyTo(array($ocs['Administrador']['email'] => $ocs['Administrador']['nombre']))
				->to($to)
				->cc($cc)
				->bcc($bcc)
				->template('oc_proveedor')
				->attachments($rutaArchivos)
				->subject(sprintf('[OC] #%d Se ha creado una Orden de compra desde Nodriza Spa', $id));


			# Cambiar estado OC a enviado
			$this->OrdenCompra->id = $id;
			$this->OrdenCompra->saveField('fecha_enviado', date('Y-m-d H:i:s'));
			$this->OrdenCompra->saveField('estado', 'enviado');

			if ($this->Email->send()) {
				$this->Session->setFlash('Email y adjuntos enviados con éxito', null, array(), 'success');
				$this->redirect(array('action' => 'index_pagadas'));
			} else {
				$this->Session->setFlash('Ocurrió un error al enviar el email. Intente nuevamente.', null, array(), 'danger');
				$this->redirect(array('action' => 'index_pagadas'));
			}
		}


		$this->request->data = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'Moneda',
				'VentaDetalleProducto',
				'Administrador',
				'Tienda',
				'Proveedor'
			)
		));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Ver OC ');
	}


	/**
	 * Revisar una OC y modificarla si corresponde para luego ser enviada a pagar
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_review($id)
	{
		if (!$this->OrdenCompra->exists($id)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index', 'sta' => 'validacion_comercial'));
		}


		$ocs = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'Moneda' => array(
					'fields' => array(
						'Moneda.nombre'
					)
				),
				'VentaDetalleProducto' => array(
					'Marca' => array(
						'PrecioEspecificoMarca' => array(
							'conditions' => array(
								'PrecioEspecificoMarca.activo' => 1,
								'OR' => array(
									'PrecioEspecificoMarca.descuento_infinito' => 1,
									'AND' => array(
										array('PrecioEspecificoMarca.fecha_inicio <=' => date('Y-m-d')),
										array('PrecioEspecificoMarca.fecha_termino >=' => date('Y-m-d')),
									)
								)
							),
							'order' => array(
								'PrecioEspecificoMarca.id' => 'DESC'
							)
						)
					),
					'PrecioEspecificoProducto' => array(
						'conditions' => array(
							'PrecioEspecificoProducto.activo' => 1,
							'OR' => array(
								'PrecioEspecificoProducto.descuento_infinito' => 1,
								'AND' => array(
									array('PrecioEspecificoProducto.fecha_inicio <=' => date('Y-m-d')),
									array('PrecioEspecificoProducto.fecha_termino >=' => date('Y-m-d')),
								)
							)
						),
						'order' => array(
							'PrecioEspecificoProducto.id' => 'DESC'
						)
					)
				),
				'Administrador' => array(
					'fields' => array(
						'Administrador.nombre', 'Administrador.email'
					)
				),
				'Proveedor' => array(
					'fields' => array(
						'Proveedor.nombre'
					)
				)
			),
			'fields' => array(
				'OrdenCompra.id', 'OrdenCompra.rut_empresa', 'OrdenCompra.razon_social_empresa', 'OrdenCompra.giro_empresa', 'OrdenCompra.nombre_contacto_empresa', 'OrdenCompra.email_contacto_empresa', 'OrdenCompra.fono_contacto_empresa', 'OrdenCompra.direccion_comercial_empresa', 'OrdenCompra.fecha', 'OrdenCompra.vendedor', 'OrdenCompra.descuento', 'OrdenCompra.validado_proveedor', 'OrdenCompra.total_neto', 'OrdenCompra.iva', 'OrdenCompra.total', 'OrdenCompra.tienda_id', 'OrdenCompra.administrador_id'
			)
		));

		# Calculo de descuentos
		foreach ($ocs['VentaDetalleProducto'] as $i => $p) {

			$descuentos = ClassRegistry::init('VentaDetalleProducto')::obtener_descuento_por_producto($p);

			$ocs['VentaDetalleProducto'][$i]['total_descuento']  = $descuentos['total_descuento'];
			$ocs['VentaDetalleProducto'][$i]['nombre_descuento'] = $descuentos['nombre_descuento'];
			$ocs['VentaDetalleProducto'][$i]['valor_descuento']  = $descuentos['valor_descuento'];
		}


		if ($this->request->is('post') || $this->request->is('put')) {

			if (isset($this->request->data['OrdenCompra']['estado'])) {


				$this->OrdenCompra->id = $id;
				$this->OrdenCompra->saveField('estado', 'creada'); # Vacio vuelve a bodega
				$this->OrdenCompra->saveField('comentario_validar', $this->request->data['OrdenCompra']['comentario_validar']); # Guarda comentario

				$emails = array($ocs['Administrador']['email']);

				$this->guardarEmailRechazo($id, $emails);
			} else {

				$this->request->data['OrdenCompra']['estado']             = 'asignacion_metodo_pago'; # Pasa a finanzas
				$this->request->data['OrdenCompra']['nombre_validado']    = $this->Session->read('Auth.Administrador.nombre'); # Guardamos el nombre de quien validó la OC
				$this->request->data['OrdenCompra']['email_comercial']    = $this->Session->read('Auth.Administrador.email'); # Guardamos el email de quien validó la OC
				$this->request->data['OrdenCompra']['validado_proveedor'] = 0;

				$emails = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('pagar_oc');

				$this->request->data['OrdenCompraHistorico'] = array(
					array(
						'estado' => 'validacion_comercial',
						'responsable' => $this->Auth->user('email'),
						'evidencia' => json_encode($this->request->data)
					)
				);

				if ($this->OrdenCompra->saveAll($this->request->data) && $this->guardarEmailAsignarPago($ocs, $emails)) {
					$this->Session->setFlash('Estado actualizado con éxito.', null, array(), 'success');
				}
			}

			$this->redirect(array('action' => 'index', 'sta' => 'validacion_comercial'));
		}

		if ($ocs['OrdenCompra']['validado_proveedor']) {
			$this->Session->setFlash('Esta OC fue reiniciada por el proveedor.', null, array(), 'success');
		}

		$estados_proveedor = $this->OrdenCompra->estado_proveedor;

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Revisar OC ');

		$this->set(compact('ocs', 'estados_proveedor'));
	}


	public function admin_notificar_proveedor($id)
	{

		if ($this->guardarEmailValidado($id)) {
			$this->Session->setFlash('Notificado con éxito.', null, array(), 'success');
			$this->redirect($this->referer('/', true));
		} else {
			$this->Session->setFlash('No fue posible notificar al proveedor.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}
	}


	public function admin_asignar_moneda($id)
	{
		if (!$this->OrdenCompra->exists($id)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		if ($this->request->is('post') || $this->request->is('put')) {


			$this->request->data['OrdenCompraHistorico'] = array(
				array(
					'estado' => 'asignacion_metodo_pago',
					'responsable' => $this->Auth->user('email'),
					'evidencia' => json_encode($this->request->data)
				)
			);

			if ($this->OrdenCompra->saveAll($this->request->data)) {
				
				$this->Session->setFlash('Método de pago asignado con éxito.', null, array(), 'success');

				# Obtenemos el id prpoveedor de la OC
				$this->OrdenCompra->id = $id;
				$oc_proveedor = $this->OrdenCompra->field('proveedor_id');
				
				# si no tiene activa la api oc se notifica vía email
				if (!ClassRegistry::init('Proveedor')->permite_api_oc($oc_proveedor) && $this->guardarEmailValidado($id))
				{
					$this->Session->setFlash('Notificado con éxito.', null, array(), 'success');
				}

				$this->redirect(array('action' => 'index', 'sta' => 'asignacion_metodo_pago'));
			} else {
				$this->Session->setFlash('Ocurrió un error al asignar el método de pago o no fue posible enviar el email al proveedor.', null, array(), 'danger');
				$this->redirect(array('action' => 'asignar_moneda', $id));
			}
		}

		$this->request->data = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'Moneda' => array(
					'fields' => array('Moneda.id', 'Moneda.nombre')
				),
				'Proveedor' => array(
					'fields' => array(
						'Proveedor.id', 'Proveedor.nombre', 'Proveedor.rut_empresa'
					)
				)
			),
			'fields' => array(
				'OrdenCompra.id', 'OrdenCompra.descuento', 'OrdenCompra.descuento_monto', 'OrdenCompra.total'
			)
		));

		$monedas = ClassRegistry::init('Moneda')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Asignar metodo de pago ');

		$this->set(compact('monedas'));
	}


	/**
	 * Permite modificar los datos de la OC antes de enviarla a revisión
	 * Reune y categoriza los productos que se encuentran en las ventas
	 * para luego crear una OC por cada Proveedor..
	 *
	 * @return [type]     [description]
	 */
	public function admin_validate()
	{
		if (empty($this->request->query['Venta'])) {
			$this->Session->setFlash('Debe seleccionar una venta para continuar', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			foreach ($this->request->data['OrdenesCompra'] as $ic => $d) {

				if (!isset($d['VentaDetalleProducto'])) {
					continue;
				}

				$d['OrdenCompraHistorico'] = array(
					array(
						'estado' => 'creada',
						'responsable' => $this->Auth->user('email'),
						'evidencia' => json_encode($d)
					)
				);

				$ventas = Hash::extract($d['Venta'], '{n}.venta_id');

				# Tomamos la bodega de la primera venta. Al permitir solo OC de ventas de una bodega en especifica, 
				# todas las ventas de este request pertenecen a la misma bodega
				$bodega_id = ClassRegistry::init('Venta')->field('bodega_id', array('id' => $ventas[0]));

				$d['OrdenCompra']['bodega_id'] = ($bodega_id) ? $bodega_id : $this->Session->read('Auth.Administrador.Rol.bodega_id');

				$d['OrdenCompra']['tipo_orden'] = "en_verde";

				$d['Venta'] = unique_multidim_array($d['Venta'], 'venta_id');

				if (!$this->OrdenCompra->saveAll($d, array('deep' => true))) {
					$this->Session->setFlash('Ocurrió un error al guardar la OC. Verifique la información.', null, array(), 'danger');
					$this->redirect(array('action' => 'validate', 'Venta' => $this->request->query['Venta']));
				}
			}

			$emailsNotificar = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('revision_oc');

			if (!empty($emailsNotificar)) {
				$this->guardarEmailRevision($this->request->data['OrdenesCompra'][0], $emailsNotificar);
			}

			$this->Session->setFlash('¡Éxito! Se ha enviado a revisión la OC.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}

		# Obtenemos las ventas seleccionadas
		$venta_detalles = ClassRegistry::init('VentaDetalle')->find('all', array(
			'conditions' => array(
				'VentaDetalle.venta_id' => Hash::extract($this->request->query['Venta'], '{n}.venta_id')
			),
			'fields' => array(
				'VentaDetalle.cantidad', 'VentaDetalle.venta_detalle_producto_id', 'VentaDetalle.venta_id'
			),
			'contain' => [
				'Venta' => ['fields' => ['Venta.bodega_id']],
				'VentaDetallesReserva' => [
					'fields' => [
						'VentaDetallesReserva.venta_detalle_id',
						'VentaDetallesReserva.venta_detalle_producto_id',
						'VentaDetallesReserva.cantidad_reservada',
						'VentaDetallesReserva.bodega_id',
					]
				]
			]
		));

		$productosSolicitar = array();
		$productosNoSolicitar = array();
		$productosTotales   = array();
		# Se calculan los totales de productos vendidos
		foreach ($venta_detalles as $iv => $venta) {
			$bodega_id[$venta['VentaDetalle']['venta_detalle_producto_id']] = $venta['Venta']['bodega_id'];
			$cantidad  = $venta['VentaDetalle']['cantidad'] - array_sum(Hash::extract($venta['VentaDetallesReserva'], "{n}.cantidad_reservada")); // Se descuenta la cantidad ya reservada

			if ($cantidad === 0) {
				continue;
			}

			if (array_key_exists($venta['VentaDetalle']['venta_detalle_producto_id'], $productosTotales)) {
				$productosTotales[$venta['VentaDetalle']['venta_detalle_producto_id']] = $productosTotales[$venta['VentaDetalle']['venta_detalle_producto_id']] + $cantidad;
			} else {
				$productosTotales[$venta['VentaDetalle']['venta_detalle_producto_id']] = $cantidad;
			}
		}
		# comprobamos el stock en bodegas para saber cuales productos se deben solicitar por OC
		foreach ($productosTotales as $ip => $p) {

			$pedir = $p;

			# Consultamos la cantiad que tenemos en la bodega principal
			$enBodega = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($ip, $bodega_id[$ip] ?? null);
			# Calculamos la diferencia que se debe pedir segun lo que tenemos en bodega
			if ($enBodega >= $p) {
				$pedir = 0;
			} else {
				$pedir = $pedir - $enBodega;
			}

			# Definimos lo que tenemos en bodega y lo que no
			if ($pedir === 0) {
				$productosNoSolicitar[$ip]['id'] = $ip;
				$productosNoSolicitar[$ip]['cantidad_bodega'] = $enBodega;
			} else {
				$productosSolicitar[$ip]['id'] = $ip;
				$productosSolicitar[$ip]['cantidad_oc'] = $pedir;
			}
		}
		# Si no hay producto que pedir se cancela el paso
		if (empty($productosSolicitar)) {
			$this->Session->setFlash('No hay productos que agregar a la OC.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		# Ordenamos los productos que se deben solicitar por proveedor
		$productos = ClassRegistry::init('VentaDetalleProducto')->find('all', array(
			'conditions' => array(
				'VentaDetalleProducto.id' => Hash::extract($productosSolicitar, '{n}.id')
			),
			'contain' => array(
				'Proveedor' => array(
					'fields' => array(
						'Proveedor.id'
					)
				),
				'Marca' => array(
					'fields' => array(
						'Marca.id'
					)
				)
			)
		));


		$productosIncompletos = array();
		# Verificamos que todos los productos solicitados tengan proveedor y marca asociado
		foreach ($productos as $ip => $p) {
			if (empty($p['Proveedor']) || empty($p['Marca'])) {
				$productosIncompletos[$ip] = $p;
			}
		}

		# Alertamos que hay productos ins proveedor
		if (!empty($productosIncompletos)) {
			$this->Session->setFlash(sprintf('Existen %d producto/s sin proveedor y/o marca asignado.', count($productosIncompletos)), null, array(), 'danger');
		}

		# Obtenemos solo los proveedores que necesitamos
		$proveedores = ClassRegistry::init('Proveedor')->find('all', array(
			'joins' => array(
				array(
					'table' => 'proveedores_venta_detalle_productos',
					'alias' => 'ProveedoresVentaDetalleProducto',
					'type'  => 'inner',
					'conditions' => array(
						'ProveedoresVentaDetalleProducto.proveedor_id = Proveedor.id',
						'ProveedoresVentaDetalleProducto.venta_detalle_producto_id IN(' . implode(',', Hash::extract($productosSolicitar, '{n}.id')) . ')'
					)
				)
			),
			'contain' => array(
				'VentaDetalleProducto' => array(
					'conditions' => array(
						'VentaDetalleProducto.id' => Hash::extract($productosSolicitar, '{n}.id')
					)
				)
			),
			'group' => array('Proveedor.id')
		));

		# $proveedores = array_map("unserialize", array_unique(array_map("serialize", $proveedores)));

		$tipoDescuento    = array(0 => '$', 1 => '%');

		$descuentosMarcaCompuestos = array();
		$descuentosMarcaEspecificos = array();

		# Calculo de descuentos
		foreach ($proveedores as $ip => $proveedor) {
			foreach ($proveedor['VentaDetalleProducto'] as $i => $p) {

				$descuentos = ClassRegistry::init('VentaDetalleProducto')::obtener_descuento_por_producto($p);

				$proveedores[$ip]['VentaDetalleProducto'][$i]['total_descuento']  = $descuentos['total_descuento'];
				$proveedores[$ip]['VentaDetalleProducto'][$i]['nombre_descuento'] = $descuentos['nombre_descuento'];
				$proveedores[$ip]['VentaDetalleProducto'][$i]['valor_descuento']  = $descuentos['valor_descuento'];
			}
		}

		$proveedoresLista = ClassRegistry::init('Proveedor')->find('list', array(
			'conditions' => array(
				'Proveedor.activo' => 1
			),
			'order' => array(
				'Proveedor.nombre'
			)
		));
		$marcas 		  = ClassRegistry::init('Marca')->find('list', array(
			'order' => array(
				'Marca.nombre'
			)
		));
		$monedas          = $this->OrdenCompra->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));


		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Revisión ');

		$this->set(compact('venta_detalles', 'monedas', 'productosNoSolicitar', 'productosSolicitar', 'productosIncompletos', 'productos', 'proveedores', 'proveedoresLista', 'tipoDescuento', 'marcas'));
	}


	/**
	 * PErmite pagar una OC y notificar a bodega
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_pay($id = null)
	{
		if (!$this->OrdenCompra->exists($id)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index', 'sta' => 'pago_finanzas'));
		}

		$ocs = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'Proveedor' => array(
					'fields' => array(
						'Proveedor.id', 'Proveedor.nombre', 'Proveedor.rut_empresa', 'Proveedor.meta_emails'
					)
				),
				'Moneda' => array(
					'fields' => array(
						'Moneda.nombre', 'Moneda.tipo'
					)
				)
			),
			'fields' => array(
				'OrdenCompra.id', 'OrdenCompra.moneda_id', 'OrdenCompra.administrador_id', 'OrdenCompra.tienda_id', 'OrdenCompra.nombre_pagado', 'OrdenCompra.estado', 'OrdenCompra.nombre_validado', 'OrdenCompra.descuento', 'OrdenCompra.descuento_monto', 'OrdenCompra.total', 'OrdenCompra.comentario_validar', 'OrdenCompra.proveedor_id'
			)
		));

		if (!empty($ocs['OrdenCompra']['nombre_pagado']) && !isset($this->request->query['update'])) {
			$this->Session->setFlash('La OC #' . $id . ' ya fue pagada por ' . $ocs['OrdenCompra']['nombre_pagado'], null, array(), 'success');
			$this->redirect(array('action' => 'index', 'sta' => 'pago_finanzas'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$data = array(
				'OrdenCompra' => array(
					'id'                 => $id,
					'estado'             => 'espera_recepcion',
					'moneda_id'          => $this->request->data['OrdenCompra']['moneda_id'],
					'nombre_pagado'      => $this->Session->read('Auth.Administrador.nombre'),
					'email_finanza'      => $this->Session->read('Auth.Administrador.email'),
					'comentario_finanza' => $this->request->data['OrdenCompra']['comentario_finanza'],
					'total'              => $this->request->data['OrdenCompra']['total'],
					'descuento_monto'    => round($this->request->data['OrdenCompra']['descuento_monto']),
					'descuento'    	     => round($this->request->data['OrdenCompra']['descuento']),
				),
				'OrdenCompraAdjunto' 	 => (isset($this->request->data['OrdenCompraAdjunto'])) ? $this->request->data['OrdenCompraAdjunto'] : array(),
			);


			$moneda = ClassRegistry::init('Moneda')->find('first', array(
				'conditions' => array(
					'Moneda.id' => $this->request->data['OrdenCompra']['moneda_id']
				)
			));

			if ($moneda['Moneda']['tipo'] == 'pagar' && empty($this->request->data['OrdenCompraAdjunto'])) {
				$this->Session->setFlash('El método de pago utilizado requiere agregar un solo comprobante de pago.', null, array(), 'warning');
				$this->redirect(array('action' => 'pay', $id));
			}

			if (isset($this->request->query['update'])) {
				unset($data['OrdenCompra']['estado']);
			}

			$data['OrdenCompraHistorico'] = array(
				array(
					'estado' => 'pago_finanzas',
					'responsable' => $this->Auth->user('email'),
					'evidencia' => json_encode($data)
				)
			);

			if ($this->OrdenCompra->saveAll($data)) {

				$ocs = $this->OrdenCompra->find('first', array(
					'conditions' => array(
						'OrdenCompra.id' => $id
					),
					'contain' => array(
						'OrdenCompraAdjunto' => array(
							'fields' => array(
								'OrdenCompraAdjunto.adjunto',
								'OrdenCompraAdjunto.incluir_email',
								'OrdenCompraAdjunto.identificador',
								'OrdenCompraAdjunto.id'
							)
						),
						'Moneda',
						'VentaDetalleProducto',
						'Administrador',
						'Venta' => array(
							'VentaDetalle'
						),
						'Tienda',
						'Proveedor',
						'Bodega'
					)
				));


				# Por cada adjunto creado se crea un pago
				foreach ($ocs['OrdenCompraAdjunto'] as $ioca => $oca) {

					switch ($ocs['Moneda']['tipo']) {
						case 'pagar':

							// Se crea un pago al dia 
							ClassRegistry::init('Pago')->crear($oca['identificador'], $id, $oca['id'], date('Y-m-d'), $ocs['OrdenCompra']['total'], array(), $ocs['OrdenCompra']['moneda_id'], $ocs['OrdenCompra']['proveedor_id']);

							break;

						case 'agendar':

							// Se crea un pago sin fecha ni monto (se debe configurar una vez recibida la/las factura/s) 
							ClassRegistry::init('Pago')->crear($oca['identificador'], $id, $oca['id'], null, 0, array(), $ocs['OrdenCompra']['moneda_id'], $ocs['OrdenCompra']['proveedor_id']);
							break;

						case 'esperar':
							// Al moento de recibir la factura se crea y asigna el pago
							break;
					}
				}


				$pdfOc = 'orden_compra_' . $ocs['OrdenCompra']['id'] . '_' . Inflector::slug($ocs['Proveedor']['nombre']) . '_' . rand(1, 100) . '.pdf';

				$this->generar_pdf($ocs, $pdfOc);

				$this->OrdenCompra->id = $id;
				$this->OrdenCompra->saveField('pdf', $pdfOc);
				$this->OrdenCompra->saveField('estado', 'espera_recepcion');
				$this->OrdenCompra->saveField('fecha_enviado', date('Y-m-d H:i:s'));

				# Quitamos el envio de emails
				$this->Session->setFlash('Estado actualizado con éxito.', null, array(), 'success');
				$this->redirect(array('action' => 'index', 'sta' => 'pago_finanzas'));
			} else {
				$this->Session->setFlash('Ocurrió un error al actualizar estado de la OC. Verifique los campos e intente nuevamente', null, array(), 'danger');
				$this->redirect(array('action' => 'pay', $id));
			}
		}

		$monedas = $this->OrdenCompra->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Pagar OC ');

		$this->set(compact('ocs', 'monedas'));
	}


	/**
	 * Agregar una OC´s desde ventas pagadas
	 * @return [type] [description]
	 */
	public function admin_add()
	{
		if ($this->request->is('post')) {

			$this->request->data['OrdenCompra']['bodega_id'] = $this->Session->read('Auth.Administrador.Rol.bodega_id');

			$this->OrdenCompra->create();
			if ($this->OrdenCompra->save($this->request->data)) {
				$current = $this->OrdenCompra->find('first', array(
					'order' => array(
						'OrdenCompra.id' => 'DESC'
					),
					'fields' => array(
						'OrdenCompra.id'
					)
				));

				//$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'validate', $current['OrdenCompra']['id']));
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$monedas = $this->OrdenCompra->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', 'ordenCompras');
		BreadcrumbComponent::add('Agregar ');

		$this->set(compact('monedas'));
	}


	/**
	 * Agregar OC manualmente, seleccionando proveedor y productos
	 * @return [type] [description]
	 */
	public function admin_add_manual()
	{
		if ($this->request->is('post')) {
			$this->request->data['OrdenCompraHistorico'] = array(
				array(
					'estado' => 'creada',
					'responsable' => $this->Auth->user('email'),
					'evidencia' => json_encode($this->request->data)
				)
			);

			$this->request->data['OrdenCompra']['bodega_id'] = $this->request->data['OrdenCompra']['bodega_id'] ?? $this->Session->read('Auth.Administrador.Rol.bodega_id');

			$this->OrdenCompra->create();
			if ($this->OrdenCompra->saveAll($this->request->data)) {

				$emailsNotificar = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('revision_oc');

				if (!empty($emailsNotificar)) {
					$this->guardarEmailRevision($this->request->data, $emailsNotificar);
				}

				$this->Session->setFlash('¡Éxito! Se ha enviado a revisión la OC.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$monedas = $this->OrdenCompra->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));
		$proveedores = $this->OrdenCompra->Proveedor->find('list', array('conditions' => array('Proveedor.activo' => 1), 'order' => array('nombre' => 'ASC')));

		$tipoDescuento    = array(0 => '$', 1 => '%');

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Agregar oc manual');

		$bodegas = [];

		foreach ($this->Auth->user('Bodega') as $b) {
			$bodegas[$b['id']] = $b['nombre'];
		}

		$this->set(compact('monedas', 'proveedores', 'tipoDescuento', 'bodegas'));
	}


	/**
	 * Modificar una OC individualmente
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_editsingle($id = null)
	{
		if (!$this->OrdenCompra->exists($id)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index', 'sta' => 'creada'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			# Limpiar data
			$this->OrdenCompra->OrdenComprasVentaDetalleProducto->deleteAll(array('OrdenComprasVentaDetalleProducto.orden_compra_id' => $id));

			if ($this->OrdenCompra->saveAll($this->request->data)) {

				$emailsNotificar = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('revision_oc');

				if (!empty($emailsNotificar)) {
					$this->guardarEmailRevision($this->request->data['OrdenCompra'], $emailsNotificar);
				}

				$this->Session->setFlash('¡Éxito! Se ha enviado a revisión la OC.', null, array(), 'success');
				$this->redirect(array('action' => 'index', 'sta' => 'creada'));
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		} else {
			$this->request->data	= $this->OrdenCompra->find('first', array(
				'conditions' => array(
					'OrdenCompra.id' => $id
				),
				'contain' => array(
					'Moneda',
					'VentaDetalleProducto' => array(
						'Marca' => array(
							'PrecioEspecificoMarca' => array(
								'conditions' => array(
									'PrecioEspecificoMarca.activo' => 1,
									'OR' => array(
										'PrecioEspecificoMarca.descuento_infinito' => 1,
										'AND' => array(
											array('PrecioEspecificoMarca.fecha_inicio <=' => date('Y-m-d')),
											array('PrecioEspecificoMarca.fecha_termino >=' => date('Y-m-d')),
										)
									)
								),
								'order' => array(
									'PrecioEspecificoMarca.id' => 'DESC'
								)
							)
						),
						'PrecioEspecificoProducto' => array(
							'conditions' => array(
								'PrecioEspecificoProducto.activo' => 1,
								'OR' => array(
									'PrecioEspecificoProducto.descuento_infinito' => 1,
									'AND' => array(
										array('PrecioEspecificoProducto.fecha_inicio <=' => date('Y-m-d')),
										array('PrecioEspecificoProducto.fecha_termino >=' => date('Y-m-d')),
									)
								)
							),
							'order' => array(
								'PrecioEspecificoProducto.id' => 'DESC'
							)
						)
					),
					'Administrador',
					'Tienda',
					'Proveedor'
				)
			));

			# Calculo de descuentos

			foreach ($this->request->data['VentaDetalleProducto'] as $i => $p) {

				$descuentos = ClassRegistry::init('VentaDetalleProducto')::obtener_descuento_por_producto($p);

				$this->request->data['VentaDetalleProducto'][$i]['total_descuento']  = $descuentos['total_descuento'];
				$this->request->data['VentaDetalleProducto'][$i]['nombre_descuento'] = $descuentos['nombre_descuento'];
				$this->request->data['VentaDetalleProducto'][$i]['valor_descuento']  = $descuentos['valor_descuento'];
			}
		}

		$tipoDescuento    = array(0 => '$', 1 => '%');
		$monedas = $this->OrdenCompra->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compa ', '/ordenCompras');
		BreadcrumbComponent::add('Editar ');

		$this->set(compact('tipoDescuento', 'monedas'));
	}


	/**
	 * Modificar una OC generada desde ventas pagadas
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_edit($id = null)
	{
		if (!$this->OrdenCompra->exists($id)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->OrdenCompra->OrdenComprasVenta->deleteAll(array('OrdenComprasVenta.orden_compra_id' => $id));

			if ($this->OrdenCompra->saveAll($this->request->data)) {
				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'validate', $id));
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		} else {
			$this->request->data	= $this->OrdenCompra->find('first', array(
				'conditions'	=> array('OrdenCompra.id' => $id)
			));
		}

		BreadcrumbComponent::add('Ordenes de compa ', '/ordenCompras');
		BreadcrumbComponent::add('Editar ');
	}


	public function admin_cancelar($id)
	{
		$this->OrdenCompra->id = $id;
		if (!$this->OrdenCompra->exists()) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		if (!$this->request->is('post')) {
			$this->Session->setFlash('Método no permitido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		$this->request->data['OrdenCompra']['razon_cancelada'] = $this->request->data['OrdenCompra']['razon_cancelada'] . ' <small class="text-muted">(Cancelada por: ' . $this->Auth->user('email') . ')</small>';

		if ($this->OrdenCompra->saveAll($this->request->data)) {
			$this->Session->setFlash('Orden de compra cancelada.', null, array(), 'success');
			$this->redirect($this->referer('/', true));
		} else {
			$this->Session->setFlash('No fue posible cancelar la orden de compra. Verifique los campos e intente nuevamente.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}
	}

	/**
	 * [admin_delete description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_delete($id = null)
	{
		$this->OrdenCompra->id = $id;
		if (!$this->OrdenCompra->exists()) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		$this->request->onlyAllow('post', 'delete');
		if ($this->OrdenCompra->delete()) {
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect($this->referer('/', true));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect($this->referer('/', true));
	}


	/**
	 * [admin_estado_retiro description]
	 * @param  [type] $id     [description]
	 * @param  [type] $estado [description]
	 * @return [type]         [description]
	 */
	public function admin_estado_retiro($id, $estado)
	{
		$this->OrdenCompra->id = $id;
		if (!$this->OrdenCompra->exists()) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		if ($this->OrdenCompra->saveField('retiro', $estado)) {
			$this->Session->setFlash('Estado del retiro modificado con éxito.', null, array(), 'success');
		} else {
			$this->Session->setFlash('No fue posible actualizar el estado de retiro.', null, array(), 'danger');
		}

		$this->redirect($this->referer('/', true));
	}


	/**
	 * [admin_exportar description]
	 * @return [type] [description]
	 */
	public function admin_exportar()
	{
		ini_set('memory_limit', '-1');
		set_time_limit(0);

		$qry = array(
			'recursive'			=> -1,
			'conditions' => array(
				'OrdenCompra.proveedor_id !=' => ''
			),
			'order' => array(
				'OrdenCompra.id' => 'DESC'
			)
		);

		if (isset($this->request->params['named']['sta'])) {
			$qry['conditions']['OrdenCompra.estado'] = $this->request->params['named']['sta'];
		}

		$datos			= $this->OrdenCompra->find('all', $qry);
		$campos			= array_keys($this->OrdenCompra->_schema);
		$modelo			= $this->OrdenCompra->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}



	public function admin_validar_stock_manual($id)
	{
		$this->OrdenCompra->id = $id;
		if (!$this->OrdenCompra->exists()) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		if ($this->request->is('put')) {

			$oc = $this->OrdenCompra->find('first', array(
				'conditions' => array(
					'OrdenCompra.id' => $id
				),
				'contain' => array(
					'Moneda',
					'VentaDetalleProducto',
					'Administrador',
					'Venta' => array(
						'VentaDetalle',
						'Tienda'
					),
					'OrdenCompraFactura',
					'Tienda',
					'Proveedor'
				)
			));

			$itemes = array();
			$itemsAceptados  = array();
			$itemsRechazados = array();

			foreach ($this->request->data['VentaDetalleProducto'] as $ip => $p) {

				$cantidad          = $p['cantidad'];
				$cantidad_validada = $p['cantidad_validada_proveedor'];

				if ($p['estado_proveedor'] == 'accept') {
					$itemsAceptados[$ip] = $p;
				}

				# si es error de stock se decuenta las unidades rechazadas
				if ($p['estado_proveedor'] == 'stockout' || $p['estado_proveedor'] == 'modified') {

					# Si la cantidad solicitada fue modifcada por el proveedor
					if ($cantidad_validada > 0) {

						$itemsAceptados[$ip] = $p;

						$itemes[$p['estado_proveedor']][$ip] = $p;
					}

					# Se guardan como rechazados as unidades sobrantes
					$itemsRechazados[$ip]                                   = $p;
					$itemsRechazados[$ip]['estado_proveedor']               = 'stockout';
					$itemes['stockout'][$ip]                                = $p;
				}

				$itemes[$p['estado_proveedor']][$ip] = $p;
			}

			$total_rechazados  = array_sum(Hash::extract($itemsRechazados, '{n}.cantidad')) - array_sum(Hash::extract($itemsRechazados, '{n}.cantidad_validada_proveedor'));
			$total_stockout    = count(Hash::extract($itemes, 'stockout.{n}.venta_detalle_producto_id'));
			$total_solicitados = array_sum(Hash::extract($oc, 'VentaDetalleProducto.{n}.OrdenComprasVentaDetalleProducto.cantidad'));

			# si existen itemes rechazados, se crea una nueva OC para el mismo proveedor pero con los produtos que correspondan
			# sí el rechazo es por precio se notifica a validador interno
			# sí es rechazo por stockout se notifica a servicio al cliente que la venta no tendrá su producto
			$nuevaOC = array();
			$ventasNotificar = array();

			$total_neto      = 0;

			# recalculamos los montos
			foreach ($itemsAceptados as $i => $item) {

				#if ($item['cantidad_validada_proveedor'] == 0) continue;

				$descuento_pp       = $item['descuento_producto'] / $item['cantidad'];
				$descuento_pp_final = $descuento_pp * $item['cantidad_validada_proveedor'];

				$this->request->data['VentaDetalleProducto'][$i]['precio_unitario']    = $item['precio_unitario'];
				$this->request->data['VentaDetalleProducto'][$i]['total_neto']         = ($item['precio_unitario'] * $item['cantidad_validada_proveedor']) - $descuento_pp_final;
				$this->request->data['VentaDetalleProducto'][$i]['descuento_producto'] = $descuento_pp_final;
				$this->request->data['VentaDetalleProducto'][$i]['diff_precio_recepcion'] = ($item['diff_precio_recepcion']) ? true : false;
				$this->request->data['VentaDetalleProducto'][$i]['cantidad_zonificada'] = ($item['cantidad_zonificada']) ? $item['cantidad_zonificada'] : 0;
				$this->request->data['VentaDetalleProducto'][$i]['zonificado'] = ($item['zonificado']) ? true : false;

				$total_neto = $total_neto + $this->request->data['VentaDetalleProducto'][$i]['total_neto'];
			}

			if ($oc['OrdenCompra']['estado'] == 'espera_recepcion') {
				$this->request->data['OrdenCompra']['estado'] = 'espera_recepcion';
			} else if (count(Hash::extract($oc, 'OrdenCompraFactura.{n}[tipo_documento=33]')) == 0) {
				$this->request->data['OrdenCompra']['estado'] = 'espera_dte';
			} else {
				$this->request->data['OrdenCompra']['estado'] = 'recepcion_completa';
			}

			$this->request->data['OrdenCompra']['total_neto']      = $total_neto;
			$this->request->data['OrdenCompra']['descuento']       = $this->OrdenCompra->obtener_descuento_oc($id);
			$this->request->data['OrdenCompra']['iva']             = obtener_iva($total_neto);
			$this->request->data['OrdenCompra']['descuento_monto'] = obtener_descuento_monto(($total_neto + $this->request->data['OrdenCompra']['iva']), $this->request->data['OrdenCompra']['descuento']);
			$this->request->data['OrdenCompra']['total']           = ($total_neto - $this->request->data['OrdenCompra']['descuento_monto']) + $this->request->data['OrdenCompra']['iva'];


			# si la cantidad de itemes rechazado es igual a la cantidad de produtos pedidos se devuelve toda la OC
			if ($total_rechazados == $total_solicitados) {
				$this->request->data['OrdenCompra']['estado'] = 'cancelada';
			}

			$this->request->data['OrdenCompraHistorico'] = array(
				array(
					'estado' => $this->request->data['OrdenCompra']['estado'],
					'responsable' => $this->Auth->user('email'),
					'evidencia' => json_encode($this->request->data)
				)
			);

			$stockoutProductos = Hash::extract($this->request->data['VentaDetalleProducto'], '{n}[estado_proveedor=stockout]');

			# Bajamos de los canales de venta los productos sin stock
			if ($stockoutProductos) {
				# Cambiamos stock canales
				$productoscontroller = new VentaDetalleProductosController;

				foreach ($stockoutProductos as $ps) {
					$productoscontroller->actualizar_canales_stock($ps['venta_detalle_producto_id'], 0);

					# Actualizamos stock virtual sistema
					$ppp = array(
						'VentaDetalleProducto' => array(
							'id' => $ps['venta_detalle_producto_id'],
							'cantidad_virtual' => 0
						)
					);

					ClassRegistry::init('VentaDetalleProducto')->save($ppp);
				}
			}
			if ($this->OrdenCompra->saveAll($this->request->data, array('deep' => true))) {

				# Flujo para cuando un producto no tenga stock
				if ($total_stockout > 0) {
					# Notificar a ventas para que coordine con el cliente
					$ventasNotificar = $this->OrdenCompra->obtener_ventas_por_productos($oc['OrdenCompra']['id'], Hash::extract($itemes['stockout'], '{n}.venta_detalle_producto_id'));
				}

				# notificar stockout a ventas
				if (!empty($ventasNotificar)) {

					$emailsVentas = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('ventas');

					if (!empty($emailsVentas)) {
						$enviado = $this->guardarEmailStockout($id, $ventasNotificar, $itemes['stockout'], $emailsVentas);
					}

					# notificamos a clientes
					App::uses('HttpSocket', 'Network/Http');
					$socket			= new HttpSocket();

					# Notificamos stockout a clientes
					foreach ($ventasNotificar as $iv => $v) {

						# No se notifica en dev
						if (Configure::read('ambiente') == 'dev')
							break;

						$request		= $socket->get(
							Router::url('/api/ventas/stockout/' . $v['Venta']['id'] . '.json?token=' . $this->Session->read('Auth.Administrador.token.token'), true)
						);
					}
				}

				$this->Session->setFlash('OC actualizada con éxito.', null, array(), 'success');

				if ($this->request->data['OrdenCompra']['estado'] == 'espera_recepcion') {
					$this->Session->setFlash('Ahora puede continuar con el flujo de esta OC.', null, array(), 'warning');
					$this->redirect(array('action' => 'reception', $id));
				} else if ($this->request->data['OrdenCompra']['estado'] == 'espera_dte') {
					$this->Session->setFlash('Se requiere un DTE para continuar.', null, array(), 'warning');
					$this->redirect(array('action' => 'reception', $id));
				} else {
					$this->redirect(array('action' => 'view', $id));
				}
			} else {

				$this->Session->setFlash('No fue posible actualizar la OC.', null, array(), 'danger');
				$this->redirect(array('action' => 'view', $id));
			}
		} else {

			$qry = array(
				'conditions' => array(
					'OrdenCompra.id' => $id,
					'OrdenCompra.estado' => array('recepcion_incompleta', 'espera_recepcion'),
				),
				'contain' => array(
					'VentaDetalleProducto' => array(
						'fields' => array(
							'VentaDetalleProducto.id'
						)
					)
				),
				'fields' => array(
					'OrdenCompra.id'
				)
			);

			$this->request->data = $this->OrdenCompra->find('first', $qry);
		}

		if (empty($this->request->data)) {
			$this->Session->setFlash('La OC ya no se encuentra en este apartado.', null, array(), 'danger');
			$this->redirect(array('action' => 'index', 'id' => $id));
		}


		BreadcrumbComponent::add('Ordenes de compra ', array('action' => 'index'));
		BreadcrumbComponent::add('Validar OC');

		$estados = $this->OrdenCompra->estado_proveedor;
		unset($estados['price_error']);
		$this->set(compact('estados'));
	}


	/**
	 * [admin_obtener_ordenes_ajax description]
	 * @return [type] [description]
	 */
	public function admin_obtener_ordenes_ajax()
	{
		$this->layout = 'ajax';

		ini_set('memory_limit', -1);
		$ventas          = $this->OrdenCompra->Venta->find('all', array(

			'fields' => array(
				'Venta.id',
				'Venta.id_externo',
				'Venta.referencia',
				'Venta.fecha_venta',
				'Venta.total',
				'Venta.prioritario',
				'Venta.picking_estado'
			),
			'joins' => array(
				array(
					'table' => 'rp_venta_estados',
					'alias' => 'venta_estados',
					'type' 	=> 'INNER',
					'conditions' => array(
						'venta_estados.id = Venta.venta_estado_id',
						'venta_estados.permitir_oc = 1'
					)
				),
				array(
					'table' => 'rp_venta_estado_categorias',
					'alias' => 'venta_estados_cat',
					'type' 	=> 'INNER',
					'conditions' => array(
						'venta_estados_cat.id = venta_estados.venta_estado_categoria_id',
						'venta_estados_cat.rechazo = 0',
						'venta_estados_cat.cancelado = 0',
						'venta_estados_cat.final = 0'
					)
				)
			),
			'contain' => array(
				'Dte' => array(
					'fields' => array('Dte.id', 'Dte.tipo_documento')
				),
				'Marketplace' => array(
					'fields' => array('Marketplace.nombre')
				),
				'VentaCliente' => array(
					'fields' => array('VentaCliente.nombre', 'VentaCliente.email', 'VentaCliente.apellido')
				),
				'VentaDetalle' => array(
					'fields' => array('VentaDetalle.id', 'VentaDetalle.venta_detalle_producto_id')
				),
				'VentaEstado' => array(
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
						)
					),
					'fields' => array(
						'VentaEstado.id', 'VentaEstado.nombre', 'VentaEstado.permitir_oc'
					)
				),
				'OrdenCompra' => array(
					'conditions' => array(
						'OrdenCompra.parent_id' => null
					),
					'fields' => array(
						'OrdenCompra.id'
					)
				),
				'Bodega' => ['fields' => 'Bodega.nombre']
			),
			'conditions' => array(
				"Venta.id in (SELECT Venta.id
					FROM rp_ventas AS Venta
							 INNER JOIN rp_venta_estados AS venta_estados
										ON (venta_estados.id = Venta.venta_estado_id AND venta_estados.permitir_oc = 1)
							 INNER JOIN rp_venta_estado_categorias AS venta_estados_cat
										ON (venta_estados_cat.id = venta_estados.venta_estado_categoria_id AND
											venta_estados_cat.rechazo = 0 AND venta_estados_cat.cancelado = 0 AND
											venta_estados_cat.final = 0)
							 INNER JOIN rp_venta_detalles rvd ON Venta.id = rvd.venta_id
					WHERE Venta.fecha_venta > ADDDATE(NOW(), INTERVAL -2 Month)
					having ((select Sum(CAST(detalle.cantidad as signed) - CAST(detalle.cantidad_anulada as signed) -
										CAST(detalle.cantidad_entregada as signed) -
										CAST(detalle.cantidad_reservada as signed))
							 from rp_venta_detalles as detalle
							 where detalle.id = rvd.id) - (SELECT ifnull(Sum(Reserva.cantidad_reservada), 0)
														   from rp_venta_detalles_reservas as Reserva
														   where Reserva.venta_detalle_id = rvd.id)) >
						   ((SELECT ifnull(Sum(StockProducto.cantidad),0)
							 from rp_bodegas_venta_detalle_productos as StockProducto
							 where StockProducto.venta_detalle_producto_id = rvd.venta_detalle_producto_id
							   and tipo <> 'GT') - (SELECT ifnull(Sum(Reserva.cantidad_reservada), 0)
													from rp_venta_detalles_reservas as Reserva
													where Reserva.venta_detalle_producto_id = rvd.venta_detalle_producto_id))
					   and ((select Sum(CAST(detalle.cantidad as signed) - CAST(detalle.cantidad_anulada as signed) -
										CAST(detalle.cantidad_entregada as signed) -
										CAST(detalle.cantidad_reservada as signed))
							 from rp_venta_detalles as detalle
							 where detalle.id = rvd.id) - (SELECT ifnull(Sum(Reserva.cantidad_reservada), 0)
														   from rp_venta_detalles_reservas as Reserva
														   where Reserva.venta_detalle_id = rvd.id)) > 0
					ORDER BY Venta.prioritario DESC, Venta.fecha_venta DESC)",
				'Venta.bodega_id' 		=> Hash::extract($this->Auth->user('Bodega'), '{n}.id')
			),
			'limit' 	=> $this->request->query['limit'] ?? 200,
			'offset' 	=> $this->request->query['offset'] ?? 0,
			'order' 	=> array('Venta.prioritario' => 'DESC', 'Venta.fecha_venta' => 'DESC')
		));


		if (empty($ventas)) {
			echo 0;
			exit;
		}

		$obtenerRelacionados = array();

		if (isset($this->request->query['id'])) {
			$relacionados =  $this->OrdenCompra->OrdenComprasVenta->find('all', array(
				'conditions' => array(
					'OrdenComprasVenta.orden_compra_id' => $this->request->query['id']
				),
				'fields' => array('OrdenComprasVenta.venta_id')
			));

			$obtenerRelacionados = Hash::extract($relacionados, '{n}.OrdenComprasVenta.venta_id');
		}



		foreach ($ventas as $io => $orden) {

			$ventas[$io]['Venta']['selected'] = false;

			if (isset($this->request->query['id'])) {
				if (in_array($orden['Venta']['id'], $obtenerRelacionados)) {
					$ventas[$io]['Venta']['selected'] = true;
				}
			}
		}

		$bodega_default = ClassRegistry::init('Bodega')->find(
			'first',
			[
				'conditions' => ['Bodega.principal' => 1],
				'fields' => ['Bodega.id', 'Bodega.nombre']
			]
		);

		$this->set(compact('ventas', 'bodega_default'));
	}

	/**
	 * [admin_calcularMontoPagar description]
	 * @return [type] [description]
	 */
	public function admin_calcularMontoPagar()
	{
		$res = array(
			'descuento_porcentaje'  => 0,
			'descuento_monto'       => 0,
			'descuento_monto_html'  => CakeNumber::currency(0, 'CLP'),
			'monto_pagar'           => 0,
			'monto_pagar_html'      => CakeNumber::currency(0, 'CLP'),
			'pago_adelantado'       => false,
			'comprobante_requerido' => false,
			'agendar'				=> false,
			'pago_contra_factura'	=> false,
		);

		if ($this->request->is('post')) {

			$oc = $this->OrdenCompra->find('first', array(
				'conditions' => array(
					'OrdenCompra.id' => $this->request->data['orden_compra_id']
				),
				'contain' => array(
					'Proveedor' => array(
						'Moneda'
					)
				)
			));

			$moneda = ClassRegistry::init('Moneda')->find('first', array(
				'conditions' => array(
					'Moneda.id' => $this->request->data['moneda_id']
				)
			));

			# Condiciones por moneda seleccionada
			if (!empty($moneda)) {
				$tipo_moneda = $moneda['Moneda']['tipo'];
				$comprobante = $moneda['Moneda']['comprobante_requerido'];

				if ($tipo_moneda == 'pagar') {
					$res['pago_adelantado'] = true;
				}

				if ($comprobante) {
					$res['comprobante_requerido'] = true;
				}

				if ($tipo_moneda == 'agendar') {
					$res['agendar'] = true;
				}

				if ($tipo_moneda == 'esperar') {
					$res['pago_contra_factura'] = true;
				}
			}

			# Descuentos por método de pago
			if (Hash::check($oc, 'Proveedor.Moneda.{n}[id=' . $this->request->data['moneda_id'] . ']')) {
				$descuento = Hash::extract($oc, 'Proveedor.Moneda.{n}[id=' . $this->request->data['moneda_id'] . '].MonedasProveedor.descuento')[0];

				$res['descuento_porcentaje'] = $descuento;
				$res['descuento_monto']      = $oc['OrdenCompra']['total'] * ($descuento / 100);
				$res['descuento_monto_html'] = CakeNumber::currency($oc['OrdenCompra']['total'] * ($descuento / 100), 'CLP');
			}

			$res['monto_pagar'] = round($oc['OrdenCompra']['total'] - $res['descuento_monto']);
			$res['monto_pagar_html'] = CakeNumber::currency($oc['OrdenCompra']['total'] - $res['descuento_monto'], 'CLP');
		}

		echo json_encode($res, true);
		exit;
	}


	/**
	 * [guardarEmailRevision description]
	 * @param  array  $ocs    [description]
	 * @param  array  $emails [description]
	 * @return [type]         [description]
	 */
	private function guardarEmailRevision($ocs = array(), $emails = array())
	{
		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompras' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';

		$url = obtener_url_base();

		$this->View->set(compact('ocs', 'url'));
		$html						= $this->View->render('notificar_revision_oc');

		$mandrill_apikey = ClassRegistry::init('Tienda')->field('mandrill_apikey', array('id' => $ocs['OrdenCompra']['tienda_id']));

		if (empty($mandrill_apikey)) {
			return false;
		}

		$mandrill = $this->Components->load('Mandrill');

		$mandrill->conectar($mandrill_apikey);

		$asunto = '[NDRZ] OC para ' . strtolower($ocs['OrdenCompra']['razon_social_empresa']) . ' lista para revisión.';

		if (Configure::read('ambiente') == 'dev') {
			$asunto = '[NDRZ-DEV] OC para ' . strtolower($ocs['OrdenCompra']['razon_social_empresa']) . ' lista para revisión.';
		}


		$remitente = array(
			'email' => 'oc@nodriza.cl',
			'nombre' => 'Sistema de Órdenes de compra Nodriza'
		);

		$destinatarios = array();

		foreach ($emails as $im => $e) {
			$destinatarios[$im]['email'] = $e;
		}

		return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
	}


	/**
	 * [guardarEmailRechazo description]
	 * @param  [type] $id     [description]
	 * @param  array  $emails [description]
	 * @return [type]         [description]
	 */
	private function guardarEmailRechazo($id, $emails = array())
	{
		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompras' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';

		$url = obtener_url_base();

		$this->View->set(compact('id', 'url'));
		$html						= $this->View->render('notificar_rechazo_oc');

		$oc = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'fields' => array(
				'OrdenCompra.tienda_id'
			)
		));

		$mandrill_apikey = ClassRegistry::init('Tienda')->field('mandrill_apikey', array('id' => $oc['OrdenCompra']['tienda_id']));

		if (empty($mandrill_apikey)) {
			return false;
		}

		$mandrill = $this->Components->load('Mandrill');

		$mandrill->conectar($mandrill_apikey);

		$asunto = sprintf('[NDRZ] OC #%d rechazada', $id);

		if (Configure::read('ambiente') == 'dev') {
			$asunto = sprintf('[NDRZ-DEV] OC #%d rechazada', $id);
		}

		$remitente = array(
			'email' => 'oc@nodriza.cl',
			'nombre' => 'Sistema de Órdenes de compra Nodriza'
		);

		$destinatarios = array();

		foreach ($emails as $im => $e) {
			$destinatarios[$im]['email'] = $e;
		}

		return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
	}


	/**
	 * [guardarEmailRechazo description]
	 * @param  [type] $id     [description]
	 * @param  array  $emails [description]
	 * @return [type]         [description]
	 */
	private function guardarEmailRechazoProveedor($id, $emails = array())
	{
		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompras' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';

		$url = obtener_url_base();

		$this->View->set(compact('id', 'url'));
		$html						= $this->View->render('notificar_rechazo_proveedor');

		$oc = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'fields' => array(
				'OrdenCompra.tienda_id'
			)
		));

		$mandrill_apikey = ClassRegistry::init('Tienda')->field('mandrill_apikey', array('id' => $oc['OrdenCompra']['tienda_id']));

		if (empty($mandrill_apikey)) {
			return false;
		}

		$mandrill = $this->Components->load('Mandrill');

		$mandrill->conectar($mandrill_apikey);

		$asunto = sprintf('[NDRZ] OC #%d rechazada por proveedor', $id);

		if (Configure::read('ambiente') == 'dev') {
			$asunto = sprintf('[NDRZ-DEV] OC #%d rechazada por proveedor', $id);
		}

		$remitente = array(
			'email' => 'oc@nodriza.cl',
			'nombre' => 'Sistema de Órdenes de compra Nodriza'
		);

		$destinatarios = array();

		foreach ($emails as $im => $e) {
			$destinatarios[$im]['email'] = $e;
		}

		return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
	}


	/**
	 * [guardarEmailStockout description]
	 * @param  array  $ventas [description]
	 * @param  array  $emails [description]
	 * @return [type]         [description]
	 */
	private function guardarEmailStockout($id, $ventas, $productos, $emails = array())
	{
		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompras' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';

		$url = obtener_url_base();

		$this->View->set(compact('ventas', 'productos', 'url'));
		$html						= $this->View->render('notificar_stockout_ventas');

		$oc = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'fields' => array(
				'OrdenCompra.tienda_id'
			)
		));

		$mandrill_apikey = ClassRegistry::init('Tienda')->field('mandrill_apikey', array('id' => $oc['OrdenCompra']['tienda_id']));

		if (empty($mandrill_apikey)) {
			return false;
		}

		$mandrill = $this->Components->load('Mandrill');

		$mandrill->conectar($mandrill_apikey);

		if (Configure::read('ambiente') == 'dev') {
			$asunto = sprintf('[NDRZ-DEV] Hay %d ventas con productos en stockout.', count($ventas));
		} else {
			$asunto = sprintf('[NDRZ] Hay %d ventas con productos en stockout.', count($ventas));
		}

		$remitente = array(
			'email' => 'oc@nodriza.cl',
			'nombre' => 'Sistema de Órdenes de compra Nodriza'
		);

		$destinatarios = array();

		foreach ($emails as $im => $e) {
			$destinatarios[$im]['email'] = $e;
		}

		return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
	}


	/**
	 * [guardarEmailValidado description]
	 * @param  [type] $id     [description]
	 * @param  array  $emails [description]
	 * @return [type]         [description]
	 */
	public function guardarEmailValidado($id, $recordatorio = false)
	{
		$oc = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'fields' => array(
				'OrdenCompra.id', 'OrdenCompra.tienda_id', 'OrdenCompra.administrador_id'
			),
			'contain' => array(
				'Proveedor' => array(
					'fields' => array(
						'Proveedor.nombre', 'Proveedor.meta_emails'
					)
				),
				'Tienda' => array(
					'fields' => array(
						'Tienda.nombre', 'Tienda.id'
					)
				),
				'Administrador' => array(
					'fields' => array(
						'Administrador.email', 'Administrador.nombre'
					)
				)
			)
		));

		$mensaje = sprintf('Estimados %s, la OC #%d emitida por "%s" se encuentra disponible para ser validada.', $oc['Proveedor']['nombre'], $oc['OrdenCompra']['id'], $oc['Tienda']['nombre']);

		# Quitamos los emails inactivos
		$oc['Proveedor']['meta_emails'] = @Hash::remove($oc['Proveedor']['meta_emails'], '{n}[activo=0]');

		# Asignamos los emails respectivos
		$validadores = Hash::extract($oc['Proveedor'], 'meta_emails.{n}[tipo=validador].email');
		$receptores  = Hash::extract($oc['Proveedor'], 'meta_emails.{n}[tipo=destinatario].email');
		$cc          = Hash::extract($oc['Proveedor'], 'meta_emails.{n}[tipo=copia].email');
		$bcc         = Hash::extract($oc['Proveedor'], 'meta_emails.{n}[tipo=copia oculta].email');

		$to = (!empty($validadores)) ? $validadores : $receptores;

		# Sin emails retornamos
		if (empty($to))
			return false;

		# Obtenemos token y lo validamos
		$gettoken = ClassRegistry::init('Token')->find('first', array(
			'conditions' => array(
				'Token.proveedor_id' => $oc['Proveedor']['id']
			),
			'order' => array('Token.created' => 'DESC')
		));

		if (empty($gettoken)) {
			# creamos un token de acceso vía email
			$token = ClassRegistry::init('Token')->crear_token_proveedor($oc['Proveedor']['id'], $oc['Tienda']['id'])['token'];

		}else if (!ClassRegistry::init('Token')->validar_token($gettoken['Token']['token'], 'proveedor')){
			# creamos un token de acceso vía email
			$token = ClassRegistry::init('Token')->crear_token_proveedor($oc['Proveedor']['id'], $oc['Tienda']['id'])['token'];
		} else {
			$token = $gettoken['Token']['token'];
		}

		if (empty($token)) {
			return false;
		}

		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompras' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';

		$url = obtener_url_base();

		$this->View->set(compact('mensaje', 'oc', 'url', 'token'));
		$html						= $this->View->render('notificar_proveedor_oc');

		$mandrill_apikey = ClassRegistry::init('Tienda')->field('mandrill_apikey', array('id' => $oc['OrdenCompra']['tienda_id']));

		if (empty($mandrill_apikey)) {
			return false;
		}

		$mandrill = $this->Components->load('Mandrill');

		$mandrill->conectar($mandrill_apikey);

		if ($recordatorio) {
			$asunto = sprintf('[OC-RECORDATORIO] #%d Se ha creado una Orden de compra desde Nodriza Spa', $id);
		} else {
			$asunto = sprintf('[OC] #%d Se ha creado una Orden de compra desde Nodriza Spa', $id);
		}


		if (Configure::read('ambiente') == 'dev') {
			if ($recordatorio) {
				$asunto = sprintf('[OC-DEV-RECORDATORIO] #%d Se ha creado una Orden de compra desde Nodriza Spa', $id);
			} else {
				$asunto = sprintf('[OC-DEV] #%d Se ha creado una Orden de compra desde Nodriza Spa', $id);
			}
		}

		$remitente = array(
			'email' => 'oc@nodriza.cl',
			'nombre' => 'Sistema de Órdenes de compra Nodriza'
		);

		$destinatarios = array();

		foreach ($to as $id => $des) {
			$destinatarios[] = array(
				'email' => $des,
				'type' => 'to'
			);
		}

		foreach ($cc as $ic => $c) {
			$destinatarios[] = array(
				'email' => $c,
				'type' => 'cc'
			);
		}

		foreach ($bcc as $ibc => $bc) {
			$destinatarios[] = array(
				'email' => $bc,
				'type' => 'bcc'
			);
		}

		$cabeceras = array(
			'Reply-To' => $oc['Administrador']['email']
		);

		return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios, $cabeceras);
	}


	/**
	 * [guardarEmailAsignarPago description]
	 * @param  [type] $id     [description]
	 * @param  array  $emails [description]
	 * @return [type]         [description]
	 */
	private function guardarEmailAsignarPago($oc, $emails = array())
	{
		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompras' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';

		$url = obtener_url_base();

		$id = $oc['OrdenCompra']['id'];

		$this->View->set(compact('id', 'url'));
		$html						= $this->View->render('notificar_asignar_moneda_oc');

		$mandrill_apikey = ClassRegistry::init('Tienda')->field('mandrill_apikey', array('id' => $oc['OrdenCompra']['tienda_id']));

		if (empty($mandrill_apikey)) {
			return false;
		}

		$mandrill = $this->Components->load('Mandrill');

		$mandrill->conectar($mandrill_apikey);

		$asunto = sprintf('[NDRZ] Asignar pago para OC #%d ', $id);

		if (Configure::read('ambiente') == 'dev') {
			$asunto = sprintf('[NDRZ-DEV] Asignar pago para OC #%d ', $id);
		}

		$remitente = array(
			'email' => 'oc@nodriza.cl',
			'nombre' => 'Sistema de Órdenes de compra Nodriza'
		);

		$destinatarios = array();

		foreach ($emails as $im => $e) {
			$destinatarios[$im]['email'] = $e;
		}

		return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
	}


	/**
	 * Notifica a finanzas una vez validada la OC por el proveedor
	 * @param  [type] $id     [description]
	 * @param  array  $emails [description]
	 * @return [type]         [description]
	 */
	private function guardarEmailValidadoProveedor($oc, $emails = array())
	{
		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompras' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';

		$url = obtener_url_base();
		$id = $oc['OrdenCompra']['id'];

		$this->View->set(compact('id', 'url'));
		$html						= $this->View->render('notificar_validado_oc');

		$mandrill_apikey = ClassRegistry::init('Tienda')->field('mandrill_apikey', array('id' => $oc['OrdenCompra']['tienda_id']));

		if (empty($mandrill_apikey)) {
			return false;
		}

		$mandrill = $this->Components->load('Mandrill');

		$mandrill->conectar($mandrill_apikey);

		$asunto = sprintf('[NDRZ] OC #%d lista para pagar', $id);

		if (Configure::read('ambiente') == 'dev') {
			$asunto = sprintf('[NDRZ-DEV] OC #%d lista para pagar', $id);
		}

		$remitente = array(
			'email' => 'oc@nodriza.cl',
			'nombre' => 'Sistema de Órdenes de compra Nodriza'
		);

		$destinatarios = array();

		foreach ($emails as $im => $e) {
			$destinatarios[$im]['email'] = $e;
		}

		return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
	}


	/**
	 * [guardarEmailPagado description]
	 * @param  [type] $id     [description]
	 * @param  array  $emails [description]
	 * @return [type]         [description]
	 */
	private function guardarEmailPagado($id, $emails = array())
	{
		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompras' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';

		$url = obtener_url_base();

		$this->View->set(compact('id', 'url'));
		$html						= $this->View->render('notificar_pagado_oc');

		/**
		 * Guarda el email a enviar
		 */
		$this->Correo->create();

		if ($this->Correo->save(array(
			'estado'					=> 'Notificación pagado oc',
			'html'						=> $html,
			'asunto'					=> sprintf('[NDRZ] OC #%d lista para enviar', $id),
			'destinatario_email'		=> trim(implode(',', $emails)),
			'destinatario_nombre'		=> '',
			'remitente_email'			=> 'cristian.rojas@nodriza.cl',
			'remitente_nombre'			=> 'Sistemas - Nodriza Spa',
			'cc_email'					=> '',
			'bcc_email'					=> 'cristian.rojas@nodriza.cl',
			'traza'						=> null,
			'proceso_origen'			=> null,
			'procesado'					=> 0,
			'enviado'					=> 0,
			'reintentos'				=> 0,
			'atachado'					=> null
		))) {
			return true;
		}

		return false;
	}


	/**
	 * [generar_pdf description]
	 * @param  array  $oc       [description]
	 * @param  string $nombreOC [description]
	 * @return [type]           [description]
	 */
	public function generar_pdf($oc = array(), $nombreOC = '')
	{

		App::uses('CakePdf', 'Plugin/CakePdf/Pdf');

		try {
			$this->CakePdf = new CakePdf();
			$this->CakePdf->template('generar_oc', 'default');
			$this->CakePdf->viewVars(compact('oc'));
			@$this->CakePdf->write(APP . 'webroot' . DS . 'Pdf' . DS . 'OrdenCompra' . DS . $oc['OrdenCompra']['id'] . DS . $nombreOC);
		} catch (Exception $e) {
			// Error
		}

		# Ruta para guardar en la Base de datos
		$archivo = Router::url('/', true) . 'Pdf/OrdenCompra/' . $oc['OrdenCompra']['id'] . '/' . $nombreOC;

		return;
	}


	/**
	 * Socios/proveedores
	 */

	/**
	 * Permite validar una OC dese un email dado
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function validate_supplier($id)
	{

		$this->Auth->allow('view', 'validate_supplier');

		if (!isset($this->request->query['access_token']) || !ClassRegistry::init('Token')->validar_token($this->request->query['access_token'], 'proveedor')) {
			throw new Exception("El token de acceso no es válido", 404);
			exit;
		}

		$this->layout = 'backend/socio';


		if ($this->request->is('put')) {

			$oc = $this->OrdenCompra->find('first', array(
				'conditions' => array(
					'OrdenCompra.id' => $id
				),
				'contain' => array(
					'Moneda',
					'VentaDetalleProducto',
					'Administrador',
					'Venta' => array(
						'VentaDetalle',
						'Tienda'
					),
					'Tienda',
					'Proveedor',
					'Bodega'
				)
			));

			$itemes = array();
			$itemsAceptados  = array();
			$itemsRechazados = array();

			foreach ($this->request->data['VentaDetalleProducto'] as $ip => $p) {

				$cantidad          = $p['cantidad'];
				$cantidad_validada = $p['cantidad_validada_proveedor'];

				if ($p['estado_proveedor'] == 'accept') {
					$itemsAceptados[$ip] = $p;
				}

				# si es error de stock se decuenta las unidades rechazadas
				if ($p['estado_proveedor'] == 'stockout' || $p['estado_proveedor'] == 'modified') {

					# Si la cantidad solicitada fue modifcada por el proveedor
					if ($cantidad_validada > 0) {

						$itemsAceptados[$ip] = $p;

						$itemes[$p['estado_proveedor']][$ip] = $p;
					}

					# Se guardan como rechazados as unidades sobrantes
					$itemsRechazados[$ip]                                   = $p;
					$itemsRechazados[$ip]['estado_proveedor']               = 'stockout';
					$itemes['stockout'][$ip]                                = $p;
				}

				if ($p['estado_proveedor'] == 'price_error') {
					$itemsRechazados[$ip] = $p;
					$itemsRechazados[$ip]['cantidad_validada_proveedor'] = 0;
				}

				$itemes[$p['estado_proveedor']][$ip] = $p;
			}

			$total_rechazados  = array_sum(Hash::extract($itemsRechazados, '{n}.cantidad')) - array_sum(Hash::extract($itemsRechazados, '{n}.cantidad_validada_proveedor'));
			$total_stockout    = count(Hash::extract($itemes, 'stockout.{n}.venta_detalle_producto_id'));
			$total_price_error = count(Hash::extract($itemes, 'price_error.{n}.venta_detalle_producto_id'));
			$total_solicitados = array_sum(Hash::extract($oc, 'VentaDetalleProducto.{n}.OrdenComprasVentaDetalleProducto.cantidad'));

			# si existen itemes rechazados, se crea una nueva OC para el mismo proveedor pero con los produtos que correspondan
			# sí el rechazo es por precio se notifica a validador interno
			# sí es rechazo por stockout se notifica a servicio al cliente que la venta no tendrá su producto
			$nuevaOC = array();
			$ventasNotificar = array();


			# si la cantidad de itemes rechazado es igual a la cantidad de produtos pedidos se devuelve toda la OC (stockout)
			if ($total_rechazados == $total_solicitados) {
				$this->request->data['OrdenCompra']['estado'] = 'cancelada';
			}


			# Error de precio en algunos productos de la oc
			# pasan a una nueva oc que se envia a revisión comercial y la actual continua solo con los itemes aceptados.
			if ($total_price_error > 0 && $total_rechazados != $total_solicitados) {
				# Item se quita de la OC y se agrega a una nueva OC

				$nuevaOC['OrdenCompra'] = $oc['OrdenCompra'];

				$total_neto = 0;

				foreach ($itemes['price_error'] as $iv => $v) {
					$nuevaOC['VentaDetalleProducto'][$iv] = $v;
					$total_neto = $total_neto + $v['total_neto'];
				}

				$nuevaOC['OrdenCompra']['total_neto']         = $total_neto;
				$nuevaOC['OrdenCompra']['descuento']          = $oc['OrdenCompra']['descuento'];
				$nuevaOC['OrdenCompra']['iva']                = obtener_iva($total_neto);
				$nuevaOC['OrdenCompra']['descuento_monto']    = obtener_descuento_monto(($total_neto + $nuevaOC['OrdenCompra']['iva']), $nuevaOC['OrdenCompra']['descuento']);
				$nuevaOC['OrdenCompra']['total']              = ($total_neto - $nuevaOC['OrdenCompra']['descuento_monto']) + $nuevaOC['OrdenCompra']['iva'];
				$nuevaOC['OrdenCompra']['estado']             = 'validacion_comercial';
				$nuevaOC['OrdenCompra']['fecha']              = date('Y-m-d');
				$nuevaOC['OrdenCompra']['vendedor']           = '(Auto) Nodriza Spa';
				$nuevaOC['OrdenCompra']['validado_proveedor'] = 0;

				$nuevaOC['OrdenCompraHistorico'] = array(
					array(
						'estado' => 'validacion_comercial',
						'responsable' => '(Auto) Nodriza spa',
						'evidencia' => json_encode($nuevaOC)
					)
				);

				# quitamos el id
				unset($nuevaOC['OrdenCompra']['id']);
				unset($nuevaOC['OrdenCompra']['created']);
				unset($nuevaOC['OrdenCompra']['modified']);
				unset($nuevaOC['OrdenCompra']['moneda_id']);
			}

			# OC completa con error de precio vuelve a validación
			if ($total_price_error > 0 && $total_rechazados == $total_solicitados) {
				$this->request->data['OrdenCompra']['estado'] = 'validacion_comercial';
			}


			# Continuan sólo los itemes aceptados
			if (count($itemsAceptados) > 0) {

				$total_neto      = 0;

				# recalculamos los montos
				foreach ($itemsAceptados as $i => $item) {

					$descuento_pp       = $item['descuento_producto'] / $item['cantidad'];
					$descuento_pp_final = $descuento_pp * $item['cantidad_validada_proveedor'];

					$this->request->data['VentaDetalleProducto'][$i]['precio_unitario']    = $item['precio_unitario'];
					$this->request->data['VentaDetalleProducto'][$i]['total_neto']         = ($item['precio_unitario'] * $item['cantidad_validada_proveedor']) - $descuento_pp_final;
					$this->request->data['VentaDetalleProducto'][$i]['descuento_producto'] = $descuento_pp_final;

					$total_neto = $total_neto + $this->request->data['VentaDetalleProducto'][$i]['total_neto'];
				}

				$this->request->data['OrdenCompra']['total_neto']      = $total_neto;
				$this->request->data['OrdenCompra']['descuento']       = $this->OrdenCompra->obtener_descuento_oc($id);
				$this->request->data['OrdenCompra']['iva']             = obtener_iva($total_neto);
				$this->request->data['OrdenCompra']['descuento_monto'] = obtener_descuento_monto(($total_neto + $this->request->data['OrdenCompra']['iva']), $this->request->data['OrdenCompra']['descuento']);
				$this->request->data['OrdenCompra']['total']           = ($total_neto - $this->request->data['OrdenCompra']['descuento_monto']) + $this->request->data['OrdenCompra']['iva'];
			}

			$this->request->data['OrdenCompraHistorico'] = array(
				array(
					'estado' => 'validacion_externa',
					'responsable' => $this->request->data['OrdenCompra']['nombre_validado_proveedor'],
					'evidencia' => json_encode($this->request->data)
				)
			);

			$stockoutProductos = Hash::extract($this->request->data['VentaDetalleProducto'], '{n}[estado_proveedor=stockout]');

			# Bajamos de los canales de venta los productos sin stock
			if ($stockoutProductos) {
				# Cambiamos stock canales
				$productoscontroller = new VentaDetalleProductosController;

				foreach ($stockoutProductos as $ps) {
					$productoscontroller->actualizar_canales_stock($ps['venta_detalle_producto_id'], 0);

					# Actualizamos stock virtual sistema
					$ppp = array(
						'VentaDetalleProducto' => array(
							'id' => $ps['venta_detalle_producto_id'],
							'cantidad_virtual' => 0
						)
					);

					ClassRegistry::init('VentaDetalleProducto')->save($ppp);
				}
			}

			if ($this->OrdenCompra->saveAll($this->request->data, array('deep' => true))) {
				# Flujo para cuando un producto no tenga stock
				if ($total_stockout > 0) {
					# Notificar a ventas para que coordine con el cliente
					$ventasNotificar = $this->OrdenCompra->obtener_ventas_por_productos($oc['OrdenCompra']['id'], Hash::extract($itemes['stockout'], '{n}.venta_detalle_producto_id'));
				}

				# notificar stockout a ventas
				if (!empty($ventasNotificar)) {

					$emailsVentas = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('ventas');

					if (!empty($emailsVentas)) {
						$enviado = $this->guardarEmailStockout($id, $ventasNotificar, $itemes['stockout'], $emailsVentas);
					}


					App::uses('HttpSocket', 'Network/Http');
					$socket			= new HttpSocket();

					# Notificamos stockout a clientes
					foreach ($ventasNotificar as $iv => $v) {

						# No se notifica en dev
						if (Configure::read('ambiente') == 'dev')
							break;

						$request		= $socket->get(
							Router::url('/api/ventas/stockout/' . $v['Venta']['id'] . '.json?token=' . $this->request->query['access_token'], true)
						);
					}
				}

				# crear la nueva OC
				if (!empty($nuevaOC)) {

					# Notificar nueva OC
					$emailsNotificar = array($nuevaOC['OrdenCompra']['email_comercial']);

					$this->OrdenCompra->create();
					if ($this->OrdenCompra->saveAll($nuevaOC, array('deep' => true)) && !empty($emailsNotificar)) {
						$this->guardarEmailRevision($nuevaOC, $emailsNotificar);
					}
				}

				# Notifcar rechazo completo a comerial
				if ($this->request->data['OrdenCompra']['estado'] == 'cancelada' || $this->request->data['OrdenCompra']['estado'] == 'validacion_comercial') {
					$email_comercial = $oc['OrdenCompra']['email_comercial'];
					$this->guardarEmailRechazoProveedor($id, array($email_comercial));

					# Mostramos mensaje de co guardada
					$redirect = sprintf('%ssocio/oc/%d?access_token=%s&success=true', obtener_url_base(), $id, $this->request->query['access_token']);
					$this->redirect($redirect);
				}

				# Genera el PDF
				if ($this->request->data['OrdenCompra']['estado'] == 'pago_finanzas') {

					$oc = $this->OrdenCompra->find('first', array(
						'conditions' => array(
							'OrdenCompra.id' => $id
						),
						'contain' => array(
							'Moneda',
							'VentaDetalleProducto',
							'Administrador',
							'Venta' => array(
								'VentaDetalle',
								'Tienda'
							),
							'Tienda',
							'Proveedor',
							'Bodega'
						)
					));

					# Notificar a finanzas (en espera)
					$emailsFinanzas = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('pagar_oc');

					if (!empty($emailsFinanzas)) {
						$this->guardarEmailValidadoProveedor($oc, $emailsFinanzas);
					}

					$pdfOc = 'orden_compra_' . $id . '_' . strtolower(Inflector::slug($oc['Proveedor']['nombre'])) . '_' . rand(1, 100) . '.pdf';

					$this->generar_pdf($oc, $pdfOc);

					$this->OrdenCompra->id = $id;
					$this->OrdenCompra->saveField('pdf', $pdfOc);
					$this->OrdenCompra->saveField('estado', 'pago_finanzas');

					# Redirigimos al PDF
					$redirect = sprintf('%ssocio/oc/pdf/%d/%d?access_token=%s', obtener_url_base(), $id, $oc['OrdenCompra']['proveedor_id'], $this->request->query['access_token']);
					$this->redirect($redirect);
				}
			} else {
				$this->redirect(array('action' => 'validate_supplier', $id, '?' => array('access_token' => $this->request->query['access_token'], 'success' => false), 'admin' => false, 'socio' => false, 'prefix' => null));
			}
		} else {

			$qry = array(
				'conditions' => array(
					'OrdenCompra.id' => $id,
					'OrdenCompra.validado_proveedor' => 0,
					'OrdenCompra.estado' => 'validacion_externa'
				),
				'contain' => array(
					'Proveedor',
					'Tienda',
					'VentaDetalleProducto',
					'Moneda'
				)
			);

			if (isset($this->request->query['success'])) {
				unset($qry['conditions']['OrdenCompra.validado_proveedor']);
				unset($qry['conditions']['OrdenCompra.estado']);
			}

			$this->request->data = $this->OrdenCompra->find('first', $qry);
		}

		if (empty($this->request->data)) {
			throw new Exception("La oc #" . $id . " no se encuentra disponible o ya fue procesada.", 404);
			exit;
		}

		$estados = $this->OrdenCompra->estado_proveedor;

		$this->set(compact('estados'));
	}


	/**
	 * Muestra el PDF de la OC al proveedor
	 * @param  [type] $id_oc        [description]
	 * @param  [type] $proveedor_id [description]
	 * @return [type]               [description]
	 */
	public function view_oc_pdf($id_oc, $proveedor_id)
	{
		$this->Auth->allow('view', 'view_oc_pdf');

		if (!isset($this->request->query['access_token']) || !ClassRegistry::init('Token')->validar_token($this->request->query['access_token'], 'proveedor')) {
			throw new Exception("El token de acceso no es válido", 404);
			exit;
		}

		$oc = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id_oc,
				'OrdenCompra.proveedor_id' => $proveedor_id
			),
			'contain' => array(
				'Tienda' => array(
					'fields' => array(
						'Tienda.id', 'Tienda.logo'
					)
				)
			),
			'fields' => array(
				'OrdenCompra.id', 'OrdenCompra.pdf', 'OrdenCompra.nombre_validado', 'OrdenCompra.email_comercial', 'OrdenCompra.tienda_id'
			)
		));

		if (empty($oc)) {
			throw new Exception(sprintf("No es posible obtener la OC solicitada. Póngase en contacto con %s <%s> de %s", $oc['OrdenCompra']['nombre_validado'], $oc['OrdenCompra']['email_comercial'], $oc['Tienda']['nombre']), 504);
			exit;
		}

		$url = obtener_url_base();

		$this->layout = 'backend/socio';

		$this->set(compact('oc', 'url'));
	}


	/**
	 * API REST
	 */

	/**
	 * API REST
	 */

	public function api_view($id)
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
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505,
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		# Obtenemos al/la admin del token
		$tokenInfo = ClassRegistry::init('Token')->obtener_propietario_token_full($this->request->query['token']);

		# Aisalmosids de la /las bodegas asignadas
		$bodegas_admin = Hash::extract($tokenInfo, 'Administrador.Rol.Bodega.{n}.id');

		if (empty($bodegas_admin)) {
			$response = array(
				'code'    => 500,
				'name' => 'error',
				'message' => 'No tienes bodegas asignadas'
			);

			$this->set($response);
			return $this->set('_serialize', array_keys($response));
		}

		# No existe venta
		if (!$this->OrdenCompra->exists($id)) {
			$response = array(
				'code'    => 404,
				'name' => 'error',
				'message' => 'OC no encontrada'
			);

			$this->set($response);
			return $this->set('_serialize', array_keys($response));
		}

		$oc = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id,
				'OrdenCompra.bodega_id IN' => $bodegas_admin,
				'OrdenCompra.estado' => array(
					'espera_recepcion',
					'recepcion_incompleta',
					'espera_dte'
				)
			),
			'contain' => array(
				'Tienda' => array(
					'fields' => array(
						'Tienda.id',
						'Tienda.apiurl_prestashop',
						'Tienda.apikey_prestashop'
					)
				),
				'OrdenComprasVentaDetalleProducto'
			)
		));

		if (empty($oc)) {
			$response = array(
				'code'    => 401,
				'name' => 'error',
				'message' => 'La OC no está disponible para recepcionar'
			);

			$this->set($response);
			return $this->set('_serialize', array_keys($response));
		}

		$this->Prestashop = $this->Components->load('Prestashop');

		# Agregamos las imagenes de prstashop al arreglo
		$this->Prestashop->crearCliente($oc['Tienda']['apiurl_prestashop'], $oc['Tienda']['apikey_prestashop']);

		$productos = array();

		foreach ($oc['OrdenComprasVentaDetalleProducto'] as $iv => $d) {
			// Producto
			$pbodega = ClassRegistry::init('ProductoWarehouse')->find('first', array(
				'conditions' => array(
					'id' => $d['venta_detalle_producto_id']
				)
			));

			$pLocal = ClassRegistry::init('VentaDetalleProducto')->find('first', array(
				'conditions' => array(
					'id' => $d['venta_detalle_producto_id']
				)
			));

			# No recibido
			if (!$d['cantidad_validada_proveedor']) {
				continue;
			}

			$imagen = $this->Prestashop->prestashop_obtener_imagenes_producto($d['venta_detalle_producto_id'], $oc['Tienda']['apiurl_prestashop']);

			$pWarehouse = $pLocal['VentaDetalleProducto'];
			$pWarehouse['sku'] = $pLocal['VentaDetalleProducto']['codigo_proveedor'];
			$pWarehouse['cod_barra'] = null;
			$pWarehouse['permitir_ingreso_sin_barra'] = false;
			$pWarehouse['imagen'] = (isset(Hash::extract($imagen, '{n}[principal=1].url')[0])) ? Hash::extract($imagen, '{n}[principal=1].url')[0] : 'https://dummyimage.com/400x400/f2f2f2/cfcfcf&text=No+photo';

			if (!empty($pbodega)) {
				$pWarehouse['sku'] = $pbodega['ProductoWarehouse']['sku'];
				$pWarehouse['cod_barra'] = ($pbodega['ProductoWarehouse']['cod_barra']) ? $pbodega['ProductoWarehouse']['cod_barra'] : null;
				$pWarehouse['permitir_ingreso_sin_barra'] = ($pbodega['ProductoWarehouse']['permitir_ingreso_sin_barra']) ? true : false;
			}

			$precioBruto = monto_bruto(round($d['precio_unitario'], 0) - ($d['descuento_producto'] / $d['cantidad_validada_proveedor']), null, 0);
			$descuentoOC = round(obtener_descuento_monto($precioBruto, $oc['OrdenCompra']['descuento']), 0);

			# Asignamos a la variable p el contenido de d
			$p = $d;
			$p = array_replace_recursive($p, array(
				'precio_unitario_bruto' => $precioBruto,
				'precio_unitario_final' => $precioBruto - $descuentoOC,
				'ProductoWarehouse' => $pWarehouse
			));

			$productos[] = $p;
		}

		$oc['OrdenComprasVentaDetalleProducto'] = $productos;


		$response = array(
			'code'    => 200,
			'name' => 'success',
			'message' => 'Oc obtenida correctamente',
			'data' => $oc
		);

		$this->set(array(
			'response' => $response,
			'_serialize' => array('response')
		));
	}



	/**
	 * api_reception
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public function api_reception($id)
	{
		# Sólo método POST
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
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505,
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		# No existe venta
		if (!$this->OrdenCompra->exists($id)) {
			$response = array(
				'code'    => 404,
				'name' => 'error',
				'message' => 'Venta no encontrada'
			);

			$this->set($response);
			return $this->set('_serialize', array_keys($response));
		}

		if (empty($this->request->data['Dte']) || empty($this->request->data['ProductoOc'])) {
			$response = array(
				'code'    => 401,
				'name' => 'error',
				'message' => 'Falta DTE o Producto'
			);

			$this->set($response);
			return $this->set('_serialize', array_keys($response));
		}

		# Información del token y propietario
		$tokenInfo = ClassRegistry::init('Token')->obtener_propietario_token_full($this->request->query['token']);

		$oc = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'OrdenComprasVentaDetalleProducto',
				'OrdenCompraFactura',
				'Tienda'
			)
		));

		$log = array();

		$log[] = array(
			'Log' => array(
				'administrador' => 'Recepción oc app',
				'modulo' => 'OrdenCompras',
				'modulo_accion' => json_encode($oc)
			)
		);

		$log[] = array(
			'Log' => array(
				'administrador' => 'Recepción oc app - Request',
				'modulo' => 'OrdenCompras',
				'modulo_accion' => json_encode($this->request->data)
			)
		);

		if ($oc['OrdenCompra']['estado'] == 'recepcion_completa') {
			$log[] = array(
				'Log' => array(
					'administrador' => 'Recepción oc app - Ya recepcionada',
					'modulo' => 'OrdenCompras',
					'modulo_accion' => json_encode($oc)
				)
			);

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);

			$response = array(
				'code'    => 401,
				'name' => 'error',
				'message' => 'Oc ya fue recepcionada'
			);

			$this->set($response);
			return $this->set('_serialize', array_keys($response));
		}

		$productosRecepcionar = array();

		foreach ($oc['OrdenComprasVentaDetalleProducto'] as $ioc => $ocp) {
			$oc['OrdenComprasVentaDetalleProducto'][$ioc]['total_neto'] = $ocp['total_neto'];

			foreach ($this->request->data['ProductoOc'] as $ip => $p) {
				if ($p['id_detalle'] != $ocp['id'])
					continue;

				$log[] = array(
					'Log' => array(
						'administrador' => 'Recepción oc app - Producto',
						'modulo' => 'OrdenCompras',
						'modulo_accion' => json_encode($p) . ' ' . json_encode($ocp)
					)
				);

				# Calcula la cantidad  de productos que faltan por recibir.
				$cantidadFaltante      = $ocp['cantidad_validada_proveedor'] - $ocp['cantidad_recibida'];
				$cantidadRecibidaAhora = $p['cantidad_recibida'];

				if (!$cantidadFaltante || !$cantidadRecibidaAhora) {
					continue;
				}

				# La cantidad recibida es mayor a la permitida
				if ($cantidadRecibidaAhora > $cantidadFaltante) {
					$response = array(
						'code'    => 401,
						'name' => 'error',
						'message' => sprintf('Producto #%d: La cantidad recepcionada es mayor a la permitida', $ocp['id'])
					);

					ClassRegistry::init('Log')->create();
					ClassRegistry::init('Log')->saveMany($log);

					$this->set($response);
					return $this->set('_serialize', array_keys($response));
				}

				$precio_compra_oc = round($ocp['precio_unitario'] - ($ocp['descuento_producto'] / $ocp['cantidad_validada_proveedor']), 0);

				$bodega_id = ($oc['OrdenCompra']['bodega_id']) ? $oc['OrdenCompra']['bodega_id'] : ClassRegistry::init('Bodega')->obtener_bodega_principal()['Bodega']['id'];

				$productosRecepcionar[] = array(
					'id' => $p['id_detalle'],
					'cantidad_recibida_total' => ($cantidadRecibidaAhora + $ocp['cantidad_recibida']),
					'cantidad_recibida_ahora' => $cantidadRecibidaAhora,
					'bodega_id' => $bodega_id,
					'producto_id' => $ocp['venta_detalle_producto_id'],
					'precio_compra' => $precio_compra_oc,
					'oc_id' => $id,
					'diferencia_precio' => $p['error_de_precio']
				);

				$oc['OrdenComprasVentaDetalleProducto'][$ioc]['total_neto'] = ($precio_compra_oc * ($cantidadRecibidaAhora + $ocp['cantidad_recibida']));
			}
		}

		$log[] = array(
			'Log' => array(
				'administrador' => 'Recepción oc app - Recepcionar',
				'modulo' => 'OrdenCompras',
				'modulo_accion' => json_encode($productosRecepcionar)
			)
		);

		# Agregamos a la bodega las unidades recepcionadas
		foreach ($productosRecepcionar as $ip => $p) {
			# Actualiamos la cantidad recibida
			$detalle = array(
				'id' => $p['id'],
				'cantidad_recibida' => $p['cantidad_recibida_total'],
				'diff_precio_recepcion' => $p['diferencia_precio']
			);

			# Guardamos
			ClassRegistry::init('OrdenComprasVentaDetalleProducto')->save($detalle);

			if (ClassRegistry::init('Bodega')->crearEntradaBodega($p['producto_id'], $p['bodega_id'], $p['cantidad_recibida_ahora'], $p['precio_compra'], 'OC', $p['oc_id'], null, null, $tokenInfo['Administrador']['email'])) {
				$log[] = array(
					'Log' => array(
						'administrador' => 'Recepción oc app - Agregar a inventario',
						'modulo' => 'OrdenCompras',
						'modulo_accion' => json_encode($p)
					)
				);
			} else {
				$log[] = array(
					'Log' => array(
						'administrador' => 'Recepción oc app - Error agregar a inventario',
						'modulo' => 'OrdenCompras',
						'modulo_accion' => json_encode($p)
					)
				);
			}
		}

		# Reservamos los productos de las ventas relacionadas a la OC padre
		if (!$oc['OrdenCompra']['oc_manual']) {
			// ! Se mueve metodo al controllador
			// ! Queda en desuzo el metodo en el Modelo
			// ClassRegistry::init('Venta')->reservar_stock_por_oc($id);
			$this->reservar_stock_por_oc($id);
		}

		$ocSave = array(
			'OrdenCompra' => array(
				'id' => $id,
				'estado' => 'recepcion_incompleta',
				'retiro' => 0
			)
		);

		# Guardamos la fecha de la primera recepción
		if (empty($oc['OrdenCompra']['fecha_recibido'])) {
			$ocSave = array_replace_recursive($ocSave, array(
				'OrdenCompra' => array(
					'fecha_recibido' => date('Y-m-d H:i:s')
				)
			));
		}

		# Dtes para descontar saldo
		$dtesDescontar = array();

		$this->request->data['Dte'] = array_unique($this->request->data['Dte']);

		# Guardamos los nuevos dtes
		foreach ($this->request->data['Dte'] as $dte) {
			$emisor   = $this->rutSinDv($oc['OrdenCompra']['rut_empresa']);
			$tipo_dte = $dte['tipo_dte'];
			$folio    = $dte['folio'];
			$receptor = $this->rutSinDv($oc['Tienda']['rut']);
			$id_factura = null;

			# Obtenemos el factura id de los dte ya guardados
			foreach ($oc['OrdenCompraFactura'] as $fact) {
				if ($fact['folio'] == $folio && $fact['tipo_documento'] == $tipo_dte) {
					$id_factura = $fact['id'];
				}
			}

			if (!$id_factura) {
				# Creamos el id antes de setear sus valores
				$id_factura = ClassRegistry::init('OrdenCompraFactura')->crear(array(
					'OrdenCompraFactura' => array(
						'orden_compra_id' => $id,
						'proveedor_id'    => $oc['OrdenCompra']['proveedor_id'],
						'folio' => $folio,
						'tipo_documento' => $tipo_dte
					)
				));
			}

			# DTE a relacionar
			$ocSave['OrdenCompraFactura'][] = array(
				'id' => $id_factura,
				'tipo_documento' => $tipo_dte,
				'folio' => $folio,
				'emisor' => $emisor,
				'receptor' => $receptor,
				'monto_facturado' => round($dte['total'], 0)
			);

			# Dtes que deben descontar saldo
			if ($tipo_dte != 33)
				continue;

			$dtesDescontar[] = array(
				'tipo_dte' => $tipo_dte,
				'folio' => $folio,
				'monto_facturado' => round($dte['total'], 2),
				'proveedor_id' => $oc['OrdenCompra']['proveedor_id'],
				'emisor' => $emisor,
				'receptor' => $receptor
			);
		}

		# Calculamos el total facturado
		$yaFacturado = 0;

		foreach ($oc['OrdenCompraFactura'] as $factura) {
			if ($factura['tipo_documento'] != 33)
				continue;

			$yaFacturado = $yaFacturado + $factura['monto_facturado'];
		}

		$total_oc = 0;

		foreach ($oc['OrdenComprasVentaDetalleProducto'] as $iocp => $p) {
			if ($p['cantidad_validada_proveedor'] == 0)
				continue;

			$total_oc = $total_oc + monto_bruto($p['total_neto']);
		}

		$total_oc_min = $total_oc - 100;
		$total_facturado = array_sum(Hash::extract($ocSave['OrdenCompraFactura'], '{n}[tipo_documento=33].monto_facturado')) + $yaFacturado;

		# Facturado
		$facturado_completo = false;

		if ($total_facturado >= $total_oc_min) {
			$facturado_completo = true;
		}

		# Items recibidos
		$total_recibidos = array_sum(Hash::extract($productosRecepcionar, '{n}.cantidad_recibida_total')) + array_sum(Hash::extract($oc['OrdenComprasVentaDetalleProducto'], '{n}.cantidad_recibida'));
		$total_validados_proveedor = array_sum(Hash::extract($oc['OrdenComprasVentaDetalleProducto'], '{n}.cantidad_validada_proveedor'));

		# OC queda en estado de espera de factura
		if ($total_recibidos == $total_validados_proveedor && !$facturado_completo) {
			$ocSave['OrdenCompra']['estado'] = 'espera_dte';
		} elseif ($total_recibidos == $total_validados_proveedor && $facturado_completo) {
			$ocSave['OrdenCompra']['estado'] = 'recepcion_completa';
		}

		$ocSave['OrdenCompraHistorico'] = array(
			array(
				'estado' => $ocSave['OrdenCompra']['estado'],
				'responsable' => $tokenInfo['Administrador']['email'],
				'evidencia' => json_encode($ocSave)
			)
		);

		$log[] = array(
			'Log' => array(
				'administrador' => 'Recepción oc app - Guardar oc',
				'modulo' => 'OrdenCompras',
				'modulo_accion' => json_encode($ocSave)
			)
		);

		# Al guardar relacionamos todas las facturas a los pagos que existan para ésta OC
		if ($this->OrdenCompra->saveAll($ocSave)) {

			# Pagos relacionados
			$pagos = ClassRegistry::init('Pago')->find('all', array(
				'conditions' => array(
					'Pago.orden_compra_id' => $id,
				),
				'fields' => array(
					'Pago.id', 'Pago.pagado'
				)
			));

			# Facturas recien creadas
			$facturas = ClassRegistry::init('OrdenCompraFactura')->find('all', array(
				'conditions' => array(
					'OrdenCompraFactura.orden_compra_id' => $id,
					'OrdenCompraFactura.tipo_documento' => 33 // Fatura
				),
				'contain' => array(
					'Pago' => array(
						'fields' => array(
							'Pago.id'
						)
					)
				),
				'fields' => array(
					'OrdenCompraFactura.id'
				),
			));

			# Relacionamos pagos facturas
			foreach ($pagos as $ip => $p) {
				foreach ($facturas as $if => $f) {

					# si tiene pago/s relaconados continua el ciclo
					foreach ($f['Pago'] as $ifp => $fp) {
						if ($fp['id'] == $p['Pago']['id']) {
							continue;
						}
					}

					$pagos[$ip]['OrdenCompraFactura'][$if] = array(
						'factura_id' => $f['OrdenCompraFactura']['id']
					);
				}
			}

			# Guardamos para que valide los pagos y faturas
			if (!empty($pagos)) {
				ClassRegistry::init('Pago')->saveMany($pagos, array('deep' => true));

				# Notificamos los pagos si corresponde
				$pagosController = new PagosController;

				foreach ($pagos as $ip => $p) {
					$pagosController->guardarEmailPagoFactura($p['Pago']['id']);
				}
			}
		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		$response = array(
			'code'    => 200,
			'name' => 'success',
			'message' => 'Oc recepcionada como ' . $ocSave['OrdenCompra']['estado'],
			'data' => array()
		);

		$this->set(array(
			'response' => $response,
			'_serialize' => array('response')
		));
	}


	/**
	 * api_zonificar
	 *
	 * Retorna las OC que estan pendiente de zonificar
	 * 
	 * @param  mixed $bodega_id
	 * @return void
	 */
	public function api_zonificar($bodega_id)
	{
		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502,
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505,
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		$tokenInfo = ClassRegistry::init('Token')->obtener_propietario_token_full($this->request->query['token']);

		# Tomamos las bodegas del rol
		$bodegas_id = Hash::extract($tokenInfo, 'Administrador.Rol.Bodega.{n}.id');

		$ocs = $this->OrdenCompra->find('all', array(
			'conditions' => array(
				'OrdenCompra.estado IN' => array(
					'recepcion_completa',
					'recepcion_incompleta',
					'espera_dte'
				),
				'OrdenCompra.fecha_recibido <>' => '',
				'OrdenCompra.bodega_id IN' => $bodegas_id
			),
			'joins' => array(
				array(
					'table' => 'orden_compras_venta_detalle_productos',
					'alias' => 'OrdenComprasVentaDetalleProducto',
					'type'  => 'inner',
					'conditions' => array(
						'OrdenComprasVentaDetalleProducto.orden_compra_id = OrdenCompra.id',
						'OrdenComprasVentaDetalleProducto.zonificado' => 0,
						'OrdenComprasVentaDetalleProducto.cantidad_zonificada < OrdenComprasVentaDetalleProducto.cantidad_recibida'
					)
				)
			),
			'contain' => array(
				'Tienda' => array(
					'fields' => array(
						'Tienda.id',
						'Tienda.apiurl_prestashop',
						'Tienda.apikey_prestashop'
					)
				),
				'OrdenComprasVentaDetalleProducto'
			),
			'order' => array(
				'OrdenCompra.fecha_recibido' => 'ASC'
			),
			'limit' => 50,
			'group' => array(
				'OrdenCompra.id'
			)
		));


		if (empty($ocs)) {
			$response = array(
				'code'    => 401,
				'name' => 'error',
				'message' => 'No hay Ocs disponibles para zonificar'
			);

			throw new CakeException($response);
		}


		// $this->Prestashop = $this->Components->load('Prestashop');
		# Agregamos las imagenes de prstashop al arreglo
		// $this->Prestashop->crearCliente($ocs[0]['Tienda']['apiurl_prestashop'], $ocs[0]['Tienda']['apikey_prestashop']);


		foreach ($ocs as $i => $oc) {
			$productos = array();

			foreach ($oc['OrdenComprasVentaDetalleProducto'] as $iv => $d) {
				// Producto
				$pbodega = ClassRegistry::init('ProductoWarehouse')->find('first', array(
					'conditions' => array(
						'id' => $d['venta_detalle_producto_id']
					)
				));

				$pLocal = ClassRegistry::init('VentaDetalleProducto')->find('first', array(
					'conditions' => array(
						'id' => $d['venta_detalle_producto_id']
					)
				));

				# No recibido
				if (!$d['cantidad_recibida']) {
					continue;
				}

				// $imagen = $this->Prestashop->prestashop_obtener_imagenes_producto($d['venta_detalle_producto_id'], $ocs[$i]['Tienda']['apiurl_prestashop']);

				$pWarehouse = $pLocal['VentaDetalleProducto'];
				$pWarehouse['sku'] = $pLocal['VentaDetalleProducto']['codigo_proveedor'];
				$pWarehouse['cod_barra'] = null;
				$pWarehouse['permitir_ingreso_sin_barra'] = false;
				// $pWarehouse['imagen'] = (isset(Hash::extract($imagen, '{n}[principal=1].url')[0])) ? Hash::extract($imagen, '{n}[principal=1].url')[0] : 'https://dummyimage.com/400x400/f2f2f2/cfcfcf&text=No+photo';
				$pWarehouse['imagen'] = 'https://dummyimage.com/400x400/f2f2f2/cfcfcf&text=No+photo';

				if (!empty($pbodega)) {
					$pWarehouse['sku'] = $pbodega['ProductoWarehouse']['sku'];
					$pWarehouse['cod_barra'] = ($pbodega['ProductoWarehouse']['cod_barra']) ? $pbodega['ProductoWarehouse']['cod_barra'] : null;
					$pWarehouse['permitir_ingreso_sin_barra'] = ($pbodega['ProductoWarehouse']['permitir_ingreso_sin_barra']) ? true : false;
				}

				$precioBruto = monto_bruto(round($d['precio_unitario'], 0) - ($d['descuento_producto'] / $d['cantidad_validada_proveedor']), null, 0);
				$descuentoOC = round(obtener_descuento_monto($precioBruto, $ocs[$i]['OrdenCompra']['descuento']), 0);

				# Asignamos a la variable p el contenido de d
				$p = $d;
				$p = array_replace_recursive($p, array(
					'precio_unitario_bruto' => $precioBruto,
					'precio_unitario_final' => $precioBruto - $descuentoOC,
					'ProductoWarehouse' => $pWarehouse
				));

				$productos[] = $p;
			}

			$ocs[$i]['OrdenComprasVentaDetalleProducto'] = $productos;
		}

		$response = array(
			'code'    => 200,
			'name' => 'success',
			'message' => 'Ocs obtenida correctamente',
			'data' => $ocs
		);

		$this->set(array(
			'response' => $response,
			'_serialize' => array('response')
		));
	}
		
	/**
	 * api_detalle_zonificar
	 * 
	 * Se encarga de actualizar una linea de producto de la oc, según 
	 * la cantidad zonificada en warehouse
	 *
	 * @return void
	 */
	public function api_detalle_zonificar($id)
	{
		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 404,
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 400,
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		if (!isset($this->request->data['cantidad_zonificada'])) {
			$response = array(
				'code'    => 400,
				'name' => 'error',
				'message' => 'cantidad_zonificada es requerido'
			);

			throw new CakeException($response);
		}

		$ocp = ClassRegistry::init('OrdenComprasVentaDetalleProducto')->find('first', array(
			'conditions' => array(
				'OrdenComprasVentaDetalleProducto.id' => $id
			),
			'joins' => array(
				array(
					'table' => 'orden_compras',
					'alias' => 'OrdenCompra',
					'type'  => 'inner',
					'conditions' => array(
						'OrdenCompra.id = OrdenComprasVentaDetalleProducto.orden_compra_id',
						'OrdenCompra.estado IN' => array(
							'recepcion_completa',
							'recepcion_incompleta',
							'espera_dte'
						)
					)
				)
			)
		));

		if (empty($ocp)) {
			$response = array(
				'code'    => 404,
				'name' => 'error',
				'message' => 'Detalle oc no encontrado o la oc no está disponible para zonificar'
			);

			throw new CakeException($response);
		}

		$cantidad_pendiente_zonificar = $ocp['OrdenComprasVentaDetalleProducto']['cantidad_recibida'] - $ocp['OrdenComprasVentaDetalleProducto']['cantidad_zonificada'];

		# Ya esta zonificada
		if ($ocp['OrdenComprasVentaDetalleProducto']['zonificado']) {
			$response = array(
				'code'    => 400,
				'name' => 'error',
				'message' => sprintf('%s ya fue zonificado - Detalle id #%d', $ocp['OrdenComprasVentaDetalleProducto']['descripcion'], $id)
			);

			throw new CakeException($response);
		}

		# Se intenta zonificar mas unidades
		if ($this->request->data['cantidad_zonificada'] > $cantidad_pendiente_zonificar) {
			$response = array(
				'code'    => 400,
				'name' => 'error',
				'message' => sprintf('La cantidad a zonificar es mayor a la cantidad pendiente: Pendiente %d', $cantidad_pendiente_zonificar)
			);

			throw new CakeException($response);
		}

		# Menos a 0
		if ($this->request->data['cantidad_zonificada'] <= 0) {
			$response = array(
				'code'    => 400,
				'name' => 'error',
				'message' => 'La cantidad a zonificar debe ser mayor 0'
			);

			throw new CakeException($response);
		}

		$cantidad_zonificar = $ocp['OrdenComprasVentaDetalleProducto']['cantidad_zonificada'] + $this->request->data['cantidad_zonificada'];

		if ($ocp['OrdenComprasVentaDetalleProducto']['cantidad_recibida'] == $cantidad_zonificar) {
			$ocp['OrdenComprasVentaDetalleProducto']['zonificado'] = 1;
		}

		$ocp['OrdenComprasVentaDetalleProducto']['cantidad_zonificada'] = $cantidad_zonificar;

		if (!ClassRegistry::init('OrdenComprasVentaDetalleProducto')->save($ocp)) {
			$response = array(
				'code'    => 500,
				'name' => 'error',
				'message' => 'No fue posible actualizar el detalle'
			);

			throw new CakeException($response);
		}

		$response = array(
			'code'    => 200,
			'name' => 'success',
			'message' => sprintf('%s zonificado con éxito', $ocp['OrdenComprasVentaDetalleProducto']['descripcion']),
			'data' => $ocp
		);

		$this->set(array(
			'response' => $response,
			'_serialize' => array('response')
		));
	}
	
	/**
	 * api_obtener_oc_validacion_externa
	 * 
	 * Obtiene las OCs que se encuentran disponibles para consultar
	 * dado el token del proveedor. Método usado para los proveedores que tiene activa la opción de oc_via_api
	 *
	 * @return void
	 */
	public function api_obtener_oc_validacion_externa()
	{
		# Existe token
		if (!isset($this->request->query['token'])) 
		{
			return $this->api_response(404, 'Token requerido');
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'], 'proveedor')) 
		{
			return $this->api_response(401, 'Token de sesión expirado o invalido');
		}

		# Obtenemos propietario
		$proveedor = ClassRegistry::init('Token')->obtener_propietario_token_full($this->request->query['token']);

		if (empty($proveedor['Proveedor']['id']))
		{
			return $this->api_response(401, 'Token de sesión no pertenece a proveedor');
		}
		
		$ocs = $this->OrdenCompra->find('all', array(
			'conditions' => array(
				'OrdenCompra.validado_proveedor' => 0,
				'OrdenCompra.estado' => 'validacion_externa',
				'OrdenCompra.consultada' => 0,
				'OrdenCompra.proveedor_id' => $proveedor['Proveedor']['id'],
			),
			'contain' => array(
				'Proveedor',
				'Tienda',
				'VentaDetalleProducto',
				'Moneda',
				'Bodega'
			)
		));
		
		$result = [];

		foreach ($ocs as $oc)
		{
			$oc_arr = [
				'OrdenCompra' => [
					'id' => $oc['OrdenCompra']['id'],
					'codigo' => sprintf('OC%S-%d', ($oc['OrdenCompra']['tipo_orden'] == 'en_verde') ? 'V' : 'I' , $oc['OrdenCompra']['id']),
					'Solicitante' => [
						'nombre' => $oc['Tienda']['nombre_fantasia'],
						'rut' => $oc['Tienda']['rut'],
						'direccion' => $oc['Tienda']['direccion'],
						'giro' => $oc['Tienda']['giro'],
						'fono' => $oc['Tienda']['fono'],
						'whatsapp' => $oc['Tienda']['whatsapp_numero']
					],
					'Proveedor' => [
						'nombre' => $oc['Proveedor']['nombre'],
						'rut' => $oc['OrdenCompra']['rut_empresa'],
						'razon_social' => $oc['OrdenCompra']['razon_social_empresa'],
						'giro' => $oc['OrdenCompra']['giro_empresa'],
						'nombre_contacto' => $oc['OrdenCompra']['nombre_contacto_empresa'],
						'email_contacto' => $oc['OrdenCompra']['email_contacto_empresa'],
						'fono_contacto' => $oc['OrdenCompra']['fono_contacto_empresa'],
						'direccion' => $oc['OrdenCompra']['direccion_comercial_empresa']
					],
					'Condiciones' => [
						'fecha_creacion' => $oc['OrdenCompra']['created'],
						'medio_de_pago' => $oc['Moneda']['nombre'],
						'tipo_oc' => $oc['OrdenCompra']['tipo_orden'],
						'vendedor' => $oc['OrdenCompra']['vendedor'],
					],
					'Entrega' => [
						'tipo_entrega' => $oc['OrdenCompra']['tipo_entrega'],
						'receptor' => $oc['OrdenCompra']['receptor_informado'],
						'bodega' => $oc['Bodega']['nombre'],
						'direccion' => $oc['Bodega']['direccion'],
						'fono' => $oc['Bodega']['fono'],
						'horario_atencion' => $oc['Bodega']['horario_atencion'],
						'informacion_adicional' => $oc['OrdenCompra']['informacion_entrega'],
					],
					'Productos' => [],
					'Totales' => [
						'total_neto' => (int) $oc['OrdenCompra']['total_neto'],
						'descuento_monto' => (int) $oc['OrdenCompra']['descuento_monto'],
						'iva' => (int) $oc['OrdenCompra']['iva'],
						'total' => (int) $oc['OrdenCompra']['total'],
					],
				]
			];
			
			foreach($oc['VentaDetalleProducto'] as $p)
			{
				$oc_arr['OrdenCompra']['Productos'][] = [
					'id' => $p['id'],
					'codigo_proveedor' => $p['codigo_proveedor'],
					'referencia' => $p['referencia'],
					'nombre' => $p['nombre'],
					'cantidad_solicitada' => $p['OrdenComprasVentaDetalleProducto']['cantidad'],
					'precio_unitario' => (int) $p['OrdenComprasVentaDetalleProducto']['precio_unitario'],
					'descuento_unitario' => (int) ($p['OrdenComprasVentaDetalleProducto']['descuento_producto'] / $p['OrdenComprasVentaDetalleProducto']['cantidad']),
					'total_neto' => (int) $p['OrdenComprasVentaDetalleProducto']['total_neto']
				];
			}

			$result[] = $oc_arr;
		}

		return $this->api_response(200, sprintf('Se obtuvieron %d órdenes de compra', count($ocs)), $result);

	}

	
	/**
	 * api_actualizar_oc_validacion_externa
	 * 
	 * Actualiza el estado de las OC a consultada y envía al proveedor la OC para la validación manual.
	 * 
	 * disponible unicamente para los proveeedores que tengan activa la opción de oc_via_api
	 *
	 * @return void
	 */
	public function api_actualizar_oc_validacion_externa()
	{	
		# Existe body
		if (empty($this->request->data))
		{	
			return $this->api_response(500, 'Debe incluir al menos una oc en el cuerpo del request');
		}

		# Existe token
		if (!isset($this->request->query['token'])) 
		{
			return $this->api_response(404, 'Token requerido');
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'], 'proveedor')) 
		{
			return $this->api_response(401, 'Token de sesión expirado o invalido');
		}

		# Obtenemos propietario
		$proveedor = ClassRegistry::init('Token')->obtener_propietario_token_full($this->request->query['token']);

		if (empty($proveedor['Proveedor']['id']))
		{
			return $this->api_response(401, 'Token de sesión no pertenece a proveedor');
		}
		
		$ocs = $this->OrdenCompra->find('all', array(
			'conditions' => array(
				'OrdenCompra.validado_proveedor' => 0,
				'OrdenCompra.estado' => 'validacion_externa',
				'OrdenCompra.consultada' => 0,
				'OrdenCompra.proveedor_id' => $proveedor['Proveedor']['id'],
				'OrdenCompra.id' => $this->request->data
			),
			'fields' => array(
				'OrdenCompra.id',
				'OrdenCompra.consultada',
				'OrdenCompra.fecha_consultada'
			),
			'joins' => array(
				array(
					'table' => 'proveedores',
					'alias' => 'Proveedor',
					'type'  => 'inner',
					'conditions' => array(
						'OrdenCompra.proveedor_id = Proveedor.id',
						'Proveedor.oc_via_api' => 1
					)
				)
			)
		));
		
		$result = [];

		foreach ($ocs as $i => $oc)
		{	
			# Enviamos el email correspondiente a esta OC para validación de proveedor.
			$this->guardarEmailValidado($oc['OrdenCompra']['id']);
			
			# Indicamos la OC como consultada
			$ocs[$i]['OrdenCompra']['consultada'] = 1;
			$ocs[$i]['OrdenCompra']['fecha_consultada'] = date('Y-m-d H:i:s');
		}
		
		if ($this->OrdenCompra->saveMany($ocs))
		{
			return $this->api_response(200, 'Oc actualizadas con éxito.', $ocs);
		}
		else
		{
			return $this->api_response(500, 'No fue posible actualizar las OCs.', $ocs);
		}

	}
	
	/**
	 * reservar_stock_por_oc
	 * 
	 *
	 * @param  mixed $id_oc
	 * @return void
	 */
	public function reservar_stock_por_oc($id_oc)
	{
		$ocVentas = ClassRegistry::init('OrdenCompra')->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id_oc
			),
			'contain' => array(
				'Venta' => array(
					'VentaDetalle' => array(
						'fields' => array(
							'VentaDetalle.id'
						)
					),
					'fields' => array(
						'Venta.id'
					),
					'order' => array('Venta.fecha_venta' => 'ASC')
				)
			),
			'fields' => array(
				'OrdenCompra.id'
			)
		));

		if (empty($ocVentas['Venta'])) {
			return;
		}

		foreach ($ocVentas['Venta'] as $iv => $venta) {
			foreach ($venta['VentaDetalle'] as $id => $d) {

				$reservado = ClassRegistry::init('Venta')->reservar_stock_producto($d['id']);

				if ($reservado > 0) {
					// * Se sigue misma logica de instanciar metodo que hay en metodo "reservar_stock_producto"
					$this->WarehouseNodriza->procesar_embalajes($venta['id']);
				}
			}
		}

		return;
	}

	/**
	 * RecorrerProveedor
	 * Se crean OCs Automaticas a los proveedores que tienen frecuencia programada
	 * @return void
	 */
	public function RecorrerProveedor()
	{
		$respuesta 		= [];
		$day 			= strtolower(date("l"));
		$proveedores 	= array_unique(Hash::extract(
			ClassRegistry::init('Proveedor')->find(
				'all',
				array(
					'fields'		=> ['Proveedor.id'],
					'conditions' 	=> array(
						'Proveedor.permitir_generar_oc'	=> true,
						'Proveedor.activo'	=> true,
						"Proveedor.$day"	=> true,
					),
					'joins' 		=> array(
						array(
							'table' => 'frecuencia_generar_oc',
							'alias' => 'FrecuenciaGenerarOC',
							'type'  => 'inner',
							'conditions' => array(
								'FrecuenciaGenerarOC.proveedor_id = Proveedor.id',
								// 'FrecuenciaGenerarOC.hora' =>  "14:00:00",
								'FrecuenciaGenerarOC.hora' =>  date('H:i') . ":00"
							)
						)
					),
				)
			),
			"{n}.Proveedor.id"
		));

		if ($proveedores) {
			$respuesta = $this->CrearOCAutomaticas($proveedores);
		}

		return $respuesta;
	}

	/**
	 * CrearOCAutomaticas
	 *
	 * Se envian los ids de los proveedores en un array
	 * @param  mixed $proveedores_id
	 * @return void
	 */
	public function CrearOCAutomaticas(array $proveedores_id)
	{

		// * Obtenemos bodega para buscar ventas
		$bodegas 		= ClassRegistry::init('Bodega')->obtener_bodegas();
		$venta_bodega 	= [];
		$respuesta 		= false;
		$OCCreadas	 	= [];
		$log			= [];
		$tienda 		= ClassRegistry::init('Tienda')->find('first', [
			'fields' => [
				'Tienda.id',
				'Tienda.nombre',
				'Tienda.administrador_id'
			],
			'contain' => [
				'Administrador' => [
					'nombre',
					'email'
				]
			],
			'conditions' => ['Tienda.principal' => true]
		]);

		foreach ($bodegas as $bodega_id => $nombre) {

			// * La Query busca traer las ventas que tengan productos asociados a un proveedor que permita genenear OC autamaticas
			// * Además la Venta debe cumplir con las condiciones para generar OC
			$venta_bodega	 = $this->OrdenCompra->Venta->find('all', array(
				'fields' => array(
					'vd_1.id as venta_detalles_id',
					'vd_1.venta_id as venta_id',
					'vd_1.venta_detalle_producto_id as producto_id',
					'rpvdp.id as proveedores_venta_detalle_productos_id',
					'rpvdp.venta_detalle_producto_id as producto_id',
					'rpvdp.proveedor_id as proveedor_id',
					"((vd_1.cantidad - vd_1.cantidad_anulada - vd_1.cantidad_entregada) - (select ifnull(sum(rvdr_1.cantidad_reservada), 0)
						from rp_venta_detalles_reservas rvdr_1
						where rvdr_1.venta_detalle_id = vd_1.id)) cantidad",
				),
				'joins' => array(
					array(
						'table' => 'rp_venta_estados',
						'alias' => 'venta_estados',
						'type' 	=> 'INNER',
						'conditions' => array(
							'venta_estados.id = Venta.venta_estado_id',
							'venta_estados.permitir_oc = 1'
						)
					),
					array(
						'table' => 'rp_venta_estado_categorias',
						'alias' => 'venta_estados_cat',
						'type' 	=> 'INNER',
						'conditions' => array(
							'venta_estados_cat.id = venta_estados.venta_estado_categoria_id',
							'venta_estados_cat.rechazo = 0',
							'venta_estados_cat.cancelado = 0',
							'venta_estados_cat.final = 0'
						)
					),
					array(
						'table' => 'rp_venta_detalles',
						'alias' => 'vd_1',
						'type' 	=> 'INNER',
						'conditions' => array(
							'vd_1.venta_id = Venta.id'
						),
					),
					array(
						'table' => 'rp_proveedores_venta_detalle_productos',
						'alias' => 'rpvdp',
						'type' 	=> 'INNER',
						'conditions' => array(
							'vd_1.venta_detalle_producto_id = rpvdp.venta_detalle_producto_id'
						)
					),
					array(
						'table' => 'rp_proveedores',
						'alias' => 'rp_1',
						'type' 	=> 'INNER',
						'conditions' => array(
							'rpvdp.proveedor_id = rp_1.id',
							'rp_1.id'	=> $proveedores_id
						)
					),
					array(
						'table' => 'rp_venta_detalles_reservas',
						'alias' => 'rvdr',
						'type' 	=> 'LEFT',
						'conditions' => array(
							'rvdr.venta_detalle_id = vd_1.id',
						)
					),
				),
				'conditions' => array(
					"Venta.id in (SELECT Venta.id
						FROM rp_ventas AS Venta
								 INNER JOIN rp_venta_estados AS venta_estados
											ON (venta_estados.id = Venta.venta_estado_id AND venta_estados.permitir_oc = 1)
								 INNER JOIN rp_venta_estado_categorias AS venta_estados_cat
											ON (venta_estados_cat.id = venta_estados.venta_estado_categoria_id AND
												venta_estados_cat.rechazo = 0 AND venta_estados_cat.cancelado = 0 AND
												venta_estados_cat.final = 0)
								 INNER JOIN rp_venta_detalles rvd ON Venta.id = rvd.venta_id
						WHERE Venta.fecha_venta > ADDDATE(NOW(), INTERVAL -2 Month)
						having ((select Sum(CAST(detalle.cantidad as signed) - CAST(detalle.cantidad_anulada as signed) -
											CAST(detalle.cantidad_entregada as signed) -
											CAST(detalle.cantidad_reservada as signed))
								 from rp_venta_detalles as detalle
								 where detalle.id = rvd.id) - (SELECT ifnull(Sum(Reserva.cantidad_reservada), 0)
															   from rp_venta_detalles_reservas as Reserva
															   where Reserva.venta_detalle_id = rvd.id)) >
							   ((SELECT ifnull(Sum(StockProducto.cantidad),0)
								 from rp_bodegas_venta_detalle_productos as StockProducto
								 where StockProducto.venta_detalle_producto_id = rvd.venta_detalle_producto_id
								   and tipo <> 'GT') - (SELECT ifnull(Sum(Reserva.cantidad_reservada), 0)
														from rp_venta_detalles_reservas as Reserva
														where Reserva.venta_detalle_producto_id = rvd.venta_detalle_producto_id))
						   and ((select Sum(CAST(detalle.cantidad as signed) - CAST(detalle.cantidad_anulada as signed) -
											CAST(detalle.cantidad_entregada as signed) -
											CAST(detalle.cantidad_reservada as signed))
								 from rp_venta_detalles as detalle
								 where detalle.id = rvd.id) - (SELECT ifnull(Sum(Reserva.cantidad_reservada), 0)
															   from rp_venta_detalles_reservas as Reserva
															   where Reserva.venta_detalle_id = rvd.id)) > 0
						ORDER BY Venta.prioritario DESC, Venta.fecha_venta DESC)",
					"0 = (select count(*) from rp_orden_compras_ventas orv where orv.venta_id = Venta.id)",
					'Venta.bodega_id' 			=> $bodega_id,
					'rp_1.permitir_generar_oc'	=> true,
					'rp_1.activo'				=> true,
				),
				'order' 	=> array('Venta.prioritario' => 'DESC', 'Venta.fecha_venta' => 'DESC'),
				'having' 	=> ['cantidad > 0'],
				'group'		=> ['`vd_1`.`id`']
			));

			// * Extraen los identificadores de los proveedores para crear oc por cada proveedor

			$proveedores = array_unique(Hash::extract($venta_bodega, '{n}.rpvdp.proveedor_id'));

			// * Se recorren los proveedores y se harán OC solo con sus productos asociados
			foreach ($proveedores as $proveedor_id) {

				$OC = [];

				// * Obtenemos los productos de ese proveedor
				$productos__oc = array_unique(Hash::extract($venta_bodega, "{n}.rpvdp[proveedor_id={$proveedor_id}].producto_id"));

				// * Obtenemos toda la informacion para generar una OC
				$proveedor = ClassRegistry::init('Proveedor')->find('first', array(
					'joins' => array(
						array(
							'table' => 'proveedores_venta_detalle_productos',
							'alias' => 'ProveedoresVentaDetalleProducto',
							'type'  => 'inner',
							'conditions' => array(
								'ProveedoresVentaDetalleProducto.proveedor_id = Proveedor.id',
								'ProveedoresVentaDetalleProducto.venta_detalle_producto_id' => $productos__oc
							)
						)
					),
					'contain' => array(
						'VentaDetalleProducto' => array(
							'conditions' => array(
								'VentaDetalleProducto.id' => $productos__oc
							),
							'Marca' => array(
								'PrecioEspecificoMarca' => array(
									'conditions' => array(
										'PrecioEspecificoMarca.activo' => 1,
										'OR' => array(
											'PrecioEspecificoMarca.descuento_infinito' => 1,
											'AND' => array(
												array('PrecioEspecificoMarca.fecha_inicio <=' => date('Y-m-d')),
												array('PrecioEspecificoMarca.fecha_termino >=' => date('Y-m-d')),
											)
										)
									),
									'order' => array(
										'PrecioEspecificoMarca.id' => 'DESC'
									)
								)
							),
							'PrecioEspecificoProducto' => array(
								'conditions' => array(
									'PrecioEspecificoProducto.activo' => 1,
									'OR' => array(
										'PrecioEspecificoProducto.descuento_infinito' => 1,
										'AND' => array(
											array('PrecioEspecificoProducto.fecha_inicio <=' => date('Y-m-d')),
											array('PrecioEspecificoProducto.fecha_termino >=' => date('Y-m-d')),
										)
									)
								),
								'order' => array(
									'PrecioEspecificoProducto.id' => 'DESC'
								)
							)
						),
						'ReglasGenerarOC',
						'TipoEntregaProveedorOC' => [
							'conditions' =>
							[
								'TipoEntregaProveedorOC.tienda_id' => $tienda['Tienda']['id'],
								'TipoEntregaProveedorOC.bodega_id' => $bodega_id,
							]
						],
						'Moneda'
					),
					'conditions' => ["Proveedor.id" => $proveedor_id]
				));

				$ventas_id = [];

				// * Buscamos las venta_id asociadas a los productos relacionados al proveedor

				foreach ($productos__oc as $producto_id) {
					$ventas_id = array_unique(array_merge($ventas_id, array_unique(Hash::extract($venta_bodega, "{n}.vd_1[producto_id=$producto_id].venta_id"))));
				}

				// * Formatiamos las ventas para asociarlas a la OC

				foreach ($ventas_id as $id) {

					$OC['Venta'][] = [
						'venta_id' => $id
					];
				}

				// * En caso de que no haya tipo de entrega definido quedara por defecto retiro
				$OC['OrdenCompra'] = [
					"administrador_id" 			=> $tienda['Tienda']['administrador_id'],
					"tienda_id" 				=> $tienda['Tienda']['id'],
					"proveedor_id" 				=> $proveedor['Proveedor']['id'],
					"estado" 					=> "asignacion_metodo_pago",
					"rut_empresa" 				=> $proveedor['Proveedor']['rut_empresa'],
					"razon_social_empresa" 		=> $proveedor['Proveedor']['nombre'],
					"giro_empresa" 				=> $proveedor['Proveedor']['giro'],
					"nombre_contacto_empresa" 	=> $proveedor['Proveedor']['nombre_encargado'],
					"email_contacto_empresa" 	=> $proveedor['Proveedor']['email_contacto'],
					"fono_contacto_empresa" 	=> $proveedor['Proveedor']['fono_contacto'],
					"direccion_empresa" 		=> $proveedor['Proveedor']['direccion'],
					"fecha"	 					=> date('Y-m-d'),
					"vendedor" 					=> $tienda['Administrador']['nombre'],
					"tipo_entrega" 				=> $proveedor['TipoEntregaProveedorOC'][0]['tipo_entrega'] ?? 'retiro',
					"receptor_informado" 		=> ($proveedor['TipoEntregaProveedorOC'][0]['tipo_entrega'] ?? 'retiro' == "retiro") ? $proveedor['TipoEntregaProveedorOC'][0]['receptor_informado'] ?? "No definido" : "",
					"informacion_entrega" 		=> $proveedor['TipoEntregaProveedorOC'][0]['informacion_entrega'] ?? "",
					"moneda_id" 				=> null,
					"total_neto" 				=> "",
					"iva" 						=> "",
					"total" 					=> "",
					"fecha_validado" 			=> date('Y-m-d H:i:s'),
					"comentario_validar" 		=> "Esto es una OC generada Automáticamente",
					"nombre_validado" 			=> $tienda['Administrador']['nombre'],
					"email_comercial" 			=> $tienda['Administrador']['email'],
					"validado_proveedor" 		=> 0,
					"bodega_id" 				=> $bodega_id,
					"tipo_orden" 				=> "en_verde"
				];

				// * Formatiamos los productos para registrarlo a la OC

				$producto_oc = [];

				foreach ($proveedor['VentaDetalleProducto'] as $p) {

					$total = array_sum(Hash::extract(array_filter(
						$venta_bodega,
						function ($v, $k) use ($p) {
							return $v['rpvdp']['producto_id'] == $p['id'];
						},
						ARRAY_FILTER_USE_BOTH
					), "{n}.0.cantidad"));
					$descuentos 		= ClassRegistry::init('VentaDetalleProducto')::obtener_descuento_por_producto($p);

					$precio_unitario 	= $p['precio_costo'];
					$total_neto 		= $total * $precio_unitario;

					// * Si el producto no posee un precio mayor a 0 no es considerado para la OC

					$descuento_producto = $total * $descuentos['total_descuento'];
					$total_neto 		= $total_neto - $descuento_producto;
					$producto_oc[] 		= [
						'venta_detalle_producto_id' => $p['id'],
						'codigo' 					=> $p['referencia'],
						'descripcion' 				=> $p['nombre'],
						'cantidad' 					=> $total,
						'precio_unitario' 			=> $precio_unitario,
						'descuento_producto' 		=> $descuento_producto,
						'total_neto' 				=> $total_neto,
					];
				}


				// * Totalizamos el neto, el total y el iva. Asignamos los productos formatiados

				$OC['OrdenCompra']['total_neto'] 	= array_sum(Hash::extract($producto_oc, "{n}.total_neto"));
				$OC['OrdenCompra']['total'] 		= $OC['OrdenCompra']['total_neto'] + round($OC['OrdenCompra']['total_neto'] * (Configure::read('iva_clp') / 100));
				$OC['OrdenCompra']['iva'] 			= $OC['OrdenCompra']['total'] - $OC['OrdenCompra']['total_neto'];
				$OC['VentaDetalleProducto'] 		= $producto_oc;

				// * Creamos los estados de la OC
				$OC['OrdenCompraHistorico'][] = [
					"estado" 		=> "creada",
					"responsable" 	=> "diego.romero@nodriza.cl",
					"evidencia" 	=> json_encode($OC)
				];

				$OC['OrdenCompraHistorico'][] = [
					"estado" 		=> "asignacion_metodo_pago",
					"responsable" 	=> "diego.romero@nodriza.cl",
					"evidencia" 	=> json_encode($OC)
				];

				// * Verificamos que medio de pago se le asignara. Si encaja en alguno se le asigna, sino queda en estado asignacion_metodo_pago

				$encontro_regla = false;

				try {
					foreach ($proveedor['ReglasGenerarOC']  as $ReglasGenerarOC) {

						if ($ReglasGenerarOC['mayor_que'] < $OC['OrdenCompra']['total']  && $ReglasGenerarOC['menor_que'] > $OC['OrdenCompra']['total'])
							$encontro_regla	= true;

						if (is_null($ReglasGenerarOC['mayor_que']) &&  $OC['OrdenCompra']['total'] < $ReglasGenerarOC['menor_que'])
							$encontro_regla	= true;

						if (is_null($ReglasGenerarOC['menor_que']) &&  $OC['OrdenCompra']['total'] > $ReglasGenerarOC['mayor_que'])
							$encontro_regla	= true;

						if ($encontro_regla) {

							$OC['OrdenCompra']['moneda_id'] 			= $ReglasGenerarOC['medio_pago_id'];
							$OC['OrdenCompra']['estado'] 				= "validacion_externa";

							# Descuentos por método de pago

							if (Hash::check($proveedor, 'Moneda.{n}[id=' . $OC['OrdenCompra']['moneda_id'] . ']')) {

								$descuento 								= Hash::extract($proveedor, 'Moneda.{n}[id=' . $OC['OrdenCompra']['moneda_id'] . '].MonedasProveedor.descuento')[0];

								$OC['OrdenCompra']['descuento'] 		= $descuento;
								$OC['OrdenCompra']['descuento_monto']  	= $OC['OrdenCompra']['total'] * ($descuento / 100);
								$OC['OrdenCompra']['total'] 			= round($OC['OrdenCompra']['total'] - $OC['OrdenCompra']['descuento_monto']);
							}

							$OC['OrdenCompraHistorico'][] 				= [
								"estado" 		=> "validacion_externa",
								"responsable" 	=> "diego.romero@nodriza.cl",
								"evidencia" 	=> json_encode($OC)
							];

							break;
						}
					}

					// * Guardamos la OC y segun el estado notificamos 

					if ($this->OrdenCompra->saveAll($OC)) {

						$respuesta 					= true;
						$OC['OrdenCompra']['id'] 	= $this->OrdenCompra->id;
						$OCCreadas[] 				= $this->OrdenCompra->id;

						if ($encontro_regla) {

							$this->guardarEmailValidado($OC['OrdenCompra']['id']);
						} else {

							$emails = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('pagar_oc');
							$this->guardarEmailAsignarPago($OC, $emails);
						}
					}
				} catch (\Throwable $th) {
					$log[] = array(
						'Log' => array(
							'administrador' => "Problemas para crear OCs a los Proveedores " . implode($proveedores_id),
							'modulo' 		=> 'OrdenComprasController',
							'modulo_accion' => json_encode($th)
						)
					);
					ClassRegistry::init('Log')->create();
					ClassRegistry::init('Log')->saveMany($log);
				}
			}
		}

		return [
			'respuesta' => $respuesta,
			'OCs' 		=> $OCCreadas,
		];
	}

	public function admin_oc_automatica($tiempo)
	{
		if (!isset($tiempo)) {
			$this->Session->setFlash("Debes indicar tiempo", null, array(), 'warning');
			$this->redirect(array('action' => 'index'));
		}

		if (strlen($tiempo) != 5) {
			$this->Session->setFlash("Debes enviar hora y minutos. Los dos puntos reemplazar por guion bajo", null, array(), 'warning');
			$this->redirect(array('action' => 'index'));
		}

		$day 			= strtolower(date("l"));
		$proveedores 	= array_unique(Hash::extract(
			ClassRegistry::init('Proveedor')->find(
				'all',
				array(
					'fields'		=> ['Proveedor.id'],
					'conditions' 	=> array(
						'Proveedor.permitir_generar_oc'	=> true,
						'Proveedor.activo'	=> true,
						"Proveedor.$day"	=> true,
					),
					'joins' 		=> array(
						array(
							'table' => 'frecuencia_generar_oc',
							'alias' => 'FrecuenciaGenerarOC',
							'type'  => 'inner',
							'conditions' => array(
								'FrecuenciaGenerarOC.proveedor_id = Proveedor.id',
								'FrecuenciaGenerarOC.hora' =>  str_replace("_", ":", $tiempo)  . ":00"
							)
						)
					),
				)
			),
			"{n}.Proveedor.id"
		));

		$respuesta = [];

		if ($proveedores) {
			$respuesta = $this->CrearOCAutomaticas($proveedores);
		}

		if ($respuesta['respuesta'] ?? false) {
			$OCs = [];
			foreach ($respuesta['OCs'] as $value) {
				$OCs[] = "<a href='/ordenCompras/view/$value' target='_blank' class='link'>Ir a Oc $value</a>";
			}
			$this->Session->setFlash($this->crearAlertaUl($OCs, 'Ordenes de compra creadas'), null, array(), 'success');
		} else {
			$this->Session->setFlash("No hay productos de proveedores para crear OC", null, array(), 'warning');
		}

		$this->redirect(array('action' => 'index'));
	}
}
