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

	public function admin_index()
	{	

		// Filtrado de oc por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ordenCompras', 'index');
		}

		$paginate		= array(
			'recursive'			=> -1,
			'contain' => array(
				'ChildOrdenCompra' => array(
					'fields' => array(
						'ChildOrdenCompra.id',
						'ChildOrdenCompra.estado',
						'ChildOrdenCompra.created',
						'ChildOrdenCompra.tienda_id',
						'ChildOrdenCompra.parent_id',
						'ChildOrdenCompra.administrador_id'
					),
					'Administrador' => array(
						'fields' => array(
							'Administrador.nombre'
						)
					),
					'Tienda' => array(
						'fields' => array(
							'Tienda.nombre'
						)
					)
				),
				'Administrador' => array(
					'fields' => array(
						'Administrador.nombre'
					)
				),
				'Tienda' => array(
					'fields' => array(
						'Tienda.nombre'
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
				'OrdenCompra.oc_manual'
			),
			'order' => array(
				'OrdenCompra.id' => 'DESC'
			)
		);
		
		try {
			$permisos = $this->hasPermission();
		} catch (Exception $e) {
			$permisos = $e;
		}

		# Administrador
		if ($permisos['validate']) {
			$paginate['conditions'] = array(
				'OrdenCompra.estado' => 'iniciado'
			);
		}

		# Finanzas
		if ($permisos['pay']) {
			$paginate['conditions'] = array(
				'OrdenCompra.estado' => 'validado'
			);
		}

		# Bodega
		/*if ($permisos['send']) {
			$paginate['conditions'] = array(
				'OrdenCompra.estado' => 'pagado'
			);
		}*/

		if ($permisos['send'] || $permisos['validate']) {
			#unset($paginate['conditions']);
			$paginate['conditions'] = array(
				'OR' => array(
					array('OrdenCompra.oc_manual' => 0),
					array('OrdenCompra.oc_manual' => 1)
				)
			);
		}

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'id':

						$find = $this->OrdenCompra->find('first', array('conditions' => array('id' => $valor, 'parent_id !=' => ''), 'fields' => array('parent_id')));


						if(!empty($find)) {
							$paginate = array_replace_recursive($paginate, array(
								'conditions' => array(
									'OrdenCompra.id' => $find['OrdenCompra']['parent_id']
								)
							));
						}else{
							$paginate = array_replace_recursive($paginate, array(
								'conditions' => array(
									'OrdenCompra.id' => $valor
								)
							));
						}
						break;
					case 'sta':

						$find = $this->OrdenCompra->find('all', array('conditions' => array('estado' => $valor, 'parent_id !=' => ''), 'fields' => array('parent_id')));

						# Obtenemos los ids padres para filtrarlos.
						if (!empty($find)) {
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array(
								'OrdenCompra.id' => Hash::extract($find, '{n}.OrdenCompra.parent_id')
							)));
						}else{
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('OrdenCompra.estado' => $valor)));
						}

						break;
					case 'dtf':

						$find = $this->OrdenCompra->find('all', array('conditions' => array('created >=' => trim($valor), 'parent_id !=' => ''), 'fields' => array('parent_id')));

						if (!empty($find)) {
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('OrdenCompra.id' => Hash::extract($find, '{n}.OrdenCompra.parent_id'))));	
						}else{
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('OrdenCompra.created >=' => trim($valor))));
						}
						break;
					case 'dtt':

						$find = $this->OrdenCompra->find('all', array('conditions' => array('created <=' => trim($valor), 'parent_id !=' => ''), 'fields' => array('parent_id')));

						if (!empty($find)) {
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('OrdenCompra.id' => Hash::extract($find, '{n}.OrdenCompra.parent_id'))));	
						}else{
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('OrdenCompra.created <=' => trim($valor))));
						}

						
						break;
				}
			}
		}

		$this->paginate = $paginate;


		$ocs = $this->OrdenCompra->find('all', array(
			'conditions' => array(
				'OrdenCompra.parent_id !=' => ''
			),
			'fields' => array(
				'OrdenCompra.id',
				'OrdenCompra.estado',
				'OrdenCompra.created'
			)
		));

		$estados = array(
			'iniciado' => 'En revisión',
			'validado' => 'En proceso de pago',
			'pagado'   => 'Pagado',
			'enviado'  => 'Enviado',
			'recibido' => 'Finalizado'
		);

		BreadcrumbComponent::add('Ordenes de compra ');

		$ordenCompras	= $this->paginate();
		$this->set(compact('ordenCompras', 'ocs', 'estados'));
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
			$this->redirect(array('action' => 'index'));
		}

		$productosActualizado = array();
		$productosNoActualizado = array();
		$res = array(
			'faltantes' => array(),
			'completos' => array()
		);

		if ($this->request->is('post') || $this->request->is('put')) {
			
			foreach ($this->request->data['OrdenCompra'] as $key => $value) {
				
				# Se obtiene los datos de la OC enviada a proveedor
				$pedido = ClassRegistry::init('OrdenComprasVentaDetalleProducto')->find('first', array(
					'conditions' => array(
						'orden_compra_id'           => $id,
						'venta_detalle_producto_id' => $value['VentaDetalleProducto']['id']
					)
				));
				
				# Calcula la cantidad  de productos que faltan por recibir.
				$cantidadFaltante = $pedido['OrdenComprasVentaDetalleProducto']['cantidad'] - $pedido['OrdenComprasVentaDetalleProducto']['cantidad_recibida'];

				if ( $value['Bodega'][0]['cantidad'] < $cantidadFaltante ) {
					$res['faltantes'][] = sprintf('#%s - %s (faltantes: %d)', $value['VentaDetalleProducto']['id'], $pedido['OrdenComprasVentaDetalleProducto']['descripcion'], $cantidadFaltante - $value['Bodega'][0]['cantidad']);

					$res['incompletos'][] = sprintf('#%s - %s (agregados: %d)', $value['VentaDetalleProducto']['id'], $pedido['OrdenComprasVentaDetalleProducto']['descripcion'], $value['Bodega'][0]['cantidad']);
				}

				if ( $value['Bodega'][0]['cantidad'] == $cantidadFaltante && $value['Bodega'][0]['cantidad'] > 0 ) {
					$res['completos'][] = sprintf('#%s - %s (agregados: %d)', $value['VentaDetalleProducto']['id'], $pedido['OrdenComprasVentaDetalleProducto']['descripcion'], $value['Bodega'][0]['cantidad']);
				}

				# Se obtiene la cantdad en la bodega elegida
				$enBodega = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($value['VentaDetalleProducto']['id'], $value['Bodega'][0]['bodega_id']);
				
				# Se modifica los detalles del pedido con la nueva información
				if ( ($pedido['OrdenComprasVentaDetalleProducto']['cantidad_recibida'] + $value['Bodega'][0]['cantidad']) == $pedido['OrdenComprasVentaDetalleProducto']['cantidad']) {
					ClassRegistry::init('OrdenComprasVentaDetalleProducto')->id = $pedido['OrdenComprasVentaDetalleProducto']['id'];
					ClassRegistry::init('OrdenComprasVentaDetalleProducto')->saveField('cantidad_recibida', $pedido['OrdenComprasVentaDetalleProducto']['cantidad']); # Actualiamos la cantidad recibidaTodo
				}else{
					ClassRegistry::init('OrdenComprasVentaDetalleProducto')->id = $pedido['OrdenComprasVentaDetalleProducto']['id'];
					ClassRegistry::init('OrdenComprasVentaDetalleProducto')->saveField('cantidad_recibida', $value['Bodega'][0]['cantidad']); # Actualiamos la cantidad recibida parcial
				}

				# Se crea la entrada de productos
				$precioCompra = $pedido['OrdenComprasVentaDetalleProducto']['precio_unitario'] - $pedido['OrdenComprasVentaDetalleProducto']['descuento_producto'];
				
				if (ClassRegistry::init('Bodega')->crearEntradaBodega($value['VentaDetalleProducto']['id'], $value['Bodega'][0]['bodega_id'], $value['Bodega'][0]['cantidad'], $precioCompra, 'OC')) {
					$productosActualizado[] = $value['VentaDetalleProducto']['id'];
				}else{
					$productosNoActualizado[] = $value['VentaDetalleProducto']['id'];
				}

			}
			
			if (!empty($res['completos'])) {
				$this->Session->setFlash($this->crearAlertaUl($res['completos'], 'Completos'), null, array(), 'success');
			}

			if (!empty($res['incompletos'])) {
				$this->Session->setFlash($this->crearAlertaUl($res['incompletos'], 'Agregados'), null, array(), 'warning');
			}

			if (!empty($res['faltantes'])) {
				$this->Session->setFlash($this->crearAlertaUl($res['faltantes'], 'Faltantes'), null, array(), 'danger');
			}

			$this->OrdenCompra->id = $id;

			# Guardamos la fecha de la primera recepción
			if (empty($this->OrdenCompra->field('fecha_recibido'))) {
				$this->OrdenCompra->saveField('fecha_recibido', date('Y-m-d H:i:s'));
			}

			# Guardamos los dtes en un objeto json
			if (isset($this->request->data['OrdenComprasDocumento'])) {
				$this->OrdenCompra->saveField('meta_dtes', json_encode($this->request->data['OrdenComprasDocumento'], true));
			}

			if (!empty($res['faltantes'])) {
				$this->OrdenCompra->saveField('estado', 'incompleto');	
			}else{
				$this->OrdenCompra->saveField('estado', 'recibido');
			}

			$this->redirect(array('action' => 'index'));

		}

		$this->request->data = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'Moneda',
				'VentaDetalleProducto' => array(
					'Bodega'
				),
				'Administrador',
				'Tienda',
				'Proveedor',
			)
		));
		#prx($this->request->data);
		$bodegas = ClassRegistry::init('Bodega')->find('list', array('conditions' => array('activo' => 1)));
		
		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Recepción OC');

		$this->set(compact('bodegas'));

	}

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
				'Proveedor'
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
					'Proveedor'
				)
			));
		}
		
		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Ver OC ');

		$this->set(compact('ocs'));

	}


	public function admin_ready($id)
	{
		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			
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
			->config('gmail')
			->viewVars(compact('mensaje'))
			->emailFormat('html')
			->from(array($this->Session->read('Auth.Administrador.email') => 'Nodriza Spa') )
			->to($to)
			->cc($cc)
			->bcc($bcc)
			->template('oc_proveedor')
			->attachments($rutaArchivos)
			->subject('[OC] Se ha creado una Orden de compra desde Nodriza Spa');


			# Cambiar estado OC a enviado
			$this->OrdenCompra->id = $id;
			$this->OrdenCompra->saveField('fecha_enviado', date('Y-m-d H:i:s'));
			$this->OrdenCompra->saveField('estado', 'enviado');

			
			if( $this->Email->send() ) {
				$this->Session->setFlash('Email y adjuntos enviados con éxito', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}else{
				$this->Session->setFlash('Ocurrió un error al enviar el email. Intente nuevamente.', null, array(), 'danger');
				$this->redirect(array('action' => 'index'));
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


	public function admin_review($id)
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
				'Proveedor'
			)
		));


		if ($this->request->is('post') || $this->request->is('put')) {
			
			if (isset($this->request->data['OrdenCompra']['estado'])) {

				$this->OrdenCompra->id = $id;
				$this->OrdenCompra->saveField('estado', ''); # Vacio vuelve a bodega
				$this->OrdenCompra->saveField('comentario_validar', $this->request->data['OrdenCompra']['comentario_validar']); # Guarda comentario

				$emails = array($ocs['Administrador']['email']);

				$this->guardarEmailRechazo($id, $emails);

			}else{

				$this->OrdenCompra->id = $id;
				$this->OrdenCompra->saveField('estado', 'validado'); # Pasa a pago
				$this->OrdenCompra->saveField('nombre_validado', $this->Session->read('Auth.Administrador.nombre')); # Guardamos el nombre de quien validó la OC
				$this->OrdenCompra->saveField('comentario_validar', $this->request->data['OrdenCompra']['comentario_validar']); # Guarda comentario


				$admins = ClassRegistry::init('Administrador')->find('all', array(
					'conditions' => array(
						'Administrador.activo' => 1
					),
					'fields' => array(
						'Administrador.email',
						'Administrador.notificaciones'
					)
				));

				$emailsNotificar = array();

				// Obtenemos a los administradores que tiene activa la notificación de oc revision
				foreach ($admins as $ia => $admin) {
					if (!empty($admin['Administrador']['notificaciones'])) {

						$confNotificacion = json_decode($admin['Administrador']['notificaciones'], true);
						
						if ( array_key_exists('pagar_oc', $confNotificacion) && $confNotificacion['pagar_oc'] ) {
							$emailsNotificar[] = $admin['Administrador']['email'];
						}
					}
				}

				if (!empty($emailsNotificar)) {
					$this->guardarEmailValidado($id, $emailsNotificar);
				}

			}


			$this->Session->setFlash('Estado actualizado con éxito.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));

		}
		
		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Revisar OC ');

		$this->set(compact('ocs'));
	}


	public function admin_validate($id)
	{
		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
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

			$admins = ClassRegistry::init('Administrador')->find('all', array(
				'conditions' => array(
					'Administrador.activo' => 1
				),
				'fields' => array(
					'Administrador.email',
					'Administrador.notificaciones'
				)
			));

			$emailsNotificar = array();

			// Obtenemos a los administradores que tiene activa la notificación de oc revision
			foreach ($admins as $ia => $admin) {
				if (!empty($admin['Administrador']['notificaciones'])) {

					$confNotificacion = json_decode($admin['Administrador']['notificaciones'], true);
					
					if ( array_key_exists('revision_oc', $confNotificacion) && $confNotificacion['revision_oc'] ) {
						$emailsNotificar[] = $admin['Administrador']['email'];
					}
				}
			}

			if (!empty($emailsNotificar)) {
				$this->guardarEmailRevision($this->request->data['OrdenesCompra'], $emailsNotificar);
			}

			$this->Session->setFlash('¡Éxito! Se ha enviado a revisión la OC.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->data = $this->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id
			),
			'contain' => array(
				'Moneda',
				'Venta' => array(
					'VentaDetalle' => array(
						'VentaDetalleProducto' => array(
							'Bodega'
						)
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

			if ( isset($productosTotales[$venta['venta_detalle_producto_id']]) && array_key_exists($venta['venta_detalle_producto_id'], $productosTotales) ) {
				$productosTotales[$venta['venta_detalle_producto_id']] = $productosTotales[$venta['venta_detalle_producto_id']] + $venta['cantidad'];
			}else{
				$productosTotales[$venta['venta_detalle_producto_id']] = $venta['cantidad'];
			}

		}

		# comprobamos el stock en bodegas para saber cuales productos se deben solicitar por OC
		foreach ($productosTotales as $ip => $p) {
			
			$producto = Hash::extract($this->request->data['Venta'], 'VentaDetalle[venta_detalle_producto_id='.$ip.'].VentaDetalleProducto');

			$pedir = $p;
			$enBodega = 0;

			if (!empty($producto)) {

				$enBodega = array_sum(Hash::extract($producto['Bodega'], '{n}.BodegasVentaDetalleProducto[io=IN].cantidad')) - array_sum(Hash::extract($producto['Bodega'], '{n}.BodegasVentaDetalleProducto[io=ED].cantidad'));

				if ($enBodega >= $p) {
					$pedir = 0;
				}else{
					$pedir = $pedir - $enBodega;
				}
			}

			if ($pedir === 0) {
				$productosNoSolicitar[$ip]['id'] = $ip;
				$productosNoSolicitar[$ip]['cantidad_bodega'] = $enBodega;
			}else{
				$productosSolicitar[$ip]['id'] = $ip;
				$productosSolicitar[$ip]['cantidad_oc'] = $pedir;
			}

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
					),
					'PrecioEspecificoProducto' => array(
						'conditions' => array(
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
					),
					'Marca' => array(
						'PrecioEspecificoMarca' => array(
							'conditions' => array(
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
					)
				),
				'OrdenCompra' => array(
					'conditions' => array(
						'OrdenCompra.parent_id' => $id
					)
				)
			)
		));
		
		#prx($proveedores);
		# Quitamos los duplicados
		foreach ($proveedores as $ip => $p) {
			if ( (count(Hash::extract($proveedores, '{n}.Proveedor[id=' . $p['Proveedor']['id'] .'].id' )) > 1) || empty($p['VentaDetalleProducto']) ) {
				unset($proveedores[$ip]);
			}
		}

		$tipoDescuento    = array(0 => '$', 1 => '%');

		$descuentosMarcaCompuestos = array();
		$descuentosMarcaEspecificos = array();

		# Calculo de descuentos
		foreach ($proveedores as $ip => $proveedor) {
			foreach ($proveedor['VentaDetalleProducto'] as $i => $p) {

				$descuentosMarcaCompuestos  = Hash::extract($p, 'Marca.PrecioEspecificoMarca.{n}[descuento_compuesto=1]');
				$descuentosMarcaEspecificos = Hash::extract($p, 'Marca.PrecioEspecificoMarca.{n}[descuento_compuesto=0]');
				$descuentosMarca 			= Hash::extract($p, 'Marca.PrecioEspecificoMarca.{n}');

				$descuentosProductoCompuestos   = Hash::extract($p, 'PrecioEspecificoProducto.{n}[descuento_compuesto=1]');
				$descuentosProductosEspecificos = Hash::extract($p, 'PrecioEspecificoProducto.{n}[descuento_compuesto=0]');
				$descuentosProducto             = Hash::extract($p, 'PrecioEspecificoProducto.{n}');

				$descuentosCompuestos = Hash::format(array_merge($descuentosMarcaCompuestos, $descuentosProductoCompuestos), array('{n}.descuento'), '%1d');
				$descCompuesto        = 0;

				$proveedores[$ip]['VentaDetalleProducto'][$i]['total_descuento']  = 0;
				$proveedores[$ip]['VentaDetalleProducto'][$i]['nombre_descuento'] = '';
				$proveedores[$ip]['VentaDetalleProducto'][$i]['valor_descuento']  = 0;

				# Descuento marca
				if ( !empty($descuentosMarca) ) {

					if ($descuentosMarca[0]['descuento_compuesto']) {

						$descCompuesto = calcularDescuentoCompuesto($descuentosCompuestos, $p['Marca']['descuento_base']);
						$proveedores[$ip]['VentaDetalleProducto'][$i]['total_descuento']  = $p['precio_costo'] * $descCompuesto;
						$proveedores[$ip]['VentaDetalleProducto'][$i]['nombre_descuento'] = 'Compuestos (%): ' . ($descCompuesto*100);
						$proveedores[$ip]['VentaDetalleProducto'][$i]['valor_descuento'] = $descCompuesto;

					}else{

						if ($p['Marca']['PrecioEspecificoMarca'][0]['tipo_descuento']) {
							$proveedores[$ip]['VentaDetalleProducto'][$i]['total_descuento'] = $p['precio_costo'] * ($p['Marca']['PrecioEspecificoMarca'][0]['descuento'] / 100); // Primer descuento
							$proveedores[$ip]['VentaDetalleProducto'][$i]['nombre_descuento'] = 'Descuento ' . $p['Marca']['PrecioEspecificoMarca'][0]['nombre'] . ': % ' . $p['Marca']['PrecioEspecificoMarca'][0]['descuento'];	
							$proveedores[$ip]['VentaDetalleProducto'][$i]['valor_descuento'] = $p['Marca']['PrecioEspecificoMarca'][0]['descuento'];

						}else{
							$proveedores[$ip]['VentaDetalleProducto'][$i]['total_descuento'] = $p['Marca']['PrecioEspecificoMarca'][0]['descuento']; // Primer descuento
							$proveedores[$ip]['VentaDetalleProducto'][$i]['nombre_descuento'] = 'Descuento ' . $p['Marca']['PrecioEspecificoMarca'][0]['nombre'] . ': $ ' . CakeNumber::currency($p['Marca']['PrecioEspecificoMarca'][0]['descuento'] , 'CLP');
							$proveedores[$ip]['VentaDetalleProducto'][$i]['valor_descuento'] = $p['Marca']['PrecioEspecificoMarca'][0]['descuento'];
						}

					}

				}

				# Descuento producto
				if ( !empty($descuentosProducto) ) {

					if ($descuentosProducto[0]['descuento_compuesto']) {

						if ($descCompuesto > 0) {

							//$descCompuesto = calcularDescuentoCompuesto($descuentosCompuestos, $descCompuesto);
							$proveedores[$ip]['VentaDetalleProducto'][$i]['total_descuento']  = $p['precio_costo'] * $descCompuesto;	
						}else{

							$descCompuesto = calcularDescuentoCompuesto($descuentosCompuestos, $p['Marca']['descuento_base']);
							
							$proveedores[$ip]['VentaDetalleProducto'][$i]['total_descuento']  = $p['precio_costo'] * $descCompuesto;
						}

						$proveedores[$ip]['VentaDetalleProducto'][$i]['nombre_descuento'] = 'Compuestos (%): ' . ($descCompuesto*100);
						$proveedores[$ip]['VentaDetalleProducto'][$i]['valor_descuento'] = $descCompuesto;

					}else{

						if ($p['PrecioEspecificoProducto'][0]['tipo_descuento']) {
							$proveedores[$ip]['VentaDetalleProducto'][$i]['total_descuento'] = $p['precio_costo'] * ($p['PrecioEspecificoProducto'][0]['descuento'] / 100); // Primer descuento
							$proveedores[$ip]['VentaDetalleProducto'][$i]['nombre_descuento'] = 'Descuento ' . $p['PrecioEspecificoProducto'][0]['nombre'] . ': % ' . $p['PrecioEspecificoProducto'][0]['descuento'];
							$proveedores[$ip]['VentaDetalleProducto'][$i]['valor_descuento'] = $p['PrecioEspecificoProducto'][0]['descuento'];
						}else{
							$proveedores[$ip]['VentaDetalleProducto'][$i]['total_descuento'] = $p['PrecioEspecificoProducto'][0]['descuento']; // Primer descuento
							$proveedores[$ip]['VentaDetalleProducto'][$i]['nombre_descuento'] = 'Descuento ' . $p['PrecioEspecificoProducto'][0]['nombre'] . ': $ ' . CakeNumber::currency($p['PrecioEspecificoProducto'][0]['descuento'] , 'CLP');
							$proveedores[$ip]['VentaDetalleProducto'][$i]['valor_descuento'] = $p['PrecioEspecificoProducto'][0]['descuento'];
						}

					}

				}


				if (empty($descuentosMarca) && empty($descuentosProducto) && isset($p['Marca']['descuento_base'])) {
					$proveedores[$ip]['VentaDetalleProducto'][$i]['total_descuento'] = $p['precio_costo'] * ($p['Marca']['descuento_base'] / 100); // Primer descuento
					$proveedores[$ip]['VentaDetalleProducto'][$i]['nombre_descuento'] = 'Descuento base marca' . ': % ' . $p['Marca']['descuento_base'];
					$proveedores[$ip]['VentaDetalleProducto'][$i]['valor_descuento'] = $p['Marca']['descuento_base'];
				}


			}
		}

		$proveedoresLista = ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('Proveedor.activo' => 1)));
		$marcas 		  = ClassRegistry::init('Marca')->find('list');
		$monedas          = $this->OrdenCompra->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));


		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Revisión ');		

		$this->set(compact('monedas', 'productosNoSolicitar', 'productosSolicitar', 'productosIncompletos', 'productos', 'proveedores', 'proveedoresLista', 'tipoDescuento', 'marcas'));
		
	}


	public function admin_pay($id = null)
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

		if ($this->request->is('post') || $this->request->is('put')) {
			
			$data = array(
				'OrdenCompra' => array(
					'id'                 => $id,
					'estado'             => 'pagado',
					'moneda_id'          => $this->request->data['OrdenCompra']['moneda_id'],
					'nombre_pagado'      => $this->Session->read('Auth.Administrador.nombre'),
					'comentario_finanza' => $this->request->data['OrdenCompra']['comentario_finanza'],
					'total'              => $this->request->data['OrdenCompra']['total'],
					'descuento_monto'    => round($this->request->data['OrdenCompra']['descuento_monto']),
					'descuento'    	     => round($this->request->data['OrdenCompra']['descuento']),
					'adjunto'            => $this->request->data['OrdenCompra']['adjunto']
				)
			);

			if ($this->OrdenCompra->save($data)) {


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

				$nombreOC = 'cotizacion_' . $ocs['OrdenCompra']['id'] . '_' . Inflector::slug($ocs['Proveedor']['nombre']) . '_' . rand(1,100) . '.pdf';

				if (empty($ocs['OrdenCompra']['pdf'])) {

					$this->generar_pdf($ocs, $nombreOC);

					$this->OrdenCompra->id = $id;
					$this->OrdenCompra->saveField('pdf', $nombreOC);
				}

				$emailsNotificar = array();

				$emailsNotificar[] = $ocs['Administrador']['email'];

				# enviar email
				$this->guardarEmailPagado($id, $emailsNotificar);


				$this->Session->setFlash('Estado actualizado con éxito.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));	
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

				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
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


	public function admin_add_manual()
	{
		if ( $this->request->is('post') )
		{	
			$this->OrdenCompra->create();
			if ( $this->OrdenCompra->save($this->request->data) )
			{	
				$admins = ClassRegistry::init('Administrador')->find('all', array(
					'conditions' => array(
						'Administrador.activo' => 1
					),
					'fields' => array(
						'Administrador.email',
						'Administrador.notificaciones'
					)
				));

				$emailsNotificar = array();

				// Obtenemos a los administradores que tiene activa la notificación de oc revision
				foreach ($admins as $ia => $admin) {
					if (!empty($admin['Administrador']['notificaciones'])) {

						$confNotificacion = json_decode($admin['Administrador']['notificaciones'], true);
						
						if ( array_key_exists('revision_oc', $confNotificacion) && $confNotificacion['revision_oc'] ) {
							$emailsNotificar[] = $admin['Administrador']['email'];
						}
					}
				}

				if (!empty($emailsNotificar)) {
					$this->guardarEmailRevision($this->request->data['OrdenCompra'], $emailsNotificar);
				}

				$this->Session->setFlash('¡Éxito! Se ha enviado a revisión la OC.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$monedas = $this->OrdenCompra->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));
		$proveedores = $this->OrdenCompra->Proveedor->find('list', array('conditions' => array('Proveedor.activo' => 1)));

		$tipoDescuento    = array(0 => '$', 1 => '%');

		BreadcrumbComponent::add('Ordenes de compra ', '/ordenCompras');
		BreadcrumbComponent::add('Agregar oc manual');

		$this->set(compact('monedas', 'proveedores', 'tipoDescuento'));
	}


	public function admin_editsingle($id = null)
	{
		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			# Limpiar data
			$this->OrdenCompra->OrdenComprasVentaDetalleProducto->deleteAll(array('OrdenComprasVentaDetalleProducto.orden_compra_id' => $id));

			if ( $this->OrdenCompra->saveAll($this->request->data) )
			{	
				$admins = ClassRegistry::init('Administrador')->find('all', array(
					'conditions' => array(
						'Administrador.activo' => 1
					),
					'fields' => array(
						'Administrador.email',
						'Administrador.notificaciones'
					)
				));

				$emailsNotificar = array();

				// Obtenemos a los administradores que tiene activa la notificación de oc revision
				foreach ($admins as $ia => $admin) {
					if (!empty($admin['Administrador']['notificaciones'])) {

						$confNotificacion = json_decode($admin['Administrador']['notificaciones'], true);
						
						if ( array_key_exists('revision_oc', $confNotificacion) && $confNotificacion['revision_oc'] ) {
							$emailsNotificar[] = $admin['Administrador']['email'];
						}
					}
				}

				if (!empty($emailsNotificar)) {
					$this->guardarEmailRevision($this->request->data['OrdenCompra'], $emailsNotificar);
				}

				$this->Session->setFlash('¡Éxito! Se ha enviado a revisión la OC.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
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
					'VentaDetalleProducto',
					'Administrador',
					'Tienda',
					'Proveedor'
				)
			));
		}

		$tipoDescuento    = array(0 => '$', 1 => '%');
		$monedas = $this->OrdenCompra->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));

		BreadcrumbComponent::add('Ordenes de compa ', '/ordenCompras');
		BreadcrumbComponent::add('Editar ');

		$this->set(compact('tipoDescuento', 'monedas'));
	}


	public function admin_edit($id = null)
	{	
		if ( ! $this->OrdenCompra->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
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


	public function admin_delete($id = null)
	{
		$this->OrdenCompra->id = $id;
		if ( ! $this->OrdenCompra->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->OrdenCompra->delete() )
		{	

			$this->OrdenCompra->deleteAll(array('OrdenCompra.parent_id' => $id));

			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->OrdenCompra->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->OrdenCompra->_schema);
		$modelo			= $this->OrdenCompra->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}

	public function admin_obtener_ordenes_ajax()
	{	

		$this->layout = 'ajax';


		$fecha_actual = date("Y-m-d H:i:s");
		$hace_un_mes  = date("Y-m-d H:i:s",strtotime($fecha_actual."- 1 months")); 

		$ventas          = $this->OrdenCompra->Venta->find('all', array(
			'conditions' => array(
				'Venta.fecha_venta BETWEEN ? AND ?' => array($hace_un_mes, $fecha_actual)
			),
			'fields' => array(
				'Venta.id', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total'
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
			'order' => array('Venta.fecha_venta' => 'DESC')
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


	public function admin_calcularMontoPagar()
	{	
		$res = array(
			'descuento_porcentaje' => 0,
			'descuento_monto'      => 0,
			'descuento_monto_html' => CakeNumber::currency(0 , 'CLP'),
			'monto_pagar' 		   => 0,
			'monto_pagar_html'     => CakeNumber::currency(0, 'CLP')
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


	private function guardarEmailRevision($ocs = array(), $emails = array()) 
    {

		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompras' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';
		$this->Correo				= ClassRegistry::init('Correo');
		
		$url = Router::fullBaseUrl();

		$this->View->set(compact('ocs', 'url'));
		$html						= $this->View->render('notificar_revision_oc');

		/**
		 * Guarda el email a enviar
		 */
		$this->Correo->create();
		
		if ( $this->Correo->save(array(
			'estado'					=> 'Notificación revisar ocs',
			'html'						=> $html,
			'asunto'					=> '[NDRZ] OC para revisión',
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


	private function guardarEmailRechazo($id, $emails = array())
	{	
		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompras' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';
		$this->Correo				= ClassRegistry::init('Correo');
		
		$url = Router::fullBaseUrl();

		$this->View->set(compact('id', 'url'));
		$html						= $this->View->render('notificar_rechazo_oc');

		/**
		 * Guarda el email a enviar
		 */
		$this->Correo->create();
		
		if ( $this->Correo->save(array(
			'estado'					=> 'Notificación rechazo oc',
			'html'						=> $html,
			'asunto'					=> '[NDRZ] OC rechazada',
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

	private function guardarEmailValidado($id, $emails = array())
	{	
		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompras' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';
		$this->Correo				= ClassRegistry::init('Correo');
		
		$url = Router::fullBaseUrl();

		$this->View->set(compact('id', 'url'));
		$html						= $this->View->render('notificar_validado_oc');

		/**
		 * Guarda el email a enviar
		 */
		$this->Correo->create();
		
		if ( $this->Correo->save(array(
			'estado'					=> 'Notificación validado oc',
			'html'						=> $html,
			'asunto'					=> '[NDRZ] OC lista para pagar',
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


	private function guardarEmailPagado($id, $emails = array())
	{	
		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompras' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';
		$this->Correo				= ClassRegistry::init('Correo');
		
		$url = Router::fullBaseUrl();

		$this->View->set(compact('id', 'url'));
		$html						= $this->View->render('notificar_pagado_oc');

		/**
		 * Guarda el email a enviar
		 */
		$this->Correo->create();
		
		if ( $this->Correo->save(array(
			'estado'					=> 'Notificación pagado oc',
			'html'						=> $html,
			'asunto'					=> '[NDRZ] OC lista para enviar',
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


	public function generar_pdf($oc = array(), $nombreOC = '') {

		App::uses('CakePdf', 'Plugin/CakePdf/Pdf');

		try {
			$this->CakePdf = new CakePdf();
			$this->CakePdf->template('generar_oc', 'default');
			$this->CakePdf->viewVars(compact('oc'));
			$this->CakePdf->write(APP . 'webroot' . DS . 'Pdf' . DS . 'OrdenCompra' . DS . $oc['OrdenCompra']['id'] . DS . $nombreOC);	
		} catch (Exception $e) {
			
		}

		# Ruta para guardar en la Base de datos
		$archivo = Router::url('/', true) . 'Pdf/OrdenCompra/' . $oc['OrdenCompra']['id'] . '/' . $nombreOC;

		return;

	}

}