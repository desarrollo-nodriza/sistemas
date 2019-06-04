<?
App::uses('AppController', 'Controller');
 
class ClientesController extends AppController {
 
    public $name = 'Clientes';    
    public $uses = array('Cliente');

    public function admin_index() 
    {   
    	$paginate = array(); 
    	$conditions = array();
    	$total = 0;
    	$totalMostrados = 0;
    	$categorias = array();

    	$textoBuscar = null;

        $backurl = array(
            'action' => 'index'
        );

        $backurl = array_replace_recursive($backurl, $this->request->params['action']);
        
        $this->Session->write($this->request->params['controller'], $backurl);

		// Filtrado de clientes por formulario
		if ( $this->request->is('post') ) {

			if ( ! empty($this->request->data['Filtro']['findby']) && empty($this->request->data['Filtro']['nombre_buscar']) ) {
				$this->Session->setFlash('Ingrese nombre o referencia del producto' , null, array(), 'danger');
				$this->redirect(array('action' => 'index'));
			}

			if ( ! empty($this->request->data['Filtro']['findby']) && ! empty($this->request->data['Filtro']['nombre_buscar']) ) {
				$this->redirect(array('controller' => 'clientes', 'action' => 'index', 'findby' => $this->request->data['Filtro']['findby'], 'nombre_buscar' => $this->request->data['Filtro']['nombre_buscar']));
			}
		}
			

		// Opciones de paginación
		$paginate = array_replace_recursive(array(
			'limit' => 10,
			'fields' => array(),
			'joins' => array(),
			'contain' => array(),
			'conditions' => array()
		));

		// Cambiamos la configuración de la base de datos
		$this->cambiarConfigDB($this->tiendaConf($this->Session->read('Tienda.id')));

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
							'Cliente.email' => trim($this->request->params['named']['nombre_buscar'])
						)
					));
					break;
				
				case 'nombre':
					$paginate		= array_replace_recursive($paginate, array(
						'conditions'	=> array(
							'Cliente.firstname LIKE "%' . trim($this->request->params['named']['nombre_buscar']) . '%"'
						)
					));
					break;
			}
			// Texto ingresado en el campo buscar
			$textoBuscar = $this->request->params['named']['nombre_buscar'];
			
		}else if ( ! empty($this->request->params['named']['findby'])) {
			$this->Session->setFlash('No se aceptan campos vacios.' ,  null, array(), 'danger');
		}

		// Total de registros de la tienda
		$total 		= $this->Cliente->find('count', array(
			'joins' => array(),
			'conditions' => array()
		));


		$this->paginate = $paginate;

		$clientes	= $this->paginate();
		$totalMostrados = count($clientes);

		if (empty($clientes)) {
			$this->Session->setFlash(sprintf('No se encontraron resultados para %s', $this->request->params['named']['nombre_buscar']) , null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		BreadcrumbComponent::add('Clientes');
		$this->set(compact('clientes', 'total', 'totalMostrados', 'textoBuscar'));
    }

    public function admin_view ($id = '') {
    	
    }

    public function admin_clientes_por_tienda ($tienda = '', $palabra = '') {
    	if (empty($tienda) || empty($palabra)) {
    		echo json_encode(array('0' => array('value' => '', 'label' => 'Ingrese email, nombre o apellido')));
    		exit;
    	}

    	$this->cambiarConfigDB($this->tiendaConf($tienda));

    	$clientes = $this->Cliente->find('all', array(
    		'conditions' => array(
    			'Cliente.email LIKE' => '%' . $palabra . '%',
                'Cliente.id_default_group' => 3
    			)
    		)
    	);
    	
    	if (empty($clientes)) {
    		echo json_encode(array('0' => array('id' => '', 'value' => 'No se encontraron coincidencias')));
    		exit;
    	}
    
    	foreach ($clientes as $index => $cliente) {
    		$arrayClientes[$index]['id'] = $cliente['Cliente']['id_customer'];
			$arrayClientes[$index]['value'] = sprintf('%s', $cliente['Cliente']['email']);
    	}

    	echo json_encode($arrayClientes);
    	exit;
    }

    public function admin_cliente_por_tienda ($tienda = '', $idCliente = '') {
    	if (empty($tienda) || empty($idCliente)) {
    		echo json_encode(array('0' => array('error' => 'Campos vacios')));
    		exit;
    	}

    	$tiendaR = ClassRegistry::init('Tienda')->find('first', array(
    		'conditions' => array(
    			'Tienda.id' => $tienda,
    			'Tienda.activo' => 1
    			)
    		));

    	if (empty($tiendaR)) {
    		echo json_encode(array('0' => array('error' => 'No se encontró la tienda')));
    		exit;
    	}

    	// Cambiamos la configuración de la base de datos
		$this->cambiarConfigDB($tiendaR['Tienda']['configuracion']);

    	$cliente = $this->Cliente->find('first', array(
    		'contain' => array(
    			'Clientedireccion' => array('Paise' => array('Lang'), 'Region')
    		),
    		'conditions' => array(
    			'Cliente.id_customer' => $idCliente,
    			'Cliente.id_default_group' => 3
    			)
    		));
    	
    	if (empty($cliente)) {
    		echo json_encode(array('0' => array('error' => 'No se encontró el cliente')));
    		exit;
    	}

    	echo json_encode($cliente);
    	exit;
    }

    public function admin_exportar()
    {
        # Aumentamos el tiempo máxmimo de ejecución para evitar caídas
        set_time_limit(600);
        ini_set('memory_limit', -1);
        
        $this->verificarTienda();

        $datos          = $this->Cliente->find('all', array(

            'recursive'             => -1

        ));

        
        $campos         = array_keys($this->Cliente->_schema);

        $modelo         = $this->Cliente->alias;



        $this->set(compact('datos', 'campos', 'modelo'));

    }
    
}