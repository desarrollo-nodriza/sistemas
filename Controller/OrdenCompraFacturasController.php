<?php
App::uses('AppController', 'Controller');
App::uses('PagosController', 'Controller');

class OrdenCompraFacturasController extends AppController
{	

	public $components = array('ApiLibreDte');

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
						'conditions' => array(
							'OrdenCompraFactura.proveedor_id' => explode(',', $valor)
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

				case 'sub_sta':
					
					if ($valor == 'pagado') {
						$opt = array_replace_recursive($opt, array(
							'joins' => array(
								array('table' => 'facturas_pagos',
							        'alias' => 'facturas_pagos',
							        'type' => 'INNER',
							        'conditions' => array(
							            'facturas_pagos.factura_id = OrdenCompraFactura.id',
							        )
							    ),
							    array('table' => 'pagos',
							        'alias' => 'pagos',
							        'type' => 'INNER',
							        'conditions' => array(
							            'pagos.id = facturas_pagos.pago_id',
							            'pagos.pagado = 1'
							        )
							    )
							)
						));

						$opt = array_replace_recursive($opt, array(
							'conditions' => array(
								'OrdenCompraFactura.pagada' => 1
							)
						));
					}

					if ($valor == 'agendado') {
						$opt = array_replace_recursive($opt, array(
							'joins' => array(
								array('table' => 'facturas_pagos',
							        'alias' => 'facturas_pagos',
							        'type' => 'INNER',
							        'conditions' => array(
							            'facturas_pagos.factura_id = OrdenCompraFactura.id',
							        )
							    ),
							    array('table' => 'pagos',
							        'alias' => 'pagos',
							        'type' => 'INNER',
							        'conditions' => array(
							            'pagos.id = facturas_pagos.pago_id',
							            'pagos.pagado' => 0,
							            'pagos.fecha_pago !=' => ''
							        )
							    )
							)
						));

						$opt = array_replace_recursive($opt, array(
							'conditions' => array(
								'OrdenCompraFactura.pagada' => 0
							)
						));
					}

					if ($valor == 'agendamineto_pendiente') {
						$opt = array_replace_recursive($opt, array(
							'joins' => array(
								array('table' => 'facturas_pagos',
							        'alias' => 'facturas_pagos',
							        'type' => 'INNER',
							        'conditions' => array(
							            'facturas_pagos.factura_id = OrdenCompraFactura.id',
							        )
							    ),
							    array('table' => 'pagos',
							        'alias' => 'pagos',
							        'type' => 'INNER',
							        'conditions' => array(
							            'pagos.id = facturas_pagos.pago_id',
							            'pagos.pagado' => 0,
							            'pagos.fecha_pago' => ''
							        )
							    )
							)
						));

						$opt = array_replace_recursive($opt, array(
							'conditions' => array(
								'OrdenCompraFactura.pagada' => 0
							)
						));
					}

					if ($valor == 'pago_pendiente') {
						$opt = array_replace_recursive($opt, array(
							'joins' => array(
								array('table' => 'facturas_pagos',
							        'alias' => 'facturas_pagos',
							        'type' => 'LEFT',
							        'conditions' => array(
							            'facturas_pagos.factura_id = OrdenCompraFactura.id',
							            'facturas_pagos.id' => null
							        )
							    )
							)
						));

						$opt = array_replace_recursive($opt, array(
							'conditions' => array(
								'OrdenCompraFactura.pagada' => 0
							)
						));
					}
					
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
				case 'per_page':
					$opt = array_replace_recursive($opt, array(
						'limit' => $valor,
						'maxLimit' => $valor
						)
					);
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

		$estados_pagos =  array('pagado' => 'Pagado', 'agendado' => 'Pago agendado', 'agendamineto_pendiente' => 'Agendamiento pendiente',  'pago_pendiente' => 'Pago pendiente');

		
		# Almacenar los periodos
		$periodos = [];
		$periodos2 = [];

		$ahora = date('Y-m-28');
		$anno_anterior = date("Y-m-28", strtotime($ahora."-1 year"));

		$ts1 = strtotime($anno_anterior);
		$ts2 = strtotime($ahora);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
		
		$periodos[date('Ym')] = date('Y-m');
		$periodos2[date('Y-m')] = date('Y-m');
		
		# Se crea la lista de periodos desde hace un año
		for ($i=1; $i <= $diff; $i++) 
		{ 
			$pt = strtotime($ahora."-{$i} month");
			$periodos[date("Ym", $pt)] = date("Y-m", $pt);
			$periodos2[date("Y-m", $pt)] = date("Y-m", $pt);
		}

		# Tipos de compras
		$tipo_compras = $this->ApiLibreDte->obtener_estados();
		
		$this->set(compact('facturas', 'folios', 'ocs', 'proveedores', 'estados_pagos', 'periodos', 'tipo_compras', 'periodos2'));
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
						'Tienda' => array('fields' => array('Tienda.rut', 'Tienda.sii_public_key', 'Tienda.sii_private_key', 'Tienda.libredte_token')),
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
	 * admin_obtener_compras_manual
	 *
	 * @param  mixed $periodo 	Año y mes
	 * @return void
	 */
	public function admin_obtener_compras_manual()
	{	

		if (!$this->request->is('post'))
		{
			$this->Session->setFlash('Petición mal ejecutada', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$tienda = ClassRegistry::init('Tienda')->tienda_principal([
			"sincronizar_compras",
			"sii_rut",
			"sii_clave",
			"sii_public_key",
			"sii_private_key",
			"libredte_token"
		]);

		if (!$tienda['Tienda']['sincronizar_compras'])
		{
			$this->Session->setFlash('La tienda no tiene activa la sincronización de compras. Por favor actívela e intente nuevamente', null, array(), 'warning');
			$this->redirect(array('action' => 'index'));
		}
		
		$cert_data = [
			"private" => $tienda['Tienda']['sii_private_key'],
			"public" => $tienda['Tienda']['sii_public_key']
		];

		$pass_data = [
			"rut" => formato_rut($tienda['Tienda']['sii_rut']),
			"clave" => $tienda['Tienda']['sii_clave']
		];

		$this->ApiLibreDte->crearCliente($tienda['Tienda']['libredte_token'], $cert_data, $pass_data, 0);
		
		$pars = [
			"formato" => "json",
			"certificacion" => 0,
			"tipo" => "csv"
		];

		$result = $this->ApiLibreDte->obtenerDocumentosCompras(formato_rut($tienda['Tienda']['sii_rut']), $this->request->data['OrdenCompraFacturas']['periodo'], 33, $this->request->data['OrdenCompraFacturas']['tipo_compra'], $pars);
	
		if ($result['httpCode'] != 200)
		{
			$this->Session->setFlash($result['body']['message'], null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if (!isset($result['body']['data']))
		{
			$this->Session->setFlash('No fue posible obtener los documentos', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		# consultamos y guardamos los folios que ya estan en nuestra bd
		$guardar = [];
		$actualizar = 0;
		foreach ($result['body']['data'] as $id => $doc) 
		{
			
			$guardar[$id] = array(
				'DteCompra' => array(
					'tipo_documento'      => $doc['detTipoDoc'],
					'rut_emisor'          => $doc['detRutDoc'],
					'dv_emisor'           => $doc['detDvDoc'],
					'razon_social_emisor' => $doc['detRznSoc'],
					'folio'               => $doc['detNroDoc'],
					'fecha_emision'       => date('Y-m-d', strtotime(str_replace('/','-', $doc['detFchDoc']))),
					'fecha_recepcion'     => date('Y-m-d H:i:s', strtotime(str_replace('/','-', $doc['detFecRecepcion']))),
					'monto_exento'        => $doc['detMntExe'],
					'monto_neto'          => $doc['detMntNeto'],
					'monto_iva'           => $doc['detMntIVA'],
					'monto_total'         => $doc['detMntTotal'],
					'estado'			  => $this->request->data['OrdenCompraFacturas']['tipo_compra']
				)
			);

			$qry_existen = ClassRegistry::init('DteCompra')->find('first', array(
				'conditions' => array(
					'rut_emisor' => $doc['detRutDoc'],
					'folio' => $doc['detNroDoc'],
				),
				'fields' => array(
					'id'
				)
			));

			# si existe, lo actualizamos si corresponde
			if ($qry_existen)
			{
				$guardar[$id] = array_replace_recursive($guardar[$id], [
					'DteCompra' => [
						'id' => $qry_existen['DteCompra']['id']
					]
				]);
			}
		}
		
		if (ClassRegistry::init('DteCompra')->saveMany($guardar)) 
		{	

			$total_actualizados = count(Hash::extract($guardar, '{n}.DteCompra.id'));
			$total_nuevos = count($guardar) - $total_actualizados;
			
			$this->Session->setFlash(sprintf('Se crearon %d documentos, y %d documentos actualizados.', $total_nuevos, $total_actualizados), null, array(), 'success');
			$this->redirect(array('action' => 'index'));	
		}
		else
		{
			$this->Session->setFlash('No se encontraron documentos nuevos para el periodo', null, array(), 'warning');
			$this->redirect(array('action' => 'index'));
		}
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

	
	/**
	 * admin_relacionar_facturas_pagos
	 *
	 * @return void
	 */
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

	
	/**
	 * admin_exportar_compras
	 *
	 * @return void
	 */
	public function admin_exportar_compras()
	{	
		set_time_limit(0);

		ini_set('memory_limit', '-1');
		
		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtro('ordenCompraFacturas', 'exportar_compras', 'OrdenCompraFacturas');
		}

		$opts = array(
			
		);

		foreach ($this->request->params['named'] as $param => $value) 
		{
			switch ($param) {
				case 'periodo':

					$inicio_perido = date('Y-m-01', strtotime($value));
					$fin_periodo = date('Y-m-t', strtotime($value));

					$opts = array_replace_recursive($opts, array(
						'conditions' => array(
							'fecha_emision BETWEEN ? AND ?' => array($inicio_perido, $fin_periodo)
						)
					));
					break;
				case 'tipo_compra':
					$opts = array_replace_recursive($opts, array(
						'conditions' => array(
							'estado' => trim($value)
						)
					));
					break;

				default:
					# code...
					break;
			}
		}

		$datos			= ClassRegistry::init('DteCompra')->find('all', $opts);
		$campos			= array_keys(ClassRegistry::init('DteCompra')->_schema);
		$modelo			= ClassRegistry::init('DteCompra')->alias;	
		
		$this->set(compact('datos', 'campos', 'modelo'));
	}

		
	/**
	 * admin_add
	 *
	 * @return void
	 */
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

		set_time_limit(0);

		ini_set('memory_limit', '-1');

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompraFacturas', 'exportar');
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
			'order' => array('OrdenCompraFactura.id' => 'DESC')
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
						'conditions' => array(
							'OrdenCompraFactura.proveedor_id' => explode(',', $valor)
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

				case 'sub_sta':
					
					if ($valor == 'pagado') {
						$opt = array_replace_recursive($opt, array(
							'joins' => array(
								array('table' => 'facturas_pagos',
							        'alias' => 'facturas_pagos',
							        'type' => 'INNER',
							        'conditions' => array(
							            'facturas_pagos.factura_id = OrdenCompraFactura.id',
							        )
							    ),
							    array('table' => 'pagos',
							        'alias' => 'pagos',
							        'type' => 'INNER',
							        'conditions' => array(
							            'pagos.id = facturas_pagos.pago_id',
							            'pagos.pagado = 1'
							        )
							    )
							)
						));

						$opt = array_replace_recursive($opt, array(
							'conditions' => array(
								'OrdenCompraFactura.pagada' => 1
							)
						));
					}

					if ($valor == 'agendado') {
						$opt = array_replace_recursive($opt, array(
							'joins' => array(
								array('table' => 'facturas_pagos',
							        'alias' => 'facturas_pagos',
							        'type' => 'INNER',
							        'conditions' => array(
							            'facturas_pagos.factura_id = OrdenCompraFactura.id',
							        )
							    ),
							    array('table' => 'pagos',
							        'alias' => 'pagos',
							        'type' => 'INNER',
							        'conditions' => array(
							            'pagos.id = facturas_pagos.pago_id',
							            'pagos.pagado' => 0,
							            'pagos.fecha_pago !=' => ''
							        )
							    )
							)
						));

						$opt = array_replace_recursive($opt, array(
							'conditions' => array(
								'OrdenCompraFactura.pagada' => 0
							)
						));
					}

					if ($valor == 'agendamineto_pendiente') {
						$opt = array_replace_recursive($opt, array(
							'joins' => array(
								array('table' => 'facturas_pagos',
							        'alias' => 'facturas_pagos',
							        'type' => 'INNER',
							        'conditions' => array(
							            'facturas_pagos.factura_id = OrdenCompraFactura.id',
							        )
							    ),
							    array('table' => 'pagos',
							        'alias' => 'pagos',
							        'type' => 'INNER',
							        'conditions' => array(
							            'pagos.id = facturas_pagos.pago_id',
							            'pagos.pagado' => 0,
							            'pagos.fecha_pago' => ''
							        )
							    )
							)
						));

						$opt = array_replace_recursive($opt, array(
							'conditions' => array(
								'OrdenCompraFactura.pagada' => 0
							)
						));
					}

					if ($valor == 'pago_pendiente') {
						$opt = array_replace_recursive($opt, array(
							'joins' => array(
								array('table' => 'facturas_pagos',
							        'alias' => 'facturas_pagos',
							        'type' => 'LEFT',
							        'conditions' => array(
							            'facturas_pagos.factura_id = OrdenCompraFactura.id',
							            'facturas_pagos.id' => null
							        )
							    )
							)
						));

						$opt = array_replace_recursive($opt, array(
							'conditions' => array(
								'OrdenCompraFactura.pagada' => 0
							)
						));
					}
					
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
				case 'per_page':
					$opt = array_replace_recursive($opt, array(
						'limit' => $valor,
						'maxLimit' => $valor
						)
					);
					break;
			}
		}

		BreadcrumbComponent::add('Pagos ');

		$facturas	= $this->paginate();


		$datos			= $this->OrdenCompraFactura->find('all', $opt);
		$campos			= array_keys($this->OrdenCompraFactura->_schema);
		$modelo			= $this->OrdenCompraFactura->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}

		
	/**
	 * admin_notificar_pagos
	 *
	 * @param  mixed $id
	 * @return void
	 */
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

	
	/**
	 * obtener_factura
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public static function obtener_factura($id)
	{
		if ( ! ClassRegistry::init('OrdenCompraFactura')->exists($id) )
		{
			throw new CakeException("El id de la factura no existe");
		}

		$libreDte = $this->Components->load('LibreDte');
	}

	
	/**
	 * recepcionar_dte_compra
	 *
	 * @param  mixed $token LibreDTE Token
	 * @param  mixed $cert Datos del certificvado público del SII
	 * @param  mixed $pk Datos de la llave privada del SII 
	 * @param  mixed $facturas Modelo OrdenCompraFactura. Debe incluir OC, Bodega y Tienda.
	 * @return void
	 */
	public function recepcionar_dte_compra($token, $cert, $pk, $facturas = [])
	{
		$this->ApiLibreDte->crearCliente($token, ['cert' => $cert, 'pkey' => $pk]);

		$docs = [];
		$logs = [];
		$result = [];
		

		$log[] = array('Log' => array(
			'administrador' => 'OrdenCompraFactura',
			'modulo' => 'DteCompra',
			'modulo_accion' => 'Init: ' . json_encode($facturas)
		));

		foreach ($facturas as $fac) 
		{	# No tiene registro de facturas recibidas
			if (!$fac['DteCompra'])
				continue;

			# El proveedor no permite recepción automática
			if(!$fac['Proveedor']['aceptar_dte'])
				continue;

			# si la factura de compra ya esta registrada se omite
			if ($fac['DteCompra']['estado'] == 'REGISTRO')
				continue;
			

			$margen_proveedor = ($fac['Proveedor']['margen_aceptar_dte']) ? $fac['Proveedor']['margen_aceptar_dte'] : 500;

			$margen_min = $fac['OrdenCompraFactura']['monto_facturado'] - $margen_proveedor;
			$margen_max = $fac['OrdenCompraFactura']['monto_facturado'] + $margen_proveedor;

			# Si el monto de la factura es diferente a la registrada en el SII no se recepciona. Se deja un margen de error
			if($fac['DteCompra']['monto_total'] < $margen_min || $fac['DteCompra']['monto_total'] > $margen_max)
				continue;

			$docs[] = [
				'TipoDTE' => (int) $fac['DteCompra']['tipo_documento'],
				'Folio' => $fac['DteCompra']['folio'],
				'FchEmis' => $fac['DteCompra']['fecha_emision'],
				'RUTEmisor' => sprintf('%s-%s', $fac['DteCompra']['rut_emisor'], $fac['DteCompra']['dv_emisor']),
				'RUTRecep' => formato_rut($fac['OrdenCompra']['Tienda']['rut']),
				'MntTotal' => $fac['DteCompra']['monto_total'],
				'EstadoRecepDTE' => 'ERM',
				'RecepDTEGlosa' => sprintf('Recibido en bodega %s el %s a las %s', $fac['OrdenCompra']['Bodega']['nombre'], date('Y-m-d'), date('H:i:s'))
			];
			
		}

		$log[] = array('Log' => array(
			'administrador' => 'OrdenCompraFactura',
			'modulo' => 'DteCompra',
			'modulo_accion' => 'Process: ' . json_encode($docs)
		));
		
		if (!empty($docs))
		{
			$result = $this->ApiLibreDte->cambiarEstadoDteCompra($docs);
		}

		$log[] = array('Log' => array(
			'administrador' => 'OrdenCompraFactura',
			'modulo' => 'DteCompra',
			'modulo_accion' => 'Result: ' . json_encode($result)
		));

		# Guardamos los logs
		ClassRegistry::init('Log')->saveMany($log);

		return $result;
	}

	/**
     * Elimina una factura
     * Endpoint: /api/facturas-oc/delete/:id.json
     * @param  [type] $id id externo del producto
     */
    public function api_delete($id) {
    	
    	$token = '';

    	if (isset($this->request->query['token'])) {
    		$token = $this->request->query['token'];
    	}

    	# Existe token
		if (!isset($token)) {
			$response = array(
				'code'    => 401, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($token)) {
			$response = array(
				'code'    => 401, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

		if (!$this->OrdenCompraFactura->exists($id))
		{
			$response = array(
				'code'    => 404, 
				'message' => 'Factura no encontrada'
			);

			throw new CakeException($response);
		}

		$res = array();

		if (!$this->OrdenCompraFactura->delete($id))
		{
			$response = array(
				'code'    => 401, 
				'message' => 'No fue posible eliminar la Factura. Intente nuevamente.'
			);

			throw new CakeException($response);
		}

        $this->set(array(
            'response' => array(
				'code' => 200,
            	'message' => sprintf('La Factura #%s fue eliminada exitosamente.', $id)
			),
            '_serialize' => array('response')
        ));
			
    }
}
