<?php
App::uses('AppController', 'Controller');
class ProspectosController extends AppController
{

	public function admin_index()
	{	

		$paginate = array(); 
    	$conditions = array();
    	$total = 0;
    	$totalMostrados = 0;
    	$categorias = array();

    	$textoBuscar = null;

		if ( $this->request->is('post') ) {

			if ( ! empty($this->request->data['Filtro']['findby']) && empty($this->request->data['Filtro']['f_inicio']) && empty($this->request->data['Filtro']['f_final']) ) {
				$this->redirect(array('controller' => 'prospectos', 'action' => 'index', 'findby' => $this->request->data['Filtro']['findby']));
			}

            if ( empty($this->request->data['Filtro']['findby']) && ! empty($this->request->data['Filtro']['f_inicio']) && ! empty($this->request->data['Filtro']['f_final']) ) {
                $this->redirect(array('controller' => 'prospectos', 'action' => 'index', 'f_inicio' => $this->request->data['Filtro']['f_inicio'], 'f_final' => $this->request->data['Filtro']['f_final']));
            }

            if ( ! empty($this->request->data['Filtro']['findby']) && ! empty($this->request->data['Filtro']['f_inicio']) && ! empty($this->request->data['Filtro']['f_final']) ) {
                $this->redirect(array('controller' => 'prospectos', 'action' => 'index', 'findby' => $this->request->data['Filtro']['findby'], 'f_inicio' => $this->request->data['Filtro']['f_inicio'], 'f_final' => $this->request->data['Filtro']['f_final']));
            }

		}

		// Opciones de paginación
		$paginate = array_replace_recursive(array(
			'limit' => 10,
			'fields' => array(),
			'joins' => array(),
			'contain' => array('Tienda', 'EstadoProspecto', 'Moneda'),
			'conditions' => array(
					'Prospecto.tienda_id' => $this->Session->read('Tienda.id')
				),
			'recursive'	=> 0,
			'order' => 'Prospecto.id ASC'
		));

		/**
		* Buscar por
		*/
		if ( !empty($this->request->params['named']['findby']) && empty($this->request->params['named']['f_inicio']) && empty($this->request->params['named']['f_final']) ) {

			
			$paginate		= array_replace_recursive($paginate, array(
				'conditions'	=> array(
					'Prospecto.estado_prospecto_id' => trim($this->request->params['named']['findby']),
					'Prospecto.tienda_id' => $this->Session->read('Tienda.id')
				)
			));
			
		}

		if ( empty($this->request->params['named']['findby']) && ! empty($this->request->params['named']['f_inicio']) && ! empty($this->request->params['named']['f_final']) ) {

			$f_inicio = date('Y-m-d 00:00:00', strtotime($this->request->params['named']['f_inicio']));
			$f_final  = date('Y-m-d 23:59:59', strtotime($this->request->params['named']['f_final']));

			$paginate		= array_replace_recursive($paginate, array(
				'conditions'	=> array(
					'Prospecto.created BETWEEN ? AND ?' => array($f_inicio, $f_final),
					'Prospecto.tienda_id' => $this->Session->read('Tienda.id')
				)
			));
			
		}

		if ( !empty($this->request->params['named']['findby']) && !empty($this->request->params['named']['f_inicio']) && !empty($this->request->params['named']['f_final']) ) {

			$f_inicio = date('Y-m-d 00:00:00', strtotime($this->request->params['named']['f_inicio']));
			$f_final  = date('Y-m-d 23:59:59', strtotime($this->request->params['named']['f_final']));

			$paginate		= array_replace_recursive($paginate, array(
				'conditions'	=> array(
					'Prospecto.estado_prospecto_id' => trim($this->request->params['named']['findby']),
					'Prospecto.created BETWEEN ? AND ?' => array($f_inicio, $f_final),
					'Prospecto.tienda_id' => $this->Session->read('Tienda.id')
				)
			));
			
		}

		// Total de registros
		$total 		= $this->Prospecto->find('count', array(
			'joins' => array(),
			'conditions' => array()
		));


		$this->paginate = $paginate;


		$prospectos	= $this->paginate();


		$estadoProspectos = $this->Prospecto->EstadoProspecto->find('list');

		BreadcrumbComponent::add('Prospectos ');
		$this->set(compact('prospectos', 'estadoProspectos'));
	}

