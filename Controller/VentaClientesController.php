<?php
App::uses('AppController', 'Controller');
App::uses('VentasController', 'Controller');
class VentaClientesController extends AppController
{
	

	public function admin_index()
	{	
		$paginate = array(); 
    	$conditions = array();

		// Filtrado de clientes por formulario
		if ( $this->request->is('post') ) {

			if ( ! empty($this->request->data['Filtro']['findby']) && empty($this->request->data['Filtro']['nombre_buscar']) ) {
				$this->Session->setFlash('Ingrese nombre o referencia del producto' , null, array(), 'danger');
				$this->redirect(array('action' => 'index'));
			}

			if ( ! empty($this->request->data['Filtro']['findby']) && ! empty($this->request->data['Filtro']['nombre_buscar']) ) {
				$this->redirect(array('controller' => 'ventaClientes', 'action' => 'index', 'findby' => $this->request->data['Filtro']['findby'], 'nombre_buscar' => $this->request->data['Filtro']['nombre_buscar']));
			}
		}
			

		// Opciones de paginación
		$paginate = array_replace_recursive(array(
			'limit' => 10,
			'fields' => array(),
			'joins' => array(),
			'contain' => array(),
			'conditions' => array(),
			'order' => array('VentaCliente.created' => 'DESC')
		));


		/**
		* Buscar por
		*/
		if ( !empty($this->request->params['named']['findby']) && !empty($this->request->params['named']['nombre_buscar']) ) {

			/**
			* Agregar condiciones a la paginación
			* según el criterio de busqueda (código de referencia o nombre del producto)
			*/
			switch ($this->request->params['named']['findby']) {
				case 'email':
					$paginate		= array_replace_recursive($paginate, array(
						'conditions'	=> array(
							'VentaCliente.email' => trim($this->request->params['named']['nombre_buscar'])
						)
					));
					break;
				
				case 'nombre':
					$paginate		= array_replace_recursive($paginate, array(
						'conditions'	=> array(
							'VentaCliente.nombre LIKE "%' . trim($this->request->params['named']['nombre_buscar']) . '%"'
						)
					));
					break;
			}			
			
		}else if ( ! empty($this->request->params['named']['findby'])) {
			$this->Session->setFlash('No se aceptan campos vacios.' ,  null, array(), 'danger');
		}


		$this->paginate = $paginate;

		$ventaClientes	= $this->paginate();
		
		if (empty($ventaClientes)) {
			$this->Session->setFlash(sprintf('No se encontraron resultados para %s', $this->request->params['named']['nombre_buscar']) , null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		BreadcrumbComponent::add('Clientes');
		$this->set(compact('ventaClientes'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->VentaCliente->create();
			if ( $this->VentaCliente->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->VentaCliente->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->VentaCliente->save($this->request->data) )
			{
				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$this->request->data = $this->VentaCliente->find('first', array(
			'conditions' => array(
				'VentaCliente.id' => $id
			),
			'contain' => array(
				'Direccion' => array(
					'Comuna'
				),
				'Prospecto' => array(
					'fields' => array(
						'Prospecto.id', 'Prospecto.estado_prospecto_id'
					),
					'Cotizacion' => array(
						'fields' => array(
							'Cotizacion.id', 'Cotizacion.nombre', 'Cotizacion.created', 'Cotizacion.total_bruto'
						)
					)
				),
				'Venta' => array(
					'order' => array('Venta.fecha_venta' => 'DESC'),
					'fields' => array(
						'Venta.id', 'Venta.fecha_venta', 'Venta.total', 'Venta.venta_estado_id', 'Venta.picking_estado'
					),
					'VentaEstado' => array(
						'fields' => array(
							'VentaEstado.nombre', 'VentaEstado.venta_estado_categoria_id'
						),
						'VentaEstadoCategoria' => array(
							'fields' => array(
								'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo', 'VentaEstadoCategoria.venta', 'VentaEstadoCategoria.final'
							)
						)
					)
				)
			)
		));

		$this->request->data['Metricas'] = array(
			'total_comprado' => 0,
			'total_cotizado' => array_sum(Hash::extract($this->request->data['Prospecto'], '{n}[estado_prospecto_id=cotizacion].Cotizacion.{n}.total_bruto')),
			'cantidad_prospectos' => count($this->request->data['Prospecto'])
		);

		# Total comprado
		foreach ($this->request->data['Venta'] as $iv => $v) {
			if ($v['VentaEstado']['VentaEstadoCategoria']['venta']) {
				$this->request->data['Metricas']['total_comprado'] = $this->request->data['Metricas']['total_comprado'] + $v['total'];
			}
		}

		BreadcrumbComponent::add('Clientes', '/ventaClientes');
		BreadcrumbComponent::add('Editar cliente');

		$comunas = ClassRegistry::init('Comuna')->find('list', array('fields' => array('Comuna.id', 'Comuna.nombre'), 'order' => array('Comuna.nombre' => 'ASC')));

		$this->set(compact('cliente', 'comunas'));
	}

	public function admin_delete($id = null)
	{
		$this->VentaCliente->id = $id;
		if ( ! $this->VentaCliente->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->VentaCliente->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{	
		# Aumentamos el tiempo máxmimo de ejecución para evitar caídas
        set_time_limit(600);
        ini_set('memory_limit', -1);

		 $datos          = $this->VentaCliente->find('all', array(
            'recursive'             => -1
        ));

        $campos         = array_keys($this->VentaCliente->_schema);
        $modelo         = $this->VentaCliente->alias;
		
		$this->set(compact('datos', 'campos', 'modelo'));
	}


	public function admin_view($id)
	{
		if ( ! $this->VentaCliente->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$cliente = $this->VentaCliente->find('first', array(
			'conditions' => array(
				'VentaCliente.id' => $id
			),
			'contain' => array(
				'Direccion' => array(
					'Comuna'
				),
				'Prospecto' => array(
					'fields' => array(
						'Prospecto.id', 'Prospecto.estado_prospecto_id'
					),
					'Cotizacion' => array(
						'fields' => array(
							'Cotizacion.id', 'Cotizacion.nombre', 'Cotizacion.created', 'Cotizacion.total_bruto'
						)
					)
				),
				'Venta' => array(
					'order' => array('Venta.fecha_venta' => 'DESC'),
					'fields' => array(
						'Venta.id', 'Venta.fecha_venta', 'Venta.total', 'Venta.venta_estado_id', 'Venta.picking_estado'
					),
					'VentaEstado' => array(
						'fields' => array(
							'VentaEstado.nombre', 'VentaEstado.venta_estado_categoria_id'
						),
						'VentaEstadoCategoria' => array(
							'fields' => array(
								'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo', 'VentaEstadoCategoria.venta', 'VentaEstadoCategoria.final'
							)
						)
					)
				)
			)
		));

		$cliente['Metricas'] = array(
			'total_comprado' => 0,
			'total_cotizado' => array_sum(Hash::extract($cliente['Prospecto'], '{n}[estado_prospecto_id=cotizacion].Cotizacion.{n}.total_bruto')),
			'cantidad_prospectos' => count($cliente['Prospecto'])
		);

		# Total comprado
		foreach ($cliente['Venta'] as $iv => $v) {
			if ($v['VentaEstado']['VentaEstadoCategoria']['venta']) {
				$cliente['Metricas']['total_comprado'] = $cliente['Metricas']['total_comprado'] + $v['total'];
			}
		}

		BreadcrumbComponent::add('Clientes', '/ventaClientes');
		BreadcrumbComponent::add('Ver cliente');

		$this->set(compact('cliente'));
	}



	/**
	 * Lista todos los clientes
	 * Endpoint :  /api/clientes.json
	 */
    public function api_index() {

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

    	$qry = array(
    		'order' => array('VentaCliente.id' => 'desc'),
    		'contain' => array(
    			'Direccion' => array(
    				'Comuna'
    			)
    		)
    	);

    	$paginacion = array(
        	'limit' => 0,
        	'offset' => 0,
        	'total' => 0
        );

    	if (isset($this->request->query['id'])) {
    		if (!empty($this->request->query['id'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'VentaCliente.id' => $this->request->query['id'])));
    		}
    	}

    	if (isset($this->request->query['limit'])) {
    		if (!empty($this->request->query['limit'])) {
    			$qry = array_replace_recursive($qry, array('limit' => $this->request->query['limit']));
    			$paginacion['limit'] = $this->request->query['limit'];
    		}
    	}

    	if (isset($this->request->query['offset'])) {
    		if (!empty($this->request->query['offset'])) {
    			$qry = array_replace_recursive($qry, array('offset' => $this->request->query['offset']));
    			$paginacion['offset'] = $this->request->query['offset'];
    		}
    	}

    	if (isset($this->request->query['email'])) {
    		if (!empty($this->request->query['email'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'VentaCliente.email LIKE' => '%'.$this->request->query['email'].'%' )));
    		}
    	}
   
        $clientes = $this->VentaCliente->find('all', $qry);

        foreach ($clientes as $ic => $c) {
        	foreach ($c['Direccion'] as $id => $d) {

        		$direccion = array(
        			'Direccion' => $d,
        			'Comuna' => $d['Comuna']
        		);

        		$v             =  new View();
				$v->autoRender = false;
				$v->output     = '';
				$v->layoutPath = '';
				$v->layout     = '';
				$v->set(compact('direccion'));	

				$clientes[$ic]['Direccion'][$id]['block'] = $v->render('/Elements/direcciones/address-block');
        	}

        }

        $this->set(array(
            'clientes' => $clientes,
            '_serialize' => array('clientes')
        ));
    }



    /**
     * Visualiza un cliente
     * Endpoint: /api/clientes/view/:id.json
     * @param  [type] $id id externo del producto
     */
    public function api_view($id) {
    	
    	$token = '';

    	if (isset($this->request->query['token'])) {
    		$token = $this->request->query['token'];
    	}

    	# Existe token
		if (!isset($token)) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
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

		$cliente = $this->VentaCliente->find('first', array(
			'conditions' => array(
				'VentaCliente.id' => $id
			),
			'contain' => array(
				'Direccion' => array(
					'Comuna'
				)
			)
		));

		foreach ($cliente['Direccion'] as $id => $d) {

    		$direccion = array(
    			'Direccion' => $d,
    			'Comuna' => $d['Comuna']
    		);

    		$v             =  new View();
			$v->autoRender = false;
			$v->output     = '';
			$v->layoutPath = '';
			$v->layout     = '';
			$v->set(compact('direccion'));	

			$cliente['Direccion'][$id]['block'] = $v->render('/Elements/direcciones/address-block');
    	}

        $this->set(array(
            'cliente' => $cliente['VentaCliente'],
            'direccion' => $cliente['Direccion'],
            '_serialize' => array('cliente', 'direccion')
        ));
			
    }

    /**
     * [api_add description]
     * @return [type] [description]
     */
    public function api_add() {

		# Solo método POST
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501,
				'name' => 'error',
				'message' => 'Método no permitido'
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


		if (empty($this->request->data['VentaCliente']['nombre'])
			|| empty($this->request->data['VentaCliente']['email'])) {
			$response = array(
				'code' => 504,
				'created' => false,
				'message' => 'Nombre y Email son requeridos.'
			);

			throw new CakeException($response);
		}


		$existe = $this->VentaCliente->find('first', array('conditions' => array('email' => $this->request->data['VentaCliente']['email'])));

		if (!empty($existe)) {

			$response = array(
				'code' => 504,
				'created' => false,
				'message' => 'Email ingresado ya existe.'
			);

			throw new CakeException($response);
		}

		$resultado = array(
			'code' => 201,
			'created' => false,
			'updated' => false
		);

		$log = array();

		$log[] = array(
			'Log' => array(
				'administrador' => 'Rest api',
				'modulo' => 'Venta Clientes',
				'modulo_accion' => json_encode($this->request->data)
			)
		);

		
		if ($this->VentaCliente->save($this->request->data)){

			$log[] = array(
				'Log' => array(
					'administrador' => 'Rest api',
					'modulo' => 'VentaCliente',
					'modulo_accion' => 'Creación: cliente id ' . $this->VentaCliente->id
				)
			);

			$cliente = $this->VentaCliente->find('first', array('conditions' => array('id' => $this->VentaCliente->id)));

			$resultado = array(
				'code' => 200,
				'created' => true,
				'cliente' => $cliente['VentaCliente']
			);
		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		$this->set(array(
			'response'   => $resultado,
			'_serialize' => array('response')
	    ));
	}


	/** 
	 *
	 *	PUBLIC
	 * 
	 */
	public function notificar_link_acceso($cliente_id = null)
	{
		$cliente = ClassRegistry::init('VentaCliente')->find('first', array(
			'conditions' => array(
				'VentaCliente.id' => $cliente_id				
			)
		));

		# Obtenemos token y lo validamos
		$gettoken = ClassRegistry::init('Token')->find('first', array(
			'conditions' => array(
				'Token.venta_cliente_id' => $cliente_id
			),
			'order' => array('Token.created' => 'DESC')
		));
		
		if (empty($gettoken)) {
			# creamos un token de acceso vía email
			$token = ClassRegistry::init('Token')->crear_token_cliente($cliente_id)['token'];

		}else if (!ClassRegistry::init('Token')->validar_token($gettoken['Token']['token'], 'cliente')){
			# creamos un token de acceso vía email
			$token = ClassRegistry::init('Token')->crear_token_cliente($cliente_id)['token'];

		}else{
			$token = $gettoken['Token']['token'];
		}

		if (empty($token)) {
			return false;
		}

		$this->View					= new View();
		$this->View->viewPath		= 'VentaClientes' . DS . 'emails';
		$this->View->layoutPath		= 'Correos' . DS . 'html';
		
		$url = obtener_url_base();

		$tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
	    	'Tienda.id', 'Tienda.nombre', 'Tienda.mandrill_apikey', 'Tienda.logo', 'Tienda.direccion'
	    ));
		
		$this->View->set(compact('cliente', 'url', 'token', 'tienda'));
		$html						= $this->View->render('cliente_link_acceso');
		
		$mandrill_apikey = $tienda['Tienda']['mandrill_apikey'];

		if (empty($mandrill_apikey)) {
			return false;
		}

		$mandrill = $this->Components->load('Mandrill');

		$mandrill->conectar($mandrill_apikey);

		$asunto = sprintf('[%s] Hemos enviado tu link mágico', rand(10, 1000));		
		
		if (Configure::read('ambiente') == 'dev') {
			$asunto = sprintf('[DEV-%s] Hemos enviado tu link mágico', rand(10, 1000));
		}
		
		$remitente = array(
			'email' => 'clientes@nodriza.cl',
			'nombre' => 'Sistema de clientes Nodriza Spa'
		);

		$destinatarios = array();

		$destinatarios[] = array(
			'email' => $cliente['VentaCliente']['email'],
			'type' => 'to'
		);
		
		return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
	}


	public function cliente_dashboard()
	{
		$PageTitle = 'Dashboard';

		$cliente = $this->VentaCliente->find('first', array(
			'conditions' => array(
				'VentaCliente.id' => $this->Auth->user('id')
			),
			'contain' => array(
				'Direccion' => array(
					'Comuna'
				),
				'Prospecto' => array(
					'fields' => array(
						'Prospecto.id', 'Prospecto.estado_prospecto_id'
					),
					'Cotizacion' => array(
						'fields' => array(
							'Cotizacion.id', 'Cotizacion.nombre', 'Cotizacion.created', 'Cotizacion.total_bruto', 'Cotizacion.archivo'
						)
					)
				),
				'Venta' => array(
					'order' => array('Venta.fecha_venta' => 'DESC'),
					'fields' => array(
						'Venta.id', 'Venta.fecha_venta', 'Venta.total', 'Venta.venta_estado_id', 'Venta.picking_estado', 'Venta.referencia'
					),
					'VentaDetalle' => array(
						'fields' => array(
							'VentaDetalle.cantidad', 'VentaDetalle.cantidad_anulada'
						)
					),
					'VentaEstado' => array(
						'fields' => array(
							'VentaEstado.nombre', 'VentaEstado.venta_estado_categoria_id'
						),
						'VentaEstadoCategoria' => array(
							'fields' => array(
								'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo', 'VentaEstadoCategoria.venta', 'VentaEstadoCategoria.final'
							)
						)
					), 
					'Dte' => array(
						'fields' => array(
							'Dte.id', 'Dte.tipo_documento', 'Dte.invalidado', 'Dte.estado', 'Dte.pdf'
						)
					)
				)
			)
		));
		
		$cliente['Metricas'] = array(
			'total_comprado' => 0,
			'total_cotizado' => array_sum(Hash::extract($cliente['Prospecto'], '{n}[estado_prospecto_id=cotizacion].Cotizacion.{n}.total_bruto')),
			'cantidad_prospectos' => count($cliente['Prospecto']),
			'ultimas_ventas' => array()
		);
		
		# Total comprado
		foreach ($cliente['Venta'] as $iv => $v) {
			if ($v['VentaEstado']['VentaEstadoCategoria']['venta']) {
				$cliente['Metricas']['total_comprado'] = $cliente['Metricas']['total_comprado'] + $v['total'];
			}

			if ($iv < 5) {
				$cliente['Metricas']['ultimas_ventas'][$iv] = $v;
				
				if (!empty($v['Dte'])) {
					$cliente['Metricas']['ultimas_ventas'][$iv]['Dte'] = VentasController::obtener_dtes_pdf_venta($v['Dte']);
				}
			}
		}

		$cotizaciones = Hash::extract($cliente['Prospecto'], '{n}.Cotizacion.{n}');
		arsort($cotizaciones);
		$cotizaciones = array_slice($cotizaciones, 0, 5);

		$this->layout = 'private';

		BreadcrumbComponent::add('Dashboard', '/cliente');

		$this->set(compact('PageTitle', 'cliente', 'cotizaciones'));
	}

	public function cliente_login()
	{	
		if ($this->Cookie->check('Cliente.mantener_sesion')) {

			# Obtenemos al cliente
			$cliente = ClassRegistry::init('VentaCliente')->find('first', array(
				'conditions' => array(
					'VentaCliente.email' => trim($this->Cookie->read('Cliente.email')),
					'VentaCliente.activo' => 1
				)
			));

			# Obtenemos token y lo validamos
			$gettoken = ClassRegistry::init('Token')->find('first', array(
				'conditions' => array(
					'Token.venta_cliente_id' => $cliente['VentaCliente']['id']
				),
				'order' => array('Token.created' => 'DESC')
			));
			
			if (empty($gettoken)) {
				# creamos un token de acceso vía email
				$token = ClassRegistry::init('Token')->crear_token_cliente($cliente['VentaCliente']['id'])['token'];

			}else if (!ClassRegistry::init('Token')->validar_token($gettoken['Token']['token'], 'cliente')){
				# creamos un token de acceso vía email
				$token = ClassRegistry::init('Token')->crear_token_cliente($cliente['VentaCliente']['id'])['token'];

			}else{
				$token = $gettoken['Token']['token'];
			}

			if($this->Auth->login($cliente['VentaCliente'])) {

				$c = array(
					'VentaCliente' => $cliente['VentaCliente']
				);

				$c['VentaCliente']['ultimo_acceso'] = date('Y-m-d H:i:s');

				$this->Session->write('Auth.Cliente.token', $token);

				$this->VentaCliente->save($c);

				$this->Session->setFlash('Haz accedido correctamente al sistema.', null, array(), 'success');

				# Creamos coookie para mantener la sesión iniciada con 1 años de duración
				$this->Cookie->write('Cliente.mantener_sesion', 1, true, '1 year');
				$this->Cookie->write('Cliente.email', $cliente['VentaCliente']['email'], true, '1 year');

				$this->redirect($this->Auth->redirectUrl());

			}else{
				$this->Session->setFlash('No es posible ingresar al sistema. Recuerda que debes haber efectuado alguna compra en nuestra tienda para estar en los registros.', null, array(), 'warning');
				$this->redirect($this->Auth->logout());
			}

		}


		if ( $this->request->is('post') )
		{	

			$cliente = ClassRegistry::init('VentaCliente')->find('first', array(
				'conditions' => array(
					'VentaCliente.email' => trim($this->request->data['email']),
					'VentaCliente.activo' => 1
				)
			));

			if (!empty($cliente)) {
				
				if ($this->notificar_link_acceso($cliente['VentaCliente']['id'])) {
					$this->Session->setFlash('Notificación enviada con éxito.', null, array(), 'success');
					$this->redirect(array('action' => 'sended'));
				}else{
					$this->Session->setFlash('Ocurrió un error al enviar la notificación.', null, array(), 'warning');
					$this->redirect(array('action' => 'sendFailed'));
				}

				/*if ($this->Auth->login()) {
		            return $this->redirect($this->Auth->redirect());
		        } else {
		        	$this->Session->setFlash('Nombre de usuario y/o clave incorrectos.', null, array(), 'danger');
		        }*/
			}else{

				$causas = array(
					'Email erroneo',
					'Cuenta desactivada',
					'No tiene compras registradas'
				);

				$this->Session->setFlash('No es posible continuar.' . $this->crearAlertaUl($causas, 'Posibles errores'), null, array(), 'danger');
				$this->redirect(array('action' => 'login'));
			}
			
	    }

	    $PageTitle = 'Login';
	    $tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
	    	'Tienda.id', 'Tienda.nombre', 'Tienda.logo', 'Tienda.url'
	    ));

	    $this->set(compact('PageTitle', 'tienda'));
	}


	public function cliente_logout()
	{	
		$this->Cookie->delete('Cliente.mantener_sesion');
		$this->redirect($this->Auth->logout());
	}


	/**
	 * Autoriza y logea a un cliente dado su token de acceso válido
	 */
	public function cliente_authorization()
	{	
		if (!isset($this->request->query['access_token'])) {
			$this->Session->setFlash('El link de acceso no es válido o ya caducó. Intenta solicitando un link nuevo.', null, array(), 'danger');
			$this->redirect(array('action' => 'login'));
		}

		$token = $this->request->query['access_token'];

		# Validamos el token
		if (!ClassRegistry::init('Token')->validar_token($token, 'cliente')) {
			$this->Session->setFlash('El link de acceso no es válido o ya caducó. Intenta solicitando un link nuevo.', null, array(), 'danger');
			$this->redirect(array('action' => 'login'));
		}

		# El token es válido, obtenemos al cliente por su token y lo logeamos.
		$cliente = ClassRegistry::init('Token')->find('first', array(
			'conditions' => array(
				'Token.token' => $token
			),
			'contain' => array(
				'VentaCliente'
			)
		));

		if($this->Auth->login($cliente['VentaCliente'])) {

			$c = array(
				'VentaCliente' => $cliente['VentaCliente']
			);

			$c['VentaCliente']['ultimo_acceso'] = date('Y-m-d H:i:s');

			$this->Session->write('Auth.Cliente.token', $token);

			$this->VentaCliente->save($c);

			$this->Session->setFlash('Haz accedido correctamente al sistema.', null, array(), 'success');

			# Creamos coookie para mantener la sesión iniciada con 1 años de duración
			$this->Cookie->write('Cliente.mantener_sesion', 1, true, '1 year');
			$this->Cookie->write('Cliente.email', $cliente['VentaCliente']['email'], true, '1 year');

			$this->redirect($this->Auth->redirectUrl());
		}else{
			$this->Session->setFlash('No es posible ingresar al sistema. Recuerda que debes haber efectuado alguna compra en nuestra tienda para estar en los registros.', null, array(), 'warning');
			$this->redirect($this->Auth->logout());
		}


		
	}

	public function cliente_sended()
	{
		$PageTitle = 'Notificación enviada';

		$tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
	    	'Tienda.id', 'Tienda.nombre', 'Tienda.logo', 'Tienda.url'
	    ));

		$this->set(compact('PageTitle', 'tienda'));
	}

	public function cliente_sendFailed()
	{
		
	}


	public function cliente_quick_message()
	{	
		$error = '';
			
		$PageTitle = 'Auto atención';

		$tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
	    	'Tienda.id', 'Tienda.nombre', 'Tienda.logo', 'Tienda.url'
	    ));

