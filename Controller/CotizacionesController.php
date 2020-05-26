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
			'order' => 'Cotizacion.id DESC'
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

		if (empty($id_prospecto)) {
			$this->Session->setFlash('Formato no válido.', null, array(), 'warning');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') )
		{				

			$this->Cotizacion->create();
			if ( $this->Cotizacion->saveAll($this->request->data) )
			{
				# Una vez creada la cotización se genera el pdf
				try {
					$generado = $this->generar_pdf($this->Cotizacion->id);
				} catch (Exception $e) {
					$generado = $e->getMessage();		
				}

				$this->Cotizacion->Prospecto->id = $this->request->data['Cotizacion']['prospecto_id'];

				if ($generado == 'Ok') {
					# Se pasa a estado Finalizado
					$this->Cotizacion->Prospecto->saveField('estado_prospecto_id', 'cotizacion');
					# Se cambia el estado de la cotización
					$this->Cotizacion->saveField('estado_cotizacion_id', 1);
					$this->Session->setFlash('Cotización generada y enviada con éxito.', null, array(), 'success');
					$this->redirect(array('action' => 'index'));
				}else{
					# Se pasa a estado esperando información
					$this->Cotizacion->Prospecto->saveField('estado_prospecto_id', 'esperando_informacion');
					# Se cambia el estado de la cotización
					$this->Cotizacion->saveField('estado_cotizacion_id', 2);
					$this->Session->setFlash('Cotización guardada, error: ' . $generado, null, array(), 'danger');
					$this->redirect(array('action' => 'index'));
				}
			}
			else
			{	
				# Se pasa a estado creado
				$this->Cotizacion->Prospecto->id = $this->request->data['Cotizacion']['prospecto_id'];
				$this->Cotizacion->Prospecto->saveField('estado_prospecto_id', 'creado');
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		# Tienda
		$tienda = ClassRegistry::init('Tienda')->find('first', array('conditions' => array('Tienda.id' => $this->Session->read('Tienda.id'))));

		# Obtenemos el prospecto	
		$prospecto = $this->Cotizacion->Prospecto->find('first', array(
			'conditions' => array('Prospecto.id' => $id_prospecto),
			'contain' => array(
				'Transporte',
				'VentaDetalleProducto',
				'VentaCliente',
				'Direccion' => array(
					'Comuna' => array(
						'fields' => array(
							'Comuna.nombre'
						)
					)
				)
			)
		));

		if (empty($prospecto['Direccion']) || empty($prospecto['VentaDetalleProducto'])  || empty($prospecto['VentaCliente'])) {
			$this->Session->setFlash('El prospecto no tiene a información mínima para pasarlo a cotización. Cliente, direccón y productos son requeridos.', null, array(), 'danger');
			$this->redirect(array('controller' => 'prospectos', 'action' => 'edit', $id));
		}

		$prospecto['Prospecto']['total_neto']      = 0;

		foreach ($prospecto['VentaDetalleProducto'] as $ip => $p) {
			$prospecto['VentaDetalleProducto'][$ip]['monto_neto'] = quitar_iva($p['ProductosProspecto']['monto']);
			$prospecto['VentaDetalleProducto'][$ip]['total_neto'] = quitar_iva($p['ProductosProspecto']['monto']) * $p['ProductosProspecto']['cantidad'];
			$prospecto['Prospecto']['total_neto']                 = $prospecto['Prospecto']['total_neto'] + $prospecto['VentaDetalleProducto'][$ip]['total_neto'];
		}

		$prospecto['Prospecto']['descuento_monto'] = 0;

		# Calcular iva
		$prospecto['Prospecto']['iva'] = obtener_iva($prospecto['Prospecto']['total_neto']);

		# Calcular descuento
		if (!empty($prospecto['Prospecto']['descuento'])) {
			$prospecto['Prospecto']['descuento_monto'] = obtener_iva( monto_bruto($prospecto['Prospecto']['total_neto']), $prospecto['Prospecto']['descuento']);
		}
		
	
		$prospecto['Prospecto']['total_bruto'] = monto_bruto($prospecto['Prospecto']['total_neto']) - $prospecto['Prospecto']['descuento_monto'] ;

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
				'Transporte',
				'Prospecto',
				'VentaDetalleProducto'
			)
		));
	
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

	public function generar_pdf($id) {
		
		if ( !$this->Cotizacion->exists($id) ) {
			throw new Exception("Error al generar el PDF. La cotización no fue encontrada", 211);
		}

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
				'Transporte',
				'Prospecto',
				'VentaDetalleProducto'
			),
			'conditions' => array('Cotizacion.id' => $id)
		));

		App::uses('CakePdf', 'Plugin/CakePdf/Pdf');

		@$this->CakePdf = new CakePdf();
		@$this->CakePdf->template('cotizacion','default');
		@$this->CakePdf->viewVars(compact('tienda', 'cotizacion'));
		@$this->CakePdf->write(APP . 'webroot' . DS . 'Pdf' . DS . 'Cotizaciones' . DS . $cotizacion['Cotizacion']['fecha_cotizacion'] . DS . 'cotizacion_' . $cotizacion['Cotizacion']['id'] . '_' . $cotizacion['Cotizacion']['email_cliente'] . '_' . Inflector::slug($cotizacion['Cotizacion']['created']) . '.pdf');

		$cotizacion_nombre = 'cotizacion_' . $cotizacion['Cotizacion']['id'] . '_' . $cotizacion['Cotizacion']['email_cliente'] . '_' . Inflector::slug($cotizacion['Cotizacion']['created']) . '.pdf';

		# Ruta para guardar en la Base de datos
		$archivo = Router::url('/', true) . 'Pdf/Cotizaciones/' . $cotizacion['Cotizacion']['fecha_cotizacion'] . '/' . $cotizacion_nombre;

		# Ruta absoluta del archivo para adjuntarlo	
		$archivoAbsoluto = APP . 'webroot' . DS . 'Pdf' . DS . 'Cotizaciones' . DS . $cotizacion['Cotizacion']['fecha_cotizacion'] . DS . $cotizacion_nombre;

		if( ! $this->Cotizacion->saveField('archivo', $archivo)) {
			throw new Exception("Error al generar el PDF. No se pudo guardar el archivo", 411);
		}else{

			# Generado
			$this->Cotizacion->saveField('generado', 1);

			/**
			* Se envia el email
			*/
			$email = $cotizacion['Cotizacion']['email_cliente'];

			# BCC
			if ( !empty($tienda['Tienda']['emails_bcc']) ) {
				$bcc = explode( ',', trim($tienda['Tienda']['emails_bcc']) );
			}
			
			/**
			 * Clases requeridas
			 */
			$this->View           = new View();
			$this->View->viewPath = 'Cotizaciones' . DS . 'emails';
			$this->View->layout   = 'backend' . DS . 'emails';
			
			$this->View->set(compact('cotizacion', 'tienda'));
			
			$html = $this->View->render('cotizacion_cliente');
			
			$mandrill_apikey = ClassRegistry::init('Tienda')->field('mandrill_apikey', array('id' => $this->Session->read('Tienda.id')));

			if (empty($mandrill_apikey)) {
				return false;
			}

			$mandrill = $this->Components->load('Mandrill');

			$mandrill->conectar($mandrill_apikey);

			if(Configure::read('debug') > 1) {
				$asunto = '[COT-DEV] Se ha creado una cotización en ' . $tienda['Tienda']['url'];
			}else{
				$asunto = '[COT] Se ha creado una cotización en ' . $tienda['Tienda']['url'];
			}
			
			$remitente = array(
				'email' => 'cotizaciones@nodriza.cl',
				'nombre' => sprintf('Ventas %s', $tienda['Tienda']['nombre'])
			);

			$destinatarios = array(
				array(
					'email' => trim($email)
				)
			);

			foreach ($bcc as $ibc => $bc) {
				$destinatarios[] = array(
					'email' => $bc,
					'type' => 'bcc'
				);
			}

			$adjuntos = array(
				array(
                	'type' => 'application/pdf',
                	'name' => $cotizacion_nombre,
                	'content' => chunk_split(base64_encode(file_get_contents($archivoAbsoluto)))
            	)
			);

            $cabeceras = array(
				'Reply-To' => $cotizacion['Cotizacion']['email_vendedor']
			);
			
			$enviado = $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios, $cabeceras, $adjuntos);

			if( $enviado ) {
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
				'Transporte'
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
								array(
									'SpecificPrice.from <= "' . date('Y-m-d H:i:s') . '"',
									'SpecificPrice.to >= "' . date('Y-m-d H:i:s') . '"'
								),
								array(
									'SpecificPrice.from' => '0000-00-00 00:00:00',
									'SpecificPrice.to >= "' . date('Y-m-d H:i:s') . '"'
								),
								array(
									'SpecificPrice.from' => '0000-00-00 00:00:00',
									'SpecificPrice.to' => '0000-00-00 00:00:00'
								),
								array(
									'SpecificPrice.from <= "' . date('Y-m-d H:i:s') . '"',
									'SpecificPrice.to' => '0000-00-00 00:00:00'
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
			'fields' => array('id', 'email_cliente', 'email_vendedor', 'nombre_cliente', 'generado', 'enviado', 'archivo', 'fecha_cotizacion', 'created')
			)
		);

		# Tienda
		$tienda = ClassRegistry::init('Tienda')->find('first', array('conditions' => array('Tienda.id' => $this->Session->read('Tienda.id'))));
		
		if ( $cotizacion['Cotizacion']['generado'] && ! empty($cotizacion['Cotizacion']['archivo']) ) {

			/**
			* Se envia el email
			*/
			$email = $cotizacion['Cotizacion']['email_cliente'];
			
			$cotizacion_nombre = 'cotizacion_' . $cotizacion['Cotizacion']['id'] . '_' . $cotizacion['Cotizacion']['email_cliente'] . '_' . Inflector::slug($cotizacion['Cotizacion']['created']) . '.pdf';

			# Ruta absoluta del archivo para adjuntarlo	
			$archivoAbsoluto = APP . 'webroot' . DS . 'Pdf' . DS . 'Cotizaciones' . DS . $cotizacion['Cotizacion']['fecha_cotizacion'] . DS . $cotizacion_nombre;

			# BCC
			if ( !empty($tienda['Tienda']['emails_bcc']) ) {
				$bcc = explode( ',', trim($tienda['Tienda']['emails_bcc']) );
			}

			/**
			 * Clases requeridas
			 */
			$this->View           = new View();
			$this->View->viewPath = 'Cotizaciones' . DS . 'emails';
			$this->View->layout   = 'backend' . DS . 'emails';
			
			$this->View->set(compact('cotizacion', 'tienda'));
			
			$html = $this->View->render('cotizacion_cliente');
			
			$mandrill_apikey = ClassRegistry::init('Tienda')->field('mandrill_apikey', array('id' => $this->Session->read('Tienda.id')));

			if (empty($mandrill_apikey)) {
				return false;
			}

			$mandrill = $this->Components->load('Mandrill');

			$mandrill->conectar($mandrill_apikey);

			$asunto = '[COT] Se ha creado una cotización en ' . $tienda['Tienda']['url'];
			
			$remitente = array(
				'email' => 'cotizaciones@nodriza.cl',
				'nombre' => sprintf('Ventas %s', $tienda['Tienda']['nombre'])
			);

			$destinatarios = array(
				array(
					'email' => trim($email)
				)
			);

			foreach ($bcc as $ibc => $bc) {
				$destinatarios[] = array(
					'email' => $bc,
					'type' => 'bcc'
				);
			}

			$adjuntos = array(
				array(
                	'type' => 'application/pdf',
                	'name' => $cotizacion_nombre,
                	'content' => chunk_split(base64_encode(file_get_contents($archivoAbsoluto)))
            	)
			);

            $cabeceras = array(
				'Reply-To' => $cotizacion['Cotizacion']['email_vendedor']
			);
			
			$enviado = $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios, $cabeceras, $adjuntos);
				
			if( $enviado ) {
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



	public function cliente_index()
	{
		$paginate = array(
			'recursive' => 0,
			'conditions' => array(
				'Cotizacion.venta_cliente_id' => $this->Auth->user('id'),
				'Cotizacion.generado' => 1
			),
			'fields' => array('Cotizacion.id', 'Cotizacion.fecha_cotizacion', 'Cotizacion.total_bruto', 'Cotizacion.archivo'),
			'order' => array('Venta.fecha_venta' => 'DESC'),
			'limit' => 20
		);

		//----------------------------------------------------------------------------------------------------
		$this->paginate = $paginate;

		$cotizaciones = $this->paginate();

		foreach ($cotizaciones as $key => $coti) {
			if (@file_get_contents($coti['Cotizacion']['archivo']) === false) {
				$cotizaciones[$key]['Cotizacion']['archivo'] = '';
			}
		}

		$this->layout = 'private';

		BreadcrumbComponent::add('Dashboard', '/cliente');
		BreadcrumbComponent::add('Mis cotizaciones', '/cliente/mis-cotizaciones');
		$PageTitle = 'Mis cotizaciones';
		$this->set(compact('PageTitle', 'cotizaciones'));
	}
}
