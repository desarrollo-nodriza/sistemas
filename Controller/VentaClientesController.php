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
}