		if (!isset($this->request->query['access_token'])
			|| !isset($this->request->query['tipo'])
			|| !isset($this->request->query['venta_id'])) {
			$error = 'No tienes permitido acceder a esta sección. Por favor ponte en contacto con nuestro equipo.';
			$this->set(compact('PageTitle', 'error', 'tienda'));
			return;
		}

		$token = $this->request->query['access_token'];
		$token_valido = false;

		# Validamos el token
		try {
			$token_valido = ClassRegistry::init('Token')->validar_token($token, 'cliente');
		} catch (Exception $e) {
			$error = 'La llave de acceso no es correcta. Por favor ponte en contacto con nuestro equipo.';
		}
		
		if (!$token_valido) {
			$error = 'El requerimiento ya fue enviado o se venció el plazo de 4 días para notificarlo. Por favor ponte en contacto con nuestro equipo.';
			$this->set(compact('PageTitle', 'error', 'tienda'));
			return;
		}

		# El token es válido, obtenemos al cliente por su token y lo logeamos.
		$cliente = ClassRegistry::init('Token')->find('first', array(
			'conditions' => array(
				'Token.token' => $token
			),
			'contain' => array(
				'VentaCliente'
			)
		));

		$mensaje = array(
			'venta_id' => $this->request->query['venta_id'],
			'venta_cliente_id' => $cliente['VentaCliente']['id'],
			'origen' => 'cliente'
		);

