<?php
App::uses('AppController', 'Controller');

App::import('Controller', 'Dtes');

class ManifiestosController extends AppController {

	public $components = array(
		'Prestashop'
	);

	public function guardar_manifiesto($created = true)
	{	
		$ids =  Hash::extract($this->request->data['Venta'], '{n}.venta_id');

		$ventas          = $this->Manifiesto->Venta->find('all', array(
			'conditions' => array(
				'Venta.id' => $ids
			),
			'contain' => array(
				'Dte' => array(
					'fields' => array('Dte.id', 'Dte.tipo_documento', 'Dte.folio'),
					'conditions' => array(
						'Dte.estado' => 'dte_real_emitido',
						'Dte.tipo_documento' => array(33, 39)
					)
				), 
				'Marketplace' => array(
					'fields' => array('Marketplace.nombre')
				), 
				'VentaCliente' => array(
					'fields' => array('VentaCliente.nombre', 'VentaCliente.email', 'VentaCliente.apellido')
				), 
				'VentaDetalle' => array(
					'fields' => array('VentaDetalle.id', 'VentaDetalle.cantidad')
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
				'Marketplace' => array(
					'fields' => array(
						'Marketplace.id'
					)
				),
				'Manifiesto' => array(
					'fields' => array(
						'Manifiesto.id'
					)
				),
				'Tienda' => array(
					'fields' => array(
						'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop'
					)
				)
			),	
			'order' => array('fecha_venta' => 'DESC')
		));




		/*$this->cambiarConfigDB($this->tiendaConf($this->Session->read('Tienda.id')));

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
		));*/

		$dataToSave['Manifiesto'] 	= $this->request->data['Manifiesto'];
		
		foreach ($ventas as $io => $venta) {

			//----------------------------------------------------------------------------------------------------
			//carga de mensajes de prestashop
			if (empty($venta['Marketplace']['id'])) {

				$this->Prestashop->crearCliente($venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop'], false);

				$ventas[$io]['VentaMensaje'] = $this->Prestashop->prestashop_obtener_venta_mensajes($venta['Venta']['id_externo']);

			}else {
				
				$ventas[$io]['VentaMensaje'] = array();

			}

			
			$dataToSave['Venta'][$io]['venta_id']          = $venta['Venta']['id'];
			$dataToSave['Venta'][$io]['items']             = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad'));
			$dataToSave['Venta'][$io]['referencia_pedido'] = $venta['Venta']['referencia'];
			$dataToSave['Venta'][$io]['id']          		= $venta['Venta']['id'];
			$dataToSave['Venta'][$io]['folio_dte']         = 0;
			$dataToSave['Venta'][$io]['tipo_dte']          = 'Vacio';
			$dataToSave['Venta'][$io]['nombre_receptor']   = 'Vacio';
			$dataToSave['Venta'][$io]['fono_receptor']     = 'Vacio';
			$dataToSave['Venta'][$io]['direcion_envio']    = 'Vacio';
			$dataToSave['Venta'][$io]['comuna']            = 'Vacio';

			if (!empty($venta['Dte'])) {

				$dte = new DtesController();

				$dataToSave['Venta'][$io]['folio_dte'] = $venta['Dte'][0]['folio'];
				$dataToSave['Venta'][$io]['tipo_dte'] = $dte->tipoDocumento[$venta['Dte'][0]['tipo_documento']];
			}
			
			$dataToSave['Venta'][$io]['nombre_receptor'] = $venta['Venta']['nombre_receptor'];
			$dataToSave['Venta'][$io]['fono_receptor']   = $venta['Venta']['fono_receptor'];
			$dataToSave['Venta'][$io]['direcion_envio']  = $venta['Venta']['direccion_entrega'];
			$dataToSave['Venta'][$io]['comuna']          = $venta['Venta']['comuna_entrega'];
			
			if (!empty($venta['VentaMensaje'])) {
				$dataToSave['Venta'][$io]['observacion'] = $this->crearAlertaUl(Hash::extract($venta['VentaMensaje'], '{n}.mensaje'), '');
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

		//$this->cambiarConfigDB($this->tiendaConf($this->Session->read('Tienda.id')));

		$fecha_actual = date("Y-m-d H:i:s");
		$hace_un_mes  = date("Y-m-d H:i:s",strtotime($fecha_actual."- 1 months")); 

		$ventas          = $this->Manifiesto->Venta->find('all', array(
			'conditions' => array(
				'Venta.fecha_venta BETWEEN ? AND ?' => array($hace_un_mes, $fecha_actual)
			),
			'fields' => array(
				'Venta.id', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total'
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
						'VentaEstado.id', 'VentaEstado.nombre', 'VentaEstado.permitir_manifiesto'
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
			'order' => array('Venta.fecha_venta' => 'DESC')
		));

		if (empty($ventas)) {
			echo 0;
			exit;
		}

		$obtenerRelacionados = array();

		if (isset($this->request->query['id'])) {
			$relacionados =  $this->Manifiesto->ManifiestosVenta->find('all', array(
				'conditions' => array(
					'ManifiestosVenta.manifiesto_id' => $this->request->query['id']
				),
				'fields' => array('ManifiestosVenta.venta_id')
			));

			$obtenerRelacionados = Hash::extract($relacionados, '{n}.ManifiestosVenta.venta_id');
		}

			

		foreach ($ventas as $io => $orden) {

			$ventas[$io]['Venta']['selected'] = false;

			if (isset($this->request->query['id'])) {
				if (in_array($orden['Venta']['id'], $obtenerRelacionados)) {
					$ventas[$io]['Venta']['selected'] = true;
				}
			}	

			if (!empty($ventas[$io]['VentaEstado'])) {
				if (!$ventas[$io]['VentaEstado']['permitir_manifiesto']) {
					unset($ventas[$io]);
				}
			}
		}
		
		$this->set(compact('ventas'));
	}


	public function admin_add() 
	{
		if ($this->request->is('post')) {
			$this->guardar_manifiesto();
		}
		
		$transportes     = $this->Manifiesto->Transporte->find('list');
		$administradores = $this->Manifiesto->Administrador->find('list');
		$tiendas         = $this->Manifiesto->Tienda->find('list');
		$ventas          = $this->Manifiesto->Venta->find('list');
		//$ordenes         = $this->Manifiesto->Orden->find('list');

		
		
		BreadcrumbComponent::add('Manifiestos ', '/manifiestos');
		BreadcrumbComponent::add('Nuevo Manifiesto ');
		//prx($ventas);
		$this->set(compact('transportes', 'administradores', 'tiendas', 'ventas'));
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
				'contain' => array('Transporte', 'Administrador', 'Tienda', 'Venta')
			));
		}

		$transportes     = $this->Manifiesto->Transporte->find('list');
		$administradores = $this->Manifiesto->Administrador->find('list');
		$tiendas         = $this->Manifiesto->Tienda->find('list');
		$ventas          = $this->Manifiesto->Venta->find('list');
		//$ordenes         = $this->Manifiesto->Orden->find('list');

		BreadcrumbComponent::add('Manifiestos ', '/manifiestos');
		BreadcrumbComponent::add('Editar Manifiesto ');

		$this->set(compact('transportes', 'administradores', 'tiendas', 'ventas'));
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
				'Venta' => array(
					'ManifiestosVenta',
					'order' => array(
						'Venta.fecha_venta' => 'DESC'
					),
					'fields' => array(
						'Venta.id'
					)
				)
			)
		));
		
