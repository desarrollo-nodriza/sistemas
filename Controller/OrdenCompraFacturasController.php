<?php
App::uses('AppController', 'Controller');
App::uses('PagosController', 'Controller');

class OrdenCompraFacturasController extends AppController
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


	public function admin_index()
	{	

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompraFacturas', 'index');
		}

		$opt		= array(
			'recursive'			=> 0,
			'conditions' => array(
				'OrdenCompraFactura.tipo_documento' => 33 // mostramos solo facturas
			),
			'contain' => array(
				'Proveedor' => array(
					'fields' => array(
						'Proveedor.id', 'Proveedor.nombre'
					)
				),
				'OrdenCompra' => array(
					'Tienda' => array('fields' => array('Tienda.rut')),
					'Proveedor' => array('fields' => array('Proveedor.rut_empresa')),
					'Moneda' => array('fields' => array('Moneda.tipo')),
					'Pago' => array('fields' => array('Pago.pagado')), 
					'fields' => array(
						'OrdenCompra.id', 'OrdenCompra.tienda_id', 'OrdenCompra.proveedor_id', 'OrdenCompra.moneda_id'
					)
				),
				'Pago' => array(
					'fields' => array(
						'Pago.id', 'Pago.pagado', 'Pago.fecha_pago', 'Pago.moneda_id'
					),
					'Moneda' => array('fields' => array('Moneda.tipo'))
				)
			),
			'order' => array('OrdenCompraFactura.id' => 'DESC'),
			'fields' => array(
				'OrdenCompraFactura.id', 'OrdenCompraFactura.proveedor_id', 'OrdenCompraFactura.orden_compra_id', 'OrdenCompraFactura.folio', 'OrdenCompraFactura.monto_pagado', 'OrdenCompraFactura.tipo_documento', 'OrdenCompraFactura.pagada', 'OrdenCompraFactura.emisor', 'OrdenCompraFactura.receptor', 'OrdenCompraFactura.created', 'OrdenCompraFactura.monto_facturado'
			)
		);

		foreach ($this->request->params['named'] as $campo => $valor) {
			switch ($campo) {
				case 'id':
					$opt = array_replace_recursive($opt, array(
						'conditions' => array(
							'OrdenCompraFactura.id' => $valor
						)
					));
					break;
				case 'oc':
					$opt = array_replace_recursive($opt, array(
						'conditions' => array(
							'OrdenCompraFactura.orden_compra_id' => explode(',', $valor)
						)
					));
					break;

				case 'prov':
					$opt = array_replace_recursive($opt, array(
						'joins' => array(
							array('table' => 'orden_compras',
						        'alias' => 'orden_compra',
						        'type' => 'INNER',
						        'conditions' => array(
						            'orden_compra.id = OrdenCompraFactura.orden_compra_id',
						            'orden_compra.proveedor_id' => $valor
						        )
						    )
						)
					));
					break;

				case 'folio':
					$opt = array_replace_recursive($opt, array(
						'conditions' => array(
							'OrdenCompraFactura.folio' => explode(',', $valor)
						)
					));
					break;

				case 'sta':
					$opt = array_replace_recursive($opt, array(
						'conditions' => array(
							'OrdenCompraFactura.pagada' => ($valor == 'y') ? 1 : 0
						)
					));
					break;

				case 'dtf':
					$opt = array_replace_recursive($opt, array(
						'conditions' => array(
							'OrdenCompraFactura.created >=' => $valor 
						)
					));
					break;

				case 'dtf':
					$opt = array_replace_recursive($opt, array(
						'conditions' => array(
							'OrdenCompraFactura.created <=' => $valor 
						)
					));
					break;
			}
		}

		$this->paginate = $opt;

		BreadcrumbComponent::add('Pagos ');

		$facturas	= $this->paginate();
		
		# Rescatamos los DTE de libre dte
		$libreDte = $this->Components->load('LibreDte');
		$libreDte->crearCliente($this->Session->read('Tienda.facturacion_apikey'));
		
		foreach ($facturas as $if => $factura) {

			$emisor   = $factura['OrdenCompraFactura']['emisor'];
			$tipo_dte = $factura['OrdenCompraFactura']['tipo_documento']; // Solo Facturas
			$folio    = $factura['OrdenCompraFactura']['folio'];
			$receptor = $factura['OrdenCompraFactura']['receptor'];

			$facturas[$if]['OrdenCompraFactura']['neto']        = monto_neto($factura['OrdenCompraFactura']['monto_facturado']);
			$facturas[$if]['OrdenCompraFactura']['iva']         = obtener_iva($facturas[$if]['OrdenCompraFactura']['neto']);
			$facturas[$if]['OrdenCompraFactura']['bruto']       = $factura['OrdenCompraFactura']['monto_facturado'];
			$facturas[$if]['OrdenCompraFactura']['total_items'] = 0;
			$facturas[$if]['OrdenCompraFactura']['anulado']     = 0;

			// Estado de la factura
			if ( $factura['OrdenCompraFactura']['pagada'] ) {
				$facturas[$if]['OrdenCompraFactura']['estados'][] = 'pagado';
			}
			elseif ( empty($factura['Pago']) ){
				$facturas[$if]['OrdenCompraFactura']['estados'][] = 'sin_pago';
			}
			else {
			
				foreach ($factura['Pago'] as $ip => $p) {

					if (!isset($p['Moneda']['tipo'])) {
						$facturas[$if]['OrdenCompraFactura']['estados'][] = 'sin_moneda';
					}
					elseif ($p['Moneda']['tipo'] == 'agendar' && $p['fecha_pago'] == '') {
						$facturas[$if]['OrdenCompraFactura']['estados'][] = 'agendamineto_pendiente';
					}
					elseif ($p['Moneda']['tipo'] == 'agendar' && $p['fecha_pago'] != '' && $p['pagado'] == 0) {
						$facturas[$if]['OrdenCompraFactura']['estados'][] = 'agendado';
					}
					elseif ($p['Moneda']['tipo'] == 'agendar' && $p['fecha_pago'] != '' && $p['pagado'] == 1) {
						$facturas[$if]['OrdenCompraFactura']['estados'][] = 'pagado';
					}
					elseif ($p['Moneda']['tipo'] == 'esperar' && $p['fecha_pago'] != '' && $p['pagado'] == 0) {
						$facturas[$if]['OrdenCompraFactura']['estados'][] = 'pago_pendiente';
					}
					elseif ($p['Moneda']['tipo'] == 'esperar' && $p['pagado'] == 1) {
						$facturas[$if]['OrdenCompraFactura']['estados'][] = 'pagado';
					}
					elseif ($p['Moneda']['tipo'] == 'pagar' && $p['pagado'] == 0) {
						$facturas[$if]['OrdenCompraFactura']['estados'][] = 'pago_pendiente';
					}
					elseif ($p['Moneda']['tipo'] == 'pagar' && $p['pagado'] == 1) {
						$facturas[$if]['OrdenCompraFactura']['estados'][] = 'pagado';
					}
				}

				$facturas[$if]['OrdenCompraFactura']['estados'] = array_unique($facturas[$if]['OrdenCompraFactura']['estados']);

			}

			$res = $libreDte->obtener_documento_recibido($emisor, $tipo_dte, $folio, $receptor);
			
			if (empty($res)) {
				continue;
			}

			$facturas[$if]['OrdenCompraFactura']['neto']        = $res['neto'];
			$facturas[$if]['OrdenCompraFactura']['iva']         = $res['iva'];
			$facturas[$if]['OrdenCompraFactura']['bruto']       = $res['total'];
			$facturas[$if]['OrdenCompraFactura']['total_items'] = (!empty($res['detalle'])) ? array_sum(Hash::extract($res['detalle'], '{n}.QtyItem')) : 0;
			$facturas[$if]['OrdenCompraFactura']['anulado']     = $res['anulado'];
		}

		$proveedores = ClassRegistry::init('Proveedor')->find('list');

		$folios = array_unique(ClassRegistry::init('OrdenCompraFactura')->find('list'));

		$this->set(compact('facturas', 'folios', 'ocs', 'proveedores'));
	}


	public function admin_view($id = '')
	{
		if ( ! $this->OrdenCompraFactura->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			if (empty($this->request->data['Pago'])) {
				$this->Session->setFlash('No ha asignado pagos a la factura', null, array(), 'warning');
				$this->redirect(array('action' => 'view', $id));
			}

			# Caluclamos el total pagado
			$this->request->data['OrdenCompraFactura']['monto_pagado'] = array_sum(Hash::extract($this->request->data['OrdenCompraPago'], '{n}.monto_pagado'));


			if ($this->request->data['OrdenCompraFactura']['monto_pagado'] == $this->request->data['OrdenCompraFactura']['monto_facturado']) {
				$this->request->data['OrdenCompraFactura']['pagada'] = 1;
			}

			if ( $this->OrdenCompraFactura->saveAll($this->request->data) )
			{
				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		else
		{
			$this->request->data	= $this->OrdenCompraFactura->find('first', array(
				'conditions'	=> array('OrdenCompraFactura.id' => $id),
				'contain' => array(
					'OrdenCompra' => array(
						'Tienda' => array('fields' => array('Tienda.rut')),
						'OrdenCompraPago' => array(
							'Moneda',
							'fields' => array(
								'OrdenCompraPago.id',
								'OrdenCompraPago.identificador',
								'OrdenCompraPago.cuenta',
								'OrdenCompraPago.monto_real_pagado',
								'OrdenCompraPago.monto_pagado',
								'OrdenCompraPago.fecha_pago',
								'OrdenCompraPago.finalizado',
								'OrdenCompraPago.adjunto'
							)
						),
						'Pago' => array(
							'fields' => array(
								'Pago.id', 
								'Pago.monto_pagado',
								'Pago.adjunto',
								'Pago.fecha_pago',
								'Pago.identificador',
								'Pago.pagado'
							)
						),
						'Moneda'
					),
					'Pago' => array(
						'fields' => array(
							'Pago.id',
							'Pago.monto_pagado',
							'Pago.orden_compra_adjunto_id',
							'Pago.fecha_pago',
							'Pago.identificador',
							'Pago.adjunto',
							'Pago.pagado'
						),
						'CuentaBancaria' => array(
							'fields' => array(
								'CuentaBancaria.alias',
								'CuentaBancaria.numero_cuenta'
							)
						),
						'OrdenCompraAdjunto' => array(
							'fields' => array(
								'OrdenCompraAdjunto.id',
								'OrdenCompraAdjunto.adjunto'
							)
						),
						'Moneda'
					),
					'Proveedor' => array('fields' => array('Proveedor.rut_empresa', 'Proveedor.nombre')),
				)
			));
		}
		
		$this->request->data['OrdenCompraFactura']['monto_asignado'] = array_sum(Hash::extract($this->request->data['Pago'], '{n}.FacturasPago.monto_pagado'));

		# Rescatamos los DTE de libre dte
		$libreDte = $this->Components->load('LibreDte');
		$libreDte->crearCliente($this->Session->read('Tienda.facturacion_apikey'));

		$emisor   = $this->rutSinDv($this->request->data['Proveedor']['rut_empresa']);
		$tipo_dte = $this->request->data['OrdenCompraFactura']['tipo_documento']; // Facturas
		$folio    = $this->request->data['OrdenCompraFactura']['folio'];
		$receptor = $this->rutSinDv($this->request->data['OrdenCompra']['Tienda']['rut']);

		$res = $libreDte->obtener_documento_recibido($emisor, $tipo_dte, $folio, $receptor, 1);

		$this->request->data['LibreDte'] = $res;
		if (!empty($res)) {
			$this->request->data['LibreDte']['Emisor'] = $libreDte->obtenerContribuyente($res['emisor']);	
		}
		
		BreadcrumbComponent::add('Facturas', '/ordenCompraFacturas');
		BreadcrumbComponent::add('Detalles ');
	}


	/**
	 * 
	 */
	public function admin_procesar()
	{	
		# solo metodos post
		if (!$this->request->is('post')) {
			$this->Session->setFlash('Acción no permitida.', null, array(), 'warning');
			$this->redirect(array('action' => 'index'));
		}

		$id_facturas = Hash::extract($this->request->data, 'OrdenCompraFactura.{n}.id');

		# no hay ids agregados
		if ( count($id_facturas) == 0 ) {
			$this->Session->setFlash('Debe seleccionar uno o más facturas.', null, array(), 'warning');
			$this->redirect(array('action' => 'index'));
		}


		$facturas = $this->OrdenCompraFactura->find('all', array(
			'conditions' => array(
				'OrdenCompraFactura.id' => $id_facturas
			),
			'fields' => array(
				'OrdenCompraFactura.monto_facturado', 'OrdenCompraFactura.monto_pagado', 'OrdenCompraFactura.orden_compra_id', 'OrdenCompraFactura.proveedor_id', 'OrdenCompraFactura.tipo_documento', 'OrdenCompraFactura.folio', 'OrdenCompraFactura.proveedor_id', 'OrdenCompraFactura.pagada'
			),
			'contain' => array(
				'Proveedor' => array(
					'fields' => array(
						'Proveedor.nombre'
					)
				),
				'Pago' => array(
					'fields' => array(
						'Pago.id',
						'Pago.monto_pagado',
						'Pago.fecha_pago',
						'Pago.identificador',
						'Pago.adjunto',
						'Pago.pagado',
						'Pago.moneda_id'
					),
					'CuentaBancaria' => array(
						'fields' => array(
							'CuentaBancaria.alias',
							'CuentaBancaria.numero_cuenta'
						)
					),
					'Moneda' => array(
						'fields' => array(
							'Moneda.nombre',
							'Moneda.id'
						)
					)
				),
				'OrdenCompra' => array(
					'Moneda' => array(
						'fields' => array(
							'Moneda.nombre',
							'Moneda.id'
						)
					),
					'Pago' => array(
						'fields' => array(
							'Pago.id',
							'Pago.monto_pagado',
							'Pago.fecha_pago',
							'Pago.identificador',
							'Pago.adjunto',
							'Pago.pagado',
							'Pago.moneda_id'
						),
						'CuentaBancaria' => array(
							'fields' => array(
								'CuentaBancaria.alias',
								'CuentaBancaria.numero_cuenta'
							)
						),
						'Moneda' => array(
							'fields' => array(
								'Moneda.nombre',
								'Moneda.id'
							)
						)
					),
					'fields' => array(
						'OrdenCompra.*'
					)
				)
			)
		));

		$pagosConfigurados = true;

		$total_facturado = array_sum(Hash::extract($facturas, '{n}.OrdenCompraFactura.monto_facturado'));
		$total_pagado    = array_sum(Hash::extract($facturas, '{n}.OrdenCompraFactura.monto_pagado'));
		
		# si las facturas ya estan pagadas no hay nada que configurar
		if ($total_pagado >= $total_facturado) {
			$this->Session->setFlash('Ya existe la relación facturas-pagos.', null, array(), 'warning');
			$this->redirect(array('action' => 'index', 'id' => $id_facturas));
		}

		$pagos = array();
		# Pagos relacionado a la oc de la factura
		$pagosOc = Hash::extract($facturas, '{n}.OrdenCompra.Pago.{n}');
		
		# Pagos realcionado a la factura
		$pagosFacturas = Hash::extract($facturas, '{n}.Pago.{n}');
		
		foreach ($pagosOc as $i => $p) {
			if (!empty($p)) {
				$pagos[$p['id']]['Pago'] = $p;
			}
		}
		
		foreach ($pagosFacturas as $i => $p) {
			if (!empty($p)) {
				$pagos[$p['id']]['Pago'] = $p;
			}
		}

		# si no estan todos los pagos agendados o configurados se redirecciona
		/*if (!$pagosConfigurados) {
			$this->Session->setFlash('Se necesita configurar uno o varios pagos.', null, array(), 'success');
			$this->redirect(array('controller' => 'pagos', 'action' => 'configuracion_multiple', 'id' => $id_facturas));
		}*/
		
		BreadcrumbComponent::add('Facturas', '/ordenCompraFacturas');
		BreadcrumbComponent::add('Asignar pagos ');

		$cuenta_bancarias = ClassRegistry::init('CuentaBancaria')->find('list', array('conditions' => array('activo' => 1)));
		$monedas = ClassRegistry::init('Moneda')->find('list', array('conditions' => array('activo' => 1, 'tipo !=' => 'esperar')));

		$this->set(compact('facturas', 'pagos', 'cuenta_bancarias', 'monedas'));

	}


	public function admin_relacionar_facturas_pagos()
	{
		# solo metodos post
		if (!$this->request->is('post')) {
			$this->Session->setFlash('Acción no permitida.', null, array(), 'warning');
			$this->redirect($this->referer('/', true));
		}

		$this->Session->setFlash('La relación Facturas - Pagos ha finalizado con éxito.', null, array(), 'success');
		$this->redirect(array('action' => 'index'));

		$facturas = array();
		
		foreach ($this->request->data['OrdenCompraFactura'] as $if => $factura) {
			$facturas[$if]['OrdenCompraFactura']['id'] = $factura['id'];

			foreach ($this->request->data['Pago'] as $ip => $pago) {
				$facturas[$if]['Pago'][$ip]['pago_id'] = $pago['id'];				
			}
		}		
		
		if ($this->OrdenCompraFactura->saveMany($facturas, array('deep' => true, 'callbacks' => false))) {
			$this->Session->setFlash('La relación Facturas - Pagos ha finalizado con éxito.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}else{
			$this->Session->setFlash('No fue posible guardar la relación. Intente nuevamente.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}
	}


	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->OrdenCompraFactura->create();
			if ( $this->OrdenCompraFactura->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Facturas', '/ordenCompraFacturas');
		BreadcrumbComponent::add('Agregar ');
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->OrdenCompraFactura->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->OrdenCompraFactura->save($this->request->data) )
			{
				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		else
		{
			$this->request->data	= $this->OrdenCompraFactura->find('first', array(
				'conditions'	=> array('OrdenCompraFactura.id' => $id)
			));
		}

		BreadcrumbComponent::add('Pagos', '/ordenCompraFacturas');
		BreadcrumbComponent::add('Editar ');
	}

	public function admin_delete($id = null)
	{
		$this->OrdenCompraFactura->id = $id;
		if ( ! $this->OrdenCompraFactura->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->OrdenCompraFactura->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->OrdenCompraFactura->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->OrdenCompraFactura->_schema);
		$modelo			= $this->OrdenCompraFactura->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}

	public function admin_notificar_pagos($id)
	{
		$factura = $this->OrdenCompraFactura->find('first', array(
			'conditions' => array(
				'OrdenCompraFactura.id' => $id
			),
			'contain' => array(
				'Pago' => array(
					'fields' => array(
						'Pago.id'
					)
				)
			)
		));

		$pagosController = new PagosController;

		# Notificamos los pagos si corresponde
		foreach ($factura['Pago'] as $ip => $p) {
			$pagosController->guardarEmailPagoFactura($p['id']);
			break;
		}

		$this->Session->setFlash('Si corresponde, la notificación fue enviada con éxito.', null, array(), 'success');
		$this->redirect($this->referer('/', true));
	}


	public static function obtener_factura($id)
	{
		if ( ! ClassRegistry::init('OrdenCompraFactura')->exists($id) )
		{
			throw new CakeException("El id de la factura no existe");
		}

		$libreDte = $this->Components->load('LibreDte');
	}
}