		switch ($this->request->query['tipo']) {
			case 'cancelar':
				$mensaje = array_replace_recursive($mensaje, array(
					'mensaje' => '(auto-atención) Cliente solicita cancelar la venta.'
				));
				break;
			case 'procesar':
				$mensaje = array_replace_recursive($mensaje, array(
					'mensaje' => '(auto-atención) Cliente solicita devolución del dinero del/los productos con stockout y que se le envien el/los productos con existencias.'
				));
				break;
			case 'cambio':
				$mensaje = array_replace_recursive($mensaje, array(
					'mensaje' => '(auto-atención) Cliente solicita cambio del/los productos con stockout, llamarlo y ofrecerle una alternativa.'
				));
				break;
		}

		App::uses('HttpSocket', 'Network/Http');
		$socket			= new HttpSocket();
		$request		= $socket->post(
			Router::url('/api/mensajes/add.json?token='.$token, true),
			$mensaje
		);

		$respuesta = json_decode($request->body(), true);

		if ($respuesta['response']['code'] != 200) {
			$error = 'Ocurrió un error al crear el mensaje. Por favor ponte en contacto con nuetro equipo.';
		}

		# Actualizamos el token indicando que esta caduco
		ClassRegistry::init('Token')->id = $cliente['Token']['id'];
		ClassRegistry::init('Token')->saveField('expires', date('Y-m-d H:i:s'));

		$this->set(compact('PageTitle', 'error', 'tienda', 'cliente'));
	}

}	
