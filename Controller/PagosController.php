<?php
App::uses('AppController', 'Controller');

class PagosController extends AppController
{
	public function admin_calendario()
	{	

		# solo metodos post
		if ($this->request->is('post')) {
			if ($this->Pago->saveMany($this->request->data)) {
				$this->Session->setFlash('Pagos actualizados con éxito.', null, array(), 'success');
			}else{
				$this->Session->setFlash('Ocurrió un error al finalizar éstos pagos. Intente nuevamente.', null, array(), 'warning');
			}

			$this->redirect(array('action' => 'calendario'));
		}		

		BreadcrumbComponent::add('Pagos', '/pagos/calendario');
		BreadcrumbComponent::add('Calendario ');
	}


	public function admin_configuracion($id_oc)
	{	

		if ($this->request->is('post')) {
			
			foreach ($this->request->data as $ip => $p) {
				if (isset($p['Pago']['adjunto']) && $p['Pago']['adjunto']['error']) {
					unset($this->request->data[$ip]['Pago']['adjunto']);
				}
			}


			# Los pagos al día los finalizamos
			foreach ($this->request->data as $ip => $pago) {
				if (isset($pago['Pago']['fecha_pago']) && $pago['Pago']['fecha_pago'] <= date('Y-m-d') && $pago['Pago']['monto_pagado'] > 0) {
					$this->request->data[$ip]['Pago']['pagado'] = 1;
				}
			}
			
			if ($this->Pago->saveMany($this->request->data)) {
				$this->Session->setFlash('Pagos configurado con éxito. ahora puede relacionar las facturas disponibles para la OC #' . $id_oc, null, array(), 'success');
				$this->redirect(array('controller' => 'ordenCompraFacturas', 'action' => 'index', 'oc' => $id_oc));
			}else{
				$this->Session->setFlash('Se necesita configurar uno o varios pagos.', null, array(), 'success');
			}

		}

		$oc = $this->Pago->OrdenCompra->find('first', array(
			'conditions' => array(
				'OrdenCompra.id' => $id_oc
			),
			'contain' => array(
				'Pago' => array(
					'OrdenCompraAdjunto' => array(
						'fields' => array(
							'OrdenCompraAdjunto.identificador',
							'OrdenCompraAdjunto.adjunto',
						)
					)
				),
				'Moneda' => array(
					'fields' => array(
						'Moneda.tipo',
						'Moneda.comprobante_requerido'
					)
				),
				'OrdenCompraFactura' => array(
					'fields' => array(
						'OrdenCompraFactura.monto_facturado'
					)
				),
				'Proveedor' => array(
					'fields' => array(
						'Proveedor.nombre',
						'Proveedor.giro',
						'Proveedor.email_contacto',
						'Proveedor.fono_contacto',
						'Proveedor.rut_empresa',
						'Proveedor.direccion'
					)
				)
			),
			'fields' => array(
				'OrdenCompra.id',
				'OrdenCompra.total',
				'OrdenCompra.moneda_id'
			)
		));

		BreadcrumbComponent::add('Pagos', '/ordenCompraFacturas/index');
		BreadcrumbComponent::add('Configuración de pagos');

		$cuenta_bancarias = ClassRegistry::init('CuentaBancaria')->find('list', array('conditions' => array('activo' => 1)));
		$monedas = ClassRegistry::init('Moneda')->find('list', array('conditions' => array('activo' => 1)));

		$this->set(compact('oc', 'cuenta_bancarias', 'monedas'));
	}


