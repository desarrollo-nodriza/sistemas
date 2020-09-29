<?php
App::uses('AppController', 'Controller');
class ComunasController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		$comunas	= $this->paginate();
		$this->set(compact('comunas'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Comuna->create();
			if ( $this->Comuna->save($this->request->data) )
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
		if ( ! $this->Comuna->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Comuna->save($this->request->data) )
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
			$this->request->data	= $this->Comuna->find('first', array(
				'conditions'	=> array('Email.id' => $id)
			));
		}
	}

	public function admin_delete($id = null)
	{
		$this->Comuna->id = $id;
		if ( ! $this->Comuna->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Comuna->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Comuna->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Comuna->_schema);
		$modelo			= $this->Comuna->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
    }
    

    /**
	 * Obtiene las comunas registradas
	 * @return mixed
	 */
	public function api_obtener_comunas()
	{
		# Sólo método get
		if (!$this->request->is('get')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only GET request allow'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

        $opts = array();

        # filtro nombre
        if( isset($this->request->query['nombre']) )
        {
            $opts = array_replace_recursive( $opts, array(
                'Comuna.nombre' => $this->request->query['nombre']
            ));
        }

        # filtro codigo starken
        if( isset($this->request->query['cod_starken']) )
        {
            $opts = array_replace_recursive( $opts, array(
                'Comuna.cod_starken' => $this->request->query['cod_starken']
            ));
        }

		$comunas = $this->Comuna->find('list', $opts);

		$this->set(array(
            'response' => $comunas,
            '_serialize' => array('response')
        ));

	}


	public function api_agregar_alias($id)
	{
		# Sólo método post
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only POST request allow'
			);

			throw new CakeException($response);
		}

		# No existe comuna
		if (!$this->Comuna->exists($id)){
			$response = array(
				'code'    => 404, 
				'message' => 'Commune not found'
			);

			throw new CakeException($response);
		}

		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

		
	}
}