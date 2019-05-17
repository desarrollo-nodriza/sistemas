<?php
App::uses('AppController', 'Controller');
class VentaEstadosController extends AppController
{

	public function admin_index () {

		$this->paginate = array(
			'recursive' => 0,
			'contain' => array(
				'VentaEstadoCategoria'
			),
			'sort' => 'VentaEstado.nombre',
			'direction' => 'ASC'
		);

		$ventaEstados = $this->paginate();

		BreadcrumbComponent::add('Estados de Ventas');

		$this->set(compact('ventaEstados'));

	}

	public function admin_edit($id = null)
	{
		if ( ! $this->VentaEstado->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{

			if ($this->request->data['VentaEstado']['preparacion']) {
				$estados = $this->VentaEstado->find('all', array(
					'conditions' => array(
						'preparacion' => 1,
						'canal' => $this->request->data['VentaEstado']['canal']
					),
					'fields' => array(
						'id', 'preparacion'
					)
				));

				foreach ($estados as $i => $e) {
					$estados[$i]['VentaEstado']['preparacion'] = false;
				}
				
				$this->VentaEstado->saveMany($estados);	
			}

			if ( $this->VentaEstado->save($this->request->data) )
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
			$this->request->data = $this->VentaEstado->find(
				'first',
				array(
					'conditions' => array(
						'VentaEstado.id' => $id
					)
				)
			);
		}

		$ventaEstadoCategorias = $this->VentaEstado->VentaEstadoCategoria->find(
			'list',
			array(
				'conditions' => array(
					'VentaEstadoCategoria.activo' => 1
				),
				'order' => 'VentaEstadoCategoria.nombre ASC'
			)
		);

		$canales = ClassRegistry::init('MarketplaceTipo')->find('list');
		
		BreadcrumbComponent::add('Estados de Ventas');
		BreadcrumbComponent::add('Editar Estado de Ventas');

		$this->set(compact('ventaEstadoCategorias', 'canales'));

	}

	public function admin_activar($id = null) {

		if ( ! $this->VentaEstado->exists($id) ){
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['VentaEstado']['id'] = $id;
			$this->request->data['VentaEstado']['activo'] = 1;

			if ( $this->VentaEstado->save($this->request->data) ) {
				$this->Session->setFlash('Registro activado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}

	public function admin_desactivar($id = null) {

		if ( ! $this->VentaEstado->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['VentaEstado']['id'] = $id;
			$this->request->data['VentaEstado']['activo'] = 0;

			if ( $this->VentaEstado->save($this->request->data) ) {
				$this->Session->setFlash('Registro desactivado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}

}