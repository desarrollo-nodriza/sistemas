<?php
App::uses('AppController', 'Controller');
App::uses('VentaDetalleProductosController', 'Controller');
class OrdenComprasController extends AppController
{	

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

		foreach ($this->request->data['Filtro'] as $campo => $valor) {
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

					/*$find = $this->OrdenCompra->find('first', array('conditions' => array('id' => $valor, 'parent_id !=' => ''), 'fields' => array('parent_id')));

					if(!empty($find)) {
						$filtro = array_replace_recursive($filtro, array(
							'conditions' => array(
								'OrdenCompra.id' => $find['OrdenCompra']['parent_id']
							)
						));
					}else{
						$filtro = array_replace_recursive($filtro, array(
							'conditions' => array(
								'OrdenCompra.id' => $valor
							)
						));
					}*/

					$filtro = array_replace_recursive($filtro, array(
						'conditions' => array(
							'OrdenCompra.id' => $valor
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
				case 'sta':

					$find = $this->OrdenCompra->find('all', array('conditions' => array('estado' => $valor, 'parent_id !=' => ''), 'fields' => array('parent_id')));

					# Obtenemos los ids padres para filtrarlos.
					if (!empty($find)) {
						$filtro = array_replace_recursive($filtro, array(
						'conditions' => array(
							'OrdenCompra.id' => Hash::extract($find, '{n}.OrdenCompra.parent_id')
						)));
					}else{
						$filtro = array_replace_recursive($filtro, array(
						'conditions' => array('OrdenCompra.estado' => $valor)));
					}

					break;
				case 'prov':

					$filtro = array_replace_recursive($filtro, array(
						'conditions' => array(
							'OrdenCompra.proveedor_id' => $valor
						)
					));
					
					break;
				case 'dtf':

					$find = $this->OrdenCompra->find('all', array('conditions' => array('created >=' => trim($valor), 'parent_id !=' => ''), 'fields' => array('parent_id')));

					if (!empty($find)) {
						$filtro = array_replace_recursive($filtro, array(
						'conditions' => array('OrdenCompra.id' => Hash::extract($find, '{n}.OrdenCompra.parent_id'))));	
					}else{
						$filtro = array_replace_recursive($filtro, array(
						'conditions' => array('OrdenCompra.created >=' => trim($valor))));
					}
					break;
				case 'dtt':

					$find = $this->OrdenCompra->find('all', array('conditions' => array('created <=' => trim($valor), 'parent_id !=' => ''), 'fields' => array('parent_id')));

					if (!empty($find)) {
						$filtro = array_replace_recursive($filtro, array(
						'conditions' => array('OrdenCompra.id' => Hash::extract($find, '{n}.OrdenCompra.parent_id'))));	
					}else{
						$filtro = array_replace_recursive($filtro, array(
						'conditions' => array('OrdenCompra.created <=' => trim($valor))));
					}

					
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
				)
			),
			'conditions' => array(
				'OR' => array(
					array(
						'OrdenCompra.parent_id !=' => '',
						'OrdenCompra.oc_manual' => 0
					),
					array(
						'OrdenCompra.parent_id' => '',
						'OrdenCompra.oc_manual' => 1
					)
				)
			),
			'fields' => array(
				'OrdenCompra.id',
				'OrdenCompra.estado',
				'OrdenCompra.created',
				'OrdenCompra.tienda_id',
				'OrdenCompra.parent_id',
				'OrdenCompra.administrador_id',
				'OrdenCompra.email_finanza',
				'OrdenCompra.oc_manual'
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

		$ocs = $this->OrdenCompra->find('all', array(
			'conditions' => array(
				'OR' => array(
					array(
						'OrdenCompra.parent_id !=' => '',
						'OrdenCompra.oc_manual' => 0,
						'OrdenCompra.estado !=' => ''
					),
					array(
						'OrdenCompra.parent_id' => '',
						'OrdenCompra.oc_manual' => 1,
						'OrdenCompra.estado !=' => ''
					)
				)
			),
			'fields' => array(
				'estado', 'id'
			)
		)); 

		$sin_iniciar = $this->OrdenCompra->find('count', array(
			'conditions' => array(
				'OR' => array(
					array(
						'OrdenCompra.parent_id !=' => '',
						'OrdenCompra.oc_manual' => 0,
						'OrdenCompra.estado' => ''
					),
					array(
						'OrdenCompra.parent_id' => '',
						'OrdenCompra.oc_manual' => 1,
						'OrdenCompra.estado' => ''
					)
				)
			),
			'fields' => array(
				'estado', 'id'
			)
		)); 

		BreadcrumbComponent::add('Ordenes de compra ');

		$this->set(compact('ocs', 'sin_iniciar'));
	}


	/**
	 * [admin_index_no_procesadas description]
	 * @return [type] [description]
	 */
	public function admin_index_no_procesadas()
	{	
		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index_no_procesadas');
		}

		$paginate =  array(
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
				)
			),
			'conditions' => array(
				'OrdenCompra.estado' => null,
				'OR' => array(
					array(
						'OrdenCompra.parent_id !=' => '',
						'OrdenCompra.oc_manual' => 0
					),
					array(
						'OrdenCompra.parent_id' => '',
						'OrdenCompra.oc_manual' => 1
					)
				)
			),
			'fields' => array(
				'OrdenCompra.id',
				'OrdenCompra.estado',
				'OrdenCompra.created',
				'OrdenCompra.tienda_id',
				'OrdenCompra.parent_id',
				'OrdenCompra.administrador_id',
				'OrdenCompra.email_finanza',
				'OrdenCompra.oc_manual'
			),
			'order' => array(
				'OrdenCompra.id' => 'DESC'
			),
			'limit' => 20
		);

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			$this->reemplazar_filtro_recursivamente($paginate);
		}
		
		$this->paginate = $paginate;

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('En Revisión', '/ordenCompras/index_no_procesadas');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'estados', 'proveedores'));
	
	}


	/**
	 * [admin_index_revision description]
	 * @return [type] [description]
	 */
	public function admin_index_revision()
	{
		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index_revision');
		}

		$paginate = $this->paginacion_index(array('iniciado'));

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			$this->reemplazar_filtro_recursivamente($paginate);
		}
		
		$this->paginate = $paginate;

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('En Revisión', '/ordenCompras/index_revision');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'estados', 'proveedores'));
	}


	/**
	 * [admin_index_enviadas description]
	 * @return [type] [description]
	 */
	public function admin_index_enviadas()
	{	

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index_enviadas');
		}

		$paginate = $this->paginacion_index(array('enviado'));

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			$this->reemplazar_filtro_recursivamente($paginate);
		}
		
		$this->paginate = $paginate;

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Incompletas', '/ordenCompras/index_incompletas');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'estados', 'proveedores'));
	}


	/**
	 * [admin_index_validadas description]
	 * @return [type] [description]
	 */
	public function admin_index_validadas()
	{	

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index_validadas');
		}

		$paginate = $this->paginacion_index(array('asignacion_moneda'));

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			$this->reemplazar_filtro_recursivamente($paginate);
		}
		
		$this->paginate = $paginate;

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('En espera de pago', '/ordenCompras/index_validadas');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'estados', 'proveedores'));
	}


	/**
	 * [admin_index_validadas description]
	 * @return [type] [description]
	 */
	public function admin_index_asignacion_moneda()
	{	

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index_asignacion_moneda');
		}

		$paginate = $this->paginacion_index(array('validado'));

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			$this->reemplazar_filtro_recursivamente($paginate);
		}
		
		$this->paginate = $paginate;

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('En espera de asignación de m. de pago', '/ordenCompras/index_asignacion_moneda');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'estados', 'proveedores'));
	}



	/**
	 * [admin_index_validadas_proveedor description]
	 * @return [type] [description]
	 */
	public function admin_index_validada_proveedores()
	{	

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index_validada_proveedores');
		}

		$paginate = $this->paginacion_index(array('validado_proveedor'));

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			$this->reemplazar_filtro_recursivamente($paginate);
		}
		
		$this->paginate = $paginate;

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('En espera de pago', '/ordenCompras/index_validada_proveedores');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'estados', 'proveedores'));
	}


	/**
	 * [admin_index_pagadas description]
	 * @return [type] [description]
	 */
	public function admin_index_todo()
	{	

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index_todo');
		}

		$paginate = $this->paginacion_index();

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			$this->reemplazar_filtro_recursivamente($paginate);
		}
		
		$this->paginate = $paginate;

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Todo', '/ordenCompras/index_todo');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'estados', 'proveedores'));
	}


	/**
	 * [admin_index_pagadas description]
	 * @return [type] [description]
	 */
	public function admin_index_pagadas()
	{	

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index_pagadas');
		}

		$paginate = $this->paginacion_index(array('pagado'));

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			$this->reemplazar_filtro_recursivamente($paginate);
		}
		
		$this->paginate = $paginate;

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Pagadas', '/ordenCompras/index_pagadas');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'estados', 'proveedores'));
	}


	/**
	 * [admin_index_incompletas description]
	 * @return [type] [description]
	 */
	public function admin_index_incompletas()
	{	

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index_incompletas');
		}

		$paginate = $this->paginacion_index(array('incompleto'));

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			$this->reemplazar_filtro_recursivamente($paginate);
		}
		
		$this->paginate = $paginate;

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Incompletas', '/ordenCompras/index_incompletas');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'estados', 'proveedores'));
	}


	/**
	 * [admin_index_finalizadas description]
	 * @return [type] [description]
	 */
	public function admin_index_finalizadas()
	{	

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index_finalizadas');
		}

		$paginate = $this->paginacion_index(array('recibido'));

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			$this->reemplazar_filtro_recursivamente($paginate);
		}
		
		$this->paginate = $paginate;

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Finalizadas', '/ordenCompras/index_finalizadas');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'estados', 'proveedores'));
	}



	/**
	 * [admin_index_canceladas description]
	 * @return [type] [description]
	 */
	public function admin_index_canceladas()
	{	

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index_canceladas');
		}

		$paginate = $this->paginacion_index(array('cancelada'));

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			$this->reemplazar_filtro_recursivamente($paginate);
		}
		
		$this->paginate = $paginate;

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Canceladas', '/ordenCompras/index_canceladas');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'estados', 'proveedores'));
	}


	/**
	 * [admin_index_pendiente_facturas description]
	 * @return [type] [description]
	 */
	public function admin_index_pendiente_facturas()
	{	

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index_pendiente_facturas');
		}

		$paginate = $this->paginacion_index(array('pendiente_factura'));

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			$this->reemplazar_filtro_recursivamente($paginate);
		}
		
		$this->paginate = $paginate;

		$estados = $this->OrdenCompra->estados;

		$proveedores = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Finalizadas', '/ordenCompras/index_pendiente_facturas');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'estados', 'proveedores'));
	}


	/**
	 * Para finalizar una OC como recibida debe indicarse el/las facturas
	 * que respaldan los porductos ingresados.
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_reception($id)
	{
		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index_enviadas'));
		}

		$productosActualizado   = array();
		$productosNoActualizado = array();

		$res                    = array(
			'incompletos'           => array(),
			'completos'             => array()
		);

		if ($this->request->is('post') || $this->request->is('put')) {

			# Variables usados directamente que deben ser quitadas del post una vez asigandas.
			$url_retorno   = $this->request->data['OrdenCompra']['url_retorno'];
			$rut_proveedor = $this->request->data['OrdenCompra']['rut_proveedor'];
			$rut_tienda    = $this->request->data['OrdenCompra']['rut_tienda'];

			unset($this->request->data['OrdenCompra']['url_retorno']);
			unset($this->request->data['OrdenCompra']['rut_proveedor']);
			unset($this->request->data['OrdenCompra']['rut_tienda']);
			
			foreach ($this->request->data['OrdenCompra'] as $key => $oc) {

				if (!isset($this->request->data['OrdenCompraFactura']) || empty($this->request->data['OrdenCompraFactura'])) {
					$this->Session->setFlash('No ha asignado pagos a esta OC.', null, array(), 'danger');
					$this->redirect(array('action' => 'reception', $id));
				}

				# Se obtiene los datos de la OC enviada a proveedor
				$pedido = ClassRegistry::init('OrdenComprasVentaDetalleProducto')->find('first', array(
					'conditions' => array(
						'orden_compra_id'           => $id,
						'venta_detalle_producto_id' => $oc['VentaDetalleProducto']['id']
					)
				));
				
				# Calcula la cantidad  de productos que faltan por recibir.
				$cantidadFaltante = $pedido['OrdenComprasVentaDetalleProducto']['cantidad_validada_proveedor'] - $pedido['OrdenComprasVentaDetalleProducto']['cantidad_recibida'];
				$cantidadRecibida = $oc['Bodega'][0]['cantidad'];
				$bodegaDestino    = $oc['Bodega'][0]['bodega_id'];

				if ($cantidadFaltante == 0) {
					continue;
				}

				if ( $cantidadFaltante == $cantidadRecibida ) {
					$res['completos'][] = sprintf('#%s - %s (agregados: %d)', $oc['VentaDetalleProducto']['id'], $pedido['OrdenComprasVentaDetalleProducto']['descripcion'], $cantidadRecibida);
				}

				if ( $cantidadFaltante > $oc['Bodega'][0]['cantidad'] ) {
					$res['incompletos'][] = sprintf('#%s - %s (agregados: %d - faltantes: %d)', $oc['VentaDetalleProducto']['id'], $pedido['OrdenComprasVentaDetalleProducto']['descripcion'], $cantidadRecibida, ($cantidadFaltante - $cantidadRecibida) );
				}
			
				# Obtenemos los productos vendidos que se solicitaron en ésta OC
				$currentOC = $this->OrdenCompra->find('first', array(
					'conditions' => array(
						'OrdenCompra.id' => $id
					),
					'fields' => array(
						'OrdenCompra.parent_id'
					)
				));	

				

				# Buscamos las ventas de la OC padre para reservar las cantidades que se estan pidiendo
				if (!empty($currentOC['OrdenCompra']['parent_id'])) {

					$padre = $this->OrdenCompra->find('first', array(
						'conditions' => array(
							'OrdenCompra.id' => $currentOC['OrdenCompra']['parent_id'],
						),
						'contain' => array(
							'Venta' => array(
								'VentaDetalle' => array(
									'fields' => array(
										'VentaDetalle.venta_detalle_producto_id', 
										'VentaDetalle.cantidad_pendiente_entrega',
										'VentaDetalle.cantidad_reservada',
										'VentaDetalle.cantidad',
										'VentaDetalle.completo',
										'VentaDetalle.cantidad_entregada',
										'VentaDetalle.venta_id'
									)
								),
								'order' => array(
									'Venta.fecha_venta' => 'asc'
								),
								'fields' => array(
									'Venta.subestado_oc', 'Venta.fecha_venta'
								)
							)
						),
						'fields' => array(
							'OrdenCompra.id', 'OrdenCompra.oc_manual'
						)
					));

					# Listamos los productos vendidos que se pideron en ésta OC
					$productos_vendidos = Hash::extract($padre['Venta'], '{n}.VentaDetalle.{n}[venta_detalle_producto_id='. $oc['VentaDetalleProducto']['id'].']');

					$ventasCompletas = array();
					
					# Reservamos stock recibido
					foreach ($productos_vendidos as $iv => $v) {

						if ($v['completo']) {
							continue;
						}

						ClassRegistry::init('VentaDetalle')->id = $v['id'];

						$reservar = $v['cantidad'] - $v['cantidad_reservada'];
						
						if ($cantidadRecibida >= $reservar && $reservar > 0) {
							ClassRegistry::init('VentaDetalle')->saveField('cantidad_reservada', $reservar);
						}

						if ($cantidadRecibida < $reservar) {
							ClassRegistry::init('VentaDetalle')->saveField('cantidad_reservada', $cantidadRecibida);
						}
						
						if ($cantidadRecibida >= $v['cantidad']) {
							$ventasCompletas[$v['venta_id']][$v['venta_detalle_producto_id']] = $cantidadRecibida;
						}

					}
					
					# Actualizamos ventas completas para que sean empaquetadas
					if (!empty($ventasCompletas)) {
						foreach ($ventasCompletas as $id_venta => $id_producto) {

							$venta = ClassRegistry::init('Venta')->find('first', array(
								'conditions' => array(
									'Venta.id' => $id_venta
								),
								'contain' => array(
									'VentaDetalle'
								)
							));

							if ( array_sum(Hash::extract($venta, 'VentaDetalle.{n}.cantidad')) == array_sum(Hash::extract($venta, 'VentaDetalle.{n}.cantidad_reservada')) ) {
								ClassRegistry::init('Venta')->id = $id_venta;
								ClassRegistry::init('Venta')->saveField('picking_estado', 'empaquetar');
							}
						}
					}
				}

				ClassRegistry::init('OrdenComprasVentaDetalleProducto')->id = $pedido['OrdenComprasVentaDetalleProducto']['id'];
				ClassRegistry::init('OrdenComprasVentaDetalleProducto')->saveField('cantidad_recibida', $cantidadRecibida); # Actualiamos la cantidad recibida

				# Se crea la entrada de productos
				$precioCompra = round($pedido['OrdenComprasVentaDetalleProducto']['total_neto'] / $pedido['OrdenComprasVentaDetalleProducto']['cantidad_validada_proveedor'], 2);
				
				if (ClassRegistry::init('Bodega')->crearEntradaBodega($oc['VentaDetalleProducto']['id'], $bodegaDestino, $cantidadRecibida, $precioCompra, 'OC', $id)) {
					$productosActualizado[] = $oc['VentaDetalleProducto']['id'];
				}else{
					$productosNoActualizado[] = $oc['VentaDetalleProducto']['id'];
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
					'id' => $id ,
					'estado' => 'recibido'
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
						'estado' => 'incompleto'
					)
				));
			}

			# Cliente Libredte
			$libreDte = $this->Components->load('LibreDte');
			$libreDte->crearCliente($this->Session->read('Tienda.facturacion_apikey'));

			$folios = array();

			foreach ($this->request->data['OrdenCompraFactura'] as $iocf => $ocf) {

				if (empty($ocf['folio']))
					continue;
				
				# Se obtiene el dTE desde el sii y se verifican los datos
				$emisor   = $this->rutSinDv($rut_proveedor);
				$tipo_dte = $ocf['tipo_documento']; // Facturas
				$folio    = $ocf['folio'];
				$receptor = $this->rutSinDv($rut_tienda);

				$res = $libreDte->obtener_documento_recibido($emisor, $tipo_dte, $folio, $receptor);
				
				if (!empty($res)) {

					$this->request->data['OrdenCompraFactura'][$iocf]['monto_facturado'] = $res['total'];
					$this->request->data['OrdenCompraFactura'][$iocf]['emisor']          = $res['emisor'];
					$this->request->data['OrdenCompraFactura'][$iocf]['receptor']        = $res['receptor'];
				
				}else{

					#$folios[] = $libreDte->tipoDocumento[$ocf['tipo_documento']] . ' folio #' . $ocf['folio'] . ' no fue encontrado. Verifique que la información del DTE sea correcta.';

					$this->request->data['OrdenCompraFactura'][$iocf]['monto_facturado'] = $ocf['monto_facturado'];
					$this->request->data['OrdenCompraFactura'][$iocf]['emisor']          = $emisor;
					$this->request->data['OrdenCompraFactura'][$iocf]['receptor']        = $receptor;
				}

				# Es factura
				if ($tipo_dte == 33) {
					# Obtenemos el saldo disponible para pagar para éste proveedor
					$id_proveedor = $this->OrdenCompra->field('proveedor_id', array('id' => $id));
					$saldo_disponible_pago = ClassRegistry::init('Saldo')->obtener_saldo_total_proveedor($id_proveedor);

					if (count($this->request->data['OrdenCompraFactura']) == 1 && $saldo_disponible_pago >= $this->request->data['OrdenCompraFactura'][$iocf]['monto_facturado']) {

						# Se paga la factura
						$this->request->data['OrdenCompraFactura'][$iocf]['monto_pagado'] = $this->request->data['OrdenCompraFactura'][$iocf]['monto_facturado'];
						$this->request->data['OrdenCompraFactura'][$iocf]['pagada']       = 1;
					}

					# Descontamos el saldo usado solo al crearla
					if (!isset($ocf['id'])) 
						ClassRegistry::init('Saldo')->descontar($id_proveedor, $id, null, null, $this->request->data['OrdenCompraFactura'][$iocf]['monto_facturado']);	
				}
			}

			# OC queda en estado de espera de factura
			if ($ocSave['OrdenCompra']['estado'] == 'recibido' && count(Hash::extract($this->request->data, 'OrdenCompraFactura.{n}[tipo_documento=33]')) == 0 ) {
				$ocSave['OrdenCompra']['estado'] = 'pendiente_factura';
			}elseif ($ocSave['OrdenCompra']['estado'] == 'recibido' && count(Hash::extract($this->request->data, 'OrdenCompraFactura.{n}[tipo_documento=33]')) > 0) {
				$ocSave['OrdenCompra']['estado'] = 'recibido';
			}

			$ocSave = array_replace_recursive($ocSave, array(
				'OrdenCompraFactura' => $this->request->data['OrdenCompraFactura']
			));	
			
			$this->OrdenCompra->saveAll($ocSave);

			if ( $this->OrdenCompra->es_pago_factura_unico($id) ) {
				ClassRegistry::init('Pago')->relacionar_pago_factura($id);
			}

			if (!empty($folios)) {
				$this->Session->setFlash($this->crearAlertaUl($folios, 'Errores'), null, array(), 'warning');
				$this->redirect(array('action' => 'reception', $id));
			}

			$this->redirect(array('action' => 'index_enviadas'));

		}

		$this->request->data = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'VentaDetalleProducto' => array(
					'Bodega'
				),
				'Administrador',
				'Tienda',
				'Proveedor',
				'OrdenCompraFactura'
			)
		));
		#prx($this->request->data);
		$bodegas = ClassRegistry::init('Bodega')->find('list', array('conditions' => array('activo' => 1)));
		
		$url_retorno = Router::url( $this->referer(), true );

		# Array de tipos de documentos
		$libreDte = $this->Components->load('LibreDte');
		$tipo_documento = array(
			33 => 'Factura electrónica',
			52 => 'Guia de despacho electrónica',
			50 => 'Guia de despacho manual'
 		);

		BreadcrumbComponent::add('Ordenes de compra ', '/index_enviadas');
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

				if ( $value['Bodega'][0]['cantidad'] < $pedido['OrdenComprasVentaDetalleProducto']['cantidad'] ) {
					$res['faltantes'][] = array(
						'producto_id'     => $value['VentaDetalleProducto']['id'],
						'producto_nombre' => $pedido['OrdenComprasVentaDetalleProducto']['descripcion'],
						'cantidad'        => $pedido['OrdenComprasVentaDetalleProducto']['cantidad'] - $value['Bodega'][0]['cantidad']
					);
				}

				if ( $value['Bodega'][0]['cantidad'] == $pedido['OrdenComprasVentaDetalleProducto']['cantidad'] ) {
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
		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$ocs = $this->OrdenCompra->find('all', array(
			'conditions' => array(
				'OrdenCompra.parent_id' => $id
			),
			'contain' => array(
				'Moneda',
				'VentaDetalleProducto',
				'Administrador',
				'Tienda',
				'Proveedor',
				'OrdenCompraFactura',
				'OrdenCompraPago'
			)
		));

		if (empty($ocs)) {
			$ocs = $this->OrdenCompra->find('all', array(
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
					'OrdenCompraPago'
				)
			));
		}
		
		$url_retorno = Router::url( $this->referer(), true );

		BreadcrumbComponent::add('Ordenes de compra ', $url_retorno);
		BreadcrumbComponent::add('Ver OC ');

		$this->set(compact('ocs', 'url_retorno'));

	}


	/**
	 * [admin_generar_pdf description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_generar_pdf($id)
	{	

		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
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
				)
			)
		));

		$nombreOC = 'orden_compra_' . $ocs['OrdenCompra']['id'] . '_' . Inflector::slug($ocs['Proveedor']['nombre']) . '_' . rand(1,100) . '.pdf';
		
		$this->generar_pdf($ocs, $nombreOC);

		$this->OrdenCompra->id = $id;

		if($this->OrdenCompra->saveField('pdf', $nombreOC)) {
			$this->Session->setFlash('OC generada en PDF con éxito.', null, array(), 'success');
		}else{
			$this->Session->setFlash('No fue posible generar el PDF.', null, array(), 'danger');
		}

		$this->redirect($this->referer('/', true));

	}


	/**
	 * Envia la OC a los destinatarios correspondientes que se configuraron en Proveedores
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_ready($id)
	{
		if ( ! $this->OrdenCompra->exists($id) )
		{
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
					)
				)
			));

			# si no se ha gnerado se intenta generar nuevamente
			if (empty($this->request->data['OrdenCompra']['pdf'])) {

				$nombreOC = 'orden_compra_' . $ocs['OrdenCompra']['id'] . '_' . Inflector::slug($ocs['Proveedor']['nombre']) . '_' . rand(1,100) . '.pdf';
				
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
			->from(array($this->Session->read('Auth.Administrador.email') => 'Nodriza Spa') )
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
			
			if( $this->Email->send() ) {
				$this->Session->setFlash('Email y adjuntos enviados con éxito', null, array(), 'success');
				$this->redirect(array('action' => 'index_pagadas'));
			}else{
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
		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index_revision'));
		}


		$ocs = $this->OrdenCompra->find('first', array(
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
		foreach ($ocs['VentaDetalleProducto'] as $i => $p) {

			$descuentos = ClassRegistry::init('VentaDetalleProducto')::obtener_descuento_por_producto($p);

			$ocs['VentaDetalleProducto'][$i]['total_descuento']  = $descuentos['total_descuento'];
			$ocs['VentaDetalleProducto'][$i]['nombre_descuento'] = $descuentos['nombre_descuento'];
			$ocs['VentaDetalleProducto'][$i]['valor_descuento']  = $descuentos['valor_descuento']; 
		}

		
		if ($this->request->is('post') || $this->request->is('put')) {
			
			if (isset($this->request->data['OrdenCompra']['estado'])) {

				$this->OrdenCompra->id = $id;
				$this->OrdenCompra->saveField('estado', ''); # Vacio vuelve a bodega
				$this->OrdenCompra->saveField('comentario_validar', $this->request->data['OrdenCompra']['comentario_validar']); # Guarda comentario

				$emails = array($ocs['Administrador']['email']);

				$this->guardarEmailRechazo($id, $emails);

			}else{

				$this->request->data['OrdenCompra']['estado']             = 'validado'; # Pasa a finanzas
				$this->request->data['OrdenCompra']['nombre_validado']    = $this->Session->read('Auth.Administrador.nombre'); # Guardamos el nombre de quien validó la OC
				$this->request->data['OrdenCompra']['email_comercial']    = $this->Session->read('Auth.Administrador.email'); # Guardamos el email de quien validó la OC
				$this->request->data['OrdenCompra']['validado_proveedor'] = 0;

				$emails = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('pagar_oc');

				if ( $this->OrdenCompra->saveAll($this->request->data) && $this->guardarEmailAsignarPago($ocs, $emails) )
				{	
					$this->Session->setFlash('Estado actualizado con éxito.', null, array(), 'success');
				}

			}

			$this->redirect(array('action' => 'index_revision'));

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
		}else{
			$this->Session->setFlash('No fue posible notificar al proveedor.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

	}


	public function admin_asignar_moneda($id)
	{
		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index_asignacion_moneda'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			
			if ($this->guardarEmailValidado($id) && $this->OrdenCompra->save($this->request->data)) {
				$this->Session->setFlash('Método de pago asignado con éxito.', null, array(), 'success');
				$this->redirect(array('action' => 'index_asignacion_moneda'));
			}else{
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
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_validate($id)
	{
		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index_no_procesadas'));
		}


		if ( $this->request->is('post') || $this->request->is('put') )
		{	

			# Limpiar data
			$this->OrdenCompra->deleteAll(array('OrdenCompra.parent_id' => $id));

			foreach ($this->request->data['OrdenesCompra'] as $ic => $d) {

				if (!isset($d['OrdenCompra']['parent_id']) || !isset($d['VentaDetalleProducto'])) {
					continue;
				}

				if ( ! $this->OrdenCompra->saveAll($d, array('deep' => true)) ) {
					$this->Session->setFlash('Ocurrió un error al guardar la OC. Verifique la información.', null, array(), 'danger');
					$this->redirect(array('action' => 'validate', $id));
				}
				
			}

			$this->OrdenCompra->id = $id;
			$this->OrdenCompra->saveField('estado', 'iniciado');

			$emailsNotificar = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('revision_oc');

			if (!empty($emailsNotificar)) {
				$this->guardarEmailRevision($this->request->data['OrdenesCompra'][0], $emailsNotificar);
			}

			$this->Session->setFlash('¡Éxito! Se ha enviado a revisión la OC.', null, array(), 'success');
			$this->redirect(array('action' => 'index_no_procesadas'));
		}

		$this->request->data = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'Moneda',
				'Venta' => array(
					'VentaDetalle' => array(
						'VentaDetalleProducto'
					)
				),
				'Administrador',
				'Tienda',
				'Proveedor'
			)
		));


		if (empty($this->request->data['Venta'])) {
			$this->Session->setFlash('No tiene ventas asociadas a la OC.', null, array(), 'danger');
			$this->redirect(array('action' => 'edit', $id));
		}

		$productosSolicitar = array();
		$productosNoSolicitar = array();
		$productosTotales   = array();


		# Se calculan los totales de productos vendidos
		foreach (Hash::extract($this->request->data['Venta'], '{n}.VentaDetalle.{n}') as $iv => $venta) {

			$cantidad = $venta['cantidad'] - $venta['cantidad_reservada']; // Se descuenta la cantidad ya reservada

			if ($cantidad === 0) {
				continue;
			}

			if ( array_key_exists($venta['venta_detalle_producto_id'], $productosTotales) ) {
				$productosTotales[$venta['venta_detalle_producto_id']] = $productosTotales[$venta['venta_detalle_producto_id']] + $cantidad;
			}else{
				$productosTotales[$venta['venta_detalle_producto_id']] = $cantidad;
			}

		}

		# comprobamos el stock en bodegas para saber cuales productos se deben solicitar por OC
		foreach ($productosTotales as $ip => $p) {
			
			$pedir = $p;			

			# Consultamos la cantiad que tenemos en la bodega principal
			$enBodega = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($ip);

			# Calculamos la diferencia que se debe pedir segun lo que tenemos en bodega
			if ($enBodega >= $p) {
				$pedir = 0;
			}else{
				$pedir = $pedir - $enBodega;
			}
			
			# Definimos lo que tenemos en bodega y lo que no
			if ($pedir === 0) {
				$productosNoSolicitar[$ip]['id'] = $ip;
				$productosNoSolicitar[$ip]['cantidad_bodega'] = $enBodega;
			}else{
				$productosSolicitar[$ip]['id'] = $ip;
				$productosSolicitar[$ip]['cantidad_oc'] = $pedir;
			}

		}
		
		# Si no hay producto que pedir se cancela el paso
		if (empty($productosSolicitar)) {
			$this->Session->setFlash('No hay productos que agregar a la OC.', null, array(), 'danger');
			$this->redirect(array('action' => 'index_no_procesadas'));
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
						'ProveedoresVentaDetalleProducto.venta_detalle_producto_id IN(' . implode(',',Hash::extract($productosSolicitar, '{n}.id')) . ')'
					)
				)
			),
			'contain' => array(
				'VentaDetalleProducto' => array(
					'conditions' => array(
						'VentaDetalleProducto.id' => Hash::extract($productosSolicitar, '{n}.id')
					)
				),
				'OrdenCompra' => array(
					'conditions' => array(
						'OrdenCompra.parent_id' => $id
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
		
		$proveedoresLista = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('Proveedor.activo' => 1)));
		$marcas 		  = ClassRegistry::init('Marca')->find('list');
		$monedas          = $this->OrdenCompra->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));


		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Revisión ');		

		$this->set(compact('monedas', 'productosNoSolicitar', 'productosSolicitar', 'productosIncompletos', 'productos', 'proveedores', 'proveedoresLista', 'tipoDescuento', 'marcas'));
		
	}


	/**
	 * PErmite pagar una OC y notificar a bodega
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_pay($id = null)
	{
		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index_validadas'));
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
				'OrdenCompraPago',
				'Proveedor' => array(
					'Moneda'
				),
				'OrdenCompraPago'
			)
		));

		if (!empty($ocs['OrdenCompra']['nombre_pagado']) && !isset($this->request->query['update'])) {
			$this->Session->setFlash('La OC #' . $id . ' ya fue pagada por ' . $ocs['OrdenCompra']['nombre_pagado'], null, array(), 'success');
			$this->redirect(array('action' => 'index_validadas'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			
			$data = array(
				'OrdenCompra' => array(
					'id'                 => $id,
					'estado'             => 'pagado',
					//'moneda_id'          => $this->request->data['OrdenCompra']['moneda_id'],
					'nombre_pagado'      => $this->Session->read('Auth.Administrador.nombre'),
					'email_finanza'      => $this->Session->read('Auth.Administrador.email'),
					'comentario_finanza' => $this->request->data['OrdenCompra']['comentario_finanza'],
					'total'              => $this->request->data['OrdenCompra']['total'],
					'descuento_monto'    => round($this->request->data['OrdenCompra']['descuento_monto']),
					'descuento'    	     => round($this->request->data['OrdenCompra']['descuento']),
				),
				'OrdenCompraAdjunto' 	 => (isset($this->request->data['OrdenCompraAdjunto'])) ? $this->request->data['OrdenCompraAdjunto'] : array(),
				//'OrdenCompraPago' => $this->request->data['OrdenCompraPago']
			);


			if (isset($this->request->query['update'])) { 
				unset($data['OrdenCompra']['estado']);
			}
			
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
						'Proveedor'
					)
				));
			

				# Por cada adjunto creado se crea un pago
				foreach ($ocs['OrdenCompraAdjunto'] as $ioca => $oca) {
					
					switch ($ocs['Moneda']['tipo']) {
						case 'pagar':

							// Se crea un pago al dia 
							ClassRegistry::init('Pago')->crear($oca['identificador'], $id, $oca['id'], date('Y-m-d'), $ocs['OrdenCompra']['total']);
							break;
						
						case 'agendar':

							// Se crea un pago sin fecha ni monto (se debe configurar una vez recibida la/las factura/s) 
							ClassRegistry::init('Pago')->crear($oca['identificador'], $id, $oca['id'], null, 0);
							break;

						case 'esperar':
							// Al moento de recibir la factura se crea y asigna el pago
							break;
					}
					
				}


				$pdfOc = 'orden_compra_' . $ocs['OrdenCompra']['id'] . '_' . Inflector::slug($ocs['Proveedor']['nombre']) . '_' . rand(1,100) . '.pdf';

				$this->generar_pdf($ocs, $pdfOc);

				$this->OrdenCompra->id = $id;
				$this->OrdenCompra->saveField('pdf', $pdfOc);
				$this->OrdenCompra->saveField('estado', 'enviado');
				$this->OrdenCompra->saveField('fecha_enviado', date('Y-m-d H:i:s'));

				# Quitamos el envio de emails
				$this->Session->setFlash('Estado actualizado con éxito.', null, array(), 'success');
				$this->redirect(array('action' => 'index_validada_proveedores'));

			}else{
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
		if ( $this->request->is('post') )
		{	
			$this->OrdenCompra->create();
			if ( $this->OrdenCompra->save($this->request->data) )
			{	
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
			}
			else
			{
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
		if ( $this->request->is('post') )
		{	
			$this->OrdenCompra->create();
			if ( $this->OrdenCompra->save($this->request->data) )
			{	

				$emailsNotificar = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('revision_oc');

				if (!empty($emailsNotificar)) {
					$this->guardarEmailRevision($this->request->data, $emailsNotificar);
				}

				$this->Session->setFlash('¡Éxito! Se ha enviado a revisión la OC.', null, array(), 'success');
				$this->redirect(array('action' => 'index_no_procesadas'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$monedas = $this->OrdenCompra->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));
		$proveedores = $this->OrdenCompra->Proveedor->find('list', array('conditions' => array('Proveedor.activo' => 1), 'order' => array('nombre' => 'ASC')));

		$tipoDescuento    = array(0 => '$', 1 => '%');

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Agregar oc manual');

		$this->set(compact('monedas', 'proveedores', 'tipoDescuento'));
	}


	/**
	 * Modificar una OC individualmente
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_editsingle($id = null)
	{
		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index_no_procesadas'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			# Limpiar data
			$this->OrdenCompra->OrdenComprasVentaDetalleProducto->deleteAll(array('OrdenComprasVentaDetalleProducto.orden_compra_id' => $id));

			if ( $this->OrdenCompra->saveAll($this->request->data) )
			{	

				$emailsNotificar = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('revision_oc');

				if (!empty($emailsNotificar)) {
					$this->guardarEmailRevision($this->request->data['OrdenCompra'], $emailsNotificar);
				}

				$this->Session->setFlash('¡Éxito! Se ha enviado a revisión la OC.', null, array(), 'success');
				$this->redirect(array('action' => 'index_no_procesadas'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		else
		{
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
		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index_no_procesadas'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	

			$this->OrdenCompra->OrdenComprasVenta->deleteAll(array('OrdenComprasVenta.orden_compra_id' => $id));

			if ( $this->OrdenCompra->saveAll($this->request->data) )
			{	
				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'validate', $id));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		else
		{
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
		if ( ! $this->OrdenCompra->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		if (!$this->request->is('post')) {
			$this->Session->setFlash('Método no permitido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		$this->request->data['OrdenCompra']['razon_cancelada'] = $this->request->data['OrdenCompra']['razon_cancelada'] . ' <small class="text-muted">(Cancelada por: '.$this->Auth->user('email').')</small>';

		if ($this->OrdenCompra->saveAll($this->request->data)) {
			$this->Session->setFlash('Orden de compra cancelada.', null, array(), 'success');
			$this->redirect($this->referer('/', true));
		}else{
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
		if ( ! $this->OrdenCompra->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->OrdenCompra->delete() )
		{	

			$this->OrdenCompra->deleteAll(array('OrdenCompra.parent_id' => $id));

			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect($this->referer('/', true));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect($this->referer('/', true));
	}


	/**
	 * [admin_exportar description]
	 * @return [type] [description]
	 */
	public function admin_exportar()
	{
		$datos			= $this->OrdenCompra->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->OrdenCompra->_schema);
		$modelo			= $this->OrdenCompra->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	/**
	 * [admin_obtener_ordenes_ajax description]
	 * @return [type] [description]
	 */
	public function admin_obtener_ordenes_ajax()
	{	

		$this->layout = 'ajax';

		ini_set('memory_limit', -1);


		$fecha_actual = date("Y-m-d H:i:s");
		$hace_un_mes  = date("Y-m-d H:i:s",strtotime($fecha_actual."-1 month")); 

		$ventas          = $this->OrdenCompra->Venta->find('all', array(
			'conditions' => array(
				'Venta.fecha_venta >' => $hace_un_mes,
				'Venta.picking_estado' => 'no_definido'
			),
			'fields' => array(
				'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.prioritario'
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
					'fields' => array('VentaDetalle.id')
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
					'fields' => array(
						'OrdenCompra.id'
					)
				)
			),
			'limit' => $this->request->query['limit'],
			'offset' => $this->request->query['offset'],
			'order' => array('Venta.prioritario' => 'DESC', 'Venta.fecha_venta' => 'DESC')
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

			if (!empty($ventas[$io]['VentaEstado'])) {
				if (!$ventas[$io]['VentaEstado']['permitir_oc']) {
					unset($ventas[$io]);
				}
			}
		}
		
		$this->set(compact('ventas'));
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
			'descuento_monto_html'  => CakeNumber::currency(0 , 'CLP'),
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
			if ( Hash::check($oc, 'Proveedor.Moneda.{n}[id=' . $this->request->data['moneda_id'] . ']') )
			{
				$descuento = Hash::extract($oc, 'Proveedor.Moneda.{n}[id=' . $this->request->data['moneda_id'] . '].MonedasProveedor.descuento')[0];
				
				$res['descuento_porcentaje'] = $descuento;
				$res['descuento_monto']      = $oc['OrdenCompra']['total'] * ($descuento / 100);
				$res['descuento_monto_html'] = CakeNumber::currency($oc['OrdenCompra']['total'] * ($descuento / 100) , 'CLP');
			}

			$res['monto_pagar'] = round($oc['OrdenCompra']['total'] - $res['descuento_monto']);
			$res['monto_pagar_html'] = CakeNumber::currency($oc['OrdenCompra']['total'] - $res['descuento_monto'] , 'CLP');

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

		$asunto = '[NDRZ] OC para '. strtolower($ocs['OrdenCompra']['razon_social_empresa']).' lista para revisión.';

		if (Configure::read('debug') > 0) {
			$asunto = '[NDRZ-DEV] OC para '. strtolower($ocs['OrdenCompra']['razon_social_empresa']).' lista para revisión.';
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
		
		if (Configure::read('debug') > 0) {
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
		
		if (Configure::read('debug') > 0) {
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

		if (Configure::read('debug') > 0) {
			$asunto = sprintf('[NDRZ-DEV] Hay %d ventas con productos en stockout.', count($ventas));
		}else{
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

				
		$validadores = Hash::extract($oc['Proveedor'], 'meta_emails.{n}[tipo=validador].email');
		$receptores  = Hash::extract($oc['Proveedor'], 'meta_emails.{n}[tipo=destinatario].email');
		$cc          = Hash::extract($oc['Proveedor'], 'meta_emails.{n}[tipo=copia].email');
		$bcc         = Hash::extract($oc['Proveedor'], 'meta_emails.{n}[tipo=copia oculta].email');

		$to = (!empty($validadores)) ? $validadores : $receptores;

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

		}else if (!ClassRegistry::init('Token')->validar_token($gettoken['Token']['token'])){
			# creamos un token de acceso vía email
			$token = ClassRegistry::init('Token')->crear_token_proveedor($oc['Proveedor']['id'], $oc['Tienda']['id'])['token'];

		}else{
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
		}else{
			$asunto = sprintf('[OC] #%d Se ha creado una Orden de compra desde Nodriza Spa', $id);
		}
		
		
		if (Configure::read('debug') > 0) {
			if ($recordatorio) {
				$asunto = sprintf('[OC-DEV-RECORDATORIO] #%d Se ha creado una Orden de compra desde Nodriza Spa', $id);
			}else{
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
		
		if (Configure::read('debug') > 0) {
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
	 * [guardarEmailValidado description]
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
		
		if (Configure::read('debug') > 0) {
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
		
		if ( $this->Correo->save(array(
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
		)) ) {
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
	public function generar_pdf($oc = array(), $nombreOC = '') {

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

		if (!isset($this->request->query['access_token']) || !ClassRegistry::init('Token')->validar_token($this->request->query['access_token'])) {
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
			
			# si la cantidad de itemes rechazado es igual a la cantidad de produtos pedidos se devuelve toda la OC
			if ($total_rechazados == $total_solicitados) {
				$this->request->data['OrdenCompra']['estado'] = 'cancelada';
			}
			
			# flujo para cuando un producto tenga un error de precio
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
				$nuevaOC['OrdenCompra']['descuento_monto']    = obtener_iva( ($total_neto + $nuevaOC['OrdenCompra']['iva']) , $nuevaOC['OrdenCompra']['descuento']);
				$nuevaOC['OrdenCompra']['total']              = ($total_neto - $nuevaOC['OrdenCompra']['descuento_monto']) + $nuevaOC['OrdenCompra']['iva'];
				$nuevaOC['OrdenCompra']['estado']             = 'iniciado';
				$nuevaOC['OrdenCompra']['fecha']              = date('Y-m-d');
				$nuevaOC['OrdenCompra']['vendedor']           = '(Auto) Nodriza Spa';
				$nuevaOC['OrdenCompra']['validado_proveedor'] = 1;

				# quitamos el id
				unset($nuevaOC['OrdenCompra']['id']);
				unset($nuevaOC['OrdenCompra']['created']);
				unset($nuevaOC['OrdenCompra']['modified']);
				unset($nuevaOC['OrdenCompra']['moneda_id']);

			}
			

			# Continuan sólo los itemes aceptados
			if (count($itemsAceptados) > 0) {

				$this->request->data['VentaDetalleProducto'] = $itemsAceptados;

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
				$this->request->data['OrdenCompra']['descuento_monto'] = obtener_iva( ($total_neto + $this->request->data['OrdenCompra']['iva']) , $this->request->data['OrdenCompra']['descuento']);
				$this->request->data['OrdenCompra']['total']           = ($total_neto - $this->request->data['OrdenCompra']['descuento_monto']) + $this->request->data['OrdenCompra']['iva'];

			}
			
			if ($this->OrdenCompra->saveAll($this->request->data, array('deep' => true))) {


				# Flujo para cuando un producto no tenga stock
				if ($total_stockout > 0) {
					# Notificar a ventas para que coordine con el cliente
					$ventasNotificar = $this->OrdenCompra->obtener_ventas_por_productos($oc['OrdenCompra']['parent_id'], Hash::extract($itemes['stockout'], '{n}.venta_detalle_producto_id'));
				}


				# notificar stockout a ventas
				if (!empty($ventasNotificar)) {

					$emailsVentas = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('ventas');

					if (!empty($emailsVentas)) {
						$enviado = $this->guardarEmailStockout($id, $ventasNotificar, $itemes['stockout'], $emailsVentas);
					}
				}

				# crear la nueva OC
				if (!empty($nuevaOC)) {
					
					# Notificar nueva OC
					$emailsNotificar = array($nuevaOC['OrdenCompra']['email_comercial']);

					$this->OrdenCompra->create();
					if ($this->OrdenCompra->saveAll($nuevaOC, array('deep' => true)) && !empty($emailsNotificar) ) {
						$this->guardarEmailRevision($nuevaOC, $emailsNotificar);
					}
				}

				# Notifcar rechazo completo a comerial
				if ($this->request->data['OrdenCompra']['estado'] == 'cancelada') {
					$email_comercial = $oc['OrdenCompra']['email_comercial'];
					$this->guardarEmailRechazoProveedor($id, array($email_comercial));

					# Mostramos mensaje de co guardada
					$redirect = sprintf('%ssocio/oc/%d?access_token=%s&success=true',obtener_url_base(), $id, $this->request->query['access_token']);
					$this->redirect($redirect);
				}

				# Genera el PDF
				if ($this->request->data['OrdenCompra']['estado'] == 'validado_proveedor') {

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
							'Proveedor'
						)
					));
					
					# Notificar a finanzas (en espera)
					$emailsFinanzas = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('pagar_oc');

					if (!empty($emailsFinanzas)) {
						$this->guardarEmailValidadoProveedor($oc, $emailsFinanzas);
					}
					
					$pdfOc = 'orden_compra_' . $id . '_' . strtolower(Inflector::slug($oc['Proveedor']['nombre'])) . '_' . rand(1,100) . '.pdf';

					$this->generar_pdf($oc, $pdfOc);

					$this->request->data['OrdenCompra']['pdf'] = $pdfOc;
					
					# Redirigimos al PDF
					$redirect = sprintf('%ssocio/oc/pdf/%d/%d?access_token=%s',obtener_url_base(), $id, $oc['OrdenCompra']['proveedor_id'], $this->request->query['access_token']);
					$this->redirect($redirect);
				}


			}else{
				$this->redirect(array('action' => 'validate_supplier', $id, '?' => array('access_token' => $this->request->query['access_token'], 'success' => false), 'admin' => false, 'socio' => false, 'prefix' => null));
			}

		}else{

			$qry = array(
				'conditions' => array(
					'OrdenCompra.id' => $id,
					'OrdenCompra.validado_proveedor' => 0,
					'OrdenCompra.estado' => 'asignacion_moneda'
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
		#prx($this->request->data);

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

		if (!isset($this->request->query['access_token']) || !ClassRegistry::init('Token')->validar_token($this->request->query['access_token'])) {
			throw new Exception("El token de acceso no es válido", 404);
			exit;
		}

		$oc = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id_oc,
				'OrdenCompra.proveedor_id' => $proveedor_id
			),
			'contain' => array(
				'Moneda',
				'VentaDetalleProducto',
				'Administrador',
				'Venta' => array(
					'VentaDetalle'
				),
				'Tienda',
				'Proveedor'
			)
		));

		if (empty($oc)) {
			throw new Exception(sprintf("No es posible obtener la OC solicitada. Póngase en contacto con %s <%s> de %s", $oc['OrdenCompra']['nombre_validado'], $oc['OrdenCompra']['email_comercial'], $oc['Tienda']['nombre']) , 504);
			exit;
		}

		# intentamos crearla nuevamente
		if (empty($oc['OrdenCompra']['pdf'])) {

			$pdfOc = 'orden_compra_' . $id_oc . '_' . strtolower(Inflector::slug($oc['Proveedor']['nombre'])) . '_' . rand(1,100) . '.pdf';

			$this->generar_pdf($oc, $pdfOc);

			$oc['OrdenCompra']['pdf'] = $pdfOc;

			$this->OrdenCompra->id = $id_oc;
			$this->OrdenCompra->saveField('pdf', $pdfOc);
		}

		$url = obtener_url_base();		

		$this->layout = 'backend/socio';
		
		$this->set(compact('oc', 'url'));
	}

}