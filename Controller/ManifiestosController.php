<?php
App::uses('AppController', 'Controller');

App::import('Controller', 'Dtes');

class ManifiestosController extends AppController {

	public function guardar_manifiesto($created = true)
	{	
		/*
		$ids =  Hash::extract($this->request->data['Venta'], '{n}.venta_id');

		$ventas          = $this->Manifiesto->Venta->find('all', array(
			'conditions' => array(
				'Venta.id' => $ids
			),
			'contain' => array(
				'Dte' => array(
					'fields' => array('Dte.id', 'Dte.tipo_documento')
				), 
				'Marketplace' => array(
					'fields' => array('Marketplace.nombre')
				), 
				'VentaCliente' => array(
					'fields' => array('VentaCliente.nombre', 'VentaCliente.email', 'VentaCliente.apellido')
				), 
				'VentaDetalle' => array(
					'fields' => array('VentaDetalle.id')
				), 
				'VentaEstado' => array(
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
						)
					),
					'fields' => array(
						'VentaEstado.id', 'VentaEstado.nombre'
					)
				),
				'Manifiesto' => array(
					'fields' => array(
						'Manifiesto.id'
					)
				)
			),	
			'order' => array('fecha_venta' => 'DESC')
		));*/


		$this->cambiarConfigDB($this->tiendaConf($this->Session->read('Tienda.id')));

		$ids =  Hash::extract($this->request->data['Orden'], '{n}.venta_id');

		$ordenes = $this->Manifiesto->Orden->find('all', array(
			'conditions' => array(
				'Orden.id_order' => $ids
			),
			'fields' => array(
				'Orden.id_order', 'Orden.reference', 'Orden.date_add', 'Orden.id_address_delivery'
			),
			'contain' => array(
				'Dte' => array(
					'conditions' => array('Dte.estado' => 'dte_real_emitido'),
					'fields' => array('Dte.id', 'Dte.tipo_documento', 'Dte.folio')
				), 
				'Cliente' => array(
					'fields' => array('Cliente.firstname', 'Cliente.email', 'Cliente.lastname')
				), 
				'OrdenDetalle' => array(
					'fields' => array('OrdenDetalle.id_order_detail', 'OrdenDetalle.product_quantity')
				), 
				'OrdenEstado' => array(
					'Lang' => array(
						'fields' => array(
							'Lang.name'
						)
					),
					'fields' => array(
						'OrdenEstado.id_order_state', 'OrdenEstado.color'
					)
				),
				'Manifiesto' => array(
					'fields' => array(
						'Manifiesto.id'
					)
				)
			),	
			'order' => array('date_add' => 'DESC')
		));

		$dataToSave['Manifiesto'] 	= $this->request->data['Manifiesto'];

		foreach ($ordenes as $io => $orden) {

			
			$dataToSave['Orden'][$io]['id_order']          = $orden['Orden']['id_order'];
			$dataToSave['Orden'][$io]['items']             = array_sum(Hash::extract($orden['OrdenDetalle'], '{n}.product_quantity'));
			$dataToSave['Orden'][$io]['referencia_pedido'] = $orden['Orden']['reference'];
			$dataToSave['Orden'][$io]['id_order']          = $orden['Orden']['id_order'];
			$dataToSave['Orden'][$io]['folio_dte']         = 0;
			$dataToSave['Orden'][$io]['tipo_dte']          = 'Vacio';
			$dataToSave['Orden'][$io]['nombre_receptor']   = 'Vacio';
			$dataToSave['Orden'][$io]['fono_receptor']     = 'Vacio';
			$dataToSave['Orden'][$io]['direcion_envio']    = 'Vacio';
			$dataToSave['Orden'][$io]['comuna']            = 'Vacio';

			if (!empty($orden['Dte'])) {

				$dte = new DtesController();

				$dataToSave['Orden'][$io]['folio_dte'] = $orden['Dte'][0]['folio'];
				$dataToSave['Orden'][$io]['tipo_dte'] = $dte->tipoDocumento[$orden['Dte'][0]['tipo_documento']];
			}
			
			$direccionEnvio = ClassRegistry::init('Clientedireccion')->find('first', array(
				'fields' => array(
					'Clientedireccion.firstname', 'Clientedireccion.lastname', 'Clientedireccion.address1', 'Clientedireccion.address2', 'Clientedireccion.other', 'Clientedireccion.city', 'Clientedireccion.phone', 'Clientedireccion.phone_mobile'
				),
				'conditions' => array(
					'Clientedireccion.id_address' => $orden['Orden']['id_address_delivery']
				),
				'contain' => array(
					'Region' => array(
						'fields' => array('Region.name')
					)
				)
			));
			
			if (!empty($direccionEnvio)) {
				$dataToSave['Orden'][$io]['nombre_receptor'] = $direccionEnvio['Clientedireccion']['firstname'] . ' ' . $direccionEnvio['Clientedireccion']['lastname'];
				$dataToSave['Orden'][$io]['fono_receptor']   = $direccionEnvio['Clientedireccion']['phone'] . ', ' . $direccionEnvio['Clientedireccion']['phone_mobile'];
				$dataToSave['Orden'][$io]['direcion_envio']  = $direccionEnvio['Clientedireccion']['address1'] . ', ' . $direccionEnvio['Clientedireccion']['address2'] . '. ' . $direccionEnvio['Clientedireccion']['city'];
				$dataToSave['Orden'][$io]['comuna']          = $direccionEnvio['Region']['name'];
			}
			
		}		
			
		if ($created) {
			$this->Manifiesto->create();	
		}

		if ($this->Manifiesto->saveAll($dataToSave, array('deep' => true))) {
			$this->Session->setFlash('Manifiesto agregado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash('Error al guardar el manifiesto. Por favor intenta nuevamente.', null, array(), 'danger');
			$this->redirect(array('action' => $this->request->params['action']));
		}
	}

	public function admin_index() {

		$this->paginate		= array(
			'recursive'	=> 0,
			'order' => array('Manifiesto.created' => 'DESC')
		);
		$manifiestos = $this->paginate();
		
		BreadcrumbComponent::add('Manifiestos ');
		$this->set(compact('manifiestos'));
	
	}


	public function admin_obtener_ordenes_ajax()
	{	
		$this->layout = 'ajax';

		$this->cambiarConfigDB($this->tiendaConf($this->Session->read('Tienda.id')));

		$fecha_actual = date("Y-m-d H:i:s");
		$hace_un_mes  = date("Y-m-d H:i:s",strtotime($fecha_actual."- 1 months")); 

		$ordenes = $this->Manifiesto->Orden->find('all', array(
			'conditions' => array(
				'Orden.date_add BETWEEN ? AND ?' => array($hace_un_mes, $fecha_actual)
			),
			'fields' => array(
				'Orden.id_order', 'Orden.reference', 'Orden.date_add', 'Orden.total_paid_real'
			),
			'contain' => array(
				'Dte' => array(
					'fields' => array('Dte.id', 'Dte.tipo_documento')
				), 
				'Cliente' => array(
					'fields' => array('Cliente.firstname', 'Cliente.email', 'Cliente.lastname')
				), 
				'OrdenDetalle' => array(
					'fields' => array('OrdenDetalle.id_order_detail')
				), 
				'OrdenEstado' => array(
					'Lang' => array(
						'fields' => array(
							'Lang.name'
						)
					),
					'fields' => array(
						'OrdenEstado.id_order_state', 'OrdenEstado.color'
					)
				),
				'Manifiesto' => array(
					'fields' => array(
						'Manifiesto.id'
					)
				)
			),	
			'limit' => $this->request->query['limit'],
			'offset' => $this->request->query['offset'],
			'order' => array('date_add' => 'DESC')
		));

		if (empty($ordenes)) {
			echo 0;
			exit;
		}

		$obtenerRelacionados = array();

		if (isset($this->request->query['id'])) {
			$relacionados =  $this->Manifiesto->ManifiestosVenta->find('all', array(
				'conditions' => array(
					'ManifiestosVenta.manifiesto_id' => $this->request->query['id']
				),
				'fields' => array('ManifiestosVenta.id_order')
			));

			$obtenerRelacionados = Hash::extract($relacionados, '{n}.ManifiestosVenta.id_order');
		}

			

		foreach ($ordenes as $io => $orden) {

			$ordenes[$io]['Orden']['selected'] = false;

			if (isset($this->request->query['id'])) {
				if (in_array($orden['Orden']['id_order'], $obtenerRelacionados)) {
					$ordenes[$io]['Orden']['selected'] = true;
				}
			}	
		}
		
		$this->set(compact('ordenes'));
	}


	public function admin_add() 
	{
		if ($this->request->is('post')) {
			$this->guardar_manifiesto();
		}
		
		$transportes     = $this->Manifiesto->Transporte->find('list');
		$administradores = $this->Manifiesto->Administrador->find('list');
		$tiendas         = $this->Manifiesto->Tienda->find('list');
		//$ventas          = $this->Manifiesto->Venta->find('list');
		$ordenes         = $this->Manifiesto->Orden->find('list');

		
		
		BreadcrumbComponent::add('Manifiestos ', '/manifiestos');
		BreadcrumbComponent::add('Nuevo Manifiesto ');
		//prx($ventas);
		$this->set(compact('transportes', 'administradores', 'tiendas', 'ventas', 'ordenes'));
	}


	public function admin_edit($id = null)
	{
		if ( ! $this->Manifiesto->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	

			$this->Manifiesto->ManifiestosVenta->deleteAll(array('manifiesto_id' => $id));

			$this->guardar_manifiesto();

		}
		else
		{
			$this->request->data	= $this->Manifiesto->find('first', array(
				'conditions'	=> array('Manifiesto.id' => $id),
				'contain' => array('Transporte', 'Administrador', 'Tienda', 'Venta', 'Orden')
			));
		}

		$transportes     = $this->Manifiesto->Transporte->find('list');
		$administradores = $this->Manifiesto->Administrador->find('list');
		$tiendas         = $this->Manifiesto->Tienda->find('list');
		//$ventas          = $this->Manifiesto->Venta->find('list');
		$ordenes         = $this->Manifiesto->Orden->find('list');

		BreadcrumbComponent::add('Manifiestos ', '/manifiestos');
		BreadcrumbComponent::add('Editar Manifiesto ');

		$this->set(compact('transportes', 'administradores', 'tiendas', 'ventas', 'ordenes'));
	}


	public function admin_delete($id = null) 
	{
		$this->Manifiesto->id = $id;
		if (!$this->Manifiesto->exists()) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->allowMethod('post', 'delete');
		if ($this->Manifiesto->delete()) {
			$this->Session->setFlash('Registro eliminado correctamente', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		} else {
			$this->Session->setFlash('No fue posible eliminar este registro', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function admin_finish($id = null)
	{
		$this->Manifiesto->id = $id;
		if (!$this->Manifiesto->exists()) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		// Actualizamos estados
		$this->Manifiesto->saveField('entregado', 1);
		$this->Manifiesto->saveField('fecha_entregado', date('Y-m-d H:i:s'));


		$this->Session->setFlash('Manifiesto procesado con éxito.', null, array(), 'success');
		$this->redirect(array('action' => 'index'));

	}


	public function admin_view($id = null) 
	{
		$this->Manifiesto->id = $id;
		if (!$this->Manifiesto->exists()) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->Manifiesto->saveField('impreso', 1);

		$manifiesto = $this->Manifiesto->find('first', array(
			'conditions' => array(
				'Manifiesto.id' => $id
			),
			'contain' => array(
				'Transporte',
				'Orden' => array(
					'ManifiestosVenta',
					'order' => array('Orden.date_add' => 'DESC'),
					'fields' => array('Orden.id_order')
				)
			)
		));

		
		$campos = array(
			'N° Documento',
			'Cód Referencia',
			'OT Transporte',
			'N° folio',
			'T documento',
			'Nombre',
			'Fonos',
			'Dirección',
			'Comuna',
			'Recepticón física'
		);

		$modelo = $this->Manifiesto->alias;

		$datos = array();
		
		foreach ($manifiesto['Orden'] as $io => $detalle) {
			
			if (!empty($detalle['ManifiestosVenta'])) {
				$datos[$io]['Manifiesto']['n_documento']    = $manifiesto['Manifiesto']['id'];
				$datos[$io]['Manifiesto']['cod_referencia'] = $detalle['ManifiestosVenta']['referencia_pedido'];
				$datos[$io]['Manifiesto']['ot_transporte']  = (!empty($manifiesto['Manifiesto']['ot_manual'])) ? $manifiesto['Manifiesto']['ot_manual'] : 0 ;
				$datos[$io]['Manifiesto']['n_folio']        = $detalle['ManifiestosVenta']['folio_dte'];
				$datos[$io]['Manifiesto']['t_documento']    = $detalle['ManifiestosVenta']['tipo_dte'];	
				$datos[$io]['Manifiesto']['nombre']         = $detalle['ManifiestosVenta']['nombre_receptor'];	
				$datos[$io]['Manifiesto']['fono']           = $detalle['ManifiestosVenta']['fono_receptor'];	
				$datos[$io]['Manifiesto']['direccion']      = $detalle['ManifiestosVenta']['direcion_envio'];	
				$datos[$io]['Manifiesto']['comuna']         = strtoupper($detalle['ManifiestosVenta']['comuna']);
				$datos[$io]['Manifiesto']['f_recepcion']    = $manifiesto['Manifiesto']['fecha_entregado'];
				//$datos[$io]['Manifiesto']['transporte']     = $manifiesto['Transporte']['nombre'];
			}

		}

		/*try {
				
		} catch (Exception $e) {
			$this->Session->setFlash('Error:' . $e->getMessage(), null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}*/
		
		

		$this->set(compact('datos', 'campos', 'modelo'));

	}


	public function admin_view_pdf($id = null)
	{
		$this->Manifiesto->id = $id;
		if (!$this->Manifiesto->exists()) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->Manifiesto->saveField('impreso', 1);

		$manifiesto = $this->Manifiesto->find('first', array(
			'conditions' => array(
				'Manifiesto.id' => $id
			),
			'contain' => array(
				'Transporte',
				'Orden' => array(
					'ManifiestosVenta',
					'order' => array('Orden.date_add' => 'DESC'),
					'fields' => array('Orden.id_order')
				)
			)
		));

		
		$campos = array(
			'N° Documento',
			'Cód Referencia',
			'OT Transporte',
			'N° folio',
			'T documento',
			'Nombre',
			'Fonos',
			'Dirección',
			'Comuna',
			'Recepticón física'
		);

		$modelo = $this->Manifiesto->alias;

		$datos = array();
		
		foreach ($manifiesto['Orden'] as $io => $detalle) {
			
			if (!empty($detalle['ManifiestosVenta'])) {
				$datos[$io]['Manifiesto']['n_documento']    = $manifiesto['Manifiesto']['id'];
				$datos[$io]['Manifiesto']['cod_referencia'] = $detalle['ManifiestosVenta']['referencia_pedido'];
				$datos[$io]['Manifiesto']['ot_transporte']  = (!empty($manifiesto['Manifiesto']['ot_manual'])) ? $manifiesto['Manifiesto']['ot_manual'] : 0 ;
				$datos[$io]['Manifiesto']['n_folio']        = $detalle['ManifiestosVenta']['folio_dte'];
				$datos[$io]['Manifiesto']['t_documento']    = $detalle['ManifiestosVenta']['tipo_dte'];	
				$datos[$io]['Manifiesto']['nombre']         = $detalle['ManifiestosVenta']['nombre_receptor'];	
				$datos[$io]['Manifiesto']['fono']           = $detalle['ManifiestosVenta']['fono_receptor'];	
				$datos[$io]['Manifiesto']['direccion']      = $detalle['ManifiestosVenta']['direcion_envio'];	
				$datos[$io]['Manifiesto']['comuna']         = strtoupper($detalle['ManifiestosVenta']['comuna']);
				$datos[$io]['Manifiesto']['f_recepcion']    = $manifiesto['Manifiesto']['fecha_entregado'];
				//$datos[$io]['Manifiesto']['transporte']     = $manifiesto['Transporte']['nombre'];
			}

		}
		
		$this->generar_pdf($datos, $campos, $manifiesto);

	}



	private function generar_pdf($datos, $campos, $manifiesto)
	{	

		$nombreArchivo   = 'manifiesto_' . $manifiesto['Manifiesto']['id'] . '_' . Inflector::slug($manifiesto['Manifiesto']['created']) . '.pdf';
		
		# Ruta para guardar en la Base de manifiesto
		$archivo         = Router::url('/', true) . 'Pdf/Manifiestos/' . $manifiesto['Manifiesto']['id'] . '/' . $nombreArchivo;
		
		# Ruta para guardar PDF
		$archivoAbsoluto = APP . 'webroot' . DS . 'Pdf' . DS . 'Manifiestos' . DS . $manifiesto['Manifiesto']['id'] . DS . $nombreArchivo;
		
		App::uses('CakePdf', 'Plugin/CakePdf/Pdf');

		if (!file_exists($archivoAbsoluto)) {
			$this->CakePdf = new CakePdf();
			$this->CakePdf->template('manifiesto', 'default');
			$this->CakePdf->viewVars(compact('datos', 'campos', 'manifiesto'));
			$this->CakePdf->write($archivoAbsoluto);	
		}

		header('Location: ' . $archivo);
		exit;
	}
}
