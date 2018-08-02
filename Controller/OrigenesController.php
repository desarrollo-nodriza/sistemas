<?php
App::uses('AppController', 'Controller');
class OrigenesController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		$origenes	= $this->paginate();
		BreadcrumbComponent::add('Origenes ');
		$this->set(compact('origenes'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Origen->create();
			if ( $this->Origen->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Origenes ', '/origenes');
		BreadcrumbComponent::add('Agregar ');
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Origen->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Origen->save($this->request->data) )
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
			$this->request->data	= $this->Origen->find('first', array(
				'conditions'	=> array('Origen.id' => $id)
			));
		}

		BreadcrumbComponent::add('Origenes ', '/origenes');
		BreadcrumbComponent::add('Editar ');
	}

	public function admin_delete($id = null)
	{
		$this->Origen->id = $id;
		if ( ! $this->Origen->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Origen->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Origen->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Origen->_schema);
		$modelo			= $this->Origen->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
