<?php
App::uses('AppController', 'Controller');

class MetodoEnviosController extends AppController
{
	public $components = array(
		'Starken',
		'Conexxion',
		'Boosmap',
		'BlueExpress'
	);

	public function admin_index()
	{

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
		if ($this->request->is('post') || $this->request->is('put')) {

			if ($this->MetodoEnvio->save($this->request->data)) {
				$this->Session->setFlash('Registro creado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			} else {
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
		if (!$this->MetodoEnvio->exists($id)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			if ($this->MetodoEnvio->save($this->request->data)) {
				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		} else {
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

	public function admin_activar($id = null)
	{

		if (!$this->MetodoEnvio->exists($id)) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->request->data['MetodoEnvio']['id'] = $id;
			$this->request->data['MetodoEnvio']['activo'] = 1;

			if ($this->MetodoEnvio->save($this->request->data)) {
				$this->Session->setFlash('Registro activado correctamente', null, array(), 'success');
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$this->redirect(array('action' => 'index'));
	}

	public function admin_desactivar($id = null)
	{

		if (!$this->MetodoEnvio->exists($id)) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$this->request->data['MetodoEnvio']['id'] = $id;
			$this->request->data['MetodoEnvio']['activo'] = 0;

			if ($this->MetodoEnvio->save($this->request->data)) {
				$this->Session->setFlash('Registro desactivado correctamente', null, array(), 'success');
			} else {
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
	 * generar_etiqueta_envio_externo
	 *
	 * @param  mixed $id_venta
	 * @return void
	 */
	public function generar_etiqueta_envio_externo($id_venta)
	{
		$venta = ClassRegistry::init('Venta')->obtener_venta_por_id($id_venta);

		$logs = array();

		$metodo_envio_enviame = explode(',', $venta['Tienda']['meta_ids_enviame']);

		$resultado = false;

		$logs[] = array(
			'Log' => array(
				'administrador' => 'Crear etiqueta envio externa venta ' . $id_venta,
				'modulo' => 'MetodoEnvio',
				'modulo_accion' => json_encode($venta)
			)
		);

		# Creamos pedido en enviame si corresponde
		if (in_array($venta['Venta']['metodo_envio_id'], $metodo_envio_enviame) && $venta['Tienda']['activo_enviame']) {
			$this->Enviame = $this->Components->load('Enviame');
			$this->Enviame = $this->Components->load('Enviame');

			# conectamos con enviame
			$this->Enviame->conectar($venta['Tienda']['apikey_enviame'], $venta['Tienda']['company_enviame'], $venta['Tienda']['apihost_enviame']);

			$resultadoEnviame = $this->Enviame->crearEnvio($venta);

			$logs[] = array(
				'Log' => array(
					'administrador' => 'Crear etiqueta Enviame venta ' . $id_venta,
					'modulo' => 'MetodoEnvio',
					'modulo_accion' => json_encode($resultadoEnviame)
				)
			);

			if ($resultadoEnviame) {
				$resultado = true;
			}
		} elseif ($venta['MetodoEnvio']['dependencia'] == 'starken' && $venta['MetodoEnvio']['generar_ot']) {
			# Es una venta para starken

			# Creamos cliente starken
			$this->Starken = $this->Components->load('Starken');
			$this->Starken->crearCliente($venta['MetodoEnvio']['rut_api_rest'], $venta['MetodoEnvio']['clave_api_rest'], $venta['MetodoEnvio']['rut_empresa_emisor'], $venta['MetodoEnvio']['rut_usuario_emisor'], $venta['MetodoEnvio']['clave_usuario_emisor']);

			# Creamos la OT
			if ($this->Starken->generar_ot($venta)) {

				$this->Starken->registrar_estados($venta['Venta']['id']);
				$resultado = true;

				$logs[] = array(
					'Log' => array(
						'administrador' => 'Crear etiqueta Starken venta ' . $id_venta,
						'modulo' => 'MetodoEnvio',
						'modulo_accion' => 'Generada con éxito'
					)
				);
			}
		} elseif ($venta['MetodoEnvio']['dependencia'] == 'conexxion' && $venta['MetodoEnvio']['generar_ot']) {
			# Es una venta para conexxion

			# Creamos cliente conexxion
			$this->Conexxion = $this->Components->load('Conexxion');
			$this->Conexxion->crearCliente($venta['MetodoEnvio']['api_key']);

			# Creamos la OT
			if ($this->Conexxion->generar_ot($venta)) {
				$resultado = true;

				$logs[] = array(
					'Log' => array(
						'administrador' => 'Crear etiqueta Conexxion venta ' . $id_venta,
						'modulo' => 'MetodoEnvio',
						'modulo_accion' => 'Generada con éxito'
					)
				);
			}
		} elseif ($venta['MetodoEnvio']['dependencia'] == 'boosmap' && $venta['MetodoEnvio']['generar_ot']) {
			# Es una venta para boosmap

			# Creamos cliente boosmap
			$this->Boosmap = $this->Components->load('Boosmap');
			$this->Boosmap->crearCliente($venta['MetodoEnvio']['boosmap_token']);

			# Creamos la OT
			if ($this->Boosmap->generar_ot($venta)) {

				$this->Boosmap->registrar_estados($venta['Venta']['id']);

				$resultado = true;

				$logs[] = array(
					'Log' => array(
						'administrador' => 'Crear etiqueta Boosmap venta ' . $id_venta,
						'modulo' => 'MetodoEnvio',
						'modulo_accion' => 'Generada con éxito'
					)
				);
			}
		} elseif ($venta['MetodoEnvio']['dependencia'] == 'blueexpress' && $venta['MetodoEnvio']['generar_ot']) {
			# Es una venta para boosmblueexpressp

			# Creamos cliente blueexpress
			$this->BlueExpress = $this->Components->load('BlueExpress');
			$this->BlueExpress->crearCliente($venta['MetodoEnvio']['token_blue_express'],$venta['MetodoEnvio']['cod_usuario_blue_express'],$venta['MetodoEnvio']['cta_corriente_blue_express']);

			# Creamos la OT
			if ($this->BlueExpress->generar_ot($venta)) {

				$this->BlueExpress->registrar_estados($venta['Venta']['id']);

				$resultado = true;

				$logs[] = array(
					'Log' => array(
						'administrador' => 'Crear etiqueta BlueExpress venta ' . $id_venta,
						'modulo' => 'MetodoEnvio',
						'modulo_accion' => 'Generada con éxito'
					)
				);
			}
		}

		$logs[] = array(
			'Log' => array(
				'administrador' => 'Finaliza generar etiqueta externa venta ' . $id_venta,
				'modulo' => 'MetodoEnvio',
				'modulo_accion' => 'Resultado de la operación: ' . $resultado
			)
		);


		ClassRegistry::init('Log')->saveMany($logs);

		return $resultado;
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


	/**
	 * api_obtener_metodos
	 *
	 * @return void
	 */
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
			'nombre LIKE' 	=>	isset($this->request->query['nombre']) ? '%' . $this->request->query['nombre'] . '%' : null,
			array('activo' 	=> 	$this->request->query['activo'] ?? 1)
		];
		$filtrar = array_filter($filtrar);
		$metodoEnvios = $this->MetodoEnvio->find('list', ['conditions' => $filtrar]);

		$this->set(array(
			'response' => $metodoEnvios,
			'_serialize' => array('response')
		));
	}


	/**
	 * api_generar_etiqueta_externa
	 *
	 * @param  mixed $id_venta
	 * @return void
	 */
	public function api_generar_etiqueta_externa($id_venta)
	{
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

		$respuesta = array(
			'code' => 401,
			'message' => 'No fue posible generar etiqueta externa o no aplica',
			'body' => array()
		);

		# Generamos
		if ($this->generar_etiqueta_envio_externo($id_venta)) {
			$respuesta = array(
				'code' => 200,
				'message' => 'Etiqueta generada con éxito',
				'body' => array()
			);
		}

		$this->set(array(
			'response' => $respuesta,
			'_serialize' => array('response')
		));
	}
}
