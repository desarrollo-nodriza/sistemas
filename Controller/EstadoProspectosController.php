<?php
App::uses('AppController', 'Controller');
class EstadoProspectosController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		BreadcrumbComponent::add('Estados Prospectos ');
		$estadoProspectos	= $this->paginate();
		$this->set(compact('estadoProspectos'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->EstadoProspecto->create();
			if ( $this->EstadoProspecto->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Estados Prospectos ', '/estadoprospectos');
		BreadcrumbComponent::add('Agregar ');
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->EstadoProspecto->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->EstadoProspecto->save($this->request->data) )
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
			$this->request->data	= $this->EstadoProspecto->find('first', array(
				'conditions'	=> array('EstadoProspecto.id' => $id)
			));
		}

		BreadcrumbComponent::add('Estados Prospectos ', '/estadoprospectos');
		BreadcrumbComponent::add('Editar ');
	}

	public function admin_delete($id = null)
	{
		$this->EstadoProspecto->id = $id;
		if ( ! $this->EstadoProspecto->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->EstadoProspecto->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->EstadoProspecto->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->EstadoProspecto->_schema);
		$modelo			= $this->EstadoProspecto->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
