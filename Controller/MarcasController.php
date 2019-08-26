<?php
App::uses('AppController', 'Controller');
class MarcasController extends AppController
{	
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		
		BreadcrumbComponent::add('Marcas ');

		$marcas	= $this->paginate();
		$this->set(compact('marcas'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Marca->create();
			if ( $this->Marca->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Marcas ', '/marcas');
		BreadcrumbComponent::add('Agregar ');
	}

	public function admin_update()
	{
		$mensaje =  $this->actualizar_marcas_base();

		$this->Session->setFlash($this->crearAlertaUl($mensaje), null, array(), 'warning');
		$this->redirect(array('action' => 'index'));

	}

	public function admin_edit($id = null)
	{	
		if ( ! $this->Marca->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	

			$this->Marca->PrecioEspecificoMarca->deleteAll(array('PrecioEspecificoMarca.marca_id' => $id));

			if ( $this->Marca->saveAll($this->request->data) )
			{	

				if ($this->request->data['Marca']['actualizar_canales']) {
					if ( ! $this->actualizar_marca($id, $this->request->data['Marca']['nombre']) ) {
						$this->Session->setFlash('No fue posible actualizar la marca en Prestashop', null, array(), 'warning');
					}
				}

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
			$this->request->data	= $this->Marca->find('first', array(
				'conditions'	=> array('Marca.id' => $id),
				'contain' => array(
					'PrecioEspecificoMarca'
				)
			));
		}

		$tipoDescuento = array(1 => '%', 0 => '$');

		BreadcrumbComponent::add('Marcas ', '/marca');
		BreadcrumbComponent::add('Editar ');

		$this->set(compact('tipoDescuento'));

	}

	public function admin_delete($id = null)
	{
		$this->Marca->id = $id;
		if ( ! $this->Marca->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Marca->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Marca->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Marca->_schema);
		$modelo			= $this->Marca->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	public function actualizar_marca($id, $nombre) 
	{
		# Se carga el componente directamente para ser usado por la consola
		$this->Prestashop = $this->Components->load('Prestashop');

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array(
				'Tienda.activo' => 1
			),
			'contain' => array(
				'Marketplace' => array(
					'MarketplaceTipo'
				)
			),
			'fields' => array(
				'Tienda.apiurl_prestashop',
				'Tienda.apiurl_prestashop',
				'Tienda.apikey_prestashop',
				'Tienda.nombre'
			)
		));

		$return = true;

		foreach ($tiendas as $it => $tienda) {

			# Cliente Prestashop
			$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );

			if( !$this->Prestashop->prestashop_actualizar_marca($id, $nombre) ) {
				$return =  false;		
			}

		}

		return $return;		
	}

	/**
	 * Obtiene los proveedores y los agrega y/o actualiza los proveedores locales
	 * @return array 	Mensaje de la operación
	 */
	public function actualizar_marcas_base()
	{
		# Se carga el componente directamente para ser usado por la consola
		$this->Prestashop = $this->Components->load('Prestashop');

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array(
				'Tienda.activo' => 1
			),
			'contain' => array(
				'Marketplace' => array(
					'MarketplaceTipo'
				)
			),
			'fields' => array(
				'Tienda.apiurl_prestashop',
				'Tienda.apiurl_prestashop',
				'Tienda.apikey_prestashop',
				'Tienda.nombre'
			)
		));

		foreach ($tiendas as $it => $tienda) {

			# Cliente Prestashop
			$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );

			$marcas = $this->Prestashop->prestashop_obtener_marcas();
			
			$marcasLocales = array();
			$arrMessage 	  = array( 'No hay cambios disponibles.' );

			foreach ($marcas['manufacturer'] as $ip => $p) {
				
				# Verificamos que exista en la BD local
				$local = $this->Marca->find('first', array('conditions' => array('id' => $p['id']), 'fields' => array('id')));

				# Crear marca
				if (empty($local)) {
					$marcasLocales[$ip]['Marca']['id'] = $p['id'];
					$marcasLocales[$ip]['Marca']['nombre'] = $p['name'];
				}
			}

		}

		if (!empty($marcasLocales)) {
				
			if ($this->Marca->saveMany($marcasLocales))
			{	

				$this->relacionarMarcasProductos($this->Prestashop, $marcasLocales);
				$arrMessage = array( sprintf('Se han creado/modificado %d marcas', count($marcasLocales)) );
			}
		}
		
		return $arrMessage;
	}


	/**
	 * Permite relacionar las marcas con los productos según la base de prestashop
	 * @param  obj $conexion instancia de prestashop   
	 * @param    $marcas Arreglo de proveedores
	 * @return void
	 */
	private function relacionarMarcasProductos($conexion, $marcas = array())
	{

		foreach ($marcas as $i => $marca) {

			$filtroProductos = array(
				'filter[active]' => '[1]',
				'filter[id_manufacturer]' => '['.$marca['Marca']['id'].']'
			);

			$productos = $conexion->prestashop_obtener_productos($filtroProductos);

			if (!empty($productos)) {
				foreach ($productos['product'] as $ip => $producto) {

					if (!isset($producto['id'])) {
						continue;
					}

					$data = array(
						'VentaDetalleProducto' => array(
							'id' => $producto['id'],
							'marca_id' => $marca['Marca']['id']
						)
					);

					if (ClassRegistry::init('VentaDetalleProducto')->exists($producto['id'])){
						$this->Marca->VentaDetalleProducto->save($data);
					}

				}	
			}
		}

		return;
	}



	/**
	 * API
	 */
	/**
     * Crea/actualiza una marca desde Prestashop
     * @param  [type] $tienda_id [description]
     * @return [type]            [description]
     */
    public function api_crear() {

		# Solo método POST
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501,
				'name' => 'error',
				'message' => 'Método no permitido'
			);

			throw new CakeException($response);
		}

		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}


		if (empty($this->request->data['id_externo']) || empty($this->request->data['nombre'])) {
			$response = array(
				'code' => 504,
				'created' => false,
				'message' => 'Id y nombre requerido'
			);

			throw new CakeException($response);
		}

		$resultado = array(
			'code' => 201,
			'created' => false,
			'updated' => false
		);

		$log = array();

		$log[] = array(
			'Log' => array(
				'administrador' => 'Prestashop rest',
				'modulo' => 'Marcas',
				'modulo_accion' => json_encode($this->request->data)
			)
		);
			
		$data = array(
			'Marca' => array(
				'id'               => $this->request->data['id_externo'],
				'nombre'           => $this->request->data['nombre']
			)
		);

		$existe = true;


		if (!$this->Marca->exists($this->request->data['id_externo'])) {
			$this->Marca->create();
			$existe = false;	
		}
		
		if ($this->Marca->save($data)){

			if ($existe) {
				
				$log[] = array(
					'Log' => array(
						'administrador' => 'Prestashop rest',
						'modulo' => 'Marcas',
						'modulo_accion' => sprintf('Marca #%d actualizado con éxito', $this->request->data['id_externo'])
					)
				);

				$resultado = array(
					'code' => 200,
					'updated' => true
				);

			}else{

				$log[] = array(
					'Log' => array(
						'administrador' => 'Prestashop rest',
						'modulo' => 'Marcas',
						'modulo_accion' => sprintf('Marca #%d creada con éxito', $this->request->data['id_externo'])
					)
				);

				$resultado = array(
					'code' => 200,
					'created' => true
				);
			}
		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		$this->set(array(
			'response'   => $resultado,
			'_serialize' => array('response')
	    ));
	}

}
