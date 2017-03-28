<?php
App::uses('AppController', 'Controller');
class CotizacionesController extends AppController
{	
	public $components = array('RequestHandler');

	public function admin_index()
	{
		$paginate = array(); 
    	$conditions = array();
    	$total = 0;
    	$totalMostrados = 0;
    	$categorias = array();

    	$textoBuscar = null;

		// Filtrado  por formulario
		if ( $this->request->is('post') ) {

			if ( ! empty($this->request->data['Filtro']['findby']) && empty($this->request->data['Filtro']['nombre_buscar']) ) {
				$this->Session->setFlash('Ingrese Identificación o email' , null, array(), 'danger');
				$this->redirect(array('action' => 'index'));
			}

			if ( ! empty($this->request->data['Filtro']['findby']) && ! empty($this->request->data['Filtro']['nombre_buscar']) ) {
				$this->redirect(array('controller' => 'cotizaciones', 'action' => 'index', 'findby' => $this->request->data['Filtro']['findby'], 'nombre_buscar' => $this->request->data['Filtro']['nombre_buscar']));
			}
		}

		// Opciones de paginación
		$paginate = array_replace_recursive(array(
			'limit' => 10,
			'fields' => array(),
			'joins' => array(),
			'contain' => array('Prospecto', 'ValidezFecha', 'EstadoCotizacion'),
			'conditions' => array(
					'Cotizacion.tienda_id' => $this->Session->read('Tienda.id')
				),
			'recursive'	=> 0,
			'order' => 'Cotizacion.id ASC'
		));

		/**
		* Buscar por
		*/
		if ( !empty($this->request->params['named']['findby']) && !empty($this->request->params['named']['nombre_buscar']) ) {
			
			$paginate		= array_replace_recursive($paginate, array(
				'conditions'	=> array(
					sprintf('Cotizacion.%s', $this->request->params['named']['findby']) => trim($this->request->params['named']['nombre_buscar'])
				)
			));
					
			// Texto ingresado en el campo buscar
			$textoBuscar = $this->request->params['named']['nombre_buscar'];
			
		}else if ( ! empty($this->request->params['named']['findby'])) {
			$this->Session->setFlash('No se aceptan campos vacios.' ,  null, array(), 'danger');
		}

		// Total de registros
		$total 		= $this->Cotizacion->find('count', array(
			'joins' => array(),
			'conditions' => array()
		));


		$this->paginate = $paginate;


		$cotizaciones	= $this->paginate();

		BreadcrumbComponent::add('Cotizaciones ');
		$this->set(compact('cotizaciones'));
	}

