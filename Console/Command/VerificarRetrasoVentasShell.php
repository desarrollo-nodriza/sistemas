<?php 

App::uses('CakeEmail', 'Network/Email');
App::uses('View', 'View');
App::uses('Controller', 'Controller');
App::uses('AppController', 'Controller');

class VerificarRetrasoVentasShell extends AppShell {
	
	public function main() {

		ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);

		$log = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'Ventas',
			'modulo_accion' => 'Inicia proceso de notificacion de ventas retrasadas: ' . date('Y-m-d H:i:s')
		));

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);
		
		# Ventas no procesadas
		$ventas = ClassRegistry::init('Venta')->find('all', array(
			'conditions' => array(
				'Venta.atendida' => 0,
				'Venta.notificar_retraso' => 1
			),
			'contain' => array(
				'Tienda' => array(
					'fields' => array(
						'Tienda.dias_retraso',
						'Tienda.nombre'
					)
				),
				'Marketplace' => array(
					'fields' => array(
						'Marketplace.nombre'
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


		// Obtenemos a los administradores que tiene activa la notificación de ventas
		foreach ($admins as $ia => $admin) {
			if (!empty($admin['Administrador']['notificaciones'])) {
				$confNotificacion = json_decode($admin['Administrador']['notificaciones'], true);
				
				if ( array_key_exists('ventas', $confNotificacion) && $confNotificacion['ventas'] ) {
					$emailsNotificar[] = $admin['Administrador']['email'];
				}
			}
		}

		$controller = new AppController();

		// Se agrupan las ventas retrasadas
		foreach ($ventas as $iv => $venta) {
			
			# Configuración de días a notificar de la tienda de la venta
			$tienda = $controller->tiendaInfo($venta['Venta']['tienda_id']);

			$diasRetrasoNotificar = (!empty($tienda['Tienda']['dias_retraso'])) ? $tienda['Tienda']['dias_retraso'] : 1;

			$diasRetraso = $this->calcular_retraso_dias($venta['Venta']['fecha_venta'], $diasRetrasoNotificar);

			if ($diasRetraso) {
				$venta['Venta']['dias_retraso'] = $diasRetraso;
				$ventasNotificar[] = $venta; 
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


	/**
     * Se encarga de definir cuantos días de retraso tiene la venta según su fecha de venta.
     * @param  string $fecha [description]
     * @return boolean       Restrasada o no
     */
    public function calcular_retraso($fecha = '', $dias = 1)
    {
    	if (!empty($fecha)) {
    		
    		$fechaVenta = new DateTime($fecha);
			$hoy = new DateTime(date('Y-m-d H:i:s'));
			$retraso = $hoy->diff($fechaVenta);

			$retrasoHoras = $fechaVenta->diff($hoy);

			if ($retrasoHoras->days > $dias) {
				return true;
			}

			return false;
    	}
    }



    /**
     * Se encarga de definir cuantos días de retraso tiene la venta según su fecha de venta.
     */
    public function calcular_retraso_dias($fecha = '', $dias = 1)
    {
    	if (!empty($fecha)) {
    		
    		$fechaVenta = new DateTime($fecha);
			$hoy = new DateTime(date('Y-m-d H:i:s'));
			$retraso = $hoy->diff($fechaVenta);

			$retrasoHoras = $fechaVenta->diff($hoy);

			if ($retrasoHoras->days > $dias) {
				return $retrasoHoras->days;
			}

			return false;
    	}
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
		$html						= $this->View->render('notificar_venta_retraso');

		/**
		 * Guarda el email a enviar
		 */
		$this->Correo->create();
		
		if ( $this->Correo->save(array(
			'estado'					=> 'Notificación ventas restrasada',
			'html'						=> $html,
			'asunto'					=> '[NDRZ] Ventas para procesar',
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