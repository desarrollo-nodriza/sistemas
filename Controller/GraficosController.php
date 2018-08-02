<?php
App::uses('AppController', 'Controller');
class GraficosController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		BreadcrumbComponent::add('Gráficos ');

		$graficos	= $this->paginate();
		$this->set(compact('graficos'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Grafico->create();

			$this->request->data['Grafico']['slug'] = strtolower(Inflector::slug($this->request->data['Grafico']['nombre']));
			if ( $this->Grafico->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Gráficos ', '/graficos');
		BreadcrumbComponent::add('Agregar ');

		$reportes	= $this->Grafico->Reporte->find('list');
		$this->set(compact('reportes'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Grafico->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			$this->request->data['Grafico']['slug'] = strtolower(Inflector::slug($this->request->data['Grafico']['nombre']));
			if ( $this->Grafico->save($this->request->data) )
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
			$this->request->data	= $this->Grafico->find('first', array(
				'conditions'	=> array('Grafico.id' => $id)
			));
		}

		BreadcrumbComponent::add('Gráficos ', '/graficos');
		BreadcrumbComponent::add('Editar ');

		$reportes	= $this->Grafico->Reporte->find('list');
		$this->set(compact('reportes'));
	}

	public function admin_delete($id = null)
	{
		$this->Grafico->id = $id;
		if ( ! $this->Grafico->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Grafico->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Grafico->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Grafico->_schema);
		$modelo			= $this->Grafico->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
