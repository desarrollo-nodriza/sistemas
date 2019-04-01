<?php
App::uses('AppController', 'Controller');
class ProveedoresController extends AppController
{	
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		
		BreadcrumbComponent::add('Proveedores ');

		$proveedores	= $this->paginate();
		$this->set(compact('proveedores'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Proveedor->create();
			if ( $this->Proveedor->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Proveedores ', 'proveedores');
		BreadcrumbComponent::add('Agregar ');
	}

	public function admin_update()
	{
		$mensaje =  $this->actualizar_proveedores_base();

		$this->Session->setFlash($this->crearAlertaUl($mensaje), null, array(), 'warning');
		$this->redirect(array('action' => 'index'));

	}

	public function admin_edit($id = null)
	{	
		if ( ! $this->Proveedor->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	

			$this->Proveedor->MonedasProveedor->deleteAll(array('MonedasProveedor.proveedor_id' => $id));

			# Guardamos los emails en un objeto json
			if (isset($this->request->data['ProveedoresEmail'])) {
				$this->request->data['Proveedor']['meta_emails'] = json_encode($this->request->data['ProveedoresEmail'], true);
			}
			
			if ( $this->Proveedor->saveAll($this->request->data) )
			{	

				if ($this->request->data['Proveedor']['actualizar_canales']) {
					if ( ! $this->actualizar_proveedor($id, $this->request->data['Proveedor']['nombre']) ) {
						$this->Session->setFlash('No fue posible actualizar el proveedor en Prestashop', null, array(), 'warning');
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
			$this->request->data	= $this->Proveedor->find('first', array(
				'conditions'	=> array('Proveedor.id' => $id),
				'contain' => array(
					'Moneda'
				)
			));
		}
	
		$monedas = ClassRegistry::init('Moneda')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Proveedores ', '/roles');
		BreadcrumbComponent::add('Editar ');

		$this->set(compact('monedas'));

	}

	public function admin_delete($id = null)
	{
		$this->Proveedor->id = $id;
		if ( ! $this->Proveedor->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Proveedor->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Proveedor->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Proveedor->_schema);
		$modelo			= $this->Proveedor->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	public function actualizar_proveedor($id, $nombre) 
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

			if( !$this->Prestashop->prestashop_actualizar_proveedor($id, $nombre) ) {
				$return =  false;		
			}

		}

		return $return;		
	}

	/**
	 * Obtiene los proveedores y los agrega y/o actualiza los proveedores locales
	 * @return array 	Mensaje de la operación
	 */
	public function actualizar_proveedores_base()
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

			$proveedores = $this->Prestashop->prestashop_obtener_proveedores();
			
			$proveedoresLocales = array();
			$arrMessage 	  = array( 'No hay cambios disponibles.' );
			
			foreach ($proveedores['supplier'] as $ip => $p) {
				
				# Verificamos que exista en la BD local
				$local = $this->Proveedor->find('first', array('conditions' => array('id' => $p['id']), 'fields' => array('id')));

				# Crear proveedor
				if (empty($local)) {
					$proveedoresLocales[$ip]['Proveedor']['id'] = $p['id'];
					$proveedoresLocales[$ip]['Proveedor']['nombre'] = $p['name'];
				}
			}

		}

		if (!empty($proveedoresLocales)) {
				
			if ($this->Proveedor->saveMany($proveedoresLocales))
			{	

				$this->relacionarProveedorProductos($this->Prestashop, $proveedoresLocales);
				$arrMessage = array( sprintf('Se han creado/modificado %d proveedores', count($proveedoresLocales)) );
			}
		}
		
		return $arrMessage;
	}


	/**
	 * Permite relacionar los proveedores con los productos según la base de prestashop
	 * @param  obj $conexion instancia de prestashop   
	 * @param    $proveedores Arreglo de proveedores
	 * @return void
	 */
	private function relacionarProveedorProductos($conexion, $proveedores = array())
	{

		foreach ($proveedores as $i => $proveedor) {

			$filtroProductos = array(
				'filter[active]' => '[1]',
				'filter[id_supplier]' => '['.$proveedor['Proveedor']['id'].']'
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
							'codigo_proveedor' => $producto['supplier_reference']
						),
						'Proveedor' => array(
							'Proveedor' => $proveedor['Proveedor']['id']
						)
					);

					if (ClassRegistry::init('VentaDetalleProducto')->exists($producto['id'])){
						$this->Proveedor->VentaDetalleProducto->save($data);
					}

				}	
			}
		}

		return;
	}


	public function admin_obtenerProveedor($id = null)
	{	
		$res = array(
			'code' => 500,
			'message' => 'Error al procesar la solicitud',
			'data' => array()
		);

		$this->Proveedor->id = $id;
		if ( ! $this->Proveedor->exists() )
		{
			echo json_encode($res, true);
			exit;
		}

		$proveedor = $this->Proveedor->find('first', array(
			'conditions' => array(
				'Proveedor.id' => $id
			),
			'fields' => array(
				'id',
				'nombre',
				'email_contacto',
				'fono_contacto',
				'rut_empresa',
				'giro',
				'direccion',
				'nombre_encargado'
			)
		));

		$res = array(
			'code' => 200,
			'message' => 'Proveedor obtenido con éxito',
			'data' => $proveedor['Proveedor']
		);

		echo json_encode($res, true);
		exit;
	}
}
