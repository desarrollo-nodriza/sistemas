<?php
App::uses('AppController', 'Controller');
class MarketplacesController extends AppController
{

	public function admin_index()
	{

		$this->paginate		= array(
			'recursive'			=> 0,
			'sort' => 'Marketplace.nombre',
			'direction' => 'ASC'
		);
		$marketplaces	= $this->paginate();

		BreadcrumbComponent::add('Marketplaces');

		$this->set(compact('marketplaces'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Marketplace->create();
			if ( $this->Marketplace->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$marketplaceTipos = $this->Marketplace->MarketplaceTipo->find(
			'list',
			array(
				'conditions' => array(
					'MarketplaceTipo.activo' => 1
				),
				'order' => 'MarketplaceTipo.nombre ASC'
			)
		);

		$tiendas = $this->Marketplace->Tienda->find(
			'list',
			array(
				'conditions' => array(
					'Tienda.activo' => 1
				),
				'order' => 'Tienda.nombre ASC'
			)
		);

		BreadcrumbComponent::add('Marketplaces');
		BreadcrumbComponent::add('Nuevo Marketplace');

		$this->set(compact('marketplaceTipos', 'tiendas'));

	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Marketplace->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{

			if ( $this->Marketplace->save($this->request->data) )
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
			$this->request->data = $this->Marketplace->find(
				'first',
				array(
					'conditions' => array(
						'Marketplace.id' => $id
					)
				)
			);
		}

		$marketplaceTipos = $this->Marketplace->MarketplaceTipo->find(
			'list',
			array(
				'conditions' => array(
					'MarketplaceTipo.activo' => 1
				),
				'order' => 'MarketplaceTipo.nombre ASC'
			)
		);

		$tiendas = $this->Marketplace->Tienda->find(
			'list',
			array(
				'conditions' => array(
					'Tienda.activo' => 1
				),
				'order' => 'Tienda.nombre ASC'
			)
		);

		BreadcrumbComponent::add('Marketplaces');
		BreadcrumbComponent::add('Editar Marketplace');

		$this->set(compact('marketplaceTipos', 'tiendas'));

	}

	public function admin_activar($id = null) {

		if ( ! $this->Marketplace->exists($id) ){
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Marketplace']['id'] = $id;
			$this->request->data['Marketplace']['activo'] = 1;

			if ( $this->Marketplace->save($this->request->data) ) {
				$this->Session->setFlash('Registro activado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}

	public function admin_desactivar($id = null) {

		if ( ! $this->Marketplace->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Marketplace']['id'] = $id;
			$this->request->data['Marketplace']['activo'] = 0;

			if ( $this->Marketplace->save($this->request->data) ) {
				$this->Session->setFlash('Registro desactivado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}

}
