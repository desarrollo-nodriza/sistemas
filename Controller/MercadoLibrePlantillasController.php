<?php
App::uses('AppController', 'Controller');
class MercadoLibrePlantillasController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);

		BreadcrumbComponent::add('Mercado Libre Plantillas ');

		$mercadoLibrePlantillas	= $this->paginate();
		$this->set(compact('mercadoLibrePlantillas'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{	
			$this->MercadoLibrePlantilla->create();
			if ( $this->MercadoLibrePlantilla->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Mercado Libre Plantillas ', '/mercadoLibrePlantillas');
		BreadcrumbComponent::add('Agregar ');
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->MercadoLibrePlantilla->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->MercadoLibrePlantilla->save($this->request->data) )
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
			$this->request->data	= $this->MercadoLibrePlantilla->find('first', array(
				'conditions'	=> array('MercadoLibrePlantilla.id' => $id)
			));
		}

		BreadcrumbComponent::add('Mercado Libre Plantillas ', '/mercadoLibrePlantillas');
		BreadcrumbComponent::add('Editar ');
	}

	public function admin_delete($id = null)
	{
		$this->MercadoLibrePlantilla->id = $id;
		if ( ! $this->MercadoLibrePlantilla->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->MercadoLibrePlantilla->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->MercadoLibrePlantilla->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->MercadoLibrePlantilla->_schema);
		$modelo			= $this->MercadoLibrePlantilla->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