	/**
	 * [admin_obtener_pagos description]
	 * @return [type] [description]
	 */
	public function admin_obtener_pagos()
	{
		$pagos = $this->Pago->find('all', array(
			'contain' => array(
				'OrdenCompra' => array(
					'fields' => array(
						'OrdenCompra.total',
						'OrdenCompra.total_pagado'
					),
					'Proveedor' => array(
						'fields' => array(
							'Proveedor.nombre',
							'Proveedor.rut_empresa'
						)
					),
					'Moneda' => array(
						'fields' => array(
							'Moneda.nombre'
						)
					),
					'OrdenCompraFactura' => array(
						'fields' => array(
							'OrdenCompraFactura.folio',
							'OrdenCompraFactura.id',
							'OrdenCompraFactura.monto_facturado'
						)
					)
				),
				'OrdenCompraFactura',
				'CuentaBancaria' => array(
					'fields' => array(
						'CuentaBancaria.alias',
						'CuentaBancaria.numero_cuenta'
					)
				)
			),
			'conditions' => array(
				'Pago.fecha_pago BETWEEN ? AND ?' => array($this->request->query['start'], $this->request->query['end']),
				'Pago.pagado' => 0
			)
		));

		$result = array();
		
		$index = 0;

		$monedas = ClassRegistry::init('Moneda')->find('list', array('conditions' => array('activo' => 1)));
		$cuentaBancarias = ClassRegistry::init('CuentaBancaria')->find('list', array('conditions' => array('activo' => 1)));


		if (Configure::read('debug') == 0) {
			$baseUrl = FULL_BASE_URL;
		}else{
			$baseUrl = FULL_BASE_URL_DEV;
		}

		# Pagos totales por día
		$dias = getDatesFromRange($this->request->query['start'], $this->request->query['end']);

		foreach ($dias as $id => $dia) {

			$pagar = array_sum(Hash::extract($pagos, '{n}.Pago[fecha_pago='.$dia.'].monto_pagado'));

			if ($pagar == 0) {
				continue;
			}

			$result[$index]['id']         = rand();
			$result[$index]['orden']      = $index;
			$result[$index]['title']      = $index . ' - Total del día: ' . CakeNumber::currency($pagar, 'CLP');
			$result[$index]['description'] = 'Suma de todos los pagos del día.';
			$result[$index]['start']      = $dia;
			$result[$index]['className']  = 'default';
			$result[$index]['trigger']    = 'hover';
			$index++;
		}

		foreach ($pagos as $i => $pago) {

			$folios = '<ul class="list-group border-bottom">';

			foreach ($pago['OrdenCompra']['OrdenCompraFactura'] as $f) {
				$folios .= '<li class="list-group-item">Folio <a href="' . $baseUrl . 'ordenCompraFacturas/view/' . $f['id'] . '" target="_blank">#' . $f['folio'] . '</a> - ' . CakeNumber::currency($f['monto_facturado'], 'CLP') . '</li>';
			}

			$folios .= '</ul>';

			$result[$index]['id']        = $pago['Pago']['id'];
			$result[$index]['orden']     = $index;
			$result[$index]['title']     = $index . ' - Pago #' . $pago['Pago']['identificador'] . ' - ' . CakeNumber::currency($pago['Pago']['monto_pagado'], 'CLP');			
			$result[$index]['start']     = $pago['Pago']['fecha_pago'];
			$result[$index]['className'] = 'orange';
			$result[$index]['trigger']   = 'click';
			$result[$index]['description'] = '<p>OC relacionada: <b><a href="' . $baseUrl . 'ordenCompras/view/'.$pago['Pago']['orden_compra_id'].'" target="_blank">#' . $pago['Pago']['orden_compra_id'] . '</a></b></p> <p>Facturas relacionadas: </p>' . $folios . '<button class="btn btn-xs btn-danger btn-block close-pop"><i class="fa fa-times"></i> cerrar</button>';

			if ( strtotime($pago['Pago']['fecha_pago']) < strtotime(date('Y-m-d')) ) {
				$result[$index]['className'] = 'red';
			}

			$v             =  new View();
			$v->autoRender = false;
			$v->output     = '';
			$v->layoutPath = '';
			$v->layout     = '';
			$v->set(compact('index', 'pago', 'result', 'monedas', 'cuentaBancarias'));

			$result[$index]['tr'] = $v->render('/Elements/Pagos/tr-pago');

			$index++;

		}

		echo json_encode($result);
		exit;

	}


	public function guardarEmailPagoAgendar($pago, $emails = array())
	{	
		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'Pagos' . DS . 'html';
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
			'asunto'					=> sprintf('[NDRZ] OC #%d - Tiene %d nueva factura por procesar', $pago['OrdenCompra']['id'],  1),
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