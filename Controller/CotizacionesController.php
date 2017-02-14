<?php
App::uses('AppController', 'Controller');
class CotizacionesController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		$cotizaciones	= $this->paginate();
		BreadcrumbComponent::add('Cotizaciones ');
		$this->set(compact('cotizaciones'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Cotizacion->create();
			if ( $this->Cotizacion->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		$monedas	= $this->Cotizacion->Moneda->find('list');
		$estadoCotizaciones	= $this->Cotizacion->EstadoCotizacion->find('list');
		$prospectos	= $this->Cotizacion->Prospecto->find('list');
		$validezFechas	= $this->Cotizacion->ValidezFecha->find('list');
		BreadcrumbComponent::add('Cotizaciones ', '/cotizaciones');
		BreadcrumbComponent::add('Agregar ');
		$this->set(compact('monedas', 'estadoCotizaciones', 'prospectos', 'validezFechas'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Cotizacion->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Cotizacion->save($this->request->data) )
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
			$this->request->data	= $this->Cotizacion->find('first', array(
				'conditions'	=> array('Cotizacion.id' => $id)
			));
		}
		$monedas	= $this->Cotizacion->Moneda->find('list');
		$estadoCotizaciones	= $this->Cotizacion->EstadoCotizacion->find('list');
		$prospectos	= $this->Cotizacion->Prospecto->find('list');
		$validezFechas	= $this->Cotizacion->ValidezFecha->find('list');
		BreadcrumbComponent::add('Cotizaciones ', '/cotizaciones');
		BreadcrumbComponent::add('Editar ');
		$this->set(compact('monedas', 'estadoCotizaciones', 'prospectos', 'validezFechas'));
	}

	public function admin_delete($id = null)
	{
		$this->Cotizacion->id = $id;
		if ( ! $this->Cotizacion->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Cotizacion->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Cotizacion->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Cotizacion->_schema);
		$modelo			= $this->Cotizacion->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
