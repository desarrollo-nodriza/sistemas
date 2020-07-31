<?php
App::uses('AppController', 'Controller');
class DireccionesController extends AppController
{	

	/**
	 * Lista y filtra las las direcciones
	 * Endpoint :  /api/direcciones.json
	 */
    public function api_index() {

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

    	$qry = array(
    		'order' => array('Direccion.id' => 'desc'),
    		'contain' => array(
    			'Comuna',
    			'VentaCliente'
    		)
    	);

    	$paginacion = array(
        	'limit' => 0,
        	'offset' => 0,
        	'total' => 0
        );

    	if (isset($this->request->query['id'])) {
    		if (!empty($this->request->query['id'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'Direccion.id' => $this->request->query['id'])));
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

    	if (isset($this->request->query['cliente'])) {
    		if (!empty($this->request->query['cliente'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'Direccion.venta_cliente_id' => $this->request->query['cliente'])));
    		}
    	}
   
        $direcciones = $this->Direccion->find('all', $qry);

        if (isset($this->request->query['block'])) {
    		if ($this->request->query['block'] == 1) {
    			foreach ($direcciones as $ip => $direccion) {
    				
    				$v             =  new View();
					$v->autoRender = false;
					$v->output     = '';
					$v->layoutPath = '';
					$v->layout     = '';
					$v->set(compact('direccion'));	

					$direcciones[$ip]['Direccion']['block'] = $v->render('/Elements/direcciones/address-block');

    			}
    		}
    	}

    	$paginacion['total'] = count($direcciones);

        $this->set(array(
            'direcciones' => $direcciones,
            'paginacion' => $paginacion,
            '_serialize' => array('direcciones', 'paginacion')
        ));
    }


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


		if (empty($this->request->data['Direccion']['alias'])
			|| empty($this->request->data['Direccion']['calle'])
			|| empty($this->request->data['Direccion']['numero'])
			|| empty($this->request->data['Direccion']['comuna_id'])
			|| empty($this->request->data['Direccion']['venta_cliente_id']))
		{

			$response = array(
				'code' => 504,
				'created' => false,
				'message' => 'Complete los campos requeridos.'
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

		
		if ($this->Direccion->save($this->request->data)){

			$log[] = array(
				'Log' => array(
					'administrador' => 'Rest api',
					'modulo' => 'Direccion',
					'modulo_accion' => 'Creación: direccion id ' . $this->Direccion->id
				)
			);

			$direccion = $this->Direccion->find('first', array('conditions' => array('Direccion.id' => $this->Direccion->id), 'contain' => array('Comuna')));

			$v             =  new View();
			$v->autoRender = false;
			$v->output     = '';
			$v->layoutPath = '';
			$v->layout     = '';
			$v->set(compact('direccion'));	

			$direccion['Direccion']['block'] = $v->render('/Elements/direcciones/address-block');
			
			$v2             =  new View();
			$v2->autoRender = false;
			$v2->output     = '';
			$v2->layoutPath = '';
			$v2->layout     = '';
			$v2->set(compact('direccion'));
			$direccion['Direccion']['tr']    = $v2->render('/Elements/direcciones/address-tr');

			$resultado = array(
				'code' => 200,
				'created' => true,
				'direccion' => $direccion['Direccion']
			);
		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		$this->set(array(
			'response'   => $resultado,
			'_serialize' => array('response')
	    ));
	}


	public function api_edit($id) {

		# Vrificamos la existencia
		if (!$this->Direccion->exists($id)) {
			$response = array(
				'code'    => 404,
				'name' => 'error',
				'message' => 'dirección no econtrada'
			);
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

		$this->request->data['Direccion']['id'] = $id;
		
		if ($this->Direccion->save($this->request->data)){

			$log[] = array(
				'Log' => array(
					'administrador' => 'Rest api',
					'modulo'        => 'Direccion',
					'modulo_accion' => 'Edición: direccion id ' . $this->Direccion->id
				)
			);

			$direccion = $this->Direccion->find('first', array('conditions' => array('Direccion.id' => $this->Direccion->id), 'contain' => array('Comuna')));

			$v             =  new View();
			$v->autoRender = false;
			$v->output     = '';
			$v->layoutPath = '';
			$v->layout     = '';
			$v->set(compact('direccion'));	

			$direccion['Direccion']['block'] = $v->render('/Elements/direcciones/address-block');


			$v2             =  new View();
			$v2->autoRender = false;
			$v2->output     = '';
			$v2->layoutPath = '';
			$v2->layout     = '';
			$v2->set(compact('direccion'));
			$direccion['Direccion']['tr']    = $v2->render('/Elements/direcciones/address-tr');

			$resultado = array(
				'code' => 200,
				'updated' => true,
				'direccion' => $direccion['Direccion']
			);
		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		$this->set(array(
			'response'   => $resultado,
			'_serialize' => array('response')
	    ));
	}


	public function api_view($id) {
    	
    	$token = '';

    	if (!$this->Direccion->exists($id)) {
    		$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Direccion no existe'
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

		$direccion = $this->Direccion->find('first', array('conditions' => array('Direccion.id' => $id), 'contain' => array('Comuna', 'VentaCliente')));

		$v             =  new View();
		$v->autoRender = false;
		$v->output     = '';
		$v->layoutPath = '';
		$v->layout     = '';
		$v->set(compact('direccion'));	

		$direccion['Direccion']['block'] = $v->render('/Elements/direcciones/address-block');

		$this->set(array(
            'direccion' => $direccion,
            '_serialize' => array('direccion')
        ));
			
    }
}