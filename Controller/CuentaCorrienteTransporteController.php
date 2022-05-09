<?php

App::uses('AppController', 'Controller');

class CuentaCorrienteTransporteController extends AppController
{
	public $components = array(
		'Starken',
		'Conexxion',
		'Boosmap',
		'BlueExpress'
	);

	public $helpers = array('Html', 'Form');

	public function filtrar($controlador = '', $accion = '')
	{
		$redirect = array(
			'controller' => $controlador,
			'action' => $accion
		);

		foreach ($this->request->data['Filtro'] as $campo => $valor) {
			if ($valor != '') {
				$redirect[$campo] = str_replace('/', '-', $valor);
			}
		}

		$this->redirect($redirect);
	}

	public function admin_index()
	{
		$filtro = [];

		if ($this->request->is('post')) {
		}

		$this->paginate	 = [
			'recursive'	=> 0,
			'limit' 	=> 20,
			'order' 	=> ['id' => 'DESC']
		];

		BreadcrumbComponent::add('Cuentas Corrientes');

		$cuentasCorrientes	= $this->paginate();
		$this->set(compact('cuentasCorrientes'));
	}

	public function admin_add()
	{
		if ($this->request->is('post') || $this->request->is('put')) {

			// * Los que tienen indice númerico corresponden al id del atributo que se va a relacionar

			$valor_tabla_dinamica = array_filter($this->request->data['CuentaCorrienteTransporte'], function ($v, $k) {
				return is_numeric($k);
			}, ARRAY_FILTER_USE_BOTH);


			if ($this->CuentaCorrienteTransporte->save($this->request->data)) {

				foreach ($valor_tabla_dinamica as $key => $valor) {
					$valor_atributo = ClassRegistry::init('ValorAtributoCuentaCorrienteTransporte')->find('first', [
						'conditions' => [
							'tabla_atributo_id' 				=> $key,
							'cuenta_corriente_trasnporte_id' 	=> $this->CuentaCorrienteTransporte->id
						]
					]);
					if ($valor_atributo) {
						$valor_atributo['ValorAtributoCuentaCorrienteTransporte']['valor'] = $valor;
					} else {
						$valor_atributo = ['ValorAtributoCuentaCorrienteTransporte' => [
							"tabla_atributo_id" 				=> $key,
							"cuenta_corriente_trasnporte_id" 	=> $this->CuentaCorrienteTransporte->id,
							"valor" 							=> $valor
						]];
					}
					ClassRegistry::init('ValorAtributoCuentaCorrienteTransporte')->create();
					ClassRegistry::init('ValorAtributoCuentaCorrienteTransporte')->saveAll($valor_atributo);
				}

				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'edit', $this->CuentaCorrienteTransporte->id));
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}


		$dependencias 			= ClassRegistry::init('MetodoEnvio')->dependencias();
		$comunas 				= ClassRegistry::init('Comuna')->find('list', array('fields' => array('Comuna.nombre', 'Comuna.nombre'), 'order' => array('Comuna.nombre' => 'ASC')));
		$dependenciasVars 		= [];

		# Starken
		$dependenciasVars['starken']['tipoEntrega']  			= $this->Starken->getTipoEntregas();
		$dependenciasVars['starken']['tipoPago']     			= $this->Starken->getTipoPagos();
		$dependenciasVars['starken']['tipoServicio'] 			= $this->Starken->getTipoServicios();
		$dependenciasVars['starken']['ciudadOrigenNom']        	= $comunas;


		# Conexxion
		$dependenciasVars['conexxion']['tipo_retornos']       	= $this->Conexxion->obtener_tipo_retornos();
		$dependenciasVars['conexxion']['tipo_productos']      	= $this->Conexxion->obtener_tipo_productos();
		$dependenciasVars['conexxion']['tipo_servicios']      	= $this->Conexxion->obtener_tipo_servicios();
		$dependenciasVars['conexxion']['tipo_notificaciones'] 	= $this->Conexxion->obtener_tipo_notificaciones();
		$dependenciasVars['conexxion']['comunas']             	= $comunas;

		# Boosmap
		$dependenciasVars['boosmap']['pickup'] 					= $this->Boosmap->obtener_pickups();
		$dependenciasVars['boosmap']['delivery_service'] 		= $this->Boosmap->obtener_tipo_servicios();

		# BlueExpress
		$dependenciasVars['blueexpress']['tipo_servicio_blue_express'] 		= $this->BlueExpress->tipo_servicio;

		$tabla_dinamica = ClassRegistry::init('TablaDinamica')->find('all', [
			'contain' => ['AtributoDinamico']
		]);

		$valor_tabla_dinamica = [];