	public function admin_add()
	{	
		if ( $this->request->is('post') )
		{	
			// Forzamos el id de tienda
			$this->request->data['Prospecto']['tienda_id'] = $this->Session->read('Tienda.id');

			// Configuración de tablas externas
			$this->cambiarConfigDB($this->tiendaConf($this->request->data['Prospecto']['tienda_id']));

			// Se normalizan las direcciones
			if ( ! empty($this->request->data['Cliente'])) {
				$this->request->data['Cliente'] = $this->limpiarDirecciones($this->request->data['Cliente']);
			}
			
			if ( ! empty($this->request->data['Cliente'])) {
	
				// Verificamos si el cliente es nuevo o existente
				if ( ! $this->request->data['Prospecto']['existente'] && ! empty($this->request->data['Cliente'][1]['email']) ) {

					# Se crea un password para el cliente default y la fecha de creación y actualización
					$this->request->data['Cliente'][1]['id_lang'] 			= 1; 							# Idioma español por defeco
					$this->request->data['Cliente'][1]['id_risk'] 			= 0;							# Valor default de prestashop
					$this->request->data['Cliente'][1]['id_default_group'] 	= 3; 							# Grupo de clientes por defecto
					$this->request->data['Cliente'][1]['passwd'] 			= 'cliente123456'; 				# Contraseña defecto
					$this->request->data['Cliente'][1]['date_add'] 			= date('Y-m-d H:i:s');			# Fecha creación
					$this->request->data['Cliente'][1]['date_upd'] 			= date('Y-m-d H:i:s'); 			# fecha de actualización
					$this->request->data['Cliente'][1]['active'] 			= 1;							# Dejar activo al cliente
					$this->request->data['Cliente'][1]['secure_key'] 		= md5(uniqid(rand(), true)); 	# Llave de seguridad unica por cliente
					$this->request->data['Cliente'][1]['newsletter'] 		= 1; 							# Inscribir al newsletter
					$this->request->data['Cliente'][1]['newsletter_date_add'] 		= date('Y-m-d H:i:s'); 	# Fecha inscrición al newsletter
					
					# Cliente nuevo, se crea.
					$this->Cliente = ClassRegistry::init('Cliente');
					
					# Verificamos su existencia en la base de datos de la tienda
					$verificarExistencia = $this->Cliente->find('all', array('conditions' => array('Cliente.email' => $this->request->data['Cliente'][1]['email'])));

					if ( empty($verificarExistencia) ) {
						if( $this->Cliente->saveAll($this->request->data['Cliente'][1]) ) {
							// Agregamos el id del cliente y su dirección
							$clienteNuevo = $this->Cliente->find('first', array(
								'fields' => array('Cliente.id_customer'),
								'order' => array('Cliente.id_customer' => 'DESC'),
								'contain' => array('Clientedireccion')
								));

							// Seteamos los id de cliente y direccion del prospecto
							$this->request->data['Prospecto']['id_customer'] = $clienteNuevo['Cliente']['id_customer'];
							$this->request->data['Prospecto']['id_address'] = $clienteNuevo['Clientedireccion'][0]['id_address'];

						}else{
							$this->Session->setFlash('No se pudo guardar el nuevo cliente.', null, array(), 'danger');
						}

						// Eliminamos a cliente del arreglo para que no se vuelva a actualizar
						unset($this->request->data['Cliente']);	

						// Seteamos cliente exstente
						$this->request->data['Prospecto']['existente'] = 1;
					}

				}

			}
			
			$this->Prospecto->create();
			if ( $this->Prospecto->save($this->request->data) )
			{	
				# Una vez guardado el prospecto se actualiza la información si es que cambio del cliente y la dirección
				# Cliente existente
				if ( $this->request->data['Prospecto']['existente'] && isset($this->request->data['Cliente']) ) {
					$this->Cliente = ClassRegistry::init('Cliente');
					
					if( $this->Cliente->saveAll($this->request->data['Cliente'][1]) ) {
						# Se pasa a estado esperando información
						$this->Prospecto->saveField('estado_prospecto_id', 3);
						$this->Session->setFlash('Información del cliente actualizada con éxito.', null, array(), 'success');

					}else{

						# Se pasa a estado esperando información
						$this->Prospecto->saveField('estado_prospecto_id', 3);
						$this->Session->setFlash('Error al actualizar la información del cliente.', null, array(), 'error');
					}
				}

				if( $this->request->data['Prospecto']['cotizacion'] ) {

					# Obtenemos el prospecto recién creado
					$prospecto = $this->Prospecto->find('first', array('order' => 'Prospecto.id DESC'));

					# Obtenemos los ID´S de productos relacionados al prospecto
					$prospectoProductos = $this->Prospecto->ProductotiendaProspecto->find('all', array(
						'conditions' => array('prospecto_id' => $prospecto['Prospecto']['id'])
					));

					# Verificamos que exista la información mínima para pasar a cotización
					if ( empty($prospecto['Prospecto']['id_customer']) || empty($prospecto['Prospecto']['id_address']) || empty($prospecto['Prospecto']['nombre']) || empty($prospecto['Prospecto']['descripcion']) || empty($prospectoProductos)) {

						# Se pasa a estado esperando información
						$this->Prospecto->saveField('estado_prospecto_id', 3);

						$this->Session->setFlash('El prospecto fue creado exitósamente, pero no puede pasar a cotización. Necesita agregar al cliente, seleccionar dirección y añadir productos.', null, array(), 'success');
						$this->redirect(array('action' => 'edit', $prospecto['Prospecto']['id']));
					}else{
						# Se pasa a estado finalizado
						$this->Prospecto->saveField('estado_prospecto_id', 7);
						$this->Session->setFlash('El prospecto fue creado exitósamente, puede crear la cotización.', null, array(), 'success');
						$this->redirect(array('controller' => 'cotizaciones', 'action' => 'add', $prospecto['Prospecto']['id']));
					}

				}

				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
			
		}


		$estadoProspectos	= $this->Prospecto->EstadoProspecto->find('list', array('conditions' => array('EstadoProspecto.activo' => 1)));
		$monedas	= $this->Prospecto->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));
		$origenes	= $this->Prospecto->Origen->find('list', array('conditions' => array('Origen.activo' => 1)));
		$tiendas	= $this->Prospecto->Tienda->find('list', array('conditions' => array('Tienda.activo' => 1)));