		$campos = array(
			'ID',
			'Cód Referencia',
			'OT Transporte',
			'N° folio',
			'T documento',
			'Nombre',
			'Teléfono',
			'Dirección',
			'Dpto',
			'Comuna',
			'Recepticón física',
			'Observacion'
		);

		$modelo = $this->Manifiesto->alias;

		$datos = array();
		
		foreach ($manifiesto['Venta'] as $io => $detalle) {
			
			if (!empty($detalle['ManifiestosVenta'])) {
				$datos[$io]['Manifiesto']['n_documento']    = $manifiesto['Manifiesto']['id'];
				$datos[$io]['Manifiesto']['cod_referencia'] = $detalle['ManifiestosVenta']['referencia_pedido'];
				$datos[$io]['Manifiesto']['ot_transporte']  = (!empty($manifiesto['Manifiesto']['ot_manual'])) ? $manifiesto['Manifiesto']['ot_manual'] : 0 ;
				$datos[$io]['Manifiesto']['n_folio']        = $detalle['ManifiestosVenta']['folio_dte'];
				$datos[$io]['Manifiesto']['t_documento']    = $detalle['ManifiestosVenta']['tipo_dte'];	
				$datos[$io]['Manifiesto']['nombre']         = $detalle['ManifiestosVenta']['nombre_receptor'];	
				$datos[$io]['Manifiesto']['fono']           = $detalle['ManifiestosVenta']['fono_receptor'];	
				$datos[$io]['Manifiesto']['direccion']      = $detalle['ManifiestosVenta']['direcion_envio'];	
				$datos[$io]['Manifiesto']['dpto']           = '';	
				$datos[$io]['Manifiesto']['comuna']         = strtoupper($detalle['ManifiestosVenta']['comuna']);
				$datos[$io]['Manifiesto']['f_recepcion']    = $manifiesto['Manifiesto']['fecha_entregado'];
				$datos[$io]['Manifiesto']['observacion']    = $detalle['ManifiestosVenta']['observacion'];
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
				'Venta' => array(
					'ManifiestosVenta',
					'order' => array(
						'Venta.fecha_venta' => 'DESC'
					),
					'fields' => array(
						'Venta.id'
					)
				)
			)
		));

		
		$campos = array(
			'ID',
			'Cód Referencia',
			'OT Transporte',
			'N° folio',
			'T documento',
			'Nombre',
			'Teléfono',
			'Dirección',
			'Dpto',
			'Comuna',
			'Recepticón física',
			'Observacion'
		);

		$modelo = $this->Manifiesto->alias;

		$datos = array();
		
		foreach ($manifiesto['Venta'] as $io => $detalle) {
			
			if (!empty($detalle['ManifiestosVenta'])) {
				$datos[$io]['Manifiesto']['n_documento']    = $manifiesto['Manifiesto']['id'];
				$datos[$io]['Manifiesto']['cod_referencia'] = $detalle['ManifiestosVenta']['referencia_pedido'];
				$datos[$io]['Manifiesto']['ot_transporte']  = (!empty($manifiesto['Manifiesto']['ot_manual'])) ? $manifiesto['Manifiesto']['ot_manual'] : 0 ;
				$datos[$io]['Manifiesto']['n_folio']        = $detalle['ManifiestosVenta']['folio_dte'];
				$datos[$io]['Manifiesto']['t_documento']    = $detalle['ManifiestosVenta']['tipo_dte'];	
				$datos[$io]['Manifiesto']['nombre']         = $detalle['ManifiestosVenta']['nombre_receptor'];	
				$datos[$io]['Manifiesto']['fono']           = $detalle['ManifiestosVenta']['fono_receptor'];	
				$datos[$io]['Manifiesto']['direccion']      = $detalle['ManifiestosVenta']['direcion_envio'];	
				$datos[$io]['Manifiesto']['dpto']           = '';	
				$datos[$io]['Manifiesto']['comuna']         = strtoupper($detalle['ManifiestosVenta']['comuna']);
				$datos[$io]['Manifiesto']['f_recepcion']    = $manifiesto['Manifiesto']['fecha_entregado'];
				$datos[$io]['Manifiesto']['observacion']    = $detalle['ManifiestosVenta']['observacion'];
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