		BreadcrumbComponent::add('Cuentas Corrientes', '/cuentaCorrienteTransporte');
		BreadcrumbComponent::add('Crear');

		$this->set(compact('dependencias', 'tipo_servicio', 'dependenciasDinamicas', 'comunas', 'dependenciasVars', 'tabla_dinamica', 'valor_tabla_dinamica'));
	}

	public function admin_edit($id = null)
	{

		if (!$this->CuentaCorrienteTransporte->exists($id)) {
			$this->Session->setFlash("No existe cuenta corriente con id {$id}", null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			// * Los que tienen indice númerico corresponden al id del atributo que se va a relacionar

			$valor_tabla_dinamica = array_filter($this->request->data['CuentaCorrienteTransporte'], function ($v, $k) {
				return is_numeric($k);
			}, ARRAY_FILTER_USE_BOTH);

			if ($this->CuentaCorrienteTransporte->save($this->request->data)) {

				foreach ($valor_tabla_dinamica as $key => $valor) {
					$valor_atributo = ClassRegistry::init('ValorAtributoCuentaCorrienteTransporte')->find('first', [
						'conditions' => [
							'tabla_atributo_id' 				=> $key,
							'cuenta_corriente_trasnporte_id' 	=> $id
						]
					]);
					if ($valor_atributo) {
						$valor_atributo['ValorAtributoCuentaCorrienteTransporte']['valor'] = $valor;
					} else {
						$valor_atributo = ['ValorAtributoCuentaCorrienteTransporte' => [
							"tabla_atributo_id" 				=> $key,
							"cuenta_corriente_trasnporte_id" 	=> $id,
							"valor" 							=> $valor
						]];
					}
					ClassRegistry::init('ValorAtributoCuentaCorrienteTransporte')->create();
					ClassRegistry::init('ValorAtributoCuentaCorrienteTransporte')->saveAll($valor_atributo);
				}


				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'edit', $id));
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}


		$dependencias 			= ClassRegistry::init('MetodoEnvio')->dependencias();
		$comunas 				= ClassRegistry::init('Comuna')->find('list', array('fields' => array('Comuna.nombre', 'Comuna.nombre'), 'order' => array('Comuna.nombre' => 'ASC')));
		$this->request->data 	= ClassRegistry::init('CuentaCorrienteTransporte')->find('first', ['conditions' => ['id' => $id]]);
		$dependenciasVars 		= [];

		# Starken
		$dependenciasVars['starken']['tipoEntrega']  			= $this->Starken->getTipoEntregas();
		$dependenciasVars['starken']['tipoPago']     			= $this->Starken->getTipoPagos();
		$dependenciasVars['starken']['tipoServicio'] 			= $this->Starken->getTipoServicios();
		$dependenciasVars['starken']['ciudadOrigenNom']        	= $comunas;


		# Conexxion
		$dependenciasVars['conexxion']['tipo_retornos']       	= $this->Conexxion->obtener_tipo_retornos();
		$dependenciasVars['conexxion']['tipo_productos']      	= $this->Conexxion->obtener_tipo_productos();
		$dependenciasVars['conexxion']['tipo_servicios']      	= $this->Conexxion->obtener_tipo_servicios();
		$dependenciasVars['conexxion']['tipo_notificaciones'] 	= $this->Conexxion->obtener_tipo_notificaciones();
		$dependenciasVars['conexxion']['comunas']             	= $comunas;

		# Boosmap
		$dependenciasVars['boosmap']['pickup'] 					= $this->Boosmap->obtener_pickups();
		$dependenciasVars['boosmap']['delivery_service'] 		= $this->Boosmap->obtener_tipo_servicios();

		# BlueExpress
		$dependenciasVars['blueexpress']['tipo_servicio_blue_express'] 		= $this->BlueExpress->tipo_servicio;

		$tabla_dinamica = ClassRegistry::init('TablaDinamica')->find('all', [
			'contain' => ['AtributoDinamico']
		]);

		$valor_tabla_dinamica = ClassRegistry::init('ValorAtributoCuentaCorrienteTransporte')->find('all', [
			'conditions' => ['cuenta_corriente_trasnporte_id' => $id]
		]);

		$dependenciasDinamicas = array_unique(Hash::extract($tabla_dinamica, "{n}.TablaDinamica.dependencia"));

		BreadcrumbComponent::add('Cuentas Corrientes', '/cuentaCorrienteTransporte');
		BreadcrumbComponent::add('Editar');

		$this->set(compact('dependencias', 'tipo_servicio', 'dependenciasDinamicas', 'comunas', 'dependenciasVars', 'tabla_dinamica', 'valor_tabla_dinamica'));
	}
}