		BreadcrumbComponent::add('Prospectos ', '/prospectos');
		BreadcrumbComponent::add('Agregar ');
		$this->set(compact('estadoProspectos', 'monedas', 'origenes', 'tiendas'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Prospecto->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			// Forzamos el id de tienda
			$this->request->data['Prospecto']['tienda_id'] = $this->Session->read('Tienda.id');

			// Configuración de tablas externas
			$this->cambiarConfigDB($this->tiendaConf($this->request->data['Prospecto']['tienda_id']));

			// Se normalizan las direcciones
			if ( ! empty($this->request->data['Cliente'])) {
				$this->request->data['Cliente'] = $this->limpiarDirecciones($this->request->data['Cliente']);
			}

			if ( ! empty($this->request->data['Cliente'])) {
				
				// Verificamos si el cliente es nuevo o existente
				if ( ! $this->request->data['Prospecto']['existente'] && ! empty($this->request->data['Cliente'][1]['email']) ) {

					# Se crea un password para el cliente default y la fecha de creación y actualización
					$this->request->data['Cliente'][1]['id_lang'] 			= 1; 							# Idioma español por defeco
					$this->request->data['Cliente'][1]['id_risk'] 			= 0;							# Valor default de prestashop
					$this->request->data['Cliente'][1]['id_default_group'] 	= 3; 							# Grupo de clientes por defecto
					$this->request->data['Cliente'][1]['passwd'] 			= 'cliente123456'; 				# Contraseña defecto
					$this->request->data['Cliente'][1]['date_add'] 			= date('Y-m-d H:i:s');			# Fecha creación
					$this->request->data['Cliente'][1]['date_upd'] 			= date('Y-m-d H:i:s'); 			# fecha de actualización
					$this->request->data['Cliente'][1]['active'] 			= 1;							# Dejar activo al cliente
					$this->request->data['Cliente'][1]['secure_key'] 		= md5(uniqid(rand(), true)); 	# Llave de seguridad unica por cliente
					$this->request->data['Cliente'][1]['newsletter'] 		= 1; 							# Inscribir al newsletter
					$this->request->data['Cliente'][1]['newsletter_date_add'] 		= date('Y-m-d H:i:s'); 	# Fecha inscrición al newsletter

					# Cliente nuevo, se crea.
					$this->Cliente = ClassRegistry::init('Cliente');
					
					# Verificamos su existencia en la base de datos de la tienda
					$verificarExistencia = $this->Cliente->find('all', array('conditions' => array('Cliente.email' => $this->request->data['Cliente'][1]['email'])));

					if ( empty($verificarExistencia) ) {
						if( $this->Cliente->saveAll($this->request->data['Cliente'][1]) ) {
							// Agregamos el id del cliente y su dirección
							$clienteNuevo = $this->Cliente->find('first', array(
								'fields' => array('Cliente.id_customer'),
								'conditions' => array('Cliente.email' => $this->request->data['Cliente'][1]['email']),
								'contain' => array('Clientedireccion')
								));

							// Seteamos los id de cliente y direccion del prospecto
							$this->request->data['Prospecto']['id_customer'] = $clienteNuevo['Cliente']['id_customer'];
							$this->request->data['Prospecto']['id_address'] = $clienteNuevo['Clientedireccion'][0]['id_address'];

						}else{
							$this->Session->setFlash('No se pudo guardar el nuevo cliente.', null, array(), 'danger');
						}

						// Eliminamos a cliente del arreglo para que no se vuelva a actualizar
						unset($this->request->data['Cliente']);	

						// Seteamos cliente exstente
						$this->request->data['Prospecto']['existente'] = 1;
					}

				}

			}
			
			# Se eliminan los productos asociados y se agrgan nuevamente
			if ( ! empty($this->request->data['Productotienda'])) {
				$this->Prospecto->ProductotiendaProspecto->deleteAll(array('ProductotiendaProspecto.prospecto_id' => $id));
			}
			
			if ( $this->Prospecto->save($this->request->data) )
			{	

				# Una vez guardado el prospecto se actualiza la información si es que cambio del cliente y la dirección
				# Cliente existente
				if ( $this->request->data['Prospecto']['existente'] && isset($this->request->data['Cliente']) ) {
					$this->Cliente = ClassRegistry::init('Cliente');
					
					if( $this->Cliente->saveAll($this->request->data['Cliente'][1]) ) {
						
						# Se pasa a estado esperando información
						$this->Prospecto->saveField('estado_prospecto_id', 3);
						$this->Session->setFlash('Información del cliente actualizada con éxito.', null, array(), 'success');

					}else{

						# Se pasa a estado esperando información
						$this->Prospecto->saveField('estado_prospecto_id', 3);
						$this->Session->setFlash('Error al actualizar la información del cliente.', null, array(), 'error');
						
					}
				}

				if( $this->request->data['Prospecto']['cotizacion'] ) {

					# Obtenemos el prospecto recién creado
					$prospecto = $this->Prospecto->find('first', array('conditions' => array('Prospecto.id' => $id)));

					# Obtenemos los ID´S de productos relacionados al prospecto
					$prospectoProductos = $this->Prospecto->ProductotiendaProspecto->find('all', array(
						'conditions' => array('prospecto_id' => $prospecto['Prospecto']['id'])
					));

					# Verificamos que exista la información mínima para pasar a cotización
					if ( empty($prospecto['Prospecto']['id_customer']) || empty($prospecto['Prospecto']['id_address']) || empty($prospecto['Prospecto']['nombre']) || empty($prospecto['Prospecto']['descripcion']) || empty($prospectoProductos)) {
						
						# Se pasa a estado en espera
						$this->Prospecto->saveField('estado_prospecto_id', 3);
						$this->Session->setFlash('El prospecto fue creado exitósamente, pero no puede pasar a cotización. Necesita agregar al cliente, seleccionar dirección y añadir productos.', null, array(), 'success');
						$this->redirect(array('action' => 'edit', $prospecto['Prospecto']['id']));
					}else{
						# Se pasa a estado finalizado
						$this->Prospecto->saveField('estado_prospecto_id', 7);
						$this->Session->setFlash('El prospecto fue creado exitósamente, puede crear la cotización.', null, array(), 'success');
						$this->redirect(array('controller' => 'cotizaciones', 'action' => 'add', $prospecto['Prospecto']['id']));
					}

				}

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
			$this->request->data	= $this->Prospecto->find('first', array(
				'conditions'	=> array('Prospecto.id' => $id)
			));

			$this->request->data['Cliente'] = ClassRegistry::init('Cliente')->find('first', array(
				'contain' => array(
	    			'Clientedireccion' => array(
	    				'Paise' => array('Lang'), 'Region')
	    		),
				'conditions' => array('Cliente.id_customer' => $this->request->data['Prospecto']['id_customer'])));
			
			# Obtenemos los ID´S de productos relacionados al prospecto
			$prospectoProductos = $this->Prospecto->ProductotiendaProspecto->find('all', array(
				'conditions' => array('prospecto_id' => $this->request->data['Prospecto']['id'])
			));

			$productos = array();

			# Obtenemos los productos por el grupo de ID´S
			if (!empty($prospectoProductos)) {
				$this->request->data['Productotienda'] = ClassRegistry::init('Productotienda')->find('all', array(
					'conditions' => array('Productotienda.id_product' => Hash::extract($prospectoProductos, '{n}.ProductotiendaProspecto.id_product')),
					'contain' => array(
		   				'Lang',
		   				'TaxRulesGroup' => array(
							'TaxRule' => array(
								'Tax'
							)
						),
						'SpecificPrice' => array(
							'conditions' => array(
								'OR' => array(
									'OR' => array(
										array('SpecificPrice.from' => '000-00-00 00:00:00'),
										array('SpecificPrice.to' => '000-00-00 00:00:00')
									),
									'AND' => array(
										'SpecificPrice.from <= "' . date('Y-m-d H:i:s') . '"',
										'SpecificPrice.to >= "' . date('Y-m-d H:i:s') . '"'
									)
								)
							)
						)
					),
					'fields' => array('Productotienda.id_product', 'Productotienda.reference', 'Productotienda.price')
				));

				# Se agrega los valores de descuentos y cantidad a los productos relacinados
				foreach ($prospectoProductos as $ix => $prospectoProducto) {
					foreach ($this->request->data['Productotienda'] as $ik => $producto) {
						if ($prospectoProductos[$ix]['ProductotiendaProspecto']['id_product'] == $this->request->data['Productotienda'][$ik]['Productotienda']['id_product']) {

							$precio_normal 	= $this->precio($producto['Productotienda']['price'], $producto['TaxRulesGroup']['TaxRule'][0]['Tax']['rate']);
							$precio_neto 	= $producto['Productotienda']['price'];

							# Aplicamos precio específico si es que existe
							if ( ! empty($producto['SpecificPrice']) ) {
								if ($producto['SpecificPrice'][0]['reduction'] > 0) {

									$precio_normal	= $this->precio($precio_normal, ($producto['SpecificPrice'][0]['reduction'] * 100 * -1) );
							
								}
							}
						
							$this->request->data['Productotienda'][$ik]['Productotienda']['precio']				= $precio_normal;
							$this->request->data['Productotienda'][$ik]['Productotienda']['cantidad'] 			= $prospectoProductos[$ix]['ProductotiendaProspecto']['cantidad'];
							$this->request->data['Productotienda'][$ik]['Productotienda']['nombre_descuento'] 	= $prospectoProductos[$ix]['ProductotiendaProspecto']['nombre_descuento'];
							$this->request->data['Productotienda'][$ik]['Productotienda']['descuento'] 			= $prospectoProductos[$ix]['ProductotiendaProspecto']['descuento'];
						}
					}
				}
			}
		}

		// Paises
		$paises = ClassRegistry::init('Paise')->find('all', array('contain' => array('Lang')));
		
		$arrayPaises = array();

		foreach ($paises as $pais) {
			$arrayPaises[$pais['Paise']['id_country']] = $pais['Lang'][0]['PaisIdioma']['name'];
		}

		// Regiones de chile por default
		$regiones = ClassRegistry::init('Region')->find('list', array('conditions' => array('Region.id_country' => 68)));

		$estadoProspectos	= $this->Prospecto->EstadoProspecto->find('list', array('conditions' => array('EstadoProspecto.activo' => 1)));
		$monedas	= $this->Prospecto->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));
		$origenes	= $this->Prospecto->Origen->find('list', array('conditions' => array('Origen.activo' => 1)));
		$tiendas	= $this->Prospecto->Tienda->find('list', array('conditions' => array('Tienda.activo' => 1)));
		BreadcrumbComponent::add('Prospectos ', '/prospectos');
		BreadcrumbComponent::add('Editar ');
		$this->set(compact('estadoProspectos', 'monedas', 'origenes', 'tiendas', 'arrayPaises', 'regiones'));
	}

	public function admin_delete($id = null)
	{
		$this->Prospecto->id = $id;
		if ( ! $this->Prospecto->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Prospecto->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Prospecto->find('all', array(
			'recursive'		=> -1
		));

		$campos			= array_keys($this->Prospecto->_schema);
		$modelo			= $this->Prospecto->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	/**
	 * 	Función que retorna los pedidos realizados en una tienda en específico,
	 *  sus estados, monto, fecha, etc. dado un cliente
	 * @param  int 		$tienda  		Identificador de la tienda
	 * @param  int 	$cliente 			Idenfitifcador del cliente
	 * @return string          Retorna un html con la información.
	 */
	public function admin_historial_pedidos($tienda, $cliente) {
		if ( empty($tienda) || empty($cliente) ) {
			throw new Exception('No se permiten campos vacios');
		}

		// Configuración de tablas externas
		$this->cambiarConfigDB($this->tiendaConf($tienda));

		$pedidos = ClassRegistry::init('Orders')->find('all', array(
			'contain' => array(
				'OrdenEstado' => array('Lang')
			),
			'conditions' => array(
				'Orders.id_customer' => $cliente
			),
			'order' => array(
				'Orders.date_add' => 'DESC'
			)
		));
		
		$htmlPedidos = '';
		$totalComprado = 0;

		if ( ! empty($pedidos)) {
			$htmlPedidos  .= '<div class="table-responsive">';
			$htmlPedidos  .= '<table class="table table-striped table-bordered">';
			$htmlPedidos  .= '<th>Referencia</th>';
			$htmlPedidos  .= '<th>Estado</th>';
			$htmlPedidos  .= '<th>Método de pago</th>';
			$htmlPedidos  .= '<th>Monto</th>';
			$htmlPedidos  .= '<th>Fecha</th>';
			foreach ($pedidos as $pedido) {
				$htmlPedidos  .= '<tr>';
				$htmlPedidos  .= sprintf('<td>%s</td>', $pedido['Orders']['reference']);
				$htmlPedidos  .= sprintf('<td><label class="label label-form" style="background-color: %s;">%s</td>', $pedido['OrdenEstado']['color'], $pedido['OrdenEstado']['Lang'][0]['OrdenEstadoIdioma']['name']);
				$htmlPedidos  .= sprintf('<td>%s</td>', $pedido['Orders']['payment']);
				$htmlPedidos  .= sprintf('<td>%s</td>', CakeNumber::currency($pedido['Orders']['total_paid_tax_incl'] , 'CLP'));
				$htmlPedidos  .= sprintf('<td>%s</td>', $pedido['Orders']['date_add']);
				$htmlPedidos  .= '</tr>';
				$totalComprado = $totalComprado + $pedido['Orders']['total_paid_tax_incl'];
			}
			$htmlPedidos  .= '<tfoot>';
			$htmlPedidos  .= '<tr>';
			$htmlPedidos  .= '<td colspan="3">';
			$htmlPedidos  .= '<b>Total comprado:</b>';
			$htmlPedidos  .= '</td>';
			$htmlPedidos  .= '<td colspan="2">';
			$htmlPedidos  .= sprintf('<b>%s pesos</b>', CakeNumber::currency($totalComprado , 'CLP'));
			$htmlPedidos  .= '</td>';
			$htmlPedidos  .= '</tr>';
			$htmlPedidos  .= '</tfoot>';
			$htmlPedidos  .= '</table>';
			$htmlPedidos  .= '</div>';
			echo $htmlPedidos;
			exit;
		}

		echo "<h4>El cliente no registra pedidos.</h4>";
		exit;
	}
}
