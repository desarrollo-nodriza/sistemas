<?php 

App::uses('CakeEmail', 'Network/Email');
App::uses('View', 'View');
App::uses('Controller', 'Controller');
App::uses('AppController', 'Controller');

class NotificarReposicionProductoShell extends AppShell {
	
	public function main() {

		ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);

		$log = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'Inicia proceso de notificacion de ocs retrasadas: ' . date('Y-m-d H:i:s')
		));

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);
		
		# Ventas no procesadas
		$ventas = ClassRegistry::init('Venta')->find('all', array(
			'conditions' => array(
				'Venta.atendida' => 0,
				'Venta.subestado_oc' => 'parcialmente_entregado'
			),
			'contain' => array(
				'VentaDetalle' => array(
					'conditions' => array(
						'VentaDetalle.fecha_llegada' => date('Y-m-d')
					),
					'fields' => array(
						'VentaDetalle.fecha_llegada',
						'VentaDetalle.cantidad_pendiente_entrega'
					),
					'VentaDetalleProducto' => array(
						'fields' => array(
							'VentaDetalleProducto.nombre'
						)
					)
				)
			),
			'fields' => array(
				'Venta.fecha_venta', 
				'Venta.id_externo',
				'Venta.referencia',
				'Venta.fecha_venta',
				'Venta.tienda_id'
			)
		));
		
		$ventasNotificar = array();
		$emailsNotificar = array();

		$admins = ClassRegistry::init('Administrador')->find('all', array(
			'conditions' => array(
				'Administrador.activo' => 1
			),
			'fields' => array(
				'Administrador.email',
				'Administrador.notificaciones'
			)
		));


		// Obtenemos a los administradores que tiene activa la notificación de bodeas
		foreach ($admins as $ia => $admin) {
			if (!empty($admin['Administrador']['notificaciones'])) {
				$confNotificacion = json_decode($admin['Administrador']['notificaciones'], true);
				
				if (!isset($confNotificacion['bodegas'])) {
					continue;
				}

				if ( array_key_exists('ventas', $confNotificacion) && $confNotificacion['bodegas'] ) {
					$emailsNotificar[] = $admin['Administrador']['email'];
				}
			}
		}

		$controller = new AppController();
		
		// Se agrupan los detalles de ventas que llegan hoy
		foreach ($ventas as $iv => $venta) {
			foreach ($venta['VentaDetalle'] as $id => $detalle) {
				$ventasNotificar[] = $detalle;	
			}
		}

		// Guardamos el email
		if (!empty($ventasNotificar) && !empty($emailsNotificar)) {
			if ($this->guardarEmail($ventasNotificar, $emailsNotificar)) {
				$this->out('Emails registrados con éxito');
				exit;
			}else{
				$this->out('Error al guardar');
				exit;
			}
		}


		$log = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'No existen ventas retrasadas o no hay receptores: ' . date('Y-m-d H:i:s')
		));

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);

		$this->out('No existen ventas retrasadas o no hay receptores');
		exit;

	}


    public function guardarEmail($retrasos = array(), $emails = array()) 
    {

		/**
		 * Clases requeridas
		 */
		$this->View					= new View();
		$this->View->viewPath		= 'Ventas' . DS . 'html';
		$this->View->layoutPath		= 'Correos' . DS . 'html';
		$this->Correo				= ClassRegistry::init('Correo');
		
		/**
		 * Correo a ventas
		 */
		$this->View->set(compact('retrasos'));
		$html						= $this->View->render('notificar_llegada_productos');

		/**
		 * Guarda el email a enviar
		 */
		$this->Correo->create();
		
		if ( $this->Correo->save(array(
			'estado'					=> 'Notificación llegada de productos',
			'html'						=> $html,
			'asunto'					=> '[NDRZ] Productos que llegan hoy',
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