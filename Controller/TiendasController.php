<?php
App::uses('AppController', 'Controller');
class TiendasController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		BreadcrumbComponent::add('Tiendas ');

		$tiendas	= $this->paginate();
		$this->set(compact('tiendas'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Tienda->create();
			if ( $this->Tienda->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Tiendas ', '/tiendas');
		BreadcrumbComponent::add('Agregar ');
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Tienda->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Tienda->save($this->request->data) )
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
			$this->request->data	= $this->Tienda->find('first', array(
				'conditions'	=> array('Tienda.id' => $id)
			));
		}

		BreadcrumbComponent::add('Tiendas ', '/tiendas');
		BreadcrumbComponent::add('Editar ');
	}

	public function admin_delete($id = null)
	{
		$this->Tienda->id = $id;
		if ( ! $this->Tienda->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Tienda->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Tienda->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Tienda->_schema);
		$modelo			= $this->Tienda->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	/**
	 * [api_obtener_tiendas description]
	 * @return [type] [description]
	 */
	public function api_obtener_tiendas()
	{	
		# Sólo método Get
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

		$tiendas = $this->Tienda->find('list', array('conditions' => array('activo' => 1)));

		$this->set(array(
            'response' => $tiendas,
            '_serialize' => array('response', 'httpCode')
        ));

	}
}
