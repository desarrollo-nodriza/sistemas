<?php
App::uses('AppController', 'Controller');
class MetodoEnviosController extends AppController
{

	public function admin_index () {

		$this->paginate = array(
			'recursive' => 0,
			'sort' => 'MetodoEnvio.nombre',
			'direction' => 'ASC'
		);

		$metodoEnvios = $this->paginate();

		BreadcrumbComponent::add('Métodos de envio');

		$this->set(compact('metodoEnvios'));

	}


	public function admin_add()
	{
		if ( $this->request->is('post') || $this->request->is('put') )
		{

			if ( $this->MetodoEnvio->save($this->request->data) )
			{
				$this->Session->setFlash('Registro creado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}		
		
		BreadcrumbComponent::add('Métodos de envio');
		BreadcrumbComponent::add('Editar Método de envio');

	}


	public function admin_edit($id = null)
	{
		if ( ! $this->MetodoEnvio->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{

			if ( $this->MetodoEnvio->save($this->request->data) )
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
			$this->request->data = $this->MetodoEnvio->find(
				'first',
				array(
					'conditions' => array(
						'MetodoEnvio.id' => $id
					)
				)
			);
		}
		
		BreadcrumbComponent::add('Métodos de envio');
		BreadcrumbComponent::add('Editar Método de envio');

	}

	public function admin_activar($id = null) {

		if ( ! $this->MetodoEnvio->exists($id) ){
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['MetodoEnvio']['id'] = $id;
			$this->request->data['MetodoEnvio']['activo'] = 1;

			if ( $this->MetodoEnvio->save($this->request->data) ) {
				$this->Session->setFlash('Registro activado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}

	public function admin_desactivar($id = null) {

		if ( ! $this->MetodoEnvio->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['MetodoEnvio']['id'] = $id;
			$this->request->data['MetodoEnvio']['activo'] = 0;

			if ( $this->MetodoEnvio->save($this->request->data) ) {
				$this->Session->setFlash('Registro desactivado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}


	public function admin_ajax_obtener_metodo_envio($id)
	{

		$this->layout = 'ajax';

		$m = $this->MetodoEnvio->find('first', array(
			'conditions' => array(
				'MetodoEnvio.id' => $id
			)
		));

		$this->set(compact('m'));

	}

}