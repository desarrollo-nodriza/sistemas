<?php
App::uses('AppController', 'Controller');

App::import('Controller', 'Dtes');

class ManifiestosController extends AppController {

	public $components = array(
		'Prestashop',
		'Conexxion'
	);

	public function guardar_manifiesto($created = true)
	{	
		$ids =  Hash::extract($this->request->data, 'Venta.{n}.venta_id');

		$ventas          = $this->Manifiesto->Venta->find('all', array(
			'conditions' => array(
				'Venta.id' => $ids
			),
			'contain' => array(
				'Dte' => array(
					'fields' => array('Dte.id', 'Dte.tipo_documento', 'Dte.folio'),
					'conditions' => array(
						'Dte.estado'         => 'dte_real_emitido',
						'Dte.invalidado'     => 0,
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
					'VentaDetalleProducto' => array(
						'fields' => array(
							'VentaDetalleProducto.peso'
						)
					),
					'fields' => array('VentaDetalle.id', 'VentaDetalle.cantidad', 'VentaDetalle.cantidad_anulada', 'VentaDetalle.cantidad_entregada', 'VentaDetalle.cantidad_en_espera')
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

		if (empty($ventas)) {
			$this->Session->setFlash('Error al guardar el manifiesto. Por favor seleccione ventas.', null, array(), 'danger');
			$this->redirect(array('action' => $this->request->params['action']));
		}

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
			$dataToSave['Venta'][$io]['items']             = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_anulada')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_en_espera')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_entregada'));
			$dataToSave['Venta'][$io]['referencia_pedido'] = $venta['Venta']['referencia'];
			$dataToSave['Venta'][$io]['peso_bulto']        = round(array_sum(Hash::extract($venta['VentaDetalle'], '{n}.VentaDetalleProducto.peso')), 1);
			$dataToSave['Venta'][$io]['id']                = $venta['Venta']['id'];
			$dataToSave['Venta'][$io]['folio_dte']         = 0;
			$dataToSave['Venta'][$io]['tipo_dte']          = 'Vacio';
			$dataToSave['Venta'][$io]['nombre_receptor']   = $venta['Venta']['nombre_receptor'];
			$dataToSave['Venta'][$io]['rut_receptor']      = formato_rut($venta['Venta']['rut_receptor']);
			$dataToSave['Venta'][$io]['fono_receptor']   = $venta['Venta']['fono_receptor'];
			$dataToSave['Venta'][$io]['email_receptor']  = $venta['VentaCliente']['email'];
			$dataToSave['Venta'][$io]['direcion_envio']  = $venta['Venta']['direccion_entrega'] . ' ' . $venta['Venta']['numero_entrega'];
			$dataToSave['Venta'][$io]['dpto_receptor']   = $venta['Venta']['otro_entrega'];
			$dataToSave['Venta'][$io]['comuna']          = $venta['Venta']['comuna_entrega'];

			if (!empty($venta['Dte'])) {

				$dte = new DtesController();

				$dataToSave['Venta'][$io]['folio_dte'] = $venta['Dte'][0]['folio'];
				$dataToSave['Venta'][$io]['tipo_dte'] = $dte->tipoDocumento[$venta['Dte'][0]['tipo_documento']];
			}
			
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
			'recursive'	=> -1,
			'contain' => array(
				'Transporte' => array(
					'fields' => array(
						'Transporte.nombre'
					)
				),
				'Tienda' => array(
					'fields' => array(
						'Tienda.nombre'
					)
				),
				'Administrador' => array(
					'fields' => array(
						'Administrador.nombre'
					)
				),
				'Venta' => array(
					'fields' => array(
						'id'
					)
				)
			),
			'order' => array('Manifiesto.created' => 'DESC'),
			'fields' => array(
				'Manifiesto.id',
				'Manifiesto.tienda_id',
				'Manifiesto.administrador_id',
				'Manifiesto.transporte_id',
				'Manifiesto.entregado',
				'Manifiesto.impreso',
				'Manifiesto.fecha_entregado',
				'Manifiesto.modified'
			)
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
				'Venta.fecha_venta BETWEEN ? AND ?' => array($hace_un_mes, $fecha_actual),
			),
			'fields' => array(
				'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida'
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
		$comunas         = ClassRegistry::init('Comuna')->find('list', array('order' => array('Comuna.nombre' => 'ASC')));
		
		$tipo_productos   = $this->Conexxion->obtener_tipo_productos_excel();
		$tamano_productos = $this->Conexxion->obtener_tamanos_excel();
		$tipo_retornos    = $this->Conexxion->obtener_tipo_retornos_excel();

		BreadcrumbComponent::add('Manifiestos ', '/manifiestos');
		BreadcrumbComponent::add('Nuevo Manifiesto ');

		$this->set(compact('transportes', 'administradores', 'tiendas', 'ventas', 'comunas', 'tipo_productos', 'tamano_productos', 'tipo_retornos'));
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
		$comunas         = ClassRegistry::init('Comuna')->find('list', array('order' => array('Comuna.nombre' => 'ASC')));
		
		$tipo_productos   = $this->Conexxion->obtener_tipo_productos_excel();
		$tamano_productos = $this->Conexxion->obtener_tamanos_excel();
		$tipo_retornos    = $this->Conexxion->obtener_tipo_retornos_excel();

		BreadcrumbComponent::add('Manifiestos ', '/manifiestos');
		BreadcrumbComponent::add('Editar Manifiesto ');

		$this->set(compact('transportes', 'administradores', 'tiendas', 'ventas', 'comunas', 'tipo_productos', 'tamano_productos', 'tipo_retornos'));
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
			'Observacion',
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



	public function admin_view_conexxion($id = null) 
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
				'Comuna' => array(
					'fields' => array(
						'Comuna.nombre'
					)
				),
				'Transporte',
				'Tienda' => array(
					'fields' => array(
						'Tienda.rut'
					)
				),
				'Venta' => array(
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
			'Remitente',
			'Email remitente',
			'RUT remitente',
			'Teléfono remitente',
			'Dirección remitente',
			'Dpto/Oficina remitente',
			'Comuna remitente',
			'Destinatario',
			'Email destinatario',
			'RUT destinatario',
			'Teléfono destinatario',
			'Dirección destinatario',
			'Dpto/Oficina destinatario',
			'Comuna destinatario',
			'Comentario',
			'Retorno',
			'Producto',
			'Servicio',
			'Tramo',
			'Descripción de Producto'
		);

		$modelo = $this->Manifiesto->alias;

		$datos = array();
		
		# Preparamos los datos
		foreach ($manifiesto['Venta'] as $io => $detalle) {
			
			$datos[$io]['Manifiesto']['solicitante']           = $manifiesto['Manifiesto']['nombre_solicitante'];
			$datos[$io]['Manifiesto']['email_solicitante']     = $manifiesto['Manifiesto']['email_solicitante'];
			$datos[$io]['Manifiesto']['rut_solicitante']       = formato_rut($manifiesto['Tienda']['rut']);
			$datos[$io]['Manifiesto']['fono_solicitante']      = $manifiesto['Manifiesto']['fono_solicitante'];
			$datos[$io]['Manifiesto']['direccion_solicitante'] = $manifiesto['Manifiesto']['direccion_solicitante'];
			$datos[$io]['Manifiesto']['dpto_solicitante']      = '';
			$datos[$io]['Manifiesto']['comuna_solicitante']    = (!empty($manifiesto['Comuna'])) ? $manifiesto['Comuna']['nombre'] : '';
			
			if (!empty($detalle['ManifiestosVenta'])) {
				$datos[$io]['Manifiesto']['contacto_destino']  = $detalle['ManifiestosVenta']['nombre_receptor'];
				$datos[$io]['Manifiesto']['email_destino']     = $detalle['ManifiestosVenta']['email_receptor'];
				$datos[$io]['Manifiesto']['rut_destino']       = formato_rut($detalle['ManifiestosVenta']['rut_receptor']);
				$datos[$io]['Manifiesto']['fono_destino']      = $detalle['ManifiestosVenta']['fono_receptor'];
				$datos[$io]['Manifiesto']['direccion_destino'] = $detalle['ManifiestosVenta']['direcion_envio'];
				$datos[$io]['Manifiesto']['dpto_destino']      = $detalle['ManifiestosVenta']['dpto_receptor'];
				$datos[$io]['Manifiesto']['comuna_destino']    = $detalle['ManifiestosVenta']['comuna'];
				$datos[$io]['Manifiesto']['comentario']        = $detalle['ManifiestosVenta']['observacion'];				
			}

			$datos[$io]['Manifiesto']['tipo_retorno']    = $manifiesto['Manifiesto']['tipo_retorno'];
			$datos[$io]['Manifiesto']['tamano_producto'] = $manifiesto['Manifiesto']['tamano_producto'];
			$datos[$io]['Manifiesto']['tipo_producto']   = $manifiesto['Manifiesto']['tipo_producto'];
			
			if (!empty($detalle['ManifiestosVenta'])) {
				$datos[$io]['Manifiesto']['tramo'] = $this->Conexxion->obtener_tramo_por_peso( $detalle['ManifiestosVenta']['peso_bulto'], $manifiesto['Manifiesto']['tamano_producto']);
			}

			$datos[$io]['Manifiesto']['descripcion'] = $detalle['ManifiestosVenta']['folio_dte'];

		}

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
