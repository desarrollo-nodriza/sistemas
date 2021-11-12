<?php
App::uses('AppController', 'Controller');
class TiendasController extends AppController
{	

	public $components = array(
		'Starken'
	);

	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		BreadcrumbComponent::add('Tiendas ');

		$tiendas	= $this->paginate();
		$this->set(compact('tiendas'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{	
			$this->request->data['Tienda']['meta_ids_enviame'] = implode(',', $this->request->data['Tienda']['meta_ids_enviame']);

			$this->Tienda->create();
			if ( $this->Tienda->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Tiendas ', '/tiendas');
		BreadcrumbComponent::add('Agregar ');

		$metodo_envios = ClassRegistry::init('MetodoEnvio')->find('list');
	
		$this->set(compact('metodo_envios'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Tienda->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			
			if ($this->request->data['Tienda']['meta_ids_enviame']) {
				$this->request->data['Tienda']['meta_ids_enviame'] = implode(',', $this->request->data['Tienda']['meta_ids_enviame']);
			}
					
			if ( $this->Tienda->save($this->request->data) )
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
			$this->request->data	= $this->Tienda->find('first', array(
				'conditions'	=> array('Tienda.id' => $id)
			));
		}

		$this->request->data['Tienda']['meta_ids_enviame'] = explode(',', $this->request->data['Tienda']['meta_ids_enviame']);

		BreadcrumbComponent::add('Tiendas ', '/tiendas');
		BreadcrumbComponent::add('Editar ');

		$metodo_envios = ClassRegistry::init('MetodoEnvio')->find('list');

		$this->set(compact('metodo_envios'));
	}

	public function admin_delete($id = null)
	{
		$this->Tienda->id = $id;
		if ( ! $this->Tienda->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Tienda->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Tienda->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Tienda->_schema);
		$modelo			= $this->Tienda->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	/**
	 * [api_obtener_tiendas description]
	 * @return [type] [description]
	 */
	public function api_obtener_tiendas()
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

		$tiendas = $this->Tienda->find('list', array('conditions' => array('activo' => 1)));

		$this->set(array(
            'response' => $tiendas,
            '_serialize' => array('response')
        ));

	}


	/**
	 * Calcula el costo de envio de un bulto según
	 * sus dimensiones.
	 * @param int $id Id de la tienda
	 */
	public function api_calcular_costo_envio($id)
	{
		# Sólo método post
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only POST request allow'
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

		# Validamos la tienda
		if( !$this->Tienda->exists($id)){
			$response = array(
				'code'    => 404, 
				'message' => 'Store selected doesn´t exist'
			);

			throw new CakeException($response);
		}

		$tienda = $this->Tienda->find('first', array(
			'conditions' => array(
				'Tienda.id' => $id
			),
			'fields' => array(
				'Tienda.id',
				'Tienda.starken_rut',
				'Tienda.starken_clave'
			)
		));

		$costos = array(
			'starken' => (float) 0.00
		);

		# Creamos cliente starken
		$this->Starken->crearCliente($tienda['Tienda']['starken_rut'], $tienda['Tienda']['starken_clave'], null, null, null);
		
		$ciudadOrigen  = $this->request->data['ciudadOrigen'];
		$ciudadDestino = $this->request->data['ciudadDestino'];
		$altoBulto     = (float) $this->request->data['altoBulto'];
		$anchoBulto    = (float) $this->request->data['anchoBulto'];
		$largoBulto    = (float) $this->request->data['largoBulto'];
		$kilosBulto    = (float) $this->request->data['kilosBulto'];


		# Validamos que todos los campos tengan valor
		if( empty($ciudadOrigen) ||
			empty($ciudadDestino) ||
			empty($altoBulto) ||
			empty($anchoBulto) ||
			empty($largoBulto) ||
			empty($kilosBulto)){
			
			$response = array(
				'code'    => 508, 
				'message' => 'Todos los parámetros son requeridos'
			);

			throw new CakeException($response);

		}


		$comunaOrigen  =  ClassRegistry::init('Comuna')->find('first', array(
			'conditions' => array(
				'OR' => array(
					'Comuna.nombre' => $ciudadOrigen,
					'Comuna.cod_starken' => $ciudadOrigen,
					'Comuna.alias LIKE' => '%'.$ciudadOrigen.'%'
				)
			),
			'fields' => array(
				'Comuna.cod_starken'
			)
		));

		# No se encontró comuna
		if (empty($comunaOrigen)){
			$response = array(
				'code'    => 404, 
				'message' => 'Comuna de origen no encontrada'
			);

			throw new CakeException($response);
		}

		$comunaDestino  =  ClassRegistry::init('Comuna')->find('first', array(
			'conditions' => array(
				'OR' => array(
					'Comuna.nombre' => $ciudadDestino,
					'Comuna.cod_starken' => $ciudadDestino,
					'Comuna.alias LIKE' => '%'.$ciudadDestino.'%'
				)
			),
			'fields' => array(
				'Comuna.cod_starken'
			)
		));

		# No se encontró comuna
		if (empty($comunaDestino)){
			$response = array(
				'code'    => 404, 
				'message' => 'Comuna de destino no encontrada'
			);

			throw new CakeException($response);
		}
		
		$starken = $this->Starken->obtener_costo_envio($comunaOrigen['Comuna']['cod_starken'], $comunaDestino['Comuna']['cod_starken'], $altoBulto, $anchoBulto, $largoBulto, $kilosBulto);

		$respuesta = array(
			'code' => 200,
			'message' => 'Respuesta obtenida',
			'transportes' => array(
				'starken' => (isset($starken['code'])) ? $starken['body'] : array()
			)
		);
		
		$this->set(array(
            'response' => $respuesta,
            '_serialize' => array('response')
        ));

	}
}
