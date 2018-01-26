<?php
App::uses('AppController', 'Controller');
class ValidezFechasController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		$validezFechas	= $this->paginate();
		$this->set(compact('validezFechas'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->ValidezFecha->create();
			if ( $this->ValidezFecha->save($this->request->data) )
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
		if ( ! $this->ValidezFecha->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->ValidezFecha->save($this->request->data) )
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
			$this->request->data	= $this->ValidezFecha->find('first', array(
				'conditions'	=> array('ValidezFecha.id' => $id)
			));
		}
	}

	public function admin_delete($id = null)
	{
		$this->ValidezFecha->id = $id;
		if ( ! $this->ValidezFecha->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->ValidezFecha->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->ValidezFecha->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->ValidezFecha->_schema);
		$modelo			= $this->ValidezFecha->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
