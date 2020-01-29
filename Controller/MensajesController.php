<?php
App::uses('AppController', 'Controller');

class MensajesController extends AppController
{	
	var $uses = array('Mensaje');

	/**
	 * [api_index description]
	 * @return [type] [description]
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
			'order' => array('Mensaje.created' => 'desc'),
			'contain' => array(
				'Administrador' => array(
					'fields' => array(
						'Administrador.nombre',
						'Administrador.email'
					)
				),
				'VentaCliente' => array(
					'fields' => array(
						'VentaCliente.nombre',
						'VentaCliente.apellido',
						'VentaCliente.email'
					)
				),
				'Venta' => array(
					'fields' => array(
						'Venta.id',
						'Venta.referencia',
						'Venta.tienda_id',
						'Venta.marketplace_id',
						'Venta.descuento',
						'Venta.costo_envio',
						'Venta.total',
						'Venta.fecha_venta',
						'Venta.direccion_entrega',
						'Venta.comuna_entrega',
						'Venta.nombre_receptor',
						'Venta.fono_receptor'
					),
					'VentaDetalle' => array(
						'fields' => array(
							'VentaDetalle.cantidad',
							'VentaDetalle.precio_bruto'
						),
						'VentaDetalleProducto' => array(
							'fields' => array(
								'VentaDetalleProducto.nombre',
								'VentaDetalleProducto.codigo_proveedor'
							)
						)
					)
				),
				'VentaDetalleProducto' => array(
					'fields' => array(
						'VentaDetalleProducto.nombre',
						'VentaDetalleProducto.codigo_proveedor'
					)
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
				$qry = array_replace_recursive($qry, array('conditions' => array( 'Mensaje.id' => $this->request->query['id'])));
			}
		}

		if (isset($this->request->query['parent_id'])) {
			if (!empty($this->request->query['parent_id'])) {
				$qry = array_replace_recursive($qry, array('conditions' => array( 'Mensaje.parent_id' => $this->request->query['parent_id'])));
			}
		}

		if (isset($this->request->query['venta_cliente_id'])) {
			if (!empty($this->request->query['venta_cliente_id'])) {
				$qry = array_replace_recursive($qry, array('conditions' => array( 'Mensaje.venta_cliente_id' => $this->request->query['venta_cliente_id'])));
			}
		}

		if (isset($this->request->query['administrador_id'])) {
			if (!empty($this->request->query['administrador_id'])) {
				$qry = array_replace_recursive($qry, array('conditions' => array( 'Mensaje.administrador_id' => $this->request->query['administrador_id'])));
			}
		}

		if (isset($this->request->query['venta_id'])) {
			if (!empty($this->request->query['venta_id'])) {
				$qry = array_replace_recursive($qry, array('conditions' => array( 'Mensaje.venta_id' => $this->request->query['venta_id'])));
			}
		}

		if (isset($this->request->query['venta_detalle_producto_id'])) {
			if (!empty($this->request->query['venta_detalle_producto_id'])) {
				$qry = array_replace_recursive($qry, array('conditions' => array( 'Mensaje.venta_detalle_producto_id' => $this->request->query['venta_detalle_producto_id'])));
			}
		}

		if (isset($this->request->query['privado'])) {
			if (!empty($this->request->query['privado'])) {
				$qry = array_replace_recursive($qry, array('conditions' => array( 'Mensaje.privado' => $this->request->query['privado'])));
			}
		}

		if (isset($this->request->query['origen'])) {
			if (!empty($this->request->query['origen'])) {
				$qry = array_replace_recursive($qry, array('conditions' => array( 'Mensaje.origen' => $this->request->query['origen'])));
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

		$mensajes = $this->Mensaje->find('all', $qry);

		$this->set(array(
		    'mensajes' => $mensajes,
		    '_serialize' => array('mensajes')
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

		$resultado = array(
			'code' => 201,
			'created' => false,
			'updated' => false
		);

		$log = array();


		$log[] = array(
			'Log' => array(
				'administrador' => 'Rest api',
				'modulo' => 'Mensajes',
				'modulo_accion' => json_encode($this->request->data)
			)
		);

		$saveData = array('Mensaje' => array());

		foreach ($this->request->data as $indice => $val) {
			$saveData['Mensaje'][$indice] = $val;
		}

		if (empty($saveData['Mensaje'])) {
			$response = array(
				'code'    => 510, 
				'name' => 'error',
				'message' => 'Bad request'
			);

			throw new CakeException($response);
		}

		$this->Mensaje->create();
		if ($this->Mensaje->save($saveData)) {

			$this->notificar($this->Mensaje->id);
			
			$mensaje = $this->Mensaje->find('first', array('conditions' => array('id' => $this->Mensaje->id)));

			$resultado = array(
				'code' => 200,
				'created' => true,
				'mensaje' => $mensaje['Mensaje']
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
     * [api_view description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function api_view($id)
    {
    	$token = '';

    	if (!$this->Mensaje->exists($id)) {
    		$response = array(
				'code'    => 404,
				'name' => 'error',
				'message' => 'Mensaje no encontrado'
			);

			throw new CakeException($response);
    	}

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

		$mensaje = $this->Mensaje->find('first', array(
			'conditions' => array(
				'Mensaje.id' => $id
			),
			'contain' => array(
				'Administrador' => array(
					'fields' => array(
						'Administrador.nombre',
						'Administrador.email'
					)
				),
				'VentaCliente' => array(
					'fields' => array(
						'VentaCliente.nombre',
						'VentaCliente.apellido',
						'VentaCliente.email'
					)
				),
				'Venta' => array(
					'VentaDetalle' => array(
						'fields' => array(
							'VentaDetalle.cantidad',
							'VentaDetalle.precio_bruto'
						),
						'VentaDetalleProducto' => array(
							'fields' => array(
								'VentaDetalleProducto.nombre',
								'VentaDetalleProducto.codigo_proveedor'
							)
						)
					)
				),
				'VentaDetalleProducto' => array(
					'fields' => array(
						'VentaDetalleProducto.nombre',
						'VentaDetalleProducto.codigo_proveedor'
					)
				)
			)
		));

        $this->set(array(
            'mensaje' => $mensaje,
            '_serialize' => array('mensaje')
        ));
    }


    /**
     * [api_delete description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function api_delete($id)
    {	

    	if (!$this->Mensaje->exists($id)) {
    		$response = array(
				'code'    => 404,
				'name' => 'error',
				'message' => 'Mensaje no encontrado'
			);

			throw new CakeException($response);
    	}

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

		$resultado = array(
			'code' => 201,
			'deleted' => false
		);

		$log = array();

		$log[] = array(
			'Log' => array(
				'administrador' => 'Rest api',
				'modulo' => 'Mensajes',
				'modulo_accion' => json_encode($this->request->data)
			)
		);

		$this->Mensaje->id = $id;
		if ( $this->Mensaje->delete() )
		{
			$resultado['code'] = 200;
			$resultado['deleted'] = true;
		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		$this->set(array(
			'response'   => $resultado,
			'_serialize' => array('response')
	    ));

    }


    public function notificar($id)
    {
    	if (!$this->Mensaje->exists($id)) {
    		return false;
    	}

		$tienda = ClassRegistry::init('Tienda')->tienda_principal(array('mandrill_apikey', 'logo', 'id', 'nombre', 'direccion', 'url'));
		
		if (empty($tienda['Tienda']['mandrill_apikey'])) {
			$response = array(
				'code'    => 511, 
				'message' => 'Tienda no configurada para enviar emails.'
			);

			throw new CakeException($response);
		}

		$mensaje = $this->Mensaje->find('first', array(
			'conditions' => array(
				'Mensaje.id' => $id
			),
			'contain' => array(
				'VentaCliente',
				'Administrador'
			)
		));

		$emisor = '';
		$destinatario = '';
		$nombre_destinatario = '';
		$plantilla = '';
		$token = '';

		$asunto = '[Nuevo mensaje #<?=$id;?>] Se ha creado un nuevo mensaje en '. $tienda['Tienda']['nombre'] .'.';

		if (Configure::read('debug') > 0) {
			$asunto = '[DEV] Se ha creado un nuevo mensaje en '. $tienda['Tienda']['nombre'] .'.';
		}

		switch ($mensaje['Mensaje']['origen']) {
			case 'cliente':

				/**
				 * Si no viene dado el destinatario, se obtiene segun configurcación
				 */
				
