<?php
App::uses('AppController', 'Controller');

class MetodoEnviosController extends AppController
{	
	public $components = array(
		'Starken',
		'Conexxion',
		'Boosmap'
	);

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

		$dependencias = $this->MetodoEnvio->dependencias();
		
		BreadcrumbComponent::add('Métodos de envio');
		BreadcrumbComponent::add('Editar Método de envio');

		$this->set(compact('dependencias'));

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
		

		$comunas = ClassRegistry::init('Comuna')->find('list', array('fields' => array('Comuna.nombre', 'Comuna.nombre'), 'order' => array('Comuna.nombre' => 'ASC')));

		$dependencias  = $this->MetodoEnvio->dependencias();

		$dependenciasVars = array();

		# Starken
		$dependenciasVars['starken']['tipo_entregas']  = $this->Starken->getTipoEntregas();
		$dependenciasVars['starken']['tipo_pagos']     = $this->Starken->getTipoPagos();
		$dependenciasVars['starken']['tipo_servicios'] = $this->Starken->getTipoServicios();
		$dependenciasVars['starken']['comunas']        = $comunas;
		

		# Conexxion
		$dependenciasVars['conexxion']['tipo_retornos']       = $this->Conexxion->obtener_tipo_retornos();
		$dependenciasVars['conexxion']['tipo_productos']      = $this->Conexxion->obtener_tipo_productos();
		$dependenciasVars['conexxion']['tipo_servicios']      = $this->Conexxion->obtener_tipo_servicios();
		$dependenciasVars['conexxion']['tipo_notificaciones'] = $this->Conexxion->obtener_tipo_notificaciones();
		$dependenciasVars['conexxion']['comunas']             = $comunas;

		# Boosmap
		$dependenciasVars['boosmap']['pickup'] = $this->Boosmap->obtener_pickups();
		$dependenciasVars['boosmap']['tipo_servicios'] = $this->Boosmap->obtener_tipo_servicios();
		
		BreadcrumbComponent::add('Métodos de envio');
		BreadcrumbComponent::add('Editar Método de envio');

		$this->set(compact('dependencias', 'dependenciasVars'));

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


	public function admin_crear_ruta()
	{
		$token = $this->Auth->user('token.token');

		$estados = ClassRegistry::init('VentaEstado')->find('list', array(
			'conditions' => array(
				'VentaEstado.activo' => 1,
				'VentaEstado.origen' => 0
			),
			'joins' => array(
				array(
					'table' => 'rp_venta_estado_categorias',
					'alias' => 'VentaEstadoCategoria',
					'type' => 'INNER',
					'conditions' => array(
						'VentaEstadoCategoria.id = VentaEstado.venta_estado_categoria_id',
						'VentaEstadoCategoria.venta' => 1
					)
				)
			)
		));
		
		$metodoEnvios = $this->MetodoEnvio->find('list');

		$this->set(compact('token', 'estados', 'metodoEnvios'));

		BreadcrumbComponent::add('Calcular ruta');
	
	}



	/**
	 * Obtener métodos de envio disponibles
	 * @return mixed
	 */
	public function api_obtener_metodos_envios()
	{	
		# Sólo método Get
		if (!$this->request->is('get')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only GET request allow'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

		$metodoEnvios = $this->MetodoEnvio->find('list', array('conditions' => array('activo' => 1)));

		$this->set(array(
            'response' => $metodoEnvios,
            '_serialize' => array('response')
        ));

	}


	public function api_obtener_metodos()
	{

		# Sólo método Get
		if (!$this->request->is('get')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only GET request allow'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}
		
		$filtrar = [
			'nombre LIKE' 	=>	isset($this->request->query['nombre'])?'%'.$this->request->query['nombre'].'%':null,	
			array('activo' 	=> 	$this->request->query['activo']??1)
		];
		$filtrar = array_filter($filtrar);		
		$metodoEnvios = $this->MetodoEnvio->find('list', ['conditions' => $filtrar]);

		$this->set(array(
            'response' => $metodoEnvios,
            '_serialize' => array('response')));

		}
}