	public function admin_add( $id_prospecto = '' ) 
	{	
		if ( $this->request->is('post') )
		{				

			$this->Cotizacion->create();
			if ( $this->Cotizacion->save($this->request->data) )
			{
				# Una vez creada la cotización se genera el pdf
				try {
					$generado = $this->generar_pdf();
				} catch (Exception $e) {
					$generado = $e->getMessage();		
				}

				if ($generado == 'Ok') {
					$this->Session->setFlash('Cotización generada y enviada con éxito.', null, array(), 'success');
					$this->redirect(array('action' => 'index'));
				}else{
					$this->Session->setFlash('Cotización guardada, error: ' . $generado, null, array(), 'danger');
					$this->redirect(array('action' => 'index'));
				}
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$prospecto = array();
		$productos = array();
		$cliente   = array();
		$tienda    = array();

		# Viene desde prospecto
		if ( ! empty($id_prospecto) ) {

			# Tienda
			$tienda = ClassRegistry::init('Tienda')->find('first', array('conditions' => array('Tienda.id' => $this->Session->read('Tienda.id'))));

			# Obtenemos el prospecto	
			$prospecto = $this->Cotizacion->Prospecto->find('first', array(
				'conditions' => array('Prospecto.id' => $id_prospecto)
				)
			);
			# Verificamos la existencia del prospecto
			if (empty($prospecto)) {
				$this->Session->setFlash('El prospecto seleccionado no existe o no se creó correctamente.', null, array(), 'danger');
				$this->redirect(array('controller' => 'prospectos', 'action' => 'index'));
			}
			# Obtenemos los ID´S de productos relacionados al prospecto
			$prospectoProductos = $this->Cotizacion->Prospecto->ProductotiendaProspecto->find('all', array(
				'conditions' => array('prospecto_id' => $prospecto['Prospecto']['id'])
			));
			# Obtenemos los productos por el grupo de ID´S
			if (!empty($prospectoProductos)) {
				$productos = ClassRegistry::init('Productotienda')->find('all', array(
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

				$totalProductosNeto 	= 0;
				$totalProductosNetoDesc = 0;
				$totalDescuento 		= 0;
				$iva 					= 0;

				# Se agrega los valores de descuentos y cantidad a los productos relacinados
				foreach ($prospectoProductos as $ix => $prospectoProducto) {
					foreach ($productos as $ik => $producto) {
						if ($prospectoProductos[$ix]['ProductotiendaProspecto']['id_product'] == $productos[$ik]['Productotienda']['id_product']) {

							$precio_normal 	= $this->precio($producto['Productotienda']['price'], $producto['TaxRulesGroup']['TaxRule'][0]['Tax']['rate']);
							$precio_neto 	= $producto['Productotienda']['price'];

							# Aplicamos precio específico si es que existe
							if ( ! empty($producto['SpecificPrice']) ) {
								if ($producto['SpecificPrice'][0]['reduction'] > 0) {

									$precio_normal	= $this->calcularDescuento($precio_normal, ($producto['SpecificPrice'][0]['reduction'] * 100) );
									$precio_neto 	= $this->calcularDescuento($producto['Productotienda']['price'], $producto['SpecificPrice'][0]['reduction'] * 100);
									
								}
							}

							# Aplicamos descuento por producto
							if ( $prospectoProductos[$ix]['ProductotiendaProspecto']['descuento'] > 0) {
								$precio_neto_desc	= $this->calcularDescuento($precio_neto, $prospectoProductos[$ix]['ProductotiendaProspecto']['descuento']);
								$totalDescuento 	= $totalDescuento + ($precio_neto - $precio_neto_desc);
							}else{
								$precio_neto_desc = $precio_neto;
							}

							# Totales
							#$totalProductosNeto = $totalProductosNeto + ($precio_neto * $prospectoProductos[$ix]['ProductotiendaProspecto']['cantidad']);
							$totalProductosNetoDesc = $totalProductosNetoDesc + ($precio_neto_desc * $prospectoProductos[$ix]['ProductotiendaProspecto']['cantidad']);
						
							$productos[$ik]['Productotienda']['precio']				= CakeNumber::currency($precio_normal , 'CLP');
							$productos[$ik]['Productotienda']['precio_neto'] 		= CakeNumber::currency($precio_neto , 'CLP');
							$productos[$ik]['Productotienda']['precio_neto_desc'] 	= CakeNumber::currency($precio_neto_desc , 'CLP');
							$productos[$ik]['Productotienda']['total_neto_desc'] 	= CakeNumber::currency(($precio_neto_desc * $prospectoProductos[$ix]['ProductotiendaProspecto']['cantidad']) , 'CLP');
							$productos[$ik]['Productotienda']['cantidad'] 			= $prospectoProductos[$ix]['ProductotiendaProspecto']['cantidad'];
							$productos[$ik]['Productotienda']['nombre_descuento'] 	= $prospectoProductos[$ix]['ProductotiendaProspecto']['nombre_descuento'];
							$productos[$ik]['Productotienda']['descuento'] 			= $prospectoProductos[$ix]['ProductotiendaProspecto']['descuento'];
						}
					}
				}


				# Aplicamos descuento global a la cotizacion
				if ($prospecto['Prospecto']['descuento'] > 0) {
					
					$totalDescuento = $totalDescuento + $totalProductosNetoDesc - $this->calcularDescuento($totalProductosNetoDesc, $prospecto['Prospecto']['descuento']);
					$totalProductosNeto = $totalProductosNetoDesc - $totalDescuento;
				}else{
					$totalProductosNeto = $totalProductosNetoDesc;
				}

				$iva = $totalProductosNeto * 0.19;


				#$prospecto['total_productos_neto'] = CakeNumber::currency($totalProductosNeto , 'CLP');
				$prospecto['total_productos_neto_desc'] = CakeNumber::currency($totalProductosNetoDesc , 'CLP');
				$prospecto['total_descuento'] = CakeNumber::currency($totalDescuento , 'CLP');
				$prospecto['iva'] = CakeNumber::currency($iva , 'CLP');
				$prospecto['total_bruto'] = CakeNumber::currency(($iva + $totalProductosNeto) , 'CLP');
			}

			# Se obtienen el clientes relacionado y la direccion para la cotización
			if (!empty($prospecto['Prospecto']['id_customer'])) {
				$cliente = ClassRegistry::init('Cliente')->find('first', array(
		    		'contain' => array(
		    			'Clientedireccion' => array(
		    				'conditions' => array('Clientedireccion.id_address' => $prospecto['Prospecto']['id_address']),
		    				'Paise' => array('Lang'), 'Region')
		    		),
		    		'conditions' => array(
		    			'Cliente.id_customer' => $prospecto['Prospecto']['id_customer']
		    			)
		    	));
			}
			
		}


		$monedas	= $this->Cotizacion->Moneda->find('list');
		$estadoCotizaciones	= $this->Cotizacion->EstadoCotizacion->find('list');
		$validezFechas	= $this->Cotizacion->ValidezFecha->find('list');
		BreadcrumbComponent::add('Cotizaciones ', '/cotizaciones');
		BreadcrumbComponent::add('Agregar ');
		$this->set(compact('monedas', 'estadoCotizaciones', 'validezFechas', 'prospecto' ,'productos', 'cliente', 'tienda'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Cotizacion->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Cotizacion->save($this->request->data) )
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
			$this->request->data	= $this->Cotizacion->find('first', array(
				'conditions'	=> array('Cotizacion.id' => $id)
			));
		}
		$monedas	= $this->Cotizacion->Moneda->find('list');
		$estadoCotizaciones	= $this->Cotizacion->EstadoCotizacion->find('list');
		$prospectos	= $this->Cotizacion->Prospecto->find('list');
		$validezFechas	= $this->Cotizacion->ValidezFecha->find('list');
		BreadcrumbComponent::add('Cotizaciones ', '/cotizaciones');
		BreadcrumbComponent::add('Editar ');
		$this->set(compact('monedas', 'estadoCotizaciones', 'prospectos', 'validezFechas'));
	}

	public function admin_view($id = null)
	{
		if ( ! $this->Cotizacion->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		# Tienda
		$tienda = ClassRegistry::init('Tienda')->find('first', array('conditions' => array('Tienda.id' => $this->Session->read('Tienda.id'))));

		# Datos e la cotización
		$this->request->data	= $this->Cotizacion->find('first', array(
			'conditions'	=> array('Cotizacion.id' => $id),
			'contain' => array(
				'Moneda',
				'EstadoCotizacion',
				'ValidezFecha',
			)
		));

		$productos = array();

		# Obtenemos los ID´S de productos relacionados de la cotización
		$cotizacionProductos = $this->Cotizacion->ProductotiendaCotizacion->find('all', array(
			'conditions' => array('cotizacion_id' => $id)
		));

		# Obtenemos los productos por el grupo de ID´S
		if (!empty($cotizacionProductos)) {
			$productos = ClassRegistry::init('Productotienda')->find('all', array(
				'conditions' => array('Productotienda.id_product' => Hash::extract($cotizacionProductos, '{n}.ProductotiendaCotizacion.id_product')),
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
			foreach ($cotizacionProductos as $ix => $cotizacionProducto) {
				foreach ($productos as $ik => $producto) {
					if ($cotizacionProductos[$ix]['ProductotiendaCotizacion']['id_product'] == $productos[$ik]['Productotienda']['id_product']) {
						$productos[$ik]['Productotienda']['precio_neto'] 		= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['precio_neto'];
						$productos[$ik]['Productotienda']['total_neto'] 		= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['total_neto'];
						$productos[$ik]['Productotienda']['cantidad'] 			= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['cantidad'];
						$productos[$ik]['Productotienda']['nombre_descuento'] 	= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['nombre_descuento'];
						$productos[$ik]['Productotienda']['descuento'] 			= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['descuento'];
					}
				}
			}

		}
	
		$monedas	= $this->Cotizacion->Moneda->find('list');
		$estadoCotizaciones	= $this->Cotizacion->EstadoCotizacion->find('list');
		$prospectos	= $this->Cotizacion->Prospecto->find('list');
		$validezFechas	= $this->Cotizacion->ValidezFecha->find('list');
		BreadcrumbComponent::add('Cotizaciones ', '/cotizaciones');
		BreadcrumbComponent::add('Editar ');
		$this->set(compact('monedas', 'estadoCotizaciones', 'prospectos', 'validezFechas', 'tienda', 'productos'));
	}

	public function admin_delete($id = null)
	{
		$this->Cotizacion->id = $id;
		if ( ! $this->Cotizacion->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Cotizacion->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Cotizacion->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Cotizacion->_schema);
		$modelo			= $this->Cotizacion->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}

	public function generar_pdf() {
		
		# Tienda
		$tienda = ClassRegistry::init('Tienda')->find('first', array('conditions' => array('Tienda.id' => $this->Session->read('Tienda.id'))));
		
		if (empty($tienda) || empty($tienda['Tienda']['logo']) || empty($tienda['Tienda']['nombre_fantasia']) || empty($tienda['Tienda']['rut']) || empty($tienda['Tienda']['direccion']) || empty($tienda['Tienda']['giro']) || empty($tienda['Tienda']['fono']) ) {
			throw new Exception("Error al generar el PDF. La tienda no fue encontrada o no está correctamente configurada", 311);
		}

		# Datos e la cotización
		$cotizacion	= $this->Cotizacion->find('first', array(
			'contain' => array(
				'Moneda',
				'EstadoCotizacion',
				'ValidezFecha',
			),
			'order' => array('Cotizacion.id' => 'DESC')
		));

		$this->Cotizacion->id = $cotizacion['Cotizacion']['id'];

		if ( ! $this->Cotizacion->exists() ) {
			throw new Exception("Error al generar el PDF. La cotización no fue encontrada", 211);
		}

		$productos = array();

		# Obtenemos los ID´S de productos relacionados de la cotización
		$cotizacionProductos = $this->Cotizacion->ProductotiendaCotizacion->find('all', array(
			'conditions' => array('cotizacion_id' => $cotizacion['Cotizacion']['id'])
		));

		# Obtenemos los productos por el grupo de ID´S
		if (!empty($cotizacionProductos)) {
			$productos = ClassRegistry::init('Productotienda')->find('all', array(
				'conditions' => array('Productotienda.id_product' => Hash::extract($cotizacionProductos, '{n}.ProductotiendaCotizacion.id_product')),
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
			foreach ($cotizacionProductos as $ix => $cotizacionProducto) {
				foreach ($productos as $ik => $producto) {
					if ($cotizacionProductos[$ix]['ProductotiendaCotizacion']['id_product'] == $productos[$ik]['Productotienda']['id_product']) {
						$productos[$ik]['Productotienda']['precio_neto'] 		= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['precio_neto'];
						$productos[$ik]['Productotienda']['total_neto'] 		= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['total_neto'];
						$productos[$ik]['Productotienda']['cantidad'] 			= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['cantidad'];
						$productos[$ik]['Productotienda']['nombre_descuento'] 	= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['nombre_descuento'];
						$productos[$ik]['Productotienda']['descuento'] 			= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['descuento'];
					}
				}
			}

		}

		App::uses('CakePdf', 'Plugin/CakePdf/Pdf');

		$this->CakePdf = new CakePdf();
		$this->CakePdf->template('admin_generar','default');
		$this->CakePdf->viewVars(compact('tienda', 'cotizacion' ,'productos'));
		$this->CakePdf->write(APP . 'webroot' . DS . 'Pdf' . DS . 'Cotizaciones' . DS . $cotizacion['Cotizacion']['fecha_cotizacion'] . DS . 'cotizacion_' . $cotizacion['Cotizacion']['id'] . '_' . $cotizacion['Cotizacion']['email_cliente'] . '_' . Inflector::slug($cotizacion['Cotizacion']['created']) . '.pdf');

		# Ruta para guardar en la Base de datos
		$archivo = Router::url('/', true) . 'Pdf/Cotizaciones/' . $cotizacion['Cotizacion']['fecha_cotizacion'] . '/cotizacion_' . $cotizacion['Cotizacion']['id'] . '_' . $cotizacion['Cotizacion']['email_cliente'] . '_' . Inflector::slug($cotizacion['Cotizacion']['created']) . '.pdf';

		# Ruta absoluta del archivo para adjuntarlo	
		$archivoAbsoluto = APP . 'webroot' . DS . 'Pdf' . DS . 'Cotizaciones' . DS . $cotizacion['Cotizacion']['fecha_cotizacion'] . DS . 'cotizacion_' . $cotizacion['Cotizacion']['id'] . '_' . $cotizacion['Cotizacion']['email_cliente'] . '_' . Inflector::slug($cotizacion['Cotizacion']['created']) . '.pdf';

		if( ! $this->Cotizacion->saveField('archivo', $archivo)) {
			throw new Exception("Error al generar el PDF. No se pudo guardar el archivo", 411);
		}else{

			# Generado
			$this->Cotizacion->saveField('generado', 1);

			/**
			* Se envia el email
			*/
			$email = $cotizacion['Cotizacion']['email_cliente'];

			App::uses('CakeEmail', 'Network/Email');
		
			$this->Email = new CakeEmail();
			$this->Email
			->viewVars(compact('cotizacion', 'tienda'))
			->emailFormat('html')
			->from(array('ventas@nodriza.cl' => 'Ventas Nodriza Spa'))
			->to($email)
			->addBcc( 'ventas@nodriza.cl' , 'Ventas Nodriza Spa') 
			->template('cotizacion_cliente')
			->attachments(array($archivoAbsoluto))
			->subject('[COT] Se ha creado una cotización en ' . $tienda['Tienda']['url']);
			
			if( $this->Email->send() ) {
				# Enviado
				$this->Cotizacion->saveField('enviado', 1);
				return "Ok";
			}else{
				throw new Exception("Error al enviar la cotización al cliente. Intente enviarla manualmente.", 511);
			}
		}

	}

	public function admin_generar($id = '') {
		if (empty($id)) {
			$this->Session->setFlash('Error al generar el registro.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->Cotizacion->id = $id;
		if ( ! $this->Cotizacion->exists() ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		# Tienda
		$tienda = ClassRegistry::init('Tienda')->find('first', array('conditions' => array('Tienda.id' => $this->Session->read('Tienda.id'))));

		# Datos e la cotización
		$cotizacion	= $this->Cotizacion->find('first', array(
			'conditions'	=> array('Cotizacion.id' => $id),
			'contain' => array(
				'Moneda',
				'EstadoCotizacion',
				'ValidezFecha',
			)
		));

		$productos = array();

		# Obtenemos los ID´S de productos relacionados de la cotización
		$cotizacionProductos = $this->Cotizacion->ProductotiendaCotizacion->find('all', array(
			'conditions' => array('cotizacion_id' => $id)
		));

		# Obtenemos los productos por el grupo de ID´S
		if (!empty($cotizacionProductos)) {
			$productos = ClassRegistry::init('Productotienda')->find('all', array(
				'conditions' => array('Productotienda.id_product' => Hash::extract($cotizacionProductos, '{n}.ProductotiendaCotizacion.id_product')),
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
			foreach ($cotizacionProductos as $ix => $cotizacionProducto) {
				foreach ($productos as $ik => $producto) {
					if ($cotizacionProductos[$ix]['ProductotiendaCotizacion']['id_product'] == $productos[$ik]['Productotienda']['id_product']) {
						$productos[$ik]['Productotienda']['precio_neto'] 		= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['precio_neto'];
						$productos[$ik]['Productotienda']['total_neto'] 		= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['total_neto'];
						$productos[$ik]['Productotienda']['cantidad'] 			= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['cantidad'];
						$productos[$ik]['Productotienda']['nombre_descuento'] 	= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['nombre_descuento'];
						$productos[$ik]['Productotienda']['descuento'] 			= $cotizacionProductos[$ix]['ProductotiendaCotizacion']['descuento'];
					}
				}
			}

		}

		App::uses('CakePdf', 'Plugin/CakePdf/Pdf');

		$this->CakePdf = new CakePdf();
		$this->CakePdf->template('admin_generar','default');
		$this->CakePdf->viewVars(compact('tienda', 'cotizacion' ,'productos'));
		$this->CakePdf->write(APP . 'webroot' . DS . 'Pdf' . DS . 'Cotizaciones' . DS . $cotizacion['Cotizacion']['fecha_cotizacion'] . DS . 'cotizacion_' . $id . '_' . $cotizacion['Cotizacion']['email_cliente'] . '_' . Inflector::slug($cotizacion['Cotizacion']['created']) . '.pdf');

		# Ruta para guardar en la Base de datos
		$archivo = Router::url('/', true) . 'Pdf/Cotizaciones/' . $cotizacion['Cotizacion']['fecha_cotizacion'] . '/cotizacion_' . $cotizacion['Cotizacion']['id'] . '_' . $cotizacion['Cotizacion']['email_cliente'] . '_' . Inflector::slug($cotizacion['Cotizacion']['created']) . '.pdf';

		# Ruta absoluta del archivo para adjuntarlo	
		$archivoAbsoluto = APP . 'webroot' . DS . 'Pdf' . DS . 'Cotizaciones' . DS . $cotizacion['Cotizacion']['fecha_cotizacion'] . DS . 'cotizacion_' . $cotizacion['Cotizacion']['id'] . '_' . $cotizacion['Cotizacion']['email_cliente'] . '_' . Inflector::slug($cotizacion['Cotizacion']['created']) . '.pdf';
		
		$this->Cotizacion->saveField('archivo', $archivo);
		
		$this->set(compact('tienda', 'productos', 'archivo'));
	}

	public function admin_reenviar($id = '') {
		
		$this->Cotizacion->id = $id;
		if ( ! $this->Cotizacion->exists() ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$cotizacion = $this->Cotizacion->find('first', array(
			'conditions' => array(
				'Cotizacion.id' => $id
				),
			'fields' => array('id', 'email_cliente', 'nombre_cliente', 'generado', 'enviado', 'archivo', 'fecha_cotizacion', 'created')
			)
		);

		# Tienda
		$tienda = ClassRegistry::init('Tienda')->find('first', array('conditions' => array('Tienda.id' => $this->Session->read('Tienda.id'))));
		
		if ( $cotizacion['Cotizacion']['generado'] && ! empty($cotizacion['Cotizacion']['archivo']) ) {

			/**
			* Se envia el email
			*/
			$email = $cotizacion['Cotizacion']['email_cliente'];
			
			# Ruta absoluta del archivo para adjuntarlo	
			$archivoAbsoluto = APP . 'webroot' . DS . 'Pdf' . DS . 'Cotizaciones' . DS . $cotizacion['Cotizacion']['fecha_cotizacion'] . DS . 'cotizacion_' . $cotizacion['Cotizacion']['id'] . '_' . $cotizacion['Cotizacion']['email_cliente'] . '_' . Inflector::slug($cotizacion['Cotizacion']['created']) . '.pdf';
			

			App::uses('CakeEmail', 'Network/Email');
		
			$this->Email = new CakeEmail();
			$this->Email
			->viewVars(compact('cotizacion', 'tienda'))
			->emailFormat('html')
			->from(array('ventas@nodriza.cl' => 'Ventas Nodriza Spa'))
			->to($email)
			->addBcc( 'ventas@nodriza.cl' , 'Ventas Nodriza Spa') 
			->template('cotizacion_cliente')
			->attachments(array($archivoAbsoluto))
			->subject('[COT] Se ha creado una cotización en ' . $tienda['Tienda']['url']);
			
			if( $this->Email->send() ) {
				# Enviado
				$this->Cotizacion->saveField('enviado', 1);
				$this->Session->setFlash('Se ha enviado con éxito el email al cliente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}else{
				$this->Session->setFlash('Ocurrió un error al enviar el email. Contacte a su administrador.', null, array(), 'danger');
				$this->redirect(array('action' => 'index'));
			}

		}else {
			$this->Session->setFlash('Esta cotización no ha sido generada.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

	}
}
