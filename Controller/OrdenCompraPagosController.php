<?php
App::uses('AppController', 'Controller');
class OrdenCompraPagosController extends AppController
{
	public function admin_index()
	{	
		$pa = $this->OrdenCompraPago->pagos_pendiente_dte();

		$this->paginate		= array(
			'recursive'			=> 0
		);
		BreadcrumbComponent::add('Pagos ');

		$pagos	= $this->paginate();
		$this->set(compact('pagos'));
	}


	public function admin_calendario()
	{	

		if ($this->request->is('post')) {
			
			$alerta = array();
			
			
			foreach ($this->request->data['OrdenCompraPago'] as $i => $pago) {

				# Re agendar pago
				$savePago = array(
					'OrdenCompraPago' => $pago
				);

				$facturasPagadas = array();
				
				if ($pago['fecha_pago'] <= date('Y-m-d')) {

					if (!isset($pago['OrdenCompraFactura'])) {
						$alerta['errors'][] = 'Debe relacionar una factura para marcar como pago finalizado.';
						continue;
					}

					# Nuevo monto real pagado
					$pago['monto_real_pagado'] = $pago['monto_pendiente'];

					# Finalizamos el pago
					if (array_sum(Hash::extract($pago['OrdenCompraFactura'], '{n}.monto_pagado')) >= $pago['monto_real_pagado'] && $pago['monto_real_pagado'] == $pago['monto_pagado']) {
						$pago['finalizado'] = 1;
					}

					foreach ($pago['OrdenCompraFactura'] as $ip => $f) {

						# quitamos las relaciones malas
						if ($f['monto_pagado'] == 0 || $f['monto_pagado'] == null) {
							unset($pago['OrdenCompraFactura'][$ip]);
							continue;
						}


						if (!isset($f['id'])) {
							unset($pago['OrdenCompraFactura'][$ip]);
							continue;
						}

						# obtenemos la factura
						$factura = ClassRegistry::init('OrdenCompraFactura')->find('first', array(
							'conditions' => array(
								'OrdenCompraFactura.id' => $f['orden_compra_factura_id']
							),
							'fields' => array(
								'OrdenCompraFactura.monto_facturado',
								'OrdenCompraFactura.monto_pagado',
								'OrdenCompraFactura.id'
							)
						));

						# Finalizamos la factura si corresponde
						if ($factura['OrdenCompraFactura']['monto_facturado'] <= $f['monto_pagado']) {
							$factura['OrdenCompraFactura']['pagada'] = 1;
						}


						$facturasPagadas[] = array(
							'orden_compra_factura_id' => $f['id'],
							'monto_pagado' => $f['monto_pagado']
						);

						#gurdamos la factura
						$factura['OrdenCompraFactura']['monto_pagado'] 	 = $factura['OrdenCompraFactura']['monto_pagado'] + $f['monto_pagado'];						

					}


					$facturasOc = ClassRegistry::init('OrdenCompraFacturasPago')->find('all', array(
						'conditions' => array(
							'OrdenCompraFacturasPago.orden_compra_pago_id' => $pago['id']
						),
						'fields' => array(
							'OrdenCompraFacturasPago.orden_compra_factura_id',
							'OrdenCompraFacturasPago.monto_pagado'
						)
					));

					# Se agregan las ralaciones ya creadas para no perderlas
					foreach ($facturasOc as $iocp => $ocp) {
						$facturasPagadas[$i] = $ocp['OrdenCompraFacturasPago'];
					}

					unset($pago['OrdenCompraFactura']);
					
					$savePago['OrdenCompraPago'] = $pago;
					$savePago['OrdenCompraFactura'] = $facturasPagadas;

				}

				# Guardamos el pago
				if ($this->OrdenCompraPago->saveAll($savePago)) {
					$alerta['success'][] = 'Pago #' . $pago['id'] . ' se ha guardado con éxito.';
				}else{
					$alerta['errors'][] = 'Ocurrió un error al guardar el pago #' . $pago['id'];
				}

				# Actualizamos la OC correspondiente
				$oc = ClassRegistry::init('OrdenCompra')->find('first', array(
					'conditions' => array(
						'OrdenCompra.id' => $pago['orden_compra_id']
					),
					'fields' => array(
						'OrdenCompra.id',
						'OrdenCompra.total',
						'OrdenCompra.total_pagado',
						'OrdenCompra.proveedor_id'
					),
					'contain' => array(
						'OrdenCompraPago' => array(
							'fields' => array(
								'OrdenCompraPago.monto_real_pagado'
							),
						),
						'OrdenCompraFactura' => array(
							'fields' => array(
								'OrdenCompraFactura.monto_pagado'
							)
						)
					)
				));

				# Total pagado
				$oc['OrdenCompra']['total_pagado'] = array_sum(Hash::extract($oc['OrdenCompraPago'], '{n}.monto_real_pagado'));

				if (ClassRegistry::init('OrdenCompra')->save($oc)) {
					$alerta['success'][] = 'OC #' . $oc['OrdenCompra']['id'] . ' se ha actualizado con éxito.';
				}else{
					$alerta['errors'][] = 'Ocurrió un error al guardar la oc #' . $oc['OrdenCompra']['id'];
				}
			}

			if (!empty($alerta['success'])){
				$this->Session->setFlash($this->crearAlertaUl($alerta['success'], 'Resultado de la operación'), null, array(), 'success');
			}


			if (!empty($alerta['errors'])){
				$this->Session->setFlash($this->crearAlertaUl($alerta['errors'], 'Errores encontrados'), null, array(), 'danger');
			}
			
			$this->redirect(array('action' => 'calendario'));

		}


		$pagos = $this->OrdenCompraPago->find('all', array(
			'contain' => array(
				'OrdenCompra' => array(
					'fields' => array(
						'OrdenCompra.total'
					)
				)
			),
			'conditions' => array(
				'OrdenCompraPago.finalizado' => 0
			)
		));

		$pagos_retrasados = array();
		$pagos_dia        = array();
		$pagos_mes        = array();

		foreach ($pagos as $ip => $pago) {

			$pago_pendiente =  $pago['OrdenCompra']['total'] - $pago['OrdenCompraPago']['monto_pagado'];

			if (strtotime($pago['OrdenCompraPago']['fecha_pago']) < strtotime(date('Y-m-d'))) {
				$pagos_retrasados[$ip]['OrdenCompraPago'] = $pago['OrdenCompraPago'];
				$pagos_retrasados[$ip]['OrdenCompraPago']['pago_pendiente'] = $pago_pendiente;
			}

			if (strtotime($pago['OrdenCompraPago']['fecha_pago']) == strtotime(date('Y-m-d'))) {
				$pagos_dia[$ip]['OrdenCompraPago'] = $pago['OrdenCompraPago'];
				$pagos_dia[$ip]['OrdenCompraPago']['pago_pendiente'] = $pago_pendiente;
			}

			if (strtotime($pago['OrdenCompraPago']['fecha_pago']) > strtotime(date('Y-m-d')) && strtotime($pago['OrdenCompraPago']['fecha_pago']) < strtotime(date('Y-m-t')) ) {
				$pagos_mes[$ip]['OrdenCompraPago'] = $pago['OrdenCompraPago'];
				$pagos_mes[$ip]['OrdenCompraPago']['pago_pendiente'] = $pago_pendiente;
			}		

		}
		
		$this->set(compact('pagos', 'pagos_retrasados', 'pagos_dia', 'pagos_mes'));

		BreadcrumbComponent::add('Pagos', '/ordenCompraPagos');
		BreadcrumbComponent::add('Calendario ');
	}


