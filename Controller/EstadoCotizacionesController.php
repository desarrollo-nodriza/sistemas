<?php
App::uses('AppController', 'Controller');
class EstadoCotizacionesController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		BreadcrumbComponent::add('Estados Cotizaciones ');
		$estadoCotizaciones	= $this->paginate();
		$this->set(compact('estadoCotizaciones'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->EstadoCotizacion->create();
			if ( $this->EstadoCotizacion->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Estados Cotizaciones ', '/estadocotizaciones');
		BreadcrumbComponent::add('Agregar ');
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->EstadoCotizacion->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->EstadoCotizacion->save($this->request->data) )
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
			$this->request->data	= $this->EstadoCotizacion->find('first', array(
				'conditions'	=> array('EstadoCotizacion.id' => $id)
			));
		}
		BreadcrumbComponent::add('Estados Cotizaciones ', '/estadocotizaciones');
		BreadcrumbComponent::add('Editar ');
	}

	public function admin_delete($id = null)
	{
		$this->EstadoCotizacion->id = $id;
		if ( ! $this->EstadoCotizacion->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->EstadoCotizacion->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->EstadoCotizacion->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->EstadoCotizacion->_schema);
		$modelo			= $this->EstadoCotizacion->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
