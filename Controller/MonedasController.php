<?php
App::uses('AppController', 'Controller');
class MonedasController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		$monedas	= $this->paginate();

		$tipos = $this->Moneda->tipos;
			
		BreadcrumbComponent::add('Monedas ');
		$this->set(compact('monedas', 'tipos'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Moneda->create();
			if ( $this->Moneda->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$tipos = $this->Moneda->tipos;

		BreadcrumbComponent::add('Monedas ', '/monedas');
		BreadcrumbComponent::add('Agregar ');

		$this->set(compact('tipos'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Moneda->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Moneda->save($this->request->data) )
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
			$this->request->data	= $this->Moneda->find('first', array(
				'conditions'	=> array('Moneda.id' => $id)
			));
		}

		$tipos = $this->Moneda->tipos;

		BreadcrumbComponent::add('Monedas ', '/monedas');
		BreadcrumbComponent::add('Editar ');

		$this->set(compact('tipos'));
	}

	public function admin_delete($id = null)
	{
		$this->Moneda->id = $id;
		if ( ! $this->Moneda->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Moneda->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Moneda->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Moneda->_schema);
		$modelo			= $this->Moneda->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	/**
	 * Lista las monedas
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
    		'order' => array('Moneda.id' => 'desc')
    	);

    	$paginacion = array(
        	'limit' => 0,
        	'offset' => 0,
        	'total' => 0
        );

    	if (isset($this->request->query['id'])) {
    		if (!empty($this->request->query['id'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'Moneda.id' => $this->request->query['id'])));
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

    	if (isset($this->request->query['nombre'])) {
    		if (!empty($this->request->query['nombre'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'Moneda.nombre' => $this->request->query['nombre'])));
    		}
    	}
   
        $monedas = $this->Moneda->find('all', $qry);

    	$paginacion['total'] = count($monedas);

        $this->set(array(
            'monedas' => $monedas,
            'paginacion' => $paginacion,
            '_serialize' => array('monedas', 'paginacion')
        ));
    }


    /**
     * Ver moneda
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function api_view($id) {
    	
    	$token = '';

    	if (!$this->Moneda->exists($id)) {
    		$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Moneda no existe'
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

		$moneda = $this->Moneda->find('first', array('conditions' => array('Moneda.id' => $id)));

		$this->set(array(
            'moneda' => $moneda,
            '_serialize' => array('moneda')
        ));
			
    }
}
