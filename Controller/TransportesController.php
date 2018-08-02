<?php
App::uses('AppController', 'Controller');
class TransportesController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		$transportes	= $this->paginate();

		BreadcrumbComponent::add('Transportes');

		$this->set(compact('transportes'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Transporte->create();
			if ( $this->Transporte->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		BreadcrumbComponent::add('Transportes', '/transportes');
		BreadcrumbComponent::add('Agregar');
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Transporte->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Transporte->save($this->request->data) )
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
			$this->request->data	= $this->Transporte->find('first', array(
				'conditions'	=> array('Transporte.id' => $id)
			));
		}

		BreadcrumbComponent::add('Transportes', '/transportes');
		BreadcrumbComponent::add('Editar');
	}

	public function admin_delete($id = null)
	{
		$this->Transporte->id = $id;
		if ( ! $this->Transporte->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Transporte->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Transporte->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Transporte->_schema);
		$modelo			= $this->Transporte->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
