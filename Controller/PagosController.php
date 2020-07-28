<?php
App::uses('AppController', 'Controller');

class PagosController extends AppController
{
	public function admin_calendario()
	{	

		# solo metodos post
		if ($this->request->is('post')) {

			if ($this->Pago->saveMany($this->request->data)) {

				# guardarEmailPagoFactura
				foreach ($this->request->data as $ip => $p) {
					$this->guardarEmailPagoFactura($p['Pago']['id']);
					break;
				}

				$this->Session->setFlash('Pagos actualizados con éxito.', null, array(), 'success');
			}else{
				$this->Session->setFlash('Ocurrió un error al finalizar éstos pagos. Intente nuevamente.', null, array(), 'warning');
			}

			$this->redirect(array('action' => 'calendario'));
		}		

		BreadcrumbComponent::add('Pagos', '/pagos/calendario');
		BreadcrumbComponent::add('Calendario ');
	}


	public function admin_configuracion($id_factura)
	{	

		if ($this->request->is('post') || $this->request->is('put')) {
			
			foreach ($this->request->data as $ip => $p) {
				if (isset($p['Pago']['adjunto']) && $p['Pago']['adjunto']['error']) {
					unset($this->request->data[$ip]['Pago']['adjunto']);
				}
			}
			
			if ($this->Pago->saveMany($this->request->data, array('deep' => true))) {

				// guardarEmailPagoFactura
				$factura = $this->Pago->OrdenCompraFactura->find('first', array(
					'conditions' => array(
						'OrdenCompraFactura.id' => $id_factura
					),
					'contain' => array(
						'Pago' => array(
							'fields' => array(
								'Pago.id'
							)
						)
					)
				));

				# Notificamos los pagos si corresponde
				foreach ($factura['Pago'] as $ip => $p) {
					$this->guardarEmailPagoFactura($p['id']);
					break;
				}

				$this->Session->setFlash('Pagos configurado con éxito', null, array(), 'success');
				$this->redirect(array('controller' => 'ordenCompraFacturas', 'action' => 'index'));
			}else{
				$this->Session->setFlash('Se necesita configurar uno o varios pagos.', null, array(), 'success');
			}

		}

		$factura = $this->Pago->OrdenCompraFactura->find('first', array(
			'conditions' => array(
				'OrdenCompraFactura.id' => $id_factura
			),
			'contain' => array(
				'OrdenCompra' => array(
					'fields' => array(
						'OrdenCompra.id',
						'OrdenCompra.total',
						'OrdenCompra.moneda_id'
					),
					'Moneda' => array(
						'fields' => array(
							'Moneda.tipo',
							'Moneda.nombre',
							'Moneda.comprobante_requerido'
						)
					),
					'OrdenCompraFactura' => array(
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
					)
				),
				'Pago' => array(
					'OrdenCompraAdjunto' => array(
						'fields' => array(
							'OrdenCompraAdjunto.identificador',
							'OrdenCompraAdjunto.adjunto',
						)
					),
					'Moneda' => array(
						'fields' => array(
							'Moneda.tipo',
							'Moneda.nombre',
							'Moneda.comprobante_requerido'
						)
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
			)
		));
	
		BreadcrumbComponent::add('Pagos', '/ordenCompraFacturas/index');
		BreadcrumbComponent::add('Configuración de pagos');

		$cuenta_bancarias = ClassRegistry::init('CuentaBancaria')->find('list', array('conditions' => array('activo' => 1)));
		$monedas = ClassRegistry::init('Moneda')->find('list', array('conditions' => array('activo' => 1)));

		$this->set(compact('factura', 'cuenta_bancarias', 'monedas'));
	}


	public function admin_configuracion_multiple()
	{
		if ($this->request->is('post')) {
			
			foreach ($this->request->data as $ip => $p) {
				if (isset($p['Pago']['adjunto']) && $p['Pago']['adjunto']['error']) {
					unset($this->request->data[$ip]['Pago']['adjunto']);
				}
			}


			# Los pagos al día los finalizamos
			foreach ($this->request->data as $ip => $pago) {
				if (isset($pago['Pago']['fecha_pago']) && $pago['Pago']['fecha_pago'] <= date('Y-m-d') && $pago['Pago']['monto_pagado'] > 0 && $pago['Pago']['pagado'] == true) {
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

		$facturas = ClassRegistry::init('OrdenCompraFactura')->find('all', array(
			'conditions' => array(
				'OrdenCompraFactura.id' => $this->request->params['named']['id']
			),
			'contain' => array(
				'OrdenCompra' => array(
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
							'Moneda.nombre',
							'Moneda.comprobante_requerido'
						)
					),
					'fields' => array(
						'OrdenCompra.id',
						'OrdenCompra.total',
						'OrdenCompra.moneda_id'
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
				'OrdenCompraFactura.monto_facturado', 'OrdenCompraFactura.folio'
			)
		));
		
		BreadcrumbComponent::add('Pagos', '/ordenCompraFacturas/index');
		BreadcrumbComponent::add('Configuración de pagos');

		$cuenta_bancarias = ClassRegistry::init('CuentaBancaria')->find('list', array('conditions' => array('activo' => 1)));
		$monedas = ClassRegistry::init('Moneda')->find('list', array('conditions' => array('activo' => 1)));

		$this->set(compact('facturas', 'cuenta_bancarias', 'monedas'));
	}


	/**
	 * [admin_obtener_pagos description]
	 * @return [type] [description]
	 */
	public function admin_obtener_pagos()
	{
		$pagos = $this->Pago->find('all', array(
			'contain' => array(
				'OrdenCompraFactura' => array(
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
				),
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
		
		$baseUrl = obtener_url_base();

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

			$folios = '';
			$ocs = '';
			if (!empty($pago['OrdenCompraFactura'])) {
				
				$folios = '<ul class="list-group border-bottom">';

				foreach ($pago['OrdenCompraFactura'] as $f) {
					
					$folios .= '<li class="list-group-item">Folio <a href="' . $baseUrl . 'ordenCompraFacturas/view/' . $f['id'] . '" target="_blank">#' . $f['folio'] . '</a> - ' . CakeNumber::currency($f['monto_facturado'], 'CLP') . '</li>';
					
					# OC relacionada
					$ocs .= '<ul class="list-group border-bottom">';
					$ocs .= '<li class="list-group-item"><a href="' . $baseUrl . 'ordenCompras/view/'.$f['OrdenCompra']['id'].'" target="_blank">OC #' . $f['OrdenCompra']['id'] . '</a> - ' . CakeNumber::currency($f['OrdenCompra']['total'], 'CLP') . '</li>';
					$ocs .= '</ul>';

				}

				$folios .= '</ul>';

			}

			$result[$index]['id']        = $pago['Pago']['id'];
			$result[$index]['orden']     = $index;
			$result[$index]['title']     = $index . ' - Pago #' . $pago['Pago']['identificador'] . ' - ' . CakeNumber::currency($pago['Pago']['monto_pagado'], 'CLP');			
			$result[$index]['start']     = $pago['Pago']['fecha_pago'];
			$result[$index]['className'] = 'orange';
			$result[$index]['trigger']   = 'click';
			$result[$index]['description'] = '<p>Facturas relacionadas: </p>' . $folios . '<br><p>OCs relacionadas: </p> ' . $ocs . '<button class="btn btn-xs btn-danger btn-block close-pop"><i class="fa fa-times"></i> cerrar</button>';

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
				
		$url = obtener_url_base();		

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


	/**
	 * [guardarEmailPagoFactura description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function guardarEmailPagoFactura($id)
	{	
		$pago = $this->Pago->find('first', array(
			'conditions' => array(
				'Pago.id' => $id,
				'Pago.pagado' => 1
			),
			'contain' => array(
				'OrdenCompraFactura' => array(
					'conditions' => array(
						'OrdenCompraFactura.pagada' => 1,
					),
					'fields' => array(
						'OrdenCompraFactura.id',
						'OrdenCompraFactura.proveedor_id'
					) 
				)
			)
		));
		
		# No hay facturas pagadas
		if (empty($pago['OrdenCompraFactura'])) {
			return;
		}

		# Obtenemos los proveedores que tengna facturas pagadas y pagos finalizados
		$proveedores = ClassRegistry::init('Proveedor')->find('all', array(
			'conditions' => array(
				'Proveedor.id' => Hash::extract($pago['OrdenCompraFactura'], '{n}.proveedor_id')
			),
			'contain' => array(
				'OrdenCompraFactura' => array(
					'conditions' => array(
						'OrdenCompraFactura.id' => Hash::extract($pago['OrdenCompraFactura'], '{n}.id')
					),
					'Pago' => array(
						'conditions' => array(
							'Pago.pagado' => 1
						)
					),
					'OrdenCompra' => array(
						'OrdenCompraAdjunto',
						'fields' => array(
							'OrdenCompra.id'
						)
					)
				)
			),
			'fields' => array(
				'Proveedor.id',
				'Proveedor.nombre',
				'Proveedor.meta_emails'
			)
		));
		
		if (empty($proveedores)) {
			return;
		}

		# Enviamos emails
		foreach ($proveedores as $ip => $proveedor) {
				
			$receptor_pago = Hash::extract($proveedor['Proveedor'], 'meta_emails.{n}[tipo=pago].email');
			$receptores    = Hash::extract($proveedor['Proveedor'], 'meta_emails.{n}[tipo=destinatario].email');
			$cc            = Hash::extract($proveedor['Proveedor'], 'meta_emails.{n}[tipo=copia].email');
			$bcc           = Hash::extract($proveedor['Proveedor'], 'meta_emails.{n}[tipo=copia oculta].email');

			$to = (!empty($receptor_pago)) ? $receptor_pago : $receptores;

			$adjuntos = array();

			$adjuntos_oc    = unique_multidim_array(Hash::extract($proveedor['OrdenCompraFactura'], '{n}.OrdenCompra.OrdenCompraAdjunto.{n}'), 'adjunto');
			$adjuntos_pagos = unique_multidim_array(Hash::extract($proveedor['OrdenCompraFactura'], '{n}.Pago.{n}'), 'adjunto');

			/* no usado por ahora
			$url_base = APP . 'webroot' . DS . 'img' . DS;

			# Adjuntamos los comprobantes de la oc
			foreach ($adjuntos_oc as $iad => $adj) {

				if (empty($adj['adjunto']))
					continue;

				$archivo = $url_base . 'OrdenCompraAdjunto' . DS . $adj['id'] . DS . $adj['adjunto'];

				$mime      = mime_content_type($archivo);
				$nombre    = pathinfo($archivo, PATHINFO_FILENAME);

				if($mime == 'inode/x-empty' && pathinfo($archivo, PATHINFO_EXTENSION) == 'docx') {
				    $mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
				}

				$adjuntos[] = array(
                	'type' => $mime,
                	'name' => $nombre,
                	'content' => chunk_split(base64_encode(file_get_contents($archivo)))
            	);
			}

			# Adjuntamos los comprobantes del pago
			foreach ($adjuntos_pagos as $iad => $adj) {

				if (empty($adj['adjunto']))
					continue;

				$archivo = $url_base . 'Pago' . DS . $adj['id'] . DS . $adj['adjunto'];

				$mime      = mime_content_type($archivo);
				$nombre    = pathinfo($archivo, PATHINFO_FILENAME);

				if($mime == 'inode/x-empty' && pathinfo($archivo, PATHINFO_EXTENSION) == 'docx') {
				    $mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
				}

				$adjuntos[] = array(
                	'type' => $mime,
                	'name' => $nombre,
                	'content' => chunk_split(base64_encode(file_get_contents($archivo)))
            	);
			}*/

			
			$this->View					= new View();
			$this->View->viewPath		= 'Pagos' . DS . 'html';
			$this->View->layoutPath		= 'Correos' . DS . 'html';
			
			$url = obtener_url_base();
			
			$this->View->set(compact('proveedor', 'url', 'adjuntos'));
			$html						= $this->View->render('notificar_pago_factura');
			
			$mandrill_apikey = SessionComponent::read('Tienda.mandrill_apikey');
			
			if (empty($mandrill_apikey)) {
				return false;
			}

			$mandrill = $this->Components->load('Mandrill');

			$mandrill->conectar($mandrill_apikey);

			$asunto = sprintf('[NDRZ - %s] Se ha realizado el pago de facturas desde Nodriza Spa', date('Y-m-d H:i:s'));
			
			if (Configure::read('ambiente') == 'dev') {
				$asunto = sprintf('[NDRZ-DEV - %s] Se ha realizado el pago de facturas desde Nodriza Spa', date('Y-m-d H:i:s'));
			}
			
			$remitente = array(
				'email' => 'oc@nodriza.cl',
				'nombre' => 'Finanzas Nodriza'
			);

			$destinatarios = array();

			foreach ($to as $id => $des) {
				$destinatarios[] = array(
					'email' => $des,
					'type' => 'to'
				);
			}

			foreach ($cc as $ic => $c) {
				$destinatarios[] = array(
					'email' => $c,
					'type' => 'cc'
				);
			}

			foreach ($bcc as $ibc => $bc) {
				$destinatarios[] = array(
					'email' => $bc,
					'type' => 'bcc'
				);
			}

			$emailsFinanzas = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('pagar_oc');
			$cabeceras      = array();
		
			if (!empty($emailsFinanzas)) {
				$cabeceras = array(
					'Reply-To' => implode(',', $emailsFinanzas)
				);	
			}
			
			$notificado = $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios, $cabeceras, $adjuntos);	
	
			# Guardamos el estado notificado para estas facturas
			foreach ($proveedor['OrdenCompraFactura'] as $i => $f) {
				ClassRegistry::init('OrdenCompraFactura')->id = $f['id'];
				ClassRegistry::init('OrdenCompraFactura')->saveField('notificada', 1);
			}

		}

		return;

	}



	public function api_add() {

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


		if (empty($this->request->data['Pago']['identificador'])
			|| empty($this->request->data['Pago']['fecha_pago'])
			|| empty($this->request->data['Pago']['monto_pagado']))
		{

			$response = array(
				'code' => 504,
				'created' => false,
				'message' => 'identificador, fecha_pago y monto_pagado son requeridos.'
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
				'administrador' => 'Rest api',
				'modulo' => 'Pagos',
				'modulo_accion' => json_encode($this->request->data)
			)
		);
		
		if ($this->Pago->saveAll($this->request->data)){

			$log[] = array(
				'Log' => array(
					'administrador' => 'Rest api',
					'modulo' => 'Pago',
					'modulo_accion' => 'Creación: pago id ' . $this->Pago->id
				)
			);

			$pago = $this->Pago->find('first', array(
				'conditions' => array('Pago.id' => $this->Pago->id),
				'contain' => array(
					'CuentaBancaria' => array(
						'fields' => array(
							'CuentaBancaria.alias',
							'CuentaBancaria.numero_cuenta'
						)
					),
					'Moneda' => array(
						'fields' => array(
							'Moneda.nombre'
						)
					),
					'OrdenCompraFactura'
				))
			);

			# si el pago finalizó notificamos las facturas afectadas
			if ($pago['Pago']['pagado']) {
				$this->guardarEmailPagoFactura($this->Pago->id);
			}

			$v             =  new View();
			$v->autoRender = false;
			$v->output     = '';
			$v->layoutPath = '';
			$v->layout     = '';
			$v->set(compact('pago'));	

			$pago['Pago']['block'] = $v->render('/Elements/Pagos/block-pago');

			$resultado = array(
				'code' => 200,
				'created' => true,
				'pago' => $pago
			);
		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		$this->set(array(
			'response'   => $resultado,
			'_serialize' => array('response')
	    ));
	}
}