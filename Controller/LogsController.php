<?php
App::uses('AppController', 'Controller');
class LogsController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		$logs	= $this->paginate();
		$this->set(compact('logs'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Log->create();
			if ( $this->Log->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		$administradores	= $this->Log->Administrador->find('list');
		$this->set(compact('administradores'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Log->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Log->save($this->request->data) )
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
			$this->request->data	= $this->Log->find('first', array(
				'conditions'	=> array('Log.id' => $id)
			));
		}
		$administradores	= $this->Log->Administrador->find('list');
		$this->set(compact('administradores'));
	}

	public function admin_delete($id = null)
	{
		$this->Log->id = $id;
		if ( ! $this->Log->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Log->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Log->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Log->_schema);
		$modelo			= $this->Log->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
