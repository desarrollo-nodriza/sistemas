<?php
App::uses('AppController', 'Controller');
App::uses('VentasController', 'Controller');

class MetodoEnviosController extends AppController
{	
	public $components = array(
		'Starken',
		'Conexxion',
		'Boosmap',
		'BlueExpress',
		'Enviame',
	);

	public $tipo_servicio=[
		"EX" => "Express.",
		"PR" => "Premium.",
		"PY" => "Prioritario.",
		"MD" => "Same Day."
	];

	public function admin_index () {

		# Al recibir el filtro lo pasamos a parámetros
		if ( $this->request->is('post') ) {

			$this->filtro('metodoEnvios', 'index', 'Filtro');

		}

		$qry = array(
			'recursive' => 0,
			'sort' => 'MetodoEnvio.nombre',
			'direction' => 'ASC'
		);
		
		# condiciones del filtro
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'id':
						$qry = array_replace_recursive($qry, array(
							'conditions' => array('MetodoEnvio.id' => str_replace('%2F', '/', urldecode($valor) ) )));
						break;
					case 'nombre':
						$qry = array_replace_recursive($qry, array(
							'conditions' => array('MetodoEnvio.nombre LIKE' => '%'.trim(str_replace('%2F', '/', urldecode($valor) )).'%')));
						break;
					case 'dependencia':
						$qry = array_replace_recursive($qry, array(
							'conditions' => array('MetodoEnvio.dependencia' => $valor)));
						break;
					case 'bodega':
						$qry = array_replace_recursive($qry, array(
							'conditions' => array('MetodoEnvio.bodega_id' => $valor)));
						break;
					case 'activo':
						$qry = array_replace_recursive($qry, array(
							'conditions' => array('MetodoEnvio.activo' => ($valor == 'activo') ? 1 : 0 )));
						
						break;
				}
			}
		}

		$this->paginate = $qry;

		$metodoEnvios = $this->paginate();

		$dependencias = $this->MetodoEnvio->dependencias();

		$bodegas = $this->MetodoEnvio->Bodega->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Métodos de envio');

		$this->set(compact('metodoEnvios', 'dependencias', 'bodegas'));

	}


	public function admin_add()
	{
		if ($this->request->is('post') || $this->request->is('put')) {
			
			$saved_metodo = $this->MetodoEnvio->save($this->request->data);
			if ($saved_metodo) {

				$mensaje = empty($saved_metodo['MetodoEnvio']['dependencia']) ? 'Se creo correctamente' : 'Se creo correctamente, favor completar Configuración para el correcto funcionamiento';

				$this->Session->setFlash($mensaje, null, array(), 'success');

				$this->redirect(array('action' => 'edit', $saved_metodo['MetodoEnvio']['id']));
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$bodegas = ClassRegistry::init('Bodega')->find('list',[
			'conditions'=>['Bodega.activo'=>true]
		]);

		$dependencias = $this->MetodoEnvio->dependencias();

		BreadcrumbComponent::add('Métodos de envio', '/metodoEnvios');
		BreadcrumbComponent::add('Editar Método de envio');

		$estados_sin_procesar = ClassRegistry::init('VentaEstado')->find(
			'all',
			[
				'fields' => ['VentaEstado.id', 'VentaEstado.nombre'],
				'conditions' => ['VentaEstado.activo'],
				'contain'   => [
					'VentaEstadoCategoria' =>
					[
						'fields' => 'VentaEstadoCategoria.nombre'
					]
				]
			]
		);
		$estados = [];
		$cuentaCorrienteTransporte = ClassRegistry::init('CuentaCorrienteTransporte')->selector();
		foreach ($estados_sin_procesar as  $value) {
			$estados[$value['VentaEstado']['id']] = "Estado {$value['VentaEstado']['nombre']} | Categoría {$value['VentaEstadoCategoria']['nombre']}";
		}

		$this->set(compact('dependencias','bodegas','estados','cuentaCorrienteTransporte'));
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
				ClassRegistry::init('BodegasMetodoEnvio')->deleteAll([
					'BodegasMetodoEnvio.bodega_id'			=> $this->request->data['MetodoEnvio']['bodega_id'],
					'BodegasMetodoEnvio.metodo_envio_id' 	=> $this->request->data['MetodoEnvio']['id']
				]);
				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'edit', $id));
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
					),
					'contain'=>[
							'BodegasMetodoEnvio'=>['Bodega']
						]
				)
			);
		}
	
		$comunas = ClassRegistry::init('Comuna')->find('list', array('fields' => array('Comuna.nombre', 'Comuna.nombre'), 'order' => array('Comuna.nombre' => 'ASC')));

		$dependencias  = $this->MetodoEnvio->dependencias();

		
		$bodegas = ClassRegistry::init('Bodega')->find('list',[
			'conditions'=>['Bodega.activo'=>true]
		]);
		$tipo_servicio = $this->tipo_servicio;

		$estados_sin_procesar = ClassRegistry::init('VentaEstado')->find(
			'all',
			[
				'fields' => ['VentaEstado.id', 'VentaEstado.nombre'],
				'conditions' => ['VentaEstado.activo'],
				'contain'   => [
					'VentaEstadoCategoria' =>
					[
						'fields' => 'VentaEstadoCategoria.nombre'
					]
				]
			]
		);
		$estados = [];
		
		foreach ($estados_sin_procesar as  $value) {
			$estados[$value['VentaEstado']['id']] = "Estado {$value['VentaEstado']['nombre']} | Categoría {$value['VentaEstadoCategoria']['nombre']}";
		}

		$cuentaCorrienteTransporte = ClassRegistry::init('CuentaCorrienteTransporte')->selector();
	
		BreadcrumbComponent::add('Métodos de envio', '/metodoEnvios');
		BreadcrumbComponent::add('Editar Método de envio');
		$this->set(compact('dependencias', 'dependenciasVars','bodegas','tipo_servicio','estados','cuentaCorrienteTransporte'));
		

	}

	public function admin_delete($id = null)
	{
		$this->MetodoEnvio->id = $id;
		if (!$this->MetodoEnvio->exists()) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ($this->MetodoEnvio->delete()) {
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
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
	 * generar_etiqueta_envio_externo
	 *
	 * @param  mixed $id_venta
	 * @return void
	 */
	public function generar_etiqueta_envio_externo($id_venta)
	{

		$venta 					= ClassRegistry::init('Venta')->obtener_venta_por_id($id_venta);
		$logs 					= [];
		$metodo_envio_enviame 	= explode(',', $venta['Tienda']['meta_ids_enviame']);
		$resultado 				= false;

		$logs[] = array(
			'Log' => array(
				'administrador' => "Crear etiqueta envio externo | Vid: {$id_venta}",
				'modulo'     	=> 'MetodoEnviosController',
				'modulo_accion' => json_encode($venta)
			)
		);
		
		$procesando = Hash::extract($venta['EmbalajeWarehouse'], "{n}[estado=procesando]");
		$finalizado = Hash::extract($venta['EmbalajeWarehouse'], "{n}[estado=finalizado]");
		$embalajes	= array_merge($procesando, $finalizado);
		
		if (!$embalajes) {

			$logs[] = array(
				'Log' => array(
					'administrador' => 'Vid ' . $venta['Venta']['id'],
					'modulo'    	=> 'MetodoEnviosController',
					'modulo_accion' => json_encode(["No posee embalajes en procesando" => $venta])
				)
			);
		}

		if (in_array($venta['Venta']['metodo_envio_id'], $metodo_envio_enviame) && $venta['Tienda']['activo_enviame']) {

			$this->Enviame->conectar($venta['Tienda']['apikey_enviame'], $venta['Tienda']['company_enviame'], $venta['Tienda']['apihost_enviame']);

			$resultadoEnviame = $this->Enviame->crearEnvio($venta);

			$logs[] = array(
				'Log' => array(
					'administrador' => 'Crear etiqueta Enviame venta ' . $id_venta,
					'modulo' 		=> 'MetodoEnviosController',
					'modulo_accion' => json_encode($resultadoEnviame)
				)
			);

			if ($resultadoEnviame) {
				$resultado = true;
			}
			
		} elseif ($venta['MetodoEnvio']['generar_ot']) {

			foreach ($embalajes as $embalaje) {

				$cuenta_corriente_transporte_id = null;
				$informacion_bodega       		= [];

				if ($embalaje['TransportesVenta']) {
					$logs[] = array(
						'Log' => array(
							'administrador' => "Vid $id_venta | Embalaje {$embalaje['id']}",
							'modulo'     	=> 'MetodoEnviosController',
							'modulo_accion' => json_encode(["Ya posee OT creadas" => $embalaje['TransportesVenta']])
						)
					);
					continue;
				}

				if ($venta['MetodoEnvio']['bodega_id'] ==  $embalaje['bodega_id']) {

					$cuenta_corriente_transporte_id = $venta['MetodoEnvio']['cuenta_corriente_transporte_id'];
					$informacion_bodega       		= $venta['MetodoEnvio']['Bodega'];

				} else {
					
					$cuenta_corriente_transporte_id = Hash::extract($venta['MetodoEnvio']['BodegasMetodoEnvio'], "{n}[bodega_id={$embalaje['bodega_id']}].cuenta_corriente_transporte_id")[0] ?? null;
					$informacion_bodega       		= Hash::extract($venta['MetodoEnvio']['BodegasMetodoEnvio'], "{n}[bodega_id={$embalaje['bodega_id']}].Bodega")[0] ?? [];

				}

				if (is_null($cuenta_corriente_transporte_id)) {

					$logs[] = array(
						'Log' => array(
							'administrador' => "Vid {$id_venta} | El metodo no tiene una cuenta corriente asignada",
							'modulo'     	=> 'MetodoEnviosController',
							'modulo_accion' => json_encode($venta['MetodoEnvio'])
						)
					);
					continue;
				}

				$CuentaCorrienteTransporte = ClassRegistry::init('CuentaCorrienteTransporte')->valor_atributos($cuenta_corriente_transporte_id);

				if (!$CuentaCorrienteTransporte) {

					$logs[] = array(
						'Log' => array(
							'administrador' => "Vid {$id_venta} | Cuenta corriente no tiene asignado valores {$cuenta_corriente_transporte_id}",
							'modulo'     	=> 'MetodoEnviosController',
							'modulo_accion' =>  json_encode($venta['MetodoEnvio'])
						)
					);
					continue;
				}

				$CuentaCorrienteTransporte['informacion_bodega'] = $informacion_bodega;

				switch (ClassRegistry::init('CuentaCorrienteTransporte')->dependencia($cuenta_corriente_transporte_id)) {

					case 'starken':

						$this->Starken = $this->Components->load('Starken');
						$this->Starken->crearCliente($CuentaCorrienteTransporte['rutApiRest'], $CuentaCorrienteTransporte['claveApiRest'], $CuentaCorrienteTransporte['rutEmpresaEmisora'], $CuentaCorrienteTransporte['rutUsuarioEmisor'], $CuentaCorrienteTransporte['claveUsuarioEmisor']);
						# Creamos la OT
						if ($this->Starken->generar_ot($venta, $embalaje, $CuentaCorrienteTransporte)) {

							$resultado = true;
							$logs[] = array(
								'Log' => array(
									'administrador' => 'Crear etiqueta Starken venta ' . $id_venta,
									'modulo'     	=> 'MetodoEnviosController',
									'modulo_accion' => 'Generada con éxito'
								)
							);
						}
						break;

					case 'conexxion':
						// # Creamos cliente conexxion
						// $this->Conexxion = $this->Components->load('Conexxion');
						// $this->Conexxion->crearCliente($venta['MetodoEnvio']['api_key']);

						// # Creamos la OT
						// if ($this->Conexxion->generar_ot($venta)) {
						// 	$resultado = true;

						// 	$logs[] = array(
						// 		'Log' => array(
						// 			'administrador' => 'Crear etiqueta Conexxion venta ' . $id_venta,
						// 			'modulo' 		=> 'MetodoEnviosController',
						// 			'modulo_accion' => 'Generada con éxito'
						// 		)
						// 	);
						// }
						break;
					case 'boosmap':

						# Creamos cliente boosmap
						$this->Boosmap = $this->Components->load('Boosmap');
						$this->Boosmap->crearCliente($CuentaCorrienteTransporte['boosmap_token']);
						# Creamos la OT
						if ($this->Boosmap->generar_ot($venta, $embalaje, $CuentaCorrienteTransporte)) {
							$resultado = true;

							$logs[] = array(
								'Log' => array(
									'administrador' => 'Crear etiqueta Boosmap venta ' . $id_venta,
									'modulo' 		=> 'MetodoEnviosController',
									'modulo_accion' => 'Generada con éxito'
								)
							);
						}
						break;

					case 'blueexpress':
						
						$this->BlueExpress = $this->Components->load('BlueExpress');
						$this->BlueExpress->crearCliente($CuentaCorrienteTransporte['BX_TOKEN'], $CuentaCorrienteTransporte['BX_USERCODE'], $CuentaCorrienteTransporte['BX_CLIENT_ACCOUNT']);
						
						# Creamos la OT
						if ($this->BlueExpress->generar_ot($venta, $embalaje, $CuentaCorrienteTransporte)) {
							$resultado = true;
							$logs[] = array(
								'Log' => array(
									'administrador' => 'Crear etiqueta BlueExpress venta ' . $id_venta,
									'modulo'     	=> 'MetodoEnviosController',
									'modulo_accion' => 'Generada con éxito'
								)
							);
						}
						break;

					default:

						break;
				}
			}
		}
		$logs[] = array(
			'Log' => array(
				'administrador' => "Finaliza generar etiqueta externa venta $id_venta",
				'modulo'     	=> 'MetodoEnviosController',
				'modulo_accion' => 'Resultado de la operación: ' . $resultado ? 'Se completo':'No se completo'
			)
		);

		ClassRegistry::init('Log')->saveMany($logs);
		
		$VentasController = new VentasController();
		$VentasController->actualizar_estados_envios($id_venta);
		
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
			['nombre LIKE' 	=>	!empty($this->request->query['nombre'])? '%'.$this->request->query['nombre'].'%': null],	
			['activo' 	    => 	$this->request->query['activo'] ??1 ],
			['bodega_id IN'    =>	explode(',', $this->request->query['bodega_id']) ?? null ]
		];
		
		$filtrar = array_filter($filtrar, function ($var) {
			foreach ($var as $value) {
				return !empty($value);
			}
		});

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
		if ($this->generar_etiqueta_envio_externo($id_venta))
		{	
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

	public function admin_bodega_add($id)
	{
		
		if ($this->request->is('post')) {

			$relaciones = array_filter($this->request->data, function ($key, $value) {
				return (!empty($key['bodega_id']));
			}, ARRAY_FILTER_USE_BOTH);
			$nuevas_relacion = [];
			foreach ($relaciones as  $value) {
				$nuevas_relacion[] = ['BodegasMetodoEnvio' => $value];
			}
			if (ClassRegistry::init('BodegasMetodoEnvio')->saveAll($nuevas_relacion)) {
				$this->Session->setFlash(
					'Registro agregado correctamente.',
					null,
					array(),
					'success'
				);
			} else {
				$this->Session->setFlash(
					'Error al guardar la relación. Por favor intenta nuevamente.',
					null,
					array(),
					'danger'
				);
			}
		}

		$this->redirect(array('action' => 'edit',$id));
	
		
	}

	public function admin_bodega_delete($id)
	{
		if ($this->request->is('post')) {

			
			if (ClassRegistry::init('BodegasMetodoEnvio')->delete(['BodegasMetodoEnvio.id' => $this->request->data['id']])) {

				$this->Session->setFlash(
					'Se ha eliminado relacion.',
					null,
					array(),
					'success'
				);
			} else {

				$this->Session->setFlash(
					'No se han podido eliminar la relacion',
					null,
					array(),
					'danger'
				);
			}
		}

		$this->redirect(array('action' => 'edit', $id));
		
	}
		
}