	/**
	 * [admin_obtener_pagos description]
	 * @return [type] [description]
	 */
	public function admin_obtener_pagos()
	{
		$pagos = $this->OrdenCompraPago->find('all', array(
			'contain' => array(
				'OrdenCompra' => array(
					'fields' => array(
						'OrdenCompra.total',
						'OrdenCompra.total_pagado'
					),
					'OrdenCompraFactura' => array(
						'fields' => array(
							'OrdenCompraFactura.monto_facturado',
							'OrdenCompraFactura.folio',
							'OrdenCompraFactura.id',
							'OrdenCompraFactura.monto_pagado',
							'OrdenCompraFactura.tipo_documento',
							'OrdenCompraFactura.nota'
						),
						'conditions' => array(
							'OrdenCompraFactura.pagada' => 0
						)
					),
					'Proveedor' => array(
						'fields' => array(
							'Proveedor.nombre'
						)
					)
				),
				'OrdenCompraFactura' => array(
					'fields' => array(
						'OrdenCompraFactura.monto_facturado',
						'OrdenCompraFactura.folio',
						'OrdenCompraFactura.id',
						'OrdenCompraFactura.monto_pagado',
						'OrdenCompraFactura.tipo_documento',
						'OrdenCompraFactura.nota'
					)
				),
				'Moneda' => array(
					'fields' => array(
						'Moneda.tipo', 'Moneda.comprobante_requerido'
					)
				)
			),
			'conditions' => array(
				'OrdenCompraPago.fecha_pago BETWEEN ? AND ?' => array($this->request->query['start'], $this->request->query['end']),
				'OrdenCompraPago.finalizado' => 0
			)
		));
		
		$monedas = $this->OrdenCompraPago->Moneda->find('list', array(
			'conditions' => array('activo' => 1)
		));

		$result = array();
		
		$index = 0;

		foreach ($pagos as $i => $pago) {

			$pago_pendiente =  $pago['OrdenCompraPago']['monto_pagado'];
			$total_pagado 	=  $pago['OrdenCompra']['total_pagado'];

			$result[$index]['id']        = $pago['OrdenCompraPago']['id'];
			$result[$index]['title']     = 'OC #' . $pago['OrdenCompraPago']['orden_compra_id'] . ' - ' . CakeNumber::currency($pago_pendiente, 'CLP');			
			$result[$index]['url']       = Router::url( '/', true ) . 'ordenCompraPagos/edit/' . $pago['OrdenCompraPago']['id'];
			$result[$index]['start']     = $pago['OrdenCompraPago']['fecha_pago'];
			$result[$index]['className'] = 'orange';

			if ( strtotime($pago['OrdenCompraPago']['fecha_pago']) < strtotime(date('Y-m-d')) ) {
				$result[$index]['className'] = 'red';
			}

			$opcionesMoneda = '';
			foreach ($monedas as $im => $moneda) {
				if ($im == $pago['OrdenCompraPago']['moneda_id']) {
					$opcionesMoneda .= '<option value="'.$im.'" selected>'.$moneda.'</option>';
				}else{
					$opcionesMoneda .= '<option value="'.$im.'">'.$moneda.'</option>';
				}
			}

			# si el pago ya se relacionó a unafactura, se quita
			if ($pago['OrdenCompra']['total_pagado'] >= array_sum(Hash::extract($pago['OrdenCompraFactura'], '{n}.monto_pagado')) && array_sum(Hash::extract($pago['OrdenCompraFactura'], '{n}.monto_pagado')) > 0 ) {
				unset($result[$index]);
				continue;
			}

			$v             =  new View();
			$v->autoRender = false;
			$v->output     = '';
			$v->layoutPath = '';
			$v->layout     = '';
			$v->set(compact('index', 'pago', 'opcionesMoneda', 'total_pagado', 'pago_pendiente', 'result'));	

			$result[$index]['tr'] = $v->render('/Elements/ordenCompraPagos/tr-pago');

			$index++;

		}

		echo json_encode($result);
		exit;

	}


	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->OrdenCompraPago->create();
			if ( $this->OrdenCompraPago->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Pagos', '/ordenCompraPagos');
		BreadcrumbComponent::add('Agregar ');
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->OrdenCompraPago->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->OrdenCompraPago->save($this->request->data) )
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
			$this->request->data	= $this->OrdenCompraPago->find('first', array(
				'conditions'	=> array('OrdenCompraPago.id' => $id)
			));
		}

		BreadcrumbComponent::add('Pagos', '/ordenCompraPagos');
		BreadcrumbComponent::add('Editar ');
	}

	public function admin_delete($id = null)
	{
		$this->OrdenCompraPago->id = $id;
		if ( ! $this->OrdenCompraPago->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->OrdenCompraPago->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->OrdenCompraPago->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->OrdenCompraPago->_schema);
		$modelo			= $this->OrdenCompraPago->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	public function guardarEmailPagoAgendar($pago, $emails = array())
	{	
		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'OrdenCompraPagos' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';
		$this->Correo				= ClassRegistry::init('Correo');
		
		if (Configure::read('debug') == 0) {
			$url = FULL_BASE_URL;
		}else{
			$url = FULL_BASE_URL_DEV;
		}		

		$this->View->set(compact('pago', 'url'));
		$html						= $this->View->render('notificar_pago_agendado');

		/**
		 * Guarda el email a enviar
		 */
		$this->Correo->create();
		
		if ( $this->Correo->save(array(
			'estado'					=> 'Notificación pagado oc',
			'html'						=> $html,
			'asunto'					=> sprintf('[NDRZ] OC #%d lista para agendar pagos', $pago['OrdenCompra']['id']),
			'destinatario_email'		=> trim(implode(',', $emails)),
			'destinatario_nombre'		=> '',
			'remitente_email'			=> 'cristian.rojas@nodriza.cl',
			'remitente_nombre'			=> 'Sistemas - Nodriza Spa',
			'cc_email'					=> '',
			'bcc_email'					=> 'cristian.rojas@nodriza.cl',
			'traza'						=> null,
			'proceso_origen'			=> null,
			'procesado'					=> 0,
			'enviado'					=> 0,
			'reintentos'				=> 0,
			'atachado'					=> null
		)) ) {
			return true;
		}

		return false;
	}
}