				$destinatarios = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('ventas', true);

				$emisor              = $mensaje['VentaCliente']['email'];
				$nombre_destinatario = '';
				$plantilla           = 'notificar_mensaje_empleado';

				$asunto = '[Nuevo mensaje] Se ha creado un nuevo mensaje para la venta #' . $mensaje['Mensaje']['venta_id']. '.';

				if (Configure::read('debug') > 0) {
					$asunto = '[DEV] Se ha creado un nuevo mensaje para la venta #' . $mensaje['Mensaje']['venta_id']. '.';
				}

				break;
			
			case 'empleado':
				$emisor              = $mensaje['Administrador']['email'];
				$destinatario        = $mensaje['VentaCliente']['email'];
				$nombre_destinatario = $mensaje['Administrador']['nombre'];
				$plantilla           = 'notificar_mensaje_cliente';

				$destinatarios[] = array(
					'email' => $destinatario,
					'name' => $nombre_destinatario
				);

				$token = ClassRegistry::init('VentaCliente')->crear_token($mensaje['Mensaje']['venta_cliente_id'], '', 720);

				$token = $token['token'];

				break;
		}

		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'Mensajes' . DS . 'emails';
		$this->View->layoutPath		= 'Correos' . DS . 'html';
		
		$url = obtener_url_base();

		$this->View->set(compact('mensaje', 'url', 'tienda', 'nombre_destinatario', 'token'));
		$html						= $this->View->render($plantilla);
		
		$mandrill_apikey = $tienda['Tienda']['mandrill_apikey'];

		if (empty($mandrill_apikey)) {
			return false;
		}

		$mandrill = $this->Components->load('Mandrill');

		$mandrill->conectar($mandrill_apikey);
		
		$remitente = array(
			'email' => 'no-replay@nodriza.cl',
			'nombre' => $tienda['Tienda']['nombre'] . ' by Nodriza'
		);

		return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
    }


    public function admin_notificar($id)
    {	
    	prx($this->notificar($id));
    }


    public function cliente_hilo($venta_id)
    {	

    	$venta = ClassRegistry::init('Venta')->obtener_venta_por_id($venta_id);
    	
    	$mensaje_id = $this->request->query['message'];

    	prx($venta);
    }

    public function cliente_guardar_mensaje()
	{
		if (!$this->request->is('post')) {
			$this->Session->setFlash('Método no permitido. Intenta nuevamente.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}
		
		if (!isset($this->request->data['Mensaje']['parent_id'])) {
			$this->request->data['Mensaje']['parent_id'] = null;
		}

		App::uses('HttpSocket', 'Network/Http');
		$socket			= new HttpSocket();
		$request		= $socket->post(
			Router::url('/api/mensajes/add.json?token=' . $this->request->data['Mensaje']['access_token'], true),
			array(
				'venta_id'         => $this->request->data['Mensaje']['venta_id'],
				'venta_cliente_id' => $this->request->data['Mensaje']['venta_cliente_id'],
				'origen'           => 'cliente',
				'autor'            => $this->request->data['Mensaje']['autor'],
				'parent_id'        => $this->request->data['Mensaje']['parent_id'],
				'mensaje'          => $this->request->data['Mensaje']['mensaje']
			)
		);

		$respuesta = json_decode($request->body(), true);
		
		if ($respuesta['response']['code'] == 200) {
			$this->Session->setFlash('Mensaje guardado con éxito. Pronto un miembro de nuestro equipo te responderá.', null, array(), 'success');
		}else{
			$this->Session->setFlash('No fue posible guardar el mensaje. Código de error: ' . $respuesta['code'], null, array(), 'success');
		}

		$this->redirect($this->referer('/', true));

	}
}