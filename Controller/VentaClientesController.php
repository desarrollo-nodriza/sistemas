<?php
App::uses('AppController', 'Controller');
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
		else
		{
			$this->request->data	= $this->VentaCliente->find('first', array(
				'conditions'	=> array('VentaCliente.id' => $id)
			));
		}
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
		$datos			= $this->VentaCliente->find('all');
		$campos			= array_keys($this->VentaCliente->_schema);
		$modelo			= $this->VentaCliente->alias;
		
		$this->set(compact('datos', 'campos', 'modelo'));
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
}
