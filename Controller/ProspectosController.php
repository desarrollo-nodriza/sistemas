<?php
App::uses('AppController', 'Controller');
class ProspectosController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		$prospectos	= $this->paginate();

		BreadcrumbComponent::add('Prospectos ');
		$this->set(compact('prospectos'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{	prx($this->request->data);
			$this->Prospecto->create();
			if ( $this->Prospecto->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		$estadoProspectos	= $this->Prospecto->EstadoProspecto->find('list', array('conditions' => array('EstadoProspecto.activo' => 1)));
		$monedas	= $this->Prospecto->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));
		$origenes	= $this->Prospecto->Origen->find('list', array('conditions' => array('Origen.activo' => 1)));
		$tiendas	= $this->Prospecto->Tienda->find('list', array('conditions' => array('Tienda.activo' => 1)));

		BreadcrumbComponent::add('Prospectos ', '/prospectos');
		BreadcrumbComponent::add('Agregar ');
		$this->set(compact('estadoProspectos', 'monedas', 'origenes', 'tiendas'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Prospecto->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Prospecto->save($this->request->data) )
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
			$this->request->data	= $this->Prospecto->find('first', array(
				'conditions'	=> array('Prospecto.id' => $id)
			));
		}
		$estadoProspectos	= $this->Prospecto->EstadoProspecto->find('list', array('conditions' => array('EstadoProspecto.activo' => 1)));
		$monedas	= $this->Prospecto->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));
		$origenes	= $this->Prospecto->Origen->find('list', array('conditions' => array('Origen.activo' => 1)));
		$tiendas	= $this->Prospecto->Tienda->find('list', array('conditions' => array('Tienda.activo' => 1)));
		BreadcrumbComponent::add('Prospectos ', '/prospectos');
		BreadcrumbComponent::add('Editar ');
		$this->set(compact('estadoProspectos', 'monedas', 'origenes', 'tiendas'));
	}

	public function admin_delete($id = null)
	{
		$this->Prospecto->id = $id;
		if ( ! $this->Prospecto->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Prospecto->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Prospecto->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Prospecto->_schema);
		$modelo			= $this->Prospecto->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
